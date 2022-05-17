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

/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_REQUISICION__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FR").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_REQUISICION__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FR_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_REQUISICION__ActivarFormulario(){
	xGetElementById("FECHA_FR").readOnly=false;
	xGetElementById("AC_FR").readOnly=false;
	xGetElementById("AC_FR").disabled=false;
	xGetElementById("AE_FR").readOnly=false;
	xGetElementById("AE_FR").disabled=false;
	xGetElementById("OAE_FR").readOnly=false;
	xGetElementById("OAE_FR").disabled=false;
	xGetElementById("TIPO_FR").readOnly=false;
	xGetElementById("TIPO_FR").disabled=false;
	xGetElementById("CONCEPTO_FR").readOnly=false;


	xGetElementById("FECHA_FR").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("AC_FR").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("AE_FR").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("OAE_FR").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("TIPO_FR").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CONCEPTO_FR").setAttribute('class','TextoCampoInputObligatorios');

	ActivarBoton("BOTON_AGREGAR_FR","IMG_AGREGAR_FR",'agregar');
	ActivarBoton("BOTON_QUITAR_FR","IMG_QUITAR_FR",'quitar');

	xGetElementById("FECHA_FR").setAttribute('ondblclick',"showCalendar('FECHA_FR','%d/%m/%Y');");
	xGetElementById("IMG_FECHA_FR").setAttribute('onclick',"showCalendar('FECHA_FR','%d/%m/%Y');");

	ActivarBoton("IMG_FECHA_FR","IMG_FECHA_FR",'calendario');


	Form_REQUISICION__MostrarTablaArticulos(false);
	//Form_REQUISICION__CargarSelectUnidadAdministrativa();

	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_REQUISICION__DesactivarFormulario(){
	xGetElementById("FECHA_FR").readOnly=true;
	xGetElementById("AC_FR").readOnly=true;
	xGetElementById("AC_FR").disabled=true;
	xGetElementById("AE_FR").readOnly=true;
	xGetElementById("AE_FR").disabled=true;
	xGetElementById("OAE_FR").readOnly=true;
	xGetElementById("OAE_FR").disabled=true;
	xGetElementById("TIPO_FR").readOnly=true;
	xGetElementById("TIPO_FR").disabled=true;
	xGetElementById("CONCEPTO_FR").readOnly=true;


	xGetElementById("FECHA_FR").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("AC_FR").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("AE_FR").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("OAE_FR").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TIPO_FR").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_FR").setAttribute('class','TextoCampoInputDesactivado');

	DesactivarBoton("BOTON_AGREGAR_FR","IMG_AGREGAR_FR",'agregar');
	DesactivarBoton("BOTON_QUITAR_FR","IMG_QUITAR_FR",'quitar');

	xGetElementById("FECHA_FR").setAttribute('ondblclick',"");
	xGetElementById("IMG_FECHA_FR").setAttribute('onclick',"");

	DesactivarBoton("IMG_FECHA_FR","IMG_FECHA_FR",'calendario');

	Form_REQUISICION__MostrarTablaArticulos(true);
	}

/**
* Activa el boton modificar
*/
function Form_REQUISICION__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FR","IMG_MODIFICAR_FR",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_REQUISICION__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FR","IMG_MODIFICAR_FR",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_REQUISICION__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FR","IMG_GUARDAR_FR",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_REQUISICION__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FR","IMG_GUARDAR_FR",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_REQUISICION__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FR","IMG_ELIMINAR_FR",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_REQUISICION__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FR","IMG_ELIMINAR_FR",'eliminar');
	}

/*Activa el boton imprimir*/
function Form_REQUISICION__ActivarBotonImprimir(){
	ActivarBoton("BOTON_IMPRIMIR_FR","IMG_IMPRIMIR_FR",'visualizar');
	}

/*Desactiva el boton imprimir*/
function Form_REQUISICION__DesactivarBotonImprimir(){
	DesactivarBoton("BOTON_IMPRIMIR_FR","IMG_IMPRIMIR_FR",'visualizar');
	}

//definir el select con las unidades de medida
var Form_REQUISICION__UnidadMedida="";
function Form_REQUISICION__CargarUnidadMedida(){
	var resp=SIGA.Ext.Ajax.request({
			async: false,
			url:"module/unidad_medida/",
			//params: SIGA.Ext.JSON.decode("{action: 'onList'}")
			params: {
				action: 'onListSelect',
				text: '',
				start: 0,
				limit: 'ALL',
				sort: '[{"property":"id","direction":"ASC"}]'
			}
		});
		if(resp.statusText=="OK"){
			var retorno=SIGA.Ext.JSON.decode(resp.responseText);
			Form_REQUISICION__UnidadMedida=retorno["result"];
		}
	}

