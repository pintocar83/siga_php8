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
* @author Irving J. Martínez R. <irvingjm@gmail.com>
* @date 2009-08-01
* @version 20090801
*/


var ListaVentanas;

function limpiarTabla(IdTabla){
    var nodo=xGetElementById(IdTabla);
    nodo.innerHTML="";
    /*while(nodo.hasChildNodes()){
    nodo.removeChild(nodo.lastChild);
    }*/
	//tabla.rows[i].cells[j].innerHTML;
}

var colorBase;
var colorSeleccionTabla='#C7C7EB';
var colorFondoTabla='#EBEBEB';
var colorFilaCuentaAgregada='#B9C8FF';

function pintarFila(fila){
    colorBase=xGetElementById(fila).style.background;
    xGetElementById(fila).style.background='#FFF8A3';
}


function despintarFila(fila){
    xGetElementById(fila).style.background="";
}


function restaurarColorTabla(IDTabla){
    var _nodo=xGetElementById(IDTabla);
    var _N=_nodo.childNodes.length;
    for(var i=0;i<_N;i++)
        _nodo.childNodes[i].style.background = colorFondoTabla;
}


function str_replace(_text,_replace,_search){
    return String(_text).split(_search).join(_replace);
}


function strtoupper(_cadena){
	if(!_cadena)
		return "";
	_cadena=_cadena.toUpperCase();
	_cadena=str_replace(_cadena,"","\"");
	_cadena=str_replace(_cadena,"","\n");
	_cadena=str_replace(_cadena,"","'");
    return _cadena;
	}

//***********************************************************************************************
// validarFecha(dia,mes, año)
//
// Valida que el día y el mes introducidos sean correctos. Además valida que el año introducido
// sea o no bisiesto
//
//***********************************************************************************************
function EsFechaValida(cadena){
    var FECHA=cadena.split("/");
    if(FECHA.length!=3)
        return false;

    var dia=FECHA[0];
    if(dia.length!=2||isNaN(dia))
        return false;

    var mes=FECHA[1];
    if(mes.length!=2||isNaN(mes))
        return false;

    var anio=FECHA[2];
    if(anio.length!=4||isNaN(anio))
        return false;

    var elMes = parseInt(mes);
    if(elMes>12)
        return false;

    // MES FEBRERO
    if(elMes == 2){
        if(esBisiesto(anio)){
            if(parseInt(dia) > 29)
                return false;
            else
                return true;
        }
        else{
        if(parseInt(dia) > 28)
            return false;
        else
            return true;
        }
    }

    //RESTO DE MESES
    if(elMes== 4 || elMes==6 || elMes==9 || elMes==11){
        if(parseInt(dia) > 30)
            return false;
    }
    else
        if(parseInt(dia) > 31)
            return false;
    return true;
}


function esBisiesto(ano){
    return ((ano%4==0 && ano%100!=0)||(ano%400==0)?true:false)
}


var completarCodigoCeros_CantidadMin=3;

function completarCodigoCeros(cadena,tamano){
//     if(tamano<completarCodigoCeros_CantidadMin)
//         tamano=completarCodigoCeros_CantidadMin;
	if(!tamano)
		tamano=3;
	cadena=String(cadena);
    var p=new String("");
    for(;tamano>cadena.length;tamano--)
        p+='0';
    return (p+=cadena);
}


var completarCodigoCeros_CantidadMin2=2;

function completarCodigoCeros2(cadena,tamano){
    if(tamano<completarCodigoCeros_CantidadMin2)
        tamano=completarCodigoCeros_CantidadMin2;
    var p=new String("");
    for(;tamano>cadena.length;tamano--)
        p+='0';
    return (p+=cadena);
}

function quitarCodigoCeros(cadena){
    cadena=String(cadena);
    var I=0;
    while(cadena.charAt(I) == '0')
        I++;
    return cadena.substring(I);
}


function NDigitosCodigo(Arreglo,ID){
    var n=Arreglo.length;
    if(n==0 || !n)
        return 0;
    var MAX=Arreglo[0][ID];
    for(var i=1; i<n; i++)
        if(parseInt(Arreglo[i][ID])>MAX)
            MAX=parseInt(Arreglo[i][ID]);
    return NDigitos=String(MAX).length;
}


