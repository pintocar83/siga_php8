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

var Form_ORDEN_COMPRA__RequisicionesSeleccionadas=new Array();
var Form_ORDEN_COMPRA__NRequisicionesSeleccionadas=0;
var Form_ORDEN_COMPRA__ArregloArticulos=new Array();
var Form_ORDEN_COMPRA__ArregloArticulosContador=0;
var Form_ORDEN_COMPRA__CargosSeleccionados=new Array();
var Form_ORDEN_COMPRA__NCargosSeleccionados=0;
var Form_ORDEN_COMPRA__SubTotal=0;
var Form_ORDEN_COMPRA__SubTotalIVA=0;
var Form_ORDEN_COMPRA__PorcentajeDescuento=0;
var Form_ORDEN_COMPRA__Descuento=0;
var Form_ORDEN_COMPRA__BaseImponible=0;
var Form_ORDEN_COMPRA__IDComprobante="";
var Form_ORDEN_COMPRA__comprobante_posterior="";


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_ORDEN_COMPRA__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_OC").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_ORDEN_COMPRA__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_OC_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_ORDEN_COMPRA__ActivarFormulario(){
	xGetElementById("FECHA_OC").readOnly=false;
	xGetElementById("RIF_PROVEEDOR_OC").readOnly=true;
	xGetElementById("CONCEPTO_OC").readOnly=false;
	xGetElementById("PORCENTAJE_DESCUENTO_OC").readOnly=false;
	xGetElementById("DESCUENTO_OC").readOnly=false;
	xGetElementById("BOTON_PROVEEDOR_OC").disabled=false;
	xGetElementById("BOTON_BENEFICIARIO_OC").disabled=false;

	if(Form_ORDEN_COMPRA__IDSeleccionActualLista==-1)
		xGetElementById("CHECK_REQUISICION_OC").disabled=false;
	else
		xGetElementById("CHECK_REQUISICION_OC").disabled=true;

	xGetElementById("CHECK_DESCUENTO_OC").disabled=false;


	xGetElementById("FECHA_OC").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("RIF_PROVEEDOR_OC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_OC").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('class','TextoCampoInput');
	xGetElementById("DESCUENTO_OC").setAttribute('class','TextoCampoInput');

	xGetElementById("FECHA_OC").setAttribute('ondblclick',"showCalendar('FECHA_OC','%d/%m/%Y');");
	xGetElementById("IMG_FECHA_OC").setAttribute('onclick',"showCalendar('FECHA_OC','%d/%m/%Y');");

	ActivarBoton("IMG_FECHA_OC","IMG_FECHA_OC",'calendario');

	xGetElementById("RIF_PROVEEDOR_OC").setAttribute('ondblclick',"Form_LISTA_PROVEEDOR__Abrir('ID_PROVEEDOR_OC','RIF_PROVEEDOR_OC','NOMBRE_PROVEEDOR_OC');");
	xGetElementById("IMG_BUSCAR_PROVEEDOR_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__SeleccionarPersona()");
	//xGetElementById("IMG_BUSCAR_PROVEEDOR_OC").setAttribute('onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_PROVEEDOR_OC','RIF_PROVEEDOR_OC','NOMBRE_PROVEEDOR_OC');");
	ActivarBoton("IMG_BUSCAR_PROVEEDOR_OC","IMG_BUSCAR_PROVEEDOR_OC",'buscar');

	xGetElementById("CHECK_REQUISICION_OC").setAttribute('onchange',"Form_ORDEN_COMPRA__CambioCheck();");
	xGetElementById("CHECK_DESCUENTO_OC").setAttribute('onchange',"Form_ORDEN_COMPRA__CambioCheckDescuento(); Form_ORDEN_COMPRA__MostrarTablaArticulos();");



	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('onblur', "PierdeFoco_InputTextNUMERICO('PORCENTAJE_DESCUENTO_OC','0.00'); Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();");
	xGetElementById("DESCUENTO_OC").setAttribute('onblur', "PierdeFoco_InputTextNUMERICO('DESCUENTO_OC','0.00');Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();");

	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('onfocus', "TomaFoco_InputTextNUMERICO('PORCENTAJE_DESCUENTO_OC','0.00');");
	xGetElementById("DESCUENTO_OC").setAttribute('onfocus', "TomaFoco_InputTextNUMERICO('DESCUENTO_OC','0.00');");


	if(xGetElementById("BOTON_AGREGAR_OC"))
		ActivarBoton("BOTON_AGREGAR_OC","IMG_AGREGAR_OC",'agregar');
	if(xGetElementById("BOTON_QUITAR_OC"))
		ActivarBoton("BOTON_QUITAR_OC","IMG_QUITAR_OC",'quitar');

	if(xGetElementById("BOTON_REQUISICIONES_OC"))
		ActivarBoton("BOTON_REQUISICIONES_OC","IMG_BUSCAR_R_OC",'buscar');

	xGetElementById("BOTON_CARGOS_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__Cargo(false)");

	Form_ORDEN_COMPRA__MostrarTablaArticulos(false);

	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_ORDEN_COMPRA__DesactivarFormulario(){
	xGetElementById("FECHA_OC").readOnly=true;
	xGetElementById("RIF_PROVEEDOR_OC").readOnly=true;
	xGetElementById("CONCEPTO_OC").readOnly=true;
	xGetElementById("PORCENTAJE_DESCUENTO_OC").readOnly=true;
	xGetElementById("DESCUENTO_OC").readOnly=true;
	xGetElementById("BOTON_PROVEEDOR_OC").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_OC").disabled=true;
	xGetElementById("CHECK_REQUISICION_OC").disabled=true;
	xGetElementById("CHECK_DESCUENTO_OC").disabled=true;

	xGetElementById("FECHA_OC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("RIF_PROVEEDOR_OC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_OC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DESCUENTO_OC").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("FECHA_OC").setAttribute('ondblclick',"");
	xGetElementById("IMG_FECHA_OC").setAttribute('onclick',"");

	DesactivarBoton("IMG_FECHA_OC","IMG_FECHA_OC",'calendario');

	xGetElementById("RIF_PROVEEDOR_OC").setAttribute('ondblclick',"");
	xGetElementById("IMG_BUSCAR_PROVEEDOR_OC").setAttribute('onclick',"");
	DesactivarBoton("IMG_BUSCAR_PROVEEDOR_OC","IMG_BUSCAR_PROVEEDOR_OC",'buscar');

	xGetElementById("CHECK_REQUISICION_OC").setAttribute('onchange',"");
	xGetElementById("CHECK_DESCUENTO_OC").setAttribute('onchange',"");

	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('onblur',"");
	xGetElementById("DESCUENTO_OC").setAttribute('onblur',"");

	xGetElementById("PORCENTAJE_DESCUENTO_OC").setAttribute('onfocus',"");
	xGetElementById("DESCUENTO_OC").setAttribute('onfocus',"");


	if(xGetElementById("BOTON_AGREGAR_OC"))
		DesactivarBoton("BOTON_AGREGAR_OC","IMG_AGREGAR_OC",'agregar');
	if(xGetElementById("BOTON_QUITAR_OC"))
		DesactivarBoton("BOTON_QUITAR_OC","IMG_QUITAR_OC",'quitar');

 	if(xGetElementById("BOTON_REQUISICIONES_OC"))
		xGetElementById("BOTON_REQUISICIONES_OC").setAttribute('onclick',"");

	xGetElementById("BOTON_CARGOS_OC").setAttribute('onclick',"");

	Form_ORDEN_COMPRA__MostrarTablaArticulos(true);
	}

var Form_ORDEN_COMPRA__SW_PERSONA="P";
function Form_ORDEN_COMPRA__BotonProveedor(bloquear){
	xGetElementById("TIPO_PERSONA_OC").innerHTML="Proveedor";
	xGetElementById("IMG_BUSCAR_PROVEEDOR_OC").setAttribute('onclick',bloquear==true?"":"Form_ORDEN_COMPRA__SeleccionarPersona()");
	xGetElementById("ID_PROVEEDOR_OC").value="";
	xGetElementById("RIF_PROVEEDOR_OC").value="";
	xGetElementById("NOMBRE_PROVEEDOR_OC").value="";
	Form_ORDEN_COMPRA__SW_PERSONA="P";
	}

function Form_ORDEN_COMPRA__BotonBeneficiario(bloquear){
	xGetElementById("TIPO_PERSONA_OC").innerHTML="Beneficiario";
	xGetElementById("IMG_BUSCAR_PROVEEDOR_OC").setAttribute('onclick',bloquear==true?"":"Form_ORDEN_COMPRA__SeleccionarPersona()");
	xGetElementById("ID_PROVEEDOR_OC").value="";
	xGetElementById("RIF_PROVEEDOR_OC").value="";
	xGetElementById("NOMBRE_PROVEEDOR_OC").value="";
	Form_ORDEN_COMPRA__SW_PERSONA="B";
	}


function Form_ORDEN_COMPRA__SeleccionarPersona(){
	siga.onPersona({
    tipo: Form_ORDEN_COMPRA__SW_PERSONA === 'P' ? 'J' : 'N',
    //onList: 'onList',
    onAccept: function(result){
    	xGetElementById("ID_PROVEEDOR_OC").value=result[0]["id"];
			xGetElementById("RIF_PROVEEDOR_OC").value=result[0]["identificacion"];
			xGetElementById("NOMBRE_PROVEEDOR_OC").value=result[0]["denominacion"];
    }
  });
}


//Form_LISTA_CARGOS_MONTO__Abrir
function Form_ORDEN_COMPRA__BaseImponibleIVA(){
	var TOTAL;
	var SUB_TOTAL_TEMP, DESCUENTO, SUB_TOTAL;
	TOTAL=0;
	for(i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador;i++)
		if(Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]==false)//si no esta eliminado
			if(Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true){
				SUB_TOTAL_TEMP=numberFormat(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]*Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"],4);
				DESCUENTO=(((Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]*SUB_TOTAL_TEMP)/100)+Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]*1.00);
				SUB_TOTAL=SUB_TOTAL_TEMP-DESCUENTO;
				TOTAL+=numberFormat(SUB_TOTAL,4)*1.0;
				}
	return TOTAL;
	}









