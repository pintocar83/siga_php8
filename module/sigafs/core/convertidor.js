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

var Form_CONVERTIDOR_GENERAL__ArregloPrincipal=new Array();
var Form_CONVERTIDOR_GENERAL__KArregloPrincipal=0;


/**
* Muestra los mensajes en la parte superior del formulario
* @param {string} MSG Mensaje a mostrar
* @param {string} color del mensaje
*/
function Form_CONVERTIDOR_GENERAL__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCG").innerHTML=MSG;
	}

/**
* Activa el boton guardar
*/
function Form_CONVERTIDOR_GENERAL__ActivarBotonGuardar(){
	ActivarBoton("BOTON_GUARDAR_FCG","IMG_GUARDAR_FCG",'guardar');
	}

/**
* Desactiva el boton guardar
*/
function Form_CONVERTIDOR_GENERAL__DesactivarBotonGuardar(){
	DesactivarBoton("BOTON_GUARDAR_FCG","IMG_GUARDAR_FCG",'guardar');
	}

var Form_CONVERTIDOR_GENERAL__ArregloPlanUnicoCuentas;
function Form_CONVERTIDOR_GENERAL__Inicializar(){
	xGetElementById("TABLA_LISTA_FCG").innerHTML=IconoCargandoTabla;
	Form_CONVERTIDOR_GENERAL__DesactivarBotonGuardar();
	Form_CONVERTIDOR_GENERAL__ArregloPrincipal=new Array();
	Form_CONVERTIDOR_GENERAL__KArregloPrincipal=0;
	
	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':"",
									'filtro': '%',
									'start':0,
									'limit':'ALL',
									'sort':'[{"property":"cuenta_presupuestaria","direction":"ASC"},{"property":"denominacion","direction":"ASC"}]'
									},
					 'onSuccess':
							function(req){
								var respuesta = req.responseText;
								respuesta=eval("(" + respuesta + ")");
								Form_CONVERTIDOR_GENERAL__ArregloPlanUnicoCuentas = respuesta["result"];
								Form_CONVERTIDOR_GENERAL__BuscarListado();
								Form_CONVERTIDOR_GENERAL__ActivarBotonGuardar();
								},
					 'url':'../cuenta_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_CONVERTIDOR_GENERAL__BuscarListado(){	
	xGetElementById("TABLA_LISTA_FCG").innerHTML=IconoCargandoTabla;
	
	AjaxRequest.post({'parameters':{ 'action':"onList",
									'text':"",
									//'filtro': '%',
									'start':0,
									'limit':'ALL',
									'sort':'[{"property":"id_cuenta_presupuestaria","direction":"ASC"}]'
									},
					 'onSuccess':
							function(req){
								var respuesta = req.responseText;
								respuesta=eval("(" + respuesta + ")");
								//console.log(respuesta);
								var ArregloAux = respuesta["result"];
								if(!ArregloAux)
									return;
								Form_CONVERTIDOR_GENERAL__ArregloPrincipal=new Array();
								Form_CONVERTIDOR_GENERAL__KArregloPrincipal=0;
								for(var i=0;i<ArregloAux.length;i++){
									Form_CONVERTIDOR_GENERAL__Agregar(	ArregloAux[i]["id_cuenta_presupuestaria"],
																		ArregloAux[i]["denominacion_presupuestaria"],
																		ArregloAux[i]["id_cuenta_contable"],
																		ArregloAux[i]["denominacion_contable"]);
									}

								Form_CONVERTIDOR_GENERAL__ArregloPrincipal.sort(Form_CONVERTIDOR_GENERAL__OrdenarDatos);
								Form_CONVERTIDOR_GENERAL__MostrarTabla();
								},
					 'url':'../convertidor/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_CONVERTIDOR_GENERAL__BuscarPresupuestaria(){
	Form_CONVERTIDOR_GENERAL__Mensaje('');
	Form_LISTA_CUENTAS_PRESUPUESTARIAS__Abrir(
		'CODIGO_PRESUPUESTARIO_FCG',
		'DENOMINACION_CTA_PRESUPUESTARIA_FCG',
		'4%',
		"Form_CONVERTIDOR_GENERAL__PostBuscarPresupuestaria();");
	}

function Form_CONVERTIDOR_GENERAL__PostBuscarPresupuestaria(){
	xGetElementById('CODIGO_CONTABLE_FCG').value='';
	xGetElementById('DENOMINACION_CTA_CONTABLE_FCG').value="";
	//buscar ya se se agregó
	for(var i=0;i<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;i++)
		if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]["id_cuenta_presupuestaria"]==xGetElementById('CODIGO_PRESUPUESTARIO_FCG').value){
			alert("La cuenta selecciona ya se encuentra agregada.");
			return false;
			}
	Form_CONVERTIDOR_GENERAL__MostrarTabla();//para desseleccionar el elemento actual
	return true;
	}

