<table border="0" cellpadding="0" cellspacing="0" width="93%" align="center">
	<tbody>
		<tr>
			<td valign="top">
				<br>
				<div class="tab-pane" id="TABPANE_RR">
					<!-- ******************** ******************** Primera Pestaña ****************** *********************-->
					<div class="tab-page"  style="height : 295px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entrada de datos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_RR" class="MensajesPestanas">&nbsp;</DIV>

						<table align="center" width="80%" >
						<tbody>
							<tr>
								<td>
									<fieldset style="border: 1px solid #A5A5A5;">
										<legend><b>Retención</b> <SELECT id="TIPO_RR"  class="TextoCampoInput">
											<OPTION value="1">I.V.A.</OPTION>
											<OPTION value="2">I.S.L.R.</OPTION>
											<OPTION value="3">1x1000</OPTION>
										</SELECT></legend>
										<table style="vertical-align: middle;">
											<tr>
												<td><input type="radio"  class="TextoCampoInput" checked="checked" name="OPCION_RR" id="OPCION_RR_A"/></td>
												<td>Listado de Retenciones Emitidas.</td>
											</tr>
											<tr>
												<td><input type="radio"  class="TextoCampoInput" name="OPCION_RR" id="OPCION_RR_D"/></td>
												<td>Listado de Retenciones Emitidas (Excel Seniat).</td>
											</tr>
											<tr>
												<td><input type="radio"  class="TextoCampoInput" name="OPCION_RR" id="OPCION_RR_B"/></td>
												<td>Listado de Retenciones Efectuadas.</td>
											</tr>
											<tr>
												<td><input type="radio"  class="TextoCampoInput" name="OPCION_RR" id="OPCION_RR_C"/></td>
												<td>Comprobantes Emitidos.</td>
											</tr>
										</table>


									</fieldset>
								</td>
							</tr>
							<tr>
								<td style="padding-left: 50px; padding-top: 10px;">
									<input name="RADIO_TIPO_PERSONA" id="RADIO_TIPO_PERSONA_TODOS" type="radio"  class="TextoCampoInput" onchange="reporte_retencion.onChange_TipoPersona()" checked /> Todos
									<input name="RADIO_TIPO_PERSONA" id="RADIO_TIPO_PERSONA_PROVEEDOR" type="radio"  class="TextoCampoInput" onchange="reporte_retencion.onChange_TipoPersona()"/> Proveedor
									<input name="RADIO_TIPO_PERSONA" id="RADIO_TIPO_PERSONA_BENEFICIARIO" type="radio"  class="TextoCampoInput" onchange="reporte_retencion.onChange_TipoPersona()"/> Beneficiario
									<br>
									<div id="DIV_PB" style="visibility: hidden;">
										<INPUT id='IDENTIFICACION_PERSONA_RR' class='TextoCampoInputDesactivado' type='text' size='12' value="" readonly="true"><INPUT id='DENOMINACION_PERSONA_RR' class='TextoCampoInputDesactivado' readonly="true" type='text' size='25' value=""><IMG id="IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_RR" class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18' onclick="reporte_retencion.onPersona()" /><IMG id="IMG_LIMPIAR_BP_RR" class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png' width='18' height='18' onclick='reporte_retencion.onClear_Persona()'/>
										<INPUT type="hidden" id="ID_PERSONA_RR" value="">
									</div>
								</td>
							</tr>
						</tbody>
						</table>


						<br>
						<DIV class='TitulosCampos' style="text-align: center;">
							Del
							<INPUT id='FECHA_INICIO_RR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php list($dia,$mes,$ano)=explode("/",date("d/m/Y")); echo "01/$mes/$ano";?>" ondblclick="showCalendar('FECHA_INICIO_RR','%d/%m/%Y')"><IMG id="IMG_FECHA_INICIO_RR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png'  width='18' height='18' onclick="showCalendar('FECHA_INICIO_RR','%d/%m/%Y')">
							al&nbsp;
							<INPUT id='FECHA_FIN_RR' class='TextoCampoInput' type='text' size='11' maxlength='10' value="<?php echo date("d/m/Y")?>" ondblclick="showCalendar('FECHA_FIN_RR','%d/%m/%Y')"><IMG id="IMG_FECHA_FIN_RR" class='BotonesParaCampos' src='../../image/icon/icon-calendar-sigafs.png' width='18' height='18' onclick="showCalendar('FECHA_FIN_RR','%d/%m/%Y')">
							<br>
							<br>
							<br>

							<BUTTON id="BOTON_ACEPTAR_RR" class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;"   onclick="reporte_retencion.onDisplay()">
								<IMG id="IMG_ACEPTAR_RR" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar&nbsp;
							</BUTTON>
						</DIV>
					</div>
					<!-- ************************ ******************** fin ******************* *************************-->
				</div>
			</td>
		</tr>
	</tbody>
</table>
<script>
	Form_REPORTE_RETENCION__TabPane = new WebFXTabPane(xGetElementById("TABPANE_RR"), true);
	window.onload=function(){
		reporte_retencion.onChange_TipoPersona();
	}
</script>