/**
* Activa el boton modificar
*/
function Form_ORDEN_COMPRA__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_OC","IMG_MODIFICAR_OC",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_ORDEN_COMPRA__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_OC","IMG_MODIFICAR_OC",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_ORDEN_COMPRA__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_OC","IMG_GUARDAR_OC",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_ORDEN_COMPRA__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_OC","IMG_GUARDAR_OC",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_ORDEN_COMPRA__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_OC","IMG_ELIMINAR_OC",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_ORDEN_COMPRA__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_OC","IMG_ELIMINAR_OC",'eliminar');
	}

/*Activa el boton imprimir*/
function Form_ORDEN_COMPRA__ActivarBotonImprimir(){
	ActivarBoton("BOTON_IMPRIMIR_OC","IMG_IMPRIMIR_OC",'visualizar');
	}

/*Desactiva el boton imprimir*/
function Form_ORDEN_COMPRA__DesactivarBotonImprimir(){
	DesactivarBoton("BOTON_IMPRIMIR_OC","IMG_IMPRIMIR_OC",'visualizar');
	}

function Form_ORDEN_COMPRA__CambioCheck(){
	var ejecutar="";

	var sw=false;
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista!=-1)
		sw=true;



	if(xGetElementById("CHECK_REQUISICION_OC").checked==true || sw){
		xGetElementById("DIV_BOTON_REQUISICION_FDC").innerHTML="<BUTTON id=\"BOTON_REQUISICIONES_OC\" class=\"BotonesParaCampos\" style=\"font-size : 14px; vertical-align : top;\" type=\"BUTTON\"><IMG id=\"IMG_BUSCAR_R_OC\" src='../../image/icon/icon-find-sigafs.png' width='18' height='18' style=\"vertical-align : middle;\">&nbsp;Requisiciones</BUTTON>";


//		xGetElementById("BOTON_REQUISICIONES_OC").setAttribute('onclick',"Form_LISTA_REQUISICION_BIENES__Abrir('Form_ORDEN_COMPRA__PostAcceptarListaRequisicines',Form_ORDEN_COMPRA__RequisicionesSeleccionadas,Form_ORDEN_COMPRA__NRequisicionesSeleccionadas,false,Form_ORDEN_COMPRA__IDSeleccionActualLista);");
		xGetElementById("BOTON_REQUISICIONES_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__RequisicionListado(false);");
		}
	else{//sino mostrar los botones de + y -
		xGetElementById("DIV_BOTON_REQUISICION_FDC").innerHTML="<BUTTON id=\"BOTON_AGREGAR_OC\" class=\"BotonesParaCampos\" style=\"font-size : 14px; vertical-align : top;\" type=\"BUTTON\"><IMG id=\"IMG_AGREGAR_OC\" src='../../image/icon/icon-listadd-sigafs.png' width='18' height='18' style=\"vertical-align : middle;\">&nbsp;Agregar</BUTTON><BUTTON id=\"BOTON_QUITAR_OC\" class=\"BotonesParaCampos\" style=\"font-size : 14px; vertical-align : top;\" type=\"BUTTON\"><IMG id=\"IMG_QUITAR_OC\" src='../../image/icon/icon-listremove-sigafs.png' width='18' height='18' style=\"vertical-align : middle;\">&nbsp;Quitar&nbsp;&nbsp;</BUTTON>";

		xGetElementById("BOTON_AGREGAR_OC").setAttribute('onclick',"Form_LISTA_ARTICULO__Abrir('COD_ART_AGREGAR_OC','DENOM_ART_AGREGAR_OC','UNID_MED_ART_AGREGAR_OC','Form_ORDEN_COMPRA__AgregarArticuloTabla();','APLICAR_IVA_ART_AGREGAR_OC');");

		xGetElementById("BOTON_QUITAR_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__QuitarArticuloTabla();");


		}
	//Si hago click en el check debe limpiar la tabla de articulos
	Form_ORDEN_COMPRA__NRequisicionesSeleccionadas=0;
	Form_ORDEN_COMPRA__ArregloArticulosContador=0;
	Form_ORDEN_COMPRA__MostrarTablaArticulos();


	}

var Form_ORDEN_COMPRA__id_requisicion_externa_seleccion=[];
function Form_ORDEN_COMPRA__RequisicionListado(_bloquear){	
	siga.open('requisicion_externa/listado',{
		bloquear: _bloquear,
		id_requisicion_externa_seleccion: Form_ORDEN_COMPRA__id_requisicion_externa_seleccion,
		tipo:	Form_ORDEN_COMPRA__tipo,
		id_comprobante: Form_ORDEN_COMPRA__IDSeleccionActualLista,
		onAccept: function(_id_requisicion_externa){
			Form_ORDEN_COMPRA__id_requisicion_externa_seleccion=_id_requisicion_externa;
			
			xGetElementById("CONCEPTO_OC").value="";
			Form_ORDEN_COMPRA__ArregloArticulos=[];
			Form_ORDEN_COMPRA__ArregloArticulosContador=0;
			Form_ORDEN_COMPRA__MostrarTablaArticulos(false);
			
			if(Form_ORDEN_COMPRA__id_requisicion_externa_seleccion.length==0)
				return true;
			
			var resp=Ext.Ajax.request({
				async: false,
				url:"module/requisicion_externa/",
				params: {
					action: 'onGet_Items',
					ids: Ext.encode(_id_requisicion_externa)
				}
			});
			if(resp.statusText=="OK"){
				var retorno=Ext.JSON.decode(resp.responseText);
				
				xGetElementById("CONCEPTO_OC").value="";
				for(var i=0;i<retorno["concepto"].length;i++){
					xGetElementById("CONCEPTO_OC").value+=retorno["concepto"][i][0];
					if(i<retorno["concepto"].length-1){
						xGetElementById("CONCEPTO_OC").value+=" | ";
					}
				}
				
				Form_ORDEN_COMPRA__ArregloArticulos=[];
				Form_ORDEN_COMPRA__ArregloArticulosContador=retorno["items"].length;				
				for(var i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador;i++){
					Form_ORDEN_COMPRA__ArregloArticulos[i]=[];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]=false;//indica si esta eliminado
					Form_ORDEN_COMPRA__ArregloArticulos[i]["id_item"]=retorno["items"][i]["id_item"];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]=retorno["items"][i]['aplica_iva']=="t"?true:false;
					Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]=retorno["items"][i]["codigo"];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["item"]=retorno["items"][i]["item"];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]=retorno["items"][i]["cantidad"];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["id_unidad_medida"]=retorno["items"][i]['id_unidad_medida'];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["medida"]=retorno["items"][i]['medida'];
					Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"]="0.00";//costo
					Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]="0.00";//descuento %
					Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]="0.00";//descuento monto
					}
				//si es orden nueva mostrar el listas desbloqueado
				Form_ORDEN_COMPRA__MostrarTablaArticulos(false);
				
				
				return true;
			}
			return false;
		}
	});
}

