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
<style>
	html{
		height: 100%;
	}
	body{
		/*background-color: #FFFFFF;*/
		background: #eeeeee; /* Old browsers */
		background: -moz-linear-gradient(top, #eeeeee 0%, #cccccc 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#eeeeee), color-stop(100%,#cccccc)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #eeeeee 0%,#cccccc 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #eeeeee 0%,#cccccc 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #eeeeee 0%,#cccccc 100%); /* IE10+ */
		background: linear-gradient(to bottom, #eeeeee 0%,#cccccc 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#cccccc',GradientType=0 ); /* IE6-9 */
		height: 100%;
	}
</style>
<DIV class='TitulosCampos' style="text-align : left; padding: 20px 10px 0px 10px;">
	<table width="100%">
		<tbody>
			<tr>
				<td width="55%" valign="middle" style="white-space: nowrap;">
					Buscar
					<INPUT id="LISTADO_BUSCAR_FCP" class='TextoCampoInput' type="text" value="" size="40" onkeyup="Form_COMPROBANTE_PREVIO__Buscar();" onkeypress="Form_COMPROBANTE_PREVIO__PresionarEnter(event)"><IMG class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png' width='18' height='18' onclick="Form_COMPROBANTE_PREVIO__LimpiarInputTextBuscarListado();">
				</td>
				<td valign="middle" style="font-size : 12px; text-align: left;">
					<INPUT id="SOMBRA_CHECKBOX_FCP" type="checkbox" checked="true">Sombrear al buscar<br>
					<INPUT id="BUSCAR_CHECKBOX_FCP" type="checkbox" checked="true">Buscar al presionar enter<br>
					<!--<INPUT id="MOSTRAR_CHECKBOX_FCP" type="checkbox" checked onchange="Form_COMPROBANTE_PREVIO__BuscarListado();">Mostrar&nbsp;requisiciones&nbsp;sin&nbsp;orden de compra-->
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tbody>
			<tr class="CabeceraTablaEstilo">
				<td width="17%">NÚMERO</td>
				<td width="12%">FECHA</td>
				<td width="17%" id="COL_TIPO_PERSONA_FCP"></td>
				<td>CONCEPTO</td>
			</tr>
		</tbody>
	</table>		
	<DIV class="AreaTablaListado" style="height:140px;">
		<table id="TABLA_LISTA_FCP" border="0" cellspacing="0" cellpadding="0" width="100%">
		</table>
	</DIV>		
	<br>
	<table width="100%">
		<tbody>
			<tr>
				<td id="MSG_CARGANDO_FCP" width="60%" valign="top">
				</td>
				<td width="40%" valign="top">
					<DIV class='TitulosCampos' style="text-align: center;">	
						<BUTTON class="BotonesParaCampos" style="font-size: 14px; vertical-align: middle;" onclick="Form_COMPROBANTE_PREVIO__Aceptar();">
							<IMG id="IMG_ACEPTAR_FCP" src='../../image/icon/icon-accept-sigafs.png' width='22' height='22' style="vertical-align: middle;">&nbsp;Aceptar
						</BUTTON>
						&nbsp;&nbsp;&nbsp;
						<BUTTON class="BotonesParaCampos" style="font-size: 14px; vertical-align: middle;" onclick="Form_COMPROBANTE_PREVIO__Cerrar();">
							<IMG id="IMG_CANCELAR_FCP" src='../../image/icon/icon-close-sigafs.png' width='22' height='22' style="vertical-align: middle;">&nbsp;Cancelar
						</BUTTON>
					</DIV>		
				</td>
			</tr>
		</tbody>
	</table>		
</DIV>

<script>
	window.onload=function(){
		var _parameter=siga.window.getCmp("comprobante_previo_listado").parameter;
		Form_COMPROBANTE_PREVIO__BloquearLista=_parameter.bloquear;
		Form_COMPROBANTE_PREVIO__Arreglo=_parameter.id_comprobante_previo;
		Form_COMPROBANTE_PREVIO__Tipo=_parameter.tipo;
		Form_COMPROBANTE_PREVIO__id_persona=_parameter.id_persona;
		Form_COMPROBANTE_PREVIO__tipo_persona=_parameter.tipo_persona;
		if(Form_COMPROBANTE_PREVIO__tipo_persona=="N") 
			xGetElementById("COL_TIPO_PERSONA_FCP").innerHTML="BENEFICIARIO";
		else if(Form_COMPROBANTE_PREVIO__tipo_persona=="J") 
			xGetElementById("COL_TIPO_PERSONA_FCP").innerHTML="PROVEEDOR";
		else
			xGetElementById("COL_TIPO_PERSONA_FCP").innerHTML="PROVEEDOR<br>BENEFICIARIO";
		Form_COMPROBANTE_PREVIO__BuscarListado();		
	}
</script>
