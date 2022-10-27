<?php
/**
 *  Definición del corazón de la interfaz 'Sistema de Asistencia - Consultar'.
 *
 *  Despliga la información del personal (hora de entrada/salida y notas) en pantalla,
 *  además permite su edición si el usuario conectado tiene acceso de escritura para:
 *  modulo_asistencia/ingresar_hora y modulo_asistencia/ingresar_nota.
 *  El archivo es llamado en un iframe por modulo_asistencia/js/reportes.js
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre
 *
 *  @version 2016.01.07
 */
include_once("../../library/include.php");
include_once("../../library/functions/bisiesto.php");
include_once("../../library/functions/letra_dia_semana.php");
include_once("../../library/functions/letra_mes.php");


$database=SIGA::database();
if(!$database)
  $database=SIGA_CONFIG::$database_default;
$db=SIGA::DBController($database);


$user=SIGA::user();
$access=SIGA::access("asistencia_ingresar_nota");

$mes=date("m");
if(isset($_REQUEST["mes"]))
  $mes=$_REQUEST["mes"];

$anio=date("Y");
if(isset($_REQUEST["anio"]))
  $anio=$_REQUEST["anio"];



$id_persona="";
if(isset($_REQUEST["id_persona"]))
  $id_persona=$_REQUEST["id_persona"];

//si no hay persona seleccionada, abrir por defecto la cuenta
if(!$id_persona){
  $user=SIGA::user();
  if($user){
    $user_inf=$db->Execute("SELECT id_persona_responsable FROM modulo_base.usuario WHERE usuario='$user'");
    if(isset($user_inf[0][0]))
      $id_persona=$user_inf[0][0];
  }
}

$dias_mes=array(31,
                bisiesto($anio)?29:28,
                31,
                30,
                31,
                30,
                31,
                31,
                30,
                31,
                30,
                31);

$add_id_persona="";
if($id_persona>0)
  $add_id_persona="P.id='$id_persona' AND";
else if($id_persona=="")
  $add_id_persona="P.id='$id_persona' AND";

