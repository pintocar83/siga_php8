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
* @version 20090923
*/


/**
* Indica el id_banco que se tiene seleccionado actualmente en el listado de bancos. Necesario para eliminar y para modificar
*/
var Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista=-1;
var Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo=-1;
var Form_CHEQUE_ORDEN_PAGO__Arreglo=new Array();
var Form_CHEQUE_ORDEN_PAGO__TamArreglo=0;
var Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="";
var Form_CHEQUE_ORDEN_PAGO__IDComprobante="";


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_CHEQUE_ORDEN_PAGO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCOP").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_CHEQUE_ORDEN_PAGO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCOP_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
* @param {integer} sw Permite activar parte del formulario, p.j. dado el caso de modificar un cheque no modificar a quien va dirigido.
*/
function Form_CHEQUE_ORDEN_PAGO__ActivarFormulario(sw){

	if(!sw){
		sw=1;
		xGetElementById("BOTON_PROVEEDOR_FCOP").disabled=false;
		xGetElementById("BOTON_BENEFICIARIO_FCOP").disabled=false;
		ActivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP",'buscar');
		}
	else{
		xGetElementById("BOTON_PROVEEDOR_FCOP").disabled=true;
		xGetElementById("BOTON_BENEFICIARIO_FCOP").disabled=true;
		DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP",'buscar');
		}

	xGetElementById("INCLUSION_RETENCION_FCOP").disabled=false;

	if(sw==1){//activar todo
		xGetElementById("IMG_BUSCAR_NCTA_FCOP").setAttribute('onclick',"Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_FCOP','NCTA_FCOP','DESCRIPCION_NCTA_FCOP','TIPO_CTA_FCOP','BANCO_FCOP','CTA_CODIGO_CONTABLE_FCOP','CUENTA_CONTABLE_FCOP','CTA_DENOMINACION_CONTABLE_FCOP','Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles()');");
		ActivarBoton("IMG_BUSCAR_NCTA_FCOP","IMG_BUSCAR_NCTA_FCOP",'buscar');
		}
	else{//no activar Buscar Banco
		xGetElementById("IMG_BUSCAR_NCTA_FCOP").setAttribute('onclick',"");
		DesactivarBoton("IMG_BUSCAR_NCTA_FCOP","IMG_BUSCAR_NCTA_FCOP",'buscar');
		}


	xGetElementById("FECHA_FCOP").readOnly=false;
	xGetElementById("N_CHEQUE_FCOP").readOnly=false;
	xGetElementById("CONCEPTO_FCOP").readOnly=false;


	xGetElementById("FECHA_FCOP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("INCLUSION_RETENCION_FCOP").setAttribute('class','TextoCampoInput');
	xGetElementById("N_CHEQUE_FCOP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CONCEPTO_FCOP").setAttribute('class','TextoCampoInputObligatorios');

	xGetElementById("BOTON_PROVEEDOR_FCOP").setAttribute('onclick',"Form_CHEQUE_ORDEN_PAGO__BotonProveedor();");
	xGetElementById("BOTON_BENEFICIARIO_FCOP").setAttribute('onclick',"Form_CHEQUE_ORDEN_PAGO__BotonBeneficiario();");

	//xGetElementById("BOTON_PROVEEDOR_FCOP").setAttribute('ondblclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");
	//xGetElementById("BOTON_BENEFICIARIO_FCOP").setAttribute( 'ondblclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");

	xGetElementById("IMG_FECHA_FCOP").setAttribute('onclick',"showCalendar('FECHA_FCOP','%d/%m/%Y')");
	xGetElementById("FECHA_FCOP").setAttribute('ondblclick',"showCalendar('FECHA_FCOP','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FCOP","IMG_FECHA_FCOP",'calendario');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_CHEQUE_ORDEN_PAGO__DesactivarFormulario(){
	xGetElementById("BOTON_PROVEEDOR_FCOP").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_FCOP").disabled=true;
	
	xGetElementById("INCLUSION_RETENCION_FCOP").disabled=true;

	xGetElementById("FECHA_FCOP").readOnly=true;
	xGetElementById("N_CHEQUE_FCOP").readOnly=true;
	xGetElementById("CONCEPTO_FCOP").readOnly=true;


	xGetElementById("FECHA_FCOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("INCLUSION_RETENCION_FCOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("N_CHEQUE_FCOP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CONCEPTO_FCOP").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_BUSCAR_NCTA_FCOP").setAttribute('onclick',"");
	xGetElementById("BOTON_PROVEEDOR_FCOP").setAttribute('ondblclick',"");
	xGetElementById("BOTON_BENEFICIARIO_FCOP").setAttribute( 'ondblclick',"");

	xGetElementById("IMG_FECHA_FCOP").setAttribute('onclick',"");
	xGetElementById("FECHA_FCOP").setAttribute('ondblclick',"");

	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP").setAttribute('onclick',"");

	DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP",'buscar');
	DesactivarBoton("IMG_BUSCAR_NCTA_FCOP","IMG_BUSCAR_NCTA_FCOP",'buscar');
	DesactivarBoton("IMG_FECHA_FCOP","IMG_FECHA_FCOP",'calendario');

	xGetElementById("CHECK_FCOP").disabled=true;
	}

/**
* Activa el boton modificar
*/
function Form_CHEQUE_ORDEN_PAGO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FCOP","IMG_MODIFICAR",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_CHEQUE_ORDEN_PAGO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FCOP","IMG_MODIFICAR",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_CHEQUE_ORDEN_PAGO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FCOP","IMG_GUARDAR",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_CHEQUE_ORDEN_PAGO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FCOP","IMG_GUARDAR",'guardar');
	}

/**
* Activa el boton guardar
*/
function Form_CHEQUE_ORDEN_PAGO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FCOP","IMG_ELIMINAR",'eliminar');
	}

/**
* Desactiva el boton guardar
*/
function Form_CHEQUE_ORDEN_PAGO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FCOP","IMG_ELIMINAR",'eliminar');
	}

/**
* Activa el boton imprimir
*/
function Form_CHEQUE_ORDEN_PAGO__ActivarBotonImprimir(){
	ActivarBoton("BOTON_IMPRIMIR_FCOP","IMG_IMPRIMIR_FCOP",'visualizar');
	}

/**
* Desactiva el boton imprimir
*/
function Form_CHEQUE_ORDEN_PAGO__DesactivarBotonImprimir(){
	DesactivarBoton("BOTON_IMPRIMIR_FCOP","IMG_IMPRIMIR_FCOP",'visualizar');
	}

/**
* Se llena cuando se selecciona un banco del listado. Esta sirve para saber si el usuario a modificado el nombre del banco. Es usada en Guardar
*/
var Form_CHEQUE_ORDEN_PAGO__NombreBanco="";


/**
* Nueva definicion
*/
function Form_CHEQUE_ORDEN_PAGO__Nuevo(){
	if(Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="B")
		Form_CHEQUE_ORDEN_PAGO__BotonBeneficiario();
	else
		Form_CHEQUE_ORDEN_PAGO__BotonProveedor();
	Form_CHEQUE_ORDEN_PAGO__TabPane.setSelectedIndex(0);
	//Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
	}

