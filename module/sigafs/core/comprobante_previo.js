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
var Form_COMPROBANTE_PREVIO__BuscarListado_CadenaBuscar="";


var Form_COMPROBANTE_PREVIO__FuncionLlamar="";
var Form_COMPROBANTE_PREVIO__Arreglo=[];
var Form_COMPROBANTE_PREVIO__K=0;
var Form_COMPROBANTE_PREVIO__IDOrdenDeCompra=-1;
var Form_COMPROBANTE_PREVIO__BloquearLista=false;
var Form_COMPROBANTE_PREVIO__id_persona="";


/*Mensaje que se muestra mientras se cargan los datos. (Busqueda de datos en la BD y creacion dinamica de la tabla)*/
function Form_COMPROBANTE_PREVIO__MensajeCargando(){
	xGetElementById("MSG_CARGANDO_FCP").innerHTML="<DIV style=\"color : #959595; font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 20px; font-style : normal; font-weight : bold; text-align : left;\" align=\"top\">Cargando... Por favor espere...</DIV>";
	}

/*Es llamada al presionar una tecla en el INPUT TEXT Buscar. Si esta marcada la opcion de 'Solo buscar al presionar enter' retorna y no busca en el listado*/
function Form_COMPROBANTE_PREVIO__Buscar(){
	if(EstadoCheckBoxSombra==xGetElementById("BUSCAR_CHECKBOX_FCP").checked)
		return;
	Form_COMPROBANTE_PREVIO__BuscarListado();
	}

