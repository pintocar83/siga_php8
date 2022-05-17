<?
include_once("../../library/session.php");

$opcion="";
if(isset($_GET["opcion"]))
  $opcion=$_GET["opcion"];

if(!$opcion)
  $opcion="favoritos";
 
  
$menu_principal=array(
                  array("id"=>"favoritos","icon"=>"../images/icon-favoritos.png","name"=>"Favoritos"),
                  array("id"=>"procesos","icon"=>"../images/icon-procesos.png","name"=>"Procesos"),
                  array("id"=>"reportes","icon"=>"../images/icon-reportes.png","name"=>"Reportes")
                  );
  
  
$arreglo["favoritos"]=array(
                  array("id"=>"modulo_base/requisicion_externa","icon"=>"../images/icon-requisicion.png","name"=>"Requisición Externa"),
                  array("id"=>"modulo_base/orden_compra","icon"=>"../images/icon-orden_compra.png","name"=>"Orden de Compra"),
                  array("id"=>"modulo_base/orden_servicio","icon"=>"../images/icon-orden_servicio.png","name"=>"Orden de Servicio"),
                  array("id"=>"modulo_base/orden_pago","icon"=>"../images/icon-orden_pago.png","name"=>"Orden de Pago"),
                  array("id"=>"modulo_base/cheque_directo","icon"=>"../images/icon-cheque_directo.png","name"=>"Cheque<br>Directo"),
                  array("id"=>"modulo_base/cheque_orden_pago","icon"=>"../images/icon-cheque.png","name"=>"Cheque<br>Orden de Pago"),
                  array("id"=>"modulo_base/banco_movimiento","icon"=>"../images/icon-banco_movimiento.png","name"=>"Movimientos<br>Bancarios"),
                  array("id"=>"modulo_base/comprobante","icon"=>"../images/icon-comprobante.png","name"=>"Comprobante"),
                  array("id"=>"modulo_base/comprobante_retencion","icon"=>"../images/icon-retencion_comprobante.png","name"=>"Comprobante de<br>Retención"),
                  array("id"=>"modulo_base/factura","icon"=>"../images/icon-factura.png","name"=>"Facturas"),
                  array("id"=>"modulo_base/proveedor","icon"=>"../images/icon-proveedor.png","name"=>"Proveedores"),
                  array("id"=>"modulo_base/beneficiario","icon"=>"../images/icon-beneficiario.png","name"=>"Beneficiarios"),
                  array("id"=>"modulo_base/materiales","icon"=>"../images/icon-materiales.png","name"=>"Materiales y/o<br>Suministros"),
                  array("id"=>"modulo_base/bienes","icon"=>"../images/icon-bienes.png","name"=>"Bienes"),
                  array("id"=>"modulo_base/servicios","icon"=>"../images/icon-servicios.png","name"=>"Servicios"),
                  array("id"=>"modulo_nomina/hoja_calculo","icon"=>"../images/icon-nomina.png","name"=>"Nómina"),
                  );

$arreglo["procesos"]=array(
                  array("id"=>"modulo_base/comprobante","icon"=>"../images/icon-comprobante.png","name"=>"Comprobante"),                  
                  array("id"=>"modulo_presupuesto/traspaso","icon"=>"../images/icon-traspaso.png","name"=>"Traspaso"),
                  array("id"=>"modulo_presupuesto/credito_adicional","icon"=>"../images/icon-credito_adicional.png","name"=>"Crédito Adicional"),
                  array("id"=>"modulo_presupuesto/reduccion","icon"=>"../images/icon-reduccion.png","name"=>"Reducción"),
                  array("id"=>"modulo_presupuesto/disponibilidad","icon"=>"../images/icon-disponibilidad.png","name"=>"Disponibilidad<br>Presupuestaria"),
                  array("id"=>"modulo_presupuesto/formulacion","icon"=>"../images/icon-formulacion.png","name"=>"Formulación"),
                  array("id"=>"modulo_presupuesto/estructura_presupuestaria","icon"=>"../images/icon-estructura_presupuestaria.png","name"=>"Estructura<br>Presupuestaria"),
                  array("id"=>"modulo_base/cerrar_mes","icon"=>"../images/icon-cerrar_mes.png","name"=>"Cierre de Meses"),
                  array("id"=>"modulo_presupuesto/convertidor","icon"=>"../images/icon-convertidor.png","name"=>"Convertidor"),
                  array("id"=>"modulo_presupuesto/cuenta_presupuestaria","icon"=>"../images/icon-cuenta_presupuestaria.png","name"=>"Plan de Cuentas<br>Presupuestarias"),
                  array("id"=>"modulo_contabilidad/cuenta_contable","icon"=>"../images/icon-cuenta_contable.png","name"=>"Plan de Cuentas<br>Contables"),
                  
                  );

