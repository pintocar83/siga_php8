<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");
include_once("../../../library/functions/letra_mes.php");
include_once("../../../library/fpdf/1.7/fpdf.php");

  
$anio=SIGA::paramRequest("anio");
$anio=str_replace(array("\"","'","/*","*/","--"),"",$anio);
if(!$anio) $anio=date("Y");
$periodo=$anio;
$db=SIGA::DBController();
  
$cursos=$db->Execute("
   SELECT
      ca.*,
      c.denominacion as nombrecurso,
      p.identificacion_tipo as nacionalidad,
      p.identificacion_numero as cedula,
      split_part(p.denominacion,';',1) as primer_nombre,
      split_part(p.denominacion,';',2) as segundo_nombre,
      split_part(p.denominacion,';',3) as primer_apellido,
      split_part(p.denominacion,';',4) as segundo_apellido,
      tc.denominacion,
      tc.horario,
      s.denominacion as sala,
      (select count(*) from modulo_asl.inscrito as i2 where i2.id_curso_aperturado=ca.id and i2.id_estado=3) as aprobados,
      (select count(*) from modulo_asl.inscrito as i2 where i2.id_curso_aperturado=ca.id) as total
   FROM
      modulo_asl.curso_aperturado as ca,
      modulo_asl.curso as c,
      modulo_asl.instructor as i,
      modulo_base.persona as p,
      modulo_asl.turno_curso as tc,
      modulo_asl.sala as s
   WHERE
      c.id=ca.id_curso and
      ca.id_instructor=i.id and
      i.id_persona=p.id and
      ca.id_turno=tc.id and
      ca.id_sala=s.id and
      ca.codigo like '$anio-%'
   ORDER BY
      ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc 
   ");
  

   $tam_fn=5;
   $tam_fn_sub=2.5;  
   $tam_mes=15;
  
   class PDF extends FPDF{
      function Header(){
         global $periodo, $tam_fn, $tam_mes, $anio;
         //$this->Image("../../images/cintillo_$anio.jpg",10,4,260,15);
         $this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",10,4,260,15);
         $this->Ln(12);
         $this->SetFont('helvetica','B',12);
         $this->Cell(260,5,"CRONOGRAMA DE CURSOS ".$periodo,"",0,'C');
         $this->Ln(5);
         $this->SetFont('helvetica','',9);
         $this->Cell(260,4,"ACADEMIA DE SOFTWARE LIBRE","",0,'C');
         
         $this->Ln(6);
         $this->SetFont('helvetica','B',7.5);
         $this->Cell(80,$tam_fn,"","",0,'L');
         for($j=0;$j<12;$j++)
            $this->Cell($tam_mes,$tam_fn,letra_mes($j+1),"LRTB",0,'C');
         $this->Ln($tam_fn);        
      }
   }

   $pdf = new PDF("L","mm","Letter");
   $pdf->SetAutoPageBreak(false);
   $pdf->AddPage("L");
  
   $formados_mes=array();
   for($j=0;$j<12;$j++)
      $formados_mes[$j]=0;
   
   $y=$pdf->GetY();
   for($i=0;$i<count($cursos);$i++){ 
      if($y>170){
         $pdf->AddPage("L");
         $x=$pdf->GetX();
         $y=$pdf->GetY(); 
         }
      
      $x=$pdf->GetX();
      $y=$pdf->GetY(); 
      
      $pdf->SetFillColor(255,255,255);
         
      //nombre del curso   
      $pdf->SetFont('helvetica','B',8);
      $pdf->Cell(80,$tam_fn,utf8_decode($cursos[$i]["nombrecurso"]),"LT",0,'L');

      $x=$pdf->GetX();
      $y=$pdf->GetY();
     
      //dibuja la cuadricula gris     
      $pdf->SetDrawColor(218,218,218);
      for($j=0;$j<12;$j++)
         $pdf->Cell($tam_mes,$tam_fn+$tam_fn_sub*4,"","RL",0,'L',0);
      
      //dibuja la cuadricula gris
      $pdf->SetXY($x,$y);
      $pdf->SetDrawColor(0,0,0);
      for($j=0;$j<12;$j++)
         $pdf->Cell($tam_mes,$tam_fn+$tam_fn_sub*4,"","BT",0,'L',0);
      
      
      $pdf->SetDrawColor(0,0,0);
      $pdf->Cell($tam_mes,$tam_fn+$tam_fn_sub*4,"","L",0,'L',0);
     
     
      //dibuja la barra de duración    
      if($cursos[$i]["estado"]==1)//abierto
         $pdf->SetFillColor(28,126,0);
      else//cerrado
         $pdf->SetFillColor(215,0,0);
      //en curso	
      $hoy=strtotime(date("Y-m-d"));
      if($hoy>=strtotime($cursos[$i]["fecha_inicio"]) and $hoy<=strtotime($cursos[$i]["fecha_culminacion"]))
         $pdf->SetFillColor(255,228,0);
        
      $pdf->SetXY($x,$y+2);
      $dias_inicio=(strtotime($cursos[$i]["fecha_inicio"])-strtotime("$periodo-01-01"))/(60 * 60 * 24)+1;
      $dias_duracion=(strtotime($cursos[$i]["fecha_culminacion"])-strtotime($cursos[$i]["fecha_inicio"]))/(60 * 60 * 24)+1;
      $pdf->Cell($dias_inicio*$tam_mes*12/365,$tam_fn,"","",0,'L');
      $pdf->Cell($dias_duracion*$tam_mes*12/365,$tam_fn,"","",0,'L',1);    
      $pdf->SetFillColor(255,255,255);
      
      //aprobados contra el total de inscritos
      $pdf->SetFont('helvetica','',7);
      $pdf->Cell(10,$tam_fn,$cursos[$i]["aprobados"]."/".$cursos[$i]["total"],"",0,'L',0);
      
      //$cursos[$i]["aprobados"]
      //$cursos[$i]["fecha_culminacion"]
      $formados_mes[date("n",strtotime($cursos[$i]["fecha_culminacion"]))-1]+=$cursos[$i]["aprobados"];
      
      //fecha
      $pdf->Ln($tam_fn-2);
      $pdf->SetFont('helvetica','',6.5);
      $pdf->Cell(80,$tam_fn_sub,"    Fecha: ".utf8_decode(formatDate($cursos[$i]["fecha_inicio"])." - ".formatDate($cursos[$i]["fecha_culminacion"])),"L",0,'L');
      
      //horario
      $pdf->Ln($tam_fn_sub);
      $pdf->Cell(80,$tam_fn_sub,"    Horario: ".utf8_decode(ucfirst(mb_convert_case($cursos[$i]["denominacion"], MB_CASE_LOWER, "UTF-8"))),"L",0,'L');
      
      //lugar
      $pdf->Ln($tam_fn_sub);
      $pdf->Cell(80,$tam_fn_sub,"    Lugar: ".utf8_decode(mb_convert_case($cursos[$i]["sala"], MB_CASE_TITLE, "UTF-8")),"L",0,'L');
      
      //facilitador
      $pdf->Ln($tam_fn_sub);
      $facilitador=$cursos[$i]['primer_nombre']." ".$cursos[$i]['segundo_nombre']." ".$cursos[$i]['primer_apellido']." ".$cursos[$i]['segundo_apellido'];
      $pdf->Cell(55,$tam_fn_sub,"    Facilitador: ".utf8_decode(mb_convert_case($facilitador, MB_CASE_TITLE, "UTF-8")),"LB",0,'L');
      
      //codigo del curso
      $pdf->SetFont('helvetica','',6);
      $pdf->Cell(25,$tam_fn_sub,utf8_decode(" ".$cursos[$i]["codigo"]." "),"B",0,'R',0); 
      
      
      $pdf->Ln($tam_fn_sub);
   }
  
$tam_fn_totales=4;
$pdf->SetFont('helvetica','B',8);
//total mensual
$pdf->Cell(80,$tam_fn_totales,"","",0,'L');
for($j=0;$j<12;$j++)
   $pdf->Cell($tam_mes,$tam_fn_totales,$formados_mes[$j],"LRTB",0,'C');
  
//total trimestral
$pdf->Ln($tam_fn_totales);
$pdf->Cell(80,$tam_fn_totales,"","",0,'L');
$pdf->Cell($tam_mes*3,$tam_fn_totales,$formados_mes[0]+$formados_mes[1]+$formados_mes[2],"LRTB",0,'C');//T 1
$pdf->Cell($tam_mes*3,$tam_fn_totales,$formados_mes[3]+$formados_mes[4]+$formados_mes[5],"LRTB",0,'C');//T 2
$pdf->Cell($tam_mes*3,$tam_fn_totales,$formados_mes[6]+$formados_mes[7]+$formados_mes[8],"LRTB",0,'C');//T 3
$pdf->Cell($tam_mes*3,$tam_fn_totales,$formados_mes[9]+$formados_mes[10]+$formados_mes[11],"LRTB",1,'C');//T 4

$pdf->Cell(80,$tam_fn_totales,"","",0,'L');
$pdf->Cell( $tam_mes*3*4,$tam_fn_totales,
            $formados_mes[0]+$formados_mes[1]+$formados_mes[2]+
            $formados_mes[3]+$formados_mes[4]+$formados_mes[5]+
            $formados_mes[6]+$formados_mes[7]+$formados_mes[8]+
            $formados_mes[9]+$formados_mes[10]+$formados_mes[11],"LRTB",0,'C');

$pdf->Output();

?>