<?php
/**
 *  Definición de las acciones que realiza 'Presupuesto - Ejecución'.
 *
 *  Define la acciones para cargar la interfaz del módulo 'Presupuesto - Ejecución'
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2015, FUNDACITE Sucre 
 *  
 *  @version 2015.02.05
 */
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO{
  public static function onInit(){
    $access=SIGA::access("reporte_ejecucion");
    $access="rw";
    switch($_REQUEST["action"]){
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        print self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        print self::onJavascript($access);
        break;
    }    
  }  
  
  public static function onCss($access){
    if(!$access) return;
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }
}

MODULO::onInit();
?>