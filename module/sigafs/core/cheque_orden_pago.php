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
* @version 20090826
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<tbody>
		<tr>
			<TD class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_CHEQUE_ORDEN_PAGO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FCOP" class="BotonesVentana" onclick="Form_CHEQUE_ORDEN_PAGO__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FCOP" class="BotonesVentana" onclick="Form_CHEQUE_ORDEN_PAGO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FCOP" class="BotonesVentana" onclick="Form_CHEQUE_ORDEN_PAGO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FCOP" class="BotonesVentana" onclick="Form_CHEQUE_ORDEN_PAGO__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FCOP"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FCOP">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 390px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCOP" class="MensajesPestanas">&nbsp;</DIV>
						<br>

						<FORM id="FORMULARIO_CHEQUE_ORDEN_PAGO" name="FORMULARIO_CHEQUE_ORDEN_PAGO">
							<table cellspacing='3px' align="center" border="0">
							<tbody>
								<tr>
									<td></td>
									<td>
										<BUTTON id="BOTON_PROVEEDOR_FCOP" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" onclick="" type="BUTTON">
											Proveedor
										</BUTTON>
										<BUTTON id="BOTON_BENEFICIARIO_FCOP" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" onclick="" type="BUTTON">
											Beneficiario
										</BUTTON>
										<span style="padding-left: 20px;">
											<BUTTON id="BOTON_CONTABLIZAR_FCOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_ORDEN_PAGO__Contabilizar();" type="BUTTON">Contabilizar</BUTTON>
											<BUTTON id="BOTON_REVERSAR_FCOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_ORDEN_PAGO__Reversar();" type="BUTTON">Reversar</BUTTON>
											<BUTTON id="BOTON_ANULAR_FCOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_ORDEN_PAGO__Anular();" type="BUTTON">Anular</BUTTON>
										</span>
									</td>
									<td id="MSG_CUSTODIA" style="font-weight : bold;"></td>
								</tr>
								<tr>
									<td class='TitulosCampos' id="TIPO_PERSONA_FCOP"></td>
									<td class='TextCampos' colspan="2">
										<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FCOP' class='TextoCampoInputDesactivado' type='text' size='22' maxlength='15' value="" readonly="true"  style="width: 150px;"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP' class='TextoCampoInputDesactivado' readonly="true" type='text' size='65' value=""  style="width: 450px;"><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" id="ID_BoP_FCOP" value="">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Cuenta</td>
									<td class='TextCampos' colspan="2">
										<INPUT id="ID_CTA_FCOP" class='TextoCampoInputDesactivado' type="hidden" value="" size="4" readonly="true">
										<INPUT id="NCTA_FCOP" class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true"  style="width: 150px;"><INPUT id="DESCRIPCION_NCTA_FCOP" class='TextoCampoInputDesactivado' type='text' size='65' value="" readonly="true"  style="width: 450px;"><IMG id="IMG_BUSCAR_NCTA_FCOP" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" value="" id="CTA_CODIGO_CONTABLE_FCOP">
										<INPUT type="hidden" value="" id="CUENTA_CONTABLE_FCOP">
										<INPUT type="hidden" value="" id="CTA_DENOMINACION_CONTABLE_FCOP">
										<INPUT id="TIPO_CTA_FCOP" class='TextoCampoInputDesactivado' type='hidden' size='15' value="" readonly="true">
										<INPUT id='BANCO_FCOP' class='TextoCampoInputDesactivado' type='hidden' size='25' value="" readonly="true">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td colspan="2">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td class='TextCampos'><INPUT id='FECHA_FCOP' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FCOP" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'></td>
											<td class='TitulosCampos'>Cheque&nbsp;No.&nbsp;</td>
											<td class='TextCampos'><INPUT id='N_CHEQUE_FCOP' class='TextoCampoInput' type='text' size='15' value=""></td>
											<td class='TitulosCampos' style="width: 30%; font-size: 11px; vertical-align: middle;" id="COMPROBANTE_FCOP"></td>
											<!--<td class='TextCampos' style="text-align : right;" width="1px"><INPUT id='N_VOUCHER_FCOP' class='TextoCampoInputDesactivado' type='text' size='15' value="" readonly="true"></td>-->
											</tr>
										</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Concepto</td>
									<td class='TextCampos' colspan="2">
										<TEXTAREA cols="73" rows="2" class='TextoCampoInput' id="CONCEPTO_FCOP" style="resize: none; width: 100%;"></TEXTAREA>
									</td>
								<tr>
									<td class='TitulosCampos'>Monto</td>
									<td class='TextCampos' colspan="2">
										<INPUT id="MONTO_FCOP" class='TextoCampoInputDesactivado' type='text' size='18' value="" style="text-align : right;" readonly="true">
										<SELECT class='TextoCampoInput' id="INCLUSION_RETENCION_FCOP" onchange="Form_CHEQUE_ORDEN_PAGO__CalcularMonto()">
											<OPTION value="0">RETENCIONES EN EL ULTIMO PAGO</OPTION>
											<OPTION value="1" selected="selected">RETENCIONES EN EL PRIMER PAGO</OPTION>
										</SELECT>
									</td>
								</tr>
							</tbody>
							</table>
						</FORM>
						<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top: 15px;">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width='16px'><img class='BotonesParaCampos' src='../../image/icon/icon-display_16x16.png' style='border: none; background: none; margin-right: 0px; margin-left: 2px; width: 16px; height: 16px;' title='Visualizar Todas' onclick="Form_CHEQUE_ORDEN_PAGO__VisualizarOPTodas()"/></td>
									<td width="1%" style="padding: 5px 0px 0px 5px;"><INPUT type="checkbox" id="CHECK_FCOP" onclick="Form_CHEQUE_ORDEN_PAGO__AlternarChecks();"></td>
									<td width="10%">NÚMERO</td>
									<td width="10%">FECHA</td>
									<td>CONCEPTO</td>
									<td width="10%" style="white-space: nowrap;">MONTO<br>ORDEN PAGO</td>
									<td width="10%">TOTAL<br>PAGADO</td>
									<td width="10%">MONTO</td>
								</tr>
							</tbody>
						</table>
						<DIV id="DIV_TABLA_SOLICITUDES_LISTA_FCOP" class="AreaTablaListado" style="height : 130px; overflow-x: hidden;">
							<table id="TABLA_LISTA_SOLICITUDES_FCOP" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 390px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCOP_DETALLES" class="MensajesPestanas">Muestra información referente al cheque, generada a partir de la(s) solicitud(es) de pago.</DIV>

						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="17%">ESTRUC. PRESUP.</td>
									<td width="13%">CUENTA</td>
									<td width="33%">DENOMINACI&Oacute;N</td>
									<td width="11%">PRESUPUESTO</td>
									<td width="11%">DEBE</td>
									<td width="11%">HABER</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 230px;">
							<table id="TABLA_LISTA_DETALLES_FCOP" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<DIV class="CabeceraTablaEstilo" style="text-align : right;">
							<INPUT id="TOTAL_PARCIALES_FCOP" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true"><INPUT id="TOTAL_DEBITOS_FCOP" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true"><INPUT id="TOTAL_CREDITOS_FCOP" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true">
						</DIV>
						<br>
						<DIV align="center">
							<br>
							<strong>Mostrar detalles </strong><SELECT id="SELECT_DETALLES_FCOP" class='TextoCampoInput' onchange="Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles()"></SELECT>
						</DIV>

					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
					<!-- ******************** ******************** Tercera Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 390px;">
						<h2 class="tab">
							<DIV id="TITULO_LISTA_FCOP">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Listado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</DIV>
						</h2>
						<DIV id="MSG_FCOP_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="11%">NÚMERO</td>
									<td width="10%">FECHA</td>
									<td width="25%" id="TITULO_PB_FCOP_LISTADO"></td>
									<td>CONCEPTO</td>									
									<td width="10%">MONTO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 210px;">
							<table id="TABLA_LISTA_FCOP" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<BUTTON id="BOTON_PROVEEDOR_2_FCOP" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_ORDEN_PAGO__BotonProveedor();" type="BUTTON">
							Proveedores
						</BUTTON>
						<BUTTON id="BOTON_BENEFICIARIO_2_FCOP" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_ORDEN_PAGO__BotonBeneficiario();" type="BUTTON">
							Beneficiarios
						</BUTTON>
						<table width="100%">
						<tbody>
							<tr>
							<td valign="top">
								
								<table>
									<tr><td colspan="3"><strong>Estado&nbsp;del&nbsp;cheque</strong></td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #FF5454; width : 5px;'></td><td>Sin contabilizar</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #48DC0E; width : 5px;'></td><td>Contabilizado</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #000000; width : 5px;'></td><td>Anulado</td></tr>
								</table>
								
								
								
								
							</td>
							<td valign="top">
							<DIV class='TitulosCampos' style="text-align : left;">
									<table border='0'>
									<tbody>
										<tr>
										<td>Buscar&nbsp;</td>
										<td style="white-space : nowrap;">
											<INPUT id="LISTADO_BUSCAR_FCOP" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_CHEQUE_ORDEN_PAGO__PresionarEnter(event);">
											<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();">
												<IMG id="IMG_LIMPIAR" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
											</BUTTON>
										</td>
										</tr>
										<tr>
										<td>Mostrar&nbsp;</td>
										<td>
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FCOP" onchange="Form_CHEQUE_ORDEN_PAGO__BuscarListado_CadenaBuscar=''; Form_CHEQUE_ORDEN_PAGO__BuscarListado();">
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
								<INPUT id="SOMBRA_CHECKBOX_FCOP" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FCOP" type="checkbox" checked="true">Solo buscar al presionar enter
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
<INPUT id="FECHA_ACTUAL_FCOP" type="hidden" value="<?php echo date("d/m/Y")?>">



<script>
	Form_CHEQUE_ORDEN_PAGO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FCOP"), true);
	window.onload=function(){
		Form_CHEQUE_ORDEN_PAGO__Nuevo();
	}
</script>