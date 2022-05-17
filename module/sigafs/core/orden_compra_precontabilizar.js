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
* Cuando se escribe en el INPUT TEXT buscar, el sistema va a la BD y actualiza el listado. Sirve para evitar que se vaya a la BD cuando la cadena nueva sea igual a la que se envio anteriormente
*/
var Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado_CadenaBuscar="";

/*ID Seleccion actual de la lista*/
var Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista=-1;

/*Contiene la Denominacion del elemento seleccionado en la lista. Necesario para mandar los datos para el formulario padre*/
var Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista="";
var Form_ORDEN_COMPRA_PRECONTABILIZAR__IDCedulaSeleccionActualLista="";
var Form_ORDEN_COMPRA_PRECONTABILIZAR__IDApellidoNombreSeleccionActualLista="";

var Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo=new Array();
var Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo=0;

/*ID de los campo donde se van a colorcar los datos cuando se Acepte o se haga doble click*/
var Form_ORDEN_COMPRA_PRECONTABILIZAR__IDOrdenDeCompra="";
var Form_ORDEN_COMPRA_PRECONTABILIZAR__LlamarFuncion="";

//Para el total inicial para luego comprararlo a ver si son iguales, si no son iguales no pertimir aceptar
var Form_ORDEN_COMPRA_PRECONTABILIZAR___IDMontoTotalODC="";

/*Abre la ventana. Es llamada con el boton buscar*/
//function Form_ORDEN_COMPRA_PRECONTABILIZAR__Abrir(_IDOrdenDeCompra,_LlamarFuncion,_IDMontoTotalODC){
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo=0;
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__IDOrdenDeCompra=_IDOrdenDeCompra;
//	if(_LlamarFuncion)
//		Form_ORDEN_COMPRA_PRECONTABILIZAR__LlamarFuncion=_LlamarFuncion;
//	if(_IDMontoTotalODC)
//		Form_ORDEN_COMPRA_PRECONTABILIZAR___IDMontoTotalODC=_IDMontoTotalODC;
//	
//	VentanaNueva('VENTANA_ORDEN_COMPRA_PRECONTABILIZAR','PRE-COMPROMETER',660,290,'../modulo_compras/Form_ORDEN_COMPRA_PRECONTABILIZAR.php',true);
//	}

/*
function Form_ORDEN_COMPRA_PRECONTABILIZAR__CargarDatosAlAbrir(){
	ActivarBoton("BOTON_ACEPTAR_OCPC","IMG_ACEPTAR_OCPC",'aceptar');
	xGetElementById("TABLA_LISTA_OCPC").innerHTML="";
	xGetElementById("TABLA_LISTA_OCPC").innerHTML=IconoCargandoTabla;	
	AjaxRequest.post({'parameters':{ 'accion':"Form_ORDEN_COMPRA_PRECONTABILIZAR__CargarDatosAlAbrir", 
									'id_orden_de_compra':Form_ORDEN_COMPRA_PRECONTABILIZAR__IDOrdenDeCompra},
					 'onSuccess':Form_ORDEN_COMPRA_PRECONTABILIZAR__PostCargarDatosAlAbrir,
					 'url':'../modulo_compras/consultas.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });	
	}



function Form_ORDEN_COMPRA_PRECONTABILIZAR__PostCargarDatosAlAbrir(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	var n=resultado.length;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo=n;
	for(var i=0;i<n;i++){
		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i]=new Array(4);
		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][0]=strtoupper(resultado[i]['codigo_programatico']);
		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][1]=strtoupper(resultado[i]['id_codigo_plan_unico']);
		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2]=resultado[i]['total'];
		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]=resultado[i]['id_casadas'];
		}
	Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();	
	}
*/
/*
function Form_ORDEN_COMPRA_PRECONTABILIZAR__Agregar(){
	var i;
	for(i=0;i<Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo;i++)
		if(Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]==xGetElementById("ID_CASADA_OCPC").value){
			alert("La selección ya se encuentra en la lista");
			return false;
			}
	i=Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i]=new Array(4);
	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][0]=xGetElementById("PROGRAMATICA_OCPC").value;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][1]=xGetElementById("CUENTA_OCPC").value;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2]="0.00";
	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]=xGetElementById("ID_CASADA_OCPC").value;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo++;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
	return true;
	}*/

//function Form_ORDEN_COMPRA_PRECONTABILIZAR__Quitar(){
//	if(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista==-1)
//		return;
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo--;
//	for(i=Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista*1;i<Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo;i++){
//		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][0]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i+1][0];
//		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][1]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i+1][1];
//		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i+1][2];
//		Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i+1][3];
//		}
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
//	}







function Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado(){
	//xGetElementById("TABLA_LISTA_OCPC").innerHTML="";
	xGetElementById("TABLA_LISTA_OCPC").innerHTML=IconoCargandoTabla;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista=-1;
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;
	var TOTAL=0;

	for(var i=0;i<Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno.length;i++){
 		FuncionOnclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__SeleccionarElementoTabla("+i+")";
// 					+resultado[i]['id_beneficiario']+"','"
// 					+resultado[i]['cedula_beneficiario']+"','"
// 					+resultado[i]['nombre_beneficiario']+"','"
// 					+resultado[i]['apellido_beneficiario']+"')";		
		//FuncionOnDblclick="Form_ORDEN_COMPRA_PRECONTABILIZAR__Aceptar()";
		FuncionOnMouseOver="pintarFila(\"OCPC"+i+"\")";
		FuncionOnMouseOut="despintarFila(\"OCPC"+i+"\")";

		Contenido+="<TR id='OCPC"+i+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		
		CadAux1=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["estructura_presupuestaria"];
		CadAux2=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["cuenta_presupuestaria"]+" "+Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["denominacion_cuenta_presupuestaria"];//+"/"+Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3];
		CadAux3=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["total"];
		TOTAL+=CadAux3*1.00;		


		//FuncionOnclickCP="Form_ORDEN_COMPRA_PRECONTABILIZAR__CambiarProgramatica('"+Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][1]+"');";
		FuncionOnclickCP="Form_ORDEN_COMPRA_PRECONTABILIZAR__EP("+i+")";
		FuncionOnDblclickMonto="Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCelda('OCPC_MONTO_"+i+"');";

		Contenido+="<TD width='23%' class='FilaEstilo' style='padding: 2px;' align='center' ondblclick=\""+FuncionOnclickCP+"\">"+CadAux1+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua' ondblclick=\""+FuncionOnclickCP+"\">"+CadAux2+"</TD>";
		Contenido+="<TD width='15%' class='FilaEstilo' align='right' id='OCPC_MONTO_"+i+"' ondblclick=\""+FuncionOnDblclickMonto+"\">"+FormatearNumero(CadAux3)+"</TD>";
		

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_OCPC").innerHTML=Contenido;
	xGetElementById("TOTAL_OCPC").value=FormatearNumero(TOTAL);	
	}

/*La funcion es llamada cuando se hace click sobre algun elemento de la tabla.*/
function Form_ORDEN_COMPRA_PRECONTABILIZAR__SeleccionarElementoTabla(IDSeleccion){
	if(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista!=-1)
		xGetElementById("OCPC"+Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("OCPC"+IDSeleccion).bgColor=colorBase;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista=IDSeleccion;
	}




//function Form_ORDEN_COMPRA_PRECONTABILIZAR__CambiarProgramaticaPostAceptar(){
//	if(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista==-1)
//		return;
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista][0]=xGetElementById("PROGRAMATICA_OCPC").value;
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista][3]=xGetElementById("ID_CASADA_OCPC").value;
//	Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
//	}



//Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2]
function Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCelda(_IDCelda){
	if(xGetElementById("txt_celda_"+_IDCelda))
		return;	
	Valor=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista]["total"];
	Valor=numberFormat(Valor,2);

	xGetElementById(_IDCelda).innerHTML="<INPUT id='txt_celda_"+_IDCelda+"' class='TextoCampoInputTabla' type='text' size='15' value='"+Valor+"' onblur=\"Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCeldaPierdeFoco('"+_IDCelda+"',"+Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista+")\" onkeypress=\"return AcceptNum(event,'txt_celda_"+_IDCelda+"');\" style='text-align: right;' onkeyup=\"Form_ORDEN_COMPRA_PRECONTABILIZAR__KeyPressEnter(event,'"+_IDCelda+"',"+Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista+")\">";
	xGetElementById("txt_celda_"+_IDCelda).focus();
	}
function Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar){
	xGetElementById("txt_celda_"+_IDCelda).onblur="";
	Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[indice_modificar]["total"]=numberFormat(xGetElementById("txt_celda_"+_IDCelda).value,"total");
	xGetElementById(_IDCelda).innerHTML=FormatearNumero(Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[indice_modificar]["total"]);
	//actualizar el monto total
	
	var TOTAL=0;
	for(var i=0;i<Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno.length;i++)
		TOTAL+=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["total"]*1.00;
	xGetElementById("TOTAL_OCPC").value=FormatearNumero(TOTAL);
	}


