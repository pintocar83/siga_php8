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
* @version 20091009
*/

var Form_ORDEN_PAGO__ArregloDetallesPresupuestarios=[];
var Form_ORDEN_PAGO__ArregloDetallesContables=[];
var Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios=0;
var Form_ORDEN_PAGO__TamanoArregloDetallesContables=0;
var Form_ORDEN_PAGO__ModificarTabla=false;

var Form_ORDEN_PAGO__id_comprobante_previo=[];
var Form_ORDEN_PAGO__ArregloDetallesRetenciones=[];
var Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones=0;

var Form_ORDEN_PAGO__ArregloDetallesCargos=[];
var Form_ORDEN_PAGO__TamanoArregloDetallesCargos=0;

var Form_ORDEN_PAGO__contabilizado="";

var Form_ORDEN_PAGO__tipo="OP";
var Form_ORDEN_PAGO__comprobante_posterior="";

/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_ORDEN_PAGO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FOP").innerHTML=MSG;
	}



/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_ORDEN_PAGO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FOP_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_ORDEN_PAGO__ActivarFormulario(){
	//xGetElementById("REFERENCIA_FOP").readOnly=false;
	xGetElementById("FECHA_FOP").readOnly=false;
	xGetElementById("DENOMINACION_FOP").readOnly=false;
	xGetElementById("BOTON_PROVEEDOR_FOP").disabled=false;
	xGetElementById("BOTON_BENEFICIARIO_FOP").disabled=false;
	xGetElementById("TIPO_DOCUMENTO_FOP").disabled=false;
	xGetElementById("TIPO_DOCUMENTO_FOP").readOnly=false;
	//xGetElementById("REFERENCIA_FOP").setAttribute('class','TextoCampoInput');
	xGetElementById("FECHA_FOP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DENOMINACION_FOP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("TIPO_DOCUMENTO_FOP").setAttribute('class','TextoCampoInputObligatorios');

	xGetElementById("IMG_FECHA_FOP").setAttribute('onclick',"showCalendar('FECHA_FOP','%d/%m/%Y')");
	xGetElementById("FECHA_FOP").setAttribute('ondblclick',"showCalendar('FECHA_FOP','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FOP","IMG_FECHA_FOP",'calendario');
	ActivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP",'buscar');

	if(Form_ORDEN_PAGO__SW_PERSONA=="P")
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute( 'onclick',"Form_ORDEN_PAGO__SeleccionarPersona()");
		//xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FOP','ID_BENEFICIARIO_PROVEEDOR_FOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FOP','','CUENTA_CONTABLE_PB_FOP')");
	else
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute( 'onclick',"Form_ORDEN_PAGO__SeleccionarPersona()");
		//xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FOP','ID_BENEFICIARIO_PROVEEDOR_FOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FOP','','CUENTA_CONTABLE_PB_FOP')");

	xGetElementById("IMG_LIMPIAR_PB_FOP").setAttribute('onclick',"xGetElementById('ID_BoP_FOP').value=''; xGetElementById('ID_BENEFICIARIO_PROVEEDOR_FOP').value=''; xGetElementById('NOMBRE_BENEFICIARIO_PROVEEDOR_FOP').value='';");
	ActivarBoton("IMG_LIMPIAR_PB_FOP","IMG_LIMPIAR_PB_FOP",'limpiar');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_ORDEN_PAGO__DesactivarFormulario(){
	//xGetElementById("REFERENCIA_FOP").readOnly=true;
	xGetElementById("FECHA_FOP").readOnly=true;
	xGetElementById("DENOMINACION_FOP").readOnly=true;
	xGetElementById("BOTON_PROVEEDOR_FOP").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_FOP").disabled=true;
	xGetElementById("TIPO_DOCUMENTO_FOP").disabled=true;
	xGetElementById("TIPO_DOCUMENTO_FOP").readOnly=true;

	//xGetElementById("REFERENCIA_FOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_FOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DENOMINACION_FOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TIPO_DOCUMENTO_FOP").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_FECHA_FOP").setAttribute('onclick',"");
	xGetElementById("FECHA_FOP").setAttribute('ondblclick',"");

	DesactivarBoton("IMG_FECHA_FOP","IMG_FECHA_FOP",'calendario');
	DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP",'buscar');
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute('onclick',"");

	xGetElementById("IMG_LIMPIAR_PB_FOP").setAttribute('onclick',"");
	DesactivarBoton("IMG_LIMPIAR_PB_FOP","IMG_LIMPIAR_PB_FOP",'limpiar');
	}

/**
* Activa el boton modificar
*/
function Form_ORDEN_PAGO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FOP","IMG_MODIFICAR_FOP",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_ORDEN_PAGO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FOP","IMG_MODIFICAR_FOP",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_ORDEN_PAGO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FOP","IMG_GUARDAR_FOP",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_ORDEN_PAGO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FOP","IMG_GUARDAR_FOP",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_ORDEN_PAGO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FOP","IMG_ELIMINAR_FOP",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_ORDEN_PAGO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FOP","IMG_ELIMINAR_FOP",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_ORDEN_PAGO__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_ORDEN_PAGO__IDTipoOperacionSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_ORDEN_PAGO__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_ORDEN_PAGO__BuscarListado_CadenaBuscar="";
var Form_ORDEN_PAGO__SW_PERSONA="";
var Form_ORDEN_PAGO__MenuVisualizar="";

/**
* Inicializador
*/
function Form_ORDEN_PAGO__Init(){
}

/**
* Nueva definicion
*/
function Form_ORDEN_PAGO__Nuevo(){
	xGetElementById("FORMULARIO_FOP").reset();
	Form_ORDEN_PAGO__ModificarTabla=true;
	Form_ORDEN_PAGO__BotonProveedor();
	Form_ORDEN_PAGO__CambioTipoDocumento();
	AjaxRequest.post({
						'parameters':{
									'action':"onGet_Correlativo",
									'tipo':Form_ORDEN_PAGO__tipo								
									},
						'onSuccess': function(req){
											var respuesta = req.responseText;
											var resultado = eval("(" + respuesta + ")");
											xGetElementById("COMPROBANTE_FOP").value=completarCodigoCeros(String(resultado[0]["correlativo"]),10);
											},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	
	
	Form_ORDEN_PAGO__LimpiarInputTextBuscarListado();

	Form_ORDEN_PAGO__TabPane.setSelectedIndex(0);
	}

function Form_ORDEN_PAGO__BotonProveedor(bloquear){
	xGetElementById("TIPO_PERSONA_FOP").innerHTML="Proveedor";
	//xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute('onclick',bloquear==true?"":"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FOP','ID_BENEFICIARIO_PROVEEDOR_FOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FOP','','CUENTA_CONTABLE_PB_FOP')");
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute('onclick',bloquear==true?"":"Form_ORDEN_PAGO__SeleccionarPersona()");
	xGetElementById("ID_BoP_FOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FOP").value="";
	Form_ORDEN_PAGO__SW_PERSONA="P";
	Form_ORDEN_PAGO__PERSONA_cuenta_destino=[];
	
	if(xGetElementById("TIPO_DOCUMENTO_FOP").value=="GC")
		Form_ORDEN_PAGO__RestablecerDetalles();
	}

function Form_ORDEN_PAGO__BotonBeneficiario(bloquear){
	xGetElementById("TIPO_PERSONA_FOP").innerHTML="Beneficiario";
	//xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute('onclick',bloquear==true?"":"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FOP','ID_BENEFICIARIO_PROVEEDOR_FOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FOP','','CUENTA_CONTABLE_PB_FOP')");
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FOP").setAttribute('onclick',bloquear==true?"":"Form_ORDEN_PAGO__SeleccionarPersona()");
	xGetElementById("ID_BoP_FOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FOP").value="";
	Form_ORDEN_PAGO__SW_PERSONA="B";
	Form_ORDEN_PAGO__PERSONA_cuenta_destino=[];
	
	if(xGetElementById("TIPO_DOCUMENTO_FOP").value=="GC")
		Form_ORDEN_PAGO__RestablecerDetalles();
	}


function Form_ORDEN_PAGO__SeleccionarPersona(){
	siga.onPersona({
    tipo: Form_ORDEN_PAGO__SW_PERSONA === 'P' ? 'J' : 'N',
    //onList: 'onList',
    onAccept: function(result){
    	xGetElementById("ID_BoP_FOP").value=result[0]["id"];
			xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value=result[0]["identificacion"];
			xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value=result[0]["denominacion"];
			xGetElementById("CUENTA_CONTABLE_PB_FOP").value=result[0]['id_cuenta_contable'];

      Form_ORDEN_PAGO__PERSONA_cuenta_destino=[
        result[0]["cuenta_bancaria_principal"],
        result[0]["cuenta_bancaria_secundaria"]
      ].filter(Boolean);
    }
  });
}





/*Verifica que los campos obligatorios esten llenos y la existencia (duplicidad) antes de guardar*/
function Form_ORDEN_PAGO__Guardar(){
	/*Se verifica que los campos obligatorio esten llenos*/
	Form_ORDEN_PAGO__TabPane.setSelectedIndex(0);
	//var NumeroRef		= xTrim(strtoupper(xGetElementById("REFERENCIA_FOP").value));
	var _fecha			= xTrim(strtoupper(xGetElementById("FECHA_FOP").value));
	var _concepto	= xTrim(strtoupper(xGetElementById("DENOMINACION_FOP").value));
	var _id_persona			= xTrim(strtoupper(xGetElementById("ID_BoP_FOP").value));
	var _tipo			= xTrim(strtoupper(xGetElementById("TIPO_DOCUMENTO_FOP").value));



	if(!_fecha){
		Form_ORDEN_PAGO__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_ORDEN_PAGO__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	if(!_id_persona){
		Form_ORDEN_PAGO__Mensaje("Por favor seleccione el proveedor o beneficiario.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!_concepto){
		Form_ORDEN_PAGO__Mensaje("Por favor introduzca el concepto.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(xGetElementById("TOTAL_DEBE_FOP_DC").value!=xGetElementById("TOTAL_HABER_FOP_DC").value){
		Form_ORDEN_PAGO__Mensaje("El total por el debe no coincide con el total del haber.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
		}
	
	//verificar si se agrego la cuenta por pagar a proveedor o beneficiario
	var _encontro=false;
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
		if(xGetElementById("CUENTA_CONTABLE_PB_FOP").value==Form_ORDEN_PAGO__ArregloDetallesContables[i][0] && Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='H') {
			_encontro=true;
			break;
		}
	if(!_encontro && Form_ORDEN_PAGO__TamanoArregloDetallesContables>0){
		Form_ORDEN_PAGO__Mensaje("No encontro la cuenta contable (por el haber) asociada al proveedor/beneficiario seleccionado.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
	}
	
	//verificar si el total por cargo coincide con su partida presupuestaria
	var _suma_cargo=0;
	var _suma_partida=0;
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++){
		_suma_cargo+=numberFormat(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"],2)*1;
		//_suma_partida=0;
		for(var j=0;j<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;j++)
			if(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id_cuenta_presupuestaria"]==Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[j][2])
				_suma_partida+=numberFormat(Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[j][7],2)*1;
	}

	if(numberFormat(_suma_cargo,2) != numberFormat(_suma_partida,2)){
		Form_ORDEN_PAGO__Mensaje("El total de cargos no coincide con el total de su cuenta presupuestaria asociada.","ROJO");
		Form_ORDEN_PAGO__MensajeListado("");
		return;
	}

	Form_ORDEN_PAGO__DesactivarFormulario();

	var _detalle={};
	_detalle.presupuestario=[];
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++){
		_detalle.presupuestario[i]={
			id_accion_subespecifica: Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0],
			id_cuenta_presupuestaria: Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2],
			operacion: Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6],
			monto: Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]
		};
	}
	
	_detalle.contable=[];
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++){
		_detalle.contable[i]={
			id_cuenta_contable: Form_ORDEN_PAGO__ArregloDetallesContables[i][0],
			operacion: Form_ORDEN_PAGO__ArregloDetallesContables[i][3],
			monto: Form_ORDEN_PAGO__ArregloDetallesContables[i][4]			
		};
	}
	
	_detalle.extra={
		tipo: _tipo,
		forma_pago: siga.base64.encode(JSON.stringify(Form_ORDEN_PAGO__ArregloDetalleFormaPago)),
	};
	
	_detalle.comprobante_previo=[];
	for(var i=0;i<Form_ORDEN_PAGO__comprobante_previo.length;i++)
		_detalle.comprobante_previo[i]=Form_ORDEN_PAGO__comprobante_previo[i]["id"];
	
	_detalle.cargo=[];
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++){
		_detalle.cargo[i]={
			id_cargo: Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id"],
			monto: Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]
		};
	}
	
	_detalle.retencion=[];
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;i++){
		_detalle.retencion[i]={
			id_retencion: Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["id"],
			monto: Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]
		};
	}
	
	var _id_comprobante="";
	if(Form_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_PAGO__IDSeleccionActualLista;
	

	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_ORDEN_PAGO__ActivarFormulario();
			return;
			}
		}
	

	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo': 'OP',
										'fecha':_fecha,
										'concepto':_concepto,
										'contabilizado': 'f',
										'id_persona': _id_persona,										
										'detalle': Ext.encode(_detalle)
										},
						'onSuccess':Form_ORDEN_PAGO__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		
		
	}




