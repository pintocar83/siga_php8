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
* @version 20091009
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<body>
		<tr>
			<TD  class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_CHEQUE_DIRECTO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FCD"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FCD" class="BotonesVentana" onclick="Form_CHEQUE_DIRECTO__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FCD"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FCD" class="BotonesVentana"  onclick="Form_CHEQUE_DIRECTO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FCD"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FCD" class="BotonesVentana" onclick="Form_CHEQUE_DIRECTO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FCD"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FCD" class="BotonesVentana" onclick="Form_CHEQUE_DIRECTO__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FCD"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FCD">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCD" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FCD">
							<table cellspacing='3px' align="center" border="0">
							<tbody>
								<tr>
									<td></td>
									<td>
										<BUTTON id="BOTON_PROVEEDOR_FCD" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonProveedor()" type="BUTTON">
											Proveedor
										</BUTTON>
										<BUTTON id="BOTON_BENEFICIARIO_FCD" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonBeneficiario()" type="BUTTON">
											Beneficiario
										</BUTTON>
										<span style="padding-left: 20px;">
											<BUTTON id="BOTON_CONTABLIZAR_FCD" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_DIRECTO__Contabilizar();" type="BUTTON">Contabilizar</BUTTON>
											<BUTTON id="BOTON_REVERSAR_FCD" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_DIRECTO__Reversar();" type="BUTTON">Reversar</BUTTON>
											<BUTTON id="BOTON_ANULAR_FCD" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_CHEQUE_DIRECTO__Anular();" type="BUTTON">Anular</BUTTON>
										</span>
									</td>
									<td id="MSG_CUSTODIA" style="font-weight : bold;"></td>
								</tr>
								<tr>
									<td class='TitulosCampos' id="TIPO_PERSONA_FCD"></td>
									<td class='TextCampos' colspan="2">
										<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FCD' class='TextoCampoInputDesactivado' type='text' size='22' maxlength='15' value="" readonly="true" style="width: 150px;"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FCD' class='TextoCampoInputDesactivado' readonly="true" type='text' size='65' value=""  style="width: 450px;"><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" id="ID_BoP_FCD" value="">
										<input type="hidden" id="CUENTA_CONTABLE_PB_FCD" value="" />
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Cuenta</td>
									<td class='TextCampos' colspan="2">
										<INPUT id="ID_CTA_FCD" class='TextoCampoInputDesactivado' type="hidden" value="" size="4" readonly="true">
										<INPUT id="NCTA_FCD" class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true"  style="width: 150px;"><INPUT id="DESCRIPCION_NCTA_FCD" class='TextoCampoInputDesactivado' type='text' size='65' value="" readonly="true"  style="width: 450px;"><IMG id="IMG_BUSCAR_NCTA_FCD" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" value="" id="CTA_CODIGO_CONTABLE_FCD">
										<INPUT type="hidden" value="" id="CUENTA_CONTABLE_FCD">
										<INPUT type="hidden" value="" id="CTA_DENOMINACION_CONTABLE_FCD">
										<INPUT id="TIPO_CTA_FCD" class='TextoCampoInputDesactivado' type='hidden' size='15' value="" readonly="true">
										<INPUT id='BANCO_FCD' class='TextoCampoInputDesactivado' type='hidden' size='25' value="" readonly="true">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td colspan="2">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td class='TextCampos'><INPUT id='FECHA_FCD' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FCD" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'></td>
											<td class='TitulosCampos'>Cheque&nbsp;No.&nbsp;</td>
											<td class='TextCampos'><INPUT id='N_CHEQUE_FCD' class='TextoCampoInput' type='text' size='15' value=""></td>
											<td class='TitulosCampos' style="width: 30%; font-size: 11px; vertical-align: middle;" id="COMPROBANTE_FCD"></td>
											<!--<td class='TextCampos' style="text-align : right;" width="1px"><INPUT id='N_VOUCHER_FCD' class='TextoCampoInputDesactivado' type='text' size='15' value="" readonly="true"></td>-->
											</tr>
										</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Concepto</td>
									<td class='TextCampos' colspan="2">
										<TEXTAREA cols="73" rows="2" class='TextoCampoInput' id="CONCEPTO_FCD" style="resize: none; width: 100%;"></TEXTAREA>
									</td>
								<tr>
									<td class='TitulosCampos'>Monto</td>
									<td class='TextCampos' colspan="2">
										<INPUT id="MONTO_FCD" class='TextoCampoInput' type='text' size='18' value="" style="text-align : right;" onkeypress="return AcceptNum(event,'MONTO_FCD')">
									</td>
								</tr>
							</tbody>
							</table>
							<!---->
							<!---->
							<!---->
							<!--<table cellspacing='3' align="center">-->
							<!--<tbody>-->
							<!--	<tr>-->
							<!--		<td class='TitulosCampos'>Comprobante</td>-->
							<!--		<td class='TextCampos'>-->
							<!--			<table width="100%" border="0" cellpadding="0" cellspacing="0">-->
							<!--			<tbody>-->
							<!--				<tr>-->
							<!--				<td>-->
							<!--					<INPUT id="ACRONIMO_FCD" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='2'><INPUT id="COMPROBANTE_FCD" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='15'>-->
							<!--				</td>-->
							<!--				<td align="right">-->
							<!--					<span id="COMPROBANTE_TIPO_FCD" style="font-weight: bold;"></span>-->
							<!--				</td>-->
							<!--				</tr>-->
							<!--			</tbody>-->
							<!--			</table>-->
							<!--		</td>-->
							<!--	</tr>-->
							<!--	<tr>-->
							<!--		<td class='TitulosCampos'>Fecha</td>-->
							<!--		<td class='TextCampos'>-->
							<!--			<INPUT id='FECHA_FCD' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FCD" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18'>-->
							<!--		</td>-->
							<!--	</tr>-->
							<!--	<tr>-->
							<!--		<td class='TitulosCampos'>Denominaci&oacute;n</td>-->
							<!--		<td class='TextCampos'>-->
							<!--			<textarea id="DENOMINACION_FCD" class='TextoCampoInput' rows="2" cols="75" style="resize: none;"></textarea>-->
							<!--		</td>-->
							<!--	</tr>-->
							<!--	<tr>-->
							<!--		<td class='TitulosCampos'></td>-->
							<!--		<td class='TextCampos'>-->
							<!--			<BUTTON id="BOTON_PROVEEDOR_FCD" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonProveedor();" type="BUTTON">-->
							<!--				Proveedor-->
							<!--			</BUTTON>-->
							<!--			<BUTTON id="BOTON_BENEFICIARIO_FCD" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonBeneficiario();" type="BUTTON">-->
							<!--				Beneficiario-->
							<!--			</BUTTON>-->
							<!--		</td>-->
							<!--	</tr>-->
							<!--	<tr>-->
							<!--		<td class='TitulosCampos' id="TIPO_PERSONA_FCD"></td>-->
							<!--		<td class='TextCampos'>-->
							<!--			<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FCD' class='TextoCampoInputDesactivado' type='text' size='20' maxlength='15' value="" readonly="true"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FCD' class='TextoCampoInputDesactivado' readonly="true" type='text' size='50' value=""><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png'  width='18' height='18'><IMG id="IMG_LIMPIAR_PB_FCD" class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png'  width='18' height='18'>-->
							<!--			<INPUT type="hidden" id="ID_BoP_FCD" value="" />-->
							<!--			<input type="hidden" id="CUENTA_CONTABLE_PB_FCD" value="" />-->
							<!--		</td>-->
							<!--	</tr>-->
							<!--</tbody>-->
							<!--</table>-->
						<br>

						<div class="tab-pane" id="SUB_TABPANE_FCD">
							<!-- ******************** ******************** Primera SUBPestaña ****************** *********************-->
							<div class="tab-page"  style="height : 145px;">
								<h2 class="tab" id="SUB_TABPANE_FCD_DP">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles presupuestarios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="15%">ESTRUC.&nbsp;PRESUP.</td>
											<td width="10%">CUENTA</td>
											<td>DESCRIPCI&Oacute;N</td>
											<td width="10%">OPERACIÓN</td>
											<td width="10%">MONTO</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 100px; overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FCD_DP" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FCD_DP">
										<BUTTON id="BOTON_AGREGAR_FCD_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CHEQUE_DIRECTO__AgregarDP()">
											<IMG id="IMG_AGREGAR_FCD_DP" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FCD_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CHEQUE_DIRECTO__DP_Quitar()">
											<IMG id="IMG_QUITAR_FCD_DP" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<INPUT id='TOTAL_COMPROMISOS_FCD_DP' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width : 150px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************ ******************** fin ******************* *************************-->
							<!-- ******************** ******************** Segunda SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 145px;">
								<h2 class="tab" id="SUB_TABPANE_FCD_DC">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles contables&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="15%">CUENTA</td>
											<td>DESCRIPCI&Oacute;N</td>
											<td width="15%">DEBE</td>
											<td width="15%">HABER</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 100px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FCD_DC" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FCD_DC">
										<BUTTON id="BOTON_AGREGAR_FCD_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CHEQUE_DIRECTO__AgregarDC()">
											<IMG id="IMG_AGREGAR_FCD_DC" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FCD_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CHEQUE_DIRECTO__DC_Quitar()">
											<IMG id="IMG_QUITAR_FCD_DC" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<INPUT id='TOTAL_DEBE_FCD_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
										<INPUT id='TOTAL_HABER_FCD_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************************ fin ****************** ********************* ***************-->
						</div>







						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCD_LISTADO" class="MensajesPestanas">&nbsp;</DIV>

						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="11%">NÚMERO</td>
									<td width="10%">FECHA</td>
									<td width="25%" id="TITULO_PB_FCD_LISTADO"></td>
									<td>CONCEPTO</td>									
									<td width="10%">MONTO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 240px;">
							<table id="TABLA_LISTA_FCD" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>

						<br>
						<BUTTON id="BOTON_PROVEEDOR_2_FCD" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonProveedor();Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado();" type="BUTTON">
							Proveedores
						</BUTTON>
						<BUTTON id="BOTON_BENEFICIARIO_2_FCD" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__BotonBeneficiario();Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado();" type="BUTTON">
							Beneficiarios
						</BUTTON>
						<table width="100%" align="center" border='0'>
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
								<DIV class='TitulosCampos' style="text-align : left; padding-left: 10px;">
									<table align="center" border="0" width="100%">
									<tbody>
										<tr >
											<td>Buscar&nbsp;</td>
											<td style="white-space : nowrap;">
												<INPUT id="LISTADO_BUSCAR_FCD" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_CHEQUE_DIRECTO__PresionarEnter(event);">
												<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado();">
													<IMG id="IMG_LIMPIAR_FCD" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
												</BUTTON>
											</td>
										</tr>
										<tr>
											<td >Mostrar&nbsp;</td>
											<td >
												<SELECT class="TextoCampoInput" id="MES_FILTRAR_FCD" onchange="Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar=''; Form_CHEQUE_DIRECTO__BuscarListado();">
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
								<!--<INPUT type="checkbox" id="MOSTRA_CONTABILIZADO_FCD" checked="true" onchange="Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar=''; Form_CHEQUE_DIRECTO__BuscarListado();">Mostrar contabilizados<br>-->
								<INPUT id="SOMBRA_CHECKBOX_FCD" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FCD" type="checkbox" checked="true">Solo buscar al presionar enter
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
	Form_CHEQUE_DIRECTO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FCD"), true);
	Form_CHEQUE_DIRECTO__TabPaneSUBTAB = new WebFXTabPane(xGetElementById("SUB_TABPANE_FCD"), true);
	window.onload=function(){
		Form_CHEQUE_DIRECTO__BotonProveedor();
		Form_CHEQUE_DIRECTO__Nuevo();
	}
</script>

