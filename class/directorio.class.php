<?php
class directorio{
  public static function onList($text,$start,$limit,$sort,$tipo){
    $db=SIGA::DBController(!SIGA::user()?SIGA::$database_default:NULL);
    
    if(SIGA::user())
      $add_columnas="P.telefono, P.correo";
    else
      $add_columnas="'' as telefono, '' as correo";
    
    $add="";
    switch($tipo){
      case "F":
        $sql="SELECT
            P.id,
            P.identificacion_tipo,
            P.identificacion_numero,
            P.identificacion_tipo || '-' || P.identificacion_numero as identificacion,
            replace(P.denominacion,';',' ') as denominacion,
            F.extension,
            $add_columnas            
          FROM
            modulo_base.persona as P, modulo_nomina.ficha as F
          WHERE
            F.activo AND
            F.id_persona=P.id AND
              (
                P.identificacion_tipo || '-' || P.identificacion_numero ILIKE '%$text%' OR
                replace(P.denominacion,';',' ') ILIKE '%$text%'
              )
            ";
      break;
      case "J":
      case "N":
        $sql="SELECT
            P.id,
            P.identificacion_tipo,
            P.identificacion_numero,
            P.identificacion_tipo || '-' || P.identificacion_numero as identificacion,
            replace(P.denominacion,';',' ') as denominacion,
            $add_columnas
          FROM
            modulo_base.persona as P
          WHERE
            P.identificacion_tipo<>'' AND
            P.tipo='$tipo' AND
              (
                P.identificacion_tipo || '-' || P.identificacion_numero ILIKE '%$text%' OR
                replace(P.denominacion,';',' ') ILIKE '%$text%'
              )
            ";
      break;
    }

    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  } 
}
?>