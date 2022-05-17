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
<table border="0" cellpadding="0" cellspacing="0" width="93%" align="center">
	<tbody>
		<tr>
			<td valign="top">
				<br>
				<div class="tab-pane" id="TABPANE_FCM">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 250px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCM" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FCM" name="FORMULARIO_FCM">
							<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marque los meses que desea cerrar:</strong>
							<br>
							<table cellspacing='3px' align="center" style="margin-top: 5px;">
							<tbody>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_1_FCM" type="checkbox"></td>
									<td class='TextCampos'>Enero</td>
									<td style="width: 50px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_7_FCM" type="checkbox"></td>
									<td class='TextCampos'>Julio</td>
								</tr>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_2_FCM" type="checkbox"></td>
									<td class='TextCampos'>Febrero</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_8_FCM" type="checkbox"></td>
									<td class='TextCampos'>Agosto</td>
								</tr>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_3_FCM" type="checkbox"></td>
									<td class='TextCampos'>Marzo</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_9_FCM" type="checkbox"></td>
									<td class='TextCampos'>Septiembre</td>
								</tr>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_4_FCM" type="checkbox"></td>
									<td class='TextCampos'>Abril</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_10_FCM" type="checkbox"></td>
									<td class='TextCampos'>Octubre</td>
								</tr>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_5_FCM" type="checkbox"></td>
									<td class='TextCampos'>Mayo</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_11_FCM" type="checkbox"></td>
									<td class='TextCampos'>Noviembre</td>
								</tr>
								<tr>
									<td class='TitulosCampos'><INPUT id="MES_6_FCM" type="checkbox"></td>
									<td class='TextCampos'>Junio</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='TitulosCampos'><INPUT id="MES_12_FCM" type="checkbox"></td>
									<td class='TextCampos'>Diciembre</td>
								</tr>
							</tbody>
							</table>
						</FORM>
						<br><br>
						<DIV class='TitulosCampos' style="text-align : center; vertical-align: middle;">
							<BUTTON id="BOTON_ACEPTAR_FCM" class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;"   onclick="Form_CERRAR_MES__Aceptar();">
								<IMG id="IMG_ACEPTAR_FCM" src='../../image/icon/icon-accept-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Aplicar&nbsp;
							</BUTTON>
							&nbsp;&nbsp;&nbsp;
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;" onclick="siga.close('cerrar_mes');">
								<IMG id="IMG_CANCELAR_FCM" src='../../image/icon/icon-close-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;&nbsp;Cerrar&nbsp;&nbsp;
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>

<script>
	Form_CERRAR_MES__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FCM"), true);
	window.onload=function(){
		Form_CERRAR_MES__Cargar();
	}
</script>
