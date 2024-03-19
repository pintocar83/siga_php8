<?php
class banco_movimiento_tipo{
  public static function onGet($tipo){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.banco_movimiento_tipo WHERE codigo='$tipo'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA:: DBController();

    $add="";
    $access=SIGA::access("banco_movimiento_tipo");//null,r,rw,a
    if($access){
      $tmp=explode("|",$access);
      if(isset($tmp[1])){
        $codigo=explode(",",$tmp[1]);
        $add.=" BMT.codigo IN ('".implode("','",$codigo)."') AND ";
      }
    }

    $sql="SELECT
            *,
            lpad(text(BMT.id),3,'0') as correlativo
          FROM modulo_base.banco_movimiento_tipo as BMT
          WHERE
            $add
            BMT.activo AND
            (
              BMT.denominacion ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
}  
?>