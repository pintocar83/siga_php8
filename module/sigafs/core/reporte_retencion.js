
var reportes_retencion={
  onChange_Persona: function(checked){
    xGetElementById("DIV_PB").style.visibility=(checked==true?"":"hidden");
  },

  onPersona: function(){
    sigafs.onPersona({id:'ID_PERSONA_RR', identificacion:'IDENTIFICACION_PERSONA_RR', denominacion:'DENOMINACION_PERSONA_RR'});
  },

  onClear_Persona: function(){
    xGetElementById("ID_PERSONA_RR").value=xGetElementById("IDENTIFICACION_PERSONA_RR").value=xGetElementById("DENOMINACION_PERSONA_RR").value="";
  },

  onDisplay: function(){
    var id_persona="";
    if(xGetElementById("CHECK_PERSONA").checked==true)
      id_persona=xGetElementById("ID_PERSONA_RR").value;
    var tipo=xGetElementById("TIPO_RR").value;
    var fecha_inicio=xGetElementById("FECHA_INICIO_RR").value;
    var fecha_culminacion=xGetElementById("FECHA_FIN_RR").value;
    if(xGetElementById("OPCION_RR_A").checked)
      window.open("../../report/retencion_emitida.php?id_persona="+id_persona+"&tipo="+tipo+"&fecha_inicio="+fecha_inicio+"&fecha_culminacion="+fecha_culminacion);
    if(xGetElementById("OPCION_RR_D").checked)
      window.open("../../report/retencion_emitida_xls.php?id_persona="+id_persona+"&tipo="+tipo+"&fecha_inicio="+fecha_inicio+"&fecha_culminacion="+fecha_culminacion);

    if(xGetElementById("OPCION_RR_B").checked)
      window.open("../../report/retencion_efectuada.php?id_persona="+id_persona+"&tipo="+tipo+"&fecha_inicio="+fecha_inicio+"&fecha_culminacion="+fecha_culminacion);

    if(xGetElementById("OPCION_RR_C").checked){
      var _archivo="";
      if(tipo==1) _archivo="comprobante_retencion_iva.php";
      else if(tipo==2) _archivo="comprobante_retencion_islr.php";
      else if(tipo==3) _archivo="comprobante_retencion_1x1000.php";
      else return;
      window.open("../../report/"+_archivo+"?id_persona="+id_persona+"&fecha_inicio="+fecha_inicio+"&fecha_culminacion="+fecha_culminacion);
    }
  }

};