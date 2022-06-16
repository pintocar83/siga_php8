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
function Form_DEFINICIONES_CARGOS__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FDC").innerHTML=MSG;
	}

/**
* Muestra los mensajes en la parte superior del listado
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_DEFINICIONES_CARGOS__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FDC_LISTADO").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_CARGOS__ActivarFormulario(){
	xGetElementById("DENOMINACION_FDC").readOnly=false;
	//xGetElementById("CODIGO_CONTABLE_FDC").readOnly=false;
	xGetElementById("FORMULA_FDC").readOnly=false;
// 	xGetElementById("AFECTACION_C_FDC").readOnly=false;
// 	xGetElementById("AFECTACION_C_FDC").disabled=false;
// 	xGetElementById("AFECTACION_P_FDC").readOnly=false;
// 	xGetElementById("AFECTACION_P_FDC").disabled=false;
	//xGetElementById("AFECTACION_C_FDC").readOnly=true;
	//xGetElementById("AFECTACION_C_FDC").disabled=true;
	xGetElementById("AFECTACION_P_FDC").readOnly=true;
	xGetElementById("AFECTACION_P_FDC").disabled=true;

	xGetElementById("ES_IVA_FDC").disabled=false;

	xGetElementById("DENOMINACION_FDC").setAttribute('class','TextoCampoInputObligatorios');
// 	xGetElementById("CODIGO_CONTABLE_FDC").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("FORMULA_FDC").setAttribute('class','TextoCampoInputObligatorios');



	if(xGetElementById("AFECTACION_P_FDC").checked)
		Form_DEFINICIONES_DE_CARGOS__ActivarCamposAfectacionPresupuestaria();
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_DEFINICIONES_CARGOS__DesactivarFormulario(){
	xGetElementById("DENOMINACION_FDC").readOnly=true;
// 	xGetElementById("CODIGO_CONTABLE_FDC").readOnly=true;
	xGetElementById("FORMULA_FDC").readOnly=true;
	//xGetElementById("AFECTACION_C_FDC").readOnly=true;
	//xGetElementById("AFECTACION_C_FDC").disabled=true;
	xGetElementById("AFECTACION_P_FDC").readOnly=true;
	xGetElementById("AFECTACION_P_FDC").disabled=true;

	xGetElementById("ES_IVA_FDC").disabled=true;

	xGetElementById("DENOMINACION_FDC").setAttribute('class','TextoCampoInputDesactivado');
//	xGetElementById("CODIGO_CONTABLE_FDC").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("FORMULA_FDC").setAttribute('class','TextoCampoInputDesactivado');

// 	xGetElementById("CODIGO_CONTABLE_FDC").setAttribute('onkeypress',"");
// 	xGetElementById("CODIGO_CONTABLE_FDC").setAttribute('onblur',"");
// 	xGetElementById("CODIGO_CONTABLE_FDC").setAttribute('ondblclick',"");
//
// 	xGetElementById("IMG_BUSCAR_CODIGO_CONTABLE_FDC").setAttribute('onclick',"");
//
// 	DesactivarBoton("IMG_BUSCAR_CODIGO_CONTABLE_FDC","IMG_BUSCAR_CODIGO_CONTABLE_FDC",'buscar');

	//if(xGetElementById("AFECTACION_C_FDC").checked)
	//	Form_DEFINICIONES_DE_CARGOS__DesactivarCamposAfectacionContable();
	if(xGetElementById("AFECTACION_P_FDC").checked)
		Form_DEFINICIONES_DE_CARGOS__DesactivarCamposAfectacionPresupuestaria();
	}


// function Form_DEFINICIONES_DE_CARGOS__ActivarCamposAfectacionPresupuestaria(){
// 	if(xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC")){
// 		xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC").setAttribute('onclick',"Form_LISTA_CUENTAS_CASADAS__Abrir('ID_CASADAS_FDC','PROGRAMATICA_FDC','CUENTA_PRESUPUESTARIA_FDC','DENOMINACION_CP_FDC')");
// 		ActivarBoton("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC","IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC",'buscar');
// 		}
// 	}
// function Form_DEFINICIONES_DE_CARGOS__DesactivarCamposAfectacionPresupuestaria(){
// 	if(xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC")){
// 		xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC").setAttribute('onclick',"");
// 		DesactivarBoton("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC","IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC",'buscar');
// 		}
// 	}

function Form_DEFINICIONES_DE_CARGOS__ActivarCamposAfectacionPresupuestaria(){
	if(xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC")){
		xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC").setAttribute('onclick',"Form_LISTA_CUENTAS_PRESUPUESTARIAS__Abrir('CUENTA_PRESUPUESTARIA_FDC','DENOMINACION_CP_FDC','4%')");
		ActivarBoton("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC","IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC",'buscar');
		}
	}
function Form_DEFINICIONES_DE_CARGOS__DesactivarCamposAfectacionPresupuestaria(){
	if(xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC")){
		xGetElementById("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC").setAttribute('onclick',"");
		DesactivarBoton("IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC","IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC",'buscar');
		}
	}


function Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionContable(){
	xGetElementById("BLOQUE_CONTABLE_PRESUPUESTARIO_FDC").innerHTML="<INPUT id='CODIGO_CONTABLE_FDC' class='TextoCampoInput' type='text' size='15' maxlength='12' value=''><INPUT id='NOMBRE_CODIGO_CONTABLE_FDC' type='text' size='35' value='' readonly='true' class='TextoCampoInputDesactivado'><IMG id='IMG_BUSCAR_CODIGO_CONTABLE_FDC' class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'>";
	}

function Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionPresupuestaria(){
	xGetElementById("BLOQUE_CONTABLE_PRESUPUESTARIO_FDC").innerHTML="<INPUT id='CUENTA_PRESUPUESTARIA_FDC' class='TextoCampoInputDesactivado' type='text' size='12' readonly='true'><INPUT id='DENOMINACION_CP_FDC' class='TextoCampoInputDesactivado' type='text' size='40' readonly='true'><IMG id='IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC' class='BotonesParaCampos' src='../../image/icon/icon-find-sigafs.png' width='18' height='18'><INPUT id='ID_CASADAS_FDC' type='hidden' value=''";
	//xGetElementById("BLOQUE_CONTABLE_PRESUPUESTARIO_FDC").innerHTML="<INPUT id='PROGRAMATICA_FDC' class='TextoCampoInputDesactivado' type='text' size='20' readonly='true'><INPUT id='CUENTA_PRESUPUESTARIA_FDC' class='TextoCampoInputDesactivado' type='text' size='12' readonly='true'><INPUT id='DENOMINACION_CP_FDC' class='TextoCampoInputDesactivado' type='text' size='30' readonly='true'><IMG id='IMG_BUSCAR_CODIGO_PRESUPUESTARIO_CASADO_FDC' class='BotonesParaCampos' src='../img/iconos/buscar_activo.png' onmouseover=\"src='../img/iconos/buscar_con_foco.png';\" onmouseout=\"src='../img/iconos/buscar_activo.png'\" width='20' height='20'><INPUT id='ID_CASADAS_FDC' type='hidden' value=''";
	}


/**
* Activa el boton modificar
*/
function Form_DEFINICIONES_CARGOS__ActivarBotonModificar(){
	ActivarBoton("BOTON_MODIFICAR_FDC","IMG_MODIFICAR_FDC",'modificar');
	}

