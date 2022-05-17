<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');


include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");


$id_curso=SIGA::paramRequest("id_curso");
$nacionalidad=SIGA::paramRequest("nacionalidad");
$cedula=SIGA::paramRequest("cedula");
$llamar=SIGA::paramRequest("llamar");
$descartar=SIGA::paramRequest("descartar");
$id_curso_mover=SIGA::paramRequest("id_curso_mover");  
$id_preinscrito=SIGA::paramRequest("id_preinscrito");
  
  
  
$db=SIGA::DBController();




if($id_curso_mover){
  //eliminar registros previos
  //$db->Delete("modulo_asl.preinscrito_curso","id_curso='$id_curso' and id_preinscrito IN (select id from modulo_asl.preinscrito where nacionalidad='$nacionalidad' and cedula='$cedula')");
  //$db->Insert("modulo_asl.preinscrito_curso",array("id_curso"=>"'$id_curso_mover'",));
  
  $db->Delete("modulo_asl.preinscrito_curso","id_curso='$id_curso' and id_preinscrito IN (select id from modulo_asl.preinscrito where nacionalidad='$nacionalidad' and cedula='$cedula')");
  $db->Insert("modulo_asl.preinscrito_curso",array("id_curso"=>"'$id_curso_mover'","id_preinscrito"=>"'$id_preinscrito'","descartar"=>"'0'"));
  
  //$db->Update("modulo_asl.preinscrito_curso",
  //            array("id_curso"=>"'$id_curso_mover'"),
  //            "id_curso='$id_curso' and id_preinscrito='$id_preinscrito'");
  
  
  
  //print_r($R);
}



//descartar preinscripcion
if($descartar==1){  
  /*$db->Update("modulo_asl.preinscrito_curso",
              array("descartar"=>'1'),
              "id_curso='$id_curso' and id_preinscrito IN (select id from modulo_asl.preinscrito where nacionalidad='$nacionalidad' and cedula='$cedula')");
  */
  $db->Delete("modulo_asl.preinscrito_curso",
              "id_curso='$id_curso' and id_preinscrito='$id_preinscrito'");

}

