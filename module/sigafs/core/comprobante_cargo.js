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
var Form_COMPROBANTE_CARGO__BuscarListado_CadenaBuscar="";


var Form_COMPROBANTE_CARGO__FuncionLlamar="";
var Form_COMPROBANTE_CARGO__Arreglo=new Array();
var Form_COMPROBANTE_CARGO__K=0;
var Form_COMPROBANTE_CARGO__BI=0;
var Form_COMPROBANTE_CARGO__BloquearLista=false;
var Form_COMPROBANTE_CARGO__BIIVA=0;


/*Mensaje que se muestra mientras se cargan los datos. (Busqueda de datos en la BD y creacion dinamica de la tabla)*/
function Form_COMPROBANTE_CARGO__MensajeCargando(){
	xGetElementById("MSG_CARGANDO_FCC").innerHTML="<DIV style=\"color : #959595; font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 20px; font-style : normal; font-weight : bold; text-align : left;\" align=\"top\">Cargando... Por favor espere...</DIV>";
	}

/*Es llamada al presionar una tecla en el INPUT TEXT Buscar. Si esta marcada la opcion de 'Solo buscar al presionar enter' retorna y no busca en el listado*/
function Form_COMPROBANTE_CARGO__Buscar(){
	if(EstadoCheckBoxSombra==xGetElementById("BUSCAR_CHECKBOX_FCC").checked)
		return;
	Form_COMPROBANTE_CARGO__BuscarListado();
	}

/**Es llamada al presionar enter o al presionar una tecla en el INPUT TEXT Buscar (Solo si esta desactivado el checkbox descrito anteriormente)*/
function Form_COMPROBANTE_CARGO__BuscarListado(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCC").value));

	if(CadenaBuscar!="")
		if(Form_COMPROBANTE_CARGO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_COMPROBANTE_CARGO__BuscarListado_CadenaBuscar=CadenaBuscar;

	Form_COMPROBANTE_CARGO__MensajeCargando();

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mes': '',
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"denominacion","direction":"ASC"}]'									
									},
					'onSuccess':Form_COMPROBANTE_CARGO__MostrarListado,
					'url':'../cargo/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_COMPROBANTE_CARGO__PresionarEnter(ev){
 	if(ev.keyCode==13)
		Form_COMPROBANTE_CARGO__BuscarListado();
	}