function DarFoco(ID){
    if(xGetElementById(ID))
        xGetElementById(ID).focus();
}


function DarFocoCampo(ID,Tiempo){
    setTimeout("DarFoco('"+ID+"')",Tiempo);
}


var IconoCargando="<IMG src='../../image/loading.gif' width='22' height='22' align='left' border='0'><DIV style=\"font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 14px;\">Cargando...</DIV>";

var IconoCargandoTabla="<TR><TD align='center'><br><table><tbody><tr><td valign='middle'><IMG src='../../image/loading.gif' width='32' height='32' border='0'></td><td style=\"font-family : 'sans-serif', 'Arial','Bitstream Vera Sans'; font-size : 18px;\">Cargando...</td></tr></tbody></table></TD></TR>";



function TipoAccesoModulo(){
	if(!xGetElementById("MODULO_ACTUAL"))
		return -1;
	if(!xGetElementById(xGetElementById("MODULO_ACTUAL").value))
		return -1;
	return xGetElementById(xGetElementById("MODULO_ACTUAL").value).value;
	}


function ActivarBoton(_ID_BOTON,_ID_IMG,_ACCION){
	switch(_ACCION){
		case "guardar":
		case "modificar":
		case "eliminar":
			if(TipoAccesoModulo()=='0'||TipoAccesoModulo()=='1'){
				DesactivarBoton(_ID_BOTON,_ID_IMG,_ACCION);
				return;
				}
		break;
		}

    xGetElementById(_ID_BOTON).disabled=false;
    //xGetElementById(_ID_IMG).setAttribute('src','../images/'+_ACCION+'.png');
    //xGetElementById(_ID_BOTON).setAttribute('onmouseover',"xGetElementById('"+_ID_IMG+"').src='../img/iconos/"+_ACCION+"_con_foco.png'");
    //xGetElementById(_ID_BOTON).setAttribute('onmouseout',"xGetElementById('"+_ID_IMG+"').src='../img/iconos/"+_ACCION+"_activo.png'");
}

function DesactivarBoton(_ID_BOTON,_ID_IMG,_ACCION){
    //xGetElementById(_ID_IMG).setAttribute('src','../img/iconos/'+_ACCION+'.png');
    //xGetElementById(_ID_BOTON).setAttribute('onmouseover',"xGetElementById('"+_ID_IMG+"').src='../img/iconos/"+_ACCION+"_desactivo.png'");
    //xGetElementById(_ID_BOTON).setAttribute('onmouseout',"xGetElementById('"+_ID_IMG+"').src='../img/iconos/"+_ACCION+"_desactivo.png'");
    xGetElementById(_ID_BOTON).disabled=true;
}


//función que valida que se introduzcan sólo números y ciertos caracteres
// NOTA: Backspace=8, Enter=13, '0'=48, '9'=57, Suprimir=127
function soloNum(evt)
{
    var nav4=window.Event?true:false;
    var key=nav4?evt.which:evt.keyCode;
	if(key==13) return false;//necesario para evitar recargas de pagina (ocurre ocacionalmente al presionar enter en el input text de una tabla. ejemplo presupuesto->clasificacion personal)
    return (key<=13 || key==127 || (key>=48 && key<=57));
}

