<?php
class fuente_recursos{
  public static function onGet($id){
    $db=SIGA::DBController(); 
    $sql="SELECT id, codigo_fuente, denominacion_fuente FROM modulo_base.fuente_recursos WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();  
    $sql="SELECT
            id,
            codigo_fuente,
            denominacion_fuente,
            codigo_fuente||' '||denominacion_fuente as codigo_denominacion_fuente
          FROM
            modulo_base.fuente_recursos
          WHERE
            activo AND
            (
              codigo_fuente ILIKE '%$text%' OR              
              denominacion_fuente ILIKE '$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }  
}  
?>