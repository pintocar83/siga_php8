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
				<BUTTON class="BotonesVentana" onClick="Form_DEFINICIONES_CUENTAS_BANCARIAS__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FDCB"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FDCB" class="BotonesVentana" onclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__GuardarVerificar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FDCB"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FDCB" class="BotonesVentana" onclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FDCB"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FDCB" class="BotonesVentana" onclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FDCB"><br>Eliminar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FDCB">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 320px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FDCB" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FDCB">
							<table cellspacing='5px' align="center">
							<tbody>
								<tr>
									<td class='TitulosCampos'>N&uacute;mero de cuenta</td>
									<td class='TextCampos'>
										<INPUT id='NUMERO_CUENTA_FDCB' class='TextoCampoInput' type='text' size='25' maxlength='20' value="">
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Descripci&oacute;n</td>
									<td class='TextCampos'>
										<textarea id='DESCRIPCION_FDCB'  class='TextoCampoInput' cols="40" rows="2" style="width: 100%;"></textarea>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Banco</td>
									<td class='TextCampos'>										
										<INPUT id='CODIGO_BANCO_FDCB' class='TextoCampoInput' type='text' size='6' value=""><INPUT id='BANCO_FDCB' class='TextoCampoInputDesactivado' type='text' size='52' value="" readonly="true"><IMG id="IMG_BUSCAR_CODIGO_BANCO_FDCB" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><!--<IMG id="IMG_AGREGAR_CODIGO_BANCO_FDCB" class='BotonesParaCampos' src='../img/iconos/agregar_activo.png' width='18' height='18'>-->
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Tipo de cuenta</td>
									<td class='TextCampos'>
										<SELECT id="TIPO_CUENTA_FDCB" class='TextoCampoInput'></SELECT><!--<IMG id="IMG_AGREGAR_TIPO_CUENTA_FDCB" class='BotonesParaCampos' src='../img/iconos/agregar_activo.png' width='18' height='18'>-->
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>C&oacute;digo contable</td>
									<td class='TextCampos'>
										<INPUT id='CODIGO_CONTABLE_FDCB' class='TextoCampoInput' type='text' size='18' maxlength='16' value=""><INPUT id='NOMBRE_CODIGO_CONTABLE_FDCB' type='text' size='40' value="" readonly="true" class="TextoCampoInputDesactivado"><IMG id="IMG_BUSCAR_CODIGO_CONTABLE_FDCB" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha apertura</td>
									<td class='TextCampos'>
										<INPUT id='FECHA_APERTURA_FDCB' class='TextoCampoInput' type='text' size='11' maxlength='10' value="" ondblclick="showCalendar('FECHA_APERTURA_FDCB','%d/%m/%Y');"><IMG id="IMG_FECHA_APERTURA_FDCB" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Fecha cierre</td>
									<td class='TextCampos'>
										<INPUT id='FECHA_CIERRE_FDCB' class='TextoCampoInput' type='text' size='11' maxlength='10' value=""><IMG id="IMG_FECHA_CIERRE_FDCB" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									</td>
								</tr>
								<tr>
									<td class='TitulosCampos'>Estado</td>
									<td class='TextCampos'>
										<INPUT id="ESTADO_FDCB" name="ESTADO_FDCB" type="radio" checked value="true">ACTIVA
										&nbsp;
										<INPUT id="ESTADO_FDCB" name="ESTADO_FDCB" type="radio" value="false">INACTIVA
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
						<DIV id="MSG_FDCB_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">	
									<td width="5%">COD</td>						
									<td width="23%">BANCO</td>
									<td width="20%">Nº CUENTA</td>
									<td width="15%">TIPO CUENTA</td>
									<td width="30%">DESCRIPCI&Oacute;N</td>
									<td width="7%">ESTADO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 60%;">
							<table id="TABLA_LISTA_FDCB" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>
						<br>
						<DIV class='TitulosCampos' style="text-align : center;">
							Buscar&nbsp;
							<INPUT id="LISTADO_BUSCAR_FDCB" class='TextoCampoInput' type="text" value="" size="50" onkeyup="Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado();">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__LimpiarInputTextBuscarListado();">
								<IMG id="IMG_LIMPIAR_FDCB" src='../../image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
							</BUTTON>							
						</DIV>
					</div>					
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	Form_DEFINICIONES_CUENTAS_BANCARIAS__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FDCB"), true);
	window.onload=function(){		
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Nuevo();
	}	
</script>