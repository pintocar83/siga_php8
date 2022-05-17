<?php
class nomina_concepto_periodo{   
  public static function onList_Agregar($ids,$text,$start,$limit,$sort){
    $db=SIGA::DBController();     
    $add="";
    for($i=0;$i<count($ids);$i++)
      $add.=$ids[$i].($i<count($ids)-1?",":"");

    if($add)
      $add="C.id NOT IN ($add) AND";
    
    $sql="SELECT
            *
          FROM
            modulo_nomina.concepto as C
          WHERE
            C.activo AND 
            $add
            (
              C.concepto ILIKE '%$text%' OR
              C.codigo ILIKE '%$text%' OR
              C.identificador ILIKE '%$text%'
            ) 
            ";
    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onAgregar($access,$id_nomina,$id_periodo,$id_concepto){
    $db=SIGA::DBController();    
    $id_nomina=explode(",",$id_nomina);
    for($i=0;$i<count($id_nomina);$i++)    
      $db->Insert("modulo_nomina.concepto_periodo", array("id_nomina"=>$id_nomina[$i], "id_periodo"=>"$id_periodo", "id_concepto"=>"$id_concepto"));
  }
  
  public static function onQuitar($access,$id_nomina,$id_periodo,$id_concepto){
    $db=SIGA::DBController();   
    $db->Delete("modulo_nomina.concepto_periodo", "id_nomina in ($id_nomina) and id_periodo=$id_periodo AND id_concepto=$id_concepto");
  }  
}  
?>