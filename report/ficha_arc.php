<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/fpdf/1.84/fpdf.php");
include_once("../class/nomina.class.php");

$db=SIGA::DBController();


$nacionalidad=SIGA::paramGet("nacionalidad");
$cedula=SIGA::paramGet("cedula");

if($nacionalidad and $cedula){
    $access=SIGA::access("ficha");
    if(!$access){
        header('Content-Type: text/html; charset=utf-8');
        print "No tiene acceso al módulo 'ficha'";
        exit;
    }
    $db=SIGA::DBController();
}
else{
    if(!SIGA::user()){
        header('Content-Type: text/html; charset=utf-8');
        print "Debe iniciar sesión para solicitar el reporte.";
        exit;
    }
    $db=SIGA::DBController();
    $sql="select
        identificacion_tipo,
        identificacion_numero
      from
        modulo_base.usuario as u,
        modulo_base.persona as p
      where
        u.usuario like '".SIGA::user()."' and
        u.id_persona_responsable=p.id";
    $persona=$db->Execute($sql);
    
    $nacionalidad=$persona[0]["identificacion_tipo"];
    $cedula=$persona[0]["identificacion_numero"];
}


$ficha=$db->Execute("SELECT F.id
                      FROM modulo_nomina.ficha as F, modulo_base.persona as P
                      WHERE F.id_persona=P.id AND P.identificacion_tipo='$nacionalidad' AND identificacion_numero='$cedula'");

if(!isset($ficha[0][0])){
  print "Error. No existen datos para la cédula: $nacionalidad-$cedula";
  exit;
}

$id_ficha=$ficha[0][0];
//$anio=2015;
$anio=SIGA::data();
if(isset($_GET['anio']))
    $anio=$_GET['anio'];

$config=[];
$config["reconversion_monetaria[2021]"]='{"fecha":"2021-09-30","monto":1000000.00}';


$reconvercion=false;
$reconvercion_fecha="";
$reconvercion_monto="";
if(isset($config["reconversion_monetaria[$anio]"]) and $config["reconversion_monetaria[$anio]"]){
	$tmp = json_decode($config["reconversion_monetaria[$anio]"],true);
	$reconvercion=true;
	$reconvercion_fecha=$tmp["fecha"];
	$reconvercion_monto=$tmp["monto"];
}


$mes=array(
             array("id"=>"1","denominacion"=>"ENERO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"2","denominacion"=>"FEBRERO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"3","denominacion"=>"MARZO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"4","denominacion"=>"ABRIL","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"5","denominacion"=>"MAYO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"6","denominacion"=>"JUNIO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"7","denominacion"=>"JULIO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"8","denominacion"=>"AGOSTO","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"9","denominacion"=>"SEPTIEMBRE","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"10","denominacion"=>"OCTUBRE","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"11","denominacion"=>"NOVIEMBRE","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
             array("id"=>"12","denominacion"=>"DICIEMBRE","asignaciones"=>0,"deducciones"=>0,"devengado"=>0,"SSO"=>0,"SPF"=>0,"LPH"=>0),
            );




$excluir=trim(SIGA::paramGet("excluir"),",");
$excluir=explode(",",$excluir);

$add="";


for($i=0;$i<count($mes);$i++):
  $result=$db->Execute("SELECT DISTINCT id_nomina, id_periodo, fecha_culminacion
                        FROM
                           modulo_nomina.ficha_concepto as FC,
                           modulo_nomina.periodo as P
                        WHERE
                           FC.id_ficha=$id_ficha AND
                           FC.id_periodo=P.id AND
                           $add
                           EXTRACT(MONTH FROM P.fecha_culminacion)=".$mes[$i]["id"]." AND EXTRACT(YEAR FROM P.fecha_culminacion)=$anio");
  $detalle=array();
  for($j=0;$j<count($result);$j++){
    $detalle=nomina::ficha_concepto($result[$j]["id_nomina"],$result[$j]["id_periodo"],$id_ficha);
    
    if($reconvercion and $reconvercion_fecha and $reconvercion_monto and $result[$j]["fecha_culminacion"]<=$reconvercion_fecha){
        
        for($k=0;$k<count($detalle["concepto"]);$k++){
            if(in_array($detalle["concepto"][$k]["identificador"],$excluir))
                continue;
            switch($detalle["concepto"][$k]["tipo"]){
                case "A":
                case "RD":
                    $mes[$i]["devengado"]+=$detalle["concepto"][$k]["valor_final"]/$reconvercion_monto;
                    break;
                case "D":
                case "AP":
                case "RA":
                    $mes[$i]["devengado"]-=$detalle["concepto"][$k]["valor_final"]/$reconvercion_monto;
                    break;
            }
        }
        
        //buscar y sumar los conceptos: SSO, SPF y LPH
        for($k=0;$k<count($detalle["concepto"]);$k++){ 
          switch($detalle["concepto"][$k]["identificador"]){
            case "SSO":          
              $mes[$i]["SSO"]+=$detalle["concepto"][$k]["valor_final"]/$reconvercion_monto;
              break;
            case "SPF":
              $mes[$i]["SPF"]+=$detalle["concepto"][$k]["valor_final"]/$reconvercion_monto;
              break;
            case "LPH":
              $mes[$i]["LPH"]+=$detalle["concepto"][$k]["valor_final"]/$reconvercion_monto;
              break;
          }
        }
    }
    else{
        
        for($k=0;$k<count($detalle["concepto"]);$k++){
            if(in_array($detalle["concepto"][$k]["identificador"],$excluir))
                continue;
            switch($detalle["concepto"][$k]["tipo"]){
                case "A":
                case "RD":
                    $mes[$i]["devengado"]+=$detalle["concepto"][$k]["valor_final"];
                    break;
                case "D":
                case "AP":
                case "RA":
                    $mes[$i]["devengado"]-=$detalle["concepto"][$k]["valor_final"];
                    break;
            }
        }
        
        //buscar y sumar los conceptos: SSO, SPF y LPH
        for($k=0;$k<count($detalle["concepto"]);$k++){ 
          switch($detalle["concepto"][$k]["identificador"]){
            case "SSO":          
              $mes[$i]["SSO"]+=$detalle["concepto"][$k]["valor_final"];
              break;
            case "SPF":
              $mes[$i]["SPF"]+=$detalle["concepto"][$k]["valor_final"];
              break;
            case "LPH":
              $mes[$i]["LPH"]+=$detalle["concepto"][$k]["valor_final"];
              break;
          }
        }
        
    }
  }


endfor;

$ficha=$db->Execute("SELECT
                        P.identificacion_tipo as nacionalidad,
                        P.identificacion_numero as cedula,
                        split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
                        replace(P.denominacion,';',' ') as nombres_apellidos
                      FROM
                        modulo_nomina.ficha AS F,
                        modulo_base.persona as P
                      WHERE
                        F.id_persona=P.id AND
                        F.id=$id_ficha");


$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion,
                                P.*
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");



$pdf=new FPDF("P","mm","letter");

$pdf->SetLeftMargin(15);
$pdf->SetTopMargin(10);
$pdf->SetAutoPageBreak(false,10);
$pdf->AddPage();

$y=$pdf->GetY();
$x=$pdf->GetX();
$ancho=180;

$pdf->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);
$pdf->Ln(15);

$pdf->SetFont('helvetica','B',14);
$pdf->Cell($ancho,5,utf8_decode('PLANILLA AR-C'),'',1,'C');

$pdf->SetFont('helvetica','B',11);
$pdf->Cell($ancho,8,utf8_decode("PERIODO: DEL 01/01/$anio AL 31/12/$anio"),'',1,'C');

$pdf->Ln(15);
$pdf->SetFont('helvetica','B',10);
$pdf->Cell(30,5,utf8_decode("CÉDULA: "),'',0,'L');
$pdf->SetFont('helvetica','',10);
$pdf->Cell(80,5,utf8_decode($ficha[0]["nacionalidad"]."-".number_format($ficha[0]["cedula"],0,"",".")."     ".$ficha[0]["nombres_apellidos"]),'',1,'L');

//ORGANISMOS
$pdf->Ln(2);
$pdf->Cell($ancho,1,utf8_decode(""),'T',1,'L');


$pdf->SetFont('helvetica','B',10);
$pdf->Cell(30,5,utf8_decode("ORGANISMO: "),'',0,'L');
$pdf->SetFont('helvetica','',10);
$pdf->Cell($ancho-30-10-30,5,utf8_decode($organismo[0]["denominacion"]),'',0,'L');

$pdf->SetFont('helvetica','B',10);
$pdf->Cell(10,5,utf8_decode("RIF: "),'',0,'L');
$pdf->SetFont('helvetica','',10);

$len=strlen($organismo[0]["identificacion_numero"])-1;
$organismo_rif=substr($organismo[0]["identificacion_numero"],0,$len)."-".$organismo[0]["identificacion_numero"][$len];

$pdf->Cell(30,5,utf8_decode($organismo[0]["identificacion_tipo"]."-".$organismo_rif),'',1,'R');

//DIRECCION ORGANISMO
$pdf->SetFont('helvetica','B',10);
$pdf->Cell(30,5,utf8_decode("DIRECCIÓN: "),'',0,'L');
$pdf->SetFont('helvetica','',10);
$pdf->MultiCell($ancho-30,5,utf8_decode(trim($organismo[0]["direccion"],".")."."),0,'J');

$pdf->Ln(1);
$pdf->Cell($ancho,1,utf8_decode(""),'T',1,'L');


$t=array(40,30,20,30,30,30);


//CABECERA TABLA
$pdf->Ln(10);
$y=$pdf->GetY();
$x=$pdf->GetX();
$pdf->SetFont('helvetica','B',9);
$pdf->Cell($t[0]-1,10,utf8_decode("MES"),'TB',0,'C');
$pdf->SetXY($x+$t[0],$y);$x=$pdf->GetX();
$pdf->MultiCell($t[1]-1,5,utf8_decode("DEVENGADO\nMENSUAL"),'TB','C');
$pdf->SetXY($x+$t[1],$y);$x=$pdf->GetX();
$pdf->Cell($t[2]-1,10,utf8_decode("% ISLR"),'TB',0,'C');
$pdf->SetXY($x+$t[2],$y);$x=$pdf->GetX();
$pdf->MultiCell($t[3]-1,5,utf8_decode("RETENCIÓN\nMENSUAL"),'TB','C');
$pdf->SetXY($x+$t[3],$y);$x=$pdf->GetX();
$pdf->MultiCell($t[4]-1,5,utf8_decode("ACUMULADO\nDEVENGADO"),'TB','C');
$pdf->SetXY($x+$t[4],$y);$x=$pdf->GetX();
$pdf->MultiCell($t[5],5,utf8_decode("ACUMULADO\nRETENCIÓN"),'TB','C');

//TABLA
$pdf->SetFont('helvetica','',10);
$acumulado=0;
$acumulado_SSO=0;
$acumulado_SPF=0;
$acumulado_LPH=0;
for($i=0;$i<count($mes);$i++):
  $acumulado+=$mes[$i]["devengado"];
  $acumulado_SSO+=$mes[$i]["SSO"];
  $acumulado_SPF+=$mes[$i]["SPF"];
  $acumulado_LPH+=$mes[$i]["LPH"];
  
  $pdf->Cell($t[0],8,utf8_decode($mes[$i]["denominacion"]),'',0,'L');
  $pdf->Cell($t[1],8,utf8_decode(number_format($mes[$i]["devengado"],2,",",".")),'',0,'R');
  $pdf->Cell($t[2],8,utf8_decode("0,00"),'',0,'R');
  $pdf->Cell($t[3],8,utf8_decode("0,00"),'',0,'R');
  $pdf->Cell($t[4],8,utf8_decode(number_format($acumulado,2,",",".")),'',0,'R');
  $pdf->Cell($t[5],8,utf8_decode("0,00"),'',0,'R');

  
  $pdf->Ln(8);
  
endfor;

$pdf->Cell($ancho,1,utf8_decode(""),'T',1,'L');

$pdf->Ln(5);

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(35,6,utf8_decode("ACUMULADO SSO:"),'',0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(20,6,utf8_decode(number_format($acumulado_SSO,2,",",".")),'',1,'R');

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(35,6,utf8_decode("ACUMULADO SPF:"),'',0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(20,6,utf8_decode(number_format($acumulado_SPF,2,",",".")),'',1,'R');

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(35,6,utf8_decode("ACUMULADO LPH:"),'',0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(20,6,utf8_decode(number_format($acumulado_LPH,2,",",".")),'',1,'R');


$pdf->Output("planilla_arc.pdf","I");

?>