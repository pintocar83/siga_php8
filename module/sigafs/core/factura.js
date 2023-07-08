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


var Form_FACTURA__IDSeleccionActualLista="";
var Form_FACTURA__BuscarListado_CadenaBuscar="";
var Form_FACTURA__SW_PERSONA="";

function Form_FACTURA__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FF").innerHTML=MSG;
	}

function Form_FACTURA__MensajeListado(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FF_LISTADO").innerHTML=MSG;
	}
	
/**
* Activa todos los campos del formulario entrada de datos
*/
function Form_FACTURA__ActivarFormulario(){
	xGetElementById("FECHA_FF").readOnly=false;
	xGetElementById("NUMERO_FF").readOnly=false;
	xGetElementById("N_CONTROL_FF").readOnly=false;
	xGetElementById("TOTAL_FF").readOnly=false;
	
	xGetElementById("BASE_IVA_FF").readOnly=false;
	xGetElementById("PORCENTAJE_IVA_FF").readOnly=false;
	xGetElementById("MONTO_IVA_FF").readOnly=false;
	xGetElementById("RETENCION_IVA_FF").readOnly=false;
	
	xGetElementById("BASE_ISLR_FF").readOnly=false;
	xGetElementById("PORCENTAJE_ISLR_FF").readOnly=false;
	xGetElementById("MONTO_ISLR_FF").readOnly=false;
	xGetElementById("RETENCION_ISLR_FF").readOnly=false;
	
	xGetElementById("BOTON_PROVEEDOR_FF").disabled=false;
	xGetElementById("BOTON_BENEFICIARIO_FF").disabled=false;
	
	xGetElementById("FECHA_FF").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("NUMERO_FF").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("N_CONTROL_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("TOTAL_FF").setAttribute('class','TextoCampoInputObligatorios');
	
	xGetElementById("BASE_IVA_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("PORCENTAJE_IVA_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("MONTO_IVA_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("RETENCION_IVA_FF").setAttribute('class','TextoCampoInput');
	
	xGetElementById("BASE_ISLR_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("PORCENTAJE_ISLR_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("MONTO_ISLR_FF").setAttribute('class','TextoCampoInput');
	xGetElementById("RETENCION_ISLR_FF").setAttribute('class','TextoCampoInput');

	xGetElementById("IMG_FECHA_FF").setAttribute('onclick',"showCalendar('FECHA_FF','%d/%m/%Y')");
	xGetElementById("FECHA_FF").setAttribute('ondblclick',"showCalendar('FECHA_FF','%d/%m/%Y')");

	ActivarBoton("IMG_FECHA_FF","IMG_FECHA_FF",'calendario');
	ActivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF",'buscar');

	if(Form_FACTURA__SW_PERSONA=="N")
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF").setAttribute( 'onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FF','ID_BENEFICIARIO_PROVEEDOR_FF','NOMBRE_BENEFICIARIO_PROVEEDOR_FF','','CUENTA_CONTABLE_PB_FF')");
	else
		xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF").setAttribute( 'onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FF','ID_BENEFICIARIO_PROVEEDOR_FF','NOMBRE_BENEFICIARIO_PROVEEDOR_FF','','CUENTA_CONTABLE_PB_FF')");
	}

/**
* Desactiva todos los campos del formulario entrada de datos
*/
function Form_FACTURA__DesactivarFormulario(){ 
	xGetElementById("FECHA_FF").readOnly=true;
	xGetElementById("NUMERO_FF").readOnly=true;
	xGetElementById("N_CONTROL_FF").readOnly=true;
	xGetElementById("TOTAL_FF").readOnly=true;
	
	xGetElementById("BASE_IVA_FF").readOnly=true;
	xGetElementById("PORCENTAJE_IVA_FF").readOnly=true;
	xGetElementById("MONTO_IVA_FF").readOnly=true;
	xGetElementById("RETENCION_IVA_FF").readOnly=true;
	
	xGetElementById("BASE_ISLR_FF").readOnly=true;
	xGetElementById("PORCENTAJE_ISLR_FF").readOnly=true;
	xGetElementById("MONTO_ISLR_FF").readOnly=true;
	xGetElementById("RETENCION_ISLR_FF").readOnly=true;
	
	xGetElementById("BOTON_PROVEEDOR_FF").disabled=true;
	xGetElementById("BOTON_BENEFICIARIO_FF").disabled=true;
	
	xGetElementById("FECHA_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("NUMERO_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("N_CONTROL_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("TOTAL_FF").setAttribute('class','TextoCampoInputDesactivado');
	
	xGetElementById("BASE_IVA_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("PORCENTAJE_IVA_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("MONTO_IVA_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("RETENCION_IVA_FF").setAttribute('class','TextoCampoInputDesactivado');
	
	xGetElementById("BASE_ISLR_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("PORCENTAJE_ISLR_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("MONTO_ISLR_FF").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("RETENCION_ISLR_FF").setAttribute('class','TextoCampoInputDesactivado');

	xGetElementById("IMG_FECHA_FF").setAttribute('onclick',"");
	xGetElementById("FECHA_FF").setAttribute('ondblclick',"");

	DesactivarBoton("IMG_FECHA_FF","IMG_FECHA_FF",'calendario');
	DesactivarBoton("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF","IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF",'buscar');
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF").setAttribute( 'onclick',"");
	}


function Form_FACTURA__Nuevo(){
	Form_FACTURA__TabPane.setSelectedIndex(0);
	Form_FACTURA__ActivarFormulario();
	Form_FACTURA__Mensaje("");
  Form_FACTURA__MensajeListado("");

	Form_FACTURA__IDSeleccionActualLista="";
	Form_FACTURA__BotonProveedor();
	xGetElementById("FORMULARIO_FF").reset();
	Form_FACTURA__BuscarListado();
	Form_FACTURA__SetIVA();
	}

function Form_FACTURA__BotonProveedor(){
	Form_FACTURA__SW_PERSONA="P";
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF").setAttribute('onclick',"Form_LISTA_PROVEEDOR__Abrir('ID_BoP_FF','ID_BENEFICIARIO_PROVEEDOR_FF','NOMBRE_BENEFICIARIO_PROVEEDOR_FF','','','')");
	xGetElementById("ID_BoP_FF").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FF").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FF").value="";
	xGetElementById("TIPO_PERSONA_FF").innerHTML="Proveedor";
	}

function Form_FACTURA__BotonBeneficiario(){
	xGetElementById("IMG_BUSCAR_BENEFICIARIO_PROVEEDOR_FF").setAttribute('onclick',"Form_LISTA_BENEFICIARIO__Abrir('ID_BoP_FF','ID_BENEFICIARIO_PROVEEDOR_FF','NOMBRE_BENEFICIARIO_PROVEEDOR_FF','','','')");
	Form_FACTURA__SW_PERSONA="B";
	xGetElementById("ID_BoP_FF").value="";
	xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FF").value="";
	xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FF").value="";
	xGetElementById("TIPO_PERSONA_FF").innerHTML="Beneficiario";
	}


function Form_FACTURA__Eliminar(){	
	var _id_factura="";
	if(Form_FACTURA__IDSeleccionActualLista>0) 
		_id_factura=Form_FACTURA__IDSeleccionActualLista;
	
	if(_id_factura=="")
		return;
	
	if(!confirm("¿Esta seguro que desea eliminarlo?"))
		return;
	AjaxRequest.post({
				'parameters':{
					'action':"onDelete",
					'id':_id_factura
				},
				'onSuccess':Form_FACTURA__EliminarMensaje,
				'url':'../factura/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
				});
	}

/**
* Muestra el mensaje al eliminar el elemento seleccionado
* @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
*/
function Form_FACTURA__EliminarMensaje(req){
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_FACTURA__LimpiarInputTextBuscarListado();
		Form_FACTURA__Mensaje(respuesta.message,"VERDE");
		Form_FACTURA__MensajeListado(respuesta.message,"VERDE");
		}
	else{
		Form_FACTURA__Mensaje(respuesta.message,"ROJO");
		Form_FACTURA__MensajeListado(respuesta.message,"ROJO");
		}
	}

function Form_FACTURA__LimpiarInputTextBuscarListado(){
	xGetElementById("LISTADO_BUSCAR_FF").value="";
	Form_FACTURA__Mensaje("");
	Form_FACTURA__MensajeListado("");
	Form_FACTURA__BuscarListado();
	}




function Form_FACTURA__Guardar()
{
	Form_FACTURA__TabPane.setSelectedIndex(0);
	var _id_persona			= xTrim(strtoupper(xGetElementById("ID_BoP_FF").value));
	var _fecha					= xTrim(strtoupper(xGetElementById("FECHA_FF").value));
	
	var _numero_factura	= xTrim(strtoupper(xGetElementById("NUMERO_FF").value));
	var _numero_control	= xTrim(strtoupper(xGetElementById("N_CONTROL_FF").value));
	var _total					= xTrim(strtoupper(xGetElementById("TOTAL_FF").value));
	
	
	var informacion_iva_1	=	xGetElementById("BASE_IVA_FF").value;
	var informacion_iva_2	=	xGetElementById("PORCENTAJE_IVA_FF").value;
	var informacion_iva_3	=	xGetElementById("MONTO_IVA_FF").value;
	var informacion_iva_4	=	xGetElementById("RETENCION_IVA_FF").value;
	
	var informacion_islr_1=	xGetElementById("BASE_ISLR_FF").value;
	var informacion_islr_2=	xGetElementById("PORCENTAJE_ISLR_FF").value;
	var informacion_islr_3=	xGetElementById("MONTO_ISLR_FF").value;
	var informacion_islr_4=	xGetElementById("RETENCION_ISLR_FF").value;
	
	informacion_iva_1=(informacion_iva_1>0?informacion_iva_1:0);
	informacion_iva_2=informacion_iva_2>0?informacion_iva_2:0;
	informacion_iva_3=informacion_iva_3>0?informacion_iva_3:0;
	informacion_iva_4=informacion_iva_4>0?informacion_iva_4:0;
	
	informacion_islr_1=informacion_islr_1>0?informacion_islr_1:0;
	informacion_islr_2=informacion_islr_2>0?informacion_islr_2:0;
	informacion_islr_3=informacion_islr_3>0?informacion_islr_3:0;
	informacion_islr_4=informacion_islr_4>0?informacion_islr_4:0;
	
	if(!_id_persona){
		Form_FACTURA__Mensaje("Por favor seleccione el proveedor o beneficiario.","ROJO");
		Form_FACTURA__MensajeListado("");
		return;
		}
	if(!_fecha){
		Form_FACTURA__Mensaje("Por favor introduzca la fecha.","ROJO");
		Form_FACTURA__MensajeListado("");
		return;
		}
	if(!EsFechaValida(_fecha)){
		Form_FACTURA__Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
		Form_FACTURA__MensajeListado("");
		return;
		}
	_fecha=DesFormatearFecha(_fecha);
	if(!_numero_factura){
		Form_FACTURA__Mensaje("Por favor introduzca el número de factura.","ROJO");
		Form_FACTURA__MensajeListado("");
		return;
		}
	if(!_total){
		Form_FACTURA__Mensaje("Por favor introduzca el monto total de la factura.","ROJO");
		Form_FACTURA__MensajeListado("");
		return;
		}


	Form_FACTURA__DesactivarFormulario();
	
	var _informacion_iva="ARRAY["+
														informacion_iva_1+","+
														informacion_iva_2+","+
														informacion_iva_3+","+
														informacion_iva_4+
														"]";
	
	var _informacion_islr="ARRAY["+
														informacion_islr_1+","+
														informacion_islr_2+","+
														informacion_islr_3+","+
														informacion_islr_4+
														"]";
	
	var _id_factura="";
	if(Form_FACTURA__IDSeleccionActualLista>0) 
		_id_factura=Form_FACTURA__IDSeleccionActualLista;
	
	if(_id_factura)
		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			Form_FACTURA__ActivarFormulario();
			return;
		}
	
	AjaxRequest.post(
						{
							'parameters':{
										'action': "onSave",
										'id': _id_factura,
										'id_persona': _id_persona,
										'fecha': _fecha,
										'numero_factura': _numero_factura,
										'numero_control': _numero_control,
										'total': _total,										
										'informacion_iva': _informacion_iva,
										'informacion_islr': _informacion_islr
										},
							'onSuccess':Form_FACTURA__GuardarMensaje,
							'url':'../factura/',
							'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
}

function Form_FACTURA__GuardarMensaje(req){
	Form_FACTURA__ActivarFormulario();
	var respuesta = eval("("+req.responseText+")");
	if(respuesta.success){
		Form_FACTURA__Nuevo();
		Form_FACTURA__Mensaje(respuesta.message,"VERDE");
		}
	else
		Form_FACTURA__Mensaje(respuesta.message,"ROJO");		
	}

function Form_FACTURA__PresionarEnter(ev){
	if(xGetElementById("BUSCAR_CHECKBOX_FF").checked){
 		if(ev.keyCode==13)
			Form_FACTURA__BuscarListado();
		return;
		}
	Form_FACTURA__BuscarListado();
	}


function Form_FACTURA__BuscarListado(){
	Form_FACTURA__Mensaje("");
	xGetElementById("FORMULARIO_FF").reset();
	Form_FACTURA__IDSeleccionActualLista="";

	var CadenaBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FF").value));

	if(CadenaBuscar!="")
		if(Form_FACTURA__BuscarListado_CadenaBuscar==CadenaBuscar)
			return;

	Form_FACTURA__BuscarListado_CadenaBuscar=CadenaBuscar;

	if(CadenaBuscar=="")
		xGetElementById("TABLA_LISTA_FF").innerHTML=IconoCargandoTabla;

	AjaxRequest.post({
					'parameters':{
									'action':"onList",
									'mes':xGetElementById("MES_FILTRAR_FF").value,
									'text':CadenaBuscar,
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"fecha","direction":"DESC"}]'									
									},
					'onSuccess':Form_FACTURA__MostrarListado,
					'url':'../factura/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
	}



function Form_FACTURA__MostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	resultado=resultado["result"];
	var n=resultado.length;



	var CadAux1, CadAux2;

	var TextoBuscar=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FF").value));
	xGetElementById("TABLA_LISTA_FF").innerHTML="";

	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2, CadAux3, CadAux4;

	for(var i=0;i< n; i++){
		if(!resultado[i]['id_orden_pago'] || resultado[i]['id_orden_pago']=="null" || resultado[i]['id_orden_pago']*1==0)
			resultado[i]['id_orden_pago']="";
		FuncionOnclick="Form_FACTURA__SeleccionarElementoTabla('"+resultado[i]['id']+"')";
 		FuncionOnMouseOver="pintarFila(\"FF"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FF"+resultado[i]['id']+"\")";
		FuncionOnDblclick="Form_FACTURA__TabPane.setSelectedIndex(0);";

		Contenido+="<TR id='FF"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
		CadAux1="";
		if(xGetElementById("SOMBRA_CHECKBOX_FF").checked && TextoBuscar!=""){
			CadAux2=str_replace(resultado[i]['numero_factura'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux3=str_replace(resultado[i]['numero_control'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux4=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

			CadAux5=str_replace(resultado[i]['identificacion'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
			CadAux6=str_replace(resultado[i]['denominacion'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

			}
		else{
			CadAux2=resultado[i]['numero_factura'];
			CadAux3=resultado[i]['numero_control'];
			CadAux4=resultado[i]['fecha'];
			CadAux5=resultado[i]['identificacion'];
			CadAux6=resultado[i]['denominacion'];
			}

		
		Contenido+="<TD width='12%' class='FilaEstilo'>"+CadAux4+"</TD>";
		Contenido+="<TD width='15%' class='FilaEstiloContinua' align='center'>"+CadAux5+"</TD>";
		Contenido+="<TD class='FilaEstiloContinua'>"+CadAux6+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux2+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo'>"+CadAux3+"</TD>";
		Contenido+="<TD width='10%' class='FilaEstilo' align='right'>"+numberFormat(resultado[i]['total'],2)+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("TABLA_LISTA_FF").innerHTML=Contenido;
	}


function Form_FACTURA__SeleccionarElementoTabla(IDSeleccion){
	if(Form_FACTURA__IDSeleccionActualLista)
		if(xGetElementById("FF"+Form_FACTURA__IDSeleccionActualLista))
			xGetElementById("FF"+Form_FACTURA__IDSeleccionActualLista).bgColor=colorFondoTabla;
	colorBase=colorSeleccionTabla;
	if(xGetElementById("FF"+IDSeleccion))
		xGetElementById("FF"+IDSeleccion).bgColor=colorBase;
	Form_FACTURA__IDSeleccionActualLista=IDSeleccion;

	Form_FACTURA__Mensaje("");	
	AjaxRequest.post({
						'parameters':{
										'action':"onGet",
										'id':Form_FACTURA__IDSeleccionActualLista
										},
						'onSuccess':
							function(req){
								var respuesta = req.responseText;
								var resultado = eval("(" + respuesta + ")");

								//cargar proveedor/beneficiario
								xGetElementById("ID_BoP_FF").value="";
								switch(resultado[0]["tipo"]){
									case "N":
										Form_FACTURA__BotonBeneficiario();
										break;
									case "J":
									default:
										Form_FACTURA__BotonProveedor();
										break;
									}
								xGetElementById("ID_BoP_FF").value=resultado[0]["id_persona"];
								xGetElementById("ID_BENEFICIARIO_PROVEEDOR_FF").value=resultado[0]["identificacion"];
								xGetElementById("NOMBRE_BENEFICIARIO_PROVEEDOR_FF").value=resultado[0]["denominacion"];
								
								xGetElementById("FECHA_FF").value=FormatearFecha(resultado[0]["fecha"]);
								xGetElementById("NUMERO_FF").value=resultado[0]["numero_factura"];
								xGetElementById("N_CONTROL_FF").value=resultado[0]["numero_control"];
								xGetElementById("TOTAL_FF").value=numberFormat(resultado[0]["total"],2);
								xGetElementById("BASE_IVA_FF").value=resultado[0]["informacion_iva_1"];
								xGetElementById("PORCENTAJE_IVA_FF").value=resultado[0]["informacion_iva_2"];
								xGetElementById("MONTO_IVA_FF").value=resultado[0]["informacion_iva_3"];
								xGetElementById("RETENCION_IVA_FF").value=resultado[0]["informacion_iva_4"];
								xGetElementById("BASE_ISLR_FF").value=resultado[0]["informacion_islr_1"];
								xGetElementById("PORCENTAJE_ISLR_FF").value=resultado[0]["informacion_islr_2"];
								xGetElementById("MONTO_ISLR_FF").value=resultado[0]["informacion_islr_3"];
								xGetElementById("RETENCION_ISLR_FF").value=resultado[0]["informacion_islr_4"];
								},
						'url':'../factura/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

function Form_FACTURA__CalcularIVA(){
	var base_calculo=xGetElementById("BASE_IVA_FF").value;
	var porcentaje_iva=xGetElementById("PORCENTAJE_IVA_FF").value;
	var total_iva=(base_calculo*porcentaje_iva/100.00).toFixed(2);
	var porcentaje_iva_retencion=xGetElementById("RETENCION_PIVA_FF").value;

	xGetElementById("MONTO_IVA_FF").value=total_iva;
	xGetElementById("RETENCION_IVA_FF").value=(porcentaje_iva_retencion*total_iva*1.00).toFixed(2);
	var exento=xGetElementById("TOTAL_FF").value*1.00-(base_calculo*1.00+total_iva*1.00);
	xGetElementById("EXENTO_FF").value=(exento>=0?exento:0).toFixed(2);	
	
	xGetElementById("TOTAL_PAGO_FF").value=(xGetElementById("TOTAL_FF").value*1.00-xGetElementById("RETENCION_IVA_FF").value*1.00).toFixed(2);
}

function Form_FACTURA__SetIVA(){
	var porcentaje_iva2=xGetElementById("PORCENTAJE_IVA2_FF").value;
	xGetElementById("PORCENTAJE_IVA_FF").value=porcentaje_iva2;
	Form_FACTURA__CalcularIVA();
}










