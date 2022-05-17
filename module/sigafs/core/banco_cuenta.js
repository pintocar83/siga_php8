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
function Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FDCB").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FDCB_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario(Nuevo){
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('ondblclick',"Form_LISTA_DE_BANCOS__Abrir('CODIGO_BANCO_FDCB','BANCO_FDCB',Form_DEFINICIONES_CUENTAS_BANCARIAS__IDBancoSeleccionActualLista)");
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('onkeypress',"Form_DEFINICIONES_CUENTAS_BANCARIAS__PresionarEnterCodigoBanco(event)");
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('onblur',"Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarBanco()");
	xGetElementById("IMG_BUSCAR_CODIGO_BANCO_FDCB").setAttribute('onclick',"Form_DEFINICIONES_CUENTAS_BANCARIAS__AbrirVentanaListaBancos()");
	//xGetElementById("IMG_AGREGAR_CODIGO_BANCO_FDCB").setAttribute('onclick',"VentanaNueva('VENTANA_BANCO','DEFINICIONES DE BANCO',800,500,'../modulo_banco/Form_DEFINICIONES_DE_BANCO.php',true);");
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('onkeypress',"Form_DEFINICIONES_CUENTAS_BANCARIAS__PresionarEnterCodigoCuentaContable(event)");
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('onblur',"Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarCuentaContable()");
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('ondblclick',"Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FDCB','NOMBRE_CODIGO_CONTABLE_FDCB')");
	xGetElementById("IMG_BUSCAR_CODIGO_CONTABLE_FDCB").setAttribute('onclick',"Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FDCB','NOMBRE_CODIGO_CONTABLE_FDCB')");
 	//xGetElementById("IMG_AGREGAR_TIPO_CUENTA_FDCB").setAttribute('onclick',"VentanaNueva('VENTANA_TIPOS_DE_CUENTA','DEFINICIONES DE TIPOS DE CUENTA',700,410,'../modulo_banco/Form_DEFINICIONES_TIPOS_DE_CUENTA.php',true);");
	xGetElementById("FECHA_APERTURA_FDCB").setAttribute('ondblclick',"showCalendar('FECHA_APERTURA_FDCB','%d/%m/%Y')");
	xGetElementById("IMG_FECHA_APERTURA_FDCB").setAttribute('onclick',"showCalendar('FECHA_APERTURA_FDCB','%d/%m/%Y')");

	//ActivarBoton("IMG_AGREGAR_TIPO_CUENTA_FDCB","IMG_AGREGAR_TIPO_CUENTA_FDCB",'agregar');
	ActivarBoton("IMG_BUSCAR_CODIGO_CONTABLE_FDCB","IMG_BUSCAR_CODIGO_CONTABLE_FDCB",'buscar');
	ActivarBoton("IMG_BUSCAR_CODIGO_BANCO_FDCB","IMG_BUSCAR_CODIGO_BANCO_FDCB",'buscar');
	//ActivarBoton("IMG_AGREGAR_CODIGO_BANCO_FDCB","IMG_AGREGAR_CODIGO_BANCO_FDCB",'agregar');
	ActivarBoton("IMG_FECHA_APERTURA_FDCB","IMG_FECHA_APERTURA_FDCB",'calendario');

	xGetElementById("NUMERO_CUENTA_FDCB").readOnly=false;
	xGetElementById("DESCRIPCION_FDCB").readOnly=false;
	xGetElementById("CODIGO_BANCO_FDCB").readOnly=false;
	xGetElementById("TIPO_CUENTA_FDCB").readOnly=false;
	xGetElementById("TIPO_CUENTA_FDCB").disabled=false;
	xGetElementById("CODIGO_CONTABLE_FDCB").readOnly=false;
	xGetElementById("FECHA_APERTURA_FDCB").readOnly=false;
	xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].disabled=false;
	xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[1].disabled=false;

	xGetElementById("NUMERO_CUENTA_FDCB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DESCRIPCION_FDCB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("TIPO_CUENTA_FDCB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("FECHA_APERTURA_FDCB").setAttribute('class','TextoCampoInputObligatorios');

	//Si es para nuevo el campo Fecha de cierre debe estar desactivado
	if(Nuevo){
		xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].checked=true;
		xGetElementById("FECHA_CIERRE_FDCB").readOnly=true;
		xGetElementById("IMG_FECHA_CIERRE_FDCB").setAttribute('onclick',"");
		xGetElementById("FECHA_CIERRE_FDCB").setAttribute('ondblclick',"");
		xGetElementById("FECHA_CIERRE_FDCB").setAttribute('class','TextoCampoInputDesactivado');
		DesactivarBoton("IMG_FECHA_CIERRE_FDCB","IMG_FECHA_CIERRE_FDCB",'calendario');
		}
	//Si es modificar debe activar este campo
	else{
		xGetElementById("FECHA_CIERRE_FDCB").readOnly=false;
		xGetElementById("IMG_FECHA_CIERRE_FDCB").setAttribute('onclick',"showCalendar('FECHA_CIERRE_FDCB','%d/%m/%Y')");
		xGetElementById("FECHA_CIERRE_FDCB").setAttribute('ondblclick',"showCalendar('FECHA_CIERRE_FDCB','%d/%m/%Y')");
		xGetElementById("FECHA_CIERRE_FDCB").setAttribute('class','TextoCampoInputObligatorios');
		ActivarBoton("IMG_FECHA_CIERRE_FDCB","IMG_FECHA_CIERRE_FDCB",'calendario');
		}
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarFormulario(){
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('ondblclick',"");
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('onkeypress',"");
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('onblur',"");
	xGetElementById("IMG_BUSCAR_CODIGO_BANCO_FDCB").setAttribute('onclick',"");
	//xGetElementById("IMG_AGREGAR_CODIGO_BANCO_FDCB").setAttribute('onclick',"");

	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('onkeypress',"");
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('onblur',"");
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('ondblclick',"");
	xGetElementById("IMG_BUSCAR_CODIGO_CONTABLE_FDCB").setAttribute('onclick',"");

	//xGetElementById("IMG_AGREGAR_TIPO_CUENTA_FDCB").setAttribute('onclick',"");
	xGetElementById("IMG_FECHA_APERTURA_FDCB").setAttribute('onclick',"");
	xGetElementById("IMG_FECHA_CIERRE_FDCB").setAttribute('onclick',"");
	xGetElementById("FECHA_CIERRE_FDCB").setAttribute('ondblclick',"");
	xGetElementById("FECHA_APERTURA_FDCB").setAttribute('ondblclick',"");

	//DesactivarBoton("IMG_AGREGAR_TIPO_CUENTA_FDCB","IMG_AGREGAR_TIPO_CUENTA_FDCB",'agregar');
	DesactivarBoton("IMG_BUSCAR_CODIGO_CONTABLE_FDCB","IMG_BUSCAR_CODIGO_CONTABLE_FDCB",'buscar');
	DesactivarBoton("IMG_BUSCAR_CODIGO_BANCO_FDCB","IMG_BUSCAR_CODIGO_BANCO_FDCB",'buscar');
	//DesactivarBoton("IMG_AGREGAR_CODIGO_BANCO_FDCB","IMG_AGREGAR_CODIGO_BANCO_FDCB",'agregar');
	DesactivarBoton("IMG_FECHA_APERTURA_FDCB","IMG_FECHA_APERTURA_FDCB",'calendario');
	DesactivarBoton("IMG_FECHA_CIERRE_FDCB","IMG_FECHA_CIERRE_FDCB",'calendario');

	xGetElementById("NUMERO_CUENTA_FDCB").readOnly=true;
	xGetElementById("DESCRIPCION_FDCB").readOnly=true;
	xGetElementById("CODIGO_BANCO_FDCB").readOnly=true;
	xGetElementById("TIPO_CUENTA_FDCB").readOnly=true;
	xGetElementById("TIPO_CUENTA_FDCB").disabled=true;
	xGetElementById("CODIGO_CONTABLE_FDCB").readOnly=true;
	xGetElementById("FECHA_APERTURA_FDCB").readOnly=true;
	xGetElementById("FECHA_CIERRE_FDCB").readOnly=true;
	xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].disabled=true;
	xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[1].disabled=true;

	xGetElementById("NUMERO_CUENTA_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DESCRIPCION_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CODIGO_BANCO_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TIPO_CUENTA_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CODIGO_CONTABLE_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_APERTURA_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FECHA_CIERRE_FDCB").setAttribute('class','TextoCampoInputDesactivado');
	}

