<?php
include_once("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];
$params=$_POST;

switch($action){
  case "onLogin":
    header('Content-Type: text/html; charset=utf-8');
    login::onLogin(SIGA::param("username"), SIGA::param("password"), SIGA::param("data"), SIGA::param("database"));
    break;
  case "onDBAvailable":
    header('Content-Type: text/html; charset=utf-8');
    print json_encode(login::onDBAvailable());
    break;
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    login::css();
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    login::javascript();
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}




class login{
  public static function css(){
    print SIGA::css("main.css");
  }
    
  public static function javascript(){
    print SIGA::js("main.js");
  }
  
  public static function ldap($user,$password){
    ini_set("display_errors","Off");
    $n_ldap=0;
    $ldap[$n_ldap]["host"]="ldaps://newton.fundacite-sucre.gob.ve/";
    $ldap[$n_ldap]["port"]=636;
    $ldap[$n_ldap]["base"]="dc=fundacite-sucre,dc=gob,dc=ve";
    $ldap[$n_ldap]["protocol_version"]=3;
    $n_ldap++;

    $ldap[$n_ldap]["host"]="ldap://newton.fundacite-sucre.gob.ve/";
    $ldap[$n_ldap]["port"]=389;
    $ldap[$n_ldap]["base"]="dc=fundacite-sucre,dc=gob,dc=ve";
    $ldap[$n_ldap]["protocol_version"]=3;
    $n_ldap++;
    
    for($i=0;$i<$n_ldap;$i++){
      $ldap_conection = ldap_connect($ldap[$i]["host"],$ldap[$i]["port"]);
      if(!$ldap_conection)
        continue;
      
      ldap_set_option($ldap_conection, LDAP_OPT_PROTOCOL_VERSION, $ldap[$i]["protocol_version"]);
      $ldap_bind = ldap_bind($ldap_conection, "uid=$user,ou=people,".$ldap[$i]["base"], $password);
      
      if ($ldap_bind){        
        $justthese = array("cn","sn","mail");
        $filter="uid=$user";
        $ldap_search=ldap_search($ldap_conection, $ldap[$i]["base"],$filter,$justthese);
        $ldap_info = ldap_get_entries($ldap_conection, $ldap_search);
        return true;
        }
      }
    return false;
  }
  
  public static function md5($user,$password,$password_database){
    if(md5($password)==$password_database)      
      return true;
    return false;
  }
  
  public static function onLogin($user,$password,$data,$database="siga"){    
    $db=SIGA::DBController($database);
    $result_login=false;
    //exit;
    //Buscar el usuario en la tabla usuario
    $sql=" 
        select
          *
        from
          modulo_base.persona as p,
          modulo_base.usuario as u,
          modulo_base.usuario_perfil as up                              
        where
          p.id=u.id_persona_responsable and
          u.usuario='$user' and
          u.activo and
          u.id=up.id_usuario and
          up.anio='$data' and                              
          up.activo
    ";
    $usuario=$db->Execute($sql);
    //print $sql;
    //print_r($usuario);
    if($usuario and count($usuario)>0){
      if(strpos($usuario[0]["denominacion"],";")===false){
        $username=$usuario[0]["denominacion"];
      }
      else{
        list($primer_nombre, ,$primer_apellido)=explode(";",$usuario[0]["denominacion"]);
        $username=$primer_nombre." ".$primer_apellido;
      }
      $email=$usuario[0]["correo"];
      $preferences=!$usuario[0]["preferencias"]?array():json_decode($usuario[0]["preferencias"],true);
      
      $perfil=explode("\n",$usuario[0]["perfil"]);
      $acceso="";
      for($i=0;$i<count($perfil);$i++){
        $acceso_perfil=$db->Execute("SELECT acceso FROM modulo_base.usuario_perfil_acceso WHERE perfil='".$perfil[$i]."'");
        if(isset($acceso_perfil[0][0]))
          $acceso.=$acceso_perfil[0][0]."\n";        
      }
      $acceso=trim($acceso,"\n");
      
      $result_password=json_decode($usuario[0]["clave"],true);
      switch($result_password["type"]){
        case "ldap_fundacite":
          $result_login=self::ldap($user,$password);
          break;
        case "plain":
          $result_login=($password==$result_password["value"])?true:false;
          break;
        case "md5":
        case "sha1":    
        case "sha256":  
        case "sha512":
          $result_login=(hash($result_password["type"],$password)==$result_password["value"])?true:false;
          break;
      }
    }
    //<b>Notice</b>:  Undefined variable: password_database in <b>/var/www/siga/modulo_base/login.php</b> on line <b>142</b><br />
    if($result_login){
      SIGA::database($database);
      SIGA::data($data);
      $descripcion=SIGA::$database[$database]["description"];
      for($i=0;$i<count(SIGA::$data);$i++)if(SIGA::$data[$i]["id"]==$data){$descripcion.=" - ".SIGA::$data[$i]["nombre"];break;}
      SIGA::dataName($descripcion);
      SIGA::user($user);
      SIGA::userPassword($password);
      SIGA::userName($username);
      SIGA::userEmail($email);
      SIGA::userPreferences($preferences);
      
      SIGA::access_array($acceso);      
      print '{"success": true, "message": ""}';
    }
    else{
      SIGA::close();
      print '{"success": false, "message": "Usuario y/o contraseña invalido."}';
    }
  }
  
  public static function onDBAvailable(){
    return SIGA::DBAvailable();
  }
}





  
?>
