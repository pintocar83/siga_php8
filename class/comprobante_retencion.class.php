<?php
class comprobante_retencion{
  public static function onList($text,$start,$limit,$sort,$mes,$id_retencion_tipo){
    $db=SIGA::DBController();
    $add="";
    if($mes)
      $add.="AND EXTRACT(MONTH FROM RC.fecha)=$mes";
    
    $sql="SELECT
            RC.*,
            P.id as id_persona,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || lpad(text(P.identificacion_numero),9,'0') as persona_identificacion,
            replace(P.denominacion,';',' ') as persona_nombre            
          FROM modulo_base.retencion_comprobante as RC LEFT JOIN modulo_base.persona as P ON RC.id_persona=P.id
          WHERE
            RC.id_retencion_tipo='$id_retencion_tipo' AND
            EXTRACT(YEAR FROM RC.fecha)=".SIGA::data()." AND
            (
              to_char(RC.fecha,'DD/MM/YYYY') like '%$text%' OR
              replace(P.denominacion,';',' ') ilike '%$text%' OR
              lpad(text(RC.numero),8,'0') ilike '%$text%'
            )
            $add";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_Factura($text,$start,$limit,$sort,$id){
    $db=SIGA::DBController();
    $sql="SELECT
            F.id,
            F.fecha,
            F.numero_factura,
            F.numero_control,
            F.total,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || lpad(text(P.identificacion_numero),9,'0') as persona_identificacion,
            replace(P.denominacion,';',' ') as persona_denominacion            
          FROM
            modulo_base.factura as F,
            modulo_base.persona as P,
            modulo_base.retencion_comprobante_tiene_factura as RCTF
          WHERE
            P.id=F.id_persona AND F.id=RCTF.id_factura AND RCTF.id_retencion_comprobante='$id'";
    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_FacturaNoAsociada($text,$start,$limit,$sort,$id){
    $db=SIGA::DBController();
    $add="";
    
    //buscar el id_persona en el comprobante
    $retencion_comprobante=$db->Execute("SELECT id_persona, id_retencion_tipo FROM modulo_base.retencion_comprobante WHERE id='$id'");
    if(isset($retencion_comprobante[0]["id_persona"])){
      $id_persona=$retencion_comprobante[0]["id_persona"];
      $add.="AND P.id='$id_persona'";
    }
    
    $id_retencion_tipo="";
    if(isset($retencion_comprobante[0]["id_retencion_tipo"]))
      $id_retencion_tipo=$retencion_comprobante[0]["id_retencion_tipo"];
    
    $sql="SELECT
            F.id,
            to_char(F.fecha,'DD/MM/YYYY') as fecha,
            F.numero_factura,
            F.numero_control,
            F.total,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || lpad(text(P.identificacion_numero),9,'0') as identificacion,
            replace(P.denominacion,';',' ') as denominacion            
          FROM            
            modulo_base.factura as F,
            modulo_base.persona as P
          WHERE
            P.id=F.id_persona AND            
            F.id NOT IN (select id_factura
                            from modulo_base.retencion_comprobante_tiene_factura as RCTF, modulo_base.retencion_comprobante as RC
                            where RCTF.id_retencion_comprobante=RC.id and RC.id_retencion_tipo='$id_retencion_tipo') AND
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
  
  public static function onNew($access, $id_retencion_tipo){
    $db=SIGA::DBController();
      
    if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");  
    
    $data=array(          
                "id_retencion_tipo"=>"'$id_retencion_tipo'",
                "fecha"=>"'".SIGA::data()."-".date("m-d")."'",
                "numero"=>"(select case when max(numero) is null then 1 else max(numero)+1 end from modulo_base.retencion_comprobante where to_char(fecha,'YYYY-MM')='".SIGA::data()."-".date("m")."' AND id_retencion_tipo='$id_retencion_tipo')"
                );

    $result=$db->Insert("modulo_base.retencion_comprobante",$data);
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }
  
  public static function onUpdate_Numero($access, $id, $numero){
    $db=SIGA::DBController();
      
    if(!($access=="rw"))//solo el acceso 'rw'
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");  
    
    $data=array("numero"=>"'$numero'");

    $result=$db->Update("modulo_base.retencion_comprobante",$data,"id='$id'");
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    return array("success"=>true, "message"=>'Datos actualizados con exito.');
  }
  
  public static function onUpdate_Fecha($access, $id, $fecha){
    $db=SIGA::DBController();
      
    if(!($access=="rw"))//solo el acceso 'rw'
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");  
    
    $data=array("fecha"=>"'$fecha'");

    $result=$db->Update("modulo_base.retencion_comprobante",$data,"id='$id'");
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    return array("success"=>true, "message"=>'Datos actualizados con exito.');
  }
  
  public static function onDelete_Factura($access, $id, $id_factura){
    $db=SIGA::DBController();
      
    if(!($access=="rw"))//solo el acceso 'rw'
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");  

    $result=$db->Delete("modulo_base.retencion_comprobante_tiene_factura","id_retencion_comprobante='$id' AND id_factura='$id_factura'");
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    
    //verificar la cantidad de facturas asociadas. Si es cero. cambiar id_persona a null en el comprobante
    $c=0;
    $cantidad_factura=$db->Execute("SELECT count(*) FROM modulo_base.retencion_comprobante_tiene_factura WHERE id_retencion_comprobante='$id'");
    if(isset($cantidad_factura[0][0])){
      $c=$cantidad_factura[0][0];
      if($c==0){
        $data=array("id_persona"=>"null");

        $result=$db->Update("modulo_base.retencion_comprobante",$data,"id='$id'");
        //Si hay error al modificar o insertar
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }        
      }    
    }
    return array("success"=>true, "message"=>'Datos actualizados con exito.', "cantidad_factura"=>"$c");
  }
  
  public static function onAdd_Factura($access, $id, $id_factura){
    $db=SIGA::DBController();
      
    if(!($access=="rw" or $access=="a"))//solo el acceso 'rw'
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");  
    
    //asociar el comprobante a la persona
    $data=array("id_persona"=>"(select id_persona from modulo_base.factura where id='$id_factura')");
    $result=$db->Update("modulo_base.retencion_comprobante",$data,"id='$id'");
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    
    $data=array("id_retencion_comprobante"=>"'$id'","id_factura"=>"'$id_factura'");
    
    $result=$db->Insert("modulo_base.retencion_comprobante_tiene_factura",$data);
    //Si hay error al modificar o insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    $c=0;
    $cantidad_factura=$db->Execute("SELECT count(*) FROM modulo_base.retencion_comprobante_tiene_factura WHERE id_retencion_comprobante='$id'");
    if(isset($cantidad_factura[0][0]))
      $c=$cantidad_factura[0][0];  
    return array("success"=>true, "message"=>'Datos actualizados con exito.', "cantidad_factura"=>"$c");
  } 
}  
?>