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

var Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios=Array();
var Form_CHEQUE_DIRECTO__ArregloDetallesContables=Array();
var Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios=0;
var Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables=0;
var Form_CHEQUE_DIRECTO__ModificarTabla=false;

var Form_CHEQUE_DIRECTO__ArregloTitulosPresupuestarios=Array();
var Form_CHEQUE_DIRECTO__ArregloTitulosContables=Array();

var Form_CHEQUE_DIRECTO__contabilizado="";


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_CHEQUE_DIRECTO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCD").innerHTML=MSG;
	}



/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_CHEQUE_DIRECTO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCD_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_CHEQUE_DIRECTO__ActivarFormulario(){
	//xGetElementById("REFERENCIA_FCD").readOnly=false;
	xGetElementById("FECHA_FCD").readOnly=false;
	xGetElementById("CONCEPTO_FCD").readOnly=false;
	xGetElementById("BOTON_PROVEEDOR_FCD").disabled=false;
	xGetElementById("BOTON_BENEFICIARIO_FCD").disabled=false;
	xGetElementById("MONTO_FCD").readOnly=false;
	xGetElementById("N_CHEQUE_FCD").readOnly=false;

	//xGetElementById("REFERENCIA_FCD").setAttribute('class','TextoCampoInput');
	xGetElementById("FECHA_FCD").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CONCEPTO_FCD").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("MONTO_FCD").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("N_CHEQUE_FCD").setAttribute('class','TextoCampoInputObligatorios');

	xGetElementById("IMG_FECHA_FCD").setAttribute('onclick',"showCalendar('FECHA_FCD','%d/%m/%Y')");
	xGetElementById("FECHA_FCD").setAttribute('ondblclick',"showCalendar('FECHA_FCD','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FCD","IMG_FECHA_FCD",'calendario');
	ActivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD",'buscar');

	if(Form_CHEQUE_DIRECTO__SW_PERSONA=="P")
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCD','ID_BENEFICIARIO_PROVEEDOR_FCD','NOMBRE_BENEFICIARIO_PROVEEDOR_FCD','','CUENTA_CONTABLE_PB_FCD')");
	else
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCD','ID_BENEFICIARIO_PROVEEDOR_FCD','NOMBRE_BENEFICIARIO_PROVEEDOR_FCD','','CUENTA_CONTABLE_PB_FCD')");

	xGetElementById("IMG_BUSCAR_NCTA_FCD").setAttribute('onclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FCD','NCTA_FCD','DESCRIPCION_NCTA_FCD','TIPO_CTA_FCD','BANCO_FCD','CTA_CODIGO_CONTABLE_FCD','CUENTA_CONTABLE_FCD','CTA_DENOMINACION_CONTABLE_FCD','');");
	ActivarBoton("IMG_BUSCAR_NCTA_FCD","IMG_BUSCAR_NCTA_FCD",'buscar');
	//xGetElementById("IMG_LIMPIAR_PB_FCD").setAttribute('onclick',"xGetElementById('ID_BoP_FCD').value=''; xGetElementById('ID_BENEFICIARIO_PROVEEDOR_FCD').value=''; xGetElementById('NOMBRE_BENEFICIARIO_PROVEEDOR_FCD').value='';");
	//ActivarBoton("IMG_LIMPIAR_PB_FCD","IMG_LIMPIAR_PB_FCD",'limpiar');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_CHEQUE_DIRECTO__DesactivarFormulario(){
	//xGetElementById("REFERENCIA_FCD").readOnly=true;
	xGetElementById("FECHA_FCD").readOnly=true;
	xGetElementById("CONCEPTO_FCD").readOnly=true;
	xGetElementById("BOTON_PROVEEDOR_FCD").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_FCD").disabled=true;
	xGetElementById("MONTO_FCD").readOnly=true;
	xGetElementById("N_CHEQUE_FCD").readOnly=true;

	//xGetElementById("REFERENCIA_FCD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_FCD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_FCD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("MONTO_FCD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("N_CHEQUE_FCD").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_FECHA_FCD").setAttribute('onclick',"");
	xGetElementById("FECHA_FCD").setAttribute('ondblclick',"");

	DesactivarBoton("IMG_FECHA_FCD","IMG_FECHA_FCD",'calendario');
	DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD",'buscar');
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD").setAttribute( 'onclick',"");

	xGetElementById("IMG_BUSCAR_NCTA_FCD").setAttribute('onclick',"");
	DesactivarBoton("IMG_BUSCAR_NCTA_FCD","IMG_BUSCAR_NCTA_FCD",'buscar');
	
	//xGetElementById("IMG_LIMPIAR_PB_FCD").setAttribute('onclick',"");
	//DesactivarBoton("IMG_LIMPIAR_PB_FCD","IMG_LIMPIAR_PB_FCD",'limpiar');
	}

