<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/dias_meses.php");
include_once("../library/functions/letra_dia_semana.php");
include_once("../library/functions/letra_mes.php");

include_once("../library/fpdf/1.84/fpdf.php");

$db=SIGA::DBController();



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
    
    $this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,210);
    
    
    global $mes, $anio, $cedula, $nombre_apellido, $ancho_col_1, $ancho_col_2, $ancho_col_3, $ancho_col_4, $ancho_col_5, $ancho_col_6, $alto_fila;
    $this->SetFont('helvetica','B',12);
    $this->MultiCell(195,4,utf8_decode("REGISTRO DE ASISTENCIA"),'','C');
    $this->SetFont('helvetica','B',10);
    $this->MultiCell(195,4,utf8_decode(strtoupper(letra_mes($mes))." ".$anio),'','C');
    $this->Ln(1);
    $this->SetFont('helvetica','B',14);
    $this->Cell(195,4,utf8_decode($nombre_apellido),'',1,'L');
    $this->SetFont('helvetica','B',8);
    $this->Cell(195,4,utf8_decode($cedula),'',0,'L');
    $this->Ln(5);
    
    $this->SetFillColor(218,218,218);
    $this->SetFont('helvetica','B',8);
    $this->Cell($ancho_col_1+$ancho_col_2,$alto_fila,utf8_decode("DÍA"),'LRTB',0,'C',1);
    $this->Cell($ancho_col_3,$alto_fila,utf8_decode("HORA DE ENTRADA"),'LRTB',0,'C',1);
    $this->Cell($ancho_col_4,$alto_fila,utf8_decode("HORA DE SALIDA"),'LRTB',0,'C',1);
    
    $x=$this->GetX();
    $y=$this->GetY();    
    $this->Cell($ancho_col_5+$ancho_col_6,$alto_fila,utf8_decode("NOTAS"),'LRTB',0,'C',1);
    
    $this->SetFont('helvetica','B',5);
    $this->SetXY($x,$y+($alto_fila/3)*2);
    $this->Cell($ancho_col_5,$alto_fila/3,utf8_decode("MOTIVO"),'',0,'C',0);
    $this->Cell($ancho_col_6,$alto_fila/3,utf8_decode("DESCRIPCIÓN"),'',0,'C',0);
    $this->Ln($alto_fila/3);
    
  }
}

$pdf = new PDF("P","mm","Letter");

$pdf->SetAutoPageBreak(true,10);
$pdf->SetTopMargin(25);




$pdf->SetFillColor(255,255,255);



$alto_fila=6;
$ancho_col_1=15;
$ancho_col_2=8;
$ancho_col_3=35;
$ancho_col_4=35;
$ancho_col_5=10;
$ancho_col_6=90;




