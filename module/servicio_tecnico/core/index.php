<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 'On');



include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");


if(!SIGA::access("servicio_tecnico")){
  print "No tiene acceso al módulo";
  exit;
}


$creador=SIGA::userName();
//$creador=$_SESSION["name"];
$estado="0";
$nombre_persona="";

//print_r($_POST);
$arraytipos=array("mp","mc","ias","rp","se","ot");
function es_tipo($t,$atipos){
  for($i=0;$i<count($atipos);$i++)
    if($atipos[$i]==$t) return true;
  return false;
}

$nuevo=true;
if(isset($_POST["guardar"]))
if($_POST["guardar"]=="guardar" or $_POST["guardar"]=="firmar_cerrar"){
  $nuevo=false;
  $codigo=$_POST["codigo"];
  
  
  if($_POST["guardar"]=="firmar_cerrar"){
    //ini_set("display_errors","On");
    //verificar clave de ldap
    
    $n_ldap2=0;
    $ldap2[$n_ldap2]["host"]="ldap://newton.fundacite-sucre.gob.ve/";
    $ldap2[$n_ldap2]["port"]=389;
    $ldap2[$n_ldap2]["base"]="dc=fundacite-sucre,dc=gob,dc=ve";
    $ldap2[$n_ldap2]["protocol_version"]=3;
    $n_ldap2++;
    
    $ldap2[$n_ldap2]["host"]="ldaps://newton.fundacite-sucre.gob.ve/";
    $ldap2[$n_ldap2]["port"]=636;
    $ldap2[$n_ldap2]["base"]="dc=fundacite-sucre,dc=gob,dc=ve";
    $ldap2[$n_ldap2]["protocol_version"]=3;
    $n_ldap2++;
    
    $username=$_POST["persona"];
    $password=$_POST["clave"];
    
    $valido=0;
    for($i=0;$i<$n_ldap2;$i++){
      
      $ldap_conection2 = ldap_connect($ldap2[$i]["host"],$ldap2[$i]["port"]);
      if(!$ldap_conection2)
        continue;
      
      
      ldap_set_option($ldap_conection2, LDAP_OPT_PROTOCOL_VERSION, $ldap2[$i]["protocol_version"]);
      $ldap_bind2 = ldap_bind($ldap_conection2, "uid=$username,ou=people,".$ldap2[$i]["base"], $password);
      
      if ($ldap_bind2){        
        $justthese2 = array("cn","sn","mail");
        $filter2="uid=$username";
        $ldap_search2=ldap_search($ldap_conection2, $ldap2[$i]["base"],$filter2,$justthese2);
        $ldap_info2 = ldap_get_entries($ldap_conection2, $ldap_search2);
      
        $nombre_persona=$ldap_info2[0]["cn"][0]." ".$ldap_info2[0]["sn"][0];
        
        $valido=1;
        }
     
    }
    
    if(!$valido){
      header('Location: index.php?codigo='.$codigo.'&msj=1');
      exit;
    }
    
    $estado="1";
    //header('Location: index.php?codigo='.$codigo);
    
    
    
  }
  
  $fecha=$_POST["fecha"];
  $unidad=$_POST["unidad"];
  $persona=$_POST["persona"];
  $diagnostico=$_POST["diagnostico"];
  
  $tipo="";
  global $arraytipos;
  
  for($i=0;$i<count($arraytipos);$i++)
    if(array_key_exists($arraytipos[$i],$_POST))
      $tipo.=$arraytipos[$i]."|";
    
  $tipo=trim($tipo,"|");
  $tipos=explode("|",$tipo);
  $datalleservicio=$_POST["datalleservicio"];
  
  $cadena="$estado\n$creador\n$fecha\n$unidad\n$persona\n$diagnostico\n$tipo\n$datalleservicio\n$nombre_persona";
  
  file_put_contents("data/$codigo.txt",$cadena);
  
  
  header('Location: index.php?codigo='.$codigo);
}