/**
* Activa el boton modificar
*/
function Form_CHEQUE_DIRECTO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FCD","IMG_MODIFICAR_FCD",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_CHEQUE_DIRECTO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FCD","IMG_MODIFICAR_FCD",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_CHEQUE_DIRECTO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FCD","IMG_GUARDAR_FCD",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_CHEQUE_DIRECTO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FCD","IMG_GUARDAR_FCD",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_CHEQUE_DIRECTO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FCD","IMG_ELIMINAR_FCD",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_CHEQUE_DIRECTO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FCD","IMG_ELIMINAR_FCD",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_CHEQUE_DIRECTO__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_CHEQUE_DIRECTO__IDTipoOperacionSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_CHEQUE_DIRECTO__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar="";
var Form_CHEQUE_DIRECTO__SW_PERSONA="";



/**
* Nueva definicion
*/
function Form_CHEQUE_DIRECTO__Nuevo(){
	
	
	
	Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado();

	//if(xGetElementById("MODULO_ACTUAL").value=="MODULO_CONTABILIDAD")
	//	Form_CHEQUE_DIRECTO__TabPaneSUBTAB.setSelectedIndex(1);
	//else
	//	Form_CHEQUE_DIRECTO__TabPaneSUBTAB.setSelectedIndex(0);
	Form_CHEQUE_DIRECTO__TabPane.setSelectedIndex(0);
	}

function Form_CHEQUE_DIRECTO__BotonProveedor(){
	xGetElementById("TIPO_PERSONA_FCD").innerHTML="Proveedor";
	xGetElementById("TITULO_PB_FCD_LISTADO").innerHTML="PROVEEDOR";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCD','ID_BENEFICIARIO_PROVEEDOR_FCD','NOMBRE_BENEFICIARIO_PROVEEDOR_FCD','','CUENTA_CONTABLE_PB_FCD')");
	xGetElementById("ID_BoP_FCD").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCD").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCD").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FCD").value="";
	Form_CHEQUE_DIRECTO__SW_PERSONA="P";
	if(!(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0))
		Form_CHEQUE_DIRECTO__BuscarListado();
	}

function Form_CHEQUE_DIRECTO__BotonBeneficiario(){
	xGetElementById("TIPO_PERSONA_FCD").innerHTML="Beneficiario";
	xGetElementById("TITULO_PB_FCD_LISTADO").innerHTML="BENEFICIARIO";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCD").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCD','ID_BENEFICIARIO_PROVEEDOR_FCD','NOMBRE_BENEFICIARIO_PROVEEDOR_FCD','','CUENTA_CONTABLE_PB_FCD')");
	xGetElementById("ID_BoP_FCD").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCD").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCD").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FCD").value="";
	Form_CHEQUE_DIRECTO__SW_PERSONA="B";
	if(!(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0))
		Form_CHEQUE_DIRECTO__BuscarListado();
	}








