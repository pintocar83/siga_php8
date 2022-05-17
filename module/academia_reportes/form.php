<?php
header('Content-Type: text/html; charset=utf-8');

include_once("../../library/include.php");
include_once("../../library/functions/formatDate.php");


$db=SIGA::DBController();


$ANIO=SIGA::paramRequest('anio');
if(!$ANIO) $ANIO=date("Y");

$retorno=$db->Execute("
                      SELECT
                        ca.*,
                        c.denominacion as nombrecurso,
                        p.identificacion_tipo as nacionalidad,
                        p.identificacion_numero as cedula,
                        split_part(p.denominacion,';',1) as primer_nombre,
                        split_part(p.denominacion,';',2) as segundo_nombre,
                        split_part(p.denominacion,';',3) as primer_apellido,
                        split_part(p.denominacion,';',4) as segundo_apellido,
                        tc.denominacion,
                        tc.horario
                      FROM
                        modulo_asl.curso_aperturado as ca,
                        modulo_asl.curso as c,
                        modulo_asl.instructor as i,
                        modulo_base.persona as p,
                        modulo_asl.turno_curso as tc
                      WHERE
                        c.id=ca.id_curso and
                        ca.id_instructor=i.id and
                        i.id_persona=p.id and
                        ca.id_turno=tc.id and
                        ca.codigo like '$ANIO-%'
                      ORDER BY
                        ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc 
                      ");


?>



<!DOCTYPE HTML>
<html>
<head>
  <title>REPORTE CURSOS</title>
  <style>
    body{
      font-family: sans-serif;
      
    }
    
    a,a:visited{
      color: #0041D8;
      text-decoration: none;
    }
    a:hover{
      text-decoration: underline;
    }
    
    .tabla{
      background-color: #232323;
      /*background-color: #300000;*/
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
    
    .tabla_titulo_1{
      font-size: 24px;
      font-weight: bold;
    }
    
    .tabla_titulo_2{
      font-size: 18px;
      font-weight: bold;
    }
    
  </style>
</head>
<body>
  <?php
  $suma_aprobados=0;
print "<table width='100%'>
        <tr>
          <td class='tabla_titulo_1'>RESUMEN DE CURSOS $ANIO</td>
          <td class='tabla_titulo_2' align='right'>
            <a href='?anio=2007'>[2007]</a>
            <a href='?anio=2008'>[2008]</a>
            <a href='?anio=2009'>[2009]</a>
            <a href='?anio=2010'>[2010]</a>
            <a href='?anio=2011'>[2011]</a>
            <a href='?anio=2012'>[2012]</a>
            <a href='?anio=2013'>[2013]</a>
            <a href='?anio=2014'>[2014]</a>
            <a href='?anio=2015'>[2015]</a>
            <a href='?anio=2016'>[2016]</a>
            <a href='?anio=2017'>[2017]</a>
            <a href='?anio=2018'>[2018]</a>
            <a href='?anio=2019'>[2019]</a>
            <a href='?anio=2020'>[2020]</a>

          </td>
        </tr>
       </table>";
echo "<br />";
echo "<a href='pdf/cronograma_anual.php?anio=$ANIO' target='_blank'>CRONOGRAMA DE CURSOS</a><br/>";
echo "<a href='personas_formadas.php' target='_blank'>PERSONAS FORMADAS</a><br/>";
echo "<a href='personas_preinscritas.php' target='_blank'>PERSONAS PREINSCRITAS</a><br/>";
echo "<a href='cursos_persona.php?nacionalidad=V&cedula=0' target='_blank'>CURSOS PERSONA</a><br/>";
//echo "<a href='../../web/asl/encuesta_curso_clave.php' target='_blank'>GENERADOR DE CLAVES PARA LA ENCUESTAS</a><br/><br/>";
echo "<table border='0' class='tabla' width='100%'>";

echo "
	<tr class='cabeceratable'>
            <td colspan='10'><b>CURSOS DICTADOS</b></td>
        </tr>
         <tr class='cabeceratable'>
            <td>Curso</td>
            <td>C&oacute;digo</td>  
            <td>Fecha Inicio</td>
            <td>Fecha Culminaci&oacute;n</td>
            <td>Horario</td>
            <td>Facilitador</td>
            <td>Aprobados</td>
            <td>Reprobados</td>
            <td>Certificados Impresos</td>
         </tr>
";
$total_aprobados=0;
$total_reprobados=0;
$CANTIDAD_CURSOS_POR_FACILITADOR=array();
for($i=0;$i<count($retorno);$i++){
  $aprobados=$db->Execute("select count(*) from modulo_asl.inscrito where id_estado=3 and id_curso_aperturado='".$retorno[$i]['id']."'");
  $aprobados=$aprobados[0][0];
  $reprobados=$db->Execute("select count(*) from modulo_asl.inscrito where id_estado=4 and id_curso_aperturado='".$retorno[$i]['id']."'");
  $reprobados=$reprobados[0][0];
  
  
  
  if(!isset($CANTIDAD_CURSOS_POR_FACILITADOR[$retorno[$i]['primer_nombre'].' '.$retorno[$i]['primer_apellido']]))
    $CANTIDAD_CURSOS_POR_FACILITADOR[$retorno[$i]['primer_nombre'].' '.$retorno[$i]['primer_apellido']]=0;
  $CANTIDAD_CURSOS_POR_FACILITADOR[$retorno[$i]['primer_nombre'].' '.$retorno[$i]['primer_apellido']]++;


  echo "
           <tr>
              <td class='celdatabla'>".$retorno[$i]['nombrecurso']."</td>
              <td class='celdatabla' style='white-space : nowrap;'><a href='curso_detalle.php?curso=".$retorno[$i]['codigo']."' target='_blank'>".$retorno[$i]['codigo']."</a></td>  
              <td class='celdatabla'>".formatDate($retorno[$i]['fecha_inicio'])."</td>
              <td class='celdatabla'>".formatDate($retorno[$i]['fecha_culminacion'])."</td>
              <td class='celdatabla'>".$retorno[$i]['denominacion'].'  '.$retorno[$i]['horario']."</td>
              <td class='celdatabla'>".$retorno[$i]['primer_nombre'].' '.$retorno[$i]['primer_apellido']."</td>
              <td class='celdatabla' style='text-align:center;'>".$aprobados."</td>
              <td class='celdatabla' style='text-align:center;'>".$reprobados."</td>              
              <td class='celdatabla'>".($retorno[$i]['impreso']==0?"No":"Si")."</td>  
           </tr>
  ";
$total_aprobados+=$aprobados;
$total_reprobados+=$reprobados;
}

echo "
           <tr class='cabeceratable'>
              <td class=''></td>
              <td class=''></td>  
              <td class=''></td>  
              <td class=''></td>  
              <td class=''></td>  
              <td class=''></td>  
              <td class=''>".$total_aprobados."</td>
              <td class=''>".$total_reprobados."</td>
              <td class=''></td>  
              <td class=''></td>  
           </tr>
  ";


echo "</table>";



echo "<br /><br />";
echo "<table border='0' class='tabla' width='100%'>";
echo "
	 <tr class='cabeceratable'>
            <td colspan='2'><b>CURSOS DICTADOS POR FACILITADOR</b></td>
         </tr>
         <tr class='cabeceratable'>
            <td>Facilitador</td>
            <td>Cantidad</td>
         </tr>
";


foreach($CANTIDAD_CURSOS_POR_FACILITADOR as $indice => $valor){
echo "
	<tr>
		<td class='celdatabla'>".$indice."</td>
		<td class='celdatabla'>".$valor."</td>
	</tr>
  ";
}
echo "</table>";


echo "<br /><br />";
echo "<table border='0' class='tabla' width='100%'>";
echo "
	 <tr class='cabeceratable'>
            <td colspan='2'><b>PERSONAS FORMADAS POR INSTITUCIÓN</b></td>
         </tr>
         <tr class='cabeceratable'>
            <td>Mes</td>
            <td>Trimestre</td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=01' target='_BLANK'>Enero</a></td>
            <td rowspan='3'><a href='formados.php?anio=$ANIO&trimestre=1' target='_BLANK'>Trimestre I</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=02' target='_BLANK'>Febrero</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=03' target='_BLANK'>Marzo</a></td>
         </tr>
         
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=04' target='_BLANK'>Abril</a></td>
            <td rowspan='3'><a href='formados.php?anio=$ANIO&trimestre=2' target='_BLANK'>Trimestre II</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=05' target='_BLANK'>Mayo</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=06' target='_BLANK'>Junio</a></td>
         </tr>
         
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=07' target='_BLANK'>Julio</a></td>
            <td rowspan='3'><a href='formados.php?anio=$ANIO&trimestre=3' target='_BLANK'>Trimestre III</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=08' target='_BLANK'>Agosto</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=09' target='_BLANK'>Septiembre</a></td>
         </tr>
         
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=10' target='_BLANK'>Octubre</a></td>
            <td rowspan='3'><a href='formados.php?anio=$ANIO&trimestre=4' target='_BLANK'>Trimestre IV</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=11' target='_BLANK'>Noviembre</a></td>
         </tr>
         <tr class='celdatabla'>
            <td><a href='formados.php?anio=$ANIO&mes=12' target='_BLANK'>Diciembre</a></td>
         </tr>
         
";


echo "</table>";


?>


</body>
</html>