/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_ORDEN_PAGO__GuardarMensaje(req){
	Form_ORDEN_PAGO__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_ORDEN_PAGO__Nuevo();
		Form_ORDEN_PAGO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_ORDEN_PAGO__Mensaje(respuesta.message,"ROJO");		
	}


/*Al presionar enter buscamos directamente en el listado*/
function Form_ORDEN_PAGO__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FOP").checked){
 		if(ev.keyCode==13)
			Form_ORDEN_PAGO__BuscarListado();
		return;
		}
	Form_ORDEN_PAGO__BuscarListado();
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_ORDEN_PAGO__BuscarListado(){
	Form_ORDEN_PAGO__IDSeleccionActualLista=-1;
	Form_ORDEN_PAGO__IDTipoOperacionSeleccionActualLista=-1;
	
	Form_ORDEN_PAGO__OcultarBotones();
	
	//xGetElementById("FORMULARIO_FOP").reset();
	Form_ORDEN_PAGO__ActivarFormulario();
	Form_ORDEN_PAGO__DesactivarBotonModificar();
	Form_ORDEN_PAGO__DesactivarBotonEliminar();
	Form_ORDEN_PAGO__ActivarBotonGuardar();


	Form_ORDEN_PAGO__ModificarTabla=false;
	Form_ORDEN_PAGO__MostrarTablaDP();
	Form_ORDEN_PAGO__MostrarTablaDC();




	var CadenaBuscar=xGetElementById("LISTADO_BUSCAR_FOP").value;
	if(CadenaBuscar!="")
		if(Form_ORDEN_PAGO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_ORDEN_PAGO__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(Form_ORDEN_PAGO__BuscarListado_CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FOP").innerHTML=IconoCargandoTabla;
	else{//busco el n del documento y lo coloco en el input text numero

		}

	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_FOP").value,
		'tipo':[Form_ORDEN_PAGO__tipo]
	};
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList_OP",
									'mostrar': Ext.encode(_mostrar),
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_ORDEN_PAGO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}
	

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_ORDEN_PAGO__MostrarListado(req){
	var respuesta = req.responseText;
	if(!respuesta)
		return;
	var resultado = eval("(" + respuesta + ")");
	
	resultado=resultado["result"];
	
	var n=resultado.length;

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FOP").value));
	var color_estado="";
	var estado="";
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
		
		FuncionOnclick="Form_ORDEN_PAGO__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
		FuncionOnDblclick="Form_ORDEN_PAGO__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FOP"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FOP"+resultado[i]['id']+"\")";
		
		Contenido+="<TR id='FOP"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";
		
		if(xGetElementById("SOMBRA_CHECKBOX_FOP").checked && TextoBuscar!=""){
			CadAux1=str_replace(resultado[i]['persona'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(resultado[i]['correlativo'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux1=resultado[i]['persona'];
			CadAux2=resultado[i]['correlativo'];
			CadAux3=resultado[i]['fecha'];
			CadAux4=resultado[i]['concepto'];
			}

		
		
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		Contenido+="<TD width='23%' class='FilaEstiloContinua'>"+CadAux1+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' style='padding-left: 5px;'>"+CadAux4+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FOP").innerHTML=Contenido;
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
function Form_ORDEN_PAGO__SeleccionarElementoTabla(IDSeleccion,estado){
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==IDSeleccion)
		return;
	
	if(Form_ORDEN_PAGO__IDSeleccionActualLista!=-1)
		xGetElementById("FOP"+Form_ORDEN_PAGO__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FOP"+IDSeleccion).bgColor=colorBase;
	
	
	
	Form_ORDEN_PAGO__IDSeleccionActualLista=IDSeleccion;
	
	xGetElementById("ID_BoP_FOP").value="";
	xGetElementById("TIPO_PERSONA_FOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FOP").value="";
	Form_ORDEN_PAGO__DesactivarFormulario();
	//Form_ORDEN_PAGO__ActivarBotonModificar();
	//Form_ORDEN_PAGO__ActivarBotonEliminar();
	Form_ORDEN_PAGO__DesactivarBotonModificar();
	Form_ORDEN_PAGO__DesactivarBotonGuardar();
	Form_ORDEN_PAGO__Mensaje("");
	Form_ORDEN_PAGO__MensajeListado("");
	Form_ORDEN_PAGO__SW_PERSONA="";
	Form_ORDEN_PAGO__OcultarBotones();
	Form_ORDEN_PAGO__PERSONA_cuenta_destino=[];
	Form_ORDEN_PAGO__ModificarTabla=false;
	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_ORDEN_PAGO__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								//console.log(resultado);
								
								//cargar informacion extra (tipo de orden de pago)
								DetalleFormaPago=[];
								if(resultado[0]["detalle_extra"]){
									for(var i=0;i<resultado[0]["detalle_extra"].length;i++){
										if(resultado[0]["detalle_extra"][i]["dato"]=="tipo")
											xGetElementById("TIPO_DOCUMENTO_FOP").value=resultado[0]["detalle_extra"][i]["valor"];
										if(resultado[0]["detalle_extra"][i]["dato"]=="forma_pago"){
											if(resultado[0]["detalle_extra"][i]["valor"]){
												try {
													DetalleFormaPago=JSON.parse(siga.base64.decode(resultado[0]["detalle_extra"][i]["valor"]));
												} catch (e) {
													DetalleFormaPago=[];
												}
											}
										}
									}
								}
								Form_ORDEN_PAGO__CambioTipoDocumento();
								
								xGetElementById("TIPO_PERSONA_FOP").value="";
								xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value="";
								xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value="";
								
								
							
								xGetElementById("FECHA_FOP").value=resultado[0]["fecha"];
								xGetElementById("COMPROBANTE_FOP").value=resultado[0]["correlativo"];
								xGetElementById("DENOMINACION_FOP").value=resultado[0]["concepto"];
								
								Form_ORDEN_PAGO__contabilizado=resultado[0]["contabilizado"];
								//cargar proveedor/beneficiario
								xGetElementById("ID_BoP_FOP").value="";
								Form_ORDEN_PAGO__BotonProveedor();
								if(resultado[0]["detalle_persona"]){
									switch(resultado[0]["detalle_persona"][0]["tipo"]){
										case "N":
											Form_ORDEN_PAGO__SW_PERSONA="B";
											Form_ORDEN_PAGO__BotonBeneficiario(true);
											break;
										case "J":
										default:
											Form_ORDEN_PAGO__SW_PERSONA="P";
											Form_ORDEN_PAGO__BotonProveedor(true);
											break;
										}
									xGetElementById("ID_BoP_FOP").value=resultado[0]["detalle_persona"][0]["id"];
									xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value=resultado[0]["detalle_persona"][0]["identificacion"];
									xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value=resultado[0]["detalle_persona"][0]["denominacion"];
									xGetElementById("CUENTA_CONTABLE_PB_FOP").value=resultado[0]["detalle_persona"][0]["id_cuenta_contable"];
									Form_ORDEN_PAGO__PERSONA_cuenta_destino=[
										resultado[0]["detalle_persona"][0]["cuenta_bancaria_principal"],
				            resultado[0]["detalle_persona"][0]["cuenta_bancaria_secundaria"]
				          ].filter(Boolean);
								}
									
								
								//cargar detalle presupuestarios								
								if(resultado[0]["detalle_presupuestario"]){
									var n=resultado[0]["detalle_presupuestario"].length;
									Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios=n
									for(var i=0;i<n;i++){
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i]=[];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][3]=resultado[0]["detalle_presupuestario"][i]['estructura_presupuestaria'];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][4]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][5]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6]=resultado[0]["detalle_presupuestario"][i]["operacion"];
										Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=resultado[0]["detalle_presupuestario"][i]["monto"];
										}
									Form_ORDEN_PAGO__MostrarTablaDP();
									}
								
								//cargar detalle contable
								if(resultado[0]["detalle_contable"]){
									var n=resultado[0]["detalle_contable"].length;
									Form_ORDEN_PAGO__TamanoArregloDetallesContables=n;
									for(var i=0;i<n;i++){
										Form_ORDEN_PAGO__ArregloDetallesContables[i]=[];
										Form_ORDEN_PAGO__ArregloDetallesContables[i][0]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
										Form_ORDEN_PAGO__ArregloDetallesContables[i][1]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
										Form_ORDEN_PAGO__ArregloDetallesContables[i][2]=resultado[0]["detalle_contable"][i]["denominacion"];
										Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=resultado[0]["detalle_contable"][i]["operacion"];
										Form_ORDEN_PAGO__ArregloDetallesContables[i][4]=resultado[0]["detalle_contable"][i]["monto"];
										}
									Form_ORDEN_PAGO__MostrarTablaDC();
									}
									
								//Form_ORDEN_PAGO__AgregarItemDCG(_id,_codigo,_denominacion,_formula,_id_cuenta_presupuestaria,_monto)	
								if(resultado[0]["detalle_cargo"]){
									var n=resultado[0]["detalle_cargo"].length;
									Form_ORDEN_PAGO__TamanoArregloDetallesCargos=n;
									for(var i=0;i<n;i++){
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]=[];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id"]=resultado[0]["detalle_cargo"][i]["id_cargo"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["codigo"]=resultado[0]["detalle_cargo"][i]["correlativo"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["denominacion"]=resultado[0]["detalle_cargo"][i]["cargo"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["formula"]=resultado[0]["detalle_cargo"][i]["formula"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id_cuenta_presupuestaria"]=resultado[0]["detalle_cargo"][i]["id_cuenta_presupuestaria"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]=resultado[0]["detalle_cargo"][i]["monto"];
										Form_ORDEN_PAGO__ArregloDetallesCargos[i]["iva"]=resultado[0]["detalle_cargo"][i]["iva"];
										}
									Form_ORDEN_PAGO__MostrarTablaDCG();
									}
									
								//cargar detalle retenciones
								if(resultado[0]["detalle_retencion"]){
									var n=resultado[0]["detalle_retencion"].length;
									Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones=n;
									for(var i=0;i<n;i++){
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]=[];
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["id"]=resultado[0]["detalle_retencion"][i]["id_retencion"];
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["codigo"]=resultado[0]["detalle_retencion"][i]["correlativo"];
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["denominacion"]=resultado[0]["detalle_retencion"][i]["retencion"];
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["formula"]=resultado[0]["detalle_retencion"][i]["formula"];
										Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]=resultado[0]["detalle_retencion"][i]["monto"];
										}
									Form_ORDEN_PAGO__MostrarTablaDR();
									}


								Form_ORDEN_PAGO__ArregloDetalleFormaPago=DetalleFormaPago;
								Form_ORDEN_PAGO__MostrarTablaFormaPago();
								
								
								
								//cargar comprobantes previos
								Form_ORDEN_PAGO__comprobante_previo=onGetComprobantes(resultado[0]["detalle_comprobante_previo"]);
								Form_ORDEN_PAGO__comprobante_posterior=resultado[0]["detalle_comprobante_posterior"];
								
								Form_ORDEN_PAGO__OcultarBotones();
								switch(estado){
									//SIN CONTABILIZAR (ROJO)
									case 0://activar el boton de modificar, mostrar el boton de contabilizar															
										Form_ORDEN_PAGO__ActivarBotonModificar();
										xGetElementById("BOTON_CONTABLIZAR_FOP").style.display="";
										break;
									//CONTABILIZADO (VERDE)
									case 2://mostrar el boton de reversar y anular
										//verificar si tiene comprobantes posteriores asociados, si es el caso, no permitir reversar ni anular
										if(resultado[0]["detalle_comprobante_posterior"].length>0){
											xGetElementById("BOTON_VER_POSTERIORES_FOP").style.display="";	
										}
										else{
											xGetElementById("BOTON_REVERSAR_FOP").style.display="";
											xGetElementById("BOTON_ANULAR_FOP").style.display="";
										}

										break;
									//ANULADO (NEGRO)
									case 3:
										xGetElementById("BOTON_VER_ANULACION_FOP").style.display="";
										break;
								}
								

							},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