/**
* Desactiva el boton modificar
*/
function Form_DEFINICIONES_CARGOS__DesactivarBotonModificar(){
	DesactivarBoton("BOTON_MODIFICAR_FDC","IMG_MODIFICAR_FDC",'modificar');
	}

/**
* Activa el boton guardar
*/
function Form_DEFINICIONES_CARGOS__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FDC","IMG_GUARDAR_FDC",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_DEFINICIONES_CARGOS__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FDC","IMG_GUARDAR_FDC",'guardar');
	}

/**
* Activa el boton eliminar
*/
function Form_DEFINICIONES_CARGOS__ActivarBotonEliminar(){
	ActivarBoton("BOTON_ELIMINAR_FDC","IMG_ELIMINAR_FDC",'eliminar');
	}

/**
* Desactiva el boton eliminar
*/
function Form_DEFINICIONES_CARGOS__DesactivarBotonEliminar(){
	DesactivarBoton("BOTON_ELIMINAR_FDC","IMG_ELIMINAR_FDC",'eliminar');
	}


function Form_DEFINICIONES_CARGOS__PierdeFoco(ID){
	if(xTrim(strtoupper(xGetElementById(ID).value))=="")
		xGetElementById(ID).value=Form_DEFINICIONES_CARGOS__ValorPorDefectoMontoDeducible;
	else if(xTrim(strtoupper(xGetElementById(ID).value))=="0")
		xGetElementById(ID).value=Form_DEFINICIONES_CARGOS__ValorPorDefectoMontoDeducible;
	}

