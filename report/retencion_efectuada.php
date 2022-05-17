<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();


$id_persona=SIGA::paramGet("id_persona");
$add="";
if($id_persona)
  $add=" AND P.id=$id_persona";
$tipo=SIGA::paramGet("tipo");
$fecha_inicio=SIGA::paramGet("fecha_inicio");
$fecha_culminacion=SIGA::paramGet("fecha_culminacion");

$tipo_col="";
if($tipo==1) $tipo_col="iva";
else if($tipo==2) $tipo_col="islr";
if($tipo_col=="") exit;



$CONSULTA=$db->Execute("select
                          P.identificacion_tipo || '-' || lpad(text(P.identificacion_numero),9,'0') as identificacion,
                          replace(P.denominacion,';',' ') as persona,
                          to_char(C.fecha,'DD/MM/YYYY') as fecha,
                          lpad(text(C.correlativo),10,'0') as correlativo,
                          (select sum(DC.monto) from modulo_base.detalle_contable as DC where DC.id_comprobante=C.id and DC.operacion='H') as total,
                          CTR.monto as retencion
                        from
                          modulo_base.persona as P,
                          modulo_base.comprobante as C,
                          modulo_base.comprobante_tiene_retencion as CTR,
                          modulo_base.retencion as R
                        where
                          P.id=C.id_persona AND
                          C.tipo='OP' AND                          
                          C.fecha BETWEEN '".unformatDate($fecha_inicio)."' AND '".unformatDate($fecha_culminacion)."' AND
                          C.id=CTR.id_comprobante AND
                          CTR.id_retencion=R.id AND
                          R.id_retencion_tipo=$tipo
                          $add
                        order by
                          C.correlativo
                        ");


if(count($CONSULTA)==0){
  print "No se encontraron datos.";
  exit;
}

$MARGEN_LEFT=10;
$MARGEN_TOP=5;
$ANCHO=195;

$TITULO_PRINCIPAL="LISTADO DE RETENCIONES EFECTUADAS (".strtoupper($tipo_col).")";
$TITULO_SECUNDARIO="DEL $fecha_inicio AL $fecha_culminacion";


$t_n=5;
$t_rif=25;
$t_fecha=18;
$t_numero=20;
$t_total=18;
$t_retencion=18;
$t_persona=$ANCHO-($t_n+$t_rif+$t_fecha+$t_numero+$t_total+$t_retencion);






$CABECERA[0]=array(
                   array("id"=>"#","nombre"=>"Nº","ancho"=>$t_n,"alinear"=>"C"),
                   array("id"=>"identificacion","nombre"=>"RIF","ancho"=>$t_rif,"alinear"=>"L"),
                   array("id"=>"persona","nombre"=>"Proveedor","ancho"=>$t_persona,"alinear"=>"L"),
                   array("id"=>"fecha","nombre"=>"Fecha","ancho"=>$t_fecha,"alinear"=>"C"),
                   array("id"=>"correlativo","nombre"=>"Orden Pago","ancho"=>$t_numero,"alinear"=>"C"),                   
                   array("id"=>"total","nombre"=>"Total","ancho"=>$t_total,"alinear"=>"R","formato"=>"numerico"),
                   array("id"=>"retencion","nombre"=>"Retención","ancho"=>$t_retencion,"alinear"=>"R","formato"=>"numerico")
                  );


include("template/pdf_reporte_1.class.php");


$pdf=new PDF_REPORTE_1("P","mm","letter");
$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$pdf->PrintData();

$pdf->SetFont('helvetica','B',7.5);
$pdf->Cell($t_n+$t_rif+$t_persona+$t_fecha+$t_numero+$t_total,5,"","",0,"R");
$pdf->Cell($t_retencion,5,number_format($SUMA["retencion"],2,",","."),"",1,"R");



$pdf->AliasNbPages();
$pdf->Output("retenciones_efectuadas_$tipo_col.pdf","I");

?>