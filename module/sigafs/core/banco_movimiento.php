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
<?php
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
	<body>
		<tr>
			<TD class="acciones">
				<BUTTON class="BotonesVentana" onclick="Form_BANCO_MOVIMIENTO__Nuevo();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO_FBM"><br>Nuevo
				</BUTTON>
				<BUTTON id="BOTON_GUARDAR_FBM" class="BotonesVentana" onclick="Form_BANCO_MOVIMIENTO__Guardar();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR_FBM"><br>Guardar
				</BUTTON>
				<BUTTON id="BOTON_MODIFICAR_FBM" class="BotonesVentana" onclick="Form_BANCO_MOVIMIENTO__Modificar();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR_FBM"><br>Modificar
				</BUTTON>
				<BUTTON id="BOTON_ELIMINAR_FBM" class="BotonesVentana" onclick="Form_BANCO_MOVIMIENTO__Eliminar();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR_FBM"><br>Eliminar
				</BUTTON>
				<BUTTON id="BOTON_IMPRIMIR_ODC" class="BotonesVentana" onclick="Form_BANCO_MOVIMIENTO__Visualizar();">
					<IMG src="../../image/icon/icon-display.png" width="22" height="22" border="0" id="IMG_IMPRIMIR_ODC"><br>Visualizar
				</BUTTON>
			</TD>
		</tr>
		<tr>
			<td valign="top" class="formulario">
				<div class="tab-pane" id="TABPANE_FBM">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 420px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FBM" class="MensajesPestanas">&nbsp;</DIV>
						<br>
						<FORM id="FORMULARIO_FBM">



<table cellspacing='3px' align="center" border="0">
<tbody>
	<tr>
		<td class='TitulosCampos'>Fecha</td>
		<td class='TextCampos' colspan="3">
			<INPUT id='FECHA_FBM' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/").SIGA::data()?>"><IMG id="IMG_FECHA_FBM" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18'>
			<span id="NUMERO_COMPROBANTE_FBM" style="font-size: 11px; padding-left: 15px;"></span>
		</td>
	</tr>
	<tr>
		<td class='TitulosCampos'>Operaci&oacute;n</td>
		<td class='TextCampos'><SELECT id="TIPO_OPERACION_FBM" class='TextoCampoInput' onchange="Form_BANCO_MOVIMIENTO__ChangeOperacion()" style="width: 300px; margin-right: 10px;"></SELECT></td>
		<td class='TextCampos' id="TD0_CTA_2" style="vertical-align : bottom;  visibility : hidden;">Cuenta a transferir</td>
	</tr>
	
	<tr>
		<td class='TitulosCampos'>Cuenta</td>
		<td class='TextCampos'>
			<INPUT id="ID_CTA_FBM" class='TextoCampoInput' type="hidden" value="" size="5">
			<INPUT id="NCTA_FBM" class='TextoCampoInputDesactivado' type='text' size='25' value=""  readonly="true" style="width: 280px;"><IMG id="IMG_BUSCAR_NCTA_FBM" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><br>
			<INPUT id="DESCRIPCION_NCTA_FBM" class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 300px; font-size: 10px; height: auto !important;">
			<INPUT id="CTA_CODIGO_CONTABLE_FBM" type="hidden" value="">
		</td>
		<td class='TextCampos' id="TD1_CTA_2" style="visibility : hidden;">
			<INPUT id="ID_CTA_FBM2" class='TextoCampoInput' type="hidden" value="" size="5">
			<INPUT id="NCTA_FBM2" class='TextoCampoInputDesactivado' type='text' size='25' value="" readonly="true" style="width: 200px;"><IMG id="IMG_BUSCAR_NCTA_FBM2" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><br>
			<INPUT id="DESCRIPCION_NCTA_FBM2" class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 220px; font-size: 10px; height: auto !important;">
		</td>
	</tr>
	<tr style="display: none;">
		<td class='TitulosCampos'></td>
		<td class='TextCampos'>
			
		</td>
		<td class='TextCampos'  id="TD2_CTA_2" style="visibility : hidden;">
			
		</td>
	</tr>
	<tr style="display: none;">
		<td class='TitulosCampos'></td>
		<td class='TextCampos'>
			<INPUT id="TIPO_CTA_FBM" class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 300px;">
		</td>
		<td class='TextCampos' id="TD3_CTA_2" style="visibility : hidden;">
			<INPUT id="TIPO_CTA_FBM2" class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 200px;">
		</td>
	</tr>
	<tr style="display: none;">
		<td class='TitulosCampos'></td>
		<td class='TextCampos'>
			<INPUT id='BANCO_FBM' class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 300px;">
			
		</td>
		<td class='TextCampos' id="TD4_CTA_2" style="visibility : hidden;">
			<INPUT id='BANCO_FBM2' class='TextoCampoInputDesactivado' type='text' size='32' value="" readonly="true" style="width: 200px;">
			<INPUT id="CTA_CODIGO_CONTABLE_FBM2" type="hidden" value="">
		</td>
	</tr>	
	<tr>
		<td class='TitulosCampos'>Concepto</td>
		<!--<td colspan="3" rowspan="1" class='TextCampos'>--><!-- <INPUT id='CONCEPTO_FBM' class='TextoCampoInput' type='text' size='71' value=""> -->
		<td class='TextCampos'><textarea id='CONCEPTO_FBM' class='TextoCampoInput' cols="30" rows="2" style="width: 300px; resize: none;"></textarea></td>
		<td class='TextCampos' id="TD5_CTA_2"><textarea id='CONCEPTO_FBM2' class='TextoCampoInput' cols="30" rows="2" style="width: 220px; resize: none;"></textarea></td>
	</tr>	
	<tr>
		<td class='TitulosCampos'>Número (Referencia)</td>
		<td class='TextCampos'>
			<INPUT id='DOCUMENTO_FBM' class='TextoCampoInput' type='text' size='15' maxlength='30' value="">
			
		</td>
		<td class='TextCampos' rowspan="2" style="vertical-align: middle; text-align: center;">
			<table align="center">
				<tr>
					<td><INPUT type="checkbox" value="" id="CHECK_CONTABILIZAR_FBM" onchange="" style="width: 20px; height: 20px;"></td>
					<td>
						<b>Contabilizar</b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class='TitulosCampos'>Monto</td>
		<td class='TextCampos'><INPUT id='MONTO_FBM' class='TextoCampoInput' type='text' size='15' value="" onkeypress="return AcceptNum(event,'MONTO_FBM')"></td>
	</tr>
	
