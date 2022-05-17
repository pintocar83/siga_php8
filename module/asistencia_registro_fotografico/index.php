<?php
/**
 *  Definición de la interfaz 'Sistema de Asistencia - Registro Clásico'.
 *
 *  La interfaz permite registrar (a traves del código) la hora de entrada y salida del personal
 *  activo en el sistema de asistencia, así como visualizar rapidamente su último ingreso. 
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;

class MODULO{
  public static function onInit(){
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