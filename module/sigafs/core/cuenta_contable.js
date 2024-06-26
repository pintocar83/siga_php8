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
function Form_PLAN_CUENTAS_CONTABLES__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FPCC").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_PLAN_CUENTAS_CONTABLES__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FPCC_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario(){
	if(Form_PLAN_CUENTAS_CONTABLES__EsCuentaAgregada=='f'){
		xGetElementById("CODIGO_CC_FPCC").readOnly=true;
		xGetElementById("CODIGO_CC_FPCC").setAttribute('class','TextoCampoInputDesactivado');
		}
	else{
		xGetElementById("CODIGO_CC_FPCC").readOnly=false;
		xGetElementById("CODIGO_CC_FPCC").setAttribute('class','TextoCampoInputObligatorios');
		}

	xGetElementById("DENOMINACION_CC_FPCC").readOnly=false;
	xGetElementById("DENOMINACION_CC_FPCC").setAttribute('class','TextoCampoInputObligatorios');

	//xGetElementById("PADRE_CC_FPCC").readOnly=false;
	//xGetElementById("PADRE_CC_FPCC").disabled=false;
	//xGetElementById("PADRE_CC_FPCC").setAttribute('class','TextoCampoInputObligatorios');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_PLAN_CUENTAS_CONTABLES__DesactivarFormulario(){
	xGetElementById("CODIGO_CC_FPCC").readOnly=true;
	xGetElementById("DENOMINACION_CC_FPCC").readOnly=true;
	//xGetElementById("PADRE_CC_FPCC").readOnly=true;
	//xGetElementById("PADRE_CC_FPCC").disabled=true;

	xGetElementById("CODIGO_CC_FPCC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DENOMINACION_CC_FPCC").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("PADRE_CC_FPCC").setAttribute('class','TextoCampoInputDesactivado');
	}

/**
* Activa el boton modificar
*/
function Form_PLAN_CUENTAS_CONTABLES__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FPCC","IMG_MODIFICAR_FPCC",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FPCC","IMG_MODIFICAR_FPCC",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_PLAN_CUENTAS_CONTABLES__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FPCC","IMG_GUARDAR_FPCC",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FPCC","IMG_GUARDAR_FPCC",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_PLAN_CUENTAS_CONTABLES__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FPCC","IMG_ELIMINAR_FPCC",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FPCC","IMG_ELIMINAR_FPCC",'eliminar');
	}


/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista=-1;
var Form_PLAN_CUENTAS_CONTABLES__EsCuentaAgregada="";

/**
* Nueva definicion
*/
function Form_PLAN_CUENTAS_CONTABLES__Nuevo(){
	Form_PLAN_CUENTAS_CONTABLES__LimpiarInputTextBuscarListado();
	Form_PLAN_CUENTAS_CONTABLES__TabPane.setSelectedIndex(0);
	DarFocoCampo("CODIGO_CC_FPCC",1000);
	}

