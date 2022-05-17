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
* @version 20090909
*/

 function vacioPresupuestaria(q)
{
var contador = 0;
    for ( i = 0; i < q.length; i++ ) {
            if ( q.charAt(i) == " " ) {
                    contador++;
            }
    }
    if (contador < q.length){
        return false;
    }
    else{
        return true;
    }
}


function validarCamposTipoOperacionesCentralizada ()
{
var id_campo = new Array("codigoCentralizada","denominacionCentralizada");
var respuesta = new Array("Código","Denominación");

    var error = "";
    var seleccionado = 0;
    for (var i=0; i<id_campo.length; i++){
        var obj = xGetElementById(id_campo[i]);
        if(obj){
            switch(obj.type){
                case "text":
                case "textarea":
                case "select-one":
                case "hidden":

                    if(vacioPresupuestaria(obj.value))
                    {
                        error = error+"- "+respuesta[i]+"\n";
                    }
                    break;
                    case "radio":

                    if(obj.checked)
                    {
                      seleccionado = 1;

                    }
                    break;
                default:
                }
            }
    }

    if(seleccionado == 0)
    {
   // error = error+"- "+respuesta[i]+"\n";
	//error = error+"- Tipo\n";
    }
    if (error!="")
    {

	var msg="Error de validación, complete los siguientes campos: \n"+error;
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(msg,"ROJO");
	//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListado("");
        return false;
    }
    return true;
}


function validarCamposTipoOperacionesEspecifica ()
{
var id_campo = new Array("codigoEspecifica","denominacionEspecifica","hidCodCentralizada");
var respuesta = new Array("Código","Denominación","Debe seleccionar un proyecto");

    var error = "";
    var seleccionado = 0;
    for (var i=0; i<id_campo.length; i++){
        var obj = xGetElementById(id_campo[i]);
        if(obj){
            switch(obj.type){
                case "text":
                case "textarea":
                case "select-one":
                case "hidden":

                    if(vacioPresupuestaria(obj.value))
                    {
                        error = error+"- "+respuesta[i]+"\n";
                    }
                    break;
                    case "radio":

                    if(obj.checked)
                    {
                      seleccionado = 1;

                    }
                    break;
                default:
                }
            }
    }

    if(seleccionado == 0)
    {
   // error = error+"- "+respuesta[i]+"\n";
	//error = error+"- Tipo\n";
    }
    if (error!="")
    {

	var msg="Error de validación, complete los siguientes campos: \n"+error;
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(msg,"ROJO");
	//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListadoEspecifica("");
        return false;
    }
    return true;
}