function Form_ORDEN_COMPRA_PRECONTABILIZAR__KeyPressEnter(event,_IDCelda,indice_modificar){
	if(event.keyCode==13 || event.keyCode==40){
		//hacemos que pierda el foco
		Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar);
		if(String(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista)==String(Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno.length-1)){			
			return;
			}
		Form_ORDEN_COMPRA_PRECONTABILIZAR__SeleccionarElementoTabla(parseInt(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista)+1);
		Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCelda("OCPC_MONTO_"+Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista);
		}
	else if(event.keyCode==38){
		Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCeldaPierdeFoco(_IDCelda,indice_modificar);
		if(String(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista)=='0')
			return;
		Form_ORDEN_COMPRA_PRECONTABILIZAR__SeleccionarElementoTabla(parseInt(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista)-1);
		Form_ORDEN_COMPRA_PRECONTABILIZAR__ModificarValorCelda("OCPC_MONTO_"+Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista);
		}
	}



/*Mensaje que se muestra mientras se cargan los datos. (Busqueda de datos en la BD y creacion dinamica de la tabla)*/
function Form_ORDEN_COMPRA_PRECONTABILIZAR__MensajeCargando(){
	xGetElementById("MSG_CARGANDO_OCPC").innerHTML="<DIV style=\"color : #959595; font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 20px; font-style : normal; font-weight : bold; text-align : left;\" align=\"top\">Cargando... Por favor espere...</DIV>";	
	}

/*Es llamada al presionar una tecla en el INPUT TEXT Buscar. Si esta marcada la opcion de 'Solo buscar al presionar enter' retorna y no busca en el listado*/
function Form_ORDEN_COMPRA_PRECONTABILIZAR__Buscar(){
	if(EstadoCheckBoxSombra==xGetElementById("BUSCAR_CHECKBOX_OCPC").checked)
		return;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado();
	}

/**Es llamada al presionar enter o al presionar una tecla en el INPUT TEXT Buscar (Solo si esta desactivado el checkbox descrito anteriormente)*/
var Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno="";
function Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado(){
	//Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista=-1;
	//var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_OCPC").value));
	//
	//if(CadenaBuscar!="")
	//	if(Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado_CadenaBuscar==CadenaBuscar)
	//		return;
	//Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado_CadenaBuscar=CadenaBuscar;
	//
	//Form_ORDEN_COMPRA_PRECONTABILIZAR__MensajeCargando();

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'id_comprobante': Form_ORDEN_COMPRA_PRECONTABILIZAR__id_comprobante					
									},
					'onSuccess': function(req){
						var respuesta = req.responseText;
						var resultado = eval("(" + respuesta + ")");
						Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno=resultado;
						
						//console.log(Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno);
						
						
						//console.log(resultado);
						Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
						//Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado
						},
					'url':'../orden_compra/precontabilizar/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_ORDEN_COMPRA_PRECONTABILIZAR__PresionarEnter(ev){ 
 	if(ev.keyCode==13)
		Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado();
	}


/**
* Es llamada cuando se presiona sobre el boton limpiar. 
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_ORDEN_COMPRA_PRECONTABILIZAR__LimpiarInputTextBuscarListado(){
	Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista=-1;
 	xGetElementById("LISTADO_BUSCAR_OCPC").value="";
 	Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado();
 	DarFocoCampo("LISTADO_BUSCAR_OCPC",1000);
	}

/*Al presiona el boton aceptar o al hacer doble click se mandan los datos al formulario padre y cerramos la ventana*/
//function Form_ORDEN_COMPRA_PRECONTABILIZAR__Aceptar(){
//	var n=Form_ORDEN_COMPRA_PRECONTABILIZAR__TamArreglo;
//	var ArregloIDCasadas=new Array();
//	var ArregloIDCasadasMontos=new Array();
//	var TamArregloIDCasadas=0;
//	var sw=false;
//
//	//metemos en el arreglo todos los id_casadas distintos, no deben repetirse (Agrupar)
//// 	for(var i=0;i<n;i++){
//// 		sw=false;
//// 		for(var j=0;j<TamArregloIDCasadas;j++)
//// 			if(Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]==ArregloIDCasadas[j]){
//// 				sw=true;
//// 				break;
//// 				}
//// 
//// 		if(sw==false){//si no lo encontro, meterlo
//// 			ArregloIDCasadas[TamArregloIDCasadas]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3];
//// 			TamArregloIDCasadas++;
//// 			}		
//// 		}
//// 	
//// 
//// 	for(var j=0;j<TamArregloIDCasadas;j++){
//// 		ArregloIDCasadasMontos[j]=0;
//// 		for(var i=0;i<n;i++){
//// 			if(ArregloIDCasadas[j]==Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3]){
//// 				ArregloIDCasadasMontos[j]+=(Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2])*1.0;
//// 				}
//// 			}		
//// 		}
//
//	for(var i=0;i<n;i++){
//		ArregloIDCasadas[TamArregloIDCasadas]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][3];
//		if(ArregloIDCasadas[TamArregloIDCasadas]==""){
//			alert("Faltan datos.\nLa cuenta "+Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][1]+" no tiene asociada la estructra presupuestaria.\nSeleccionela e intente nuevamente.");
//			return;
//			
//			}
//		ArregloIDCasadasMontos[TamArregloIDCasadas]=Form_ORDEN_COMPRA_PRECONTABILIZAR__Arreglo[i][2];
//		TamArregloIDCasadas++;	
//		}
//
//	//si el monto actual no coincide con el monto de la orden de compra, no permitir aceptar
//	if(Form_ORDEN_COMPRA_PRECONTABILIZAR___IDMontoTotalODC)
//		if(xGetElementById("TOTAL_OCPC").value!=xGetElementById(Form_ORDEN_COMPRA_PRECONTABILIZAR___IDMontoTotalODC).value){
//			alert("El monto total a pre-comprometer no coincide con el monto total de la orden de compra.\nMonto: "+xGetElementById(Form_ORDEN_COMPRA_PRECONTABILIZAR___IDMontoTotalODC).value);
//			return;
//			}
//	
//
//
//
//	xGetElementById("BOTON_ACEPTAR_OCPC").disabled=true;
//
//	AjaxRequest.post({'parameters':{ 'accion':"Form_ORDEN_COMPRA_PRECONTABILIZAR__Precontabilizar", 
//									'id_orden_de_compra':Form_ORDEN_COMPRA_PRECONTABILIZAR__IDOrdenDeCompra,
//									'ARREGLO_ID_CASADAS':ArregloIDCasadas,
//									'ARREGLO_ID_CASADAS_MONTOS':ArregloIDCasadasMontos,
//									'TAM_ARREGLO':TamArregloIDCasadas},
//					 'onSuccess':Form_ORDEN_COMPRA_PRECONTABILIZAR__PostPrecontabilizar,
//					 'url':'../modulo_compras/consultas.php',
//					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
//					 });
//	}

