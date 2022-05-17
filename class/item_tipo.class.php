<?php
class item_tipo{  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.item_tipo WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.item_tipo WHERE activo AND UPPER(tipo) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>