$arreglo["reportes"]=array(
                  array("id"=>"modulo_contabilidad/reportes","icon"=>"../images/icon-reportes.png","name"=>"Contabilidad"),
                  array("id"=>"modulo_presupuesto/reportes_ejecucion","icon"=>"../images/icon-reportes.png","name"=>"Presupuesto<br>Ejecución"),
                  array("id"=>"modulo_presupuesto/reportes_mayor","icon"=>"../images/icon-reportes.png","name"=>"Presupuesto<br>Mayor Analítico"),
                  array("id"=>"modulo_banco/reportes","icon"=>"../images/icon-reportes.png","name"=>"Banco<br>Estado de Cuenta"),
                  array("id"=>"modulo_base/reportes_retencion","icon"=>"../images/icon-reportes.png","name"=>"Retenciones<br>IVA / ISLR"),
                  array("id"=>"modulo_nomina/constancia_trabajo","icon"=>"../images/icon-reportes.png","name"=>"Constancias<br>de Trabajo")
                  );

?>
<html>
  <head>
    <style>
      body{
        margin: 0px;
        padding: 0px;
        text-align: center;
        background: #ffffff; /* Old browsers */
        background: -moz-radial-gradient(center, ellipse cover, #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
        background: -webkit-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
        background: -o-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* Opera 12+ */
        background: -ms-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* IE10+ */
        background: radial-gradient(ellipse at center, #ffffff 0%,#e5e5e5 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
      }
      
      #icono{
        text-align: center;
        /*border-radius: 65px;*/
        width: 150px;
        padding: 10px 0 10px 0;
        display: inline-table;
        margin-top: 10px;
        opacity: 0.8;
        color: #000;
        cursor: pointer;
      }
      
      #icono:hover{
        opacity: 1;
        /*background-color: #FFFDED;*/
       
       background: rgba(226, 226, 226,0.3);
       border-radius: 10px;
       font-weight: bold;
      }
      
      #icono img{
        width: 48px;
        height: 48px;    
      }
      
      #icono span{
        font-family: sans-serif;
        font-size: 12px;
      }
      
      #icono.desactivado{
        -webkit-filter: grayscale(0.9);
        filter: grayscale(0.9);
        opacity: 0.5;
      }
      
      #icono.desactivado:hover{
        opacity: 0.5;
        font-weight: normal;
        background: none;
      }
      
      #left{
        width: 15%;
        float: left;
        border: 0px solid black;
        margin: 0px;
        height: 100%;
        background: #F7F7F7;
      }
      
      #right{
        width: 85%;
        float: right;
        border: 0px solid black;
        margin: 0px;
        /*border-left: 2px solid rgba(0, 161, 241,1);*/
      }
      
      .icono-principal{
        padding-top: 10px;
        padding-bottom: 10px;
        opacity: 0.9;
        color: #000;
        cursor: pointer;
        border-right: 5px solid rgba(0, 0, 0, 0);
      }
      
      .icono-principal span{
        font-family: sans-serif;
        font-size: 12px;
      }
      
      .icono-principal img{
        width: 48px;
        height: 48px;    
      }
      
      .icono-principal.foco:hover{
        opacity: 0.7;       
        background: rgba(226, 226, 226,0.3);
        border-radius: 0px;
        font-weight: bold;
        border-right: 5px solid rgba(0, 161, 241,0);
      }
      
      .selected{
        opacity: 1;       
        background: #ffffff; /* Old browsers */
        background: -moz-radial-gradient(center, ellipse cover, #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
        background: -webkit-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
        background: -o-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* Opera 12+ */
        background: -ms-radial-gradient(center, ellipse cover, #ffffff 0%,#e5e5e5 100%); /* IE10+ */
        background: radial-gradient(ellipse at center, #ffffff 0%,#e5e5e5 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
        border-radius: 0px;
        font-weight: bold;
        border-right: 5px solid rgba(0, 161, 241,1);
      }
      
    </style>
  </head>
  <body>
    <div id="left">
      <?php
      for($i=0;$i<count($menu_principal);$i++){
        $onclick="window.location.href='?opcion=".$menu_principal[$i]["id"]."'";
        $add_class="foco";
        if($menu_principal[$i]["id"]==$opcion){
          $onclick="";
          $add_class="selected";
        }
        print "<div class='icono-principal $add_class' onclick=\"$onclick\"><span><img src='".$menu_principal[$i]["icon"]."'/><br />".$menu_principal[$i]["name"]."</span></div>";
      }      
      ?>
    </div>
    <div id="right">
      <?php 
      for($i=0;$i<count($arreglo["$opcion"]);$i++){
        $add_class="";
        $onclick="";
        if(Session::access($arreglo["$opcion"][$i]["id"])==""){
          $add_class="desactivado";
        }
        else{
          $onclick="parent.siga.open('".$arreglo["$opcion"][$i]["id"]."')";
        }
        print "<div id='icono' class='$add_class' onclick=\"$onclick\"><span><img src='".$arreglo["$opcion"][$i]["icon"]."' /><br>".$arreglo["$opcion"][$i]["name"]."</span></div>";
      }    
      ?>
    </div>
  </body>
</html>