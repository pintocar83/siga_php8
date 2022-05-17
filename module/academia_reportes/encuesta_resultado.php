<?php
header('Content-Type: text/html; charset=utf-8');
include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/str_clear.php");



function texto_puntaje($n){
  switch($n){
    case 1: case "1": return "Deficiente";
    case 2: case "2": return "Regular";
    case 3: case "3": return "Bueno";
    case 4: case "4": return "Muy Bueno";
    case 5: case "5": return "Excelente";    
  }
  return "";
}



$db=SIGA::DBController();

$id_curso_aperturado=str_clear($_GET["id_curso_aperturado"]);
$total_i1=0;
$total_i2=0;
$total_i3=0;
$total_i4=0;
$total_i5=0;
$total_i6=0;
$total_i7=0;
$total_i8=0;
$total_i9=0;
$total_i10=0;
$total_i11=0;
$total_i12=0;
$total_i13=0;


$detalle_curso=$db->Execute("
                      SELECT
                        ca.*,
                        c.denominacion as nombrecurso,
                        p.*,
                        tc.denominacion,
                        tc.horario
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
                        ca.id='$id_curso_aperturado'
                      ORDER BY
                        ca.codigo desc
                      ");



$encuesta=$db->Execute("SELECT * FROM modulo_asl.curso_encuesta as CE, modulo_base.persona as P WHERE CE.id_curso_aperturado='$id_curso_aperturado' AND CE.id_persona=P.id ORDER BY P.identificacion_tipo, P.identificacion_numero");



print "<div class='curso'>CURSO: ".$detalle_curso[0]["codigo"]."</div>";

print "<table border='0' style='border-collapse:collapse;'>";
print "<tr class='cabecera_tabla'>";
print "<td>Cedula</td>";
print "<td>Nombre/Apellido</td>";
print "<td>Promoción de la actividad</td>";
print "<td>Atenci&oacute;n del personal</td>";
print "<td>Demostración del dominio del tema</td>";
print "<td>Claridad y objetidad de la exposición</td>";
print "<td>Capacidad para despertar atención</td>";
print "<td>Capacidad para esclarecer dudas</td>";
print "<td>Cumplimiento del programa</td>";
print "<td>Contenido tem&aacute;tic</td>";
print "<td>Actividades pr&aacute;cticas</td>";
print "<td>Material did&aacute;ctico</td>";
print "<td>Recursos audiovisuales</td>";
print "<td>Ante de la actividad</td>";
print "<td>Después de la actividad</td>";
print "<td>¿Recomendaría Usted esta actividad a otras personas?</td>";
print "<td>Sugerencia para el mejoramiento de la actividad</td>";
print "</tr>";
for($i=0;$i<count($encuesta);$i++){

  
  print "<tr style='text-align:center; font-size: 11px;'>";
  print "<td>".$encuesta[$i]["identificacion_tipo"]."".$encuesta[$i]["identificacion_numero"]."</td>";
  print "<td style='text-align:left;'>".str_replace(";"," ",$encuesta[$i]["denominacion"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item1"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item2"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item3"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item4"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item5"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item6"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item7"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item8"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item9"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item10"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item11"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item12"])."</td>";
  print "<td>".texto_puntaje($encuesta[$i]["item13"])."</td>";
  print "<td>".($encuesta[$i]["item14"]==1?"SI":"NO")."</td>";
  print "<td>".$encuesta[$i]["item15"]."</td>";
  print "</tr>";
  
  $total_i1+=$encuesta[$i]["item1"];
  $total_i2+=$encuesta[$i]["item2"];
  $total_i3+=$encuesta[$i]["item3"];
  $total_i4+=$encuesta[$i]["item4"];
  $total_i5+=$encuesta[$i]["item5"];
  $total_i6+=$encuesta[$i]["item6"];
  $total_i7+=$encuesta[$i]["item7"];
  $total_i8+=$encuesta[$i]["item8"];
  $total_i9+=$encuesta[$i]["item9"];
  $total_i10+=$encuesta[$i]["item10"];
  $total_i11+=$encuesta[$i]["item11"];
  $total_i12+=$encuesta[$i]["item12"];
  $total_i13+=$encuesta[$i]["item13"];
}
$n=count($encuesta);
if($n>0):
  print "<tr style='text-align:center; font-size: 11px; font-weight: bold;'>";
  print "<td></td>";
  print "<td style='text-align:left;'></td>";
  print "<td>".texto_puntaje(ceil($total_i1/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i2/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i3/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i4/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i5/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i6/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i7/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i8/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i9/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i10/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i11/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i12/$n))."</td>";
  print "<td>".texto_puntaje(ceil($total_i13/$n))."</td>";
  print "<td></td>";
  print "<td></td>";
  print "</tr>";
endif;

print "</table>";
?>
<style>
  body{
    font-family: sans-serif;
  }
  .curso{
    font-size: 20px;
    font-weight: bold;
  }
  .cabecera_tabla{
    font-size: 11px;
    font-weight: bold;
    
    text-align: center;
    background-color: #E5E5E5;
    
  }
  
  td{
    border: 1px solid #000;
  }
  
</style>