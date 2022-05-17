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
* @version 20091005
*/

var Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal=new Array();
var Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal=0;
var Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion=new Array(
																					"monto_real",
																					"monto_estimado",
																					"monto",
																					"monto_ene",
																					"monto_feb",
																					"monto_mar",
																					"monto_abr",
																					"monto_may",
																					"monto_jun",
																					"monto_jul",
																					"monto_ago",
																					"monto_sep",
																					"monto_oct",
																					"monto_nov",
																					"monto_dic");

var Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPlanUnicoCuentas;

/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FFPDG").innerHTML=MSG;
	}

/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarFormulario(){
	xGetElementById("BOTON_AGREGAR_FFPDG").disabled=false;
	xGetElementById("BOTON_QUITAR_FFPDG").disabled=false;
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarFormulario(){
	xGetElementById("BOTON_AGREGAR_FFPDG").disabled=true;
	xGetElementById("BOTON_QUITAR_FFPDG").disabled=true;
	}

/**
* Activa el boton guardar
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FFPDG","IMG_GUARDAR_FFPDG",'guardar');
	ActivarBoton("BOTON_ASIGNAR_FFPDG","IMG_ASIGNAR_FFPDG",'contabilizar');
	}

/**
* Desactiva el boton guardar
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FFPDG","IMG_GUARDAR_FFPDG",'guardar');
	DesactivarBoton("BOTON_ASIGNAR_FFPDG","IMG_ASIGNAR_FFPDG",'contabilizar');
	}



/*Nueva definicion*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Nuevo(){
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal=new Array();
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal=0;
/*
	AjaxRequest.post({'parameters':{ 'accion':"Form_LISTA_CUENTAS_PRESUPUESTARIAS__BuscarListado",
									'CadenaBuscar':""},
									'onSuccess':
										function(req){
											var respuesta = req.responseText;
											Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPlanUnicoCuentas = eval("(" + respuesta + ")");
											},
									'url':'../modulo_cuentas/consultas.php',
									'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
									});
*/
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__TabPane.setSelectedIndex(0);

	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarFormulario();
	}


