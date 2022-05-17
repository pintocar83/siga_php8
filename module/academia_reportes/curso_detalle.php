<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../../library/include.php");
include_once("../../library/functions/formatDate.php");


$db=SIGA::DBController();




$CURSO=SIGA::paramRequest('curso');
if(!$CURSO) exit;



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

$instructores=$detalle_curso[0]['primer_nombre']." ".$detalle_curso[0]['primer_apellido'];
if($instructor2)
  $instructores="$instructores / ".$instructor2[0]['primer_nombre']." ".$instructor2[0]['primer_apellido'];    

$participantes=$db->Execute("
  SELECT
    i.id_estado,
    e.denominacion as estadoaprendiz,
    ins.nombre as institucion,
    p.identificacion_tipo as nacionalidad,
    p.identificacion_numero as cedula,
    split_part(p.denominacion,';',1) as primer_nombre,
    split_part(p.denominacion,';',2) as segundo_nombre,
    split_part(p.denominacion,';',3) as primer_apellido,
    split_part(p.denominacion,';',4) as segundo_apellido,
    p.telefono,
    p.correo,
    i.calificacion_final
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

$clave_encuesta=$db->Execute("SELECT clave FROM modulo_asl.encuesta_curso_clave WHERE id_curso_aperturado='".$detalle_curso[0]['id']."'");
if(isset($clave_encuesta[0]["clave"]))
  $clave_encuesta=$clave_encuesta[0]["clave"];
else
  $clave_encuesta="No se encontro la clave";


?>
<!doctype html>
<html>
<head>
  <title>DETALLE CURSO </title>
  <style>
    body{
      font-family: sans-serif;
      font-size: 13px;
    }
    
    a,a:visited{
      color: #0041D8;
      text-decoration: none;
    }
    a:hover{
      text-decoration: underline;
    }
    
    .tabla{
      background-color: #232323;
      color: #FFFFFF;
      font-size: 14px;
    }
    

    
    .cabeceratable{
      font-weight: bold;
      text-align: center;
    }
    

    .celdatabla{
      background-color: #FFFFFF;
      color: #2D2D2D;
      font-size: 12px;
    }
    
    .titulo_curso{
      font-size: 22px;
      font-weight: bold;
    }
    
  </style>
</head>
<body>

<?php


echo 	"<span class='titulo_curso'>".$detalle_curso[0]['nombrecurso']."</span>";
echo 	"<table>
	   <tr>
	      <td><b>C&oacute;digo</b></td>
	      <td><b>:</b></td>
	      <td>".$detalle_curso[0]['codigo']."</td>
	   </tr>
	   <tr>
	      <td><b>Fecha</b></td>
        <td><b>:</b></td>
	      <td>".formatDate($detalle_curso[0]['fecha_inicio'])." - ".formatDate($detalle_curso[0]['fecha_culminacion'])."</td>
	   </tr>
	   
	   <tr>
	      <td><b>Horario</b></td>
        <td><b>:</b></td>
	      <td>".$detalle_curso[0]['denominacion'].'  '.$detalle_curso[0]['horario']."</td>
	   </tr>
	   <tr>
	      <td><b>Facilitador</b></td>
        <td><b>:</b></td>
	      <td>$instructores</td>
	   </tr>
     <tr>
	      <td><b>Clave Encuesta</b></td>
        <td><b>:</b></td>
        <td>".$detalle_curso[0]['encuesta_clave']."</td>
	   </tr>
	 </table>   
	";
  echo "<table border='0' class='tabla' width='100%'>
           <tr class='cabeceratable'>
              <td>No</td>
              <td>C&eacute;dula</td>
              <td>Participante</td>
              <td>Tel&eacute;fono</td>
              <td>Correo</td>
              <td>Dependencia</td>
              <td>Estado</td>
              <td>Calificación</td>
           </tr>
  ";
  
  $aprobados=0;
  $reprobados=0;
  for($j=0;$j<count($participantes);$j++){

    $link_certificado="";
    if($participantes[$j]['id_estado']==3){
      $aprobados++;
      $link_certificado="<a target='_blank' href='../../public/academia/certificado.php?persona=".$participantes[$j]['nacionalidad'].$participantes[$j]['cedula']."&certificado=".$detalle_curso[0]['codigo']."'><span style='font-size:10px; line-height: 0.20;'>Certificado&nbsp;Digital</span></a>";
    }
    if($participantes[$j]['id_estado']==4) $reprobados++;
    
    echo "
           <tr>
              <td class='celdatabla' style='text-align: center;'>".($j+1)."</td>
              <td class='celdatabla' style='white-space: nowrap;'>".$participantes[$j]['nacionalidad'].'-'.number_format($participantes[$j]['cedula'],0,'','.')."</td>
              <td class='celdatabla' style='white-space: nowrap;'>".$participantes[$j]['primer_nombre'].' '.$participantes[$j]['primer_apellido']."</td>
              <td class='celdatabla'>".$participantes[$j]['telefono']."</td>
              <td class='celdatabla'>".$participantes[$j]['correo']."</td>
              <td class='celdatabla'>".$participantes[$j]['institucion']."</td>
              <td class='celdatabla'>".$participantes[$j]['estadoaprendiz']."</td>
              <td class='celdatabla' style='text-align: center;'>".$participantes[$j]['calificacion_final']."</td>
              <td class='celdatabla' style='text-align: center;'>$link_certificado</td>
           </tr>
  ";
  }
  echo "</table>";


echo 	"<table>
	   <tr>
	      <td><b>APROBADOS</b></td>
	      <td><b>:</b></td>
	      <td>".$aprobados."</td>
	   </tr>
	   <tr>
	      <td><b>REPROBADOS</b></td>
	      <td><b>:</b></td>
	      <td>".$reprobados."</td>
	   </tr>
	 </table>   
	";

echo"
<br/>
<a href='pdf/curso_participantes.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Lista de Participantes</a><br />
<a href='pdf/curso_asistencia.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Lista de Asistencia</a><br />
<a href='pdf/curso_calificaciones.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Lista de Calificaciones</a><br />
Certificados:
  <a href='pdf/curso_certificados.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>[A = Una Contraportada por documento]</a>
  <a href='pdf/curso_certificados.php?curso=".$detalle_curso[0]['codigo']."&formato=B' target='_blank'>[B = Una Contraportada por cada certificado ]</a>
  <a href='#' onclick=\"if(confirm('¡Advertencia! Se reemplazaran certificados existentes.\\n¿Desea generar los certificados?')) window.open('pdf/curso_certificados.php?curso=".$detalle_curso[0]['codigo']."&formato=C');\">[C = Generar/Publicar (versión digital)]</a>
<br />
<a href='pdf/curso_actacertificados.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Acta de Entrega de Certificados</a><br />
<a href='correo_confirmacion.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Correo Confirmacion</a><br />
<a href='pdf/afiche.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Afiche Publicitario</a><br />
<a href='pdf/curso_constancia_asistencia.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Constancia Participación (Formato A)</a><br />
<a href='pdf/curso_constancia_asistencia_B.php?curso=".$detalle_curso[0]['codigo']."' target='_blank'>Constancia Participación (Formato B)</a><br />
<a href='encuesta_resultado.php?id_curso_aperturado=".$detalle_curso[0]['id']."' target='_blank'>Resultado de la encuesta</a>
";        


//pdf_curso_certificados.php?curso=".$detalle_curso[0]['codigo']."&formato=C



?>
</body>
</html>