<?php
class banco{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT *, lpad(text(id),3,'0') as correlativo FROM modulo_base.banco WHERE id='$id'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA:: DBController();
    $sql="SELECT
            *,
            lpad(text(B.id),3,'0') as correlativo
          FROM modulo_base.banco as B
          WHERE
            not B.eliminado AND
            (
              B.banco ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>