function Form_CONVERTIDOR_GENERAL__BuscarContable(){
	Form_CONVERTIDOR_GENERAL__Mensaje('');
	if(xTrim(xGetElementById('CODIGO_PRESUPUESTARIO_FCG').value)==""){
		Form_CONVERTIDOR_GENERAL__Mensaje("Debe seleccionar la presupuestaria.","ROJO");
		return;
	}

	Form_LISTA_CUENTAS_CONTABLES__Abrir('CODIGO_CONTABLE_FCG',
										'DENOMINACION_CTA_CONTABLE_FCG',
										"",
										"",
										"Form_CONVERTIDOR_GENERAL__Agregar()");

	
	}


function Form_CONVERTIDOR_GENERAL__Agregar(A,B,C,D){
	var aux_codigo="";
	var aux_denominacion="";
	var aux_codigo2="";
	var aux_denominacion2="";

	if(A){
		aux_codigo=A;
		aux_denominacion=B;
		aux_codigo2=C;
		aux_denominacion2=D;
		}
	else{
		if(Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion==-1){//no no hay nada seleccionado es nuevo
			if(xTrim(xGetElementById("CODIGO_PRESUPUESTARIO_FCG").value)==""){
				Form_CONVERTIDOR_GENERAL__Mensaje("Debe seleccionar la presupuestaria.","ROJO");
				return false;
				}
			if(xTrim(xGetElementById("CODIGO_CONTABLE_FCG").value)==""){
				Form_CONVERTIDOR_GENERAL__Mensaje("Debe seleccionar la cuenta contable.","ROJO");
				return false;
				}
			aux_codigo=xGetElementById("CODIGO_PRESUPUESTARIO_FCG").value;
			aux_denominacion=xGetElementById("DENOMINACION_CTA_PRESUPUESTARIA_FCG").value;
			aux_codigo2=xGetElementById("CODIGO_CONTABLE_FCG").value;
			aux_denominacion2=xGetElementById("DENOMINACION_CTA_CONTABLE_FCG").value;
			}
		else{//si es modificar
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion]["id_cuenta_presupuestaria"]=xGetElementById("CODIGO_PRESUPUESTARIO_FCG").value;
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion]["denominacion_presupuestaria"]=xGetElementById("DENOMINACION_CTA_PRESUPUESTARIA_FCG").value;
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion]["id_cuenta_contable"]=xGetElementById("CODIGO_CONTABLE_FCG").value;
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion]["denominacion_contable"]=xGetElementById("DENOMINACION_CTA_CONTABLE_FCG").value;
			
			xGetElementById("CODIGO_PRESUPUESTARIO_FCG").value="";
			xGetElementById("DENOMINACION_CTA_PRESUPUESTARIA_FCG").value="";
			xGetElementById("CODIGO_CONTABLE_FCG").value="";
			xGetElementById("DENOMINACION_CTA_CONTABLE_FCG").value="";
			
			Form_CONVERTIDOR_GENERAL__MostrarTabla();
			
			return true;
			}
		}

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
	for(var i=0;i<k;i++){
		sw=false;
		for(var j=0;j<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;j++)
			if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[j]["id_cuenta_presupuestaria"]==padre[i]){
				if(i==0){
					Form_CONVERTIDOR_GENERAL__Mensaje("La cuenta selecciona ya se encuentra agregada.","ROJO");
					return false;
					}
				sw=true;
				break;
				}
		if(sw==false){//si no la encontro, agregarla
			n=Form_CONVERTIDOR_GENERAL__KArregloPrincipal;
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]=new Array();
			Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["id_cuenta_presupuestaria"]=padre[i];
			if(i==0){//si es el hijo
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["padre"]=false;
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["denominacion_presupuestaria"]=aux_denominacion;
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["id_cuenta_contable"]=aux_codigo2;
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["denominacion_contable"]=aux_denominacion2;
				}
			else{//buscar denominacion del padre
				
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["padre"]=true;
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["denominacion_presupuestaria"]="";
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["id_cuenta_contable"]="";
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["denominacion_contable"]="";
				
				
				for(var s=0;s<Form_CONVERTIDOR_GENERAL__ArregloPlanUnicoCuentas.length;s++)
					if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["id_cuenta_presupuestaria"]==Form_CONVERTIDOR_GENERAL__ArregloPlanUnicoCuentas[s]["id_cuenta_presupuestaria"]){
						Form_CONVERTIDOR_GENERAL__ArregloPrincipal[n]["denominacion_presupuestaria"]=Form_CONVERTIDOR_GENERAL__ArregloPlanUnicoCuentas[s]["denominacion"];
						break;
						}
						
				}
			Form_CONVERTIDOR_GENERAL__KArregloPrincipal++;
			}
		}

	if(!A){
		//ordenar tabla por id_cuenta_presupuestaria
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal.length=Form_CONVERTIDOR_GENERAL__KArregloPrincipal;
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal.sort(Form_CONVERTIDOR_GENERAL__OrdenarDatos);
		//re imprimir la tabla en pantalla
		Form_CONVERTIDOR_GENERAL__MostrarTabla();
		}

	xGetElementById('CODIGO_PRESUPUESTARIO_FCG').value="";
	xGetElementById('CODIGO_CONTABLE_FCG').value="";
	xGetElementById('DENOMINACION_CTA_PRESUPUESTARIA_FCG').value="";
	xGetElementById('DENOMINACION_CTA_CONTABLE_FCG').value="";
	return true;
	}