/**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
function Form_PLAN_CUENTAS_CONTABLES__GuardarVerificar(){
	Form_PLAN_CUENTAS_CONTABLES__TabPane.setSelectedIndex(0);
	var Codigo = xTrim(strtoupper(xGetElementById("CODIGO_CC_FPCC").value));

	if(!Codigo){
		var msg="Por favor introduzca el código contable."
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(msg,"ROJO");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado("");
		return;
		}
	if(Codigo.length!=12){
		var msg="El código contable debe tener 12 caracteres."
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(msg,"ROJO");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado("");
		return;
		}


	Form_PLAN_CUENTAS_CONTABLES__DesactivarFormulario();
	AjaxRequest.post({'parameters':{ 'action':"onExist",'id_cuenta_contable':Codigo},
					 'onSuccess':Form_PLAN_CUENTAS_CONTABLES__Guardar,
					 'url':'../cuenta_contable/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Guarda los datos en la BD
*/
function Form_PLAN_CUENTAS_CONTABLES__Guardar(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");

	var Codigo = xTrim(strtoupper(xGetElementById("CODIGO_CC_FPCC").value));

	//Si ya existe un tipo de cuenta con el mismo numero, verificar si es nuevo o modificar. si es nuevo No guardar pq ya existe, sino se llama a modificar para guardar.
	if(resultado[0]['count']>=1 && Codigo!=Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista){
		Form_PLAN_CUENTAS_CONTABLES__Mensaje("No se puedo guardar los datos. Ya existe una cuenta con el mismo código contable.","ROJO");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado("");
		Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario();
		return;
		}

	var Denominacion 	= xTrim(strtoupper(xGetElementById("DENOMINACION_CC_FPCC").value));
	//var Padre 	= xTrim(xGetElementById("PADRE_CC_FPCC").value);

	/*Si es guardar nuevo*/
	if(Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista==-1){
		AjaxRequest.post({'parameters':{ 'action':"onSave",
										'id_cuenta_contable':Codigo,
										'id_cuenta_contable_seleccion':"",
										'denominacion':Denominacion},
						'onSuccess':Form_PLAN_CUENTAS_CONTABLES__GuardarMensaje,
						'url':'../cuenta_contable/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	/*Si es modificar*/
	else{
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario();
			return;
			}
		AjaxRequest.post({'parameters':{ 'action':"onSave",
										'id_cuenta_contable':Codigo,
										'id_cuenta_contable_seleccion':Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista,
										'denominacion':Denominacion},
						'onSuccess':Form_PLAN_CUENTAS_CONTABLES__GuardarMensaje,
						'url':'../cuenta_contable/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_PLAN_CUENTAS_CONTABLES__GuardarMensaje(req){
	Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario();
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_PLAN_CUENTAS_CONTABLES__Nuevo();
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(respuesta.message,"ROJO");
	}

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_PLAN_CUENTAS_CONTABLES__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_PLAN_CUENTAS_CONTABLES__BuscarListado(){
	Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista=-1;
	Form_PLAN_CUENTAS_CONTABLES__EsCuentaAgregada="";
	xGetElementById("FORMULARIO_PLAN_CUENTAS_CONTABLES").reset();
	Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario();
	Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonModificar();
	Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonEliminar();
	Form_PLAN_CUENTAS_CONTABLES__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FPCC").value));

	if(CadenaBuscar!="")
		if(Form_PLAN_CUENTAS_CONTABLES__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_PLAN_CUENTAS_CONTABLES__BuscarListado_CadenaBuscar=CadenaBuscar;

	xGetElementById("TABLA_LISTA_FPCC").innerHTML=IconoCargandoTabla;

	var Prefijo="";

	var limit="ALL";
	if(xGetElementById("MOSTRAR_FPCC").checked)
		limit="50";

	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':CadenaBuscar,
									'filtro': '%',
									'start':0,
									'limit':limit,
									'sort':'[{"property":"cuenta_contable","direction":"ASC"},{"property":"denominacion","direction":"ASC"}]'
									},
					 'onSuccess':Form_PLAN_CUENTAS_CONTABLES__MostrarListado,
					 'url':'../cuenta_contable/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_PLAN_CUENTAS_CONTABLES__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;

	//var tablaPrueba = xGetElementById("TABLA_LISTA_FPCC");

	var CadAux1, CadAux2;

	var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FPCC").value)));



	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var EstiloFila="";
	for(var i=0;i< n; i++){
		FuncionOnclick="Form_PLAN_CUENTAS_CONTABLES__SeleccionarElementoTabla('"
					+resultado[i]['id_cuenta_contable']+"','"
					//+resultado[i]['denominacion']+"','"
					+resultado[i]['denominacion']+"')";
 		FuncionOnDblclick="Form_PLAN_CUENTAS_CONTABLES__TabPane.setSelectedIndex(0);";

		Contenido+="<TR class='FilaListado' id='FPCC"+resultado[i]['id_cuenta_contable']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		CadAux1=str_replace(strtoupper(resultado[i]['cuenta_contable']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['denominacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='18%'>"+CadAux1+"</TD>";
		Contenido+="<TD>"+CadAux2+"</TD>";


		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FPCC").innerHTML=Contenido;
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
function Form_PLAN_CUENTAS_CONTABLES__SeleccionarElementoTabla(IDSeleccion, Denominacion){
		if(Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista!=-1)
			xGetElementById("FPCC"+Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista).bgColor=colorFondoTabla;
        colorBase=colorSeleccionTabla;
        xGetElementById("FPCC"+IDSeleccion).bgColor=colorBase;
		Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista=IDSeleccion;
		Form_PLAN_CUENTAS_CONTABLES__EsCuentaAgregada='t';

		xGetElementById("CODIGO_CC_FPCC").value=IDSeleccion;
		xGetElementById("DENOMINACION_CC_FPCC").value=Denominacion;
		//xGetElementById("PADRE_CC_FPCC").value=Padre;

		Form_PLAN_CUENTAS_CONTABLES__DesactivarFormulario();
		Form_PLAN_CUENTAS_CONTABLES__ActivarBotonModificar();

		if(Form_PLAN_CUENTAS_CONTABLES__EsCuentaAgregada=='t')
			Form_PLAN_CUENTAS_CONTABLES__ActivarBotonEliminar();
		else
			Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonEliminar();

		Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonGuardar();
		Form_PLAN_CUENTAS_CONTABLES__Mensaje("");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado("");
		}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_PLAN_CUENTAS_CONTABLES__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_FPCC").value="";
	Form_PLAN_CUENTAS_CONTABLES__Mensaje("");
	Form_PLAN_CUENTAS_CONTABLES__MensajeListado("");
	Form_PLAN_CUENTAS_CONTABLES__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FPCC",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_PLAN_CUENTAS_CONTABLES__Modificar(){
	Form_PLAN_CUENTAS_CONTABLES__ActivarFormulario();
	Form_PLAN_CUENTAS_CONTABLES__ActivarBotonGuardar();
	Form_PLAN_CUENTAS_CONTABLES__DesactivarBotonModificar();
	Form_PLAN_CUENTAS_CONTABLES__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_PLAN_CUENTAS_CONTABLES__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({'parameters':{ 'action':"onDelete",
									'id_cuenta_contable':Form_PLAN_CUENTAS_CONTABLES__IDSeleccionActualLista},
					 'onSuccess':Form_PLAN_CUENTAS_CONTABLES__EliminarMensaje,
					 'url':'../cuenta_contable/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_PLAN_CUENTAS_CONTABLES__EliminarMensaje(req){
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_PLAN_CUENTAS_CONTABLES__Nuevo();
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(respuesta.message,"VERDE");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado(respuesta.message,"VERDE");

		}
	else{
		Form_PLAN_CUENTAS_CONTABLES__Mensaje(respuesta.message,"ROJO");
		Form_PLAN_CUENTAS_CONTABLES__MensajeListado(respuesta.message,"ROJO");
		}

	}


function Form_PLAN_CUENTAS_CONTABLES__Visualizar(){
	window.open("../../report/contabilidad_plan_cuentas_xls.php");
}