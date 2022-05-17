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
				<BUTTON class="BotonesVentana" onclick="Form_CREDITO_ADICIONAL__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FCA"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FCA" class="BotonesVentana" onclick="Form_CREDITO_ADICIONAL__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FCA"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FCA" class="BotonesVentana"  onclick="Form_CREDITO_ADICIONAL__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FCA"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FCA" class="BotonesVentana" onclick="Form_CREDITO_ADICIONAL__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FCA"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FCA" class="BotonesVentana" onclick="Form_CREDITO_ADICIONAL__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FCA"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FCA">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCA" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FCA">


							<table cellspacing='3' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>Comprobante</td>
									<td class='TextCampos'>
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td>
												<INPUT id="ACRONIMO_FCA" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='2'><INPUT id="COMPROBANTE_FCA" class='TextoCampoInputDesactivado'  readonly="true" type='text' size='15'>
											</td>
											<td align="right">
												<span id="COMPROBANTE_TIPO_FCA" style="font-weight: bold;"></span>
											</td>
											</tr>
										</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha</td>
									<td class='TextCampos' style="white-space: nowrap;">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
											<td>
												<INPUT id='FECHA_FCA' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><IMG id="IMG_FECHA_FCA" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
											</td>
											<td align="right">
												&nbsp;Tipo&nbsp;<SELECT id="TIPO_FCA" class="TextoCampoInput"><OPTION value="CO" selected="selected" id="TIPO_FCA_0">GASTOS CORRIENTES</OPTION><OPTION value="CA" id="TIPO_FCA_1">GASTOS DE CAPITAL</OPTION></SELECT>
											</td>
											<td align="right">
												&nbsp;Fuente&nbsp;Financiera&nbsp;<SELECT id="FUENTE_FINANCIERA_FCA" class="TextoCampoInput" style="max-width: 200px;"></SELECT>
											</td>
											</tr>
										</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Denominaci&oacute;n</td>
									<td class='TextCampos'>
										<textarea id="DENOMINACION_FCA" class='TextoCampoInput' rows="2" cols="75" style="resize: none; width: 100%;"></textarea>
									</td>
								</tr>
								
								
							</tbody>
							</table>
						<br>

						<div class="tab-pane" id="SUB_TABPANE_FCA">
							<!-- ******************** ******************** SUBPestaña ****************** *********************-->
							<div class="tab-page"  style="height : 215px;">
								<h2 class="tab" id="SUB_TABPANE_FCA_AU">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cuentas a aumentar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
								<DIV class="AreaTablaListado" style="height : 170px; overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FCA_AU" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FCA_AU">
										<BUTTON id="BOTON_AGREGAR_FCA_AU" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CREDITO_ADICIONAL__AgregarAU()">
											<IMG id="IMG_AGREGAR_FCA_AU" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FCA_AU" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CREDITO_ADICIONAL__DP_Quitar()">
											<IMG id="IMG_QUITAR_FCA_AU" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<INPUT id='TOTAL_COMPROMISOS_FCA_AU' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width : 150px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************ ******************** fin ******************* *************************-->
						</div>







						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 410px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lista&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCA_LISTADO" class="MensajesPestanas">&nbsp;</DIV>

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
						<DIV class="AreaTablaListado" style="height : 270px;">
							<table id="TABLA_LISTA_FCA" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>

						<br>
						<DIV class='TitulosCampos' style="text-align : center;">
							<table cellspacing="3" cellpadding="0" align="center" border="0" width="100%">
							<tbody>
								<tr >
								<td class='TitulosCampos'>Buscar&nbsp;</td>
								<td class='TextCampos'>
									<INPUT id="LISTADO_BUSCAR_FCA" class='TextoCampoInput' type="text" value="" size="35" onkeyup="Form_CREDITO_ADICIONAL__PresionarEnter(event);">
									<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_CREDITO_ADICIONAL__LimpiarInputTextBuscarListado();">
										<IMG id="IMG_LIMPIAR_FCA" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
									</BUTTON>
								</td>
								<td valign="top" rowspan="2" class='TextCampos'>
									<INPUT type="checkbox" id="MOSTRA_CONTABILIZADO_FCA" checked="true" onchange="Form_CREDITO_ADICIONAL__BuscarListado_CadenaBuscar=''; Form_CREDITO_ADICIONAL__BuscarListado();">Mostrar contabilizados<br>
									<INPUT id="SOMBRA_CHECKBOX_FCA" type="checkbox" checked="true">Sombrear al buscar<br>
									<INPUT id="BUSCAR_CHECKBOX_FCA" type="checkbox" checked="true">Solo buscar al presionar enter
								</td>
								</tr>
								<tr>
								<td  class='TitulosCampos'>Mostrar&nbsp;</td>
								<td class='TextCampos'>
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FCA" onchange="Form_CREDITO_ADICIONAL__BuscarListado_CadenaBuscar=''; Form_CREDITO_ADICIONAL__BuscarListado();">
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
					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	Form_CREDITO_ADICIONAL__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FCA"), true);
	Form_CREDITO_ADICIONAL__TabPaneSUBTAB = new WebFXTabPane(xGetElementById("SUB_TABPANE_FCA"), true);
	window.onload=function(){
		Form_CREDITO_ADICIONAL__Nuevo();
	}
</script>

