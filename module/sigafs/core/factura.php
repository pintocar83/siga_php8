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
				<BUTTON class="BotonesVentana" onclick="Form_FACTURA__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FF"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FF" class="BotonesVentana"  onclick="Form_FACTURA__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FF"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FF" class="BotonesVentana" onclick="Form_FACTURA__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FF"><br>Eliminar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FF">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 360px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FF" class="MensajesPestanas">&nbsp;</DIV>
						<br>

						<FORM id="FORMULARIO_FF">
							<table cellspacing='3px' align="center">
							<tbody>
								<tr>
									<td></td>
									<td>
										<BUTTON id="BOTON_PROVEEDOR_FF" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_FACTURA__BotonProveedor();" type="BUTTON">
											Proveedor
										</BUTTON>
										<BUTTON id="BOTON_BENEFICIARIO_FF" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_FACTURA__BotonBeneficiario();" type="BUTTON">
											Beneficiario
										</BUTTON>
									</td>
									<td id="TD_ANULAR_FF" width="1"></td>
								</tr>
								<tr>
									<td class='TitulosCampos' id="TIPO_PERSONA_FF"></td>
									<td class='TextCampos' colspan="2">
										<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FF' class='TextoCampoInputDesactivado' type='text' size='15' value="" readonly="true"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FF' class='TextoCampoInputDesactivado' readonly="true" type='text' size='40' value=""><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" id="ID_BoP_FF" value="">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos' colspan="2">
										<INPUT id='FECHA_FF' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FF" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>N&uacute;mero</td>
									<td class='TextCampos'>
										<TABLE width="100%" cellpadding="0" cellspacing="0">
											<TR>
												<TD><INPUT id='NUMERO_FF' class='TextoCampoInput' type='text' size='10' value=""></TD>
												<TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
												<TD>N&uacute;mero de control&nbsp;<INPUT id='N_CONTROL_FF' class='TextoCampoInput' type='text' size='10' value=""></TD>
												<TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
												<TD align="right">Total&nbsp;<INPUT id='TOTAL_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'TOTAL_FF')" style="text-align: right;"></TD>
											</TR>
										</TABLE>
									</td>
								</tr>
							</tbody>
							</table>

							<br><br>
							<table width="98%" align="center" cellpadding="0" cellspacing="2">
								<TR valign="top">
									<!--<TD>
										<FIELDSET>
										<LEGEND><strong>Informaci&oacute;n orden de pago</strong></LEGEND>
										<TABLE align="center">
											<TR><TD class='TitulosCampos'>Orden</TD><TD class='TextCampos'><INPUT id='ORDEN_PAGO_FF' class='TextoCampoInput' type='text' size='10' value="" onchange="Form_CargarDatosOrdenPago()"><IMG id="IMG_ORDEN_PAGO_RECARGAR_FF" class='BotonesParaCampos' src='../img/iconos/actualizar_activo.png' onmouseover="src='../img/iconos/actualizar_con_foco.png';" onmouseout="src='../img/iconos/actualizar_activo.png'" width='20' height='20'></TD></TR>
											<TR><TD class='TitulosCampos'>Total retenci&oacute;n IVA</TD><TD class='TextCampos'><INPUT id='OP_TOTAL_RETENCION_IVA_FF' class='TextoCampoInputDesactivado' type='text' size='10' value="" readonly="true"></TD></TR>
											<TR><TD class='TitulosCampos'>Total retenci&oacute;n ISLR</TD><TD class='TextCampos'><INPUT id='OP_TOTAL_RETENCION_ISLR_FF' class='TextoCampoInputDesactivado' type='text' size='10' value="" readonly="true"></TD></TR>
											<TR>
												<TD class='TextCampos' colspan="2">
												<strong>Documentos</strong><br>
												<SELECT multiple="true" class="TextoCampoInput" style="height : 50px; width : 230px;" id="SELECT_DOCUMENTOS_FF"></SELECT>
												</TD>
											</TR>
										</TABLE>
										</FIELDSET>
									</TD>-->
									<TD>
										<FIELDSET>
										<LEGEND><strong>Informaci&oacute;n IVA</strong></LEGEND>
										<TABLE align="center">
											<TR>
												<TD class='TitulosCampos'>Monto base</TD>
												<TD class='TextCampos' style="text-align: right;">
													<INPUT id='BASE_IVA_FF' class='TextoCampoInput' type='text' size='10' value="" onchange='Form_FACTURA__SetIVA()' onkeypress="return AcceptNum(event,'BASE_IVA_FF')" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<TD class='TitulosCampos'>% IVA</TD>
												<TD class='TextCampos' style="text-align: right;">
													<select class='TextoCampoInput' id="PORCENTAJE_IVA2_FF" onchange="Form_FACTURA__SetIVA()">
														<option value="16">16%</option>
														<option value="12">12%</option>
														<option value="10">10%</option>
														<option value="9">9%</option>
														<option value="8">8%</option>
													</select>
													<INPUT id='PORCENTAJE_IVA_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'PORCENTAJE_IVA_FF')" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<TD class='TitulosCampos'>IVA</TD>
												<TD class='TextCampos' style="text-align: right;">
													<INPUT id='MONTO_IVA_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'MONTO_IVA_FF')" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<TD class='TitulosCampos'>Retenci&oacute;n</TD>
												<TD class='TextCampos' style="text-align: right;">
													<select class='TextoCampoInput' id="RETENCION_PIVA_FF" onchange="Form_FACTURA__CalcularIVA()">
														<option value="0.75">75%</option>
														<option value="1">100%</option>
													</select>
													<INPUT id='RETENCION_IVA_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'RETENCION_IVA_FF')" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<TD class='TitulosCampos'>Exento</TD>
												<TD class='TextCampos' style="text-align: right;">													
													<INPUT id='EXENTO_FF' class='TextoCampoInput' type='text' size='10' value="" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<TD class='TitulosCampos'>Total Pago</TD>
												<TD class='TextCampos' style="text-align: right;">													
													<INPUT id='TOTAL_PAGO_FF' class='TextoCampoInput' type='text' size='10' value="" style="text-align: right;">
												</TD>
											</TR>
											<TR>
												<td></td>
												<td style="text-align: right;">
													<input type="BUTTON" value="Calcular" class="BotonesParaCampos" onclick='Form_FACTURA__CalcularIVA()'>
												</td>
											</TR>
										</TABLE>
										</FIELDSET>
									</TD>
									<TD>
										<FIELDSET>
										<LEGEND><strong>Informaci&oacute;n ISLR</strong></LEGEND>
										<TABLE align="center">
											<TR><TD class='TitulosCampos'>Monto base</TD><TD class='TextCampos'><INPUT id='BASE_ISLR_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'BASE_ISLR_FF')" style="text-align: right;"></TD></TR>
											<TR><TD class='TitulosCampos'>% ISLR</TD><TD class='TextCampos'><INPUT id='PORCENTAJE_ISLR_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'PORCENTAJE_ISLR_FF')" style="text-align: right;"></TD></TR>
											<TR style='display: none;'><TD class='TitulosCampos'>ISLR</TD><TD class='TextCampos'><INPUT id='MONTO_ISLR_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'MONTO_ISLR_FF')" style="text-align: right;"></TD></TR>
											<TR><TD class='TitulosCampos'>Retenci&oacute;n</TD><TD class='TextCampos'><INPUT id='RETENCION_ISLR_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'RETENCION_ISLR_FF')" style="text-align: right;"></TD></TR>
											<TR>
												<td></td>
												<td style="text-align: right;">
													<input type="BUTTON" value="Calcular" class="BotonesParaCampos" onclick='Form_FACTURA__CalcularISLR()'>
												</td>
											</TR>
										</TABLE>
										</FIELDSET>
									</TD>
									<TD>
										<FIELDSET>
										<LEGEND><strong>Informaci&oacute;n 1x1000</strong></LEGEND>
										<TABLE align="center">
											<TR><TD class='TitulosCampos'>Monto base</TD><TD class='TextCampos'><INPUT id='BASE_1X1000_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'BASE_1X1000_FF')" style="text-align: right;"></TD></TR>
											<TR><TD class='TitulosCampos'>% 1x1000</TD><TD class='TextCampos'><INPUT id='PORCENTAJE_1X1000_FF' class='TextoCampoInput' type='text' size='10' value="0.10" onkeypress="return AcceptNum(event,'PORCENTAJE_1X1000_FF')" style="text-align: right;"></TD></TR>
											<TR style='display: none;'><TD class='TitulosCampos'>1x1000</TD><TD class='TextCampos'><INPUT id='MONTO_1X1000_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'MONTO_1X1000_FF')" style="text-align: right;"></TD></TR>
											<TR><TD class='TitulosCampos'>Retenci&oacute;n</TD><TD class='TextCampos'><INPUT id='RETENCION_1X1000_FF' class='TextoCampoInput' type='text' size='10' value="" onkeypress="return AcceptNum(event,'RETENCION_1X1000_FF')" style="text-align: right;"></TD></TR>
											<TR>
												<td></td>
												<td style="text-align: right;">
													<input type="BUTTON" value="Calcular" class="BotonesParaCampos" onclick='Form_FACTURA__Calcular1x1000()'>
												</td>
											</TR>
										</TABLE>
										</FIELDSET>
									</TD>
								</TR>
							</table>
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 340px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FF_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="12%">Fecha</td>
									<td width="15%">RIF/CI</td>
									<td>Nombre</td>
									<td width="10%">Nº Factura</td>
									<td width="10%">Nº Control</td>
									<td width="10%">Total</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 200px;">
							<table id="TABLA_LISTA_FF" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<table width="100%" align="center">
						<tbody>
							<tr>
							<td valign="top">
								<DIV class='TitulosCampos' style="text-align : center;">
									<table>
									<tbody>
										<tr>
										<td>Buscar&nbsp;</td>
										<td style="white-space : nowrap; text-align: left;">
											<INPUT id="LISTADO_BUSCAR_FF" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_FACTURA__PresionarEnter(event);">
											<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_FACTURA__BuscarListado();">
											<IMG id="IMG_LIMPIAR_FF" src='../../image/icon/icon-clear-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Limpiar
											</BUTTON>
										</td>
										</tr>
										<tr>
										<td>Mostrar&nbsp;</td>
										<td style="text-align: left;">
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FF" onchange="Form_FACTURA__BuscarListado_CadenaBuscar=''; Form_FACTURA__BuscarListado();">
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
								<INPUT id="SOMBRA_CHECKBOX_FF" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FF" type="checkbox" checked="true">Solo buscar al presionar enter
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
	Form_FACTURA__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FF"), true);
	window.onload=function(){
		Form_FACTURA__Nuevo();
	}
</script>

