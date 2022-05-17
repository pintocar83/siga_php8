<?php
class movimiento_material{  
  public static function onGetCorrelativo($tipo){
    $db=SIGA::DBController();
    $sql="SELECT _if(max(correlativo) is null, 1, max(correlativo)+1) as correlativo FROM modulo_inventario.movimiento_material WHERE tipo='$tipo' and text(fecha) like '".SIGA::data()."-%'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_inventario.movimiento_material WHERE id='$id'";
    $return=$db->Execute($sql);
    
    //buscar items de la movimiento_material
    $sql="SELECT id_item, cantidad, id_unidad_medida FROM modulo_inventario.movimiento_material_tiene_item WHERE id_movimiento_material='$id'";
    $return[0]["items"]=$db->Execute($sql);
    return $return;
  }
  
  public static function onGetByRequisicion($id_requisicion){
    $db=SIGA::DBController();
    $return="";
    //buscar si la requisicion tiene movimiento de salida en el inventario
    $sql="SELECT id_movimiento_material FROM modulo_inventario.movimiento_material_tiene_requisicion WHERE id_requisicion=$id_requisicion";
    $id_movimiento_material=$db->Execute($sql);
   
    //si tiene asociado un movimiento cargar los articulos con la cantidad
    if(isset($id_movimiento_material[0][0])){
      $id=$id_movimiento_material[0][0];
      $sql="SELECT * FROM modulo_inventario.movimiento_material WHERE id='$id'";
      $movimiento_material=$db->Execute($sql);
      
      $return["correlativo"]=$movimiento_material[0]["correlativo"];
      $return["fecha"]=$movimiento_material[0]["fecha"];
      $return["concepto"]=$movimiento_material[0]["concepto"];
      
      //para cada item de la requisicion
      $sql="SELECT
              id_item,
              cantidad AS cantidad_solicitada,
              modulo_inventario.disponibilidad_material(id_item) as cantidad_disponible
            FROM
              modulo_inventario.requisicion_tiene_item
            WHERE
              id_requisicion='$id_requisicion'";
            
      $return["items"]=$db->Execute($sql);
      for($i=0;$i<count($return["items"]);$i++){
        $entregar=$db->Execute("SELECT
                                  MMTI.cantidad*UM.cantidad_unidades AS cantidad
                                FROM
                                  modulo_inventario.movimiento_material_tiene_item AS MMTI,
                                  modulo_base.unidad_medida AS UM
                                WHERE
                                  UM.id=MMTI.id_unidad_medida AND
                                  MMTI.id_movimiento_material='$id' AND
                                  MMTI.id_item='".$return["items"][$i]["id_item"]."'");
        if(isset($entregar[0][0]))
          $entregar=$entregar[0][0];
        else
          $entregar=0;
        $return["items"][$i]["cantidad_entregar"]=$entregar;
      }
      
      //buscar si existen items agregados, extras de la requisicion original
      $items_extras=$db->Execute("SELECT
                                    MMTI.id_item,
                                    0 as cantidad_solicitada,
                                    modulo_inventario.disponibilidad_material(MMTI.id_item) as cantidad_disponible,
                                    MMTI.cantidad*UM.cantidad_unidades AS cantidad_entregar
                                  FROM
                                    modulo_inventario.movimiento_material_tiene_item AS MMTI,
                                    modulo_base.unidad_medida AS UM
                                  WHERE
                                     UM.id=MMTI.id_unidad_medida AND
                                     MMTI.id_movimiento_material='$id' AND
                                     MMTI.id_item NOT IN (select id_item from modulo_inventario.requisicion_tiene_item WHERE id_requisicion='$id_requisicion')");
      
      for($i=0;$i<count($items_extras);$i++)
        $return["items"][]=$items_extras[$i];
    }
    else{
      //buscar items de la requisicion
      $sql="SELECT
              id_item,
              cantidad AS cantidad_solicitada,
              modulo_inventario.disponibilidad_material(id_item) as cantidad_disponible
            FROM
              modulo_inventario.requisicion_tiene_item
            WHERE
              id_requisicion='$id_requisicion'";
            
      $return["items"]=$db->Execute($sql);
      for($i=0;$i<count($return["items"]);$i++){
        $entregar=$return["items"][$i]["cantidad_solicitada"];
        if($entregar>$return["items"][$i]["cantidad_disponible"])
          $entregar=$return["items"][$i]["cantidad_disponible"];
        $return["items"][$i]["cantidad_entregar"]=$entregar;
      }
      
      $return["correlativo"]=self::onGetCorrelativo('S',false);
      $return["correlativo"]=$return["correlativo"][0][0];
      $return["fecha"]=date("Y-m-d");
      $return["observacion"]="";
    }
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();  
    $sql="SELECT
            MM.*,
            case when MM.tipo='E' then 'ENTRADA' else 'SALIDA' end as tipo_denominacion
          FROM
            modulo_inventario.movimiento_material as MM
          WHERE            
            text(MM.fecha) LIKE '".SIGA::data()."-%'
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onSave($access,
                                $id,
                                $tipo,
                                $fecha,
                                $concepto,
                                $items){
    
    $db=SIGA::DBController();  

    //validación de la información
    if(!($tipo=="E" or $tipo=="S")){
      return array("success"=> false, "message"=> "Error. El campo tipo de movimiento es inválido.");
    }
     
    if(!$fecha){
      return array("success"=> false, "message"=> "Error. El campo fecha se encuentra vacío.");
    }
    
    if(!$concepto){
      return array("success"=> false, "message"=> "Error. El campo concepto se encuentra vacío.");
    }
    
    $db->Execute("BEGIN WORK");

    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> "Error. El usuario no tiene permiso para modificar datos.");
      }
      $data=array("tipo"=>"'$tipo'",
                  "fecha"=>"'$fecha'",
                  "concepto"=>"'$concepto'",
                  );
      
      //Modificar registro
      $result=$db->Update("modulo_inventario.movimiento_material",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> "Error. El usuario no tiene permiso para guardar datos.");
      }
      
      $correlativo=self::onGetCorrelativo($tipo,false);
      if(!isset($correlativo[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> "Error. No se puedo determinar el siguiente correlativo.");
      }
      $correlativo=$correlativo[0][0];
      
      $id_persona=$db->Execute("SELECT id_persona_responsable FROM modulo_base.usuario WHERE usuario='".SIGA::user()."'");
      if(!isset($id_persona[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> "Error. No se puedo determinar el identificador del usuario actual.");
      }
      $id_persona=$id_persona[0][0];
      
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_inventario.movimiento_material(tipo,correlativo,fecha,concepto,id_persona)
                           VALUES('$tipo','$correlativo','$fecha','$concepto','$id_persona') RETURNING id");
      
      //buscar el id de la movimiento_material recien ingresada
      if(!isset($result[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> "Error al obtener el identificador de la movimiento_material ingresada");
      }
      $id=$result[0][0];
    }
    
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=> false, "message"=> "Error al guardar en la tabla: modulo_inventario.movimiento_material", "messageDB"=> $mensajeDB);
    }
    
    
    
    
    //1) eliminar los items no presentes en $items
    $items_presentes="";
    for($i=0;$i<count($items);$i++){
      $items_presentes.=$items[$i]["id_item"];
      if($i<count($items)-1)
        $items_presentes.=",";
    }
    if(!$items_presentes)
      $result=$db->Delete("modulo_inventario.movimiento_material_tiene_item","id_movimiento_material=$id");
    else
      $result=$db->Delete("modulo_inventario.movimiento_material_tiene_item","id_movimiento_material=$id AND id_item NOT IN ($items_presentes)");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=> false, "message"=> "'.$mensajeDB.'", "messageDB"=> $mensajeDB);
    }
    //2) Modificar o Ingresar la cantidad para $items
    for($i=0;$i<count($items);$i++){
      //2.1 buscar si existe el item
      $item_existe=$db->Execute("SELECT count(*) FROM modulo_inventario.movimiento_material_tiene_item WHERE id_movimiento_material='$id' AND id_item=".$items[$i]["id_item"]);
      if($item_existe[0][0]>0){//2.1.1 si existe, modificar
        $result=$db->Update("modulo_inventario.movimiento_material_tiene_item",
                            array (
                                  "cantidad"=>"'".$items[$i]["cantidad"]."'",
                                  "id_unidad_medida"=>"'".$items[$i]["id_unidad_medida"]."'"
                                  ),
                            "id_movimiento_material=$id AND id_item=".$items[$i]["id_item"]);
      }
      else{//2.1.2 si no, ingresar
        $result=$db->Insert("modulo_inventario.movimiento_material_tiene_item",
                            array (
                                  "id_movimiento_material"=>"'$id'",
                                  "id_item"=>"'".$items[$i]["id_item"]."'",
                                  "cantidad"=>"'".$items[$i]["cantidad"]."'",
                                  "id_unidad_medida"=>"'".$items[$i]["id_unidad_medida"]."'")
                                  );
      }
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=> false, "message"=> $mensajeDB, "messageDB"=> $mensajeDB);
      }
    }
    
    
    
    $db->Execute("COMMIT WORK");
    return array("success"=> true, "message"=> "Datos guardados con exito.", "id"=> $id);
  }
  
  public static function onSaveByRequisicion( $access,
                                              $id_requisicion,
                                              $fecha,
                                              $concepto,
                                              $items,
                                              $aprobar){
    $db=SIGA::DBController();
    //buscar si la requisicion tiene movimiento de salida en el inventario
    $sql="SELECT id_movimiento_material FROM modulo_inventario.movimiento_material_tiene_requisicion WHERE id_requisicion='$id_requisicion'";
    $id=$db->Execute($sql);
    if(isset($id[0][0]))//si es modificar
      $id=$id[0][0];
    else
      $id="";//si es nuevo
    
    
    $result=self::onSave($access,$id,'S',$fecha,$concepto,$items);    
    if(!$result["success"])
      return array("success"=> false, "message"=> $result["message"]);
    
    if($id==""){//si es nuevo, asociar el movimiento de inventario a la requisicion
      //obtener el id del movimiento_material    
      $id=$result["id"];
      //asociar el movimiento de material a la requisicion
      //tabla modulo_inventario.movimiento_material_tiene_requisicion
      $result=$db->Insert("modulo_inventario.movimiento_material_tiene_requisicion",array( "id_movimiento_material"=>"'$id'", "id_requisicion"=>"'$id_requisicion'"));
      if(!$result)   
        return array("success"=> false, "message"=> "Error al tratar de asociar el comprobante de salida con la requisición.", "messageDB"=> $db->GetMsgErrorClear());
    }
    
    if($aprobar=="true"){
      $result=$db->Update("modulo_inventario.requisicion",array( "estado"=>"'D'"),"id='$id_requisicion'");
      if(!$result)
        return array("success"=> false, "message"=> "Error al aprobar la solicitud de materiales.", "messageDB"=> $db->GetMsgErrorClear());
    }
    
    return array("success"=> true, "message"=> "Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      return array("success"=> false, "message"=> 'Error. El usuario no tiene permiso para eliminar datos.');
    }
    $db->Execute("BEGIN WORK");
    //borrar los items
    $result=$db->Delete("modulo_inventario.movimiento_material_tiene_item","id_movimiento_material=$id");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=> false, "message"=> $mensajeDB, "messageDB" => $mensajeDB);
    }
    //borrar el comprobante
    $result=$db->Delete("modulo_inventario.movimiento_material","id=$id");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=> false, "message"=> $mensajeDB, "messageDB"=> $mensajeDB);
    }
    $db->Execute("COMMIT WORK");
    return array("success"=> true, "message"=> 'Registro eliminado con éxito.');
  }  
}
  
?>