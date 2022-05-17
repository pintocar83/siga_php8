<?php
class cargo{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.cargo WHERE id='$id'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();    
    $sql="SELECT
            *,
            lpad(text(C.id),3,'0') as correlativo
          FROM modulo_base.cargo as C
          WHERE            
            (
              C.denominacion ILIKE '%$text%' OR
              C.formula ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>