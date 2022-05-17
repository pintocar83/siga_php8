<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../../library/db.controller.php");
include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");
include_once("../../../library/functions/formatDate.php");
include_once("../../../library/functions/str_clear.php");



$db=SIGA::DBController();

$id_curso=SIGA::paramRequest('id_curso');

if($id_curso){
  $codigo_curso=$db->Execute("SELECT codigo FROM modulo_asl.curso_aperturado WHERE id = '$id_curso'");
  $CURSO=$codigo_curso[0][0];
}
else{
  $CURSO=SIGA::paramRequest('curso');
  if(!$CURSO) exit;  
}



$detalle_curso=$db->Execute("
  SELECT
    ca.*,
    c.denominacion as nombrecurso,
    c.acronimo as acronimocurso,
    c.duracion,
    tc.denominacion as horario,
    tc.dias,
    s.denominacion as sala
  FROM
    modulo_asl.curso_aperturado as ca,
    modulo_asl.curso as c,
    modulo_asl.turno_curso as tc,
    modulo_asl.sala as s
  WHERE
    c.id=ca.id_curso and
    ca.id_turno=tc.id and
    ca.id_sala=s.id and
    ca.codigo='$CURSO'
  ");

if(!$detalle_curso){
  echo "Curso no encontrado.";
  exit;
}

$plantilla_svg="../image/afiche/".$detalle_curso[0]["acronimocurso"].$detalle_curso[0]["duracion"].".svg";

if(!file_exists($plantilla_svg)){
  echo "No existe el archivo plantilla '$plantilla_svg'";
  exit;
}
$xmlsvg=file_get_contents($plantilla_svg);  
$xmlsvg=str_replace("VARIABLE_FECHA","Del ".formatDate($detalle_curso[0]["fecha_inicio"])." al ".formatDate($detalle_curso[0]["fecha_culminacion"]),$xmlsvg);
$xmlsvg=str_replace("VARIABLE_HORARIO",ucfirst(mb_convert_case($detalle_curso[0]["horario"], MB_CASE_LOWER, "UTF-8")),$xmlsvg);
$xmlsvg=str_replace("VARIABLE_DURACION",$detalle_curso[0]["duracion"]." Horas",$xmlsvg);
$xmlsvg=str_replace("VARIABLE_LUGAR",$detalle_curso[0]["sala"],$xmlsvg);
$xmlsvg=str_replace("VARIABLE_PIE_APLICACIONES","Unidad de Telemática e Innovación Tecnológica",$xmlsvg);

$tmp_file_svg="../image/afiche/cache/$CURSO.svg";
$tmp_file_pdf=SIGA::path()."/cache/academia_afiche_$CURSO.pdf";

file_put_contents($tmp_file_svg,$xmlsvg);

system("inkscape --export-pdf='$tmp_file_pdf' -f '$tmp_file_svg'");

if(file_exists($tmp_file_pdf)){
  header('Content-type:application/pdf');
  header("Content-Disposition: inline; filename=\"AFICHE_$CURSO.pdf\"");
  readfile($tmp_file_pdf);
  unlink($tmp_file_pdf);
  if(file_exists($tmp_file_svg))
    unlink($tmp_file_svg);
}

?>