/**
* Carga las solicitudes pendientes/programadas a un proveedor/beneficiario escogido
*/
function Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes(){
	if(xGetElementById("ID_BoP_FCOP").value=="")
		return;

	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML=IconoCargandoTabla;

	
	var _id_comprobante="";
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista;
	
	var _mostrar={
		'id_persona': xGetElementById("ID_BoP_FCOP").value,
		'id': _id_comprobante
	};

	AjaxRequest.post({
					'parameters':{
									'action':"onList_OP_pendiente",
									'mostrar': Ext.encode(_mostrar),
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"ASC"}]'									
									},
					'onSuccess':Form_CHEQUE_ORDEN_PAGO__PostCargarSolicitudes,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	
	
	/*
	AjaxRequest.post({'parameters':{ 'accion':"Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes",
									'sw_persona':Form_CHEQUE_ORDEN_PAGO__SW_PERSONA,
									'id_pb':xGetElementById("ID_BoP_FCOP").value},
					 'onSuccess':Form_CHEQUE_ORDEN_PAGO__PostCargarSolicitudes,
					 'url':'../modulo_banco/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });*/
}

/**
* Carga los datos de la solicitudes en el arreglo Form_CHEQUE_ORDEN_PAGO__Arreglo.
* @param {Array} req Datos provenientes de la BD
*/
function Form_CHEQUE_ORDEN_PAGO__PostCargarSolicitudes(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	console.log(resultado);
	//return;
	
	var n=resultado.length;
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=n;
	for(var i=0;i<n;i++){
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i]=[];
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=false;//check
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]=resultado[i]['id'];
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][8]=resultado[i]['correlativo'];
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][2]=resultado[i]['fecha'];
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][3]=numberFormat(resultado[i]['monto'],2);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][4]=strtoupper(resultado[i]['concepto']);
		//Form_CHEQUE_ORDEN_PAGO__Arreglo[i][5]=resultado[i]['id_cta_bancaria']+"&%?~"+resultado[i]['numero_cta_bancaria']+"&%?~"+resultado[i]['nombre_tipo_cta_bancaria']+"&%?~"+resultado[i]['nombre_banco']+" ("+resultado[i]['direccion_banco']+")&%?~"+resultado[i]['id_codigo_contable'];
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][5]="";
		if(!resultado[i]['monto_pagado_acumulado'] || resultado[i]['monto_pagado_acumulado']=="null" || resultado[i]['monto_pagado_acumulado']=="")
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]="0.00";
		else
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]=numberFormat(resultado[i]['monto_pagado_acumulado'],2);
		if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")	
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]="0.00";
		else{
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=true;
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]=numberFormat(resultado[i]['monto_pagado'],2);
			
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]*1.0-Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]*1.0;
			Form_CHEQUE_ORDEN_PAGO__onGetDetallesOP(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]);
			}
		}
	Form_CHEQUE_ORDEN_PAGO__TipoModificar=1;
	Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	}


/**
* Carga los datos de la solicitudes en el arreglo Form_CHEQUE_ORDEN_PAGO__Arreglo.
* @param {Array} req Datos provenientes de la BD
*/
function Form_CHEQUE_ORDEN_PAGO__PostCargarSolicitudesModificar(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	var n=resultado.length;
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=n;
	for(var i=0;i<n;i++){
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i]=new Array(8);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=false;//check
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]=strtoupper(resultado[i]['id_solicitud_pago']);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][2]=FormatearFecha(resultado[i]['fecha_programar_pg']);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][3]=numberFormat(resultado[i]['monto_sp'],2);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][4]=strtoupper(resultado[i]['concepto_sp']);
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][5]=resultado[i]['id_cta_bancaria']+"&%?~"+resultado[i]['numero_cta_bancaria']+"&%?~"+resultado[i]['nombre_tipo_cta_bancaria']+"&%?~"+resultado[i]['nombre_banco']+" ("+resultado[i]['direccion_banco']+")&%?~"+resultado[i]['id_codigo_contable'];
		if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]="0.00";
		else
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]=numberFormat(resultado[i]['monto_pagado']-resultado[i]['monto_pagar'],2);

		if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]="0.00"
		else
			Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]=numberFormat(resultado[i]['monto_pagar'],2);//monto introducido por teclado

		}
	Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	}



/**
* Permite trabajar el cheque en base a los proveedores
*/
function Form_CHEQUE_ORDEN_PAGO__BotonProveedor(){
	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML="";
	xGetElementById("TITULO_PB_FCOP_LISTADO").innerHTML="PROVEEDOR";
	xGetElementById("TIPO_PERSONA_FCOP").innerHTML="Proveedor";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");
	xGetElementById("ID_BoP_FCOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("MONTO_FCOP").value="0,00";
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=0;
	if(Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="B"){
		Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="P";
		Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();
		Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
		return;
		}
	else
		Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="P";
	Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
	}

function Form_CHEQUE_ORDEN_PAGO__BotonProveedor2(){
	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML="";
	xGetElementById("TITULO_PB_FCOP_LISTADO").innerHTML="PROVEEDOR";
	xGetElementById("TIPO_PERSONA_FCOP").innerHTML="Proveedor";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");
	xGetElementById("ID_BoP_FCOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("MONTO_FCOP").value="0,00";
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=0;
	if(Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="B"){
		Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="P";
		Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();
		return;
		}
	else
		Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="P";
	}

/**
* Permite trabajar el cheque en base a los beneficiarios
*/
function Form_CHEQUE_ORDEN_PAGO__BotonBeneficiario(){
	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML="";
	//Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
	xGetElementById("TITULO_PB_FCOP_LISTADO").innerHTML="BENEFICIARIO";
	xGetElementById("TIPO_PERSONA_FCOP").innerHTML="Beneficiario";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");
	xGetElementById("ID_BoP_FCOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("MONTO_FCOP").value="0,00";
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=0;
	if(Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="P"){
		Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="B";
		Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();
		Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
		return;
		}
	else
		Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="B";
	Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
	}

function Form_CHEQUE_ORDEN_PAGO__BotonBeneficiario2(){
	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML="";
	xGetElementById("TITULO_PB_FCOP_LISTADO").innerHTML="BENEFICIARIO";
	xGetElementById("TIPO_PERSONA_FCOP").innerHTML="Beneficiario";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FCOP").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FCOP','ID_BENEFICIARIO_PROVEEDOR_FCOP','NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP','Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();','','BNCCHE_1')");

	xGetElementById("ID_BoP_FCOP").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP").value="";
	xGetElementById("MONTO_FCOP").value="0,00";
	Form_CHEQUE_ORDEN_PAGO__TamArreglo=0;
	if(Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="P"){
		Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="B";
		Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();
		return;
		}
	else
		Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
	Form_CHEQUE_ORDEN_PAGO__SW_PERSONA="B";
	}

/**
* Muestra las solicitudes en la tabla.
*/
function Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes(){
	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML=IconoCargandoTabla;
	Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnclickMonto="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4, CadAux5, CadAux6, CadAux7;

	for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__TamArreglo;i++){
		FuncionOnclick="";
		FuncionOnclickMonto="";
		FuncionOnDblclickMonto="";
		if(Form_CHEQUE_ORDEN_PAGO__TipoModificar!=-1){
			FuncionOnclick="Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudes("+i+")";
			FuncionOnclickMonto="Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudesMonto("+i+")";
			FuncionOnDblclickMonto="Form_CHEQUE_ORDEN_PAGO__ModificarValorCelda('FCOP_S_MONTO_"+i+"');";
			}
		FuncionOnMouseOver="pintarFila(\"FCOP_S"+i+"\")";
		FuncionOnMouseOut="despintarFila(\"FCOP_S"+i+"\")";

		CadAux1=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0];
		CadAux2=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1];
		CadAux3=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][2];
		CadAux4=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][3]);
		CadAux5=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][4];
		CadAux7=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]);
		CadAux8=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]);
		CadAux9=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][8];

		Contenido+="<TR id='FCOP_S"+i+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"' style='vertical-align: middle;'>";
		Contenido+="<TD width='1%' class='FilaEstilo' align='right'><img class='BotonesParaCampos' src='../../image/icon/icon-display_16x16.png' style='border: none; background: none; margin-right: 3px; margin-left: 1px; width: 16px; height: 16px;' title='Visualizar' onclick='Form_CHEQUE_ORDEN_PAGO__VisualizarOP("+i+")'/></TD>";
		if(Form_CHEQUE_ORDEN_PAGO__TipoModificar!=-1){
			if(CadAux1==true)
				Contenido+="<TD width='1%' class='FilaEstilo' ><INPUT type='checkbox' id='CHECK_FCOP_S_"+i+"' value='"+CadAux2+"' checked onchange='Form_CHEQUE_ORDEN_PAGO__Change("+i+");'></TD>";
			else
				Contenido+="<TD width='1%' class='FilaEstilo' ><INPUT type='checkbox' id='CHECK_FCOP_S_"+i+"' value='"+CadAux2+"' onchange='Form_CHEQUE_ORDEN_PAGO__Change("+i+");'></TD>";
			}
		else{
			if(CadAux1==true)
				Contenido+="<TD width='1%' class='FilaEstilo' ><INPUT type='checkbox' disabled checked></TD>";
			else
				Contenido+="<TD width='1%' class='FilaEstilo' ><INPUT type='checkbox' disabled></TD>";
			}

		Contenido+="<TD width='10%' class='FilaEstilo' align='left' onclick=\""+FuncionOnclick+"\">"+CadAux9+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux3+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' style='font-size: 9px;' onclick=\""+FuncionOnclick+"\">"+CadAux5+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='right' onclick=\""+FuncionOnclick+"\">"+CadAux4+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='right' onclick=\""+FuncionOnclick+"\">"+CadAux7+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='right' id='FCOP_S_MONTO_"+i+"' onclick=\""+FuncionOnclickMonto+"\" ondblclick=\""+FuncionOnDblclickMonto+"\">"+CadAux8+"</TD>";
				

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML=Contenido;
	if(Form_CHEQUE_ORDEN_PAGO__TipoModificar!=-1)
		Form_CHEQUE_ORDEN_PAGO__CalcularMonto();
	}

