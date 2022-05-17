<?php
class requisicion_externa{
  public static function onGet_Correlativo($tipo){
    $db=SIGA::DBController();
    $sql="SELECT _if(max(correlativo) is null, 1, max(correlativo)+1) as correlativo FROM modulo_base.requisicion_externa WHERE tipo='$tipo' and EXTRACT(YEAR FROM fecha)=".SIGA::data()."";
    return $db->Execute($sql);
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $return=$db->Execute("SELECT
                            RE.id,
                            lpad(text(RE.correlativo),10,'0') as correlativo,
                            to_char(RE.fecha,'DD/MM/YYYY') as fecha,
                            RE.concepto,
                            RE.id_accion_subespecifica,
                            ASE.id_accion_especifica,
                            AE.id_accion_centralizada
                          FROM
                            modulo_base.requisicion_externa AS RE,
                            modulo_base.accion_subespecifica as ASE,
                            modulo_base.accion_especifica as AE,
                            modulo_base.accion_centralizada as AC
                          WHERE
                            RE.id='$id' AND
                            RE.id_accion_subespecifica=ASE.id AND
                            ASE.id_accion_especifica=AE.id AND
                            AE.id_accion_centralizada=AC.id
                          
                          ");
    $return[0]["items"]=$db->Execute("SELECT
                                        RETI.id_item,
                                        I.codigo,
                                        I.item,
                                        I.id_item_tipo,
                                        RETI.cantidad,
                                        RETI.id_unidad_medida
                                      FROM
                                        modulo_base.requisicion_externa_tiene_item as RETI,
                                        modulo_base.item as I
                                      WHERE
                                        I.id=RETI.id_item AND
                                        RETI.id_requisicion_externa='$id'");
    return $return;
  }
  
  public static function onGet_Items($ids){
    $db=SIGA::DBController();
    $id_requisicion="";
    for($i=0;$i<count($ids);$i++){
      $id_requisicion.=$ids[$i];
      if($i<count($ids)-1)
        $id_requisicion.=",";
    }
    $return["concepto"]=$db->Execute("SELECT concepto FROM modulo_base.requisicion_externa WHERE id IN ($id_requisicion)"); 
    $return["items"]=$db->Execute("SELECT
                                        RETI.id_item,
                                        I.aplica_iva,
                                        I.codigo,
                                        I.item,
                                        I.id_item_tipo,
                                        sum(RETI.cantidad) as cantidad,
                                        RETI.id_unidad_medida,
                                        UM.medida
                                      FROM
                                        modulo_base.requisicion_externa_tiene_item as RETI,
                                        modulo_base.item as I,
                                        modulo_base.unidad_medida as UM
                                      WHERE                                        
                                        I.id=RETI.id_item AND
                                        RETI.id_unidad_medida=UM.id AND
                                        RETI.id_requisicion_externa IN ($id_requisicion)
                                      GROUP BY
                                        RETI.id_item,
                                        I.aplica_iva,
                                        I.codigo,
                                        I.item,
                                        I.id_item_tipo,
                                        RETI.id_unidad_medida,
                                        UM.medida
                                      ");    
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort,$mes,$tipo){
    $db=SIGA::DBController();
    $add="";
    if($mes)
      $add.="AND EXTRACT(MONTH FROM RE.fecha)=$mes";
    
    $sql="SELECT
            RE.id,
            lpad(text(RE.correlativo),10,'0') as correlativo,
            to_char(RE.fecha,'DD/MM/YYYY') as fecha,
            _formatear_estructura_presupuestaria(RE.id_accion_subespecifica) as estructura_presupuestaria,
            RE.concepto
          FROM modulo_base.requisicion_externa AS RE
          WHERE
            tipo='$tipo' AND
            EXTRACT(YEAR FROM RE.fecha)=".SIGA::data()." AND
            (
              lpad(text(RE.correlativo),10,'0') LIKE '%$text%' OR
              to_char(RE.fecha,'DD/MM/YYYY') ilike '%$text%' OR
              _formatear_estructura_presupuestaria(RE.id_accion_subespecifica) ilike '%$text%' OR
              RE.concepto ilike '%$text%'
            )
            $add";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_Tipo($text,$start,$limit,$sort,$mes,$tipo,$id_comprobante){
    $db=SIGA::DBController();
    $add="";
    if($mes)
      $add.="AND EXTRACT(MONTH FROM RE.fecha)=$mes ";
    
    /*if($tipo=="OC")//si es para orden de compra
      $add.="AND (RE.id_item_tipo='1' OR RE.id_item_tipo='2')";
    else if($tipo=="OS")//si es para orden de servicio
      $add.="AND RE.id_item_tipo='3'";*/
    
    $sql="SELECT
            RE.id,
            lpad(text(RE.correlativo),10,'0') as correlativo,
            to_char(RE.fecha,'DD/MM/YYYY') as fecha,
            RE.concepto
          FROM modulo_base.requisicion_externa AS RE
          WHERE
            RE.tipo='$tipo' AND
            RE.id NOT IN (select id_requisicion_externa from modulo_base.comprobante_tiene_requisicion_externa as CTRE where CTRE.id_comprobante<>'$id_comprobante') AND
            EXTRACT(YEAR FROM RE.fecha)=".SIGA::data()." AND
            (
              lpad(text(RE.correlativo),10,'0') LIKE '%$text%' OR
              to_char(RE.fecha,'DD/MM/YYYY') ilike '%$text%' OR
              RE.concepto ilike '%$text%'
            )
            $add";
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
                                $id_accion_subespecifica,
                                $items){
    $db=SIGA::DBController();
    
    //usar str_clear para los valores dentro de $items, dado que esta información es enviada directamente desde el cliente sin limpiar.
    //y de no usarse pueden hacer inyección sql    
    $anio=explode("-",$fecha)[0];
    if($anio!=SIGA::data())
      return array("success"=>false, "message"=>"Error. La fecha de la requisición no corresponde con el año de trabajo actual.");
    
    $db->Execute("BEGIN WORK");
    
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      }
      
      //buscar si el tipo cambio, si cambio obtener un nuevo correlativo
      $result=$db->Execute("select tipo from modulo_base.requisicion_externa where id='$id'");
      if(!isset($result[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error al obtener el tipo de requisición.");
      }
      $tipo_anterior=$result[0][0];
      if($tipo!=$tipo_anterior){
        $correlativo=self::onGet_Correlativo($tipo);
        $correlativo=$correlativo[0][0];
        $data=array(
                    "tipo"=>"'$tipo'",
                    "correlativo"=>"'".$correlativo[0][0]."'",
                    "fecha"=>"'$fecha'",
                    "concepto"=>"'$concepto'",
                    "id_accion_subespecifica"=>"'$id_accion_subespecifica'"
                    );
      }
      else{
        $data=array(
                    "fecha"=>"'$fecha'",
                    "concepto"=>"'$concepto'",
                    "id_accion_subespecifica"=>"'$id_accion_subespecifica'"
                    );
      }
      
      //Modificar registro
      $result=$db->Update("modulo_base.requisicion_externa",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      }
      
      $correlativo=self::onGet_Correlativo($tipo);
      $correlativo=$correlativo[0][0];

      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_base.requisicion_externa(tipo,correlativo,fecha,concepto,id_accion_subespecifica)
                           VALUES('$tipo','$correlativo','$fecha','$concepto','$id_accion_subespecifica') RETURNING id");
      
      //Si hay error al modificar o insertar
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
      
      //buscar el id de registro recien ingresado
      if(!isset($result[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error al obtener el identificador de la requisición.");
      }
      $id=$result[0][0];
    }
    
    //borrar los items ateriores
    $db->Delete("modulo_base.requisicion_externa_tiene_item","id_requisicion_externa='$id'");
    //insertar los nuevos items
    for($i=0;$i<count($items);$i++){
      $result=$db->Insert("modulo_base.requisicion_externa_tiene_item",
                     array(
                            "id_requisicion_externa"=>"'$id'",
                            "id_item"=>"'".$items[$i]["id_item"]."'",
                            "cantidad"=>"'".$items[$i]["cantidad"]."'",
                            "id_unidad_medida"=>"'".$items[$i]["id_unidad_medida"]."'"
                          )
                    );
      //Si hay error al insertar
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
    }
    
    
    
    
    $db->Execute("COMMIT WORK");
    return array("success"=>true, "message"=>'Datos guardados con exito.',"id"=>"$id");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();exit;
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");

    $result=$db->Delete("modulo_base.factura","id='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }    
    return array("success"=>true, "message"=>'Registro eliminado con exito.');
  }  
}  
?>
