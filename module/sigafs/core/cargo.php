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
			<!--
			<TD align="right">
				<br>
				<BUTTON class="BotonesVentana" onmouseover="IMG_NUEVO_FDC.src='../img/iconos/nuevo_con_foco.png';" onmouseout="IMG_NUEVO_FDC.src='../img/iconos/nuevo_activo.png'" onClick="Form_DEFINICIONES_CARGOS__Nuevo();">
					<IMG src="../img/iconos/nuevo_activo.png" width="22" height="22" border="0" id="IMG_NUEVO_FDC"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FDC" class="BotonesVentana" onmouseover="IMG_GUARDAR_FDC.src='../img/iconos/guardar_con_foco.png';" onmouseout="IMG_GUARDAR_FDC.src='../img/iconos/guardar_activo.png'" onclick="Form_DEFINICIONES_CARGOS__GuardarVerificar();">
					<IMG src="../img/iconos/guardar_activo.png" width="22" height="22" border="0" id="IMG_GUARDAR_FDC"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FDC" class="BotonesVentana" onmouseover="IMG_MODIFICAR_FDC.src='../img/iconos/modificar_con_foco.png';" onmouseout="IMG_MODIFICAR_FDC.src='../img/iconos/modificar_activo.png'" onclick="Form_DEFINICIONES_CARGOS__Modificar();">
					<IMG src="../img/iconos/modificar_activo.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FDC"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FDC" class="BotonesVentana" onmouseover="IMG_ELIMINAR_FDC.src='../img/iconos/eliminar_con_foco.png';" onmouseout="IMG_ELIMINAR_FDC.src='../img/iconos/eliminar_activo.png'" onclick="Form_DEFINICIONES_CARGOS__Eliminar();">
					<IMG src="../img/iconos/eliminar_activo.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FDC"><br>Eliminar
				</BUTTON>
			</TD>
			-->
			<TD class="acciones">
				<BUTTON class="BotonesVentana" onClick="Form_DEFINICIONES_CARGOS__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FB"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FDC" class="BotonesVentana" onclick="Form_DEFINICIONES_CARGOS__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FB"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FDC" class="BotonesVentana" onclick="Form_DEFINICIONES_CARGOS__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FB"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FDC" class="BotonesVentana" onclick="Form_DEFINICIONES_CARGOS__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FB"><br>Eliminar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top"  class="formulario">
				<br>
				<div class="tab-pane" id="TABPANE_FDC">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 340px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FDC" class="MensajesPestanas">&nbsp;</DIV>
						<br>

						<FORM id="FORMULARIO_FDC">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Denominaci&oacute;n</td>
									<td class='TextCampos'>
										<INPUT id='DENOMINACION_FDC' class='TextoCampoInput' type='text' size='40' maxlength='30' value="">
									</td>
								</tr>
								<!--<tr>
									<td class='TitulosCampos'>C&oacute;digo contable</td>
									<td class='TextCampos'>

									</td>
								</tr>-->

								<tr>
									<td class='TitulosCampos'>F&oacute;rmula</td>
									<td class='TextCampos'>
										<INPUT id='FORMULA_FDC' class='TextoCampoInput' type='text' size='60' value="" onkeyup="Form_DEFINICIONES_CARGOS__ProbarFormula();">
									</td>
								</tr>
								</tr>
									<td class='TitulosCampos'>Es I.V.A.</td>
									<td class='TextCampos'><INPUT type="checkbox" id="ES_IVA_FDC">
									</td>
								</tr>
								<tr>
									<td colspan="2" rowspan="1" class='TextCampos' style="text-align : center; vertical-align : top;">
										<br>
										<fieldset style="width:630px;">
											<LEGEND><strong>Afectaci&oacute;n</strong></LEGEND>
											<DIV align="left" style="padding-top : 5px;">
												<!--<INPUT id="AFECTACION_C_FDC" type="radio" name="AFECTACION_N_FDC" onclick="Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionContable(); Form_DEFINICIONES_DE_CARGOS__ActivarCamposAfectacionContable();"> Contable&nbsp;&nbsp;&nbsp;&nbsp;-->
												<INPUT id="AFECTACION_P_FDC" type="radio" name="AFECTACION_N_FDC" onclick="Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionPresupuestaria();Form_DEFINICIONES_DE_CARGOS__ActivarCamposAfectacionPresupuestaria();" checked> Presupuestaria
											</DIV>
											<DIV id="BLOQUE_CONTABLE_PRESUPUESTARIO_FDC" align="center" style="padding-top : 5px;"></DIV>
										</fieldset>
									</td>
								</tr>
								<tr>
									<td colspan="2" rowspan="1" class='TextCampos' style="text-align : center; vertical-align : top;">
										<br>
										<fieldset>
											<LEGEND><strong>Probar f&oacute;rmula</strong></LEGEND>
											<table cellspacing="5px" align="center">
											<tbody>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
												<tr>
													<td>Monto</td>
													<td><INPUT id='MONTO_PRUEBA_FDC' class='TextoCampoInput' type='text' size='28' value="0.00" onkeypress="return AcceptNum(event);" onkeyup="Form_DEFINICIONES_CARGOS__ProbarFormula();" onblur="Form_DEFINICIONES_CARGOS__PierdeFoco('MONTO_PRUEBA_FDC')" onclick="Form_DEFINICIONES_CARGOS__TomaFoco('MONTO_PRUEBA_FDC')"></td>
													<td>&nbsp;&nbsp;&nbsp;Resultado</td>
													<td><INPUT id='RESULTADO_PRUEBA_FDC' class='TextoCampoInputDesactivado' type='text' size='28' value="0,00" readonly="true"></td>
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
					<div class="tab-page" style="height : 340px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FDC_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="5%">COD</td>
									<td width="50%">DENOMINACI&Oacute;N</td>
									<td width="45%">F&Oacute;RMULA</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 200px;">
							<table id="TABLA_LISTA_FDC" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<DIV class='TitulosCampos' style="text-align : center;">
							Buscar&nbsp;
							<INPUT id="LISTADO_BUSCAR_FDC" class='TextoCampoInput' type="text" value="" size="40" onkeyup="Form_DEFINICIONES_CARGOS__BuscarListado();">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_DEFINICIONES_CARGOS__BuscarListado();">
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
	Form_DEFINICIONES_CARGOS__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FDC"), true);
	window.onload=function(){
		Form_DEFINICIONES_CARGOS__Nuevo();
		Form_DEFINICIONES_CARGOS__Mensaje("Permite la definición o actualización de los cargos e impuestos.");
	}
</script>
