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

var Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios=Array();
var Form_COMPROBANTE_GASTO__ArregloDetallesContables=Array();
var Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios=0;
var Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables=0;
var Form_COMPROBANTE_GASTO__ModificarTabla=false;

var Form_COMPROBANTE_GASTO__ArregloTitulosPresupuestarios=Array();
var Form_COMPROBANTE_GASTO__ArregloTitulosContables=Array();

var Form_COMPROBANTE_GASTO__contabilizado="";


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_COMPROBANTE_GASTO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCG").innerHTML=MSG;
	}



/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_COMPROBANTE_GASTO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCG_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_COMPROBANTE_GASTO__ActivarFormulario(){
	//xGetElementById("REFERENCIA_FCG").readOnly=false;
	xGetElementById("FECHA_FCG").readOnly=false;
	xGetElementById("DENOMINACION_FCG").readOnly=false;
	xGetElementById("BOTON_PROVEEDOR_FCG").disabled=false;
	xGetElementById("BOTON_BENEFICIARIO_FCG").disabled=false;

	//xGetElementById("REFERENCIA_FCG").setAttribute('class','TextoCampoInput');
	xGetElementById("FECHA_FCG").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DENOMINACION_FCG").setAttribute('class','TextoCampoInputObligatorios');

	xGetElementById("IMG_FECHA_FCG").setAttribute('onclick',"showCalendar('FECHA_FCG','%d/%m/%Y')");
	xGetElementById("FECHA_FCG").setAttribute('ondblclick',"showCalendar('FECHA_FCG','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FCG","IMG_FECHA_FCG",'calendario');
	ActivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG",'buscar');

	if(Form_COMPROBANTE_GASTO__SW_PERSONA=="P")
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCG','ID_BENEFICIARIO_PROVEEDOR_FCG','NOMBRE_BENEFICIARIO_PROVEEDOR_FCG','','CUENTA_CONTABLE_PB_FCG')");
	else
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCG','ID_BENEFICIARIO_PROVEEDOR_FCG','NOMBRE_BENEFICIARIO_PROVEEDOR_FCG','','CUENTA_CONTABLE_PB_FCG')");

	xGetElementById("IMG_LIMPIAR_PB_FCG").setAttribute('onclick',"xGetElementById('ID_BoP_FCG').value=''; xGetElementById('ID_BENEFICIARIO_PROVEEDOR_FCG').value=''; xGetElementById('NOMBRE_BENEFICIARIO_PROVEEDOR_FCG').value='';");
	ActivarBoton("IMG_LIMPIAR_PB_FCG","IMG_LIMPIAR_PB_FCG",'limpiar');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_COMPROBANTE_GASTO__DesactivarFormulario(){
	//xGetElementById("REFERENCIA_FCG").readOnly=true;
	xGetElementById("FECHA_FCG").readOnly=true;
	xGetElementById("DENOMINACION_FCG").readOnly=true;
	xGetElementById("BOTON_PROVEEDOR_FCG").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_FCG").disabled=true;

	//xGetElementById("REFERENCIA_FCG").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_FCG").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DENOMINACION_FCG").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_FECHA_FCG").setAttribute('onclick',"");
	xGetElementById("FECHA_FCG").setAttribute('ondblclick',"");

	DesactivarBoton("IMG_FECHA_FCG","IMG_FECHA_FCG",'calendario');
	DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG",'buscar');
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG").setAttribute( 'onclick',"");

	xGetElementById("IMG_LIMPIAR_PB_FCG").setAttribute('onclick',"");
	DesactivarBoton("IMG_LIMPIAR_PB_FCG","IMG_LIMPIAR_PB_FCG",'limpiar');
	}

/**
* Activa el boton modificar
*/
function Form_COMPROBANTE_GASTO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FCG","IMG_MODIFICAR_FCG",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_COMPROBANTE_GASTO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FCG","IMG_MODIFICAR_FCG",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_COMPROBANTE_GASTO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FCG","IMG_GUARDAR_FCG",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_COMPROBANTE_GASTO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FCG","IMG_GUARDAR_FCG",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_COMPROBANTE_GASTO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FCG","IMG_ELIMINAR_FCG",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_COMPROBANTE_GASTO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FCG","IMG_ELIMINAR_FCG",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_COMPROBANTE_GASTO__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_COMPROBANTE_GASTO__IDTipoOperacionSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_COMPROBANTE_GASTO__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar="";
var Form_COMPROBANTE_GASTO__SW_PERSONA="";


var Form_COMPROBANTE_GASTO__Tipo_PC=SIGA.Ext.decode(SIGA.Ext.Ajax.request({async: false, url: "module/comprobante_tipo/", params:{action:'onGet',tipo:'PC'}}).responseText)[0][0];


/**
* Nueva definicion
*/
function Form_COMPROBANTE_GASTO__Nuevo(){
	Form_COMPROBANTE_GASTO__BotonProveedor();
	AjaxRequest.post({
						'parameters':{
									'action':"onGet_Correlativo",
									'tipo':"PC"									
									},
						'onSuccess': function(req){
											var respuesta = req.responseText;
											var resultado = eval("(" + respuesta + ")");
											xGetElementById("ACRONIMO_FCG").value="PC";
											xGetElementById("COMPROBANTE_FCG").value=completarCodigoCeros(String(resultado[0]["correlativo"]),10);
											xGetElementById("COMPROBANTE_TIPO_FCG").innerHTML=Form_COMPROBANTE_GASTO__Tipo_PC;
											},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	
	Form_COMPROBANTE_GASTO__LimpiarInputTextBuscarListado();

	//if(xGetElementById("MODULO_ACTUAL").value=="MODULO_CONTABILIDAD")
	//	Form_COMPROBANTE_GASTO__TabPaneSUBTAB.setSelectedIndex(1);
	//else
	//	Form_COMPROBANTE_GASTO__TabPaneSUBTAB.setSelectedIndex(0);
	Form_COMPROBANTE_GASTO__TabPane.setSelectedIndex(0);
	}

function Form_COMPROBANTE_GASTO__BotonProveedor(){
	xGetElementById("TIPO_PERSONA_FCG").innerHTML="Proveedor";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCG','ID_BENEFICIARIO_PROVEEDOR_FCG','NOMBRE_BENEFICIARIO_PROVEEDOR_FCG','','CUENTA_CONTABLE_PB_FCG')");
	xGetElementById("ID_BoP_FCG").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCG").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCG").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FCG").value="";
	Form_COMPROBANTE_GASTO__SW_PERSONA="P";
	}

function Form_COMPROBANTE_GASTO__BotonBeneficiario(){
	xGetElementById("TIPO_PERSONA_FCG").innerHTML="Beneficiario";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCG").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCG','ID_BENEFICIARIO_PROVEEDOR_FCG','NOMBRE_BENEFICIARIO_PROVEEDOR_FCG','','CUENTA_CONTABLE_PB_FCG')");
	xGetElementById("ID_BoP_FCG").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCG").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCG").value="";
	xGetElementById("CUENTA_CONTABLE_PB_FCG").value="";
	Form_COMPROBANTE_GASTO__SW_PERSONA="B";
	}








