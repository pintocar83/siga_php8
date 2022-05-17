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

var Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios=[];
var Form_BANCO_MOVIMIENTO__ArregloDetallesContables=[];
var Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios=0;
var Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables=0;
var Form_BANCO_MOVIMIENTO__ModificarTabla=false;



/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_BANCO_MOVIMIENTO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FBM").innerHTML=MSG;
	}


/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_BANCO_MOVIMIENTO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FBM_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_BANCO_MOVIMIENTO__ActivarFormulario(){
	xGetElementById("TD0_CTA_2").style.visibility="hidden";
	xGetElementById("TD1_CTA_2").style.visibility="hidden";
	xGetElementById("TD2_CTA_2").style.visibility="hidden";
	xGetElementById("TD3_CTA_2").style.visibility="hidden";
	xGetElementById("TD4_CTA_2").style.visibility="hidden";
	xGetElementById("TD5_CTA_2").style.visibility="hidden";

	xGetElementById("IMG_BUSCAR_NCTA_FBM").setAttribute('onclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FBM','NCTA_FBM','DESCRIPCION_NCTA_FBM','TIPO_CTA_FBM','BANCO_FBM','CTA_CODIGO_CONTABLE_FBM','')");
	xGetElementById("IMG_BUSCAR_NCTA_FBM2").setAttribute('onclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FBM2','NCTA_FBM2','DESCRIPCION_NCTA_FBM2','TIPO_CTA_FBM2','BANCO_FBM2','CTA_CODIGO_CONTABLE_FBM2','')");

	xGetElementById("IMG_BUSCAR_NCTA_BUSCAR_FBM").setAttribute('onclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_BUSCAR_FBM','NCTA_BUSCAR_FBM','DESCRIPCION_NCTA_BUSCAR_FBM','TIPO_CTA_BUSCAR_FBM','BANCO_BUSCAR_FBM','CTA_CODIGO_CONTABLE_BUSCAR_FBM','','','Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado();')");



	ActivarBoton("IMG_BUSCAR_NCTA_FBM","IMG_BUSCAR_NCTA_FBM",'buscar');
	ActivarBoton("IMG_FECHA_FBM","IMG_FECHA_FBM",'calendario');

	xGetElementById("IMG_FECHA_FBM").setAttribute('onclick',"showCalendar('FECHA_FBM','%d/%m/%Y')");
	xGetElementById("FECHA_FBM").setAttribute('ondblclick',"showCalendar('FECHA_FBM','%d/%m/%Y')");
	xGetElementById("FECHA_INICIO_FBM").setAttribute('ondblclick',"showCalendar('FECHA_INICIO_FBM','%d/%m/%Y')");
	xGetElementById("FECHA_FIN_FBM").setAttribute('ondblclick',"showCalendar('FECHA_FIN_FBM','%d/%m/%Y')");
	xGetElementById("IMG_FECHA_INICIO_FBM").setAttribute('onclick',"showCalendar('FECHA_INICIO_FBM','%d/%m/%Y')");
	xGetElementById("IMG_FECHA_FIN_FBM").setAttribute('onclick',"showCalendar('FECHA_FIN_FBM','%d/%m/%Y')");


	xGetElementById("ID_CTA_FBM").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("TIPO_OPERACION_FBM").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("ID_CTA_FBM2").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("FECHA_FBM").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DOCUMENTO_FBM").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CONCEPTO_FBM").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CONCEPTO_FBM2").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("MONTO_FBM").setAttribute('class','TextoCampoInputObligatorios');


	xGetElementById("ID_CTA_FBM").readOnly=false;
	xGetElementById("TIPO_OPERACION_FBM").readOnly=false;
	xGetElementById("TIPO_OPERACION_FBM").disabled=false;
	xGetElementById("ID_CTA_FBM2").readOnly=false;
	xGetElementById("FECHA_FBM").readOnly=false;
	xGetElementById("DOCUMENTO_FBM").readOnly=false;
	xGetElementById("CONCEPTO_FBM").readOnly=false;
	xGetElementById("CONCEPTO_FBM2").readOnly=false;
	xGetElementById("MONTO_FBM").readOnly=false;

	xGetElementById("CHECK_CONTABILIZAR_FBM").disabled=false;


	xGetElementById("ID_CTA_FBM").setAttribute('ondblclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FBM','NCTA_FBM','DESCRIPCION_NCTA_FBM','TIPO_CTA_FBM','BANCO_FBM','CTA_CODIGO_CONTABLE_FBM','')");

	xGetElementById("ID_CTA_FBM2").setAttribute('ondblclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FBM2','NCTA_FBM2','DESCRIPCION_NCTA_FBM2','TIPO_CTA_FBM2','BANCO_FBM2','CTA_CODIGO_CONTABLE_FBM2','')");


	xGetElementById("ID_CTA_BUSCAR_FBM").setAttribute('ondblclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_BUSCAR_FBM','NCTA_BUSCAR_FBM','DESCRIPCION_NCTA_BUSCAR_FBM','TIPO_CTA_BUSCAR_FBM','BANCO_BUSCAR_FBM','CTA_CODIGO_CONTABLE_FBM','','','Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado();')");

	xGetElementById("MONTO_FBM").setAttribute('onblur',"xGetElementById('MONTO_FBM').value=numberFormat(xGetElementById('MONTO_FBM').value,2);");
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_BANCO_MOVIMIENTO__DesactivarFormulario(){
	xGetElementById("IMG_BUSCAR_NCTA_FBM").setAttribute('onclick',"");

	DesactivarBoton("IMG_BUSCAR_NCTA_FBM","IMG_BUSCAR_NCTA_FBM",'buscar');
	DesactivarBoton("IMG_FECHA_FBM","IMG_FECHA_FBM",'calendario');


	xGetElementById("IMG_FECHA_FBM").setAttribute('onclick',"");
	xGetElementById("FECHA_FBM").setAttribute('ondblclick',"");


	xGetElementById("ID_CTA_FBM").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TIPO_OPERACION_FBM").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_FBM").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DOCUMENTO_FBM").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_FBM").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("MONTO_FBM").setAttribute('class','TextoCampoInputDesactivado');


	xGetElementById("ID_CTA_FBM").readOnly=true;
	xGetElementById("TIPO_OPERACION_FBM").readOnly=true;
	xGetElementById("TIPO_OPERACION_FBM").disabled=true;
	xGetElementById("FECHA_FBM").readOnly=true;
	xGetElementById("DOCUMENTO_FBM").readOnly=true;
	xGetElementById("CONCEPTO_FBM").readOnly=true;
	xGetElementById("MONTO_FBM").readOnly=true;



	xGetElementById("CHECK_CONTABILIZAR_FBM").disabled=true;


	xGetElementById("ID_CTA_FBM").setAttribute('ondblclick',"");
	xGetElementById("ID_CTA_FBM").setAttribute('onblur',"");
	xGetElementById("ID_CTA_FBM").setAttribute('onkeypress',"");

	xGetElementById("MONTO_FBM").setAttribute('onblur',"");
	}

