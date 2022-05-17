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
				<BUTTON class="BotonesVentana" onclick="Form_ORDEN_PAGO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FOP"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FOP" class="BotonesVentana" onclick="Form_ORDEN_PAGO__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FOP"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FOP" class="BotonesVentana"  onclick="Form_ORDEN_PAGO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FOP"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FOP" class="BotonesVentana" onclick="Form_ORDEN_PAGO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FOP"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FOP" class="BotonesVentana" onclick="Form_ORDEN_PAGO__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FOP"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FOP">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 420px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FOP" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FOP">


							<table cellspacing='3' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Número</td>
									<td class='TextCampos'>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td><INPUT id="COMPROBANTE_FOP" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='15' style="width: 120px;"></td>
													<td align="left" width="100%" style="padding-left: 10px;">
														<BUTTON id="BOTON_CONTABLIZAR_FOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_PAGO__Contabilizar();" type="BUTTON">Contabilizar</BUTTON>
														<BUTTON id="BOTON_REVERSAR_FOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_PAGO__Reversar();" type="BUTTON">Reversar</BUTTON>
														<BUTTON id="BOTON_ANULAR_FOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_PAGO__Anular();" type="BUTTON">Anular</BUTTON>
														<BUTTON id="BOTON_VER_POSTERIORES_FOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_PAGO__VerCheques();" type="BUTTON">Cheques Asociados</BUTTON>
														<BUTTON id="BOTON_VER_ANULACION_FOP" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" onclick="Form_ORDEN_PAGO__VerAnulacion();" type="BUTTON">Ver Anulación</BUTTON>
													</td>
												</tr>
											</tbody>
										</table>
										
										
										
										
										<!--
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td>
												<INPUT id="COMPROBANTE_FOP" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='15' style="width: 120px;">
											</td>
											<td align="right">
												
											</td>
											</tr>
										</tbody>
										</table>-->
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos'>
											
										
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td><INPUT id='FECHA_FOP' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>" style="width: 100px;"><IMG id="IMG_FECHA_FOP" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'></td>
												<td style="white-space: nowrap;" width="1">Tipo de Documento&nbsp;</td>
												<td width="1">
													<select class='TextoCampoInput' id="TIPO_DOCUMENTO_FOP" onchange="Form_ORDEN_PAGO__CambioTipoDocumento()">
														<option value="CC">COMPROMETE/CAUSA</option>
														<option value="GC">CAUSA</option>
														<option value="AC">SOLO AFECTACIÓN CONTABLE</option>
													</select>
												</td>
											</tr>
										</table>
										
										
										
										
									</td>
								</tr>								
								<tr>
									<td class='TitulosCampos'></td>
									<td class='TextCampos'>
										<BUTTON id="BOTON_PROVEEDOR_FOP" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_ORDEN_PAGO__BotonProveedor();" type="BUTTON">
											Proveedor
										</BUTTON>
										<BUTTON id="BOTON_BENEFICIARIO_FOP" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_ORDEN_PAGO__BotonBeneficiario();" type="BUTTON">
											Beneficiario
										</BUTTON>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos' id="TIPO_PERSONA_FOP"></td>
									<td class='TextCampos'>
										<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FOP' class='TextoCampoInputDesactivado' type='text' size='20' maxlength='15' value="" readonly="true" style="width: 150px;"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FOP' class='TextoCampoInputDesactivado' readonly="true" type='text' size='50' value="" style="width: 400px;"><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><IMG id="IMG_LIMPIAR_PB_FOP" class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png' width='18' height='18'>
										<INPUT type="hidden" id="ID_BoP_FOP" value="" />
										<input type="hidden" id="CUENTA_CONTABLE_PB_FOP" value="" />
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Concepto</td>
									<td class='TextCampos'>
										<textarea id="DENOMINACION_FOP" class='TextoCampoInput' rows="3" style="resize: none; width: 588px;"></textarea>
									</td>
								</tr>
							</tbody>
							</table>
							
						<div class="tab-pane" id="SUB_TABPANE_FOP" style="margin-top: 8px;">
							<!-- ******************** ******************** Primera SUBPestaña ****************** *********************-->
							<div class="tab-page"  style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FOP_DP">
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
								<DIV class="AreaTablaListado" style="height : 130px; overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FOP_DP" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FOP_DP">
										<BUTTON id="BOTON_AGREGAR_FOP_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__AgregarDP()">
											<IMG id="IMG_AGREGAR_FOP_DP" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FOP_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__DP_Quitar()">
											<IMG id="IMG_QUITAR_FOP_DP" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="left" id="COMPROMISO_PREVIO_FOP_DC">
											<BUTTON id="BOTON_COMPROMISO_PREVIO_FOP_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__Compromisos()">
												<IMG id="IMG_COMPROMISO_PREVIO_FOP_DC" src='../../image/icon/icon-find-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Compromisos
											</BUTTON>
										</td>
									<td align="right">
										<INPUT id='TOTAL_COMPROMISOS_FOP_DP' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width : 150px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************ ******************** fin ******************* *************************-->
							<!-- ******************** ******************** Segunda SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FOP_DC">
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
								<DIV class="AreaTablaListado" style="height : 130px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FOP_DC" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td align="left" id="BOTONES_AGREGAR_QUITAR_FOP_DC">
											<BUTTON id="BOTON_AGREGAR_FOP_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__AgregarDC()">
												<IMG id="IMG_AGREGAR_FOP_DC" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
											</BUTTON>
											<BUTTON id="BOTON_QUITAR_FOP_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__DC_Quitar()">
												<IMG id="IMG_QUITAR_FOP_DC" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
											</BUTTON>
										</td>
										<td align="right">
											<INPUT id='TOTAL_DEBE_FOP_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
											<INPUT id='TOTAL_HABER_FOP_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
										</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************************ fin ****************** ********************* ***************-->
							<!-- ******************** ******************** Tercera SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FOP_DCG">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cargos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="10%">CARGO</td>
											<td>DENOMINACI&Oacute;N</td>
											<td width="30%">FORMULA</td>
											<td width="15%">MONTO</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 130px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FOP_DCG" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td align="left" id="BOTONES_AGREGAR_QUITAR_FOP_DCG">
											<BUTTON id="BOTON_AGREGAR_FOP_DCG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__AgregarDCG()">
												<IMG id="IMG_AGREGAR_FOP_DCG" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
											</BUTTON>
											<BUTTON id="BOTON_QUITAR_FOP_DCG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__QuitarDCG()">
												<IMG id="IMG_QUITAR_FOP_DCG" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
											</BUTTON>
											<SELECT class="TextoCampoInput" id="AC_FOP" onchange="Form_ORDEN_PAGO__CargarAE();" style="font-size: 10px;"></SELECT>
											<SELECT class="TextoCampoInput" id="AE_FOP" onchange="Form_ORDEN_PAGO__CargarOAE();" style="font-size: 10px;"></SELECT>
											<SELECT class="TextoCampoInput" id="OAE_FOP" onchange="" style="font-size: 10px;"></SELECT>
											
											&nbsp;&nbsp;&nbsp;<span style="font-size: 10px;">MONTO=</span>
											<INPUT id='TOTAL_BC_FOP_DCG' class='TextoCampoInput' type='text' value="" placeholder="MONTO" title="Base de calculo para el cargo" style="text-align : center; width:60px; font-size: 10px; height: auto !important;" onkeypress="return AcceptNum(event,'TOTAL_BC_FOP_DCG');">
										</td>
										<td align="right">											
											<INPUT id='TOTAL_FOP_DCG' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
										</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************************ fin ****************** ********************* ***************-->
							<!-- ******************** ******************** Cuarda SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FOP_DR">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retenciones&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="10%">RETENCIÓN</td>
											<td>DENOMINACI&Oacute;N</td>
											<td width="30%">FORMULA</td>
											<td width="15%">MONTO</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 130px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FOP_DR" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td align="left" id="BOTONES_AGREGAR_QUITAR_FOP_DR">
											<BUTTON id="BOTON_AGREGAR_FOP_DR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__AgregarDR()">
												<IMG id="IMG_AGREGAR_FOP_DR" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
											</BUTTON>
											<BUTTON id="BOTON_QUITAR_FOP_DR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_PAGO__QuitarDR()">
												<IMG id="IMG_QUITAR_FOP_DR" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
											</BUTTON>
											&nbsp;&nbsp;&nbsp;<span style="font-size: 10px;">MONTO=</span>
											<INPUT id='TOTAL_BC_FOP_DR' class='TextoCampoInput' type='text' value="" placeholder="MONTO" title="Base de calculo para la retención" style="text-align : center; width:60px; font-size: 10px; height: auto !important;" onkeypress="return AcceptNum(event,'TOTAL_BC_FOP_DR');">
											<input class='BotonesParaCampos' type="button" value="I.V.A." style="text-align : center; width:60px; font-size: 10px;" onclick="Form_ORDEN_PAGO__MontoBaseIVA()"/>
											<input class='BotonesParaCampos' type="button" value="I.S.L.R." style="text-align : center; width:60px; font-size: 10px;" onclick="Form_ORDEN_PAGO__MontoBaseISLR()"/>
											<input class='BotonesParaCampos' type="button" value="1x1000" style="text-align : center; width:60px; font-size: 10px;" onclick="Form_ORDEN_PAGO__MontoBase1x1000()"/>
										</td>
										<td align="right">
											
											<INPUT id='TOTAL_FOP_DR' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
											
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
					<div class="tab-page" style="height : 420px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FOP_LISTADO" class="MensajesPestanas">&nbsp;</DIV>

						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width='5px'></td>
									<td width="10%">NÚMERO</td>
									<td width="10%">FECHA</td>
									<td width="23%">PROVEEDOR / BENEFICIARIO</td>
									<td>CONCEPTO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 260px;">
							<table id="TABLA_LISTA_FOP" border="0" cellspacing="0" cellpadding="0" width="100%">
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
											<INPUT id="LISTADO_BUSCAR_FOP" class='TextoCampoInput' type="text" value="" size="35" onkeyup="Form_ORDEN_PAGO__PresionarEnter(event);">
											<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;"  onclick="Form_ORDEN_PAGO__LimpiarInputTextBuscarListado();">
											<IMG id="IMG_LIMPIAR_FOP" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
											</BUTTON>
										</td>
										</tr>
										<tr>
										<td>Mostrar&nbsp;</td>
										<td>
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FOP" onchange="Form_ORDEN_PAGO__BuscarListado_CadenaBuscar=''; Form_ORDEN_PAGO__BuscarListado();">
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
								<INPUT id="SOMBRA_CHECKBOX_FOP" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FOP" type="checkbox" checked="true">Solo buscar al presionar enter
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
	Form_ORDEN_PAGO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FOP"), true);
	Form_ORDEN_PAGO__TabPaneSUBTAB = new WebFXTabPane(xGetElementById("SUB_TABPANE_FOP"), true);
	window.onload=function(){
		Form_ORDEN_PAGO__CargarAC();
		Form_ORDEN_PAGO__Nuevo();
	}
</script>