/**
* Guarda los datos en la BD
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Guardar(asignar){
	if(!asignar)
		asignar="0";
	else
		asignar="1";

	var IDEstructuraPresupuestaria		= xTrim(strtoupper(xGetElementById("OAE_FFPDG").value));

	var IDCodigoPlanUnico=new Array();
	var MontoReal=new Array();
	var MontoEstimado=new Array();
	var TotalAnio=new Array();
	var MontoEne=new Array();
	var MontoFeb=new Array();
	var MontoMar=new Array();
	var MontoAbr=new Array();
	var MontoMay=new Array();
	var MontoJun=new Array();
	var MontoJul=new Array();
	var MontoAgo=new Array();
	var MontoSep=new Array();
	var MontoOct=new Array();
	var MontoNov=new Array();
	var MontoDic=new Array();
	
	var data=[];
	var n=0;

	for(var i=0;i<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;i++){
		if (Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["padre"])
		  continue;
		data[n]={
			id_cuenta_presupuestaria: Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["id_cuenta_presupuestaria"],
			monto_real: Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[0]],
			monto_estimado: Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[1]],
			monto: "ARRAY["+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[3]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[4]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[5]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[6]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[7]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[8]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[9]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[10]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[11]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[12]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[13]]+","+
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[14]]+"]"
			};
		n++;
		}

	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarFormulario();

	AjaxRequest.post({
					'parameters':{
									'action':"onSave",
									'anio':xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value,
									'tipo':xGetElementById("TIPO_FFPDG").value,
									'id_accion_subespecifica':xGetElementById("OAE_FFPDG").value,
									'data': SIGA.Ext.encode(data),
									'asignar':asignar
									},
					'onSuccess':Form_FORMULACION_PRESUPUESTO_DE_GASTOS__GuardarMensaje,
					'url':'../formulacion/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__GuardarMensaje(req){
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	
	if(respuesta.success){
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje(respuesta.message,"ROJO");
	}



function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarAC(){
	AjaxRequest.post({'parameters':{'action':"onList_AccionCentralizada",'text':'','start':0,'limit':'ALL','sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"},{"property":"denominacion_centralizada","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AC_FFPDG");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_centralizada"]});
								opcion.innerHTML=resultado[i]["tipo"]+resultado[i]["codigo_centralizada"];
								mD.agregaHijo(SelectU, opcion);
								}
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarAE(){
	if(!xGetElementById("AC_FFPDG").value)
		return;
	xGetElementById("AC_FFPDG").title=xGetElementById("AC_FFPDG").options[xGetElementById("AC_FFPDG").selectedIndex].title;
	xGetElementById("AC_FFPDG_INFO").innerHTML=xGetElementById("AC_FFPDG").title;

	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("AC_FFPDG").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("AE_FFPDG");
							SelectU.innerHTML="";
							var opcion;
							var textoAC;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_especifica"]});
								textoAC=xGetElementById("AC_FFPDG").options[xGetElementById("AC_FFPDG").selectedIndex].innerHTML;
								//opcion.innerHTML=resultado[i]["codigo_especifico"];
								//opcion.innerHTML=FormatearCodigoProgramaticoAE(textoAC,resultado[i]["codigo_especifica"]);
								opcion.innerHTML=resultado[i]["codigo_especifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarOAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarOAE(){
	if(!xGetElementById("AC_FFPDG").value||!xGetElementById("AE_FFPDG").value)
		return;
	xGetElementById("AE_FFPDG").title=xGetElementById("AE_FFPDG").options[xGetElementById("AE_FFPDG").selectedIndex].title;
	xGetElementById("AE_FFPDG_INFO").innerHTML=xGetElementById("AE_FFPDG").title;
	AjaxRequest.post({'parameters':{'action':"onList_AccionSubEspecifica",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]',
									'id_accion_especifica':xGetElementById("AE_FFPDG").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							var n=resultado.length;
							var SelectU = xGetElementById("OAE_FFPDG");
							SelectU.innerHTML="";
							var opcion;
							for(var i=0;i<n;i++){
								opcion = mD.agregaNodoElemento("option", null, null, {'value':resultado[i]["id"],'title':resultado[i]["denominacion_subespecifica"]});
								opcion.innerHTML=resultado[i]["codigo_subespecifica"];
								mD.agregaHijo(SelectU, opcion);
								}
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion(sw){
	xGetElementById("OAE_FFPDG").title=xGetElementById("OAE_FFPDG").options[xGetElementById("OAE_FFPDG").selectedIndex].title;
	xGetElementById("OAE_FFPDG_INFO").innerHTML=xGetElementById("OAE_FFPDG").title;

	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje("");
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarBotonGuardar();
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarFormulario();
	//xGetElementById("TD_DESHACER_ASIGNACION").innerHTML="";
	var Contenido="<tr class='CabeceraTablaEstilo' style='font-size : 11px;'>"+
						"<td width='600px'></td>"+
						"<td width='100px' rowspan='2'>REAL<br>"+(xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value-2)+"</td>"+
						"<td width='100px' rowspan='2'>ESTIMADO<br>"+(xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value-1)+"</td>"+
						"<td width='100px' rowspan='2'>TOTAL<br>"+xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value+"</td>"+
						"<td width='900px' colspan='12'>PRESUPUESTO DE GASTO "+xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value+"</td>"+
					"</tr>"+
					"<tr class='CabeceraTablaEstilo' style='font-size : 11px;'>"+
						"<td width='600px'>CUENTA PRESUPUESTARIA</td>"+
						"<td width='75px'>ENE</td>"+
						"<td width='75px'>FEB</td>"+
						"<td width='75px'>MAR</td>"+
						"<td width='75px'>ABR</td>"+
						"<td width='75px'>MAY</td>"+
						"<td width='75px'>JUN</td>"+
						"<td width='75px'>JUL</td>"+
						"<td width='75px'>AGO</td>"+
						"<td width='75px'>SEP</td>"+
						"<td width='75px'>OCT</td>"+
						"<td width='75px'>NOV</td>"+
						"<td width='75px'>DIC</td>"+
					"</tr>"+
					"<tr>"+
						"<td colspan='4'><TABLE width='100%'>"+IconoCargandoTabla+"</TABLE></td>"+
					"</tr>";

	xGetElementById("TABLA_FORMULACION_FFPDG").innerHTML=Contenido;
	//var sw2=0;
	//if(sw>=0)
	//	sw2=sw;
	//else
	//	sw2=xGetElementById("sw_formulacion_reformulacion").value;

	AjaxRequest.post({
						'parameters':{
									'action':"onGet",
									'anio':xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value,
									'tipo':xGetElementById("TIPO_FFPDG").value,
									'id_accion_subespecifica':xGetElementById("OAE_FFPDG").value
									},
						'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							
							//FFPDG_COMPROBANTE_GENERADO
							
							xGetElementById("FFPDG_COMPROBANTE_GENERADO").innerHTML="NO GENERADO";
							var n=resultado.length;
							
							
							
							var AUX="";
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal=0;
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal=new Array();
							for(var i=0;i<n;i++){
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__AgregarEspecifica(resultado[i]);
								}
							//ordenar tabla
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal.sort(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__OrdenarDatos);
							
							//recalcular totales
							for(var i=0;i<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;i++)
								if(!Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["padre"])
									for(var h=0;h<=14;h++)
										Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,h);
							
							


							//si es asignado bloquear boton guardar
							if(n>0 && resultado[0]["id_comprobante_apertura"]>0) {
								xGetElementById("FFPDG_COMPROBANTE_GENERADO").innerHTML=""+resultado[0]["comprobante_apertura"]+"";
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion(true);
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarBotonGuardar();
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarFormulario();
								DesactivarBoton("BOTON_NUEVO_FFPDG","IMG_NUEVO_FFPDG",'nuevo');
							}
							else{
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarBotonGuardar();
								Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ActivarFormulario();
								ActivarBoton("BOTON_NUEVO_FFPDG","IMG_NUEVO_FFPDG",'nuevo');
							}
							
							
							
							/*
							if(i>0)
								if(resultado[0]["asignado"]=="t" && !(xGetElementById("sw_formulacion_reformulacion").value=="1" || xGetElementById("sw_formulacion_reformulacion").value=="2")){
									Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje("El presupuesto se encuentra asignado.");
									Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion(true);
									Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarBotonGuardar();
									Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DesactivarFormulario();
									DesactivarBoton("BOTON_NUEVO_FFPDG","IMG_NUEVO_FFPDG",'nuevo');
									//mostrar botón deshacer asignación
									xGetElementById("TD_DESHACER_ASIGNACION").innerHTML="<INPUT type='button'  class='BotonesParaCampos' value='Deshacer asignación' onclick='Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DeshacerAsignacion()'>";
									return;
									}*/
							

							},
					 'url':'../formulacion/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });

	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__AgregarEspecifica(registro){
	var n=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;
	
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]=new Array();
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["id_cuenta_presupuestaria"]=registro["id_cuenta_presupuestaria"];
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]=registro["denominacion"];
	if(registro["padre"]=="t")
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=true;
	else
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=false;

	for(var j=0;j<=14;j++){
		AUX=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[j];
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n][AUX]=registro[AUX];
		}
	n++;
	
	//agregar las cuentas padres
	
	var aux_codigo=registro["id_cuenta_presupuestaria"];
	
	//ver cuales son las cuentas que deberian agregarse
	var padre=new Array();
	var k;
	padre[0]=aux_codigo;
	padre[1]=aux_codigo.substring(0,1)+"00000000";
	padre[2]=aux_codigo.substring(0,3)+"000000";
	padre[3]=aux_codigo.substring(0,5)+"0000";
	padre[4]=aux_codigo.substring(0,7)+"00";
	if(padre[4]==aux_codigo)
		k=4;
	else
		k=5;
	
	var sw;
	var n;
	var AUX="";
	for(i=1;i<k;i++){
		sw=false;
		
		for(var j=0;j<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;j++)
			if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[j]["id_cuenta_presupuestaria"]==padre[i]){
				sw=true;
				break;
				}
		//sino la encontro agregarla
		if(sw==false){
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]=new Array();			
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["id_cuenta_presupuestaria"]=padre[i];
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]="";
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=true;
			var respuesta=Ext.Ajax.request({
				async: false,
				url: "module/cuenta_presupuestaria/",
				method: 'POST',
				params:{
					action: 'onGet',
					id_cuenta_presupuestaria: Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["id_cuenta_presupuestaria"]
					}				
				});
			if(respuesta.statusText=="OK") {
				cuenta_presupuestaria=SIGA.Ext.decode(respuesta.responseText);
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]=cuenta_presupuestaria[0]["denominacion"];
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=cuenta_presupuestaria[0]["padre"]=='t'?true:false;
				}
			n++;
			}
		}	
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal=n;
	}


