<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/dias_meses.php");
include_once("../library/functions/letra_dia_semana.php");
include_once("../library/functions/letra_mes.php");

include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");


function arrayRangoFecha($fechaInicio, $fechaFin){
  $arrayFechas=array();
  $fechaMostrar = $fechaInicio;
  while(strtotime($fechaMostrar) <= strtotime($fechaFin)){
    $arrayFechas[]=$fechaMostrar;
    $fechaMostrar = date("Y-m-d", strtotime($fechaMostrar . " + 1 day"));
    }
  return $arrayFechas;
} 




$mes=date("m");
if(isset($_REQUEST["mes"]))
  $mes=$_REQUEST["mes"];

$anio=date("Y");
if(isset($_REQUEST["anio"]))
  $anio=$_REQUEST["anio"];

$id_ficha=-1;
if(isset($_REQUEST["id_ficha"]))
  $id_ficha=$_REQUEST["id_ficha"];

//$id_persona=-1;
//$mes="05";
//$anio=2014;


$dias_mes=dias_meses($anio);

$add_id_ficha="";
if($id_ficha>0)
  $add_id_ficha="F.id='$id_ficha' AND";
else if($id_ficha=="")
  $add_id_ficha="F.id='$id_ficha' AND";

$personal=$db->Execute("SELECT
                          *
                        FROM
                          modulo_nomina.ficha as F,
                          modulo_base.persona as P
                        WHERE
                          $add_id_ficha
                          P.id=F.id_persona AND
                          F.activo
                        ORDER BY
                          P.denominacion");

class PDF extends FPDF{
  function Header(){
    global $organismo;
    
    $this->Image(SIGA::databasePath()."/config/plantilla_horizontal.jpg",0,0,280);
    
    global $mes, $anio;
    $this->SetFont('helvetica','B',12);
    $this->MultiCell(260,4,utf8_decode("REGISTRO DE ASISTENCIA"),'','C');
    $this->SetFont('helvetica','B',10);
    $this->MultiCell(260,4,utf8_decode(strtoupper(letra_mes($mes))." ".$anio),'','C');
    
    $this->SetFont('helvetica','',7);
    $this->SetY(200);
    
    
    $this->SetFillColor(83,145,215);
    $this->Cell(3,3,utf8_decode(""),'',0,'L',1);
    $this->SetFillColor(255,255,255);
    $this->Cell(100,3,utf8_decode("ENTRADAS ANTES DE 7:50AM Y SALIDAS DESPUES DE 4:40PM."),'',1,'L',1);
    
    $this->SetFillColor(0,0,0);
    $this->Cell(3,3,utf8_decode(""),'',0,'L',1);
    $this->SetFillColor(255,255,255);
    $this->Cell(100,3,utf8_decode("ENTRADAS Y SALIDAS NORMALES (ENTRE 7:50AM-8:10AM Y 4:20PM-4:40PM)."),'',1,'L',1);
    
    $this->SetFillColor(255,0,0);
    $this->Cell(3,3,utf8_decode(""),'',0,'L',1);
    $this->SetFillColor(255,255,255);    
    $this->Cell(100,3,utf8_decode("ENTRADAS TARDES (MAYOR A 8:10AM) Y SALIDAS ANTES DE LA HORA (MENOR A 4:20PM)."),'',1,'L',1);
    
    
    
    $this->SetY(35);
  }
}
/*
$hora_a=strtotime("07:55:00 AM");
$hora_b=strtotime("08:05:00 AM");
$hora_c=strtotime("04:25:00 PM");
$hora_d=strtotime("04:35:00 PM");
*/
$hora_a=strtotime("07:50:00 AM");
$hora_b=strtotime("08:10:00 AM");
$hora_c=strtotime("04:20:00 PM");
$hora_d=strtotime("04:40:00 PM");

$pdf = new PDF("L","mm","Letter");

$pdf->SetAutoPageBreak(true,15);
$pdf->SetTopMargin(25);
$pdf->SetFillColor(255,255,255);

$pdf->AddPage("L");





$alto_fila=4;
$ancho_col_1=40;
$ancho_col_2=6.8;
$ancho_col_3=2.5;

$pdf->SetFillColor(218,218,218);

$pdf->SetFont('helvetica','B',7);
$pdf->Cell($ancho_col_1,4,utf8_decode("NOMBRE / APELLIDO"),'LRT',0,'L',1);

$pdf->SetFont('helvetica','',4);
for($j=1;$j<=$dias_mes[$mes-1];$j++){
  $fecha="$anio-$mes-".($j<10?"0$j":"$j");  
  $pdf->Cell($ancho_col_2,2,utf8_decode(letra_dia_semana(date("N",strtotime("$fecha")))),'LRT',0,'C',1);
}
$pdf->SetFont('helvetica','B',4);
$pdf->Cell($ancho_col_3*3,2,utf8_decode("TOTAL"),'LRT',0,'C',1);

$pdf->Ln(2);



$pdf->SetFont('helvetica','B',7);
$pdf->SetX($pdf->GetX()+$ancho_col_1);
for($j=1;$j<=$dias_mes[$mes-1];$j++)
  $pdf->Cell($ancho_col_2,2,utf8_decode("$j"),'LRB',0,'C',1);
$pdf->SetFont('helvetica','B',3);

$pdf->Cell($ancho_col_3*3,1,utf8_decode("ENTRADAS"),'LR',0,'C',1);
$pdf->SetXY($pdf->GetX()-$ancho_col_3*3,$pdf->GetY()+1);
$pdf->Cell($ancho_col_3*3,1,utf8_decode("SALIDAS"),'LRB',0,'C',1);


$pdf->Ln(1);





$pdf->SetFillColor(255,255,255);
for($i=0;$i<count($personal);$i++){
  $pdf->SetTextColor(0,0,0);
  $tmp=explode(";",$personal[$i]["denominacion"]);
  $nombre_apellido=$tmp[0]." ".$tmp[2];
  $cedula=$personal[$i]["identificacion_tipo"]."-".number_format($personal[$i]["identificacion_numero"],0,"",".");
  $idp=$personal[$i]["id_persona"];
  
  $asistencia=$db->Execute("SELECT
                                      fecha,
                                      hora,
                                      manual
                                    FROM
                                      modulo_asistencia.asistencia
                                    WHERE
                                      id_persona='$idp' AND
                                      fecha between '$anio-$mes-01' and '$anio-$mes-".$dias_mes[$mes-1]."'
                                    ORDER BY
                                      fecha,
                                      hora");
    
  $pdf->SetFont('helvetica','',8);
  $pdf->Cell($ancho_col_1,$alto_fila,utf8_decode("$nombre_apellido"),'LRTB',0,'L',1);
  
  $suma_e_rojo=0;
  $suma_e_azul=0;
  
  $suma_s_rojo=0;
  $suma_s_azul=0;
  
  $suma_e_normal=0;
  $suma_s_normal=0;
  
  for($j=1;$j<=$dias_mes[$mes-1];$j++){
    
    $fecha="$anio-$mes-".($j<10?"0$j":"$j");
    //buscar fecha en $asistencia_dia
    $horas=array();
    $c=0;
    for($k=0;$k<count($asistencia);$k++){
      if($fecha==$asistencia[$k]["fecha"]){
        
        $timestamp=strtotime($asistencia[$k]["hora"]);
        if($timestamp){//si es valida la hora
          $horas[$c]["timestamp"]=$timestamp;
          $c++;
        }   
      }
    }
    
    $hora_e="";
    $hora_s="";
    
    if(isset($horas[0]["timestamp"]))
      $hora_e=date("h:ia",$horas[0]["timestamp"]);
    if(isset($horas[$c-1]["timestamp"]) and $c>1)
      $hora_s=date("h:ia",$horas[$c-1]["timestamp"]);
    
    
    
    
      
    $pdf->SetFont('helvetica','',4);
    
    $x=$pdf->GetX();
    $y=$pdf->GetY();
    if($hora_e){
      if(strtotime($hora_e)>$hora_b){
        $pdf->SetTextColor(255,0,0);
        $suma_e_rojo++;
      }
      else if(strtotime($hora_e)<$hora_a){
        $pdf->SetTextColor(83,145,215);
        $suma_e_azul++;
      }
      else{
        $suma_e_normal++;
      }
    }
    
    $pdf->Cell($ancho_col_2,$alto_fila/2,utf8_decode("$hora_e"),'LRT',0,'C',1);
    
    $pdf->SetTextColor(0,0,0);
    if($hora_s){
      if(strtotime($hora_s)<$hora_c){
        $pdf->SetTextColor(255,0,0);
        $suma_s_rojo++;
      }
      else if(strtotime($hora_s)>$hora_d){
        $pdf->SetTextColor(83,145,215);
        $suma_s_azul++;
      }
      else{
        $suma_s_normal++;
      }
    }
    
    $pdf->SetXY($x,$y+$alto_fila/2);
    $pdf->Cell($ancho_col_2,$alto_fila/2,utf8_decode("$hora_s"),'LRB',0,'C',1);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetXY($x+$ancho_col_2,$y);

  }
  
  
  $x=$pdf->GetX();
  $y=$pdf->GetY();
  $pdf->SetTextColor(255,0,0);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_e_rojo"),'LRT',0,'C',1);
  $pdf->SetXY($x,$y+$alto_fila/2);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_s_rojo"),'LRB',0,'C',1);
  $pdf->SetXY($x+$ancho_col_3,$y);
  
  $x=$pdf->GetX();
  $y=$pdf->GetY();
  $pdf->SetTextColor(83,145,215);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_e_azul"),'LRT',0,'C',1);
  $pdf->SetXY($x,$y+$alto_fila/2);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_s_azul"),'LRB',0,'C',1);
  $pdf->SetXY($x+$ancho_col_3,$y);
  
  $x=$pdf->GetX();
  $y=$pdf->GetY();
  $pdf->SetTextColor(0,0,0);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_e_normal"),'LRT',0,'C',1);
  $pdf->SetXY($x,$y+$alto_fila/2);
  $pdf->Cell($ancho_col_3,$alto_fila/2,utf8_decode("$suma_s_normal"),'LRB',0,'C',1);
  $pdf->SetXY($x+$ancho_col_3,$y);
  
  
  
  $pdf->Ln($alto_fila);
  
  
}


$pdf->Output();