function Form_CHEQUE_ORDEN_PAGO__VisualizarOP(i){
	window.open("../../report/orden_pago.php?id="+Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]);
}

function Form_CHEQUE_ORDEN_PAGO__VisualizarOPTodas(){
	if(Form_CHEQUE_ORDEN_PAGO__TamArreglo==0) return;
	var ids=[];
	for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__TamArreglo;i++)
		ids[i]=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1];	
	window.open("../../report/orden_pago.php?id="+ids.join());
}

/**
* Permite seleccionar un elemento en la tabla solicitudes, la seleccion actual se guarda en Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo.
*/
function Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudesMonto(i){
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo!=-1)
		xGetElementById("FCOP_S"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo).bgColor=colorFondoTabla;
	Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo=i;
	}

/**
* Permite seleccionar un elemento en la tabla solicitudes, a diferencia de que muestra la informacion del banco en los campos correspondiente y modifica la cuenta contable asociada al banco en los detalles del cheque.
*/
function Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudes(i){
	Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudesMonto(i);
	var InfBanco,sw=false;
	//InfBanco=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][5].split("&%?~");

	//xGetElementById("ID_CTA_FCOP").value=completarCodigoCeros(InfBanco[0],NDigitos_Codigo_CtaBancaria);
	//xGetElementById("NCTA_FCOP").value=InfBanco[1];
	//xGetElementById("TIPO_CTA_FCOP").value=InfBanco[2];
	//xGetElementById("BANCO_FCOP").value=InfBanco[3];

	if(xTrim(xGetElementById("CONCEPTO_FCOP").value)==""){
		//xGetElementById("CONCEPTO_FCOP").value=xGetElementById("FCOP_S"+i).title;
		xGetElementById("CONCEPTO_FCOP").value=Form_CHEQUE_ORDEN_PAGO__Arreglo[i][4];
		}

	//if(xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value!=InfBanco[4]){
	//	Form_CHEQUE_ORDEN_PAGO__BuscarDenominacionCtaContableBanco(InfBanco[4]);
	//	}

	xGetElementById("CHECK_FCOP_S_"+i).checked=!xGetElementById("CHECK_FCOP_S_"+i).checked;
	Form_CHEQUE_ORDEN_PAGO__Change(i);
	}

/**
* Busca la denominacion de la cta contable asociada al banco seleccionado
*/
/*
function Form_CHEQUE_ORDEN_PAGO__BuscarDenominacionCtaContableBanco(cc){
		xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value=cc;
		AjaxRequest.post({	'parameters':{ 	'accion':"Form_LISTA_CUENTAS_CONTABLES__BuscarListado",
											'CadenaBuscar':xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value},
							'onSuccess':
								function(req){
									var respuesta = req.responseText;
									var resultado = eval("(" + respuesta + ")");
									xGetElementById("CTA_DENOMINACION_CONTABLE_FCOP").value=resultado[0]["denominacion_cta_contable"];
									Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles();
									},
							'url':'../modulo_cuentas/consultas.php',
							'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
							});
	}
*/

/**
* Al cambiar el un check de la tabla solicitudes, se debe modificar y recalcular el monto a pagar y los detalles del cheque
* @param {Integer} i Indice marcado/desmarcado.
*/
var Form_CHEQUE_ORDEN_PAGO__ArregloDetalles=[];
function Form_CHEQUE_ORDEN_PAGO__Change(i){
	Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=xGetElementById("CHECK_FCOP_S_"+i).checked;
	Form_CHEQUE_ORDEN_PAGO__LlenarMontoPagar(i);
	if(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]==true){
		xGetElementById("MONTO_FCOP").value="Calculando...";
		//buscar inf contables solicitud
		Form_CHEQUE_ORDEN_PAGO__onGetDetallesOP(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]);
		Form_CHEQUE_ORDEN_PAGO__CalcularMonto();
		}
	else
		Form_CHEQUE_ORDEN_PAGO__CalcularMonto();
	}

function Form_CHEQUE_ORDEN_PAGO__onGetDetallesOP(_id){
	var _tmp=Ext.Ajax.request({
		async: false,
		url:"module/comprobante/",
		params:{
			'action':"onGet",
			'id': _id
		}
	});
	if(_tmp.statusText=="OK"){
		var resultado=Ext.decode(_tmp.responseText);

		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]]=[];
		
		K=0;
		for(var i=0;i<resultado[0]["detalle_presupuestario"].length;i++){
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]=[];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["cuenta"]=resultado[0]["detalle_presupuestario"][i]["estructura_presupuestaria"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["columna"]="P";
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_presupuestario"][i]["monto"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
			K++;
		}
		var SUMA_DEBE=0;
		for(var i=0;i<resultado[0]["detalle_contable"].length;i++){
			if(resultado[0]["detalle_contable"][i]["operacion"]=="D") continue;
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]=[];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_contable"][i]["denominacion"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["columna"]="D";
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_contable"][i]["monto"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
			SUMA_DEBE+=resultado[0]["detalle_contable"][i]["monto"]*1.0;
			K++;
		}
		var SUMA_RETENCIONES=0;
		for(var i=0;i<resultado[0]["detalle_retencion"].length;i++){
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]=[];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_retencion"][i]["id_cuenta_contable"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_retencion"][i]["cuenta_contable"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_retencion"][i]["denominacion_contable"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["columna"]="C";
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_retencion"][i]["monto"];
			Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
			SUMA_RETENCIONES+=resultado[0]["detalle_retencion"][i]["monto"]*1.0;
			K++;
		}
		//cuenta de banco
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]=[];
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]="x";
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]="";
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]="";
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["columna"]="C";
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["monto"]=SUMA_DEBE*1.0-SUMA_RETENCIONES*1.0;
		Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
		
		
		
	}
}