/*Verifica que los campos obligatorios esten llenos y la existencia (duplicidad) antes de guardar*/
function Form_CHEQUE_DIRECTO__Guardar(){
	/*Se verifica que los campos obligatorio esten llenos*/
	Form_CHEQUE_DIRECTO__TabPane.setSelectedIndex(0);


	var _id_persona							= xTrim(strtoupper(xGetElementById("ID_BoP_FCD").value));
	var _id_banco_cuenta				= xTrim(strtoupper(xGetElementById("ID_CTA_FCD").value));
	var _fecha									= xTrim(strtoupper(xGetElementById("FECHA_FCD").value));
	var _numero									= xTrim(strtoupper(xGetElementById("N_CHEQUE_FCD").value));
	var _concepto								= xTrim(strtoupper(xGetElementById("CONCEPTO_FCD").value));
	var _monto									= xTrim(strtoupper(xGetElementById("MONTO_FCD").value));
	var _id_cuenta_contable			= xGetElementById("CTA_CODIGO_CONTABLE_FCD").value;
	
	if(!_id_persona){
		msg="Por favor seleccione el proveedor o beneficiario.";
		Form_CHEQUE_DIRECTO__Mensaje(msg,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
	if(!_id_banco_cuenta){
		msg="Por favor seleccione la cuenta bancaria.";
		Form_CHEQUE_DIRECTO__Mensaje(msg,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
	if(!_fecha){
		msg="Por favor introduzca la fecha.";
		Form_CHEQUE_DIRECTO__Mensaje(msg,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_CHEQUE_DIRECTO__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	
	if(!_numero){
		msg="Por favor introduzca el número del cheque.";
		Form_CHEQUE_DIRECTO__Mensaje(msg,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
	if(!_concepto){
		msg="Por favor introduzca el concepto.";
		Form_CHEQUE_DIRECTO__Mensaje(msg,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}
		
	
	if(xGetElementById("TOTAL_DEBE_FCD_DC").value!=xGetElementById("TOTAL_HABER_FCD_DC").value){
		Form_CHEQUE_DIRECTO__Mensaje("El total por el debe no coincide con el total del haber.","ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado("");
		return;
		}

	//buscar la cuenta contable del banco seleccionado
	var _encontro=false;
	for(var i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;i++)
		if(_id_cuenta_contable==Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][0]) {
			//verificar el monto del cheque con el monto de la cuenta contable
			if(_monto!=Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]){
				Form_CHEQUE_DIRECTO__Mensaje("El monto del cheque no coincide con el monto de la cuenta contable asociada.","ROJO");
				Form_CHEQUE_DIRECTO__MensajeListado("");
				return;
			}
			_encontro=true;
			break;
		}
	
	if(Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables>0) 
		if(!_encontro) {
			Form_CHEQUE_DIRECTO__Mensaje("No se encontro en los detalles contables la cuenta asociada a la cuenta bancaria.","ROJO");
			Form_CHEQUE_DIRECTO__MensajeListado("");
			return;
		}
	

	Form_CHEQUE_DIRECTO__DesactivarFormulario();

	var _detalle={};
	_detalle.presupuestario=[];
	for(var i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;i++){
		_detalle.presupuestario[i]={
			id_accion_subespecifica: Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][0],
			id_cuenta_presupuestaria: Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][2],
			operacion: Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][6],
			monto: Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]
		};
	}
	
	_detalle.contable=[];
	for(var i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;i++){
		_detalle.contable[i]={
			id_cuenta_contable: Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][0],
			operacion: Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3],
			monto: Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]			
		};
	}
	
	_detalle.comprobante_bancario={
		id_banco_cuenta: _id_banco_cuenta,
		id_banco_movimiento_tipo: 2,//cheque directo
		numero: _numero,
		monto: _monto
	};
	
	var _id_comprobante="";
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_DIRECTO__IDSeleccionActualLista;
	

	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_CHEQUE_DIRECTO__ActivarFormulario();
			return;
			}
		}
	
	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo': 'MB',
										'fecha':_fecha,
										'concepto':_concepto,
										'contabilizado': 'f',
										'id_persona': _id_persona,										
										'detalle': Ext.encode(_detalle)
										},
						'onSuccess':Form_CHEQUE_DIRECTO__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	

	}




/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_CHEQUE_DIRECTO__GuardarMensaje(req){
	Form_CHEQUE_DIRECTO__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_CHEQUE_DIRECTO__Nuevo();
		Form_CHEQUE_DIRECTO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_CHEQUE_DIRECTO__Mensaje(respuesta.message,"ROJO");		
	}


