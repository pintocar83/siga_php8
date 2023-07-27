<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO {
  public static function onInit(){
    switch($_REQUEST["action"]){
      case "onChange":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onChange(SIGA::param("data")));
        break;
    }
  }

  public static function onChange($data){
    $database=SIGA::database();
    $user=SIGA::user();
    if(!$database || !$user)
      return ["success"=>false, "message"=>"Sesión de usuario invalida."];
    $db=SIGA::DBController();

     //Buscar el usuario en la tabla usuario
    $sql="
        select
          up.perfil
        from
          modulo_base.usuario as u,
          modulo_base.usuario_perfil as up
        where
          u.usuario='$user' and
          u.activo and
          u.id=up.id_usuario and
          up.anio='$data' and
          up.activo
    ";
    $usuario=$db->Execute($sql);
    if(!isset($usuario[0]))
      return ["success"=>false, "message"=> "Perfil de acceso no valido para el usuario en el año de trabajo seleccionado."];

    $perfil=explode("\n",$usuario[0]["perfil"]);
    $acceso="";
    for($i=0;$i<count($perfil);$i++){
      $acceso_perfil=$db->Execute("SELECT acceso FROM modulo_base.usuario_perfil_acceso WHERE perfil='".$perfil[$i]."'");
      if(isset($acceso_perfil[0][0]))
        $acceso.=$acceso_perfil[0][0]."\n";
    }
    $acceso=trim($acceso,"\n");
    SIGA::data($data);
    $descripcion=SIGA::$database[$database]["description"];
    for($i=0;$i<count(SIGA::$data);$i++)if(SIGA::$data[$i]["id"]==$data){$descripcion.=" - ".SIGA::$data[$i]["nombre"];break;}
    SIGA::dataName($descripcion);
    SIGA::access_array($acceso);
    return ["success"=>true];
  }
}
MODULO::onInit();
?>