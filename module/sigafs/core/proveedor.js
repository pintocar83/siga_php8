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
* @fileoverview Contiene todas la rutinas relacionadas a la ventana Definicion de beneficiarios
*
* @author Carlos Pinto <pintocar83@gmail.com> <pintocar83@gmail.com>
* @version 1.0
*/


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_PROVEEDOR__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FP").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_PROVEEDOR__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FP_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_PROVEEDOR__ActivarFormulario(){
	xGetElementById("RIF_TIPO_FP").readOnly=false;
	xGetElementById("RIF_NUMERO_FP").readOnly=false;
	xGetElementById("DENOMINACION_FP").readOnly=false;
	xGetElementById("TELEFONO_FP").readOnly=false;
	xGetElementById("CORREO_FP").readOnly=false;
	xGetElementById("DIRECCION_FP").readOnly=false;
	xGetElementById("CUENTA_BANCARIA_1_FP").readOnly=false;
	xGetElementById("CUENTA_BANCARIA_2_FP").readOnly=false;


	xGetElementById("RIF_TIPO_FP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("RIF_NUMERO_FP").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("DENOMINACION_FP").setAttribute('class','TextoCampoInputObligatorios');	
	xGetElementById("TELEFONO_FP").setAttribute('class','TextoCampoInput');
	xGetElementById("CORREO_FP").setAttribute('class','TextoCampoInput');
	xGetElementById("DIRECCION_FP").setAttribute('class','TextoCampoInput');
	xGetElementById("DIRECCION_FP").setAttribute('class','TextoCampoInput');
	xGetElementById("CUENTA_BANCARIA_1_FP").setAttribute('class','TextoCampoInput');
	xGetElementById("CUENTA_BANCARIA_2_FP").setAttribute('class','TextoCampoInput');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_PROVEEDOR__DesactivarFormulario(){
	xGetElementById("RIF_TIPO_FP").readOnly=true;
	xGetElementById("RIF_NUMERO_FP").readOnly=true;
	xGetElementById("DENOMINACION_FP").readOnly=true;
	xGetElementById("TELEFONO_FP").readOnly=true;
	xGetElementById("CORREO_FP").readOnly=true;
	xGetElementById("DIRECCION_FP").readOnly=true;
	xGetElementById("CUENTA_BANCARIA_1_FP").readOnly=true;
	xGetElementById("CUENTA_BANCARIA_2_FP").readOnly=true;


	xGetElementById("RIF_TIPO_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("RIF_NUMERO_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DENOMINACION_FP").setAttribute('class','TextoCampoInputDesactivado');	
	xGetElementById("TELEFONO_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CORREO_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("DIRECCION_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CUENTA_BANCARIA_1_FP").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CUENTA_BANCARIA_2_FP").setAttribute('class','TextoCampoInputDesactivado');
	}

/**
* Activa el boton modificar
*/
function Form_PROVEEDOR__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FP","IMG_MODIFICAR_FP",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_PROVEEDOR__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FP","IMG_MODIFICAR_FP",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_PROVEEDOR__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FP","IMG_GUARDAR_FP",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_PROVEEDOR__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FP","IMG_GUARDAR_FP",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_PROVEEDOR__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FP","IMG_ELIMINAR_FP",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_PROVEEDOR__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FP","IMG_ELIMINAR_FP",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_PROVEEDOR__IDSeleccionActualLista=-1;

/**
* Nueva definicion
*/
function Form_PROVEEDOR__Nuevo(){
	Form_PROVEEDOR__LimpiarInputTextBuscarListado();
	Form_PROVEEDOR__TabPane.setSelectedIndex(0);
	DarFocoCampo("RIF_NUMERO_FP",1000);
	}


/**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
function Form_PROVEEDOR__GuardarVerificar(){
	Form_PROVEEDOR__TabPane.setSelectedIndex(0);
	var RifTipo = xTrim(strtoupper(xGetElementById("RIF_TIPO_FP").value));
	var RifNumero = xTrim(strtoupper(xGetElementById("RIF_NUMERO_FP").value));
	var Denominacion  = xTrim(strtoupper(xGetElementById("DENOMINACION_FP").value));
	var Telefono  = xTrim(strtoupper(xGetElementById("TELEFONO_FP").value));
	var Correo  = xTrim(xGetElementById("CORREO_FP").value);
	var Direccion  = xTrim(strtoupper(xGetElementById("DIRECCION_FP").value));
	var CuentaBancaria1  = xTrim(strtoupper(xGetElementById("CUENTA_BANCARIA_1_FP").value));
	var CuentaBancaria2  = xTrim(strtoupper(xGetElementById("CUENTA_BANCARIA_2_FP").value));

	if(!RifNumero){
		var msg="Por favor introduzca el número de RIF."
		Form_PROVEEDOR__Mensaje(msg,"ROJO");
		Form_PROVEEDOR__MensajeListado("");
		return;
		}
	if(!Denominacion){
		var msg="Por favor introduzca la denominacion del proveedor."
		Form_PROVEEDOR__Mensaje(msg,"ROJO");
		Form_PROVEEDOR__MensajeListado("");
		return;
		}	
	if(Telefono){
		if(!SIGA.validPhone.test(Telefono)){
			var msg="Número telefónico invalido.\nFormato: (0999)999.99.99";
			Form_PROVEEDOR__Mensaje(msg,"ROJO");
			Form_PROVEEDOR__MensajeListado("");
			return;
			}
		}

	Form_PROVEEDOR__DesactivarFormulario();
	var id_persona="";
	if(!(Form_PROVEEDOR__IDSeleccionActualLista==-1 || Form_PROVEEDOR__IDSeleccionActualLista=="")){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_PROVEEDOR__ActivarFormulario();
			return;
			}
		id_persona=Form_PROVEEDOR__IDSeleccionActualLista;
		}
	
	AjaxRequest.post({'parameters':{ 'action':"onSave_PersonaJuridica",
										'id':id_persona,
										'identificacion_tipo':RifTipo,
										'identificacion_numero':RifNumero,
										'denominacion':Denominacion,
										'telefono':Telefono,
										'correo':Correo,
										'direccion':Direccion,
										'cuenta_bancaria_principal': CuentaBancaria1,
										'cuenta_bancaria_secundaria': CuentaBancaria2,
									},
						'onSuccess':Form_PROVEEDOR__GuardarMensaje,
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_PROVEEDOR__GuardarMensaje(req){
	Form_PROVEEDOR__ActivarFormulario();
	var respuesta = req.responseText;
	respuesta= eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_PROVEEDOR__Nuevo();
		Form_PROVEEDOR__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_PROVEEDOR__Mensaje(respuesta.message,"ROJO");
	}

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_PROVEEDOR__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_PROVEEDOR__BuscarListado(){
	Form_PROVEEDOR__IDSeleccionActualLista=-1;
	xGetElementById("FORMULARIO_PROVEEDOR").reset();
	Form_PROVEEDOR__ActivarFormulario();
	Form_PROVEEDOR__DesactivarBotonModificar();
	Form_PROVEEDOR__DesactivarBotonEliminar();
	Form_PROVEEDOR__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FP").value));

	if(CadenaBuscar!="")
		if(Form_PROVEEDOR__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_PROVEEDOR__BuscarListado_CadenaBuscar=CadenaBuscar;

	AjaxRequest.post({
						'parameters':{
							'action':"onList_PersonaJuridica",
							'text':CadenaBuscar,
							'start':0,
							'limit':xGetElementById("LISTADO_MOSTRAR_FP").value,
							'sort':'[{"property":"identificacion_tipo","direction":"ASC"},{"property":"identificacion_numero","direction":"ASC"}]'
						},
						'onSuccess':Form_PROVEEDOR__MostrarListado,
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_PROVEEDOR__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;

	//var tablaPrueba = xGetElementById("TABLA_LISTA_FP");

	var CadAux1, CadAux2;

	var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FP").value)));

	xGetElementById("TABLA_LISTA_FP").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_PROVEEDOR__SeleccionarElementoTabla('"+resultado[i]['id']+"')";
 		FuncionOnDblclick="Form_PROVEEDOR__TabPane.setSelectedIndex(0);";
 		FuncionOnMouseOver="pintarFila(\"FB"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FB"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FB"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux1=str_replace(strtoupper(resultado[i]['identificacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['denominacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='20%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='80%' class='FilaEstilo'>"+CadAux2+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FP").innerHTML=Contenido;
	}

/**
* Es llamada cuando se hace click sobre algun elemento de la tabla.
* Esta manda los datos para el formulario que se encuentra en la pestaña 'entrada de datos'
* @param {Integer} IDSeleccion Id del elemento seleccionado
* @param {String} Cedula beneficiario seleccionado
* @param {String} Nombres del beneficiario seleccionado
* @param {String} Apellido Apellido del beneficiario seleccionado
* @param {String} RIF Rid del beneficiario seleccionado
* @param {String} Observaciones Observaciones del beneficiario seleccionado
* @param {String} CodicoContable Codigo contable sel beneficiario seleccionado
* @param {String} DenominacionCC Denominacion del codigo contable asociado beneficiario al seleccionado
*/
function Form_PROVEEDOR__SeleccionarElementoTabla(IDSeleccion){
		if(Form_PROVEEDOR__IDSeleccionActualLista!=-1)
			xGetElementById("FB"+Form_PROVEEDOR__IDSeleccionActualLista).bgColor=colorFondoTabla;
        colorBase=colorSeleccionTabla;
        xGetElementById("FB"+IDSeleccion).bgColor=colorBase;
		Form_PROVEEDOR__IDSeleccionActualLista=IDSeleccion;
		
		Form_PROVEEDOR__DesactivarFormulario();
		Form_PROVEEDOR__ActivarBotonModificar();
		Form_PROVEEDOR__ActivarBotonEliminar();
		Form_PROVEEDOR__DesactivarBotonGuardar();
		Form_PROVEEDOR__Mensaje("");
		Form_PROVEEDOR__MensajeListado("");
		
		
		AjaxRequest.post({
						'parameters':{
							'action':"onGet_PersonaJuridica",
							'id':Form_PROVEEDOR__IDSeleccionActualLista
						},
						'onSuccess':function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							xGetElementById("RIF_TIPO_FP").value=resultado[0]["identificacion_tipo"];
							xGetElementById("RIF_NUMERO_FP").value=resultado[0]["identificacion_numero"];
							xGetElementById("DENOMINACION_FP").value=resultado[0]["denominacion"];
							xGetElementById("TELEFONO_FP").value=resultado[0]["telefono"];
							xGetElementById("CORREO_FP").value=resultado[0]["correo"];
							xGetElementById("DIRECCION_FP").value=resultado[0]["direccion"];
							xGetElementById("CUENTA_BANCARIA_1_FP").value=resultado[0]["cuenta_bancaria_principal"];
							xGetElementById("CUENTA_BANCARIA_2_FP").value=resultado[0]["cuenta_bancaria_secundaria"];
						},
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_PROVEEDOR__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_FP").value="";
	Form_PROVEEDOR__Mensaje("");
	Form_PROVEEDOR__MensajeListado("");
	Form_PROVEEDOR__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FP",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_PROVEEDOR__Modificar(){
	Form_PROVEEDOR__ActivarFormulario();
	Form_PROVEEDOR__ActivarBotonGuardar();
	Form_PROVEEDOR__DesactivarBotonModificar();
	Form_PROVEEDOR__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_PROVEEDOR__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_PROVEEDOR__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({
						'parameters':{
							'action':"onDelete",
							'id':Form_PROVEEDOR__IDSeleccionActualLista
						},
					  'onSuccess':Form_PROVEEDOR__EliminarMensaje,
					  'url':'../persona/',
					  'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					  });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_PROVEEDOR__EliminarMensaje(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	if(resultado.success){
		Form_PROVEEDOR__LimpiarInputTextBuscarListado();
		Form_PROVEEDOR__Mensaje(resultado.message,"VERDE");
		Form_PROVEEDOR__MensajeListado(resultado.message,"VERDE");
		}
	else{
		Form_PROVEEDOR__Mensaje(resultado.message,"ROJO");
		Form_PROVEEDOR__MensajeListado(resultado.message,"ROJO");
		}
	}
	
function Form_PROVEEDOR__Visualizar(){
	//window.open("reportes/reporte_beneficiarios.php");
}