function Form_ORDEN_PAGO__OcultarBotones(){	
	xGetElementById("BOTON_ANULAR_FOP").style.display="none";
	xGetElementById("BOTON_CONTABLIZAR_FOP").style.display="none";
	xGetElementById("BOTON_REVERSAR_FOP").style.display="none";
	xGetElementById("BOTON_VER_POSTERIORES_FOP").style.display="none";	
	xGetElementById("BOTON_VER_ANULACION_FOP").style.display="none";	
}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_ORDEN_PAGO__LimpiarInputTextBuscarListado(){
	Form_ORDEN_PAGO__ModificarTabla=false;
	Form_ORDEN_PAGO__IDComprobante="";
	Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesContables=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesCargos=0;
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DP").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DC").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DR").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DCG").innerHTML="";

	Form_ORDEN_PAGO__IDBancoSeleccionActualLista=-1;
	Form_ORDEN_PAGO__IDTipoOperacionSeleccionActualLista=-1;
	Form_ORDEN_PAGO__IDSeleccionActualLista=-1;
	Form_ORDEN_PAGO__Denominacion="";
	Form_ORDEN_PAGO__DesactivarBotonModificar();
	Form_ORDEN_PAGO__DesactivarBotonEliminar();
	Form_ORDEN_PAGO__ActivarBotonGuardar();
	Form_ORDEN_PAGO__ActivarFormulario();
	//xGetElementById("FORMULARIO_FOP").reset();
	xGetElementById("LISTADO_BUSCAR_FOP").value="";
	Form_ORDEN_PAGO__Mensaje("");
	Form_ORDEN_PAGO__MensajeListado("");
	Form_ORDEN_PAGO__BuscarListado();

	
	//DarFocoCampo("LISTADO_BUSCAR_FOP",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_ORDEN_PAGO__Modificar(){
	Form_ORDEN_PAGO__ModificarTabla=true;
	Form_ORDEN_PAGO__MostrarTablaDP();
	Form_ORDEN_PAGO__MostrarTablaDC();
	Form_ORDEN_PAGO__MostrarTablaDR();
	Form_ORDEN_PAGO__MostrarTablaDCG();
	Form_ORDEN_PAGO__MostrarTablaFormaPago();
	Form_ORDEN_PAGO__ActivarFormulario();
	Form_ORDEN_PAGO__ActivarBotonGuardar();
	Form_ORDEN_PAGO__DesactivarBotonModificar();
	Form_ORDEN_PAGO__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_ORDEN_PAGO__Eliminar(){
	var _mensaje="La opción se encuentra desactivada. Contacte al administrador del sistema.";
	Form_ORDEN_PAGO__Mensaje(_mensaje,"ROJO");
	Form_ORDEN_PAGO__MensajeListado(_mensaje,"ROJO");
	return;
	
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	var _id_comprobante="";
	if(Form_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_PAGO__IDSeleccionActualLista;
	
	if(_id_comprobante=="")
		return;
	
	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_comprobante},
				'onSuccess':Form_ORDEN_PAGO__EliminarMensaje,
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_ORDEN_PAGO__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_ORDEN_PAGO__LimpiarInputTextBuscarListado();
		Form_ORDEN_PAGO__Mensaje(respuesta.message,"VERDE");
		Form_ORDEN_PAGO__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_ORDEN_PAGO__Mensaje(respuesta.message,"ROJO");
		Form_ORDEN_PAGO__MensajeListado(respuesta.message,"ROJO");
		}
	}

var Form_MOV_DP__iSeleccionActual=-1;
var Form_MOV_DC__iSeleccionActual=-1;

function Form_ORDEN_PAGO__MostrarTablaDP(){
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
	sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;
	if(xGetElementById("TIPO_DOCUMENTO_FOP").value=="GC")
		sw=false;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FOP_DP","IMG_AGREGAR_FOP_DP",'agregar');
		ActivarBoton("BOTON_QUITAR_FOP_DP","IMG_QUITAR_FOP_DP",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FOP_DP","IMG_AGREGAR_FOP_DP",'agregar');
		DesactivarBoton("BOTON_QUITAR_FOP_DP","IMG_QUITAR_FOP_DP",'quitar');
		}

	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DP__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DP__ModificarValorCelda("+i+")";
			}

 		//FuncionOnMouseOver="pintarFila(\"FOP_DP"+i+"\");Form_MOV_DP__MostrarInfExtra("+i+");";
 		//FuncionOnMouseOut="despintarFila(\"FOP_DP"+i+"\");Form_MOV_DP__OcultarInfExtra();";
		
		//FuncionOnMouseOver="pintarFila(\"FOP_DP"+i+"\");";
 		//FuncionOnMouseOut="despintarFila(\"FOP_DP"+i+"\");";

		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]*1.0;

		Contenido+="<TR class='FilaListado' id='FOP_DP"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='15%'  style='font-size : 11px;'>"+Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][3]+"</TD>";
		Contenido+="<TD width='10%' align='center' style='font-size : 11px;'>"+Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][4]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][5]+"</TD>";
		Contenido+="<TD width='7%'  align='center' style='font-size : 11px;'>"+Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6]+"</TD>";
		Contenido+="<TD width='10%' align='right'  style='font-size : 11px;' id='TD_FOP_DP_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DP").innerHTML=Contenido;
	xGetElementById("TOTAL_COMPROMISOS_FOP_DP").value=FormatearNumero(TOTAL);


	}



