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
function Form_DEFINICIONES_DEDUCCIONES__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";	
	xGetElementById("MSG_FDD").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_DEFINICIONES_DEDUCCIONES__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FDD_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario(){
	xGetElementById("DENOMINACION_FDD").readOnly=false;
	xGetElementById("CODIGO_CONTABLE_FDD").readOnly=false;
	xGetElementById("DEDUCIBLE_FDD").readOnly=false;
	xGetElementById("FORMULA_FDD").readOnly=false;	

	xGetElementById("DENOMINACION_FDD").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DEDUCIBLE_FDD").setAttribute('class','TextoCampoInput');
	xGetElementById("FORMULA_FDD").setAttribute('class','TextoCampoInputObligatorios');	
	
	xGetElementById("DEDUCIBLE_FDD").setAttribute('onblur',"Form_DEFINICIONES_DEDUCCIONES__PierdeFoco('DEDUCIBLE_FDD')");
	xGetElementById("DEDUCIBLE_FDD").setAttribute('onclick',"Form_DEFINICIONES_DEDUCCIONES__TomaFoco('DEDUCIBLE_FDD')");
		
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('onkeypress',"Form_DEFINICIONES_DEDUCCIONES__PresionarEnterCodigoCuentaContable(event)");
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('onblur',"Form_DEFINICIONES_DEDUCCIONES__CargarCuentaContable()");
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('ondblclick',"Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FDD','NOMBRE_CODIGO_CONTABLE_FDD')");
	
	xGetElementById("IMG_BUSCAR_CODIGO_CONTABLE_FDD").setAttribute('onclick',"Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FDD','NOMBRE_CODIGO_CONTABLE_FDD')");
	
	ActivarBoton("IMG_BUSCAR_CODIGO_CONTABLE_FDD","IMG_BUSCAR_CODIGO_CONTABLE_FDD",'buscar');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_DEDUCCIONES__DesactivarFormulario(){
	xGetElementById("DENOMINACION_FDD").readOnly=true;
	xGetElementById("CODIGO_CONTABLE_FDD").readOnly=true;
	xGetElementById("DEDUCIBLE_FDD").readOnly=true;
	xGetElementById("FORMULA_FDD").readOnly=true;	

	xGetElementById("DENOMINACION_FDD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DEDUCIBLE_FDD").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FORMULA_FDD").setAttribute('class','TextoCampoInputDesactivado');
	
	xGetElementById("DEDUCIBLE_FDD").setAttribute('onblur',"");
	xGetElementById("DEDUCIBLE_FDD").setAttribute('onclick',"");
	
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('onkeypress',"");
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('onblur',"");
	xGetElementById("CODIGO_CONTABLE_FDD").setAttribute('ondblclick',"");
	
	xGetElementById("IMG_BUSCAR_CODIGO_CONTABLE_FDD").setAttribute('onclick',"");
	
	DesactivarBoton("IMG_BUSCAR_CODIGO_CONTABLE_FDD","IMG_BUSCAR_CODIGO_CONTABLE_FDD",'buscar');
	}

/**
* Activa el boton modificar
*/
function Form_DEFINICIONES_DEDUCCIONES__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FDD","IMG_MODIFICAR_FDD",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FDD","IMG_MODIFICAR_FDD",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_DEFINICIONES_DEDUCCIONES__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FDD","IMG_GUARDAR_FDD",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FDD","IMG_GUARDAR_FDD",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_DEFINICIONES_DEDUCCIONES__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FDD","IMG_ELIMINAR_FDD",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FDD","IMG_ELIMINAR_FDD",'eliminar');
	}

	
function Form_DEFINICIONES_DEDUCCIONES__PierdeFoco(ID){
	if(xTrim(strtoupper(xGetElementById(ID).value))=="")
		xGetElementById(ID).value=Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible;
	else if(xTrim(strtoupper(xGetElementById(ID).value))=="0")
		xGetElementById(ID).value=Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible;
	}	

function Form_DEFINICIONES_DEDUCCIONES__TomaFoco(ID){
	if(xTrim(strtoupper(xGetElementById(ID).value))==Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible)
		xGetElementById(ID).value="";		
	}
	
