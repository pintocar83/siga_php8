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
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tbody>
			<tr class="CabeceraTablaEstilo">						
				<td width="23%">ESTRUC. PRESUP.</td>
				<td>CUENTA</td>
				<td width="15%">MONTO</td>
			</tr>
		</tbody>
	</table>
	<DIV class="AreaTablaListado" style="height: 140px; overflow-x: hidden;">
		<table id="TABLA_LISTA_OCPC" border="0" cellspacing="0" cellpadding="0" width="100%"></table>
	</DIV>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tbody>
			<tr class="CabeceraTablaEstilo">	
				<td width="100%" align="left"><!--style="visibility : hidden;"-->
					<BUTTON id="BOTON_AGREGAR_OCPC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Agregar();"><IMG id="IMG_AGREGAR_OCPC" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Agregar</BUTTON>
					<BUTTON id="BOTON_QUITAR_OCPC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Quitar();"><IMG id="IMG_QUITAR_OCPC" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style="vertical-align : middle;">&nbsp;Quitar&nbsp;&nbsp;</BUTTON>
					<BUTTON id="BOTON_RESTAURAR_OCPC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : top;" type="BUTTON" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Restaurar();" title="Regenera el listado según los datos de la orden."><IMG id="IMG_RESTAURAR_OCPC" src='../../image/icon/icon-reload.png' width='18' height='18' style="vertical-align : middle;" >&nbsp;Regenerar&nbsp;&nbsp;</BUTTON>
				</td>
				<td align="right" style="white-space: nowrap;">TOTAL&nbsp;<INPUT id='TOTAL_OCPC' class='TextoCampoInputDesactivado' type='text' size='12' value="" readonly="true" style="text-align : right;"></td>
			</tr>
		</tbody>
	</table>
		
	<br>
	<table width="100%" border="0">
		<tbody>
			<tr>
				<td width="1%" valign="top">
					<BUTTON id="BOTON_ELIMINAR_OCPC" class="BotonesParaCampos" style="white-space: nowrap; font-size : 14px; vertical-align : middle;" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Eliminar();" title="Eliminar la precontabilización">
						<IMG id="IMG_ELIMINAR_OCPC" src='../../image/icon/icon-anular-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Eliminar
					</BUTTON>
				</td>
				<td id="MSG_CARGANDO_OCPC" valign="top">								
				</td>
				<td width="1%" valign="top" style="white-space: nowrap;">
					<DIV class='TitulosCampos' style="text-align : center;">	
						<BUTTON id="BOTON_ACEPTAR_OCPC" class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Aceptar();">
							<IMG id="IMG_ACEPTAR_OCPC" src='../../image/icon/icon-accept-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Aceptar
						</BUTTON>
						&nbsp;&nbsp;&nbsp;
						<BUTTON class="BotonesParaCampos" style="font-size : 14px; vertical-align : middle;" onclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Cerrar();">
							<IMG id="IMG_CANCELAR_OCPC" src='../../image/icon/icon-close-sigafs.png' width='22' height='22' style="vertical-align : middle;">&nbsp;Cancelar
						</BUTTON>
					</DIV>		
				</td>
			</tr>
		</tbody>
	</table>		
</DIV>


<INPUT type="hidden" id="ID_CASADA_OCPC" value="">
<INPUT type="hidden" id="PROGRAMATICA_OCPC" value="">
<INPUT type="hidden" id="CUENTA_OCPC" value="">

<script>
	window.onload=function(){
		var _parameter=siga.window.getCmp("orden_compra/precontabilizar").parameter;
		Form_ORDEN_COMPRA_PRECONTABILIZAR__id_comprobante=_parameter.id_comprobante;
		Form_ORDEN_COMPRA_PRECONTABILIZAR__BloquearLista=_parameter.bloquear;
		var _detalle_presupuestario=_parameter.detalle_presupuestario;
		//si existe un precontabilizacion previa
		Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno=[];
		if(_detalle_presupuestario.length>0){
			for(var i=0;i<_detalle_presupuestario.length;i++){
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]=[];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["id_accion_subespecifica"]=_detalle_presupuestario[i]["id_accion_subespecifica"];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["estructura_presupuestaria"]=_detalle_presupuestario[i]["estructura_presupuestaria"];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["id_cuenta_presupuestaria"]=_detalle_presupuestario[i]["id_cuenta_presupuestaria"];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["cuenta_presupuestaria"]=_detalle_presupuestario[i]["cuenta_presupuestaria"];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["denominacion_cuenta_presupuestaria"]=_detalle_presupuestario[i]["denominacion"];
				Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["total"]=_detalle_presupuestario[i]["monto"];				
			}
			Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
		}
		else
			Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado();
	}
</script>
