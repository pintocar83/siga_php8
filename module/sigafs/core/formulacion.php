<?php
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
?>
<!--
/**
* Sistema para la Gestion Administrativa Fundacite Sucre (SIGAFS)
* Copyright (C) 2009 by FUNDACITE Sucre
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @author Carlos J. Pinto B. <pintocar83@gmail.com>
* @date 2009-08-01
* @version 20091005
*/
-->

<INPUT id="sw_formulacion_reformulacion" type="hidden" value="<?php echo $_GET['sw'];?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<tbody>
		<tr>
			<TD class="acciones">
				<BUTTON id="BOTON_NUEVO_FFPDG" class="BotonesVentana" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FFPDG"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FFPDG" class="BotonesVentana" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FFPDG"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FFPDG" class="BotonesVentana" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Visualizar(true);">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FFPDG"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FFPDG">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 490px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FFPDG" class="MensajesPestanas">&nbsp;</DIV>
						<FORM id="FORMULARIO_FORMULACION_PRESUPUESTO_DE_GASTOS" name="FORMULARIO_FORMULACION_PRESUPUESTO_DE_GASTOS">
							<table cellspacing='3px' align="center" border="0" width="100%">
							<tbody>
								<tr>
									<td class='TitulosCampos' width="30%">Tipo</td>
									<td class='TextCampos'>
										<select id="TIPO_FFPDG" class="TextoCampoInput" onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();">
											<option value="F">FORMULACIÓN</option>
											<option value="R1">REFORMULACIÓN 1</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos' width="30%">Estructura Presupuestaria</td>
									<td class='TextCampos'>
										<SELECT class="TextoCampoInput" id="AC_FFPDG" onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarAE();"></SELECT>
										<SELECT class="TextoCampoInput" id="AE_FFPDG" onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarOAE();"></SELECT>
										<SELECT class="TextoCampoInput" id="OAE_FFPDG" onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();"></SELECT>
										
										<div style="font-size: x-small;">
											<div id="AC_FFPDG_INFO" style="overflow: hidden; width: 700px; white-space: nowrap; text-overflow: ellipsis;">&nbsp;</div>
											<div id="AE_FFPDG_INFO" style="overflow: hidden; width: 700px; white-space: nowrap; text-overflow: ellipsis;">&nbsp;</div>
											<div id="OAE_FFPDG_INFO" style="overflow: hidden; width: 700px; white-space: nowrap; text-overflow: ellipsis;">&nbsp;</div>
										</div>
										<!--<SELECT class="TextoCampoInput" id="FUENTE_FFPDG" title="FUENTE DE RECURSOS" onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();"></SELECT>-->
									</td>
								</tr>
							</tbody>
							</table>							
							<DIV class="AreaTablaListado" style="height : 340px; width: 920px;">
								<table id="TABLA_FORMULACION_FFPDG" border="0" cellspacing="0" cellpadding="0" width="1800px">
								</table>
							</DIV>
							<table border="0" cellpadding="1" cellspacing="0" style="width: 922px;">
							<tbody>
								<tr class="CabeceraTablaEstilo">
								<td align="left">
									<BUTTON id="BOTON_AGREGAR_FFPDG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Agregar();" type="BUTTON"><IMG id="IMG_AGREGAR_FFPDG" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar</BUTTON>
									<BUTTON id="BOTON_QUITAR_FFPDG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Quitar();"><IMG id="IMG_QUITAR_FFPDG" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;</BUTTON>
									<BUTTON id="BOTON_ASIGNAR_FFPDG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top; white-space : nowrap;" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Asignar();" type="BUTTON" title="Generar comprobante de asignación de presupuesto."><IMG id="IMG_ASIGNAR_FFPDG" src='../../image/icon/icon-contabilizar-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Asignar</BUTTON>									
								</td>
								<td align="right">
									Comprobante de Asignación Generado: <span id="FFPDG_COMPROBANTE_GENERADO"></span>
								</td>
								</tr>
							</tbody>
							</table>
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>

<INPUT type="hidden" id="CODIGO_CTA_PRESUPUESTARIA_FFPDG">
<INPUT type="hidden" id="DENOMINACION_CTA_PRESUPUESTARIA_FFPDG">
<INPUT type="hidden" id="ANIO_PRESUPUESTARIO_ACTUAL" value="<?php print SIGA::data();?>">
<script>
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FFPDG"), true);
	window.onload=function(){
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Nuevo();
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarAC();
	}
</script>


