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
function Form_BENEFICIARIO__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FB").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_BENEFICIARIO__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FB_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_BENEFICIARIO__ActivarFormulario(){
	xGetElementById("NACIONALIDAD_FB").readOnly=false;
	xGetElementById("CEDULA_FB").readOnly=false;
	xGetElementById("PRIMER_NOMBRE_FB").readOnly=false;
	xGetElementById("PRIMER_APELLIDO_FB").readOnly=false;
	xGetElementById("SEGUNDO_NOMBRE_FB").readOnly=false;
	xGetElementById("SEGUNDO_APELLIDO_FB").readOnly=false;
	xGetElementById("TELEFONO_FB").readOnly=false;
	xGetElementById("CORREO_FB").readOnly=false;


	xGetElementById("NACIONALIDAD_FB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("CEDULA_FB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("PRIMER_NOMBRE_FB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("PRIMER_APELLIDO_FB").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("SEGUNDO_NOMBRE_FB").setAttribute('class','TextoCampoInput');
	xGetElementById("SEGUNDO_APELLIDO_FB").setAttribute('class','TextoCampoInput');
	xGetElementById("TELEFONO_FB").setAttribute('class','TextoCampoInput');
	xGetElementById("CORREO_FB").setAttribute('class','TextoCampoInput');
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_BENEFICIARIO__DesactivarFormulario(){
	xGetElementById("NACIONALIDAD_FB").readOnly=true;
	xGetElementById("CEDULA_FB").readOnly=true;
	xGetElementById("PRIMER_NOMBRE_FB").readOnly=true;
	xGetElementById("PRIMER_APELLIDO_FB").readOnly=true;
	xGetElementById("SEGUNDO_NOMBRE_FB").readOnly=true;
	xGetElementById("SEGUNDO_APELLIDO_FB").readOnly=true;
	xGetElementById("TELEFONO_FB").readOnly=true;
	xGetElementById("CORREO_FB").readOnly=true;


	xGetElementById("NACIONALIDAD_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CEDULA_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("PRIMER_NOMBRE_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("PRIMER_APELLIDO_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("SEGUNDO_NOMBRE_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("SEGUNDO_APELLIDO_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TELEFONO_FB").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("CORREO_FB").setAttribute('class','TextoCampoInputDesactivado');
	}

/**
* Activa el boton modificar
*/
function Form_BENEFICIARIO__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FB","IMG_MODIFICAR_FB",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_BENEFICIARIO__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FB","IMG_MODIFICAR_FB",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_BENEFICIARIO__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FB","IMG_GUARDAR_FB",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_BENEFICIARIO__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FB","IMG_GUARDAR_FB",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_BENEFICIARIO__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FB","IMG_ELIMINAR_FB",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_BENEFICIARIO__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FB","IMG_ELIMINAR_FB",'eliminar');
	}

/**
* Indica el elemento que se tiene seleccionado actualmente en el listado. Necesario para eliminar y para modificar
*/
var Form_BENEFICIARIO__IDSeleccionActualLista=-1;

/**
* Nueva definicion
*/
function Form_BENEFICIARIO__Nuevo(){
	Form_BENEFICIARIO__LimpiarInputTextBuscarListado();
	Form_BENEFICIARIO__TabPane.setSelectedIndex(0);
	DarFocoCampo("CEDULA_FB",1000);
	}


/**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
function Form_BENEFICIARIO__GuardarVerificar(){
	Form_BENEFICIARIO__TabPane.setSelectedIndex(0);
	var Nacionalidad = xTrim(strtoupper(xGetElementById("NACIONALIDAD_FB").value));
	var Cedula = xTrim(strtoupper(xGetElementById("CEDULA_FB").value));
	var PrimerNombre  = xTrim(strtoupper(xGetElementById("PRIMER_NOMBRE_FB").value));
	var SegundoNombre  = xTrim(strtoupper(xGetElementById("SEGUNDO_NOMBRE_FB").value));
	var PrimerApellido  = xTrim(strtoupper(xGetElementById("PRIMER_APELLIDO_FB").value));
	var SegundoApellido  = xTrim(strtoupper(xGetElementById("SEGUNDO_APELLIDO_FB").value));
	var Telefono  = xTrim(strtoupper(xGetElementById("TELEFONO_FB").value));
	var Correo  = xTrim(xGetElementById("CORREO_FB").value);
	var CuentaBancaria1  = xTrim(strtoupper(xGetElementById("CUENTA_BANCARIA_1_FB").value));
	var CuentaBancaria2  = xTrim(strtoupper(xGetElementById("CUENTA_BANCARIA_2_FB").value));

	if(!Cedula){
		var msg="Por favor introduzca el número de cedula."
		Form_BENEFICIARIO__Mensaje(msg,"ROJO");
		Form_BENEFICIARIO__MensajeListado("");
		return;
		}
	if(!PrimerNombre){
		var msg="Por favor introduzca el primer nombre."
		Form_BENEFICIARIO__Mensaje(msg,"ROJO");
		Form_BENEFICIARIO__MensajeListado("");
		return;
		}
	if(!PrimerApellido){
		var msg="Por favor introduzca el primer apellido."
		Form_BENEFICIARIO__Mensaje(msg,"ROJO");
		Form_BENEFICIARIO__MensajeListado("");
		return;
		}
	if(Telefono){
		if(!SIGA.validPhone.test(Telefono)){
			var msg="Número telefónico invalido.\nFormato: (0999)999.99.99";
			Form_BENEFICIARIO__Mensaje(msg,"ROJO");
			Form_BENEFICIARIO__MensajeListado("");
			return;
			}
		}

	Form_BENEFICIARIO__DesactivarFormulario();
	var id_persona="";
	if(!(Form_BENEFICIARIO__IDSeleccionActualLista==-1 || Form_BENEFICIARIO__IDSeleccionActualLista=="")){
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_BENEFICIARIO__ActivarFormulario();
			return;
			}
		id_persona=Form_BENEFICIARIO__IDSeleccionActualLista;
		}
	
	AjaxRequest.post({'parameters':{ 'action':"onSave_PersonaNatural",
										'id':id_persona,
										'identificacion_tipo':Nacionalidad,
										'identificacion_numero':Cedula,
										'primer_nombre':PrimerNombre,
										'segundo_nombre':SegundoNombre,
										'primer_apellido':PrimerApellido,
										'segundo_apellido':SegundoApellido,
										'telefono':Telefono,
										'correo':Correo,
										'cuenta_bancaria_principal': CuentaBancaria1,
										'cuenta_bancaria_secundaria': CuentaBancaria2,
									},
						'onSuccess':Form_BENEFICIARIO__GuardarMensaje,
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_BENEFICIARIO__GuardarMensaje(req){
	Form_BENEFICIARIO__ActivarFormulario();
	var respuesta = req.responseText;
	respuesta= eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_BENEFICIARIO__Nuevo();
		Form_BENEFICIARIO__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_BENEFICIARIO__Mensaje(respuesta.message,"ROJO");
	}

/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_BENEFICIARIO__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_BENEFICIARIO__BuscarListado(){
	Form_BENEFICIARIO__IDSeleccionActualLista=-1;
	xGetElementById("BOTON_BUSCAR_PERSONA_FB").style.display="";
	xGetElementById("FORMULARIO_BENEFICIARIO").reset();
	Form_BENEFICIARIO__ActivarFormulario();
	Form_BENEFICIARIO__DesactivarBotonModificar();
	Form_BENEFICIARIO__DesactivarBotonEliminar();
	Form_BENEFICIARIO__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FB").value));

	if(CadenaBuscar!="")
		if(Form_BENEFICIARIO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_BENEFICIARIO__BuscarListado_CadenaBuscar=CadenaBuscar;

	AjaxRequest.post({
						'parameters':{
							'action':"onList_PersonaNatural",
							'text':CadenaBuscar,
							'start':0,
							'limit':xGetElementById("LISTADO_MOSTRAR_FB").value,
							'sort':'[{"property":"identificacion_tipo","direction":"ASC"},{"property":"identificacion_numero","direction":"ASC"}]'
						},
						'onSuccess':Form_BENEFICIARIO__MostrarListado,
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_BENEFICIARIO__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;

	//var tablaPrueba = xGetElementById("TABLA_LISTA_FB");

	var CadAux1, CadAux2;

	var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FB").value)));

	xGetElementById("TABLA_LISTA_FB").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_BENEFICIARIO__SeleccionarElementoTabla('"+resultado[i]['id']+"')";
 		FuncionOnDblclick="Form_BENEFICIARIO__TabPane.setSelectedIndex(0);";
 		FuncionOnMouseOver="pintarFila(\"FB"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FB"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FB"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux1=str_replace(strtoupper(resultado[i]['identificacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['denominacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='20%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='80%' class='FilaEstilo'>"+CadAux2+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FB").innerHTML=Contenido;
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
function Form_BENEFICIARIO__SeleccionarElementoTabla(IDSeleccion){
		if(Form_BENEFICIARIO__IDSeleccionActualLista!=-1)
			xGetElementById("FB"+Form_BENEFICIARIO__IDSeleccionActualLista).bgColor=colorFondoTabla;
        colorBase=colorSeleccionTabla;
        xGetElementById("FB"+IDSeleccion).bgColor=colorBase;
		Form_BENEFICIARIO__IDSeleccionActualLista=IDSeleccion;
		
		Form_BENEFICIARIO__DesactivarFormulario();
		Form_BENEFICIARIO__ActivarBotonModificar();
		Form_BENEFICIARIO__ActivarBotonEliminar();
		Form_BENEFICIARIO__DesactivarBotonGuardar();
		Form_BENEFICIARIO__Mensaje("");
		Form_BENEFICIARIO__MensajeListado("");
		
		xGetElementById("BOTON_BUSCAR_PERSONA_FB").style.display="none";
		
		AjaxRequest.post({
						'parameters':{
							'action':"onGet_PersonaNatural",
							'id':Form_BENEFICIARIO__IDSeleccionActualLista
						},
						'onSuccess':function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							xGetElementById("NACIONALIDAD_FB").value=resultado[0]["identificacion_tipo"];
							xGetElementById("CEDULA_FB").value=resultado[0]["identificacion_numero"];
							xGetElementById("PRIMER_NOMBRE_FB").value=resultado[0]["primer_nombre"];
							xGetElementById("SEGUNDO_NOMBRE_FB").value=resultado[0]["segundo_nombre"];
							xGetElementById("PRIMER_APELLIDO_FB").value=resultado[0]["primer_apellido"];
							xGetElementById("SEGUNDO_APELLIDO_FB").value=resultado[0]["segundo_apellido"];
							xGetElementById("TELEFONO_FB").value=resultado[0]["telefono"];
							xGetElementById("CORREO_FB").value=resultado[0]["correo"];
							xGetElementById("CUENTA_BANCARIA_1_FB").value=resultado[0]["cuenta_bancaria_principal"];
							xGetElementById("CUENTA_BANCARIA_2_FB").value=resultado[0]["cuenta_bancaria_secundaria"];
						},
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_BENEFICIARIO__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_FB").value="";
	Form_BENEFICIARIO__Mensaje("");
	Form_BENEFICIARIO__MensajeListado("");
	Form_BENEFICIARIO__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FB",1000);
	}

/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_BENEFICIARIO__Modificar(){
	Form_BENEFICIARIO__ActivarFormulario();
	Form_BENEFICIARIO__ActivarBotonGuardar();
	Form_BENEFICIARIO__DesactivarBotonModificar();
	Form_BENEFICIARIO__TabPane.setSelectedIndex(0);
	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_BENEFICIARIO__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_BENEFICIARIO__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({
						'parameters':{
							'action':"onDelete",
							'id':Form_BENEFICIARIO__IDSeleccionActualLista
						},
					  'onSuccess':Form_BENEFICIARIO__EliminarMensaje,
					  'url':'../persona/',
					  'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					  });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_BENEFICIARIO__EliminarMensaje(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	if(resultado.success){
		Form_BENEFICIARIO__LimpiarInputTextBuscarListado();
		Form_BENEFICIARIO__Mensaje(resultado.message,"VERDE");
		Form_BENEFICIARIO__MensajeListado(resultado.message,"VERDE");
		}
	else{
		Form_BENEFICIARIO__Mensaje(resultado.message,"ROJO");
		Form_BENEFICIARIO__MensajeListado(resultado.message,"ROJO");
		}
	}

function Form_BENEFICIARIO__Buscar(){
	var Nacionalidad = xTrim(strtoupper(xGetElementById("NACIONALIDAD_FB").value));
	var Cedula = xTrim(strtoupper(xGetElementById("CEDULA_FB").value));
	
	AjaxRequest.post({
						'parameters':{
							'action':"onGet_PersonaCNE",
							'cedula': Cedula,
							'nacionalidad': Nacionalidad
						},
						'onSuccess':function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							
							if(resultado[0]["id_persona"]>0) {
								Form_BENEFICIARIO__Mensaje("La persona se encuentra agregada en el listado.","VERDE");
								Form_BENEFICIARIO__MensajeListado("","VERDE");
								return;
							}
							

							xGetElementById("PRIMER_NOMBRE_FB").value=resultado[0]["primer_nombre"];
							xGetElementById("SEGUNDO_NOMBRE_FB").value=resultado[0]["segundo_nombre"];
							xGetElementById("PRIMER_APELLIDO_FB").value=resultado[0]["primer_apellido"];
							xGetElementById("SEGUNDO_APELLIDO_FB").value=resultado[0]["segundo_apellido"];
							xGetElementById("TELEFONO_FB").value=resultado[0]["telefono"];
							xGetElementById("CORREO_FB").value=resultado[0]["correo"];
						},
						'url':'../persona/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	
}

function Form_BENEFICIARIO__Visualizar(){
	//window.open("reportes/reporte_beneficiarios.php");
}
