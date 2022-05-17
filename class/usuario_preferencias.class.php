<?php
class usuario_preferencias{
  public static function onSave($access,$background){
    if(!$access or !SIGA::user()) return;
    $db=SIGA::DBController();
    
    $preferencias=$db->Execute("SELECT preferencias FROM modulo_base.usuario WHERE usuario='".SIGA::user()."'");
    $preferencias=isset($result[0][0])?$result[0][0]:"";
    
    
    if(!$preferencias){
      $preferencias=array();
    }
    else{
      $preferencias=json_decode($preferencias,true);
    }
    
    $preferencias["background"]="$background";
    
    SIGA::userPreferences($preferencias);
    
    $preferencias=json_encode($preferencias);    
    return $db->Update("modulo_base.usuario",array("preferencias"=>"'".$preferencias."'"),"usuario='".SIGA::user()."'");
  }
}
?>