if(array_key_exists("codigo",$_GET))
  if($_GET["codigo"]>0){
    $nuevo=false;
    $codigo=$_GET["codigo"];
    $archivo="";
    
    
    $estado="0";
    
    $fecha=date("d/m/Y");
    $unidad="";
    $persona="";
    $diagnostico="";
    $tipos=array();
    $datalleservicio="";
    $nombre_persona="";
    
    
    
    if(file_exists("data/$codigo.txt")){
      $archivo=file_get_contents("data/$codigo.txt");
       $linea=explode("\n",$archivo);
    
      $estado=!$linea[0]?"0":$linea[0];
      $creador=!$linea[1]?$creador:$linea[1];
      
      $fecha=$linea[2];
      if(!$fecha) $fecha=date("d/m/Y");
      $unidad=$linea[3];
      $persona=$linea[4];
      $diagnostico=$linea[5];
      $tipos=explode("|",$linea[6]);
      $datalleservicio=$linea[7];
      $nombre_persona=$linea[8];
    }
   
  }


if($nuevo){
  $codigo=file_get_contents("secuencia.txt");
  header('Location: index.php?codigo='.$codigo);
}


if(array_key_exists("nuevo",$_GET))
  if($_GET["nuevo"]){
    $codigo=file_get_contents("secuencia.txt");
    file_put_contents("secuencia.txt",$codigo+1);
    header('Location: index.php');
  }


?>


<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicio Técnico</title>
  <link rel="stylesheet" href="css/style.css">
  <script>
    function onSubmit(){
      if(!String(document.getElementById("persona").value).trim()){
        alert("Introduzca el nombre de la persona");
        return false;
      }
      if(!String(document.getElementById("diag").value).trim()){
        alert("Introduzca el diagnostico");
        return false;
      }
      
      return true;
    }
    function Guardar(){
      //document.getElementById("formulario").submit();
      
    }
    
    function onkeybuscar (e) {
      var eve = e || window.event;
      var keycode = eve.keyCode || eve.which || eve.charCode;
    
      if (keycode == 13) {
        window.location="?codigo="+document.getElementById("codigo").value;
        
        return false;
      }
      return true;
    }
    
    function onkeyfirmar (e) {
      var eve = e || window.event;
      var keycode = eve.keyCode || eve.which || eve.charCode;
    
      if (keycode == 13) {
        firmar_cerrar();
        
        return false;
      }
      return true;
    }
    
    function firmar_cerrar(){
      document.getElementById('guardar').value='firmar_cerrar';
      document.getElementById('formulario').submit();
    }
    
    function onLoad(){
      <?php
      if(isset($_GET["msj"]))
      if($_GET["msj"]==1)
        echo "alert('No se pudo firmar la planilla. \\nUsuario y/o Contraseña son invalidos.');";
      
      ?>
      
      
      document.getElementById('codigo').focus();
      
      
      
      
    }
    
  </script>
