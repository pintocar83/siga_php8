<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");
include_once("../../../library/functions/letra_mes.php");
include_once("../../../library/functions/letra_numero.php");
include_once("../../../library/fpdf/1.84/WriteTag.php");

$db=SIGA::DBController();

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
    tc.denominacion as turno,
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

$facilitador="";
if($instructor2)
  $facilitador="Facilitadores: ".mb_convert_case($detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['primer_apellido'], MB_CASE_TITLE, "UTF-8")." y ".mb_convert_case($instructor2[0]['primer_nombre']." ".$instructor2[0]['primer_apellido'], MB_CASE_TITLE, "UTF-8");    
else
  $facilitador="Facilitador: ".mb_convert_case($detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['primer_apellido'], MB_CASE_TITLE, "UTF-8");
  

  
$fecha=explode("-",$detalle_curso[0]['fecha_culminacion']);
$fecha_inicio=explode("-",$detalle_curso[0]['fecha_inicio']);
$fecha_culminacion=explode("-",$detalle_curso[0]['fecha_culminacion']);



  
  

$ANCHO=185;
$MARGEN_LEFT=15;
$MARGEN_TOP=40;

$tam_fn=7;
$j=0;
$pdf = new PDF_WriteTag("P","mm","Letter");
$pdf->SetAutoPageBreak(false);
$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetStyle("parrafo", "helvetica", "I", 13, "0, 0, 0", 15);
$pdf->SetStyle("negrita", "helvetica", "BI", 13, "0, 0, 0");


for($i=0;$i<count($participantes);$i++){
    $nombres=mb_convert_case($participantes[$i]['primer_nombre']." ".$participantes[$i]['segundo_nombre']." ".$participantes[$i]['primer_apellido']." ".$participantes[$i]['segundo_apellido'], MB_CASE_UPPER, "UTF-8");
    $cedula=$participantes[$i]['nacionalidad']."-".number_format($participantes[$i]['cedula'],0,"",".");
    
    $pdf->AddPage("P");
    //cintillo
    $pdf->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$MARGEN_LEFT,8,$ANCHO);


    $pdf->SetFont('helvetica','B',16);
    //$pdf->SetXY(9,25);
    //$pdf->SetY(20);
    $pdf->Cell($ANCHO,7,utf8_decode("CONSTANCIA"),0,0,'C',0);
    $pdf->Ln(15);
    
    $tipo="CURSO ";
    if(stripos($detalle_curso[0]['nombrecurso'],"taller")===0) $tipo="";
    if(stripos($detalle_curso[0]['nombrecurso'],"charla")===0) $tipo="";
    if(stripos($detalle_curso[0]['nombrecurso'],"curso") ===0) $tipo="";
    
    $texto="Quien suscribe Dr. Luis Brito, en mi carácter de Coordinador de la Unidad de Telemática e Innovación Tecnológica de la ".
    "Fundación para el Desarrollo de la Ciencia y la Tecnología del estado Sucre, organismo adscrito al  Ministerio del Poder ".
    "Popular para la Educación Universitaria, Ciencia y Tecnología (MPPEUCT), hago constar que el ciudadano(a) <negrita>$nombres</negrita>, ". 
    "titular de la cédula de identidad Nro. <negrita>$cedula</negrita> asistió al <negrita>$tipo".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_UPPER, "UTF-8")."</negrita> ".
    "realizado desde el ".$fecha_inicio[2]." de ".letra_mes($fecha_inicio[1])." de ".$fecha_inicio[0]." hasta el ".$fecha_culminacion[2]." de ".letra_mes($fecha_culminacion[1])." de ".$fecha_culminacion[0].", ".
    "en el horario: ".$detalle_curso[0]['turno'].".";
    
    $texto="Quien suscribe Lcdo. José Ponce Tatá, en mi carácter de Director Ejecutivo de la ".
    "Fundación para el Desarrollo de la Ciencia y la Tecnología del estado Sucre, organismo adscrito al  Ministerio del Poder ".
    "Popular para la Educación Universitaria, Ciencia y Tecnología (MPPEUCT), hago constar que el ciudadano(a) <negrita>$nombres</negrita>, ". 
    "titular de la cédula de identidad Nro. <negrita>$cedula</negrita> asistió al <negrita>$tipo".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_UPPER, "UTF-8")."</negrita> ".
    "realizado desde el ".$fecha_inicio[2]." de ".letra_mes($fecha_inicio[1])." de ".$fecha_inicio[0]." hasta el ".$fecha_culminacion[2]." de ".letra_mes($fecha_culminacion[1])." de ".$fecha_culminacion[0].", ".
    "en el horario: ".$detalle_curso[0]['turno'].".";

    $texto="Quien suscribe Lcdo. Enrique Ortiz, en mi carácter de Presidente de la ".
    "Fundación para el Desarrollo de la Ciencia y la Tecnología del Estado Sucre (FUNDACITE SUCRE), organismo adscrito al Ministerio del Poder ".
    "Popular para Ciencia y Tecnología (MPPCT), hago constar que el ciudadano(a) <negrita>$nombres</negrita>, ". 
    "titular de la cédula de identidad Nro. <negrita>$cedula</negrita> asistió al <negrita>$tipo".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_UPPER, "UTF-8")."</negrita> ".
    "realizado el ".$fecha_inicio[2]." de ".letra_mes($fecha_inicio[1])." de ".$fecha_inicio[0].".";




    
    $pdf->SetFont('helvetica','I',13);
    $pdf->SetXY(22,65);
    $pdf->WriteTag(175,9,utf8_decode(
    "<parrafo>$texto</parrafo>".
    "<parrafo>Se expide la presente constancia a petición de parte interesada, en Cumaná, a los ".letra_numero(date("d"))." días del mes de ".letra_mes(date("m"))." de ".date("Y").".</parrafo>"),'','J');
    
    
    $pdf->SetFont('helvetica','BI',13);
    
    $pdf->Line(70,218,145,218);
    
    $pdf->SetXY(22,220);
    //$pdf->MultiCell(170,6,utf8_decode("Dr. Luis A. Brito"),'','C');
    $pdf->MultiCell(170,6,utf8_decode("Lcdo. Enrique Ortiz"),'','C');
    
    $pdf->SetFont('helvetica','',13);
    $pdf->SetXY(22,220+6);
    //$pdf->MultiCell(170,6,utf8_decode("Coord. UTIT - FUNDACITE Sucre"),'','C');
    $pdf->MultiCell(170,6,utf8_decode("Presidente - FUNDACITE Sucre"),'','C');
    
    $pdf->SetFont('helvetica','I',9);
    $pdf->SetXY(22,260);
    $pdf->MultiCell(170,4,utf8_decode("Av. Monseñor Alfredo Rodríguez Figueroa, vía El Peñon, Urb. Cristobal Colón, Cumaná 6101, Edo. Sucre, Venezuela.\nTelf. (0293)467.25.31 - http://www.fundacite-sucre.gob.ve/"),'','C');

    
    $pdf->SetDrawColor(160,0,0);
    $pdf->Line(15, 30, 15, 260-5);
    $pdf->Line(15+3, 30+3, 15+3, 260+3-5);
    
    $pdf->Line(15, 260-5, 195, 260-5);
    $pdf->Line(15+3, 260+3-5, 195+3, 260+3-5);
    
    $pdf->Image(SIGA::databasePath()."/config/sello.jpg",130,185,38.5,38.5);
    $pdf->Image(SIGA::databasePath()."/persona/V8647822/firma.png",80,175,60);
}




$pdf->Output();

?>