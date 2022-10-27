<?php

//si es la imagen
if(isset($_REQUEST["imagen"])):
  $data=$_REQUEST["imagen"];

  list($type, $data)      = explode(',', $data);

  if($type!="data:image/png;base64"){
    exit;
  }

  //crear imagen png en el servidor
  $data = str_replace(' ', '+', $data);
  $data = base64_decode($data);
  file_put_contents('tmp_qr.png', $data);


  //comparar la imagen png con zbar
  $result=exec("zbarimg -q tmp_qr.png");
  if(!$result){
    print "null";
    exit;
  }

  $result=substr($result,8+40);//quitar de la cadena QR Code: http://www.fundacite-sucre.gob.ve/query?
  $result=base64_decode($result);
  $result=strtoupper($result);

  if(!$result){
    print "null";
    exit;
  }

  list($tipo,$cedula)=explode("=",$result);

  if($tipo!="PERSONAL"){
    print "null";
    exit;
  }
endif;

//si es la query (resultado de hacer split o explode 'query?' sobre la url obtenida desde el qr)
if(isset($_REQUEST["query"])):
  $result=base64_decode($_REQUEST["query"]);
  $result=strtoupper($result);
  if(!$result){
    print "null";
    exit;
  }

  list($tipo,$cedula)=explode("=",$result);

  if($tipo!="FICHA"){
    print "null";
    exit;
  }
endif;


//buscar codigo en la BD
//ejemplo V16315637
include_once("../../library/include.php");
include_once("../../library/functions/letra_mes.php");
//buscar los datos de la persona
$database=SIGA::database();
if(!$database)
  $database=SIGA_CONFIG::$database_default;
$db=SIGA::DBController($database);

if(isset($cedula)):
  $_tipo=substr($cedula,0,1);
  $_numero=intval(substr($cedula,1));
  $cedula=$_tipo.$_numero;
  $persona=$db->Execute("SELECT * FROM modulo_base.persona WHERE identificacion_tipo||identificacion_numero='$cedula'");
elseif(isset($_REQUEST["codigo"])):
  $codigo_dedometro=str_clear($_REQUEST["codigo"]);
  if(!$codigo_dedometro){
    print "null";
    exit;
  }

  $persona=$db->Execute("SELECT P.*
                        FROM modulo_base.persona as P, modulo_nomina.ficha as F
                        WHERE P.id=F.id_persona AND F.codigo='$codigo_dedometro' and F.activo");

  /*$persona=$db->Execute("SELECT *
                        FROM modulo_base.persona as P, modulo_asistencia.asistencia_codigo_persona as ACP
                        WHERE P.id=ACP.id_persona AND ACP.codigo='$codigo_dedometro'");*/
else:
  print "null";
  exit;
endif;

if(!isset($persona)){
  print "null";
  exit;
}
if(!$persona or count($persona)==0){
  print "null";
  exit;
}

$return["imagen"]="../ficha/?action=onGet_ArchivoFoto&archivo=".$persona[0]["identificacion_tipo"].$persona[0]["identificacion_numero"];

//if(!file_exists($return["imagen"]))
//  $return["imagen"]="personal/default.png";
$return["id_persona"]=$persona[0]["id"];
$return["cedula"]=$persona[0]["identificacion_tipo"]."-".number_format($persona[0]["identificacion_numero"],0,"",".");
$str_tmp=explode(";",$persona[0]["denominacion"]);
$return["nombre_apellido"]=$str_tmp[0]." ".$str_tmp[2];

$return["registro_previo"]=$db->Execute("SELECT
                                            fecha,
                                            to_char(hora,'HH:MI:SS am') as hora
                                          FROM modulo_asistencia.asistencia
                                          WHERE
                                            id_persona=".$persona[0]["id"]." AND
                                            fecha=(select max(fecha) from modulo_asistencia.asistencia where id_persona=".$persona[0]["id"].")
                                          ORDER BY fecha, hora desc
                                          ");

$return["registro_nuevo"]["fecha"]=date("Y-m-d");
$return["registro_nuevo"]["hora"]=date("H:i:s");
$return["registro_nuevo"]["fecha_mostrar"]=date("d")."/".ucfirst(letra_mes(date("m")))."/".date("Y");
$return["registro_nuevo"]["hora_mostrar"]=date("h:i:s a");

//para la alerta de retraso
$sw="";
$h=date("H")*1;
$m=date("i")*1;
$s=date("s");
if($h==8){
  if($m>=20)
    $sw=date("H:i:s",strtotime("00:00:00")+strtotime(($h<10?"0$h":$h).":".($m<10?"0$m":$m).":$s")-strtotime("08:00:00"));
}
else if($h==13){
  if($m>=50)
    $sw=date("H:i:s",strtotime("00:00:00")+strtotime(($h<10?"0$h":$h).":".($m<10?"0$m":$m).":$s")-strtotime("13:30:00"));
}
else if($h>13 and $h<16){
  $sw=date("H:i:s",strtotime("00:00:00")+strtotime(($h<10?"0$h":$h).":".($m<10?"0$m":$m).":$s")-strtotime("13:30:00"));
}
else if($h>8 and $h<12){
  $sw=date("H:i:s",strtotime("00:00:00")+strtotime(($h<10?"0$h":$h).":".($m<10?"0$m":$m).":$s")-strtotime("08:00:00"));
}

$return["registro_nuevo"]["alerta"]=$sw;

print json_encode($return);


//"http://www.fundacite-sucre.gob.ve/query?UEVSU09OQUw9VjE2MzE1NjM3"


?>
