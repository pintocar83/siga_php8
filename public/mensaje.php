<?php
header('Content-Type: text/html; charset=utf-8');
//include_once("../library/define.php");


$id_mensaje=$_GET["id"];

$titulo_navegador="";
$titulo="";
$mensaje="";

switch($id_mensaje){
  case 1:
    $titulo_navegador="FUNDACITE SUCRE - Política de Acceso: RESTRINGIDO";
    $titulo="Acceso Restringido";
    $mensaje="Por políticas de la institución<br>el acceso a este sitio web se encuentra restringido.";
  break;
  default:
    exit;
}


?>
<!doctype html>
<html>
	<head>
		<title><?php print $titulo_navegador;?></title>
    <style>
      body{
        font-family: Verdana, Arial, Helvetica, sans-serif;
      }
      .pre_titulo{
        color:#ff0000;
        font-size: large;
        font-weight: bold;
        margin-top: 80px;
      }
      .titulo{
        color:#ff0000;
        font-size: x-large;
        font-weight: bold;
        margin-top: 10px;
        text-decoration: underline;
      }
      .mensaje{
        color: #00557d;
        font-size: small;
        font-weight: bold;
        margin-top: 10px;
      }
      .pie{
        color: #00557d;
        font-size: medium;
        font-weight: bold;
        margin-top: 10px;
        line-height: 1.5;
      }
    </style>
	</head>
	<body>
		<div align="center">
      <img src="data:image/jpeg;base64,<?php print base64_encode(file_get_contents("image/cintillo_actual.jpg"))?>" width="900" />
      <div class='pre_titulo'>ATENCI&Oacute;N</div>
      <div class='titulo'><?php print $titulo;?></div>
      <div class="mensaje"><?php print $mensaje;?></div>
			<img src="data:image/jpeg;base64,<?php print base64_encode(file_get_contents("image/logo_01.jpg"))?>" width="200" style="margin-top: 100px;" />
		</div>
	</body>
</html>