function validarCamposTipoOperacionesEspecificaOtros ()
{
var id_campo = new Array("codigoOtros","denominacionOtros","hidCodEspecifica");
var respuesta = new Array("Código","Denominación","Debe seleccionar una acción específica");

    var error = "";
    var seleccionado = 0;
    for (var i=0; i<id_campo.length; i++){
        var obj = xGetElementById(id_campo[i]);
        if(obj){
            switch(obj.type){
                case "text":
                case "textarea":
                case "select-one":
                case "hidden":

                    if(vacioPresupuestaria(obj.value))
                    {
                        error = error+"- "+respuesta[i]+"\n";
                    }
                    break;
                    case "radio":

                    if(obj.checked)
                    {
                      seleccionado = 1;

                    }
                    break;
                default:
                }
            }
    }

    if(seleccionado == 0)
    {
   // error = error+"- "+respuesta[i]+"\n";
	//error = error+"- Tipo\n";
    }
    if (error!="")
    {

	var msg="Error de validación, complete los siguientes campos: \n"+error;
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE(msg,"ROJO");
	//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListadoEspecifica("");
        return false;
    }
    return true;
}


 /**
* Verifica la existencia de los datos (duplicidad) antes de guardar
*/
	function GuardarVerificarCentralizada()
	{

	var accionEspecOtros=IDSeleccionActualListaOtros;
	var accionEspecifica=IDSeleccionActualListaEspecifica;
	var accionCentralizada=IDSeleccionActualLista;


  //TERCERA PESTAÑA
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==2){
    var codOtros=completarCodigoCeros(xGetElementById('codigoOtros').value,2);
    var denomOtros= xGetElementById('denominacionOtros').value.toUpperCase();
    var id_accion_subespecifica="";
      if(IDSeleccionActualListaOtros==-1 || IDSeleccionActualListaOtros==""){
        id_accion_subespecifica="";
      }
      else{
        id_accion_subespecifica=IDSeleccionActualListaOtros;
        if(!confirm("¿Esta seguro que desea guardar los cambios?")){
          return;
        }
      }
    desactivarFormularioOtros();
    AjaxRequest.post({
        'parameters':{
          'action': "onSave_AccionSubEspecifica",
          'id_accion_especifica':IDSeleccionActualListaEspecifica,
          'id_accion_subespecifica':id_accion_subespecifica,
          'codigo':codOtros,
          'denominacion':denomOtros
        },
        'onSuccess':mostrarDatosOtros,
        'url':'../estructura_presupuestaria/',
        'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
        });
	}

  //SEGUNDA PESTAÑA
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==1){
		var codEspecifica= xGetElementById('codigoEspecifica').value.toUpperCase();
    var denomEspecifica= xGetElementById('denominacionEspecifica').value.toUpperCase();
    
    var id_accion_especifica="";
      if(IDSeleccionActualListaEspecifica==-1 || IDSeleccionActualListaEspecifica==""){
        id_accion_especifica="";
      }
      else{
        id_accion_especifica=IDSeleccionActualListaEspecifica;
        if(!confirm("¿Esta seguro que desea guardar los cambios?")){
          return;
        }
      }
    desactivarFormularioEspecifica();
    AjaxRequest.post({
        'parameters':{
          'action': "onSave_AccionEspecifica",
          'id_accion_centralizada':IDSeleccionActualLista,
          'id_accion_especifica':id_accion_especifica,
				  'codigo':codEspecifica,
          'denominacion':denomEspecifica
				},
				'onSuccess':mostrarDatosAccionEspecifica,
				'url':'../estructura_presupuestaria/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
      });
		}
  //PRIMERA PESTAÑA
	if(Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==0){
    if(validarCamposTipoOperacionesCentralizada()){
      var tipo = xGetElementById('escestPres').value;
		  var codCentralizada=xGetElementById('codigoCentralizada').value;
		  var denomCentralizada= xGetElementById('denominacionCentralizada').value.toUpperCase();
      var id_accion_centralizada="";
      if(IDSeleccionActualLista==-1 || IDSeleccionActualLista==""){
        id_accion_centralizada="";
      }
      else{
        id_accion_centralizada=IDSeleccionActualLista;
        if(!confirm("¿Esta seguro que desea guardar los cambios?")){
          return;
        }
      }
      
		  desactivarFormularioCentralizada();
		  AjaxRequest.post({
        'parameters':{
          'action': "onSave_AccionCentralizada",
          'id_accion_centralizada':id_accion_centralizada,
          'tipo': tipo,
				  'codigo':codCentralizada,
          'denominacion':denomCentralizada
				},
				'onSuccess':mostrarDatosAccionCentralizada,
				'url':'../estructura_presupuestaria/',
				'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
      });
		}
	}
}

//***Verifica que se introduzcan en el campo del cód de la estrut presupuestaria sólo 6 digitos
function soloMinDigitos()
{

	if(xGetElementById('codigoCentralizada').value.length<=6)
	{
		xGetElementById('codigoCentralizada').focus();
		return false;
	}
}

function colocarCeros()
{
xGetElementById('codigoCentralizada').value="";
if(xGetElementById('escestPres').value=="ACC")
xGetElementById('codigoCentralizada').value='000000';

}

//	function guardarOtros(req)
//	{
//	var respuesta = req.responseText;
// 	var resultado = eval("(" + respuesta + ")");
//
////completarCodigoCeros(String(parseInt(cod,10)+1),10);
//	if(!resultado || resultado[0]['cantidad']==0)
//	{
//	if(validarCamposTipoOperacionesEspecificaOtros())
//    	{
//	var codOtros= completarCodigoCeros(xGetElementById('codigoOtros').value,2);
//    	var denomOtros= xGetElementById('denominacionOtros').value.toUpperCase();
//// 	alert(codOtros);
//// 	return;
//
//		if(IDSeleccionActualListaOtros==-1)
//		{
//			AjaxRequest.post({'parameters':{ 'accion':"guardarEspecificaOtros", 'codOtros':codOtros, 'denomOtros':denomOtros, 'accionEspecifica':IDSeleccionActualListaEspecifica},
//			'onSuccess':function(req){mostrarDatosOtros(req);},
//			'url':'consultas.php',
//			'onError':function(req)
//			{
//			alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);
//			}
//			});
//		}
//
//		else{
//
//		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
//			activarFormularioOtros();
//			return;
//			}
//		AjaxRequest.post({'parameters':{ 'accion':"modificarEspecificaOtros",
//									'codOtros':codOtros,
//									'denomOtros':denomOtros,
//									'id_otras_acciones_especificas':IDSeleccionActualListaOtros},
//						'onSuccess':function(req){mostrarDatosOtrosMod(req);},
//						'url':'consultas.php',
//						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
//						});
//		}
//	}
//
//
//	}
//
// 	//Si ya existe un tipo de cuenta con el mismo nombre. No guardar.
//		if(resultado[0]['cantidad']>=1)
//		{
//		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("No se puede guardar los datos. Código existente","ROJO");
//		activarFormularioOtros();
//		return;
//		}
//
//	}

