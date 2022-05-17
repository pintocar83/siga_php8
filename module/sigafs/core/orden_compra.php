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
* @version 20090801
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<tbody>
		<tr>
			<TD  class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_ORDEN_COMPRA__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_OC"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_OC" class="BotonesVentana"  onclick="Form_ORDEN_COMPRA__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_OC"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_OC" class="BotonesVentana" onclick="Form_ORDEN_COMPRA__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_OC"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_OC" class="BotonesVentana" onclick="Form_ORDEN_COMPRA__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_OC"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_OC" class="BotonesVentana" onclick="Form_ORDEN_COMPRA__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_OC"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_OC">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 410px;">
						<h2 id="TAB_0_FOC" class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_OC" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_ORDEN_COMPRA" name="FORMULARIO_ORDEN_COMPRA">
							<table cellspacing='3' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Número</td>
									<td class='TextCampos'>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td><INPUT id='CODIGO_OC' class='TextoCampoInputDesactivado' type='text' size='15' value="" readonly="true"></td>
													<td align="left" width="100%" style="padding-left: 10px;">
														<BUTTON id="BOTON_PRECONTABLIZAR_OC" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__PreContabilizar();" type="BUTTON">Pre-Contabilizar</BUTTON>
														<BUTTON id="BOTON_CONTABLIZAR_OC" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__Contabilizar();" type="BUTTON">Contabilizar</BUTTON>
														<BUTTON id="BOTON_REVERSAR_OC" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__Reversar();" type="BUTTON">Reversar</BUTTON>
														<BUTTON id="BOTON_ANULAR_OC" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__Anular();" type="BUTTON">Anular</BUTTON>
														<BUTTON id="BOTON_VER_OP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__VerComprobanteAsociado('OP');" type="BUTTON">Ver Orden de Pago</BUTTON>
														<BUTTON id="BOTON_VER_ANULACION" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_COMPRA__VerComprobanteAsociado('CA');" type="BUTTON">Ver Anulación</BUTTON>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos'>
										<INPUT id='FECHA_OC' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_OC" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									</td>									
								</tr>
								<tr>
									<td class='TitulosCampos'>Proveedor</td>
									<td class='TextCampos'>
										<INPUT id='RIF_PROVEEDOR_OC' class='TextoCampoInputDesactivado' type='text' size='20' maxlength='15' value="" readonly="true"><INPUT id='NOMBRE_PROVEEDOR_OC' class='TextoCampoInputDesactivado' readonly="true" type='text' size='50' value=""><IMG id="IMG_BUSCAR_PROVEEDOR_OC" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT id='ID_PROVEEDOR_OC' type="hidden" value="">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Concepto</td>
									<td class='TextCampos'>
										<textarea id='CONCEPTO_OC' class='TextoCampoInput' style="width: 100%; resize: none;" rows="3"></textarea>
									</td>
								</tr>								
							</tbody>
							</table>
							<DIV style="height : 7px;"></DIV>
							<table border="0" cellspacing="0" cellpadding="0" width="100%" id="TITULOS_TABLA_ARTICULOS_OC"></table>
							<DIV class="AreaTablaListado" style="height: 100px;">
								<table id="TABLA_LISTA_ARTICULOS_OC" border="0" cellspacing="0" cellpadding="0" width="100%"></table>
							</DIV>
							<table border="0" cellpadding="1" cellspacing="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td align="left" rowspan="4" valign="top">
										<DIV id="DIV_BOTON_REQUISICION_FDC"></DIV>
										<span style="display: none;"><INPUT id="CHECK_REQUISICION_OC" type="checkbox" checked>&nbsp;Usar&nbsp;requisici&oacute;n&nbsp;existente</span><br>
										<INPUT type="checkbox" id="CHECK_DESCUENTO_OC" checked onchange="">&nbsp;Aplicar descuento al Sub Total
									</td>
									<td>
									</td>
									<td align="right" width="1px">
										Sub Total&nbsp;
									</td>
									<td align="right" width="1px">
										<INPUT id='SUB_TOTAL_OC' class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true" style="text-align : right;">
									</td>
								</tr>
								<tr class="CabeceraTablaEstilo">
									<td align="center" id="FILA_DESCUENTO_OC_1">
										<INPUT id='PORCENTAJE_DESCUENTO_OC' class='TextoCampoInput' type='text' size='5' value="0.00" style="text-align : right;" onkeypress="return AcceptNum(event,'PORCENTAJE_DESCUENTO_OC');">
									</td>
									<td align="right" width="1px" id="FILA_DESCUENTO_OC_2">
										%&nbsp;&nbsp;Descuento&nbsp;
									</td>
									<td align="right" width="1px" id="FILA_DESCUENTO_OC_3">
										<INPUT id='DESCUENTO_OC' class='TextoCampoInput' type='text' size='22' value="0.00" style="text-align : right;"  onkeypress="return AcceptNum(event,'DESCUENTO_OC');">
									</td>
								</tr>
								<tr class="CabeceraTablaEstilo">
									<td>
									</td>
									<td align="right" width="1px">
										Base&nbsp;imponible&nbsp;
									</td>
									<td align="right" width="1px">
										<INPUT id='BASE_IMPONIBLE_OC' class='TextoCampoInputDesactivado' type='text' size='22' value="" style="text-align : right;" readonly="true">
									</td>
								</tr>
								<tr class="CabeceraTablaEstilo">
									<td align="center" width="1px">
										<BUTTON id="BOTON_CARGOS_OC" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="" type="BUTTON">
											Cargos
										</BUTTON>
									</td>
									<td align="right" width="1px">
										Impuesto&nbsp;
									</td>
									<td align="right" width="1px">
										<INPUT id='TOTAL_IMPUESTOS_OC' class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true" style="text-align : right;">
									</td>
								</tr>
								<tr class="CabeceraTablaEstilo">
									<td>
									</td>
									<td>
									</td>
									<td align="right" width="1px">
										TOTAL&nbsp;
									</td>
									<td align="right" width="1px">
										<INPUT id='TOTAL_OC' class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true" style="text-align : right;">
									</td>
								</tr>
							</tbody>
							</table>
							<!--Contiene los datos a ser agregados al listado de articulos de la requisicion-->
							<INPUT type="hidden" value="" id="COD_ART_AGREGAR_OC">
							<INPUT type="hidden" value="" id="DENOM_ART_AGREGAR_OC">
							<INPUT type="hidden" value="" id="UNID_MED_ART_AGREGAR_OC">
							<INPUT type="hidden" value="" id="APLICAR_IVA_ART_AGREGAR_OC">
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_OC_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width='5px'></td>
									<td width="10%">NÚMERO</td>
									<td width="10%">FECHA</td>
									<td width="23%">PROVEEDOR</td>
									<td>CONCEPTO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 60%;">
							<table id="TABLA_LISTA_OC" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<table width="100%" align="center">
						<tbody>
							<tr>
							<td valign="top">
								<table>
									<tr><td colspan="3"><strong>Estado&nbsp;de&nbsp;la&nbsp;orden</strong></td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #FF5454; width : 5px;'></td><td>Sin contabilizar</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #FFDD00; width : 5px;'></td><td>Pre-contabilizada</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #48DC0E; width : 5px;'></td><td>Contabilizada</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #000000; width : 5px;'></td><td>Anulada</td></tr>
								</table>
							</td>
							<td valign="top">
								<DIV class='TitulosCampos' style="text-align : left; padding-left: 10px;">
									<table>
									<tbody>
										<tr>
										<td>Buscar&nbsp;</td>
										<td style="white-space : nowrap;">
											<INPUT id="LISTADO_BUSCAR_OC" class='TextoCampoInput' type="text" value="" size="35" onkeyup="Form_ORDEN_COMPRA__PresionarEnter(event);">
											<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;"  onclick="Form_ORDEN_COMPRA__LimpiarInputTextBuscarListado();">
											<IMG id="IMG_LIMPIAR_OC" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
											</BUTTON>
										</td>
										</tr>
										<tr>
										<td>Mostrar&nbsp;</td>
										<td>
