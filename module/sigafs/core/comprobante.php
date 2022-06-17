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
* @version 20091009
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<body>
		<tr>
			<TD  class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_COMPROBANTE_GASTO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FCG"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FCG" class="BotonesVentana" onclick="Form_COMPROBANTE_GASTO__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FCG"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FCG" class="BotonesVentana"  onclick="Form_COMPROBANTE_GASTO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FCG"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FCG" class="BotonesVentana" onclick="Form_COMPROBANTE_GASTO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FCG"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FCG" class="BotonesVentana" onclick="Form_COMPROBANTE_GASTO__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FCG"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FCG">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCG" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FCG">


							<table cellspacing='3' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Comprobante</td>
									<td class='TextCampos'>
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td>
												<INPUT id="ACRONIMO_FCG" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='2'><INPUT id="COMPROBANTE_FCG" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='15'>
											</td>
											<td align="right">
												<span id="COMPROBANTE_TIPO_FCG" style="font-weight: bold;"></span>
											</td>
											</tr>
										</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos'>
										<INPUT id='FECHA_FCG' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FCG" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Denominaci&oacute;n</td>
									<td class='TextCampos'>
										<textarea id="DENOMINACION_FCG" class='TextoCampoInput' rows="2" cols="75" style="resize: none;"></textarea>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'></td>
									<td class='TextCampos'>
										<BUTTON id="BOTON_PROVEEDOR_FCG" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_COMPROBANTE_GASTO__BotonProveedor();" type="BUTTON">
											Proveedor
										</BUTTON>
										<BUTTON id="BOTON_BENEFICIARIO_FCG" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" onclick="Form_COMPROBANTE_GASTO__BotonBeneficiario();" type="BUTTON">
											Beneficiario
										</BUTTON>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos' id="TIPO_PERSONA_FCG"></td>
									<td class='TextCampos'>
										<INPUT id='ID_BENEFICIARIO_PROVEEDOR_FCG' class='TextoCampoInputDesactivado' type='text' size='20' maxlength='15' value="" readonly="true"><INPUT id='NOMBRE_BENEFICIARIO_PROVEEDOR_FCG' class='TextoCampoInputDesactivado' readonly="true" type='text' size='50' value=""><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png'  width='18' height='18'><IMG id="IMG_LIMPIAR_PB_FCG" class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png'  width='18' height='18'>
										<INPUT type="hidden" id="ID_BoP_FCG" value="" />
										<input type="hidden" id="CUENTA_CONTABLE_PB_FCG" value="" />
									</td>
								</tr>
							</tbody>
							</table>
						<br>

						<div class="tab-pane" id="SUB_TABPANE_FCG">
							<!-- ******************** ******************** Primera SUBPestaña ****************** *********************-->
							<div class="tab-page"  style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FCG_DP">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles presupuestarios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="15%">ESTRUC.&nbsp;PRESUP.</td>
											<td width="10%">CUENTA</td>
											<td>DESCRIPCI&Oacute;N</td>
											<td width="10%">OPERACIÓN</td>
											<td width="10%">MONTO</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 130px; overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FCG_DP" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FCG_DP">
										<BUTTON id="BOTON_AGREGAR_FCG_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_COMPROBANTE_GASTO__AgregarDP()">
											<IMG id="IMG_AGREGAR_FCG_DP" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FCG_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_COMPROBANTE_GASTO__DP_Quitar()">
											<IMG id="IMG_QUITAR_FCG_DP" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<input class='BotonesParaCampos' type="button" value="txt" title="Ingresar detalles a través de texto plano." style="vertical-align: middle; text-align : center; font-size: 9px;" onclick="Form_COMPROBANTE_GASTO__inputText()"/>
										<INPUT id='TOTAL_COMPROMISOS_FCG_DP' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width : 150px; vertical-align: middle;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************ ******************** fin ******************* *************************-->
							<!-- ******************** ******************** Segunda SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 170px;">
								<h2 class="tab" id="SUB_TABPANE_FCG_DC">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles contables&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</h2>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tbody>
										<tr class="CabeceraTablaEstilo" style="font-size : 11px;">
											<td width="15%">CUENTA</td>
											<td>DESCRIPCI&Oacute;N</td>
											<td width="15%">DEBE</td>
											<td width="15%">HABER</td>
										</tr>
									</tbody>
								</table>
								<DIV class="AreaTablaListado" style="height : 130px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FCG_DC" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FCG_DC">
										<BUTTON id="BOTON_AGREGAR_FCG_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_COMPROBANTE_GASTO__AgregarDC()">
											<IMG id="IMG_AGREGAR_FCG_DC" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FCG_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_COMPROBANTE_GASTO__DC_Quitar()">
											<IMG id="IMG_QUITAR_FCG_DC" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<INPUT id='TOTAL_DEBE_FCG_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
										<INPUT id='TOTAL_HABER_FCG_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************************ fin ****************** ********************* ***************-->
						</div>







						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCG_LISTADO" class="MensajesPestanas">&nbsp;</DIV>

						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="13%">COMPROBANTE</td>
									<td width="10%">FECHA</td>
									<td>DENOMINACI&Oacute;N</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 260px;">
							<table id="TABLA_LISTA_FCG" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>

						<br>
						<table width="100%" align="center" border='0'>
						<tbody>
							<tr>
							<td valign="top">
								<table>
									<tr><td colspan="3"><strong>Estado&nbsp;del&nbsp;comprobante</strong></td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #FF5454; width : 5px;'></td><td>Sin contabilizar</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #48DC0E; width : 5px;'></td><td>Contabilizado</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #000000; width : 5px;'></td><td>Anulado</td></tr>
								</table>
							</td>
							<td valign="top">
								<DIV class='TitulosCampos' style="text-align : left; padding-left: 10px;">
									<table align="center" border="0" width="100%">
									<tbody>
										<tr >
											<td>Buscar&nbsp;</td>
											<td style="white-space : nowrap;">
												<INPUT id="LISTADO_BUSCAR_FCG" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_COMPROBANTE_GASTO__PresionarEnter(event);">
												<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_COMPROBANTE_GASTO__LimpiarInputTextBuscarListado();">
													<IMG id="IMG_LIMPIAR_FCG" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
												</BUTTON>
											</td>
										</tr>
										<tr>
											<td >Mostrar&nbsp;</td>
											<td >
												<SELECT class="TextoCampoInput" id="MES_FILTRAR_FCG" onchange="Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar=''; Form_COMPROBANTE_GASTO__BuscarListado();">
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
								<!--<INPUT type="checkbox" id="MOSTRA_CONTABILIZADO_FCG" checked="true" onchange="Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar=''; Form_COMPROBANTE_GASTO__BuscarListado();">Mostrar contabilizados<br>-->
								<INPUT id="SOMBRA_CHECKBOX_FCG" type="checkbox" checked="true">Sombrear al buscar<br>
								<INPUT id="BUSCAR_CHECKBOX_FCG" type="checkbox" checked="true">Solo buscar al presionar enter
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
	Form_COMPROBANTE_GASTO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FCG"), true);
	Form_COMPROBANTE_GASTO__TabPaneSUBTAB = new WebFXTabPane(xGetElementById("SUB_TABPANE_FCG"), true);
	window.onload=function(){
		Form_COMPROBANTE_GASTO__Nuevo();
	}
</script>

