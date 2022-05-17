<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;

$params=$_POST;

class MODULO{
  public static function onInit(){
    $access=SIGA::access("nomina");
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/html; charset=utf-8');
        periodo_nota::onGet(SIGA::param("id_periodo"),SIGA::param("id_nomina"));
        break;
      case "onList":  
      
      case "onSave":
      case "save":
        header('Content-Type: text/html; charset=utf-8');
        periodo_nota::onSave($access,SIGA::param("id_periodo"),SIGA::param("id_nomina"),SIGA::param("nota"));
        break;  
    }

  }  
}
MODULO::onInit();

class periodo_nota{
  public static function onGet($id_periodo, $id_nomina){
    $db=SIGA::DBController();  
    $sql="SELECT nota FROM modulo_nomina.periodo_nota WHERE id_periodo=$id_periodo and id_nomina=$id_nomina";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onSave($access, $id_periodo, $id_nomina, $nota){
    $db=SIGA::DBController();
    
    $db->Delete("modulo_nomina.periodo_nota","id_periodo=$id_periodo and id_nomina=$id_nomina");
    $db->Insert("modulo_nomina.periodo_nota",array("id_periodo"=>"$id_periodo","id_nomina"=>"$id_nomina","nota"=>"'$nota'"));
    
    print "{success: true, message: 'Datos guardados con exito.'}";
  }  
 
}
  
?>