function Form_DEFINICIONES_CARGOS__TomaFoco(ID){
	if(xTrim(strtoupper(xGetElementById(ID).value))==Form_DEFINICIONES_CARGOS__ValorPorDefectoMontoDeducible)
		xGetElementById(ID).value="";
	}

function Form_DEFINICIONES_CARGOS__CargarCuentaPresupuestaria(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value));
	if(CadenaBuscar==""){
		xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value="";
		xGetElementById("DENOMINACION_CP_FDC").value="";
		return;
		}
	//Si no es numero salimos
	if(isNaN(CadenaBuscar)){
		xGetElementById("DENOMINACION_CP_FDC").value="CÓDIGO NO ENCONTRADO";
		return;
		}

	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':"",
									'filtro': CadenaBuscar,
									'start':0,
									'limit':'ALL',
									'sort':'[{"property":"cuenta_presupuestaria","direction":"ASC"},{"property":"denominacion","direction":"ASC"}]'
									},
					 'onSuccess':function(req){
										var respuesta = req.responseText;
										var resultado = eval("(" + respuesta + ")");
										resultado=resultado["result"];
										if(resultado.length==1){
											xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value=resultado[0]['id_cuenta_presupuestaria'];
											if(!EsCodigoPresupuestario_General(xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value))
												xGetElementById("DENOMINACION_CP_FDC").value=resultado[0]['denominacion'];
											else//si es codigo general
												xGetElementById("DENOMINACION_CP_FDC").value="CÓDIGO NO ENCONTRADO";
											}
										else{//Codigo invalido
											xGetElementById("DENOMINACION_CP_FDC").value="CÓDIGO NO ENCONTRADO";
											}
										},
					 'url':'../cuenta_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}






/*Indica el id que se tiene seleccionado actualmente en el listado de bancos. Necesario para eliminar y para modificar*/
var Form_DEFINICIONES_CARGOS__IDSeleccionActualLista=-1;

var Form_DEFINICIONES_CARGOS__ValorPorDefectoMontoDeducible="0.00";



/**
* Nueva definicion
*/
function Form_DEFINICIONES_CARGOS__Nuevo(){
	//xGetElementById("AFECTACION_P_FDC").checked=true;
	Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionPresupuestaria();
	Form_DEFINICIONES_CARGOS__LimpiarInputTextBuscarListado();
	Form_DEFINICIONES_CARGOS__TabPane.setSelectedIndex(0);
	DarFocoCampo("DENOMINACION_FDC",1000);
	}




