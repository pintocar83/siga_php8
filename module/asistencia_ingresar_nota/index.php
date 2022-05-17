<?php
/**
 *  Definición de las acciones que realiza 'Sistema de Asistencia - Ingresar/Editar Nota'.
 *
 *  Define la acciones para cargar la interfaz, y las operaciones en la base de datos:
 *  obtener, guardar y eliminar registros; específicamente en la tabla
 *  modulo_asistencia.asistencia_nota
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
include_once("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];


$access=SIGA::access("asistencia_ingresar_nota");//null,r,rw,a

switch($action){
  case "onGet":
  case "get":
    header('Content-Type: text/plain; charset=utf-8');
    asistencia_ingresar_nota::onGet(SIGA::param("id"));
    break; 
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    asistencia_ingresar_nota::onSave($access,
                          SIGA::param("id"),
                          SIGA::paramUpper("tipo"),
                          SIGA::param("descripcion"),
                          SIGA::param("id_persona"),
                          SIGA::param("fecha"));
    break;  
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    asistencia_ingresar_nota::onDelete($access,SIGA::param("id"));
    break;  
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    asistencia_ingresar_nota::onCss($access);
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    asistencia_ingresar_nota::onJavascript($access);
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "{success: false, message: 'Acción no encontrada: \"$action\"'}";
}


class asistencia_ingresar_nota{
  public static function onCss($access){
    if(!$access) return;
    print SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_asistencia.asistencia_nota WHERE id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onSave($access,
                                $id,
                                $tipo,
                                $descripcion,
                                $id_persona,
                                $fecha){
    $db=SIGA::DBController(); 
    
    if(date("Y",strtotime($fecha))!=SIGA::data()){
      print "{success: false, message: 'El año de trabajo (".SIGA::data().") no corresponde con la fecha de la nota.'}";
      return;
    }
    
    if(!$tipo){
      print "{success: false, message: 'Error. El campo tipo de nota está vacío.'}";
      return;
    }
    if(!$descripcion){
      print "{success: false, message: 'Error. El campo descripción está vacío.'}";
      return;
    }
    if(!$id_persona){
      print "{success: false, message: 'Error. El parametro id_persona es nulo.'}";
      return;
    }
    
    if(!strtotime($fecha)){
      print "{success: false, message: 'Error. El parametro fecha es invalido.'}";
      return;
    }

    $data=array("id_persona"=>"'$id_persona'",
                "fecha"=>"'$fecha'",
                "tipo"=>"'$tipo'",
                "descripcion"=>"'$descripcion'"
                );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $result=$db->Update("modulo_asistencia.asistencia_nota",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("modulo_asistencia.asistencia_nota",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result){      
      print "{success: false, message: \"Error al guardar en la tabla: curso_aperturado. Detalle: ".$db->GetMsgError()."\"}";
      return;
    }
    print "{success: true, message: 'Datos guardados con exito.'}";
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }
    
    $result=$db->Delete("modulo_asistencia.asistencia_nota","id='$id'");
    if(!$result){                    
      print "{success: false, message: \"Error al guardar en la tabla: modulo_asistencia.asistencia_nota. Detalle: ".$db->GetMsgError()."\"}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
 
}
?>