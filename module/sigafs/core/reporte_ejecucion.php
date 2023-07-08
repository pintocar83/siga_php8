<table border="0" cellpadding="0" cellspacing="0" width="93%" align="center">
	<tbody>
		<tr>
			<td valign="top">
				<br>
				<div class="tab-pane" id="TABPANE_FEI">
					<!-- ******************** ******************** Primera Pestaña ****************** ********************* -->
					<div class="tab-page" style="height : 415px;">
						<h2 class="tab">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Presupuesto - Ejecuci&oacute;n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</h2>
						<DIV id="MSG_FEI" class="MensajesPestanas">&nbsp;</DIV>

						<table cellspacing='3px' align="center" width="90%">
						<tbody>
							<tr>
								<td class='TextCampos' style="padding-bottom: 10px;">									
									<SELECT id="SELECT_TIPO_FEI" class='TextoCampoInput' onchange="Form_EJECUCION__CargarSelectTrimestreMes()">
										<OPTION value="T"><strong>Trimestre</strong></OPTION>
										<OPTION value="M"><strong>Mes</strong></OPTION>
									</SELECT>
									<SELECT id="TRIMESTRE_MES_FEI" class='TextoCampoInput'>
									</SELECT>
									&nbsp;&nbsp;&nbsp;<strong>Nivel de detalle</strong>
									<SELECT id="NIVEL_DETALLE_FEI" class='TextoCampoInput'>
										<OPTION value="1">SOLO PARTIDA</OPTION>
										<OPTION value="2">PARTIDA | GENERICA</OPTION>
										<OPTION value="3" selected>PARTIDA | GENERICA | ESPECIFICA | SUBESPECIFICA</OPTION>
									</SELECT>									
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_1" value="V" checked>
									<strong>Consolidado general por acciones centralizadas y proyectos.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_2" value="E">
									<strong>Consolidado general por acciones centralizadas.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_3" value="3">
									<strong>Consolidado general por proyectos.</strong>
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_4" value="4">
									<strong>Consolidado por acci&oacute;n centralizada o proyecto.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO_FEI" class='TextoCampoInput'></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_PROYECTO_FEI" value="" size="50" readonly="true">
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_5" value="5">
									<strong>Consolidado por acci&oacute;n especifica.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO2_FEI" class='TextoCampoInput' onchange="Form_EJECUCION__CargarAE();"></SELECT>
									<SELECT id="ID_ACCION_ESPECIFICA_FEI" class='TextoCampoInput'></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_ACCION_ESPECIFICA_FEI" value="" size="30" readonly="true">
								</td>
							</tr>
							<tr>
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_6" value="5">
									<strong>Consolidado por otras acciones especificas.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO3_FEI" class='TextoCampoInput' onchange="Form_EJECUCION__CargarAE_6();"></SELECT>
									<SELECT id="ID_ACCION_ESPECIFICA3_FEI" class='TextoCampoInput' onchange="Form_EJECUCION__CargarOAE_6()"></SELECT>
									<SELECT id="ID_OTRA_ACCION_ESPECIFICA3_FEI" class='TextoCampoInput' title=""></SELECT>
									<INPUT type="text" class='CampoTextoBlanco' id="NOMBRE_OTRA_ACCION_ESPECIFICA_FEI" value="" size="30" readonly="true">
								</td>
							</tr>							
							<tr style="display: none;">
								<td class='TextCampos' >
									<INPUT type="radio" class='TextoCampoInput' name="RADIO_IMPRIMIR_FEI" id="ID_RADIO_IMPRIMIR_FEI_7" value="5">
									<strong>Consolidado por acci&oacute;n centralizada o proyecto, y por otras acciones especificas.</strong><br>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<SELECT id="ID_PROYECTO4_FEI" class='TextoCampoInput' onchange=""></SELECT>
									<SELECT id="ID_OTRA_ACCION_ESPECIFICA4_FEI" class='TextoCampoInput'></SELECT>
								</td>
							</tr>
						</tbody>
						</table>
						<DIV align="center"><INPUT type="checkbox" id="OCULTAR_FILAS_FFPDG_1" onchange="xGetElementById('OCULTAR_FILAS_FFPDG_2').checked=xGetElementById('OCULTAR_FILAS_FFPDG_1').checked; xGetElementById('OCULTAR_FILAS_FFPDG_3').checked=xGetElementById('OCULTAR_FILAS_FFPDG_1').checked;">Ocultar filas con montos cero.</DIV>
						<br>
						<DIV align="center" style="position : absolute; text-align : center; top : 340px; width : 100%;">
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_EJECUCION__Imprimir();">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar
							</BUTTON>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_EJECUCION__Imprimir('ejecucion_bss');">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar en Bs.S
							</BUTTON>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_EJECUCION__Imprimir('ejecucion_bsdigital');">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Visualizar en Bs.Digital
							</BUTTON>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_EJECUCION__Imprimir('ejecucion_alcaldia_mejia');">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Formato Alcald&iacute;a Mej&iacute;a
							</BUTTON>
							<br>
							<br>
							<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="Form_EJECUCION__Imprimir('ejecucion_alcaldia_mejia','&sw_pagado=off');">
								<IMG id="IMG_IMPRIMIR_FPC" src='../../image/icon/icon-display.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Formato Alcald&iacute;a Mej&iacute;a (Pagado)
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
	Form_EJECUCION__TabPane = new WebFXTabPane(xGetElementById("TABPANE_FEI"), true);
	window.onload=function(){		
		Form_EJECUCION__CargarDatos();		
	}	
</script>
