<?php
class unidad_medida{
  public static function onGet($id){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_base.unidad_medida WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onGetSelect($id){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_base.unidad_medida WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onListSelect($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();    
    $sql="SELECT * FROM modulo_base.unidad_medida WHERE activo AND UPPER(medida) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_base.unidad_medida WHERE UPPER(medida) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
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