var Form_ORDEN_COMPRA__ArregloCargos=[];
function Form_ORDEN_COMPRA__Cargo(_bloqueado){
	siga.open('comprobante/cargo',{
		bloqueado: _bloqueado,
		monto_base: Form_ORDEN_COMPRA__BaseImponible,
		monto_base_iva: Form_ORDEN_COMPRA__BaseImponibleIVA(),
		cargo: Form_ORDEN_COMPRA__ArregloCargos,
		onAccept: function(_arreglo_cargos){			
				Form_ORDEN_COMPRA__ArregloCargos=_arreglo_cargos;				
				Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();
				return true;
		}
	});
}

function Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total(){
	if(xGetElementById("CHECK_DESCUENTO_OC").checked==true){
		Form_ORDEN_COMPRA__PorcentajeDescuento=(xGetElementById("PORCENTAJE_DESCUENTO_OC").value)*1.0;
		Form_ORDEN_COMPRA__Descuento=(xGetElementById("DESCUENTO_OC").value)*1.0;
		var BI=Form_ORDEN_COMPRA__SubTotal-((Form_ORDEN_COMPRA__SubTotal*Form_ORDEN_COMPRA__PorcentajeDescuento/100) +Form_ORDEN_COMPRA__Descuento);
		}
	else{
		var BI=Form_ORDEN_COMPRA__SubTotal;
		}
	Form_ORDEN_COMPRA__BaseImponible=BI;

	xGetElementById("BASE_IMPONIBLE_OC").value=FormatearNumero(BI);
	var MONTO;
	var ACUMULADO_CARGOS=0;

	for(var i=0;i<Form_ORDEN_COMPRA__ArregloCargos.length;i++){
		if(Form_ORDEN_COMPRA__ArregloCargos[i]["iva"]==true){//si es iva
			MONTO=Form_ORDEN_COMPRA__SubTotalIVA;
			}
		else{
			MONTO=BI;
			}
		ACUMULADO_CARGOS+=(eval(Form_ORDEN_COMPRA__ArregloCargos[i]["formula"]))+Form_ORDEN_COMPRA__ArregloCargos[i]["correccion"]*1.00;
		}
	xGetElementById("TOTAL_IMPUESTOS_OC").value=FormatearNumero(ACUMULADO_CARGOS);
	xGetElementById("TOTAL_OC").value=FormatearNumero(BI+ACUMULADO_CARGOS);
	}


/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_ORDEN_COMPRA__IDSeleccionActualLista=-1;

/**
* Nueva definicion
*/
function Form_ORDEN_COMPRA__Nuevo(){
	//busco el n de la orden y lo coloco en el input text
	Form_ORDEN_COMPRA__BotonProveedor();
	AjaxRequest.post({
						'parameters':{
											'action':"onGet_Correlativo",
											'tipo': Form_ORDEN_COMPRA__tipo,
											},
						'onSuccess': function(req){
											var respuesta = req.responseText;
											var resultado = eval("(" + respuesta + ")");
											xGetElementById("CODIGO_OC").value=completarCodigoCeros(String(resultado[0]["correlativo"]),10);
											},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});


	xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="none";
	xGetElementById("BOTON_ANULAR_OC").style.display="none";
	xGetElementById("BOTON_CONTABLIZAR_OC").style.display="none";
	xGetElementById("BOTON_REVERSAR_OC").style.display="none";

	Form_ORDEN_COMPRA__IDComprobante="null";
	Form_ORDEN_COMPRA__IDSeleccionActualLista=-1;
	Form_ORDEN_COMPRA__id_requisicion_externa_seleccion=[];
	Form_ORDEN_COMPRA__NRequisicionesSeleccionadas=0;
	Form_ORDEN_COMPRA__NCargosSeleccionados=0;
	Form_ORDEN_COMPRA__SubTotal=0;
	Form_ORDEN_COMPRA__SubTotalIVA=0;
	Form_ORDEN_COMPRA__PorcentajeDescuento=0;
	Form_ORDEN_COMPRA__Descuento=0;
	Form_ORDEN_COMPRA__ArregloArticulosContador=0;
	Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=-1;
	xGetElementById("CHECK_REQUISICION_OC").checked=true;
	xGetElementById("CHECK_DESCUENTO_OC").checked=true;

	Form_ORDEN_COMPRA__CambioCheck();
	Form_ORDEN_COMPRA__CambioCheckDescuento();
	xGetElementById("BOTON_CARGOS_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__Cargo(true)");

	Form_ORDEN_COMPRA__MostrarTablaArticulos();

	Form_ORDEN_COMPRA__LimpiarInputTextBuscarListado();




	Form_ORDEN_COMPRA__TabPane.setSelectedIndex(0);
	//DarFocoCampo("DENOMINACION_OC",1000);
	}

/*var Form_ORDEN_COMPRA__IDUnidadAdministrativaSeleccionActualLista=-1;
function Form_ORDEN_COMPRA__CargarSelectUnidadAdministrativa(){
	AjaxRequest.post({'parameters':{ 'accion':"Form_ORDEN_COMPRA__BuscarListadoUnidadAdministrativa",
									'Modificar':Form_ORDEN_COMPRA__IDUnidadAdministrativaSeleccionActualLista},
					 'onSuccess':function(req){
									CargarSELECT(req,"UNIDAD_ADMINISTRATIVA_OC", Form_ORDEN_COMPRA__IDUnidadAdministrativaSeleccionActualLista, "id_cod_unidades_administrativas", "denominacion_und_adm",Form_ORDEN_COMPRA__NDigitosCodigoUnidadAdministrativa,30);
									},
					 'url':'../modulo_compras/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}*/

var Form_ORDEN_COMPRA__TempArreglo;
var Form_ORDEN_COMPRA__TempArregloK;

/**
* Guarda los datos en la BD
*/
function Form_ORDEN_COMPRA__Guardar(){
	Form_ORDEN_COMPRA__TabPane.setSelectedIndex(0);
	var _fecha 						= xTrim(strtoupper(xGetElementById("FECHA_OC").value));
	var _id_persona 			= xTrim(strtoupper(xGetElementById("ID_PROVEEDOR_OC").value));
	var _concepto 				= xTrim(strtoupper(xGetElementById("CONCEPTO_OC").value));

	if(!_fecha){
		Form_ORDEN_COMPRA__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_ORDEN_COMPRA__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_ORDEN_COMPRA__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_ORDEN_COMPRA__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	if(!_id_persona){
		var msg="Por favor seleccione el proveedor."
		Form_ORDEN_COMPRA__Mensaje(msg,"ROJO");
		Form_ORDEN_COMPRA__MensajeListado("");
		return;
		}
	if(!_concepto){
		var msg="Por favor introduzca el concepto."
		Form_ORDEN_COMPRA__Mensaje(msg,"ROJO");
		Form_ORDEN_COMPRA__MensajeListado("");
		return;
		}

	var _descuento_porcentaje = "0.00";
	var _descuento_monto			= "0.00";
	if(xGetElementById("CHECK_DESCUENTO_OC").checked==true){
		_descuento_porcentaje = xTrim(strtoupper(xGetElementById("PORCENTAJE_DESCUENTO_OC").value));
		_descuento_monto			= xTrim(strtoupper(xGetElementById("DESCUENTO_OC").value));
		}

	//busca los articulos que esten repetidos
	for(var i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador-1;i++)
		for(var j=i+1;j<Form_ORDEN_COMPRA__ArregloArticulosContador;j++)
			if(Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]==false) 
				if(Form_ORDEN_COMPRA__ArregloArticulos[i]["id_item"]==Form_ORDEN_COMPRA__ArregloArticulos[j]["id_item"]){
					var msg="El artículo "+Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]+" se encuentra repetido. Agrupe y vuelva a intentarlo."
					Form_ORDEN_COMPRA__Mensaje(msg,"ROJO");
					Form_ORDEN_COMPRA__MensajeListado("");
					return;
					}

	var K=0;
	var _detalle={};
	_detalle.item=[];
	for(var i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador;i++)
		if(Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]==false){
			if(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]*1==0){
				var msg="El artículo "+Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]+" tiene cantidad cero."
				Form_ORDEN_COMPRA__Mensaje(msg,"ROJO");
				Form_ORDEN_COMPRA__MensajeListado("");
				return;
				}
			if(Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"]*1==0){
				var msg="El artículo "+Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]+" tiene costo cero."
				Form_ORDEN_COMPRA__Mensaje(msg,"ROJO");
				Form_ORDEN_COMPRA__MensajeListado("");
				return;
				}

			_detalle.item[K]={
				id_item: Form_ORDEN_COMPRA__ArregloArticulos[i]["id_item"],
				cantidad: Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"],
				id_unidad_medida: Form_ORDEN_COMPRA__ArregloArticulos[i]["id_unidad_medida"],
				costo: Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"],
				descuento: xGetElementById("CHECK_DESCUENTO_OC").checked==true?"":('{"porcentaje": '+Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]+', "monto": '+Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]+'}'),
				aplica_iva: Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true?'t':'f'
				};
			K++;
			}

	_detalle.extra={
		descuento_porcentaje: _descuento_porcentaje,
		descuento_monto: _descuento_monto
	};
	
	_detalle.cargo=[];
	for(var i=0;i<Form_ORDEN_COMPRA__ArregloCargos.length;i++){
		_detalle.cargo[i]={
			id_cargo: Form_ORDEN_COMPRA__ArregloCargos[i]["id"],
			monto: Form_ORDEN_COMPRA__ArregloCargos[i]["correccion"]
			};
		}
		
	_detalle.requisicion_externa=[];
	for(var i=0;i<Form_ORDEN_COMPRA__id_requisicion_externa_seleccion.length;i++){
		_detalle.requisicion_externa[i]={
			id_requisicion_externa: Form_ORDEN_COMPRA__id_requisicion_externa_seleccion[i]
			};
		}
	
	Form_ORDEN_COMPRA__DesactivarFormulario();
	var _id_comprobante="";
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_COMPRA__IDSeleccionActualLista;
	

	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_ORDEN_COMPRA__ActivarFormulario();
			return;
			}
		}
	
	var _tipo=Form_ORDEN_COMPRA__tipo;
	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo': _tipo,
										'fecha': _fecha,
										'concepto': _concepto,
										'contabilizado': 'f',
										'id_persona': _id_persona,										
										'detalle': Ext.encode(_detalle)
										},
						'onSuccess': function(req){
										Form_ORDEN_COMPRA__ActivarFormulario();
										Form_ORDEN_COMPRA__GuardarMensaje(req);
										},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}
	
	
