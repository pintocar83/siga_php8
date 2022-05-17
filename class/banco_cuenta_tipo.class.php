<?php
class banco_cuenta_tipo{
  public static function onGet($tipo){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.banco_cuenta_tipo WHERE codigo='$tipo'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA:: DBController();
    $sql="SELECT
            *,
            lpad(text(BCT.id),3,'0') as correlativo
          FROM modulo_base.banco_cuenta_tipo as BCT
          WHERE
            BCT.activo AND
            (
              BCT.denominacion ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>