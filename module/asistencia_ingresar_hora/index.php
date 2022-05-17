<?php
/**
 *  Definición de las acciones que realiza 'Sistema de Asistencia - Ingresar/Editar Hora'.
 *
 *  Define la acciones para cargar la interfaz.
 *  El ingreso y modificación de las horas se realiza desde modulo_asistencia/core/registrar.php
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
    $access=SIGA::access("asistencia_ingresar_hora");    
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