<?php
/**
 *  Permite registrar, modificar y eliminar horas en el registro de asistencia.
 *
 *  Es llamado por:
 *    modulo_asistencia/ingresar_hora.js y
 *    modulo_asistencia/core/index.php
 *  para registrar, modificar y eliminar horas en el registro de asistencia.
 *  
 *  @param  integer $id_persona    Identificador de la persona a registrar la asistencia.
 *  @param  string  $fecha         Fecha correspondiente al registro
 *  @param  string  $hora          Hora a ingresar en el registro.
 *                                 Cuando el campo es vacio, no se ingresa la hora en el listado,
 *                                 se usa vacio en la opcion eliminar desde modulo_asistencia/ingresar_hora.js
 *  @param  string  $hora_anterior Se usa para modificar una hora previa cuando $hora tiene valor,
 *                                 se usa vacio en la opcion eliminar desde modulo_asistencia/ingresar_hora.js
 *  @param  boolean $manual        Indica que el registro a ingresar es manual.
 *  @param  string  $imagen        Captura fotográfica tomada al marcar la asistencia
 *  @return boolean                Indica si el registro tuvo exito o no.
 *  
 *  
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */



//USO DE PARAMETROS:
//   modulo_asistencia/core/index.php
//     'registrar asistencia' -> id_persona fecha hora

//   modulo_asistencia/ingresar_hora.js
//     'ingresar nueva hora' -> id_persona fecha hora manual=true
//     'modificar hora previa' -> id_persona fecha hora hora_anterior manual=true
//     'eliminar hora' -> id_persona fecha hora='' hora_anterior

include_once("../../library/include.php");



if(!isset($_POST["id_persona"])){
  print "false";
  exit;
}
if(!isset($_POST["fecha"])){
  print "false";
  exit;
}
if(!isset($_POST["hora"])){
  print "false";
  exit;
}

$usuario_validador="default";
$manual=SIGA::param("manual");
if($manual==true or $manual=='t' or $manual=='true' or $manual==1){
  $manual='true';
  $usuario_validador="'".SIGA::user()."'";
}
else{
  $manual='false';
}


$hora_anterior=SIGA::param("hora_anterior");  
if($hora_anterior)
  $hora_anterior=date("H:i:s",strtotime($hora_anterior));



$id_persona=SIGA::param("id_persona");
$fecha=SIGA::param("fecha");
$hora=SIGA::param("hora");
if($hora)
  $hora=date("H:i:s",strtotime($hora));



//si hay una session abierta
if(SIGA::data()){
  $anio_modificar=date("Y",strtotime($fecha));
  if(!($anio_modificar==SIGA::data() or $anio_modificar==date("Y"))){
    print "{success: false, message: 'El año de trabajo (".SIGA::data().") no corresponde con la fecha del registro.'}";//print "false";
    exit;
  }
}




$db=SIGA::DBController("siga");


$return="";
//si es un registro nuevo, (insertar)
if($hora and !$hora_anterior): 
  /*
  $data=array("id_persona"=>"'$id_persona'",
              "fecha"=>"'$fecha'",
              "hora"=>"'$hora'",
              "manual"=>"$manual",
              "usuario_validador"=>"$usuario_validador"
              );
  $return=$db->Insert("modulo_asistencia.asistencia",$data);            
  */
  $return=$db->Execute("INSERT INTO modulo_asistencia.asistencia(id_persona,fecha,hora,manual,usuario_validador)
                       VALUES('$id_persona','$fecha','$hora',$manual,$usuario_validador) RETURNING id");
  if(isset($return[0][0]) and isset($_POST["imagen"])):
    $id=$return[0][0];
    if(!file_exists(SIGA::databasePath()."/asistencia_registro/"))
      mkdir(SIGA::databasePath()."/asistencia_registro/");
    
    $data=$_POST["imagen"];    
    list($type, $data)      = explode(',', $data);    
    if($type=="data:image/png;base64"){
      //crear imagen png en el servidor
      $data = str_replace(' ', '+', $data);
      $data = base64_decode($data);
      file_put_contents(SIGA::databasePath()."/asistencia_registro/$id.png", $data);
    }
  endif;
//si es modificar un registro (modificar)
elseif($hora and $hora_anterior):
  $data=array("hora"=>"'$hora'");
  $return=$db->Update("modulo_asistencia.asistencia",$data,"id_persona='$id_persona' and fecha='$fecha' and hora='$hora_anterior'");
//si es eliminar hora (eliminar)
elseif(!$hora and $hora_anterior):
  $return=$db->Execute("SELECT id FROM modulo_asistencia.asistencia WHERE id_persona='$id_persona' and fecha='$fecha' and hora='$hora_anterior'");
  if(isset($return[0]["id"])){
    $id=$return[0]["id"];
    $return=$db->Delete("modulo_asistencia.asistencia","id='$id'");
    if(file_exists(SIGA::databasePath()."/asistencia_registro/$id.png"))
      unlink(SIGA::databasePath()."/asistencia_registro/$id.png");
  }  
endif;

if(!$return)
  print "false";
else
  print "true";

?>