function Form_MOV_DP__SeleccionarElementoTabla(i){
	if(Form_MOV_DP__iSeleccionActual!=-1)
		xGetElementById("FOP_DP"+Form_MOV_DP__iSeleccionActual).style.background="";
	xGetElementById("FOP_DP"+i).style.background=colorSeleccionTabla;
	Form_MOV_DP__iSeleccionActual=i;
	}

function Form_MOV_DP__ModificarValorCelda(i){
	if(xGetElementById("FOP_DP_txt_celda"))
		return;
	Valor=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FOP_DP_"+i).innerHTML="<INPUT id='FOP_DP_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DP__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FOP_DP_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DP__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FOP_DP_txt_celda").focus();
	}

function Form_MOV_DP__ModificarValorCeldaPierdeFoco(i){
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=numberFormat(xGetElementById("FOP_DP_txt_celda").value,2);
	xGetElementById("TD_FOP_DP_"+i).innerHTML=FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;k++)
		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[k][7]*1.0;
	xGetElementById("TOTAL_COMPROMISOS_FOP_DP").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DP__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios)
			return;		
		xGetElementById("FOP_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DP__ModificarValorCelda(i+1);
		Form_MOV_DP__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FOP_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DP__ModificarValorCelda(i-1);
		Form_MOV_DP__SeleccionarElementoTabla(i-1);
		}
	}








function Form_ORDEN_PAGO__MostrarTablaDC(){
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
	var FuncionOnDblclickOPERACION_H="";
	var FuncionOnDblclickOPERACION_D="";
	
	var sw;//sw indica si la tabla se puede editar
	sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;
	if(xGetElementById("TIPO_DOCUMENTO_FOP").value=="GC")
		sw=false;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FOP_DC","IMG_AGREGAR_FOP_DC",'agregar');
		ActivarBoton("BOTON_QUITAR_FOP_DC","IMG_QUITAR_FOP_DC",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FOP_DC","IMG_AGREGAR_FOP_DC",'agregar');
		DesactivarBoton("BOTON_QUITAR_FOP_DC","IMG_QUITAR_FOP_DC",'quitar');
		}

	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DC__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DC__ModificarValorCelda("+i+")";
			FuncionOnDblclickOPERACION_H="Form_ORDEN_PAGO__CambiarOperacionContable("+i+",'H')";
			FuncionOnDblclickOPERACION_D="Form_ORDEN_PAGO__CambiarOperacionContable("+i+",'D')";
			}


 		//FuncionOnMouseOver="pintarFila(\"FOP_DC"+i+"\")";
 		//FuncionOnMouseOut="despintarFila(\"FOP_DC"+i+"\")";


		Contenido+="<TR class='FilaListado' id='FOP_DC"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		Contenido+="<TD width='15%' style='font-size : 12px;'>"+Form_ORDEN_PAGO__ArregloDetallesContables[i][1]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_ORDEN_PAGO__ArregloDetallesContables[i][2]+"</TD>";

		if(Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='D' || Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='d'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FOP_DC_"+i+"'>"  +FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesContables[i][4])+"</TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\""+FuncionOnDblclickOPERACION_H+"\"></TD>";
			TOTAL_DEBE+=Form_ORDEN_PAGO__ArregloDetallesContables[i][4]*1.0;
			}
		else if(Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='H' || Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='h'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\""+FuncionOnDblclickOPERACION_D+"\"></TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FOP_DC_"+i+"'>" +FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesContables[i][4])+"</TD>";
			TOTAL_HABER+=Form_ORDEN_PAGO__ArregloDetallesContables[i][4]*1.0;
			}

		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DC").innerHTML=Contenido;
	xGetElementById("TOTAL_DEBE_FOP_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FOP_DC").value=FormatearNumero(TOTAL_HABER);

	}

function Form_ORDEN_PAGO__CambiarOperacionContable(i,Operacion){
	var sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;
	if(sw==false) return;
	Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=Operacion;
	Form_ORDEN_PAGO__MostrarTablaDC();
	}


function Form_MOV_DC__SeleccionarElementoTabla(i){
	if(Form_MOV_DC__iSeleccionActual!=-1)
		xGetElementById("FOP_DC"+Form_MOV_DC__iSeleccionActual).style.background="";
	xGetElementById("FOP_DC"+i).style.background=colorSeleccionTabla;
	Form_MOV_DC__iSeleccionActual=i;
	}

function Form_MOV_DC__ModificarValorCelda(i){
	if(xGetElementById("FOP_DC_txt_celda"))
		return;
	Valor=Form_ORDEN_PAGO__ArregloDetallesContables[i][4];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FOP_DC_"+i).innerHTML="<INPUT id='FOP_DC_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DC__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FOP_DC_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DC__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FOP_DC_txt_celda").focus();
	}

function Form_MOV_DC__ModificarValorCeldaPierdeFoco(i){
	Form_ORDEN_PAGO__ArregloDetallesContables[i][4]=numberFormat(xGetElementById("FOP_DC_txt_celda").value,2);
	xGetElementById("TD_FOP_DC_"+i).innerHTML=FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesContables[i][4]);
	//recalcular la suma
	var TOTAL_DEBE=0;
	var TOTAL_HABER=0;
	for(var k=0;k<Form_ORDEN_PAGO__TamanoArregloDetallesContables;k++){
		if(Form_ORDEN_PAGO__ArregloDetallesContables[k][3]=="D"||Form_ORDEN_PAGO__ArregloDetallesContables[k][3]=="d")
			TOTAL_DEBE+=Form_ORDEN_PAGO__ArregloDetallesContables[k][4]*1.0;
		else
			TOTAL_HABER+=Form_ORDEN_PAGO__ArregloDetallesContables[k][4]*1.0;
		}
	xGetElementById("TOTAL_DEBE_FOP_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FOP_DC").value=FormatearNumero(TOTAL_HABER);
	}

function Form_MOV_DC__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_ORDEN_PAGO__TamanoArregloDetallesContables)
			return;
		xGetElementById("FOP_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DC__SeleccionarElementoTabla(i+1)
		Form_MOV_DC__ModificarValorCelda(i+1);
		}
	else if(event.keyCode==38){//key up
		if((i-1)<0)
			return;
		xGetElementById("FOP_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DC__SeleccionarElementoTabla(i-1)
		Form_MOV_DC__ModificarValorCelda(i-1);
		}
	}






function Form_ORDEN_PAGO__AgregarDP() {
	var _operacion=[];
	switch(xGetElementById("TIPO_DOCUMENTO_FOP").value){
		case "CC": _operacion=["CC"]; break;
		//case "GC": _operacion=["GC"]; break; no se cumple
	}
	siga.open("detalle_presupuestario",{
		operacion: _operacion,
		onAdd: function(me){
			//verificar si existe, si existe sumarlo al anterior
			Form_ORDEN_PAGO__AgregarItemDP(me.internal.data.id_accion_subespecifica,
																		 me.internal.data.id_cuenta_presupuestaria,
																		 me.internal.data.estructura_presupuestaria,
																		 me.internal.data.cuenta_presupuestaria,
																		 me.internal.data.denominacion_presupuestaria,
																		 me.internal.data.operacion,
																		 me.internal.data.monto);
	
			
			Form_ORDEN_PAGO__MostrarTablaDP();
			
			//agregar detalle contable
			Form_ORDEN_PAGO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						'D',
																						me.internal.data.monto
																						);
		}
	});	
}

function Form_ORDEN_PAGO__AgregarItemDP(id_accion_subespecifica, id_cuenta_presupuestaria, estructura_presupuestaria, cuenta_presupuestaria, denominacion_presupuestaria, operacion, monto){
	for(i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++)
		if(Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0]==id_accion_subespecifica &&
			 Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]==id_cuenta_presupuestaria &&
			 Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6]==operacion){
			Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]*1+monto*1;
			return;
			}
	
	var i=Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i]=[];
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0]=id_accion_subespecifica;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]=id_cuenta_presupuestaria;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][3]=estructura_presupuestaria;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][4]=cuenta_presupuestaria;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][5]=denominacion_presupuestaria;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6]=operacion;
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=monto;
	Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios++;
}

function Form_ORDEN_PAGO__AgregarDC() {
	var _id_cuenta_contable=xGetElementById("CUENTA_CONTABLE_PB_FOP").value;
		
	siga.open("detalle_contable",{
		id_cuenta_contable: _id_cuenta_contable,
		operacion: 'H',
		monto: function(){
			var _monto=0;
			for(i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
				if(Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=="D") 
					_monto+=Form_ORDEN_PAGO__ArregloDetallesContables[i][4]*1;
			return _monto;
		},
		tooltip: 'Cuentas por pagar a proveedores/beneficiarios',
		onAdd: function(me){			
			Form_ORDEN_PAGO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						me.internal.data.operacion,
																						me.internal.data.monto
																						);
		}
	});
}