//function Form_ORDEN_COMPRA_PRECONTABILIZAR__PostPrecontabilizar(req){
//	xGetElementById("BOTON_ACEPTAR_OCPC").disabled=false;
//	var respuesta = eval(req.responseText);
//	if(respuesta==1){//si todo esta bien cerramos la ventana
//		if(Form_ORDEN_COMPRA_PRECONTABILIZAR__LlamarFuncion)
//				eval(Form_ORDEN_COMPRA_PRECONTABILIZAR__LlamarFuncion);
//			VentanaCerrar('VENTANA_ORDEN_COMPRA_PRECONTABILIZAR');		
//		}
//	else{//si algo salio mal mostramos un mensaje
//		if(respuesta<=0)
//			alert("Error "+respuesta+". No se pudo pre-comprometer.\nIntente nuevamente.");
//		else
//			alert(respuesta);
//		}	
//	}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__EP(i){
	siga.open("disponibilidad",{
		id_cuenta_presupuestaria: Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["id_cuenta_presupuestaria"],
		modal: true,
		onAccept: function(me, dataview, record, item, index, e){
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["id_accion_subespecifica"]=record.get("id_accion_subespecifica");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["estructura_presupuestaria"]=record.get("estructura_presupuestaria");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[i]["denominacion_cuenta_presupuestaria"]=record.get("denominacion");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
			return true;
		}
	});
}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Agregar(){
	siga.open("disponibilidad",{
		modal: true,
		onAccept: function(me, dataview, record, item, index, e){
			var n=Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno.length;
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]=[];
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["id_accion_subespecifica"]=record.get("id_accion_subespecifica");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["id_cuenta_presupuestaria"]=record.get("id_cuenta_presupuestaria");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["estructura_presupuestaria"]=record.get("estructura_presupuestaria");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["cuenta_presupuestaria"]=record.get("cuenta_presupuestaria");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["denominacion_cuenta_presupuestaria"]=record.get("denominacion");
			Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno[n]["total"]="0.00";
			Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
			return true;
		}
	});
}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Quitar(){
	if(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista==-1)
		return;
	Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno.splice(Form_ORDEN_COMPRA_PRECONTABILIZAR__IDSeleccionActualLista,1);
	Form_ORDEN_COMPRA_PRECONTABILIZAR__MostrarListado();
	}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Restaurar(){
	Form_ORDEN_COMPRA_PRECONTABILIZAR__BuscarListado();
}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Eliminar(){
	siga.window.getCmp("orden_compra/precontabilizar").parameter.onAccept([]);
}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Cerrar(){
	siga.close("orden_compra/precontabilizar");
}

function Form_ORDEN_COMPRA_PRECONTABILIZAR__Aceptar(){
	siga.window.getCmp("orden_compra/precontabilizar").parameter.onAccept(Form_ORDEN_COMPRA_PRECONTABILIZAR__retorno);
}
