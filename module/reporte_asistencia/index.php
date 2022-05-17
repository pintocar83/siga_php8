<?php
/**
 *  Definición de las acciones que realiza 'Sistema de Asistencia - Reportes PDF'.
 *
 *  Define la acciones para cargar la interfaz del 'Sistema de Asistencia - Reportes PDF'
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

$access="rw";
switch($action){
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    reporte_asistencia::onCss($access);
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    reporte_asistencia::onJavascript($access);
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}




class reporte_asistencia{
  public static function onCss($access){
    if(!$access) return;
    print SIGA::css("main.css");
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
 
}





  
?>
