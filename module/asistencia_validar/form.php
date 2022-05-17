<?php
include_once("../../library/include.php");
header('Content-Type: text/html; charset=utf-8');

$access=SIGA::access("asistencia_validar");
if($access!="rw"){
	print "No tiene acceso al módulo";
	exit;
}
include_once("../../library/functions/formatDate.php");



$db=SIGA::DBController();



$accion=SIGA::paramUpper("accion");


$buscar=NULL;
switch($accion){
	case "LIMPIAR":
		$buscar="";
    SIGA::cookie("SIGA::asistencia_validar::buscar",$buscar);
		break;
  case "BUSCAR":
    $buscar=SIGA::paramUpper("buscar");
    SIGA::cookie("SIGA::asistencia_validar::buscar",$buscar);
  break;
  case "RECHAZAR":
    $ids=SIGA::param("id",false);
    if(!$ids) break;
    
    $sql_delete="";
    for($i=0;$i<count($ids);$i++)
      $sql_delete.=str_clear($ids[$i]).($i<count($ids)-1?",":"");
    if($sql_delete)
      $db->Delete("modulo_asistencia.asistencia","id IN ($sql_delete)");
  break;
  case "VALIDAR":
    $ids=SIGA::param("id",false);
    if(!$ids) break;
    
    $usuario=SIGA::user();
    $sql_update="";
    for($i=0;$i<count($ids);$i++)
      $sql_update.=str_clear($ids[$i]).($i<count($ids)-1?",":"");
    if($sql_update)
      $db->Update("modulo_asistencia.asistencia",array("usuario_validador"=>"'$usuario'"),"id IN ($sql_update)");
  break;
}


if($buscar===NULL)
  $buscar=SIGA::cookie("SIGA::asistencia_validar::buscar");

$add_sql="";
$buscar_array=preg_split("/[\s,;| ]+/",$buscar);
for($i=0;$i<count($buscar_array);$i++)
  if(str_clear($buscar_array[$i]))
    $add_sql.="'".str_clear($buscar_array[$i])."'".($i<count($buscar_array)-1?",":"");


if($add_sql)
  $add_sql="AND P.identificacion_tipo||P.identificacion_numero IN ($add_sql)";