/**
* Activa el boton modificar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FDCB","IMG_MODIFICAR_FDCB",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FDCB","IMG_MODIFICAR_FDCB",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FDCB","IMG_GUARDAR_FDCB",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FDCB","IMG_GUARDAR_FDCB",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FDCB","IMG_ELIMINAR_FDCB",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FDCB","IMG_ELIMINAR_FDCB",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del Tipo de cuenta, para mostralo en el listado en caso de que estee eliminado*/
var Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista=-1;

/*Al seleccionar un elemento de la lista, necesitamos saber el ID del banco, para mostralo en el listado en caso de que estee eliminado*/
var Form_DEFINICIONES_CUENTAS_BANCARIAS__IDBancoSeleccionActualLista=-1;

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado_CadenaBuscar="";

/**
* Nueva definicion
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__Nuevo(){
	Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectTipoCta();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__LimpiarInputTextBuscarListado();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__TabPane.setSelectedIndex(0);
	DarFocoCampo("NUMERO_CUENTA_FDCB",1000);
	}

/*Actualiza el select Tipo Cuenta, es llamado desde el formulario DEFINICIONES_TIPOS_DE_CUENTA, al agregar o al eliminar*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActualizarSelectTipoCuenta(){
	if(xGetElementById("TIPO_CUENTA_FDCB"))
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectTipoCta();
	}

/*Carga el listado de tipos de cuenta en el select*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectTipoCta(){
	AjaxRequest.post({'parameters':{ 'action':"onList",
									'start':0,
									'limit':"ALL",
									'sort':'[{"property":"denominacion","direction":"ASC"}]',
									'Modificar':Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista},
					 'onSuccess': Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectMostrarTipoCta,
					 'url':'../banco_cuenta_tipo/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/*Despues que cargar lo tipos de cuenta, los mostramos en el select*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectMostrarTipoCta(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");
    resultado=resultado["result"];
	var SelectTipoCta = xGetElementById("TIPO_CUENTA_FDCB");
	SelectTipoCta.innerHTML="";
	var opcion;
	//Cuando es nuevo, sale por defecto SELECCIONE | AGREGE
	if(Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista==-1){
		opcion = mD.agregaNodoElemento("option", null, null, { 'value':"" } );
		opcion.innerHTML="SELECCIONE";
		mD.agregaHijo(SelectTipoCta, opcion);
		for(var i=0; i<resultado.length; i++){
			opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id'] } );
			opcion.innerHTML=resultado[i]['denominacion'];
			mD.agregaHijo(SelectTipoCta, opcion);
			}
		}
	//Cuando es modificar, sale por defecto el guardado
	else{
		for(var i=0; i<resultado.length; i++){
			if(Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista==resultado[i]['id'])
				opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id'], 'selected':true} );
			else
				opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i]['id']} );
			opcion.innerHTML=resultado[i]['denominacion'];
			mD.agregaHijo(SelectTipoCta, opcion);
			}
		}
	}


/*Carga la descripcion del codigo contable introducido por el teclado, cuando pierde el foco, presionamos enter, ...*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarCuentaContable(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDCB").value));
	if(CadenaBuscar==""){
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value="";
		return;
		}
	//Si no es numero salimos
	if(isNaN(CadenaBuscar)){
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value="CÓDIGO NO ENCONTRADO";
		return;
		}

	AjaxRequest.post({'parameters':{ 'action':"onGet",
									'id_cuenta_contable':CadenaBuscar},
					 'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__PostCargaCuentaContable,
					 'url':'../cuenta_contable/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/*Al cargar la descripcion del codigo, mostramos la correspondiente descripcion*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__PostCargaCuentaContable(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");

	if(resultado.length==1){
		xGetElementById("CODIGO_CONTABLE_FDCB").value=resultado[0]['id_cuenta_contable'];
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value=resultado[0]['denominacion'];
		}
	else{//Codigo invalido
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value="CÓDIGO NO ENCONTRADO";
		}
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__PresionarEnterCodigoCuentaContable(ev){
 	if(ev.keyCode==13)
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarCuentaContable();
	}

/*Es llamada desde la ventana DEFINICIONES DE BANCO, al guardar o eliminar*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__ActualizarBanco(){
	if(xGetElementById("CODIGO_BANCO_FDCB"))
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarBanco();
	}

/*Abre la ventana listado de bancos, al hacer doble click sobre el INPUT TEXT o en el icono de buscar*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__AbrirVentanaListaBancos(){
	Form_LISTA_DE_BANCOS__Abrir('CODIGO_BANCO_FDCB','BANCO_FDCB',Form_DEFINICIONES_CUENTAS_BANCARIAS__IDBancoSeleccionActualLista);
	}

/*Carga el banco introducido por el teclado, cuando pierde el foco, presionamos enter, ...*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarBanco(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("CODIGO_BANCO_FDCB").value));
	if(CadenaBuscar==""){
		xGetElementById("BANCO_FDCB").value="";
		return;
		}
	//Si no es numero salimos
	if(isNaN(CadenaBuscar)){
		xGetElementById("BANCO_FDCB").value="CÓDIGO NO ENCONTRADO";
		return;
		}
	else
		CadenaBuscar=quitarCodigoCeros(CadenaBuscar);

	AjaxRequest.post({'parameters':{ 'action':"onGet",
									'id':CadenaBuscar},
					 'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__PostCargaBanco,
					 'url':'../banco/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/*Muestra el nombre del banco en el INPUT TEXT*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__PostCargaBanco(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");

	if(resultado.length==1){
		xGetElementById("CODIGO_BANCO_FDCB").value=resultado[0]['correlativo'];
		xGetElementById("BANCO_FDCB").value=resultado[0]['banco'];
		}
	else{//Codigo invalido
		xGetElementById("BANCO_FDCB").value="CÓDIGO NO ENCONTRADO";
		}
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__PresionarEnterCodigoBanco(ev){
 	if(ev.keyCode==13)
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarBanco();
	}

/*Verifica que los campos obligatorios esten llenos y la existencia (duplicidad) antes de guardar*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__GuardarVerificar(){
	/*Se verifica que los campos obligatorio esten llenos*/
	Form_DEFINICIONES_CUENTAS_BANCARIAS__TabPane.setSelectedIndex(0);
	var NumeroCuenta 				= xTrim(strtoupper(xGetElementById("NUMERO_CUENTA_FDCB").value));
	var DescripcionCuenta 			= xTrim(strtoupper(xGetElementById("DESCRIPCION_FDCB").value));
	var CodigoBanco 				= xTrim(strtoupper(xGetElementById("CODIGO_BANCO_FDCB").value));
	var NombreBanco 				= xTrim(strtoupper(xGetElementById("BANCO_FDCB").value));
	var TipoCuenta 					= xGetElementById("TIPO_CUENTA_FDCB").value;
	var CodigoContable 				= xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDCB").value));
	var DescripcionCodigoContable 	= xTrim(strtoupper(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value));
	var FechaApertura 				= xTrim(strtoupper(xGetElementById("FECHA_APERTURA_FDCB").value));
	var FechaCierre 				= xTrim(strtoupper(xGetElementById("FECHA_CIERRE_FDCB").value));
	var EstadoCuenta 				= xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].checked;

	if(!NumeroCuenta){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor introduzca el número de cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!DescripcionCuenta){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor introduzca una breve descripción para esta cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!CodigoBanco){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el banco asociado a la cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(NombreBanco=="CÓDIGO NO ENCONTRADO"){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el banco asociado a la cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!TipoCuenta){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el tipo de cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!CodigoContable){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el código contable asociado a la cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(DescripcionCodigoContable=="CÓDIGO NO ENCONTRADO"){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el código contable asociado a la cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!FechaApertura){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor introduzca la fecha de apertura de la cuenta.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!EsFechaValida(FechaApertura)){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("La fecha de apertura es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(FechaCierre)
		if(!EsFechaValida(FechaCierre)){
			Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("La fecha de cierre es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
			Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
			return;
			}

	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarFormulario();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__Guardar();
	/*AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_CUENTAS_BANCARIAS__Existe",
									'id_cta_bancaria':Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista,
									'numero_cta_bancaria':NumeroCuenta},
					 'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__Guardar,
					 'url':'../modulo_banco/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });*/
	}

/**
* Guarda los datos en la BD
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__Guardar(req){
//	var respuesta = req.responseText;
//	var resultado = eval("(" + respuesta + ")");
//
//	//si el numero de cuenta introducido ya existe en la BD
//	if(resultado[0]['count']>=1){
//		Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario(false);
//		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("No se puedo guardar los datos. Ya existe una cuenta con el mismo número.","ROJO");
//		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
//		return;
//		}
//
	var NumeroCuenta 				= xTrim(strtoupper(xGetElementById("NUMERO_CUENTA_FDCB").value));
	var DescripcionCuenta 			= xTrim(strtoupper(xGetElementById("DESCRIPCION_FDCB").value));
	var CodigoBanco 				= xTrim(strtoupper(xGetElementById("CODIGO_BANCO_FDCB").value));
	var NombreBanco 				= xTrim(strtoupper(xGetElementById("BANCO_FDCB").value));
	var TipoCuenta 					= xGetElementById("TIPO_CUENTA_FDCB").value;
	var CodigoContable 				= xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDCB").value));
	var DescripcionCodigoContable 	= xTrim(strtoupper(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDCB").value));
	var FechaApertura 				= xTrim(strtoupper(xGetElementById("FECHA_APERTURA_FDCB").value));
	var FechaCierre 				= xTrim(strtoupper(xGetElementById("FECHA_CIERRE_FDCB").value));
	var EstadoCuenta 				=xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].checked;

	if(FechaApertura){
		if(!EsFechaValida(FechaApertura)){
			Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("La fecha de apertura es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
			Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
			return;
		}
		FechaApertura=DesFormatearFecha(FechaApertura);
	}

	if(FechaCierre){
		if(!EsFechaValida(FechaCierre)){
			Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("La fecha de cierre es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
			Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
			return;
		}
		FechaCierre=DesFormatearFecha(FechaCierre);
	}

	/*Si es guardar nuevo*/
	if(Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista==-1){
		AjaxRequest.post({'parameters':{ 'action':"onSave",
										'numero_cuenta':NumeroCuenta,
										'denominacion':DescripcionCuenta,
										'fecha_apertura':FechaApertura,
										'fecha_cierre':FechaCierre,
										'cuenta_activa':EstadoCuenta,
										'id_banco':quitarCodigoCeros(CodigoBanco),
										'id_banco_cuenta_tipo':TipoCuenta,
										'id_cuenta_contable':CodigoContable },
						'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__GuardarMensaje,
						'url':'../banco_cuenta/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	/*Si es modificar*/
	else{
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario(false);
			return;
			}
		AjaxRequest.post({'parameters':{ 'action':"onSave",
										'id':Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista,
										'numero_cuenta':NumeroCuenta,
										'denominacion':DescripcionCuenta,
										'fecha_apertura':FechaApertura,
										'fecha_cierre':FechaCierre,
										'cuenta_activa':EstadoCuenta,
										'id_banco':quitarCodigoCeros(CodigoBanco),
										'id_banco_cuenta_tipo':TipoCuenta,
										'id_cuenta_contable':CodigoContable },
						'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__GuardarMensaje,
						'url':'../banco_cuenta/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__GuardarMensaje(req){
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario(false);
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Nuevo();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje(respuesta.message,"ROJO");
	}

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado(){
	Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista=-1;
	xGetElementById("FORMULARIO_FDCB").reset();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario(true);
	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonModificar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonEliminar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDCB").value));
	if(CadenaBuscar!="")
		if(Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado_CadenaBuscar=CadenaBuscar;

	//si la cadena es numerica. Eliminamos los ceros a la izquierda.
	if(CadenaBuscar.length!=0)
		if(isNaN(CadenaBuscar)==false)
			CadenaBuscar=quitarCodigoCeros(CadenaBuscar);

	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':CadenaBuscar,
									'filtro': '%',
									'start':0,
									'limit':"ALL",
									'sort':'[{"property":"correlativo","direction":"ASC"}]'},
					 'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__MostrarListado,
					 'url':'../banco_cuenta/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;

	//Si hay mas de 1000 registros Desactivar Busqueda rapida y resaldado en las coincidencias.
	if(n>1000){
		//xGetElementById("SOMBRA_CHECKBOX_FDCB").checked=false;
		//xGetElementById("BUSCAR_CHECKBOX_FDCB").checked=true;
		}

	var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDCB").value)));
	var EstadoCheckBoxSombra=true;//xGetElementById("SOMBRA_CHECKBOX_FDCB").checked;

	xGetElementById("TABLA_LISTA_FDCB").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__SeleccionarElementoTabla('"
					+resultado[i]['id']+"','"
					+resultado[i]['numero_cuenta']+"','"
					+resultado[i]['denominacion']+"','"
					+resultado[i]['fecha_apertura']+"','"
					+resultado[i]['fecha_cierre']+"','"
					+resultado[i]['cuenta_activa']+"','"
					+resultado[i]['id_banco']+"','"
					+resultado[i]['id_banco_cuenta_tipo']+"','"
					+resultado[i]['id_cuenta_contable']+"')";
		FuncionOnDblclick="Form_DEFINICIONES_CUENTAS_BANCARIAS__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FDCB"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FDCB"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FDCB"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		if(n!=1)
			Contenido+="<TD width='5%' class='FilaEstilo'>"+resultado[i]['correlativo']+ "</TD>";
		else
			Contenido+="<TD width='5%' class='FilaEstilo'><strong>"+resultado[i]['correlativo']+ "</strong></TD>";
		/*if(TextoBuscar!="" && EstadoCheckBoxSombra){
			CadAux1=str_replace(strtoupper(resultado[i]['nombre_banco']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(strtoupper(resultado[i]['direccion_banco']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux1=strtoupper(resultado[i]['nombre_banco']);
			CadAux2=strtoupper(resultado[i]['direccion_banco']);
			}*/

		CadAux1=str_replace(strtoupper(resultado[i]['banco']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['numero_cuenta']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux3=str_replace(strtoupper(resultado[i]['tipo']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux4=str_replace(strtoupper(resultado[i]['denominacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='23%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='20%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='12%' class='FilaEstilo'>"+CadAux3+"</TD>";
		Contenido+="<TD width='30%' class='FilaEstilo'>"+CadAux4+"</TD>";
		Contenido+="<TD width='7%'  class='FilaEstilo'>"+(resultado[i]['cuenta_activa']=='t'?"ACTIVA":"INACTIVA")+"</TD>";
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FDCB").innerHTML=Contenido;
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
function Form_DEFINICIONES_CUENTAS_BANCARIAS__SeleccionarElementoTabla(IDSeleccion, NCuenta, Descripcion, FechaApertura, FechaCierre, Estado, IDBanco, IDTipoCuenta, IDCodigoContable){
		if(Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista!=-1)
			xGetElementById("FDCB"+Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista).bgColor=colorFondoTabla;
        colorBase=colorSeleccionTabla;
        xGetElementById("FDCB"+IDSeleccion).bgColor=colorBase;
		Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista=IDSeleccion;
		Form_DEFINICIONES_CUENTAS_BANCARIAS__IDBancoSeleccionActualLista=IDBanco;
		Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista=IDTipoCuenta;

		xGetElementById("NUMERO_CUENTA_FDCB").value=NCuenta;
		xGetElementById("DESCRIPCION_FDCB").value=Descripcion;
		xGetElementById("CODIGO_BANCO_FDCB").value=IDBanco;
		xGetElementById("CODIGO_CONTABLE_FDCB").value=IDCodigoContable;

		var FECHA=FechaApertura.split("-");
		xGetElementById("FECHA_APERTURA_FDCB").value=FECHA[2]+"/"+FECHA[1]+"/"+FECHA[0];
 		if(FechaCierre=="null")
			xGetElementById("FECHA_CIERRE_FDCB").value="";
		else{
			FECHA=FechaCierre.split("-");
			xGetElementById("FECHA_CIERRE_FDCB").value=FECHA[2]+"/"+FECHA[1]+"/"+FECHA[0];
			}

		if(Estado=='t')
			xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[0].checked=true;
		else
			xGetElementById("FORMULARIO_FDCB").ESTADO_FDCB[1].checked=true;

		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarBanco();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarSelectTipoCta();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__CargarCuentaContable();


		Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarFormulario();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonModificar();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonEliminar();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonGuardar();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__LimpiarInputTextBuscarListado(){
	Form_DEFINICIONES_CUENTAS_BANCARIAS__IDBancoSeleccionActualLista=-1;
	Form_DEFINICIONES_CUENTAS_BANCARIAS__IDTipoCuentaSeleccionActualLista=-1;
	Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista=-1;
	Form_DEFINICIONES_CUENTAS_BANCARIAS__Denominacion="";
	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonModificar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonEliminar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonGuardar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario();
	xGetElementById("FORMULARIO_FDCB").reset();
	xGetElementById("LISTADO_BUSCAR_FDCB").value="";
	Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("");
	Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
	Form_DEFINICIONES_CUENTAS_BANCARIAS__BuscarListado("");
	DarFocoCampo("LISTADO_BUSCAR_FDCB",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__Modificar(){
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarFormulario();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__ActivarBotonGuardar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__DesactivarBotonModificar();
	Form_DEFINICIONES_CUENTAS_BANCARIAS__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	AjaxRequest.post({'parameters':{ 'action':"onDelete",
									'id':Form_DEFINICIONES_CUENTAS_BANCARIAS__IDSeleccionActualLista},
					 'onSuccess':Form_DEFINICIONES_CUENTAS_BANCARIAS__EliminarMensaje,
					 'url':'../banco_cuenta/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_CUENTAS_BANCARIAS__EliminarMensaje(req){
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_DEFINICIONES_CUENTAS_BANCARIAS__LimpiarInputTextBuscarListado();
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		}
	}
