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
				<BUTTON class="BotonesVentana" onClick="Form_BENEFICIARIO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FB"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FB" class="BotonesVentana" onclick="Form_BENEFICIARIO__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FB"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FB" class="BotonesVentana" onclick="Form_BENEFICIARIO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FB"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FB" class="BotonesVentana" onclick="Form_BENEFICIARIO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FB"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_VISUALIZAR_FB" class="BotonesVentana" onclick="Form_BENEFICIARIO__Visualizar();">
		      <IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_VISUALIZAR_FB"><br>Visualizar
		    </BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FB">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 290px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FB" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_BENEFICIARIO" name="FORMULARIO_BENEFICIARIO">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>C&eacute;dula</td>
									<td class='TextCampos'>
										<select id="NACIONALIDAD_FB" class='TextoCampoInput'>
											<option value="V" title="VENEZOLANO">V</option>
											<option value="E" title="EXTRANJERO">E</option>
											<option value="P" title="PASAPORTE">P</option>
											<option value="" title="SIN NÚMERO">S/N</option>
										</select>
										<INPUT id="CEDULA_FB" class='TextoCampoInput' type='text' size='15' onkeypress="return soloNum(event)" />
										<BUTTON id="BOTON_BUSCAR_PERSONA_FB" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_BENEFICIARIO__Buscar()">
											Buscar - CNE
										</BUTTON>
									</td>
								</tr>								
								<tr>
									<td class='TitulosCampos'>Primer Nombre</td>
									<td class='TextCampos'><INPUT id="PRIMER_NOMBRE_FB" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Segundo Nombre</td>
									<td class='TextCampos'><INPUT id="SEGUNDO_NOMBRE_FB" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Primer Apellido</td>
									<td class='TextCampos'><INPUT id="PRIMER_APELLIDO_FB" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Segundo Apellido</td>
									<td class='TextCampos'><INPUT id="SEGUNDO_APELLIDO_FB" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Teléfono</td>
									<td class='TextCampos'><INPUT id="TELEFONO_FB" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Correo</td>
									<td class='TextCampos'><INPUT id="CORREO_FB" class='TextoCampoInput' type='text' size='30' style="text-transform: none;"></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Cuenta Bancaria - Principal</td>
									<td class='TextCampos'><INPUT id="CUENTA_BANCARIA_1_FB" class='TextoCampoInput' type='text' size='30' maxlength="50" style="text-transform: none;"></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Alternativa</td>
									<td class='TextCampos'><INPUT id="CUENTA_BANCARIA_2_FB" class='TextoCampoInput' type='text' size='30' maxlength="50" style="text-transform: none;"></td>
								</tr>
							</tbody>
							</table>

						</FORM>
					</div>					
					<!-- ************************ ******************** fin ******************* *************************-->		
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 290px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FB_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">	
									<td width="20%">C&Eacute;DULA</td>
									<td width="80%">NOMBRES APELLIDOS</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 60%;">
							<table id="TABLA_LISTA_FB" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<DIV class='TitulosCampos' style="text-align : center; vertical-align: middle;">
							Buscar&nbsp;
							<INPUT id="LISTADO_BUSCAR_FB" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_BENEFICIARIO__BuscarListado();">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_BENEFICIARIO__LimpiarInputTextBuscarListado();">
								<IMG id="IMG_LIMPIAR_FB" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
							</BUTTON>
							Registros a Mostrar
							<select id="LISTADO_MOSTRAR_FB" class='TextoCampoInput' onchange="Form_BENEFICIARIO__BuscarListado()">
								<option value="50">50</option>
								<option value="100">100</option>
								<option value="500" selected="true">500</option>
								<option value="ALL">TODOS</option>
							</select>
						</DIV>
					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>	
	Form_BENEFICIARIO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FB"), true);
	window.onload=function(){		
		Form_BENEFICIARIO__Nuevo();
		Form_BENEFICIARIO__Mensaje("Permite la definición o actualización de los datos b&aacute;sicos de los beneficiarios.");
	}	
</script>