$personal=$db->Execute("SELECT
                          *
                        FROM
                          modulo_nomina.ficha as F,
                          modulo_base.persona as P
                        WHERE
                          $add_id_persona
                          P.id=F.id_persona AND
                          F.activo
                        ORDER BY
                          P.identificacion_tipo,
                          P.identificacion_numero");


$lista_personal=$db->Execute("SELECT
                          P.id,
                          P.identificacion_tipo,
                          P.identificacion_numero,
                          P.denominacion
                        FROM
                          modulo_nomina.ficha as F,
                          modulo_base.persona as P
                        WHERE
                          P.id=F.id_persona AND
                          F.activo
                        ORDER BY
                          P.denominacion");


?>
<!doctype html>
<html>
<head>
  <style>
    body{
      font-family: sans-serif;
      background-color: #FFF;
      margin: 0px;
      padding: 0px;
    }
    .contenedor{
      border: 1px solid #E0E0E0;
      margin-bottom: 20px;
      border-radius: 0px;
      background-color: #F7F7F7;
      padding: 2px 20px 2px 20px;
      line-height: 1;
      margin-left: 10px;
      /*line-height: 1.4;*/
    }

    .nombre_apellidor{
      font-size: 20px;
      font-weight: bold;
      text-align: left;
      color: #000000;

    }

    .cedula{
      font-size: 12px;
      text-align: left;
      color: #545454;

    }

    #bloque_asistencia{


    }

    .tabla_asistencia{
      width: 100%;

    }

    .tabla_asistencia_cabecera{
      text-align: center;
      font-weight: bold;
      font-size: 13px;
      color: #FFFFFF;
      background-color: #545454;
    }

    .tabla_asistencia_contenido{
      text-align: center;
      background-color: #FFF;
      font-size: 13px;
      white-space: nowrap;
      cursor: pointer;
    }

    .tabla_asistencia_col_dia{
      font-weight: bold;
      background-color: #DBDBDB;
    }

    .tabla_asistencia_col_dia div{
      font-weight: normal;
      font-size: 9px;
    }

    .tabla_asistencia_col_totalhoras{
      background-color: #EAEAEA;
    }

    .tabla_asistencia_col_totaldiario{
      font-weight: bold;
      background-color: #DBDBDB;

    }

    .tabla_asistencia_fila_blanca{
      background-color: #FFF;
    }

    .lista_empleados{
      white-space: nowrap;
      font-weight: bold;
      font-size: 14px;
      width: 1%;
    }

    .menu{
      border: 1px solid #D5E7ED;
      margin-bottom: 2px;
      background-color: #E6EFF4;
      border-radius: 0px;
      color: #545454;
      cursor: pointer;
      padding: 0px 10px 0px 10px;
      text-align: center;
    }

    .menu:hover{
      background-color: #FFF9C6;
    }

    .menu_sel{
      color: #FFF;
      background-color: #537689;
    }

    .menu_sel:hover{
      color: #000000;
    }

    .menu_nombre_apellido{
      font-size: 12px;
      font-weight: bold;
    }

    .menu_cedula{
      font-size: 9px;
      font-weight: normal;
    }

    .menu_superior{
      border: 1px solid #F9F463;
      margin-bottom: 2px;
      background-color: #FFFD9E;
      border-radius: 0px;
      color: #000000;
      padding: 0px 10px 0px 10px;
      position: fixed;
      top: 0px;
      right: 0px;
      z-index: 1000;

    }

    .select, input{
      border: 1px solid #E0E0E0;
      background: #FFF;
      font-size: 18px;

    }

    .tabla_asistencia_contenido:hover,
    .tabla_asistencia_contenido:hover .tabla_asistencia_col_dia,
    .tabla_asistencia_contenido:hover .tabla_asistencia_col_totalhoras,
    .tabla_asistencia_contenido:hover .tabla_asistencia_col_totaldiario
    {
      background-color: #FFFAB7;
    }

    .tabla_asistencia_col_nota{
      font-weight: normal;
      font-size: 10px;
      line-height: 0.9;
      width: 150px;
      padding: 0px;
      white-space: normal;
    }

    .hora_manual{
      color: #D30000;
    }

    .usuario {
      font-size: 9px;
      /*font-style: italic;*/
      color: gray;
    }

    .oculto{
      color: rgba(0,0,0,0.5);

    }

    .tabla_asistencia td{
      position: relative;
    }

    .tabla_asistencia td:hover .asistencia_imagen{
      display: block;
      position: absolute;
      top: -100px;
      right: -200px;
      border: 1px solid #A3A3A3;
      z-index: 2000;
    }

    .asistencia_imagen{
      display: none;
    }

  </style>
  <script type="text/javascript">
    function onchange_mes(value){
      reload();
    }

    function onchange_anio(value){
      reload();
    }

    function reload(id_persona){
      if(!id_persona)
        var id_persona="<?php print $id_persona;?>";
      var mes=document.getElementById("s_mes").value;
      var anio=document.getElementById("s_anio").value;

      window.location.href='?id_persona='+id_persona+'&mes='+mes+'&anio='+anio;
    }

    function reload2(id_persona){
      if(!id_persona)
        var id_persona="<?php print $id_persona;?>";
      var mes=document.getElementById("s_mes").value;
      var anio=document.getElementById("s_anio").value;

      window.location.href='?id_persona='+id_persona+'&mes='+mes+'&anio='+anio;
    }

    function mostrar_nota(_id_persona,_fecha,_nota){
      var mostrar="";
      mostrar="<table width='100%' cellpadding='3'>";
      mostrar+="<tr style='background-color: #000; color: #FFF; text-align: center;'>";
      mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Nº</b></td>";
      mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Tipo de Nota</b></td>";
      mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Descripción</b></td>";
      <?php
      if($access=="rw"):
      ?>
      mostrar+="<td style=''></td>";
      <?php
      endif;
      ?>
      mostrar+="</tr>";

      for(var i=0;i<_nota.length;i++){
        mostrar+="<tr style='background-color: #FFF;'>";
        mostrar+="<td style='text-align: center;'><b>"+(i+1)+"</b></td>";
        mostrar+="<td>"+_nota[i]["tipo"]+"</td>";
        mostrar+="<td>"+_nota[i]["descripcion"]+"</td>";
        <?php
        if($access=="rw"):
        ?>
        mostrar+="<td title='Editar' style='cursor: pointer;' onclick='siga.open(\"asistencia_ingresar_nota\",{accion:\"buscar\", id_nota:"+_nota[i]["id"]+"})'><img src='image/icon/icon-edit.png' width='16' height='16'></td>";
        <?php
        endif;
        ?>
        mostrar+="</tr>";
      }

      mostrar+="</table>";


      <?php
      if($access=="rw" or $access=="a"):
      ?>

      mostrar+="<br><b>¿Desea agregar otra nota?</b>";
      parent.Ext.Msg.show({
        title:'NOTAS DEL '+parent.formatDate(_fecha),
        msg: mostrar,
        buttons:  parent.Ext.Msg.YESNO,
        fn: function(buttonId){
          if(buttonId!="yes") return;
          crear_nota(_id_persona,_fecha);
        }
        });
      <?php
      else:
      ?>
      parent.Ext.Msg.show({
        title:'NOTAS DEL '+parent.formatDate(_fecha),
        msg: mostrar,
        buttons:  parent.Ext.Msg.OK
        });
      <?php
      endif;
      ?>
    }


    function crear_nota(_id_persona,_fecha){
      parent.siga.open(
        "asistencia_ingresar_nota",
        {
          id_persona:_id_persona,
          fecha:_fecha
        });
    }

    function ingresar_hora(_id_persona,_fecha,_hora){
      parent.siga.open(
        "asistencia_ingresar_hora",
        {
          id_persona:_id_persona,
          fecha:_fecha,
          hora:_hora
        });
    }

    function ver_imagen(event, id_asistencia) {
      if (event.stopPropagation)
        event.stopPropagation();   // W3C model
      else
        event.cancelBubble = true; // IE model

      alert(id_asistencia);

      //return false;
    }

  </script>

</head>
<body>
  <table width="100%" border="0">
    <tr>
      <td colspan="2">
        <div class="menu_superior">
          <b>Mes</b>

          <select class="select" id="s_mes" onchange="onchange_mes(value)">
            <?php
            for($i=1;$i<=12;$i++)
              echo "<option value='".str_pad($i,2,"0",STR_PAD_LEFT)."' ".(intval($mes)==$i?'selected':'').">".ucfirst(letra_mes($i))."</option>";
            ?>
          </select>
          <b>Datos</b>
          <select class="select" id="s_anio" onchange="onchange_anio(value)">
            <?php
            for($i=0;$i<count(SIGA::$data);$i++)
              echo "<option value='".SIGA::$data[$i]["id"]."' ".($anio==SIGA::$data[$i]["id"]?"selected":"").">".SIGA::$data[$i]["nombre"]."</option>";
            ?>
          </select>

        </div>
      </td>
    </tr>
    <tr>
      <td class="lista_empleados" valign="top">
        <?php
        for($l=0;$l<count($lista_personal);$l++){
          $tmp=explode(";",$lista_personal[$l]["denominacion"]);
          $nombre_apellido=$tmp[0]." ".$tmp[2];
          $cedula=$lista_personal[$l]["identificacion_tipo"]."-".number_format($lista_personal[$l]["identificacion_numero"],0,"",".");
          $idp=$lista_personal[$l]["id"];

          $add_cls="";
          if($idp==$id_persona)
            $add_cls="menu_sel";

          echo "<div class='menu $add_cls' onclick=\"reload('$idp')\">";
          echo "<div class='menu_nombre_apellido'>$nombre_apellido</div>";
          //echo "<div class='menu_cedula'>$cedula</div>";
          echo "</div>";
        }
        $add_cls="";
        if(!($id_persona>0 or $id_persona==""))
          $add_cls="menu_sel";
        echo "<div class='menu $add_cls' onclick=\"reload('-1')\"><div class='menu_nombre_apellido'>TODOS</div></div>";
        ?>

      </td>
      <td class="" valign="">

  <?php
  if(!$personal)
    echo "<div style='text-align: center; font-size: 14px;'>Haga click en el nombre de la persona,<br>para mostrar su registro de asistencia.</div>";

  for($i=0;$i<count($personal);$i++){
    $tmp=explode(";",$personal[$i]["denominacion"]);
    $nombre_apellido=$tmp[0]." ".$tmp[2];
    $cedula=$personal[$i]["identificacion_tipo"]."-".number_format($personal[$i]["identificacion_numero"],0,"",".");
    $idp=$personal[$i]["id_persona"];


    echo "<div class='contenedor'>";
    echo "<div class='nombre_apellidor'>".$nombre_apellido."</div>";
    echo "<div class='cedula'>".$cedula."</div>";
    echo "<div id='bloque_asistencia'>";

    //buscar asistencia de la persona para el mes y anio en curso
    $asistencia_turno=$db->Execute("SELECT
                                count(fecha) as maximo
                              FROM
                                modulo_asistencia.asistencia
                              WHERE
                                id_persona='$idp' AND
                                text(fecha) ILIKE '$anio-$mes-%'
                              GROUP BY fecha
                              ORDER BY maximo desc
                              LIMIT 1
                              ");
    $turno=0;
    if(isset($asistencia_turno[0][0]))
      $turno=$asistencia_turno[0][0];
    $turno=ceil($turno/2);
    if($turno<2) $turno=2;

    echo "<table class='tabla_asistencia' border='0' cellspacing='2' cellpadding='3'>";
    echo "<tr class='tabla_asistencia_cabecera'>";
    echo "<td rowspan='2'>Fecha</td>";
    //mostrar la columna de turnos
    for($t=1;$t<=$turno;$t++)
      echo "<td colspan='3'>Turno $t</td>";
    echo "<td rowspan='2'>Total<br>Diario</td>";
    echo "<td rowspan='2'>Notas</td>";
    echo "</tr>";
    echo "<tr class='tabla_asistencia_cabecera'>";
    for($t=1;$t<=$turno;$t++)
      echo "<td>Inicio</td><td>Salida</td><td>Total</td>";
    echo "</tr>";



    for($d=1;$d<=$dias_mes[intval($mes)-1];$d++){
      $dia=$d<10?"0$d":"$d";


      //si dia de la semana es domingo insertar una fila en blanco
      $dia_semana=date("N",strtotime("$anio-$mes-$dia"));
      if($dia_semana==1)
        echo "<tr class='tabla_asistencia_fila_blanca'><td colspan='9'></td></tr>";

      $asistencia_dia=$db->Execute("SELECT
                                      id,
                                      fecha,
                                      hora,
                                      manual,
                                      usuario_validador
                                    FROM
                                      modulo_asistencia.asistencia
                                    WHERE
                                      id_persona='$idp' AND
                                      fecha='$anio-$mes-$dia'
                                    ORDER BY
                                      fecha,
                                      hora");


      $fecha="$dia/".$mes."/$anio";
      echo "<tr class='tabla_asistencia_contenido'>";
      echo "<td class='tabla_asistencia_col_dia'><div>".letra_dia_semana($dia_semana)."</div>$fecha</td>";
      $total_diario=0;

      for($k=0;$k<count($asistencia_dia);$k+=2){
        $add_img_hentrada="";
        $add_img_hsalida="";

        if($user=="admin"){
          $add_img_hentrada="<img class='asistencia_imagen' src='../asistencia_registro/?action=onGet_Imagen&id=".$asistencia_dia[$k]["id"]."' width='200' height='100' />";
          if(isset($asistencia_dia[$k+1]["id"]))
            $add_img_hsalida= "<img  class='asistencia_imagen' src='../asistencia_registro/?action=onGet_Imagen&id=".$asistencia_dia[$k+1]["id"]."' width='200' height='100' />";
        }


        $timestamp_hentrada=strtotime($asistencia_dia[$k]["hora"]);
        $manual_hentrada=$asistencia_dia[$k]["manual"];
        $usuario_hentrada=$asistencia_dia[$k]["usuario_validador"];

        $timestamp_hsalida="";
        $manual_hsalida="";
        $usuario_hsalida=NULL;
        if(isset($asistencia_dia[$k+1]["hora"]))
          $timestamp_hsalida=strtotime($asistencia_dia[$k+1]["hora"]);
        if(isset($asistencia_dia[$k+1]["manual"]))
          $manual_hsalida=$asistencia_dia[$k+1]["manual"];
        if(isset($asistencia_dia[$k+1]["usuario_validador"]))
          $usuario_hsalida=$asistencia_dia[$k+1]["usuario_validador"];

        if(!$timestamp_hsalida or !$timestamp_hentrada)
          $total_horas="";
        else
          $total_horas=date("H:i:s",strtotime("00:00:00")+$timestamp_hsalida-$timestamp_hentrada);

        if($total_horas)//si hay total horas por tuno, sumar al total diario
          $total_diario+=($timestamp_hsalida-$timestamp_hentrada);

        if($timestamp_hentrada){//si hay hora de entrada
          $add_td='';
          $add_cls='';
          if($usuario_hentrada===NULL){ $usuario_hentrada="&nbsp;"; $add_cls.="oculto "; }
          elseif($usuario_hentrada)     $usuario_hentrada="$usuario_hentrada";
          else                          $usuario_hentrada="&nbsp;";

          if($manual_hentrada=='t' or $user=="admin"){
            if($manual_hentrada=='t') $add_cls.="hora_manual ";
            $add_td="onclick='ingresar_hora($idp,\"$anio-$mes-$dia\",\"".date("H:i:s",$timestamp_hentrada)."\")'";
          }
          if($add_cls) $add_cls="class='$add_cls'";

          echo "<td $add_cls $add_td>".date("h:i:s a",$timestamp_hentrada)."<div class='usuario'>$usuario_hentrada</div>$add_img_hentrada</td>";
        }
        else
          echo "<td onclick='ingresar_hora($idp,\"$anio-$mes-$dia\")'>-</td>";

        if($timestamp_hsalida){//si hay hora de salida
          $add_td='';
          $add_cls='';
          if($usuario_hsalida===NULL){ $usuario_hsalida="&nbsp;"; $add_cls.="oculto "; }
          elseif($usuario_hsalida)     $usuario_hsalida="$usuario_hsalida";
          else                         $usuario_hsalida="&nbsp;";

          if($manual_hsalida=='t' or $user=="admin"){
            if($manual_hsalida=='t') $add_cls.="hora_manual ";
            $add_td="onclick='ingresar_hora($idp,\"$anio-$mes-$dia\",\"".date("H:i:s",$timestamp_hsalida)."\")'";
          }
          if($add_cls) $add_cls="class='$add_cls'";
          echo "<td $add_cls $add_td>".date("h:i:s a",$timestamp_hsalida)."<div class='usuario'>$usuario_hsalida</div>$add_img_hsalida</td>";
        }
        else
          echo "<td onclick='ingresar_hora($idp,\"$anio-$mes-$dia\")'>-</td>";
        //total de horas por el turno
        echo "<td class='tabla_asistencia_col_totalhoras'>".$total_horas."</td>";
      }
      //llenar vacios en la tabla
      for($t=count($asistencia_dia)/2+1;$t<=$turno;$t++)
        echo "<td onclick='ingresar_hora($idp,\"$anio-$mes-$dia\")'>-</td><td onclick='ingresar_hora($idp,\"$anio-$mes-$dia\")'>-</td><td class='tabla_asistencia_col_totalhoras'></td>";
      //total diario
      if($total_diario)
        echo "<td class='tabla_asistencia_col_totaldiario'>".date("H:i:s",strtotime("00:00:00")+$total_diario)."</td>";
      else
        echo "<td class='tabla_asistencia_col_totaldiario'></td>";

      //notas
      $asistencia_nota=$db->Execute("SELECT
                                      id,
                                      tipo,
                                      descripcion
                                    FROM
                                      modulo_asistencia.asistencia_nota
                                    WHERE
                                      id_persona='$idp' AND
                                      fecha='$anio-$mes-$dia'");
      $nota="No";
      $onclick="crear_nota(\"$idp\",\"$anio-$mes-$dia\")";
      if(count($asistencia_nota)>0){
        if(strlen($asistencia_nota[0]["descripcion"])>50)
          $nota=substr($asistencia_nota[0]["descripcion"],0,50)."...";
        else
          $nota=$asistencia_nota[0]["descripcion"];

        if(count($asistencia_nota)>1)
          $nota.="<sup>(".count($asistencia_nota).")<sup>";
        $onclick="mostrar_nota(\"$idp\",\"$anio-$mes-$dia\",".json_encode($asistencia_nota).")";
      }
      echo "<td class='tabla_asistencia_col_nota' onclick='$onclick'>$nota</td>";

      echo "</tr>";
    }

    echo "</table>";

    echo "</div>";
    echo "</div>";
  }

  ?>

        </td>
    </tr>
  </table>


</body>
</html>