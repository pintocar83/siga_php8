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
* @version 20091001
*/
-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<tbody>
		<tr>
			<TD  class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_COMPROBANTE_RETENCION__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FCR"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FCR" class="BotonesVentana" onclick="Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar='';Form_COMPROBANTE_RETENCION__BuscarListado();">
					<IMG src="../../image/icon/icon-reload.png" width="22" height="22" border="0" id="IMG_GUARDAR_FCR"><br>Recargar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_FCR" class="BotonesVentana" onclick="Form_COMPROBANTE_RETENCION__Imprimir();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_FCR"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top"  class="formulario">
				<div class="tab-pane" id="TABPANE_FCR">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 390px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FCR" class="MensajesPestanas">&nbsp;</DIV>
						<table>
							<TR>
								<TD>
								Comprobante de retenciones
<!--								datos segun tabla modulo_base.retencion_tipo   cargar datos desde la BD-->
								<SELECT class="TextoCampoInput" id="SELECT_TIPO_COMPROBANTE_RETENCION_FCR" onchange="Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar='';Form_COMPROBANTE_RETENCION__BuscarListado();">
									<OPTION value="1">I.V.A.</OPTION>
									<OPTION value="2">I.S.L.R.</OPTION>
									<OPTION value="3">1x1000</OPTION>
								</SELECT>
								</TD>
								<TD>
									<DIV id="MSG_FCR" class="MensajesPestanas">&nbsp;</DIV>
								</TD>
							</TR>
						</table>
						<FORM id="FORMULARIO_COMPROBANTE_RETENCION" name="FORMULARIO_COMPROBANTE_RETENCION">
							<table cellspacing='5px' align="center" border="0" width="100%">
							<tbody>
								<tr>
									<TD colspan="2">
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tbody>
											<tr class="CabeceraTablaEstilo">
												<td width="25%">COMPROBANTE</td>
												<td width="15%">FECHA</td>
												<td width="60%">NOMBRE</td>
											</tr>
										</tbody>
									</table>
									<DIV class="AreaTablaListado" style="height : 145px;">
										<table id="TABLA_LISTA_FCR" border="0" cellspacing="0" cellpadding="0" width="100%">
										</table>
									</DIV>
									<table border="0" cellspacing="0" cellpadding="2" width="100%">
										<tbody>
											<tr class="CabeceraTablaEstilo">
												<td style="text-align: left;">&nbsp;&nbsp;&nbsp;Mostrar&nbsp;
<SELECT class="TextoCampoInput" id="MES_FILTRAR_FCR" onchange="Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar='';Form_COMPROBANTE_RETENCION__BuscarListado()">
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
												&nbsp;&nbsp;&nbsp;&nbsp;Buscar&nbsp;<INPUT id='BUSCAR_FCR' class='TextoCampoInput' type='text' size='45' value="" style="width:430px;" onkeyup="Form_COMPROBANTE_RETENCION__PresionarEnter(event);">
												</td>
											</tr>
										</tbody>
									</table>
									<br>
									Facturas asociadas
									<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px;">
										<tbody>
											<tr class="CabeceraTablaEstilo">
												<td width="10%">Fecha</td>
												<td width="20%">RIF/CI</td>
												<td>Nombre</td>
												<td width="10%">Nº Factura</td>
												<td width="10%">Nº Control</td>
												<td width="10%">Total</td>
											</tr>
										</tbody>
									</table>
									<DIV class="AreaTablaListado" style="height : 70px;">
										<table id="TABLA_LISTA_FACTURA_FCR" border="0" cellspacing="0" cellpadding="0" width="100%">
										</table>
									</DIV>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tbody>
											<tr class="CabeceraTablaEstilo">
												<td align="left" width="1px">
												<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_COMPROBANTE_RETENCION__AgregarFacturaAsociada()" type="BUTTON">
													<table><TR><TD><IMG id="IMG_AGREGAR_FCR" src='../../image/icon/icon-listadd-sigafs.png' width='16' height='16' style="vertical-align : middle;"></TD><TD>Agregar</TD></TR></table>
												</BUTTON>												
												<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_COMPROBANTE_RETENCION__QuitarFacturaAsociada()" type="BUTTON">
													<table><TR><TD><IMG id="IMG_QUITAR_FCR" src='../../image/icon/icon-listremove-sigafs.png' width='16' height='16' style="vertical-align : middle;"></TD><TD>Quitar&nbsp;&nbsp;&nbsp;</TD></TR></table>
												</BUTTON>												
												</td>
											</td>
										</tbody>
									</table>
									</TD>
								</tr>
							</tbody>
							</table>
						</FORM>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	window.onload=function(){
		Form_COMPROBANTE_RETENCION__BuscarListado();
	}
</script>
