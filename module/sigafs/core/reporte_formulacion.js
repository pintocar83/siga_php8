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



function Form_FORMULACION_PRESUPUESTO_DE_GATOS_IMPRIMIR_CargarDatos(){
	if(xGetElementById("sw_formulacion_reformulacion"))
		if(xGetElementById("sw_formulacion_reformulacion").value=="1")
			xGetElementById("sw_formulacion_reformulacion_FFPDGI").options[1].selected=true;

	Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__TabPane.setSelectedIndex(0);
	Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarFuente();
	}


function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarFuente(){
	AjaxRequest.post({'parameters':{'action':"onList",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_fuente","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA4_FFPDGI",-2,"id","codigo_denominacion_fuente",'','','denominacion_fuente');
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA3_FFPDGI",-2,"id","codigo_denominacion_fuente",'','','denominacion_fuente');
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarAC();
							},
					 'url':'../fuente_recursos/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}


function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarAC(){
	AjaxRequest.post({'parameters':{'action':"onList_AccionCentralizada",'text':'','start':0,'limit':'ALL','sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"}]'},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];						
							
							CargarSELECT(resultado,"ID_PROYECTO_FFPDGI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO2_FFPDGI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO3_FFPDGI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							CargarSELECT(resultado,"ID_PROYECTO4_FFPDGI",-2,"id","tipo_codigo_centralizada",'','','denominacion_centralizada');
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarAE();
							Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR3__CargarAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__CargarAE(){
	if(!xGetElementById("ID_PROYECTO2_FFPDGI").value)
		return;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("ID_PROYECTO2_FFPDGI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];							
							CargarSELECT(resultado,"ID_ACCION_ESPECIFICA_FFPDGI",-2,"id","codigo_especifica",'','','denominacion_especifica');
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}
	
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR3__CargarAE(){
	if(!xGetElementById("ID_PROYECTO3_FFPDGI").value)
		return;
	AjaxRequest.post({'parameters':{'action':"onList_AccionEspecifica",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_especifica","direction":"ASC"}]',
									'id_accion_centralizada':xGetElementById("ID_PROYECTO3_FFPDGI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];							
							CargarSELECT(resultado,"ID_ACCION_ESPECIFICA3_FFPDGI",-2,"id","codigo_especifica",'','','denominacion_especifica');
							//Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR3__CargarOAE();
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}

/*
function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR3__CargarOAE(){
	if(!xGetElementById("ID_ACCION_ESPECIFICA3_FFPDGI").value)
		return;
	AjaxRequest.post({'parameters':{'action':"onList_AccionSubEspecifica",'text':'','start':0,'limit':'ALL','sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]',
									'id_accion_especifica':xGetElementById("ID_ACCION_ESPECIFICA3_FFPDGI").value},
					 'onSuccess':
						function(req){
							var respuesta = req.responseText;
							var resultado = eval("(" + respuesta + ")");
							resultado=resultado["result"];
							CargarSELECT(resultado,"ID_OTRA_ACCION_ESPECIFICA3_FFPDGI",-2,"id","codigo_subespecifica",'','','denominacion_subespecifica');
							},
					 'url':'../estructura_presupuestaria/',
					 'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					 });
	}
*/











function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__Imprimir(){
	var Opcion;
	for(var i=1;i<=7;i++)
		if(xGetElementById("ID_RADIO_IMPRIMIR_FFPDGI_"+i).checked){
			Opcion=i;
			break;
			}
	var sw_ocultar=0;
	if(xGetElementById("OCULTAR_FILAS_FFPDG_1").checked)
		sw_ocultar=1;

	var tipo=xGetElementById("sw_formulacion_reformulacion_FFPDGI").value;
	


	if(Opcion<=3)
		window.open("../../report/formulacion.php?tipo="+tipo+"&opcion="+Opcion+"&ocultar="+sw_ocultar);
	else if(Opcion==4)
		window.open("../../report/formulacion.php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO_FFPDGI").value+"&OCULTAR="+sw_ocultar);
	else if(Opcion==5)
		window.open("../../report/formulacion.php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO2_FFPDGI").value+"&id_accion_especifica="+xGetElementById("ID_ACCION_ESPECIFICA_FFPDGI").value+"&OCULTAR="+sw_ocultar);
	else if(Opcion==6)
		window.open("../../report/formulacion.php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_especifica="+xGetElementById("ID_ACCION_ESPECIFICA3_FFPDGI").value+"&id_fuente_recursos="+xGetElementById("ID_OTRA_ACCION_ESPECIFICA3_FFPDGI").value+"&OCULTAR="+sw_ocultar);
	else if(Opcion==7)
		window.open("../../report/formulacion.php?tipo="+tipo+"&opcion="+Opcion+"&id_accion_centralizada="+xGetElementById("ID_PROYECTO4_FFPDGI").value+"&id_fuente_recursos="+xGetElementById("ID_OTRA_ACCION_ESPECIFICA4_FFPDGI").value+"&OCULTAR="+sw_ocultar);
	}

function Form_FORMULACION_PRESUPUESTO_DE_GASTOS_IMPRIMIR__Imprimir_2(){
	var Opcion;
	for(var i=1;i<=17;i++)
		if(xGetElementById("ID_RADIO_IMPRIMIR_FFPDGI_2_"+i).checked){
			Opcion=i;
			break;
			}
	var sw_ocultar=0;
	if(xGetElementById("OCULTAR_FILAS_FFPDG_2").checked)
		sw_ocultar=1;

	var tabla="formulacion";
	if(xGetElementById("sw_formulacion_reformulacion_FFPDGI").value=="1")//reformulacion
		tabla="reformulacion";


	if(Opcion==1)
		window.open("../modulo_ayuda/instructivos/INST_04-2009.pdf");
	else if(Opcion==2)
		window.open("../modulo_instructivo_4/reportes/formulacion_B.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==3)
		window.open("../modulo_instructivo_4/reportes/formulacion_C.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==4)
		window.open("../modulo_instructivo_4/reportes/formulacion_D.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==5)
		window.open("../modulo_instructivo_4/reportes/formulacion_E.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==6)
		window.open("../modulo_instructivo_4/reportes/formulacion_F.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==7)
		window.open("../modulo_instructivo_4/reportes/formulacion_G.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==8)
		window.open("../modulo_instructivo_4/reportes/formulacion_H.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==9)
		window.open("../modulo_instructivo_4/reportes/estado_resultado_A.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==10)
		window.open("../modulo_instructivo_4/reportes/presupuesto_caja_A.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==11)
		window.open("../modulo_instructivo_4/reportes/resumen_inversiones_A.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==12)
		window.open("../modulo_instructivo_4/reportes/cta_ahorro_inversion_financiamiento_A.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
	else if(Opcion==13)
		window.open("../modulo_instructivo_4/reportes/identificacion_ente.php");
	else if(Opcion==15)
		window.open("../modulo_instructivo_4/reportes/deuda_servicios_basicos.php");
	else if(Opcion==16)
		window.open("../modulo_instructivo_4/reportes/proyectos_exceden_ejerc_fiscal.php");
	else if(Opcion==17)
		window.open("../modulo_instructivo_4/reportes/formulacion_I.php?OCULTAR="+sw_ocultar+"&tabla="+tabla);
}