</head>
<body onload="onLoad();">
  <form id="formulario" action="index.php" method="post" onsubmit="return onSubmit()">
  <input type="hidden" value="" id="guardar" name="guardar"/>
  <input type="hidden" value="" id="clave" name="clave"/>
  <input type="hidden" value="<?php echo $estado;?>" id="estado" name="estado"/>
  <header>
    <img src="<?php print "../../../".SIGA::databasePath(false)."/config/cintillo_actual.jpg";?>" alt="" />
  </header>
  <section id="titulo1"><h1>servicio técnico</h1></section>
  <p class="titulo2 fech">fecha:
    <input type='text' name="fecha" id="fecha" value="<?php echo $fecha;?>" style="" />
  </p>
  <div>
    <b>Codigo: </b>
    <input type='text' name="codigo" id="codigo" value="<?php echo $codigo;?>" style="width: 100px;" onkeypress="return onkeybuscar();" autocomplete="off"/>
    <button type="button" name="buscar" onclick="window.location='?codigo='+document.getElementById('codigo').value">Buscar</button>
    <button type="button" name="anterior" onclick="window.location='?codigo=<?php echo (($codigo-1>0)?$codigo-1:$codigo);?>'">Anterior</button>
    <button type="button" name="siguiente" onclick="window.location='?codigo=<?php echo $codigo+1;?>'">Siguiente</button>
    <button type="button" name="ultimo" onclick="window.location='.'">Ultimo</button>
    <button type="button" name="nuevo" onclick="window.location='?nuevo=1'">Nuevo</button>
    <?php
    if($estado=="0"):
    ?>
    <button type="button" value="guardar" onclick="document.getElementById('guardar').value='guardar';document.getElementById('formulario').submit()">Guardar</button>
    <?php
    endif;
    ?>
  </div><br>
  <div><p class="titulo2">unidad atendida:</p></div>
  </div>
    <select name="unidad" id="uni">
      <option value="presidencia" <?php if($unidad=="presidencia") echo "selected";?>>Presidencia</option>
      <option value="administracion" <?php if($unidad=="administracion") echo "selected";?>>Unidad de Apoyo Administrativo y Recursos Humanos</option>
      <option value="planificacion_articulacion" <?php if($unidad=="planificacion_articulacion") echo "selected";?>>Unidad de Planificación, Gestión Social y Articulación</option>
      <option value="proyectos" <?php if($unidad=="proyectos") echo "selected";?>>Unidad de Programas y Proyectos</option>
      <option value="informatica" <?php if($unidad=="informatica") echo "selected";?>>Unidad de Tecnologias de Información y Comunicación</option>
      <option value="opsu" <?php if($unidad=="opsu") echo "selected";?>>OPSU</option>
    </select>
  </div>
  <section id="user">
  <div><p class="titulo2">usuario del equipo: </p></div>
    <input type="text" name="persona" id="persona" style="width: 500px;" value="<?php echo $persona;?>">
  </section>
  <br>
  <div>
    <p class="titulo2">diagnostico de la falla:</p>
  </div>
  <div>
    <textarea name="diagnostico" id="diag" cols="70" rows="5"><?php echo $diagnostico;?></textarea>
  </div>
  <br>
  <p class="titulo2">tipo de servicio realizado:</p>
  <table>
    <tr>
      <td><input type="checkbox" name="mp" <?php  if(es_tipo("mp",$tipos)) echo "checked";?>></td>
      <td><p class="tex">mantenimiento preventivo.</p></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="mc"  <?php  if(es_tipo("mc",$tipos)) echo "checked";?>></td>
      <td><p class="tex">mantenimiento correctivo.</p></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="ias"  <?php  if(es_tipo("ias",$tipos)) echo "checked";?>></td>
      <td><p class="tex">instalación/actualización de software.</p></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="rp"  <?php  if(es_tipo("rp",$tipos)) echo "checked";?>></td>
      <td><p class="tex">reemplazo de piezas.</p></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="se"  <?php  if(es_tipo("se",$tipos)) echo "checked";?>></td>
      <td><p class="tex">servicio técnico externo.</p></td>
    </tr>
    <tr>
      <td><input type="checkbox" name="ot"  <?php  if(es_tipo("ot",$tipos)) echo "checked";?>></td>
      <td><p class="tex">otro.</p></td>
    </tr>
  </table>
  <div>
    <p class="titulo2">detalles del servicio:</p>
  </div>
  <div>
    <textarea name="datalleservicio" id="datalleservicio" cols="70" rows="5"><?php echo $datalleservicio;?></textarea>
  </div>
  <footer>
    <div style="border-bottom: solid 1px black; width: 300px; ">
      <input type="text" name="creador" id="creador" style="border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 0px;" value="" readonly="true">
      <p class="titulo2 firma">realizado por: <?php echo $creador;?></p>
    </div>
    <div style="width: 30px;"></div>
    <div  style="border-bottom: solid 1px black; width: 300px; position: relative;">
      
      
      <br>
      <p class="titulo2 firma">conforme: <?php echo $nombre_persona;?>
      <?php
      if($estado=="0"):
      ?>
      <input type="password" name="clave" id="clave" style="width: 100px;" value="" <?php if($estado=="1") echo 'readonly="true"';?>>
      <button type="button" value="firmar_cerrar" onclick="firmar_cerrar()">Firmar y Cerrar</button>
      <?php
      endif;
      ?>
      </p>
      
    </div>
  </footer>
  
        <article style="font-size: 16px;">Unidad de Telemática e Innovación Tecnológica..!</article>
  </form>
</body>
</html>