function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Agregar(){
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje("");
	Form_LISTA_CUENTAS_PRESUPUESTARIAS__Abrir(	'CODIGO_CTA_PRESUPUESTARIA_FFPDG',
												'DENOMINACION_CTA_PRESUPUESTARIA_FFPDG',
												'4%',
												"Form_FORMULACION_PRESUPUESTO_DE_GASTOS__PostAgregar()");
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Quitar(){
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Mensaje("");
	if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion==-1)
		return;
	var i=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion;

	for(var c=0;c<=14;c++){
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]]= "0.00";
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,c);
		}


	var CuentaEliminar=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["id_cuenta_presupuestaria"];
	var padre=new Array();
	var cabeza_padre=new Array();
	var k;
	padre[0]=CuentaEliminar.substring(0,1)+"00000000";	cabeza_padre[0]=CuentaEliminar.substring(0,1);
	padre[1]=CuentaEliminar.substring(0,3)+"000000";	cabeza_padre[1]=CuentaEliminar.substring(0,3);
	padre[2]=CuentaEliminar.substring(0,5)+"0000";		cabeza_padre[2]=CuentaEliminar.substring(0,5);
	padre[3]=CuentaEliminar.substring(0,7)+"00";		cabeza_padre[3]=CuentaEliminar.substring(0,7);
	if(padre[3]==CuentaEliminar)
		k=2;
	else
		k=3;

	var aux;
	//elimino la cuenta seleccionada
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal--;
	for(var m=i;m<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;m++){
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["id_cuenta_presupuestaria"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["id_cuenta_presupuestaria"];
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["denominacion"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["denominacion"];
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["padre"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["padre"];
		for(var c=0;c<=14;c++)
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]];
		}

	//para cada padre buscar si tienen hijos, si no tiene, eliminarlo
	var pos_padre;
	var sw;
	for(var o=k;o>=0;o--){
		pos_padre=-1;
		for(var c=0;c<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;c++)
			if(!Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["padre"]){//si es hijo
				if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["id_cuenta_presupuestaria"].indexOf(cabeza_padre[o])==0){//si el padre del elimando tiene hijo
					Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
					return;//encontro un hijo, no se puede eliminar el padre
					}
				}
			else{//si es padre, guardo la posicion del padre actual padre[o]
				if(padre[o]==Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["id_cuenta_presupuestaria"])
					pos_padre=c;
				}

		if(pos_padre!=-1){//eliminar pos_padre
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal--;
			for(var m=pos_padre;m<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;m++){
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["id_cuenta_presupuestaria"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["id_cuenta_presupuestaria"];
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["denominacion"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["denominacion"];
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m]["padre"]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1]["padre"];
				for(var c=0;c<=14;c++)
					Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]]=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[m+1][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]];
				}
			}
		}
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__PostAgregar(){
	var aux_codigo=xGetElementById("CODIGO_CTA_PRESUPUESTARIA_FFPDG").value;
	var aux_denominacion=xGetElementById("DENOMINACION_CTA_PRESUPUESTARIA_FFPDG").value;

	//ver cuales son las cuentas que deberian agregarse
	var padre=new Array();
	var k;
	padre[0]=aux_codigo;
	padre[1]=aux_codigo.substring(0,1)+"00000000";
	padre[2]=aux_codigo.substring(0,3)+"000000";
	padre[3]=aux_codigo.substring(0,5)+"0000";
	padre[4]=aux_codigo.substring(0,7)+"00";
	if(padre[4]==aux_codigo)
		k=4;
	else
		k=5;



	//agregar las cuentas si y solo si no existen
	var sw;
	var n;
	var AUX="";
	for(i=0;i<k;i++){
		sw=false;
		for(var j=0;j<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;j++)
			if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[j]["id_cuenta_presupuestaria"]==padre[i]){
				if(i==0){
					alert("La cuenta selecciona ya se encuentra agregada.");
					return false;
					}
				sw=true;
				break;
				}
		if(sw==false){//si no la encontro, agregarla
			n=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]=new Array();
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["id_cuenta_presupuestaria"]=padre[i];
			if(i==0){//si es el hijo
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=false;
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]=aux_denominacion;
				}
			else{//buscar denominacion del padre
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["padre"]=true;
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]="";
				var respuesta=Ext.Ajax.request({
					async: false,
					url: "module/cuenta_presupuestaria/",
					method: 'POST',
					params:{
						action: 'onGet',
						id_cuenta_presupuestaria: Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["id_cuenta_presupuestaria"]
					}				
				});
				if(respuesta.statusText=="OK") {
					cuenta_presupuestaria=SIGA.Ext.decode(respuesta.responseText);
					Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n]["denominacion"]=cuenta_presupuestaria[0]["denominacion"];
					}
				}
			for(var j=0;j<=14;j++){
				AUX=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[j];
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[n][AUX]=0;
				}
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal++;
			}
		}//for(i=0;i<k;i++)
	//ordenar tabla por id_codigo_plan_unico
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal.length=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal.sort(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__OrdenarDatos);
	//re imprimir la tabla en pantalla
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
	return true;
	}