/**
* Activa el boton modificar
*/
function Form_BANCO_MOVIMIENTO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FBM","IMG_MODIFICAR_FBM",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_BANCO_MOVIMIENTO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FBM","IMG_MODIFICAR_FBM",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_BANCO_MOVIMIENTO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FBM","IMG_GUARDAR_FBM",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_BANCO_MOVIMIENTO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FBM","IMG_GUARDAR_FBM",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_BANCO_MOVIMIENTO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FBM","IMG_ELIMINAR_FBM",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_BANCO_MOVIMIENTO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FBM","IMG_ELIMINAR_FBM",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_BANCO_MOVIMIENTO__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_BANCO_MOVIMIENTO__IDTipoOperacionSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_BANCO_MOVIMIENTO__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_BANCO_MOVIMIENTO__BuscarListado_CadenaBuscar="";

/**
* Nueva definicion
*/
function Form_BANCO_MOVIMIENTO__Nuevo(){
	Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado();
	Form_BANCO_MOVIMIENTO__TabPane.setSelectedIndex(0);
	}

/*Actualiza el select Tipo Cuenta, es llamado desde el formulario DEFINICIONES_TIPOS_DE_CUENTA, al agregar o al eliminar*/
function Form_BANCO_MOVIMIENTO__ActualizarSelectTipoOperacion(){
	if(xGetElementById("TIPO_CUENTA_FBM"))
		Form_BANCO_MOVIMIENTO__CargarSelectTipoOperacion();
	}

/*Carga el listado de tipos de cuenta en el select*/
function Form_BANCO_MOVIMIENTO__CargarSelectTipoOperacion(){
	AjaxRequest.post({
						'parameters':{
									'action':"onList",
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"denominacion","direction":"ASC"}]'									
									},
						'onSuccess': Form_BANCO_MOVIMIENTO__CargarSelectMostrarTipoOperacion,
						'url':'../banco_movimiento_tipo/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

/*Despues que cargar lo tipos de cuenta, los mostramos en el select*/
function Form_BANCO_MOVIMIENTO__CargarSelectMostrarTipoOperacion(req){
	var respuesta = req.responseText;
  var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];

	var SelectTipoOperacion = xGetElementById("TIPO_OPERACION_FBM");
	SelectTipoOperacion.innerHTML="";
	var opcion;
	//Cuando es nuevo, sale por defecto SELECCIONE | AGREGE
	if(Form_BANCO_MOVIMIENTO__IDTipoOperacionSeleccionActualLista==-1){
		opcion = mD.agregaNodoElemento("option", null, null, { 'value':"" } );
		opcion.innerHTML="";//SELECCIONE
		mD.agregaHijo(SelectTipoOperacion, opcion);
		for(var i=0; i<resultado.length; i++){
			if(resultado[i]['codigo']!="TR")
				opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id']+"|"+resultado[i]['operacion']+'|'+resultado[i]['codigo'] } );
			else
				opcion = mD.agregaNodoElemento("option", null, null, {'id':"TR_FBM", 'value':resultado[i]['id']+"|"+resultado[i]['operacion']+'|'+resultado[i]['codigo']} );
			opcion.innerHTML=resultado[i]['denominacion'];
			mD.agregaHijo(SelectTipoOperacion, opcion);
			}
		}
	//Cuando es modificar, sale por defecto el guardado
	else{
		for(var i=0; i<resultado.length; i++){
			if(Form_BANCO_MOVIMIENTO__IDTipoOperacionSeleccionActualLista!=resultado[i]['id']){
				if(resultado[i]['codigo']=="TR")
					continue;
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id']+"|"+resultado[i]['operacion']+'|'+resultado[i]['codigo'] } );
				}
			else{
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id']+"|"+resultado[i]['operacion']+'|'+resultado[i]['codigo'], 'selected':true} );
				}
			opcion.innerHTML=resultado[i]['denominacion'];
			mD.agregaHijo(SelectTipoOperacion, opcion);
			}
		}
	}

function Form_BANCO_MOVIMIENTO__ChangeOperacion(){
	var _codigo = String(xGetElementById("TIPO_OPERACION_FBM").value).split("|");
	
	xGetElementById("TD0_CTA_2").style.visibility="hidden";
	xGetElementById("TD1_CTA_2").style.visibility="hidden";
	xGetElementById("TD2_CTA_2").style.visibility="hidden";
	xGetElementById("TD3_CTA_2").style.visibility="hidden";
	xGetElementById("TD4_CTA_2").style.visibility="hidden";
	xGetElementById("TD5_CTA_2").style.visibility="hidden";
	
	if(_codigo.length>0) {
		if(_codigo[2]=="TR") {
			xGetElementById("TD0_CTA_2").style.visibility="inherit";
			xGetElementById("TD1_CTA_2").style.visibility="inherit";
			xGetElementById("TD2_CTA_2").style.visibility="inherit";
			xGetElementById("TD3_CTA_2").style.visibility="inherit";
			xGetElementById("TD4_CTA_2").style.visibility="inherit";
			xGetElementById("TD5_CTA_2").style.visibility="inherit";
		}
	}
}


//cuando cambia de estado. mostramos o ocultamos el tab contabilizar.
function Form_BANCO_MOVIMIENTO__MostrarTAB_CONTABILIZAR(){
	
	return;

	if(xGetElementById("CHECK_CONTABILIZAR_FBM").checked){
		xGetElementById("TAB_CONTABILIZAR_FBM").style.visibility="inherit";


		if(!(Form_BANCO_MOVIMIENTO__IDComprobante=="null"||!Form_BANCO_MOVIMIENTO__IDComprobante))
			return;

		var sw;
		var TipoOperacion = String(xGetElementById("TIPO_OPERACION_FBM").value).split("|");
		if(TipoOperacion.length==3)
			TipoOperacion=TipoOperacion[1];
		else
			TipoOperacion="";
		var CuentaContable	= xTrim(strtoupper(xGetElementById("CTA_CODIGO_CONTABLE_FBM").value));
		var CuentaContableD	= xTrim(strtoupper(xGetElementById("CTA_CODIGO_CONTABLE_FBM2").value));
		var Monto 			= xTrim(strtoupper(xGetElementById("MONTO_FBM").value));

		if(TipoOperacion=="" || CuentaContable=="" || Monto=="")
			return;

		sw=Form_BANCO_MOVIMIENTO__AgregarElementoArregloDC("0",
																"0",
																CuentaContable,
																TipoOperacion,
																Monto);

		if(xGetElementById("TR_FBM"))
			if(xGetElementById("TR_FBM").selected){//si es tranferencia
				if(CuentaContableD=="")
					return;
				sw=Form_BANCO_MOVIMIENTO__AgregarElementoArregloDC("0",
																		"0",
																		CuentaContableD,
																		"D",
																		Monto);

				}
		}
	else
		xGetElementById("TAB_CONTABILIZAR_FBM").style.visibility="hidden";
	}


