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
* @version 20091001
*/

var Form_COMPROBANTE_RETENCION__IDSeleccionActualLista="";
var Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar="";
var Form_COMPROBANTE_RETENCION__FechaSeleccionActualLista="";
var Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas="";

function Form_COMPROBANTE_RETENCION__Nuevo(){
	AjaxRequest.post({
						'parameters':{
									'action':"onNew",
									'id_retencion_tipo':xGetElementById("SELECT_TIPO_COMPROBANTE_RETENCION_FCR").value
									},
						'onSuccess':
						function(req){
									var respuesta = req.responseText;
									var resultado = eval("(" + respuesta + ")");
									var respuesta = eval("("+req.responseText+")");									
									Form_COMPROBANTE_RETENCION__BuscarListado();
									},
						'url':'../comprobante_retencion/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

function Form_COMPROBANTE_RETENCION__PresionarEnter(ev){
	if(ev.keyCode==13)
		Form_COMPROBANTE_RETENCION__BuscarListado();
	}


function Form_COMPROBANTE_RETENCION__BuscarListado(sw){
	var CadenaBuscar=xTrim(strtoupper(xGetElementById("BUSCAR_FCR").value));

 	if(CadenaBuscar!="")
 		if(Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar==CadenaBuscar)
 			return;
 	Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar=CadenaBuscar;
	
	if(!sw){
		Form_COMPROBANTE_RETENCION__IDSeleccionActualLista="";
		Form_COMPROBANTE_RETENCION__FechaSeleccionActualLista="";
	}
	

	xGetElementById("TABLA_LISTA_FCR").innerHTML=IconoCargandoTabla;
	xGetElementById("TABLA_LISTA_FACTURA_FCR").innerHTML="";
	
	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'id_retencion_tipo':xGetElementById("SELECT_TIPO_COMPROBANTE_RETENCION_FCR").value,
									'mes':xGetElementById("MES_FILTRAR_FCR").value,
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"numero","direction":"DESC"}]'									
									},
					'onSuccess': function(req){
									Form_COMPROBANTE_RETENCION__MostrarListado(req,sw)
									},
					'url':'../comprobante_retencion/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}


function Form_COMPROBANTE_RETENCION__MostrarListado(req,sw){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	xGetElementById("TABLA_LISTA_FCR").innerHTML="";

	if(!resultado) return;
	var TextoBuscar=strtoupper(xTrim(xGetElementById("BUSCAR_FCR").value));

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, aux;
	var n=resultado.length;
	for(var i=0;i< n; i++){
		FuncionOnclick="Form_COMPROBANTE_RETENCION__SeleccionarElementoTabla('"+resultado[i]['id']+"','"+resultado[i]['fecha']+"')";
 		FuncionOnMouseOver="pintarFila(\"FCR"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FCR"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FCR"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";


		aux=String(resultado[i]['fecha']).split("-");
		CadAux1=aux[0]+"-"+aux[1]+"-"+completarCodigoCeros(resultado[i]['numero'],NDigitos_Codigo_ComprobanteCargoRetencion);
		CadAux1=str_replace(CadAux1,"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(FormatearFecha(resultado[i]['fecha'])),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		if(!resultado[i]['persona_nombre'])
			CadAux3="";
		else
			CadAux3=str_replace(strtoupper(resultado[i]['persona_nombre']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);


		Contenido+="<TD width='25%' class='FilaEstilo' ondblclick=\"Form_COMPROBANTE_RETENCION__ModificarCeldaNumero("+resultado[i]['id']+",'"+completarCodigoCeros(resultado[i]['numero'],NDigitos_Codigo_ComprobanteCargoRetencion)+"')\">"+CadAux1+"</TD>";
		Contenido+="<TD width='15%' id='FECHA_FCR_"+resultado[i]['id']+"' class='FilaEstilo' style='text-align: center;'  ondblclick='Form_COMPROBANTE_RETENCION__ModificarCeldaFecha()'>"+CadAux2+"</TD>";
		Contenido+="<TD width='60%' class='FilaEstilo' id='CELL_PB_FCR"+resultado[i]['id']+"'>"+CadAux3+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FCR").innerHTML=Contenido;
	
	if(sw){
		Form_COMPROBANTE_RETENCION__SeleccionarElementoTabla(Form_COMPROBANTE_RETENCION__IDSeleccionActualLista,Form_COMPROBANTE_RETENCION__FechaSeleccionActualLista);
		}
	}

function Form_COMPROBANTE_RETENCION__ModificarCeldaFecha(){
	if(xGetElementById("txt_celda_FCR"))
		return;
	var Valor=Form_COMPROBANTE_RETENCION__FechaSeleccionActualLista;

	xGetElementById("FECHA_FCR_"+Form_COMPROBANTE_RETENCION__IDSeleccionActualLista).innerHTML="<INPUT id='txt_celda_FCR' class='TextoCampoInputTabla' type='text' value='"+Valor+"' onblur='Form_COMPROBANTE_RETENCION__ModificarCeldaPierdeFoco()'  style='text-align : right;' onkeyup='Form_COMPROBANTE_RETENCION__ModificarCeldaKeyPressEnter(event)'>";
	xGetElementById("txt_celda_FCR").focus();
	}

function Form_COMPROBANTE_RETENCION__ModificarCeldaPierdeFoco(){
	xGetElementById("txt_celda_FCR").onblur="";
	var Valor=xGetElementById("txt_celda_FCR").value;
	if(!EsFechaValida(Valor)){
		alert("Fecha invalida.");
		xGetElementById("txt_celda_FCR").focus();
		return;
		}
	xGetElementById("TABLA_LISTA_FCR").innerHTML=IconoCargandoTabla;
	xGetElementById("TABLA_LISTA_FACTURA_FCR").innerHTML="";

	//modificar en la BD
	AjaxRequest.post({
					'parameters':{
								'action':"onUpdate_Fecha",
								'fecha':DesFormatearFecha(Valor),
								'id':Form_COMPROBANTE_RETENCION__IDSeleccionActualLista
								},
					'onSuccess':
								function(req){
									Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar="";
									Form_COMPROBANTE_RETENCION__BuscarListado();
								},
					'url':'../comprobante_retencion/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}

function Form_COMPROBANTE_RETENCION__ModificarCeldaKeyPressEnter(ev){
	if(ev.keyCode==13)
		Form_COMPROBANTE_RETENCION__ModificarCeldaPierdeFoco();
	}

function Form_COMPROBANTE_RETENCION__SeleccionarElementoTabla(IDSeleccion,fecha){
	if(Form_COMPROBANTE_RETENCION__IDSeleccionActualLista!="")
		xGetElementById("FCR"+Form_COMPROBANTE_RETENCION__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FCR"+IDSeleccion).bgColor=colorBase;
	Form_COMPROBANTE_RETENCION__IDSeleccionActualLista=IDSeleccion;
	Form_COMPROBANTE_RETENCION__FechaSeleccionActualLista=FormatearFecha(fecha);
	Form_COMPROBANTE_RETENCION__CargarFacturasAsociadas();
	}

function Form_COMPROBANTE_RETENCION__ModificarCeldaNumero(id,numero){
	//pedir el nuevo numero
	while(true){
		numero=prompt("Introduzca el número del comprobante.", numero);
		if(numero==null)//si es cancelar
			return;
		if(numero*1>=1)//si es valida
			break;
		alert("El número introducido es invalido.");
		}
	//mandar la inf pa la BD
	AjaxRequest.post({
						'parameters':{
									'action':"onUpdate_Numero",
									'numero':numero,
									'id':Form_COMPROBANTE_RETENCION__IDSeleccionActualLista
									},
						'onSuccess':
						function(req){
									Form_COMPROBANTE_RETENCION__BuscarListado_CadenaBuscar="";
									Form_COMPROBANTE_RETENCION__BuscarListado();
									},
						'url':'../comprobante_retencion/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}



function Form_COMPROBANTE_RETENCION__CargarFacturasAsociadas(){
	xGetElementById("TABLA_LISTA_FACTURA_FCR").innerHTML=IconoCargandoTabla;
	Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas="";
	AjaxRequest.post({
						'parameters':{
									'action':"onList_Factura",
									'id':Form_COMPROBANTE_RETENCION__IDSeleccionActualLista,
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"}]'		
									},
						'onSuccess':Form_COMPROBANTE_RETENCION__MostrarFacturasAsociadas,
						'url':'../comprobante_retencion/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}



function Form_COMPROBANTE_RETENCION__MostrarFacturasAsociadas(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"]
	var n=resultado.length;

	var CadAux1, CadAux2;
	var TextoBuscar="";
	xGetElementById("TABLA_LISTA_FACTURA_FCR").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		FuncionOnclick="Form_COMPROBANTE_RETENCION__SeleccionarElementoTablaFacturasAsociadas('"+resultado[i]['id']+"')";
 		FuncionOnMouseOver="pintarFila(\"FA_FCR"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FA_FCR"+resultado[i]['id']+"\")";
		FuncionOnDblclick="";

		Contenido+="<TR id='FA_FCR"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		
		CadAux1=FormatearFecha(resultado[i]['fecha']);		
		CadAux2=resultado[i]['persona_identificacion'];
		CadAux3=resultado[i]['persona_denominacion'];
		CadAux4=resultado[i]['numero_factura'];
		CadAux5=resultado[i]['numero_control'];		
		CadAux6=numberFormat(resultado[i]['total'],2);
		
		

		Contenido+="<TD width='12%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='15%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua'>"+CadAux3+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux4+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux5+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='right'>"+CadAux6+"</TD>";
		Contenido+="</TR>";
		}
	xGetElementById("TABLA_LISTA_FACTURA_FCR").innerHTML=Contenido;
	}

function Form_COMPROBANTE_RETENCION__SeleccionarElementoTablaFacturasAsociadas(IDSeleccion){
	if(Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas!="")
		xGetElementById("FA_FCR"+Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	xGetElementById("FA_FCR"+IDSeleccion).bgColor=colorBase;
	Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas=IDSeleccion;
	}

function Form_COMPROBANTE_RETENCION__QuitarFacturaAsociada(){
	if(!Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas)
		return;

	AjaxRequest.post({
					'parameters':{
									'action':"onDelete_Factura",
									'id':Form_COMPROBANTE_RETENCION__IDSeleccionActualLista,
									'id_factura':Form_COMPROBANTE_RETENCION__IDSeleccionActualListaFacturasAsociadas},
					'onSuccess':
									function(req){
													var respuesta = req.responseText;
													var resultado = eval("(" + respuesta + ")");
													if(resultado.cantidad_factura==0)
														Form_COMPROBANTE_RETENCION__BuscarListado(true);
													else
														Form_COMPROBANTE_RETENCION__CargarFacturasAsociadas();
													},
					'url':'../comprobante_retencion/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});


	}

function Form_COMPROBANTE_RETENCION__AgregarFacturaAsociada(){
	if(!Form_COMPROBANTE_RETENCION__IDSeleccionActualLista)
		return;
	Form_LISTA_FACTURA__Abrir();
	}

function Form_LISTA_FACTURA__Abrir(){
      var campo={
        fieldLabel: 'Listado de Facturas',
        setValue: function(v){
					//asociar la factura al comprobante
					AjaxRequest.post({
						'parameters':{
									'action':"onAdd_Factura",
									'id_factura':v,
									'id':Form_COMPROBANTE_RETENCION__IDSeleccionActualLista
									},
						'onSuccess':
						function(req){
									var respuesta = req.responseText;
									var resultado = eval("(" + respuesta + ")");
									if(resultado.cantidad_factura==1)
										Form_COMPROBANTE_RETENCION__BuscarListado(true);
									else
										Form_COMPROBANTE_RETENCION__CargarFacturasAsociadas();
									},
						'url':'../comprobante_retencion/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
        },
        internal:{
          page:1,
          limit: 100,
          valueField: 'id',
					columns: {
						field: ["fecha","identificacion","denominacion","numero_factura","numero_control","total"],
						title: ["Fecha","RIF/Cédula","Nombre","Nº Factura","Nº Control","Total"],
						width: ['12%','15%',"43%","10%","10%","10%"],
						align: ["","","","","","right"],
						sort:  ["DESC",'NULL','NULL','NULL','NULL','NULL']
					},
          url: 'module/comprobante_retencion/',
					extraParams:{
						id: Form_COMPROBANTE_RETENCION__IDSeleccionActualLista
					},
          actionOnList:'onList_FacturaNoAsociada',
          actionOnGet:'onGet',
        }
      };
      
			var _opt={};
			_opt.internal={};
			_opt.internal.parent=campo;
			var selector=Ext.create("siga.windowSelect",_opt);
      selector.show();
      selector.search();
      }



function Form_COMPROBANTE_RETENCION__Imprimir(){
	if(!Form_COMPROBANTE_RETENCION__IDSeleccionActualLista)
		return;
	if(xGetElementById("SELECT_TIPO_COMPROBANTE_RETENCION_FCR").value=="1")
		window.open("../../report/comprobante_retencion_iva.php?id="+Form_COMPROBANTE_RETENCION__IDSeleccionActualLista*1);
	else if(xGetElementById("SELECT_TIPO_COMPROBANTE_RETENCION_FCR").value=="2")
		window.open("../../report/comprobante_retencion_islr.php?id="+Form_COMPROBANTE_RETENCION__IDSeleccionActualLista*1);
	}

