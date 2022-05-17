<?php
/**
 *  Definición de la interfaz 'Sistema de Asistencia - Validar Asistencia'.
 *
 *  La interfaz permite al coordinador de la unidad validar la hora de entrada y salida 
 *  del personal bajo su responsabilidad.
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2015.08.03
 */
include_once("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];


$access="rw";

switch($action){
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    asistencia_validar::onCss();
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    asistencia_validar::onJavascript($access);
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}

class asistencia_validar{
  public static function onCss(){    
    print SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  } 
}
?>