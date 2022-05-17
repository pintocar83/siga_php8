<?php
include("../../library/include.php");

if(!array_key_exists("action",$_REQUEST))
    exit;
$action=$_REQUEST["action"];
$params=$_POST;


$access=SIGA::access("requisicion_interna");//null,r,rw,a
$access="rw";

switch($action){
  case "onGetCorrelativo":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onGetCorrelativo(SIGA::param("id_item_tipo"));
    break;
  case "onGet":
  case "get":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onGet(SIGA::param("id"));
    break;  
  
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onSave($access,
                        SIGA::param("id"),
                        SIGA::param("id_item_tipo"),
                        SIGA::param("fecha"),
                        SIGA::param("id_unidad_coordinacion"),
                        SIGA::paramUpper("concepto"),
                        SIGA::param("items",false),
                        SIGA::param("enviar",false));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onDelete($access,SIGA::param("id"));
    break;
  
  
  case "onListObservacion":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onListObservacion(SIGA::param("id"));
    break;  
  case "onDevolver":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onDevolver(SIGA::param("id"),SIGA::paramUpper("observacion"));
    break;
  case "onReprobar":
    header('Content-Type: text/plain; charset=utf-8');
    requisicion::onReprobar(SIGA::param("id"),SIGA::paramUpper("observacion"));
    break;
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    print requisicion::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    print requisicion::onJavascript($access);
    break;
}



class requisicion{
  public static function onCss($access){
    if(!$access) return;
    return SIGA::css("main.css");
  }

  public static function onJavascript($access,$ofuscar=true){
    if(!$access) return;
    return SIGA::css("main.js");
  }
  
