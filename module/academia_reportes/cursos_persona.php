<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors','On');

/*function formatDate($f){
  list($ano,$mes,$dia)=explode("-",$f);
  return "$dia/$mes/$ano";
}*/

include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");

//include_once("../../library/xml.php");



//$db_asl=new DBController();
//$db_asl->Connect(DB_DRIVER,DB_SERVER,DB_NAME,DB_USER,DB_PASSWORD);

$db=SIGA::DBController();

$nacionalidad="";
$cedula="";
$id_institucion="";
$id_inscrito="";
$id_llamada_eliminar="";
$llamada_fecha="";
$llamada_telefono="";
$llamada_motivo="";
$llamada_resultado="";
$codigo_curso_inscribir="";
$id_curso_preinscrito="";

$accion="nuevo";
$c=0;
$cr=0;
$ci=0;

if(array_key_exists("nacionalidad",$_REQUEST))
  $nacionalidad=$_REQUEST["nacionalidad"];
if(array_key_exists("cedula",$_REQUEST))
  $cedula=$_REQUEST["cedula"];
if(array_key_exists("accion",$_REQUEST))
  $accion=$_REQUEST["accion"];
if(array_key_exists("id_institucion",$_REQUEST))
  $id_institucion=$_REQUEST["id_institucion"];

if(array_key_exists("id_inscrito",$_REQUEST))  $id_inscrito=$_REQUEST["id_inscrito"];
if(array_key_exists("id_curso_preinscrito",$_REQUEST))  $id_curso_preinscrito=$_REQUEST["id_curso_preinscrito"];

if(array_key_exists("id_llamada_eliminar",$_REQUEST))  $id_llamada_eliminar=$_REQUEST["id_llamada_eliminar"];
if(array_key_exists("llamada_fecha",$_REQUEST))  $llamada_fecha=$_REQUEST["llamada_fecha"];
if(array_key_exists("llamada_telefono",$_REQUEST))  $llamada_telefono=$_REQUEST["llamada_telefono"];
if(array_key_exists("llamada_motivo",$_REQUEST))  $llamada_motivo=$_REQUEST["llamada_motivo"];
if(array_key_exists("llamada_resultado",$_REQUEST))  $llamada_resultado=$_REQUEST["llamada_resultado"];
if(array_key_exists("codigo_curso_inscribir",$_REQUEST))  $codigo_curso_inscribir=$_REQUEST["codigo_curso_inscribir"];

$primer_nombre="";
$segundo_nombre="";
$primer_apellido="";
$segundo_apellido="";
$institucion="";
$correo="";
$telefono="";


 




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>CURSOS PERSONA</title>

    <link rel="stylesheet" type="text/css" href="../library/extjs-4.1.1/resources/css/ext-all.css" />
    <script type="text/javascript" src="../library/extjs-4.1.1/ext-all.js"></script>
    <script type="text/javascript" src="../library/extjs-4.1.1/locale/ext-lang-es.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/icons.css" />


<style>
body {
  padding-left: 50px;
  padding-right: 50px;
  
}

.contenedor, input, button, textarea, select{
  font-size : 12px;
  padding: 2px;
}

.contenedor td{
  margin: 0px;
  padding: 3px;

}

.contenedor table{
  border-collapse: separate;
  border-spacing: 2px;
}

input,select,textarea {
  background-color : #FFFFFF;
  border: 1px solid #566272;
}

input:focus,select:focus,textarea:focus {
  background-color : #FFFFFF;
  border: 1px solid #ACC5E9;
}
  
input:disabled,select:disabled,textarea:disabled {
  background-color : #BABABA;
  border: 1px solid #AEAEAE;
}

button {
  background-color : #FFFFFF;
  color : #47545E;
  border: 1px solid #566272;
  font-weight : bold;
}

button:hover {
  background-color : #FFF8C9;
  color : #000000;
  border: 1px solid #566272;
  font-weight : bold;
}

.title_field{
  font-size : 12px;
  font-weight: bold;  
}

.buttons{
  text-align: right;
  
}

.buttons img{
  vertical-align: middle; 
  cursor:pointer; 
}


