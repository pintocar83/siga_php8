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
var Form_REQUISICION_LISTADO__BuscarListado_CadenaBuscar="";


var Form_REQUISICION_LISTADO__FuncionLlamar="";
var Form_REQUISICION_LISTADO__Arreglo=new Array();
var Form_REQUISICION_LISTADO__K=0;
var Form_REQUISICION_LISTADO__IDOrdenDeCompra=-1;
var Form_REQUISICION_LISTADO__BloquearLista=false;


/*Mensaje que se muestra mientras se cargan los datos. (Busqueda de datos en la BD y creacion dinamica de la tabla)*/
function Form_REQUISICION_LISTADO__MensajeCargando(){
	xGetElementById("MSG_CARGANDO_FRL").innerHTML="<DIV style=\"color : #959595; font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 20px; font-style : normal; font-weight : bold; text-align : left;\" align=\"top\">Cargando... Por favor espere...</DIV>";
	}

/*Es llamada al presionar una tecla en el INPUT TEXT Buscar. Si esta marcada la opcion de 'Solo buscar al presionar enter' retorna y no busca en el listado*/
function Form_REQUISICION_LISTADO__Buscar(){
	if(EstadoCheckBoxSombra==xGetElementById("BUSCAR_CHECKBOX_FRL").checked)
		return;
	Form_REQUISICION_LISTADO__BuscarListado();
	}