/**
* Al hacer marcar/descmarcar el check que se encuentra en la cabecera de la tabla solicitudes, este debe recorrer todos todas las solicitudes para cambiar el estado al del padre.
*/
function Form_CHEQUE_ORDEN_PAGO__AlternarChecks(){
	var estado=xGetElementById("CHECK_FCOP").checked;
	for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__TamArreglo;i++){
		if(xGetElementById("CHECK_FCOP_S_"+i))
			xGetElementById("CHECK_FCOP_S_"+i).checked=estado;
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=estado;
		Form_CHEQUE_ORDEN_PAGO__LlenarMontoPagar(i);
		}
	Form_CHEQUE_ORDEN_PAGO__CalcularMonto();
	}

/**
* Calcula el monto a pagar para la solicitud en proceso.
*/
function Form_CHEQUE_ORDEN_PAGO__LlenarMontoPagar(i){
	if(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]==true){
		if(numberFormat(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7],2)!="0.00")
			return;
		Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]=numberFormat(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][3]-Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6],2);
		xGetElementById("FCOP_S_MONTO_"+i).innerHTML=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]);
		}
	}

/**
* Calcula el monto real por el cual se va a emitir el cheque
*/
function Form_CHEQUE_ORDEN_PAGO__CalcularMonto(){
	//llenar select detalles
	var Cad="<OPTION value=''>CHEQUE</OPTION>";
	for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__TamArreglo;i++)
		if(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]==true){
			Cad+="<OPTION value='"+Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]+"'>OP# "+Form_CHEQUE_ORDEN_PAGO__Arreglo[i][8]+"</OPTION>";
			}
	xGetElementById("SELECT_DETALLES_FCOP").innerHTML=Cad;

	//llenar tabla de los detalles (Especificamente todos)
	Form_CHEQUE_ORDEN_PAGO__GenerarDetalles();

	var MONTO=0;
	for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__ArregloTodosK;i++)
		if(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]['subcuenta']=="x"){
			MONTO=numberFormat(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]['monto'],2);
			break;
			}
	xGetElementById("MONTO_FCOP").value=FormatearNumero(MONTO);
	return MONTO;
	}


/**
* Muesta los detalles del cheque, dependiendo de la solicitud que se desea mostrar la información.
*/
function Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles(){
	var SelectSolicitud=xGetElementById("SELECT_DETALLES_FCOP").value;
	var n=0;
	var ArregloImprimir=new Array();

	if(SelectSolicitud==""){//todos
		ArregloImprimir=copy(Form_CHEQUE_ORDEN_PAGO__ArregloTodos);
		n=Form_CHEQUE_ORDEN_PAGO__ArregloTodosK;
		}
	else{
		ArregloImprimir=copy(Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SelectSolicitud]);
		n=Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SelectSolicitud].length;
		}

	var Aux=new Array();
	for(i=0;i<n-1;i++)
		for(j=i+1;j<n;j++)
			if(Form_CHEQUE_ORDEN_PAGO__PrioridadOrdenarColumna(ArregloImprimir[i]["columna"])>Form_CHEQUE_ORDEN_PAGO__PrioridadOrdenarColumna(ArregloImprimir[j]["columna"])){
				Aux=copy(ArregloImprimir[j]);
				ArregloImprimir[j]=copy(ArregloImprimir[i]);
				ArregloImprimir[i]=copy(Aux);
				}
			else if(Form_CHEQUE_ORDEN_PAGO__PrioridadOrdenarColumna(ArregloImprimir[i]["columna"])==Form_CHEQUE_ORDEN_PAGO__PrioridadOrdenarColumna(ArregloImprimir[j]["columna"]))
				if(ArregloImprimir[i]["monto"]*1.0<ArregloImprimir[j]["monto"]*1.0){
				Aux=copy(ArregloImprimir[j]);
				ArregloImprimir[j]=copy(ArregloImprimir[i]);
				ArregloImprimir[i]=copy(Aux);
				}


	var SUMA_P=0;
	var SUMA_D=0;
	var SUMA_C=0;
	var Contenido="";
	var CadAux1="";
	var CadAux2="";

	for(i=0;i<n;i++){
		Contenido+="<TR>";
		Contenido+="<TD width='17%' class='FilaEstilo' style='font-size : 11px;' align='center'>"+ArregloImprimir[i]['cuenta']+"</TD>";
		if(ArregloImprimir[i]['subcuenta']!="x"){
			CadAux1=ArregloImprimir[i]['subcuenta_mostrar'];
			CadAux2=ArregloImprimir[i]['denominacion_subcuenta'];
			}
		else{
			CadAux1=xGetElementById("CUENTA_CONTABLE_FCOP").value;
			CadAux2=xGetElementById("CTA_DENOMINACION_CONTABLE_FCOP").value;
			}

		Contenido+="<TD width='13%' class='FilaEstilo' align='center'>"+CadAux1+"</TD>";
		Contenido+="<TD width='33%' class='FilaEstiloContinua' align='left' style='font-size : 11px;'>&nbsp;"+CadAux2+"</TD>";

		if(ArregloImprimir[i]['columna']=="P"){
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+FormatearNumero(ArregloImprimir[i]['monto'])+"</TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			SUMA_P=SUMA_P*1.0+numberFormat(ArregloImprimir[i]['monto'],2)*1.0;
			}
		else if(ArregloImprimir[i]['columna']=="D"){
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+FormatearNumero(ArregloImprimir[i]['monto'])+"</TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			SUMA_D=SUMA_D*1.0+numberFormat(ArregloImprimir[i]['monto'],2)*1.0;
			}
		else{
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
			Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+FormatearNumero(ArregloImprimir[i]['monto'])+"</TD>";
			SUMA_C=SUMA_C*1.0+numberFormat(ArregloImprimir[i]['monto'],2)*1.0;
			}
		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_DETALLES_FCOP").innerHTML=Contenido;

	xGetElementById("TOTAL_PARCIALES_FCOP").value=FormatearNumero(SUMA_P);
	xGetElementById("TOTAL_DEBITOS_FCOP").value=FormatearNumero(SUMA_D);
	xGetElementById("TOTAL_CREDITOS_FCOP").value=FormatearNumero(SUMA_C);

	}


var Form_CHEQUE_ORDEN_PAGO__ArregloTodos=[];
var Form_CHEQUE_ORDEN_PAGO__ArregloTodosK=0;
var Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado=[];
var Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltradoK=0;