function Form_ORDEN_PAGO__AgregarItemDC(id_cuenta_contable, cuenta_contable, denominacion, operacion, monto){
	for(i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
		if(Form_ORDEN_PAGO__ArregloDetallesContables[i][0]==id_cuenta_contable &&
			 Form_ORDEN_PAGO__ArregloDetallesContables[i][3]==operacion){
			Form_ORDEN_PAGO__ArregloDetallesContables[i][4]=Form_ORDEN_PAGO__ArregloDetallesContables[i][4]*1+monto*1;
			Form_ORDEN_PAGO__MostrarTablaDC();
			return;
			}
	
	var i=Form_ORDEN_PAGO__TamanoArregloDetallesContables;
	Form_ORDEN_PAGO__ArregloDetallesContables[i]=[];
	Form_ORDEN_PAGO__ArregloDetallesContables[i][0]=id_cuenta_contable;
	Form_ORDEN_PAGO__ArregloDetallesContables[i][1]=cuenta_contable;
	Form_ORDEN_PAGO__ArregloDetallesContables[i][2]=denominacion;
	Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=operacion;
	Form_ORDEN_PAGO__ArregloDetallesContables[i][4]=monto;
	Form_ORDEN_PAGO__TamanoArregloDetallesContables++;
	Form_ORDEN_PAGO__MostrarTablaDC();
}







function Form_ORDEN_PAGO__DP_Quitar(){
	if(Form_MOV_DP__iSeleccionActual==-1)
		return;

	Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios--;
	for(i=Form_MOV_DP__iSeleccionActual*1;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++)
 		for(j=0;j<8;j++)
 			Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][j]= Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i+1][j];

	Form_ORDEN_PAGO__MostrarTablaDP();
	}

function Form_ORDEN_PAGO__DC_Quitar(){
	if(Form_MOV_DC__iSeleccionActual==-1)
		return;

	Form_ORDEN_PAGO__TamanoArregloDetallesContables--;
	for(i=Form_MOV_DC__iSeleccionActual*1;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
 		for(j=0;j<5;j++)
 			Form_ORDEN_PAGO__ArregloDetallesContables[i][j]= Form_ORDEN_PAGO__ArregloDetallesContables[i+1][j];

	Form_ORDEN_PAGO__MostrarTablaDC();
	}

function Form_ORDEN_PAGO__Imprimir(){
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		return;
	window.open("../../report/orden_pago.php?id="+Form_ORDEN_PAGO__IDSeleccionActualLista);
	}

function Form_ORDEN_PAGO__Reportes(){
	siga.open("reporte_orden_pago",{modal: true})
	}

function Form_ORDEN_PAGO__CambioTipoDocumento(){
	Form_ORDEN_PAGO__RestablecerDetalles();
	
	xGetElementById("DENOMINACION_FOP").value="";
	
	switch(xGetElementById("TIPO_DOCUMENTO_FOP").value){
		case "AC":
			Form_ORDEN_PAGO__TabPaneSUBTAB.setSelectedIndex(1);
			xGetElementById("SUB_TABPANE_FOP_DP").style.display="none";
			break;
		case "CC":
			Form_ORDEN_PAGO__TabPaneSUBTAB.setSelectedIndex(0);
			xGetElementById("SUB_TABPANE_FOP_DP").style.display="";
			xGetElementById("BOTONES_AGREGAR_QUITAR_FOP_DP").style.display="";
			xGetElementById("COMPROMISO_PREVIO_FOP_DC").style.display="none";
			break;
		case "GC":
			Form_ORDEN_PAGO__TabPaneSUBTAB.setSelectedIndex(0);
			xGetElementById("SUB_TABPANE_FOP_DP").style.display="";
			xGetElementById("BOTONES_AGREGAR_QUITAR_FOP_DP").style.display="none";
			xGetElementById("COMPROMISO_PREVIO_FOP_DC").style.display="";
			break;
	}
	
	
}

function Form_ORDEN_PAGO__RestablecerDetalles(){
	Form_ORDEN_PAGO__comprobante_previo=[];
	Form_ORDEN_PAGO__comprobante_posterior=[];
	
	Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesContables=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones=0;
	Form_ORDEN_PAGO__TamanoArregloDetallesCargos=0;
	
	Form_ORDEN_PAGO__ArregloDetallesPresupuestarios=[];
  Form_ORDEN_PAGO__ArregloDetallesContables=[];
	Form_ORDEN_PAGO__ArregloDetallesRetenciones=[];
	Form_ORDEN_PAGO__ArregloDetallesCargos=[];
	Form_ORDEN_PAGO__ArregloDetalleFormaPago=[];
  
	Form_ORDEN_PAGO__MostrarTablaDP();
	Form_ORDEN_PAGO__MostrarTablaDC();
	Form_ORDEN_PAGO__MostrarTablaDR();
	Form_ORDEN_PAGO__MostrarTablaDCG();
	Form_ORDEN_PAGO__MostrarTablaFormaPago();
}


/*
function Form_ORDEN_PAGO__onGetComprobantes(_ids){
	var _tmp=null;
	var _comprobante=[];
	for(var i=0;i<_ids.length;i++){
		_tmp=Ext.Ajax.request({
			async: false,
			url:"modulo_base/comprobante.php",
			params: {
				action: 'onGet',
				id: _ids[i]
			}
		});
		if(_tmp.statusText=="OK"){
			var _retorno=Ext.JSON.decode(_tmp.responseText);
			_comprobante[i]=_retorno[0];					
		}				
	}
	return _comprobante;
}*/

var Form_ORDEN_PAGO__comprobante_previo=[];
function Form_ORDEN_PAGO__Compromisos(){
	var _bloquear=!Form_ORDEN_PAGO__ModificarTabla;
	if(!(Form_ORDEN_PAGO__IDSeleccionActualLista>0) && _bloquear==true)
		_bloquear=false;
	
	
	var _id_persona=xTrim(strtoupper(xGetElementById("ID_BoP_FOP").value));
	var _tipo_persona=Form_ORDEN_PAGO__SW_PERSONA=="P"?"J":"N";
	//id de los comprobantes seleccionados (asosiados a la orden de pago)
	var _id_comprobante_previo_seleccion=[];
	for(var i=0;i<Form_ORDEN_PAGO__comprobante_previo.length;i++)
		_id_comprobante_previo_seleccion[i]=Form_ORDEN_PAGO__comprobante_previo[i]["id"];
	
	
	siga.open('comprobante_previo_listado',{
		bloquear: _bloquear,
		id_comprobante_previo: _id_comprobante_previo_seleccion,
		id_persona: _id_persona,
		tipo_persona: _tipo_persona,
		tipo:	["OC","OS"],
		onAccept: function(_id_comprobante_previo){
			Ext.MessageBox.wait("Por favor espere mientras se agregan los movimientos.",siga.window.getCmp("comprobante_previo_listado").title);
			
			Form_ORDEN_PAGO__RestablecerDetalles();
			
			xGetElementById("DENOMINACION_FOP").value="";
			
			if(_id_comprobante_previo.length==0){
				Ext.MessageBox.alert(siga.window.getCmp("comprobante_previo_listado").title,"Debe seleccionar por lo menos un elemento en el listado.");
				return false;
			}

  		var _comprobante=onGetComprobantes(_id_comprobante_previo);
			Form_ORDEN_PAGO__comprobante_previo=_comprobante;
			
			//comprobar que corresponden a un mismo proveedor o beneficiario
			var _id_persona=_comprobante[0]["id_persona"];
			for(var i=1;i<_id_comprobante_previo.length;i++){
				if(_id_persona!=_comprobante[i]["id_persona"]){
					//Ext.MessageBox.hide();
					Ext.MessageBox.alert(siga.window.getCmp("comprobante_previo_listado").title,"El proveedor y/o beneficiario debe ser el mismo en la selección.");
					return false;
				}
			}
			
			//agrupar datos de los comprobantes seleccionados
			//cargar detalles presupuestarios
			var _concepto="";
			for(var k=0;k<_id_comprobante_previo.length;k++){
				_concepto+=_comprobante[k]["concepto"];
				if(k<_id_comprobante_previo.length-1)
					_concepto+=" | ";
				
				//agregar detalles presupuestario
				for(var j=0;j<_comprobante[k]["detalle_presupuestario"].length;j++){
					var _encontro=false;
					//verificar si existe, si existe sumarlo al anterior
					for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++)
						if(Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0]==_comprobante[k]["detalle_presupuestario"][j]["id_accion_subespecifica"] &&
							 Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]==_comprobante[k]["detalle_presupuestario"][j]["id_cuenta_presupuestaria"]){
							Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]*1+_comprobante[k]["detalle_presupuestario"][j]["monto"]*1;
							_encontro=true;
							break;
							}
					
					if(!_encontro){
						var i=Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i]=[];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][0]=_comprobante[k]["detalle_presupuestario"][j]["id_accion_subespecifica"];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]=_comprobante[k]["detalle_presupuestario"][j]["id_cuenta_presupuestaria"];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][3]=_comprobante[k]["detalle_presupuestario"][j]["estructura_presupuestaria"];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][4]=_comprobante[k]["detalle_presupuestario"][j]["cuenta_presupuestaria"];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][5]=_comprobante[k]["detalle_presupuestario"][j]["denominacion"];
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][6]="GC";
						Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]=_comprobante[k]["detalle_presupuestario"][j]["monto"];
						Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios++;
					}
				}//fin for j detalle_presupuestario
				
				//agregar detalle de cargos
				//si es orden de compra o servico, calcular la base imponible para realizar el calculo de los cargos
				switch(_comprobante[k]["tipo"]) {
					case "OC":
					case "OS":
						var _tmp_sub_total=0;
						var _tmp_descuento=0;
						var _sub_total=0;
						var _total=0;
						var _total_iva=0;
						var _descuento_monto=0;
						var _descuento_porcentaje=0;
						var _bi=0;
						var _total_cargo=0;
						var MONTO=0;
						//porcentaje de descueto y monto descuento
						if(_comprobante[k]["detalle_extra"]){
							for(var j=0;j<_comprobante[k]["detalle_extra"].length;j++){
								if(_comprobante[k]["detalle_extra"][j]["dato"]=="descuento_porcentaje")
									_descuento_porcentaje=_comprobante[k]["detalle_extra"][j]["valor"];
								else if(_comprobante[k]["detalle_extra"][j]["dato"]=="descuento_monto")
									_descuento_monto=_comprobante[k]["detalle_extra"][j]["valor"];
								}
							}
						var _descuento="";
						var _descuento_articulo=false;
						for(var j=0;j<_comprobante[k]["detalle_item"].length;j++){
							_comprobante[k]["detalle_item"][j]["descuento_p"]=0;
							_comprobante[k]["detalle_item"][j]["descuento_m"]=0;
							//si el articulo tiene descuento
							if(_comprobante[k]["detalle_item"][j]["descuento"]){
								_descuento_articulo=true;
								_descuento=Ext.decode(_comprobante[k]["detalle_item"][j]["descuento"]);
								_comprobante[k]["detalle_item"][j]["descuento_p"]=_descuento.porcentaje;//descuento %
								_comprobante[k]["detalle_item"][j]["descuento_m"]=_descuento.monto;//descuento monto										
							}
							_tmp_sub_total=numberFormat(_comprobante[k]["detalle_item"][j]["cantidad"]*_comprobante[k]["detalle_item"][j]["costo"],2);
							_tmp_descuento=(numberFormat((_comprobante[k]["detalle_item"][j]["descuento_p"]*_tmp_sub_total)/100,2)*1.0+_comprobante[k]["detalle_item"][j]["descuento_m"]*1.00);
							_sub_total=_tmp_sub_total-_tmp_descuento;
			
							_total+=numberFormat(_sub_total,2)*1.0;
							if(_comprobante[k]["detalle_item"][j]["aplica_iva"]=='t')
								_total_iva+=numberFormat(_sub_total,2)*1.0;
						}//fin for j detalle_item
						
						_bi=_total-((_total*_descuento_porcentaje/100)+_descuento_monto);

						//cargar los cargos provenientes de las ordenes de compra y/o servicios
						for(var j=0;j<_comprobante[k]["detalle_cargo"].length;j++){
							MONTO=_comprobante[k]["detalle_cargo"][j]["iva"]=='t'?_total_iva:_bi;							
							_total_cargo=(eval(_comprobante[k]["detalle_cargo"][j]["formula"]))+_comprobante[k]["detalle_cargo"][j]["monto"]*1.00;
							//agregar el cargo a un arreglo como, como en dp, dc y dr
							
							Form_ORDEN_PAGO__AgregarItemDCG(_comprobante[k]["detalle_cargo"][j]["id_cargo"],
																							_comprobante[k]["detalle_cargo"][j]["correlativo"],
																							_comprobante[k]["detalle_cargo"][j]["cargo"],
																							_comprobante[k]["detalle_cargo"][j]["formula"],
																							_comprobante[k]["detalle_cargo"][j]["id_cuenta_presupuestaria"],
																							_total_cargo,
																							_comprobante[k]["detalle_item"][j]["aplica_iva"]);
							
							
						}//firn for j detalle_cargo
						

						
						break;
				}//fin switch

			}//fin for k
			
			//cargar detalles contables, apartir de los DP
			var _total_debe=0;
			for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesPresupuestarios;i++){
				_tmp=Ext.Ajax.request({
					async: false,
					url:"module/convertidor/",
					params: {
						action: 'onGet',
						id_cuenta_presupuestaria: Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][2]
					}
				});
				if(_tmp.statusText=="OK"){
					var _retorno=Ext.JSON.decode(_tmp.responseText);
					Form_ORDEN_PAGO__AgregarItemDC(_retorno[0]["id_cuenta_contable"], _retorno[0]["cuenta_contable"], _retorno[0]["denominacion_contable"], "D", Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]);
					_total_debe+=Form_ORDEN_PAGO__ArregloDetallesPresupuestarios[i][7]*1;
				}
			}
			
			//Agregar cuenta contable por el haber asociada al proveedor o beneficiario
			_tmp=Ext.Ajax.request({
				async: false,
				url:"module/cuenta_contable/",
				params: {
					action: 'onGet',
					id_cuenta_contable: _comprobante[0]["detalle_persona"][0]["id_cuenta_contable"]
				}
			});
			if(_tmp.statusText=="OK"){
				var _retorno=Ext.JSON.decode(_tmp.responseText);
				Form_ORDEN_PAGO__AgregarItemDC(_retorno[0]["id_cuenta_contable"], _retorno[0]["cuenta_contable"], _retorno[0]["denominacion"], "H", _total_debe);
			}
			
			
			
			
			
			
			xGetElementById("ID_BoP_FOP").value=_comprobante[0]["detalle_persona"][0]["id"];
			xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FOP").value=_comprobante[0]["detalle_persona"][0]["identificacion"];
			xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FOP").value=_comprobante[0]["detalle_persona"][0]["denominacion"];
			xGetElementById("CUENTA_CONTABLE_PB_FOP").value=_comprobante[0]["detalle_persona"][0]["id_cuenta_contable"];
			xGetElementById("DENOMINACION_FOP").value=_concepto;
			
			Form_ORDEN_PAGO__MostrarTablaDP();
			Form_ORDEN_PAGO__MostrarTablaDC();
			Form_ORDEN_PAGO__MostrarTablaDCG();
			Ext.MessageBox.hide();
			return true;
		}
	});
	
}