function Form_REQUISICION__SelectUnidadMedida(indice,id_unidad_medida,bloquear){
	var add="";
	if(bloquear)
		add="disabled='true'";
	var select="<select "+add+" id='ID_UM_ITEM_"+indice+"' onchange='Form_REQUISICION__ArregloArticulos["+indice+"][4]=this.value;' style='border: none; background: none; width: 100%; padding-left: 10px; font-size: 12px; color: #344360;'>";
	for(var i=0;i<Form_REQUISICION__UnidadMedida.length;i++){
		add="";
		if(Form_REQUISICION__UnidadMedida[i]["id"]==id_unidad_medida)
			add="selected='true'";
		select+="<option value='"+Form_REQUISICION__UnidadMedida[i]["id"]+"' "+add+">"+Form_REQUISICION__UnidadMedida[i]["medida"]+"</option>";
	}
	select+="</select>";
	return select;
}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_REQUISICION__IDSeleccionActualLista=-1;

/**
* Nueva definicion
*/
function Form_REQUISICION__Nuevo(){
	//busco el n de la requisicion y lo coloco en el input text
	
	var index=xGetElementById('TIPO_FR').selectedIndex;
	xGetElementById("FORMULARIO_REQUISICION").reset();
	xGetElementById('TIPO_FR').selectedIndex=index;
	
	//xGetElementById("FECHA_FR").value="";
	//xGetElementById("CODIGO_FR").value="";
	//xGetElementById("CONCEPTO_FR").value="";
	
	
	Form_REQUISICION__Correlativo();
	Form_REQUISICION__ArregloArticulosContador=0;
	Form_REQUISICION__IDSeleccionActualListaArticulos=-1;

	Form_REQUISICION__MostrarTablaArticulos();

	Form_REQUISICION__LimpiarInputTextBuscarListado();

	//Form_REQUISICION__CargarSelectUnidadAdministrativa();
	Form_REQUISICION__CargarAC();
	Form_REQUISICION__TabPane.setSelectedIndex(0);
	//DarFocoCampo("DENOMINACION_FR",1000);
	}



var Form_REQUISICION__IDEstructuraPresupuestariaSeleccionActualLista=-1;

function Form_REQUISICION__CambioSelect2() {
	/*xGetElementById('TIPO_FR').value=xGetElementById('ID_ITEM_TIPO_2_FR').value;
	xGetElementById('TIPO_FR').selected();*/
	
	xGetElementById('TIPO_FR').selectedIndex = xGetElementById('ID_ITEM_TIPO_2_FR').selectedIndex;
	
	Form_REQUISICION__Correlativo();
}

