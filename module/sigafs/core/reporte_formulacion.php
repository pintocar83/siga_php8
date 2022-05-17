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
				<div class="tab-pane" id="TABPANE_FFPDGI">
					<!-- ******************** ******************** Primera Pestaña ****************** ********************* -->
					<div class="tab-page" style="height : 415px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imprimir formulaci&oacute;n presupuesto de gastos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FFPDGI" class="MensajesPestanas">&nbsp;</DIV>

						<table cellspacing='3px' align="center" width="90%">
						<tbody>
							<tr>
								<TD>
								<SELECT class='TextoCampoInput' id="sw_formulacion_reformulacion_FFPDGI">
									<option value="F">FORMULACIÓN</option>
									<option value="R1">REFORMULACIÓN 1</option>
								</SELECT>
								</TD>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_1" value="V" checked>
									<strong>Consolidado general por acciones centralizadas y proyectos.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_2" value="E">
									<strong>Consolidado general por acciones centralizadas.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_3" value="3">
									<strong>Consolidado general por proyectos.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_4" value="4">
									<strong>Consolidado por acci&oacute;n centralizada o proyecto.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO_FFPDGI" class='TextoCampoInput'></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_PROYECTO_FFPDGI" value="" size="50" readonly="true">
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_5" value="5">
									<strong>Consolidado por acci&oacute;n especifica.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO2_FFPDGI" class='TextoCampoInput' onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarAE();"></SELECT>
									<SELECT id="ID_ACCION_ESPECIFICA_FFPDGI" class='TextoCampoInput'></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_ACCION_ESPECIFICA_FFPDGI" value="" size="30" readonly="true">
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_6" value="5">
									<strong>Consolidado por otras acciones especificas.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO3_FFPDGI" class='TextoCampoInput' onchange="Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR3__CargarAE();"></SELECT>
									<SELECT id="ID_ACCION_ESPECIFICA3_FFPDGI" class='TextoCampoInput' onchange=""></SELECT>
									<SELECT id="ID_OTRA_ACCION_ESPECIFICA3_FFPDGI" class='TextoCampoInput'></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_OTRA_ACCION_ESPECIFICA_FFPDGI" value="" size="30" readonly="true">
								</td>
							</tr>							
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI" id="ID_RADIO_IMPRIMIR_FFPDGI_7" value="5">
									<strong>Consolidado por acci&oacute;n centralizada o proyecto, y por otras acciones especificas.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO4_FFPDGI" class='TextoCampoInput' onchange=""></SELECT>
									<SELECT id="ID_OTRA_ACCION_ESPECIFICA4_FFPDGI" class='TextoCampoInput'></SELECT>
								</td>
							</tr>
						</tbody>
						</table>
						<DIV align="center"><INPUT type="checkbox" id="OCULTAR_FILAS_FFPDG_1" onchange="xGetElementById('OCULTAR_FILAS_FFPDG_2').checked=xGetElementById('OCULTAR_FILAS_FFPDG_1').checked; xGetElementById('OCULTAR_FILAS_FFPDG_3').checked=xGetElementById('OCULTAR_FILAS_FFPDG_1').checked;">Ocultar filas con montos cero.</DIV>
						<br>
						<DIV align="center" style="position : absolute; text-align : center; top : 390px; width : 100%;">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__Imprimir();">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** ********************* -->
					<div class="tab-page" style="height : 415px;">
						<h2 class="tab"  style="display: none;">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Instructivo nº 4 (pt. I) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FFPDGI_2" class="MensajesPestanas">&nbsp;</DIV>

						<table cellspacing='3px' align="center" width="90%">
						<tbody>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_1" value="1" checked="true">
									<strong>Instructivo nº4.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_3" value="3">
									<strong>Consolidado de proyectos y acciones centralizadas por partidas de egreso.</strong>&nbsp;<em>(Pag.&nbsp;57)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_2" value="2">
									<strong>Resumen de cr&eacute;ditos por partidas y fuentes de financiamiento.</strong>&nbsp;<em>(Pag.&nbsp;59)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_8" value="8">
									<strong>Relaci&oacute;n de transferencia y donaciones por recibir del sector p&uacute;blico.</strong>&nbsp;<em>(Pag.&nbsp;64)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_4" value="4">
									<strong>Relaci&oacute;n de transferencia y donaciones por otorgar al sector p&uacute;blico.</strong>&nbsp;<em>(Pag.&nbsp;66)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_5" value="5">
									<strong>Relaci&oacute;n de transferencia y donaciones por otorgar al sector privado.</strong>&nbsp;<em>(Pag.&nbsp;68)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_11" value="11">
									<strong>Resumen de inversiones.</strong>&nbsp;<em>(Pag.&nbsp;71)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_6" value="6">
									<strong>Presupuesto de ingresos y fuentes financieras.</strong>&nbsp;<em>(Pag.&nbsp;73)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_7" value="7">
									<strong>Presupuesto de gastos y aplicaciones financieras.</strong>&nbsp;<em>(Pag.&nbsp;75)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_12" value="12">
									<strong>Cuenta ahorro / inversi&oacute;n / financiamiento. (*)</strong>&nbsp;<em>(Pag.&nbsp;80)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_9" value="9">
									<strong>Estado de resultados.</strong>&nbsp;<em>(Pag.&nbsp;103)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_10" value="10">
									<strong>Presupuesto de caja.</strong>&nbsp;<em>(Pag.&nbsp;105)</em>
								</td>
							</tr><!--Ultimo valor 12-->
							<tr>
								<td class='TextCampos' >
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(*) Adaptado a las necesidades de la instituci&oacute;n.
								</td>
							</tr>
						</tbody>
						</table>
						<DIV align="center"><INPUT type="checkbox" id="OCULTAR_FILAS_FFPDG_2" onchange="xGetElementById('OCULTAR_FILAS_FFPDG_1').checked=xGetElementById('OCULTAR_FILAS_FFPDG_2').checked; xGetElementById('OCULTAR_FILAS_FFPDG_3').checked=xGetElementById('OCULTAR_FILAS_FFPDG_2').checked;">Ocultar filas con montos cero.</DIV>
						<br>
						<DIV align="center" style="position : absolute; text-align : center; top : 390px; width : 100%;">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__Imprimir_2();">
								<IMG id="IMG_IMPRIMIR_FPC_2" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
