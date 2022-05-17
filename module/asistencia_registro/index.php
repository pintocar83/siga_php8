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
      case "onGet_Imagen":
        self::onGet_Imagen($access,SIGA::paramRequest("id"));
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
  
  public static function onGet_Imagen($access,$id){
    if(!$access) return;
    
    if(!file_exists(SIGA::databasePath()."/asistencia_registro/$id.png")){
      header("Content-Type: image/png");
      print base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==");
      exit;
    }
    
    $finfo = new finfo(FILEINFO_MIME);
    $type  = $finfo->file(SIGA::databasePath()."/asistencia_registro/$id.png");
    header("Content-Type: $type");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: inline; filename='$id.png'");
    readfile(SIGA::databasePath()."/asistencia_registro/$id.png");
  }
}

MODULO::onInit();  
?>