/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_ORDEN_COMPRA__GuardarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_ORDEN_COMPRA__Nuevo();
		Form_ORDEN_COMPRA__Mensaje(respuesta.message,"VERDE");
		}
	else{		
		Form_ORDEN_COMPRA__Mensaje(respuesta.message,"ROJO");
		}
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_ORDEN_COMPRA__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_OC").checked){
 		if(ev.keyCode==13)
			Form_ORDEN_COMPRA__BuscarListado();
		return;
		}
	Form_ORDEN_COMPRA__BuscarListado();
	}

function Form_ORDEN_COMPRA__OcultarBotones() {
	xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="none";
	xGetElementById("BOTON_ANULAR_OC").style.display="none";
	xGetElementById("BOTON_CONTABLIZAR_OC").style.display="none";
	xGetElementById("BOTON_REVERSAR_OC").style.display="none";
	xGetElementById("BOTON_VER_OP").style.display="none";
	xGetElementById("BOTON_VER_ANULACION").style.display="none";
}


/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_ORDEN_COMPRA__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_ORDEN_COMPRA__BuscarListado(){
	Form_ORDEN_COMPRA__IDSeleccionActualLista=-1;
	//Form_ORDEN_COMPRA__IDUnidadAdministrativaSeleccionActualLista=-1;
	Form_ORDEN_COMPRA__comprobante_posterior="";
	
	Form_ORDEN_COMPRA__OcultarBotones();

	xGetElementById("FORMULARIO_ORDEN_COMPRA").reset();
	Form_ORDEN_COMPRA__ActivarFormulario();
	Form_ORDEN_COMPRA__DesactivarBotonModificar();
	Form_ORDEN_COMPRA__DesactivarBotonEliminar();
	Form_ORDEN_COMPRA__ActivarBotonGuardar();
	Form_ORDEN_COMPRA__DesactivarBotonImprimir();
	

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_OC").value));


	if(CadenaBuscar!="")
		if(Form_ORDEN_COMPRA__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;

	Form_ORDEN_COMPRA__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_OC").innerHTML=IconoCargandoTabla;

	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_OC").value,
		'tipo':[Form_ORDEN_COMPRA__tipo]
	};

	AjaxRequest.post({
					'parameters':{
									'action':"onList_OC_OS",
									'mostrar': Ext.encode(_mostrar),
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_ORDEN_COMPRA__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/*Muestra el listado (Crea tabla dinamicamente)*/
function Form_ORDEN_COMPRA__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	
	var n=resultado.length;
	//var tablaPrueba = xGetElementById("TABLA_LISTA_OC");

	var CadAux1, CadAux2;

	//var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_OC").value)));
	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_OC").value));
	xGetElementById("TABLA_LISTA_OC").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var color_estado="";
	var estado="";
	for(var i=0;i< n; i++){
		estado="";
		color_estado="";
		if(resultado[i]['contabilizado']=="f" && resultado[i]['detalle_presupuestario']=='t'){
			color_estado="#FFDD00";//amarillo (precontabilizado)
			estado=1;
		}
		else if(resultado[i]['contabilizado']=="t" && resultado[i]['anulado']=='t'){
			color_estado="#000000";//negro (anulado)
			estado=3;
		}
		else if(resultado[i]['contabilizado']=="t"){
			color_estado="#48DC0E";//verde (contabilizado)
			estado=2;
		}
		else{
			color_estado="#FF5454";//rojo (sin contabilizar)
			estado=0;
		}
		
		FuncionOnclick="Form_ORDEN_COMPRA__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
 		FuncionOnDblclick="Form_ORDEN_COMPRA__TabPane.setSelectedIndex(0);";
 		FuncionOnMouseOver="pintarFila(\"OC"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"OC"+resultado[i]['id']+"\")";

		Contenido+="<TR id='OC"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"' title='"+resultado[i]['concepto']+"'>";
		
		
			
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;<DIV></TD>";
		
		if(xGetElementById("SOMBRA_CHECKBOX_OC").checked && TextoBuscar!=""){
			CadAux1=str_replace(resultado[i]['correlativo'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['persona'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux1=resultado[i]['correlativo'];
			CadAux2=resultado[i]['fecha'];
			CadAux3=resultado[i]['concepto'];
			CadAux4=resultado[i]['persona'];
			}

		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux2+"</TD>";
		Contenido+="<TD width='23%' class='FilaEstiloContinua'>"+CadAux4+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' style='padding-left: 5px;'>"+CadAux3+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_OC").innerHTML=Contenido;
	}

/**
* Es llamada cuando se hace click sobre algun elemento de la tabla.
* Esta manda los datos para el formulario que se encuentra en la pestaña 'entrada de datos'
* @param {Integer} IDSeleccion Id del elemento seleccionado
*/
function Form_ORDEN_COMPRA__SeleccionarElementoTabla(IDSeleccion,estado){
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista==IDSeleccion)
		return;
	
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista!=-1)
		xGetElementById("OC"+Form_ORDEN_COMPRA__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("OC"+IDSeleccion).bgColor=colorBase;
	Form_ORDEN_COMPRA__IDSeleccionActualLista=IDSeleccion;
	

	Form_ORDEN_COMPRA__ArregloArticulosContador=0;
	Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=-1;

	
	Form_ORDEN_COMPRA__CambioCheck();
	Form_ORDEN_COMPRA__DesactivarFormulario();

	Form_ORDEN_COMPRA__ActivarBotonImprimir();
	Form_ORDEN_COMPRA__DesactivarBotonGuardar();
	Form_ORDEN_COMPRA__Mensaje("");
	Form_ORDEN_COMPRA__MensajeListado("");
	
	Form_ORDEN_COMPRA__OcultarBotones();
	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_ORDEN_COMPRA__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								//console.log(resultado);
								
								
								xGetElementById("CODIGO_OC").value=resultado[0]["correlativo"];
								xGetElementById("FECHA_OC").value=resultado[0]["fecha"];
								Form_ORDEN_COMPRA__BotonProveedor();
								if(resultado[0]["detalle_persona"]){
									switch(resultado[0]["detalle_persona"][0]["tipo"]){
										case "N":
											Form_ORDEN_COMPRA__SW_PERSONA="B";
											Form_ORDEN_COMPRA__BotonBeneficiario(true);
											break;
										case "J":
										default:
											Form_ORDEN_COMPRA__SW_PERSONA="P";
											Form_ORDEN_COMPRA__BotonProveedor(true);
											break;
									}
								}


								xGetElementById("ID_PROVEEDOR_OC").value=resultado[0]["detalle_persona"][0]["id"];
								xGetElementById("RIF_PROVEEDOR_OC").value=resultado[0]["detalle_persona"][0]["identificacion"];
								xGetElementById("NOMBRE_PROVEEDOR_OC").value=resultado[0]["detalle_persona"][0]["denominacion"];
								xGetElementById("CONCEPTO_OC").value=resultado[0]["concepto"];
								
								//cargar informacion extra
								//porcentaje de descueto y monto descuento
								if(resultado[0]["detalle_extra"]){
									for(var i=0;i<resultado[0]["detalle_extra"].length;i++){
										if(resultado[0]["detalle_extra"][i]["dato"]=="descuento_porcentaje")
											xGetElementById("PORCENTAJE_DESCUENTO_OC").value=resultado[0]["detalle_extra"][i]["valor"];
										else if(resultado[0]["detalle_extra"][i]["dato"]=="descuento_monto")
											xGetElementById("DESCUENTO_OC").value=resultado[0]["detalle_extra"][i]["valor"];
										}
									}
								
								var _descuento="";
								var _descuento_articulo=false;
								Form_ORDEN_COMPRA__ArregloArticulos=[];
								Form_ORDEN_COMPRA__ArregloArticulosContador=resultado[0]["detalle_item"].length;				
								for(var i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador;i++){
									Form_ORDEN_COMPRA__ArregloArticulos[i]=[];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]=false;//indica si esta eliminado
									Form_ORDEN_COMPRA__ArregloArticulos[i]["id_item"]=resultado[0]["detalle_item"][i]["id_item"];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]=resultado[0]["detalle_item"][i]['aplica_iva']=="t"?true:false;
									Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]=resultado[0]["detalle_item"][i]["codigo"];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["item"]=resultado[0]["detalle_item"][i]["item"];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]=resultado[0]["detalle_item"][i]["cantidad"];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["id_unidad_medida"]=resultado[0]["detalle_item"][i]['id_unidad_medida'];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["medida"]=resultado[0]["detalle_item"][i]['medida'];
									Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"]=resultado[0]["detalle_item"][i]["costo"];//costo
									Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]="0.00";//descuento %
									Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]="0.00";//descuento monto
									if(resultado[0]["detalle_item"][i]["descuento"]){
										_descuento_articulo=true;
										_descuento=Ext.decode(resultado[0]["detalle_item"][i]["descuento"]);
										Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]=_descuento.porcentaje;//descuento %
										Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]=_descuento.monto;//descuento monto										
										}									
									}
								
								//si es descuento por articulo
								if(_descuento_articulo==true)
									xGetElementById("CHECK_DESCUENTO_OC").checked=false;
								else
									xGetElementById("CHECK_DESCUENTO_OC").checked=true;
								Form_ORDEN_COMPRA__CambioCheckDescuento();
								
								//mostrar el listado de articulos bloqueado
								Form_ORDEN_COMPRA__MostrarTablaArticulos(true);
								
								//cargos asociados
								Form_ORDEN_COMPRA__ArregloCargos=[];
								for(var i=0;i<resultado[0]["detalle_cargo"].length;i++){
									Form_ORDEN_COMPRA__ArregloCargos[i]=[];
									Form_ORDEN_COMPRA__ArregloCargos[i]["id"]=resultado[0]["detalle_cargo"][i]["id_cargo"];
									Form_ORDEN_COMPRA__ArregloCargos[i]["formula"]=resultado[0]["detalle_cargo"][i]["formula"];
									Form_ORDEN_COMPRA__ArregloCargos[i]["correccion"]=resultado[0]["detalle_cargo"][i]["monto"];
									Form_ORDEN_COMPRA__ArregloCargos[i]["iva"]=resultado[0]["detalle_cargo"][i]["iva"]=='t'?true:false;
									}
								Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();
								
								//requisiciones asociadas								
								Form_ORDEN_COMPRA__id_requisicion_externa_seleccion=[];
								for(var i=0;i<resultado[0]["detalle_requisicion_externa"].length;i++)
									Form_ORDEN_COMPRA__id_requisicion_externa_seleccion.push(resultado[0]["detalle_requisicion_externa"][i]["id_requisicion_externa"]);
								
								//detalle presupuestarios obtenidos, necesario para contabilizar
								Form_ORDEN_COMPRA__detalle_presupuestario=resultado[0]["detalle_presupuestario"];
								
								//activar los botones para solo lectura
								xGetElementById("BOTON_REQUISICIONES_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__RequisicionListado(true)");
								xGetElementById("BOTON_CARGOS_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__Cargo(true)");
								
								Form_ORDEN_COMPRA__OcultarBotones();
								Form_ORDEN_COMPRA__DesactivarBotonModificar();
								Form_ORDEN_COMPRA__DesactivarBotonEliminar();
								Form_ORDEN_COMPRA__DesactivarBotonGuardar();
								
								Form_ORDEN_COMPRA__comprobante_posterior=[];
								
								var _tmp_id=[];
								if(resultado[0]["detalle_comprobante_posterior"]){
									if(resultado[0]["detalle_comprobante_posterior"].length>0){
                    for(var m=0;m<resultado[0]["detalle_comprobante_posterior"].length;m++)
											_tmp_id.push(resultado[0]["detalle_comprobante_posterior"][m][0]);
										
										Form_ORDEN_COMPRA__comprobante_posterior=siga.onGetComprobante({id: _tmp_id, detalle: false});
										
										for(var m=0;m<Form_ORDEN_COMPRA__comprobante_posterior.length;m++){
											switch(Form_ORDEN_COMPRA__comprobante_posterior[m]["tipo"]) {
												case "OP": xGetElementById("BOTON_VER_OP").style.display=""; break;
												case "CA": xGetElementById("BOTON_VER_ANULACION").style.display=""; break;
											}										
										}
										estado=4;
                  }
								}
								
								switch(estado){
									//SIN CONTABILIZAR (ROJO)
									case 0://activar el boton de modificar, mostrar el boton de precontabilizar															
										Form_ORDEN_COMPRA__ActivarBotonModificar();
										xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="";
										break;
									//PRECONTABILIZADO (AMARILLO)
									case 1:
										xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="";										
										xGetElementById("BOTON_CONTABLIZAR_OC").style.display="";
										break;
									//CONTABILIZADO (VERDE)
									case 2://mostrar el boton de reversar y anular
										xGetElementById("BOTON_REVERSAR_OC").style.display="";
										xGetElementById("BOTON_ANULAR_OC").style.display="";
										break;
									case 3://ANULADO
										//xGetElementById("BOTON_VER_ANULACION").style.display="";
										break;									
								}
								
							},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}