//	function mostrarDatosOtrosMod(req)
//	{
//	var respuesta = req.responseText;
//	var resultado = eval("(" + respuesta + ")");
//	//activarFormularioCentralizada();
////  	alert(respuesta);
////  	alert(resultado);
////
//	if(respuesta==1)
//	{
//		xGetElementById('codigoOtros').value="";
//		xGetElementById('denominacionOtros').value="";
//		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("Los datos se guadaron satisfactoriamente.","VERDE");
//		buscarListadoOtros(IDSeleccionActualListaEspecifica);
//
//
//	}
//	else
//		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("Error. No se pudo guardar los datos. Vuelva a intentarlo.","ROJO");
//	}

//LISTO
function mostrarDatosOtros(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	if(resultado.success){
		xGetElementById('codigoOtros').value="";
		xGetElementById('denominacionOtros').value="";
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE(resultado.message,"VERDE");
		buscarListadoOtros();
	}
	else
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("Error. No se pudo guardar los datos. Vuelva a intentarlo.","ROJO");
}



//LISTO
function mostrarDatosAccionEspecifica(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
	if(resultado.success){
		xGetElementById('codigoEspecifica').value="";
		xGetElementById('denominacionEspecifica').value="";
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(resultado.message,"VERDE");
		buscarListadoEspecifica();
	}
	else
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(resultado.message,"ROJO");
}

	
/*
	function guardarAccionCentralizada(req)
	{
	var respuesta = req.responseText;
 	var resultado = eval("(" + respuesta + ")");

// 	alert(respuesta);
// 	alert(resultado);


 	//Si ya existe un tipo de cuenta con el mismo nombre. No guardar.
		if(resultado[0]['count']>=1)
		{
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("No se puede guardar los datos. Ya existe un proyecto con el mismo código","ROJO");
		//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListado("");
		activarFormularioCentralizada();
		return;
		}

	if(validarCamposTipoOperacionesCentralizada())
    	{
	var codCentralizada= xGetElementById('escestPres').value+xGetElementById('codigoCentralizada').value;
    	var denomCentralizada= xGetElementById('denominacionCentralizada').value.toUpperCase();

        //var unidMedida= xGetElementById('unidMedida').value;
    	//var areaEstrat= xGetElementById('areaEstrat').value.toUpperCase();
        //var responsableProy= xGetElementById('responsableProy').value;
    	//var masculino= xGetElementById('masculino').value;
    	//var femenino= xGetElementById('femenino').value;



		if(IDSeleccionActualLista==-1)
		{
			AjaxRequest.post({'parameters':{ 'accion':"guardarAccionCentralizada", 'codCentralizada':codCentralizada, 'denomCentralizada':denomCentralizada},
			'onSuccess':mostrarDatosAccionCentralizada,
			'url':'consultas.php',
			'onError':function(req)
			{
			alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);
			}
			});
		}


		else{

		if(!confirm("¿Esta seguro que desea guardar los cambios?")){
			activarFormularioCentralizada();
			return;
			}
		AjaxRequest.post({'parameters':{ 'accion':"modificarAccionCentralizada",
										'codCentralizada':codCentralizada,
										'denomCentralizada':denomCentralizada,
										'id_accion_centralizada':IDSeleccionActualLista},
						'onSuccess':mostrarDatosAccionCentralizada,
						'url':'consultas.php',
						'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
						});
		}


	}

	}*/

//LISTA
function mostrarDatosAccionCentralizada(req){
  var respuesta = req.responseText;
  var resultado = eval("(" + respuesta + ")");
  activarFormularioCentralizada();
  if(resultado.success){
    nuevoCentralizada();
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(resultado.message,"VERDE");
  }
  else
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(resultado.message,"ROJO");
}



	function Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(MSG,color)
	{
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FB_CENTRALIZADA").innerHTML=MSG;
	}

	function Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(MSG,color)
	{
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FB_ESPECIFICA").innerHTML=MSG;
	}


	function Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE(MSG,color)
	{
	if(!MSG)
		MSG="&nbsp;";
	if(color=="VERDE")
		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
	else if(color=="ROJO")
		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
	xGetElementById("MSG_FB_OTROS").innerHTML=MSG;
	}

// 	function Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListado(MSG,color)
// 	{
// 	if(!MSG)
// 		MSG="&nbsp;";
// 	if(color=="VERDE")
// 		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
// 	else if(color=="ROJO")
// 		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
// 	xGetElementById("MSG_FB_LISTADO_CENTRALIZADA").innerHTML=MSG;
// 	}

