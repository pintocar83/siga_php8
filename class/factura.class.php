<?php
class factura{
  public static function onGet($id){
    $db=SIGA::DBController();
    $return=$db->Execute("SELECT
                            F.*,
                            F.informacion_iva[1] as informacion_iva_1,
                            F.informacion_iva[2] as informacion_iva_2,
                            F.informacion_iva[3] as informacion_iva_3,
                            F.informacion_iva[4] as informacion_iva_4,
                            F.informacion_iva[5] as informacion_iva_5,
                            F.informacion_iva[6] as informacion_iva_6,
                            F.informacion_iva[7] as informacion_iva_7,
                            F.informacion_islr[1] as informacion_islr_1,
                            F.informacion_islr[2] as informacion_islr_2,
                            F.informacion_islr[3] as informacion_islr_3,
                            F.informacion_islr[4] as informacion_islr_4,
                            F.informacion_1x1000[1] as informacion_1x1000_1,
                            F.informacion_1x1000[2] as informacion_1x1000_2,
                            F.informacion_1x1000[3] as informacion_1x1000_3,
                            F.informacion_1x1000[4] as informacion_1x1000_4,
                            P.tipo,
                            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
                            replace(P.denominacion,';',' ') as denominacion  
                          FROM modulo_base.factura as F, modulo_base.persona as P
                          WHERE
                            F.id='$id' AND
                            P.id=F.id_persona");
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort,$mes){
    $db=SIGA::DBController();
    $add="";
    if($mes)
      $add.="AND EXTRACT(MONTH FROM F.fecha)=$mes";
    
    $sql="SELECT
            F.id,
            to_char(F.fecha,'DD/MM/YYYY') as fecha,
            F.numero_factura,
            F.numero_control,
            F.total,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || lpad(text(P.identificacion_numero),9,'0') as identificacion,
            replace(P.denominacion,';',' ') as denominacion            
          FROM modulo_base.factura as F, modulo_base.persona as P
          WHERE
            P.id=F.id_persona AND
            --EXTRACT(YEAR FROM F.fecha)=".SIGA::data()." AND
            (
              to_char(F.fecha,'DD/MM/YYYY') like '%$text%' OR
              F.numero_factura ilike '%$text%' OR
              F.numero_control ilike '%$text%' OR
              (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || lpad(text(P.identificacion_numero),9,'0') ilike '%$text%' OR
              replace(P.denominacion,';',' ') ilike '%$text%'
            )
            $add";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,
                                $id,
                                $id_persona,
                                $fecha,
                                $numero_factura,
                                $numero_control,
                                $total,
                                $informacion_iva,
                                $informacion_islr,
                                $informacion_1x1000,
                                $id_retencion_islr){
    $db=SIGA::DBController();

    //buscar si la factura se encuentra registrada
    $result=$db->Execute("select count(*) from modulo_base.factura where id_persona='$id_persona' and numero_factura='$numero_factura' and text(id)<>'$id'");      
    if(isset($result[0][0]))
      if($result[0][0]>0)
        return array("success"=>false, "message"=>"Error. Ya existe una factura con el mismo numero para el proveedor o beneficiario.");
    
    $data=array(
                "id_persona"=>"'$id_persona'",
                "fecha"=>"'$fecha'",
                "numero_factura"=>"'$numero_factura'",
                "numero_control"=>"'$numero_control'",
                "total"=>"'$total'",
                "informacion_iva"=>"$informacion_iva",
                "informacion_islr"=>"$informacion_islr",
                "informacion_1x1000"=>"$informacion_1x1000",
                "id_retencion_islr"=> $id_retencion_islr?"'$id_retencion_islr'":"NULL"
                );

    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.factura",$data,"id='$id'");      
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.factura",$data);      
    }    
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    return array("success"=>true, "message"=>'Datos guardados con exito.',"id"=>"$id");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
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