/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_ORDEN_COMPRA__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_OC").value="";
	Form_ORDEN_COMPRA__Mensaje("");
	Form_ORDEN_COMPRA__MensajeListado("");
	Form_ORDEN_COMPRA__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_OC",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_ORDEN_COMPRA__Modificar(){
	Form_ORDEN_COMPRA__ActivarFormulario();
	Form_ORDEN_COMPRA__ActivarBotonGuardar();
	Form_ORDEN_COMPRA__DesactivarBotonModificar();
	
	Form_ORDEN_COMPRA__MostrarTablaArticulos(false);
	//activar los botones para modificar
	xGetElementById("BOTON_REQUISICIONES_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__RequisicionListado(false)");
	xGetElementById("BOTON_CARGOS_OC").setAttribute('onclick',"Form_ORDEN_COMPRA__Cargo(false)");
	
	xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="none";
	xGetElementById("BOTON_ANULAR_OC").style.display="none";
	xGetElementById("BOTON_REVERSAR_OC").style.display="none";
	
	
	
	
	Form_ORDEN_COMPRA__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_ORDEN_COMPRA__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;

	AjaxRequest.post({'parameters':{ 'accion':"Form_ORDEN_COMPRA__Eliminar",
									'id_orden_de_compra':Form_ORDEN_COMPRA__IDSeleccionActualLista},
					 'onSuccess':Form_ORDEN_COMPRA__EliminarMensaje,
					 'url':'../modulo_compras/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_ORDEN_COMPRA__EliminarMensaje(req){
	var respuesta = req.responseText;
	if(respuesta==1){
		Form_ORDEN_COMPRA__ArregloArticulosContador=0;
		Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=-1;
		Form_ORDEN_COMPRA__MostrarTablaArticulos();

		Form_ORDEN_COMPRA__LimpiarInputTextBuscarListado();
		Form_ORDEN_COMPRA__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_ORDEN_COMPRA__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		Form_ORDEN_COMPRA__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		Form_ORDEN_COMPRA__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		}
	}


function Form_ORDEN_COMPRA__MostrarTablaArticulos(Bloquear){
	if(!Bloquear)
		Bloquear=false;
	else
		Bloquear=true;

	//si tenemos seleccionado una contabilizada, bloqueamos la tabla
	if(Form_ORDEN_COMPRA__IDComprobante!="null")
		Bloquear=true;

	xGetElementById("TABLA_LISTA_ARTICULOS_OC").innerHTML="";
	var aux="";
	var FuncionOnChangeCheck="";
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var FuncionOnDblclickTDCantidad="";
	var FuncionOnDblclickTDPrecio="";
	var Cantidad;
 	var Costo;
	var TOTAL=0;
	var TOTAL_IVA=0;



	for(i=0;i<Form_ORDEN_COMPRA__ArregloArticulosContador;i++){
		if(Form_ORDEN_COMPRA__ArregloArticulos[i]["eliminado"]==false){
			if(xGetElementById("CHECK_DESCUENTO_OC").checked==true){
				if(Bloquear){
					DisabledOnChangeCheck=" disabled ";
					FuncionOnclick="";
					FuncionOnDblclick="";
					FuncionOnMouseOver="";
					FuncionOnMouseOut="";
					FuncionOnDblclickTDCantidad="";
					FuncionOnDblclickTDPrecio="";
					}
				else{
					DisabledOnChangeCheck="";
					FuncionOnclick="Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos('"+i+"')";
					//FuncionOnDblclick="Form_ORDEN_COMPRA__ModificarArticuloTabla();";
					FuncionOnMouseOver="pintarFila(\"OC_A"+i+"\")";
					FuncionOnMouseOut="despintarFila(\"OC_A"+i+"\")";
					if(xGetElementById("CHECK_REQUISICION_OC").checked==true)//si proviene de requisiciones bloquear cantidad
						FuncionOnDblclickTDCantidad="";
					else
						FuncionOnDblclickTDCantidad="Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_CANTIDAD_"+i+"','cantidad')";
					FuncionOnDblclickTDPrecio=      "Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_PRECIO___"+i+"','costo')";
					}

				Contenido+="<TR id='OC_A"+i+"' onclick=\""+FuncionOnclick+"\" onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
				aux="";
				if(Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true)
					aux=" checked ";
				Contenido+="<TD width='1%' class='FilaEstilo'><INPUT type='checkbox' id='OC_CHECK_ARTICULO_IVA_"+i+"' "+aux+" onchange='Form_ORDEN_COMPRA__CambioCheckAplicaIVA("+i+")' "+DisabledOnChangeCheck+"></TD>";
				Contenido+="<TD width='5%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>"+Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]+"</TD>";
				Contenido+="<TD width='26%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>" +Form_ORDEN_COMPRA__ArregloArticulos[i]["item"]+"</TD>";
				Contenido+="<TD id='OC_A_CANTIDAD_"+i+"' width='13%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTDCantidad+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"])+"</TD>";
				Contenido+="<TD width='22%' class='FilaEstilo' align='center' ondblclick='"+FuncionOnDblclick+"'>" +Form_ORDEN_COMPRA__ArregloArticulos[i]["medida"]+"</TD>";
				Contenido+="<TD id='OC_A_PRECIO___"+i+"' width='15%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTDPrecio+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"])+"</TD>";

				SUB_TOTAL=numberFormat(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]*Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"],4);
				TOTAL+=numberFormat(SUB_TOTAL,4)*1.0;
				if(Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true)
					TOTAL_IVA+=numberFormat(SUB_TOTAL,4)*1.0;

				Contenido+="<TD width='18%' class='FilaEstilo' align='right'>"+FormatearNumero(SUB_TOTAL)+"</TD>";

				Contenido+="</TR>";
				}//fin si checked 
			else{
				if(Bloquear){
					DisabledOnChangeCheck=" disabled ";
					FuncionOnclick="";
					FuncionOnDblclick="";
					FuncionOnMouseOver="";
					FuncionOnMouseOut="";
					FuncionOnDblclickTDCantidad="";
					FuncionOnDblclickTDPrecio="";
					FuncionOnDblclickTD_PDescuento= "";
					FuncionOnDblclickTD_Descuento=  "";
					}
				else{
					DisabledOnChangeCheck="";
					FuncionOnclick="Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos('"+i+"')";
					FuncionOnMouseOver="pintarFila(\"OC_A"+i+"\")";
					FuncionOnMouseOut="despintarFila(\"OC_A"+i+"\")";
					if(xGetElementById("CHECK_REQUISICION_OC").checked==true)//si proviene de requisiciones bloquear cantidad
						FuncionOnDblclickTDCantidad="";
					else
						FuncionOnDblclickTDCantidad="Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_CANTIDAD_"+i+"','cantidad')";
					FuncionOnDblclickTDPrecio=      "Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_PRECIO___"+i+"','costo')";

					FuncionOnDblclickTD_PDescuento= "Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_P_DESC___"+i+"','descuento_p')";
					FuncionOnDblclickTD_Descuento=  "Form_ORDEN_COMPRA__ModificarValorCelda('OC_A_DESC_____"+i+"','descuento_m')";
					}

				Contenido+="<TR id='OC_A"+i+"' onclick=\""+FuncionOnclick+"\" onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

				aux="";
				if(Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true)
					aux=" checked ";
				Contenido+="<TD width='1%' class='FilaEstilo'><INPUT type='checkbox' id='OC_CHECK_ARTICULO_IVA_"+i+"' "+aux+" onchange='Form_ORDEN_COMPRA__CambioCheckAplicaIVA("+i+")' "+DisabledOnChangeCheck+"></TD>";
				Contenido+="<TD width='3%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>" +Form_ORDEN_COMPRA__ArregloArticulos[i]["codigo"]+"</TD>";
				Contenido+="<TD width='23%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>" +Form_ORDEN_COMPRA__ArregloArticulos[i]["item"]+"</TD>";
				Contenido+="<TD id='OC_A_CANTIDAD_"+i+"' width='10%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTDCantidad+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"])+"</TD>";
				Contenido+="<TD width='18%' class='FilaEstilo' align='center' ondblclick='"+FuncionOnDblclick+"'>" +Form_ORDEN_COMPRA__ArregloArticulos[i]["medida"]+"</TD>";
				Contenido+="<TD id='OC_A_PRECIO___"+i+"' width='12%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTDPrecio+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"])+"</TD>";
				Contenido+="<TD id='OC_A_P_DESC___"+i+"' width='8%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTD_PDescuento+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"])+"</TD>";
				Contenido+="<TD id='OC_A_DESC_____"+i+"' width='10%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTD_Descuento+"\">"+FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"])+"</TD>";

				SUB_TOTAL_TEMP=numberFormat(Form_ORDEN_COMPRA__ArregloArticulos[i]["cantidad"]*Form_ORDEN_COMPRA__ArregloArticulos[i]["costo"],4);
				DESCUENTO=(numberFormat((Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_p"]*SUB_TOTAL_TEMP)/100,4)*1.0+Form_ORDEN_COMPRA__ArregloArticulos[i]["descuento_m"]*1.00);
				SUB_TOTAL=SUB_TOTAL_TEMP-DESCUENTO;

				TOTAL+=numberFormat(SUB_TOTAL,4)*1.0;
				if(Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]==true)
					TOTAL_IVA+=numberFormat(SUB_TOTAL,4)*1.0;

				Contenido+="<TD width='15%' class='FilaEstilo' align='right'>"+FormatearNumero(SUB_TOTAL)+"</TD>";

				Contenido+="</TR>";
				}//fin else
			}//fin if    [i][5]==true
		}//fin for
	xGetElementById("TABLA_LISTA_ARTICULOS_OC").innerHTML=Contenido;

	Form_ORDEN_COMPRA__SubTotal=TOTAL;
	Form_ORDEN_COMPRA__SubTotalIVA=TOTAL_IVA;
	xGetElementById("SUB_TOTAL_OC").value=FormatearNumero(TOTAL);
	Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();
	}