// 	function Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListadoEspecifica(MSG,color)
// 	{
// 	if(!MSG)
// 		MSG="&nbsp;";
// 	if(color=="VERDE")
// 		MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
// 	else if(color=="ROJO")
// 		MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
// 	xGetElementById("MSG_FB_LISTADO_ESPECIFICA").innerHTML=MSG;
// 	}


	function nuevoCentralizada()
	{

//tercera pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==2)
	{
	activarFormularioOtros();
	DarFocoCampo("codigoOtros",1000);
	activarBotonGuardar();
	desactivarBotonModificar();
	desactivarBotonEliminar();
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
	limpiarInputTextBuscarListadoEspecificaOtros();
	}


//segunda pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==1)
	{
	activarFormularioEspecifica();
	DarFocoCampo("codigoEspecifica",1000);
	activarBotonGuardar();
	desactivarBotonModificar();
	desactivarBotonEliminar();
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
	limpiarInputTextBuscarListadoEspecifica();
	xGetElementById("lstOtros").innerHTML="";
	xGetElementById('hidCodEspecifica').value="";
	xGetElementById('guardOtros').value="";
  
  xGetElementById('codigoOtros').value="";
	xGetElementById('denominacionOtros').value="";
	xGetElementById("codigoSubEspecificaInicial").value="";
  IDSeleccionActualListaOtros=-1;
	}

//primera pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==0)
	{
	activarFormularioCentralizada();
	DarFocoCampo("codigoCentralizada",1000);
	xGetElementById('accionEspecifica').reset();
	xGetElementById('accionCentralizada').reset();
	xGetElementById('accionEspecificaOtros').reset();
	activarBotonGuardar();
	desactivarBotonModificar();
	desactivarBotonEliminar();
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
	limpiarInputTextBuscarListado();
	xGetElementById("lstAccionesEspecificas").innerHTML="";
	xGetElementById("lstOtros").innerHTML="";
	desactivarFormularioEspecifica();
	desactivarFormularioOtros();
	//xGetElementById('codigoOtrosInicial').value="";
	xGetElementById('codigoEspecificaInicial').value="";
  xGetElementById("codigoSubEspecificaInicial").value="";
	}


	}


	function limpiarInputTextBuscarListado()
	{
	xGetElementById("LISTADO_BUSCAR_FB_CENTRALIZADA").value="";
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
	//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListado("");
	buscarListado();
	}

	function limpiarInputTextBuscarListadoEspecifica()
	{
	xGetElementById('codigoEspecifica').value="";
	xGetElementById('denominacionEspecifica').value="";
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
	//Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PROListadoEspecifica("");
	IDCentralizada=xGetElementById('hidCodCentralizada').value;
	buscarListadoEspecifica();
	}

	function limpiarInputTextBuscarListadoEspecificaOtros()
	{
	xGetElementById('codigoOtros').value="";
	xGetElementById('denominacionOtros').value="";
	Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
	IDEspecifica=xGetElementById('hidCodEspecifica').value;
	buscarListadoOtros();
	}

	/**
* Activa el boton modificar
*/
	function activarBotonModificar()
	{
	ActivarBoton("CMD_MODIFICAR","IMG_MODIFICAR",'modificar');
	}

	/**
* Desactiva el boton modificar
*/
	function desactivarBotonModificar()
	{
	DesactivarBoton("CMD_MODIFICAR","IMG_MODIFICAR",'modificar');
	}

	/**
* Activa el boton guardar
*/
	function activarBotonGuardar()
	{
	ActivarBoton("CMD_GUARDAR","IMG_GUARDAR",'guardar');
	}

	/**
* Desactiva el boton guardar
*/
	function desactivarBotonGuardar()
	{
	DesactivarBoton("CMD_GUARDAR","IMG_GUARDAR",'guardar');
	}

	/**
* Activa el boton eliminar
*/
	function activarBotonEliminar()
	{
	ActivarBoton("CMD_ELIMINAR","IMG_ELIMINAR",'eliminar');
	}

	/**
* Desactiva el boton eliminar
*/
	function desactivarBotonEliminar()
	{
	DesactivarBoton("CMD_ELIMINAR","IMG_ELIMINAR",'eliminar');
	}

	//var IDSeleccionActualListaEspecifica=-1;
	var buscarListado_CadenaBuscarCentralizada="";