/*Al presionar enter buscamos directamente en el listado*/
function Form_CHEQUE_DIRECTO__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FCD").checked){
 		if(ev.keyCode==13)
			Form_CHEQUE_DIRECTO__BuscarListado();
		return;
		}
	Form_CHEQUE_DIRECTO__BuscarListado();
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_CHEQUE_DIRECTO__BuscarListado(){
	Form_CHEQUE_DIRECTO__contabilizado="";
	Form_CHEQUE_DIRECTO__IDSeleccionActualLista=-1;
	Form_CHEQUE_DIRECTO__IDTipoOperacionSeleccionActualLista=-1;
	//xGetElementById("FORMULARIO_FCD").reset();
	Form_CHEQUE_DIRECTO__OcultarBotones();
	
	Form_CHEQUE_DIRECTO__ActivarFormulario();
	Form_CHEQUE_DIRECTO__DesactivarBotonModificar();
	Form_CHEQUE_DIRECTO__DesactivarBotonEliminar();
	Form_CHEQUE_DIRECTO__ActivarBotonGuardar();

	//xGetElementById("ACRONIMO_FCD").value="PC";

	Form_CHEQUE_DIRECTO__ModificarTabla=false;
	Form_CHEQUE_DIRECTO__MostrarTablaDP();
	Form_CHEQUE_DIRECTO__MostrarTablaDC();

	


	var CadenaBuscar=xGetElementById("LISTADO_BUSCAR_FCD").value;
	if(CadenaBuscar!="")
		if(Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(Form_CHEQUE_DIRECTO__BuscarListado_CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FCD").innerHTML=IconoCargandoTabla;
	else{//busco el n del documento y lo coloco en el input text numero

		}

	var _tipo_persona=Form_CHEQUE_DIRECTO__SW_PERSONA=="P"?"J":"N";
		
	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_FCD").value,		
		'tipo':['MB'],
		'operacion_codigo': ['PD'],
		'persona': 't',
		'tipo_persona': _tipo_persona
	};
	
	//var _mostrar={
	//	'mes':xGetElementById("MES_FILTRAR_FCD").value
	//};
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort': '[{"property":"fecha","direction":"DESC"},{"property":"tipo","direction":"ASC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_CHEQUE_DIRECTO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}
	

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_CHEQUE_DIRECTO__MostrarListado(req){
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
	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCD").value));

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
		
		FuncionOnclick="Form_CHEQUE_DIRECTO__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
		FuncionOnDblclick="Form_CHEQUE_DIRECTO__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FCD"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FCD"+resultado[i]['id']+"\")";

/*
		if(xGetElementById("SOMBRA_CHECKBOX_FCD").checked && TextoBuscar!=""){
			CadAux1=str_replace(resultado[i]['tipo'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(resultado[i]['correlativo'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux1=resultado[i]['tipo'];
			CadAux2=resultado[i]['correlativo'];
			CadAux3=resultado[i]['fecha'];
			CadAux4=resultado[i]['concepto'];
			}

		Contenido+="<TR id='FCD"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";
		Contenido+="<TD width='3%' class='FilaEstilo' align='left'>"+CadAux1+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua'>"+CadAux4+"</TD>";

		
		*/
		if(!resultado[i]['persona']) 
			resultado[i]['persona']="";
		
		
		if(xGetElementById("SOMBRA_CHECKBOX_FCD").checked && TextoBuscar!=""){
			//CadAux1=str_replace(completarCodigoCeros(resultado[i]['id_cheque'],NDigitos_Codigo_VoucherCheque),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(resultado[i]['numero'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux5=str_replace(resultado[i]['persona'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux6=FormatearNumero(resultado[i]['monto']);
			}
		else{
			//CadAux1=completarCodigoCeros(resultado[i]['id_cheque'],NDigitos_Codigo_VoucherCheque);
			CadAux2=resultado[i]['numero'];
			CadAux3=resultado[i]['fecha'];
			CadAux4=resultado[i]['concepto'];
			CadAux5=resultado[i]['persona'];
			CadAux6=FormatearNumero(resultado[i]['monto']);
			}
		
		Contenido+="<TR id='FCD"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";

		//Contenido+="<TD width='1%' class='FilaEstilo'><INPUT id='CBL_FCOP"+resultado[i]['id']+"' type='checkbox' "+Aux+" onchange='Form_CHEQUE_DIRECTO__ListadoAlternarCustoria("+resultado[i]['id']+")' title='"+Form_CHEQUE_DIRECTO__MsgCustodia(resultado[i]['entregado']=="t"?1:0)+"'></TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		Contenido+="<TD width='25%' class='FilaEstiloContinua'>"+CadAux5+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' style='padding-left: 5px;'>"+CadAux4+"</TD>";		
		Contenido+="<TD width='10%' class='FilaEstilo' align='right'>"+CadAux6+"</TD>";
		
		
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCD").innerHTML=Contenido;
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
function Form_CHEQUE_DIRECTO__SeleccionarElementoTabla(IDSeleccion,estado){
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==IDSeleccion)
		return;
	
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista!=-1)
		xGetElementById("FCD"+Form_CHEQUE_DIRECTO__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FCD"+IDSeleccion).bgColor=colorBase;
	
	//if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==IDSeleccion)
	//	return;
	
	Form_CHEQUE_DIRECTO__IDSeleccionActualLista=IDSeleccion;
	
	xGetElementById("ID_BoP_FCD").value="";
	xGetElementById("TIPO_PERSONA_FCD").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCD").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCD").value="";
	Form_CHEQUE_DIRECTO__OcultarBotones();
	Form_CHEQUE_DIRECTO__DesactivarFormulario();
	Form_CHEQUE_DIRECTO__ActivarBotonModificar();
	Form_CHEQUE_DIRECTO__ActivarBotonEliminar();
	Form_CHEQUE_DIRECTO__DesactivarBotonGuardar();
	Form_CHEQUE_DIRECTO__Mensaje("");
	Form_CHEQUE_DIRECTO__MensajeListado("");
	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_CHEQUE_DIRECTO__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								
								
								
								//console.log(resultado);
								
								xGetElementById("COMPROBANTE_FCD").innerHTML=resultado[0]["tipo"]+"-"+resultado[0]["correlativo"];
								
								xGetElementById("FECHA_FCD").value=resultado[0]["fecha"];
								xGetElementById("CONCEPTO_FCD").value=resultado[0]["concepto"];
								
								//cargar proveedor/beneficiario
								xGetElementById("ID_BoP_FCD").value=resultado[0]["detalle_persona"][0]["id"];
								xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCD").value=resultado[0]["detalle_persona"][0]["identificacion"];
								xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCD").value=resultado[0]["detalle_persona"][0]["denominacion"];
								
								//numero de cuenta
								xGetElementById("ID_CTA_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["id_banco_cuenta"];
								xGetElementById("NCTA_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["numero_cuenta"];
								xGetElementById("DESCRIPCION_NCTA_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_denominacion"];
								xGetElementById("CTA_CODIGO_CONTABLE_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["id_cuenta_contable"];
								xGetElementById("CUENTA_CONTABLE_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_contable"];
								xGetElementById("CTA_DENOMINACION_CONTABLE_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["denominacion_contable"];
								xGetElementById("TIPO_CTA_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_tipo"];
								xGetElementById("BANCO_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["banco"];
								
								//otros datos del cheque
								xGetElementById("N_CHEQUE_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["numero"];
								xGetElementById("MONTO_FCD").value=resultado[0]["detalle_comprobante_bancario"][0]["monto"];
								
								//cargar detalle presupuestarios								
								if(resultado[0]["detalle_presupuestario"]){
									var n=resultado[0]["detalle_presupuestario"].length;
									Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios=n
									for(var i=0;i<n;i++){
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i]=new Array(8);
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][0]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][2]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][3]=resultado[0]["detalle_presupuestario"][i]['estructura_presupuestaria'];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][4]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][5]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][6]=resultado[0]["detalle_presupuestario"][i]["operacion"];
										Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]=resultado[0]["detalle_presupuestario"][i]["monto"];
										}
									Form_CHEQUE_DIRECTO__MostrarTablaDP();
									}
								
								//cargar detalle contable
								if(resultado[0]["detalle_contable"]){
									var n=resultado[0]["detalle_contable"].length;
									Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables=n;
									for(var i=0;i<n;i++){
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i]=new Array(5);
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][0]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][1]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][2]=resultado[0]["detalle_contable"][i]["denominacion"];
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=resultado[0]["detalle_contable"][i]["operacion"];
										Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]=resultado[0]["detalle_contable"][i]["monto"];
										}
									Form_CHEQUE_DIRECTO__MostrarTablaDC();
									}
								
								
								Form_CHEQUE_DIRECTO__OcultarBotones();
								switch(estado){
									//SIN CONTABILIZAR (ROJO)
									case 0://activar el boton de modificar, mostrar el boton de contabilizar															
										Form_CHEQUE_DIRECTO__ActivarBotonModificar();
										xGetElementById("BOTON_CONTABLIZAR_FCD").style.display="";
										break;
									//CONTABILIZADO (VERDE)
									case 2://mostrar el boton de reversar y anular
										xGetElementById("BOTON_REVERSAR_FCD").style.display="";
										xGetElementById("BOTON_ANULAR_FCD").style.display="";
										break;
									case 3:
										
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
function Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado(){
	Form_CHEQUE_DIRECTO__ArregloTitulosPresupuestarios=new Array();
	Form_CHEQUE_DIRECTO__ModificarTabla=false;
	Form_CHEQUE_DIRECTO__IDComprobante="";
	Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios=0;
	Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables=0;
	xGetElementById("TABLA_LISTA_ARTICULOS_FCD_DP").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FCD_DC").innerHTML="";

	Form_CHEQUE_DIRECTO__IDBancoSeleccionActualLista=-1;
	Form_CHEQUE_DIRECTO__IDTipoOperacionSeleccionActualLista=-1;
	Form_CHEQUE_DIRECTO__IDSeleccionActualLista=-1;
	Form_CHEQUE_DIRECTO__Denominacion="";
	Form_CHEQUE_DIRECTO__DesactivarBotonModificar();
	Form_CHEQUE_DIRECTO__DesactivarBotonEliminar();
	Form_CHEQUE_DIRECTO__ActivarBotonGuardar();
	Form_CHEQUE_DIRECTO__ActivarFormulario();
	xGetElementById("FORMULARIO_FCD").reset();
	xGetElementById("LISTADO_BUSCAR_FCD").value="";
	Form_CHEQUE_DIRECTO__Mensaje("");
	Form_CHEQUE_DIRECTO__MensajeListado("");
	Form_CHEQUE_DIRECTO__BuscarListado();

	
	//DarFocoCampo("LISTADO_BUSCAR_FCD",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_CHEQUE_DIRECTO__Modificar(){
	Form_CHEQUE_DIRECTO__ModificarTabla=true;
	Form_CHEQUE_DIRECTO__MostrarTablaDP();
	Form_CHEQUE_DIRECTO__MostrarTablaDC();
	Form_CHEQUE_DIRECTO__ActivarFormulario();
	Form_CHEQUE_DIRECTO__ActivarBotonGuardar();
	Form_CHEQUE_DIRECTO__DesactivarBotonModificar();
	Form_CHEQUE_DIRECTO__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_CHEQUE_DIRECTO__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	var _id_comprobante="";
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_DIRECTO__IDSeleccionActualLista;
	
	if(_id_comprobante=="")
		return;
	
	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	var _msj="La opción se encuentra desactivada, contacta al administrador del sistema.";
	Form_CHEQUE_DIRECTO__Mensaje(_msj,"ROJO");
	Form_CHEQUE_DIRECTO__MensajeListado(_msj,"ROJO");
	return;
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_comprobante},
				'onSuccess':Form_CHEQUE_DIRECTO__EliminarMensaje,
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_CHEQUE_DIRECTO__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_CHEQUE_DIRECTO__LimpiarInputTextBuscarListado();
		Form_CHEQUE_DIRECTO__Mensaje(respuesta.message,"VERDE");
		Form_CHEQUE_DIRECTO__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_CHEQUE_DIRECTO__Mensaje(respuesta.message,"ROJO");
		Form_CHEQUE_DIRECTO__MensajeListado(respuesta.message,"ROJO");
		}
	}

