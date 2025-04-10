<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO {
  public static function onInit(){
    switch($_REQUEST["action"]){
      case "onChange":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onChange(SIGA::param("data"), SIGA::param("crear")));
        break;
    }
  }

  public static function onChange($data, $crear=false){
    $database=SIGA::database();
    $user=SIGA::user();
    if(!$database || !$user)
      return ["success"=>false, "message"=>"Sesión de usuario invalida."];
    $db=SIGA::DBController();

    if($crear){
      if(isset(SIGA::$database[$database]["data"])){
        return ["success"=>false, "message"=>"La configuración de años se encuentra estática, elimine 'data' en el array de configuración: siga.config.php"];
      }
      //PROCESO DE APERTURA DE NUEVO AÑO
      //Agregar el año a crear
      $db->Insert("modulo_base.anio_detalle",["anio"=>$data]);

      //Crear asiento de apertura de contabilidad
      $sql="INSERT INTO modulo_base.comprobante(tipo, correlativo, fecha, concepto, contabilizado, id_persona, usuario)
            VALUES('AC', 1, '$data-01-01', 'SALDOS AL 31/12/".($data-1)."', true, NULL, '".SIGA::user()."')";
      $db->Execute($sql);

      //FIN PROCESO DE APERTURA
    }

    function sql_usuario($_user, $_data){
      return " 
        select
          up.perfil
        from
          modulo_base.usuario as u,
          modulo_base.usuario_perfil as up
        where
          u.usuario='$_user' and
          u.activo and
          u.id=up.id_usuario and
          up.anio='$_data' and
          up.activo
      ";
    }

    //Buscar el usuario en la tabla usuario por anio=$data
    $sql=sql_usuario($user, $data);
    $usuario=$db->Execute($sql);

    //Si no existe, buscar el usuario por anio default
    if(!isset($usuario[0])){
      $sql=sql_usuario($user, 'default');
      $usuario=$db->Execute($sql);
    }


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
    SIGA::$data = SIGA::dataAvailable();
    $descripcion=SIGA::$database[$database]["description"];
    for($i=0;$i<count(SIGA::$data);$i++)if(SIGA::$data[$i]["id"]==$data){$descripcion.=" - ".SIGA::$data[$i]["nombre"];break;}
    SIGA::dataName($descripcion);
    SIGA::access_array($acceso);
    return ["success"=>true];
  }
}
MODULO::onInit();
?>