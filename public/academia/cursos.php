<?php
/*
Es llamado desde la pagina web con el siguiente codigo

<?php
print file_get_contents("http://aplicaciones.fundacite-sucre.gob.ve/publico/asl/cursos.php?anio=2014");
?>
<br>
<br>
<a href="/?q=aslpreinscripcion">Preinscribirse</a>
<br>
<br>
<a href="/?q=asl">Volver</a>


*/


header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');



include("../../library/db.controller.php");
include("library/db.constants.php");

include_once("../../library/functions.php");
  

  
$params=$_REQUEST;
$anio=getParamClearUpper('anio');
if(!$anio) $anio=2014;
  

$db=new DBController();
$db->ConnectQuick("siga_online");

$cursos=$db->Execute("
              SELECT
                ca.*,
                c.denominacion as nombrecurso,
                c.duracion,
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
                p.id=i.id_persona and
                ca.id_turno=tc.id and
                ca.id_sala=s.id and
                ca.codigo like '$anio-%'
              ORDER BY
                ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc 
              ");
              

?>
<style>



.curso_contenedor{
  text-align: left;  
  border: 0px solid #ADADAD;
  border-radius: 5px;
  /*box-shadow: 0 0 20px 0px #C9C9C9;*/
  margin: 0px;
  margin-bottom: 5px;
  padding: 3px;
  width: 100%;
  position: relative;
}

.curso_culminado{
  background-color: #FF7575;
}

.curso_enproceso{
  background-color: #FFF4A5;
}

.curso_poriniciar{
  background-color: #D7FF9B;
}

.curso_titulo{
  text-align: center;
  font-weight: bold;
  font-size: small;
  cursor: pointer;
  white-space:nowrap;
  overflow:hidden;
}

.curso_codigo{
  text-align: right;
  font-size: smaller;
  /*float: right;*/
  right:0px;
  top: 0px;
  position: absolute;
  /*width: 100%;*/
  padding: 0px;
  margin: 0px;
  margin-top: 3px;
  margin-left: -6px;
  padding-left: 6px;
  padding-right: 3px;
  cursor: pointer;
}

.curso_informacion{
  font-size: smaller;
  line-height: 110%;
  display: none;
}
</style>
<!--<script src="http://code.jquery.com/jquery.min.js"></script>-->


<script src="/archivos/jquery.min.js"></script>
<script>
var divs_contenidos=new Array();
<?php
for($i=0;$i<count($cursos);$i++)
  echo "divs_contenidos.push('#".$cursos[$i]["codigo"]."');\n";
?>

function mostrar_ocultar_contenido_cursos(src){for(var i=0;i<divs_contenidos.length;i++){if(divs_contenidos[i]!=src)$(divs_contenidos[i]).hide(400);}$(src).toggle(400);}

</script>
<?php

echo "<p align=''><b>CURSOS REALIZADOS EN EL $anio</b></p>";
//echo base64_encode("http://aplicaciones.fundacite-sucre.gob.ve/asl/website/cursos.php");
$total_formados=0;

for($i=0;$i<count($cursos);$i++){
  $total_formados+=$cursos[$i]["aprobados"];
  $class_estado="";
  //dibuja la barra de duración    
  if($cursos[$i]["estado"]==1)//abierto
    $class_estado="curso_poriniciar"; 
  else//cerrado
    $class_estado="curso_culminado";
  
  //en curso
  $apr_curso=0;
  $hoy=strtotime(date("Y-m-d"));
  if($hoy>=strtotime($cursos[$i]["fecha_inicio"]) and $hoy<=strtotime($cursos[$i]["fecha_culminacion"])){
    $class_estado="curso_enproceso";
    //no publicar cantidad aprobados por el curso
    $apr_curso=0;
    }
  if($hoy>strtotime($cursos[$i]["fecha_culminacion"])){
    $class_estado="curso_culminado";
    $apr_curso=$cursos[$i]["aprobados"];
    }
  
  $personas_formadas=0;
  if($class_estado!="curso_poriniciar")//publicar la cantidad de personas y el listado de inscritos para los cursos iniciados y culminados
    $personas_formadas=$apr_curso."/".$cursos[$i]["total"]." <a href='/sites/asl/asl_descargar_pdf.php?curso=".$cursos[$i]["codigo"]."'>(ver detalle)</a>";
  
  echo "<div class='curso_contenedor $class_estado'>";  
  echo "<div class='curso_titulo' onclick=\"mostrar_ocultar_contenido_cursos('#".$cursos[$i]["codigo"]."');\">".$cursos[$i]["nombrecurso"]."</div>";
  echo "<div class='curso_codigo $class_estado' onclick=\"mostrar_ocultar_contenido_cursos('#".$cursos[$i]["codigo"]."');\">".$cursos[$i]["codigo"]."</div>";
  echo "<div class='curso_informacion' id='".$cursos[$i]["codigo"]."'>";
  if($cursos[$i]["fecha_inicio"]!=$cursos[$i]["fecha_culminacion"])
    echo "<div><b>Fecha: </b>".fecha_formatear($cursos[$i]["fecha_inicio"])." - ".fecha_formatear($cursos[$i]["fecha_culminacion"])."</div>";
  else
    echo "<div><b>Fecha: </b>".fecha_formatear($cursos[$i]["fecha_inicio"])."</div>";
  echo "<div><b>Horario: </b>".ucfirst(mb_convert_case($cursos[$i]["denominacion"], MB_CASE_LOWER, "UTF-8"))."</div>";
  echo "<div><b>Duración: </b>".$cursos[$i]["duracion"]." Horas</div>";
  echo "<div><b>Lugar: </b>".mb_convert_case($cursos[$i]["sala"], MB_CASE_TITLE, "UTF-8")."</div>";
  $facilitador=$cursos[$i]['primer_nombre']." ".$cursos[$i]['primer_apellido'];
  echo "<div><b>Facilitador: </b>".mb_convert_case($facilitador, MB_CASE_TITLE, "UTF-8")."</div>";
  
  echo "<div><b>Personas Formadas: </b>$personas_formadas</div>";
  echo "</div>";
  echo "</div>";
  }




echo "<table>";
echo "<tr>";
echo "<td colspan='2'><b>Estado del curso</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td style='width:15px;'><div class='curso_contenedor curso_culminado' style='width:20px;height:20px; margin:2px; padding:0px;'></div></td>";
echo "<td>Culminado</td>";
echo "</tr>";
echo "<tr>";
echo "<td><div class='curso_contenedor curso_enproceso' style='width:20px;height:20px;margin:2px; padding:0px;'></div></td>";
echo "<td>En proceso</td>";
echo "</tr>";
echo "<tr>";
echo "<td><div class='curso_contenedor curso_poriniciar' style='width:20px;height:20px;margin:2px; padding:0px;'></div></td>";
echo "<td>Por iniciar</td>";
echo "</tr>";
echo "</table>";

echo "<table >";
echo "<tr>";
echo "<td><b>Personas Formadas</b></td>";
echo "</tr>";
echo "<tr>";
echo "<td style='font-size: xx-large;padding-top:10px;'><b>$total_formados</b></td>";
echo "</tr>";

echo "</table>";

?>