function Form_ORDEN_PAGO__AgregarDR(){
	var campo={
		setValue: function(v){},
		fieldLabel: 'Lista de Retenciones',		
		internal:{
			page:1,
			limit: 100,
			valueField: 'id',
			//columns: {field: ["identificacion_tipo","identificacion_numero","denominacion","identificacion"], title: ["Nac.","Cédula","Denominación"], width: ['5%','20%','75%'], sort: ["DESC",'ASC']},
			columns: {field: ["correlativo","denominacion","formula"], title: ["Código","Denominación","Formula"], width: ['8%','52%','40%'], sort: ['NULL',"ASC",'NULL']},
			url: 'module/retencion/',
			actionOnList:'onList',
			actionOnGet:'onGet',
			onAccept: function(){},
			onBeforeAccept: function(dataview, record, item, index, e){
				var _id_retencion=record.get("id");
				var _codigo=record.get("correlativo");
				var _denominacion=record.get("denominacion");
				var _formula=record.get("formula");
				return Form_ORDEN_PAGO__AgregarItemDR(_id_retencion,_codigo,_denominacion,_formula);
				return true;
			}
		}
	};
	//var selector=new Ext.form.eWindowSelect({parent: campo});
	var _opt={};
  _opt.internal={};
  _opt.internal.parent=campo;
  var selector=Ext.create("siga.windowSelect",_opt);
	selector.show();
	selector.search();
}


function Form_ORDEN_PAGO__AgregarItemDR(_id_retencion, _codigo, _denominacion, _formula){
	var MONTO=xGetElementById("TOTAL_BC_FOP_DR").value*1;
	var MONTO_RETENCION=eval(_formula);
	
	for(i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;i++)
		if(Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["id"]==_id_retencion){
			Ext.MessageBox.alert("Lista de Retenciones","La retención ya se encuentra agregada en el listado.");
			return false;
			}
	
	var i=Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]=[];
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["id"]=_id_retencion;
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["codigo"]=_codigo;
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["denominacion"]=_denominacion;
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["formula"]=_formula;
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]=MONTO_RETENCION;
	Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones++;
	Form_ORDEN_PAGO__MostrarTablaDR();
	return true;
}

function Form_ORDEN_PAGO__QuitarDR(){
	if(Form_MOV_DR__iSeleccionActual==-1)
		return;

	Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones--;
	for(i=Form_MOV_DR__iSeleccionActual*1;i<Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;i++)
 		Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]= Form_ORDEN_PAGO__ArregloDetallesRetenciones[i+1];

	Form_ORDEN_PAGO__MostrarTablaDR();
	}

var Form_MOV_DR__iSeleccionActual=-1;
function Form_ORDEN_PAGO__MostrarTablaDR() {
	Form_MOV_DR__iSeleccionActual=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnDblclickMONTO="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL=0;

	var sw;
	sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;
	

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FOP_DR","IMG_AGREGAR_FOP_DR",'agregar');
		ActivarBoton("BOTON_QUITAR_FOP_DR","IMG_QUITAR_FOP_DR",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FOP_DR","IMG_AGREGAR_FOP_DR",'agregar');
		DesactivarBoton("BOTON_QUITAR_FOP_DR","IMG_QUITAR_FOP_DR",'quitar');
		}

	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DR__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DR__ModificarValorCelda("+i+")";
			}


		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]*1.0;

		Contenido+="<TR class='FilaListado' id='FOP_DR"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='10%' align='center'>"+Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["codigo"]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["denominacion"]+"</TD>";
		Contenido+="<TD class='CeldaContinua' width='30%' >"+Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["formula"]+"</TD>";
		Contenido+="<TD width='15%' align='right' id='TD_FOP_DR_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DR").innerHTML=Contenido;
	xGetElementById("TOTAL_FOP_DR").value=FormatearNumero(TOTAL);


	}



function Form_MOV_DR__SeleccionarElementoTabla(i){
	if(Form_MOV_DR__iSeleccionActual!=-1)
		xGetElementById("FOP_DR"+Form_MOV_DR__iSeleccionActual).style.background="";
	xGetElementById("FOP_DR"+i).style.background=colorSeleccionTabla;
	Form_MOV_DR__iSeleccionActual=i;
	}

function Form_MOV_DR__ModificarValorCelda(i){
	if(xGetElementById("FOP_DR_txt_celda"))
		return;
	Valor=Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FOP_DR_"+i).innerHTML="<INPUT id='FOP_DR_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DR__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FOP_DR_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DR__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FOP_DR_txt_celda").focus();
	}

function Form_MOV_DR__ModificarValorCeldaPierdeFoco(i){
	Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]=numberFormat(xGetElementById("FOP_DR_txt_celda").value,2);
	xGetElementById("TD_FOP_DR_"+i).innerHTML=FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]["monto"]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones;k++)
		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesRetenciones[k]["monto"]*1.0;
	xGetElementById("TOTAL_FOP_DR").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DR__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones)
			return;		
		xGetElementById("FOP_DR_txt_celda").onblur="";
		Form_MOV_DR__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DR__ModificarValorCelda(i+1);
		Form_MOV_DR__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FOP_DR_txt_celda").onblur="";
		Form_MOV_DR__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DR__ModificarValorCelda(i-1);
		Form_MOV_DR__SeleccionarElementoTabla(i-1);
		}
	}
	
	
