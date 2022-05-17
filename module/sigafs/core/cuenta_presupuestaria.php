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
				<BUTTON class="BotonesVentana" onClick="Form_PLAN_CUENTAS_PRESUPUESTARIAS__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FPCP"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FPCP" class="BotonesVentana" onclick="Form_PLAN_CUENTAS_PRESUPUESTARIAS__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FPCP"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FPCP" class="BotonesVentana" onclick="Form_PLAN_CUENTAS_PRESUPUESTARIAS__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FPCP"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FPCP" class="BotonesVentana" onclick="Form_PLAN_CUENTAS_PRESUPUESTARIAS__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FPCP"><br>Eliminar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FPCP">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 320px;">
						<h2 id="abcd" class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FPCP" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_PLAN_CUENTAS_PRESUPUESTARIAS" name="FORMULARIO_PLAN_CUENTAS_PRESUPUESTARIAS">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>C&oacute;digo</td>
									<td class='TextCampos'><INPUT id="CODIGO_CC_FPCP" class='TextoCampoInput' type='text' size='30' maxlength="9"></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Denominaci&oacute;n</td>
									<td class='TextCampos'>
										<textarea id="DENOMINACION_CC_FPCP" class='TextoCampoInput' cols="50" rows="3"></textarea>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Padre</td>
									<td class='TextCampos'>
										<select id="PADRE_CC_FPCP" class='TextoCampoInput'>
											<option value="t">SI</option>
											<option value="f">NO</option>
										</select>
									</td>
								</tr>
							</tbody>
							</table>

						</FORM>
					</div>					
					<!-- ************************ ******************** fin ******************* *************************-->		
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 320px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FPCP_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">	
									<td width="13%">C&Oacute;DIGO</td>
									<td width="87%">DENOMINACI&Oacute;N</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 60%;">
							<table id="TABLA_LISTA_FPCP" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<table width="100%" border="0">
						<tbody>
							<tr>
								<td width="70%">
									<DIV class='TitulosCampos' style="text-align : center; vertical-align : middle;">
										Buscar&nbsp;
										<INPUT id="LISTADO_BUSCAR_FPCP" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_PLAN_CUENTAS_PRESUPUESTARIAS__BuscarListado();">
										<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_PLAN_CUENTAS_PRESUPUESTARIAS__LimpiarInputTextBuscarListado();">
											<IMG id="IMG_LIMPIAR_FPCP" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
										</BUTTON>
									</DIV>				
								</td>
								<td width="30%" class='TitulosCampos' style="text-align : left;">
									<!--<INPUT id="AGREGADAS_FPCP" type="checkbox" value="" onchange="Form_PLAN_CUENTAS_PRESUPUESTARIAS__BuscarListado();">&nbsp;Todas&nbsp;las&nbsp;cuentas<br>-->
									<INPUT id="MOSTRAR_FPCP" type="checkbox" value="" checked="true" onchange="Form_PLAN_CUENTAS_PRESUPUESTARIAS__BuscarListado();">&nbsp;Cargar&nbsp;solo&nbsp;50&nbsp;cuentas
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
	Form_PLAN_CUENTAS_PRESUPUESTARIAS__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FPCP"), true);
	window.onload=function(){		
		Form_PLAN_CUENTAS_PRESUPUESTARIAS__Nuevo();
	}	
</script>
