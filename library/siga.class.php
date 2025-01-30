<?php
class SIGA extends SIGA_CONFIG {
  private static $path;
  private static $session_previous;
  public static $DBMode=NULL;

  public static function access_array($value=NULL){
    if($value===NULL) return self::session_value("SIGA::access");

    $siga_access=array();
    $access=explode("\n",$value);
    for($i=0;$i<count($access);$i++){
      $tmp=explode("=",$access[$i]);
      $module=trim($tmp[0]);
      $type=isset($tmp[1])?trim($tmp[1]):"rw";

      //buscar si el acceso ya existe
      if(isset($siga_access[$module])){
        //verificar el tipo de acceso anterior
        if($type=="rw") //si el nuevo es rw sobre escribir el anterior
          $siga_access[$module]="rw";
        elseif($type=="a" and $siga_access[$module]=="r") //si el nuevo es solo guardar y el anterior es solo lectura, dejar 'a'
          $siga_access[$module]="a";
        elseif($type=="r" and $siga_access[$module]=="") //si el nuevo es solo lectura y el anterior es ninguno, dejar 'r'
          $siga_access[$module]="r";
      }
      else
        $siga_access[$module]=$type;
    }
    self::session_value("SIGA::access",$siga_access);
  }

  public static function access($module){
    return (isset(self::access_array()["ALL"])?self::access_array()["ALL"]:(isset(self::access_array()[$module])?self::access_array()[$module]:""));
  }

  public static function close(){
    $_SESSION=array();
    session_unset();
    session_destroy();
  }

  public static function css($file,$option="CACHE"){
    /*$file_cache=self::path()."/cache/".md5(realpath($file));
    if($option=="CACHE")
      if(file_exists("$file_cache")) return file_get_contents("$file_cache");
    if($option=="CACHE" or $option=="RE-CACHE"){
      exec("java -jar ".self::path()."/library/yuicompressor-2.4.8.jar --type css -o '$file_cache' '$file'");
      if(file_exists("$file_cache"))
        return file_get_contents("$file_cache");
    }*/
    //NO-CACHE, Ó ERROR AL OBTENER ARCHIVO CACHE
    return file_exists("$file")?file_get_contents("$file"):"";
  }

  public static function data($value=NULL){
    if($value===NULL) return self::session_value("SIGA::data");
    self::session_value("SIGA::data",$value);

    self::$data = self::dataAvailable();
    for($i=0;$i<count(self::$data);$i++)
      if(self::$data[$i]["id"]=="$value"){
        self::dataName(self::$data[$i]["nombre"]);
        return;
      }
    self::dataName("NOT FOUNT");
  }

  //retorna los años disponibles para una base de datos
  public static function dataAvailableOld($value=NULL){
    $connection_name=$value===NULL?self::session_value("SIGA::database"):$value;
    if($connection_name===NULL or !isset(self::$database[$connection_name]) or !isset(self::$database[$connection_name]["data"])) return NULL;
    $database_data=self::$database[$connection_name]["data"];

    $return=array();
    for($i=0;$i<count(self::$data);$i++)
      for($j=0;$j<count($database_data);$j++)
        if(self::$data[$i]["id"]==$database_data[$j]){
          $return[]=self::$data[$i];
          continue;
        }
    return $return;
  }

  //retorna los años disponibles para una base de datos
  public static function dataAvailable($value=NULL){
    $connection_name=$value===NULL?self::session_value("SIGA::database"):$value;
    if($connection_name===NULL or !isset(self::$database[$connection_name])) return NULL;
    $tmp=[];
    if(isset(self::$database[$connection_name]["data"])){
      $tmp=self::$database[$connection_name]["data"];
    }
    else{
      $db=self::DBController($connection_name);
      $sql="SELECT anio FROM modulo_base.anio_detalle order by anio";
      $result=$db->Execute($sql);
      for($i=0;$i<count($result);$i++)
        $tmp[]=$result[$i]["anio"];
      $db->Close();
    }

    $data=[];
    for($i=0;$i<count($tmp);$i++){
      $data[]=["id"=>$tmp[$i],"nombre"=>"Año ".$tmp[$i]];
    }

    return $data;
  }

  public static function database($value=NULL){
    if($value===NULL) return self::session_value("SIGA::database");
    self::session_value("SIGA::database",$value);
  }

  public static function databaseDefault(){
    return SIGA_CONFIG::$database_default;
  }

  public static function databasePath($absolute=true){
    return ($absolute?self::path()."/":"")."data/".(self::database()?self::database():self::databaseDefault());
  }

  public static function DBAvailable(){
    $return=array();
    foreach(self::$database as $key => $value){
      if($value["display"]=="t"){
        $data=self::dataAvailable($key);

        $return[]=array("id"=>"$key","description"=>$value["description"],"data"=>$data);
      }
    }
    return $return;
  }

  public static function DBController($value=NULL){
    $connection_name=$value===NULL?self::session_value("SIGA::database"):$value;
    if($connection_name===NULL or !isset(self::$database[$connection_name])) return NULL;
    $database_config=self::$database[$connection_name];
    $db=new DBController();
    $db->Connect(
      $database_config["driver"],
      $database_config["server"],
      isset($database_config["name"])?$database_config["name"]:"",
      isset($database_config["user"])?$database_config["user"]:"",
      isset($database_config["password"])?$database_config["password"]:"",
      isset($database_config["port"])?$database_config["port"]:""
    );
    if(self::$DBMode)
      $db->mode=self::$DBMode;
    return $db;
  }