/*Verifica que los campos obligatorios esten llenos y la existencia (duplicidad) antes de guardar*/
function Form_COMPROBANTE_GASTO__Guardar(){
	/*Se verifica que los campos obligatorio esten llenos*/
	Form_COMPROBANTE_GASTO__TabPane.setSelectedIndex(0);
	var _tipo		= xTrim(strtoupper(xGetElementById("ACRONIMO_FCG").value));
	//var NumeroRef		= xTrim(strtoupper(xGetElementById("REFERENCIA_FCG").value));
	var _fecha			= xTrim(strtoupper(xGetElementById("FECHA_FCG").value));
	var _denominacion	= xTrim(strtoupper(xGetElementById("DENOMINACION_FCG").value));
	var _id_persona			= xTrim(strtoupper(xGetElementById("ID_BoP_FCG").value));



	if(!_fecha){
		Form_COMPROBANTE_GASTO__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_COMPROBANTE_GASTO__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_COMPROBANTE_GASTO__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_COMPROBANTE_GASTO__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	if(!_denominacion){
		Form_COMPROBANTE_GASTO__Mensaje("Por favor introduzca la denominación.","ROJO");
		Form_COMPROBANTE_GASTO__MensajeListado("");
		return;
		}
	if(xGetElementById("TOTAL_DEBE_FCG_DC").value!=xGetElementById("TOTAL_HABER_FCG_DC").value){
		Form_COMPROBANTE_GASTO__Mensaje("El total por el debe no coincide con el total del haber.","ROJO");
		Form_COMPROBANTE_GASTO__MensajeListado("");
		return;
		}


	Form_COMPROBANTE_GASTO__DesactivarFormulario();

	var _detalle={};
	_detalle.presupuestario=[];
	for(var i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;i++){
		_detalle.presupuestario[i]={
			id_accion_subespecifica: Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0],
			id_fuente_recursos: Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][1],
			id_cuenta_presupuestaria: Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2],
			operacion: Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6],
			monto: Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]
		};
	}
	
	_detalle.contable=[];
	for(var i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;i++){
		_detalle.contable[i]={
			id_cuenta_contable: Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][0],
			operacion: Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3],
			monto: Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]			
		};
	}
	
	var _id_comprobante="";
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_COMPROBANTE_GASTO__IDSeleccionActualLista;
	

	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_COMPROBANTE_GASTO__ActivarFormulario();
			return;
			}
		}
	
	if(Form_COMPROBANTE_GASTO__contabilizado=="")
		Form_COMPROBANTE_GASTO__contabilizado="t";
	
	
	AjaxRequest.post({
						'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo':_tipo,
										'fecha':_fecha,
										'concepto':_denominacion,
										'contabilizado': Form_COMPROBANTE_GASTO__contabilizado,
										'id_persona': _id_persona,										
										'detalle': SIGA.Ext.encode(_detalle)
										},
						'onSuccess':Form_COMPROBANTE_GASTO__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		
		
	}