$datos=$db->Execute("SELECT DISTINCT 
                        A.id_persona,
                        P.identificacion_tipo,
                        P.identificacion_numero,
                        P.denominacion
                      FROM 
                        modulo_asistencia.asistencia AS A,
                        modulo_base.persona as P
                      WHERE 
                        P.id=A.id_persona AND
                        A.usuario_validador is null 
                        $add_sql
                      ORDER BY 
                        P.identificacion_tipo,
                        P.identificacion_numero");


for($i=0;$i<count($datos);$i++):
  //buscar registro de asistencia de la persona
  $datos[$i]["registro"]=$db->Execute("SELECT id, fecha, hora
                                        FROM 
                                          modulo_asistencia.asistencia
                                        WHERE 
                                          id_persona='".$datos[$i]["id_persona"]."' AND
                                          usuario_validador is null
                                        ORDER BY
                                          fecha, hora");
  
  
endfor;


?>

<!doctype html>
<html>
<head>
  <style>
    
    body{
      font-family: sans-serif;
      background-color:#FFF;
    }
    
    .persona_nombre{
      font-size: 12px;
    }
    
    .persona_cedula{
      font-size: 10px;
      color: gray;
    }
    
    .contenedor_video_foto{
      text-align: center;
    }
    .contenedor_titulo{
      font-size: 18px;
      font-family: sans-serif;
      color: #282828;
      font-weight: bold;
      /*font-style: italic;*/
      margin-bottom: 5px;
    }
    
    input[type=button], input[type=submit], button {
      font-size: 11px;
      padding:0px 15px;
      background:#EFEFEF;
      border:1px solid #D3D3D3;
      cursor:pointer;
      -webkit-border-radius: 5px;
      border-radius: 5px;
    }
    
    input[type=button]:hover, input[type=submit]:hover, button:hover, .buttons_hover {
      background-color:rgba(255,204,0,0.8);
    }
    
    .titulo{
      font-size: 12px;
      font-weight: bold;
      color: white;
      background-color:#5C5C5C;
      text-align: center;
			
    }
    
    .fila_par{
      background-color:#DEDEDE;
    }
    
    .fila_impar{
      background-color:#EDEDED;
    }
    
    .registro{
      font-size: 12px;
      vertical-align: middle;
    }
    
    .tabla{
      width: 100%;
      max-width: 550px;
      margin-top: 50px;
			margin-bottom: 70px;
    }
    
    .filtro{
      position: fixed;
      top: 0px;
      left: 0px;
      width:100%;
      /*border: 1px solid black;*/
      text-align: center;
      background-color: rgba(255,255,255,1);
      padding: 10px 0px 10px 0px;
    }
    
    .filtro div{
      width: 95%;
      max-width: 540px;
      /*align: center;*/
      margin: auto auto;
    }
    
    .filtro input[type='text']{
      width: 70%;
      box-sizing: border-box;
      height: 20px;
			vertical-align: middle;
			border:1px solid #C1C1C1;
			border-radius: 0px;
    }
    
    .filtro img{
      vertical-align: middle;
    }
    
    .filtro button{
      vertical-align: middle;
      border-radius: 0px;
      height: 20px;
			margin: 0px;
    }
    
    .filtro input[type='button']{
      width: 80px;
    }
		
		.pie {
			position: fixed;
			bottom: 0px;
			width:100%;
      text-align: center;
      /*background-color: rgba(255,255,255,1);*/
			/*background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1));*/
      padding: 10px 0px 10px 0px;
			background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 47%, rgba(255,255,255,1) 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0)), color-stop(47%,rgba(255,255,255,1)), color-stop(100%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 47%,rgba(255,255,255,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 47%,rgba(255,255,255,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 47%,rgba(255,255,255,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 47%,rgba(255,255,255,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
		}
		
		.pie div{
      width: 95%;
      max-width: 540px;			
      margin: auto auto;
    }
		
		input[type=checkbox].css-checkbox {
			display: none;
		}

		input[type=checkbox].css-checkbox + label.css-label {
			padding-left:24px;
			height: 24px; 
			display:inline-block;
			/*line-height:36px;*/
			background-repeat:no-repeat;
			background-position: 0 0;
			/*font-size:36px;*/
			vertical-align:middle;
			cursor:pointer;

		}

		input[type=checkbox].css-checkbox:checked + label.css-label {
			background-position: 0 100%;
		}
		
		label.css-label {
			background-image:url('../../image/icon/icon-checkbox_24x24.png');
			-webkit-touch-callout: none;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
    
  </style>
  <script type="text/javascript">
    function persona_marcar(id_persona,ids){
      for(var i=0;i<ids.length;i++)
        document.getElementById("reg_"+ids[i]).checked=document.getElementById("persona_"+id_persona).checked;
    }
  </script>
</head>
<body onload="">
<form id='formulario' method="post" onkeypress="return event.keyCode != 13;">
<?php
print "<div class='filtro'>";
print "<div>";
print "<input type='text' id='buscar' name='buscar' value='$buscar' placeholder='Filtro por cédula. Ej: V16315637' autocomplete='off' /><button type='submit' name='accion' value='buscar'><img src='../../image/icon/icon-find.png' /></button><button type='submit' name='accion' value='limpiar'><img src='../../image/icon/icon-clear.png' /></button>";
print "</div>";
print "</div>";


print "<div class='pie'>";
print "<div>";
print "<button type='submit' name='accion' value='validar'><img src='../../image/icon/icon-aprobar.png' /></button><button type='submit' name='accion' value='rechazar'><img src='../../image/icon/icon-reprobar.png' /></button>";
print "</div>";
print "</div>";

//print "<div style='position:fixed; bottom:10px; right:30px;'>";
//print "<button type='submit' name='accion' value='validar'><img src='../../images/icon-aprobar.png' /></button><button type='submit' name='accion' value='rechazar'><img src='../../images/icon-reprobar.png' /></button>";
//print "</div>";

print "<table class='tabla' align='center'>";
print "<tr class='titulo'>";
print "<td></td>";
print "<td>PERSONA</td>";
print "<td>REGISTROS</td>";
print "</tr>";
for($i=0;$i<count($datos);$i++):
  $id_persona=$datos[$i]["id_persona"];
  $cedula=$datos[$i]["identificacion_tipo"]."".$datos[$i]["identificacion_numero"];
  $nombre_apellido=explode(";",$datos[$i]["denominacion"]);
  $nombre_apellido=$nombre_apellido[0]." ".$nombre_apellido[2];
  $registro="";
  $ids="";
  for($j=0;$j<count($datos[$i]["registro"]);$j++):
    $ids.=$datos[$i]["registro"][$j]["id"].(($j<count($datos[$i]["registro"])-1)?",":"");
    $registro.="<label><input type='checkbox' name='id[]' id='reg_".$datos[$i]["registro"][$j]["id"]."' value='".$datos[$i]["registro"][$j]["id"]."'/> ".formatDate($datos[$i]["registro"][$j]["fecha"])." ".$datos[$i]["registro"][$j]["hora"]."</label><br>";
  endfor;
  $cls_fila="fila_impar";
  if($i%2==0)
    $cls_fila="fila_par";
  
  print "<tr class='$cls_fila'>";
  print "<td style='text-align:center;'><input class='css-checkbox' type='checkbox' id='persona_$id_persona' onchange='persona_marcar($id_persona,[$ids])'/><label class='css-label' for='persona_$id_persona'></label></td>";
  print "<td><div class='persona_nombre'>$nombre_apellido</div><div class='persona_cedula'>$cedula</div></td>";
  print "<td class='registro'>$registro</td>";
  print "</tr>";
endfor;
print "</table>";
?>
</form>
</body>
</html>