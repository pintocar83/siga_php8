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

var Form_TRASPASOS__ArregloDetallesPresupuestarios=Array();
var Form_TRASPASOS__TamanoArregloDetallesPresupuestarios=0;
var Form_TRASPASOS__ModificarTabla=false;

var Form_TRASPASOS__ArregloTitulosPresupuestarios=Array();

var Form_TRASPASOS__contabilizado="";


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_TRASPASOS__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FT").innerHTML=MSG;
	}



/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_TRASPASOS__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FT_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_TRASPASOS__ActivarFormulario(){
	//xGetElementById("REFERENCIA_FT").readOnly=false;
	xGetElementById("FECHA_FT").readOnly=false;
	xGetElementById("TIPO_FT").readOnly=false;
	xGetElementById("FUENTE_FINANCIERA_FT").readOnly=false;
	xGetElementById("DENOMINACION_FT").readOnly=false;
	
	xGetElementById("TIPO_FT").disabled=false;
	xGetElementById("FUENTE_FINANCIERA_FT").disabled=false;


	//xGetElementById("REFERENCIA_FT").setAttribute('class','TextoCampoInput');
	xGetElementById("FECHA_FT").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("TIPO_FT").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("FUENTE_FINANCIERA_FT").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DENOMINACION_FT").setAttribute('class','TextoCampoInputObligatorios');

	xGetElementById("IMG_FECHA_FT").setAttribute('onclick',"showCalendar('FECHA_FT','%d/%m/%Y')");
	xGetElementById("FECHA_FT").setAttribute('ondblclick',"showCalendar('FECHA_FT','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FT","IMG_FECHA_FT",'calendario');

	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_TRASPASOS__DesactivarFormulario(){
	//xGetElementById("REFERENCIA_FT").readOnly=true;
	xGetElementById("FECHA_FT").readOnly=true;
	xGetElementById("TIPO_FT").readOnly=true;
	xGetElementById("FUENTE_FINANCIERA_FT").readOnly=true;
	xGetElementById("DENOMINACION_FT").readOnly=true;
	
	xGetElementById("TIPO_FT").disabled=true;
	xGetElementById("FUENTE_FINANCIERA_FT").disabled=true;


	//xGetElementById("REFERENCIA_FT").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_FT").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TIPO_FT").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FUENTE_FINANCIERA_FT").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DENOMINACION_FT").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_FECHA_FT").setAttribute('onclick',"");
	xGetElementById("FECHA_FT").setAttribute('ondblclick',"");

	DesactivarBoton("IMG_FECHA_FT","IMG_FECHA_FT",'calendario');
	}

/**
* Activa el boton modificar
*/
function Form_TRASPASOS__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FT","IMG_MODIFICAR_FT",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_TRASPASOS__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FT","IMG_MODIFICAR_FT",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_TRASPASOS__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FT","IMG_GUARDAR_FT",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_TRASPASOS__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FT","IMG_GUARDAR_FT",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_TRASPASOS__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FT","IMG_ELIMINAR_FT",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_TRASPASOS__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FT","IMG_ELIMINAR_FT",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_TRASPASOS__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_TRASPASOS__IDTipoOperacionSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_TRASPASOS__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_TRASPASOS__BuscarListado_CadenaBuscar="";
var Form_TRASPASOS__SW_PERSONA="";


var Form_TRASPASOS__Tipo_PC=SIGA.Ext.decode(SIGA.Ext.Ajax.request({async: false, url: "module/comprobante_tipo/", params:{action:'onGet',tipo:'TR'}}).responseText)[0][0];


/**
* Nueva definicion
*/
function Form_TRASPASOS__Nuevo(){	
	AjaxRequest.post({
						'parameters':{
									'action':"onGet_Correlativo",
									'tipo':"TR"									
									},
						'onSuccess': function(req){
											var respuesta = req.responseText;
											var resultado = eval("(" + respuesta + ")");
											xGetElementById("ACRONIMO_FT").value="TR";
											xGetElementById("COMPROBANTE_FT").value=completarCodigoCeros(String(resultado[0]["correlativo"]),10);
											xGetElementById("COMPROBANTE_TIPO_FT").innerHTML=Form_TRASPASOS__Tipo_PC;
											},
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	
	Form_TRASPASOS__LimpiarInputTextBuscarListado();
	Form_TRASPASOS__CargarSelectFF();
	
	Form_TRASPASOS__TabPane.setSelectedIndex(0);
	Form_TRASPASOS__TabPaneSUBTAB.setSelectedIndex(0);
	}


var Form_TRASPASOS__IDSeleccionActualListaFuenteFinanciera=-2;
function Form_TRASPASOS__CargarSelectFF(){
	AjaxRequest.post({
						'parameters':{
								'action':"onList",
								'text':'',
								'start':'0',
								'limit': 'ALL',
								'sort':'[{"property":"codigo_denominacion_fuente","direction":"ASC"}]'				
								},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								CargarSELECT(resultado["result"],"FUENTE_FINANCIERA_FT",Form_TRASPASOS__IDSeleccionActualListaFuenteFinanciera,"id","codigo_denominacion_fuente");
								},
						'url':'../fuente_recursos/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}


/*Verifica que los campos obligatorios esten llenos y la existencia (duplicidad) antes de guardar*/
function Form_TRASPASOS__Guardar(){
	/*Se verifica que los campos obligatorio esten llenos*/
	Form_TRASPASOS__TabPane.setSelectedIndex(0);
	var _tipo		= xTrim(strtoupper(xGetElementById("ACRONIMO_FT").value));
	//var NumeroRef		= xTrim(strtoupper(xGetElementById("REFERENCIA_FT").value));
	var _fecha			= xTrim(strtoupper(xGetElementById("FECHA_FT").value));
	var _denominacion	= xTrim(strtoupper(xGetElementById("DENOMINACION_FT").value));
	var _id_persona			= "";
	var _tipo_gasto = xTrim(strtoupper(xGetElementById("TIPO_FT").value));
	var _fuente_recursos = xTrim(strtoupper(xGetElementById("FUENTE_FINANCIERA_FT").value));


	if(!_fecha){
		Form_TRASPASOS__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_TRASPASOS__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_TRASPASOS__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_TRASPASOS__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	if(!_denominacion){
		Form_TRASPASOS__Mensaje("Por favor introduzca la denominación.","ROJO");
		Form_TRASPASOS__MensajeListado("");
		return;
		}
	if(xGetElementById("TOTAL_COMPROMISOS_FT_AU").value!=xGetElementById("TOTAL_COMPROMISOS_FT_DI").value){
		Form_TRASPASOS__Mensaje("El total de aumentos no coincide con el total de disminuciones.","ROJO");
		Form_TRASPASOS__MensajeListado("");
		return;
		}


	Form_TRASPASOS__DesactivarFormulario();

	var _detalle={};
	_detalle.presupuestario=[];
	for(i=0;i<Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;i++){
		_detalle.presupuestario[i]={
			id_accion_subespecifica: Form_TRASPASOS__ArregloDetallesPresupuestarios[i][0],
			id_cuenta_presupuestaria: Form_TRASPASOS__ArregloDetallesPresupuestarios[i][2],
			operacion: Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6],
			monto: Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]
		};
	}
	
	_detalle.extra={
		tipo_gasto: _tipo_gasto,
		fuente_recursos: _fuente_recursos
	};
	
	
	var _id_comprobante="";
	if(Form_TRASPASOS__IDSeleccionActualLista>0) 
		_id_comprobante=Form_TRASPASOS__IDSeleccionActualLista;
	

	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_TRASPASOS__ActivarFormulario();
			return;
			}
		}
	
	if(Form_TRASPASOS__contabilizado=="")
		Form_TRASPASOS__contabilizado="t";
	
	AjaxRequest.post(
						{'parameters':{
										'action':"onSave",
										'id': _id_comprobante,
										'tipo':_tipo,
										'fecha':_fecha,
										'concepto':_denominacion,
										'contabilizado': Form_TRASPASOS__contabilizado,
										'id_persona': _id_persona,										
										'detalle': SIGA.Ext.encode(_detalle)
										},
						'onSuccess':Form_TRASPASOS__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		
		
	}




/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_TRASPASOS__GuardarMensaje(req){
	//console.log(req);
	
	Form_TRASPASOS__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_TRASPASOS__Nuevo();
		Form_TRASPASOS__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_TRASPASOS__Mensaje(respuesta.message,"ROJO");		
	}


/*Al presionar enter buscamos directamente en el listado*/
function Form_TRASPASOS__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FT").checked){
 		if(ev.keyCode==13)
			Form_TRASPASOS__BuscarListado();
		return;
		}
	Form_TRASPASOS__BuscarListado();
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_TRASPASOS__BuscarListado(){
	Form_TRASPASOS__contabilizado="";
	Form_TRASPASOS__IDSeleccionActualLista=-1;
	Form_TRASPASOS__IDTipoOperacionSeleccionActualLista=-1;
	//xGetElementById("FORMULARIO_FT").reset();
	Form_TRASPASOS__ActivarFormulario();
	Form_TRASPASOS__DesactivarBotonModificar();
	Form_TRASPASOS__DesactivarBotonEliminar();
	Form_TRASPASOS__ActivarBotonGuardar();

	//xGetElementById("ACRONIMO_FT").value="PC";

	Form_TRASPASOS__ModificarTabla=false;
	Form_TRASPASOS__MostrarTablaDP();




	var CadenaBuscar=xGetElementById("LISTADO_BUSCAR_FT").value;
	if(CadenaBuscar!="")
		if(Form_TRASPASOS__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_TRASPASOS__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(Form_TRASPASOS__BuscarListado_CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FT").innerHTML=IconoCargandoTabla;
	else{//busco el n del documento y lo coloco en el input text numero

		}

	var MostraContabilizado=0;
	if(xGetElementById("MOSTRA_CONTABILIZADO_FT"))
		if(xGetElementById("MOSTRA_CONTABILIZADO_FT").checked)
			MostraContabilizado=1;
	
	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_FT").value,
		'tipo':["TR"],
		'contabilizado':MostraContabilizado
	};
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"},{"property":"tipo","direction":"ASC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_TRASPASOS__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}
	

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_TRASPASOS__MostrarListado(req){
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
	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FT").value));

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_TRASPASOS__SeleccionarElementoTabla('"+resultado[i]['id']+"')";
		FuncionOnDblclick="Form_TRASPASOS__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FCG"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FCG"+resultado[i]['id']+"\")";


		if(xGetElementById("SOMBRA_CHECKBOX_FT").checked && TextoBuscar!=""){
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

		Contenido+="<TD width='3%' class='FilaEstilo' align='left'>"+CadAux1+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		//Contenido+="<TD width='67%' class='FilaEstilo'><DIV style='overflow : hidden; width:98%'><UL style='list-style-type : none; margin-bottom : 0; margin-left : 0; margin-right : 0%; margin-top : 0; padding-bottom : 0px; padding-left : 0px; padding-right : 0; padding-top : 0; text-align : left; white-space : nowrap; font-size : 12px;'><li>"+resultado[i]['denominacion_c']+"</li></UL><DIV></TD>";

		Contenido+="<TD class='FilaEstilo'>"+CadAux4+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FT").innerHTML=Contenido;
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
function Form_TRASPASOS__SeleccionarElementoTabla(IDSeleccion){
	if(Form_TRASPASOS__IDSeleccionActualLista!=-1)
		xGetElementById("FCG"+Form_TRASPASOS__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FCG"+IDSeleccion).bgColor=colorBase;
	
	//if(Form_TRASPASOS__IDSeleccionActualLista==IDSeleccion)
	//	return;
	
	Form_TRASPASOS__IDSeleccionActualLista=IDSeleccion;
	
	Form_TRASPASOS__DesactivarFormulario();
	Form_TRASPASOS__ActivarBotonModificar();
	Form_TRASPASOS__ActivarBotonEliminar();
	Form_TRASPASOS__DesactivarBotonGuardar();
	Form_TRASPASOS__Mensaje("");
	Form_TRASPASOS__MensajeListado("");
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_TRASPASOS__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								

							
								xGetElementById("FECHA_FT").value=resultado[0]["fecha"];
								xGetElementById("COMPROBANTE_FT").value=resultado[0]["correlativo"];
								xGetElementById("ACRONIMO_FT").value=resultado[0]["tipo"];
								xGetElementById("DENOMINACION_FT").value=resultado[0]["concepto"];
								xGetElementById("COMPROBANTE_TIPO_FT").innerHTML=resultado[0]["denominacion_tipo"];
								Form_TRASPASOS__contabilizado=resultado[0]["contabilizado"];
								
									
								
								//cargar detalle presupuestarios								
								if(resultado[0]["detalle_presupuestario"]){
									var n=resultado[0]["detalle_presupuestario"].length;
									Form_TRASPASOS__TamanoArregloDetallesPresupuestarios=n
									for(var i=0;i<n;i++){
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i]=new Array(8);
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][0]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][2]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][3]=resultado[0]["detalle_presupuestario"][i]['estructura_presupuestaria'];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][4]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][5]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]=resultado[0]["detalle_presupuestario"][i]["operacion"];
										Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]=resultado[0]["detalle_presupuestario"][i]["monto"];
										}
									Form_TRASPASOS__MostrarTablaDP();
									}
								//cargar informacion extra
								if(resultado[0]["detalle_extra"]){
									for(var i=0;i<resultado[0]["detalle_extra"].length;i++){
										if(resultado[0]["detalle_extra"][i]["dato"]=="tipo_gasto")
											xGetElementById("TIPO_FT").value=resultado[0]["detalle_extra"][i]["valor"];
										else if(resultado[0]["detalle_extra"][i]["dato"]=="fuente_recursos")
											xGetElementById("FUENTE_FINANCIERA_FT").value=resultado[0]["detalle_extra"][i]["valor"];
										}
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
function Form_TRASPASOS__LimpiarInputTextBuscarListado(){
	Form_TRASPASOS__ArregloTitulosPresupuestarios=new Array();
	Form_TRASPASOS__ModificarTabla=false;
	Form_TRASPASOS__IDComprobante="";
	Form_TRASPASOS__TamanoArregloDetallesPresupuestarios=0;
	Form_TRASPASOS__TamanoArregloDetallesContables=0;
	xGetElementById("TABLA_LISTA_ARTICULOS_FT_AU").innerHTML="";
	xGetElementById("TABLA_LISTA_ARTICULOS_FT_DI").innerHTML="";

	Form_TRASPASOS__IDBancoSeleccionActualLista=-1;
	Form_TRASPASOS__IDTipoOperacionSeleccionActualLista=-1;
	Form_TRASPASOS__IDSeleccionActualListaFuenteFinanciera=-2;
	Form_TRASPASOS__IDSeleccionActualLista=-1;
	Form_TRASPASOS__Denominacion="";
	Form_TRASPASOS__DesactivarBotonModificar();
	Form_TRASPASOS__DesactivarBotonEliminar();
	Form_TRASPASOS__ActivarBotonGuardar();
	Form_TRASPASOS__ActivarFormulario();
	xGetElementById("FORMULARIO_FT").reset();
	xGetElementById("LISTADO_BUSCAR_FT").value="";
	Form_TRASPASOS__Mensaje("");
	Form_TRASPASOS__MensajeListado("");
	Form_TRASPASOS__BuscarListado();

	
	//DarFocoCampo("LISTADO_BUSCAR_FT",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_TRASPASOS__Modificar(){
	Form_TRASPASOS__ModificarTabla=true;
	Form_TRASPASOS__MostrarTablaDP();	
	Form_TRASPASOS__ActivarFormulario();
	Form_TRASPASOS__ActivarBotonGuardar();
	Form_TRASPASOS__DesactivarBotonModificar();
	Form_TRASPASOS__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_TRASPASOS__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	var _id_comprobante="";
	if(Form_TRASPASOS__IDSeleccionActualLista>0) 
		_id_comprobante=Form_TRASPASOS__IDSeleccionActualLista;
	
	if(_id_comprobante=="")
		return;
	
	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_comprobante},
				'onSuccess':Form_TRASPASOS__EliminarMensaje,
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_TRASPASOS__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_TRASPASOS__LimpiarInputTextBuscarListado();
		Form_TRASPASOS__Mensaje(respuesta.message,"VERDE");
		Form_TRASPASOS__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_TRASPASOS__Mensaje(respuesta.message,"ROJO");
		Form_TRASPASOS__MensajeListado(respuesta.message,"ROJO");
		}
	}