function Form_BANCO_MOVIMIENTO__Guardar(){
	Form_BANCO_MOVIMIENTO__TabPane.setSelectedIndex(0);
	var _fecha												= xTrim(strtoupper(xGetElementById("FECHA_FBM").value));
	var _banco_movimiento_tipo				= xTrim(strtoupper(xGetElementById("TIPO_OPERACION_FBM").value));
	var _id_banco_cuenta							= xTrim(strtoupper(xGetElementById("ID_CTA_FBM").value));
	var _id_banco_cuenta_destino			= xTrim(strtoupper(xGetElementById("ID_CTA_FBM2").value));
	var _numero												= xTrim(strtoupper(xGetElementById("DOCUMENTO_FBM").value));
	var _concepto											= xTrim(strtoupper(xGetElementById("CONCEPTO_FBM").value));
	var _concepto_destino							= xTrim(strtoupper(xGetElementById("CONCEPTO_FBM2").value));
	var _monto												= xTrim(strtoupper(xGetElementById("MONTO_FBM").value));
	

	if(!_fecha){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_BANCO_MOVIMIENTO__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);	
	if(!_banco_movimiento_tipo){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor seleccione el tipo de operación.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	if(!_id_banco_cuenta){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor seleccione el número de cuenta.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}		
	if(!_numero){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor introduzca el código del documento de referencia.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	if(!_concepto){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor introduzca el concepto.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	if(!_monto){
		Form_BANCO_MOVIMIENTO__Mensaje("Por favor introduzca el monto.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	
	var _transferencia="";
	_banco_movimiento_tipo=_banco_movimiento_tipo.split("|");
	var _id_banco_movimiento_tipo=_banco_movimiento_tipo[0];
	if(_banco_movimiento_tipo[2]=="TR") {
		if(!_id_banco_cuenta_destino){
			Form_BANCO_MOVIMIENTO__Mensaje("Por favor seleccione el número de cuenta (destino).","ROJO");
			Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
		}
		if(_id_banco_cuenta==_id_banco_cuenta_destino){
			Form_BANCO_MOVIMIENTO__Mensaje("El número de cuenta destino, debe ser distinto al de origen.","ROJO");
			Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
		}
		
		if(!_concepto_destino){
			Form_BANCO_MOVIMIENTO__Mensaje("Por favor introduzca el concepto (destino).","ROJO");
			Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
		}
		_transferencia={
			id_banco_cuenta: _id_banco_cuenta_destino,
			concepto: _concepto_destino
		};
	}
	else{
		_id_banco_cuenta_destino="";
		_concepto_destino="";
	}
	
	
	if(xGetElementById("TOTAL_DEBE_FBM_DC").value!=xGetElementById("TOTAL_HABER_FBM_DC").value){
		Form_BANCO_MOVIMIENTO__Mensaje("El total por el debe no coincide con el total del haber.","ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado("");
		return;
		}
	
	//verificar si existe la cuenta contable de la cuenta seleccionada en detalles contables, por el tipo de operacion
	var _operacion=_banco_movimiento_tipo[1];
	var _id_cuenta_contable=xGetElementById("CTA_CODIGO_CONTABLE_FBM").value;
	var _encontro=false;
	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++)
		if(_operacion==Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3] && _id_cuenta_contable==Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]) {
			_encontro=true;
			break;
		}
	
	if(Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables>0) 
		if(!_encontro) {
			Form_BANCO_MOVIMIENTO__Mensaje("No se encontro en los detalles contables la cuenta asociada a la cuenta bancaria por el "+(_operacion=="D"?"debe":"haber")+".","ROJO");
			Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
		}
	
	
	
	
	Form_BANCO_MOVIMIENTO__DesactivarFormulario();

	var _detalle={};
	_detalle.presupuestario=[];
	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;i++){
		_detalle.presupuestario[i]={
			id_accion_subespecifica: Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0],
			id_cuenta_presupuestaria: Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2],
			operacion: Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6],
			monto: Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]
		};
	}
	
	_detalle.contable=[];
	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++){
		_detalle.contable[i]={
			id_cuenta_contable: Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0],
			operacion: Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3],
			monto: Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]			
		};
	}
	

	
	_detalle.comprobante_bancario={
		id_banco_cuenta: _id_banco_cuenta,
		id_banco_movimiento_tipo: _id_banco_movimiento_tipo,
		numero: _numero,
		monto: _monto,
		transferencia: _transferencia
	};
	
	
	
	var _id_comprobante="";
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_BANCO_MOVIMIENTO__IDSeleccionActualLista;
	

	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_BANCO_MOVIMIENTO__ActivarFormulario();
			return;
			}
		}
		
	var _id_persona="null";
	if (Form_BANCO_MOVIMIENTO__id_persona>0)
		_id_persona=Form_BANCO_MOVIMIENTO__id_persona;
	
	var _contabilizado='f';
	if(xGetElementById("CHECK_CONTABILIZAR_FBM").checked)
		_contabilizado='t';
	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo': 'MB',
										'fecha':_fecha,
										'concepto':_concepto,
										'contabilizado': _contabilizado,
										'id_persona': _id_persona,										
										'detalle': Ext.encode(_detalle)
										},
						'onSuccess':Form_BANCO_MOVIMIENTO__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		
		
	}
	

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_BANCO_MOVIMIENTO__GuardarMensaje(req){
	Form_BANCO_MOVIMIENTO__ActivarBotonGuardar();
	Form_BANCO_MOVIMIENTO__ActivarFormulario(false);
	
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_BANCO_MOVIMIENTO__Nuevo();
		Form_BANCO_MOVIMIENTO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_BANCO_MOVIMIENTO__Mensaje(respuesta.message,"ROJO");
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_BANCO_MOVIMIENTO__BuscarListado(){
	Form_BANCO_MOVIMIENTO__IDSeleccionActualLista=-1;
	Form_BANCO_MOVIMIENTO__IDTipoOperacionSeleccionActualLista=-1;
	Form_BANCO_MOVIMIENTO__id_persona="";
	//xGetElementById("FORMULARIO_FBM").reset();
	Form_BANCO_MOVIMIENTO__ActivarFormulario(true);
	Form_BANCO_MOVIMIENTO__DesactivarBotonModificar();
	Form_BANCO_MOVIMIENTO__DesactivarBotonEliminar();
	Form_BANCO_MOVIMIENTO__ActivarBotonGuardar();
	
	Form_BANCO_MOVIMIENTO__ModificarTabla=false;
	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	
	xGetElementById("NUMERO_COMPROBANTE_FBM").innerHTML="";


	var CadenaBuscar=xGetElementById("ID_CTA_BUSCAR_FBM").value;
	if(CadenaBuscar=="")
		return;
	
	xGetElementById("TABLA_LISTA_FBM").innerHTML=IconoCargandoTabla;

	var FechaInicio=xGetElementById("FECHA_INICIO_FBM").value;
	var FechaFin=xGetElementById("FECHA_FIN_FBM").value;

	if(!EsFechaValida(FechaInicio))
		xGetElementById("FECHA_INICIO_FBM").value="";
	if(!EsFechaValida(FechaFin))
		xGetElementById("FECHA_FIN_FBM").value="";
	
	if(FechaInicio){
		var tmp=String(FechaInicio).split("/");
		FechaInicio=tmp[2]+"-"+tmp[1]+"-"+tmp[0];
	}

	if(FechaFin){
		var tmp=String(FechaFin).split("/");
		FechaFin=tmp[2]+"-"+tmp[1]+"-"+tmp[0];
	}

	var _mostrar={
		//'mes':xGetElementById("MES_FILTRAR_FOP").value,
		'id_banco_cuenta': CadenaBuscar,
		'fecha_inicio':FechaInicio,
		'fecha_culminacion':FechaFin,
		'tipo':['MB']
	};

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"C.fecha","direction":"ASC"},{"property":"operacion","direction":"ASC"},{"property":"correlativo","direction":"ASC"}]'									
									},
					'onSuccess':Form_BANCO_MOVIMIENTO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

	

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_BANCO_MOVIMIENTO__MostrarListado(req){
	var respuesta = req.responseText;
	if(!respuesta)
		return;
	var resultado = eval("(" + respuesta + ")");
	var saldo=resultado["saldo_previo"]*1.0;
	
	//console.log(resultado);
	
	resultado=resultado["result"];
	var n=resultado.length;

	xGetElementById("TABLA_LISTA_FBM").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	//var saldo_inicial=resultado["saldo_previo"]*1.0;
	
	
	for(var i=0;i< n; i++){
		estado="";
		color_estado="";
		if(resultado[i]['contabilizado']=="t" && resultado[i]['anulado']=='t'){
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
		
		if(resultado[i]['id_tipo_operacion_bancaria']!="-1"){//cheques sin emitir, ultima fila
			FuncionOnclick="Form_BANCO_MOVIMIENTO__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
			FuncionOnDblclick="Form_BANCO_MOVIMIENTO__TabPane.setSelectedIndex(0);";
			FuncionOnMouseOver="pintarFila(\"FBM"+resultado[i]['id']+"\")";
			FuncionOnMouseOut="despintarFila(\"FBM"+resultado[i]['id']+"\")";
			}
		else{
			FuncionOnclick="";
			FuncionOnDblclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			}
		
		Contenido+="<TR id='FBM"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";

		Contenido+="<TD width='11%' class='FilaEstilo'>"+resultado[i]['operacion_codigo']+"("+resultado[i]['numero']+")</TD>";


		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+resultado[i]['fecha']+"</TD>";
		Contenido+="<TD width='' class='FilaEstiloContinua'>"+resultado[i]['concepto']+"</TD>";
		if(resultado[i]['operacion']=='D'){
			Contenido+="<TD width='9%' class='FilaEstilo' align='right'>"+ FormatearNumero(resultado[i]['monto']) +"</TD>";
			Contenido+="<TD width='9%' class='FilaEstilo'></TD>";
			saldo+=resultado[i]['monto']*1.0;
			}
		else{
			Contenido+="<TD width='9%' class='FilaEstilo'></TD>";
			Contenido+="<TD width='9%' class='FilaEstilo' align='right'>"+ FormatearNumero(resultado[i]['monto']) +"</TD>";
			saldo-=resultado[i]['monto']*1.0;
			}
		Contenido+="<TD width='12%' class='FilaEstilo' align='right'>"+ FormatearNumero(saldo) +"</TD>";
		//Contenido+="<TD width='7%'  class='FilaEstilo'>"+(resultado[i]['activa_cta_bancaria']=='t'?"ACTIVA":"INACTIVA")+"</TD>";
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FBM").innerHTML=Contenido;
	}

var Form_BANCO_MOVIMIENTO__id_persona="";
function Form_BANCO_MOVIMIENTO__SeleccionarElementoTabla(IDSeleccion,estado){
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista==IDSeleccion)
		return;
	
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista!=-1)
		xGetElementById("FBM"+Form_BANCO_MOVIMIENTO__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FBM"+IDSeleccion).bgColor=colorBase;
	
	
	
	Form_BANCO_MOVIMIENTO__IDSeleccionActualLista=IDSeleccion;
	
	xGetElementById("FECHA_FBM").value="";
	xGetElementById("TIPO_OPERACION_FBM").value="";
	Form_BANCO_MOVIMIENTO__ChangeOperacion();
	
	xGetElementById("ID_CTA_FBM").value="";
	xGetElementById("NCTA_FBM").value="";
	xGetElementById("DESCRIPCION_NCTA_FBM").value="";
	xGetElementById("TIPO_CTA_FBM").value="";
	xGetElementById("BANCO_FBM").value="";
	xGetElementById("CTA_CODIGO_CONTABLE_FBM").value="";
	xGetElementById("CONCEPTO_FBM").value="";
	
	xGetElementById("ID_CTA_FBM2").value="";
	xGetElementById("NCTA_FBM2").value="";
	xGetElementById("DESCRIPCION_NCTA_FBM2").value="";
	xGetElementById("TIPO_CTA_FBM2").value="";
	xGetElementById("BANCO_FBM2").value="";
	xGetElementById("CTA_CODIGO_CONTABLE_FBM2").value="";
	xGetElementById("CONCEPTO_FBM2").value="";
	
	xGetElementById("DOCUMENTO_FBM").value="";	
	xGetElementById("MONTO_FBM").value="";

	xGetElementById("CHECK_CONTABILIZAR_FBM").checked=false;
	//xGetElementById("TAB_CONTABILIZAR_FBM").style.visibility="hidden";
	
	Form_BANCO_MOVIMIENTO__DesactivarFormulario();
	Form_BANCO_MOVIMIENTO__DesactivarBotonGuardar();
	Form_BANCO_MOVIMIENTO__Mensaje("");
	Form_BANCO_MOVIMIENTO__MensajeListado("");
	Form_BANCO_MOVIMIENTO__id_persona="";

	xGetElementById("NUMERO_COMPROBANTE_FBM").innerHTML="";
	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_BANCO_MOVIMIENTO__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								//console.log(resultado);
								xGetElementById("NUMERO_COMPROBANTE_FBM").innerHTML=resultado[0]["tipo"]+"-"+resultado[0]["correlativo"];
								
								Form_BANCO_MOVIMIENTO__id_persona=resultado[0]["id_persona"];
								xGetElementById("FECHA_FBM").value=resultado[0]["fecha"];
								xGetElementById("CONCEPTO_FBM").value=resultado[0]["concepto"];
								
								xGetElementById("TIPO_OPERACION_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["id_banco_movimiento_tipo"]+"|"+resultado[0]["detalle_comprobante_bancario"][0]["operacion"]+"|"+resultado[0]["detalle_comprobante_bancario"][0]["operacion_codigo"];
								Form_BANCO_MOVIMIENTO__ChangeOperacion();								
								xGetElementById("ID_CTA_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["id_banco_cuenta"];
								xGetElementById("NCTA_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["numero_cuenta"];
								xGetElementById("DESCRIPCION_NCTA_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_denominacion"];
								xGetElementById("TIPO_CTA_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_tipo"];
								xGetElementById("BANCO_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["banco"];
								xGetElementById("CTA_CODIGO_CONTABLE_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["id_cuenta_contable"];
								
								xGetElementById("DOCUMENTO_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["numero"];	
								xGetElementById("MONTO_FBM").value=resultado[0]["detalle_comprobante_bancario"][0]["monto"];
								
								if(resultado[0]["contabilizado"]=="t"){
									xGetElementById("CHECK_CONTABILIZAR_FBM").checked=true;
									//xGetElementById("TAB_CONTABILIZAR_FBM").style.visibility="inherit";
								}
								
								//cargar detalle presupuestarios								
								if(resultado[0]["detalle_presupuestario"]){
									var n=resultado[0]["detalle_presupuestario"].length;
									Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios=n
									for(var i=0;i<n;i++){
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i]=[];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]=resultado[0]["detalle_presupuestario"][i]['estructura_presupuestaria'];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][4]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][5]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]=resultado[0]["detalle_presupuestario"][i]["operacion"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=resultado[0]["detalle_presupuestario"][i]["monto"];
										}
									Form_BANCO_MOVIMIENTO__MostrarTablaDP();
									}
								
								//cargar detalle contable
								if(resultado[0]["detalle_contable"]){
									var n=resultado[0]["detalle_contable"].length;
									Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables=n;
									for(var i=0;i<n;i++){
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i]=[];
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][1]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]=resultado[0]["detalle_contable"][i]["denominacion"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=resultado[0]["detalle_contable"][i]["operacion"];
										Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=resultado[0]["detalle_contable"][i]["monto"];
										}
									Form_BANCO_MOVIMIENTO__MostrarTablaDC();
									}
								
								
								Form_BANCO_MOVIMIENTO__ActivarBotonModificar();
								Form_BANCO_MOVIMIENTO__ActivarBotonEliminar();
								
								
								

								},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}