function Form_REQUISICION__Correlativo(){
	
	if(xGetElementById("TIPO_FR").value=="OS") //si es servicios, ocultar la columna unidad de medida
		xGetElementById("CABECERA_UM_FR").style.display="none";
	else
		xGetElementById("CABECERA_UM_FR").style.display="";
	
	//xGetElementById('ID_ITEM_TIPO_2_FR').value=xGetElementById('TIPO_FR').value;
	xGetElementById('ID_ITEM_TIPO_2_FR').selectedIndex = xGetElementById('TIPO_FR').selectedIndex;
	Form_REQUISICION__BuscarListado_CadenaBuscar='';
	Form_REQUISICION__BuscarListado();
	
	AjaxRequest.post({
					'parameters':{
									'action':"onGet_Correlativo",
									'tipo': xGetElementById("TIPO_FR").value
									},
					'onSuccess': function(req){
									var respuesta = req.responseText;
									var resultado = eval("(" + respuesta + ")");
									xGetElementById("CODIGO_FR").value=completarCodigoCeros(String(resultado[0][0]),10);
									Form_REQUISICION__ArregloArticulosContador=0;
									Form_REQUISICION__IDSeleccionActualListaArticulos=-1;
									Form_REQUISICION__MostrarTablaArticulos();
									},
					'url':'../requisicion_externa/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});	
}


/**
* Guarda los datos en la BD
*/
function Form_REQUISICION__Guardar(){
	Form_REQUISICION__TabPane.setSelectedIndex(0);

	var _id_item_tipo			= xGetElementById("TIPO_FR").value;
	var _fecha 						= xTrim(strtoupper(xGetElementById("FECHA_FR").value));
	var _id_accion_subespecifica 	= xTrim(strtoupper(xGetElementById("OAE_FR").value));
	var _concepto 					= xTrim(strtoupper(xGetElementById("CONCEPTO_FR").value));
	
	if(!_id_accion_subespecifica || !_id_item_tipo){
		alert("Error. Alguno de los campos de selección no fueron cargados.");
		return;
		}
	
	if(!_fecha){
		Form_REQUISICION__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_REQUISICION__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_REQUISICION__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_REQUISICION__MensajeListado("");
		return;
		}	
	_fecha=DesFormatearFecha(_fecha);
	if(!_concepto){
		var msg="Por favor introduzca el concepto."
		Form_REQUISICION__Mensaje(msg,"ROJO");
		Form_REQUISICION__MensajeListado("");
		return;
		}
	var K=0;
	var _items=new Array();
	for(i=0;i<Form_REQUISICION__ArregloArticulosContador;i++)
		if(Form_REQUISICION__ArregloArticulos[i][5]==true){
			_items[K]={
				id_item: Form_REQUISICION__ArregloArticulos[i][0],
				cantidad: Form_REQUISICION__ArregloArticulos[i][3],
				id_unidad_medida: _id_item_tipo==3?1:xGetElementById("ID_UM_ITEM_"+i).value
			};			
			K++;
			}
	//if(K==0){
	//	var msg="Por favor agrege artículos a la requisición."
	//	Form_REQUISICION__Mensaje(msg,"ROJO");
	//	Form_REQUISICION__MensajeListado("");
	//	return;
	//	}
	
	Form_REQUISICION__DesactivarFormulario();
	var _id="";
	if(Form_REQUISICION__IDSeleccionActualLista>0) 
		_id=Form_REQUISICION__IDSeleccionActualLista;
		
	if(_id){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_REQUISICION__ActivarFormulario();
			return;
			}
		}
	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id,
										'tipo':_id_item_tipo,
										'fecha':_fecha,
										'concepto':_concepto,
										'id_accion_subespecifica': _id_accion_subespecifica,
										'items': SIGA.Ext.encode(_items)
										},
						'onSuccess':Form_REQUISICION__GuardarMensaje,
						'url':'../requisicion_externa/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});

return;
	/*Si es guardar nuevo*/
	if(Form_REQUISICION__IDSeleccionActualLista==-1){
		AjaxRequest.post({
						'parameters':{'accion':"Form_REQUISICION__Guardar",
										'fecha_rb':Fecha,
										'concepto_rb':Concepto,
										'id_estructura_presupuestaria':IDEstructuraPresupuestaria,
										'ARREGLO':Arreglo,
										'TAM_ARREGLO':K},
						'onSuccess':Form_REQUISICION__GuardarMensaje,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	/*Si es modificar*/
	else{
		AjaxRequest.post({'parameters':{'accion':"Form_REQUISICION__EstaAsociadaOrdenCompra",
										'id_requisicion_bienes':Form_REQUISICION__IDSeleccionActualLista},
						'onSuccess':function(req){
										var respuesta = req.responseText;
										var resultado = eval("(" + respuesta + ")");
										var n=resultado.length;
										if(n>0){//si encuentra ordenes de compra con la requisicion
											var ArregloOrdenes=new Array();
											var cadena_aux="";
											for(m=0;m<n;m++){
												cadena_aux+="\t"+completarCodigoCeros(resultado[m]['id_orden_de_compra'],NDigitos_Codigo_OrdenDeCompra)+"\n";
												ArregloOrdenes[m]=resultado[m]['id_orden_de_compra'];
												}
											if(!confirm("Esta requisición se encuentra relacionada con la orden de compra:\n"+cadena_aux+"Se modificará la orden de compra.\n¿Esta seguro que desea guardar los cambios?")){
												Form_REQUISICION__ActivarFormulario();
												return;
												}
											AjaxRequest.post({'parameters':{
																'accion':"Form_REQUISICION__Modificar_R_ODC",
																'id_requisicion_bienes':Form_REQUISICION__IDSeleccionActualLista,
																'fecha_rb':Fecha,
																'concepto_rb':Concepto,
																'id_estructura_presupuestaria':IDEstructuraPresupuestaria,
																'ARREGLO':Arreglo,
																'TAM_ARREGLO':K,
																'ORDENES':ArregloOrdenes},
												'onSuccess':Form_REQUISICION__GuardarMensaje,
												'url':'../modulo_compras/consultas.php',
												'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
												});
											}
										else{//si no encuentra
											if(!confirm("¿Esta seguro que desea guardar los cambios?")){
												Form_REQUISICION__ActivarFormulario();
												return;
												}
											AjaxRequest.post({'parameters':{'accion':"Form_REQUISICION__Modificar",
																			'id_requisicion_bienes':Form_REQUISICION__IDSeleccionActualLista,
																			'fecha_rb':Fecha,
																			'concepto_rb':Concepto,
																			'id_estructura_presupuestaria':IDEstructuraPresupuestaria,
																			'ARREGLO':Arreglo,
																			'TAM_ARREGLO':K},
															'onSuccess':Form_REQUISICION__GuardarMensaje,
															'url':'../modulo_compras/consultas.php',
															'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
															});
											}
										},
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_REQUISICION__GuardarMensaje(req){
	Form_REQUISICION__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_REQUISICION__Nuevo();
		Form_REQUISICION__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_REQUISICION__Mensaje(respuesta.message,"ROJO");		
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_REQUISICION__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FR").checked){
 		if(ev.keyCode==13)
			Form_REQUISICION__BuscarListado();
		return;
		}
	Form_REQUISICION__BuscarListado();
	}

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_REQUISICION__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_REQUISICION__BuscarListado(){ 
	Form_REQUISICION__IDSeleccionActualLista=-1;
	Form_REQUISICION__IDEstructuraPresupuestariaSeleccionActualLista=-1;
	Form_REQUISICION__id_accion_centralizada="";
  Form_REQUISICION__id_accion_especifica="";
  Form_REQUISICION__id_accion_subespecifica="";

	//xGetElementById("FORMULARIO_REQUISICION").reset();
	Form_REQUISICION__ActivarFormulario();
	Form_REQUISICION__DesactivarBotonModificar();
	Form_REQUISICION__DesactivarBotonEliminar();
	Form_REQUISICION__ActivarBotonGuardar();
	Form_REQUISICION__DesactivarBotonImprimir();
	//xGetElementById("EMITIDO_FR").innerHTML="";

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FR").value));

	if(CadenaBuscar!="")
		if(Form_REQUISICION__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_REQUISICION__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FR").innerHTML=IconoCargandoTabla;

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'tipo': xGetElementById("ID_ITEM_TIPO_2_FR").value,
									'mes': xGetElementById("MES_FILTRAR_FR").value,
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_REQUISICION__MostrarListado,
					'url':'../requisicion_externa/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_REQUISICION__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;
	

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FR").value));

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_REQUISICION__SeleccionarElementoTabla('"+resultado[i]['id']+"')";
 		FuncionOnDblclick="Form_REQUISICION__TabPane.setSelectedIndex(0);";
 		FuncionOnMouseOver="pintarFila(\"FR"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FR"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FR"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		if(xGetElementById("SOMBRA_CHECKBOX_FR").checked && TextoBuscar!=""){
			CadAux1=str_replace(resultado[i]['correlativo'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['estructura_presupuestaria'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux1=resultado[i]['correlativo'];
			CadAux2=resultado[i]['fecha'];
			CadAux3=resultado[i]['estructura_presupuestaria'];
			CadAux4=resultado[i]['concepto'];
			}
				
		Contenido+="<TD width='12%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='11%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='20%' class='FilaEstilo' style='font-size:11px;'>"+CadAux3+"</TD>";
		Contenido+="<TD width='57%' class='FilaEstiloContinua' style='font-size:11px;'>"+CadAux4+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FR").innerHTML=Contenido;
	}

/**
* Es llamada cuando se hace click sobre algun elemento de la tabla.
* Esta manda los datos para el formulario que se encuentra en la pestaña 'entrada de datos'
* @param {Integer} IDSeleccion Id del elemento seleccionado
* @param {String} RIF Rif del proveedor seleccionado
* @param {String} NIT Nit del proveedor seleccionado
* @param {String} Compania Nombre del proveedor seleccionado
* @param {String} IDTipoOrganizacion Id del tipo de organizacion del proveedor seleccionado
* @param {String} NombreTipoOrganizacion Nombre del tipo de organizacion del proveedor seleccionado
* @param {String} Direccion Direccion del proveedor seleccionado
* @param {String} Telefono Telefono del proveedor seleccionado
* @param {String} CodigoContable Codigo contable asociado al proveedor seleccionado
* @param {String} DenominacionCC Denominacion del codigo contable asociado al proveedor seleccionado
*/

var Form_REQUISICION__ProgramaticaSeleccionActualLista="";
var Form_REQUISICION__id_accion_centralizada="";
var Form_REQUISICION__id_accion_especifica="";
var Form_REQUISICION__id_accion_subespecifica="";

function Form_REQUISICION__SeleccionarElementoTabla(IDSeleccion){
	if(Form_REQUISICION__IDSeleccionActualLista!=-1)
		xGetElementById("FR"+Form_REQUISICION__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FR"+IDSeleccion).bgColor=colorBase;
	Form_REQUISICION__IDSeleccionActualLista=IDSeleccion;
	
	Form_REQUISICION__DesactivarFormulario();
	Form_REQUISICION__ActivarBotonImprimir();
	Form_REQUISICION__ActivarBotonModificar();
	Form_REQUISICION__ActivarBotonEliminar();
	Form_REQUISICION__DesactivarBotonGuardar();
	Form_REQUISICION__Mensaje("");
	Form_REQUISICION__MensajeListado("");
	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_REQUISICION__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								//xGetElementById("TIPO_FR").value=resultado[0]["id_item_tipo"];
								//xGetElementById('ID_ITEM_TIPO_2_FR').selectedIndex = xGetElementById('TIPO_FR').selectedIndex;
								xGetElementById("CODIGO_FR").value=resultado[0]["correlativo"];
								xGetElementById("FECHA_FR").value=resultado[0]["fecha"];
								xGetElementById("CONCEPTO_FR").value=resultado[0]["concepto"];
								
								Form_REQUISICION__id_accion_centralizada=resultado[0]["id_accion_centralizada"];
								Form_REQUISICION__id_accion_especifica=resultado[0]["id_accion_especifica"];
								Form_REQUISICION__id_accion_subespecifica=resultado[0]["id_accion_subespecifica"];
								Form_REQUISICION__CargarAC();
								
								Form_REQUISICION__ArregloArticulosContador=0;
								for(var i=0;i<resultado[0]["items"].length;i++){
									Form_REQUISICION__ArregloArticulos[i]=new Array(6);
									Form_REQUISICION__ArregloArticulos[i][0]=resultado[0]["items"][i]["id_item"];
									Form_REQUISICION__ArregloArticulos[i][1]=resultado[0]["items"][i]["codigo"];
									Form_REQUISICION__ArregloArticulos[i][2]=resultado[0]["items"][i]["item"];
									Form_REQUISICION__ArregloArticulos[i][3]=resultado[0]["items"][i]["cantidad"];
									Form_REQUISICION__ArregloArticulos[i][4]=resultado[0]["items"][i]["id_unidad_medida"];
									Form_REQUISICION__ArregloArticulos[i][5]=true;
								
									Form_REQUISICION__ArregloArticulosContador++;
									}
								Form_REQUISICION__MostrarTablaArticulos(true);								
									
								for(var i=0;i<resultado[0]["items"].length;i++){
									xGetElementById("ID_UM_ITEM_"+i).value=resultado[0]["items"][i]["id_unidad_medida"];
									}
								
								},
						'url':'../requisicion_externa/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});


	}












function Form_REQUISICION__CargarArregloArticulos(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	var n=resultado.length;

	for(i=0;i<n;i++){
		Form_REQUISICION__ArregloArticulos[i]=new Array(6);
		Form_REQUISICION__ArregloArticulos[i][0]=completarCodigoCeros(resultado[i]['id_articulo'],NDigitos_Codigo_Articulo);
		Form_REQUISICION__ArregloArticulos[i][1]=resultado[i]['denominacion_a'];
		Form_REQUISICION__ArregloArticulos[i][2]=resultado[i]['cantidad_arb'];
		Form_REQUISICION__ArregloArticulos[i][3]=resultado[i]['denominacion_udm'];
		//Form_REQUISICION__ArregloArticulos[i][4]=resultado[i]['costo_ar'];
		Form_REQUISICION__ArregloArticulos[i][4]=true;
		}

	Form_REQUISICION__ArregloArticulosContador=n;
	Form_REQUISICION__MostrarTablaArticulos(true);
	}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_REQUISICION__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_FR").value="";
	Form_REQUISICION__Mensaje("");
	Form_REQUISICION__MensajeListado("");
	Form_REQUISICION__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FR",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_REQUISICION__Modificar(){
	Form_REQUISICION__ActivarFormulario();
	Form_REQUISICION__ActivarBotonGuardar();
	Form_REQUISICION__DesactivarBotonModificar();
	Form_REQUISICION__TabPane.setSelectedIndex(0);
	
	xGetElementById("TIPO_FR").readOnly=true;
	xGetElementById("TIPO_FR").disabled=true;
	xGetElementById("TIPO_FR").setAttribute('class','TextoCampoInputDesactivado');
	//Form_REQUISICION__CargarAC();
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_REQUISICION__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_REQUISICION__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;

	AjaxRequest.post({'parameters':{ 'accion':"Form_REQUISICION__Eliminar",
									'id_requisicion_bienes':Form_REQUISICION__IDSeleccionActualLista},
					 'onSuccess':Form_REQUISICION__EliminarMensaje,
					 'url':'../modulo_compras/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_REQUISICION__EliminarMensaje(req){
	var respuesta = req.responseText;
	if(respuesta==1){
		Form_REQUISICION__ArregloArticulosContador=0;
		Form_REQUISICION__IDSeleccionActualListaArticulos=-1;
		Form_REQUISICION__MostrarTablaArticulos();

		Form_REQUISICION__LimpiarInputTextBuscarListado();
		Form_REQUISICION__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_REQUISICION__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		Form_REQUISICION__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		Form_REQUISICION__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		}
	}

var Form_REQUISICION__ArregloArticulos=new Array();
var Form_REQUISICION__ArregloArticulosContador=0;

function Form_REQUISICION__AgregarArticuloTabla(){
  //buscar si el articulo existe
	for(var i=0;i<Form_REQUISICION__ArregloArticulosContador;i++)
		if(Form_REQUISICION__ArregloArticulos[i][5]==true)
			if(Form_REQUISICION__ArregloArticulos[i][0]==xGetElementById("ID_ITEM_AGREGAR_FR").value) {
				alert("El ítem ya se encuentra agregado en el listado");
				return false;
			}
	
	var i=Form_REQUISICION__ArregloArticulosContador;
	Form_REQUISICION__ArregloArticulos[i]=new Array(6);
	Form_REQUISICION__ArregloArticulos[i][0]=xGetElementById("ID_ITEM_AGREGAR_FR").value;
	Form_REQUISICION__ArregloArticulos[i][1]=xGetElementById("COD_ITEM_AGREGAR_FR").value;
	Form_REQUISICION__ArregloArticulos[i][2]=xGetElementById("DEM_ITEM_AGREGAR_FR").value;
	Form_REQUISICION__ArregloArticulos[i][3]="0.00";
	Form_REQUISICION__ArregloArticulos[i][4]=1;
	//Form_REQUISICION__ArregloArticulos[i][4]="<select id='ID_UM_ITEM_"+i+"' style='border: none; background: none; width: 100%; padding-left: 10px; font-size: 12px; color: #344360;'>"+Form_REQUISICION__OptionUnidadMedida+"</select>";
	Form_REQUISICION__ArregloArticulos[i][5]=true;

	Form_REQUISICION__ArregloArticulosContador++;
	Form_REQUISICION__MostrarTablaArticulos();
	return true;
	}


function Form_REQUISICION__MostrarTablaArticulos(Bloquear){
	if(!Bloquear)
		Bloquear=false;
	else
		Bloquear=true;
	xGetElementById("TABLA_LISTA_ARTICULOS_FR").innerHTML="";
	var style="";
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var FuncionOnDblclickTDCantidad="";
	var Cantidad;
 	var Costo;
	var TOTAL=0;
	for(i=0;i<Form_REQUISICION__ArregloArticulosContador;i++){
		if(Form_REQUISICION__ArregloArticulos[i][5]==true){
			if(Bloquear){
				FuncionOnclick="";
				FuncionOnDblclick="";
				FuncionOnMouseOver="";
				FuncionOnMouseOut="";
				FuncionOnDblclickTDCantidad="";
				}
			else{
				FuncionOnclick="Form_REQUISICION__SeleccionarElementoTablaArticulos('"+i+"')";
				//FuncionOnDblclick="Form_REQUISICION__ModificarArticuloTabla();";
				FuncionOnDblclick="";
				FuncionOnMouseOver="pintarFila(\"FR_A"+i+"\")";
				FuncionOnMouseOut="despintarFila(\"FR_A"+i+"\")";
				FuncionOnDblclickTDCantidad="Form_REQUISICION__ModificarValorCelda('FR_A"+i+"_CANTIDAD')";
				}
			style="";
			if(xGetElementById("TIPO_FR").value=="OS")
				style="display: none;";

			Contenido+="<TR id='FR_A"+i+"' onclick=\""+FuncionOnclick+"\" onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

			Contenido+="<TD width='10%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>" +completarCodigoCeros(Form_REQUISICION__ArregloArticulos[i][1],NDigitos_Codigo_Articulo)+"</TD>";
			Contenido+="<TD width='50%' class='FilaEstilo' ondblclick='"+FuncionOnDblclick+"'>" +Form_REQUISICION__ArregloArticulos[i][2]+"</TD>";
			Contenido+="<TD id='FR_A"+i+"_CANTIDAD' width='15%' class='FilaEstilo' align='right' ondblclick=\""+FuncionOnDblclickTDCantidad+"\">"+FormatearNumero(Form_REQUISICION__ArregloArticulos[i][3])+"</TD>";
			Contenido+="<TD style='"+style+"' width='25%' class='FilaEstilo' align='center' ondblclick='"+FuncionOnDblclick+"'>"+Form_REQUISICION__SelectUnidadMedida(i,Form_REQUISICION__ArregloArticulos[i][4],Bloquear)+"</TD>";

			Contenido+="</TR>";
			}//fin if
		}//fin for
	xGetElementById("TABLA_LISTA_ARTICULOS_FR").innerHTML=Contenido;
	
	
	
	}



function Form_REQUISICION__ModificarValorCelda(_IDCelda){
	if(xGetElementById("txt_celda_"+_IDCelda))
		return;
	Valor=Form_REQUISICION__ArregloArticulos[Form_REQUISICION__IDSeleccionActualListaArticulos][3];
	xGetElementById(_IDCelda).innerHTML="<INPUT id='txt_celda_"+_IDCelda+"' class='TextoCampoInputTabla' type='text' size='15' value='"+Valor+"' onblur=\"Form_REQUISICION__ModificarValorCeldaPierdeFoco('"+_IDCelda+"',"+Form_REQUISICION__IDSeleccionActualListaArticulos+")\" onkeypress=\"return AcceptNum(event,'txt_celda_"+_IDCelda+"');\" style='text-align : right;'>";
	xGetElementById("txt_celda_"+_IDCelda).focus();
	}
function Form_REQUISICION__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar){
	Form_REQUISICION__ArregloArticulos[indice_modificar][3]=numberFormat(xGetElementById("txt_celda_"+_IDCelda).value,2);
	xGetElementById(_IDCelda).innerHTML=FormatearNumero(Form_REQUISICION__ArregloArticulos[indice_modificar][3]);
	}



var Form_REQUISICION__IDSeleccionActualListaArticulos=-1;
function Form_REQUISICION__SeleccionarElementoTablaArticulos(IDSeleccion){
	if(Form_REQUISICION__IDSeleccionActualListaArticulos!=-1)
		xGetElementById("FR_A"+Form_REQUISICION__IDSeleccionActualListaArticulos).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FR_A"+IDSeleccion).bgColor=colorBase;
	Form_REQUISICION__IDSeleccionActualListaArticulos=IDSeleccion;
	}

function Form_REQUISICION__Quitar(){
	Form_REQUISICION__Mensaje('');
	Form_REQUISICION__MensajeListado('');
	if(Form_REQUISICION__IDSeleccionActualListaArticulos==-1)
		return;	
	
	Form_REQUISICION__ArregloArticulos[Form_REQUISICION__IDSeleccionActualListaArticulos][5]=false;
	Form_REQUISICION__IDSeleccionActualListaArticulos=-1;
	Form_REQUISICION__MostrarTablaArticulos();
	}
/*
function Form_REQUISICION__ModificarArticuloTabla(){
	Form_REQUISICION__Mensaje('');
	Form_REQUISICION__MensajeListado('');
	var Ejecutar="Form_REQUISICION__IDSeleccionActualListaArticulos="+Form_REQUISICION__IDSeleccionActualListaArticulos+"; Form_REQUISICION__ModificarArticuloTablaPostAceptar();"

	Form_LISTA_ARTICULO__Abrir( 'COD_ART_AGREGAR_FR',
								'DENOM_ART_AGREGAR_FR',
								'UNID_MED_ART_AGREGAR_FR',
								Ejecutar);
	}

function Form_REQUISICION__ModificarArticuloTablaPostAceptar(){
	if(xGetElementById("COD_ART_AGREGAR_FR").value=="")//en caso que no se seleccione ninguno y le damos aceptar, dejamos el mismo.
		return;
	var i=Form_REQUISICION__IDSeleccionActualListaArticulos;
	Form_REQUISICION__ArregloArticulos[i][0]=xGetElementById("COD_ART_AGREGAR_FR").value;
	Form_REQUISICION__ArregloArticulos[i][1]=xGetElementById("DENOM_ART_AGREGAR_FR").value;
	Form_REQUISICION__ArregloArticulos[i][3]=xGetElementById("UNID_MED_ART_AGREGAR_FR").value;
	Form_REQUISICION__MostrarTablaArticulos();
	}*/

function Form_REQUISICION__Imprimir(){
	if(Form_REQUISICION__IDSeleccionActualLista==-1)//nunca deberia cumpliser porque el boton esta desactivado
		return;
	window.open("../../report/requisicion_externa.php?id="+Form_REQUISICION__IDSeleccionActualLista);
	}



								
function Form_REQUISICION__CargarAC(){
	AjaxRequest.post({'parameters':{'action':"onList_AccionCentralizada_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AC_FR");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_centralizada"]});
								opcion.innerHTML=resultado[i]["tipo"]+resultado[i]["codigo_centralizada"];
								mD.agregaHijo(SelectU, opcion);
								}
							if(Form_REQUISICION__id_accion_centralizada) 
								xGetElementById("AC_FR").value=Form_REQUISICION__id_accion_centralizada;
							Form_REQUISICION__CargarAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_REQUISICION__CargarAE(){
	if(!xGetElementById("AC_FR").value)
		return;
	xGetElementById("AC_FR").title=xGetElementById("AC_FR").options[xGetElementById("AC_FR").selectedIndex].title;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("AC_FR").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AE_FR");
							SelectU.innerHTML="";
							var opcion;
							var textoAC;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_especifica"]});
								textoAC=xGetElementById("AC_FR").options[xGetElementById("AC_FR").selectedIndex].innerHTML;
								//opcion.innerHTML=resultado[i]["codigo_especifico"];
								//opcion.innerHTML=FormatearCodigoProgramaticoAE(textoAC,resultado[i]["codigo_especifica"]);
								opcion.innerHTML=resultado[i]["codigo_especifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							if(Form_REQUISICION__id_accion_especifica) 
								xGetElementById("AE_FR").value=Form_REQUISICION__id_accion_especifica;
							Form_REQUISICION__CargarOAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_REQUISICION__CargarOAE(){
	if(!xGetElementById("AC_FR").value||!xGetElementById("AE_FR").value)
		return;
	xGetElementById("AE_FR").title=xGetElementById("AE_FR").options[xGetElementById("AE_FR").selectedIndex].title;
	AjaxRequest.post({'parameters':{'action':"onList_AccionSubEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]',
									'id_accion_especifica':xGetElementById("AE_FR").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("OAE_FR");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_subespecifica"]});
								opcion.innerHTML=resultado[i]["codigo_subespecifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							if(Form_REQUISICION__id_accion_subespecifica) 
								xGetElementById("OAE_FR").value=Form_REQUISICION__id_accion_subespecifica;
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

//Form_LISTA_ARTICULO__Abrir('COD_ART_AGREGAR_FR','DENOM_ART_AGREGAR_FR','UNID_MED_ART_AGREGAR_FR','Form_REQUISICION__AgregarArticuloTabla();');
function Form_REQUISICION__Agregar(){
	var _id_item_tipo=[];
	if(xGetElementById("TIPO_FR").value=="OC")
		_id_item_tipo=["1","2"];
	else if(xGetElementById("TIPO_FR").value=="OS")
		_id_item_tipo=["3"];
	
	var campo={
		fieldLabel: "Agregar items",
		internal:{
			valueField: 'id',
			columns: {field: ["codigo","denominacion","cuenta_presupuestaria","item_tipo"], title: ["Código","Denominación","Partida Presupuestaria"], width: ['10%','50%','40%'], sort: ["ASC","ASC","ASC"]},
			url: 'module/item/',
			extraParams:{
				id_item_tipo: SIGA.Ext.encode(_id_item_tipo),
				//id_item_tipo: "1",
			},
			gridList:{
				features:[{
					ftype: 'grouping',
					groupHeaderTpl: '{name}',
					collapsible : false,
				}],
				groupField: 'item_tipo'
			},
			actionOnList:'onList',
			page: 1,
			limit: 100,
			onBeforeAccept: function(dataview, record, item, index, e){
				//verificar si el item no se encuentra en el listado, si se encuentra mostrar una alerta y no cerrar la ventana de seleccion del item
				xGetElementById("ID_ITEM_AGREGAR_FR").value=record.get("id");
				xGetElementById("COD_ITEM_AGREGAR_FR").value=record.get("codigo");
				xGetElementById("DEM_ITEM_AGREGAR_FR").value=record.get("denominacion");
				return Form_REQUISICION__AgregarArticuloTabla();
			}
		},
		setValue: function(id_item){
		}
	};

	var _opt={};
	_opt.internal={};
	_opt.internal.parent=campo;
	var selector=Ext.create("siga.windowSelect",_opt)
	//mostrar la ventana
	selector.show();
	//cargar el listado
	selector.search();
}