<!-- ******************** ******************** tercera Pestaña ****************** ********************* -->
					<div class="tab-page" style="height : 415px;">
						<h2 class="tab" style="display: none;">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Instructivo nº 4 (pt. II) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FFPDGI_3" class="MensajesPestanas">&nbsp;</DIV>

						<table cellspacing='3px' align="center" width="100%">
						<tbody>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_13" value="13">
									<strong>Identificaci&oacute;n del ente</strong>&nbsp;<em>(Pag.&nbsp;28)</em>
								</td>
							</tr>

							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_14" value="14">
									<strong>Resumen de serie estad&iacute;stica.</strong>&nbsp;<em>(Pag.&nbsp;100)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_15" value="15">
									<strong>Deudas por servicios b&aacute;sicos.</strong>&nbsp;<em>(Pag.&nbsp;88)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_16" value="16">
									<strong>Proyectos que exceden el ejercicio fiscal.</strong>&nbsp;<em>(Pag.&nbsp;60)</em>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FFPDGI_2" id="ID_RADIO_IMPRIMIR_FFPDGI_2_17" value="17">
									<strong>Consolidado de proyectos y acciones centralizadas por fuentes de financiamiento.</strong>&nbsp;<em>(Pag.&nbsp;53)</em>
								</td>
							</tr>
							
							<tr>
								<td class='TextCampos' >
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(*) Adaptado a las necesidades de la instituci&oacute;n.
								</td>
							</tr>
						</tbody>
						</table>
						<DIV align="center"><INPUT type="checkbox" id="OCULTAR_FILAS_FFPDG_3" onchange="xGetElementById('OCULTAR_FILAS_FFPDG_1').checked=xGetElementById('OCULTAR_FILAS_FFPDG_3').checked; xGetElementById('OCULTAR_FILAS_FFPDG_2').checked=xGetElementById('OCULTAR_FILAS_FFPDG_3').checked;">Ocultar filas con montos cero.</DIV>
						<br>
						<DIV align="center" style="position : absolute; text-align : center; top : 390px; width : 100%;">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__Imprimir_2();">
								<IMG id="IMG_IMPRIMIR_FPC_3" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar
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
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FFPDGI"), true);
	window.onload=function(){		
		Form_FORMULACION_PRESUPUESTO_DE_GATOS_IMPRIMIR_CargarDatos();		
	}	
</script>
