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



function Form_EJECUCION__CargarDatos(){
	if(xGetElementById("sw_formulacion_reformulacion"))
		if(xGetElementById("sw_formulacion_reformulacion").value=="1")
			xGetElementById("sw_formulacion_reformulacion_FEI").options[1].selected=true;

	Form_EJECUCION__TabPane.setSelectedIndex(0);
	Form_EJECUCION__CargarSelectTrimestreMes();
	//Form_EJECUCION__CargarFuente();
	
	Form_EJECUCION__CargarAC();
	
	
	}

function Form_EJECUCION__CargarSelectTrimestreMes(){
	switch(xGetElementById("SELECT_TIPO_FEI").value){
		case 'T'://trinestral
			xGetElementById("TRIMESTRE_MES_FEI").innerHTML="<OPTION value='1'>I</OPTION><OPTION value='2'>II</OPTION><OPTION value='3'>III</OPTION><OPTION value='4'>IV</OPTION>";
			break;
		case 'M'://mensual
			xGetElementById("TRIMESTRE_MES_FEI").innerHTML="<OPTION value='1'>ENERO</OPTION><OPTION value='2'>FEBRERO</OPTION><OPTION value='3'>MARZO</OPTION><OPTION value='4'>ABRIL</OPTION><OPTION value='5'>MAYO</OPTION><OPTION value='6'>JUNIO</OPTION><OPTION value='7'>JULIO</OPTION><OPTION value='8'>AGOSTO</OPTION><OPTION value='9'>SEPTIEMBRE</OPTION><OPTION value='10'>OCTUBRE</OPTION><OPTION value='11'>NOVIEMBRE</OPTION><OPTION value='12'>DICIEMBRE</OPTION>";
			break;
		default:
			xGetElementById("TRIMESTRE_MES_FEI").innerHTML="";
			return;
	}
}
/*
function Form_EJECUCION__CargarFuente(){
	AjaxRequest.post({'parameters':{'action':"onList",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_fuente","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA4_FEI",-2,"id","codigo_denominacion_fuente",'','','denominacion_fuente');
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA3_FEI",-2,"id","codigo_denominacion_fuente",'','','denominacion_fuente');
							Form_EJECUCION__CargarAC();
							},
					 'url':'../modulo_presupuesto/fuente_recursos.php',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}
*/

function Form_EJECUCION__CargarAC(){
	AjaxRequest.post({'parameters':{'action':"onList_AccionCentralizada_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];						
							
							CargarSELECT(resultado,"ID_PROYECTO_FEI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO2_FEI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO3_FEI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO4_FEI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							Form_EJECUCION__CargarAE();
							Form_EJECUCION__CargarAE_6();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_EJECUCION__CargarAE(){
	if(!xGetElementById("ID_PROYECTO2_FEI").value)
		return;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("ID_PROYECTO2_FEI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];							
							CargarSELECT(resultado,"ID_ACCION_ESPECIFICA_FEI",-2,"id","codigo_especifica",'','','denominacion_especifica');
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}
	
function Form_EJECUCION__CargarAE_6(){
	if(!xGetElementById("ID_PROYECTO3_FEI").value)
		return;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("ID_PROYECTO3_FEI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];							
							CargarSELECT(resultado,"ID_ACCION_ESPECIFICA3_FEI",-2,"id","codigo_especifica",'','','denominacion_especifica');
							Form_EJECUCION__CargarOAE_6();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}


function Form_EJECUCION__CargarOAE_6(){
	if(!xGetElementById("ID_ACCION_ESPECIFICA3_FEI").value)
		return;
	
	
	AjaxRequest.post({'parameters':{'action':"onList_AccionSubEspecifica_AP",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]',
									'id_accion_especifica':xGetElementById("ID_ACCION_ESPECIFICA3_FEI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA3_FEI",-2,"id","codigo_subespecifica",'','','denominacion_subespecifica');
							
							
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}







function Form_EJECUCION__Imprimir(formato){
	if(!formato) formato="ejecucion";
	var Opcion;
	for(var i=1;i<=7;i++)
		if(xGetElementById("ID_RADIO_IMPRIMIR_FEI_"+i).checked){
			Opcion=i;
			break;
			}
	var sw_ocultar=0;
	if(xGetElementById("OCULTAR_FILAS_FFPDG_1").checked)
		sw_ocultar=1;
	
	
	var t=xGetElementById("SELECT_TIPO_FEI").value;
	var v=xGetElementById("TRIMESTRE_MES_FEI").value;
	
	var tipo=xGetElementById("NIVEL_DETALLE_FEI").value;
	


	if(Opcion<=3)
		window.open("../../report/"+formato+".php?tipo="+tipo+"&opcion="+Opcion+"&ocultar="+sw_ocultar+"&periodotipo="+t+"&periodo="+v);
	else if(Opcion==4)
		window.open("../../report/"+formato+".php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO_FEI").value+"&OCULTAR="+sw_ocultar+"&periodotipo="+t+"&periodo="+v);
	else if(Opcion==5)
		window.open("../../report/"+formato+".php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO2_FEI").value+"&id_accion_especifica="+xGetElementById("ID_ACCION_ESPECIFICA_FEI").value+"&OCULTAR="+sw_ocultar+"&periodotipo="+t+"&periodo="+v);
	else if(Opcion==6)
		window.open("../../report/"+formato+".php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_subespecifica="+xGetElementById("ID_OTRA_ACCION_ESPECIFICA3_FEI").value+"&OCULTAR="+sw_ocultar+"&periodotipo="+t+"&periodo="+v);
	else if(Opcion==7)
		window.open("../../report/"+formato+".php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO4_FEI").value+"&id_fuente_recursos="+xGetElementById("ID_OTRA_ACCION_ESPECIFICA4_FEI").value+"&OCULTAR="+sw_ocultar+"&periodotipo="+t+"&periodo="+v);
	}



