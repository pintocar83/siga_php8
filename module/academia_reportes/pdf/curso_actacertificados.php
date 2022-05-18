<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");
include_once("../../../library/fpdf/1.84/fpdf.php");

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
  

$paginas=1;
$tam_fn=7;
$j=0;
$pdf = new FPDF("P","mm","Letter");
$pdf->SetAutoPageBreak(false);
for($i=0;$i<$paginas;$i++){
    $pdf->AddPage("P");
    //cintillo
    $pdf->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",10,4,195);
    
    
    
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

    
    
    
    $pdf->SetFont('helvetica','I',13);
    $pdf->SetXY(9,25);
    if(!in_array($detalle_curso[0]['acronimocurso'],array("F1_JAVA_B"))){
      $pdf->MultiCell(196,6,utf8_decode("A través de la siguiente acta se deja constancia de la entrega de ".
        "certificados de aprobación del curso \"".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8").
        "\", correspondiente al Programa Nacional Científico-Tecnológico Academia de Software Libre, ".
        "cuya rectoría depende del Ministerio del Poder Popular para la Ciencia, Tecnología e Innovación, ".
        "el cual es ejecutado en la región a través de la Unidad Territorial Sucre (FUNDACITE Sucre).".
        "\n\nLos abajo firmantes corresponden a los participantes aprobados del curso ".
        "\"".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8").
        "\", Sección ".
        utf8_decode($detalle_curso[0]['codigo']).", $facilitador.".
        "\n\nEn el mes de ".strtolower($mes)." de ".$fecha[0]."."),'','J');
    }
    else{
      $pdf->MultiCell(196,6,utf8_decode("A través de la siguiente acta se deja constancia de la entrega de los ".
        "certificados correspondientes al curso \"".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8").
        "\", ".
        "el cual es ejecutado por la Fundación para el Desarrollo de la Ciencia y Tecnología en en Estado Sucre (FUNDACITE Sucre).".
        "\n\nLos abajo firmantes corresponden a los participantes que culminaron satisfactoriamente el curso ".
        "\"".mb_convert_case($detalle_curso[0]['nombrecurso'], MB_CASE_TITLE, "UTF-8").
        "\", Sección ".
        utf8_decode($detalle_curso[0]['codigo']).", $facilitador.".
        "\n\nEn el mes de ".strtolower($mes)." de ".$fecha[0]."."),'','J');
    }

    
    //cabecera de la tabla
    $pdf->ln(3);
    
    $pdf->SetFillColor(218,218,218);
    $pdf->SetFont('helvetica','B',9);
    $pdf->Cell(5,7,utf8_decode("Nº"),1,0,'C',1);
    $pdf->Cell(25,7,utf8_decode("CÉDULA"),1,0,'C',1);
    $pdf->Cell(85,7,utf8_decode("NOMBRES/APELLIDOS"),1,0,'C',1);
    //cabecera de la tabla (dias)
    $pdf->SetFont('helvetica','B',9);    
    
    $pdf->Cell(40,7,utf8_decode("FECHA"),1,0,'C',1);
    $pdf->Cell(40,7,utf8_decode("FIRMA"),1,0,'C',1);
    
    $pdf->SetFont('helvetica','',10);
    $pdf->ln(7);
    $pdf->SetFillColor(255,255,255);
    //listado de participantes
    for(;$j<count($participantes);$j++){  
      $nombres=mb_convert_case($participantes[$j]['primer_nombre']." ".$participantes[$j]['segundo_nombre']." ".$participantes[$j]['primer_apellido']." ".$participantes[$j]['segundo_apellido'], MB_CASE_TITLE, "UTF-8");
      $cedula=$participantes[$j]['nacionalidad']."-".number_format($participantes[$j]['cedula'],0,"",".");
      
      $pdf->Cell(5,$tam_fn,utf8_decode("".($j+1).""),1,0,'C');
      $pdf->Cell(25,$tam_fn,utf8_decode($cedula),1,0,'L');
      $pdf->Cell(85,$tam_fn,utf8_decode($nombres),1,0,'L');
      
      $pdf->Cell(40,$tam_fn,"",1,0,'L',1);
      $pdf->Cell(40,$tam_fn,"",1,0,'C',1);
      
      $pdf->ln($tam_fn);
      if(($j+1)%25==0 and ($j+1)<count($participantes)){
        $paginas++;
        $j++;
        break;
        }
    }
}




$pdf->Output();

?>