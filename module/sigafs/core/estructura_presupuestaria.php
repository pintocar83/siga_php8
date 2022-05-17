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
* @version 20090909
*/
-->
<TABLE border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td class="acciones">
				<BUTTON id="CMD_NUEVO" class="BotonesVentana" onclick="nuevoCentralizada();">
					<IMG src="../../image/icon/icon-new.png" width="22" height="22" border="0" id="IMG_NUEVO"><br>Nuevo
				</BUTTON>
				<BUTTON id="CMD_GUARDAR" class="BotonesVentana" onclick="GuardarVerificarCentralizada();">
					<IMG src="../../image/icon/icon-save.png" width="22" height="22" border="0" id="IMG_GUARDAR"><br>Guardar
				</BUTTON>
				<BUTTON id="CMD_MODIFICAR" class="BotonesVentana" onclick="modificarCentralizada();">
					<IMG src="../../image/icon/icon-edit-sigafs.png" width="22" height="22" border="0"  id="IMG_MODIFICAR"><br>Modificar
				</BUTTON>
				<BUTTON id="CMD_ELIMINAR" class="BotonesVentana" onclick="eliminarCentralizada();">
					<IMG src="../../image/icon/icon-delete-sigafs.png" width="22" height="22" border="0"  id="IMG_ELIMINAR"><br>Eliminar
				</BUTTON>
			</td>
		</tr>
    <tr>
      <td  valign="top" class="formulario">
          <div class="tab-pane" id="TABPANE_FEP">
            <!-- ******************** ******************** Primera Pestaña ****************** *********************-->
            <div class="tab-page" style="height : 320px;">
              <h2 class="tab">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acciones Centralizadas y/o Proyectos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
							<DIV id="MSG_FB_CENTRALIZADA" class="MensajesPestanas">&nbsp;</DIV><br>
              <FORM id="accionCentralizada" name="accionCentralizada">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="5">
                  <tr>
											<td width="35%"><div align="right">C&oacute;digo</div></td>
											<td width="65%"><select id="escestPres" class="TextoCampoInputObligatorios" onchange="colocarCeros();"><option value="PRO">PRO</option><option value="ACC">ACC</option></select><input class="TextoCampoInputObligatorios" type="text" id="codigoCentralizada" size="10" maxlength="6" onkeypress="return AcceptNum(event);" onblur="xGetElementById('codigoCentralizada').value=completarCodigoCeros(xGetElementById('codigoCentralizada').value,5);"><INPUT type="hidden" name="guardCentralizada" id="guardCentralizada" value="1"><INPUT type="hidden" name="hidNuevoProf" id="hidNuevoProf"></td>
									</tr>
									<tr>
											<td><div align="right">Denominaci&oacute;n</div></td>
											<td><input class="TextoCampoInputObligatorios" name="denominacionCentralizada" type="text" id="denominacionCentralizada" size="70"></td>
									</tr>
                </table>
							</FORM>
              <table width="100%" border="0" cellspacing="0">
                <tr class="CabeceraTablaEstilo">
									<td width="30%"><div align="left">CÓDIGO</div></td>
									<td width="70%"><div align="left">DENOMINACIÓN</div></td>
								</tr>
              </table>
              <div style="border: 1px solid #b4b4b4; height:150px; overflow: auto;" class="AreaTablaListado">
                <table width="100%" border="0" align="center" cellspacing="1" cellpadding="2" id="lstAccionesCentralizadas"></table>
              </div>
              <br>
              <div class='TitulosCampos' style="text-align : center;">Buscar&nbsp;
								<input id="LISTADO_BUSCAR_FB_CENTRALIZADA" class='TextoCampoInput' type="text" value="" size="50" onkeyup="buscarListado();">
								<button class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" onclick="limpiarInputTextBuscarListado();">
									<img id="IMG_LIMPIAR_FB" src='../../image/icon/icon-clear-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Limpiar
								</button>
							</div>
            </div>
            <!-- ************************** ******************** fin ******************* **************************-->

            <!-- ******************** ******************** Segunda Pestaña ****************** *********************-->
            <div class="tab-page" style="height:320px;">
							<h2 class="tab">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acciones Específicas&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
							<DIV id="MSG_FB_ESPECIFICA" class="MensajesPestanas">&nbsp;</DIV><br>
							<FORM id="accionEspecifica" name="frmProfesion">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="5">
									<tbody>
										<tr>
											<td width="35%"><div align="right">C&oacute;digo</div></td>
											<td width="65%"><input type="text" id="codigoEspecificaInicial" style="border:0; color : #000000;" readonly="true" size="10">-&nbsp;<input class="TextoCampoInputObligatorios" name="codigoEspecifica" type="text" id="codigoEspecifica" size="5" maxlength="2" onkeypress="return soloNum(event)"><INPUT type="hidden" size="5" name="hidCodCentralizada" id="hidCodCentralizada"><INPUT type="hidden" name="guardEspecifica" id="guardEspecifica" size="5"></td>
										</tr>
										<tr>
											<td><div align="right">Denominaci&oacute;n</div></td>
											<td><input class="TextoCampoInputObligatorios" name="denominacionEspecifica" type="text" id="denominacionEspecifica" size="70"></td>
										</tr>
									</tbody>
								</table>
              </FORM>
							<table width="100%" border="0" cellspacing="0">
								<tbody>
									<tr class="CabeceraTablaEstilo">
										<td width="30%"><div align="left">CÓDIGO</div></td>
										<td width="70%"><div align="left">DENOMINACIÓN</div></td>
									</tr>
								</tbody>
							</table>
							<div style="border: 1px solid #b4b4b4; height:180px; overflow: auto;" class="AreaTablaListado">
								<table width="100%" border="0" align="center" cellspacing="1" cellpadding="2" id="lstAccionesEspecificas"></table>
							</div>
              <br>
            </div>
            <!-- ************************** ******************** fin ******************* **************************-->

            <!-- ******************** ******************** Tercera Pestaña ****************** *********************-->
            <div class="tab-page" style="height : 320px;">
              <h2 class="tab">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acciones Subespecíficas&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h2>
							<DIV id="MSG_FB_OTROS" class="MensajesPestanas">&nbsp;</DIV><br>
							<FORM id="accionEspecificaOtros" name="frmProfesion">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="5">
									<tbody>
										<tr>
											<td width="35%"><div align="right">C&oacute;digo</div></td>
											<td width="65%"><input type="text" id="codigoSubEspecificaInicial" style="border:0; color : #000000;" readonly="true" size="12">-&nbsp;<input class="TextoCampoInputObligatorios" name="codigoOtros" type="text" id="codigoOtros" size="5" maxlength="2" onkeypress="return soloNum(event)"><INPUT type="hidden" size="5" name="hidCodEspecifica" id="hidCodEspecifica"><INPUT type="hidden" name="guardOtros" id="guardOtros" size="5"></td>
										</tr>
										<tr>
											<td><div align="right">Denominaci&oacute;n</div></td>
											<td><input class="TextoCampoInputObligatorios" name="denominacionOtros" type="text" id="denominacionOtros" size="70"></td>
										</tr>
									</tbody>
								</table>
                <table width="100%" border="0" cellspacing="0">
									<tbody>
										<tr class="CabeceraTablaEstilo">
											<td width="30%"><div align="left">CÓDIGO</div></td>
											<td width="70%"><div align="left">DENOMINACIÓN</div></td>
										</tr>
									</tbody>
								</table>
								<DIV style="border: 1px solid #b4b4b4; height:180px; overflow: auto;" class="AreaTablaListado">
									<table width="100%" border="0" align="center" cellspacing="1" cellpadding="2" id="lstOtros"></table>
								</DIV>								
                <br>
							</FORM>
						</div>
						<!-- ************************** ******************** fin ******************* **************************-->
          </div>
        </td>
    </tr>
  </tbody>
</TABLE>
<script>
	Form_ESTRUCTURA_PRESUPUESTARIA__TabPane= new WebFXTabPane(xGetElementById("TABPANE_FEP"), true);
	window.onload=function(){		
		Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.setSelectedIndex(0);
		nuevoCentralizada();
	}	
</script>