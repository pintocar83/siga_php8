<?php
include_once("../../library/db.controller.php");
include_once("library/siga.config.php");
include_once("../../library/siga.class.php");

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$db=SIGA::DBController("siga_online");


$id_curso_aperturado=$_POST["id_curso_aperturado"];
$nacionalidad=$_POST["nacionalidad"];
$cedula=$_POST["cedula"];
$clave=$_POST["clave"];
$mensaje_fallo="";






$curso=$db->Execute("SELECT
											CA.codigo,
   										C.denominacion,
											split_part(P.denominacion,';',1) as primer_nombre,
											split_part(P.denominacion,';',3) as primer_apellido
										FROM
											modulo_asl.curso_aperturado as CA,
											modulo_asl.curso as C,
											modulo_asl.instructor AS I,
											modulo_base.persona as P
										WHERE
											CA.id='$id_curso_aperturado' AND
											CA.encuesta_clave='$clave' AND
											CA.id_curso=C.id AND
											CA.id_instructor=I.id AND
											I.id_persona=P.id 											
											");
if(count($curso)>0){
}
else{
	$mensaje_fallo="NO COINCIDE EL CURSO CON LA CLAVE";
}

$persona=$db->Execute("SELECT *
														 FROM modulo_asl.inscrito as i, modulo_base.persona as p
														 WHERE
																i.id_curso_aperturado='$id_curso_aperturado' AND
																i.id_persona=p.id and
																p.identificacion_tipo='$nacionalidad' and
																p.identificacion_numero='$cedula'");

if(count($persona)>0){
	
	
	
	
	
	
}
else{
	$mensaje_fallo="NO COINCIDE LA PERSONA CON EL CURSO SELECCIONADO";
}

//print $persona[0]["encuesta_clave"];
//
//if($persona[0]["encuesta_clave"]!=$clave){
//	$mensaje_fallo="NO COINCIDE EL CURSO CON LA CLAVE";
//}

$id_persona=$persona[0]["id_persona"];


//buscar si la persona ya lleno la encuesta
$existe_encuesta=$db->Execute("SELECT *
															FROM modulo_asl.curso_encuesta as i
															WHERE
															  i.id_curso_aperturado='$id_curso_aperturado' AND
																i.id_persona=".$persona[0]["id_persona"]);
if(count($existe_encuesta)>0){
	$mensaje_fallo="YA LA PERSONA LLENO LA ENCUESTA";
}


if($mensaje_fallo){
	print "<div align='center'>";
	print "$mensaje_fallo<br>";
  print "<a href='#' onclick='window.history.go(-2)'>Volver al inicio</a>";  
	print "</div>";
	exit;
}


//$curso=$db->Execute("SELECT
//										CA.codigo,
//										C.denominacion,
//										P.primer_nombre,
//										P.primer_apellido
//										FROM curso_aperturado as CA, curso as C, instructor AS I, base.persona as P
//										WHERE
//											CA.id='$id_curso_aperturado' AND
//											CA.id_curso=C.id AND
//											CA.id_instructor=I.id AND
//											I.nacionalidad_instructor=P.nacionalidad AND
//											I.cedula_instructor=P.cedula
//										");

/*
$persona=$db->Execute("
        SELECT
          p.*,
          pd.direccion,
          pd.correo,
          pd.telefono
        FROM
          base.persona as p LEFT OUTER JOIN base.persona_detalle as pd ON p.nacionalidad=pd.nacionalidad and p.cedula=pd.cedula
        WHERE
          p.nacionalidad='$nacionalidad' and
          p.cedula='$cedula'          
        ORDER BY
          p.nacionalidad, p.cedula
        ");

*/




?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8" >
			<title>Encuesta</title>
		  <!--<script type="text/javascript" src="validacion2.js"> </script>-->
		  <link rel="stylesheet" type="text/css" href="css/encuesta.css" media="screen">
	</head>
  <body>
	<div class="contenedor">
  	<img src="images/cintillo_actual.jpg" width="100%">
  	<form action="encuesta_paso3.php" method="POST" style="padding: 0px 20px 0px 20px;">
  		<div id="mensaje"></div>
      	<h3 class="titulo_encuesta">ENCUESTA</h3>
  			<!--<h4 class="titulo_secundarios"> Fecha: 
  				<script>
  					var mydate=new Date();
  					var year=mydate.getYear();
  					if (year < 1000)
  					year+=1900;
  					var day=mydate.getDay();
  					var month=mydate.getMonth()+1;
  					if (month<10)
  					month="0"+month;
  					var daym=mydate.getDate();
  					if (daym<10)
  					daym="0"+daym;
  					document.write("<smll><font face='Arial'><u>"+daym+"/"+month+"/"+year+"</u></font></smll>")
  				</script> </h4>-->
					<table border="0">
						<tr>
							<td class="titulo_secundarios">Curso:</td>
							<td rowspan="2" style="width: 20px;"></td>
							<td><?php print $curso[0]["codigo"]."<br>".$curso[0]["denominacion"]?></td>
						</tr>						
						<tr>
							<td class="titulo_secundarios">Facilitador:</td>
							<td><?php print $curso[0]["primer_nombre"]." ".$curso[0]["primer_apellido"]?></td>
						</tr>
					</table>
  				<!--<h4 class="titulo_secundarios">Curso: <?php print $curso[0]["codigo"]." ".$curso[0]["denominacion"]?></h4>
  				<h4 class="titulo_secundarios">Facilitador: <?php print $curso[0]["primer_nombre"]." ".$curso[0]["primer_apellido"]?></h4>-->
      <p>Seleccione su opinión sobre los siquientes aspectos de las actividad</p>
      <table class="borde_tabla" border="1" WIDTH="100%" cellpadding="5" cellspacing="5"> 
      <tr class='titulo_tabla1'>
        <td class='titulo2_tabla1'>ORGANIZACIÓN</td>
        <td>Excelente</td>
        <td>Muy Bueno</td>
        <td>Bueno</td>
        <td>Regular</td>
        <td>Deficiente</td>
      </tr> 
      <TR>
        <TD class='titulo2_tabla1'>Promoción de la actividad</TD>
        <TD><center><input type="radio" name="item1" value="5"></center></TD>
        <TD><center><input type="radio" name="item1" value="4"></center></TD>
        <TD><center><input type="radio" name="item1" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item1" value="2"></center></TD>
        <TD><center><input type="radio" name="item1" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Atenci&oacute;n del personal</TD>
        <TD><center><input type="radio" name="item2" value="5"></center></TD>
        <TD><center><input type="radio" name="item2" value="4"></center></TD>
        <TD><center><input type="radio" name="item2" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item2" value="2"></center></TD>
        <TD><center><input type="radio" name="item2" value="1"></center></TD>
      </TR>
    	<tr style="height: 10px;"></tr>
      <tr class='titulo_tabla1'>
        <td class='titulo2_tabla1'>INSTRUCTOR</td>
        <td>Excelente</td>
        <td>Muy Bueno</td>
        <td>Bueno</td>
        <td>Regular</td>
        <td>Deficiente</td>
      </tr> 
      <TR>
        <TD class='titulo2_tabla1'>Demostración del dominio del tema</TD>
        <TD><center><input type="radio" name="item3" value="5"></center></TD>
        <TD><center><input type="radio" name="item3" value="4"></center></TD>
        <TD><center><input type="radio" name="item3" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item3" value="2"></center></TD>
        <TD><center><input type="radio" name="item3" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Claridad y objetidad de la exposición</TD>
        <TD><center><input type="radio" name="item4" value="5"></center></TD>
        <TD><center><input type="radio" name="item4" value="4"></center></TD>
        <TD><center><input type="radio" name="item4" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item4" value="2"></center></TD>
        <TD><center><input type="radio" name="item4" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Capacidad para despertar atención</TD>
        <TD><center><input type="radio" name="item5" value="5"></center></TD>
        <TD><center><input type="radio" name="item5" value="4"></center></TD>
        <TD><center><input type="radio" name="item5" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item5" value="2"></center></TD>
        <TD><center><input type="radio" name="item5" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Capacidad para esclarecer dudas</TD>
        <TD><center><input type="radio" name="item6" value="5"></center></TD>
        <TD><center><input type="radio" name="item6" value="4"></center></TD>
        <TD><center><input type="radio" name="item6" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item6" value="2"></center></TD>
        <TD><center><input type="radio" name="item6" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Cumplimiento del programa</TD>
        <TD><center><input type="radio" name="item7" value="5"></center></TD>
        <TD><center><input type="radio" name="item7" value="4"></center></TD>
        <TD><center><input type="radio" name="item7" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item7" value="2"></center></TD>
        <TD><center><input type="radio" name="item7" value="1"></center></TD>
      </TR>
      <tr style="height: 10px;"></tr>
      <tr class='titulo_tabla1'>
        <td class='titulo2_tabla1'>METODOLOGÍA</td>
        <td>Excelente</td>
        <td>Muy Bueno</td>
        <td>Bueno</td>
        <td>Regular</td>
        <td>Deficiente</td>
      </tr> 
      <TR>
        <TD class='titulo2_tabla1'>Contenido tem&aacute;tico</TD>
        <TD><center><input type="radio" name="item8" value="5"></center></TD>
        <TD><center><input type="radio" name="item8" value="4"></center></TD>
        <TD><center><input type="radio" name="item8" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item8" value="2"></center></TD>
        <TD><center><input type="radio" name="item8" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Actividades pr&aacute;cticas</TD>
        <TD><center><input type="radio" name="item9" value="5"></center></TD>
        <TD><center><input type="radio" name="item9" value="4"></center></TD>
        <TD><center><input type="radio" name="item9" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item9" value="2"></center></TD>
        <TD><center><input type="radio" name="item9" value="1"></center></TD>
      </TR>
      <tr style="height: 10px;"></tr>
      <tr class='titulo_tabla1'>
        <TD class='titulo2_tabla1'>RECURSOS DIDÁCTICOS</TD>
        <td>Excelente</td>
        <td>Muy Bueno</td>
        <td>Bueno</td>
        <td>Regular</td>
        <td>Deficiente</td>
      </tr> 
      <TR>
        <TD class='titulo2_tabla1'>Material did&aacute;ctico</TD>
        <TD><center><input type="radio" name="item10" value="5"></center></TD>
        <TD><center><input type="radio" name="item10" value="4"></center></TD>
        <TD><center><input type="radio" name="item10" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item10" value="2"></center></TD>
        <TD><center><input type="radio" name="item10" value="1"></center></TD>
      </TR>
      <TR>
        <TD class='titulo2_tabla1'>Recursos audiovisuales</TD>
        <TD><center><input type="radio" name="item11" value="5"></center></TD>
        <TD><center><input type="radio" name="item11" value="4"></center></TD>
        <TD><center><input type="radio" name="item11" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item11" value="2"></center></TD>
        <TD><center><input type="radio" name="item11" value="1"></center></TD>
      </TR>
      <tr style="height: 10px;"></tr>
      <tr class='titulo_tabla1'>
        <td class='titulo2_tabla1'>SU CONOCIMIENTO SOBRE EL TEMA</td>
        <td>Excelente</td>
        <td>Muy Bueno</td>
        <td>Bueno</td>
        <td>Regular</td>
        <td>Deficiente</td>
      </tr> 
      <TR>
        <TD class='titulo2_tabla1'>Ante de la actividad</TD>
        <TD><center><input type="radio" name="item12" value="5"></center></TD>
        <TD><center><input type="radio" name="item12" value="4"></center></TD>
        <TD><center><input type="radio" name="item12" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item12" value="2"></center></TD>
        <TD><center><input type="radio" name="item12" value="1"></center></TD>
      </TR>
      <TR> 
        <TD class='titulo2_tabla1'>Después de la actividad</TD>
        <TD><center><input type="radio" name="item13" value="5"></center></TD>
        <TD><center><input type="radio" name="item13" value="4"></center></TD>
        <TD><center><input type="radio" name="item13" value="3" checked></center></TD>
        <TD><center><input type="radio" name="item13" value="2"></center></TD>
        <TD><center><input type="radio" name="item13" value="1"></center></TD>
      </TR> 
      </table>
      <br>
      <table  class="borde_tabla" border="1" WIDTH="100%" cellpadding="5" cellspacing="5">
        <tr>
          <TD class="titulostabla1">¿Recomendaría Usted esta actividad a otras personas?</td>
          <TD><b>Si:</b><input type="radio" name="item14" value="1" checked></TD>
          <TD><b>No:</b><input type="radio" name="item14" value="0"></TD>
       </tr>
      </table>
        <br>
      <table  class="borde_tabla" border="1" WIDTH="100%" cellpadding="5" cellspacing="5">
        <tr>
          <td class="titulostabla1">Sugerencia para el mejoramiento de la actividad</td>
        </tr>   
        <tr>
            <TD><textarea name="item15"  rows="5" cols="109" style="height: 100px; width: 100%; resize: none;"></textarea></TD>
        </tr>
      </table>
        <br>
      <!--<table border="1" WIDTH="100%">
        <tr>
          <td colspan="2" class="titulostabla1">¿Estaría interesado en continuar la formación?</td>
          <td colspan="2"><b>Si:</b><input type="radio" name="¿Estaría interesado en continuar la formación?" value=""> 
          </td>
          <td colspan="2"><b>No:</b><input type="radio" name="¿Estaría interesado en continuar la formación?" value=""> 
          </td>
        </tr>   
      </table>
      <table width="100%"e='border: inset 0pt'>En caso afirmativo seleccione con una "<b>FLECHA</b>" los cursos de      interés:
      </table>
      <table border="1" width="100%">
              <td width="50%" style='border: inset 0pt'>
                  <input type="checkbox" name="" value=""> Filosofía del Software Libre(20 Horas)<br>
                  <input type="checkbox" name="" value=""> Usuario Final (20 Horas).<br>
                  <input type="checkbox" name="" value=""> Soporte Técnico (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Administración Local (40 Horas).<br>  
                  <input type="checkbox" name="" value=""> Diseño Gráfico Inscape (20 Horas)<br>
                  <input type="checkbox" name="" value=""> Diseño Gráfico Gimp (20 Horas).<br>
                  <input type="checkbox" name="" value=""> Base de Datos Mysql (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Base de Datos Postgresql (40 Horas).<br> 
                  <input type="checkbox" name="" value=""> HTML (40 Horas).<br> 
      	      </td>
              <td width="50%" style='border: inset 0pt'>
                  <input type="checkbox" name="" value=""> Programación Python (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Programación Php (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Programación C (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Programación C++ (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Programación Gambas (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Programación Perl (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Manejadores de contenido en Software Libre (40 Horas)<br>
                  <input type="checkbox" name="" value=""> Otros:<input type="text" name="" style='width: 210px; border-bottom: 1px solid black; border-top: none; border-left: none; border-right: none;'> <br>
              </td>
      </table>-->  
      <br>
			<input type="hidden" name="id_persona" value="<?php print $id_persona;?>" />
			<input type="hidden" name="id_curso_aperturado" value="<?php print $id_curso_aperturado;?>" />
      <!--<fieldset>
       <legend><u>Datos Personales</u></legend>
      	<label>Nombre y Apellido:</label>&nbsp;&nbsp;&nbsp;<input readonly="true" value="<?php print $persona[0]["primer_nombre"]." ".$persona[0]["segundo_nombre"]." ".$persona[0]["primer_apellido"]." ".$persona[0]["segundo_apellido"]?>" title="Se necesita nombre y apellido" type="text" id="Nombre" size="62" maxlength="100" onkeypress="return soloLetras(event)">
      	<br>
      	<label>Cédula de identidad:</label><input readonly="true" value="<?php print $nacionalidad."-".$cedula;?>" title="Se necesita cedula de identidad" type="text" id="Cedula" size="24" maxlength="50" onkeypress="return solonumeros(event)"> <label>Nro. Teléfono:</label>&nbsp;<input readonly="true" value="<?php print $persona[0]["telefono"];?>" title="Se necesita numero de telefono" type="text" id="Numero" size="25" maxlength="50" onkeypress="return solonumeros(event)">
      	<br>
      	<label>Correo electrónico:</label> &nbsp;&nbsp;<input  readonly="true" value="<?php print $persona[0]["correo"];?>" type="email" id="correo" size="62" maxlength="100"> 
      </fieldset>-->
      
			<div align="center">
				<input class="btn" type="submit" value="Guardar">	
			</div>
				
			
      <br>
      <!--<input type="Reset" name"borrar" value="Borrar datos del formulario">-->
      <!--<h6 class="tiulo_secundarios2">Programa Academica de Software Libre-Sede Sucre</h6>-->
  	</form>
	</div>
	</body>
</html>