/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_COMPROBANTE_GASTO__GuardarMensaje(req){
	Form_COMPROBANTE_GASTO__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_COMPROBANTE_GASTO__Nuevo();
		Form_COMPROBANTE_GASTO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_COMPROBANTE_GASTO__Mensaje(respuesta.message,"ROJO");		
	}


/*Al presionar enter buscamos directamente en el listado*/
function Form_COMPROBANTE_GASTO__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FCG").checked){
 		if(ev.keyCode==13)
			Form_COMPROBANTE_GASTO__BuscarListado();
		return;
		}
	Form_COMPROBANTE_GASTO__BuscarListado();
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_COMPROBANTE_GASTO__BuscarListado(){
	Form_COMPROBANTE_GASTO__contabilizado="";
	Form_COMPROBANTE_GASTO__IDSeleccionActualLista=-1;
	Form_COMPROBANTE_GASTO__IDTipoOperacionSeleccionActualLista=-1;
	//xGetElementById("FORMULARIO_FCG").reset();
	Form_COMPROBANTE_GASTO__ActivarFormulario();
	Form_COMPROBANTE_GASTO__DesactivarBotonModificar();
	Form_COMPROBANTE_GASTO__DesactivarBotonEliminar();
	Form_COMPROBANTE_GASTO__ActivarBotonGuardar();

	//xGetElementById("ACRONIMO_FCG").value="PC";

	Form_COMPROBANTE_GASTO__ModificarTabla=false;
	Form_COMPROBANTE_GASTO__MostrarTablaDP();
	Form_COMPROBANTE_GASTO__MostrarTablaDC();




	var CadenaBuscar=xGetElementById("LISTADO_BUSCAR_FCG").value;
	if(CadenaBuscar!="")
		if(Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(Form_COMPROBANTE_GASTO__BuscarListado_CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FCG").innerHTML=IconoCargandoTabla;
	else{//busco el n del documento y lo coloco en el input text numero

		}

	
	
	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_FCG").value
	};
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort': '[{"property":"C.fecha","direction":"DESC"},{"property":"tipo","direction":"ASC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_COMPROBANTE_GASTO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}
	

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_COMPROBANTE_GASTO__MostrarListado(req){
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
	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCG").value));

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
		
		FuncionOnclick="Form_COMPROBANTE_GASTO__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
		FuncionOnDblclick="Form_COMPROBANTE_GASTO__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FCG"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FCG"+resultado[i]['id']+"\")";


		if(xGetElementById("SOMBRA_CHECKBOX_FCG").checked && TextoBuscar!=""){
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

		Contenido+="<TR id='FCG"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";
		Contenido+="<TD width='3%' class='FilaEstilo' align='left'>"+CadAux1+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		//Contenido+="<TD width='67%' class='FilaEstilo'><DIV style='overflow : hidden; width:98%'><UL style='list-style-type : none; margin-bottom : 0; margin-left : 0; margin-right : 0%; margin-top : 0; padding-bottom : 0px; padding-left : 0px; padding-right : 0; padding-top : 0; text-align : left; white-space : nowrap; font-size : 12px;'><li>"+resultado[i]['denominacion_c']+"</li></UL><DIV></TD>";

		Contenido+="<TD class='FilaEstiloContinua'>"+CadAux4+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCG").innerHTML=Contenido;
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
function Form_COMPROBANTE_GASTO__SeleccionarElementoTabla(IDSeleccion){
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista!=-1)
		xGetElementById("FCG"+Form_COMPROBANTE_GASTO__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FCG"+IDSeleccion).bgColor=colorBase;
	
	//if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista==IDSeleccion)
	//	return;
	
	Form_COMPROBANTE_GASTO__IDSeleccionActualLista=IDSeleccion;
	
	xGetElementById("ID_BoP_FCG").value="";
	xGetElementById("TIPO_PERSONA_FCG").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCG").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCG").value="";
	Form_COMPROBANTE_GASTO__DesactivarFormulario();
	Form_COMPROBANTE_GASTO__ActivarBotonModificar();
	Form_COMPROBANTE_GASTO__ActivarBotonEliminar();
	Form_COMPROBANTE_GASTO__DesactivarBotonGuardar();
	Form_COMPROBANTE_GASTO__Mensaje("");
	Form_COMPROBANTE_GASTO__MensajeListado("");
	Form_COMPROBANTE_GASTO__SW_PERSONA="";
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_COMPROBANTE_GASTO__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								
								
								xGetElementById("TIPO_PERSONA_FCG").value="";
								xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCG").value="";
								xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCG").value="";
								
								
							
								xGetElementById("FECHA_FCG").value=resultado[0]["fecha"];
								xGetElementById("COMPROBANTE_FCG").value=resultado[0]["correlativo"];
								xGetElementById("ACRONIMO_FCG").value=resultado[0]["tipo"];
								xGetElementById("DENOMINACION_FCG").value=resultado[0]["concepto"];
								xGetElementById("COMPROBANTE_TIPO_FCG").innerHTML=resultado[0]["denominacion_tipo"];
								Form_COMPROBANTE_GASTO__contabilizado=resultado[0]["contabilizado"];
								//cargar proveedor/beneficiario
								xGetElementById("ID_BoP_FCG").value="";
								Form_COMPROBANTE_GASTO__BotonProveedor();
								if(resultado[0]["detalle_persona"]){
									switch(resultado[0]["detalle_persona"][0]["tipo"]){
										case "N":
											Form_COMPROBANTE_GASTO__SW_PERSONA="B";
											Form_COMPROBANTE_GASTO__BotonBeneficiario();
											break;
										case "J":
										default:
											Form_COMPROBANTE_GASTO__SW_PERSONA="P";
											Form_COMPROBANTE_GASTO__BotonProveedor();
											break;
										}
									xGetElementById("ID_BoP_FCG").value=resultado[0]["detalle_persona"][0]["id"];
									xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCG").value=resultado[0]["detalle_persona"][0]["identificacion"];
									xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCG").value=resultado[0]["detalle_persona"][0]["denominacion"];
									}
									
								
								//cargar detalle presupuestarios								
								if(resultado[0]["detalle_presupuestario"]){
									var n=resultado[0]["detalle_presupuestario"].length;
									Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios=n
									for(var i=0;i<n;i++){
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i]=new Array(8);
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][1]="";
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][3]=resultado[0]["detalle_presupuestario"][i]['estructura_presupuestaria'];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][4]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][5]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]=resultado[0]["detalle_presupuestario"][i]["operacion"];
										Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=resultado[0]["detalle_presupuestario"][i]["monto"];
										}
									Form_COMPROBANTE_GASTO__MostrarTablaDP();
									}
								
								//cargar detalle contable
								if(resultado[0]["detalle_contable"]){
									var n=resultado[0]["detalle_contable"].length;
									Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables=n;
									for(var i=0;i<n;i++){
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i]=new Array(5);
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][0]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][1]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][2]=resultado[0]["detalle_contable"][i]["denominacion"];
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=resultado[0]["detalle_contable"][i]["operacion"];
										Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]=resultado[0]["detalle_contable"][i]["monto"];
										}
									Form_COMPROBANTE_GASTO__MostrarTablaDC();
									}
								//cargar detalle bancario (pendiente)
								
								
								},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}



