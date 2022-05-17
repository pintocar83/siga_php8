<?php
/*
El codigo es llamado desde la pagina web de la siguiente forma

<?php
$options = array(
    'http'=>array(
      'method'=>"POST",
      'header'=>"Content-type: application/x-www-form-urlencoded\r\n",
      'content'=>http_build_query($_POST)
  ));
$contexto = stream_context_create($options);  
print file_get_contents('http://aplicaciones.fundacite-sucre.gob.ve/publico/academia/preinscribir.php', false, $contexto);
?>

*/




header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors','On');

include_once("../../library/db.controller.php");
include_once("library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");




$nacionalidad=SIGA::paramUpper("nacionalidad");
$cedula=SIGA::param("cedula");
$accion=SIGA::param("accion");


if(!$accion)
  $accion="nuevo";


$primer_nombre="";
$segundo_nombre="";
$primer_apellido="";
$segundo_apellido="";
$institucion="";
$correo="";
$telefono="";

$cursos=array();
$cursos_realizar=array();

?>
<div style="font-family: sans-serif; font-size: 12px;">
<?php
if($accion=="registrar"):
  
  $id_curso=$_POST["id_curso"];
  if(count($id_curso)==0){
    echo "<div style='text-align:center;'>";
    echo "Debe seleccionar al menos un curso.<br />";
    echo "<a href='javascript:history.back();' title='Ir la página anterior'>Volver</a>";
    echo "</div>";
    exit;
  }
  
  
  $fecha=date("Y-m-d H:i:s");
  
  $nombres_apellidos=SIGA::paramUpper("nombres_apellidos");
  $telefono=SIGA::paramUpper("telefono");
  $correo=SIGA::param("correo");
  $id_institucion=SIGA::paramUpper("id_institucion");
  $turno="";
  
  if(SIGA::paramUpper("turno1"))
    $turno.=SIGA::paramUpper("turno1").",";
  if(SIGA::paramUpper("turno2"))
    $turno.=SIGA::paramUpper("turno2").",";
  if(SIGA::paramUpper("turno3"))
    $turno.=SIGA::paramUpper("turno3").",";
  if(SIGA::paramUpper("turno4"))
    $turno.=SIGA::paramUpper("turno4").",";
  $turno=trim($turno,",");
  
  $db=SIGA::DBController("siga_online");
  
  $result=$db->Execute("INSERT INTO modulo_asl.preinscrito(fecha,nacionalidad,cedula,nombres_apellidos,telefono,correo,id_institucion,turno)
                       VALUES('$fecha','$nacionalidad','$cedula','$nombres_apellidos','$telefono','$correo','$id_institucion','$turno') RETURNING id");
  
  
  if(!isset($result[0][0])){
    echo "Error al registrar datos. [codigo 1]";
    exit;
    }
  $id=$result[0][0];
  
  for($i=0;$i<count($id_curso);$i++){
    $result=$db->Insert("modulo_asl.preinscrito_curso",array("id_preinscrito"=>"'$id'","id_curso"=>"'".str_clear($id_curso[$i])."'"));
  }
  
  
  
  echo "<div style='text-align:center;'>";
  echo "El registro se realizó sin problemas.<br />";
  echo "<a href='?q=aslpreinscripcion' title='Volver'>Volver</a>";
  echo "</div>";

elseif($accion=="buscar" or $accion=="nuevo" or $accion==""):
  if($cedula and $nacionalidad){
    //$xml = new DOMDocument();
    //$xml->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/persona.php?nacionalidad=$nacionalidad&cedula=$cedula");
    $xml=SIGA::xml("../../module/persona/xml/?nacionalidad=$nacionalidad&cedula=$cedula");
    $persona = $xml->getElementsByTagName("persona");
    foreach( $persona as $persona ) {
      $nacionalidad=$persona->getAttribute('nacionalidad');
      $cedula=$persona->getAttribute('cedula');
      $primer_nombre=$persona->getAttribute('primer_nombre');
      $segundo_nombre=$persona->getAttribute('segundo_nombre');
      $primer_apellido=$persona->getAttribute('primer_apellido');
      $segundo_apellido=$persona->getAttribute('segundo_apellido');
      }
    
    $db=SIGA::DBController("siga_online");
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

/*
    $cursos_realizar=array();
    $xml_cursos_realizar = new DOMDocument();
    $xml_cursos_realizar->load("http://aplicaciones.fundacite-sucre.gob.ve/xml/asl/persona_cursos_realizar.php?nacionalidad=$nacionalidad&cedula=$cedula");
    $persona_cursos_realizar = $xml_cursos_realizar->getElementsByTagName("curso");
    $cr=0;
    foreach( $persona_cursos_realizar as $pcr ) {
      $cursos_realizar[$cr]["id"]=                  $pcr->getAttribute('id');
      $cursos_realizar[$cr]["denominacion"]=        $pcr->getAttribute('denominacion');
      $cursos_realizar[$cr]["duracion"]=            $pcr->getAttribute('duracion');
      $cursos_realizar[$cr]["preinscrito"]=         $pcr->getAttribute('preinscrito');
      $cursos_realizar[$cr]["codigo"]=              $pcr->getAttribute('codigo');
      $cursos_realizar[$cr]["prelacion"]=           $pcr->getAttribute('prelacion');
      $cursos_realizar[$cr]["estatus"]=             $pcr->getAttribute('estatus');
      $cr++;
      }*/
  }
  
?>
  <style>
  
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
  </style>
  <script>
    function SubmitBuscarEnter(evt){
      var nav4=window.Event?true:false;
      var key=nav4?evt.which:evt.keyCode;
      if(key==13){
        SubmitBuscar();
        return false;
        }
      return (key<=13 || key==127 || (key>=48 && key<=57));
    }
    
    function SubmitGuardar(){
      document.getElementById('accion').value='registrar';
      document.getElementById('cedula').value=String(document.getElementById('cedula').value).trim();
      document.getElementById('nombres_apellidos').value=String(document.getElementById('nombres_apellidos').value).trim();
      document.getElementById('telefono').value=String(document.getElementById('telefono').value).trim();
      document.getElementById('correo').value=String(document.getElementById('correo').value).trim();
      if(!document.getElementById('cedula').value ||
         !document.getElementById('nombres_apellidos').value ||
         !document.getElementById('telefono').value ||
         !document.getElementById('correo').value 
         ){
        alert("Existen campos vacios.");
        return;
      }      
      if(document.getElementById('turno1').checked==false &&
        document.getElementById('turno2').checked==false &&
        document.getElementById('turno3').checked==false &&
        document.getElementById('turno4').checked==false){
        alert("Debe seleccionar al menos un turno.");
        return;
      }      
      document.getElementById('formulario').submit();
    }
    
    function SubmitBuscar(){
      document.getElementById('accion').value='buscar'
      document.getElementById('formulario').submit();
    }
    
  </script>
  <div>    
    <b>INDICACIONES:<br></b>
    <div style="margin-left: 20px;">
      1)Introduzca su número de cédula y pulse 'buscar'.<br>
      2)Complete los datos personales.<br>
      3)Indique la institución donde trabaja o estudia, así como el turno que tiene disponible para realizar el o los cursos.<br>
      4)Marque los cursos que desea realizar.<br>
      5)Para finalizar pulse 'Guardar', para registrar la preinscripción.<br>
    </div>    
    <br>    
  </div>
  <br>
  <form id="formulario" action="" method="post" style=" padding: 0px; margin: 0px;">
    <div style="background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>DATOS PERSONALES</b></div>
    <div style="background-color: #F4F4F4;">
      <br />
      <input type="hidden" id="accion" name="accion" value="<?php echo $accion;?>" />
      <table cellpadding="3" cellspacing="0" border="0" align="center" style="border: none;" width="80%">
        <tbody>
          <tr valign="top">
            <td class="title_field" width="20%">C&Eacute;DULA</td>
            <td>
              <SELECT name="nacionalidad">
                <OPTION <?php if($nacionalidad=="V") echo "selected";?> title="VENEZOLANO">V</OPTION>
                <OPTION <?php if($nacionalidad=="E") echo "selected";?> title="EXTRANJERO">E</OPTION>
                <OPTION <?php if($nacionalidad=="P") echo "selected";?> title="PASAPORTE">P</OPTION>
              </SELECT>
              <input type="text" id="cedula" name="cedula" value="<?php echo "$cedula";?>" autocomplete="off" onkeypress="return SubmitBuscarEnter(event)" />
              <input type="button" value="Buscar" onclick="SubmitBuscar()" />
            </td>
          </tr>
          <tr valign="top">
            <td class="title_field">NOMBRES/APELLIDOS</td>
            <td><input type="text" id="nombres_apellidos" name="nombres_apellidos" value="<?php echo trim("$primer_nombre $segundo_nombre $primer_apellido $segundo_apellido");?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">TEL&Eacute;FONO</td>
            <td><input type="text" id="telefono" name="telefono" value="<?php echo "$telefono";?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">CORREO ELECTR&Oacute;NICO</td>
            <td><input type="text" id="correo" name="correo" value="<?php echo "$correo";?>" style="width: 100%;"></td>
          </tr>
          <tr valign="top">
            <td class="title_field">INSTITUCI&Oacute;N DONDE TRABAJA O ESTUDIA</td>
            <td>
              <!--<input type="hidden" id="institucion" name="institucion" value="<?php echo "$institucion";?>" style="width: 100%;">-->
              <SELECT  id="id_institucion" name="id_institucion">
                <?php
                for($i=0;$i<count($instituciones);$i++)
                  echo "<option value='".$instituciones[$i]["id"]."'>".$instituciones[$i]["nombre"]."</option>";
                ?>
              </SELECT>
            </td>
          </tr>
          <tr valign="top">
            <td class="title_field">DISPONIBILIDAD DE TIEMPO PARA REALIZAR CURSOS</td>
            <td>
              <input type="checkbox" id="turno1" name="turno1" value="1">LUNES A VIERNES (MAÑANA)<br />
              <input type="checkbox" id="turno2" name="turno2" value="2">LUNES A VIERNES (TARDE)<br />
              <input type="checkbox" id="turno3" name="turno3" value="3">SABADOS (MAÑANA)<br />
              <input type="checkbox" id="turno4" name="turno4" value="4">SABADOS (TARDE)<br />
            </td>
          </tr>
        </tbody>
      </table>
      <br />
      <div style="margin: 0px; padding: 0px;background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>CURSOS REALIZADOS</b></div>
      <?php if(count($cursos)>0):?>
      <table border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%">
        <tr style="background-color: #fff; font-weight: bold; text-align: center;">
          <td>CÓDIGO</td>
          <td>CURSO</td>
          <td>FECHA</td>
          <td>ESTADO</td>
        </tr>
      <?
      
      for($i=0;$i<count($cursos);$i++){
        echo "<tr style='background-color: #fff; font-size : 10px;'>
          <td style='white-space: nowrap;'>".$cursos[$i]["codigo"]."</td>
          <td>".$cursos[$i]["denominacion"]." (".$cursos[$i]["duracion"]." Horas)</td>
          <td>".formatDate($cursos[$i]["fecha_inicio"])."<br>".formatDate($cursos[$i]["fecha_culminacion"])."</td>
          <td>".$cursos[$i]["estado"]."</td>
        </tr>";
      }
      ?>
      </table>
      <?php
      else:
        echo "<div style='text-align: center;'>No se encontraron registros.</div>";
      endif;?>
      <br /><br />        
      <div style="margin: 0px; padding: 0px; background-color: #CECECE; text-align: center; color: #000000; border-radius: 5px 5px 0px 0px;"><b>CURSOS PREINSCRIBIR</b></div>
      <table border="0" style="margin: 0px; padding: 0px; border: #D6D6D6 solid 1px; font-size : 12px;line-height: 110%;" align="center" width="100%">
        <tr style="background-color: #fff; font-weight: bold; text-align: center;">
          <td></td>
          <td>CÓDIGO</td>
          <td>CURSO</td>
          <td>REQUISITO</td>
        </tr>
      <?
      for($i=0;$i<count($cursos_realizar);$i++){
        $checked_disabled="";
        if($cursos_realizar[$i]["preinscrito"]>0){
          $checked_disabled="checked disabled";
        }
        echo "<tr style='background-color: #fff; font-size : 10px;".($cursos_realizar[$i]["estatus"]==2?" color:red;":"")."' >
          <td width='1'><input type='checkbox' name='id_curso[]' value='".$cursos_realizar[$i]["id"]."'  $checked_disabled /></td>
          <td align='center'>".$cursos_realizar[$i]["codigo"]."</td>
          <td>".$cursos_realizar[$i]["denominacion"]." (".$cursos_realizar[$i]["duracion"]." HORAS)".($cursos_realizar[$i]["estatus"]==2?" (*)":"")."</td>
          <td align='center'>".$cursos_realizar[$i]["prelacion"]."</td>
        </tr>";
      }
      ?>
      </table>
      <br>
      <span style="color: red;">(*) Actualmente nos encontramos en espera (por parte de la Coordinacion Nacional de las ASL) para la incorporación de estos cursos a la nueva malla curricular.</span>  
      <br><br>
      <div style="text-align: center;"><input type="button" value="Guardar" onclick="SubmitGuardar()" style="" /></div>
      <br />
      <br />
      <br />
    </div>
  </form>
<?php
endif;
?>
</div>