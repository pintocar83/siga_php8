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
				<BUTTON class="BotonesVentana" onclick="Form_REQUISICION__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FR"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FR" class="BotonesVentana"  onclick="Form_REQUISICION__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FR"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FR" class="BotonesVentana" onclick="Form_REQUISICION__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FR"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FR" class="BotonesVentana" onclick="Form_REQUISICION__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FR"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FR" class="BotonesVentana" onclick="Form_REQUISICION__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FR"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FR">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 340px;">
						<h2 id="abcd" class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FR" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_REQUISICION" name="FORMULARIO_REQUISICION">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Requisici&oacute;n</td>
									<td class='TextCampos'>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td>
														<select class='TextoCampoInput' id="TIPO_FR" onchange="Form_REQUISICION__Correlativo();">
															<option value="OC">BIENES, MATERIALES Y SUMINISTROS</option>
															<option value="OS">SERVICIOS</option>
														</select>
													</td>													
													<td id="EMITIDO_FR" style="text-align: right;">Correlativo&nbsp;&nbsp;<INPUT id='CODIGO_FR' class='TextoCampoInputDesactivado' type='text' size='12' value="" readonly="true"></td>
												</tr>
											</tbody>
										</table>
									</td>

								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos'>
										<INPUT id='FECHA_FR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Estructura presupuestaria</td>
									<td class='TextCampos'>
										<SELECT class="TextoCampoInput" id="AC_FR" onchange="Form_REQUISICION__id_accion_centralizada='';Form_REQUISICION__id_accion_especifica='';Form_REQUISICION__id_accion_subespecifica='';Form_REQUISICION__id_fuente_recursos='';Form_REQUISICION__CargarAE();"></SELECT>
										<SELECT class="TextoCampoInput" id="AE_FR" onchange="Form_REQUISICION__id_accion_centralizada='';Form_REQUISICION__id_accion_especifica='';Form_REQUISICION__id_accion_subespecifica='';Form_REQUISICION__CargarOAE();"></SELECT>
										<SELECT class="TextoCampoInput" id="OAE_FR"></SELECT>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Concepto</td>
									<td class='TextCampos'>
										<textarea id="CONCEPTO_FR" class='TextoCampoInput' cols="60" rows="2" style="resize: none;"></textarea>
									</td>
								</tr>
							</tbody>
							</table>

							<DIV style="height : 3px;"></DIV>
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td width="10%">COD.</td>
										<td width="50%">DENOMINACI&Oacute;N</td>
										<td width="15%">CANTIDAD</td>
										<td width="25%" id="CABECERA_UM_FR">PRESENTACI&Oacute;N</td>
									</tr>
								</tbody>
							</table>
							<DIV class="AreaTablaListado" style="height:120px;">
								<table id="TABLA_LISTA_ARTICULOS_FR" border="0" cellspacing="0" cellpadding="0" width="100%">
								</table>
							</DIV>
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
								<td align="left">
									<BUTTON id="BOTON_AGREGAR_FR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_REQUISICION__Agregar()">
										<IMG id="IMG_AGREGAR_FR" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
									</BUTTON>
									<BUTTON id="BOTON_QUITAR_FR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_REQUISICION__Quitar();">
										<IMG id="IMG_QUITAR_FR" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
									</BUTTON>
								</td>
								</tr>
							</tbody>
							</table>
							<!--Contiene los datos a ser agregados al listado de articulos de la requisicion-->
							<INPUT type="hidden" value="" id="ID_ITEM_AGREGAR_FR">
							<INPUT type="hidden" value="" id="COD_ITEM_AGREGAR_FR">
							<INPUT type="hidden" value="" id="DEM_ITEM_AGREGAR_FR">
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 340px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FR_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<div style="margin-bottom: 5px;">
							Requisición
							<select class='TextoCampoInput' id="ID_ITEM_TIPO_2_FR" onchange="Form_REQUISICION__CambioSelect2()">
								<option value="OC">BIENES, MATERIALES Y SUMINISTROS</option>
								<option value="OS">SERVICIOS</option>
							</select>
						</div>							
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="12%">C&Oacute;DIGO</td>
									<td width="11%">FECHA</td>
									<td width="20%">ESTRUC. PRESUP.</td>
									<td width="57%">CONCEPTO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 190px;">
							<table id="TABLA_LISTA_FR" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<table width="100%" align="center">
						<tbody>
							<tr>
							<td valign="top">
								<DIV class='TitulosCampos' style="text-align : center;">							
									<table align="center">
									<tbody>
										<tr>
										<td>Buscar&nbsp;</td>
										<td style="white-space : nowrap; text-align: left;">
											<INPUT id="LISTADO_BUSCAR_FR" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_REQUISICION__PresionarEnter(event);">
											<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_REQUISICION__LimpiarInputTextBuscarListado();">
												<IMG id="IMG_LIMPIAR_FR" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
											</BUTTON>
										</td>
										</tr>
										<tr>
										<td>Mostrar&nbsp;</td>
										<td style="text-align: left;">
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FR" onchange="Form_REQUISICION__BuscarListado_CadenaBuscar=''; Form_REQUISICION__BuscarListado();">
	<OPTION value="">TODO</OPTION>
	<OPTION value="01" <?php if(date("m")=="01")echo "selected";?>>ENERO</OPTION>
	<OPTION value="02" <?php if(date("m")=="02")echo "selected";?>>FEBRERO</OPTION>
	<OPTION value="03" <?php if(date("m")=="03")echo "selected";?>>MARZO</OPTION>
	<OPTION value="04" <?php if(date("m")=="04")echo "selected";?>>ABRIL</OPTION>
	<OPTION value="05" <?php if(date("m")=="05")echo "selected";?>>MAYO</OPTION>
	<OPTION value="06" <?php if(date("m")=="06")echo "selected";?>>JUNIO</OPTION>
	<OPTION value="07" <?php if(date("m")=="07")echo "selected";?>>JULIO</OPTION>
	<OPTION value="08" <?php if(date("m")=="08")echo "selected";?>>AGOSTO</OPTION>
	<OPTION value="09" <?php if(date("m")=="09")echo "selected";?>>SEPTIEMBRE</OPTION>
	<OPTION value="10" <?php if(date("m")=="10")echo "selected";?>>OCTUBRE</OPTION>
	<OPTION value="11" <?php if(date("m")=="11")echo "selected";?>>NOVIEMBRE</OPTION>
	<OPTION value="12" <?php if(date("m")=="12")echo "selected";?>>DICIEMBRE</OPTION>
</SELECT>
										</td>
										</tr>
									</tbody>
									</table>
								</DIV>
							</td>
							<td valign="top">
								<INPUT id="SOMBRA_CHECKBOX_FR" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FR" type="checkbox" checked="true">Solo buscar al presionar enter
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
	Form_REQUISICION__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FR"), true);
	window.onload=function(){
		Form_REQUISICION__CargarUnidadMedida();
		Form_REQUISICION__Nuevo();
	}
</script>