function Form_BANCO_MOVIMIENTO__CargarArregloDP(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");
	if(resultado==false){
		Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios=0;
		Form_BANCO_MOVIMIENTO__MostrarTablaDP();
		return;
		}
	var n=resultado.length;

	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios=n;
	for(var i=0;i<n;i++){
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i]=new Array(8);
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0]=resultado[i]["id_movimiento_presupuestario"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][1]=resultado[i]["id_comprobante"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]=FormatearCodigoProgramatico(resultado[i]['codigo_centralizada'],resultado[i]['codigo_especifico'],resultado[i]['codigo_otras']);
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]=resultado[i]["id_codigo_plan_unico"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][4]=resultado[i]["monto_mp"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][5]=resultado[i]["id_estruc_presupuestaria"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]=resultado[i]["id_operacion"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=resultado[i]["tipo_operacion"];
		}
	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
	}

function Form_BANCO_MOVIMIENTO__CargarArregloDC(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");
	if(resultado==false){
		Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables=0;
		Form_BANCO_MOVIMIENTO__MostrarTablaDC();
		return;
		}
	var n=resultado.length;
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables=n;
	for(var i=0;i<n;i++){
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i]=new Array(5);
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]=resultado[i]["id_movimiento_contable"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][1]=resultado[i]["id_comprobante"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]=resultado[i]["id_codigo_contable"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=resultado[i]["tipo_operacion_mc"];
		Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=resultado[i]["monto_mc"];
		}
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	}