/**
* Se calcula y se generan los detalles dependiendo del monto a pagar por solicitud y de las retenciones.
* Calculos necesario para conocer el monto real del cheque.
*/
function Form_CHEQUE_ORDEN_PAGO__GenerarDetalles(){
	var SelectSolicitud="";
	var MontoSolicitud=0;
	var MontoPagado=0;
	var MontoPagar=0;
	var n=0;
	var ArregloImprimir=new Array();

	var Porcentaje=0;
	var sw=false;
	var PosCtaBanco=-1;
	var Suma=0;
	var K=0;



	Form_CHEQUE_ORDEN_PAGO__ArregloTodos=[];

	for(var w=0;w<Form_CHEQUE_ORDEN_PAGO__TamArreglo;w++)
		if(Form_CHEQUE_ORDEN_PAGO__Arreglo[w][0]==true){
			ArregloImprimir="";
			ArregloImprimir=new Array();
			n=0;

			SelectSolicitud=Form_CHEQUE_ORDEN_PAGO__Arreglo[w][1];
			MontoSolicitud=numberFormat(Form_CHEQUE_ORDEN_PAGO__Arreglo[w][3],2);
			MontoPagado=numberFormat(Form_CHEQUE_ORDEN_PAGO__Arreglo[w][6],2);
			MontoPagar=numberFormat(Form_CHEQUE_ORDEN_PAGO__Arreglo[w][7],2);
			if(MontoSolicitud==MontoPagar && MontoPagado=="0.00"){//si se paga toda la solicitud en el 1er intento
				ArregloImprimir=copy(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud]);
				n=ArregloImprimir.length;
				}
			else{
				if(xGetElementById("INCLUSION_RETENCION_FCOP").value=="0"){//INCLUIR RETENCIONES EN EL ULTIMO PAGO
					sw=false;
					if(numberFormat(MontoPagado*1.0+MontoPagar*1.0,2)==MontoSolicitud)//si el ultimo pago incluir las retenciones
						sw=true;
					}
				else{//INCLUIR RETENCIONES EN EL 1er PAGO
					sw=true;
					if(MontoPagado*1.0>0)//si existen pagos realizados
						sw=false;
					}

				n=0;
				PosCtaBanco=-1;
				Suma=0;
				Porcentaje=MontoPagar/MontoSolicitud;
				for(i=0;i<Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud].length;i++){
					if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]['columna']=="P" || Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]['columna']=="D"){
						ArregloImprimir[n]=new Array();
						ArregloImprimir[n]=copy(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]);
						ArregloImprimir[n]['monto']=numberFormat(ArregloImprimir[n]['monto']*Porcentaje,2);
						n++;
						}
					else{//si es C, Credito
						if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]['subcuenta']=='x')
							PosCtaBanco=i;
						if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]['subcuenta']!='x' && sw==true){//si no es la cuenta banco y es incluir retenciones (si es una retencion)
							ArregloImprimir[n]=new Array();
							ArregloImprimir[n]=copy(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][i]);
							Suma=Suma*1.0+ArregloImprimir[n]['monto']*1.0;
							n++;
							}
						}
					}//fin for

				if(PosCtaBanco!=-1){
					ArregloImprimir[n]=new Array();
					ArregloImprimir[n]=copy(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SelectSolicitud][PosCtaBanco]);

					if(sw==false)//si no estan incluidas las retenciones, cta banco = montopagar
						ArregloImprimir[n]['monto']=MontoPagar;
					else
						ArregloImprimir[n]['monto']=numberFormat(MontoPagar-Suma,2);
					n++;
					}
				}

			Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SelectSolicitud]=copy(ArregloImprimir);

			if(K==0){
				for(i=0;i<n;i++){
					Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]=copy(ArregloImprimir[i]);
					K++;
					}
				}
			else{
				for(i=0;i<n;i++){
					sw=false;
					for(j=0;j<K;j++)
						if(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[j]["id_accion_subespecifica"]==ArregloImprimir[i]["id_accion_subespecifica"] && Form_CHEQUE_ORDEN_PAGO__ArregloTodos[j]["subcuenta"]==ArregloImprimir[i]["subcuenta"] && Form_CHEQUE_ORDEN_PAGO__ArregloTodos[j]["columna"]==ArregloImprimir[i]["columna"]){
							Form_CHEQUE_ORDEN_PAGO__ArregloTodos[j]["monto"]=numberFormat(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[j]["monto"]*1.0+ArregloImprimir[i]['monto']*1.0,2);
							sw=true;
							}
					if(sw==false){
						Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]=copy(ArregloImprimir[i]);
						K++;
						}
					}
				}

			}



	Form_CHEQUE_ORDEN_PAGO__ArregloTodosK=K;
	Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles();

	}

/**
* Necesario para ordenar los detalles en el siguiente orden: P (Parciales=Mobimientos presupuestarios), D (Debitos), C (Creditos)
* @param {Caracter}
*/
function Form_CHEQUE_ORDEN_PAGO__PrioridadOrdenarColumna(V){
	if(V=="P") return 0;
	if(V=="D") return 1;
	return 2;
	}


/**
* Al hacer dobleclick sobre el monto de la solicitud apaperce el campo de texto para modificarlo.
*/
function Form_CHEQUE_ORDEN_PAGO__ModificarValorCelda(_IDCelda){
	if(xGetElementById("txt_celda_"+_IDCelda))
		return;
	Valor=Form_CHEQUE_ORDEN_PAGO__Arreglo[Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo][7];
	Valor=numberFormat(Valor,2);

	xGetElementById(_IDCelda).innerHTML="<INPUT id='txt_celda_"+_IDCelda+"' class='TextoCampoInputTabla' type='text' size='15' value='"+Valor+"' onblur=\"Form_CHEQUE_ORDEN_PAGO__ModificarValorCeldaPierdeFoco('"+_IDCelda+"',"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo+")\" onkeypress=\"return AcceptNum(event,'txt_celda_"+_IDCelda+"',false,true);\" style='text-align : right;' onkeyup=\"Form_CHEQUE_ORDEN_PAGO__KeyPressEnter(event,'"+_IDCelda+"',"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo+")\">";
	xGetElementById("txt_celda_"+_IDCelda).focus();
	}

/**
* Al perder el foco el campo de texto del monto de la solicitud, eliminanos el campo de texto y escribimos sobre la tabla el monto escrito.
* Y realizamos los calculos correspondientes.
*/
function Form_CHEQUE_ORDEN_PAGO__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar){
	//cuando el numero tengo % debe calcular el % en base al monto total de la solicitud
	var Aux=xGetElementById("txt_celda_"+_IDCelda).value;
	var ArregloAux=Aux.split("%");
	Aux=ArregloAux[0];
	if(ArregloAux.length==2)//es decir, tiene %
		if(ArregloAux[1]==""){//todo bien   10.00%
			//hacer el calculo del % en base al total de la solicitud
			Aux=Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][3]*Aux/100;
			}
		else{//error p.ej. 10.00%12
			Aux="0.00";
			}
	//si se pasa del monto de deberia ser. Forzar monto nuevo al tope maximo a pagar
	var Aux2=Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][3]*1.0-Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][6]*1.0;
	if(Aux*1.0>Aux2*1.0)
		Aux=Aux2;


	Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][7]=numberFormat(Aux,2);
	xGetElementById(_IDCelda).innerHTML=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][7]);
	Form_CHEQUE_ORDEN_PAGO__CalcularMonto();

	var SolicitudActual=Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][1];
	if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SolicitudActual] && Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SolicitudActual]){//si existen los arreglos
		//Busco el monto maximo real a pagar al proveedor en Form_CHEQUE_ORDEN_PAGO__ArregloDetalles
		//Busco el monto a pagar al proveedor en el cheque en curso	Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado
		//si este monto + monto_pagado es mayor que el monto real maximo, forzar a la emision del cheque a ser el monto total por pagar o restante para cancelar la solicitud
		var n=Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SolicitudActual].length;
		var n2=Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SolicitudActual].length;
		var A, B, C=Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][6];//monto pagado
		for(i=0;i<n;i++)
			if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SolicitudActual][i]['columna']=="C")
				if(Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SolicitudActual][i]['subcuenta']=="x"){
					for(j=0;j<n2;j++)
						if(Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SolicitudActual][j]['columna']=="C")
							if(Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SolicitudActual][j]['subcuenta']=="x"){
								A=Form_CHEQUE_ORDEN_PAGO__ArregloDetalles[SolicitudActual][i]['monto'];
								B=Form_CHEQUE_ORDEN_PAGO__ArregloDetallesFiltrado[SolicitudActual][j]['monto'];

								if(B*1.0+C*1.0>A*1.0){//si excede el monto a pagar al proveedor, pagar todo
									Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][7]=numberFormat(Aux2,2);
									xGetElementById(_IDCelda).innerHTML=FormatearNumero(Form_CHEQUE_ORDEN_PAGO__Arreglo[indice_modificar][7]);
									Form_CHEQUE_ORDEN_PAGO__CalcularMonto();
									}
								break;
								}
					break;
					}
		}//fin si
	}

