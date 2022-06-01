<?php
class grupo_familiar_parentesco{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.grupo_familiar_parentesco WHERE id='$id'";
    return $db->Execute($sql);
  }

  public static function onList($text,$start,$limit,$sort){
    $db=SIGA:: DBController();
    $sql="SELECT
            *
          FROM modulo_nomina.grupo_familiar_parentesco
          WHERE
            activo AND
            (
              parentesco ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}
?>