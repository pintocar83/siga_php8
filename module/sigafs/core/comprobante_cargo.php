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
				<td width="60%" valign="middle">
					Buscar
					<INPUT id="LISTADO_BUSCAR_FCC" class='TextoCampoInput' type="text" value="" size="30" onkeyup="Form_COMPROBANTE_CARGO__Buscar();" onkeypress="Form_COMPROBANTE_CARGO__PresionarEnter(event)"><IMG class='BotonesParaCampos' src='../../image/icon/icon-clear-sigafs.png' width='18' height='18' onclick="Form_COMPROBANTE_CARGO__LimpiarInputTextBuscarListado();">
				</td>
				<td width="40%" valign="middle" style="font-size : 12px;">
					<INPUT id="SOMBRA_CHECKBOX_FCC" type="checkbox" checked="true">Sombrear al buscar<br>
					<INPUT id="BUSCAR_CHECKBOX_FCC" type="checkbox">Solo buscar al presionar enter
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tbody>
			<tr class="CabeceraTablaEstilo">
				<td width="10%">CARGO</td>
				<td width="30%">DENOMINACI&Oacute;N</td>
				<td width="25%">F&Oacute;RMULA</td>
				<td width="15%">CORRECCI&Oacute;N</td>
				<td width="20%">MONTO</td>
			</tr>
		</tbody>
	</table>		
	<DIV id="DIV_TABLA_LISTA_FCC" class="AreaTablaListado" style="height : 160px;">
		<table id="TABLA_LISTA_FCC" border="0" cellspacing="0" cellpadding="0" width="100%">
		</table>
	</DIV>		
	<br>
	<table width="100%">
		<tbody>
			<tr>
				<td id="MSG_CARGANDO_FCC" width="60%" valign="top">
				</td>
				<td width="40%" valign="top">
					<DIV class='TitulosCampos' style="text-align : center;">	
						<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;" onclick="Form_COMPROBANTE_CARGO__Aceptar();">
							<IMG id="IMG_ACEPTAR_FCC" src='../../image/icon/icon-accept-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Aceptar
						</BUTTON>
						&nbsp;&nbsp;&nbsp;
						<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;" onclick="Form_COMPROBANTE_CARGO__Cerrar();">
							<IMG id="IMG_CANCELAR_FCC" src='../../image/icon/icon-close-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Cancelar
						</BUTTON>
					</DIV>		
				</td>
			</tr>
		</tbody>
	</table>		
</DIV>
<script>
	window.onload=function(){
		Form_COMPROBANTE_CARGO__BloquearLista=siga.window.getCmp("comprobante/cargo").parameter.bloqueado;
		Form_COMPROBANTE_CARGO__BIIVA=siga.window.getCmp("comprobante/cargo").parameter.monto_base_iva;
		Form_COMPROBANTE_CARGO__BI=siga.window.getCmp("comprobante/cargo").parameter.monto_base;
		Form_COMPROBANTE_CARGO__Arreglo=siga.window.getCmp("comprobante/cargo").parameter.cargo;
		Form_COMPROBANTE_CARGO__BuscarListado();		
	}
</script>