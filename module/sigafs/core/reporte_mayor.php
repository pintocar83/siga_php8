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
* @date 2009-11-11
* @version 20091111
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="93%" align="center">
	<tbody>
		<tr>
			<td valign="top">
				<br>
				<div class="tab-pane" id="TABPANE_RM">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 295px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Presupuesto - Mayor Anal&iacute;tico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_RM" class="MensajesPestanas">&nbsp;</DIV>
						<FORM id="FORMULARIO_RM" name="FORMULARIO_RM">
							<table cellspacing='5px' align="center" border="0">
							<tbody>
								<tr>
									<td class='TextCampos' width='30%'>
										<b>Estructura&nbsp;presupuestaria</b><br>
										<SELECT multiple="false" class="TextoCampoInput" style="height: 150px; width: 100%; font-size: 10px;" id="SELECT_EP_RM"></SELECT>
									</td>
									<td>&nbsp;&nbsp;</td>
									<td class='TextCampos' width='70%'>
										<b>Cuenta</b><br>
										<SELECT multiple="false" class="TextoCampoInput" style="height: 150px; width: 100%; font-size: 10px;" id="SELECT_CUENTA_RM"></SELECT>
									</td>
								</tr>								
							</tbody>
							</table>							
						</FORM>
						<br>
						<DIV class='TitulosCampos' style="text-align: center;">
							Del
							<INPUT id='FECHA_INICIO_RM' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php list($dia,$mes,$ano)=explode("/",date("d/m/Y")); echo "01/$mes/$ano";?>" ondblclick="showCalendar('FECHA_INICIO_RR','%d/%m/%Y')"><IMG id="IMG_FECHA_INICIO_RR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18' onclick="showCalendar('FECHA_INICIO_RR','%d/%m/%Y')">
							al&nbsp;
							<INPUT id='FECHA_CULMINACION_RM' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/Y")?>" ondblclick="showCalendar('FECHA_FIN_RR','%d/%m/%Y')"><IMG id="IMG_FECHA_FIN_RR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_FIN_RR','%d/%m/%Y')">
							<br>
							<br>
							<BUTTON id="BOTON_ACEPTAR_RR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;"   onclick="reportes_mayor.onDisplay()">
								<IMG id="IMG_ACEPTAR_RR" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar&nbsp;
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
	reportes_mayor.tabPane = new WebFXTabPane(xGetElementById("TABPANE_RM"), true);
	window.onload=function(){
		reportes_mayor.onLoad();
	}
</script>