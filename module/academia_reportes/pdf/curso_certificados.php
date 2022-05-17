<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");
include_once("../../../library/fpdf/1.7/fpdf.php");


$db=SIGA::DBController("siga_online");


if(isset($_REQUEST['id_curso'])){
  $codigo_curso=$db->Execute("SELECT codigo FROM modulo_asl.curso_aperturado WHERE id = ".str_clear($_REQUEST['id_curso']));
  $CURSO=$codigo_curso[0][0];
}
else{
  $CURSO=str_clear($_REQUEST['curso']);
  if(!$CURSO) exit;  
}

$FORMATO="A";
if(isset($_REQUEST['formato']))
  $FORMATO=$_REQUEST['formato'];



$detalle_curso=$db->Execute("
  SELECT
    ca.*,
    c.denominacion as nombrecurso,
    c.acronimo as acronimocurso,
    c.duracion,
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





$participantes=$db->Execute("
  SELECT
    i.id_estado,
    i.calificacion_final,
    e.denominacion as estadoaprendiz,
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
    e.id=3
  ORDER BY
    p.identificacion_tipo, p.identificacion_numero
  ");
//echo $detalle_curso[0]['acronimocurso'];exit;

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



function parte_posterior(){
  global $FORMATO, $detalle_curso, $pdf, $persona_curso;
  $contraportada="../image/".$detalle_curso[0]['acronimocurso'].$detalle_curso[0]['duracion']."_A.jpg";
  //print $contraportada;exit;
  if(file_exists($contraportada)){
    $pdf->AddPage("L");
    $pdf->Image($contraportada,5,5,280-10,216-10);
    if($FORMATO=="C"){
      $pdf->Image(SIGA::path()."/cache/tmp_qrcode_$persona_curso.png",233,25,25,25);
    }
  }
}


if($FORMATO=="A" or $FORMATO=="B"){
  $pdf = new FPDF("L","mm","Letter");
  $pdf->SetAutoPageBreak(false);
  
  if($FORMATO=="A")
    parte_posterior();
}
if($FORMATO=="C"){
  if($detalle_curso[0]["estado"]==0){//si esta cerrado
    print "El curso se encuentra cerrado, no puede generar los certificados.";
    exit;
  }
  include_once("../../../library/phpqrcode/1.1.4/phpqrcode.php");
}


/*
$OPCION="";
if(isset($_REQUEST['opcion']))
  $OPCION=$_REQUEST['opcion'];*/
/*
$x_firma_presidente=156;
$x_sello=$x_firma_presidente+29;
$x_firma_instructor=156+54;
$x_firma_instructor2=$x_firma_instructor;
$y_firma_instructor2=155;*/

$x_firma_presidente=156;
$x_sello=$x_firma_presidente+29;
$x_firma_instructor=156+54;
$x_firma_instructor2=156-54;
$y_firma_instructor2=184;
  
for($i=0;$i<count($participantes);$i++){
  if($FORMATO=="C"){
    $pdf = new FPDF("L","mm","Letter");
    $pdf->SetAutoPageBreak(false);
  }
  $nombres=$participantes[$i]['primer_nombre']." ".$participantes[$i]['primer_apellido'];
  $cedula=$participantes[$i]['nacionalidad']."-".number_format($participantes[$i]['cedula'],0,"",".");
  $pdf->AddPage("L");
  if($detalle_curso[0]['acronimocurso']=="F1_JAVA_B"){
    $pdf->SetMargins(25,0);
    $pdf->Image("../image/certificado_fondo_java_A.jpg",5,5,280-10,216-10);
    $texto="";
    $texto_participacion="Por haber realizado el curso:";
  }
  else{
    $pdf->Image("../image/certificado_fondo_A.jpg",5,5,280-10,216-10);
    $texto="La República Bolivariana de Venezuela a través del\nMinisterio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología\notorga el presente Certificado a:";
    
    if($detalle_curso[0]['acronimocurso']=="UF")
      $texto_participacion="Por su asistencia al curso:";
    else if($detalle_curso[0]['acronimocurso']=="SIGAFS")
      $texto_participacion="Por la culminación del curso:";
    else
      $texto_participacion="Por la aprobación del curso:";
  }
  
  if($FORMATO=="C"){
    $pdf->Image(SIGA::databasePath()."/config/sello.jpg",$x_sello,145,38.5,38.5);
  }
  
  
  $pdf->SetFont('times','I',17);
  $pdf->SetY(70);
  $pdf->MultiCell(265,8,utf8_decode("$texto"),'','C');
  
  
  $pdf->SetFont('times','BI',32);
  $pdf->SetY(100);
  $pdf->Cell(265,15,utf8_decode(mb_convert_case($nombres, MB_CASE_TITLE, "UTF-8")),0,'','C');
  
  $pdf->SetFont('times','BI',16);
  $pdf->SetY(100+12);
  
  if(trim($participantes[$i]['nacionalidad'])=="")
    $cedula="";
  
  $pdf->Cell(265,10,utf8_decode($cedula),0,'','C');
  
  $pdf->SetFont('times','I',17);
  $pdf->SetY(130);

  $pdf->Cell(265,5,utf8_decode("$texto_participacion"),'','','C');
  
  $pdf->SetFont('times','B',20);
  $pdf->SetY(132);
  $pdf->Cell(265,20,utf8_decode($detalle_curso[0]['nombrecurso']),0,'','C');

  $pdf->SetFont('times','I',14);
  $pdf->SetY(140);

  $fecha=explode("-",$detalle_curso[0]['fecha_culminacion']);
  $mes="";
  switch($fecha[1]){
    case '01': $mes="ENERO";break;
    case '02': $mes="FEBRERO";break;
    case '03': $mes="MARZO";break;
    case '04': $mes="ABRIL";break;
    case '05': $mes="MAYO";break;
    case '06': $mes="JUNIO";break;
    case '07': $mes="JULIO";break;
    case '08': $mes="AGOSTO";break;
    case '09': $mes="SEPTIEMBRE";break;
    case '10': $mes="OCTUBRE";break;
    case '11': $mes="NOVIEMBRE";break;
    case '12': $mes="DICIEMBRE";break;
    }
  $pdf->Cell(265,20,utf8_decode("Cumaná, ".strtolower($mes)." ".$fecha[0]),0,'','C');
  
  $pdf->SetFont('times','',12);
  
  
  
  if(!in_array($detalle_curso[0]['acronimocurso'],array("F1_JAVA_B"))){
    $pdf->SetXY($x_firma_presidente,184);
    //$pdf->MultiCell(45,4,utf8_decode("Lcdo. Juan B. Centeno\nPresidente\nFUNDACITE SUCRE"),"T","C");
    $pdf->MultiCell(45,4,utf8_decode("Soc. Enrique Ortiz\nPresidente\nFUNDACITE SUCRE"),"T","C");
    //$pdf->MultiCell(45,4,utf8_decode("Lcdo. Juan Lares\nDirector Ejecutivo\nFUNDACITE SUCRE"),"T","C");
    $pdf->SetXY($x_firma_instructor,184);
    $pdf->MultiCell(45,4,utf8_decode(mb_convert_case($detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['primer_apellido'], MB_CASE_TITLE, "UTF-8")."\nINSTRUCTOR"),"T","C");
    
    if($instructor2){
      $pdf->SetXY($x_firma_instructor2,$y_firma_instructor2);
      $pdf->MultiCell(45,4,utf8_decode(mb_convert_case($instructor2[0]['primer_nombre']." ".$instructor2[0]['primer_apellido'], MB_CASE_TITLE, "UTF-8")."\nINSTRUCTOR"),"T","C");
      $pdf->Image(SIGA::databasePath()."/persona/".$instructor2[0]['nacionalidad'].$instructor2[0]['cedula']."/firma.png",$x_firma_instructor2-7,$y_firma_instructor2-36,60);
      
    }
  }
  
  //insertar codigo qr
  if($FORMATO=="C"){
    $persona=$participantes[$i]['nacionalidad'].$participantes[$i]['cedula'];
    $persona_curso=$persona."|$CURSO";
    $url="http://www.fundacite-sucre.gob.ve/query?".base64_encode("CERTIFICADO=$persona_curso");
    QRcode::png($url,SIGA::path()."/cache/tmp_qrcode_$persona_curso.png",QR_ECLEVEL_L,4);

    $pdf->Image(SIGA::path()."/cache/tmp_qrcode_$persona_curso.png",231,25,25,25);
    
    $pdf->Image(SIGA::databasePath()."/persona/".$detalle_curso[0]['nacionalidad'].$detalle_curso[0]['cedula']."/firma.png",$x_firma_instructor-7,148,60);
    $pdf->Image(SIGA::databasePath()."/persona/V8647822/firma.png",$x_firma_presidente-6,148,60);//firma de enrrique
  }
  
  
  if($FORMATO=="B" or $FORMATO=="C"){
    parte_posterior();
    
    if($FORMATO=="C"){
      if(!file_exists(SIGA::databasePath()."/persona/".$persona))
        mkdir(SIGA::databasePath()."/persona/".$persona);
      if(!file_exists(SIGA::databasePath()."/persona/".$persona."/certificado"))
        mkdir(SIGA::databasePath()."/persona/".$persona."/certificado");
      $archivo=SIGA::databasePath()."/persona/".$persona."/certificado/$CURSO.pdf";

      print "(".($i+1).") Generando archivo: <a href='$url' target='_blank'>$archivo</a><br>";
      
      $pdf->Output($archivo,"F");
      if(file_exists(SIGA::path()."/cache/tmp_qrcode_$persona_curso.png"))
        unlink(SIGA::path()."/cache/tmp_qrcode_$persona_curso.png");
    }
  }
}

if($FORMATO=="A" or $FORMATO=="B"){
  $pdf->Output();
}

?>