/**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
function Form_DEFINICIONES_CARGOS__GuardarVerificar(){
	Form_DEFINICIONES_CARGOS__TabPane.setSelectedIndex(0);
	var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDC").value));
	if(xGetElementById("CODIGO_CONTABLE_FDC"))
		var codigo_contable = xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDC").value));
	if(xGetElementById("CUENTA_PRESUPUESTARIA_FDC"))
		var id_cuenta_presupuestaria = xTrim(strtoupper(xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value));
	if(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDC"))
		var DescripcionCodigoContable 	= xTrim(strtoupper(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDC").value));
	var formula = xTrim(strtoupper(xGetElementById("FORMULA_FDC").value));


	if(!denominacion){
		var msg="Por favor introduzca la denominación para el cargo."
		Form_DEFINICIONES_CARGOS__Mensaje(msg,"ROJO");
		Form_DEFINICIONES_CARGOS__MensajeListado("");
		return;
		}
	if(!formula){
		var msg="Por favor introduzca la fórmula asociada al cargo."
		Form_DEFINICIONES_CARGOS__Mensaje(msg,"ROJO");
		Form_DEFINICIONES_CARGOS__MensajeListado("");
		return;
		}
	Form_DEFINICIONES_CARGOS__EsValidaGuardarFormula();

	if(xGetElementById("CODIGO_CONTABLE_FDC"))
		if(!codigo_contable){
			var msg="Por favor selecione el código contable asociado al cargo."
			Form_DEFINICIONES_CARGOS__Mensaje(msg,"ROJO");
			Form_DEFINICIONES_CARGOS__MensajeListado("");
			return;
			}
	if(xGetElementById("CUENTA_PRESUPUESTARIA_FDC"))
		if(!id_cuenta_presupuestaria){
			var msg="Por favor selecione cuenta presupuestaria asociada al cargo."
			Form_DEFINICIONES_CARGOS__Mensaje(msg,"ROJO");
			Form_DEFINICIONES_CARGOS__MensajeListado("");
			return;
			}
	if(xGetElementById("NOMBRE_CODIGO_CONTABLE_FDC"))
		if(DescripcionCodigoContable=="CÓDIGO NO ENCONTRADO"){
			Form_DEFINICIONES_CUENTAS_BANCARIAS__Mensaje("Por favor seleccione el código contable asociado al cargo.","ROJO");
			Form_DEFINICIONES_CUENTAS_BANCARIAS__MensajeListado("");
			return;
			}



	//var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDC").value));
	//AjaxRequest.post({'parameters':{ 'action':"Form_DEFINICIONES_CARGOS__Existe",
	//								'id':Form_DEFINICIONES_CARGOS__IDSeleccionActualLista,
	//								'denominacion':denominacion},
	//				 'onSuccess':Form_DEFINICIONES_CARGOS__Guardar,
	//				 'url':'../modulo_cuentas_por_pagar/consultas.php',
	//				 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
	//				 });
	Form_DEFINICIONES_CARGOS__Guardar();
	}

/**
* Guarda los datos en la BD
*/
function Form_DEFINICIONES_CARGOS__Guardar(){
	var msg="";
	Form_DEFINICIONES_CARGOS__DesactivarFormulario();

	//Si ya existe un tipo de cuenta con el mismo nombre. No guardar.
	//if(resultado[0]['count']>=1){
	//	Form_DEFINICIONES_CARGOS__Mensaje("No se puedo guardar los datos. Ya existe un cargo con la misma denominación","ROJO");
	//	Form_DEFINICIONES_CARGOS__MensajeListado("");
	//	Form_DEFINICIONES_CARGOS__ActivarFormulario();
	//	return;
	//	}

	var denominacion = xTrim(strtoupper(xGetElementById("DENOMINACION_FDC").value));
	var formula = xTrim(strtoupper(xGetElementById("FORMULA_FDC").value));
	var es_iva=xGetElementById("ES_IVA_FDC").checked?'true':'false';
	var codigo_contable = "";
	if(xGetElementById("CODIGO_CONTABLE_FDC"))
		codigo_contable = xTrim(strtoupper(xGetElementById("CODIGO_CONTABLE_FDC").value));

	if(xGetElementById("CUENTA_PRESUPUESTARIA_FDC"))
		id_cuenta_presupuestaria = xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value;

	/*Si es guardar nuevo*/
	if(Form_DEFINICIONES_CARGOS__IDSeleccionActualLista==-1){
		AjaxRequest.post({'parameters':{ 'action':"onSave",
						'denominacion':denominacion,
	  					//'id_cuenta_contable':codigo_contable,
						'id_cuenta_presupuestaria':id_cuenta_presupuestaria,
						'formula':formula,
						'iva':es_iva},
						'onSuccess':Form_DEFINICIONES_CARGOS__GuardarMensaje,
						'url':'../cargo/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	/*Si es modificar*/
	else{
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_DEFINICIONES_CARGOS__ActivarFormulario();
			return;
			}
		AjaxRequest.post({'parameters':{ 'action':"onSave",
										'id':Form_DEFINICIONES_CARGOS__IDSeleccionActualLista,
										'denominacion':denominacion,
										//'id_cuenta_contable':codigo_contable,
										'id_cuenta_presupuestaria':id_cuenta_presupuestaria,
										'formula':formula,
										'iva':es_iva},
						'onSuccess':Form_DEFINICIONES_CARGOS__GuardarMensaje,
						'url':'../cargo/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_CARGOS__GuardarMensaje(req){
	Form_DEFINICIONES_CARGOS__ActivarFormulario();
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_DEFINICIONES_CARGOS__Nuevo();
		Form_DEFINICIONES_CARGOS__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_DEFINICIONES_CARGOS__Mensaje(respuesta.message,"ROJO");
	}





/**
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_DEFINICIONES_CARGOS__BuscarListado_CadenaBuscar="";

/**
* Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
*/
function Form_DEFINICIONES_CARGOS__BuscarListado(){
	Form_DEFINICIONES_CARGOS__IDSeleccionActualLista=-1;
	xGetElementById("FORMULARIO_FDC").reset();
	Form_DEFINICIONES_CARGOS__ActivarFormulario();
	Form_DEFINICIONES_CARGOS__DesactivarBotonModificar();
	Form_DEFINICIONES_CARGOS__DesactivarBotonEliminar();
	Form_DEFINICIONES_CARGOS__ActivarBotonGuardar();

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDC").value));

	if(CadenaBuscar!="")
		if(Form_DEFINICIONES_CARGOS__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_DEFINICIONES_CARGOS__BuscarListado_CadenaBuscar=CadenaBuscar;


	//si la cadena es numerica. Eliminamos los ceros a la izquierda.
	if(CadenaBuscar.length!=0)
		if(isNaN(CadenaBuscar)==false)
			CadenaBuscar=quitarCodigoCeros(CadenaBuscar);

	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':CadenaBuscar,
									'start':0,
									'limit':"ALL",
									'sort':'[{"property":"correlativo","direction":"ASC"}]'},
					 'onSuccess':Form_DEFINICIONES_CARGOS__MostrarListado,
					 'url':'../cargo/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el listado (Crea tabla dinamicamente)
* @param {Array} req Datos provenientes de la BD
*/
function Form_DEFINICIONES_CARGOS__MostrarListado(req){
	var respuesta = req.responseText;//alert(respuesta);return;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FDC").value));

	xGetElementById("TABLA_LISTA_FDC").innerHTML="";
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_DEFINICIONES_CARGOS__SeleccionarElementoTabla('"
					+resultado[i]['id']+"','"
					+resultado[i]['denominacion']+"','"
					+resultado[i]['id_cuenta_contable']+"','"
					+resultado[i]['formula']+"','"
					+resultado[i]['id_cuenta_presupuestaria']+"','"
					+resultado[i]['iva']+"')";
		FuncionOnDblclick="Form_DEFINICIONES_CARGOS__TabPane.setSelectedIndex(0);";
		FuncionOnMouseOver="pintarFila(\"FDC"+resultado[i]['id']+"\")";
		FuncionOnMouseOut="despintarFila(\"FDC"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FDC"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		if(n!=1)
			Contenido+="<TD width='5%' class='FilaEstilo'>"+resultado[i]['correlativo']+ "</TD>";
		else
			Contenido+="<TD width='5%' class='FilaEstilo'><strong>"+resultado[i]['correlativo']+ "</strong></TD>";

		CadAux1=str_replace(strtoupper(resultado[i]['denominacion']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['formula']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='50%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='45%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FDC").innerHTML=Contenido;
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
function Form_DEFINICIONES_CARGOS__SeleccionarElementoTabla(IDSeleccion, Denominacion, IDCodigoContable, Formula, IDCodigoPlanUnico,EsIVA){
	if(Form_DEFINICIONES_CARGOS__IDSeleccionActualLista!=-1)
		xGetElementById("FDC"+Form_DEFINICIONES_CARGOS__IDSeleccionActualLista).bgColor=colorFondoTabla;

	Form_DEFINICIONES_CARGOS__IDSeleccionActualLista=IDSeleccion;
	colorBase=colorSeleccionTabla;
	xGetElementById("FDC"+IDSeleccion).bgColor=colorBase;

	xGetElementById("DENOMINACION_FDC").value=Denominacion;


	xGetElementById("AFECTACION_P_FDC").checked=true;
	Form_DEFINICIONES_DE_CARGOS__MostrarAfectacionPresupuestaria();
	xGetElementById("CUENTA_PRESUPUESTARIA_FDC").value=IDCodigoPlanUnico;
	Form_DEFINICIONES_CARGOS__CargarCuentaPresupuestaria();


	xGetElementById("FORMULA_FDC").value=Formula;
	if(EsIVA=="t")
		xGetElementById("ES_IVA_FDC").checked=true;
	else
		xGetElementById("ES_IVA_FDC").checked=false;

	Form_DEFINICIONES_CARGOS__DesactivarFormulario();
	Form_DEFINICIONES_CARGOS__ActivarBotonModificar();
	Form_DEFINICIONES_CARGOS__ActivarBotonEliminar();
	Form_DEFINICIONES_CARGOS__DesactivarBotonGuardar();
	Form_DEFINICIONES_CARGOS__Mensaje("");
	Form_DEFINICIONES_CARGOS__MensajeListado("");
	}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_DEFINICIONES_CARGOS__LimpiarInputTextBuscarListado(){
	Form_DEFINICIONES_CARGOS__IDSeleccionActualLista=-1;
	Form_DEFINICIONES_CARGOS__DesactivarBotonModificar();
	Form_DEFINICIONES_CARGOS__DesactivarBotonEliminar();
	Form_DEFINICIONES_CARGOS__ActivarBotonGuardar();
	Form_DEFINICIONES_CARGOS__ActivarFormulario();
	xGetElementById("FORMULARIO_FDC").reset();
	xGetElementById("LISTADO_BUSCAR_FDC").value="";
	Form_DEFINICIONES_CARGOS__Mensaje("");
	Form_DEFINICIONES_CARGOS__MensajeListado("");
	Form_DEFINICIONES_CARGOS__BuscarListado();
	DarFocoCampo("LISTADO_BUSCAR_FDC",1000);
	}






/**
* Es llamada cuando se presiona el boton de modificar
*/
function Form_DEFINICIONES_CARGOS__Modificar(){
	Form_DEFINICIONES_CARGOS__ActivarFormulario();
	Form_DEFINICIONES_CARGOS__ActivarBotonGuardar();
	Form_DEFINICIONES_CARGOS__DesactivarBotonModificar();
	Form_DEFINICIONES_CARGOS__TabPane.setSelectedIndex(0);
	}





/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function Form_DEFINICIONES_CARGOS__Eliminar(){
	//OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
	if(Form_DEFINICIONES_CARGOS__IDSeleccionActualLista==-1)
		return;

	if(!confirm("¿Esta seguro que quiere eliminarlo?"))
		return;
	AjaxRequest.post({'parameters':{ 'action':"onDelete",
									'id':Form_DEFINICIONES_CARGOS__IDSeleccionActualLista},
					 'onSuccess':Form_DEFINICIONES_CARGOS__EliminarMensaje,
					 'url':'../cargo/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_DEFINICIONES_CARGOS__EliminarMensaje(req){
	var respuesta = req.responseText;
	respuesta=eval("(" + respuesta + ")");
	if(respuesta.success){
		Form_DEFINICIONES_CARGOS__LimpiarInputTextBuscarListado();
		Form_DEFINICIONES_CARGOS__Mensaje("La eliminación se realizó satisfactoriamente.","VERDE");
		Form_DEFINICIONES_CARGOS__MensajeListado("La eliminación se realizó satisfactoriamente.","VERDE");
		}
	else{
		Form_DEFINICIONES_CARGOS__Mensaje("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		Form_DEFINICIONES_CARGOS__MensajeListado("Error. No se pudo eliminar los datos. Vuelva a intentarlo.","ROJO");
		}
	}


function Form_DEFINICIONES_CARGOS__ProbarFormula(){
	var RESULTADO;
	var MONTO=(xGetElementById("MONTO_PRUEBA_FDC").value)*1.0;
	var CadenaEvaluar="RESULTADO=(" + xTrim(strtoupper(xGetElementById("FORMULA_FDC").value)) + ");";
	xGetElementById("RESULTADO_PRUEBA_FDC").value="ERROR";
	eval(CadenaEvaluar);

	if(!isNaN(RESULTADO))
		xGetElementById("RESULTADO_PRUEBA_FDC").value=FormatearNumero(RESULTADO);
	}

function Form_DEFINICIONES_CARGOS__EsValidaGuardarFormula(){
	var RESULTADO;
	var MONTO;
	var CadenaEvaluar="RESULTADO=(" + xTrim(strtoupper(xGetElementById("FORMULA_FDC").value)) + ");";
	MONTO=0;
	Form_DEFINICIONES_CARGOS__Mensaje("Error en la formula.","ROJO");
	eval(CadenaEvaluar);
	Form_DEFINICIONES_CARGOS__Mensaje("");
	}

