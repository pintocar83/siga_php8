<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");
include_once("../../../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();
  
  function es_dia_curso($dias_semana,$d){
    switch(date("N",$d)){
      //case 1: return stripos($dias_semana,"LL")>=0?2:FALSE;
      case 1: return substr_count($dias_semana,"L");
      //case 2: return stripos($dias_semana,"MM")>=0?2:FALSE;
      case 2: return substr_count($dias_semana,"M");
      //case 3: return stripos($dias_semana,"XX")>=0?2:FALSE;
      case 3: return substr_count($dias_semana,"X");
      //case 4: return stripos($dias_semana,"JJ")>=0?2:FALSE;
      case 4: return substr_count($dias_semana,"J");
      //case 5: return stripos($dias_semana,"VV")>=0?2:FALSE;
      case 5: return substr_count($dias_semana,"V");
      //case 6: return stripos($dias_semana,"SS")>=0?2:FALSE;
      case 6: return substr_count($dias_semana,"S");
      //case 7: return stripos($dias_semana,"DD")>=0?2:FALSE;
      case 7: return substr_count($dias_semana,"D");
    }
    return FALSE;
  }
  
  function dia_semana($dias_semana,$d){
    switch(date("N",$d)){
      case 1: return "LUNES";
      case 2: return "MARTES";
      case 3: return "MIERCOLES";
      case 4: return "JUEVES";
      case 5: return "VIERNES";
      case 6: return "SABADO";
      case 7: return "DOMINGO";
    }
    return FALSE;
  }
  





if(isset($_REQUEST['id_curso'])){
  $codigo_curso=$db->Execute("SELECT codigo FROM modulo_asl.curso_aperturado WHERE id = ".str_clear($_REQUEST['id_curso']));
  $CURSO=$codigo_curso[0][0];
}
else{
  $CURSO=str_clear($_REQUEST['curso']);
  if(!$CURSO) exit;  
}

$ANIO=explode("-",$CURSO);
$ANIO=$ANIO[0];


$detalle_curso=$db->Execute("
  SELECT
    ca.*,
    c.denominacion as nombrecurso,
    c.acronimo as acronimocurso,
    p.identificacion_tipo as nacionalidad,
    p.identificacion_numero as cedula,
    split_part(p.denominacion,';',1) as primer_nombre,
    split_part(p.denominacion,';',2) as segundo_nombre,
    split_part(p.denominacion,';',3) as primer_apellido,
    split_part(p.denominacion,';',4) as segundo_apellido,
    p.telefono,
    p.correo,
    tc.denominacion,
    tc.horario,
    tc.dias
  FROM
    modulo_asl.curso_aperturado as ca,
    modulo_asl.curso as c,
    modulo_asl.instructor as i,
    modulo_base.persona as p,
    modulo_asl.turno_curso as tc
  WHERE
    c.id=ca.id_curso and
    ca.id_instructor=i.id and
    i.id_persona=p.id and
    ca.id_turno=tc.id and
    ca.codigo='$CURSO'
  ");

$instructor2=NULL;
$id_instructor_secundario=$detalle_curso[0]['id_instructor_secundario'];
if($id_instructor_secundario>0){
  $instructor2=$db->Execute("SELECT
                                p.identificacion_tipo as nacionalidad,
                                p.identificacion_numero as cedula,
                                split_part(p.denominacion,';',1) as primer_nombre,
                                split_part(p.denominacion,';',3) as primer_apellido
                              FROM
                                modulo_base.persona as p
                              WHERE
                                p.id=(select I.id_persona from modulo_asl.instructor as I where I.id=$id_instructor_secundario)");
}



$participantes=$db->Execute("
  SELECT    
    ins.nombre as institucion,
    p.identificacion_tipo as nacionalidad,
    p.identificacion_numero as cedula,
    split_part(p.denominacion,';',1) as primer_nombre,
    split_part(p.denominacion,';',2) as segundo_nombre,
    split_part(p.denominacion,';',3) as primer_apellido,
    split_part(p.denominacion,';',4) as segundo_apellido,
    p.telefono,
    p.correo
  FROM
    modulo_asl.inscrito as i,
    modulo_asl.institucion as ins,
    modulo_asl.estado as e,
    modulo_base.persona as p
  WHERE
    p.id=i.id_persona and
    i.id_curso_aperturado='".$detalle_curso[0]['id']."' and
    i.id_institucion=ins.id and
    i.id_estado=e.id and
    (e.id=2 or e.id=3 or e.id=4)
  ORDER BY
    p.identificacion_tipo, p.identificacion_numero
  ");

  
  $dias_semana=$detalle_curso[0]['dias'];
  $fecha_inicio=$detalle_curso[0]['fecha_inicio'];
  $fecha_final=$detalle_curso[0]['fecha_culminacion'];
  //$fecha_final="2013-02-14";
  //$dias_semana="LXS";

  $dias_curso=array();
  $c=0;
  for($d=strtotime($fecha_inicio);$d<=strtotime($fecha_final);$d+=(60 * 60 * 24)){
    $esdiacurso=es_dia_curso($dias_semana,$d);
    if($esdiacurso!==FALSE and $esdiacurso!=0){
      $dias_curso[$c]["fecha"]=date("Y-m-d",$d);
      $dias_curso[$c]["dia"]=dia_semana($dias_semana,$d);
      $c++;
      if($esdiacurso==2){
        $dias_curso[$c]["fecha"]=date("Y-m-d",$d);
        $dias_curso[$c]["dia"]=dia_semana($dias_semana,$d);
        $c++;
      }
    }
  }
  
  if($c<=7){
    $columns_pag=$c;
    $paginas=1;
    }
  else if($c>=8 and $c<=14){
    $columns_pag=ceil($c/2);
    $paginas=2;
    }
  else{
    $columns_pag=5;  
    $paginas=$c/5;
    }
$tam_max_columns=160;
$tam_fn=5.5;

if($columns_pag!=0)
  $tam_columns=$tam_max_columns/$columns_pag;
else 
  $tam_columns=$tam_max_columns;

  
//print_r($dias_curso);exit;
  
$ic=0;
$ic2=0;
$j=0;
$pdf = new FPDF("L","mm","Letter");
$pdf->SetAutoPageBreak(false);
for($i=0;$i<$paginas;$i++){
    $pdf->AddPage("L");
    //cintillo
    //$pdf->Image("images/cintillo.jpg",10,4,260,15);
    if(substr($detalle_curso[0]['acronimocurso'],0,3)=="F1_"){
      $pdf->Image(SIGA::databasePath()."/config/logo_01.jpg",200,5,65);
      $pdf->SetFont('helvetica','B',12);
      $pdf->Text(11,15,utf8_decode("Fundación para el Desarrollo de la Ciencia y Tecnología en el Estado Sucre"));
    }
    else{
      $pdf->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",10,4,260,15);
      
      if(file_exists("../image/".$detalle_curso[0]['acronimocurso'].".jpg"))
        $pdf->Image("../image/".$detalle_curso[0]['acronimocurso'].".jpg",190,20,80);
      else
        $pdf->Image("../image/logo_academia.jpg",190,20,80);
    }
    
    //Nombre del curso
    $pdf->SetFont('helvetica','B',18);
    $pdf->SetXY(10,20);
    $pdf->Cell(270,8,utf8_decode($detalle_curso[0]['nombrecurso']),'',1,'L');
    //codigo del curso
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(35,4,utf8_decode("CÓDIGO: "),'',0,'L');
    $pdf->SetFont('helvetica','',10);
    $pdf->Cell(200,4,utf8_decode($detalle_curso[0]['codigo']),'',1,'L');
    //fecha
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(35,4,utf8_decode("FECHA: "),'',0,'L');
    $pdf->SetFont('helvetica','',10);
    $pdf->Cell(200,4,utf8_decode("DEL ".formatDate($detalle_curso[0]['fecha_inicio'])." AL ".formatDate($detalle_curso[0]['fecha_culminacion'])),'',1,'L');
    //Turno
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(35,4,utf8_decode("TURNO: "),'',0,'L');
    $pdf->SetFont('helvetica','',10);
    $pdf->Cell(200,4,utf8_decode($detalle_curso[0]['denominacion']." ".$detalle_curso[0]['horario']."."),'',1,'L');
    //Facilitador
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(35,4,utf8_decode("FACILITADOR: "),'',0,'L');
    $pdf->SetFont('helvetica','',10);
    //$pdf->Cell(200,4,utf8_decode($detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['segundo_nombre']." ".$detalle_curso[0]['primer_apellido']." ".$detalle_curso[0]['segundo_apellido']."."),'',1,'L');
    
    $instructores=$detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['primer_apellido'];
    if($instructor2)
      $instructores="$instructores / ".$instructor2[0]['primer_nombre']." ".$instructor2[0]['primer_apellido'];    
    $pdf->Cell(200,4,utf8_decode("$instructores."),'',1,'L');
    
    
    
    //cabecera de la tabla
    $pdf->ln(3);
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(275,4,utf8_decode("LISTADO DE ASISTENCIA"),'',1,'C');
    
    $pdf->SetFillColor(218,218,218);
    $pdf->SetFont('helvetica','B',8);
    $pdf->Cell(5,5,utf8_decode("Nº"),1,0,'C',1);
    $pdf->Cell(25,5,utf8_decode("CÉDULA"),1,0,'C',1);
    $pdf->Cell(70,5,utf8_decode("NOMBRES/APELLIDOS"),1,0,'C',1);
    //cabecera de la tabla (dias)
    $pdf->SetFont('helvetica','B',7);    
    for($k=0,$ic2=0;$k<$columns_pag and $ic<$c;$k++,$ic++,$ic2++)
      $pdf->Cell($tam_columns,5,utf8_decode($dias_curso[$ic]["dia"]." ".formatDate($dias_curso[$ic]["fecha"])),1,0,'C',1);
      
    $pdf->ln(5);
    $pdf->SetFillColor(255,255,255);
    //listado de participantes
    $sw_p=true;
    for(;$j<count($participantes);$j++){  
      $nombres=$participantes[$j]['primer_nombre']." ".$participantes[$j]['segundo_nombre']." ".$participantes[$j]['primer_apellido']." ".$participantes[$j]['segundo_apellido'];
      $cedula=$participantes[$j]['nacionalidad']."-".number_format($participantes[$j]['cedula'],0,"",".");
      if(trim($participantes[$j]['nacionalidad'])=="")
        $cedula="S/N [".$participantes[$j]['cedula']."]";
      $pdf->SetFont('helvetica','',8);
      $pdf->Cell(5,$tam_fn,utf8_decode("".($j+1).""),1,0,'C');
      $pdf->SetFont('helvetica','',10);
      $pdf->Cell(25,$tam_fn,utf8_decode($cedula),1,0,'L');
      $pdf->SetFont('helvetica','',8);
      $pdf->Cell(70,$tam_fn,utf8_decode($nombres),1,0,'L');
      //area de  firma de las columna de dias
      for($k=0;$k<$ic2;$k++)
      	$pdf->Cell($tam_columns,$tam_fn,utf8_decode(""),1,0,'C');
      $pdf->ln($tam_fn);
      if(($j+1)%27==0 and ($j+1)<count($participantes)){
        $paginas++;
        $j++;
        $ic=0;
        $ic2=0;
        $sw_p=false;
        break;
        }
    }
    if($sw_p)
      $j=0;
}




$pdf->Output();

?>