for($i=0;$i<count($personal);$i++){
  $tmp=explode(";",$personal[$i]["denominacion"]);
  $nombre_apellido=$tmp[0]." ".$tmp[2];
  $cedula=$personal[$i]["identificacion_tipo"]."-".number_format($personal[$i]["identificacion_numero"],0,"",".");
  $idp=$personal[$i]["id_persona"];
  
  $pdf->AddPage("P");
  
  
  
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
    
  $suma_hora_e=0;           $total_hora_e=0;
  $suma_hora_s=0;           $total_hora_s=0;
  $suma_hora_s_viernes=0;   $total_hora_s_viernes=0;
  
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
    
    $n_dia_semana=date("N",strtotime("$fecha"));
    
    if(isset($horas[0]["timestamp"])){
      $hora_e=date("h:i a",$horas[0]["timestamp"]);
      $suma_hora_e+=$horas[0]["timestamp"];
      $total_hora_e++;
    }
    if(isset($horas[$c-1]["timestamp"]) and $c>1){
      $hora_s=date("h:i a",$horas[$c-1]["timestamp"]);
      if($n_dia_semana!=5){//si no es viernes
        $suma_hora_s+=$horas[$c-1]["timestamp"];
        $total_hora_s++;
      }
      else{//si es viernes
        $suma_hora_s_viernes+=$horas[$c-1]["timestamp"];
        $total_hora_s_viernes++;
      }
      
    }
    
    
    
    if($n_dia_semana==6 or $n_dia_semana==7)
      $pdf->SetFillColor(255,200,200);
    else
      $pdf->SetFillColor(255,255,255);
    
    $pdf->SetFont('helvetica','',8);
    $pdf->Cell($ancho_col_1,$alto_fila,utf8_decode(letra_dia_semana($n_dia_semana)),'LTB',0,'L',1);
    $pdf->Cell($ancho_col_2,$alto_fila,utf8_decode(" $j"),'RTB',0,'R',1);
    
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell($ancho_col_3,$alto_fila,utf8_decode($hora_e),'LRTB',0,'C',1);
    $pdf->Cell($ancho_col_4,$alto_fila,utf8_decode($hora_s),'LRTB',0,'C',1);
    
       
    
    //buscar notas del dia
    //notas
    $asistencia_nota=$db->Execute("SELECT
                                    id,
                                    tipo,
                                    descripcion
                                  FROM
                                    modulo_asistencia.asistencia_nota
                                  WHERE
                                    id_persona='$idp' AND
                                    fecha='".$fecha."'");
    $nota="";
    
    
    $n_notas=count($asistencia_nota);
    if($n_notas==0){
      $pdf->Cell($ancho_col_5,$alto_fila,"",'LRTB',0,'C',1);
      $pdf->Cell($ancho_col_6,$alto_fila,"",'LRTB',0,'C',1);
    }
    else{
      //$asistencia_nota[3]=$asistencia_nota[2]=$asistencia_nota[1]=$asistencia_nota[0];$n_notas=4;
      switch($n_notas){
        case 1:
          $pdf->SetFont('helvetica','',5);
          $pdf->Cell($ancho_col_5,$alto_fila,utf8_decode($asistencia_nota[0]["tipo"]),'LRTB',0,'C',1);
          $x=$pdf->GetX();
          $y=$pdf->GetY();
          $pdf->Cell($ancho_col_6,$alto_fila,"",'LRTB',0,'C',1);
          
          $pdf->SetXY($x,$y);
          $pdf->SetFont('helvetica','',8);
          $pdf->MultiCell($ancho_col_6,$alto_fila/2,utf8_decode($asistencia_nota[0]["descripcion"]),'','L');
          $pdf->SetXY($x,$y);
        break;
        default:
          if($n_notas>3)
            $n_notas=3;
          
          $x=$pdf->GetX();
          $y=$pdf->GetY();          
          
          for($k=0;$k<$n_notas;$k++){
            $pdf->SetXY($x,$y+($alto_fila/$n_notas)*$k);
            $pdf->SetFont('helvetica','',5);
            $pdf->Cell($ancho_col_5,$alto_fila/$n_notas,utf8_decode($asistencia_nota[$k]["tipo"]),'LRTB',0,'C',1);
            $pdf->SetFont('helvetica','',8);
            $pdf->Cell($ancho_col_6,$alto_fila/$n_notas,utf8_decode($asistencia_nota[$k]["descripcion"]),'LRTB',0,'L',1);
          }
      }
      $pdf->Ln($alto_fila/$n_notas);
      continue;
    }    
    $pdf->Ln($alto_fila);    
  }
  $pdf->SetFillColor(255,255,255);
  $pdf->SetFont('helvetica','B',8);
  $pdf->Cell($ancho_col_1+$ancho_col_2,$alto_fila,utf8_decode(""),'T',0,'C',1);
  $pdf->Cell($ancho_col_3,$alto_fila,utf8_decode($total_hora_e!=0?date("h:i a",$suma_hora_e/$total_hora_e):"-"),'LRTB',0,'C',1);
  $pdf->SetFont('helvetica','B',8);
  $pdf->Cell($ancho_col_4,$alto_fila,utf8_decode(($total_hora_s!=0?date("h:i a",$suma_hora_s/$total_hora_s):"-")." / ".($total_hora_s_viernes!=0?date("h:i a",$suma_hora_s_viernes/$total_hora_s_viernes):"-")),'LRTB',0,'C',1);
  
  
  
  
  
}

$pdf->Output();