function Form_TRASPASOS__MostrarTablaDP(){
	Form_MOV_AU__iSeleccionActual=-1;
	var Contenido="";
	var ContenidoAU="";
	var ContenidoDI="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnDblclickMONTO="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL_AU=0;
	var TOTAL_DI=0;

	var sw;
	sw=Form_TRASPASOS__ModificarTabla;
	if(Form_TRASPASOS__IDSeleccionActualLista==-1)
		sw=true;

	if(sw){
		ActivarBoton("BOTON_AGREGAR_FT_AU","IMG_AGREGAR_FT_AU",'agregar');
		ActivarBoton("BOTON_QUITAR_FT_AU","IMG_QUITAR_FT_AU",'quitar');
		ActivarBoton("BOTON_AGREGAR_FT_DI","IMG_AGREGAR_FT_DI",'agregar');
		ActivarBoton("BOTON_QUITAR_FT_DI","IMG_QUITAR_FT_DI",'quitar');
		}
	else{
		DesactivarBoton("BOTON_AGREGAR_FT_AU","IMG_AGREGAR_FT_AU",'agregar');
		DesactivarBoton("BOTON_QUITAR_FT_AU","IMG_QUITAR_FT_AU",'quitar');
		DesactivarBoton("BOTON_AGREGAR_FT_DI","IMG_AGREGAR_FT_DI",'agregar');
		DesactivarBoton("BOTON_QUITAR_FT_DI","IMG_QUITAR_FT_DI",'quitar');
		}

	for(var i=0;i<Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;i++){
		if(sw){
 			FuncionOnclick="Form_MOV_DP__SeleccionarElementoTabla('"+i+"')";
			FuncionOnDblclickMONTO="Form_MOV_DP__ModificarValorCelda("+i+")";
			}
		
		
		Contenido="";
		Contenido+="<TR class='FilaListado' id='FT_DP"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		Contenido+="<TD width='15%'  style='font-size : 11px;'>"+Form_TRASPASOS__ArregloDetallesPresupuestarios[i][3]+"</TD>";
		Contenido+="<TD width='10%' align='center' style='font-size : 11px;'>"+Form_TRASPASOS__ArregloDetallesPresupuestarios[i][4]+"</TD>";
		Contenido+="<TD class='CeldaContinua'>"+Form_TRASPASOS__ArregloDetallesPresupuestarios[i][5]+"</TD>";
		Contenido+="<TD width='7%'  align='center' style='font-size : 11px;'>"+Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]+"</TD>";
		Contenido+="<TD width='10%' align='right'  style='font-size : 11px;' id='TD_FT_DP_"+i+"' ondblclick='"+FuncionOnDblclickMONTO+"'>"+FormatearNumero(Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7])+"</TD>";
		Contenido+="</TR>";
		
		if(Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]=="AU") {
			ContenidoAU+=Contenido;
			TOTAL_AU+=Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]*1.0;
		}
		else if(Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]=="DI") {
			ContenidoDI+=Contenido;
			TOTAL_DI+=Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]*1.0;
		}
		
		}
	xGetElementById("TABLA_LISTA_ARTICULOS_FT_AU").innerHTML=ContenidoAU;
	xGetElementById("TABLA_LISTA_ARTICULOS_FT_DI").innerHTML=ContenidoDI;
	xGetElementById("TOTAL_COMPROMISOS_FT_AU").value=FormatearNumero(TOTAL_AU);
	xGetElementById("TOTAL_COMPROMISOS_FT_DI").value=FormatearNumero(TOTAL_DI);
	
}