/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_COMPROBANTE_GASTO__LimpiarInputTextBuscarListado(){
	Form_COMPROBANTE_GASTO__ArregloTitulosPresupuestarios=new Array();
	Form_COMPROBANTE_GASTO__ModificarTabla=false;
	Form_COMPROBANTE_GASTO__IDComprobante="";
	Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios=0;
	Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables=0;
	xGetElementById("TABLA_LISTA_ARTICULOS_FCG_DP").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FCG_DC").innerHTML="";

	Form_COMPROBANTE_GASTO__IDBancoSeleccionActualLista=-1;
	Form_COMPROBANTE_GASTO__IDTipoOperacionSeleccionActualLista=-1;
	Form_COMPROBANTE_GASTO__IDSeleccionActualLista=-1;
	Form_COMPROBANTE_GASTO__Denominacion="";
	Form_COMPROBANTE_GASTO__DesactivarBotonModificar();
	Form_COMPROBANTE_GASTO__DesactivarBotonEliminar();
	Form_COMPROBANTE_GASTO__ActivarBotonGuardar();
	Form_COMPROBANTE_GASTO__ActivarFormulario();
	xGetElementById("FORMULARIO_FCG").reset();
	xGetElementById("LISTADO_BUSCAR_FCG").value="";
	Form_COMPROBANTE_GASTO__Mensaje("");
	Form_COMPROBANTE_GASTO__MensajeListado("");
	Form_COMPROBANTE_GASTO__BuscarListado();

	
	//DarFocoCampo("LISTADO_BUSCAR_FCG",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_COMPROBANTE_GASTO__Modificar(){
	Form_COMPROBANTE_GASTO__ModificarTabla=true;
	Form_COMPROBANTE_GASTO__MostrarTablaDP();
	Form_COMPROBANTE_GASTO__MostrarTablaDC();
	Form_COMPROBANTE_GASTO__ActivarFormulario();
	Form_COMPROBANTE_GASTO__ActivarBotonGuardar();
	Form_COMPROBANTE_GASTO__DesactivarBotonModificar();
	Form_COMPROBANTE_GASTO__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_COMPROBANTE_GASTO__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	var _id_comprobante="";
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_COMPROBANTE_GASTO__IDSeleccionActualLista;
	
	if(_id_comprobante=="")
		return;
	
	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_comprobante},
				'onSuccess':Form_COMPROBANTE_GASTO__EliminarMensaje,
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_COMPROBANTE_GASTO__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_COMPROBANTE_GASTO__LimpiarInputTextBuscarListado();
		Form_COMPROBANTE_GASTO__Mensaje(respuesta.message,"VERDE");
		Form_COMPROBANTE_GASTO__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_COMPROBANTE_GASTO__Mensaje(respuesta.message,"ROJO");
		Form_COMPROBANTE_GASTO__MensajeListado(respuesta.message,"ROJO");
		}
	}