var Form_MOV_DP__iSeleccionActual=-1;
var Form_MOV_DC__iSeleccionActual=-1;

function Form_CHEQUE_DIRECTO__MostrarTablaDP(){
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
	sw=Form_CHEQUE_DIRECTO__ModificarTabla;
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==-1)
		sw=true;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FCD_DP","IMG_AGREGAR_FCD_DP",'agregar');
		ActivarBoton("BOTON_QUITAR_FCD_DP","IMG_QUITAR_FCD_DP",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FCD_DP","IMG_AGREGAR_FCD_DP",'agregar');
		DesactivarBoton("BOTON_QUITAR_FCD_DP","IMG_QUITAR_FCD_DP",'quitar');
		}

	for(var i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DP__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DP__ModificarValorCelda("+i+")";
			}

 		//FuncionOnMouseOver="pintarFila(\"FCD_DP"+i+"\");Form_MOV_DP__MostrarInfExtra("+i+");";
 		//FuncionOnMouseOut="despintarFila(\"FCD_DP"+i+"\");Form_MOV_DP__OcultarInfExtra();";
		
		//FuncionOnMouseOver="pintarFila(\"FCD_DP"+i+"\");";
 		//FuncionOnMouseOut="despintarFila(\"FCD_DP"+i+"\");";

		TOTAL+=Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]*1.0;

		Contenido+="<TR class='FilaListado' id='FCD_DP"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='15%'  style='font-size : 11px;'>"+Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][3]+"</TD>";
		Contenido+="<TD width='10%' align='center' style='font-size : 11px;'>"+Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][4]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][5]+"</TD>";
		Contenido+="<TD width='7%'  align='center' style='font-size : 11px;'>"+Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][6]+"</TD>";
		Contenido+="<TD width='10%' align='right'  style='font-size : 11px;' id='TD_FCD_DP_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FCD_DP").innerHTML=Contenido;
	xGetElementById("TOTAL_COMPROMISOS_FCD_DP").value=FormatearNumero(TOTAL);


	}



