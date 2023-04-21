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
* @version 20091113
*/
-->

<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<tbody>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FIR">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 240px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar reporte&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FIR" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_IMPRIMIR_REPORTE_FIR" name="FORMULARIO_IMPRIMIR_REPORTE_FIR">
							<table cellspacing='3px' align="center" width="90%">
							<tbody>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_1" name="TIPO_REPORTE_FIR" checked="true">
										Ordenes de Pago con Afectación Presupuestaria - Listado Excel
									</td>
								</tr>
								<tr>
									<TD>
									<br>
									<br>
									<DIV class='TitulosCampos' style="text-align : center;">
										DEL&nbsp;
										<INPUT id='FECHA_INICIO_FIR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php list($dia,$mes,$ano)=explode("/",date("d/m/").SIGA::data()); echo "01/$mes/$ano";?>" ondblclick="showCalendar('FECHA_INICIO_FIR','%d/%m/%Y')"><IMG id="IMG_FECHA_INICIO_FIR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_INICIO_FIR','%d/%m/%Y')">
										AL&nbsp;
										<INPUT id='FECHA_FIN_FIR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/").SIGA::data()?>" ondblclick="showCalendar('FECHA_FIN_FIR','%d/%m/%Y')"><IMG id="IMG_FECHA_FIN_FIR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_FIN_FIR','%d/%m/%Y')">
									</DIV>
									</TD>
								</tr>
							</tbody>
							</table>
						</FORM>
						<br><br>
						<br><br>
						<br><br>
						<DIV class='TitulosCampos' style="text-align : center;">
							<BUTTON type="BUTTON" id="BOTON_IMPRIMIR_FIR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;"  onclick="Form_IMPRIMIR_REPORTE_Imprimir();">
								<IMG id="IMG_IMPRIMIR_FIR" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>

