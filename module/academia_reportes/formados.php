<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 'Off');



include_once("../../library/db.controller.php");
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
include_once("../../library/functions/formatDate.php");
include_once("../../library/functions/str_clear.php");
include_once("../../library/functions/letra_mes.php");





$mes=SIGA::paramRequest("mes");
$trimestre=SIGA::paramRequest("trimestre");
$ano=SIGA::paramRequest("anio");
$add="";
$titulo="";

if($mes){
  $titulo=strtoupper(letra_mes($mes));
  $add=" and text(ca.fecha_culminacion) like '$ano-$mes-%' ";
}
else if($trimestre){
  switch($trimestre){
    case 1:
      $titulo="TRIMESTRE I";
      $add=" and (text(ca.fecha_culminacion) like '$ano-01-%' or text(ca.fecha_culminacion) like '$ano-02-%' or text(ca.fecha_culminacion) like '$ano-03-%') ";
      break;
    case 2:
      $titulo="TRIMESTRE II";
      $add=" and (text(ca.fecha_culminacion) like '$ano-04-%' or text(ca.fecha_culminacion) like '$ano-05-%' or text(ca.fecha_culminacion) like '$ano-06-%') ";
      break;
    case 3:
      $titulo="TRIMESTRE III";
      $add=" and (text(ca.fecha_culminacion) like '$ano-07-%' or text(ca.fecha_culminacion) like '$ano-08-%' or text(ca.fecha_culminacion like) '$ano-09-%') ";
      break;
    case 4:
      $titulo="TRIMESTRE IV";
      $add=" and (text(ca.fecha_culminacion) like '$ano-10-%' or text(ca.fecha_culminacion) like '$ano-11-%' or text(ca.fecha_culminacion) like '$ano-12-%') ";
      break;
  }


  
}
else{
  exit;
}

$titulo="PERSONAS FORMADAS EN LA ACADEMIA DE SOFTWARE LIBRE<br>PERIODO: $titulo, $ano";
$db=SIGA::DBController();

$retorno=$db->Execute("
                      SELECT
                        ca.*,
                        c.denominacion as nombrecurso,
                        tc.denominacion,
                        tc.horario
                      FROM
                        modulo_asl.curso_aperturado as ca,
                        modulo_asl.curso as c,
                        modulo_asl.turno_curso as tc
                      WHERE
                        c.id=ca.id_curso and
                        ca.id_turno=tc.id  
                        $add
                      ORDER BY
                        ca.codigo desc
                      ");
                      

$agrupar=array(
               " ",
               "COMUNIDAD/PUBLICO GENERAL",
               "INSTITUCION",
               "INSTITUCIÓN",
               "UNIVERSIDAD",
               "CONSEJO COMUNAL",        
               "OTRO"          
               );

$agrupar_color=array(
                     "#00EDFF",
                     "#0019FF",
                     "#FFFF00",
                     "#FF0000",
                     "#D400FF",
                     "#00FF11",
                     "#FF9400"
                     );

$total_aprobados=0;
$total_reprobados=0;
for($i=0;$i<count($agrupar);$i++){

  $por_dependencia_aprobados[$i]=$db->Execute("
                                        SELECT REPLACE(I2.nombre,'".$agrupar[$i]." - ','') as nombre, count(*) as cantidad
                                        FROM modulo_asl.inscrito as I1, modulo_asl.institucion as I2, modulo_asl.curso_aperturado as ca
                                        WHERE I1.id_institucion=I2.id and I2.nombre like '".$agrupar[$i]."%' and I1.id_estado=3 and ca.id=I1.id_curso_aperturado $add
                                        GROUP BY nombre
                                        ");
  if(!isset($TOTAL_POR_DEPENDECIA_A[$agrupar[$i]])) 
    $TOTAL_POR_DEPENDECIA_A[$agrupar[$i]]=0;
  for($j=0;$j<count($por_dependencia_aprobados[$i]);$j++){
    $total_aprobados+=$por_dependencia_aprobados[$i][$j][1];
    $TOTAL_POR_DEPENDECIA_A[$agrupar[$i]]+=$por_dependencia_aprobados[$i][$j][1];
    }
    
  $por_dependencia_reprobados[$i]=$db->Execute("
                                        SELECT REPLACE(I2.nombre,'".$agrupar[$i]." - ','') as nombre, count(*) as cantidad
                                        FROM modulo_asl.inscrito as I1, modulo_asl.institucion as I2, modulo_asl.curso_aperturado as ca
                                        WHERE I1.id_institucion=I2.id and I2.nombre like '".$agrupar[$i]."%' and I1.id_estado=4 and ca.id=I1.id_curso_aperturado $add
                                        GROUP BY nombre
                                        ");
  if(!isset($TOTAL_POR_DEPENDECIA_R[$agrupar[$i]])) 
    $TOTAL_POR_DEPENDECIA_R[$agrupar[$i]]=0;
  for($j=0;$j<count($por_dependencia_reprobados[$i]);$j++){
    $total_reprobados+=$por_dependencia_reprobados[$i][$j][1];
    $TOTAL_POR_DEPENDECIA_R[$agrupar[$i]]+=$por_dependencia_reprobados[$i][$j][1];
    }
  
}

$max_width=600;



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>REPORTE CURSOS</title>
  <style>
    body{
      font-family: sans-serif;
      
    }
    
    .tabla{
      background-color: #232323;
      color: #FFFFFF;
    }
    

    
    .cabeceratable{
      font-weight: bold;
      text-align: center;
    }
    

    .celdatabla{
      background-color: #FFFFFF;
      color: #2D2D2D;
      font-size: small;
    }
    
    
  </style>
</head>
<body>
<?php
echo "<table border='0' class='tabla' width='100%'>";
echo "<tr class='cabeceratable'>
            <td colspan='6'><b>$titulo</b></td>
      </tr>";
echo "<tr class='cabeceratable'>
            <td>Clase</td>
            <td>Instituci&oacute;n/Organizaci&oacute;n</td>
            <td>Personas</td>
            <td>Total</td>
            <td></td>
            <td>%</td>
      </tr>";
for($i=0;$i<count($agrupar);$i++){
  $sw=true;
  for($j=0;$j<count($por_dependencia_aprobados[$i]);$j++){
    
    echo "<tr>";
    if($sw){ echo "<TH class='celdatabla' ROWSPAN=".count($por_dependencia_aprobados[$i]).">".$agrupar[$i]."</TH>"; }
    
    echo "
            <td class='celdatabla'>".$por_dependencia_aprobados[$i][$j][0]."</td>
            <td class='celdatabla' style='text-align: center;'>".$por_dependencia_aprobados[$i][$j][1]."</td>";

    if($sw){ echo "<TH class='celdatabla' ROWSPAN=".count($por_dependencia_aprobados[$i]).">".$TOTAL_POR_DEPENDECIA_A[$agrupar[$i]]."</TH>"; $sw=false;}

    echo "
            <td class='celdatabla' style='width:".$max_width."px;'><div style='background-color: ".$agrupar_color[$i]."; width: ".$max_width*$por_dependencia_aprobados[$i][$j][1]/$total_aprobados."px;'>&nbsp;</div></td>
            <td class='celdatabla' style='text-align: right;'>".number_format(100*$por_dependencia_aprobados[$i][$j][1]/$total_aprobados,2,",",".")."%</td>
          </tr>";
  }
}
echo "<tr class='cabeceratable'>
            <td></td>
            <td></td>
            <td></td>
            <td>".$total_aprobados."</td>
            <td></td>
            <td></td>
      </tr>";
echo "</table>";
?>

</body>
</html>