function Form_MOV_DP__SeleccionarElementoTabla(i){
	if(Form_MOV_DP__iSeleccionActual!=-1)
		xGetElementById("FCD_DP"+Form_MOV_DP__iSeleccionActual).style.background="";
	xGetElementById("FCD_DP"+i).style.background=colorSeleccionTabla;
	Form_MOV_DP__iSeleccionActual=i;
	}

function Form_MOV_DP__ModificarValorCelda(i){
	if(xGetElementById("FCD_DP_txt_celda"))
		return;
	Valor=Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FCD_DP_"+i).innerHTML="<INPUT id='FCD_DP_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DP__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FCD_DP_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DP__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FCD_DP_txt_celda").focus();
	}

function Form_MOV_DP__ModificarValorCeldaPierdeFoco(i){
	Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]=numberFormat(xGetElementById("FCD_DP_txt_celda").value,2);
	xGetElementById("TD_FCD_DP_"+i).innerHTML=FormatearNumero(Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;k++)
		TOTAL+=Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[k][7]*1.0;
	xGetElementById("TOTAL_COMPROMISOS_FCD_DP").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DP__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios)
			return;		
		xGetElementById("FCD_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DP__ModificarValorCelda(i+1);
		Form_MOV_DP__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FCD_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DP__ModificarValorCelda(i-1);
		Form_MOV_DP__SeleccionarElementoTabla(i-1);
		}
	}