function AcceptNum(evt,ID,Negativo,Porcentaje){
	var nav4 = window.Event ? true : false;
	var key = nav4 ? evt.which : evt.keyCode;
	if(xGetElementById(ID)){
		var Cadena=xGetElementById(ID).value;
		if(key==45 && Negativo){//si es - y esta activado para aceptar numeros negativos
			if(Cadena.indexOf("-")==-1)//si no encuentra -, lo colocamos al inicio, retornamos falso para que no se el incluya - donde presionamos
				xGetElementById(ID).value="-"+Cadena;
			return false;
			}
		else if(key==46){//si es punto
			if(Cadena.length==0)//si la cadena tiene longitud 0 no puedo meter .
				return false;
			if(Cadena.indexOf(".")==-1)//solo debe haber un . en la cadena
				return true;
			return false;
			}
		else if(key==48){//si es cero
			if(Cadena.length==1 && Cadena.indexOf("0")==0)//si hay un caracter en la cadena y ese caracter es cero, no puedo meter otro cero
				return false;
			return true;
			}
		else if(key==37 && Porcentaje){//si es % y esta activado porcentaje
			if(Cadena.indexOf("%")==-1)
				xGetElementById(ID).value=Cadena+"%";
			return false;
			}
		}
	if(key==13) return false;//necesario para evitar recargas de pagina (ocurre ocacionalmente al presionar enter en el input text de una tabla. ejemplo presupuesto->formulacion)
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
	}



	function FormatearFecha(cadena){
		var FECHA=String(cadena).split("-");
		return FECHA[2]+"/"+FECHA[1]+"/"+FECHA[0];
		}


	function DesFormatearFecha(cadena){
		var FECHA=String(cadena).split("/");
		return FECHA[2]+"-"+FECHA[1]+"-"+FECHA[0];
		}


	function FormatearNumero(num){
		if(!num)
			num=0;
		num = num.toString().replace(/$|,/g,'');
		if(isNaN(num))
		num = "0";
		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();
		if(cents<10)
		cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
			num = num.substring(0,num.length-(4*i+3))+'.'+num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') + num + ',' + cents);
		}


	function URLgetValues(){
		var urlEnd = document.URL.indexOf('?');
		var values = new Array();
		var names;
		if (urlEnd != -1){
			var params = document.URL.substring(urlEnd+1, document.URL.length).split('&');
			for(i=0; i<params.length; i++) {
				names = params[i].split('=');
				values[names[0]] = names[1];
				}
			}
		return values;
		}


/*Activa todos los campos de un formulario*/
function activarFormulario(id)
{
    var formulario=xGetElementById(id);

    for (i=0;i<formulario.elements.length;i++)
    {
        tipo=formulario.elements[i].type;
        if(tipo == "select-one" || tipo == "radio" || tipo == "checkbox" || tipo == "button" || tipo == "file")
            formulario.elements[i].disabled=false;
        else
        {
            formulario.elements[i].readOnly=false;
            formulario.elements[i].disabled=false;
        }

        if (tipo)
            formulario.elements[i].setAttribute('class','TextoCampoInputObligatorios');
    }

}

/*Desactiva todos los campos de un formulario*/
function desactivarFormulario(id)
{
    var formulario=xGetElementById(id);

    for (i=0;i<formulario.elements.length;i++)
    {
        tipo=formulario.elements[i].type;
        if(tipo == "select-one" || tipo == "radio" || tipo == "checkbox" || tipo == "button" || tipo == "file")
            formulario.elements[i].disabled=true;
        else
        {
            formulario.elements[i].readOnly=true;
            formulario.elements[i].disabled=true;
        }

        if (tipo)
            formulario.elements[i].setAttribute('class','TextoCampoInputDesactivado');
    }
}

//Redimensionar la foto del personal a 195x175 para mostrar
var ancho, alto;
function redimensionar(xx,yy,ruta)
{
    var n, p;
    var maxAlto=175;
    var maxAncho=195;
    if (xx>yy)
    {
        if (xx>maxAncho)
        {
            n=xx-maxAncho;
            p=(n*100)/xx;
            alto=yy*(1-p/100);
        }
        else
        {
            n=maxAncho-xx;
            p=(n*100)/xx;
            alto=yy*(1+p/100);
        }
        ancho=maxAncho;
    }
    else if (xx<yy)
    {
        if (yy>maxAlto)
        {
            n=yy-maxAlto;
            p=(n*100)/yy;
            ancho=xx*(1-p/100);
        }
        else
        {
            n=maxAlto-yy;
            p=(n*100)/yy;
            ancho=xx*(1+p/100);
        }
        alto=maxAlto;
    }
    else
    {
        ancho=maxAncho;
        alto=maxAlto;
    }
    xGetElementById('imgFotoPersonal').width = ancho;
    xGetElementById('imgFotoPersonal').height = alto;
    xGetElementById('imgFotoPersonal').src = ruta;

}


extArray = new Array(".gif", ".jpg", ".png", ".jpeg", ".bmp");
function LimitAttach(form, file)
{
    allowSubmit = false;
    if (!file) return;
        while (file.indexOf("\\") != -1)
            file = file.slice(file.indexOf("\\") + 1);

    ext = file.slice(file.indexOf(".")).toLowerCase();
    for (var i = 0; i < extArray.length; i++)
    {
        if (extArray[i] == ext)
        {
            allowSubmit = true;
            break;
        }
    }

    if (allowSubmit)
        return true;
    else
    {
        alert("Se permiten únicamente archivos con la extensión: " + (extArray.join(" ")) + "\nPor favor, seleccione otro archivo "
        + "e intente de nuevo.");
        return false;
    }
}


