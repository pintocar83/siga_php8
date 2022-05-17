
var reportes_mayor={
  setMessage: function(MSG,color){
    if(!MSG)
      MSG="&nbsp;";
    if(color=="VERDE")
      MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
    else if(color=="ROJO")
      MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
    xGetElementById("MSG_RM").innerHTML=MSG;    
  },
  
  onLoad: function(){
    var me=this;
    AjaxRequest.post({
					'parameters':{
									'action':"onList_AP",	
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"estructura_presupuestaria","direction":"ASC"}]'									
									},
					'onSuccess':function(req){
            var respuesta = req.responseText;
            var resultado = eval("(" + respuesta + ")");
            resultado=resultado["result"];
            var n=resultado.length;            
            var Contenido="<option value='*'>TODOS</option>";
            for(var i=0;i<n;i++)
              Contenido+="<option value='"+resultado[i]["id_accion_subespecifica"]+"'>"+resultado[i]["estructura_presupuestaria"]+"</option>";
            xGetElementById("SELECT_EP_RM").innerHTML=Contenido;
            me.onLoad_Cuentas();            
          },
					'url':'../estructura_presupuestaria/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
  },  
  
  onLoad_Cuentas: function(){    
    var me=this;
    AjaxRequest.post({
					'parameters':{
									'action':"onList_AP",	
									'text':'',
									'start': '0',
									'limit' : 'ALL',
									'sort':'[{"property":"id_cuenta_presupuestaria","direction":"ASC"}]'									
									},
					'onSuccess':function(req){
            var respuesta = req.responseText;
            var resultado = eval("(" + respuesta + ")");
            resultado=resultado["result"];
            var n=resultado.length;
            var Contenido="<option value='*'>TODAS</option>";
							for(var i=0;i<n;i++)
								Contenido+="<option value='"+resultado[i]["id_cuenta_presupuestaria"]+"'>"+resultado[i]["cuenta_presupuestaria"]+"   "+resultado[i]["denominacion"]+"</option>";
							xGetElementById("SELECT_CUENTA_RM").innerHTML=Contenido;
          },
					'url':'../cuenta_presupuestaria/',
					'onError':function(req){alert('Error!\nStatusText='+req.statusText+'\nContents='+req.responseText);}
					});
  },
  
  onDisplay: function(){
    var me=this;
    var lista_EP=ContenidoSelectValueSelected("SELECT_EP_RM");
    var lista_Cuentas=ContenidoSelectValueSelected("SELECT_CUENTA_RM");
    var fecha_inicio=xTrim(xGetElementById("FECHA_INICIO_RM").value);
    var fecha_culminacion=xTrim(xGetElementById("FECHA_CULMINACION_RM").value);
    me.setMessage("");
    if(!fecha_inicio){
      me.setMessage("Por favor introduzca la fecha de inicio.","ROJO");
      return;
      }
    if(!EsFechaValida(fecha_inicio)){
      me.setMessage("La fecha de inicio es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
      return;
      }
  
    if(!fecha_culminacion){
      me.setMessage("Por favor introduzca la fecha de finalización.","ROJO");
      return;
      }
    if(!EsFechaValida(fecha_culminacion)){
      me.setMessage("La fecha de finalización es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
      return;
      }
  
    window.open("../../report/mayor.php?ep="+lista_EP+"&cuenta="+lista_Cuentas+"&fecha_inicio="+fecha_inicio+"&fecha_culminacion="+fecha_culminacion);
  }

};