/*Carga la descripcion del codigo contable introducido por el teclado, cuando pierde el foco, presionamos enter, ...*/
function Form_DEFINICIONES_DEDUCCIONES__CargarCuentaContable(){	
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDD").value));
	if(CadenaBuscar==""){
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDD").value="";
		return;
		}
	//Si no es numero salimos
	if(isNaN(CadenaBuscar)){
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDD").value="CÓDIGO NO ENCONTRADO";
		return;
		}

	AjaxRequest.post({'parameters':{ 'accion':"Form_LISTA_CUENTAS_CONTABLES__BuscarListado", 
									'CadenaBuscar':CadenaBuscar},
					 'onSuccess':Form_DEFINICIONES_DEDUCCIONES__PostCargaCuentaContable,
					 'url':'../modulo_cuentas/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/*Al cargar la descripcion del codigo, mostramos la correspondiente descripcion*/
function Form_DEFINICIONES_DEDUCCIONES__PostCargaCuentaContable(req){
	var respuesta = req.responseText;
    var resultado = eval("(" + respuesta + ")");
	
	if(resultado.length==1){
		xGetElementById("CODIGO_CONTABLE_FDD").value=resultado[0]['id_codigo_contable'];
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDD").value=resultado[0]['denominacion_cta_contable'];
		}
	else{//Codigo invalido
		xGetElementById("NOMBRE_CODIGO_CONTABLE_FDD").value="CÓDIGO NO ENCONTRADO";
		}	
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_DEFINICIONES_DEDUCCIONES__PresionarEnterCodigoCuentaContable(ev){ 
 	if(ev.keyCode==13)
		Form_DEFINICIONES_DEDUCCIONES__CargarCuentaContable();
	}
	
	
	
/*Indica el id_deduccion que se tiene seleccionado actualmente en el listado de bancos. Necesario para eliminar y para modificar*/
var Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista=-1;

var Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible="0.00";



/**
* Nueva definicion
*/
function Form_DEFINICIONES_DEDUCCIONES__Nuevo(){
	Form_DEFINICIONES_DEDUCCIONES__LimpiarInputTextBuscarListado();
	Form_DEFINICIONES_DEDUCCIONES__TabPane.setSelectedIndex(0);
	DarFocoCampo("DENOMINACION_FDD",1000);
	}




/**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
function Form_DEFINICIONES_DEDUCCIONES__GuardarVerificar(){
	Form_DEFINICIONES_DEDUCCIONES__TabPane.setSelectedIndex(0);
	var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDD").value));
	var codigo_contable = xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDD").value));
	var DescripcionCodigoContable 	= xTrim(strtoupper(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDD").value));
	var deducible = xTrim(strtoupper(xGetElementById("DEDUCIBLE_FDD").value));
	var formula = xTrim(strtoupper(xGetElementById("FORMULA_FDD").value));
	
	
	if(!denominacion){
		var msg="Por favor introduzca la denominación para la dedución."
		Form_DEFINICIONES_DEDUCCIONES__Mensaje(msg,"ROJO");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
		return;
		}	
	if(!codigo_contable){
		var msg="Por favor selecione el código contable asociado a la deducción."
		Form_DEFINICIONES_DEDUCCIONES__Mensaje(msg,"ROJO");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
		return;
		}
	if(DescripcionCodigoContable=="CÓDIGO NO ENCONTRADO"){		
		Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el código contable asociado al cargo.","ROJO");
		Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
		return;
		}
	if(!formula){
		var msg="Por favor introduzca la fórmula asociada a la deducción."
		Form_DEFINICIONES_DEDUCCIONES__Mensaje(msg,"ROJO");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
		return;
		}
	Form_DEFINICIONES_DEDUCCIONES__EsValidaGuardarFormula();
		

	Form_DEFINICIONES_DEDUCCIONES__DesactivarFormulario();
	var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDD").value));
	AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_DEDUCCIONES__Existe",
									'id_deduccion':Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista,
									'denominacion_deduccion':denominacion},
					 'onSuccess':Form_DEFINICIONES_DEDUCCIONES__Guardar,
					 'url':'../modulo_cuentas_por_pagar/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });	
	}

/**
* Guarda los datos en la BD
*/
function Form_DEFINICIONES_DEDUCCIONES__Guardar(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");	
	var msg="";

	//Si ya existe un tipo de cuenta con el mismo nombre. No guardar.
	if(resultado[0]['count']>=1){
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("No se puedo guardar los datos. Ya existe una deducción con la misma denominación","ROJO");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
		Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
		return;
		}

	var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDD").value));
	var codigo_contable = xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDD").value));
	var deducible = xTrim(strtoupper(xGetElementById("DEDUCIBLE_FDD").value));
	var formula = xTrim(strtoupper(xGetElementById("FORMULA_FDD").value));
		
	
	/*Si es guardar nuevo*/
	if(Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista==-1){
		AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_DEDUCCIONES__Guardar",
						'denominacion_deduccion':denominacion,
	  					'id_codigo_contable':codigo_contable,
						'monto_deducible':deducible,
						'formula_deduccion':formula},
						'onSuccess':Form_DEFINICIONES_DEDUCCIONES__GuardarMensaje,
						'url':'../modulo_cuentas_por_pagar/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	/*Si es modificar*/
	else{
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
			return;
			}
		AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_DEDUCCIONES__Modificar",
										'id_deduccion':Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista,
										'denominacion_deduccion':denominacion,
										'id_codigo_contable':codigo_contable,
										'monto_deducible':deducible,
										'formula_deduccion':formula},
						'onSuccess':Form_DEFINICIONES_DEDUCCIONES__GuardarMensaje,
						'url':'../modulo_cuentas_por_pagar/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});		
		}
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_DEDUCCIONES__GuardarMensaje(req){
	Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
	var respuesta = req.responseText;
	if(respuesta==1){		
		Form_DEFINICIONES_DEDUCCIONES__Nuevo();
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("Los datos se guadaron satisfactoriamente.","VERDE");
		}
	else
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("Error. No se pudo guardar los datos. Vuelva a intentarlo.","ROJO");
	}





/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_DEFINICIONES_DEDUCCIONES__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_DEFINICIONES_DEDUCCIONES__BuscarListado(){
	Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista=-1;
	xGetElementById("FORMULARIO_FDD").reset();
	Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
	Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonModificar();
	Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonEliminar();
	Form_DEFINICIONES_DEDUCCIONES__ActivarBotonGuardar();
	xGetElementById("DEDUCIBLE_FDD").value=Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible;
	
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDD").value));

	if(CadenaBuscar!="")
		if(Form_DEFINICIONES_DEDUCCIONES__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_DEFINICIONES_DEDUCCIONES__BuscarListado_CadenaBuscar=CadenaBuscar;


	//si la cadena es numerica. Eliminamos los ceros a la izquierda.
	if(CadenaBuscar.length!=0)
		if(isNaN(CadenaBuscar)==false)
			CadenaBuscar=quitarCodigoCeros(CadenaBuscar);

	AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_DEDUCCIONES__BuscarListado",
									'CadenaBuscar':CadenaBuscar},
					 'onSuccess':Form_DEFINICIONES_DEDUCCIONES__MostrarListado,
					 'url':'../modulo_cuentas_por_pagar/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_DEFINICIONES_DEDUCCIONES__MostrarListado(req){
	var respuesta = req.responseText;//alert(respuesta);return;
	var resultado = eval("(" + respuesta + ")");
	var n=resultado.length;


	var tablaPrueba = xGetElementById("TABLA_LISTA_FDD");
	limpiarTabla("TABLA_LISTA_FDD");

	var CadAux1, CadAux2;
	//var NDigitos=NDigitosCodigo(resultado,'id_deduccion');
	var NDigitos=3;
	for(var i=0; i<n; i++){
		var col0 = mD.agregaNodoElemento('td', null, null,{'width':'5%','class':'FilaEstilo'});
		var col1 = mD.agregaNodoElemento('td', null, null,{'width':'35%','class':'FilaEstilo'});
		var col2 = mD.agregaNodoElemento('td', null, null,{'width':'35%','class':'FilaEstilo'});
		var col3 = mD.agregaNodoElemento('td', null, null,{'width':'25%','class':'FilaEstilo'});
		
		var fila = mD.agregaNodoElemento('tr', null,"FDD"+resultado[i]['id_deduccion'] ,{'onclick':"Form_DEFINICIONES_DEDUCCIONES__SeleccionarElementoTabla('"+resultado[i]['id_deduccion']+"','"+resultado[i]['denominacion_deduccion']+"','"+resultado[i]['id_codigo_contable']+"','"+resultado[i]['monto_deducible']+"','"+resultado[i]['formula_deduccion']+"')",'onmouseover':"pintarFila('FDD"+resultado[i]['id_deduccion']+"')",'onmouseout':"despintarFila('FDD"+resultado[i]['id_deduccion']+"')",'ondblclick':"Form_DEFINICIONES_DEDUCCIONES__TabPane.setSelectedIndex(0);",'bgColor':colorFondoTabla});

		CadAux2=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDD").value));

		if(n==1 && CadAux2)
			CadAux1="<strong>"+completarCodigoCeros(resultado[i]['id_deduccion'],NDigitos)+"</strong>";
		else
			CadAux1=completarCodigoCeros(resultado[i]['id_deduccion'],NDigitos);

		col0.innerHTML=CadAux1;
		col1.innerHTML=str_replace(strtoupper(resultado[i]['denominacion_deduccion']),"<strong>"+CadAux2+"</strong>",CadAux2);		
			
		col2.innerHTML=resultado[i]['formula_deduccion'];				
		col3.innerHTML="<DIV align='right'>"+FormatearNumero(resultado[i]['monto_deducible'])+"</DIV>";
		
		
		fila.appendChild(col0);
		fila.appendChild(col1);
		fila.appendChild(col2);
		fila.appendChild(col3);
		
		tablaPrueba.appendChild(fila);
		}
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
function Form_DEFINICIONES_DEDUCCIONES__SeleccionarElementoTabla(IDSeleccion, Denominacion, IDCodigoContable, Deducible, Formula){
		Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista=IDSeleccion;
		restaurarColorTabla("TABLA_LISTA_FDD");
        colorBase=colorSeleccionTabla;
        xGetElementById("FDD"+IDSeleccion).bgColor=colorBase;
		
		xGetElementById("DENOMINACION_FDD").value=Denominacion;
		xGetElementById("CODIGO_CONTABLE_FDD").value=IDCodigoContable;
		xGetElementById("DEDUCIBLE_FDD").value=Deducible;
		xGetElementById("FORMULA_FDD").value=Formula;
	
		Form_DEFINICIONES_DEDUCCIONES__CargarCuentaContable();
		
		Form_DEFINICIONES_DEDUCCIONES__DesactivarFormulario();
		Form_DEFINICIONES_DEDUCCIONES__ActivarBotonModificar();
		Form_DEFINICIONES_DEDUCCIONES__ActivarBotonEliminar();
		Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonGuardar();
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
		}

/**
* Es llamada cuando se presiona sobre el boton limpiar. 
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_DEFINICIONES_DEDUCCIONES__LimpiarInputTextBuscarListado(){
	Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista=-1;	
	Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonModificar();
	Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonEliminar();
	Form_DEFINICIONES_DEDUCCIONES__ActivarBotonGuardar();
	Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
	xGetElementById("FORMULARIO_FDD").reset();
	xGetElementById("DEDUCIBLE_FDD").value=Form_DEFINICIONES_DEDUCCIONES__ValorPorDefectoMontoDeducible;
	xGetElementById("LISTADO_BUSCAR_FDD").value="";
	Form_DEFINICIONES_DEDUCCIONES__Mensaje("");
	Form_DEFINICIONES_DEDUCCIONES__MensajeListado("");
	Form_DEFINICIONES_DEDUCCIONES__BuscarListado("");	
	DarFocoCampo("LISTADO_BUSCAR_FDD",1000);
	}






/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_DEFINICIONES_DEDUCCIONES__Modificar(){
	Form_DEFINICIONES_DEDUCCIONES__ActivarFormulario();
	Form_DEFINICIONES_DEDUCCIONES__ActivarBotonGuardar();
	Form_DEFINICIONES_DEDUCCIONES__DesactivarBotonModificar();
	Form_DEFINICIONES_DEDUCCIONES__TabPane.setSelectedIndex(0);
	}





/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_DEFINICIONES_DEDUCCIONES__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista==-1)
		return;
		
	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({'parameters':{ 'accion':"Form_DEFINICIONES_DEDUCCIONES__Eliminar",
									'id_deduccion':Form_DEFINICIONES_DEDUCCIONES__IDSeleccionActualLista},
					 'onSuccess':Form_DEFINICIONES_DEDUCCIONES__EliminarMensaje,
					 'url':'../modulo_cuentas_por_pagar/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });	
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_DEDUCCIONES__EliminarMensaje(req){
	var respuesta = req.responseText;
	if(respuesta==1){
		Form_DEFINICIONES_DEDUCCIONES__LimpiarInputTextBuscarListado();
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		Form_DEFINICIONES_DEDUCCIONES__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		Form_DEFINICIONES_DEDUCCIONES__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		}	
	}

	
function Form_DEFINICIONES_DEDUCCIONES__ProbarFormula(){	
	var RESULTADO;
	var MONTO=xGetElementById("MONTO_PRUEBA_FDD").value;
	var CadenaEvaluar="RESULTADO=(" + xTrim(strtoupper(xGetElementById("FORMULA_FDD").value)) + ")-" + xTrim(strtoupper(xGetElementById("DEDUCIBLE_FDD").value)) + ";";
	xGetElementById("RESULTADO_PRUEBA_FDD").value="ERROR";	
	eval(CadenaEvaluar);
	
	if(!isNaN(RESULTADO))		
		xGetElementById("RESULTADO_PRUEBA_FDD").value=FormatearNumero(RESULTADO);	
	}

function Form_DEFINICIONES_DEDUCCIONES__EsValidaGuardarFormula(){
	var RESULTADO;
	var MONTO;
	var CadenaEvaluar="RESULTADO=(" + xTrim(strtoupper(xGetElementById("FORMULA_FDD").value)) + ")-" + xTrim(strtoupper(xGetElementById("DEDUCIBLE_FDD").value)) + ";";	
	MONTO=0;	
	Form_DEFINICIONES_DEDUCCIONES__Mensaje("Error en la formula.","ROJO");
	eval(CadenaEvaluar);
	Form_DEFINICIONES_DEDUCCIONES__Mensaje("");	
	} 
	
	