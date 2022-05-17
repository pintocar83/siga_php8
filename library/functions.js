var validPhone = /^([(][0]([4][12][246]|[2][0-9][0-9])[)][0-9]{3}[.][0-9]{2}[.][0-9]{2}([ ][(][0]([4][12][246]|[2][0-9][0-9])[)][0-9]{3}[.][0-9]{2}[.][0-9]{2})*)$/;
Ext.apply(Ext.form.field.VTypes, {
  phone: function(val, field) {
      return validPhone.test(val);
  },
  phoneText: 'Número telefónico invalido.\nFormato: (0999)999.99.99'
});

function getData(url,json){
  json = (typeof json === "undefined")?true:json;//valor por defecto true
  var request = ((window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
  request.open("GET", url, false); //<-- false makes it a synchonous request!
  request.send(null);
  return json==true?Ext.decode(request.responseText):request.responseText;
}



function postData(url,params){
  var request = ((window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
  request.open("POST", url, false); //<-- false makes it a synchonous request!
  request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  request.send(params);
  return Ext.decode(request.responseText);
}

function formatDate(str){
  var d=String(str).split("-");
  return d[2]+"/"+d[1]+"/"+d[0];
}

function unformatDate(str){
  var d=String(str).split("/");
  return d[2]+"-"+d[1]+"-"+d[0];
}

function formatNumberDec(num,dec){
	if(String(num).trim()=="")
		return "0.00";
	var N=roundingNumber(num,dec);
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

function formatNumber(num){
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

function roundingNumber(num,dec){
  var numstr=String(num);//Ej. redondear(3.31545,2)
  if(numstr.indexOf(".") == -1){
    numstr = numstr + ".";
    for(nfi=0;nfi<dec;nfi++)
      numstr = numstr + "0";
  }
  partes=numstr.split("."); //dividimos por el punto para separar el entero del decimal Ej. 3|31545
  if(partes[1].length>dec){
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

function onlyNumber(o){
	var key = window.Event ? o.event.which : o.event.keyCode;        
	if(o.elDom){
    var Value=String(o.elDom.value);    
		if(key==45 && o.negative){//si es - y esta activado para aceptar numeros negativos
			if(Value.indexOf("-")==-1)//si no encuentra -, lo colocamos al inicio, retornamos falso para que no se el incluya - donde presionamos
				o.elDom.value="-"+Value;
			return false;
			}
		else if(key==46){//si es punto
			if(Value.length==0)//si la cadena tiene longitud 0 no puedo meter .
				return false;
			if(Value.indexOf(".")==-1)//solo debe haber un . en la cadena
				return true;
			return false;
			}
		else if(key==48){//si es cero
			if(Value.length==1 && Value.indexOf("0")==0)//si hay un caracter en la cadena y ese caracter es cero, no puedo meter otro cero
				return false;
			return true;
			}
		else if(key==37 && o.percent){//si es % y esta activado porcentaje
			if(Value.indexOf("%")==-1)
				o.elDom.value=Value+"%";
			return false;
			}
		}
	if(key==13) return false;//necesario para evitar recargas de pagina (ocurre ocacionalmente al presionar enter en el input text de una tabla. ejemplo presupuesto->formulacion)
	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

//Copia un array u objeto de cualquier tipo (segun)
function copy(o){
	if (typeof o != "object" || o === null) return o;
	var r = o.constructor == Array ? [] : {};
	for(var i in o)
		r[i] = copy(o[i]);		
  return r;
}

function now(f){
  return siga.get({action: "date", format: f}).result;
}

function isValidDate(str) {
  var regex = /\d{2}[/]\d{2}[/]\d{4}$/;
  if(!regex.test(str)) return false;
  var bits=str.split('/');
  var y=bits[2], m=bits[1], d=bits[0];
  var daysInMonth=[31,28,31,30,31,30,31,31,30,31,30,31]; 
  if((!(y%4) && y%100) || !(y%400)) daysInMonth[1]=29;    
  return d<=daysInMonth[--m];
}

function strtoupper(str){
  return String(str).toUpperCase();
}

function str_replace(_text,_replace,_search){
    return String(_text).split(_search).join(_replace);
}


