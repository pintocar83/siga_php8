<?php
class retencion{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.retencion WHERE id='$id'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();    
    $sql="SELECT
            *,
            lpad(text(R.id),3,'0') as correlativo
          FROM modulo_base.retencion as R
          WHERE
            R.activo AND
            (
              R.denominacion ILIKE '%$text%' OR
              R.formula ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>