function Form_ORDEN_COMPRA__ModificarValorCelda(_IDCelda,columna_arreglo){
	if(xGetElementById("txt_celda_"+_IDCelda))
		return;
	var Valor=Form_ORDEN_COMPRA__ArregloArticulos[Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos][columna_arreglo];
	if(Valor==0)
		Valor="";
	xGetElementById(_IDCelda).innerHTML="<INPUT id='txt_celda_"+_IDCelda+"' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco('"+_IDCelda+"',"+Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos+",'"+columna_arreglo+"')\" onkeypress=\"return AcceptNum(event,'txt_celda_"+_IDCelda+"');\" style='text-align : right;' onkeyup=\"Form_ORDEN_COMPRA__KeyPressEnter(event,'"+_IDCelda+"',"+Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos+",'"+columna_arreglo+"')\">";
	xGetElementById("txt_celda_"+_IDCelda).focus();
	}
function Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo){
	if(!xGetElementById("txt_celda_"+_IDCelda))
		return;
	xGetElementById("txt_celda_"+_IDCelda).onblur="";
	var Valor=xGetElementById("txt_celda_"+_IDCelda).value;
	if(Valor=="")
		Valor="0.00";
	Form_ORDEN_COMPRA__ArregloArticulos[indice_modificar][columna_arreglo]=numberFormat(Valor,4);
	xGetElementById(_IDCelda).innerHTML=FormatearNumero(Form_ORDEN_COMPRA__ArregloArticulos[indice_modificar][columna_arreglo]);
	Form_ORDEN_COMPRA__MostrarTablaArticulos(false);
	}
