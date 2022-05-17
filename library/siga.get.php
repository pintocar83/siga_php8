<?php
header('Content-Type: text/plain; charset=UTF-8');
include_once("siga.config.php");
include_once("siga.class.php");




$return=array();

switch(SIGA::param("action")){
  case "session":
    include_once("functions/letra_fecha_actual.php");
    $return["session"]=SIGA::user()?true:false;
    $return["fecha_letra"]=letra_fecha_actual();
    $return["fecha"]=date("Y-m-d");
    $return["hora"]=date("h:i a");
    break;
  case "access":
    $return["access"]=SIGA::access(SIGA::param("module"));
    break;
  case "session_hash":    
    $return["session"]=base64_encode(base64_encode(SIGA::user()."/".SIGA::userPassword()));
    break;
  case "date":
    $return["result"]=date(SIGA::param("format"));
    break;
  case "cmd[zbarimg]":
    $image_data=SIGA::param("image");
    if(!$image_data){
      $return["result"]="null";
      break;
    }    
    list($type, $image_data)=explode(',', $image_data);  
    if($type!="data:image/png;base64"){
      $return["result"]="null";
      break;
    }  
    //crear imagen png en el servidor
    $image_data = str_replace(' ', '+', $image_data);
    $image_data = base64_decode($image_data);
    file_put_contents(SIGA::path().'/cache/tmp_qr.png', $image_data);    
    //comparar la imagen png con zbar
    $result=exec("zbarimg -q ".SIGA::path()."/cache/tmp_qr.png");
    if(!$result){
      $return["result"]="null";
      break;
    }
    $return["result"]=$result;
    break;
}



print json_encode($return);


?>