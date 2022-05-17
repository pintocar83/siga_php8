<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO{
   public static function onInit(){
    $access=SIGA::access("detalle_presupuestario");//null,r,rw,a
    switch($_REQUEST["action"]){
      case "onList_Operacion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Operacion(SIGA::param("operacion",false)));
        break;
      case "onList_CuentaPresupuestariaDisponibilidad":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_CuentaPresupuestariaDisponibilidad(SIGA::paramUpper("text"),
                                                                          SIGA::param("start"),
                                                                          SIGA::param("limit"),
                                                                          SIGA::param("sort",false),
                                                                          SIGA::param("group",false),
                                                                          SIGA::param("id_accion_subespecifica"),
                                                                          SIGA::param("id_cuenta_presupuestaria")));
        break;
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        print self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        print self::onJavascript($access);
        break;
    }
  }
  
  public static function onCss($access){
    if(!$access) return;
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }
  
  public static function onList_Operacion($operacion){
    $db=SIGA::DBController();
    $add="";
    if($operacion){
      $operacion=json_decode($operacion,true);
      for($i=0;$i<count($operacion);$i++){
        $operacion[$i]=str_clear($operacion[$i]);
        $add.=" '".$operacion[$i]."'";
        if($i<count($operacion)-1)
          $add.=",";
      }
      $add="WHERE operacion IN ($add)";
    }
    
    $sql="SELECT
            *, denominacion||' ('||operacion||')' as denominacion_operacion
          FROM modulo_base.detalle_presupuestario_operacion
          $add
          ORDER BY denominacion";

    return $db->Execute($sql);
  }
  
   public static function onList_CuentaPresupuestariaDisponibilidad($text,$start,$limit,$sort,$group,$id_accion_subespecifica,$id_cuenta_presupuestaria){
      $db=SIGA::DBController();
      
      $add="";
      if($id_accion_subespecifica)
        $add.=" AND DP.id_accion_subespecifica='$id_accion_subespecifica'";
      if($id_cuenta_presupuestaria)
        $add.=" AND DP.id_cuenta_presupuestaria like '$id_cuenta_presupuestaria'";
        
      $sql="with consulta as (
               SELECT 
                  DP.id_accion_subespecifica,
                  DP.id_cuenta_presupuestaria,
                  _formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
                  _formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                  sum(case when DP.operacion='AP' or DP.operacion='AU' then DP.monto else -DP.monto end) as disponibilidad
               FROM
                  modulo_base.comprobante as C,
                  modulo_base.detalle_presupuestario as DP            
               WHERE
                  EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                  C.contabilizado AND
                  C.id=DP.id_comprobante AND
                  (DP.operacion='AP' OR DP.operacion='AU' OR DP.operacion='DI' OR DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP')
                  $add
               GROUP BY
                  id_accion_subespecifica,
                  id_cuenta_presupuestaria,
                  estructura_presupuestaria,
                  cuenta_presupuestaria
            )
            select
               CLT.id_accion_subespecifica,
               CLT.id_cuenta_presupuestaria,
               CLT.estructura_presupuestaria,
               CLT.cuenta_presupuestaria,
               _formatear_numero(CLT.disponibilidad) as disponibilidad,               
               CP.denominacion
            from
               consulta as CLT,
               modulo_base.cuenta_presupuestaria as CP
            where
               CLT.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
               (
                  CLT.estructura_presupuestaria ILIKE '%$text%' OR
                  CLT.id_cuenta_presupuestaria ILIKE '%$text%' OR
                  CLT.cuenta_presupuestaria ILIKE '%$text%' OR
                  CP.denominacion ILIKE '%$text%'                  
               )
            ";

      $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
      //$return["result"]=$db->Execute($sql." ".sql_sort($sort)." ");
      //$return["total"]=$db->Execute(sql_query_total($sql));
      //$return["total"]=$return["total"][0][0];
      $return["total"]="";
      return $return;
  }
}

MODULO::onInit();
?>
