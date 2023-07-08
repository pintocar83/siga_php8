<DIV id="SIGAFS">
	<div class="tab-pane" id="TABPANE">
		<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
		<div class="tab-page"  style="height : 430px;">
			<h2 class="tab">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</h2>
			<DIV id="MSG" class="MensajesPestanas">&nbsp;</DIV>
			<br>

			<FORM id="FORMULARIO" name="FORMULARIO" enctype="multipart/form-data">
				<table cellspacing='3px' align="center" border="0">
				<tbody>
					<tr>
						<td></td>
						<td colspan="2" style="display: flex;">
							<BUTTON id="BOTON_PROVEEDOR" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" type="BUTTON">
								Proveedor
							</BUTTON>
							<BUTTON id="BOTON_BENEFICIARIO" class="BotonesParaCampos" style="font-size : 11px; vertical-align : top;" type="BUTTON">
								Beneficiario
							</BUTTON>
							<span style="padding-left: 20px; flex: 1;">
								<BUTTON id="BOTON_CONTABLIZAR" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" type="BUTTON">Contabilizar</BUTTON>
								<BUTTON id="BOTON_REVERSAR" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" type="BUTTON">Reversar</BUTTON>
								<BUTTON id="BOTON_ANULAR" class="BotonesParaCampos" style="vertical-align : top; font-size: 11px; white-space: nowrap;" type="BUTTON">Anular</BUTTON>
							</span>
							<span class='TitulosCampos' style="font-size: 11px; vertical-align: bottom;" id="COMPROBANTE"></span>
						</td>
					</tr>
					<tr>
						<td class='TitulosCampos' id="PERSONA_TIPO"></td>
						<td class='TextCampos' colspan="2" style="display: flex;">
							<INPUT id='PERSONA_IDENTIFICACION' class='TextoCampoInputDesactivado' type='text' size='22' maxlength='15' value="" readonly="true"  style="width: 150px;"><INPUT id='PERSONA_DENOMINACION' class='TextoCampoInputDesactivado' readonly="true" type='text' size='65' value=""  style="width: 450px; flex: 1;"><button type="button" class="boton_campo" id="BOTON_SELECCIONAR_PERSONA"><IMG src='image/icon/icon-find.png' /></button>
							<INPUT type="hidden" id="PERSONA_ID" value="">
						</td>
					</tr>
					<tr>
						<td class='TitulosCampos'>Cuenta</td>
						<td class='TextCampos' colspan="2" style="display: flex;">
							<INPUT id="ID_CTA" class='TextoCampoInputDesactivado' type="hidden" value="" size="4" readonly="true">
							<INPUT id="NCTA" class='TextoCampoInputDesactivado' type='text' size='22' value="" readonly="true"  style="width: 150px;"><INPUT id="DESCRIPCION_NCTA" class='TextoCampoInputDesactivado' type='text' size='65' value="" readonly="true"  style="width: 450px; flex:1;"><button type="button" class="boton_campo" id="BOTON_SELECCIONAR_CUENTA_BANCARIA"><IMG src='image/icon/icon-find.png' /></button>
							<INPUT type="hidden" value="" id="CTA_CODIGO_CONTABLE">
							<INPUT type="hidden" value="" id="CUENTA_CONTABLE">
							<INPUT type="hidden" value="" id="CTA_DENOMINACION_CONTABLE">
							<INPUT id="TIPO_CTA" class='TextoCampoInputDesactivado' type='hidden' size='15' value="" readonly="true">
							<INPUT id='BANCO' class='TextoCampoInputDesactivado' type='hidden' size='25' value="" readonly="true">
						</td>
					</tr>
					<tr>
						<td class='TitulosCampos'>Fecha</td>
						<td colspan="2">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
								<td class='TextCampos'><INPUT id='FECHA' class='TextoCampoInput' type='text' size='10' maxlength='10' value="<?php echo date("d/m/Y")?>"><button type="button" class="boton_campo" id="BOTON_CALENDARIO"><IMG src='image/icon/icon-calendar.png' /></td>
								<td class='TitulosCampos' style="">Forma&nbsp;de&nbsp;Pago&nbsp;</td>
								<td class='TextCampos'>
									<SELECT class="TextoCampoInput" id="FORMA_PAGO">
										<option value="" disabled>&nbsp;</option>
										<option value="cheque" id="FORMA_PAGO_cheque">Cheque</option>
										<option value="transferencia">Transferencia</option>
										<option value="deposito">Deposito</option>
										<option value="pago_movil">Pago Movil</option>
										<option value="efectivo">Efectivo</option>
									</SELECT>
								</td>
								<td class='TitulosCampos' id="PAGO_TIPO" style="width: 100px;"></td>
								<td class='TextCampos' style="width: 1%;"><INPUT id='N_CHEQUE' class='TextoCampoInput' type='text' size='15' value=""></td>
								</tr>
							</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class='TitulosCampos'>Cuenta&nbsp;Destino</td>
						<td colspan="2">
							<INPUT id='CUENTA_DESTINO' class='TextoCampoInput' type='text' size='100' value="" style='width: 100%;'>
						</td>
					</tr>
					<tr>
						<td class='TitulosCampos'>Concepto</td>
						<td class='TextCampos' colspan="2">
							<TEXTAREA cols="73" rows="2" class='TextoCampoInput' id="CONCEPTO" style="resize: none; width: 100%;"></TEXTAREA>
						</td>
					<tr>
						<td class='TitulosCampos'>Monto</td>
						<td class='TextCampos' colspan="2">
							<INPUT id="MONTO" class='TextoCampoInputDesactivado' type='text' size='18' value="" style="text-align : right;" readonly="true">
							<SELECT class='TextoCampoInput' id="SELECT_RETENCION">
								<OPTION value="0">RETENCIONES EN EL ULTIMO PAGO</OPTION>
								<OPTION value="1" selected="selected">RETENCIONES EN EL PRIMER PAGO</OPTION>
							</SELECT>
						</td>
					</tr>
					<tr id="PAGO_ADJUNTO">
						<td class='TitulosCampos'>Adjunto</td>
						<td class='TextCampos' colspan="2">
							<input  type="text" id="ARCHIVO_ADJUNTO" class='TextoCampoInputDesactivado' value="" style="width: 560px;" readonly="true" /><button type="button" class="boton_campo centro" id="BOTON_ARCHIVO_BORRAR"><IMG src='image/icon/icon-listremove.png' /></button><div type="button" class="boton_campo centro" id="BOTON_ARCHIVO_ADJUNTAR_MASCARA" style="display: inline-block;"><IMG src='image/icon/icon-upload.svg' /><input type="file" id="BOTON_ARCHIVO_ADJUNTAR" name="comprobante_file[]" style="position: absolute; top:0px; bottom: 0px; left: 0px; right: 0px; opacity: 0; cursor: pointer; z-index: 100;" /></div><button type="button" class="boton_campo" id="BOTON_ARCHIVO_MOSTRAR"><IMG src='image/icon/icon-display_16x16.png' /></button>
						</td>
					</tr>
				</tbody>
				</table>
			</FORM>
			<table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top: 15px;">
				<tbody>
					<tr class="CabeceraTablaEstilo">
						<td width='16px'><img id='VIEW_ALL' class='BotonesParaCampos' src='image/icon/icon-display_16x16.png' style='border: none; background: none; margin-right: 0px; margin-left: 2px; width: 16px; height: 16px;' title='Visualizar Todas' /></td>
						<td width="1%" style="padding: 5px 0px 0px 5px;"><INPUT type="checkbox" id="CHECK_ALL" /></td>
						<td width="10%">NÚMERO</td>
						<td width="10%">FECHA</td>
						<td>CONCEPTO</td>
						<td width="10%" style="white-space: nowrap;">MONTO<br>ORDEN PAGO</td>
						<td width="10%">TOTAL<br>PAGADO</td>
						<td width="10%">MONTO</td>
					</tr>
				</tbody>
			</table>
			<DIV id="DIV_TABLA_SOLICITUDES_LISTA" class="listado" style="height : 110px; overflow-x: hidden;">
				<table id="TABLA_LISTA_SOLICITUDES" border="0" cellspacing="0" cellpadding="0" width="100%">
				</table>
			</DIV>
		</div>
		<!-- ************************ ******************** fin ******************* *************************-->
		<!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
		<div class="tab-page" style="height : 430px;">
			<h2 class="tab">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detalles&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</h2>
			<DIV id="MSG_DETALLES" class="MensajesPestanas">Muestra información referente al cheque, generada a partir de la(s) solicitud(es) de pago.</DIV>

			<br>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tbody>
					<tr class="CabeceraTablaEstilo">
						<td width="17%">ESTRUC. PRESUP.</td>
						<td width="13%">CUENTA</td>
						<td width="33%">DENOMINACI&Oacute;N</td>
						<td width="11%">PRESUPUESTO</td>
						<td width="11%">DEBE</td>
						<td width="11%">HABER</td>
					</tr>
				</tbody>
			</table>
			<DIV class="listado" style="height : 240px;">
				<table id="TABLA_LISTA_DETALLES" border="0" cellspacing="0" cellpadding="0" width="100%">
				</table>
			</DIV>
			<DIV class="CabeceraTablaEstilo" style="text-align : right;">
				<INPUT id="TOTAL_PARCIALES" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true"><INPUT id="TOTAL_DEBITOS" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true"><INPUT id="TOTAL_CREDITOS" type="text" class='TextoCampoInputDesactivado' style="font-size : 11px; text-align : right; width : 12%;" readonly="true">
			</DIV>
			<br>
			<DIV align="center">
				<br>
				<strong>Mostrar detalles </strong><SELECT id="SELECT_DETALLES" class='TextoCampoInput'></SELECT>
			</DIV>

		</div>
		<!-- ************************************ fin ****************** ********************* ***************-->
		<!-- ******************** ******************** Tercera Pestaña ****************** *********************-->
		<div class="tab-page" style="height : 430px;">
			<h2 class="tab">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Listado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</h2>
			<DIV id="MSG_LISTADO" class="MensajesPestanas">&nbsp;</DIV>
			<br>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tbody>
					<tr class="CabeceraTablaEstilo">
						<td width="11%">NÚMERO</td>
						<td width="10%">FECHA</td>
						<td width="25%" id="LISTADO_TD_PERSONA"></td>
						<td>CONCEPTO</td>									
						<td width="10%">MONTO</td>
					</tr>
				</tbody>
			</table>
			<DIV class="listado" style="height : 210px;">
				<table id="TABLA_LISTA" border="0" cellspacing="0" cellpadding="0" width="100%">
				</table>
			</DIV>
			<br>
			<BUTTON id="BOTON_PROVEEDOR_2" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" type="BUTTON">
				Proveedores
			</BUTTON>
			<BUTTON id="BOTON_BENEFICIARIO_2" class="BotonesParaCampos" style="font-size : 12px; height : 19px; vertical-align : top;" type="BUTTON">
				Beneficiarios
			</BUTTON>
			<table width="100%">
			<tbody>
				<tr>
				<td valign="top">
					<table>
						<tr><td colspan="3"><strong>Estado&nbsp;del&nbsp;cheque</strong></td></tr>
						<tr><td>&nbsp;</td><td style='background-color : #FF5454; width : 5px;'></td><td>Sin contabilizar</td></tr>
						<tr><td>&nbsp;</td><td style='background-color : #48DC0E; width : 5px;'></td><td>Contabilizado</td></tr>
						<tr><td>&nbsp;</td><td style='background-color : #000000; width : 5px;'></td><td>Anulado</td></tr>
					</table>
				</td>
				<td valign="top">
				<DIV class='TitulosCampos' style="text-align : left;">
						<table border='0'>
						<tbody>
							<tr>
							<td>Buscar&nbsp;</td>
							<td style="white-space : nowrap;">
								<INPUT id="LISTADO_TXT_BUSCAR" class='TextoCampoInput' type="text" value="" size="30">
								<BUTTON id="LISTADO_TXT_LIMPIAR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;">
									<IMG id="IMG_LIMPIAR" src='image/icon/icon-clear-sigafs.png' width='14' height='14' style="vertical-align : middle;">&nbsp;Limpiar
								</BUTTON>
							</td>
							</tr>
							<tr>
							<td>Mostrar&nbsp;</td>
							<td>
								<SELECT class="TextoCampoInput" id="LISTADO_MES_FILTRAR">
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
					<INPUT id="SOMBRA_CHECKBOX" type="checkbox" checked="true">Sombrear al buscar<br>
					<INPUT id="BUSCAR_CHECKBOX" type="checkbox" checked="true">Solo buscar al presionar enter
				</td>
				</tr>
			</tbody>
			</table>
		</div>
		<!-- ************************************ fin ****************** ********************* ***************-->
	</div>
</DIV>