//LISTO
function buscarListado(){
  xGetElementById("accionCentralizada").reset();
  activarFormularioCentralizada();
  xGetElementById("lstAccionesEspecificas").innerHTML="";
  xGetElementById("lstOtros").innerHTML="";
  xGetElementById('codigoEspecifica').value="";
	xGetElementById('denominacionEspecifica').value="";
	xGetElementById("codigoEspecificaInicial").value="";
  xGetElementById('codigoOtros').value="";
	xGetElementById('denominacionOtros').value="";
	xGetElementById("codigoSubEspecificaInicial").value="";
  IDSeleccionActualLista=-1;
  IDSeleccionActualListaEspecifica=-1;
  IDSeleccionActualListaOtros=-1;

  var CadenaBuscarCentralizada=xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FB_CENTRALIZADA").value));

  if(CadenaBuscarCentralizada!="")
    if(buscarListado_CadenaBuscarCentralizada==CadenaBuscarCentralizada)
      return;
  buscarListado_CadenaBuscarCentralizada=CadenaBuscarCentralizada;

  AjaxRequest.post({
    'parameters':{
      'action':"onList_AccionCentralizada",
      'text':CadenaBuscarCentralizada,
      'start':0,
      'limit':'ALL',
      'sort':'[{"property":"tipo","direction":"ASC"},{"property":"codigo_centralizada","direction":"ASC"},{"property":"denominacion_centralizada","direction":"ASC"}]'
    },
    'onSuccess':mostrarListado,
    'url':'../estructura_presupuestaria/',
    'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
  });
}

var IDSeleccionActualLista=-1;
var IDSeleccionActualListaEspecifica=-1;
var IDSeleccionActualListaOtros=-1;

