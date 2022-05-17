<?php
include("../../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO{
  public static function onInit(){
    $access=SIGA::access("orden_compra");//null,r,rw,a    
    switch($_REQUEST["action"]){      
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::param("id_comprobante")));
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
  
  public static function onList($id_comprobante){
    $db=SIGA::DBController();
    //articulos de la orden de compra
    /*$items=$db->Execute("select CTI.id_item, CTI.cantidad, CTI.costo, CTI.descuento, CTI.aplica_iva, I.id_cuenta_presupuestaria
                          from modulo_base.comprobante_tiene_item as CTI, modulo_base.item as I
                          where CTI.id_comprobante='$id_comprobante' and CTI.id_item=I.id");*/
    $items=$db->Execute("SELECT
                            CTI.id_item,
                            --CTI.cantidad,
                            RETI.cantidad,
                            CTI.costo,
                            CTI.descuento,
                            CTI.aplica_iva,
                            I.id_cuenta_presupuestaria,
                            _formatear_cuenta_presupuestaria(I.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                            CP.denominacion as denominacion_cuenta_presupuestaria,
                            RE.id_accion_subespecifica,
                            _formatear_estructura_presupuestaria(RE.id_accion_subespecifica) as estructura_presupuestaria
                          FROM
                            modulo_base.comprobante_tiene_item as CTI,
                            modulo_base.item as I,
                            modulo_base.comprobante_tiene_requisicion_externa as CTRE,
                            modulo_base.requisicion_externa_tiene_item as RETI,
                            modulo_base.requisicion_externa as RE,
                            modulo_base.cuenta_presupuestaria as CP
                          WHERE
                            CTI.id_comprobante='$id_comprobante' and
                            CTI.id_item=I.id and
                            CTRE.id_comprobante=CTI.id_comprobante and
                            RETI.id_requisicion_externa=CTRE.id_requisicion_externa and
                            RETI.id_item=CTI.id_item and
                            RE.id=RETI.id_requisicion_externa and
                            CP.id_cuenta_presupuestaria=I.id_cuenta_presupuestaria
                            ");
    
    //buscar cargos
    $cargos=$db->Execute("SELECT
                            C.formula,
                            C.iva,
                            C.id_cuenta_presupuestaria,
                            _formatear_cuenta_presupuestaria(C.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                            CP.denominacion as denominacion_cuenta_presupuestaria,
                            CTC.monto
                          FROM
                            modulo_base.comprobante_tiene_cargo as CTC,
                            modulo_base.cargo as C,
                            modulo_base.cuenta_presupuestaria as CP
                          WHERE
                            CTC.id_comprobante='$id_comprobante' and
                            CTC.id_cargo=C.id and
                            CP.id_cuenta_presupuestaria=C.id_cuenta_presupuestaria
                            ");
    
    //para cada articulo, buscar de que requisicon externa proviene
    $sub_total=0;
    $sub_total_iva=0;
    $K=0;
    $retorno=array();
    for($i=0;$i<count($items);$i++){
      $descuento_p=0;
      $descuento_m=0;
      if($items[$i]["descuento"]){
        $descuento=json_decode($items[$i]["descuento"],true);
        $descuento_p=$descuento["porcentaje"];
        $descuento_m=$descuento["monto"];
      }
      $sub_total_item=round($items[$i]["cantidad"]*$items[$i]["costo"],2);
      $descuento=round(($descuento_p*$sub_total_item)/100,2)+$descuento_m;
      $total_item=round($sub_total_item-$descuento,2);
      $sub_total+=$total_item;
      if($items[$i]["aplica_iva"]=='t'){
        $sub_total_iva+=$total_item;
      }
      $items[$i]["total"]=$total_item;
      $retorno[$K]=array();
      $retorno[$K]["id_accion_subespecifica"]=$items[$i]["id_accion_subespecifica"];
      $retorno[$K]["estructura_presupuestaria"]=$items[$i]["estructura_presupuestaria"];
      $retorno[$K]["id_cuenta_presupuestaria"]=$items[$i]["id_cuenta_presupuestaria"];
      $retorno[$K]["cuenta_presupuestaria"]=$items[$i]["cuenta_presupuestaria"];
      $retorno[$K]["denominacion_cuenta_presupuestaria"]=$items[$i]["denominacion_cuenta_presupuestaria"];
      $retorno[$K]["total"]=$items[$i]["total"];
      $K++;
    }
    
    //buscar descuento
    $descuento_p=0;
    $descuento_m=0;
    $descuento=$db->Execute("SELECT valor FROM modulo_base.comprobante_datos WHERE id_comprobante='$id_comprobante' AND dato='descuento_porcentaje'");
    if(isset($descuento[0][0]))
      $descuento_p=$descuento[0][0];
    $descuento=$db->Execute("SELECT valor FROM modulo_base.comprobante_datos WHERE id_comprobante='$id_comprobante' AND dato='descuento_monto'");
    if(isset($descuento[0][0]))
      $descuento_m=$descuento[0][0];
    
    $total=0;
    $total_iva=0;
    for($i=0;$i<count($cargos);$i++){
      $evaluar=str_replace("MONTO","$sub_total_iva",$cargos[$i]["formula"]);
      eval("\$temporal=$evaluar;");
      $iva=(round($temporal,2)+$cargos[$i]["monto"]);
      $total_iva+=$iva;
      $cargos[$i]["total"]=$iva;
      
      $retorno[$K]=array();
      $retorno[$K]["id_accion_subespecifica"]=$items[0]["id_accion_subespecifica"];
      $retorno[$K]["estructura_presupuestaria"]=$items[0]["estructura_presupuestaria"];
      $retorno[$K]["id_cuenta_presupuestaria"]=$cargos[$i]["id_cuenta_presupuestaria"];
      $retorno[$K]["cuenta_presupuestaria"]=$cargos[$i]["cuenta_presupuestaria"];
      $retorno[$K]["denominacion_cuenta_presupuestaria"]=$cargos[$i]["denominacion_cuenta_presupuestaria"];
      $retorno[$K]["total"]=$cargos[$i]["total"];
      $K++;
    }
    
    //$total=$sub_total+$total_iva;
    
    return $retorno;
  }
}

MODULO::onInit();
?>
