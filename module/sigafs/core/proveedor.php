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
				<BUTTON class="BotonesVentana" onClick="Form_PROVEEDOR__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FP"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FP" class="BotonesVentana" onclick="Form_PROVEEDOR__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FP"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FP" class="BotonesVentana" onclick="Form_PROVEEDOR__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FP"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FP" class="BotonesVentana" onclick="Form_PROVEEDOR__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FP"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_VISUALIZAR_FP" class="BotonesVentana" onclick="Form_PROVEEDOR__Visualizar();">
		      <IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_VISUALIZAR_FP"><br>Visualizar
		    </BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FP">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 290px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FP" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_PROVEEDOR" name="FORMULARIO_PROVEEDOR">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>RIF</td>
									<td class='TextCampos'>
										<select id="RIF_TIPO_FP" class='TextoCampoInput'>
											<option value="J" title="JURÍDICO">J</option>
											<option value="V" title="FIRMA PERSONAL">V</option>
											<option value="G" title="GOBIERNO">G</option>
											<!--<option value="" title="SIN NÚMERO">S/N</option>-->
										</select>
										<INPUT id="RIF_NUMERO_FP" class='TextoCampoInput' type='text' size='15' onkeypress="return soloNum(event)" /></td>
								</tr>								
								<tr>
									<td class='TitulosCampos'>Denominación</td>
									<td class='TextCampos'><INPUT id="DENOMINACION_FP" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Teléfono</td>
									<td class='TextCampos'><INPUT id="TELEFONO_FP" class='TextoCampoInput' type='text' size='30'></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Correo</td>
									<td class='TextCampos'><INPUT id="CORREO_FP" class='TextoCampoInput' type='text' size='30' style="text-transform: none;"></td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Dirección</td>
									<td class='TextCampos'>
										<textarea id="DIRECCION_FP" class='TextoCampoInput' cols="40" rows="4" style="resize: none;"></textarea>
									</td>
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
						<DIV id="MSG_FP_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">	
									<td width="20%">RIF</td>
									<td width="80%">DENOMINACIÓN</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 60%;">
							<table id="TABLA_LISTA_FP" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<DIV class='TitulosCampos' style="text-align : center; vertical-align: middle;">
							Buscar&nbsp;
							<INPUT id="LISTADO_BUSCAR_FP" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_PROVEEDOR__BuscarListado();">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_PROVEEDOR__LimpiarInputTextBuscarListado();">
								<IMG id="IMG_LIMPIAR_FP" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
							</BUTTON>
							Registros a Mostrar
							<select id="LISTADO_MOSTRAR_FP" class='TextoCampoInput' onchange="Form_PROVEEDOR__BuscarListado()">
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
	Form_PROVEEDOR__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FP"), true);
	window.onload=function(){		
		Form_PROVEEDOR__Nuevo();
		Form_PROVEEDOR__Mensaje("Permite la definición o actualización de los datos b&aacute;sicos de los beneficiarios.");
	}	
</script>