<?php
class nomina_periodo_tipo{  
  public static function onGet($tipo){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.periodo_tipo WHERE tipo='$tipo'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList_Activo($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.periodo_tipo WHERE activo";    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }  
}  
?>
