<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');




include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");
include_once("../../library/functions/letra_mes.php");

?>
<!doctype html>
<html>
<head>
  <title>PERSONAS FORMADAS</title>
  <style>
    body{
      font-family: sans-serif;      
    }    
    a,a:visited{
      color: #0041D8;
      text-decoration: none;
    }
    a:hover{
      text-decoration: underline;
    }
  </style>
</head>
<body>

<?php



$db=SIGA::DBController();

$persona=$db->Execute("
        SELECT DISTINCT 
          i.id_persona,
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
          modulo_base.persona as p
        WHERE
          i.id_persona=P.id
        ORDER BY
          p.identificacion_tipo, p.identificacion_numero
        ");

echo "<div style='font-family: sans-serif;'>";
for($i=0;$i<count($persona);$i++){
  
  $cursos=$db->Execute("
        SELECT
          * ,
          ins.*,
          e.denominacion as nombreestado,
          ca.*,
          c.denominacion as nombrecurso
        FROM
          modulo_asl.inscrito as i,
          modulo_asl.institucion as ins,
          modulo_asl.estado as e,
          modulo_asl.curso_aperturado as ca,
          modulo_asl.curso as c
        WHERE
          i.id_institucion=ins.id and
          i.id_estado=e.id and          
          i.id_curso_aperturado=ca.id and
          ca.id_curso=c.id and
          i.id_persona='".$persona[$i]['id_persona']."'
        ORDER BY
          ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc
          ");
  
  
  echo "<div style='font-weight: bold;'>";
  if(!trim($persona[$i]['nacionalidad']))
    $cedula="S/N COD: ".$persona[$i]['cedula'];
  else
    $cedula=$persona[$i]['nacionalidad']."-".$persona[$i]['cedula'];
  echo $cedula."  ".$persona[$i]["primer_nombre"]." ".$persona[$i]["segundo_nombre"]." ".$persona[$i]["primer_apellido"]." ".$persona[$i]["segundo_apellido"]." ".$persona[$i]["telefono"]." ".$persona[$i]["correo"];
  echo "</div>";
  echo "<table width='100%' border='1' cellpading='0' cellspacing='0'>
         <tr style='font-weight: bold;'>
           <td width='25%'>CURSO</td>
           <td width='15%'>CODIGO</td>
           <td width='15%'>INICIO</td>
           <td width='15%'>CULMINACION</td>
           <td width='15%'>ESTADO</td>
           <td width='15%'>CALIFICACION</td>
         </tr>
         ";
  for($j=0;$j<count($cursos);$j++){
    echo "
         <tr>
           <td>".$cursos[$j]["nombrecurso"]."</td>
           <td><a href='cursos_detalle.php?curso=".$cursos[$j]["codigo"]."' target='_blank'>".$cursos[$j]["codigo"]."</a></td>
           <td>".formatDate($cursos[$j]["fecha_inicio"])."</td>
           <td>".formatDate($cursos[$j]["fecha_culminacion"])."</td>
           <td>".$cursos[$j]["nombreestado"]."</td>
           <td>".$cursos[$j]["calificacion_final"]."</td>
         </tr>
         ";
  }
  echo "</table>";
  echo "<br>";
  }


echo "</div>";

?>
</body>
</html>