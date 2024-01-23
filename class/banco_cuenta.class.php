<?php
class banco_cuenta{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT
            BC.*,
            _formatear_cuenta_contable(BC.id_cuenta_contable) as cuenta_contable,
            CC.denominacion as denominacion_contable
          FROM
            modulo_base.banco_cuenta as BC,
            modulo_base.cuenta_contable as CC
          WHERE
            BC.id='$id' AND
            BC.id_cuenta_contable=CC.id_cuenta_contable";
    return $db->Execute($sql);
  }
  
  public static function onGet2($numero_cuenta){
    $db=SIGA::DBController();
    $sql="SELECT
            BC.*,
            lpad(text(BC.id),3,'0') as correlativo,
            _formatear_cuenta_contable(BC.id_cuenta_contable) as cuenta_contable,
            CC.denominacion as denominacion_contable,
            B.banco,
            BCT.denominacion as cuenta_tipo
          FROM
            modulo_base.banco_cuenta as BC,
            modulo_base.cuenta_contable as CC,
            modulo_base.banco as B,
            modulo_base.banco_cuenta_tipo as BCT
          WHERE
            BC.numero_cuenta='$numero_cuenta' AND
            BC.id_cuenta_contable=CC.id_cuenta_contable AND
            BC.id_banco=B.id AND
            BC.id_banco_cuenta_tipo=BCT.id";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();

    $add="";
    $access_banco_cuenta = SIGA::access("banco_cuenta");
    if($access_banco_cuenta){
      $tmp=explode("|",$access_banco_cuenta);
      if(isset($tmp[1])){
        $add.=" BC.id IN (".$tmp[1].") AND ";
      }
    }

    $sql="SELECT
            BC.*,
            lpad(text(BC.id),3,'0') as correlativo,
            _formatear_cuenta_contable(BC.id_cuenta_contable) as cuenta_contable,
            CC.denominacion as denominacion_contable,
            B.banco,
            BCT.denominacion as tipo
          FROM
            modulo_base.banco_cuenta as BC,
            modulo_base.cuenta_contable as CC,
            modulo_base.banco as B,
            modulo_base.banco_cuenta_tipo as BCT
          WHERE
            $add
            BC.activo AND
            BC.id_cuenta_contable=CC.id_cuenta_contable AND
            BC.id_banco=B.id AND
            BC.id_banco_cuenta_tipo=BCT.id AND
            (
              BC.denominacion ILIKE '%$text%' OR
              BC.numero_cuenta ILIKE '%$text%' OR
              CC.denominacion ILIKE '%$text%' OR
              BC.id_cuenta_contable ILIKE '%$text%' OR
              _formatear_cuenta_contable(BC.id_cuenta_contable) ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onSave(
    $access,
    $id,
    $numero_cuenta,
    $denominacion,
    $fecha_apertura,
    $fecha_cierre,
    $cuenta_activa,
    $id_banco,
    $id_banco_cuenta_tipo,
    $id_cuenta_contable
    ){
    $db=SIGA::DBController();

    $existe=$db->Execute("SELECT count(*) n FROM modulo_base.banco_cuenta WHERE activo AND text(id)<>'".$id."' AND numero_cuenta ='".$numero_cuenta."'");
    if(isset($existe[0]["n"]) and $existe[0]["n"]>0){
      return array("success"=>false, "message"=>"Ya existe una cuenta con el número #{$numero_cuenta}.");
    }

    if(!trim($fecha_apertura))  
      $fecha_apertura="DEFAULT";
    else
      $fecha_apertura="'$fecha_apertura'";

    if(!trim($fecha_cierre))  
      $fecha_cierre="DEFAULT";
    else
      $fecha_cierre="'$fecha_cierre'";

    $data=array(
      "numero_cuenta"        => "'$numero_cuenta'",
      "denominacion"         => "'$denominacion'",
      "fecha_apertura"       => "$fecha_apertura",
      "fecha_cierre"         => "$fecha_cierre",
      "cuenta_activa"        => "'$cuenta_activa'",
      "id_banco"             => "'$id_banco'",
      "id_banco_cuenta_tipo" => "'$id_banco_cuenta_tipo'",
      "id_cuenta_contable"   => "'$id_cuenta_contable'"
    );

    if($id!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.banco_cuenta",
                          $data,
                          "id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.banco_cuenta",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.banco_cuenta", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();  
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Update("modulo_base.banco_cuenta",["activo"=>"false"],"id='$id'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.banco_cuenta", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Registro eliminado con éxito.");
  }
}  
?>