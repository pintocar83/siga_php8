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

function Form_CERRAR_MES__Mensaje(MSG,color){
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FCM").innerHTML=MSG;
	}

function Form_CERRAR_MES__Cargar(){
	DesactivarBoton("BOTON_ACEPTAR_FCM","BOTON_ACEPTAR_FCM",'aceptar');
	for(var i=1;i<=12;i++)
		xGetElementById("MES_"+i+"_FCM").disabled=true;

	AjaxRequest.post({
						'parameters':{ 'action':"onGet"},
						'onSuccess':
							function(req){
								var resultado = eval(req.responseText);								
								resultado=resultado[0]["mes_cerrado"].replace("{","").replace("}","").split(",");
								for(var i=0;i<resultado.length;i++)
									if(resultado[i]=="t")
										xGetElementById("MES_"+(i+1)+"_FCM").checked=true;
									else
										xGetElementById("MES_"+(i+1)+"_FCM").checked=false;
								for(var i=1;i<=12;i++)
									xGetElementById("MES_"+i+"_FCM").disabled=false;
								ActivarBoton("BOTON_ACEPTAR_FCM","BOTON_ACEPTAR_FCM",'aceptar');
								},
						'url':'../anio_detalle/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

function Form_CERRAR_MES__Aceptar(){
	DesactivarBoton("BOTON_ACEPTAR_FCM","BOTON_ACEPTAR_FCM",'aceptar');
	var arreglo=[];
	for(var i=1;i<=12;i++)
		if(xGetElementById("MES_"+i+"_FCM").checked)
			arreglo[i-1]='t';
		else
			arreglo[i-1]='f';

	var mes_cerrado="{"+arreglo.join()+"}";
	
	AjaxRequest.post({
						'parameters':{
								'action':"onSave",
								'mes_cerrado':mes_cerrado},
						'onSuccess':
							function(req){
								var respuesta = eval("("+req.responseText+")");
								Form_CERRAR_MES__Mensaje(respuesta.message,respuesta.success==true?"VERDE":"ROJO");
								Form_CERRAR_MES__Cargar();
								},
						'url':'../anio_detalle/',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
	}