$cursos=$db->Execute("
        SELECT DISTINCT
          c.id,
          c.denominacion,
          c.duracion,
          (select count(*) 
          from modulo_asl.curso as c2, modulo_asl.preinscrito_curso as pc 
          where pc.descartar=0 and pc.id_curso=c2.id and c.id=c2.id) as cantidad
        FROM
          modulo_asl.curso as c     
        ORDER BY
          c.denominacion
        ");

        
$cursos_enviar=$db->Execute("
        SELECT 
          c.id,
          c.acronimo as codigo,
          c.denominacion,
          c.duracion          
        FROM
          modulo_asl.curso as c
        WHERE
          c.mostrar_preinscripcion>0
        ORDER BY
          c.denominacion
        ");

        
$filtrar_turno="";
if(array_key_exists("filtrar_turno",$_REQUEST))
  $filtrar_turno=$_REQUEST["filtrar_turno"];
  


$preinscritos=$db->Execute("
        SELECT 
          p.id,
          p.fecha,
          p.nacionalidad,
          p.cedula,
          p.nombres_apellidos,
          p.telefono,
          p.correo,
          i.nombre as institucion,
          p.turno,
          pc.id_preinscrito
        FROM
          modulo_asl.curso as c,
          modulo_asl.preinscrito_curso as pc,
          modulo_asl.preinscrito as p,
          modulo_asl.institucion as i
        WHERE
          pc.descartar=0 and
          c.id = '$id_curso' AND
          c.id=pc.id_curso AND
          pc.id_preinscrito=p.id AND
          p.id_institucion=i.id 
          
          /*AND 
          c.denominacion NOT IN ( select c2.denominacion 
                                  from 
                                    inscrito as i2, 
                                    curso_aperturado as ca2,
                                    curso as c2
                                  where 
                                    i2.nacionalidad_persona=p.nacionalidad and
                                    i2.cedula_persona=p.cedula and
                                    i2.id_curso_aperturado=ca2.id and
                                    ca2.id_curso=c2.id and
                                    (i2.id_estado=2 or i2.id_estado=3)
                                 )*/
        ORDER BY
          p.fecha
        ");


$detalle_persona=$db->Execute("
        SELECT
          p.identificacion_tipo as nacionalidad,
          p.identificacion_numero as cedula,
          split_part(p.denominacion,';',1) as primer_nombre,
          split_part(p.denominacion,';',2) as segundo_nombre,
          split_part(p.denominacion,';',3) as primer_apellido,
          split_part(p.denominacion,';',4) as segundo_apellido,
          p.telefono,
          p.correo
        FROM
          modulo_base.persona as p
        WHERE
          p.identificacion_tipo='$nacionalidad' and
          p.identificacion_numero='$cedula'  
        ");

?>
<style>
 
 
a,a:visited{
  color: #0041D8;
  text-decoration: none;
}
a:hover{
  text-decoration: underline;
}
  
.contenedor_principal{
  font-size: smaller;
  font-family: sans-serif;
}
.no_space_break{
  white-space:nowrap;
}



.tabla_preinscritos{
  font-size: small;
  border: solid 1px #000;
}

.tabla_preinscritos_cabecera{
  border: solid 1px #000;
  border-bottom: solid 2px #000;
  text-align: center;
}

.tabla_preinscritos_fila{
  cursor: pointer;
  opacity: 0.3;
}
.tabla_preinscritos_fila:hover{
  background-color: #FFF6C6;
  opacity: 1;
}

.tabla_preinscritos_fila_actual{
  /*border: 3px solid #000;*/
  background-color: #EFEFEF;
  cursor: pointer;
  font-weight: bold;
  opacity: 1;
}

/*.tabla_preinscritos_fila_actual > td{*/
/*  border-top: 3px solid black;*/
/*}*/

.tabla_llamada{
  font-size: smaller;
  border: solid 1px #A8A8A8;
  color: #7A7A7A;
}
.tabla_llamada_cabecera{
  border: solid 1px #A8A8A8;
  border-bottom: solid 2px #A8A8A8;
  text-align: center;
}

.botones{
  border: solid 2px #648a9a;
  background-color: #FFF;
  font-weight: bold;
  
}
</style>
<script>
function popupwindow(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  //var top = (screen.height/2)-(h/2);
  var top = 0;
  return window.open(url, title, 'fullscreen=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, resizable=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  //return window.open(url, title, 'fullscreen=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, resizable=yes, copyhistory=no');
}

function mover_curso() {
  var url="<?php print "?filtrar_turno=$filtrar_turno&id_curso=$id_curso&nacionalidad=$nacionalidad&cedula=$cedula&mover=1&id_curso_mover="?>";
  //alert(url+document.getElementById("id_curso_mover").value);
  //alert("xx");
  window.location.href=url+document.getElementById("id_curso_mover").value+"&scroll="+document.body.scrollTop+"&id_preinscrito="+document.getElementById("id_preinscrito").value;
}

function descartar() {
  //?filtrar_turno=$filtrar_turno&id_curso=$id_curso&nacionalidad=".$preinscritos[$i]["nacionalidad"]."&cedula=".$preinscritos[$i]["cedula"]."&descartar=1'
  
  var url="<?php print "?filtrar_turno=$filtrar_turno&id_curso=$id_curso&nacionalidad=$nacionalidad&cedula=$cedula&descartar=1&id_preinscrito="?>";
  
  window.location.href=url+document.getElementById("id_preinscrito").value;
}

</script>

<?php


echo "<div class='contenedor_principal'>";
echo "<div><b>CURSOS</b></div>";
for($i=0;$i<count($cursos);$i++){
  //echo "<div><a href='?nombrecurso=".$cursos[$i]["denominacion"]."'>".$cursos[$i]["denominacion"]."</a> (".$cursos[$i]["cantidad"].")</div>";
  if($cursos[$i]["cantidad"]>0)
    echo "<div style='".($cursos[$i]["id"]==$id_curso?"font-weight: bold; text-decoration: underline;":"")."'><a href='?id_curso=".$cursos[$i]["id"]."'>".$cursos[$i]["denominacion"]." (".$cursos[$i]["duracion"]." horas)</a> (".$cursos[$i]["cantidad"].")</div>";
}
echo "<br />";
echo "<b>MOSTRAR TURNO </b>";
echo "<select name='filtrar_turno' onchange=\"window.location.href='?id_curso=".$id_curso."&filtrar_turno='+value\">
<option value=''>TODOS</option>
<option value='1' ".($filtrar_turno==1?"selected":"").">LUNES A VIERNES (MAÑANA)</option>
<option value='2' ".($filtrar_turno==2?"selected":"").">LUNES A VIERNES (TARDE)</option>
<option value='3' ".($filtrar_turno==3?"selected":"").">SÁBADOS (MAÑANA)</option>
<option value='4' ".($filtrar_turno==4?"selected":"").">SÁBADOS (TARDE)</option>
</select>";


echo "<br />";
//echo "<div><b>".$cursos[$i]["curso"]."</b></div>";

$lista_correo="";



echo "<table width='100%' border='0' cellpadding='3' cellspacing='0' class='tabla_preinscritos'>
         <tr style='font-weight: bold;'>
            <td width='4%' class='tabla_preinscritos_cabecera'>N</td>
            <td width='4%' class='tabla_preinscritos_cabecera'>FECHA</td>
            <td width='5%' class='tabla_preinscritos_cabecera'>CEDULA</td>
            <td width='30%' class='tabla_preinscritos_cabecera'>NOMBRES/APELLIDOS</td>
            <td width='10%' class='tabla_preinscritos_cabecera'>TELEFONO</td>
            <td width='15%' class='tabla_preinscritos_cabecera'>CORREO</td>
            <td width='15%' class='tabla_preinscritos_cabecera'>INSTITUCION</td>
            <td width='15%' class='tabla_preinscritos_cabecera'>TURNO</td>
         </tr>
         ";
for($i=0;$i<count($preinscritos);$i++){
  $saltar=true;
  $turno="";
  $t=explode(",",$preinscritos[$i]["turno"]);
  for($it=0;$it<count($t);$it++){    
    if($t[$it]==1) $turno.="LUNES A VIERNES (MAÑANA)<br />";
    if($t[$it]==2) $turno.="LUNES A VIERNES (TARDE)<br />";
    if($t[$it]==3) $turno.="SÁBADOS (MAÑANA)<br />";
    if($t[$it]==4) $turno.="SÁBADOS (TARDE)<br />";
    
    if($filtrar_turno==$t[$it]) $saltar=false;
    }
  if($filtrar_turno=='') $saltar=false;
  
  if($saltar) continue;
  
  $class_fila="tabla_preinscritos_fila";
  $sw=false;
  if($nacionalidad==$preinscritos[$i]["nacionalidad"] and $cedula==$preinscritos[$i]["cedula"]){
    $sw=true;
    $class_fila="tabla_preinscritos_fila_actual";
    }
  
  echo "
         <tr class='$class_fila'  onclick=\"window.location.href='?filtrar_turno=$filtrar_turno&id_curso=$id_curso&nacionalidad=".$preinscritos[$i]["nacionalidad"]."&cedula=".$preinscritos[$i]["cedula"]."&llamar=".(($llamar==1 and $sw)?1:1)."&scroll='+document.body.scrollTop\">
           <td class=''>".($i+1)."</td>
           <td class=''>".date("d/m/Y h:i:sa",strtotime($preinscritos[$i]["fecha"]))."</td>
           <td class='no_space_break'>".$preinscritos[$i]["nacionalidad"]."-".number_format($preinscritos[$i]["cedula"],0,'.','.')."</td>
           <td>".$preinscritos[$i]["nombres_apellidos"]."</td>
           <td>".$preinscritos[$i]["telefono"]."</td>
           <td>".$preinscritos[$i]["correo"]."</td>
           <td>".$preinscritos[$i]["institucion"]."</td>
           <td class='no_space_break'>".$turno."</td>
         </tr>
         ";
$lista_correo.="".$preinscritos[$i]["correo"].", ";

  if($sw){
    if($llamar==1){
      $color_nombres="green";
      $color_telefono="green";
      $color_correo="green";
      
      //$detalle_persona[0]["telefono"]=str_replace(array("."),""$detalle_persona[0]["telefono"]);
      
      $data_registrada["nombres_apellidos"]="";
      $data_registrada["telefono"]="";
      $data_registrada["correo"]="";
      if(isset($detalle_persona[0])){
        $data_registrada["nombres_apellidos"]=$detalle_persona[0]["primer_nombre"]." ".$detalle_persona[0]["segundo_nombre"]." ".$detalle_persona[0]["primer_apellido"]." ".$detalle_persona[0]["segundo_apellido"];
        $data_registrada["telefono"]=$detalle_persona[0]["telefono"];
        $data_registrada["correo"]=$detalle_persona[0]["correo"];
      }
      
      if($data_registrada["nombres_apellidos"]!=$preinscritos[$i]["nombres_apellidos"]){
        $color_nombres="red";
        }
      if($data_registrada["telefono"]!=$preinscritos[$i]["telefono"]){
        $color_telefono="red";
        }
      if($data_registrada["correo"]!=$preinscritos[$i]["correo"]){
        $color_correo="red";
        }
      $telefono=$data_registrada["telefono"];
      if(!trim($telefono)) $telefono="SIN DATA";
      $correo=$data_registrada["correo"];
      if(!trim($correo)) $correo="SIN DATA";
      
      echo "
         <tr class='$class_fila'>
           <td class='no_space_break' colspan='3'>DATA REGISTRADA</td>
           <td style='color:$color_nombres;'>".$data_registrada["nombres_apellidos"]."</td>
           <td style='color:$color_telefono;'>".$telefono."</td>
           <td style='color:$color_correo;'>".$correo."</td>
           <td></td>
           <td class='no_space_break' style='text-align:center;'>
            <!--<div style='color:blue;text-decoration:underline;cursor:pointer;' onclick=\"popupwindow('cursos_persona.php?nacionalidad=".$nacionalidad."&cedula=".$cedula."','_BLANK',850,850);\">CURSOS</div>-->
            
           </td>
         </tr>
         <tr class='$class_fila'>
          <td colspan='8'>";
          
          ?>
          <div style="margin: 0px; padding: 0px;background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>CURSOS REALIZADOS</b></div>
          <?php
          $cursos_participante=$db->Execute("SELECT
                            ca.id_curso as id,
                            c.denominacion as denominacion,
                            c.duracion,
                            ca.codigo,
                            ca.fecha_inicio,
                            ca.fecha_culminacion,
                            i.id_estado,
                            e.denominacion as estado,
                            i.id as id_inscrito
                          FROM
                            modulo_asl.inscrito as i,
                            modulo_asl.institucion as ins,
                            modulo_asl.estado as e,
                            modulo_asl.curso_aperturado as ca,
                            modulo_asl.curso as c,
                            modulo_base.persona as p
                          WHERE
                            i.id_institucion=ins.id and
                            i.id_estado=e.id and          
                            i.id_curso_aperturado=ca.id and
                            ca.id_curso=c.id and
                            i.id_persona=p.id and
                            p.identificacion_tipo='".$nacionalidad."' and p.identificacion_numero='".$cedula."'
                          ORDER BY
                            ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc
                            ");
          //$xml_cursos = new DOMDocument();
          //$xml_cursos->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/asl/persona_cursos.php?nacionalidad=$nacionalidad&cedula=$cedula");
          /*$xml_cursos=XML::get("../../xml/asl/persona_cursos.php?nacionalidad=$nacionalidad&cedula=$cedula");
          $persona_cursos = $xml_cursos->getElementsByTagName("curso");
          $c=0;
          foreach( $persona_cursos as $pc ){
            $cursos_participante[$c]["id"]=                  $pc->getAttribute('id');
            $cursos_participante[$c]["denominacion"]=        $pc->getAttribute('denominacion');
            $cursos_participante[$c]["duracion"]=            $pc->getAttribute('duracion');
            $cursos_participante[$c]["codigo"]=              $pc->getAttribute('codigo');
            $cursos_participante[$c]["fecha_inicio"]=        $pc->getAttribute('fecha_inicio');
            $cursos_participante[$c]["fecha_culminacion"]=   $pc->getAttribute('fecha_culminacion');
            $cursos_participante[$c]["id_estado"]=           $pc->getAttribute('id_estado');
            $cursos_participante[$c]["estado"]=              $pc->getAttribute('estado');
            $cursos_participante[$c]["id_inscrito"]=         $pc->getAttribute('id_inscrito');
            $c++;
            }*/
          
          if(count($cursos_participante)>0):
          
          ?>
          <table cellpadding="3" cellspacing="5" border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%">
            <tr style="background-color: #fff; font-weight: bold; text-align: center;">
              <td>CÓDIGO</td>
              <td>CURSO</td>
              <td>FECHA</td>
              <td>ESTADO</td>
            </tr>
          <?php   
          for($x=0;$x<count($cursos_participante);$x++){
            echo "<tr style='background-color: #fff; font-size : 10px;'>
              <td style='white-space: nowrap;'><a target='_blank' href='cursos_detalle.php?curso=".$cursos_participante[$x]["codigo"]."'>".$cursos_participante[$x]["codigo"]."</a></td>
              <td>".$cursos_participante[$x]["denominacion"]." (".$cursos_participante[$x]["duracion"]." Horas)</td>
              <td style='text-align:center;'>".formatDate($cursos_participante[$x]["fecha_inicio"])." - ".formatDate($cursos_participante[$x]["fecha_culminacion"])."</td>
              <td style='text-align:center;'>".$cursos_participante[$x]["estado"]."</td>
            </tr>";
          }
          ?>    
          </table>
          <?php
          else:
            echo "<div style='text-align: center;'>No se encontraron registros.</div>";
          endif;
          print "<input type='hidden' id='id_preinscrito' value=".$preinscritos[$i]["id_preinscrito"]." />";

          print "<div><small>MOVER A: ";
          print "<select id='id_curso_mover'>";
          for($p=0;$p<count($cursos_enviar);$p++){
            print "<option value=".$cursos_enviar[$p]["id"].">".$cursos_enviar[$p]["codigo"]." - ".$cursos_enviar[$p]["denominacion"]." (".$cursos_enviar[$p]["duracion"]." horas)</option>";
          }
          print "</select>
          <a href='#' onclick='mover_curso()'>[MOVER PREINSCRIPCIÓN]</a>
           
          <a href='#' onclick='descartar()'>[DESCARTAR PREINSCRIPCIÓN]</a></small>
          </div>";
          
        echo
          "</td>
         </tr>
         ";
      }
    }
  
  }
echo "</table>";


echo "<b>LISTA DE CORREOS:</b><br />".$lista_correo;












echo "</div>";//contenedor principal












?>

<script>
  window.onload=function(){
    //document.body.scrollTop
    window.scrollTo(0,<?php print isset($_GET["scroll"])?$_GET["scroll"]:0;?>);
  }
  
</script>