/**
* Permite moverse por los montos de las solicitudes con el teclado, enter, tecla de direccion hacia abajo y hacia arriba.
*/
function Form_CHEQUE_ORDEN_PAGO__KeyPressEnter(event,_IDCelda,indice_modificar){
	if(event.keyCode==13 || event.keyCode==40){
		xGetElementById("txt_celda_"+_IDCelda).onblur="";
		//hacemos que pierda el foco
		Form_CHEQUE_ORDEN_PAGO__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar);
		if(String(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo)==String(Form_CHEQUE_ORDEN_PAGO__TamArreglo-1)){
			return;
			}
		Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudesMonto(parseInt(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo)+1);
		Form_CHEQUE_ORDEN_PAGO__ModificarValorCelda("FCOP_S_MONTO_"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo);
		}
	else if(event.keyCode==38){
		xGetElementById("txt_celda_"+_IDCelda).onblur="";
		Form_CHEQUE_ORDEN_PAGO__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar);
		if(String(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo)=='0')
			return;
		Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTablaSolicitudesMonto(parseInt(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo)-1);
		Form_CHEQUE_ORDEN_PAGO__ModificarValorCelda("FCOP_S_MONTO_"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualArreglo);
		}
	}


/**
* Guarda los datos en la BD
*/
function Form_CHEQUE_ORDEN_PAGO__Guardar(){
	var msg="";
	
	var _id_persona							= xTrim(strtoupper(xGetElementById("ID_BoP_FCOP").value));
	var _id_banco_cuenta				= xTrim(strtoupper(xGetElementById("ID_CTA_FCOP").value));
	var _fecha									= xTrim(strtoupper(xGetElementById("FECHA_FCOP").value));
	var _numero									= xTrim(strtoupper(xGetElementById("N_CHEQUE_FCOP").value));
	var _concepto								= xTrim(strtoupper(xGetElementById("CONCEPTO_FCOP").value));
	var _monto=0;
	var _id_cuenta_contable			= "";
	//if(Form_CHEQUE_ORDEN_PAGO__TipoModificar!=-1)
 	//	_monto				= Form_CHEQUE_ORDEN_PAGO__CalcularMonto();

	if(!_id_persona){
		msg="Por favor seleccione el proveedor o beneficiario.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!_id_banco_cuenta){
		msg="Por favor seleccione la cuenta bancaria.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!_fecha){
		msg="Por favor introduzca la fecha.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_CHEQUE_ORDEN_PAGO__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	
	if(!_numero){
		msg="Por favor introduzca el número del cheque.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	if(!_concepto){
		msg="Por favor introduzca el concepto.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	
	if(xGetElementById("TOTAL_DEBITOS_FCOP").value!=xGetElementById("TOTAL_CREDITOS_FCOP").value){
		msg="Error. No coinciden los totales contables en los detalles del cheque.";
		Form_CHEQUE_ORDEN_PAGO__Mensaje(msg,"ROJO");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
		return;
		}
	
	
	Form_CHEQUE_ORDEN_PAGO__DesactivarFormulario();
	
	var _detalle={};	
	
	
	
	
	if(Form_CHEQUE_ORDEN_PAGO__TipoModificar!=-1){
		_detalle.presupuestario=[];
		_detalle.contable=[];
		_detalle.comprobante_previo_monto_pagado=[];
		
		//asociar el cheque a las ordedes de pago seleccionadas
		for(var i=0;i<Form_CHEQUE_ORDEN_PAGO__TamArreglo;i++)
			if(Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]==true)
				_detalle.comprobante_previo_monto_pagado.push({
					id_comprobante: Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1],
					monto_pagado: Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]
				});
		
		
		//agregar detalles presupuestarios y contables
		for(i=0;i<Form_CHEQUE_ORDEN_PAGO__ArregloTodosK;i++){
			if(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["columna"]=="P"){//presupuesto			
				_detalle.presupuestario.push({
					id_accion_subespecifica: Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["id_accion_subespecifica"],
					id_cuenta_presupuestaria: Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["subcuenta"],
					operacion: 'P',
					monto: Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["monto"]
				});
			}
			else{//contabilidad
				_id_cuenta_contable=Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["subcuenta"];
				if(Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["subcuenta"]=='x'){
					_monto=Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["monto"];
					_id_cuenta_contable=xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value;
				}
				
				_detalle.contable.push({
					id_cuenta_contable: _id_cuenta_contable,
					operacion: Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["columna"]=="D"?"D":"H",
					monto: Form_CHEQUE_ORDEN_PAGO__ArregloTodos[i]["monto"]			
				});
			}
		}
	}
	
	_detalle.comprobante_bancario={
		id_banco_cuenta: _id_banco_cuenta,
		id_banco_movimiento_tipo: 3,//cheque orden de pago
		numero: _numero,
		monto: _monto
	};
	
	var _id_comprobante="";
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista;
	
	if(_id_comprobante){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_CHEQUE_ORDEN_PAGO__ActivarFormulario();
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
						'onSuccess':Form_CHEQUE_ORDEN_PAGO__GuardarMensaje,
						'url':'../comprobante/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_CHEQUE_ORDEN_PAGO__GuardarMensaje(req){
	Form_CHEQUE_ORDEN_PAGO__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_CHEQUE_ORDEN_PAGO__Nuevo();
		Form_CHEQUE_ORDEN_PAGO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_CHEQUE_ORDEN_PAGO__Mensaje(respuesta.message,"ROJO");
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_CHEQUE_ORDEN_PAGO__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FCOP").checked){
 		if(ev.keyCode==13)
			Form_CHEQUE_ORDEN_PAGO__BuscarListado();
		return;
		}
	Form_CHEQUE_ORDEN_PAGO__BuscarListado();
	}


