<?php
class usuario_perfil_acceso{
  public static function onGet($perfil){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.usuario_perfil_acceso WHERE perfil='$perfil'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onGetSelect($perfil){
    $db=SIGA::DBController();
    $sql="SELECT
            *
          FROM
            modulo_base.usuario_perfil_acceso
          WHERE
            perfil='$perfil'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onListSelect($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT 
            perfil,
            acceso            
          FROM
            modulo_base.usuario_perfil_acceso
          WHERE
            perfil ILIKE '%$text%'";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.usuario_perfil_acceso WHERE UPPER(perfil) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id){    
  }
  
  public static function onDelete($access,$id){
  }  
}
?>