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
				<BUTTON id="BOTON_GUARDAR_FCG" class="BotonesVentana" onclick="Form_CONVERTIDOR_GENERAL__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FCG"><br>Guardar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FCG">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 405px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>						
						<DIV id="MSG_FCG" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<DIV>
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td width="50%" colspan="2" style="background-color : #003B51;">PRESUPUESTO</td>
										<td width="50%" colspan="2" style="background-color : #002634;">CONTABILIDAD</td>
									</tr>
									<tr class="CabeceraTablaEstilo">
										<td width="10%" style="background-color : #003E55;">C&Oacute;DIGO</td>
										<td width="40%" style="background-color : #003E55;">DENOMINACI&Oacute;N</td>
										<td width="10%" style="background-color : #002C3C;">C&Oacute;DIGO</td>
										<td width="40%" style="background-color : #002C3C;">DENOMINACI&Oacute;N</td>
									</tr>
								</tbody>
							</table>
							<DIV class="AreaTablaListado" style="height : 290px; overflow-x: hidden;">
								<table id="TABLA_LISTA_FCG" border="0" cellspacing="0" cellpadding="0" width="100%">
								</table>
							</DIV>
			
							<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td width="50%" align="left" style="background-color : #003E55;">
										<INPUT id='CODIGO_PRESUPUESTARIO_FCG' class='TextoCampoInputDesactivado' type='text' size='18' value="" readonly="true" style="width: 100px;"><INPUT class='TextoCampoInputDesactivado' type="text" value="" readonly="true" id="DENOMINACION_CTA_PRESUPUESTARIA_FCG" style="width: 270px;"><IMG id="IMG_BUSCAR_CODIGO_PRESUPUESTARIO_FCG" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png'  width='18' height='18' onclick="Form_CONVERTIDOR_GENERAL__BuscarPresupuestaria();">
										</td>
										<td width="50%" align="left" style="background-color : #002C3C;">
										<INPUT id='CODIGO_CONTABLE_FCG' class='TextoCampoInputDesactivado' type='text' size='18' value="" readonly="true" style="width: 100px;"><INPUT class='TextoCampoInputDesactivado' type="text" value="" readonly="true" id="DENOMINACION_CTA_CONTABLE_FCG" style="width: 270px;"><IMG id="IMG_BUSCAR_CODIGO_CONTABLE_FCG" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18' onclick="Form_CONVERTIDOR_GENERAL__BuscarContable();">
										</td>
									</tr>
									<tr class="CabeceraTablaEstilo">
									<td align="left" colspan="2">
										<!--<BUTTON id="BOTON_AGREGAR_FCG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onmouseover="IMG_AGREGAR_FCG.src='../img/iconos/agregar_con_foco.png';" onmouseout="IMG_AGREGAR_FCG.src='../../image/icon/icon-listadd-sigafs.png'" onclick="Form_CONVERTIDOR_GENERAL__Agregar();" type="BUTTON"><IMG id="IMG_AGREGAR_FCG" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar</BUTTON>-->
										<BUTTON id="BOTON_QUITAR_FCG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CONVERTIDOR_GENERAL__Quitar();"><IMG id="IMG_QUITAR_FCG" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;</BUTTON>
										<BUTTON id="BOTON_QUITAR_NS_FCG" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_CONVERTIDOR_GENERAL__QuitarNoAsociados();"><IMG id="IMG_QUITAR_NS_FCG" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar no asociados para guardar&nbsp;</BUTTON>
									</td>
									</tr>
								</tbody>
							</table>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->		
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	Form_CONVERTIDOR_GENERAL__TabPane= new WebFXTabPane(xGetElementById("TABPANE_FCG"), true);
	window.onload=function(){
		Form_CONVERTIDOR_GENERAL__TabPane.setSelectedIndex(0);
		Form_CONVERTIDOR_GENERAL__Inicializar();
	}	
</script>