/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_CHEQUE_ORDEN_PAGO__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_CHEQUE_ORDEN_PAGO__BuscarListado(){
	xGetElementById("MSG_CUSTODIA").innerHTML="";
	
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();

	Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista=-1;
	Form_CHEQUE_ORDEN_PAGO__TipoModificar=-1;
	xGetElementById("FORMULARIO_CHEQUE_ORDEN_PAGO").reset();
	xGetElementById("MONTO_FCOP").value="0,00";

	xGetElementById("TABLA_LISTA_SOLICITUDES_FCOP").innerHTML="";
	xGetElementById("TABLA_LISTA_DETALLES_FCOP").innerHTML="";
	xGetElementById("SELECT_DETALLES_FCOP").innerHTML="<OPTION value=''>CHEQUE</OPTION>";
	xGetElementById("TOTAL_PARCIALES_FCOP").value="0,00";
	xGetElementById("TOTAL_DEBITOS_FCOP").value="0,00";
	xGetElementById("TOTAL_CREDITOS_FCOP").value="0,00";



	Form_CHEQUE_ORDEN_PAGO__ActivarFormulario();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonModificar();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonEliminar();
	Form_CHEQUE_ORDEN_PAGO__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCOP").value));
	if(CadenaBuscar!="")
		if(Form_CHEQUE_ORDEN_PAGO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_CHEQUE_ORDEN_PAGO__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FCOP").innerHTML=IconoCargandoTabla;

	
	var _tipo_persona=Form_CHEQUE_ORDEN_PAGO__SW_PERSONA=="P"?"J":"N";
		
	var _mostrar={
		'mes':xGetElementById("MES_FILTRAR_FCOP").value,		
		'tipo':['MB'],
		'operacion_codigo': ['CH'],
		'persona': 't',
		'tipo_persona': _tipo_persona
	};

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_CHEQUE_ORDEN_PAGO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	

	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_CHEQUE_ORDEN_PAGO__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	//console.log(resultado);
	resultado=resultado["result"];
	
	var n=resultado.length;

	var CadAux1, CadAux2;

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCOP").value));
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4, CadAux5, CadAux6, Aux, Aux2;

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
		
		FuncionOnclick="Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
  		FuncionOnDblclick="Form_CHEQUE_ORDEN_PAGO__TabPane.setSelectedIndex(0);";
  		FuncionOnMouseOver="pintarFila(\"FCOP"+resultado[i]['id']+"\")";
  		FuncionOnMouseOut="despintarFila(\"FCOP"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FCOP"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		if(!resultado[i]['persona']) 
			resultado[i]['persona']="";
		
		
		if(xGetElementById("SOMBRA_CHECKBOX_FCOP").checked && TextoBuscar!=""){
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
//		Aux="";
// 		if(resultado[i]['entregado']=="t")	Aux="checked";
		
		Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";

		//Contenido+="<TD width='1%' class='FilaEstilo'><INPUT id='CBL_FCOP"+resultado[i]['id']+"' type='checkbox' "+Aux+" onchange='Form_CHEQUE_ORDEN_PAGO__ListadoAlternarCustoria("+resultado[i]['id']+")' title='"+Form_CHEQUE_ORDEN_PAGO__MsgCustodia(resultado[i]['entregado']=="t"?1:0)+"'></TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='center'>"+CadAux3+"</TD>";
		Contenido+="<TD width='25%' class='FilaEstiloContinua'>"+CadAux5+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' style='padding-left: 5px;'>"+CadAux4+"</TD>";		
		Contenido+="<TD width='10%' class='FilaEstilo' align='right'>"+CadAux6+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCOP").innerHTML=Contenido;
	}

/**
* Es llamada cuando se hace click sobre algun elemento de la tabla.
* Esta manda los datos para el formulario que se encuentra en la pestaña 'entrada de datos'
* @param {Integer} IDSeleccion Id del elemento seleccionado
* @param {String} NumeroCheque Numero del cheque
* @param {String} FechaCheque Fecha del cheque
* @param {Numeric} MontoCheque Monto del cheque
* @param {String} ConceptoCheque concepto del cheque
* @param {Integer} IDCtaBancaria Id de la cuenta bancaria
* @param {Integer} IDpb Id del proveedor/Id del beneficiario
* @param {String} RifCiPB Rif del proveedor/Cedula del beneficiario
* @param {String} NombrePB Nombre del proveedor/Nombre del beneficiario
* @param {Integer} IDComprobante Id del comprobante asociado al cheque
* @param {Boolean} Emitido Indica si el cheque fue emitido
* @param {Boolean} Anulado Indica si el cheque fue anulado
*/
function Form_CHEQUE_ORDEN_PAGO__SeleccionarElementoTabla(IDSeleccion,estado){
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista==IDSeleccion)
		return;

	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista!=-1)
		xGetElementById("FCOP"+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FCOP"+IDSeleccion).bgColor=colorBase;
	Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista=IDSeleccion;
	xGetElementById("SELECT_DETALLES_FCOP").innerHTML="<OPTION value=''>CHEQUE</OPTION>";

	Form_CHEQUE_ORDEN_PAGO__TipoModificar=-1;


	
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();
	
	
	Form_CHEQUE_ORDEN_PAGO__DesactivarFormulario();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonGuardar();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonModificar();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonEliminar();
	Form_CHEQUE_ORDEN_PAGO__ActivarBotonImprimir();
	
	
	Form_CHEQUE_ORDEN_PAGO__Mensaje("");
	Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");
								
								xGetElementById("COMPROBANTE_FCOP").innerHTML=resultado[0]["tipo"]+"-"+resultado[0]["correlativo"];
								
								xGetElementById("FECHA_FCOP").value=resultado[0]["fecha"];
								xGetElementById("CONCEPTO_FCOP").value=resultado[0]["concepto"];
								
								//cargar proveedor/beneficiario
								xGetElementById("ID_BoP_FCOP").value=resultado[0]["detalle_persona"][0]["id"];
								xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FCOP").value=resultado[0]["detalle_persona"][0]["identificacion"];
								xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FCOP").value=resultado[0]["detalle_persona"][0]["denominacion"];
								
								//numero de cuenta
								xGetElementById("ID_CTA_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["id_banco_cuenta"];
								xGetElementById("NCTA_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["numero_cuenta"];
								xGetElementById("DESCRIPCION_NCTA_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_denominacion"];
								xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["id_cuenta_contable"];
								xGetElementById("CUENTA_CONTABLE_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_contable"];
								xGetElementById("CTA_DENOMINACION_CONTABLE_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["denominacion_contable"];
								xGetElementById("TIPO_CTA_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_tipo"];
								xGetElementById("BANCO_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["banco"];
								
								//otros datos del cheque
								xGetElementById("N_CHEQUE_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["numero"];
								xGetElementById("MONTO_FCOP").value=resultado[0]["detalle_comprobante_bancario"][0]["monto"];
								
								//cargar ordenes de pago asociadas al cheque
								var _mostrar={
									id: resultado[0]["id"]
								};
								
								_tmp=Ext.Ajax.request({
									async: false,
									url:"module/comprobante/",
									params:{
										action: 'onList_OP_cheque',
										mostrar: Ext.encode(_mostrar),
										text:'',
										start: '0',
										limit : 'ALL',
										sort:'[{"property":"fecha","direction":"ASC"}]'		
									}
								});
								if(_tmp.statusText=="OK"){
									var _retorno=Ext.decode(_tmp.responseText);
									_retorno=_retorno["result"];
									//console.log(_retorno);
									for(var i=0;i<_retorno.length;i++){
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i]=[];
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][0]=true;//check
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][1]=_retorno[i]['id'];
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][8]=_retorno[i]['correlativo'];
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][2]=_retorno[i]['fecha'];
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][3]=numberFormat(_retorno[i]['monto'],2);
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][4]=strtoupper(_retorno[i]['concepto']);
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][5]="";
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][6]=numberFormat(_retorno[i]['monto_pagado_acumulado'],2);
										Form_CHEQUE_ORDEN_PAGO__Arreglo[i][7]=numberFormat(_retorno[i]['monto_pagado'],2);;
									}
								Form_CHEQUE_ORDEN_PAGO__TamArreglo=_retorno.length;								
								Form_CHEQUE_ORDEN_PAGO__MostrarListadoSolicitudes();
								}
								
								//cargar y mostrar detalles presupuestarios y contables
								Form_CHEQUE_ORDEN_PAGO__ArregloTodos=[];								
								K=0;
								for(var i=0;i<resultado[0]["detalle_presupuestario"].length;i++){
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]=[];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["id_solicitud_pago"]=resultado[0]["id"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["cuenta"]=resultado[0]["detalle_presupuestario"][i]["estructura_presupuestaria"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["subcuenta"]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["subcuenta_mostrar"]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["denominacion_subcuenta"]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["columna"]="P";
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["monto"]=resultado[0]["detalle_presupuestario"][i]["monto"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["id_accion_subespecifica"]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
									K++;
								}
								
								for(var i=0;i<resultado[0]["detalle_contable"].length;i++){
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]=[];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["id_solicitud_pago"]=resultado[0]["id"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["cuenta"]="";
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["subcuenta"]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["subcuenta_mostrar"]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["denominacion_subcuenta"]=resultado[0]["detalle_contable"][i]["denominacion"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["columna"]=resultado[0]["detalle_contable"][i]["operacion"]=="D"?"D":"C";
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["monto"]=resultado[0]["detalle_contable"][i]["monto"];
									Form_CHEQUE_ORDEN_PAGO__ArregloTodos[K]["id_accion_subespecifica"]="";
									K++;
								}
								Form_CHEQUE_ORDEN_PAGO__ArregloTodosK=K;
								Form_CHEQUE_ORDEN_PAGO__CambioSelectDetalles();
								
								
								//segun el estado de la orden, activar los botones de contabilizar, reversar y anular
								Form_CHEQUE_ORDEN_PAGO__OcultarBotones();
								switch(estado){
									//SIN CONTABILIZAR (ROJO)
									case 0://activar el boton de modificar, mostrar el boton de contabilizar															
										Form_CHEQUE_ORDEN_PAGO__ActivarBotonModificar();
										xGetElementById("BOTON_CONTABLIZAR_FCOP").style.display="";
										break;
									//CONTABILIZADO (VERDE)
									case 2://mostrar el boton de reversar y anular
										xGetElementById("BOTON_REVERSAR_FCOP").style.display="";
										xGetElementById("BOTON_ANULAR_FCOP").style.display="";
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
function Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado(){
	Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista=-1;
	Form_CHEQUE_ORDEN_PAGO__NombreBanco="";
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonImprimir()
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonModificar();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonEliminar();
	Form_CHEQUE_ORDEN_PAGO__ActivarBotonGuardar();
	Form_CHEQUE_ORDEN_PAGO__ActivarFormulario();
	xGetElementById("FORMULARIO_CHEQUE_ORDEN_PAGO").reset();
	
	xGetElementById("CTA_CODIGO_CONTABLE_FCOP").value="";
	xGetElementById("CUENTA_CONTABLE_FCOP").value="";
	xGetElementById("CTA_DENOMINACION_CONTABLE_FCOP").value="";
	
	xGetElementById("LISTADO_BUSCAR_FCOP").value="";
	Form_CHEQUE_ORDEN_PAGO__Mensaje("");
	Form_CHEQUE_ORDEN_PAGO__MensajeListado("");
	Form_CHEQUE_ORDEN_PAGO__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FCOP",1000);
	}

var Form_CHEQUE_ORDEN_PAGO__TipoModificar=-1;
/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_CHEQUE_ORDEN_PAGO__Modificar(){
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();
	Form_CHEQUE_ORDEN_PAGO__DesactivarBotonModificar();
	Form_CHEQUE_ORDEN_PAGO__ActivarBotonGuardar();
	
	Form_CHEQUE_ORDEN_PAGO__CargarSolicitudes();
	
	Form_CHEQUE_ORDEN_PAGO__TipoModificar=1;
	xGetElementById("CHECK_FCOP").disabled=false;
	Form_CHEQUE_ORDEN_PAGO__ActivarFormulario(Form_CHEQUE_ORDEN_PAGO__TipoModificar);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_CHEQUE_ORDEN_PAGO__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({'parameters':{ 'accion':"Form_CHEQUE_ORDEN_PAGO__Eliminar",
									'id_cheque':Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista,
									'id_comprobante':Form_CHEQUE_ORDEN_PAGO__IDComprobante},
					 'onSuccess':Form_CHEQUE_ORDEN_PAGO__EliminarMensaje,
					 'url':'../modulo_banco/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_CHEQUE_ORDEN_PAGO__EliminarMensaje(req){
	var respuesta = req.responseText;
	if(respuesta==1){
		Form_CHEQUE_ORDEN_PAGO__LimpiarInputTextBuscarListado();
		Form_CHEQUE_ORDEN_PAGO__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_CHEQUE_ORDEN_PAGO__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		respuesta=eval("(" + respuesta + ")");
		respuesta=String(respuesta).split("|");
		if(parseInt(respuesta[0])==-2){
			alert("No se pudo eliminar.\nDebido a que depende del cheque "+completarCodigoCeros(String(respuesta[1]),NDigitos_Codigo_VoucherCheque)+" de posterior elaboración.\nEliminelo primero e intente nuevamente.");
			}
		else{
			Form_CHEQUE_ORDEN_PAGO__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
			Form_CHEQUE_ORDEN_PAGO__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
			}
		}
	}


function Form_CHEQUE_ORDEN_PAGO__OcultarBotones(){
	xGetElementById("BOTON_ANULAR_FCOP").style.display="none";
	xGetElementById("BOTON_CONTABLIZAR_FCOP").style.display="none";
	xGetElementById("BOTON_REVERSAR_FCOP").style.display="none";	
}

function Form_CHEQUE_ORDEN_PAGO__Contabilizar(){
	Form_CHEQUE_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 't'
								},
				'onSuccess': function(req){
								Form_CHEQUE_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_CHEQUE_ORDEN_PAGO__Reversar(){
	Form_CHEQUE_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista;
	else
		return;
	
	//ocultar el boton de contabilizar
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();
	
	AjaxRequest.post({
				'parameters':{
								'action':"onSet_Contabilizar",
								'id': _id_comprobante,
								'contabilizado': 'f'
								},
				'onSuccess': function(req){
								Form_CHEQUE_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}
	
function Form_CHEQUE_ORDEN_PAGO__Anular(){
	Form_CHEQUE_ORDEN_PAGO__Mensaje("");
	var _id_comprobante="";
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista>0) 
		_id_comprobante=Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista;
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
	
	Form_CHEQUE_ORDEN_PAGO__Mensaje("Anulando. Por favor espere...");
	Form_CHEQUE_ORDEN_PAGO__OcultarBotones();

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
								Form_CHEQUE_ORDEN_PAGO__GuardarMensaje(req);
								},
				'url':'../comprobante/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
}

/**
* Imprime el cheque
*/
function Form_CHEQUE_ORDEN_PAGO__Imprimir(){
	if(Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista==-1)
		return;
	window.open("../../report/cheque.php?id="+Form_CHEQUE_ORDEN_PAGO__IDSeleccionActualLista);
	}
