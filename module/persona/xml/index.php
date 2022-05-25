<?php
header( "content-type: application/xml; charset=UTF-8" );
include_once("../../../library/db.controller.php");

include_once("../../../library/siga.config.php");
include_once("../../../library/siga.class.php");

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');





$nacionalidad=SIGA::paramRequest("nacionalidad");
$cedula=SIGA::paramRequest("cedula");



$db=SIGA::DBController();



$add="";
switch($nacionalidad){
  case "":case " "://personas sin cedula (p.j. niños)
  case "V"://venezolano
  case "E"://extranjero
  case "P"://pasaporte
    $add="split_part(P.denominacion,';',1) as primer_nombre,
          split_part(P.denominacion,';',2) as segundo_nombre,
          split_part(P.denominacion,';',3) as primer_apellido,
          split_part(P.denominacion,';',4) as segundo_apellido,";
    break;
  default://persona juridica
    $add="P.denominacion,";
    break;
}



$persona=$db->Execute("
        SELECT
          P.identificacion_tipo as nacionalidad,
          P.identificacion_numero as cedula,
          $add
          P.correo,
          P.telefono,
          P.direccion,
          PN.fecha_nacimiento,
          PN.genero
        FROM
          modulo_base.persona as P LEFT JOIN modulo_base.persona_natural as PN ON P.id=PN.id_persona
        WHERE
          P.identificacion_tipo='$nacionalidad' and
          P.identificacion_numero='$cedula'
        ");


if(!$persona){
  $db=SIGA::DBController("base");
  $persona_tmp=$db->Execute("
        SELECT
          p.nacionalidad,
          p.cedula,
          p.primer_nombre,
          p.segundo_nombre,
          p.primer_apellido,
          p.segundo_apellido
        FROM
          persona as p
        WHERE
          p.nacionalidad='$nacionalidad' and
          p.cedula='$cedula'
        ");

  $persona=$persona_tmp;
}


$xml = new DOMDocument( "1.0", "UTF-8" );
$xml_persona = $xml->createElement("persona");

if(isset($persona[0]))
  foreach($persona[0] as $indice => $valor)
    if(!is_int($indice))
      $xml_persona->setAttribute( "$indice", "$valor" );  
$xml->appendChild( $xml_persona );
print $xml->saveXML();
?>