//LISTO
function mostrarListado(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
  resultado=resultado["result"];
	IDSeleccionActualLista=-1;
	var n=resultado.length;
	var tablaPrueba = xGetElementById("lstAccionesCentralizadas");
	var TextoBuscar=quitarCodigoCeros(xTrim(strtoupper(xGetElementById("LISTADO_BUSCAR_FB_CENTRALIZADA").value)));
	xGetElementById("lstAccionesCentralizadas").innerHTML="";
	var Contenido="";
	var FuncionOnclick="";
	var FuncionOnDblclick="";
	var FuncionOnMouseOver="";
	var FuncionOnMouseOut="";
	var CadAux1, CadAux2;

	for(var i=0;i<n; i++){
		FuncionOnclick="seleccionarElementoTablaCentralizada('"
					+resultado[i]['id']+"','"
					+resultado[i]['tipo']+"','"
					+resultado[i]['codigo_centralizada']+"','"
					+resultado[i]['denominacion_centralizada']+"')";
 		FuncionOnDblclick="Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.setSelectedIndex(0);";
 		FuncionOnMouseOver="pintarFila(\"FB"+resultado[i]['id']+"\")";
 		FuncionOnMouseOut="despintarFila(\"FB"+resultado[i]['id']+"\")";


		Contenido+="<TR id='FB"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";

		CadAux1=str_replace(strtoupper(resultado[i]['tipo']+resultado[i]['codigo_centralizada']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
		CadAux2=str_replace(strtoupper(resultado[i]['denominacion_centralizada']),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);

		Contenido+="<TD width='30%' class='FilaEstilo'>"+CadAux1+"</TD>";
		Contenido+="<TD width='70%' class='FilaEstilo'>"+CadAux2+"</TD>";

		Contenido+="</TR>";
		}

	xGetElementById("lstAccionesCentralizadas").innerHTML=Contenido;
}

//LISTO
function seleccionarElementoTablaCentralizada(IDCentralizada, tipo, codigoCentralizada, denominacionCentralizada){
  if(IDSeleccionActualLista!=-1)
    xGetElementById("FB"+IDSeleccionActualLista).bgColor=colorFondoTabla;
  colorBase=colorSeleccionTabla;
  xGetElementById("FB"+IDCentralizada).bgColor=colorBase;
  IDSeleccionActualLista=IDCentralizada;

  xGetElementById("hidCodCentralizada").value=IDCentralizada;
  xGetElementById("escestPres").value=tipo;
  xGetElementById("codigoCentralizada").value=codigoCentralizada;
  xGetElementById("denominacionCentralizada").value=denominacionCentralizada;

  xGetElementById("guardEspecifica").value=1;
  xGetElementById("guardCentralizada").value=0;

  desactivarFormularioCentralizada();
  activarBotonModificar();
  activarBotonEliminar();
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");

	xGetElementById('codigoEspecifica').value="";
	xGetElementById('denominacionEspecifica').value="";

	//codCentralizada=codigoCentralizada.substring(3,codigoCentralizada.length);
	xGetElementById("codigoEspecificaInicial").value=tipo+codigoCentralizada;

	buscarListadoEspecifica();
}

//LISTO
function buscarListadoEspecifica(){
	AjaxRequest.post({
    'parameters':{
      'action':"onList_AccionEspecifica",
      'id_accion_centralizada':IDSeleccionActualLista,
      'text':'',
      'start':0,
      'limit':'ALL',
      'sort':'[{"property":"codigo_especifica","direction":"ASC"}]'
    },
    'onSuccess':mostrarListadoEspecifica,
    'url':'../estructura_presupuestaria/',
    'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
  });
}

//LISTO
function mostrarListadoEspecifica(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
  resultado=resultado["result"];
  IDSeleccionActualListaEspecifica=-1;
	var n=resultado.length;

	var tablaPrueba = xGetElementById("lstAccionesEspecificas");

	xGetElementById("lstAccionesEspecificas").innerHTML="";

	var ContenidoE="";
	var FuncionOnclickE="";
	var FuncionOnDblclickE="";
	var FuncionOnMouseOverE="";
	var FuncionOnMouseOutE="";
	var CadAux1E, CadAux2E;

	for(var i=0;i<n; i++){
		FuncionOnclickE="seleccionarElementoTablaEspecifica('"
					+resultado[i]['id']+"','"
					+resultado[i]['codigo_especifica']+"','"
					+resultado[i]['denominacion_especifica']+"')";
 		FuncionOnDblclickE="Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.setSelectedIndex(1);";
 		FuncionOnMouseOverE="pintarFila(\"FBE"+resultado[i]['id']+"\")";
 		FuncionOnMouseOutE="despintarFila(\"FBE"+resultado[i]['id']+"\")";


		ContenidoE+="<TR id='FBE"+resultado[i]['id']+"' onclick=\""+FuncionOnclickE+"\" ondblclick='"+FuncionOnDblclickE+"' onmouseover='"+FuncionOnMouseOverE+"' onmouseout='"+FuncionOnMouseOutE+"'>";

		CadAux1E=str_replace(strtoupper(resultado[i]['codigo_especifica']));
		CadAux2E=str_replace(strtoupper(resultado[i]['denominacion_especifica']));

		ContenidoE+="<TD width='30%' class='FilaEstilo'>"+CadAux1E+"</TD>";
		ContenidoE+="<TD width='70%' class='FilaEstilo'>"+CadAux2E+"</TD>";
		ContenidoE+="</TR>";
		}
	xGetElementById("lstAccionesEspecificas").innerHTML=ContenidoE;
}


//LISTO
function seleccionarElementoTablaEspecifica(IDEspecifica, codigoEspecifica, denominacionEspecifica){
  if(IDSeleccionActualListaEspecifica!=-1)
    xGetElementById("FBE"+IDSeleccionActualListaEspecifica).bgColor=colorFondoTabla;
  colorBase=colorSeleccionTabla;
  xGetElementById("FBE"+IDEspecifica).bgColor=colorBase;
  IDSeleccionActualListaEspecifica=IDEspecifica;

  xGetElementById("hidCodEspecifica").value=IDEspecifica;
  xGetElementById("codigoEspecifica").value=codigoEspecifica;
  xGetElementById("denominacionEspecifica").value=denominacionEspecifica;
  xGetElementById("guardOtros").value=2;
  desactivarFormularioEspecifica();
  activarBotonModificar();
  activarBotonEliminar();
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");

	xGetElementById('codigoOtros').value="";
	xGetElementById('denominacionOtros').value="";

	//codEspecifica=codigoEspecifica.substring(0,6);
	xGetElementById("codigoSubEspecificaInicial").value=xGetElementById("codigoEspecificaInicial").value+"-"+codigoEspecifica;


	buscarListadoOtros();
}

//LISTO
function buscarListadoOtros(){
  AjaxRequest.post({
    'parameters':{
      'action':"onList_AccionSubEspecifica",
      'id_accion_especifica':IDSeleccionActualListaEspecifica,
      'text':'',
      'start':0,
      'limit':'ALL',
      'sort':'[{"property":"codigo_subespecifica","direction":"ASC"}]'
    },
    'onSuccess':mostrarListadoOtros,
    'url':'../estructura_presupuestaria/',
    'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
  });
}

//LISTO
function mostrarListadoOtros(req){
	var respuesta = req.responseText;
	var resultado = eval("(" + respuesta + ")");
  resultado=resultado["result"];
	IDSeleccionActualListaOtros=-1;
	var n=resultado.length;

	var tablaPrueba = xGetElementById("lstOtros");

	xGetElementById("lstOtros").innerHTML="";

	var ContenidoO="";
	var FuncionOnclickO="";
	var FuncionOnDblclickO="";
	var FuncionOnMouseOverO="";
	var FuncionOnMouseOutO="";
	var CadAux1O, CadAux2O;

	for(var i=0;i<n; i++){
		FuncionOnclickO="seleccionarElementoTablaOtros('"
					+resultado[i]['id']+"','"
					+resultado[i]['codigo_subespecifica']+"','"
					+resultado[i]['denominacion_subespecifica']+"')";
 		FuncionOnDblclickO="Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.setSelectedIndex(2);";
 		FuncionOnMouseOverO="pintarFila(\"FBEO"+resultado[i]['id']+"\")";
 		FuncionOnMouseOutO="despintarFila(\"FBEO"+resultado[i]['id']+"\")";


		ContenidoO+="<TR id='FBEO"+resultado[i]['id']+"' onclick=\""+FuncionOnclickO+"\" ondblclick='"+FuncionOnDblclickO+"' onmouseover='"+FuncionOnMouseOverO+"' onmouseout='"+FuncionOnMouseOutO+"'>";

		CadAux1O=str_replace(strtoupper(resultado[i]['codigo_subespecifica']));
		CadAux2O=str_replace(strtoupper(resultado[i]['denominacion_subespecifica']));

		ContenidoO+="<TD width='30%' class='FilaEstilo'>"+CadAux1O+"</TD>";
		ContenidoO+="<TD width='70%' class='FilaEstilo'>"+CadAux2O+"</TD>";

		ContenidoO+="</TR>";
		}

	xGetElementById("lstOtros").innerHTML=ContenidoO;
}

	function seleccionarElementoTablaOtros(IDOtros, codigoOtros, denominacionOtros)
	{

//alert(codigoOtros);

		if(IDSeleccionActualListaOtros!=-1)
			xGetElementById("FBEO"+IDSeleccionActualListaOtros).bgColor=colorFondoTabla;
        		colorBase=colorSeleccionTabla;
        		xGetElementById("FBEO"+IDOtros).bgColor=colorBase;
			IDSeleccionActualListaOtros=IDOtros;


	//	xGetElementById("codigoOtrosInicial").value=codigoOtros.substring(0,6);
		xGetElementById("codigoOtros").value=codigoOtros;
 		xGetElementById("denominacionOtros").value=denominacionOtros;
// 		xGetElementById("guardEspecifica").value=1;
// 		xGetElementById("guardOtros").value=2;



		desactivarFormularioOtros();
		activarBotonModificar();
 		activarBotonEliminar();
// 		desactivarBotonGuardar();
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
		Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");

	}


	function activarFormularioCentralizada()
	{
	xGetElementById("codigoCentralizada").readOnly=false;
	xGetElementById("denominacionCentralizada").readOnly=false;
	//xGetElementById("unidMedida").readOnly=false;
	//xGetElementById("areaEstrat").readOnly=false;
	//xGetElementById("responsableProy").readOnly=false;
	//xGetElementById("masculino").readOnly=false;
	//xGetElementById("femenino").readOnly=false;
	xGetElementById("escestPres").readOnly=false;
	xGetElementById("escestPres").disabled=false;

	xGetElementById("codigoCentralizada").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("denominacionCentralizada").setAttribute('class','TextoCampoInputObligatorios');
	//xGetElementById("unidMedida").setAttribute('class','TextoCampoInputObligatorios');
	//xGetElementById("areaEstrat").setAttribute('class','TextoCampoInputObligatorios');
	//xGetElementById("responsableProy").setAttribute('class','TextoCampoInputObligatorios');
	//xGetElementById("masculino").setAttribute('class','TextoCampoInputObligatorios');
	//xGetElementById("femenino").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("escestPres").setAttribute('class','TextoCampoInputObligatorios');
	}


	function desactivarFormularioCentralizada()
	{
	xGetElementById("codigoCentralizada").readOnly=true;
	xGetElementById("denominacionCentralizada").readOnly=true;
	//xGetElementById("unidMedida").readOnly=true;
	//xGetElementById("areaEstrat").readOnly=true;
	//xGetElementById("responsableProy").readOnly=true;
	//xGetElementById("masculino").readOnly=true;
	//xGetElementById("femenino").readOnly=true;
	xGetElementById("escestPres").readOnly=true;
	xGetElementById("escestPres").disabled=true;

	xGetElementById("codigoCentralizada").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("denominacionCentralizada").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("unidMedida").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("areaEstrat").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("responsableProy").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("masculino").setAttribute('class','TextoCampoInputDesactivado');
	//xGetElementById("femenino").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("escestPres").setAttribute('class','TextoCampoInputDesactivado');
	}


	function activarFormularioEspecifica()
	{
	xGetElementById("codigoEspecifica").readOnly=false;
	xGetElementById("denominacionEspecifica").readOnly=false;


	xGetElementById("codigoEspecifica").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("denominacionEspecifica").setAttribute('class','TextoCampoInputObligatorios');

	}


	function desactivarFormularioEspecifica()
	{
	xGetElementById("codigoEspecifica").readOnly=true;
	xGetElementById("denominacionEspecifica").readOnly=true;

	xGetElementById("codigoEspecifica").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("denominacionEspecifica").setAttribute('class','TextoCampoInputDesactivado');
	}


	function activarFormularioOtros()
	{
	xGetElementById("codigoOtros").readOnly=false;
	xGetElementById("denominacionOtros").readOnly=false;

	xGetElementById("codigoOtros").setAttribute('class','TextoCampoInputObligatorios');
	xGetElementById("denominacionOtros").setAttribute('class','TextoCampoInputObligatorios');
	}


	function desactivarFormularioOtros()
	{
	xGetElementById("codigoOtros").readOnly=true;
	xGetElementById("denominacionOtros").readOnly=true;

	xGetElementById("codigoOtros").setAttribute('class','TextoCampoInputDesactivado');
	xGetElementById("denominacionOtros").setAttribute('class','TextoCampoInputDesactivado');
	}

	function modificarCentralizada()
	{
	activarBotonGuardar();
	desactivarBotonModificar();
	desactivarBotonEliminar();

//tercera pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==2)
	{
	activarFormularioOtros();
	xGetElementById('guardCentralizada').value=0;
	}


//segunda pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==1)
	{
	activarFormularioEspecifica();
	xGetElementById('guardCentralizada').value=0;
	}


//primera pestaña
	if(Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==0)
	{
	xGetElementById('guardCentralizada').value=1;
	activarFormularioCentralizada();
	}



	}

/**
* Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
*/
function eliminarCentralizada(){
	if(IDSeleccionActualLista==-1)
		return;

  //tercera pestaña
	if (Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==2){
	  if(validarCamposTipoOperacionesEspecificaOtros()){
	    if(!confirm("¿Esta seguro que quiere eliminarlo?"))
	      return;
      AjaxRequest.post({
        'parameters':{
          'action':"onDelete_AccionSubEspecifica",
		  		'id_accion_subespecifica':IDSeleccionActualListaOtros
        },
        'onSuccess':mostrarMensajeEliminarEspecificaOtros,
        'url':'../estructura_presupuestaria/',
        'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
      });
	  }
	  else{
      Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("Error. No se pudo eliminar los datos. Seleccione el registro a eliminar.","ROJO");
    }
	}

  //segunda pestaña
	if(Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==1){
    if(validarCamposTipoOperacionesEspecifica()){
	    if(!confirm("¿Esta seguro que quiere eliminarlo?"))
	      return;
      AjaxRequest.post({
        'parameters':{
          'action':"onDelete_AccionEspecifica",
		  		'id_accion_especifica':IDSeleccionActualListaEspecifica
        },
        'onSuccess':mostrarMensajeEliminarEspecifica,
        'url':'../estructura_presupuestaria/',
        'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
      });
	  }
	  else{
      Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("Error. No se pudo eliminar los datos. Seleccione el registro a eliminar.","ROJO");
      Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
    }
  }
  

  //primera pestaña LISTO
	if(Form_ESTRUCTURA_PRESUPUESTARIA__TabPane.getSelectedIndex()==0){
    if(validarCamposTipoOperacionesCentralizada()){
	    if(!confirm("¿Esta seguro que quiere eliminarlo?"))
	      return;
	    AjaxRequest.post({
        'parameters':{
          'action':"onDelete_AccionCentralizada",
		  		'id_accion_centralizada':IDSeleccionActualLista
        },
        'onSuccess':mostrarMensajeEliminar,
        'url':'../estructura_presupuestaria/',
        'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
      });
    }
	  else{
	    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("Error. No se pudo eliminar los datos. Seleccione el registro a eliminar.","ROJO");
      Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
    }
	}
}

//LISTO
function mostrarMensajeEliminar(req){
  var respuesta = eval("(" +req.responseText + ")");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
  if(respuesta.success){
    limpiarInputTextBuscarListado();
    xGetElementById('accionEspecifica').reset();
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(respuesta.message,"VERDE");
  }
  else{			
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO(respuesta.message,"ROJO");
  }
}

//LISTO
function mostrarMensajeEliminarEspecifica(req){
  var respuesta = eval("(" +req.responseText + ")");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
  if(respuesta.success){
    limpiarInputTextBuscarListadoEspecifica();
    xGetElementById('accionEspecificaOtros').reset();
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(respuesta.message,"VERDE");
  }
  else{
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE(respuesta.message,"ROJO");
  }
}

function mostrarMensajeEliminarEspecificaOtros(req){
  var respuesta = eval("(" +req.responseText + ")");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAC_PRO("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeAE("");
  Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE("");
  if(respuesta.success){
    limpiarInputTextBuscarListadoEspecificaOtros();
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE(respuesta.message,"VERDE");
  }
  else{
    Form_ESTRUCTURA_PRESUPUESTARIA__MensajeOAE(respuesta.message,"ROJO");
  }
}