function Form_MOV_DP__SeleccionarElementoTabla(i){
	if(Form_MOV_AU__iSeleccionActual!=-1)
		xGetElementById("FT_DP"+Form_MOV_AU__iSeleccionActual).style.background="";
	xGetElementById("FT_DP"+i).style.background=colorSeleccionTabla;
	Form_MOV_AU__iSeleccionActual=i;
	}

function Form_MOV_DP__ModificarValorCelda(i){
	if(xGetElementById("FT_DP_txt_celda"))
		return;
	Valor=Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TD_FT_DP_"+i).innerHTML="<INPUT id='FT_DP_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_MOV_DP__ModificarValorCeldaPierdeFoco("+i+")\" onkeypress=\"return AcceptNum(event,'FT_DP_txt_celda');\" style='text-align : right;' onkeyup='Form_MOV_DP__KeyPressEnter(event,"+i+")';>";
	xGetElementById("FT_DP_txt_celda").focus();
	}
	
function Form_MOV_DP__ModificarValorCeldaPierdeFoco(i){
	Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]=numberFormat(xGetElementById("FT_DP_txt_celda").value,2);
	xGetElementById("TD_FT_DP_"+i).innerHTML=FormatearNumero(Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]);
	//recalcular la suma
	var TOTAL_DI=0;
	var TOTAL_AU=0;
	for(var k=0;k<Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;k++){
		if(Form_TRASPASOS__ArregloDetallesPresupuestarios[k][6]=="AU")
			TOTAL_AU+=Form_TRASPASOS__ArregloDetallesPresupuestarios[k][7]*1.0;
		else if(Form_TRASPASOS__ArregloDetallesPresupuestarios[k][6]=="DI")
			TOTAL_DI+=Form_TRASPASOS__ArregloDetallesPresupuestarios[k][7]*1.0;
	}
	xGetElementById("TOTAL_COMPROMISOS_FT_AU").value=FormatearNumero(TOTAL_AU);
	xGetElementById("TOTAL_COMPROMISOS_FT_DI").value=FormatearNumero(TOTAL_DI);
	}
	