function limpiarMensaje(td,mensaje)
{
    var td=xGetElementById(td);
    limpiarTabla(td);
    mD.agregaNodoTexto(td,mensaje);
    mD.agregaAtributo(td,{'style':'background-color:#FFFFFF;'});
}

function numberFormat(num,dec){
	if(xTrim(String(num))=="")
		return "0.00";
	var N=redondear(num,dec);
	if(N==0)
		return "0.00";
	var aux=String(N).split(".");
	if(aux.length==1)
		return N+".00";
	else if(aux.length==2)
		if(aux[1].length==1)
			return N+"0";
	return String(N);
	}

function redondear(num,dec)
{
    var numstr=String(num);//Ej. redondear(3.31545,2)

    if(numstr.indexOf(".") == -1)
    {
        numstr = numstr + ".";
        for(nfi=0;nfi<dec;nfi++)
            numstr = numstr + "0";
    }

    partes=numstr.split("."); //dividimos por el punto para separar el entero del decimal Ej. 3|31545

    if (partes[1].length>dec)
    {
        comadecimal="0."+partes[1];
        partes[1]=partes[1].substr(0,dec+1); //tomamos los dec+1 dígitos de la parte decimal Ej.315

        //truncamos las parte decimal a dec dígitos
        truncamiento=comadecimal.substr(0,dec+2);
        decimal=parseFloat(truncamiento);

        if (parseInt(partes[1].charAt(dec))>=5) //si el siguiente a dec >= 5 Ej. el 3er caracter de 315 es 5
            decimal=decimal+(1/(Math.pow(10,dec))); //incrementamos en 1 la parte decimal Ej. 0.32

        //sumamos la parte entera más la decimal Ej. 3+0.32=3.32
        numstr=parseFloat(parseInt(partes[0],10)+decimal);
    }
    return (parseFloat(numstr));
}


function EsCodigoPresupuestario_General(Codigo){
	var Cad1;
	Cad1=Codigo.substring(5,9);
	if(Cad1=="0000")
		return true;
// 	else if(Cad1.substring(2,4)=="00");
// 		return true;
	return false;
	}




function RecortarTexto(cadena,TAM_MAX){
	if(!TAM_MAX)
		return cadena;
	if(cadena.length>=TAM_MAX)
		return cadena.substring(0,TAM_MAX)+"...";
	return cadena;
	}

function CargarSELECT(resultado,IDSelect,IDSeleccionActual,NombreIdCampoTabla,NombreDenominacionCampoTabla,TamCodigo,TAM_MAX,NombreTitulo){
	if(!TamCodigo)
		TamCodigo=0;


	//var respuesta = req.responseText;
  //var resultado = eval("(" + respuesta + ")");
	var SelectX = xGetElementById(IDSelect);
	SelectX.innerHTML="";
	var opcion;
	//Cuando es nuevo, sale por defecto SELECCIONE | AGREGE
	if(IDSeleccionActual==-1){
		opcion = mD.agregaNodoElemento("option", null, null, { 'value':"" } );
		opcion.innerHTML="SELECCIONE";
		mD.agregaHijo(SelectX, opcion);
		for(var i=0; i<resultado.length; i++){
			if(!NombreTitulo)
				opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla] } );
			else
				opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla], 'title':resultado[i][NombreTitulo]} );
			//opcion.innerHTML=RecortarTexto(resultado[i][NombreDenominacionCampoTabla],TAM_MAX);

			if(TamCodigo>0)
				opcion.innerHTML=completarCodigoCeros(resultado[i][NombreIdCampoTabla],TamCodigo)+"| "+RecortarTexto(resultado[i][NombreDenominacionCampoTabla],TAM_MAX);
			else
				opcion.innerHTML=RecortarTexto(resultado[i][NombreDenominacionCampoTabla]);

			mD.agregaHijo(SelectX, opcion);
			}
		}
	//Cuando es modificar, sale por defecto el guardado
	else{
		for(var i=0; i<resultado.length; i++){
			if(IDSeleccionActual==resultado[i][NombreIdCampoTabla]){
				if(!NombreTitulo)
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla], 'selected':true} );
				else
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla], 'selected':true, 'title':resultado[i][NombreTitulo]} );
				}
			else{
				if(!NombreTitulo)
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla]} );
				else
					opcion = mD.agregaNodoElemento("option", null, null, { 'value':resultado[i][NombreIdCampoTabla], 'title':resultado[i][NombreTitulo]} );
				}
			if(TamCodigo>0)
				opcion.innerHTML=completarCodigoCeros(resultado[i][NombreIdCampoTabla],TamCodigo)+"| "+RecortarTexto(resultado[i][NombreDenominacionCampoTabla],TAM_MAX);
			else
				opcion.innerHTML=RecortarTexto(resultado[i][NombreDenominacionCampoTabla]);

			mD.agregaHijo(SelectX, opcion);
			}
		}
	}