function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__OrdenarDatos(a,b) {
	var stringA=a["id_cuenta_presupuestaria"];
	var stringB=b["id_cuenta_presupuestaria"];
	if(stringA>stringB)
		return 1;
	if(stringA<stringB)
		return -1;
	return 0;
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion(bloquear){
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion=-1;
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var estilo_fila="";
	var FuncionOnDblclickTDMonto="";
	var sw;
	var style;

	var Contenido="<tr class='CabeceraTablaEstilo' style='font-size : 11px;'>"+
						"<td width='600px'></td>"+
						"<td width='100px' rowspan='2'>REAL<br>"+(xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value-2)+"</td>"+
						"<td width='100px' rowspan='2'>ESTIMADO<br>"+(xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value-1)+"</td>"+
						"<td width='100px' rowspan='2'>TOTAL<br>"+xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value+"</td>"+
						"<td width='900px' colspan='12'>PRESUPUESTO DE GASTO "+xGetElementById("ANIO_PRESUPUESTARIO_ACTUAL").value+"</td>"+
					"</tr>"+
					"<tr class='CabeceraTablaEstilo' style='font-size : 11px;'>"+
						"<td width='600px'>CUENTA PRESUPUESTARIA</td>"+
						"<td width='75px'>ENE</td>"+
						"<td width='75px'>FEB</td>"+
						"<td width='75px'>MAR</td>"+
						"<td width='75px'>ABR</td>"+
						"<td width='75px'>MAY</td>"+
						"<td width='75px'>JUN</td>"+
						"<td width='75px'>JUL</td>"+
						"<td width='75px'>AGO</td>"+
						"<td width='75px'>SEP</td>"+
						"<td width='75px'>OCT</td>"+
						"<td width='75px'>NOV</td>"+
						"<td width='75px'>DIC</td>"+
					"</tr>";

	xGetElementById("TABLA_FORMULACION_FFPDG").innerHTML=Contenido;
	var pre="";


	for(var i=0;i<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;i++){
		FuncionOnclick="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__SeleccionarElementoTablaFormulacion("+i+")";
		FuncionOnMouseOver="pintarFila(\"TF_FFPDG_FILA_"+i+"\")";
 		FuncionOnMouseOut="despintarFila(\"TF_FFPDG_FILA_"+i+"\")";

		Contenido+="<TR id='TF_FFPDG_FILA_"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		style="";
		FuncionOnDblclickTDMonto="";
		if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["padre"]){
			estilo_fila="FilaEstiloBloqueado";
			FuncionOnclick="";
			FuncionOnDblclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			sw=true;
			style="style='font-weight : bold; font-size : 11px;'";
			}
		else{
			estilo_fila="FilaEstilo";
			FuncionOnclick="";
			FuncionOnDblclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			sw=false;
			}

		if(bloquear)
			sw=true;

 		Contenido+="<TD class='"+estilo_fila+"'><DIV style='overflow : hidden; width:600px;'><UL style='list-style-type : none; margin-bottom : 0; margin-left : 0; margin-right : 0%; margin-top : 0; padding-bottom : 0px; padding-left : 0px; padding-right : 0; padding-top : 0; text-align : left; white-space : nowrap; font-size : 11px;'><li "+style+"><strong>"+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["id_cuenta_presupuestaria"]+"</strong> "+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["denominacion"]+"</li></UL><DIV></TD>";

		for(var h=0;h<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion.length;h++){
			if(sw==false)
				FuncionOnDblclickTDMonto="Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCelda("+i+","+h+")";
			Contenido+="<TD class='"+estilo_fila+"' "+style+" align='right' ondblclick=\""+FuncionOnDblclickTDMonto+"\" id='TF_FFPDG_"+i+"_"+h+"' title='"+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Titulo(h)+"'>"+FormatearNumero(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]])+"</TD>";
			}
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_FORMULACION_FFPDG").innerHTML=Contenido;
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Titulo(h){
	switch(h){
		case 0: return "REAL";
		case 1: return "ESTIMADO";
		case 2: return "TOTAL";
		case 3: return "ENERO";
		case 4: return "FEBRERO";
		case 5: return "MARZO";
		case 6: return "ABRIL";
		case 7: return "MAYO";
		case 8: return "JUNIO";
		case 9: return "JULIO";
		case 10:return "AGOSTO";
		case 11:return "SEPTIEMBRE";
		case 12:return "OCTUBRE";
		case 13:return "NOVIEMBRE";
		case 14:return "DICIEMBRE";
		}
	return "";
	}


var Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion=-1;
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__SeleccionarElementoTablaFormulacion(i){
	if(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion!=-1)
		xGetElementById("TF_FFPDG_FILA_"+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("TF_FFPDG_FILA_"+i).bgColor=colorBase;
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__iSeleccionActualTablaFormulacion=i;
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCelda(i,h){
	if(xGetElementById("FFPDG_txt_celda"))
		return;
	Valor=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]];
	if(Valor*1==0)
		Valor="";
	xGetElementById("TF_FFPDG_"+i+"_"+h).innerHTML="<INPUT id='FFPDG_txt_celda' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCeldaPierdeFoco("+i+","+h+")\" onkeypress=\"return AcceptNum(event,'FFPDG_txt_celda');\" style='text-align : right;' onkeyup='Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KeyPressEnter(event,"+i+","+h+")';>";
	xGetElementById("FFPDG_txt_celda").focus();
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KeyPressEnter(event,i,h){
	if(event.keyCode==13 || event.keyCode==40){//si es enter o key down
		//xGetElementById("TF_FFPDG_"+i+"_"+h).onblur();
		xGetElementById("FFPDG_txt_celda").onblur="";
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCeldaPierdeFoco(i,h);
		//busco el siguente hacia abajo que no sea padre
		for(var b=i+1;b<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;b++)
			if(!Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[b]["padre"]){
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCelda(b,h);
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__SeleccionarElementoTablaFormulacion(b);
				break;
				}
		}
	else if(event.keyCode==38){//key up
		xGetElementById("FFPDG_txt_celda").onblur="";
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCeldaPierdeFoco(i,h);
		//busco el siguente hacia arriba que no sea padre
		for(var b=i-1;b>=0;b--)
			if(!Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[b]["padre"]){
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCelda(b,h);
				Form_FORMULACION_PRESUPUESTO_DE_GASTOS__SeleccionarElementoTablaFormulacion(b);
				break;
				}
		}
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ModificarValorCeldaPierdeFoco(i,h){
	if(!xGetElementById("TF_FFPDG_"+i+"_"+h)) return;
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]=numberFormat(xGetElementById("FFPDG_txt_celda").value,2);
	xGetElementById("TF_FFPDG_"+i+"_"+h).innerHTML=FormatearNumero(Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]);

	//si modifico el total anio, dividir el monto/12 para cada mes
	var monto_asignar;
	var monto_original;
	if(h==2){//total anio
		monto_original=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]];
		monto_asignar=numberFormat(monto_original/12,0);

		for(var c=3;c<=13;c++){//para cada mes hasta noviembre
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]]=monto_asignar;
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,c);
			}
		//para diciembre
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[14]]=monto_original-monto_asignar*11;
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,14);

		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]= numberFormat(monto_original,2);

		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,h);
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
		return;
		}

	//si modifico uno de los meses
	//hacer calculo del mes
	//sumar todos los meses
	//hacer el calculo del total anho
	if(h>=3 && h<=14){//rango de meses en la matriz principal
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,h);
		monto_asignar=0;
		for(var c=3;c<=14;c++)
			monto_asignar+=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[c]]*1.0;
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[2]]= numberFormat(monto_asignar,2);
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,2);
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
		return;
		}


	//actualizar montos
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,h);
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__MostrarTablaFormulacion();
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CalcularMontosRelacionado(i,h){//hace el calculo solo por la columna Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]
	//cuenta modificada
	var CuentaModificada=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[i]["id_cuenta_presupuestaria"];
	var padre=new Array();
	var cabeza_padre=new Array();
	var k;
	padre[0]=CuentaModificada.substring(0,1)+"00000000";	cabeza_padre[0]=CuentaModificada.substring(0,1);
	padre[1]=CuentaModificada.substring(0,3)+"000000";		cabeza_padre[1]=CuentaModificada.substring(0,3);
	padre[2]=CuentaModificada.substring(0,5)+"0000";		cabeza_padre[2]=CuentaModificada.substring(0,5);
	padre[3]=CuentaModificada.substring(0,7)+"00";			cabeza_padre[3]=CuentaModificada.substring(0,7);
	if(padre[3]==CuentaModificada)
		k=2;
	else
		k=3;


	//sumar todos los hijos de padre[k]
	var SUMA=0;
	var pos;
	var pos_padre=-1;
	var CuentaActual="";
	for(var c=0;c<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;c++){
		CuentaActual=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["id_cuenta_presupuestaria"];
		if(CuentaActual.indexOf(cabeza_padre[k])==0){
			if(!Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["padre"])
				SUMA=SUMA+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]*1.0;
			else{//si es padre, verifico si es el padre de la cuenta modificada
				if(CuentaActual==padre[k])
					pos_padre=c;
				}
			}
		}
	//coloco la suma en el padre
	if(pos_padre!=-1)
		Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[pos_padre][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]=numberFormat(SUMA,2);

	for(var o=k-1;o>=0;o--){
		pos_padre=-1;
		SUMA=0;
		//sumar todos los hijos de padre[k-1] que son padres unicamente (no sumar los hijos)
		for(var c=0;c<Form_FORMULACION_PRESUPUESTO_DE_GASTOS__KArregloPrincipal;c++){
			CuentaActual=Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c]["id_cuenta_presupuestaria"];
			if(CuentaActual==padre[o])
				pos_padre=c;
			else{
				sw=false;
				if(cabeza_padre[o].length==3){
					if(CuentaActual.indexOf(cabeza_padre[o])==0 && CuentaActual.lastIndexOf("0000")==5)//si es de la forma XXX**0000
						sw=true;
					}
				else if(cabeza_padre[o].length==5){
					if(CuentaActual.indexOf(cabeza_padre[o])==0 && CuentaActual.lastIndexOf("00")==7)//si es de la forma XXXXX**00
						sw=true;
					}
				else{// == 1
					if(CuentaActual.indexOf(cabeza_padre[o])==0 && CuentaActual.lastIndexOf("000000")==3)//si es de la forma X**000000
						sw=true;
					}
				if(sw==true)
					SUMA=SUMA+Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[c][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]*1.0;
				}
			}
		if(pos_padre!=-1)
			Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloPrincipal[pos_padre][Form_FORMULACION_PRESUPUESTO_DE_GASTOS__ArregloCamposTablaFormulacion[h]]=numberFormat(SUMA,2);
		}

	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Asignar(){
	if(!confirm("¿Seguro que desea asignar el presupuesto?"))
		return;
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Guardar(true);
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DeshacerAsignacion(){
	var IDEstructuraPresupuestaria	= xTrim(strtoupper(xGetElementById("OAE_FFPDG").value));
	AjaxRequest.post({'parameters':{'accion':"Form_FORMULACION_PRESUPUESTO_DE_GASTOS__DeshacerAsignacion",
									'id_estructura_presupuestaria':IDEstructuraPresupuestaria},
					'onSuccess':
					function(req){
						var respuesta = req.responseText;
						var resultado = eval("(" + respuesta + ")");
						if(resultado!=1)
							alert(resultado);
						Form_FORMULACION_PRESUPUESTO_DE_GASTOS__CargarFormulacion();
						},
					'url':'../modulo_presupuesto/consultas.php',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});

	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS__Visualizar(modal) {
	siga.open("reporte_formulacion",{modal: true});
}

