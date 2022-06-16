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
			<TD class="acciones">
				<BUTTON class="BotonesVentana" onClick="Form_DEFINICIONES_DEDUCCIONES__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FDD"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FDD" class="BotonesVentana" onclick="Form_DEFINICIONES_DEDUCCIONES__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FDD"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FDD" class="BotonesVentana" onclick="Form_DEFINICIONES_DEDUCCIONES__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FDD"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FDD" class="BotonesVentana" onclick="Form_DEFINICIONES_DEDUCCIONES__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FDD"><br>Eliminar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<br>
				<div class="tab-pane" id="TABPANE_FDD">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 265px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FDD" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FDD">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Denominaci&oacute;n</td>
									<td class='TextCampos'>
										<INPUT id='DENOMINACION_FDD' class='TextoCampoInput' type='text' size='40' maxlength='30' value="">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Tipo</td>
									<td class='TextoCampo'>
										<select id='RETENCION_TIPO_FDD' class='TextoCampoInput'>
											<option value="1">IVA</option>
											<option value="2">ISLR</option>
											<option value="3">1 X 1000</option>
											<option value="4">OTRO</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>C&oacute;digo contable</td>
									<td class='TextCampos'>
										<INPUT id='CODIGO_CONTABLE_FDD' class='TextoCampoInput' type='text' size='18' maxlength='12' value=""><INPUT id='NOMBRE_CODIGO_CONTABLE_FDD' type='text' size='40' value="" readonly="true" class="TextoCampoInputDesactivado"><IMG id='IMG_BUSCAR_CODIGO_CONTABLE_FDD' class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
									</td>
								</tr>
								<tr style="display: none;">
									<td class='TitulosCampos'>Deducible</td>
									<td class='TextCampos'>
										<INPUT id='DEDUCIBLE_FDD' class='TextoCampoInput' type='text' size='32' value="" onkeypress="return AcceptNum(event)" onkeyup="Form_DEFINICIONES_DEDUCCIONES__ProbarFormula();">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>F&oacute;rmula</td>
									<td class='TextCampos'>
										<INPUT id='FORMULA_FDD' class='TextoCampoInput' type='text' size='60' value="" onkeyup="Form_DEFINICIONES_DEDUCCIONES__ProbarFormula();">
									</td>
								</tr>
								<tr>
									<td colspan="2" rowspan="1" class='TextCampos' style="text-align : center; vertical-align : top;">
										<br>
										<fieldset >
											<LEGEND><strong>Probar f&oacute;rmula</strong></LEGEND>
											<table cellspacing="5px">
											<tbody>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
												<tr>
													<td>Monto</td>
													<td><INPUT id='MONTO_PRUEBA_FDD' class='TextoCampoInput' type='text' size='28' value="0.00" onkeypress="return AcceptNum(event);" onkeyup="Form_DEFINICIONES_DEDUCCIONES__ProbarFormula();" onblur="Form_DEFINICIONES_DEDUCCIONES__PierdeFoco('MONTO_PRUEBA_FDD')" onclick="Form_DEFINICIONES_DEDUCCIONES__TomaFoco('MONTO_PRUEBA_FDD')"></td>
													<td>&nbsp;&nbsp;&nbsp;Resultado</td>
													<td><INPUT id='RESULTADO_PRUEBA_FDD' class='TextoCampoInputDesactivado' type='text' size='28' value="0,00" readonly="true"></td>
												</tr>
											</tbody>
											</table>
										</fieldset>
									</td>
								</tr>
							</tbody>
							</table>
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 265px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FDD_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="5%">COD</td>
									<td width="65%">DENOMINACI&Oacute;N</td>
									<td width="20%">F&Oacute;RMULA</td>
									<td width="10%">TIPO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 50%;">
							<table id="TABLA_LISTA_FDD" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<DIV class='TitulosCampos' style="text-align : center;">
							Buscar&nbsp;
							<INPUT id="LISTADO_BUSCAR_FDD" class='TextoCampoInput' type="text" value="" size="40" onkeyup="Form_DEFINICIONES_DEDUCCIONES__BuscarListado();">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_DEFINICIONES_DEDUCCIONES__LimpiarInputTextBuscarListado();">
								<IMG id="IMG_LIMPIAR_FDC" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	Form_DEFINICIONES_DEDUCCIONES__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FDD"), true);
	window.onload=function(){
		Form_DEFINICIONES_DEDUCCIONES__Nuevo();
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("Permite la definición o actualización de las retenciones.");
	}
</script>