/**Es llamada al presionar enter o al presionar una tecla en el INPUT TEXT Buscar (Solo si esta desactivado el checkbox descrito anteriormente)*/
function Form_COMPROBANTE_PREVIO__BuscarListado(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCP").value));

	if(CadenaBuscar!="")
		if(Form_COMPROBANTE_PREVIO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_COMPROBANTE_PREVIO__BuscarListado_CadenaBuscar=CadenaBuscar;

	Form_COMPROBANTE_PREVIO__MensajeCargando();
	
	var _mostrar={
		'tipo': Form_COMPROBANTE_PREVIO__Tipo,
		'id_comprobante':Form_COMPROBANTE_PREVIO__Arreglo,
		'contabilizado':true,
		'id_persona':Form_COMPROBANTE_PREVIO__id_persona,
		'tipo_persona':Form_COMPROBANTE_PREVIO__tipo_persona,
		'persona':true,
		//'anulado':false, //no hace falta, pq al decir que no este asociado no se incluyen las anuladas
		'asociado':false
		};
	
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mostrar': Ext.encode(_mostrar),
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_COMPROBANTE_PREVIO__MostrarListado,
					'url':'../comprobante/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
/*
	if(xGetElementById("MOSTRAR_CHECKBOX_FCP").checked){
		AjaxRequest.post({'parameters':{ 'accion':"Form_COMPROBANTE_PREVIO__BuscarListado_RSinODC", 
										'CadenaBuscar':CadenaBuscar,
										'ARREGLO':Form_COMPROBANTE_PREVIO__Arreglo,
										'TAM_ARREGLO':Form_COMPROBANTE_PREVIO__K,
										'id_orden_de_compra':Form_COMPROBANTE_PREVIO__IDOrdenDeCompra},
						'onSuccess':Form_COMPROBANTE_PREVIO__MostrarListado,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	else{
		AjaxRequest.post({'parameters':{ 'accion':"Form_COMPROBANTE_PREVIO__BuscarListado", 
										'CadenaBuscar':CadenaBuscar,
										'ARREGLO':Form_COMPROBANTE_PREVIO__Arreglo,
										'TAM_ARREGLO':Form_COMPROBANTE_PREVIO__K},
						'onSuccess':Form_COMPROBANTE_PREVIO__MostrarListado,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}*/
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_COMPROBANTE_PREVIO__PresionarEnter(ev){
 	if(ev.keyCode==13)
		Form_COMPROBANTE_PREVIO__BuscarListado();
	}
var EstadoCheckBoxSombra="";
var Form_COMPROBANTE_PREVIO__NElementosTabla=0;
var sw_FCP=false;
/*Muestra el listado de cuentas contables (Crea tabla dinamicamente)*/
function Form_COMPROBANTE_PREVIO__MostrarListado(req){
	var respuesta = req.responseText;
	
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;
	Form_COMPROBANTE_PREVIO__NElementosTabla=n;
	//Si hay mas de 1000 registros Desactivar Busqueda rapida y resaldado en las coincidencias.
	if(n>1000 && sw_FCP==false){
		sw_FCP=true;
		xGetElementById("SOMBRA_CHECKBOX_FCP").checked=false;
		xGetElementById("BUSCAR_CHECKBOX_FCP").checked=true;
		}

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FCP").value));
	EstadoCheckBoxSombra=xGetElementById("SOMBRA_CHECKBOX_FCP").checked;

	xGetElementById("TABLA_LISTA_FCP").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux0, CadAux1, CadAux2, CadAux3;
	var SeleccionCheck;

	for(var i=0;i< n; i++){
		if(Form_COMPROBANTE_PREVIO__BloquearLista==false){
			FuncionOnclick="Form_COMPROBANTE_PREVIO__SeleccionarElementoTabla('FCP_CHECK_"+i+"');";		
			FuncionOnMouseOver="pintarFila(\"FCP"+resultado[i]['id']+"\")";
			FuncionOnMouseOut="despintarFila(\"FCP"+resultado[i]['id']+"\")";
			EstadoCheck="";
			}
		else{
			FuncionOnclick="";			
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			EstadoCheck="disabled";
			}		

		Contenido+="<TR id='FCP"+resultado[i]['id']+"' ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux0=resultado[i]['tipo'];
		CadAux1=resultado[i]['correlativo'];
		
		Concepto=strtoupper(RecortarTexto(resultado[i]['concepto'],110));
		if(TextoBuscar!="" && EstadoCheckBoxSombra){
			
			CadAux1=str_replace(CadAux1,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(Concepto,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['persona'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			
			}
		else{
			CadAux2=Concepto;
			CadAux3=resultado[i]['fecha'];
			CadAux4=resultado[i]['persona'];
			}
		
		SeleccionCheck="";
 		for(j=0;j<Form_COMPROBANTE_PREVIO__Arreglo.length;j++){
 			if(String(Form_COMPROBANTE_PREVIO__Arreglo[j])==String(resultado[i]['id'])){
				SeleccionCheck="checked";
 				break;
 				}
 			}
		
		Contenido+="<TD width='3%' class='FilaEstilo'><INPUT id='FCP_CHECK_"+i+"' type='checkbox' value='"+resultado[i]['id']+"' "+SeleccionCheck+" "+EstadoCheck+"></TD>";
		Contenido+="<TD width='1%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux0+"</TD>";
		Contenido+="<TD width='11%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux1+"</TD>";
		Contenido+="<TD width='12%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux3+"</TD>";
		Contenido+="<TD width='17%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux4+"</TD>";
		Contenido+="<TD class='FilaEstilo' onclick=\""+FuncionOnclick+"\">"+CadAux2+"<INPUT id='FCP_CONCEPTO_"+i+"' type='hidden' value='"+resultado[i]['concepto']+"'></TD>";
		

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCP").innerHTML=Contenido;
	xGetElementById("MSG_CARGANDO_FCP").innerHTML="";
	}


/*La funcion es llamada cuando se hace click sobre algun elemento de la tabla.*/
function Form_COMPROBANTE_PREVIO__SeleccionarElementoTabla(IDSeleccionCheck){
	if(xGetElementById(IDSeleccionCheck).checked)
		xGetElementById(IDSeleccionCheck).checked=false;
	else
		xGetElementById(IDSeleccionCheck).checked=true;
	}

/**
* Es llamada cuando se presiona sobre el boton limpiar. 
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_COMPROBANTE_PREVIO__LimpiarInputTextBuscarListado(){
 	xGetElementById("LISTADO_BUSCAR_FCP").value="";
 	Form_COMPROBANTE_PREVIO__BuscarListado();
 	DarFocoCampo("LISTADO_BUSCAR_FCP",1000);
	}

/*Al presiona el boton aceptar o al hacer doble click se mandan los datos al formulario padre y cerramos la ventana*/
function Form_COMPROBANTE_PREVIO__Aceptar(){
	if(Form_COMPROBANTE_PREVIO__BloquearLista==true){
		//VentanaCerrar('VENTANA_Form_COMPROBANTE_PREVIO');
		Form_COMPROBANTE_PREVIO__Cerrar();
		return;
		}
		
		
	//siga.window.getCmp("modulo_base/requisicion_externa_listado").parameter.onAccept()
	//var Conceptos="";
	var Arreglo=[];
	//var K=0;
  for(var i=0;i<Form_COMPROBANTE_PREVIO__NElementosTabla;i++)
		if(xGetElementById("FCP_CHECK_"+i))
			if(xGetElementById("FCP_CHECK_"+i).checked){
				Arreglo.push(xGetElementById("FCP_CHECK_"+i).value);
				}
			
	if(siga.window.getCmp("comprobante_previo_listado").parameter.onAccept(Arreglo))
		Form_COMPROBANTE_PREVIO__Cerrar();

	//if(Form_COMPROBANTE_PREVIO__FuncionLlamar)
	//	eval(Form_COMPROBANTE_PREVIO__FuncionLlamar+"(Arreglo,K,Conceptos);");
	//VentanaCerrar('VENTANA_Form_COMPROBANTE_PREVIO');
	}

function Form_COMPROBANTE_PREVIO__Cerrar(){
	siga.close('comprobante_previo_listado');
}

/*consulta suma los articulos iguales de unas requisiones especificas
select id_articulo, sum(cantidad_arb) from modulo_compras.articulo_requisicion_bienes 
where (id_requisicion_bienes=3 or id_requisicion_bienes=5) 
group by id_articulo





//esta es
select ARB.id_articulo, sum(ARB.cantidad_arb), A.denominacion_a 
from modulo_compras.articulo_requisicion_bienes as ARB, modulo_compras.articulo as A 
where A.id_articulo=ARB.id_articulo AND (ARB.id_requisicion_bienes=3 or ARB.id_requisicion_bienes=5) 
group by ARB.id_articulo, A.denominacion_a

//esta es 2

select ARB.id_articulo, sum(ARB.cantidad_arb), A.denominacion_a, UDM.denominacion_udm 
from 
	modulo_compras.articulo_requisicion_bienes as ARB, 
	modulo_compras.articulo as A, 
	modulo_compras.unidades_de_medida as UDM
where 
A.id_articulo=ARB.id_articulo AND 
A.id_unidades_de_medida=UDM.id_unidades_de_medida AND 
(ARB.id_requisicion_bienes=3 or ARB.id_requisicion_bienes=5) 
group by ARB.id_articulo, A.denominacion_a, UDM.denominacion_udm  
order by A.denominacion_a










select ARB.id_articulo, A.denominacion_a, sum(ARB.cantidad_arb), ARB.costo_arb 
from modulo_compras.articulo_requisicion_bienes as ARB, modulo_compras.articulo as A 
where A.id_articulo=ARB.id_articulo AND (ARB.id_requisicion_bienes=3 or ARB.id_requisicion_bienes=5) 
group by ARB.id_articulo, A.denominacion_a, ARB.costo_arb


*/

