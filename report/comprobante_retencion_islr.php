<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/unformatDate.php");
include_once("../library/functions/str_clear.php");
include_once("../library/fpdf/1.84/rotation.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT
                            P.identificacion_tipo||P.identificacion_numero as identificacion,
                            P.identificacion_tipo,
                            P.identificacion_numero,
                            P.denominacion,
                            P.direccion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");

function formatear_rif($tipo,$numero){
  $len=strlen($numero)-1;
  return $tipo."-".str_pad(substr($numero,0,$len),8, "0", STR_PAD_LEFT )."-".$numero[$len];
}

$id=str_clear(isset($_GET["id"])?$_GET["id"]:"");
if($id){
  $ids=array($id);  
}
else{
  $ids=str_clear(isset($_GET["ids"])?$_GET["ids"]:"");
  if($ids){
    $ids=explode(",",str_clear($_GET["ids"]));
  }
  else{    
    $fecha_inicio=SIGA::paramGet("fecha_inicio");
    $fecha_culminacion=SIGA::paramGet("fecha_culminacion");
    $id_persona=SIGA::paramGet("id_persona");
    $add="";
    if($id_persona)
      $add=" AND id_persona=$id_persona";
  
    $ids_tmp=$db->Execute("SELECT
                              id
                            FROM
                              modulo_base.retencion_comprobante
                            WHERE
                              id_retencion_tipo=2 and
                              not id_persona is null and
                              fecha BETWEEN '".unformatDate($fecha_inicio)."' AND '".unformatDate($fecha_culminacion)."'
                              $add
                            ORDER BY
                              to_char(fecha,'YYYY-MM') || '-' || lpad(text(numero),8,'0')
                            ");
    if(count($ids_tmp)==0){
      print "No se encontraron datos.";
      exit;
    }
    
    $ids=array();
    for($i=0;$i<count($ids_tmp);$i++)
      $ids[$i]=$ids_tmp[$i][0];
  }
}


$denominacion_ente=$organismo[0]["denominacion"];
$rif_ente=formatear_rif($organismo[0]["identificacion_tipo"],$organismo[0]["identificacion_numero"]);
$direccion_ente=$organismo[0]["direccion"];
$ciudad_ente="";
$estado_ente="";
$codigo_postal_ente="";








//require('../../library/fpdf/rotation.php');
$pdf=new PDF_Rotate("L","mm","letter");

$pdf->SetFont('helvetica','',8);
$pdf->SetFillColor(255,255,255);




$MARGEN_LEFT=20;
$MARGEN_TOP=15;
$ANCHO=240;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);



for($N=0;$N<count($ids);$N++):
  $id=$ids[$N];
  

  $COMPROBANTE=$db->Execute("SELECT
						RC.fecha,						
						lpad(text(RC.numero),8,'0') as numero,
						RT.denominacion as tipo,
            P.identificacion_tipo,
            P.identificacion_numero,
						(case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as persona_identificacion,
            replace(P.denominacion,';',' ') as persona_nombre
				FROM modulo_base.retencion_comprobante as RC, modulo_base.persona as P, modulo_base.retencion_tipo as RT				
				WHERE RC.id='$id' AND RC.id_persona=P.id AND RC.id_retencion_tipo=RT.id");
  
  

  $DOC=$db->Execute("SELECT
              F.id,
              F.fecha,
              F.numero_factura,
              F.numero_control,
              F.total,
              F.informacion_islr[1] as monto_base_islr,
              F.informacion_islr[2] as porcentaje_islr,
              F.informacion_islr[3] as monto_islr,
              F.informacion_islr[4] as monto_retencion_islr
            FROM
              modulo_base.factura as F,
              modulo_base.retencion_comprobante_tiene_factura as RCTF
            WHERE
              F.id=RCTF.id_factura AND RCTF.id_retencion_comprobante='$id'
            ORDER BY
              F.fecha");
  
  $pdf->AddPage();
  $pdf->Image(SIGA::databasePath()."/config/plantilla_horizontal.jpg",0,0,280);
  
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+21);
  $pdf->SetFont('helvetica','B',12);
  $pdf->Cell($ANCHO,3,utf8_decode('COMPROBANTE DE RETENCIÓN IMPUESTO SOBRE LA RENTA (ISLR)'),'',0,'C',0);
  
  
  //BORDE Y FONDO CABECERA TABLA
  $pdf->SetLineWidth(0.3);
  $pdf->SetFillColor(220,220,220);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25);
  $pdf->Cell($ANCHO,10,'','LRTB',1,'',1);
  $pdf->Cell($ANCHO,12,'','LRTB',1,'',0);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25);
  
  
  
  
  
  
  //FILA 1(1)
  $pdf->SetLineWidth(0.2);
  $pdf->SetFont('helvetica','B',5);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25);
  $pdf->Cell(65,3,utf8_decode(' NOMBRE O RAZÓN SOCIAL DEL AGENTE DE RETENCIÓN'),'R',0,'',0);
  $pdf->Cell(30,3,utf8_decode(' R.I.F. DEL AGENTE DE RET.'),'R',0,'',0);
  $pdf->Cell($ANCHO-(65+30),3,utf8_decode(' DIRECCIÓN FISCAL DEL AGENTE DE RETENCIÓN'),'',1,'',0);
  
  //FILA 1(2)
  $pdf->SetFont('helvetica','',8);
  $pdf->Cell(65,7,utf8_decode($denominacion_ente),'R',0,'C',0);
  $pdf->Cell(30,7,utf8_decode($rif_ente),'LR',0,'C',0);
  $pdf->SetFont('helvetica','',7);
  $pdf->MultiCell($ANCHO-(65+30),3,utf8_decode("".$direccion_ente."."),'L','L',0);
  
  $pdf->SetXY($MARGEN_LEFT+65+30,$MARGEN_TOP+25+3+3);
  $pdf->Cell($ANCHO-(65+30),4,utf8_decode(trim(" $ciudad_ente $codigo_postal_ente.",".")),'',1,'L',0);
  
  //FILA 2 (vacia)
  $pdf->Cell($ANCHO,4,'','B',1,'',0);
  
  //FILA 3
  $pdf->SetFont('helvetica','B',7);
  $pdf->Cell(25,4,utf8_decode('PERIODO FISCAL'),'RTB',0,'',0);
  $pdf->Cell(100,4,utf8_decode('NOMBRE O RAZÓN SOCIAL DEL SUJETO RETENIDO:'),'LTB',0,'',0);
  $pdf->SetFont('helvetica','',7);
  $pdf->Cell($ANCHO-(25+100+20+30),4,utf8_decode($COMPROBANTE[0]["persona_nombre"]."      "),'RTB',0,'R',0);
  $pdf->SetFont('helvetica','B',7);
  $pdf->Cell(20,4,utf8_decode('FECHA'),'LRTB',0,'C',0);
  $pdf->Cell(30,4,utf8_decode('Nº COMPROBANTE'),'LRTB',1,'C',0);
  
  //FILA 4
  $fecha_comprobante=explode("-",$COMPROBANTE[0]["fecha"]);
  $pdf->SetFont('helvetica','',7);
  $pdf->Cell(25,4,utf8_decode($fecha_comprobante[0]."/".$fecha_comprobante[1]),'RTB',0,'C',0);
  $pdf->SetFont('helvetica','B',7);
  $pdf->Cell(100,4,utf8_decode('REGISTRO DE INFORMACIÓN FISCAL DEL SUJETO RETENIDO (R.I.F.):'),'LTB',0,'',0);
  $pdf->SetFont('helvetica','',7);
  $pdf->Cell($ANCHO-(25+100+20+30),4,utf8_decode(formatear_rif($COMPROBANTE[0]["identificacion_tipo"],$COMPROBANTE[0]["identificacion_numero"])."      "),'RTB',0,'R',0);
  $pdf->Cell(20,4,utf8_decode($fecha_comprobante[2]."/".$fecha_comprobante[1]."/".$fecha_comprobante[0]),'LRTB',0,'C',0);
  $pdf->Cell(30,4,utf8_decode($fecha_comprobante[0]."".$fecha_comprobante[1]."".$COMPROBANTE[0]["numero"]),'LRTB',1,'C',0);
  
  
  
  
  
  
  
  
  
  //espacio entre tablas
  $pdf->Cell($ANCHO,4,'','',1,'',0);
  $pdf->Cell($ANCHO,4,'','',1,'',0);
  
  //BORDE Y FONDO CABECERA
  $pdf->SetLineWidth(0.3);
  $pdf->SetFillColor(220,220,220);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4);
  $pdf->Cell(8+17*2+18+11*4+20+11+20*2,9,'','LRTB',1,'',1);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4);
  $pdf->SetFillColor(255,255,255);
  
  
  //tabla movimientos
  $pdf->SetLineWidth(0.2);
  $pdf->SetFont('helvetica','B',5);
  
  
  //CABECERA TABLA P1
  $pdf->Cell(8,3,utf8_decode("OPER."),'R',0,'C',0);
  $pdf->Cell(17,3,utf8_decode("FECHA DE LA"),'LR',0,'C',0);
  $pdf->Cell(17,3,utf8_decode("NÚMERO"),'LR',0,'C',0);
  $pdf->Cell(18,3,utf8_decode("Nº CONTROL"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("Nº NOTA"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("Nº NOTA"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("TIPO DE"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("Nº FACT."),'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("TOTAL"),'LR',0,'C',0);
  //$pdf->Cell(15,3,"COMPRA SIN",'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("BASE"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("%"),'LR',0,'C',0);
  //$pdf->Cell(20,3,"IMPUESTO",'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("ISLR"),'',1,'C',0);
  
  //CABECERA TABLA P2
  $pdf->Cell(8,3,utf8_decode("Nº"),'R',0,'C',0);
  $pdf->Cell(17,3,utf8_decode("FACTURA"),'LR',0,'C',0);
  $pdf->Cell(17,3,utf8_decode("DE FACTURA"),'LR',0,'C',0);
  $pdf->Cell(18,3,utf8_decode("DE FACTURA"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("DE DEBITO"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("DE CREDITO"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("TRANSAC."),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("AFECTADA"),'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("FACTURA"),'LR',0,'C',0);
  //$pdf->Cell(15,3,"DERECHO A",'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("IMPONIBLE"),'LR',0,'C',0);
  $pdf->Cell(11,3,utf8_decode("RETENIDO"),'LR',0,'C',0);
  //$pdf->Cell(20,3,"IVA",'LR',0,'C',0);
  $pdf->Cell(20,3,utf8_decode("RETENIDO"),'',1,'C',0);
  
  //CABECERA TABLA P3
  $pdf->Cell(8,3,"",'R',0,'C',0);
  $pdf->Cell(17,3,"",'LR',0,'C',0);
  $pdf->Cell(17,3,"",'LR',0,'C',0);
  $pdf->Cell(18,3,"",'LR',0,'C',0);
  $pdf->Cell(11,3,"",'LR',0,'C',0);
  $pdf->Cell(11,3,"",'LR',0,'C',0);
  $pdf->Cell(11,3,"",'LR',0,'C',0);
  $pdf->Cell(11,3,"",'LR',0,'C',0);
  $pdf->Cell(20,3,"",'LR',0,'C',0);
  //$pdf->Cell(15,3,"CREDITO IVA",'LR',0,'C',0);
  $pdf->Cell(20,3,"",'LR',0,'C',0);
  $pdf->Cell(11,3,"",'LR',0,'C',0);
  //$pdf->Cell(20,3,"",'LR',0,'C',0);
  $pdf->Cell(20,3,"",'',1,'C',0);
  
  
  
  $pdf->Cell(8,1,"",'R',0,'C',0);
  $pdf->Cell(17,1,"",'LR',0,'C',0);
  $pdf->Cell(17,1,"",'LR',0,'C',0);
  $pdf->Cell(18,1,"",'LR',0,'C',0);
  $pdf->Cell(11,1,"",'LR',0,'C',0);
  $pdf->Cell(11,1,"",'LR',0,'C',0);
  $pdf->Cell(11,1,"",'LR',0,'C',0);
  $pdf->Cell(11,1,"",'LR',0,'C',0);
  $pdf->Cell(20,1,"",'LR',0,'C',0);
  //$pdf->Cell(15,1,"",'LR',0,'C',0);
  $pdf->Cell(20,1,"",'LR',0,'C',0);
  $pdf->Cell(11,1,"",'LR',0,'C',0);
  //$pdf->Cell(20,1,"",'LR',0,'C',0);
  $pdf->Cell(20,1,"",'',1,'C',0);
  
  
  
  $pdf->SetLineWidth(0.3);
  // $pdf->SetXY($MARGEN_LEFT+8+17*2+18+11*4,$MARGEN_TOP+25+10+12+4);
  // $pdf->SetFillColor(220,220,220);
  // $pdf->Cell(20+15+20+11+20,4,'COMPRAS INTERNAS O IMPORTACIONES','LRTB',1,'C',1);
  // $pdf->SetFillColor(255,255,255);
  
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4+9);
  $pdf->Cell(8+17*2+18+11*4+20+11+20*2,15*3,'','LRTB',1,'',0);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4+9);
  $pdf->SetLineWidth(0.2);
  
  
  $pdf->SetFont('helvetica','',7.5);
  $k=0;
  $SUMA_TOTAL=0;
  $SUMA_BI=0;
  $SUMA_IVA=0;
  $SUMA_IVA_RET=0;
  for(;$k<count($DOC);$k++){
    $pdf->Cell(8,3,($k+1),'R',0,'C',0);
    $pdf->Cell(17,3,formatDate($DOC[$k]["fecha"]),'LR',0,'C',0);
    $pdf->Cell(17,3,$DOC[$k]["numero_factura"],'LR',0,'C',0);
    $pdf->Cell(18,3,$DOC[$k]["numero_control"],'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"01",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(20,3,number_format($DOC[$k]["total"],2,",","."),'LR',0,'R',0);
    //$pdf->Cell(15,3,"",'LR',0,'C',0);
    $pdf->Cell(20,3,number_format($DOC[$k]["monto_base_islr"],2,",","."),'LR',0,'R',0);
    $pdf->Cell(11,3,number_format($DOC[$k]["porcentaje_islr"],2,",","."),'LR',0,'C',0);
    //$pdf->Cell(20,3,'','LR',0,'R',0);
    $pdf->Cell(20,3,number_format($DOC[$k]["monto_retencion_islr"],2,",","."),'',1,'R',0);
    $SUMA_TOTAL+=$DOC[$k]["total"];
    $SUMA_BI+=$DOC[$k]["monto_base_islr"];
    $SUMA_IVA+=$DOC[$k]["porcentaje_islr"];
    $SUMA_IVA_RET+=$DOC[$k]["monto_retencion_islr"];
    }
  
  
  for($i=$k;$i<15;$i++){
    $pdf->Cell(8,3,"",'R',0,'C',0);
    $pdf->Cell(17,3,"",'LR',0,'C',0);
    $pdf->Cell(17,3,"",'LR',0,'C',0);
    $pdf->Cell(18,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    $pdf->Cell(20,3,"",'LR',0,'C',0);
    //$pdf->Cell(15,3,"",'LR',0,'C',0);
    $pdf->Cell(20,3,"",'LR',0,'C',0);
    $pdf->Cell(11,3,"",'LR',0,'C',0);
    //$pdf->Cell(20,3,"",'LR',0,'C',0);
    $pdf->Cell(20,3,"",'',1,'C',0);
    }
  
  
  
  $pdf->SetLineWidth(0.3);
  $pdf->SetFillColor(220,220,220);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4+9+15*3);
  $pdf->Cell(8+17*2+18+11*4+20+11+20*2,4,'','LRTB',1,'',1);
  $pdf->SetXY($MARGEN_LEFT,$MARGEN_TOP+25+10+12+4+4+9+15*3);
  $pdf->SetLineWidth(0.2);
  
  $pdf->SetFont('helvetica','B',7.5);
  $pdf->Cell(8,4,"",'R',0,'C',0);
  $pdf->Cell(17,4,"",'LR',0,'C',0);
  $pdf->Cell(17,4,"",'LR',0,'C',0);
  $pdf->Cell(18,4,"",'LR',0,'C',0);
  $pdf->Cell(11,4,"",'LR',0,'C',0);
  $pdf->Cell(11,4,"",'LR',0,'C',0);
  $pdf->Cell(11,4,"",'LR',0,'C',0);
  $pdf->Cell(11,4,"",'LR',0,'C',0);
  $pdf->Cell(20,4,utf8_decode(number_format($SUMA_TOTAL,2,",",".")),'LR',0,'R',0);
  //$pdf->Cell(15,4,"",'LR',0,'C',0);
  $pdf->Cell(20,4,utf8_decode(number_format($SUMA_BI,2,",",".")),'LR',0,'R',0);
  $pdf->Cell(11,4,"",'LR',0,'C',0);
  //$pdf->Cell(20,4,number_format($SUMA_IVA,2,",","."),'LR',0,'R',0);
  $pdf->Cell(20,4,utf8_decode(number_format($SUMA_IVA_RET,2,",",".")),'',1,'R',0);
  
  
  
  
  $pdf->SetFont('helvetica','',7.5);
  $pdf->SetXY($MARGEN_LEFT+$ANCHO/2-(45+4),$MARGEN_TOP+130);
  $pdf->Cell(45,5,utf8_decode("ELABORADO POR:"),'LRTB',0,'C',0);
  $pdf->Cell(4,5,"",'',0,'C',0);
  $pdf->Cell(45,5,utf8_decode("REVISADO POR:"),'LRTB',1,'C',0);
  
  
  $pdf->SetXY($MARGEN_LEFT+$ANCHO/2-(45+4),$MARGEN_TOP+130+5);
  $pdf->Cell(45,40,"",'LRTB',0,'C',0);
  $pdf->Cell(4,40,"",'',0,'C',0);
  $pdf->Cell(45,40,"",'LRTB',1,'C',0);
  
  $pdf->SetXY($MARGEN_LEFT+$ANCHO/2-(45+4),$MARGEN_TOP+130+5+40);
  $pdf->Cell(45,5,utf8_decode("ADMINISTRACIÓN"),'LRTB',0,'C',0);
  $pdf->Cell(4,5,"",'',0,'C',0);
  $pdf->Cell(45,5,utf8_decode("ADMINISTRACIÓN"),'LRTB',1,'C',0);
  
  
  
  $pos_x=240;
  $pos_y=127;
  $pdf->SetFont('helvetica','B',28);
  $pdf->Rotate(90,$pos_x,$pos_y);
  $pdf->SetXY($pos_x,$pos_y);
  $pdf->Cell(19*3,20,utf8_decode('ORIGINAL'),'',1,'C',0);

endfor;

$aux=$fecha_comprobante[0]."-".$fecha_comprobante[1]."-".$COMPROBANTE[0]["numero"];
$pdf->Output("comprobante_retencion_islr_$aux.pdf","I");


?>