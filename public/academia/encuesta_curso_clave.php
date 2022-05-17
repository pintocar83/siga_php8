<?php

include("../../library/db.controller.php");
include("../../library/db.constants.php");


$db=new DBController();
$db->ConnectQuick("asl");


if(isset($_GET["curso"])){
  $clave_generado=rand(1000,9999);
  $db->Delete("encuesta_curso_clave","id_curso_aperturado='".$_GET["curso"]."'");
  $db->Insert("encuesta_curso_clave",array("id_curso_aperturado"=>"'".$_GET["curso"]."'","clave"=>"'$clave_generado'","estado"=>"1"));
}






$cursos=$db->Execute("SELECT
                        *
                      FROM curso_aperturado as ca LEFT OUTER JOIN encuesta_curso_clave as ecc ON ca.id=ecc.id_curso_aperturado
                      ORDER BY ca.codigo DESC");
//base.persona as p LEFT OUTER JOIN base.persona_detalle as pd ON p.nacionalidad=pd.nacionalidad and p.cedula=pd.cedula
print "<table border='0'>";
print "<tr>          
          <td>CURSO</td>
          <td>CLAVE</td>
        </tr>";

for($i=0;$i<count($cursos);$i++){
  print "<tr>          
          <td>".$cursos[$i]["codigo"]."</td>";
  if($cursos[$i]["clave"])
    print "<td><a href='?curso=".$cursos[$i]["id"]."'>".$cursos[$i]["clave"]."</a></td>";
  else
    print "<td><a href='?curso=".$cursos[$i]["id"]."'>GENERAR</a></td>";
  print      "</tr>";
  
}
print "</table>";


?>