function Form_ORDEN_COMPRA__KeyPressEnter(event,_IDCelda,indice_modificar,columna_arreglo){
	if(event.keyCode==13 || event.keyCode==40){
		if(columna_arreglo=="descuento_p"||columna_arreglo=="descuento_m"){
			Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo);
			return;
			}

		if(xGetElementById("CHECK_REQUISICION_OC").checked==true){
			if(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos==String(Form_ORDEN_COMPRA__ArregloArticulosContador-1)){
				if(event.keyCode==13)
					Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo);
				return;
				}
			Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo);
			var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length))+1;
			Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(parseInt(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos)+1));
			Form_ORDEN_COMPRA__ModificarValorCelda(_IDCelda.substring(0,14)+fila_actual,columna_arreglo);
			}
		else{
			var ID_COL_ACTUAL=_IDCelda.substring(0,14);
			if(ID_COL_ACTUAL=="OC_A_CANTIDAD"){//si estamos en cantidad y que pasar pa precio
				Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,'cantidad');
				var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length));
				Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos));
				Form_ORDEN_COMPRA__ModificarValorCelda("OC_A_PRECIO__"+fila_actual,'costo');
				}
			else{//si es precio
				if(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos==String(Form_ORDEN_COMPRA__ArregloArticulosContador-1)){
					if(event.keyCode==13)
						Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,'costo');
					return;
					}
				Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,'costo');
				var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length))+1;
				Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(parseInt(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos)+1));
				Form_ORDEN_COMPRA__ModificarValorCelda("OC_A_CANTIDAD"+fila_actual,'cantidad');
				}
			}
		}
	else if(event.keyCode==38){
		if(columna_arreglo==6||columna_arreglo==7){
			Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo);
			return;
			}
		if(xGetElementById("CHECK_REQUISICION_OC").checked==true){
			if(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=='0')
				return;
			Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,columna_arreglo);
			var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length))-1;
			Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(parseInt(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos)-1));
			Form_ORDEN_COMPRA__ModificarValorCelda(_IDCelda.substring(0,14)+fila_actual,columna_arreglo);
			}
		else{
			var ID_COL_ACTUAL=_IDCelda.substring(0,14);
			if(ID_COL_ACTUAL=="OC_A_CANTIDAD"){//si estamos en cantidad y que pasar pa precio
				if(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=='0')
					return;
				Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,'cantidad');
				var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length))-1;
				Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(parseInt(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos)-1));
				Form_ORDEN_COMPRA__ModificarValorCelda("OC_A_PRECIO__"+fila_actual,'costo');
				}
			else{//si es precio
				Form_ORDEN_COMPRA__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar,'costo');
				var fila_actual=parseInt(_IDCelda.substring(14,_IDCelda.length));
				Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(String(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos));
				Form_ORDEN_COMPRA__ModificarValorCelda("OC_A_CANTIDAD"+fila_actual,'cantidad');
				}
			}
		}
	}

function Form_ORDEN_COMPRA__CambioCheckAplicaIVA(i){
	Form_ORDEN_COMPRA__ArregloArticulos[i]["aplica_iva"]=xGetElementById("OC_CHECK_ARTICULO_IVA_"+i).checked;
	Form_ORDEN_COMPRA__MostrarTablaArticulos();//Form_ORDEN_COMPRA__CalcularBaseImponible_Cargos_Total();
	}

var Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=-1;
function Form_ORDEN_COMPRA__SeleccionarElementoTablaArticulos(IDSeleccion){
	if(Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos!=-1)
		xGetElementById("OC_A"+Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("OC_A"+IDSeleccion).bgColor=colorBase;
	Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos=IDSeleccion;
	}

function Form_ORDEN_COMPRA__ModificarArticuloTabla(){
	Form_ORDEN_COMPRA__Mensaje('');
	Form_ORDEN_COMPRA__MensajeListado('');
	var Ejecutar="Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos="+Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos+"; Form_ORDEN_COMPRA__ModificarArticuloTablaPostAceptar();"

	Form_LISTA_ARTICULO__Abrir( 'COD_ART_AGREGAR_OC',
								'DENOM_ART_AGREGAR_OC',
								'UNID_MED_ART_AGREGAR_OC',
								Ejecutar,
								'APLICAR_IVA_ART_AGREGAR_OC');
	}

function Form_ORDEN_COMPRA__ModificarArticuloTablaPostAceptar(){
	if(xGetElementById("COD_ART_AGREGAR_OC").value=="")//en caso que no se seleccione ninguno y le damos aceptar, dejamos el mismo.
		return;
	var i=Form_ORDEN_COMPRA__IDSeleccionActualListaArticulos;
	Form_ORDEN_COMPRA__ArregloArticulos[i][0]=xGetElementById("COD_ART_AGREGAR_OC").value;
	Form_ORDEN_COMPRA__ArregloArticulos[i][1]=xGetElementById("DENOM_ART_AGREGAR_OC").value;
	Form_ORDEN_COMPRA__ArregloArticulos[i][3]=xGetElementById("UNID_MED_ART_AGREGAR_OC").value;
	Form_ORDEN_COMPRA__MostrarTablaArticulos();
	}

function Form_ORDEN_COMPRA__PostAcceptarListaRequisicines(Arreglo,N,Conceptos){
	Form_ORDEN_COMPRA__RequisicionesSeleccionadas=copy(Arreglo);
	Form_ORDEN_COMPRA__NRequisicionesSeleccionadas=N;

	if(xTrim(xGetElementById("CONCEPTO_OC").value)=="")
		xGetElementById("CONCEPTO_OC").value=Conceptos;

	AjaxRequest.post({'parameters':{ 'accion':"Form_ORDEN_COMPRA__BuscarRequisicionesFusionar",
									'ARREGLO':Arreglo,
									'TAM_ARREGLO':N},
						'onSuccess':Form_ORDEN_COMPRA__CargarArregloArticulos_Requisicion,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}


function Form_ORDEN_COMPRA__CambioCheckDescuento(){
	if(xGetElementById("CHECK_DESCUENTO_OC").checked==true){
		xGetElementById("TITULOS_TABLA_ARTICULOS_OC").innerHTML="<tbody><tr class='CabeceraTablaEstilo'><td width='1%'>IVA</td><td width='5%'>COD.</td><td width='26%'>DENOMINACI&Oacute;N</td><td width='13%'>CANTIDAD</td><td width='22%'>PRESENTACI&Oacute;N</td><td width='15%'>PRECIO</td><td width='18%'>SUB-TOTAL</td></tr></tbody>";
		xGetElementById("FILA_DESCUENTO_OC_1").style.display="";
		xGetElementById("FILA_DESCUENTO_OC_2").style.display="";
		xGetElementById("FILA_DESCUENTO_OC_3").style.display="";
		}
	else{
		xGetElementById("TITULOS_TABLA_ARTICULOS_OC").innerHTML="<tbody><tr class='CabeceraTablaEstilo'><td width='1%'>IVA</td><td width='3%'>COD.</td><td width='23%'>DENOMINACI&Oacute;N</td><td width='10%'>CANTIDAD</td><td width='18%'>PRESENTACI&Oacute;N</td><td width='12%'>PRECIO</td><td width='8%' align='right'>%&nbsp;&nbsp;</td><td width='10%' align='left'>DESCUENTO</td><td width='15%'>SUB-TOTAL</td></tr></tbody>";
		xGetElementById("FILA_DESCUENTO_OC_1").style.display="none";
		xGetElementById("FILA_DESCUENTO_OC_2").style.display="none";
		xGetElementById("FILA_DESCUENTO_OC_3").style.display="none";
		}
	}




function Form_ORDEN_COMPRA__Imprimir(){
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista==-1)//nunca deberia cumpliser porque el boton esta desactivado
		return;
	window.open("../../report/orden_compra.php?id="+Form_ORDEN_COMPRA__IDSeleccionActualLista);
}

function Form_ORDEN_COMPRA__VerComprobanteAsociado(tipo){//tipo=OP - CA
	if(Form_ORDEN_COMPRA__comprobante_posterior.length==0)//nunca deberia cumpliser porque el boton esta desactivado
		return;
	var pos=-1;
	for(var m=0;m<Form_ORDEN_COMPRA__comprobante_posterior.length;m++){
		if(Form_ORDEN_COMPRA__comprobante_posterior[m]["tipo"]==tipo)
      pos=m;
	}
	if(tipo=="OP")	
		window.open("../../report/orden_pago.php?id="+Form_ORDEN_COMPRA__comprobante_posterior[pos]["id"]);
	if(tipo=="CA")	
		window.open("../../report/comprobante.php?id="+Form_ORDEN_COMPRA__comprobante_posterior[pos]["id"]);
}
	
function Form_ORDEN_COMPRA__PreContabilizar(_bloqueado){
	Form_ORDEN_COMPRA__Mensaje("");
	siga.open('orden_compra/precontabilizar',{
		bloqueado: false,
		id_comprobante: Form_ORDEN_COMPRA__IDSeleccionActualLista,
		detalle_presupuestario: Form_ORDEN_COMPRA__detalle_presupuestario,
		onAccept: function(_resultado){			
			var _id_comprobante="";
			if(Form_ORDEN_COMPRA__IDSeleccionActualLista>0) 
				_id_comprobante=Form_ORDEN_COMPRA__IDSeleccionActualLista;
			else
				return;
			
			//ocultar el boton de contabilizar
			Form_ORDEN_COMPRA__OcultarBotones();
			
			var _tipo=Form_ORDEN_COMPRA__tipo;
			var _fecha 						= xTrim(strtoupper(xGetElementById("FECHA_OC").value));
			_fecha=DesFormatearFecha(_fecha);
			var _id_persona 			= xTrim(strtoupper(xGetElementById("ID_PROVEEDOR_OC").value));
			var _concepto 				= xTrim(strtoupper(xGetElementById("CONCEPTO_OC").value));
			var _detalle={};
			
			_detalle.presupuestario=[];
			var sw=false;
			var k=0;
			for(var i=0;i<_resultado.length;i++){
				sw=false;
				for(var j=0;j<k;j++)
					if(_resultado[i]["id_accion_subespecifica"]==_detalle.presupuestario[j].id_accion_subespecifica &&
						 _resultado[i]["id_cuenta_presupuestaria"]==_detalle.presupuestario[j].id_cuenta_presupuestaria){
						_detalle.presupuestario[j].monto+=numberFormat(_resultado[i]["total"],2)*1.0;
						sw=true;						
					}
				if(sw==true)
					continue;
				_detalle.presupuestario[k]={
					id_accion_subespecifica: _resultado[i]["id_accion_subespecifica"],
					id_cuenta_presupuestaria: _resultado[i]["id_cuenta_presupuestaria"],
					operacion: "NN",
					monto: numberFormat(_resultado[i]["total"],2)*1.0
				};
				k++;
			}
	
			AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo': _tipo,
										'fecha': _fecha,
										'concepto': _concepto,
										'contabilizado': 'f',
										'id_persona': _id_persona,
										'detalle': Ext.encode(_detalle)
										},
						'onSuccess': function(req){
										Form_ORDEN_COMPRA__GuardarMensaje(req);
										siga.close('orden_compra/precontabilizar');
										},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});

		}
	});
}

function Form_ORDEN_COMPRA__Contabilizar(){
	Form_ORDEN_COMPRA__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_COMPRA__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_ORDEN_COMPRA__OcultarBotones();

	var _tipo=Form_ORDEN_COMPRA__tipo;
	var _fecha 						= xTrim(strtoupper(xGetElementById("FECHA_OC").value));
	_fecha=DesFormatearFecha(_fecha);
	var _id_persona 			= xTrim(strtoupper(xGetElementById("ID_PROVEEDOR_OC").value));
	var _concepto 				= xTrim(strtoupper(xGetElementById("CONCEPTO_OC").value));
	var _detalle={};
	
	_detalle.presupuestario=[];
	for(var i=0;i<Form_ORDEN_COMPRA__detalle_presupuestario.length;i++){
		_detalle.presupuestario[i]={
						id_accion_subespecifica: Form_ORDEN_COMPRA__detalle_presupuestario[i]["id_accion_subespecifica"],
						id_cuenta_presupuestaria: Form_ORDEN_COMPRA__detalle_presupuestario[i]["id_cuenta_presupuestaria"],
						operacion: "C",
						monto: Form_ORDEN_COMPRA__detalle_presupuestario[i]["monto"]
					};
	}
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSave",
								'id': _id_comprobante,
								'tipo': _tipo,
								'fecha': _fecha,
								'concepto': _concepto,
								'contabilizado': 't',
								'id_persona': _id_persona,
								'detalle': Ext.encode(_detalle)
								},
				'onSuccess': function(req){
								xGetElementById("BOTON_PRECONTABLIZAR_OC").style.display="";										
								xGetElementById("BOTON_CONTABLIZAR_OC").style.display="";
								Form_ORDEN_COMPRA__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

function Form_ORDEN_COMPRA__Reversar(){
	Form_ORDEN_COMPRA__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_COMPRA__IDSeleccionActualLista;
	else
		return;
	
	Form_ORDEN_COMPRA__OcultarBotones();
	
	var _tipo=Form_ORDEN_COMPRA__tipo;
	var _fecha 						= xTrim(strtoupper(xGetElementById("FECHA_OC").value));
	_fecha=DesFormatearFecha(_fecha);
	var _id_persona 			= xTrim(strtoupper(xGetElementById("ID_PROVEEDOR_OC").value));
	var _concepto 				= xTrim(strtoupper(xGetElementById("CONCEPTO_OC").value));
	var _detalle={};
	
	_detalle.presupuestario=[];
	for(var i=0;i<Form_ORDEN_COMPRA__detalle_presupuestario.length;i++){
		_detalle.presupuestario[i]={
						id_accion_subespecifica: Form_ORDEN_COMPRA__detalle_presupuestario[i]["id_accion_subespecifica"],
						id_cuenta_presupuestaria: Form_ORDEN_COMPRA__detalle_presupuestario[i]["id_cuenta_presupuestaria"],
						operacion: "NN",
						monto: Form_ORDEN_COMPRA__detalle_presupuestario[i]["monto"]
					};
	}
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSave",
								'id': _id_comprobante,
								'tipo': _tipo,
								'fecha': _fecha,
								'concepto': _concepto,
								'contabilizado': 'f',
								'id_persona': _id_persona,
								'detalle': Ext.encode(_detalle)
								},
				'onSuccess': function(req){
								Form_ORDEN_COMPRA__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
}


function Form_ORDEN_COMPRA__Anular(){
	Form_ORDEN_COMPRA__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_COMPRA__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_COMPRA__IDSeleccionActualLista;
	else
		return;
	

	//pedir fecha de anulacion
	var _fecha=xGetElementById("FECHA_ACTUAL_SIGAFS").value;
	while(true){
		_fecha=prompt("Introduzca la fecha de anulación (DD/MM/AAAA).", _fecha);
		if(_fecha==null)//si es cancelar
			return;
		if(EsFechaValida(_fecha))//si es valida
			break;
		alert("La fecha introducida es invalida.");
		}
	_fecha=DesFormatearFecha(_fecha);
	
	Form_ORDEN_COMPRA__Mensaje("Anulando. Por favor espere...");
	Form_ORDEN_COMPRA__OcultarBotones();

	AjaxRequest.post({
				'parameters':{
								'action':"onAnular",
								'id': _id_comprobante,
								'fecha': _fecha
								},
				'onSuccess': function(req){
								Form_ORDEN_COMPRA__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
}