</tbody>
</table>







						</FORM>
						
						
						

						<div class="tab-pane" id="SUB_TABPANE_FBM" style="margin-top: 15px;">
							<!-- ******************** ******************** Primera SUBPestaña ****************** *********************-->
							<div class="tab-page"  style="height : 140px;">
								<h2 class="tab" id="SUB_TABPANE_FBM_DP">
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
								<DIV class="AreaTablaListado" style="height : 100px; overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FBM_DP" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FBM_DP">
										<BUTTON id="BOTON_AGREGAR_FBM_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_BANCO_MOVIMIENTO__AgregarDP()">
											<IMG id="IMG_AGREGAR_FBM_DP" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FBM_DP" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_BANCO_MOVIMIENTO__DP_Quitar()">
											<IMG id="IMG_QUITAR_FBM_DP" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right" style="">
										<input class='BotonesParaCampos' type="button" value="txt" title="Ingresar detalles a través de texto plano." style="vertical-align: middle; text-align : center; font-size: 9px;" onclick="Form_BANCO_MOVIMIENTO__inputText()"/>
										<INPUT id='TOTAL_COMPROMISOS_FBM_DP' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width : 150px; vertical-align: middle;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************ ******************** fin ******************* *************************-->
							<!-- ******************** ******************** Segunda SUBPestaña ****************** *********************-->
							<div class="tab-page" style="height : 140px;">
								<h2 class="tab" id="SUB_TABPANE_FBM_DC">
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
								<DIV class="AreaTablaListado" style="height : 100px;  overflow-x: hidden;">
									<table id="TABLA_LISTA_ARTICULOS_FBM_DC" border="0" cellspacing="0" cellpadding="0" width="100%">
									</table>
								</DIV>
								<table border="0" cellpadding="1" cellspacing="0" width="100%">
								<tbody>
									<tr class="CabeceraTablaEstilo">
									<td align="left" id="BOTONES_AGREGAR_QUITAR_FBM_DC">
										<BUTTON id="BOTON_AGREGAR_FBM_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_BANCO_MOVIMIENTO__AgregarDC()">
											<IMG id="IMG_AGREGAR_FBM_DC" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar
										</BUTTON>
										<BUTTON id="BOTON_QUITAR_FBM_DC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_BANCO_MOVIMIENTO__DC_Quitar()">
											<IMG id="IMG_QUITAR_FBM_DC" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;
										</BUTTON>
									</td>
									<td align="right">
										<INPUT id='TOTAL_DEBE_FBM_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
										<INPUT id='TOTAL_HABER_FBM_DC' class='TextoCampoInputDesactivado' readonly="true" type='text' value="" style="text-align : right; width:115px;">
									</td>
									</tr>
								</tbody>
								</table>
							</div>
							<!-- ************************************ fin ****************** ********************* ***************-->
						</div>
						
						
						
						
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
					<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
					<div class="tab-page" style="height : 420px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Movimientos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FBM_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
						<br>

						<table cellspacing='5px' align="center">
						<tbody>
							<tr>
								<td class='TitulosCampos'>Cuenta</td>
								<td class='TextCampos'>
									<INPUT id="ID_CTA_BUSCAR_FBM" class='TextoCampoInput' type="hidden" value="" size="4">
									<INPUT id="NCTA_BUSCAR_FBM" class='TextoCampoInputDesactivado' type='text' value="" readonly="true" style="width: 170px"><INPUT  style="width: 440px" id="DESCRIPCION_NCTA_BUSCAR_FBM" class='TextoCampoInputDesactivado' type='text' value="" size='70' readonly="true"><IMG id="IMG_BUSCAR_NCTA_BUSCAR_FBM" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><br>
									<INPUT id="TIPO_CTA_BUSCAR_FBM" class='TextoCampoInputDesactivado' type='hidden' size='20' value="" readonly="true" style="width: 170px">
									<INPUT style="width: 440px" id='BANCO_BUSCAR_FBM' class='TextoCampoInputDesactivado' type='hidden' size='70' value="" readonly="true">
									<INPUT type="hidden" value="" id="CTA_CODIGO_CONTABLE_BUSCAR_FBM">
								</td>
							</tr>
						</tbody>
						</table>
						<br>


						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tbody>
								<tr class="CabeceraTablaEstilo">
									<td width="12%">OPERACI&Oacute;N</td>
									<td width="10%">FECHA</td>
									<td>CONCEPTO</td>
									<td width="9%">DEBE</td>
									<td width="9%">HABER</td>
									<td width="12%">SALDO</td>
								</tr>
							</tbody>
						</table>
						<DIV class="AreaTablaListado" style="height : 240px;">
							<table id="TABLA_LISTA_FBM" border="0" cellspacing="0" cellpadding="0" width="100%">
							</table>
						</DIV>

						<table width="100%">
						<tbody>
							<tr>
							<td width="30%">
								<table>
									<tr><td colspan="3"><strong>Estado&nbsp;del&nbsp;movimiento</strong></td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #FF5454; width : 5px;'></td><td>Sin contabilizar</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #48DC0E; width : 5px;'></td><td>Contabilizado</td></tr>
									<tr><td>&nbsp;</td><td style='background-color : #000000; width : 5px;'></td><td>Anulado</td></tr>
								</table>
							</td>
							<td width="70%">
								<DIV class='TitulosCampos' style="text-align : center;">
									DEL&nbsp;
									<INPUT id='FECHA_INICIO_FBM' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php list($dia,$mes,$ano)=explode("/",date("d/m/Y")); echo "01/$mes/".SIGA::data();?>"><IMG id="IMG_FECHA_INICIO_FBM" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									AL&nbsp;
									<INPUT id='FECHA_FIN_FBM' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/").SIGA::data()?>"><IMG id="IMG_FECHA_FIN_FBM" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18'>
									&nbsp;&nbsp;
									<INPUT class="BotonesParaCampos" type="button" value="Recargar" onclick="Form_BANCO_MOVIMIENTO__BuscarListado();">
								</DIV>
							</td>
							</tr>
						</tbody>
						</table>



					<!--<DIV style="background-color : #ff0000; height : 10px; width : 5px;"></DIV>-->


					</div>
					<!-- ************************************ fin ****************** ********************* ***************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>


<script>
	Form_BANCO_MOVIMIENTO__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FBM"), true);
	Form_BANCO_MOVIMIENTO__TabPaneSUBTAB = new WebFXTabPane(xGetElementById("SUB_TABPANE_FBM"), true);
	window.onload=function(){
		Form_BANCO_MOVIMIENTO__Nuevo();
	}
</script>