/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado(){
	Form_BANCO_MOVIMIENTO__ModificarTabla=false;
	Form_BANCO_MOVIMIENTO__IDComprobante="";
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios=0;
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables=0;
	xGetElementById("TABLA_LISTA_ARTICULOS_FBM_DP").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FBM_DC").innerHTML="";

	Form_BANCO_MOVIMIENTO__IDBancoSeleccionActualLista=-1;
	Form_BANCO_MOVIMIENTO__IDTipoOperacionSeleccionActualLista=-1;
	Form_BANCO_MOVIMIENTO__IDSeleccionActualLista=-1;
	Form_BANCO_MOVIMIENTO__Denominacion="";
	xGetElementById("FORMULARIO_FBM").reset();
	Form_BANCO_MOVIMIENTO__DesactivarBotonModificar();
	Form_BANCO_MOVIMIENTO__DesactivarBotonEliminar();
	Form_BANCO_MOVIMIENTO__ActivarBotonGuardar();
	Form_BANCO_MOVIMIENTO__ActivarFormulario();

	//xGetElementById("LISTADO_BUSCAR_FBM").value="";
	Form_BANCO_MOVIMIENTO__Mensaje("");
	Form_BANCO_MOVIMIENTO__MensajeListado("");
	//Form_BANCO_MOVIMIENTO__InicializarFechaEntradaDatos();
	Form_BANCO_MOVIMIENTO__CargarSelectTipoOperacion();
	Form_BANCO_MOVIMIENTO__BuscarListado();
	//DarFocoCampo("LISTADO_BUSCAR_FBM",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_BANCO_MOVIMIENTO__Modificar(){
	Form_BANCO_MOVIMIENTO__ModificarTabla=true;
	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	Form_BANCO_MOVIMIENTO__ActivarFormulario();
	Form_BANCO_MOVIMIENTO__ActivarBotonGuardar();
	Form_BANCO_MOVIMIENTO__DesactivarBotonModificar();
	Form_BANCO_MOVIMIENTO__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_BANCO_MOVIMIENTO__Eliminar(){
	var _id_comprobante="";
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_BANCO_MOVIMIENTO__IDSeleccionActualLista;
	
	if(_id_comprobante=="")
		return;

	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_comprobante},
				'onSuccess':Form_BANCO_MOVIMIENTO__EliminarMensaje,
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_BANCO_MOVIMIENTO__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado();
		Form_BANCO_MOVIMIENTO__Mensaje(respuesta.message,"VERDE");
		Form_BANCO_MOVIMIENTO__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_BANCO_MOVIMIENTO__Mensaje(respuesta.message,"ROJO");
		Form_BANCO_MOVIMIENTO__MensajeListado(respuesta.message,"ROJO");
		}
	}


var Form_MOV_DP__iSeleccionActual=-1;
var Form_MOV_DC__iSeleccionActual=-1;


function Form_BANCO_MOVIMIENTO__MostrarTablaDP(){
	Form_MOV_DP__iSeleccionActual=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnDblclickMONTO="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL=0;

	var sw;
	sw=Form_BANCO_MOVIMIENTO__ModificarTabla;
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista==-1)
		sw=true;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FBM_DP","IMG_AGREGAR_FBM_DP",'agregar');
		ActivarBoton("BOTON_QUITAR_FBM_DP","IMG_QUITAR_FBM_DP",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FBM_DP","IMG_AGREGAR_FBM_DP",'agregar');
		DesactivarBoton("BOTON_QUITAR_FBM_DP","IMG_QUITAR_FBM_DP",'quitar');
		}

	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DP__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DP__ModificarValorCelda("+i+")";
			}


		TOTAL+=Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]*1.0;

		Contenido+="<TR class='FilaListado' id='FBM_DP"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='15%'  style='font-size : 11px;'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]+"</TD>";
		Contenido+="<TD width='10%' align='center' style='font-size : 11px;'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][4]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][5]+"</TD>";
		Contenido+="<TD width='7%'  align='center' style='font-size : 11px;'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]+"</TD>";
		Contenido+="<TD width='10%' align='right'  style='font-size : 11px;' id='TD_FBM_DP_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FBM_DP").innerHTML=Contenido;
	xGetElementById("TOTAL_COMPROMISOS_FBM_DP").value=FormatearNumero(TOTAL);


	}



function Form_MOV_DP__SeleccionarElementoTabla(i){
	if(Form_MOV_DP__iSeleccionActual!=-1)
		xGetElementById("FBM_DP"+Form_MOV_DP__iSeleccionActual).style.background="";
	xGetElementById("FBM_DP"+i).style.background=colorSeleccionTabla;
	Form_MOV_DP__iSeleccionActual=i;
	}