var EstadoCheckBoxSombra="";
var Form_COMPROBANTE_CARGO__NElementosTabla=0;
var sw_FCC=false;
/*Muestra el listado de cuentas contables (Crea tabla dinamicamente)*/
function Form_COMPROBANTE_CARGO__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;
	Form_COMPROBANTE_CARGO__NElementosTabla=n;
	//Si hay mas de 1000 registros Desactivar Busqueda rapida y resaldado en las coincidencias.
	if(n>1000 && sw_FCC==false){
		sw_FCC=true;
		xGetElementById("SOMBRA_CHECKBOX_FCC").checked=false;
		xGetElementById("BUSCAR_CHECKBOX_FCC").checked=true;
		}

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCC").value));
	EstadoCheckBoxSombra=xGetElementById("SOMBRA_CHECKBOX_FCC").checked;

	xGetElementById("TABLA_LISTA_FCC").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2;

	var EstadoCheck="";
	var SeleccionCheck="";

	for(var i=0;i< n; i++){
		if(Form_COMPROBANTE_CARGO__BloquearLista==false){
			FuncionOnclick="Form_COMPROBANTE_CARGO__SeleccionarElementoTabla('"+i+"');";
			FuncionOnMouseOver="pintarFila(\"FCC"+resultado[i]['id']+"\");";
			FuncionOnMouseOut="despintarFila(\"FCC"+resultado[i]['id']+"\");";
			FuncionClickCheck="Form_COMPROBANTE_CARGO__ClickCheck('"+i+"');";
			DobleClick="Form_COMPROBANTE_CARGO__ModificarCeldaCorrecion('"+i+"');";
			EstadoCheck="";
			}
		else{
			FuncionOnclick="";
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			FuncionClickCheck="";
			DobleClick="";
			EstadoCheck="disabled";
			}
		Contenido+="<TR id='FCC"+resultado[i]['id']+"' ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux1=completarCodigoCeros(resultado[i]['id'],3);
		Concepto=strtoupper(resultado[i]['denominacion']);
		if(TextoBuscar!="" && EstadoCheckBoxSombra){
			CadAux2=str_replace(Concepto,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			}
		else{
			CadAux2=Concepto;
			}
		CadAux3=strtoupper(resultado[i]['formula']);

		SeleccionCheck="";
		SeleccionCorrecion=0;
		SeleccionMonto=0;
 		for(j=0;j<Form_COMPROBANTE_CARGO__Arreglo.length;j++){
 			if(String(Form_COMPROBANTE_CARGO__Arreglo[j]["id"])==String(resultado[i]['id'])){
				SeleccionCheck="checked";
				SeleccionCorrecion=Form_COMPROBANTE_CARGO__Arreglo[j]["correccion"];
				if(Form_COMPROBANTE_CARGO__Arreglo[j]["iva"]==true)//si es iva
					MONTO=Form_COMPROBANTE_CARGO__BIIVA;
				else
					MONTO=Form_COMPROBANTE_CARGO__BI;
				SeleccionMonto=(eval(resultado[i]['formula']))+SeleccionCorrecion*1.0;
 				break;
 				}
 			}
		if(SeleccionCheck=="" && resultado[i]['activo']=="f")
			continue;

		Contenido+="<TD width='3%' class='FilaEstilo'><INPUT type='hidden' id='FCC_ESIVA_"+i+"' value='"+resultado[i]['iva']+"'><INPUT id='FCC_CHECK_"+i+"' type='checkbox' value='"+resultado[i]['id']+"' "+SeleccionCheck+" "+EstadoCheck+" onchange=\""+FuncionClickCheck+"\"></TD>";
		Contenido+="<TD width='7%' class='FilaEstilo' onclick=\""+FuncionOnclick+"\">"+CadAux1+"</TD>";
		Contenido+="<TD width='30%' class='FilaEstilo' onclick=\""+FuncionOnclick+"\">"+CadAux2+"</TD>";
		Contenido+="<TD width='25%' class='FilaEstilo' align='center' id='FCC_FORMULA_"+i+"' onclick=\""+FuncionOnclick+"\">"+CadAux3+"</TD>";

		Contenido+="<TD width='15%' class='FilaEstilo' align='right' ondblclick=\""+DobleClick+"\"><DIV id='FCC_CORRECION_FORMATEADO_"+i+"'>"+FormatearNumero(SeleccionCorrecion)+"</DIV><INPUT id='FCC_CORRECION_MONTO_"+i+"' type='hidden' value='"+SeleccionCorrecion+"'></TD>";

		Contenido+="<TD width='20%' class='FilaEstilo' align='right' id='FCC_MONTO_"+i+"' onclick=\""+FuncionOnclick+"\">"+FormatearNumero(SeleccionMonto)+"</TD>";


		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCC").innerHTML=Contenido;
	xGetElementById("MSG_CARGANDO_FCC").innerHTML="";
	}


function Form_COMPROBANTE_CARGO__ClickCheck(IDSeleccionCheck){
	if(!xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked){
		xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked=false;
		xGetElementById("FCC_CORRECION_FORMATEADO_"+IDSeleccionCheck).innerHTML="0,00";
		xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value="0";
		xGetElementById("FCC_MONTO_"+IDSeleccionCheck).innerHTML="0,00";
		}
	else{
		xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked=true;
		FormulaCargo=xGetElementById("FCC_FORMULA_"+IDSeleccionCheck).innerHTML;
		C=xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value;
		if(xGetElementById("FCC_ESIVA_"+IDSeleccionCheck).value=="t")
			MONTO=Form_COMPROBANTE_CARGO__BIIVA;
		else
			MONTO=Form_COMPROBANTE_CARGO__BI;
		xGetElementById("FCC_MONTO_"+IDSeleccionCheck).innerHTML=FormatearNumero((eval(FormulaCargo))+C*1.0);
		}
	}

/*La funcion es llamada cuando se hace click sobre algun elemento de la tabla.*/
function Form_COMPROBANTE_CARGO__SeleccionarElementoTabla(IDSeleccionCheck){
	if(xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked){
		xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked=false;
		xGetElementById("FCC_CORRECION_FORMATEADO_"+IDSeleccionCheck).innerHTML="0,00";
		xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value="0";
		xGetElementById("FCC_MONTO_"+IDSeleccionCheck).innerHTML="0,00";
		}
	else{
		xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked=true;
		FormulaCargo=xGetElementById("FCC_FORMULA_"+IDSeleccionCheck).innerHTML;
		C=xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value;
		if(xGetElementById("FCC_ESIVA_"+IDSeleccionCheck).value=="t")
			MONTO=Form_COMPROBANTE_CARGO__BIIVA;
		else
			MONTO=Form_COMPROBANTE_CARGO__BI;
		xGetElementById("FCC_MONTO_"+IDSeleccionCheck).innerHTML=FormatearNumero((eval(FormulaCargo))+C*1.0);
		}
	}

function Form_COMPROBANTE_CARGO__ModificarCeldaCorrecion(IDSeleccionCheck){
	if(!xGetElementById("FCC_CHECK_"+IDSeleccionCheck).checked)
		return;

	if(xGetElementById("txt_celda_"+IDSeleccionCheck))
		return;
	Valor=xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value;
	if(Valor*1.0==0)
		Valor="";
	xGetElementById("FCC_CORRECION_FORMATEADO_"+IDSeleccionCheck).innerHTML="<INPUT id='txt_celda_"+IDSeleccionCheck+"' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur=\"Form_COMPROBANTE_CARGO__ModificarCeldaCorrecionPierdeFoco('"+IDSeleccionCheck+"')\" onkeypress=\"return AcceptNum(event,'txt_celda_"+IDSeleccionCheck+"',true);\" style='text-align : right;'  onkeyup=\"Form_COMPROBANTE_CARGO__KeyPressEnter(event,'"+IDSeleccionCheck+"')\">";
	xGetElementById("txt_celda_"+IDSeleccionCheck).focus();
	}

function Form_COMPROBANTE_CARGO__ModificarCeldaCorrecionPierdeFoco(IDSeleccionCheck){
	C=xGetElementById("txt_celda_"+IDSeleccionCheck).value;
	if(C=="")
		C=0;
	C=numberFormat(C,2);
	xGetElementById("FCC_CORRECION_MONTO_"+IDSeleccionCheck).value=C;
	xGetElementById("FCC_CORRECION_FORMATEADO_"+IDSeleccionCheck).innerHTML=FormatearNumero(C);
	FormulaCargo=xGetElementById("FCC_FORMULA_"+IDSeleccionCheck).innerHTML;
	MONTO=Form_COMPROBANTE_CARGO__BI;
	xGetElementById("FCC_MONTO_"+IDSeleccionCheck).innerHTML=FormatearNumero((eval(FormulaCargo))+C*1.0);
	}

function Form_COMPROBANTE_CARGO__KeyPressEnter(event,IDSeleccionCheck){
	if(event.keyCode==13)
		Form_COMPROBANTE_CARGO__ModificarCeldaCorrecionPierdeFoco(IDSeleccionCheck);
	}

/**
* Es llamada cuando se presiona sobre el boton limpiar.
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_COMPROBANTE_CARGO__LimpiarInputTextBuscarListado(){
 	xGetElementById("LISTADO_BUSCAR_FCC").value="";
 	Form_COMPROBANTE_CARGO__BuscarListado();
 	DarFocoCampo("LISTADO_BUSCAR_FCC",1000);
	}

/*Al presiona el boton aceptar o al hacer doble click se mandan los datos al formulario padre y cerramos la ventana*/
function Form_COMPROBANTE_CARGO__Aceptar(){
	if(Form_COMPROBANTE_CARGO__BloquearLista==true){
		Form_COMPROBANTE_CARGO__Cerrar();//VentanaCerrar('VENTANA_COMPROBANTE_CARGO');
		return;
		}

	var Arreglo=[];
	var K=0;
  for(var i=0;i<Form_COMPROBANTE_CARGO__NElementosTabla;i++){
		if(xGetElementById("FCC_CHECK_"+i)){
			if(xGetElementById("FCC_CHECK_"+i).checked){
				Arreglo[K]=[];
				Arreglo[K]["id"]=xGetElementById("FCC_CHECK_"+i).value;
				Arreglo[K]["formula"]=xGetElementById("FCC_FORMULA_"+i).innerHTML;
				Arreglo[K]["correccion"]=xGetElementById("FCC_CORRECION_MONTO_"+i).value;
				Arreglo[K]["iva"]=xGetElementById("FCC_ESIVA_"+i).value=="t"?true:false;
				K++;				
				}
			}
		}
	if(siga.window.getCmp("comprobante/cargo").parameter.onAccept(Arreglo))
		Form_COMPROBANTE_CARGO__Cerrar();
	}

function Form_COMPROBANTE_CARGO__Cerrar(){
	siga.close('comprobante/cargo');
}