function Traspasar_InfSelect(SELECTOrigen,SELECTDestino){
	var SO=xGetElementById(SELECTOrigen);
	var SD=xGetElementById(SELECTDestino);
	for(i=0;i<SO.length;i++)
		if(SO[i].selected){
			mD.agregaHijo(SD,SO[i]);
			i--;
			}
	}

function TraspasarTodo_InfSelect(SELECTOrigen,SELECTDestino){
	var SO=xGetElementById(SELECTOrigen);
	var SD=xGetElementById(SELECTDestino);
	while(SO.hasChildNodes()){
		mD.agregaHijo(SD,SO.lastChild);
		}
	}

function SubirElementoSelect(SELECT){
	var S=xGetElementById(SELECT);
	var AUX_value, AUX_innerHTML;
	for(i=0;i<S.length;i++)
		if(S[i].selected){
			if(i==0)
				break;
			AUX_value=S[i].value;
			AUX_innerHTML=S[i].innerHTML;

			S[i].value=S[i-1].value;
			S[i].innerHTML=S[i-1].innerHTML;
			S[i].selected=false;

			S[i-1].value=AUX_value;
			S[i-1].innerHTML=AUX_innerHTML;
			S[i-1].selected=true;
			break;
			}
	}

function BajarElementoSelect(SELECT){
	var S=xGetElementById(SELECT);
	var AUX_value, AUX_innerHTML;
	for(i=0;i<S.length;i++)
		if(S[i].selected){
			if(i==S.length-1)
				break;
			AUX_value=S[i].value;
			AUX_innerHTML=S[i].innerHTML;

			S[i].value=S[i+1].value;
			S[i].innerHTML=S[i+1].innerHTML;
			S[i].selected=false;

			S[i+1].value=AUX_value;
			S[i+1].innerHTML=AUX_innerHTML;
			S[i+1].selected=true;
			break;
			}
	}

function ContenidoSelectValue(SELECT){
	var S=xGetElementById(SELECT);
	var Arreglo=new Array();
	for(i=0;i<S.length;i++){
		Arreglo[i]=S[i].value;
		}
	return Arreglo;
	}

function ContenidoSelectValueSelected(SELECT){
	var S=xGetElementById(SELECT);
	var Arreglo=new Array();
	for(var i=0, c=0;i<S.length;i++){
		if(S[i].selected){
			Arreglo[c]=S[i].value;
			c++;
			}
		}
	return Arreglo;
	}



function PierdeFoco_InputTextNUMERICO(ID,ValorPorDefecto){
	if(xTrim(xGetElementById(ID).value)=="")
		xGetElementById(ID).value=ValorPorDefecto;
	else
		xGetElementById(ID).value=numberFormat(xGetElementById(ID).value,2);
	}

function TomaFoco_InputTextNUMERICO(ID,ValorPorDefecto){
	if(xTrim(xGetElementById(ID).value)==ValorPorDefecto)
		xGetElementById(ID).value="";
	}

var NDigitos_Codigo_Articulo=4;
var NDigitos_Codigo_Requisicion=10;
var NDigitos_Codigo_OrdenDeCompra=10;
var NDigitos_Codigo_Comprobante=10;
var NDigitos_Codigo_Documento=10;
var Form_REQUISICION_DE_BIENES__NDigitosCodigoUnidadAdministrativa=4;
var NDigitos_Codigo_SolicitudPago=10;
var NDigitos_Codigo_ProgramacionPago=8;
var NDigitos_Codigo_CtaBancaria=3;
var NDigitos_Codigo_VoucherCheque=8;
var NDigitos_Codigo_InventarioBienes=10;
var NDigitos_Codigo_GradoInstruccion=10;
var NDigitos_Codigo_ComprobanteCargoRetencion=8;