function Form_MOV_DP__ModificarValorCelda(i){
	if(xGetElementById("FBM_DP_txt_celda"))
		return;
	Valor=Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FBM_DP_"+i).innerHTML="<INPUT id='FBM_DP_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DP__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FBM_DP_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DP__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FBM_DP_txt_celda").focus();
	}

function Form_MOV_DP__ModificarValorCeldaPierdeFoco(i){
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=numberFormat(xGetElementById("FBM_DP_txt_celda").value,2);
	xGetElementById("TD_FBM_DP_"+i).innerHTML=FormatearNumero(Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;k++)
		TOTAL+=Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[k][7]*1.0;
	xGetElementById("TOTAL_COMPROMISOS_FBM_DP").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DP__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios)
			return;		
		xGetElementById("FBM_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DP__ModificarValorCelda(i+1);
		Form_MOV_DP__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FBM_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DP__ModificarValorCelda(i-1);
		Form_MOV_DP__SeleccionarElementoTabla(i-1);
		}
	}


function Form_BANCO_MOVIMIENTO__MostrarTablaDC(){
	Form_MOV_DC__iSeleccionActual=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL_DEBE=0;
	var TOTAL_HABER=0;
	var FuncionOnDblclickMONTO="";
	var sw;//sw indica si la tabla se puede editar
	sw=Form_BANCO_MOVIMIENTO__ModificarTabla;
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista==-1)
		sw=true;


	if(sw){
		ActivarBoton("BOTON_AGREGAR_FBM_DC","IMG_AGREGAR_FBM_DC",'agregar');
		ActivarBoton("BOTON_QUITAR_FBM_DC","IMG_QUITAR_FBM_DC",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FBM_DC","IMG_AGREGAR_FBM_DC",'agregar');
		DesactivarBoton("BOTON_QUITAR_FBM_DC","IMG_QUITAR_FBM_DC",'quitar');
		}

	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DC__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DC__ModificarValorCelda("+i+")";
			}

		Contenido+="<TR class='FilaListado' id='FBM_DC"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		Contenido+="<TD width='15%' style='font-size : 12px;'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][1]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]+"</TD>";

		if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=='D' || Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=='d'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FBM_DC_"+i+"'>"  +FormatearNumero(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4])+"</TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_BANCO_MOVIMIENTO__CambiarOperacionContable("+i+",'H');\"></TD>";
			TOTAL_DEBE+=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]*1.0;
			}
		else if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=='H' || Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=='h'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_BANCO_MOVIMIENTO__CambiarOperacionContable("+i+",'D');\"></TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FBM_DC_"+i+"'>" +FormatearNumero(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4])+"</TD>";
			TOTAL_HABER+=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]*1.0;
			}

		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FBM_DC").innerHTML=Contenido;
	xGetElementById("TOTAL_DEBE_FBM_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FBM_DC").value=FormatearNumero(TOTAL_HABER);

	}

function Form_BANCO_MOVIMIENTO__CambiarOperacionContable(i,Operacion){
	var sw=Form_BANCO_MOVIMIENTO__ModificarTabla;
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista==-1)
		sw=true;
	if(sw==false) return;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=Operacion;
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	}


function Form_MOV_DC__SeleccionarElementoTabla(i){
	if(Form_MOV_DC__iSeleccionActual!=-1)
		xGetElementById("FBM_DC"+Form_MOV_DC__iSeleccionActual).style.background="";
	xGetElementById("FBM_DC"+i).style.background=colorSeleccionTabla;
	Form_MOV_DC__iSeleccionActual=i;
	}

function Form_MOV_DC__ModificarValorCelda(i){
	if(xGetElementById("FBM_DC_txt_celda"))
		return;
	Valor=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FBM_DC_"+i).innerHTML="<INPUT id='FBM_DC_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DC__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FBM_DC_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DC__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FBM_DC_txt_celda").focus();
	}

function Form_MOV_DC__ModificarValorCeldaPierdeFoco(i){
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=numberFormat(xGetElementById("FBM_DC_txt_celda").value,2);
	xGetElementById("TD_FBM_DC_"+i).innerHTML=FormatearNumero(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]);
	//recalcular la suma
	var TOTAL_DEBE=0;
	var TOTAL_HABER=0;
	for(var k=0;k<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;k++){
		if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[k][3]=="D"||Form_BANCO_MOVIMIENTO__ArregloDetallesContables[k][3]=="d")
			TOTAL_DEBE+=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[k][4]*1.0;
		else
			TOTAL_HABER+=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[k][4]*1.0;
		}
	xGetElementById("TOTAL_DEBE_FBM_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FBM_DC").value=FormatearNumero(TOTAL_HABER);
	}

function Form_MOV_DC__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables)
			return;
		xGetElementById("FBM_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DC__SeleccionarElementoTabla(i+1)
		Form_MOV_DC__ModificarValorCelda(i+1);
		}
	else if(event.keyCode==38){//key up
		if((i-1)<0)
			return;
		xGetElementById("FBM_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DC__SeleccionarElementoTabla(i-1)
		Form_MOV_DC__ModificarValorCelda(i-1);
		}
	}

function Form_BANCO_MOVIMIENTO__AgregarElementoArregloDP(id_movimiento_presupuestario,
																id_comprobante,
																codigo_estructura_presupuestaria,
																id_codigo_plan_unico,
																monto_mp,
																id_estructura_presupuestaria,
																id_codigo_contable){
	//buscamos si ya no se a insertado antes, es decir, si existe en el arreglo, si existe returnamos false
	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;i++)
		if(Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]==codigo_estructura_presupuestaria && Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]==id_codigo_plan_unico)
			return false;
	//si no existe lo insertamos y retornamos true

	//Insertamos el movimiento contable correspondiente Debe o Hoaber depende del tipo de documento
	var TipoOperacion = String(xGetElementById("TIPO_OPERACION_FBM").value).split("|");
	if(TipoOperacion.length==3)
		TipoOperacion=TipoOperacion[1];
	else
		TipoOperacion="D";



	Form_BANCO_MOVIMIENTO__AgregarElementoArregloDC("-1","0",id_codigo_contable,TipoOperacion,monto_mp);



	//cuando modifiquemos, los agregados el numero de compromiso deben tener todo el mismo n
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista!=-1)
		if(id_comprobante=='0'||id_comprobante==0)
			if(!(Form_BANCO_MOVIMIENTO__IDComprobante=="null"||Form_BANCO_MOVIMIENTO__IDComprobante==""))
				id_comprobante=Form_BANCO_MOVIMIENTO__IDComprobante;

	var i;
	i=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i]=new Array(8);
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0]=id_movimiento_presupuestario;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][1]=id_comprobante;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]=codigo_estructura_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]=id_codigo_plan_unico;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][4]=monto_mp;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][5]=id_estructura_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]=xGetElementById("OPERACION_FRDDADP").options[xGetElementById("OPERACION_FRDDADP").selectedIndex].value;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=xGetElementById("OPERACION_FRDDADP").options[xGetElementById("OPERACION_FRDDADP").selectedIndex].innerHTML;