var Form_MOV_DP__iSeleccionActual=-1;
var Form_MOV_DC__iSeleccionActual=-1;

function Form_COMPROBANTE_GASTO__MostrarTablaDP(){
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
	sw=Form_COMPROBANTE_GASTO__ModificarTabla;
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista==-1)
		sw=true;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FCG_DP","IMG_AGREGAR_FCG_DP",'agregar');
		ActivarBoton("BOTON_QUITAR_FCG_DP","IMG_QUITAR_FCG_DP",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FCG_DP","IMG_AGREGAR_FCG_DP",'agregar');
		DesactivarBoton("BOTON_QUITAR_FCG_DP","IMG_QUITAR_FCG_DP",'quitar');
		}

	for(var i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DP__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DP__ModificarValorCelda("+i+")";
			FuncionOnDblclick="Form_MOV_DP__ModificarElementoTabla("+i+")";
			}

 		//FuncionOnMouseOver="pintarFila(\"FCG_DP"+i+"\");Form_MOV_DP__MostrarInfExtra("+i+");";
 		//FuncionOnMouseOut="despintarFila(\"FCG_DP"+i+"\");Form_MOV_DP__OcultarInfExtra();";
		
		//FuncionOnMouseOver="pintarFila(\"FCG_DP"+i+"\");";
 		//FuncionOnMouseOut="despintarFila(\"FCG_DP"+i+"\");";

		TOTAL+=Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]*1.0;

		Contenido+="<TR class='FilaListado' id='FCG_DP"+i+"' onclick=\""+FuncionOnclick+"\" onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		Contenido+="<TD width='15%'  style='font-size : 11px;' ondblclick='"+FuncionOnDblclick+"'>"+Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][3]+"</TD>";
		Contenido+="<TD width='10%' align='center' style='font-size : 11px;' ondblclick='"+FuncionOnDblclick+"'>"+Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][4]+"</TD>";
		Contenido+="<TD class='CeldaContinua' ondblclick='"+FuncionOnDblclick+"'>"+Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][5]+"</TD>";
		Contenido+="<TD width='7%'  align='center' style='font-size : 11px;' ondblclick='"+FuncionOnDblclick+"'>"+Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]+"</TD>";
		Contenido+="<TD width='10%' align='right'  style='font-size : 11px;' id='TD_FCG_DP_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7])+"</TD>";


		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FCG_DP").innerHTML=Contenido;
	xGetElementById("TOTAL_COMPROMISOS_FCG_DP").value=FormatearNumero(TOTAL);


	}



