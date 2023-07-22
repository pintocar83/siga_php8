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
					<div class="tab-page"  style="height : 300px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar reporte&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FIR" class="MensajesPestanas">&nbsp;</DIV>

						<FORM id="FORMULARIO_IMPRIMIR_REPORTE_FIR" name="FORMULARIO_IMPRIMIR_REPORTE_FIR">
							<table cellspacing='3px' align="center" width="90%">
							<tbody>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_1" name="TIPO_REPORTE_FIR" checked="true">
										Libro diario
									</td>
								</tr>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_2" name="TIPO_REPORTE_FIR">
										Libro mayor
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<select id="SELECT_MAYOR_FIR" class="TextoCampoInput"><option value="1">NORMAL</option><option value="2">POR CHEQUE/BENEFICIARIO</option></select>&nbsp;<INPUT id='CODIGO_CONTABLE_FIR' class='TextoCampoInput' type='text' size='12' value=""><IMG id="IMG_BUSCAR_CODIGO_CONTABLE_FIR" class='BotonesParaCampos' src='../../image/icon/icon-find.png' width='18' height='18' onclick="Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FIR');"><IMG id="IMG_LIMPIAR_CODIGO_CONTABLE_FIR" class='BotonesParaCampos' src='../../image/icon/icon-clear.png' width='18' height='18' onclick="xGetElementById('CODIGO_CONTABLE_FIR').value=''">&nbsp;&nbsp;&nbsp;<INPUT type="checkbox" id="FORMA_CONTINUA_FFCO_1" checked onchange="xGetElementById('FORMA_CONTINUA_FFCO_1').checked;">Forma continua.
									</td>
								</tr>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_3" name="TIPO_REPORTE_FIR">
										Balance de comprobanci&oacute;n
									</td>
								</tr>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_4" name="TIPO_REPORTE_FIR">
										Balance general
										<br>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<select id="SELECT_BALANCEGENERAL_FIR" class="TextoCampoInput"><option value="1" style="display:none;">FORMATO A</option><option value="2" selected>FORMATO B</option></select>
									</td>
								</tr>
								<tr>
									<td class='TextCampos'>
										<INPUT type="radio" id="TIPO_REPORTE_FIR_5" name="TIPO_REPORTE_FIR">
										Estado de resultados
									</td>
								</tr>
								<tr>
									<TD>

									<DIV class='TitulosCampos' style="text-align : center;">
										<?php
										$anio=SIGA::data();
										$anio_actual=date("Y");
										if($anio!=$anio_actual){
											$fecha_inicio="01/01/$anio";
											$fecha_final="31/12/$anio";
										}
										else{
											$fecha_inicio="01/".date("m")."/$anio";
											$fecha_final=date("d/m/Y");
										}

										?>
										DEL&nbsp;
										<INPUT id='FECHA_INICIO_FIR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo "$fecha_inicio";?>" ondblclick="showCalendar('FECHA_INICIO_FIR','%d/%m/%Y')"><IMG id="IMG_FECHA_INICIO_FIR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_INICIO_FIR','%d/%m/%Y')">
										AL&nbsp;
										<INPUT id='FECHA_FIN_FIR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo "$fecha_final"?>" ondblclick="showCalendar('FECHA_FIN_FIR','%d/%m/%Y')"><IMG id="IMG_FECHA_FIN_FIR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_FIN_FIR','%d/%m/%Y')">
									</DIV>
									</TD>
								</tr>
							</tbody>
							</table>
						</FORM>
						
						<DIV align="center"><INPUT type="checkbox" id="OCULTAR_FILAS_FFCO_1" onchange="xGetElementById('OCULTAR_FILAS_FFCO_1').checked;">Ocultar filas con montos cero.</DIV>
						<br>
						
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

