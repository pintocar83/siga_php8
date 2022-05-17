<?php
include("../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;

$params=$_POST;

class MODULO{
  public static function onInit(){
    $access=Session::access("modulo_nomina/hoja_calculo");//null,r,rw,a
    $access="rw";
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/html; charset=utf-8');
        periodo_nota::onGet(getParamClear("id_periodo"),getParamClear("id_nomina"));
        break;
      case "onList":  
      
      case "onSave":
      case "save":
        header('Content-Type: text/html; charset=utf-8');
        periodo_nota::onSave($access,getParamClear("id_periodo"),getParamClear("id_nomina"),getParamClear("nota"));
        break;  
    }

  }  
}
MODULO::onInit();

class periodo_nota{
  public static function onGet($id_periodo, $id_nomina){
    $db=new DBController();
    $db->ConnectQuick("siga");    
    $sql="SELECT nota FROM modulo_nomina.periodo_nota WHERE id_periodo=$id_periodo and id_nomina=$id_nomina";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onSave($access, $id_periodo, $id_nomina, $nota){
    $db=new DBController();
    $db->ConnectQuick("siga"); 
    
    $db->Delete("modulo_nomina.periodo_nota","id_periodo=$id_periodo and id_nomina=$id_nomina");
    $db->Insert("modulo_nomina.periodo_nota",array("id_periodo"=>"$id_periodo","id_nomina"=>"$id_nomina","nota"=>"'$nota'"));
    
    print "{success: true, message: 'Datos guardados con exito.'}";
  }  
 
}
  
?>