function Form_MOV_DP__SeleccionarElementoTabla(i){
	if(Form_MOV_DP__iSeleccionActual!=-1)
		xGetElementById("FCG_DP"+Form_MOV_DP__iSeleccionActual).style.background="";
	xGetElementById("FCG_DP"+i).style.background=colorSeleccionTabla;
	Form_MOV_DP__iSeleccionActual=i;
	}
	
function Form_MOV_DP__ModificarElementoTabla(i){
	//siga.open("modulo_presupuesto/detalle_presupuestario",{});
	
}

function Form_MOV_DP__ModificarValorCelda(i){
	if(xGetElementById("FCG_DP_txt_celda"))
		return;
	Valor=Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FCG_DP_"+i).innerHTML="<INPUT id='FCG_DP_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DP__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FCG_DP_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DP__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FCG_DP_txt_celda").focus();
	}

function Form_MOV_DP__ModificarValorCeldaPierdeFoco(i){
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=numberFormat(xGetElementById("FCG_DP_txt_celda").value,2);
	xGetElementById("TD_FCG_DP_"+i).innerHTML=FormatearNumero(Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]);
	//recalcular la suma
	var TOTAL=0;
	for(var k=0;k<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;k++)
		TOTAL+=Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[k][7]*1.0;
	xGetElementById("TOTAL_COMPROMISOS_FCG_DP").value=FormatearNumero(TOTAL);
	}

function Form_MOV_DP__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios)
			return;		
		xGetElementById("FCG_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DP__ModificarValorCelda(i+1);
		Form_MOV_DP__SeleccionarElementoTabla(i+1);
		}
	else if(event.keyCode==38){//key up		
		if((i-1)<0)
			return;
		xGetElementById("FCG_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DP__ModificarValorCelda(i-1);
		Form_MOV_DP__SeleccionarElementoTabla(i-1);
		}
	}








function Form_COMPROBANTE_GASTO__MostrarTablaDC(){
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
	sw=Form_COMPROBANTE_GASTO__ModificarTabla;
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista==-1)
		sw=true;


	if(sw){
		ActivarBoton("BOTON_AGREGAR_FCG_DC","IMG_AGREGAR_FCG_DC",'agregar');
		ActivarBoton("BOTON_QUITAR_FCG_DC","IMG_QUITAR_FCG_DC",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FCG_DC","IMG_AGREGAR_FCG_DC",'agregar');
		DesactivarBoton("BOTON_QUITAR_FCG_DC","IMG_QUITAR_FCG_DC",'quitar');
		}

	for(var i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DC__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DC__ModificarValorCelda("+i+")";
			}


 		//FuncionOnMouseOver="pintarFila(\"FCG_DC"+i+"\")";
 		//FuncionOnMouseOut="despintarFila(\"FCG_DC"+i+"\")";


		Contenido+="<TR class='FilaListado' id='FCG_DC"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		Contenido+="<TD width='15%' style='font-size : 12px;'>"+Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][1]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][2]+"</TD>";

		if(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=='D' || Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=='d'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FCG_DC_"+i+"'>"  +FormatearNumero(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4])+"</TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_COMPROBANTE_GASTO__CambiarOperacionContable("+i+",'H');\"></TD>";
			TOTAL_DEBE+=Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]*1.0;
			}
		else if(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=='H' || Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=='h'){
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick=\"Form_COMPROBANTE_GASTO__CambiarOperacionContable("+i+",'D');\"></TD>";
			Contenido+="<TD width='15%' align='right' style='font-size : 12px;' ondblclick='"+FuncionOnDblclickMONTO+"' id='TD_FCG_DC_"+i+"'>" +FormatearNumero(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4])+"</TD>";
			TOTAL_HABER+=Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]*1.0;
			}

		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FCG_DC").innerHTML=Contenido;
	xGetElementById("TOTAL_DEBE_FCG_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FCG_DC").value=FormatearNumero(TOTAL_HABER);

	}

function Form_COMPROBANTE_GASTO__CambiarOperacionContable(i,Operacion){
	var sw=Form_COMPROBANTE_GASTO__ModificarTabla;
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista==-1)
		sw=true;
	if(sw==false) return;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=Operacion;
	Form_COMPROBANTE_GASTO__MostrarTablaDC();
	}