  public static function onGetCorrelativo($id_item_tipo,$json=true){
    $db=SIGA::DBController();
    $sql="SELECT _if(max(correlativo) is null, 1, max(correlativo)+1) as correlativo FROM modulo_inventario.requisicion WHERE id_item_tipo='$id_item_tipo' and text(fecha) like '".SIGA::data()."-%'";
    $return=$db->Execute($sql);
    if($json)
      print json_encode($return);
    else
      return $return;
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_inventario.requisicion WHERE id='$id'";
    $return=$db->Execute($sql);
    
    //si la requisicion no esta aprobada
    if($return[0]["estado"]!="D"){
      //buscar items de la requisicion
      $sql="SELECT id_item, cantidad, 0 as cantidad_aprobada FROM modulo_inventario.requisicion_tiene_item WHERE id_requisicion='$id'";
      $return[0]["items"]=$db->Execute($sql);
    }
    else{
      $sql="SELECT
              RTI.id_item,
              RTI.cantidad,
              (select
                  MMTI.cantidad*UM.cantidad_unidades
                from
                  modulo_inventario.movimiento_material_tiene_requisicion as MMTR,
                  modulo_inventario.movimiento_material_tiene_item AS MMTI,
                  modulo_base.unidad_medida AS UM
                where
                  MMTI.id_item=RTI.id_item AND
                  MMTR.id_requisicion='$id' AND
                  MMTR.id_movimiento_material=MMTI.id_movimiento_material AND
                  UM.id=MMTI.id_unidad_medida
              ) as cantidad_aprobada
            FROM modulo_inventario.requisicion_tiene_item AS RTI WHERE RTI.id_requisicion='$id'";
      $return[0]["items"]=$db->Execute($sql);
      
      //buscar si existen items agregados, extras de la requisicion original
      $items_extras=$db->Execute("SELECT
                                    MMTI.id_item,
                                    0 as cantidad,
                                    MMTI.cantidad*UM.cantidad_unidades AS cantidad_aprobada
                                  FROM
                                    modulo_inventario.movimiento_material_tiene_requisicion as MMTR,
                                    modulo_inventario.movimiento_material_tiene_item AS MMTI,
                                    modulo_base.unidad_medida AS UM
                                  WHERE
                                    MMTR.id_requisicion='$id' AND
                                    MMTR.id_movimiento_material=MMTI.id_movimiento_material AND
                                    UM.id=MMTI.id_unidad_medida AND
                                    MMTI.id_item NOT IN (select id_item from modulo_inventario.requisicion_tiene_item WHERE id_requisicion='$id')
                                    ");
      
      for($i=0;$i<count($items_extras);$i++)
        $return[0]["items"][]=$items_extras[$i];
    }    
    print json_encode($return);
  }
  
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT
            R.id,
            R.correlativo,
            R.fecha,
            R.concepto,
            R.estado,
            IT.tipo
          FROM
            modulo_inventario.requisicion as R,
            modulo_base.item_tipo AS IT,
            modulo_base.usuario as U
          WHERE
            U.usuario='".SIGA::user()."' AND
            U.id_persona_responsable=R.id_persona AND
            R.id_item_tipo=IT.id AND
            text(R.fecha) LIKE '".SIGA::data()."-%'
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onSave($access,
                                $id,
                                $id_item_tipo,
                                $fecha,
                                $id_unidad_coordinacion,
                                $concepto,
                                $items,
                                $enviar){
    
    $db=SIGA::DBController();
    
    //validación de la información
    if(!is_numeric($id_item_tipo)){
      print "{success: false, message: 'Error. El campo tipo de solicitud es inválido.'}";
      return;
    }
     
    if(!$fecha){
      print "{success: false, message: 'Error. El campo fecha se encuentra vacío.'}";
      return;
    }
    
    if(!is_numeric($id_unidad_coordinacion)){
      print "{success: false, message: 'Error. El campo unidad/coordinacion se encuentra vacío.'}";
      return;
    }
    
    if(!$concepto){
      print "{success: false, message: 'Error. El campo concepto se encuentra vacío.'}";
      return;
    }

    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      $data=array("id_item_tipo"=>"'$id_item_tipo'",
                  "fecha"=>"'$fecha'",
                  "id_unidad_coordinacion"=>"'$id_unidad_coordinacion'",
                  "concepto"=>"'$concepto'",
                  );
      
      //Modificar registro
      $result=$db->Update("modulo_inventario.requisicion",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      
      $correlativo=self::onGetCorrelativo($id_item_tipo,false);
      if(!isset($correlativo[0][0])){
        print "{success: false, message: 'Error. No se puedo determinar el siguiente correlativo.'}";
        return;
      }
      $correlativo=$correlativo[0][0];
      
      $id_persona=$db->Execute("SELECT id_persona_responsable FROM modulo_base.usuario WHERE usuario='".SIGA::user()."'");
      if(!isset($id_persona[0][0])){
        print "{success: false, message: 'Error. No se puedo determinar el identificador del usuario actual.'}";
        return;
      }
      $id_persona=$id_persona[0][0];
      
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_inventario.requisicion(id_item_tipo,correlativo,fecha,id_unidad_coordinacion,concepto,id_persona)
                           VALUES('$id_item_tipo','$correlativo','$fecha','$id_unidad_coordinacion','$concepto','$id_persona') RETURNING id");
      
      //buscar el id de la requisicion recien ingresada
      if(!isset($result[0][0])){
        print "{success: false, message: 'Error al obtener el identificador de la requisicion ingresada.'}";
        return;
      }
      $id=$result[0][0];
    }
    
    //Si hay error al modificar o insertar
    if(!$result){      
      print "{success: false, message: 'Error al guardar en la tabla: modulo_inventario.requisicion', messageDB: '".$db->GetMsgErrorClear()."'}";
      return;
    }
    
    //actualizar lista de articulos
    $db->Delete("modulo_inventario.requisicion_tiene_item","id_requisicion='$id'");
    $items=json_decode($items,true);
    for($i=0;$i<count($items);$i++){
      $result=$db->Insert("modulo_inventario.requisicion_tiene_item",array("id_requisicion"=>"'$id'",
                                                                           "id_item"=>"'".$items[$i]["id_item"]."'",
                                                                           "cantidad"=>"'".$items[$i]["cantidad"]."'"));
      if(!$result){      
        print "{success: false, message: 'Error al guardar en la tabla: modulo_inventario.requisicion_tiene_item', messageDB: '".$db->GetMsgErrorClear()."'}";
        return;
      }
    }
    
    
    if($enviar=="true"){
      $result=$db->Update("modulo_inventario.requisicion",array("estado"=>"'E'"),"id='$id'");
      if(!$result){      
        print "{success: false, message: 'Error al guardar en la tabla: modulo_inventario.requisicion', messageDB: '".$db->GetMsgErrorClear()."'}";
        return;
      }
    }
    
    print "{success: true, message: 'Datos guardados con exito.'}";
  }
  
  public static function onDelete($access,$id){
    
    print "{success: false, message: 'Opción deshabilidad. El usuario no tiene permiso para eliminar datos.'}";
    exit;
    //$db=new DBController();
    //$db->ConnectQuick("asl"); 
    //if(!($access=="rw")){//solo el acceso 'rw' es permitido
    //  print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
    //  return;
    //}
    //
    //$result=$db->Delete("curso_aperturado","id='$id'");
    //if(!$result){                    
    //  print "{success: false, message: \"Error al guardar en la tabla: curso_aperturado. Detalle: ".$db->GetMsgError()."\"}";
    //  return;
    //}
    //print "{success: true, message: 'Registro eliminado con éxito.'}";
  }
  
  public static function onAddObservacion($id,$observacion){
    $db=SIGA::DBController();
    
    $id_persona=$db->Execute("SELECT id_persona_responsable FROM modulo_base.usuario WHERE usuario='".SIGA::user()."'");
    if(!isset($id_persona[0][0])){
      return '{"success": false, "message": "Error. No se puedo determinar el identificador del usuario actual."}';
    }
    $id_persona=$id_persona[0][0];
    
    $data=array(
                "id_requisicion"=>"'$id'",
                "observacion"=>"'$observacion'",
                "id_persona_observacion"=>"'$id_persona'"
                );
    
    $result=$db->Insert("modulo_inventario.requisicion_observacion",$data);
    if(!$result){      
      return '{"success": false, "message": "Error al guardar en la tabla: modulo_inventario.requisicion_observacion", "messageDB": "'.$db->GetMsgErrorClear().'"}';
    }   
    return '{"success": true, "message": "Datos guardados con exito."}';
  }
  
  public static function onListObservacion($id){
    $db=SIGA::DBController();
    $sql="SELECT
            to_char(RO.fecha,'DD/MM/YYYY HH:MI AM') as fecha,
            RO.observacion,
            (split_part(P.denominacion,';',1)||' '||split_part(P.denominacion,';',3)) as persona
          FROM
            modulo_inventario.requisicion_observacion AS RO,
            modulo_base.persona as P
          WHERE
            RO.id_requisicion=$id AND
            RO.id_persona_observacion=P.id
          ORDER BY
            RO.fecha
            ";
    $result=$db->Execute($sql);    
    print json_encode($result);
  }
  
  public static function onDevolver($id,$observacion){
    $db=SIGA::DBController();
    $result=$db->Update("modulo_inventario.requisicion",array("estado"=>"'C'"),"id='$id'");
    if(!$result){      
      print "{success: false, message: 'Error al guardar en la tabla: modulo_inventario.requisicion', messageDB: '".$db->GetMsgErrorClear()."'}";
      return;
    }
    //ingresar la observacion
    $result_json=self::onAddObservacion($id,$observacion);
    $result=json_decode($result_json,true);
    if(!$result["success"]){
      print $result_json;
      return;
    }    
    print "{success: true, message: 'Datos guardados con exito.'}";
  }
  
  public static function onReprobar($id,$observacion){
    $db=SIGA::DBController();
    
    $result=$db->Update("modulo_inventario.requisicion",array("estado"=>"'R'"),"id='$id'");
    if(!$result){      
      print "{success: false, message: 'Error al guardar en la tabla: modulo_inventario.requisicion', messageDB: '".$db->GetMsgErrorClear()."'}";
      return;
    }
    //ingresar la observacion
    $result_json=self::onAddObservacion($id,$observacion);
    $result=json_decode($result_json,true);
    if(!$result["success"]){
      print $result_json;
      return;
    }
    
    //borrar el movimiento de material
    $db->Execute("BEGIN WORK");
    //borrar los items
    $result=$db->Delete("modulo_inventario.movimiento_material_tiene_item","id_movimiento_material IN (select id_movimiento_material from modulo_inventario.movimiento_material_tiene_requisicion where id_requisicion=$id)");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      print "{success: false, message: '$mensajeDB', messageDB: '$mensajeDB'}";
      return;
    }
    //borrar el comprobante
    $result=$db->Delete("modulo_inventario.movimiento_material","id IN (select id_movimiento_material from modulo_inventario.movimiento_material_tiene_requisicion where id_requisicion=$id)");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      print "{success: false, message: '$mensajeDB', messageDB: '$mensajeDB'}";
      return;
    }
    $db->Execute("COMMIT WORK");    
    print "{success: true, message: 'Datos guardados con exito.'}";
  }
}
  
?>