function Form_MOV_DP__KeyPressEnter(event,i){
	if(event.keyCode==13){//si es enter
		xGetElementById("FT_DP_txt_celda").onblur="";
		Form_MOV_DP__ModificarValorCeldaPierdeFoco(i);
		}
	}
	

function Form_TRASPASOS__AgregarAU(){
	Form_TRASPASOS__AgregarDP(["AU","DI"]);
}

function Form_TRASPASOS__AgregarDI(){
	Form_TRASPASOS__AgregarDP(["DI","AU"]);
}

function Form_TRASPASOS__AgregarDP(_operacion) {
	SIGA.siga.open("detalle_presupuestario",{
		operacion: _operacion,
		onAdd: function(me){
			if(!(me.internal.data.operacion=="AU" || me.internal.data.operacion=="DI")){
				me.setMessage("Error. Solo se permite el tipo de operación AU o DI.","red");
				return;
			}
			
			//verificar si existe, si existe sumarlo al anterior
			for(i=0;i<Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;i++)
				if(Form_TRASPASOS__ArregloDetallesPresupuestarios[i][0]==me.internal.data.id_accion_subespecifica &&
					 Form_TRASPASOS__ArregloDetallesPresupuestarios[i][2]==me.internal.data.id_cuenta_presupuestaria &&
					 Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]==me.internal.data.operacion){
					Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]=Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]*1+me.internal.data.monto*1;
					Form_TRASPASOS__MostrarTablaDP();
					return;
					}
			
			var i=Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i]=new Array(8);
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][0]=me.internal.data.id_accion_subespecifica;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][2]=me.internal.data.id_cuenta_presupuestaria;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][3]=me.internal.data.estructura_presupuestaria;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][4]=me.internal.data.cuenta_presupuestaria;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][5]=me.internal.data.denominacion_presupuestaria;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][6]=me.internal.data.operacion;
			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][7]=me.internal.data.monto;
			Form_TRASPASOS__TamanoArregloDetallesPresupuestarios++;
			Form_TRASPASOS__MostrarTablaDP();
		}
	});	
}





function Form_TRASPASOS__DP_Quitar(){
	if(Form_MOV_AU__iSeleccionActual==-1)
		return;

	Form_TRASPASOS__TamanoArregloDetallesPresupuestarios--;
	for(i=Form_MOV_AU__iSeleccionActual*1;i<Form_TRASPASOS__TamanoArregloDetallesPresupuestarios;i++)
 		for(j=0;j<8;j++)
 			Form_TRASPASOS__ArregloDetallesPresupuestarios[i][j]= Form_TRASPASOS__ArregloDetallesPresupuestarios[i+1][j];

	Form_TRASPASOS__MostrarTablaDP();
	}


function Form_TRASPASOS__Imprimir(){
	if(Form_TRASPASOS__IDSeleccionActualLista==-1)
		return;
	window.open("../../report/modificacion_a.php?id="+Form_TRASPASOS__IDSeleccionActualLista);
	}

