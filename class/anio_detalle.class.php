<?php
class anio_detalle{
  public static function onGet(){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.anio_detalle WHERE anio=".SIGA::data()."";
    return $db->Execute($sql);
  }
  
  public static function onSave($mes_cerrado){
    $db=SIGA::DBController();
    
    $data=array();
    $data["mes_cerrado"]="'$mes_cerrado'";
    
    $result=$db->Update("modulo_base.anio_detalle",$data,"anio=".SIGA::data());
    //Si hay error al modificar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();      
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }
}
  
?>