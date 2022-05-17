<?php
class convertidor{
   public static function onGet($id_cuenta_presupuestaria){
    $db=SIGA::DBController();
    $sql="SELECT
            CP.id_cuenta_presupuestaria,
            CP.denominacion as denominacion_presupuestaria,
            CP.padre,
            _formatear_cuenta_presupuestaria(CP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
            CC.id_cuenta_contable,
            CC.denominacion as denominacion_contable,
            _formatear_cuenta_contable(CC.id_cuenta_contable) as cuenta_contable
          FROM
            modulo_base.cuenta_presupuestaria as CP
            LEFT JOIN modulo_base.convertidor as C ON CP.id_cuenta_presupuestaria = C.id_cuenta_presupuestaria
            LEFT JOIN  modulo_base.cuenta_contable as CC ON C.id_cuenta_contable=CC.id_cuenta_contable
          WHERE
            CP.id_cuenta_presupuestaria='$id_cuenta_presupuestaria'";    
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="select 
            p.id_cuenta_presupuestaria,
            p.denominacion as denominacion_presupuestaria,
            c.id_cuenta_contable,
            c.denominacion as denominacion_contable
          from 
            modulo_base.convertidor as cg, 
            modulo_base.cuenta_presupuestaria p, 
            modulo_base.cuenta_contable as c
          where
            cg.id_cuenta_presupuestaria=p.id_cuenta_presupuestaria and
            cg.id_cuenta_contable=c.id_cuenta_contable";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$cuentas){
    $db=SIGA::DBController();    
    if(!($access=="rw"))
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
    
    $cuentas=json_decode($cuentas,true);    
    $db->Execute("BEGIN WORK");
    $db->Delete("modulo_base.convertidor");
    for($i=0;$i<count($cuentas);$i++){
      $result=$db->Insert("modulo_base.convertidor",array("id_cuenta_presupuestaria"  => "'".str_clear($cuentas[$i]["id_cuenta_presupuestaria"])."'",
                                                          "id_cuenta_contable"        => "'".str_clear($cuentas[$i]["id_cuenta_contable"])."'"));
      if(!$result){
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_base.convertidor", "messageDB"=>$db->GetMsgErrorClear());
      }
    }

    $db->Execute("COMMIT WORK");    
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }  
}  
?>