function Form_CHEQUE_DIRECTO__MostrarTablaDC(){
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
	sw=Form_CHEQUE_DIRECTO__ModificarTabla;
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==-1)
		sw=true;


	if(sw){
		ActivarBoton("BOTON_AGREGAR_FCD_DC","IMG_AGREGAR_FCD_DC",'agregar');
		ActivarBoton("BOTON_QUITAR_FCD_DC","IMG_QUITAR_FCD_DC",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FCD_DC","IMG_AGREGAR_FCD_DC",'agregar');
		DesactivarBoton("BOTON_QUITAR_FCD_DC","IMG_QUITAR_FCD_DC",'quitar');
		}

	for(var i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DC__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DC__ModificarValorCelda("+i+")";
			}


 		//FuncionOnMouseOver="pintarFila(\"FCD_DC"+i+"\")";
 		//FuncionOnMouseOut="despintarFila(\"FCD_DC"+i+"\")";


		Contenido+="<TR class='FilaListado' id='FCD_DC"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		Contenido+="<TD width='15%' style='font-size : 12px;'>"+Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][1]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][2]+"</TD>";

		if(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=='D' || Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=='d'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FCD_DC_"+i+"'>"  +FormatearNumero(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4])+"</TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_CHEQUE_DIRECTO__CambiarOperacionContable("+i+",'H');\"></TD>";
			TOTAL_DEBE+=Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]*1.0;
			}
		else if(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=='H' || Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=='h'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_CHEQUE_DIRECTO__CambiarOperacionContable("+i+",'D');\"></TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FCD_DC_"+i+"'>" +FormatearNumero(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4])+"</TD>";
			TOTAL_HABER+=Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]*1.0;
			}

		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FCD_DC").innerHTML=Contenido;
	xGetElementById("TOTAL_DEBE_FCD_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FCD_DC").value=FormatearNumero(TOTAL_HABER);

	}

function Form_CHEQUE_DIRECTO__CambiarOperacionContable(i,Operacion){
	var sw=Form_CHEQUE_DIRECTO__ModificarTabla;
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==-1)
		sw=true;
	if(sw==false) return;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=Operacion;
	Form_CHEQUE_DIRECTO__MostrarTablaDC();
	}


function Form_MOV_DC__SeleccionarElementoTabla(i){
	if(Form_MOV_DC__iSeleccionActual!=-1)
		xGetElementById("FCD_DC"+Form_MOV_DC__iSeleccionActual).style.background="";
	xGetElementById("FCD_DC"+i).style.background=colorSeleccionTabla;
	Form_MOV_DC__iSeleccionActual=i;
	}

function Form_MOV_DC__ModificarValorCelda(i){
	if(xGetElementById("FCD_DC_txt_celda"))
		return;
	Valor=Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FCD_DC_"+i).innerHTML="<INPUT id='FCD_DC_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DC__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FCD_DC_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DC__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FCD_DC_txt_celda").focus();
	}

function Form_MOV_DC__ModificarValorCeldaPierdeFoco(i){
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]=numberFormat(xGetElementById("FCD_DC_txt_celda").value,2);
	xGetElementById("TD_FCD_DC_"+i).innerHTML=FormatearNumero(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]);
	//recalcular la suma
	var TOTAL_DEBE=0;
	var TOTAL_HABER=0;
	for(var k=0;k<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;k++){
		if(Form_CHEQUE_DIRECTO__ArregloDetallesContables[k][3]=="D"||Form_CHEQUE_DIRECTO__ArregloDetallesContables[k][3]=="d")
			TOTAL_DEBE+=Form_CHEQUE_DIRECTO__ArregloDetallesContables[k][4]*1.0;
		else
			TOTAL_HABER+=Form_CHEQUE_DIRECTO__ArregloDetallesContables[k][4]*1.0;
		}
	xGetElementById("TOTAL_DEBE_FCD_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FCD_DC").value=FormatearNumero(TOTAL_HABER);
	}

function Form_MOV_DC__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables)
			return;
		xGetElementById("FCD_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DC__SeleccionarElementoTabla(i+1)
		Form_MOV_DC__ModificarValorCelda(i+1);
		}
	else if(event.keyCode==38){//key up
		if((i-1)<0)
			return;
		xGetElementById("FCD_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DC__SeleccionarElementoTabla(i-1)
		Form_MOV_DC__ModificarValorCelda(i-1);
		}
	}






function Form_CHEQUE_DIRECTO__AgregarDP() {
	siga.open("detalle_presupuestario",{
		operacion: ['CCP'],
		onAdd: function(me){
			//verificar si existe, si existe sumarlo al anterior
			for(i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;i++)
				if(Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][0]==me.internal.data.id_accion_subespecifica &&
					 Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][2]==me.internal.data.id_cuenta_presupuestaria &&
					 Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][6]==me.internal.data.operacion){
					Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]=Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]*1+me.internal.data.monto*1;
					Form_CHEQUE_DIRECTO__MostrarTablaDP();
					return;
					}
			
			var i=Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i]=new Array(8);
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][0]=me.internal.data.id_accion_subespecifica;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][2]=me.internal.data.id_cuenta_presupuestaria;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][3]=me.internal.data.estructura_presupuestaria;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][4]=me.internal.data.cuenta_presupuestaria;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][5]=me.internal.data.denominacion_presupuestaria;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][6]=me.internal.data.operacion;
			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][7]=me.internal.data.monto;
			Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios++;
			Form_CHEQUE_DIRECTO__MostrarTablaDP();
			
			//agregar detalle contable
			Form_CHEQUE_DIRECTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						'D',
																						me.internal.data.monto
																						);
		}
	});	
}