function Form_MOV_DC__SeleccionarElementoTabla(i){
	if(Form_MOV_DC__iSeleccionActual!=-1)
		xGetElementById("FCG_DC"+Form_MOV_DC__iSeleccionActual).style.background="";
	xGetElementById("FCG_DC"+i).style.background=colorSeleccionTabla;
	Form_MOV_DC__iSeleccionActual=i;
	}

function Form_MOV_DC__ModificarValorCelda(i){
	if(xGetElementById("FCG_DC_txt_celda"))
		return;
	Valor=Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FCG_DC_"+i).innerHTML="<INPUT id='FCG_DC_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DC__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FCG_DC_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DC__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FCG_DC_txt_celda").focus();
	}

function Form_MOV_DC__ModificarValorCeldaPierdeFoco(i){
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]=numberFormat(xGetElementById("FCG_DC_txt_celda").value,2);
	xGetElementById("TD_FCG_DC_"+i).innerHTML=FormatearNumero(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]);
	//recalcular la suma
	var TOTAL_DEBE=0;
	var TOTAL_HABER=0;
	for(var k=0;k<Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;k++){
		if(Form_COMPROBANTE_GASTO__ArregloDetallesContables[k][3]=="D"||Form_COMPROBANTE_GASTO__ArregloDetallesContables[k][3]=="d")
			TOTAL_DEBE+=Form_COMPROBANTE_GASTO__ArregloDetallesContables[k][4]*1.0;
		else
			TOTAL_HABER+=Form_COMPROBANTE_GASTO__ArregloDetallesContables[k][4]*1.0;
		}
	xGetElementById("TOTAL_DEBE_FCG_DC").value=FormatearNumero(TOTAL_DEBE);
	xGetElementById("TOTAL_HABER_FCG_DC").value=FormatearNumero(TOTAL_HABER);
	}

function Form_MOV_DC__KeyPressEnter(event,i){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		if((i+1)>=Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables)
			return;
		xGetElementById("FCG_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia abajo
		Form_MOV_DC__SeleccionarElementoTabla(i+1)
		Form_MOV_DC__ModificarValorCelda(i+1);
		}
	else if(event.keyCode==38){//key up
		if((i-1)<0)
			return;
		xGetElementById("FCG_DC_txt_celda").onblur="";
		Form_MOV_DC__ModificarValorCeldaPierdeFoco(i);
		//busco el siguiente hacia arriba
		Form_MOV_DC__SeleccionarElementoTabla(i-1)
		Form_MOV_DC__ModificarValorCelda(i-1);
		}
	}






function Form_COMPROBANTE_GASTO__AgregarDP() {
	siga.open("detalle_presupuestario",{
		onAdd: function(me){
			//verificar si existe, si existe sumarlo al anterior
			/*
			for(i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;i++)
				if(Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0]==me.internal.data.id_accion_subespecifica &&
					 Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][1]==me.internal.data.id_fuente_recursos &&
					 Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2]==me.internal.data.id_cuenta_presupuestaria &&
					 Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]==me.internal.data.operacion){
					Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]*1+me.internal.data.monto*1;
					Form_COMPROBANTE_GASTO__MostrarTablaDP();
					return;
					}
			
			var i=Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i]=new Array(8);
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0]=me.internal.data.id_accion_subespecifica;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][1]="";
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2]=me.internal.data.id_cuenta_presupuestaria;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][3]=me.internal.data.estructura_presupuestaria;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][4]=me.internal.data.cuenta_presupuestaria;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][5]=me.internal.data.denominacion_presupuestaria;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]=me.internal.data.operacion;
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=me.internal.data.monto;
			Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios++;
			Form_COMPROBANTE_GASTO__MostrarTablaDP();
			*/
			//agregar detalle presupuestario
			Form_COMPROBANTE_GASTO__AgregarItemDP(me.internal.data.id_accion_subespecifica,
																					 me.internal.data.id_cuenta_presupuestaria,
																					 me.internal.data.estructura_presupuestaria,
																					 me.internal.data.cuenta_presupuestaria,
																					 me.internal.data.denominacion_presupuestaria,
																					 me.internal.data.operacion,
																					 me.internal.data.monto);

			//agregar detalle contable
			Form_COMPROBANTE_GASTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						'D',
																						me.internal.data.monto
																						);
		}
	});	
}