//alert(xGetElementById("OPERACION_FRDDADP").options[xGetElementById("OPERACION_FRDDADP").selectedIndex].innerHTML);

	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios++;
	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
	//xGetElementById("BOTONES_AGREGAR_QUITAR_FBM_DP").style.visibility="inherit";
	return true;
	}

function Form_BANCO_MOVIMIENTO__AgregarElementoArregloDC(id_movimiento_contable,
																id_comprobante,
																id_codigo_contable,
																tipo_operacion_mc,
																monto_mc){


	for(var i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++)
		if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]==id_codigo_contable){
			if(id_movimiento_contable=="-1"){//si viene de movimiendo presupuestario, buscar si hay uno igual y sumarle el mondo si existe, sino existe se agrega
				Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4])*1.0+monto_mc*1.0;
				Form_BANCO_MOVIMIENTO__MostrarTablaDC();
				return true;
				}
			else{//buscamos si ya no se a insertado antes, es decir, si existe en el arreglo, si existe returnamos false
				if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]=="-1")
					continue;
				return false;
				}
			}
	//si no existe lo insertamos y retornamos true


	//cuando modifiquemos, los agregados el numero de compromiso deben tener todo el mismo n
	if(Form_BANCO_MOVIMIENTO__IDSeleccionActualLista!=-1)
		if(id_comprobante=='0'||id_comprobante==0)
			if(!(Form_BANCO_MOVIMIENTO__IDComprobante=="null"||Form_BANCO_MOVIMIENTO__IDComprobante==""))
				id_comprobante=Form_BANCO_MOVIMIENTO__IDComprobante;

	var i;
	i=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i]=new Array(5);
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]=id_movimiento_contable;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][1]=id_comprobante;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]=id_codigo_contable;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=tipo_operacion_mc;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=monto_mc;
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables++;
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	return true;
	}


function Form_BANCO_MOVIMIENTO__AbrirReporteEstadoDeCuenta(){
	var IDCuenta=quitarCodigoCeros(xTrim(xGetElementById("ID_CTA_BUSCAR_FBM").value));
	if(IDCuenta=="")
		return;

	if(xTrim(xGetElementById("NCTA_BUSCAR_FBM").value)==""){
		return;
		}

	var FechaInicio=xTrim(xGetElementById("FECHA_INICIO_FBM").value);
	var FechaFin=xTrim(xGetElementById("FECHA_FIN_FBM").value);

	if(FechaInicio!="")
		if(!EsFechaValida(FechaInicio)){
			Form_BANCO_MOVIMIENTO__MensajeListado("La fecha de inicio es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
			//Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
			}
	if(FechaFin!="")
		if(!EsFechaValida(FechaFin)){
			Form_BANCO_MOVIMIENTO__MensajeListado("La fecha de finalización es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
			//Form_BANCO_MOVIMIENTO__MensajeListado("");
			return;
			}

	Form_IMPRIMIR_ESTADO_CTA__Abrir(true);
	}





function Form_BANCO_MOVIMIENTO__AgregarDP() {
	siga.open("detalle_presupuestario",{
		operacion: ['CCP'],
		onAdd: function(me){
			//agregar detalle presupuestario
			Form_BANCO_MOVIMIENTO__AgregarItemDP(me.internal.data.id_accion_subespecifica,
																					 me.internal.data.id_cuenta_presupuestaria,
																					 me.internal.data.estructura_presupuestaria,
																					 me.internal.data.cuenta_presupuestaria,
																					 me.internal.data.denominacion_presupuestaria,
																					 me.internal.data.operacion,
																					 me.internal.data.monto);
			
			//agregar detalle contable
			Form_BANCO_MOVIMIENTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						'D',
																						me.internal.data.monto
																						);
		}
	});	
}

function Form_BANCO_MOVIMIENTO__AgregarItemDP(id_accion_subespecifica,id_cuenta_presupuestaria,estructura_presupuestaria,cuenta_presupuestaria,denominacion_presupuestaria,operacion,monto){
	for(i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;i++)
		if(Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0]==id_accion_subespecifica &&
			 Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]==id_cuenta_presupuestaria &&
			 Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]==operacion){
			Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]*1+monto*1;
			Form_BANCO_MOVIMIENTO__MostrarTablaDP();
			return;
			}
	
	var i=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i]=new Array(8);
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][0]=id_accion_subespecifica;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][2]=id_cuenta_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][3]=estructura_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][4]=cuenta_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][5]=denominacion_presupuestaria;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][6]=operacion;
	Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][7]=monto;
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios++;
	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
}

function Form_BANCO_MOVIMIENTO__AgregarDC() {
	var _id_cuenta_contable=xGetElementById("CTA_CODIGO_CONTABLE_FBM").value;
	var _monto=xGetElementById("MONTO_FBM").value;
	var _banco_movimiento_tipo=(xGetElementById("TIPO_OPERACION_FBM").value).split("|");
	var _operacion=_banco_movimiento_tipo[1];
	
	siga.open("detalle_contable",{
		id_cuenta_contable: _id_cuenta_contable,
		operacion: _operacion,
		monto: _monto,
		tooltip: 'Cuenta asociada a la cuenta bancaria',		
		onAdd: function(me){			
			Form_BANCO_MOVIMIENTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						me.internal.data.operacion,
																						me.internal.data.monto
																						);
		}
	});
}


function Form_BANCO_MOVIMIENTO__AgregarItemDC(id_cuenta_contable, cuenta_contable, denominacion, operacion, monto){
	for(i=0;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++)
		if(Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]==id_cuenta_contable &&
			 Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]==operacion){
			Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]*1+monto*1;
			Form_BANCO_MOVIMIENTO__MostrarTablaDC();
			return;
			}
	
	var i=Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i]=new Array(5);
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][0]=id_cuenta_contable;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][1]=cuenta_contable;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][2]=denominacion;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][3]=operacion;
	Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][4]=monto;
	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables++;
	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
}







function Form_BANCO_MOVIMIENTO__DP_Quitar(){
	if(Form_MOV_DP__iSeleccionActual==-1)
		return;

	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios--;
	for(i=Form_MOV_DP__iSeleccionActual*1;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesPresupuestarios;i++)
 		for(j=0;j<8;j++)
 			Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i][j]= Form_BANCO_MOVIMIENTO__ArregloDetallesPresupuestarios[i+1][j];

	Form_BANCO_MOVIMIENTO__MostrarTablaDP();
	}

function Form_BANCO_MOVIMIENTO__DC_Quitar(){
	if(Form_MOV_DC__iSeleccionActual==-1)
		return;

	Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables--;
	for(i=Form_MOV_DC__iSeleccionActual*1;i<Form_BANCO_MOVIMIENTO__TamanoArregloDetallesContables;i++)
 		for(j=0;j<5;j++)
 			Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i][j]= Form_BANCO_MOVIMIENTO__ArregloDetallesContables[i+1][j];

	Form_BANCO_MOVIMIENTO__MostrarTablaDC();
	}