</style>

<?php
    /*
    for($c=0;$c<count($FORM) and $FORM;$c++){
        $TYPE_FORM=$db->Execute("SELECT value FROM eureka_v1.form_property WHERE name='type' AND type='view' AND id_form=".$FORM[$c]['id']);
        if(count($TYPE_FORM)>0 and $TYPE_FORM){
            switch($TYPE_FORM[0][0]){
                case 'dialog':
                    include("../dialog.php");
                    break;
                case 'form':
                    include("../form.php");
                    break;
                case 'report':
                    include("../report.php");
                    break;
            }
            
        }
        else
            include("../form.php");
        
        
        
    }
    
    
    */
    
    ?>

<?
/*
if($accion=="eliminar_llamada"):
  $resultado=$db->Delete("base.llamada_telefonica","id=$id_llamada_eliminar");
  if(!$resultado){
    echo "Error al registrar datos. [codigo 4]";
    }  
  $accion="buscar";
endif;
*/

if($accion=="eliminar_inscrito"):
  $resultado=$db->Delete("modulo_asl.inscrito","id=$id_inscrito");
  if(!$resultado){
    echo "Error al registrar datos. [codigo 5]";
    } 
  $accion="buscar";
endif;

if($accion=="eliminar_curso_preinscrito"):
  $resultado=$db->Update("modulo_asl.preinscrito_curso",array("descartar"=>"1"),"id_curso=$id_curso_preinscrito and 
  id_preinscrito in (select id from modulo_asl.preinscrito where nacionalidad='$nacionalidad' and cedula='$cedula')");
  
  if(!$resultado){
    echo "Error al registrar datos. [codigo 6]";
    } 
  $accion="buscar";
endif;
//
//if($accion=="guardar"):
//  $llamada_telefono=preg_replace("/([^0-9])*/","", $llamada_telefono);
//  
//  $resultado=$db->Insert("base.llamada_telefonica",array( "nacionalidad"=>"'$nacionalidad'",
//		                                          "cedula"=>"'$cedula'",
//		                                          "fecha"=>"'$llamada_fecha'",
//		                                          "telefono"=>"'$llamada_telefono'",
//		                                          "motivo"=>"'$llamada_motivo'",
//		                                          "resultado"=>"'$llamada_resultado'"));
//  if(!$resultado){
//    echo "Error al registrar datos. [codigo 1]";
//    }
//  
//  
//  $id_curso_aperturado=$db->Execute("
//        SELECT 
//          ca.id
//        FROM
//          asl.curso_aperturado as ca
//        WHERE          
//          ca.codigo LIKE '$codigo_curso_inscribir'
//        ");
//  $id_curso_aperturado=="";
//  if(array_key_exists(0,$id_curso_aperturado))
//    if(array_key_exists(0,$id_curso_aperturado[0]))
//      $id_curso_aperturado=$id_curso_aperturado[0][0];
//  if($id_curso_aperturado){
//    $resultado=$db->Insert("asl.inscrito",array(          "id_curso_aperturado"=>"$id_curso_aperturado",
//                                                          "nacionalidad_persona"=>"'$nacionalidad'",
//		                                          "cedula_persona"=>"'$cedula'",
//		                                          "fecha_inscripcion"=>"'$llamada_fecha'",
//		                                          "id_institucion"=>"'$id_institucion'",
//		                                          "id_estado"=>"2"));
//    if(!$resultado){
//      echo "Error al registrar datos. [codigo 3]";
//      }
//
//    }
//  
//    
//    
//  $accion="buscar";
//endif;

  
  
  
if($accion=="buscar" or $accion=="nuevo" or $accion==""):    
    if(trim($cedula)=="" or trim($nacionalidad)==""){
      }
    else{
      //$xml=XML::get("../../xml/persona.php?nacionalidad=$nacionalidad&cedula=$cedula");
      $xml=SIGA::xml("../../module/persona/xml/?nacionalidad=$nacionalidad&cedula=$cedula");
      //$xml = new DOMDocument();
      //$xml->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/base/persona.php?nacionalidad=$nacionalidad&cedula=$cedula");
      $persona = $xml->getElementsByTagName("persona");
      foreach( $persona as $persona ) {
        $nacionalidad=$persona->getAttribute('nacionalidad');
        $cedula=$persona->getAttribute('cedula');
        $primer_nombre=$persona->getAttribute('primer_nombre');
        $segundo_nombre=$persona->getAttribute('segundo_nombre');
        $primer_apellido=$persona->getAttribute('primer_apellido');
        $segundo_apellido=$persona->getAttribute('segundo_apellido');
        $correo=$persona->getAttribute('correo');
        $telefono=$persona->getAttribute('telefono');  
        }
        
      //$xml_institucion=XML::get("../../xml/asl/instituciones.php");
      ////$xml_institucion = new DOMDocument();
      ////$xml_institucion->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/asl/instituciones.php");
      //$institucion_tmp = $xml_institucion->getElementsByTagName("institucion");
      //$ci=0;
      //foreach( $institucion_tmp as $inst ) {
      //  $instituciones[$ci]["id"]=                  $inst->getAttribute('id');
      //  $instituciones[$ci]["nombre"]=              $inst->getAttribute('nombre');
      //  $ci++;
      //  }
      //      
      //$cursos=array();
      ////$xml_cursos = new DOMDocument();
      ////$xml_cursos->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/asl/persona_cursos.php?nacionalidad=$nacionalidad&cedula=$cedula");
      //$xml_cursos=XML::get("../../xml/asl/persona_cursos.php?nacionalidad=$nacionalidad&cedula=$cedula");
      //$persona_cursos = $xml_cursos->getElementsByTagName("curso");
      //$c=0;
      //foreach( $persona_cursos as $pc ){
      //  $cursos[$c]["id"]=                  $pc->getAttribute('id');
      //  $cursos[$c]["denominacion"]=        $pc->getAttribute('denominacion');
      //  $cursos[$c]["duracion"]=            $pc->getAttribute('duracion');
      //  $cursos[$c]["codigo"]=              $pc->getAttribute('codigo');
      //  $cursos[$c]["fecha_inicio"]=        $pc->getAttribute('fecha_inicio');
      //  $cursos[$c]["fecha_culminacion"]=   $pc->getAttribute('fecha_culminacion');
      //  $cursos[$c]["id_estado"]=           $pc->getAttribute('id_estado');
      //  $cursos[$c]["estado"]=              $pc->getAttribute('estado');
      //  $cursos[$c]["id_inscrito"]=         $pc->getAttribute('id_inscrito');
      //  $c++;
      //  }
      //
      //$cursos_realizar=array();
      //$xml_cursos_realizar=XML::get("../../xml/asl/persona_cursos_realizar.php?nacionalidad=$nacionalidad&cedula=$cedula");
      //
      ////$xml_cursos_realizar = new DOMDocument();
      ////$xml_cursos_realizar->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/asl/persona_cursos_realizar.php?nacionalidad=$nacionalidad&cedula=$cedula");
      //$persona_cursos_realizar = $xml_cursos_realizar->getElementsByTagName("curso");
      //$cr=0;
      //foreach( $persona_cursos_realizar as $pcr ) {
      //  $cursos_realizar[$cr]["id"]=                  $pcr->getAttribute('id');
      //  $cursos_realizar[$cr]["denominacion"]=        $pcr->getAttribute('denominacion');
      //  $cursos_realizar[$cr]["duracion"]=            $pcr->getAttribute('duracion');
      //  $cursos_realizar[$cr]["preinscrito"]=         $pcr->getAttribute('preinscrito');
      //  $cr++;
      //  }
      //
      //
      

    $instituciones=$db->Execute("SELECT i.id, i.nombre FROM modulo_asl.institucion as i ORDER BY i.nombre");

    $cursos=$db->Execute("SELECT
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

    $cursos_realizar=$db->Execute("SELECT
                                    c.id,
                                    c.denominacion,
                                    c.duracion,
                                    c.acronimo as codigo,
                                    c.prelacion,
                                    c.mostrar_preinscripcion as estatus,
                                    (select count(*) 
                                     from modulo_asl.preinscrito as p, modulo_asl.preinscrito_curso as pc 
                                     where 
                                       pc.descartar=0 and
                                       p.nacionalidad='".$nacionalidad."' and
                                       p.cedula='".$cedula."' and
                                       p.id=pc.id_preinscrito and 
                                       c.id=pc.id_curso
                                    ) as preinscrito
                                  FROM	  
                                    modulo_asl.curso as c
                                  WHERE
                                    c.mostrar_preinscripcion>0 and
                                    c.denominacion not in (
                                    SELECT		  
                                      c2.denominacion
                                    FROM
                                      modulo_asl.inscrito as i,
                                      modulo_asl.institucion as ins,
                                      modulo_asl.estado as e,
                                      modulo_asl.curso_aperturado as ca,
                                      modulo_asl.curso as c2,
                                      modulo_base.persona as p
                                    WHERE
                                      i.id_institucion=ins.id and
                                      i.id_estado=e.id and          
                                      i.id_curso_aperturado=ca.id and
                                      (i.id_estado=1 or i.id_estado=2 or i.id_estado=3) and
                                      ca.id_curso=c2.id and
                                      i.id_persona=p.id and
                                      p.identificacion_tipo='".$nacionalidad."' and p.identificacion_numero='".$cedula."'
                                    )
                                  ORDER BY
                                    c.denominacion
                                    ");
      
      $nombrecurso="";
      
      /*
      $cursos_abiertos=$db->Execute("
        SELECT 
          ca.*,
          c.denominacion as nombrecurso,
          tc.denominacion as turno
        FROM
          asl.curso_aperturado as ca,
          asl.curso as c,
          asl.turno_curso as tc
        WHERE
          c.id=ca.id_curso and
          ca.estado=1 and
          ca.id_turno=tc.id 
        ORDER BY
          c.denominacion
        ");
      */
      $ultima_institucion_persona=$db->Execute("
        SELECT 
          i.id_institucion
        FROM
          modulo_asl.inscrito as i,
          modulo_base.persona as p
        WHERE
          i.id_persona=p.id and
          p.identificacion_tipo='$nacionalidad' and
          p.identificacion_numero='$cedula'
        ORDER BY
          i.fecha_inscripcion DESC
        LIMIT 1
        ");

      if(!$ultima_institucion_persona){
        $ultima_institucion_persona=$db->Execute("
	        SELECT 
	          p.id_institucion
	        FROM
	          modulo_asl.preinscrito as p
	        WHERE
	          p.nacionalidad='$nacionalidad' and
	          p.cedula='$cedula'
	        ORDER BY
	          p.fecha DESC
	        LIMIT 1
	        ");
        }
      if(count($ultima_institucion_persona))
        $id_ultima_institucion_persona=$ultima_institucion_persona[0][0];

      //$llamadas_telefonicas=$db->Execute("
      //  SELECT 
      //    *
      //  FROM
      //    base.llamada_telefonica
      //  WHERE
      //    nacionalidad='$nacionalidad' and
      //    cedula='$cedula'
      //  ORDER BY
      //    fecha DESC
      //  ");
      
      
      
      //$x1=$persona_cursos->childNodes;
      //echo $x1->lenght;
      
      //echo $persona_cursos->childNodes->item(0)->length;
      //var_dump($persona_cursos_root);
      
      /*$c=0;
      foreach( $persona_cursos_root as $persona_cursos_root ){
        $persona_cursos = $persona_cursos_root->getElementsByTagName("curso");
        foreach( $persona_cursos as $persona_cursos ) {
          $cursos[$c]["id"]=                  $persona_cursos->getAttribute('id');
          $cursos[$c]["denominacion"]=        $persona_cursos->getAttribute('denominacion');
          $cursos[$c]["duracion"]=            $persona_cursos->getAttribute('duracion');
          $cursos[$c]["codigo"]=              $persona_cursos->getAttribute('codigo');
          $cursos[$c]["fecha_inicio"]=        $persona_cursos->getAttribute('fecha_inicio');
          $cursos[$c]["fecha_culminacion"]=   $persona_cursos->getAttribute('fecha_culminacion');
          $cursos[$c]["estado"]=              $persona_cursos->getAttribute('estado');
          $c++;
          }
        }*/
      
      if(trim($cedula)=="" or trim($nacionalidad)==""){
        $nacionalidad=$_REQUEST["nacionalidad"];
        $cedula=$_REQUEST["cedula"];
        }
    }
  
?>
<script>
    
    function SubmitBuscar(){
      document.getElementById('accion').value='buscar';
      document.getElementById('formulario').submit();
    }
    
    function SubmitGuardar(){
      document.getElementById('accion').value='guardar';
      document.getElementById('formulario').submit();
    }
    
    function SubmitEliminarLlamada(id_llamada){
      if(!confirm("Se eliminará el registro de la llamada.\n ¿Desea continuar?")) return;
      document.getElementById('id_llamada_eliminar').value=id_llamada;
      document.getElementById('accion').value='eliminar_llamada';      
      document.getElementById('formulario').submit();
    }
    
    function SubmitEliminarInscrito(id_inscrito){
      if(!confirm("Se eliminará la persona del curso.\n ¿Desea continuar?")) return;
      document.getElementById('id_inscrito').value=id_inscrito;
      document.getElementById('accion').value='eliminar_inscrito';      
      document.getElementById('formulario').submit();
    }
    
    function SubmitEliminarPreinscripcion(id_curso_preinscrito){
      if(!confirm("Se eliminará el curso preinscrito de la persona.\n ¿Desea continuar?")) return;
      document.getElementById('id_curso_preinscrito').value=id_curso_preinscrito;
      document.getElementById('accion').value='eliminar_curso_preinscrito';      
      document.getElementById('formulario').submit();
    }
    
  function formatDate(str){
    var d=String(str).split("-");
    return d[2]+"/"+d[1]+"/"+d[0];
    }

  function unformatDate(str){
    var d=String(str).split("/");
    return d[2]+"-"+d[1]+"-"+d[0];
    }
    
  var ICON_LOADING_SMALL="<DIV style=\"color : #959595;  text-align : center;\" align=\"middle\"><img src='../images/loading_small.gif' align=\"top\">&nbsp;Cargando...</DIV>";
  Ext.Loader.setConfig({enabled: true});


  var validPhone = /^([(][0]([4][12][246]|[2][0-9][0-9])[)][0-9]{3}[.][0-9]{2}[.][0-9]{2}([ ][(][0]([4][12][246]|[2][0-9][0-9])[)][0-9]{3}[.][0-9]{2}[.][0-9]{2})*)$/;
  Ext.apply(Ext.form.field.VTypes, {
    phone: function(val, field) {
      return validPhone.test(val);
      },
    phoneText: 'Número telefónico invalido.\nFormato: (0999)999.99.99'
    });

    
  function EditarPersona(){return;
    //crear el dialogo
    var value=new Array();
    value[0]=document.getElementById('nacionalidad').value;
    value[1]=document.getElementById('cedula').value;
    if(!value[0]) return;
    if(!value[1]) return;
    
    var dialogedit = Ext.create("form1006",{
        actionSave: function(){this.close();SubmitBuscar();},
        modal : true,
        value: value
        });
    
    dialogedit.show();
    }
    
</script>
</head>
<body onload="Ext.QuickTips.init();">
<div class='contenedor'>
  <br>
  <form id="formulario" action="" method="post" style=" padding: 0px; margin: 0px;">
    <input type='hidden' value='' id='id_llamada_eliminar' name='id_llamada_eliminar' />
    <input type='hidden' value='' id='id_inscrito' name='id_inscrito' />
    <input type='hidden' value='' id='id_curso_preinscrito' name='id_curso_preinscrito' />
    <div style="background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>DATOS PERSONALES</b></div>
    <div style="background-color: #F4F4F4;">
      <br />
      <input type="hidden" id="accion" name="accion" value="<?php echo $accion;?>" />
      <table cellpadding="3" cellspacing="0" border="0" align="center" style="border: none;" width="80%">
        <tbody>
          <tr valign="top">
            <td class="title_field" width="20%">C&Eacute;DULA</td>
            <td>
              <SELECT name="nacionalidad" id="nacionalidad">
                <OPTION <?php if($nacionalidad=="V") echo "selected";?>>V</OPTION>
                <OPTION <?php if($nacionalidad=="E") echo "selected";?>>E</OPTION>
                <OPTION <?php if($nacionalidad=="P") echo "selected";?>>P</OPTION>
                <OPTION <?php if($nacionalidad=="" or $nacionalidad==" ") echo "selected";?>>S/N</OPTION>
              </SELECT>
              <input type="text" id="cedula" name="cedula" value="<?php echo "$cedula";?>" />
              <input type="button" value="Buscar" onclick="SubmitBuscar()" />
              <!--<input type="button" value="Editar" onclick="EditarPersona()" />-->
            </td>
          </tr>
          <tr valign="top">
            <td class="title_field">NOMBRES/APELLIDOS</td>
            <td><input type="text" disabled id="nombres_apellidos" name="nombres_apellidos" value="<?php echo trim("$primer_nombre $segundo_nombre $primer_apellido $segundo_apellido");?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">TEL&Eacute;FONO</td>
            <td><input type="text" disabled id="telefono" name="telefono" value="<?php echo "$telefono";?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">CORREO ELECTR&Oacute;NICO</td>
            <td><input type="text" disabled id="correo" name="correo" value="<?php echo "$correo";?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">INSTITUCI&Oacute;N DONDE TRABAJA O ESTUDIA</td>
            <td>
              <SELECT  id="id_institucion" name="id_institucion" style="width: 100%;" value="$id_ultima_institucion_persona">
                <?php
                for($i=0;$i<count($instituciones);$i++){
                  $default_institucion="";
                  if($id_ultima_institucion_persona==$instituciones[$i]["id"]) $default_institucion="selected";
                  echo "<option value='".$instituciones[$i]["id"]."' $default_institucion>".$instituciones[$i]["nombre"]."</option>";
                  }
                ?>
              </SELECT>
            </td>
          </tr>
        </tbody>
      </table>
      <br />
      
      
      
      <div style="margin: 0px; padding: 0px;background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>CURSOS REALIZADOS</b></div>
      
      <?php if(count($cursos)>0):?>
      <table cellpadding="3" cellspacing="5" border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%">
        <tr style="background-color: #fff; font-weight: bold; text-align: center;">
          <td>CÓDIGO</td>
          <td>CURSO</td>
          <td>FECHA</td>
          <td>ESTADO</td>
          <td width='1%'></td>
        </tr>
      <?
      
      
      
      for($i=0;$i<count($cursos);$i++){
        $icon_eliminar_inscrito="";
        if($cursos[$i]["id_estado"]==2)
          $icon_eliminar_inscrito="<div class='buttons' style='float:right;'><img src='../../images/icon-listremove_16x16.png' title='Eliminar inscripción' onclick='SubmitEliminarInscrito(".$cursos[$i]["id_inscrito"].")' /></div>";
        echo "<tr style='background-color: #fff; font-size : 10px;'>
          <td style='white-space: nowrap;'>".$cursos[$i]["codigo"]."</td>
          <td>".$cursos[$i]["denominacion"]." (".$cursos[$i]["duracion"]." Horas)</td>
          <td style='text-align:center;'>".formatDate($cursos[$i]["fecha_inicio"])." - ".formatDate($cursos[$i]["fecha_culminacion"])."</td>
          <td style='text-align:center;'>".$cursos[$i]["estado"]."</td>
          <td>$icon_eliminar_inscrito</td>
        </tr>";
      }
      
      
      ?>

      </table>
      <?php
      else:
        echo "<div style='text-align: center;'>No se encontraron registros.</div>";
      
      endif;?>
      <br /><br />
        
      <div style="margin: 0px; padding: 0px; background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>CURSOS PREINSCRITOS</b></div>
      <table border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%"><?
      for($i=0;$i<count($cursos_realizar);$i++){
        $checked="";
        $icon_eliminar_preinscripcion="";
        if($cursos_realizar[$i]["preinscrito"]>0){
          $checked="checked";
          $icon_eliminar_preinscripcion="<div class='buttons' style='float:right;'><img src='../../image/icon/icon-listremove.png' title='Eliminar preinscripción' onclick='SubmitEliminarPreinscripcion(".$cursos_realizar[$i]["id"].")' /></div>";
        }
        
        echo "<tr style='background-color: #fff; font-size : 10px;'>
          <td width='1'><input type='checkbox' name='id_curso[]' value='".$cursos_realizar[$i]["id"]."'  $checked disabled /></td>
          <td>".$cursos_realizar[$i]["denominacion"]." (".$cursos_realizar[$i]["duracion"]." HORAS)</td>
          <td width='1%'>$icon_eliminar_preinscripcion</td>
        </tr>";
      }
      ?>
      </table>
      
      
      <br /><br />
      <!--
      
      <div style="margin: 0px; padding: 0px; background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>REGISTRO DE LLAMADAS</b></div>
      <table border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%">
        <tr style="background-color: #fff; font-weight: bold; text-align: center;">
          <td width='10%'>FECHA</td>
          <td width='20%'>NUMERO<br />TELEFÓNICO </td>
          <td width='30%'>MOTIVO</td>
          <td width='40%'>RESULTADO</td>
          <td width='0%'></d>
        </tr>
        <tr valign='top'>
	  <td class=''><?php echo date("d/m/Y h:i:sa");?><input type='hidden' name="llamada_fecha" style='width:100%;' value='<?php echo date("Y-m-d H:i:s");?>' /></td>
	  <td class=''>
	  <select style='width:100%;' name="llamada_telefono">
	    <?php
	    $telefonos=explode(" ",$telefono);
	    for($i=0;$i<count($telefonos);$i++)
	      echo "<option value='".$telefonos[$i]."'>".$telefonos[$i]."</option>";
	    ?>
	  </select>
	  </td>
	  <td class=''><textarea name="llamada_motivo" rows="3" id='text_motivo' style='resize:none; width:98%;'></textarea></td>
	  <td class='' colspan='2'>
	    <textarea rows="3" name="llamada_resultado" style='resize:none; width:98%;'></textarea>	    
	  </td>
	</tr>
	<tr style="">
          <td colspan='5'>
            <b>INSCRIBIR EN </b>
            
            <select id='select_cursos' name='codigo_curso_inscribir' onchange="document.getElementById('text_motivo').value=!value?'':'CONFIRMAR DISPONIBILIDAD PARA REALIZAR EL CURSO: '+value;">
               <option value=''></option>
               <?php
               for($s=0;$s<count($cursos_abiertos);$s++){
                 $title="".$cursos_abiertos[$s]["turno"];
                 echo "<option title='$title' value='".$cursos_abiertos[$s]["codigo"]."'>".$cursos_abiertos[$s]["codigo"]." [".$cursos_abiertos[$s]["nombrecurso"]."]</option>";
                 }
               ?>
            </select>
            
            <div class='buttons' style='float:right;'><img src='images/icons/icon_save.png' title='Registrar llamada' onclick='SubmitGuardar()' /></div>
          </td>
        </tr>        
        <?php
        for($i=0;$i<count($llamadas_telefonicas);$i++){
          echo "
          <tr style='background-color: #fff; font-size : 10px;'>
            <td>".date("d/m/Y h:i:sa",strtotime($llamadas_telefonicas[$i]["fecha"]))."</td>
            <td>".$llamadas_telefonicas[$i]["telefono"]."</td>
            <td>".$llamadas_telefonicas[$i]["motivo"]."</td>
            <td>".$llamadas_telefonicas[$i]["resultado"]."</td>
            <td><div class='buttons' style='float:right;'><img src='images/icons/icon_delete.png' title='Eliminar llamada' onclick='SubmitEliminarLlamada(".$llamadas_telefonicas[$i]["id"].")' /></div></td>
          </tr>";
        }        
        ?>
        
      </table>
      <br />
      -->
      
    </div>
  </form>




<?php

endif;
?>
</div>
</body>
</html>