/**Es llamada al presionar enter o al presionar una tecla en el INPUT TEXT Buscar (Solo si esta desactivado el checkbox descrito anteriormente)*/
function Form_REQUISICION_LISTADO__BuscarListado(){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FRL").value));

	if(CadenaBuscar!="")
		if(Form_REQUISICION_LISTADO__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;
	Form_REQUISICION_LISTADO__BuscarListado_CadenaBuscar=CadenaBuscar;

	Form_REQUISICION_LISTADO__MensajeCargando();
	
	var _id_comprobante=xGetElementById("ID_COMPROBANTE_ORDEN_PAGO_FRL").value;

	AjaxRequest.post({
					'parameters':{
									'action':"onList_Tipo",
									'tipo': xGetElementById("TIPO_FRL").value,
									'id_comprobante':_id_comprobante,
									'mes': '',
									'text': CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"},{"property":"correlativo","direction":"DESC"}]'									
									},
					'onSuccess':Form_REQUISICION_LISTADO__MostrarListado,
					'url':'../requisicion_externa/listado/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
/*
	if(xGetElementById("MOSTRAR_CHECKBOX_FRL").checked){
		AjaxRequest.post({'parameters':{ 'accion':"Form_REQUISICION_LISTADO__BuscarListado_RSinODC", 
										'CadenaBuscar':CadenaBuscar,
										'ARREGLO':Form_REQUISICION_LISTADO__Arreglo,
										'TAM_ARREGLO':Form_REQUISICION_LISTADO__K,
										'id_orden_de_compra':Form_REQUISICION_LISTADO__IDOrdenDeCompra},
						'onSuccess':Form_REQUISICION_LISTADO__MostrarListado,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}
	else{
		AjaxRequest.post({'parameters':{ 'accion':"Form_REQUISICION_LISTADO__BuscarListado", 
										'CadenaBuscar':CadenaBuscar,
										'ARREGLO':Form_REQUISICION_LISTADO__Arreglo,
										'TAM_ARREGLO':Form_REQUISICION_LISTADO__K},
						'onSuccess':Form_REQUISICION_LISTADO__MostrarListado,
						'url':'../modulo_compras/consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}*/
	}

/*Al presionar enter buscamos directamente en el listado*/
function Form_REQUISICION_LISTADO__PresionarEnter(ev){
 	if(ev.keyCode==13)
		Form_REQUISICION_LISTADO__BuscarListado();
	}
var EstadoCheckBoxSombra="";
var Form_REQUISICION_LISTADO__NElementosTabla=0;
var sw_FRL=false;
/*Muestra el listado de cuentas contables (Crea tabla dinamicamente)*/
function Form_REQUISICION_LISTADO__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;
	Form_REQUISICION_LISTADO__NElementosTabla=n;
	//Si hay mas de 1000 registros Desactivar Busqueda rapida y resaldado en las coincidencias.
	if(n>1000 && sw_FRL==false){
		sw_FRL=true;
		xGetElementById("SOMBRA_CHECKBOX_FRL").checked=false;
		xGetElementById("BUSCAR_CHECKBOX_FRL").checked=true;
		}

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FRL").value));
	EstadoCheckBoxSombra=xGetElementById("SOMBRA_CHECKBOX_FRL").checked;

	xGetElementById("TABLA_LISTA_FRL").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2;
	var SeleccionCheck;
	
	for(var i=0;i< n; i++){
		if(Form_REQUISICION_LISTADO__BloquearLista==false){
			FuncionOnclick="Form_REQUISICION_LISTADO__SeleccionarElementoTabla('FRL_CHECK_"+i+"');";		
			FuncionOnMouseOver="pintarFila(\"FRL"+resultado[i]['id']+"\")";
			FuncionOnMouseOut="despintarFila(\"FRL"+resultado[i]['id']+"\")";
			EstadoCheck="";
			}
		else{
			FuncionOnclick="";			
			FuncionOnMouseOver="";
			FuncionOnMouseOut="";
			EstadoCheck="disabled";
			}		

		Contenido+="<TR id='FRL"+resultado[i]['id']+"' ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux1=completarCodigoCeros(resultado[i]['correlativo'],10);
		
		Concepto=strtoupper(RecortarTexto(resultado[i]['concepto'],110));
		if(TextoBuscar!="" && EstadoCheckBoxSombra){
			
			CadAux1=str_replace(CadAux1,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux2=str_replace(Concepto,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			
			}
		else{
			CadAux2=Concepto;
			CadAux3=resultado[i]['fecha'];
			}
		
		SeleccionCheck="";
 		for(j=0;j<Form_REQUISICION_LISTADO__Arreglo.length;j++){
 			if(String(Form_REQUISICION_LISTADO__Arreglo[j])==String(resultado[i]['id'])){
				SeleccionCheck="checked";
 				break;
 				}
 			}
		
		Contenido+="<TD width='3%' class='FilaEstilo'><INPUT id='FRL_CHECK_"+i+"' type='checkbox' value='"+resultado[i]['id']+"' "+SeleccionCheck+" "+EstadoCheck+"></TD>";
		Contenido+="<TD width='12%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux1+"</TD>";
		Contenido+="<TD width='12%' class='FilaEstilo' align='center' onclick=\""+FuncionOnclick+"\">"+CadAux3+"</TD>";
		Contenido+="<TD class='FilaEstilo' onclick=\""+FuncionOnclick+"\">"+CadAux2+"<INPUT id='FRL_CONCEPTO_"+i+"' type='hidden' value='"+resultado[i]['concepto']+"'></TD>";
		

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FRL").innerHTML=Contenido;
	xGetElementById("MSG_CARGANDO_FRL").innerHTML="";
	}


/*La funcion es llamada cuando se hace click sobre algun elemento de la tabla.*/
function Form_REQUISICION_LISTADO__SeleccionarElementoTabla(IDSeleccionCheck){
	if(xGetElementById(IDSeleccionCheck).checked)
		xGetElementById(IDSeleccionCheck).checked=false;
	else
		xGetElementById(IDSeleccionCheck).checked=true;
	}

/**
* Es llamada cuando se presiona sobre el boton limpiar. 
* Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
*/
function Form_REQUISICION_LISTADO__LimpiarInputTextBuscarListado(){
 	xGetElementById("LISTADO_BUSCAR_FRL").value="";
 	Form_REQUISICION_LISTADO__BuscarListado();
 	DarFocoCampo("LISTADO_BUSCAR_FRL",1000);
	}

/*Al presiona el boton aceptar o al hacer doble click se mandan los datos al formulario padre y cerramos la ventana*/
function Form_REQUISICION_LISTADO__Aceptar(){
	if(Form_REQUISICION_LISTADO__BloquearLista==true){
		//VentanaCerrar('VENTANA_Form_REQUISICION_LISTADO');
		Form_REQUISICION_LISTADO__Cerrar();
		return;
		}
		
		
	//siga.window.getCmp("modulo_base/requisicion_externa_listado").parameter.onAccept()
	//var Conceptos="";
	var Arreglo=[];
	//var K=0;
  for(var i=0;i<Form_REQUISICION_LISTADO__NElementosTabla;i++)
		if(xGetElementById("FRL_CHECK_"+i))
			if(xGetElementById("FRL_CHECK_"+i).checked){
				Arreglo.push(xGetElementById("FRL_CHECK_"+i).value);
				}
			
	if(siga.window.getCmp("requisicion_externa/listado").parameter.onAccept(Arreglo))
		Form_REQUISICION_LISTADO__Cerrar();

	//if(Form_REQUISICION_LISTADO__FuncionLlamar)
	//	eval(Form_REQUISICION_LISTADO__FuncionLlamar+"(Arreglo,K,Conceptos);");
	//VentanaCerrar('VENTANA_Form_REQUISICION_LISTADO');
	}

function Form_REQUISICION_LISTADO__Cerrar(){
	siga.close('requisicion_externa/listado');
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