function Form_COMPROBANTE_GASTO__AgregarDC() {
	var _id_cuenta_contable=xGetElementById("CUENTA_CONTABLE_PB_FCG").value;
	siga.open("detalle_contable",{
		id_cuenta_contable: _id_cuenta_contable,
		tooltip: 'Cuentas por pagar a proveedores/beneficiarios',
		onAdd: function(me){			
			Form_COMPROBANTE_GASTO__AgregarItemDC(me.internal.data.id_cuenta_contable,
																						me.internal.data.cuenta_contable,
																						me.internal.data.denominacion_contable,
																						me.internal.data.operacion,
																						me.internal.data.monto
																						);
		}
	});
}


function Form_COMPROBANTE_GASTO__AgregarItemDC(id_cuenta_contable, cuenta_contable, denominacion, operacion, monto){
	for(i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;i++)
		if(Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][0]==id_cuenta_contable &&
			 Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]==operacion){
			Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]=Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]*1+monto*1;
			Form_COMPROBANTE_GASTO__MostrarTablaDC();
			return;
			}
	
	var i=Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i]=new Array(5);
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][0]=id_cuenta_contable;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][1]=cuenta_contable;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][2]=denominacion;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][3]=operacion;
	Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][4]=monto;
	Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables++;
	Form_COMPROBANTE_GASTO__MostrarTablaDC();
}


function Form_COMPROBANTE_GASTO__AgregarItemDP(id_accion_subespecifica,id_cuenta_presupuestaria,estructura_presupuestaria,cuenta_presupuestaria,denominacion_presupuestaria,operacion,monto){
	for(i=0;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;i++)
		if(Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0]==id_accion_subespecifica &&
			 Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2]==id_cuenta_presupuestaria &&
			 Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]==operacion){
			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]*1+monto*1;
			Form_COMPROBANTE_GASTO__MostrarTablaDP();
			return;
			}

	var i=Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i]=new Array(8);
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][0]=id_accion_subespecifica;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][2]=id_cuenta_presupuestaria;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][3]=estructura_presupuestaria;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][4]=cuenta_presupuestaria;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][5]=denominacion_presupuestaria;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][6]=operacion;
	Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][7]=monto;
	Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios++;
	Form_COMPROBANTE_GASTO__MostrarTablaDP();
}





function Form_COMPROBANTE_GASTO__DP_Quitar(){
	if(Form_MOV_DP__iSeleccionActual==-1)
		return;

	Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios--;
	for(i=Form_MOV_DP__iSeleccionActual*1;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesPresupuestarios;i++)
 		for(j=0;j<8;j++)
 			Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i][j]= Form_COMPROBANTE_GASTO__ArregloDetallesPresupuestarios[i+1][j];

	Form_COMPROBANTE_GASTO__MostrarTablaDP();
	}

function Form_COMPROBANTE_GASTO__DC_Quitar(){
	if(Form_MOV_DC__iSeleccionActual==-1)
		return;

	Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables--;
	for(i=Form_MOV_DC__iSeleccionActual*1;i<Form_COMPROBANTE_GASTO__TamanoArregloDetallesContables;i++)
 		for(j=0;j<5;j++)
 			Form_COMPROBANTE_GASTO__ArregloDetallesContables[i][j]= Form_COMPROBANTE_GASTO__ArregloDetallesContables[i+1][j];

	Form_COMPROBANTE_GASTO__MostrarTablaDC();
	}

function Form_COMPROBANTE_GASTO__Imprimir(){
	if(Form_COMPROBANTE_GASTO__IDSeleccionActualLista==-1)
		return;
	window.open("../../report/comprobante.php?id="+Form_COMPROBANTE_GASTO__IDSeleccionActualLista);
	}


function Form_COMPROBANTE_GASTO__inputText(){
	Ext.MessageBox.show({
		title: 'Ingresar detalles por txt',
		msg: "<b>Ejemplo para entradas:</b><span style='font-size: 10px;'><br>ACC000001-00-01 401010100 CCP 200.00<br>ACC000002-00-01 401010200 CCP 800.00<br>PRO124900-02-02 403100700 CCP 5000.00<br>611000000000 D 1000<br>613000000000 D 5000.00<br>111010201001 H 6000.00</span>",
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
				if(segmento[0].length==12 && segmento[1].length==1 && (segmento[1]=="D" || segmento[1]=="H") && segmento[2]*1>=0 ) {
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
							Form_COMPROBANTE_GASTO__AgregarItemDC(	segmento[0],
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
									Form_COMPROBANTE_GASTO__AgregarItemDP(	_id_accion_subespecifica,
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