//Copia un array u objeto de cualquier tipo (segun)
function copy(o){
	if (typeof o != "object" || o === null) return o;
	var r = o.constructor == Array ? [] : {};
	for (var i in o) {
		r[i] = copy(o[i]);
		}
    	return r;
	}


function FormatearCodigoProgramatico(ACC,AE,OAE){
	if(ACC.substring(0,3)=="ACC")
		return ACC+"-"+completarCodigoCeros(AE,7)+"-"+completarCodigoCeros(OAE,2);
	return ACC+"-"+ACC.substring(3,ACC.length)+completarCodigoCeros(AE,2)+"-"+completarCodigoCeros(OAE,2);
	}


function FormatearCodigoProgramaticoAE(ACC,AE){
	if(ACC.substring(0,3)=="ACC")
		return completarCodigoCeros(AE,7);
	return ACC.substring(3,ACC.length)+completarCodigoCeros(AE,2);
	}


function mesEnLetras(mes)
{
	mes=parseInt(mes,10);
	if (mes==1)
		mes="ENERO";
	else if (mes==2)
		mes="FEBRERO";
	else if (mes==3)
		mes="MARZO";
	else if (mes==4)
		mes="ABRIL";
	else if (mes==5)
		mes="MAYO";
	else if (mes==6)
		mes="JUNIO";
	else if (mes==7)
		mes="JULIO";
	else if (mes==8)
		mes="AGOSTO";
	else if (mes==9)
		mes="SEPTIEMBRE";
	else if (mes==10)
		mes="OCTUBRE";
	else if (mes==11)
		mes="NOVIEMBRE";
	else if (mes==12)
		mes="DICIEMBRE";

	return mes;
}

function xTrimCeros(s) {
  return s.replace(/^0+|0+$/g, '');
}



function VerificarSession(){
	AjaxRequest.post({'onSuccess':
					 	function(req){
							var respuesta = req.responseText;
							if(respuesta==1)
								setTimeout("VerificarSession()",60000);
							else
								javascript:document.location.href= '../index.php';
					 		},
					 'url':'../modulo_principal/verificar_sesion.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}


function TraduceAcronimo(acronimo){
	switch(acronimo.toUpperCase()){
		case "SPGFAP": return "MODULO PRESUPUESTO -> FORMULACIÓN ASIGNACIÓN PRESUPUESTO";
		case "SCOASC": return "MODULO CONTABILIDAD -> ASIENTO CONTABLE";
		case "SPGCMP": return "MODULO PRESUPUESTO -> COMPROBANTE DE GASTO";
		case "SPGCRA": return "MODULO PRESUPUESTO -> MODIFICACIÓN PRESUPUESTARIA -> CRÉDITO ADICIONAL";
		case "SPGRED": return "MODULO PRESUPUESTO -> MODIFICACIÓN PRESUPUESTARIA -> REDUCCIÓN DE CRÉDITO";
		case "SPGTRA": return "MODULO PRESUPUESTO -> MODIFICACIÓN PRESUPUESTARIA -> TRASPASO DE CRÉDITO";
		case "SOCCOC": return "MODULO COMPRAS -> ORDEN DE COMPRA";
		case "SOCCOS": return "MODULO COMPRAS -> ORDEN DE SERVICO";
		case "CXPRDD": return "MODULO CUENTAS POR PAGAR -> RECEPCIÓN DE DOCUMENTOS";
		case "CXPSOP": return "MODULO CUENTAS POR PAGAR -> SOLICITUD/ORDEN DE PAGO";
		case "BNCCHE": return "MODULO BANCO -> CHEQUE";
		case "BNCMOV": return "MODULO BANCO -> MOVIMIENTO BANCARIO";
		case "SOCOCP": return "MODULO COMPRAS -> ORDEN DE COMPRA (PRECOMPROMISO)";
		case "SOCOSP": return "MODULO COMPRAS -> ORDEN DE SERVICIO (PRECOMPROMISO)";
		}
	return acronimo+" SIN REGISTRAR";
	}

