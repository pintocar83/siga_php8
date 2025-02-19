
var reporte_retencion={
  onChange_Persona: function(checked){
    xGetElementById("DIV_PB").style.visibility=(checked==true?"":"hidden");
  },

  onPersona: function(){
    //sigafs.onPersona({id:'ID_PERSONA_RR', identificacion:'IDENTIFICACION_PERSONA_RR', denominacion:'DENOMINACION_PERSONA_RR'});
    const me=this;
    if(me.persona_tipo=='J')
      Form_LISTA_PROVEEDOR__Abrir('ID_PERSONA_RR','IDENTIFICACION_PERSONA_RR','DENOMINACION_PERSONA_RR');
    else
      Form_LISTA_BENEFICIARIO__Abrir('ID_PERSONA_RR','IDENTIFICACION_PERSONA_RR','DENOMINACION_PERSONA_RR');
  },

  persona_tipo: '',
  onChange_TipoPersona: function(){
    const me=this;
    me.onClear_Persona();

    if(xGetElementById("RADIO_TIPO_PERSONA_TODOS").checked){
      xGetElementById("DIV_PB").style.visibility="hidden";
      me.persona_tipo='';
    }
    else{
      xGetElementById("DIV_PB").style.visibility="";
      if(xGetElementById("RADIO_TIPO_PERSONA_PROVEEDOR").checked){
        me.persona_tipo='J';
      }
      else{
        me.persona_tipo='N';
      }
    }
  },

  onClear_Persona: function(){
    xGetElementById("ID_PERSONA_RR").value=xGetElementById("IDENTIFICACION_PERSONA_RR").value=xGetElementById("DENOMINACION_PERSONA_RR").value="";
  },

  onDisplay: function(){
    var id_persona="";
    if(xGetElementById("RADIO_TIPO_PERSONA_TODOS").checked==false)
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