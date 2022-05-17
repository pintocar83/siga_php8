<?php
include_once("../../library/db.controller.php");
include_once("library/siga.config.php");
include_once("../../library/siga.class.php");

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$db=SIGA::DBController("siga_online");


$id_curso_aperturado=$_POST["id_curso_aperturado"];
$id_persona=$_POST["id_persona"];
$item1=$_POST["item1"];
$item2=$_POST["item2"];
$item3=$_POST["item3"];
$item4=$_POST["item4"];
$item5=$_POST["item5"];
$item6=$_POST["item6"];
$item7=$_POST["item7"];
$item8=$_POST["item8"];
$item9=$_POST["item9"];
$item10=$_POST["item10"];
$item11=$_POST["item11"];
$item12=$_POST["item12"];
$item13=$_POST["item13"];
$item14=$_POST["item14"];
$item15=$_POST["item15"];



$data=array(
            "id_curso_aperturado"=>"'$id_curso_aperturado'",
            "id_persona"=>"'$id_persona'",
            "item1"=>"'$item1'",
            "item2"=>"'$item2'",
            "item3"=>"'$item3'",
            "item4"=>"'$item4'",
            "item5"=>"'$item5'",
            "item6"=>"'$item6'",
            "item7"=>"'$item7'",
            "item8"=>"'$item8'",
            "item9"=>"'$item9'",
            "item10"=>"'$item10'",
            "item11"=>"'$item11'",
            "item12"=>"'$item12'",
            "item13"=>"'$item13'",
            "item14"=>"'$item14'",
            "item15"=>"'$item15'"
            );

$result=$db->Insert("modulo_asl.curso_encuesta",$data);
print "<div align='center'>";
if(!$result){
  print $db->GetMsgError();
  echo "Error al registrar datos.<br>";
}
else{  
  print "Datos guardados con exito.<br>";  
}
print "<a href='index.php'>Volver al inicio</a>";  
print "</div>";
?>