var Form_MOV_DCG__iSeleccionActual=-1;
function Form_ORDEN_PAGO__MostrarTablaDCG(){
	Form_MOV_DCG__iSeleccionActual=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnDblclickMONTO="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL=0;

	var sw;
	sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;
	if(xGetElementById("TIPO_DOCUMENTO_FOP").value=="GC")
		sw=false;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FOP_DCG","IMG_AGREGAR_FOP_DCG",'agregar');
		ActivarBoton("BOTON_QUITAR_FOP_DCG","IMG_QUITAR_FOP_DCG",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FOP_DCG","IMG_AGREGAR_FOP_DCG",'agregar');
		DesactivarBoton("BOTON_QUITAR_FOP_DCG","IMG_QUITAR_FOP_DCG",'quitar');
		}

	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++){
		FuncionOnclick="Form_MOV_DCG__SeleccionarElementoTabla('"+i+"')";
		FuncionOnDblclickMONTO="Form_MOV_DCG__ModificarValorCelda("+i+")";

		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]*1.0;

		Contenido+="<TR class='FilaListado' id='FOP_DCG"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='10%' align='center'>"+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["codigo"]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["denominacion"]+"</TD>";
		Contenido+="<TD class='CeldaContinua' width='30%' >"+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["formula"]+"</TD>";
		Contenido+="<TD width='15%' align='right' id='TD_FOP_DCG_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FOP_DCG").innerHTML=Contenido;
	xGetElementById("TOTAL_FOP_DCG").value=FormatearNumero(TOTAL);


	}
  

function Form_MOV_DCG__SeleccionarElementoTabla(i){
	if(Form_MOV_DCG__iSeleccionActual!=-1)
		xGetElementById("FOP_DCG"+Form_MOV_DCG__iSeleccionActual).style.background="";
	xGetElementById("FOP_DCG"+i).style.background=colorSeleccionTabla;
	Form_MOV_DCG__iSeleccionActual=i;
	}

function Form_MOV_DCG__ModificarValorCelda(i){
	if(xGetElementById("FOP_DCG_txt_celda"))
		return;
	Valor=numberFormat(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"],2);
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FOP_DCG_"+i).innerHTML="<INPUT id='FOP_DCG_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DCG__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FOP_DCG_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DCG__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FOP_DCG_txt_celda").focus();
	}

function Form_MOV_DCG__ModificarValorCeldaPierdeFoco(i){
	Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]=numberFormat(xGetElementById("FOP_DCG_txt_celda").value,2);
	xGetElementById("TD_FOP_DCG_"+i).innerHTML=FormatearNumero(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;k++)
		TOTAL+=Form_ORDEN_PAGO__ArregloDetallesCargos[k]["monto"]*1.0;
	xGetElementById("TOTAL_FOP_DCG").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DCG__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_ORDEN_PAGO__TamanoArregloDetallesCargos)
			return;		
		xGetElementById("FOP_DCG_txt_celda").onblur="";
		Form_MOV_DCG__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DCG__ModificarValorCelda(i+1);
		Form_MOV_DCG__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FOP_DCG_txt_celda").onblur="";
		Form_MOV_DCG__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DCG__ModificarValorCelda(i-1);
		Form_MOV_DCG__SeleccionarElementoTabla(i-1);
		}
	}
	
function Form_ORDEN_PAGO__AgregarItemDCG(_id,_codigo,_denominacion,_formula,_id_cuenta_presupuestaria,_monto,_iva){
	var _encontro=false;
	//verificar si existe, si existe sumarlo al anterior
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++)
		if(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id"]==_id){
			Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]+=_monto*1;
			_encontro=true;
			break;
			}
	
	if(!_encontro){
		var i=Form_ORDEN_PAGO__TamanoArregloDetallesCargos;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]=[];
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id"]=_id;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["codigo"]=_codigo;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["denominacion"]=_denominacion;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["formula"]=_formula;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id_cuenta_presupuestaria"]=_id_cuenta_presupuestaria;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]=_monto;
		Form_ORDEN_PAGO__ArregloDetallesCargos[i]["iva"]=_iva;
		Form_ORDEN_PAGO__TamanoArregloDetallesCargos++;
	}
}

function Form_ORDEN_PAGO__AgregarDCG(){
	var campo={
		setValue: function(v){},
		fieldLabel: 'Lista de Cargos',		
		internal:{
			page:1,
			limit: 100,
			valueField: 'id',
			//columns: {field: ["identificacion_tipo","identificacion_numero","denominacion","identificacion"], title: ["Nac.","Cédula","Denominación"], width: ['5%','20%','75%'], sort: ["DESC",'ASC']},
			columns: {field: ["correlativo","denominacion","formula","id_cuenta_presupuestaria"], title: ["Código","Denominación","Formula"], width: ['8%','52%','40%'], sort: ['NULL',"ASC",'NULL']},
			url: 'module/cargo/',
			actionOnList:'onList',
			actionOnGet:'onGet',
			onAccept: function(){},
			onBeforeAccept: function(dataview, record, item, index, e){
				var _id=record.get("id");
				var _codigo=record.get("correlativo");
				var _denominacion=record.get("denominacion");
				var _formula=record.get("formula");
				var _id_cuenta_presupuestaria=record.get("id_cuenta_presupuestaria");
				var _iva=record.get("iva");
				
				var MONTO=xGetElementById("TOTAL_BC_FOP_DCG").value*1;
				var _monto=eval(_formula);
				
				for(i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++)
					if(Form_ORDEN_PAGO__ArregloDetallesCargos[i]["id"]==_id){
						Ext.MessageBox.alert("Lista de Cargos","El cargo ya se encuentra agregado al listado.");
						return false;
						}
				
				Form_ORDEN_PAGO__AgregarItemDCG(_id,_codigo,_denominacion,_formula,_id_cuenta_presupuestaria,_monto,_iva);
				Form_ORDEN_PAGO__MostrarTablaDCG();
				
				//agregar la parte presupuestaria
				var _id_accion_subespecifica=xGetElementById("OAE_FOP").value;				
				var _estructura_presupuestaria="";//buscar por _id_accion_subespecifica
				_tmp=Ext.Ajax.request({
					async: false,
					url:"module/estructura_presupuestaria/",
					params: {
						action: 'onGet_Codigo',
						id_accion_subespecifica: _id_accion_subespecifica
					}
				});
				if(_tmp.statusText=="OK"){
					var _retorno=Ext.JSON.decode(_tmp.responseText);
					_estructura_presupuestaria=_retorno[0]["estructura_presupuestaria"];
				}
				
				var _cuenta_presupuestaria="";
				var _denominacion_presupuestaria="";
				var _id_cuenta_contable="";
				var _cuenta_contable="";
				var _denominacion_contable="";
				_tmp=Ext.Ajax.request({
					async: false,
					url:"module/convertidor/",
					params: {
						action: 'onGet',
						id_cuenta_presupuestaria: _id_cuenta_presupuestaria
					}
				});
				if(_tmp.statusText=="OK"){
					var _retorno=Ext.JSON.decode(_tmp.responseText);
					_cuenta_presupuestaria=_retorno[0]["cuenta_presupuestaria"];
					_denominacion_presupuestaria=_retorno[0]["denominacion_presupuestaria"];
					_id_cuenta_contable=_retorno[0]["id_cuenta_contable"];
					_cuenta_contable=_retorno[0]["cuenta_contable"];
					_denominacion_contable=_retorno[0]["denominacion_contable"];
				}
				
				var _operacion="CC";
				
				
				Form_ORDEN_PAGO__AgregarItemDP(	_id_accion_subespecifica,
																				_id_cuenta_presupuestaria,
																				_estructura_presupuestaria,
																				_cuenta_presupuestaria,
																				_denominacion_presupuestaria,
																				_operacion,
																				_monto);
				
				Form_ORDEN_PAGO__MostrarTablaDP();
				
				//agregar parte contable
				Form_ORDEN_PAGO__AgregarItemDC(	_id_cuenta_contable,
																				_cuenta_contable,
																				_denominacion_contable,
																				'D',
																				_monto
																				);
				
				Form_ORDEN_PAGO__MostrarTablaDC();
				
				return true;
			}
		}
	};
	//var selector=new Ext.form.eWindowSelect({parent: campo});
	var _opt={};
  _opt.internal={};
  _opt.internal.parent=campo;
  var selector=Ext.create("siga.windowSelect",_opt);
	selector.show();
	selector.search();
}

function Form_ORDEN_PAGO__QuitarDCG(){
	if(Form_MOV_DCG__iSeleccionActual==-1)
		return;

	Form_ORDEN_PAGO__TamanoArregloDetallesCargos--;
	for(i=Form_MOV_DCG__iSeleccionActual*1;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++)
 		Form_ORDEN_PAGO__ArregloDetallesCargos[i]= Form_ORDEN_PAGO__ArregloDetallesCargos[i+1];

	Form_ORDEN_PAGO__MostrarTablaDCG();
}