function Form_CHEQUE_DIRECTO__AgregarDC() {
	var _id_cuenta_contable=xGetElementById("CTA_CODIGO_CONTABLE_FCD").value;
	var _monto=xGetElementById("MONTO_FCD").value;
	siga.open("detalle_contable",{
		id_cuenta_contable: _id_cuenta_contable,
		operacion: 'H',
		monto: _monto,
		tooltip: 'Cuenta asociada a la cuenta bancaria',
		onAdd: function(me){			
			Form_CHEQUE_DIRECTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						me.internal.data.operacion,
																						me.internal.data.monto
																						);
		}
	});
}


function Form_CHEQUE_DIRECTO__AgregarItemDC(id_cuenta_contable, cuenta_contable, denominacion, operacion, monto){
	for(i=0;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;i++)
		if(Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][0]==id_cuenta_contable &&
			 Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]==operacion){
			Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]=Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]*1+monto*1;
			Form_CHEQUE_DIRECTO__MostrarTablaDC();
			return;
			}
	
	var i=Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i]=new Array(5);
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][0]=id_cuenta_contable;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][1]=cuenta_contable;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][2]=denominacion;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][3]=operacion;
	Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][4]=monto;
	Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables++;
	Form_CHEQUE_DIRECTO__MostrarTablaDC();
}







function Form_CHEQUE_DIRECTO__DP_Quitar(){
	if(Form_MOV_DP__iSeleccionActual==-1)
		return;

	Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios--;
	for(i=Form_MOV_DP__iSeleccionActual*1;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesPresupuestarios;i++)
 		for(j=0;j<8;j++)
 			Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i][j]= Form_CHEQUE_DIRECTO__ArregloDetallesPresupuestarios[i+1][j];

	Form_CHEQUE_DIRECTO__MostrarTablaDP();
	}

function Form_CHEQUE_DIRECTO__DC_Quitar(){
	if(Form_MOV_DC__iSeleccionActual==-1)
		return;

	Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables--;
	for(i=Form_MOV_DC__iSeleccionActual*1;i<Form_CHEQUE_DIRECTO__TamanoArregloDetallesContables;i++)
 		for(j=0;j<5;j++)
 			Form_CHEQUE_DIRECTO__ArregloDetallesContables[i][j]= Form_CHEQUE_DIRECTO__ArregloDetallesContables[i+1][j];

	Form_CHEQUE_DIRECTO__MostrarTablaDC();
	}

function Form_CHEQUE_DIRECTO__OcultarBotones(){
	xGetElementById("BOTON_ANULAR_FCD").style.display="none";
	xGetElementById("BOTON_CONTABLIZAR_FCD").style.display="none";
	xGetElementById("BOTON_REVERSAR_FCD").style.display="none";	
}

function Form_CHEQUE_DIRECTO__Contabilizar(){
	Form_CHEQUE_DIRECTO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_DIRECTO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_CHEQUE_DIRECTO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 't'
								},
				'onSuccess': function(req){
								Form_CHEQUE_DIRECTO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_CHEQUE_DIRECTO__Reversar(){
	Form_CHEQUE_DIRECTO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_DIRECTO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_CHEQUE_DIRECTO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 'f'
								},
				'onSuccess': function(req){
								Form_CHEQUE_DIRECTO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_CHEQUE_DIRECTO__Anular(){
	Form_CHEQUE_DIRECTO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_DIRECTO__IDSeleccionActualLista;
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
	
	Form_CHEQUE_DIRECTO__Mensaje("Anulando. Por favor espere...");
	Form_CHEQUE_DIRECTO__OcultarBotones();

	var _detalle={};
	_detalle.comprobante_previo='liberar';
	
	AjaxRequest.post({
				'parameters':{
								'action':"onAnular",
								'id': _id_comprobante,
								'fecha': _fecha,
								'detalle': Ext.encode(_detalle)
								},
				'onSuccess': function(req){
								Form_CHEQUE_DIRECTO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
}

/**
* Imprime el cheque
*/
function Form_CHEQUE_DIRECTO__Imprimir(){
	if(Form_CHEQUE_DIRECTO__IDSeleccionActualLista==-1)
		return;
	window.open("../../report/cheque.php?id="+Form_CHEQUE_DIRECTO__IDSeleccionActualLista);
	}