function Form_BANCO_MOVIMIENTO__Visualizar(){
	var _id_banco_cuenta=xGetElementById("ID_CTA_BUSCAR_FBM").value;
	var _fecha_inicio=xGetElementById("FECHA_INICIO_FBM").value;
	var _fecha_culminacion=xGetElementById("FECHA_FIN_FBM").value;
	Form_BANCO_MOVIMIENTO__TabPane.setSelectedIndex(1);
	Form_BANCO_MOVIMIENTO__Mensaje("");
	Form_BANCO_MOVIMIENTO__MensajeListado("");
	
	if(!_id_banco_cuenta) {		
		Form_BANCO_MOVIMIENTO__MensajeListado("Por favor seleccione la cuenta bancaria.","ROJO");
		return;
	}
	
	if(!_fecha_inicio) {
		Form_BANCO_MOVIMIENTO__MensajeListado("Por ingrese la fecha de inicio.","ROJO");
		return;
	}
	
	if(!EsFechaValida(_fecha_inicio)){
		Form_BANCO_MOVIMIENTO__MensajeListado("La fecha de inicio es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		return;
	}
		
	if(!_fecha_culminacion) {
		Form_BANCO_MOVIMIENTO__MensajeListado("Por ingrese la fecha de culminación.","ROJO");
		return;
	}
	
	if(!EsFechaValida(_fecha_culminacion)){
		Form_BANCO_MOVIMIENTO__MensajeListado("La fecha de culminación es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		return;
		}
	window.open("../../report/estado_cuenta.php?fecha_inicio="+_fecha_inicio+"&fecha_culminacion="+_fecha_culminacion+"&id_banco_cuenta="+_id_banco_cuenta);
}

function Form_BANCO_MOVIMIENTO__inputText(){
	Ext.MessageBox.show({
		title: 'Ingresar detalles por txt',
		msg: "<b>Ejemplo para entradas:</b><span style='font-size: 10px;'><br>00070081920000002654 ND 6000.00<br>ACC000001-00-01 401010100 CCP 200.00<br>ACC000002-00-01 401010200 CCP 800.00<br>PRO124900-02-02 403100700 CCP 5000.00<br>611000000000 D 1000<br>613000000000 D 5000.00<br>111010201001 H 6000.00</span>",
		width:700,
		buttons: Ext.MessageBox.OKCANCEL,
		multiline: true,
		defaultTextHeight: 300,
		value: '',
		fn: function(btn, text){
			if(btn=="cancel") return;
			
			var _tmp="";
			var linea=text.split("\n");
			var segmento="";
			for(var i=0;i<linea.length;i++){
				segmento=linea[i].split(" ");
				//validar entradas
				if(segmento[0].length==20 && segmento[1].length==2 && (segmento[1]=="NC" || segmento[1]=="ND" || segmento[1]=="RT" || segmento[1]=="DP" || segmento[1]=="BI") && segmento[2]*1>=0 ) {
					console.log("Agregando detalle bancario: "+linea[i]);
					_tmp=Ext.Ajax.request({
						async: false,
						url:"module/banco_movimiento_tipo/",
						params: {
							action: 'onGet',
							tipo: segmento[1]
						}
					});
					if(_tmp.statusText=="OK"){
						var _retorno=Ext.JSON.decode(_tmp.responseText);
						if(_retorno.length>0){
							document.getElementById("TIPO_OPERACION_FBM").value=_retorno[0]["id"];
							
							xGetElementById("TIPO_OPERACION_FBM").value=_retorno[0]["id"]+"|"+_retorno[0]["operacion"]+"|"+_retorno[0]["codigo"];
							Form_BANCO_MOVIMIENTO__ChangeOperacion();
							_tmp=Ext.Ajax.request({
								async: false,
								url:"module/banco_cuenta/",
								params: {
									action: 'onGet',
									numero_cuenta: segmento[0]
								}
							});
							if(_tmp.statusText=="OK"){
								var _retorno=Ext.JSON.decode(_tmp.responseText);
								if(_retorno.length>0){
							
								xGetElementById("ID_CTA_FBM").value=_retorno[0]["id"];
								xGetElementById("NCTA_FBM").value=_retorno[0]["numero_cuenta"];
								xGetElementById("DESCRIPCION_NCTA_FBM").value=_retorno[0]["denominacion"];
								xGetElementById("TIPO_CTA_FBM").value=_retorno[0]["cuenta_tipo"];
								xGetElementById("BANCO_FBM").value=_retorno[0]["banco"];
								xGetElementById("CTA_CODIGO_CONTABLE_FBM").value=_retorno[0]["id_cuenta_contable"];
								
								xGetElementById("MONTO_FBM").value=numberFormat(segmento[2]*1,2);
								}
							}						
						}
					}
				}
				else if(segmento[0].length==12 && segmento[1].length==1 && (segmento[1]=="D" || segmento[1]=="H") && segmento[2]*1>=0 ) {
					console.log("Agregando detalle contable: "+linea[i]);
					_tmp=Ext.Ajax.request({
						async: false,
						url:"module/cuenta_contable/",
						params: {
							action: 'onGet',
							id_cuenta_contable: segmento[0]
						}
					});
					if(_tmp.statusText=="OK"){
						var _retorno=Ext.JSON.decode(_tmp.responseText);
						if(_retorno.length>0){
							Form_BANCO_MOVIMIENTO__AgregarItemDC(	segmento[0],
																										_retorno[0]["cuenta_contable"],
																										_retorno[0]["denominacion"],
																										segmento[1],
																										segmento[2]*1
																										);
						}
					}
				}
				else if(segmento[0].split("-").length==3 && (segmento[0].substring(0,3)=="ACC" || segmento[0].substring(0,3)=="PRO") && segmento[1].length==9 && (segmento[2]=="C" || segmento[2]=="CC" || segmento[2]=="CCP" || segmento[2]=="AU" || segmento[2]=="DI" || segmento[2]=="AP" || segmento[2]=="P" || segmento[2]=="GC" || segmento[2]=="NN") && segmento[3]*1>=0 ) {
					console.log("Agregando detalle presupuestario: "+linea[i]);
					_tmp=Ext.Ajax.request({
						async: false,
						url:"module/estructura_presupuestaria/",
						params: {
							action: 'onGet_IdCodigo',
							codigo: segmento[0]
						}
					});
					if(_tmp.statusText=="OK"){
						var _retorno=Ext.JSON.decode(_tmp.responseText);
						if(_retorno.length>0){
							var _id_accion_subespecifica=_retorno[0]["id_accion_subespecifica"];
							
							_tmp=Ext.Ajax.request({
								async: false,
								url:"module/cuenta_presupuestaria/",
								params: {
									action: 'onGet',
									id_cuenta_presupuestaria: segmento[1]
								}
							});
							if(_tmp.statusText=="OK"){
								var _retorno=Ext.JSON.decode(_tmp.responseText);
								if(_retorno.length>0){
									var _cuenta_presupuestaria=_retorno[0]["cuenta_presupuestaria"];
									var _denominacion_cuenta_presupuestaria=_retorno[0]["denominacion"];
									Form_BANCO_MOVIMIENTO__AgregarItemDP(	_id_accion_subespecifica,
																												segmento[1],
																												segmento[0],
																												_cuenta_presupuestaria,
																												_denominacion_cuenta_presupuestaria,
																												segmento[2],
																												segmento[3]*1);
									
								}								
							}
						}
					}	
				}
				else
					console.log("Linea no reconocida: "+linea[i]);
			}
		}
	});
}