  public static function dataName($value=NULL){
    if($value===NULL) return self::session_value("SIGA::dataName");
    self::session_value("SIGA::dataName",$value);
  }

  public static function configuration($property,$value=NULL){
    $db=self::DBController();
    if(!$value){
      $dato="'".implode("','",$property)."'";
      $result=$db->Execute("select * from modulo_base.configuracion where dato in ($dato)");
      if(!$result) return [];
      $return=array();
      for($i=0;$i<count($result);$i++)
        $return[$result[$i]["dato"]]=$result[$i]["valor"];
      return $return;
    }

    for($i=0;$i<count($property);$i++){
      $db->Delete("modulo_base.configuracion","dato='".$property[$i]."'");
      $result=$db->Insert("modulo_base.configuracion",["dato"=>"'".$property[$i]."'","valor"=>"'".$db->EscapeString($value[$i])."'"]);
      if(!$result)
        return ["success"=>"false","message"=>"Error al guardar los datos en '".$property[$i]."'", "error"=>$db->Error()];
    }
    return ["success"=>"true","message"=>"Datos guardados con éxito."];
  }

  public static function init(){
    date_default_timezone_set('America/Caracas');
    self::$path                       = str_replace("\\", "/",dirname(dirname(__FILE__)));
    self::$session_previous=array();
    self::$session_previous["name"]   = session_name("SIGA");
    session_start();
  }

  //retorna el codigo js de un archivo enviado el parametro $file. Por defecto minimiza el codigo y lo envia a la carpeta cache.
  public static function js($file,$option="CACHE"){
    /*$option="NO-CACHE";
    $file_cache=self::path()."/cache/".md5(realpath($file));
    if($option=="CACHE")
      if(file_exists("$file_cache")) return file_get_contents("$file_cache");
    if($option=="CACHE" or $option=="RE-CACHE"){
      exec("java -jar ".self::path()."/library/yuicompressor-2.4.8.jar --type js -o '$file_cache' '$file'");
      if(file_exists("$file_cache"))
        return file_get_contents("$file_cache");
    }*/
    //NO-CACHE, Ó ERROR AL OBTENER ARCHIVO CACHE
    return file_exists("$file")?file_get_contents("$file"):"";
  }

  public static function clear($str){
    return trim(str_replace(array("\"","'","/*","*/","--"),"",$str));
  }

  public static function param($value,$clear=true){
    return self::paramPost($value,$clear);
  }

  public static function paramGet($value,$clear=true){
    $string=isset($_GET[$value])?$_GET[$value]:"";
    if($clear) return trim(str_replace(array("\"","'","/*","*/","--"),"",$string));
    return $string;
  }

  public static function paramPost($value,$clear=true){
    $string=isset($_POST[$value])?$_POST[$value]:"";
    if($clear) return trim(str_replace(array("\"","'","/*","*/","--"),"",$string));
    return $string;
  }

  public static function paramRequest($value,$clear=true){
    $string=isset($_REQUEST[$value])?$_REQUEST[$value]:"";
    if($clear) return trim(str_replace(array("\"","'","/*","*/","--"),"",$string));
    return $string;
  }

  public static function paramUpper($value){
    return mb_strtoupper(self::param($value),'UTF-8');
  }

  public static function path(){
    return self::$path;
  }

  private static function session_value($key,$value=NULL){
    if($value===NULL) return isset($_SESSION[$key])?$_SESSION[$key]:NULL;
    $_SESSION[$key]=$value;
  }

  public static function cookie($key,$value=NULL,$expire=NULL){
    if($value===NULL) return isset($_COOKIE[$key])?$_COOKIE[$key]:NULL;
    if($expire===NULL) $expire=strtotime('+360 days');
    setcookie($key,$value,$expire);
  }

  public static function session_previous(){
    return self::$session_previous;
  }

  public static function user($value=NULL){
    if($value===NULL) return self::session_value("SIGA::user");
    self::session_value("SIGA::user",$value);
  }

  public static function userName($value=NULL){
    if($value===NULL) return mb_convert_case(self::session_value("SIGA::userName"), MB_CASE_TITLE, "UTF-8");
    self::session_value("SIGA::userName",$value);
  }

  public static function userEmail($value=NULL){
    if($value===NULL) return self::session_value("SIGA::userEmail");
    self::session_value("SIGA::userEmail",$value);
  }

  public static function userPassword($value=NULL){
    if($value===NULL) return base64_decode(self::session_value("SIGA::userPassword"));
    self::session_value("SIGA::userPassword",base64_encode($value));
  }

  public static function userPreferences($value=NULL){
    if($value===NULL) return self::session_value("SIGA::userPreferences");
    self::session_value("SIGA::userPreferences",$value);
  }

  public static function xml($file){
    $xml = new DOMDocument();
    $xml->load("http://".$_SERVER["SERVER_NAME"].dirname($_SERVER["PHP_SELF"])."/$file", LIBXML_NOWARNING | LIBXML_NOENT | LIBXML_DTDLOAD);
    return $xml;
  }

  public static function xmlArray($file){
    $xml = self::xml($file);

    return NULL;
  }


}

SIGA::init();



?>