function Form_CONVERTIDOR_GENERAL__OrdenarDatos(a,b){
	var stringA=a["id_cuenta_presupuestaria"];
	var stringB=b["id_cuenta_presupuestaria"];
	if(stringA>stringB)
		return 1;
	if(stringA<stringB)
		return -1;
	return 0;
	}

function Form_CONVERTIDOR_GENERAL__Quitar(i_quitar){
	Form_CONVERTIDOR_GENERAL__Mensaje('');

	if(!i_quitar){
		if(Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion==-1)
			return;
		var i=Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion;
		}
	else{
		i=i_quitar;
		if(i<0) return;
		}

	var CuentaEliminar=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]["id_cuenta_presupuestaria"];
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
	Form_CONVERTIDOR_GENERAL__KArregloPrincipal--;
	for(var m=i;m<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;m++){
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["id_cuenta_presupuestaria"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["id_cuenta_presupuestaria"];
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["denominacion_presupuestaria"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["denominacion_presupuestaria"];
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["padre"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["padre"];
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["id_cuenta_contable"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["id_cuenta_contable"];
		Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["denominacion_contable"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["denominacion_contable"];
		}

	//para cada padre buscar si tienen hijos, si no tiene, eliminarlo
	var pos_padre;
	var sw;
	for(var o=k;o>=0;o--){
		pos_padre=-1;
		for(var c=0;c<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;c++)
			if(!Form_CONVERTIDOR_GENERAL__ArregloPrincipal[c]["padre"]){//si es hijo
				if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[c]["id_cuenta_presupuestaria"].indexOf(cabeza_padre[o])==0){//si el padre del elimando tiene hijo
					if(!i_quitar)
						Form_CONVERTIDOR_GENERAL__MostrarTabla();
					return;//encontro un hijo, no se puede eliminar el padre
					}
				}
			else{//si es padre, guardo la posicion del padre actual padre[o]
				if(padre[o]==Form_CONVERTIDOR_GENERAL__ArregloPrincipal[c]["id_cuenta_presupuestaria"])
					pos_padre=c;
				}

		if(pos_padre!=-1){//eliminar pos_padre
			Form_CONVERTIDOR_GENERAL__KArregloPrincipal--;
			for(var m=pos_padre;m<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;m++){
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["id_cuenta_presupuestaria"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["id_cuenta_presupuestaria"];
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["denominacion_presupuestaria"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["denominacion_presupuestaria"];
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["padre"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["padre"];
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["id_cuenta_contable"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["id_cuenta_contable"];
				Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m]["denominacion_contable"]=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[m+1]["denominacion_contable"];
				}
			}
		}
	if(!i_quitar)
		Form_CONVERTIDOR_GENERAL__MostrarTabla();
	}

function Form_CONVERTIDOR_GENERAL__QuitarNoAsociados(){
	for(var i=0;i<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;i++)
		if(!Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]["padre"])
			if(xTrim(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]["id_cuenta_contable"])==""){
				Form_CONVERTIDOR_GENERAL__Quitar(i);
				i=0;
				}
	Form_CONVERTIDOR_GENERAL__MostrarTabla();
	}


var Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion=-1;
function Form_CONVERTIDOR_GENERAL__MostrarTabla(){
	Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion=-1;
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var estilo_fila="";
	var FuncionOnDblclickTDMonto="";
	var sw;
	var style;
	var style2;
	var Contenido="";

	for(var i=0;i<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;i++){
		FuncionOnclick="Form_CONVERTIDOR_GENERAL__SeleccionarElementoTablaFormulacion("+i+")";
		FuncionOnMouseOver="pintarFila(\"TF_FCG_FILA_"+i+"\")";
 		FuncionOnMouseOut="despintarFila(\"TF_FCG_FILA_"+i+"\")";


		Contenido+="<TR id='TF_FCG_FILA_"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		style="";style2="";
		FuncionOnDblclickTDMonto="";
		if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['padre']){
			estilo_fila="FilaEstiloBloqueado";
			FuncionOnclick="";
			FuncionOnDblclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			sw=true;
			style="font-weight : bold;";
			}
		else{
			estilo_fila="FilaEstilo";
			FuncionOnclick="";
			FuncionOnDblclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			sw=false;
			if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_contable']=="")
				style2="style='color : #FF0004;'";
			else if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_contable']==" ")
				style2="style='color : #FF6156;'";
			}

		Contenido+="<TD class='"+estilo_fila+"' width='50%' style='max-width : 425px; overflow : hidden; white-space : nowrap; "+style+"'><strong "+style2+">"+Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_presupuestaria']+"</strong> "+Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['denominacion_presupuestaria']+"</TD>";

		Contenido+="<TD class='"+estilo_fila+"' width='50%' style='max-width : 425px; overflow : hidden; white-space : nowrap; padding-left: 5px; "+style+"'><strong "+style2+">"+Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_contable']+"</strong> "+Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['denominacion_contable']+"</TD>";
		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCG").innerHTML=Contenido;
	}

function Form_CONVERTIDOR_GENERAL__SeleccionarElementoTablaFormulacion(i){
	if(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]["padre"])
		return;
	
	if(Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion!=-1)
		xGetElementById("TF_FCG_FILA_"+Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("TF_FCG_FILA_"+i).bgColor=colorBase;
	Form_CONVERTIDOR_GENERAL__iSeleccionActualTablaFormulacion=i;


	xGetElementById("CODIGO_PRESUPUESTARIO_FCG").value=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_presupuestaria'];
	xGetElementById("DENOMINACION_CTA_PRESUPUESTARIA_FCG").value=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['denominacion_presupuestaria'];
	xGetElementById("CODIGO_CONTABLE_FCG").value=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['id_cuenta_contable'];
	xGetElementById("DENOMINACION_CTA_CONTABLE_FCG").value=Form_CONVERTIDOR_GENERAL__ArregloPrincipal[i]['denominacion_contable'];
	}


function Form_CONVERTIDOR_GENERAL__Guardar(){
	Form_CONVERTIDOR_GENERAL__DesactivarBotonGuardar();
	var _cuentas=[];
	for(var k=0;k<Form_CONVERTIDOR_GENERAL__KArregloPrincipal;k++){
		if(!Form_CONVERTIDOR_GENERAL__ArregloPrincipal[k]["padre"]){
			if(xTrim(Form_CONVERTIDOR_GENERAL__ArregloPrincipal[k]["id_cuenta_contable"])==""){
				Form_CONVERTIDOR_GENERAL__ActivarBotonGuardar();
				Form_CONVERTIDOR_GENERAL__Mensaje("Falta asociar la cuenta "+Form_CONVERTIDOR_GENERAL__ArregloPrincipal[k]["id_cuenta_presupuestaria"]+".","ROJO");
				return;
				}				
			_cuentas.push({
				id_cuenta_presupuestaria: Form_CONVERTIDOR_GENERAL__ArregloPrincipal[k]["id_cuenta_presupuestaria"],
				id_cuenta_contable: Form_CONVERTIDOR_GENERAL__ArregloPrincipal[k]["id_cuenta_contable"]
			 });
			}
		}


	AjaxRequest.post({'parameters':{
									'action':"onSave",
									'cuentas':SIGA.Ext.JSON.encode(_cuentas)
									},
					'onSuccess':Form_CONVERTIDOR_GENERAL__GuardarMensaje,
					'url':'../convertidor/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/**
* Muestra el mensaje despues de guardar los datos
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_CONVERTIDOR_GENERAL__GuardarMensaje(req){
	var respuesta = req.responseText;
	respuesta=SIGA.Ext.JSON.decode(respuesta);
	if(respuesta.success){
		Form_CONVERTIDOR_GENERAL__Mensaje(respuesta.message,"VERDE");
		Form_CONVERTIDOR_GENERAL__BuscarListado();
		}
	else
		Form_CONVERTIDOR_GENERAL__Mensaje(respuesta.message,"ROJO");
	Form_CONVERTIDOR_GENERAL__ActivarBotonGuardar();
	}