<SELECT class="TextoCampoInput" id="MES_FILTRAR_OC" onchange="Form_ORDEN_COMPRA__BuscarListado_CadenaBuscar=''; Form_ORDEN_COMPRA__BuscarListado();">
	<OPTION value="">TODO</OPTION>
	<OPTION value="01" <?php if(date("m")=="01")echo "selected";?>>ENERO</OPTION>
	<OPTION value="02" <?php if(date("m")=="02")echo "selected";?>>FEBRERO</OPTION>
	<OPTION value="03" <?php if(date("m")=="03")echo "selected";?>>MARZO</OPTION>
	<OPTION value="04" <?php if(date("m")=="04")echo "selected";?>>ABRIL</OPTION>
	<OPTION value="05" <?php if(date("m")=="05")echo "selected";?>>MAYO</OPTION>
	<OPTION value="06" <?php if(date("m")=="06")echo "selected";?>>JUNIO</OPTION>
	<OPTION value="07" <?php if(date("m")=="07")echo "selected";?>>JULIO</OPTION>
	<OPTION value="08" <?php if(date("m")=="08")echo "selected";?>>AGOSTO</OPTION>
	<OPTION value="09" <?php if(date("m")=="09")echo "selected";?>>SEPTIEMBRE</OPTION>
	<OPTION value="10" <?php if(date("m")=="10")echo "selected";?>>OCTUBRE</OPTION>
	<OPTION value="11" <?php if(date("m")=="11")echo "selected";?>>NOVIEMBRE</OPTION>
	<OPTION value="12" <?php if(date("m")=="12")echo "selected";?>>DICIEMBRE</OPTION>
</SELECT>
										</td>
										</tr>
									</tbody>
									</table>
								</DIV>
							</td>
							<td valign="top">
								<INPUT id="SOMBRA_CHECKBOX_OC" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_OC" type="checkbox" checked="true">Solo buscar al presionar enter
							</td>
							</tr>
						</tbody>
						</table>
					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	var Form_ORDEN_COMPRA__TabPane = new WebFXTabPane(xGetElementById("TABPANE_OC"), true);
	var Form_ORDEN_COMPRA__tipo="<?php print $_REQUEST["sw"];?>";
	window.onload=function(){
		Form_ORDEN_COMPRA__Nuevo();
	}
</script>