function Form_ORDEN_PAGO__CargarAC(){
	AjaxRequest.post({'parameters':{'action':"onList_AccionCentralizada_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AC_FOP");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_centralizada"]});
								opcion.innerHTML=resultado[i]["tipo"]+resultado[i]["codigo_centralizada"];
								mD.agregaHijo(SelectU, opcion);
								}
							Form_ORDEN_PAGO__CargarAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_ORDEN_PAGO__CargarAE(){
	if(!xGetElementById("AC_FOP").value)
		return;
	xGetElementById("AC_FOP").title=xGetElementById("AC_FOP").options[xGetElementById("AC_FOP").selectedIndex].title;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("AC_FOP").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AE_FOP");
							SelectU.innerHTML="";
							var opcion;
							var textoAC;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_especifica"]});
								textoAC=xGetElementById("AC_FOP").options[xGetElementById("AC_FOP").selectedIndex].innerHTML;
								//opcion.innerHTML=resultado[i]["codigo_especifico"];
								//opcion.innerHTML=FormatearCodigoProgramaticoAE(textoAC,resultado[i]["codigo_especifica"]);
								opcion.innerHTML=resultado[i]["codigo_especifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							Form_ORDEN_PAGO__CargarOAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_ORDEN_PAGO__CargarOAE(){
	if(!xGetElementById("AC_FOP").value||!xGetElementById("AE_FOP").value)
		return;
	xGetElementById("AE_FOP").title=xGetElementById("AE_FOP").options[xGetElementById("AE_FOP").selectedIndex].title;
	AjaxRequest.post({'parameters':{'action':"onList_AccionSubEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]',
									'id_accion_especifica':xGetElementById("AE_FOP").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("OAE_FOP");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_subespecifica"]});
								opcion.innerHTML=resultado[i]["codigo_subespecifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}
	
	
function Form_ORDEN_PAGO__Contabilizar(){
	Form_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_PAGO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_ORDEN_PAGO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 't'
								},
				'onSuccess': function(req){
								Form_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_ORDEN_PAGO__Reversar(){
	Form_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_PAGO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_ORDEN_PAGO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 'f'
								},
				'onSuccess': function(req){
								Form_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_ORDEN_PAGO__Anular(){
	Form_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_ORDEN_PAGO__IDSeleccionActualLista;
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
	
	//dar la posibilidad de anular o no los compromisos previos
	
	Form_ORDEN_PAGO__Mensaje("Anulando. Por favor espere...");
	Form_ORDEN_PAGO__OcultarBotones();

	var _detalle={};
	_detalle.comprobante_previo='anular';
	
	AjaxRequest.post({
				'parameters':{
								'action':"onAnular",
								'id': _id_comprobante,
								'fecha': _fecha,
								'detalle': Ext.encode(_detalle)
								},
				'onSuccess': function(req){
								Form_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
}

function Form_ORDEN_PAGO__VerCheques(){
	//Form_ORDEN_PAGO__comprobante_posterior
	var id_cheque="";
	for(var i=0;i<Form_ORDEN_PAGO__comprobante_posterior.length;i++){
		id_cheque+=Form_ORDEN_PAGO__comprobante_posterior[i][0];
		if(i<Form_ORDEN_PAGO__comprobante_posterior.length-1)
			id_cheque+=",";		
	}
	window.open("../../report/cheque.php?id="+id_cheque);
}

function Form_ORDEN_PAGO__VerAnulacion(){
	//Form_ORDEN_PAGO__comprobante_posterior
	var id_comprobante="";
	for(var i=0;i<Form_ORDEN_PAGO__comprobante_posterior.length;i++){
		id_comprobante+=Form_ORDEN_PAGO__comprobante_posterior[i][0];
		if(i<Form_ORDEN_PAGO__comprobante_posterior.length-1)
			id_comprobante+=",";		
	}
	window.open("../../report/comprobante.php?id="+id_comprobante);
}

function Form_ORDEN_PAGO__MontoBaseIVA(){
	var _monto_base=0;	
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++)
		_monto_base+=Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]*1;
	xGetElementById("TOTAL_BC_FOP_DR").value=numberFormat(_monto_base,2);	
}

function Form_ORDEN_PAGO__MontoBaseISLR(){
	var _monto_base_iva=0;	
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++)
		_monto_base_iva+=Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]*1;
		
	//buscar cuentas por pagar proveedores
	var _monto_cxp=0;
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
		if(xGetElementById("CUENTA_CONTABLE_PB_FOP").value==Form_ORDEN_PAGO__ArregloDetallesContables[i][0] && Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='H') {
			_monto_cxp=numberFormat(Form_ORDEN_PAGO__ArregloDetallesContables[i][4],2)*1;
			break;
		}	
	xGetElementById("TOTAL_BC_FOP_DR").value=numberFormat(_monto_cxp-_monto_base_iva,2);	
}

function Form_ORDEN_PAGO__MontoBase1x1000(){
	/*
	var _monto_base=0;	
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesCargos;i++){
		var MONTO=1;
		var RESULTADO=eval("("+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]+"/("+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["formula"]+"))-"+Form_ORDEN_PAGO__ArregloDetallesCargos[i]["monto"]);
		_monto_base+=RESULTADO*1;
	}*/
	Form_ORDEN_PAGO__MontoBaseISLR();
}

function Form_ORDEN_PAGO__MontoBaseNegroPrimero(){
	//buscar cuentas por pagar proveedores
	var _monto_cxp=0;
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
		if(xGetElementById("CUENTA_CONTABLE_PB_FOP").value==Form_ORDEN_PAGO__ArregloDetallesContables[i][0] && Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='H') {
			_monto_cxp=numberFormat(Form_ORDEN_PAGO__ArregloDetallesContables[i][4],2)*1;
			break;
		}
	xGetElementById("TOTAL_BC_FOP_DR").value=numberFormat(_monto_cxp,2);
}

//Agregar Detalle Forma de Pago
function Form_ORDEN_PAGO__AgregarDFP(){
	var total_cxp = 0;
	var total_retenciones=0;
	for(var i=0;i<Form_ORDEN_PAGO__TamanoArregloDetallesContables;i++)
		if(xGetElementById("CUENTA_CONTABLE_PB_FOP").value==Form_ORDEN_PAGO__ArregloDetallesContables[i][0] && Form_ORDEN_PAGO__ArregloDetallesContables[i][3]=='H') {
			total_cxp=numberFormat(Form_ORDEN_PAGO__ArregloDetallesContables[i][4],2)*1;
			break;
		}
	var total_retenciones = 0;
	for (var i = 0; i < Form_ORDEN_PAGO__TamanoArregloDetallesRetenciones; i++) {
		total_retenciones+=Form_ORDEN_PAGO__ArregloDetallesRetenciones[i]['monto']*1.00;
	}
	var total = (total_cxp-total_retenciones).toFixed(2);

	siga.open("orden_pago/forma_pago", {
		cuenta_destino: Form_ORDEN_PAGO__PERSONA_cuenta_destino,
		monto: total,
		onAccept: function(data){
			console.log("Forma Pago", data)
			Form_ORDEN_PAGO__AgregarFormaPago(data);
		}
	});
}

var Form_ORDEN_PAGO__ArregloDetalleFormaPago=[];
function Form_ORDEN_PAGO__AgregarFormaPago(data){
	Form_ORDEN_PAGO__ArregloDetalleFormaPago.push(data);
	Form_ORDEN_PAGO__MostrarTablaFormaPago();
}

var Form_MOV_FP__iSeleccionActual=-1;
function Form_ORDEN_PAGO__MostrarTablaFormaPago() {
	Form_MOV_DFP__iSeleccionActual=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnDblclickMONTO="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL=0;

	var sw;
	sw=Form_ORDEN_PAGO__ModificarTabla;
	if(Form_ORDEN_PAGO__IDSeleccionActualLista==-1)
		sw=true;


	if(sw){
		ActivarBoton("BOTON_AGREGAR_FOP_DFP","IMG_AGREGAR_FOP_DFP",'agregar');
		ActivarBoton("BOTON_QUITAR_FOP_DFP","IMG_QUITAR_FOP_DFP",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FOP_DFP","IMG_AGREGAR_FOP_DFP",'agregar');
		DesactivarBoton("BOTON_QUITAR_FOP_DFP","IMG_QUITAR_FOP_DFP",'quitar');
		}

	for(var i=0;i<Form_ORDEN_PAGO__ArregloDetalleFormaPago.length;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DFP__SeleccionarElementoTabla('"+i+"')";
 			FuncionOnDblclick="Form_MOV_DFP__EditarElementoTabla('"+i+"')";
		}


		TOTAL+=Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["monto"]*1.0;

		Contenido+="<TR class='FilaListado' id='FOP_DFP"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick=\""+FuncionOnDblclick+"\" onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		//Contenido+="<TD width='10%' align='center'>"+FormatearFecha(Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["fecha"])+"</TD>";
		Contenido+="<TD width='30%' class=''>"+Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["cuenta_origen"]['numero']+"<br/>"+Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["cuenta_origen"]['banco']+"</TD>";
		Contenido+="<TD width='30%' class=''>"+Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["cuenta_destino"]['numero']+"<br/>"+Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["cuenta_destino"]['banco']+"</TD>";
		Contenido+="<TD class='CeldaContinua' align='center'>"+Form_ORDEN_PAGO__DenominacionFormaPago(Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["forma_pago"])+"</TD>";
		Contenido+="<TD width='15%' align='right' id='TD_FOP_DR_"+i+"'>"+FormatearNumero(Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]["monto"])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_FOP_DFP").innerHTML=Contenido;
	xGetElementById("TOTAL_FOP_DFP").value=FormatearNumero(TOTAL);
	}

function Form_ORDEN_PAGO__DenominacionFormaPago(forma_pago){
	switch(forma_pago){
		case 'cheque': return "CHEQUE";
		case 'transferencia': return "TRANSFERENCIA";
		case 'deposito': return "DEPOSITO";
		case 'pago_movil': return "PAGO MOVIL";
		case 'efectivo': return "EFECTIVO";
	}
	return 'N/A';
}

function Form_MOV_DFP__SeleccionarElementoTabla(i){
	if(Form_MOV_DFP__iSeleccionActual!=-1)
		xGetElementById("FOP_DFP"+Form_MOV_DFP__iSeleccionActual).style.background="";
	xGetElementById("FOP_DFP"+i).style.background=colorSeleccionTabla;
	Form_MOV_DFP__iSeleccionActual=i;
}

function Form_MOV_DFP__EditarElementoTabla(i){
	var data = Form_ORDEN_PAGO__ArregloDetalleFormaPago[i];

	siga.open("orden_pago/forma_pago", {
		cuenta_origen_id: data['cuenta_origen']['id'],
		cuenta_destino_numero: data['cuenta_destino']['numero'],
		forma_pago: data['forma_pago'],
		monto: data['monto'],
		onAccept: function(data){
			console.log("Forma Pago", data)
			Form_ORDEN_PAGO__ArregloDetalleFormaPago[i]=data;
			Form_ORDEN_PAGO__MostrarTablaFormaPago();
		}
	});


}

function Form_ORDEN_PAGO__QuitarDFP(){
	if(Form_MOV_DFP__iSeleccionActual==-1)
		return;

	Form_ORDEN_PAGO__ArregloDetalleFormaPago.splice(Form_MOV_DFP__iSeleccionActual, 1);
	Form_ORDEN_PAGO__MostrarTablaFormaPago();
}
