siga.require.css("css/sigafs.css");
siga.require.css("library/tabpane/tabpane.css");
siga.require.js("library/tabpane/tabpane.js");

siga.define('pago', {
    extend: 'siga.windowBase',
    title: 'Banco - Pago por cheque o transferencia',
    width: 850,
    height: 550,
    
    initComponent: function(){
        var me = this;        
        var response = Ext.Ajax.request({async: false,url: "module/pago/form.php"});
        me.itemsToolbar=[
          me.btnNew(),
          me.btnSave(),
          me.btnEdit(),
          me.btnDelete(),
          me.btnDisplay(),
          {
            xtype: "tbspacer",
            flex:1
          },
          {
            xtype: 'button',
            id: me._('btnCheque'),
            height: 45,
            width: 70,
            text: 'Cheque',
            cls: 'siga-btn-base',
            focusCls: '',
            iconCls: 'siga-btn-base-icon icon-cheque',
            iconAlign: 'top',
            tooltip: 'Pago por cheque',
            toggleGroup: "tipo_pago",            
            listeners: {
              click: function(){
                me.getCmp('btnCheque').toggle(true);
                //me.$$('PAGO_ADJUNTO').set({style: {'display': "none"}});
                //me.getCmp('btnTransferencia').toggle(false);
                me.onSearch();
                //me.onSearch();
              }
            }
          },
          {
            xtype: 'button',
            id: me._('btnTransferencia'),
            height: 45,
            width: 70,
            text: 'Transferencia',
            cls: 'siga-btn-base',
            focusCls: '',
            iconCls: 'siga-btn-base-icon icon-transferencia',
            iconAlign: 'top',
            tooltip: 'Pago por Transferencia',
            toggleGroup: "tipo_pago",
            listeners: {
                click: function(){
                  me.getCmp('btnTransferencia').toggle(true);
                  me.onSearch();
                  //me.$$('PAGO_ADJUNTO').set({style: {'display': ""}});
                  //me.onSearch();
                }
            }
          },
        ];
        
        me.items=[
          {
            xtype: "component",
            html: response.responseText
          }          
        ];
        
        me.callParent(arguments);
    },
    
    $: function(id){
      var me = this;  
      return me.getEl().getById(id,true);
    },
    
    $$: function(id){
      var me = this;
      return Ext.get(me.$(id));
    },
    
    init: function(){
      var me = this;
      me.getCmp('btnCheque').toggle(true);
      
      me.IDSeleccionActualLista=-1;
      me.IDSeleccionActualArreglo=-1;
      me.Arreglo=[];
      me.TamArreglo=0;
      me.SW_PERSONA="";
      me.IDComprobante="";
      me.ArregloDetalles=[];
      
      me.ArregloTodos=[];
      me.ArregloTodosK=0;
      me.ArregloDetallesFiltrado=[];
      me.ArregloDetallesFiltradoK=0;
      me.NombreBanco="";
      me.BuscarListado_CadenaBuscar="";
      
      me.TipoModificar=-1;
      
      me.TabPane = new WebFXTabPane(me.$("TABPANE"), true);
      
      
      me.$$("BOTON_PROVEEDOR").on("click", function(){me.onProveedor();});
      me.$$("BOTON_BENEFICIARIO").on("click", function(){me.onBeneficiario();});
      me.$$("BOTON_PROVEEDOR_2").on("click", function(){me.onProveedor();});
      me.$$("BOTON_BENEFICIARIO_2").on("click", function(){me.onBeneficiario();});
      me.$$("BOTON_CONTABLIZAR").on("click", function(){me.onContabilizar();});
      me.$$("BOTON_REVERSAR").on("click", function(){me.onReversar();});
      me.$$("BOTON_ANULAR").on("click", function(){me.onAnular();});
      
      me.$$("BOTON_SELECCIONAR_PERSONA").on("click", function(){
        siga.onPersona({
          tipo: me.SW_PERSONA,
          onList: 'onList_OP_pendiente',
          onAccept: function(result){
            me.$("PERSONA_ID").value=result[0]["id"];
            me.$("PERSONA_IDENTIFICACION").value=result[0]["identificacion"];
            me.$("PERSONA_DENOMINACION").value=result[0]["denominacion"];
            me.CargarSolicitudes();
          }
        });
      });
      
      me.$$("BOTON_SELECCIONAR_CUENTA_BANCARIA").on("click", function(){
        siga.onCuentaBancaria({onAccept: function(result){
          me.$("ID_CTA").value=result[0]["id"];
          me.$("NCTA").value=result[0]["numero_cuenta"];
          me.$("DESCRIPCION_NCTA").value=result[0]["denominacion"];
          
          me.$("CTA_CODIGO_CONTABLE").value=result[0]["id_cuenta_contable"];
          me.$("CUENTA_CONTABLE").value=result[0]["cuenta_contable"];
          me.$("CTA_DENOMINACION_CONTABLE").value=result[0]["denominacion_contable"];
          me.CambioSelectDetalles();
          return true;
        }});
      });
      
      me.$$("BOTON_CALENDARIO").on("click", function(){siga.onCalendar(me.$("FECHA"));});
      me.$$("SELECT_RETENCION").on("change", function(){me.CalcularMonto();});
      me.$$("SELECT_DETALLES").on("change", function(){me.CambioSelectDetalles();});
      
      me.$$("VIEW_ALL").on("click", function(){me.onViewAll();});
      me.$$("CHECK_ALL").on("change", function(){me.onCheckAll();});
      
      me.$$("LISTADO_TXT_BUSCAR").on("keyup", function(e,t,o){me.PresionarEnter(e);});
      me.$$("LISTADO_TXT_LIMPIAR").on("click", function(){me.LimpiarInputTextBuscarListado();});
      me.$$("LISTADO_MES_FILTRAR").on("change", function(){me.BuscarListado_CadenaBuscar=''; me.onSearch();})
      

      me.$$("BOTON_ARCHIVO_BORRAR").on("click", function(){me.$("ARCHIVO_ADJUNTO").value="";});
      me.$$("BOTON_ARCHIVO_ADJUNTAR").on("change", function(){me.$("ARCHIVO_ADJUNTO").value=me.$("BOTON_ARCHIVO_ADJUNTAR").value;});
      me.$$("BOTON_ARCHIVO_MOSTRAR").on("click", function(){if(me.$("ARCHIVO_ADJUNTO").value) window.open("module/comprobante/?action=onGet_Archivo&archivo="+me.IDSeleccionActualLista+"/"+me.$("ARCHIVO_ADJUNTO").value)});
      
      
      me.$$("SIGAFS").set({style:{display:"block"}});
      
      me.onNew();
    },
    
    /**
    * Nueva definicion
    */
    onNew: function(){
      var me = this;
      if(me.SW_PERSONA=="N")
        me.onBeneficiario();
      else
        me.onProveedor();
      me.TabPane.setSelectedIndex(0);	
    },
    
    /**
    * Activa el boton modificar
    */
    ActivarBotonModificar: function(){
      var me=this;
      me.getCmp("btnEdit").enable();
    },
    
    /**
    * Desactiva el boton modificar
    */
    DesactivarBotonModificar: function(){
      var me=this;
      me.getCmp("btnEdit").disable();
    },
    
    /**
    * Activa el boton guardar
    */
    ActivarBotonGuardar: function(){
      var me=this;
      me.getCmp("btnSave").enable();
    },
    
    /**
    * Desactiva el boton guardar
    */
    DesactivarBotonGuardar: function(){
      var me=this;
      me.getCmp("btnSave").disable();
    },
    
    /**
    * Activa el boton guardar
    */
    ActivarBotonEliminar: function(){
      var me=this;
      me.getCmp("btnDelete").enable();
    },
    
    /**
    * Desactiva el boton guardar
    */
    DesactivarBotonEliminar: function(){
      var me=this;
      me.getCmp("btnDelete").disable();
    },
    
    /**
    * Activa el boton imprimir
    */
    ActivarBotonImprimir: function(){
      var me=this;
      me.getCmp("btnDisplay").enable();
    },
    
    /**
    * Desactiva el boton imprimir
    */
    DesactivarBotonImprimir: function(){
      var me=this;
      me.getCmp("btnDisplay").disable();
    },
    
    /**
    * Muestra los mensajes en la parte superior del formulario
    * @param {string} MSG Mensaje a mostrar
    * @param {string} color del mensaje
    */
    Mensaje: function(MSG,color){
      var me = this;
      if(!MSG)
        MSG="&nbsp;";
      if(color=="VERDE")
        MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
      else if(color=="ROJO")
        MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
      me.$("MSG").innerHTML=MSG;
    },
    
    /**
    * Muestra los mensajes en la parte superior del listado
    * @param {string} MSG Mensaje a mostrar
    * @param {string} color del mensaje
    */
    MensajeListado: function(MSG,color){
      var me = this;
      if(!MSG)
        MSG="&nbsp;";
      if(color=="VERDE")
        MSG="<DIV style='color:#006600'>"+MSG+"</DIV>";
      else if(color=="ROJO")
        MSG="<DIV style='color:#FF0000'>"+MSG+"</DIV>";
      me.$("MSG_LISTADO").innerHTML=MSG;
    },
    
    /**
    * Activa todos los campos del formulario entrada de datos
    * @param {integer} sw Permite activar parte del formulario, p.j. dado el caso de modificar un cheque no modificar a quien va dirigido.
    */
    ActivarFormulario: function(sw){
      var me = this;
      var v=true;
      if(!sw) {sw=1; v=false};
      
      me.$("BOTON_PROVEEDOR").disabled=v;      
      me.$("BOTON_BENEFICIARIO").disabled=v;
      me.$("BOTON_SELECCIONAR_PERSONA").disabled=v;
      me.$("BOTON_SELECCIONAR_CUENTA_BANCARIA").disabled=(sw==1?false:true);
      me.$("BOTON_CALENDARIO").disabled=false;
      me.$("BOTON_ARCHIVO_BORRAR").disabled=false;  
      me.$("BOTON_ARCHIVO_ADJUNTAR_MASCARA").disabled=false;
      me.$("SELECT_RETENCION").disabled=false;
      
      me.$("FECHA").readOnly=false;      
      me.$("N_CHEQUE").readOnly=false;
      me.$("CONCEPTO").readOnly=false;      
      me.$("CHECK_ALL").disabled=false;
      
      me.$("FECHA").setAttribute('class','TextoCampoInputObligatorios');
      me.$("SELECT_RETENCION").setAttribute('class','TextoCampoInput');
      me.$("N_CHEQUE").setAttribute('class','TextoCampoInputObligatorios');
      me.$("CONCEPTO").setAttribute('class','TextoCampoInputObligatorios');
    },
    
    /**
    * Desactiva todos los campos del formulario entrada de datos
    */
    DesactivarFormulario: function(){
      var me = this;
      me.$("BOTON_PROVEEDOR").disabled=true;      
      me.$("BOTON_BENEFICIARIO").disabled=true;
      me.$("BOTON_SELECCIONAR_PERSONA").disabled=true;
      me.$("BOTON_SELECCIONAR_CUENTA_BANCARIA").disabled=true;
      me.$("BOTON_CALENDARIO").disabled=true;
      me.$("BOTON_ARCHIVO_BORRAR").disabled=true;   
      me.$("BOTON_ARCHIVO_ADJUNTAR_MASCARA").disabled=true;
      me.$("SELECT_RETENCION").disabled=true;
      
      me.$("FECHA").readOnly=true;      
      me.$("N_CHEQUE").readOnly=true;
      me.$("CONCEPTO").readOnly=true;      
      me.$("CHECK_ALL").disabled=true;
      
      me.$("FECHA").setAttribute('class','TextoCampoInputDesactivado');
      me.$("SELECT_RETENCION").setAttribute('class','TextoCampoInputDesactivado');
      me.$("N_CHEQUE").setAttribute('class','TextoCampoInputDesactivado');
      me.$("CONCEPTO").setAttribute('class','TextoCampoInputDesactivado');
    },
    
    /**
    * Permite trabajar el cheque en base a los beneficiarios
    */
    onBeneficiario: function(){
      var me = this;
      me.$("TABLA_LISTA_SOLICITUDES").innerHTML=" ";
      me.$("LISTADO_TD_PERSONA").innerHTML="BENEFICIARIO";
      me.$("PERSONA_TIPO").innerHTML="Beneficiario";
      me.$("PERSONA_ID").value="";
      me.$("PERSONA_IDENTIFICACION").value="";
      me.$("PERSONA_DENOMINACION").value="";
      me.$("MONTO").value="0,00";
      me.TamArreglo=0;
      if(me.SW_PERSONA=="J"){
        me.SW_PERSONA="N";
        me.CargarSolicitudes();
        me.LimpiarInputTextBuscarListado();
        return;
      }
      else
        me.MostrarListadoSolicitudes();
      me.SW_PERSONA="N";
      me.LimpiarInputTextBuscarListado();
    },
    
    /**
    * Permite trabajar el cheque en base a los proveedores
    */
    onProveedor: function(){
      var me = this;
      me.$("TABLA_LISTA_SOLICITUDES").innerHTML=" ";
      me.$("LISTADO_TD_PERSONA").innerHTML="PROVEEDOR";
      me.$("PERSONA_TIPO").innerHTML="Proveedor";
      me.$("PERSONA_ID").value="";
      me.$("PERSONA_IDENTIFICACION").value="";
      me.$("PERSONA_DENOMINACION").value="";
      me.$("MONTO").value="0,00";
      me.TamArreglo=0;
      if(me.SW_PERSONA=="N"){
        me.SW_PERSONA="J";
        me.CargarSolicitudes();
        me.LimpiarInputTextBuscarListado();
        return;
      }
      else
        me.MostrarListadoSolicitudes();
      me.SW_PERSONA="J";
      me.LimpiarInputTextBuscarListado();
    },
    
    /**
    * Carga las solicitudes pendientes/programadas a un proveedor/beneficiario escogido
    */
    CargarSolicitudes: function(){
      var me = this;
      if(me.$("PERSONA_ID").value=="")
        return;
    
      //me.$("TABLA_LISTA_SOLICITUDES").innerHTML=IconoCargandoTabla;    
      me.$("TABLA_LISTA_SOLICITUDES").innerHTML=""; //Mostrar icono cargando   
      
      var _id_comprobante="";
      if(me.IDSeleccionActualLista>0) 
        _id_comprobante=me.IDSeleccionActualLista;
      
      var _mostrar={
        'id_persona': me.$("PERSONA_ID").value,
        'id': _id_comprobante
      };
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onList_OP_pendiente',
          mostrar: Ext.encode(_mostrar),
          text: '',
          start: '0',
          limit: 'ALL',
          sort: '[{"property":"fecha","direction":"ASC"}]'	
        },
        success:function(request){
          me.PostCargarSolicitudes(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
    
    /**
    * Carga los datos de la solicitudes en el arreglo me.Arreglo.
    * @param {Array} req Datos provenientes de la BD
    */
     PostCargarSolicitudes: function(req){
      var me = this;
      var respuesta = req.responseText;
      var resultado = eval("(" + respuesta + ")");
      resultado=resultado["result"];
      var n=resultado.length;
      me.TamArreglo=n;
      for(var i=0;i<n;i++){
        me.Arreglo[i]=[];
        me.Arreglo[i][0]=false;//check
        me.Arreglo[i][1]=resultado[i]['id'];
        me.Arreglo[i][8]=resultado[i]['correlativo'];
        me.Arreglo[i][2]=resultado[i]['fecha'];
        me.Arreglo[i][3]=formatNumberDec(resultado[i]['monto'],2);
        me.Arreglo[i][4]=strtoupper(resultado[i]['concepto']);
        //me.Arreglo[i][5]=resultado[i]['id_cta_bancaria']+"&%?~"+resultado[i]['numero_cta_bancaria']+"&%?~"+resultado[i]['nombre_tipo_cta_bancaria']+"&%?~"+resultado[i]['nombre_banco']+" ("+resultado[i]['direccion_banco']+")&%?~"+resultado[i]['id_codigo_contable'];
        me.Arreglo[i][5]="";
        if(!resultado[i]['monto_pagado_acumulado'] || resultado[i]['monto_pagado_acumulado']=="null" || resultado[i]['monto_pagado_acumulado']=="")
          me.Arreglo[i][6]="0.00";
        else
          me.Arreglo[i][6]=formatNumberDec(resultado[i]['monto_pagado_acumulado'],2);
        if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")	
          me.Arreglo[i][7]="0.00";
        else{
          me.Arreglo[i][0]=true;
          me.Arreglo[i][7]=formatNumberDec(resultado[i]['monto_pagado'],2);
          
          me.Arreglo[i][6]=me.Arreglo[i][6]*1.0-me.Arreglo[i][7]*1.0;
          me.onGetDetallesOP(me.Arreglo[i][1]);
          }
        }
      me.TipoModificar=1;
      me.MostrarListadoSolicitudes();
    },
      
    /**
    * Carga los datos de la solicitudes en el arreglo me.Arreglo.
    * @param {Array} req Datos provenientes de la BD
    */
    PostCargarSolicitudesModificar: function(req){
      var me = this;
      var respuesta = req.responseText;
      var resultado = eval("(" + respuesta + ")");
      var n=resultado.length;
      me.TamArreglo=n;
      for(var i=0;i<n;i++){
        me.Arreglo[i]=new Array(8);
        me.Arreglo[i][0]=false;//check
        me.Arreglo[i][1]=strtoupper(resultado[i]['id_solicitud_pago']);
        me.Arreglo[i][2]=formatDate(resultado[i]['fecha_programar_pg']);
        me.Arreglo[i][3]=formatNumberDec(resultado[i]['monto_sp'],2);
        me.Arreglo[i][4]=strtoupper(resultado[i]['concepto_sp']);
        me.Arreglo[i][5]=resultado[i]['id_cta_bancaria']+"&%?~"+resultado[i]['numero_cta_bancaria']+"&%?~"+resultado[i]['nombre_tipo_cta_bancaria']+"&%?~"+resultado[i]['nombre_banco']+" ("+resultado[i]['direccion_banco']+")&%?~"+resultado[i]['id_codigo_contable'];
        if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")
          me.Arreglo[i][6]="0.00";
        else
          me.Arreglo[i][6]=formatNumberDec(resultado[i]['monto_pagado']-resultado[i]['monto_pagar'],2);
    
        if(!resultado[i]['monto_pagado'] || resultado[i]['monto_pagado']=="null" || resultado[i]['monto_pagado']=="")
          me.Arreglo[i][7]="0.00"
        else
          me.Arreglo[i][7]=formatNumberDec(resultado[i]['monto_pagar'],2);//monto introducido por teclado
    
        }
      me.MostrarListadoSolicitudes();
    },
    
    /**
    * Muestra las solicitudes en la tabla.
    */
    MostrarListadoSolicitudes: function(){
      var me = this;
      
      me.IDSeleccionActualArreglo=-1;
      var Contenido=" ";
      var FuncionOnclick="";
      var FuncionOnclickMonto="";
      var FuncionOnDblclick="";
      var FuncionOnDblclickMonto="";
      var FuncionOnMouseOver="";
      var FuncionOnMouseOut="";
      var CadAux1, CadAux2, CadAux3, CadAux4, CadAux5, CadAux6, CadAux7;
    
      for(var i=0;i<me.TamArreglo;i++){
        CadAux1=me.Arreglo[i][0];
        CadAux2=me.Arreglo[i][1];
        CadAux3=me.Arreglo[i][2];
        CadAux4=formatNumber(me.Arreglo[i][3]);
        CadAux5=me.Arreglo[i][4];
        CadAux7=formatNumber(me.Arreglo[i][6]);
        CadAux8=formatNumber(me.Arreglo[i][7]);
        CadAux9=me.Arreglo[i][8];
    
        Contenido+="<TR id='FCOP_S"+i+"' v='{\"index\":\""+i+"\"}' style='vertical-align: middle;'>";
        Contenido+="<TD width='1%' class='FilaEstilo' align='right'><img view_op class='BotonesParaCampos' src='image/icon/icon-display_16x16.png' style='border: none; background: none; margin-right: 3px; margin-left: 1px; width: 16px; height: 16px;' title='Visualizar'/></TD>";
        if(me.TipoModificar!=-1){
          if(CadAux1==true)
            Contenido+="<TD width='1%' class='FilaEstilo valign' ><INPUT type='checkbox' id='CHECK_"+i+"' value='"+CadAux2+"' checked></TD>";
          else
            Contenido+="<TD width='1%' class='FilaEstilo valign' ><INPUT type='checkbox' id='CHECK_"+i+"' value='"+CadAux2+"'></TD>";
          }
        else{
          if(CadAux1==true)
            Contenido+="<TD width='1%' class='FilaEstilo valign' ><INPUT type='checkbox' disabled checked></TD>";
          else
            Contenido+="<TD width='1%' class='FilaEstilo valign' ><INPUT type='checkbox' disabled></TD>";
          }
    
        Contenido+="<TD width='10%' class='FilaEstilo' align='left' cell_click>"+CadAux9+"</TD>";
        Contenido+="<TD width='10%' class='FilaEstilo' align='center' cell_click>"+CadAux3+"</TD>";
        Contenido+="<TD class='FilaEstiloContinua' style='font-size: 9px;' cell_click>"+CadAux5+"</TD>";
        Contenido+="<TD width='10%' class='FilaEstilo' align='right' cell_click>"+CadAux4+"</TD>";
        Contenido+="<TD width='10%' class='FilaEstilo' align='right' cell_click>"+CadAux7+"</TD>";
        Contenido+="<TD width='10%' class='FilaEstilo' align='right' id='td_celda_"+i+"' cell_monto>"+CadAux8+"</TD>";
            
    
        Contenido+="</TR>";
        }
      
      me.$("TABLA_LISTA_SOLICITUDES").innerHTML=Contenido;
      if(me.TipoModificar!=-1)
        me.CalcularMonto();
      
      
      
      
      
      //agregar las funciones onclick al listado
      Ext.each(me.getEl().query("#TABLA_LISTA_SOLICITUDES tr"),function(fila){
        var v=Ext.decode(fila.getAttribute("v"));
        
        Ext.each(Ext.get(fila).query("td img[view_op]"),function(img){
          Ext.get(img).on("click", function(ev,el){
            me.onView(v.index);
          });            
        });
        
        //si es distinto a -1, se puede modificar
        if(me.TipoModificar!=-1){
          Ext.each(Ext.get(fila).query("td[cell_click]"),function(td){
            Ext.get(td).on("click", function(ev,el){
              me.SeleccionarElementoTablaSolicitudes(v.index);
            });            
          });
          
          Ext.each(Ext.get(fila).query("td[cell_monto]"),function(td){            
            Ext.get(td).on("click", function(ev,el){
              me.SeleccionarElementoTablaSolicitudesMonto(v.index);
            });
            Ext.get(td).on("dblclick", function(ev,el){
              me.onEdit_Celda();
            });
          });
          
          Ext.each(Ext.get(fila).query("td input[type='checkbox']"),function(input){
            Ext.get(input).on("change", function(ev,el){
              me.onCheck(v.index);
            });            
          });
        }//fin me.TipoModificar!=-1
        

      });

    },
    
    /**
    * Permite seleccionar un elemento en la tabla solicitudes, la seleccion actual se guarda en me.IDSeleccionActualArreglo.
    */
    SeleccionarElementoTablaSolicitudesMonto: function(i){
      var me = this;
      me.IDSeleccionActualArreglo=i;
    },    
    
    /**
    * Permite seleccionar un elemento en la tabla solicitudes, a diferencia de que muestra la informacion del banco en los campos correspondiente y modifica la cuenta contable asociada al banco en los detalles del cheque.
    */
    SeleccionarElementoTablaSolicitudes: function(i){
      var me = this;
      me.SeleccionarElementoTablaSolicitudesMonto(i);      

      if(Ext.String.trim(me.$("CONCEPTO").value)=="")
        me.$("CONCEPTO").value=me.Arreglo[i][4];
        
      me.$("CHECK_"+i).checked=!me.$("CHECK_"+i).checked;
      me.onCheck(i);
    },
    
    /**
    * Al cambiar el un check de la tabla solicitudes, se debe modificar y recalcular el monto a pagar y los detalles del cheque
    * @param {Integer} i Indice marcado/desmarcado.
    */    
    onCheck: function(i){
      var me = this;
      me.Arreglo[i][0]=me.$("CHECK_"+i).checked;
      me.LlenarMontoPagar(i);
      if(me.Arreglo[i][0]==true){
        me.$("MONTO").value="Calculando...";
        //buscar inf contables solicitud
        me.onGetDetallesOP(me.Arreglo[i][1]);
        me.CalcularMonto();
        }
      else
        me.CalcularMonto();
    },
    
    /**
    * Al hacer marcar/descmarcar el check que se encuentra en la cabecera de la tabla solicitudes, este debe recorrer todos todas las solicitudes para cambiar el estado al del padre.
    */
    onCheckAll: function(){
      var me = this;
      var estado=me.$("CHECK_ALL").checked;
      for(var i=0;i<me.TamArreglo;i++){
        if(me.$("CHECK_"+i))
          me.$("CHECK_"+i).checked=estado;
        me.Arreglo[i][0]=estado;
        me.LlenarMontoPagar(i);
        }
      me.CalcularMonto();
    },
    
    /**
    * Al hacer dobleclick sobre el monto de la solicitud apaperce el campo de texto para modificarlo.
    */
    onEdit_Celda: function(){
      var me = this;
      var index=me.IDSeleccionActualArreglo;
      var id_txt_celda="txt_celda_"+index;
      var id_td_celda="td_celda_"+index;
      if(me.$(id_txt_celda))
        return;
      Valor=me.Arreglo[index][7];
      Valor=formatNumberDec(Valor,2);
    
      me.$(id_td_celda).innerHTML="<INPUT id='"+id_txt_celda+"' class='TextoCampoInputTabla' type='text' size='15' value='"+Valor+"'  style='text-align : right;'>";

      me.internal.event_blur_celda=me.$$(id_txt_celda).on({
        destroyable: true,
        blur: function(){me.onBlur_Celda();}
      });
      me.$(id_txt_celda).setAttribute("onkeypress","return onlyNumber({event: event, elDom: this, negative: false, percent: true});");
      me.$$(id_txt_celda).on("keyup",function(event){
        me.onKeyPress_Celda(event)
      });
      
      me.$(id_txt_celda).focus();
    },
    
    /**
    * Al perder el foco el campo de texto del monto de la solicitud, eliminanos el campo de texto y escribimos sobre la tabla el monto escrito.
    * Y realizamos los calculos correspondientes.
    */
    onBlur_Celda: function(){
      var me = this;
      var index=me.IDSeleccionActualArreglo;
      var id_txt_celda="txt_celda_"+index;
      var id_td_celda="td_celda_"+index;
      
      if(!me.$(id_txt_celda))
        return;
      //borrar el evento blur del txt antes de cargar el valor en el td de la tabla
      me.internal.event_blur_celda.destroy();
      //cuando el numero tengo % debe calcular el % en base al monto total de la solicitud
      var Aux=me.$(id_txt_celda).value;
      var ArregloAux=Aux.split("%");
      Aux=ArregloAux[0];
      if(ArregloAux.length==2)//es decir, tiene %
        if(ArregloAux[1]==""){//todo bien   10.00%
          //hacer el calculo del % en base al total de la solicitud
          Aux=me.Arreglo[index][3]*Aux/100;
          }
        else{//error p.ej. 10.00%12
          Aux="0.00";
          }
      //si se pasa del monto de deberia ser. Forzar monto nuevo al tope maximo a pagar
      var Aux2=me.Arreglo[index][3]*1.0-me.Arreglo[index][6]*1.0;
      if(Aux*1.0>Aux2*1.0)
        Aux=Aux2;
    
    
      me.Arreglo[index][7]=formatNumberDec(Aux,2);
      me.$(id_td_celda).innerHTML=formatNumber(me.Arreglo[index][7]);
      me.CalcularMonto();
    
      var SolicitudActual=me.Arreglo[index][1];
      if(me.ArregloDetalles[SolicitudActual] && me.ArregloDetallesFiltrado[SolicitudActual]){//si existen los arreglos
        //Busco el monto maximo real a pagar al proveedor en me.ArregloDetalles
        //Busco el monto a pagar al proveedor en el cheque en curso	me.ArregloDetallesFiltrado
        //si este monto + monto_pagado es mayor que el monto real maximo, forzar a la emision del cheque a ser el monto total por pagar o restante para cancelar la solicitud
        var n=me.ArregloDetalles[SolicitudActual].length;
        var n2=me.ArregloDetallesFiltrado[SolicitudActual].length;
        var A, B, C=me.Arreglo[index][6];//monto pagado
        for(i=0;i<n;i++)
          if(me.ArregloDetalles[SolicitudActual][i]['columna']=="C")
            if(me.ArregloDetalles[SolicitudActual][i]['subcuenta']=="x"){
              for(j=0;j<n2;j++)
                if(me.ArregloDetallesFiltrado[SolicitudActual][j]['columna']=="C")
                  if(me.ArregloDetallesFiltrado[SolicitudActual][j]['subcuenta']=="x"){
                    A=me.ArregloDetalles[SolicitudActual][i]['monto'];
                    B=me.ArregloDetallesFiltrado[SolicitudActual][j]['monto'];
    
                    if(B*1.0+C*1.0>A*1.0){//si excede el monto a pagar al proveedor, pagar todo
                      me.Arreglo[index][7]=formatNumberDec(Aux2,2);
                      me.$(id_td_celda).innerHTML=formatNumber(me.Arreglo[index][7]);
                      me.CalcularMonto();
                      }
                    break;
                    }
              break;
              }
        }//fin si
    },
    
    /**
    * Permite moverse por los montos de las solicitudes con el teclado, enter, tecla de direccion hacia abajo y hacia arriba.
    */
    onKeyPress_Celda: function(event){
      var me = this;
      
      if(event.getKey()==13 || event.getKey()==40){//si es enter o tecla hacia abajo
        me.onBlur_Celda(me.IDSeleccionActualArreglo);//hacemos que pierda el foco
        if(String(me.IDSeleccionActualArreglo)==String(me.TamArreglo-1)){
          return;
        }
        me.IDSeleccionActualArreglo++;
        me.SeleccionarElementoTablaSolicitudesMonto(me.IDSeleccionActualArreglo);
        me.onEdit_Celda(me.IDSeleccionActualArreglo);
      }
      else if(event.getKey()==38){//si es tecla hacia arriba
        me.onBlur_Celda(me.IDSeleccionActualArreglo);//hacemos que pierda el foco
        if(String(me.IDSeleccionActualArreglo)=='0')
          return;
        me.IDSeleccionActualArreglo--;
        me.SeleccionarElementoTablaSolicitudesMonto(me.IDSeleccionActualArreglo);
        me.onEdit_Celda(me.IDSeleccionActualArreglo);
      }
    },
    
    
    onView: function(i){
      var me = this;
      window.open("report/orden_pago.php?id="+me.Arreglo[i][1]);
    },
    
    onViewAll: function(){
      var me = this;
      if(me.TamArreglo==0) return;
      var ids=[];
      for(var i=0;i<me.TamArreglo;i++)
        ids[i]=me.Arreglo[i][1];	
      window.open("report/orden_pago.php?id="+ids.join());
    },
    
    onGetDetallesOP: function(_id){
      var me = this;
      var _tmp=Ext.Ajax.request({
        async: false,
        url:"module/comprobante/",
        params:{
          'action':"onGet",
          'id': _id
        }
      });
      if(_tmp.statusText=="OK"){
        var resultado=Ext.decode(_tmp.responseText);
    
        me.ArregloDetalles[resultado[0]["id"]]=[];
        var anio_op=resultado[0]["fecha"].split("/")[2];
        
        K=0;
        //si la orden de pago corresponde al mismo año de trabajo, cargar los DP
        //en caso contrario omitirlos
        if(String(anio_op)==String(siga.value("anio"))) {          
          for(var i=0;i<resultado[0]["detalle_presupuestario"].length;i++){
            me.ArregloDetalles[resultado[0]["id"]][K]=[];
            me.ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
            me.ArregloDetalles[resultado[0]["id"]][K]["cuenta"]=resultado[0]["detalle_presupuestario"][i]["estructura_presupuestaria"];
            me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
            me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
            me.ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
            me.ArregloDetalles[resultado[0]["id"]][K]["columna"]="P";
            me.ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_presupuestario"][i]["monto"];
            me.ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
            K++;
          }
        }
        
        var SUMA_DEBE=0;
        for(var i=0;i<resultado[0]["detalle_contable"].length;i++){
          if(resultado[0]["detalle_contable"][i]["operacion"]=="D") continue;
          me.ArregloDetalles[resultado[0]["id"]][K]=[];
          me.ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
          me.ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
          me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
          me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
          me.ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_contable"][i]["denominacion"];
          me.ArregloDetalles[resultado[0]["id"]][K]["columna"]="D";
          me.ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_contable"][i]["monto"];
          me.ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
          SUMA_DEBE+=resultado[0]["detalle_contable"][i]["monto"]*1.0;
          K++;
        }
        var SUMA_RETENCIONES=0;
        for(var i=0;i<resultado[0]["detalle_retencion"].length;i++){
          me.ArregloDetalles[resultado[0]["id"]][K]=[];
          me.ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
          me.ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
          me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]=resultado[0]["detalle_retencion"][i]["id_cuenta_contable"];
          me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]=resultado[0]["detalle_retencion"][i]["cuenta_contable"];
          me.ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]=resultado[0]["detalle_retencion"][i]["denominacion_contable"];
          me.ArregloDetalles[resultado[0]["id"]][K]["columna"]="C";
          me.ArregloDetalles[resultado[0]["id"]][K]["monto"]=resultado[0]["detalle_retencion"][i]["monto"];
          me.ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
          SUMA_RETENCIONES+=resultado[0]["detalle_retencion"][i]["monto"]*1.0;
          K++;
        }
        //cuenta de banco
        me.ArregloDetalles[resultado[0]["id"]][K]=[];
        me.ArregloDetalles[resultado[0]["id"]][K]["id_solicitud_pago"]=resultado[0]["id"];
        me.ArregloDetalles[resultado[0]["id"]][K]["cuenta"]="";
        me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta"]="x";
        me.ArregloDetalles[resultado[0]["id"]][K]["subcuenta_mostrar"]="";
        me.ArregloDetalles[resultado[0]["id"]][K]["denominacion_subcuenta"]="";
        me.ArregloDetalles[resultado[0]["id"]][K]["columna"]="C";
        me.ArregloDetalles[resultado[0]["id"]][K]["monto"]=SUMA_DEBE*1.0-SUMA_RETENCIONES*1.0;
        me.ArregloDetalles[resultado[0]["id"]][K]["id_accion_subespecifica"]="";
      }
    },
    
    
    
    /**
    * Calcula el monto a pagar para la solicitud en proceso.
    */
    LlenarMontoPagar: function(i){
      var me = this;
      if(me.Arreglo[i][0]==true){
        if(formatNumberDec(me.Arreglo[i][7],2)!="0.00")
          return;
        me.Arreglo[i][7]=formatNumberDec(me.Arreglo[i][3]-me.Arreglo[i][6],2);
        me.$("td_celda_"+i).innerHTML=formatNumber(me.Arreglo[i][7]);
        }
    },
    
    /**
    * Calcula el monto real por el cual se va a emitir el cheque
    */
    CalcularMonto: function(){
      var me = this;
      //llenar select detalles
      var Cad="<OPTION value=''>CHEQUE</OPTION>";
      for(var i=0;i<me.TamArreglo;i++)
        if(me.Arreglo[i][0]==true){
          Cad+="<OPTION value='"+me.Arreglo[i][1]+"'>OP# "+me.Arreglo[i][8]+"</OPTION>";
          }
      me.$("SELECT_DETALLES").innerHTML=Cad;
    
      //llenar tabla de los detalles (Especificamente todos)
      me.GenerarDetalles();
    
      var MONTO=0;
      for(var i=0;i<me.ArregloTodosK;i++)
        if(me.ArregloTodos[i]['subcuenta']=="x"){
          MONTO=formatNumberDec(me.ArregloTodos[i]['monto'],2);
          break;
          }
      me.$("MONTO").value=formatNumber(MONTO);
      return MONTO;
    },
    
    /**
    * Muesta los detalles del cheque, dependiendo de la solicitud que se desea mostrar la información.
    */
    CambioSelectDetalles: function(){
      var me = this;
      var SelectSolicitud=me.$("SELECT_DETALLES").value;
      var n=0;
      var ArregloImprimir=new Array();
    
      if(SelectSolicitud==""){//todos
        ArregloImprimir=copy(me.ArregloTodos);
        n=me.ArregloTodosK;
        }
      else{
        ArregloImprimir=copy(me.ArregloDetallesFiltrado[SelectSolicitud]);
        n=me.ArregloDetallesFiltrado[SelectSolicitud].length;
        }
    
      var Aux=new Array();
      for(i=0;i<n-1;i++)
        for(j=i+1;j<n;j++)
          if(me.PrioridadOrdenarColumna(ArregloImprimir[i]["columna"])>me.PrioridadOrdenarColumna(ArregloImprimir[j]["columna"])){
            Aux=copy(ArregloImprimir[j]);
            ArregloImprimir[j]=copy(ArregloImprimir[i]);
            ArregloImprimir[i]=copy(Aux);
            }
          else if(me.PrioridadOrdenarColumna(ArregloImprimir[i]["columna"])==me.PrioridadOrdenarColumna(ArregloImprimir[j]["columna"]))
            if(ArregloImprimir[i]["monto"]*1.0<ArregloImprimir[j]["monto"]*1.0){
            Aux=copy(ArregloImprimir[j]);
            ArregloImprimir[j]=copy(ArregloImprimir[i]);
            ArregloImprimir[i]=copy(Aux);
            }
    
    
      var SUMA_P=0;
      var SUMA_D=0;
      var SUMA_C=0;
      var Contenido=" ";
      var CadAux1="";
      var CadAux2="";
    
      for(i=0;i<n;i++){
        Contenido+="<TR>";
        Contenido+="<TD width='17%' class='FilaEstilo' style='font-size : 11px;' align='center'>"+ArregloImprimir[i]['cuenta']+"</TD>";
        if(ArregloImprimir[i]['subcuenta']!="x"){
          CadAux1=ArregloImprimir[i]['subcuenta_mostrar'];
          CadAux2=ArregloImprimir[i]['denominacion_subcuenta'];
          }
        else{
          CadAux1=me.$("CUENTA_CONTABLE").value;
          CadAux2=me.$("CTA_DENOMINACION_CONTABLE").value;
          }
    
        Contenido+="<TD width='13%' class='FilaEstilo' align='center'>"+CadAux1+"</TD>";
        Contenido+="<TD width='33%' class='FilaEstiloContinua' align='left' style='font-size : 11px;'>&nbsp;"+CadAux2+"</TD>";
    
        if(ArregloImprimir[i]['columna']=="P"){
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+formatNumber(ArregloImprimir[i]['monto'])+"</TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          SUMA_P=SUMA_P*1.0+formatNumberDec(ArregloImprimir[i]['monto'],2)*1.0;
          }
        else if(ArregloImprimir[i]['columna']=="D"){
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+formatNumber(ArregloImprimir[i]['monto'])+"</TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          SUMA_D=SUMA_D*1.0+formatNumberDec(ArregloImprimir[i]['monto'],2)*1.0;
          }
        else{
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'></TD>";
          Contenido+="<TD width='11%' class='FilaEstilo' align='right'>"+formatNumber(ArregloImprimir[i]['monto'])+"</TD>";
          SUMA_C=SUMA_C*1.0+formatNumberDec(ArregloImprimir[i]['monto'],2)*1.0;
          }
        Contenido+="</TR>";
        }
      me.$("TABLA_LISTA_DETALLES").innerHTML=Contenido;
    
      me.$("TOTAL_PARCIALES").value=formatNumber(SUMA_P);
      me.$("TOTAL_DEBITOS").value=formatNumber(SUMA_D);
      me.$("TOTAL_CREDITOS").value=formatNumber(SUMA_C);    
    },
    
    /**
    * Se calcula y se generan los detalles dependiendo del monto a pagar por solicitud y de las retenciones.
    * Calculos necesario para conocer el monto real del cheque.
    */
    GenerarDetalles: function(){
      var me = this;
      var SelectSolicitud="";
      var MontoSolicitud=0;
      var MontoPagado=0;
      var MontoPagar=0;
      var n=0;
      var ArregloImprimir=new Array();
    
      var Porcentaje=0;
      var sw=false;
      var PosCtaBanco=-1;
      var Suma=0;
      var K=0;
    
    
    
      me.ArregloTodos=[];
    
      for(var w=0;w<me.TamArreglo;w++)
        if(me.Arreglo[w][0]==true){
          ArregloImprimir="";
          ArregloImprimir=new Array();
          n=0;
    
          SelectSolicitud=me.Arreglo[w][1];
          MontoSolicitud=formatNumberDec(me.Arreglo[w][3],2);
          MontoPagado=formatNumberDec(me.Arreglo[w][6],2);
          MontoPagar=formatNumberDec(me.Arreglo[w][7],2);
          if(MontoSolicitud==MontoPagar && MontoPagado=="0.00"){//si se paga toda la solicitud en el 1er intento
            ArregloImprimir=copy(me.ArregloDetalles[SelectSolicitud]);
            n=ArregloImprimir.length;
            }
          else{
            if(me.$("SELECT_RETENCION").value=="0"){//INCLUIR RETENCIONES EN EL ULTIMO PAGO
              sw=false;
              if(formatNumberDec(MontoPagado*1.0+MontoPagar*1.0,2)==MontoSolicitud)//si el ultimo pago incluir las retenciones
                sw=true;
              }
            else{//INCLUIR RETENCIONES EN EL 1er PAGO
              sw=true;
              if(MontoPagado*1.0>0)//si existen pagos realizados
                sw=false;
              }
    
            n=0;
            PosCtaBanco=-1;
            Suma=0;
            Porcentaje=MontoPagar/MontoSolicitud;
            for(i=0;i<me.ArregloDetalles[SelectSolicitud].length;i++){
              if(me.ArregloDetalles[SelectSolicitud][i]['columna']=="P" || me.ArregloDetalles[SelectSolicitud][i]['columna']=="D"){
                ArregloImprimir[n]=new Array();
                ArregloImprimir[n]=copy(me.ArregloDetalles[SelectSolicitud][i]);
                ArregloImprimir[n]['monto']=formatNumberDec(ArregloImprimir[n]['monto']*Porcentaje,2);
                n++;
                }
              else{//si es C, Credito
                if(me.ArregloDetalles[SelectSolicitud][i]['subcuenta']=='x')
                  PosCtaBanco=i;
                if(me.ArregloDetalles[SelectSolicitud][i]['subcuenta']!='x' && sw==true){//si no es la cuenta banco y es incluir retenciones (si es una retencion)
                  ArregloImprimir[n]=new Array();
                  ArregloImprimir[n]=copy(me.ArregloDetalles[SelectSolicitud][i]);
                  Suma=Suma*1.0+ArregloImprimir[n]['monto']*1.0;
                  n++;
                  }
                }
              }//fin for
    
            if(PosCtaBanco!=-1){
              ArregloImprimir[n]=new Array();
              ArregloImprimir[n]=copy(me.ArregloDetalles[SelectSolicitud][PosCtaBanco]);
    
              if(sw==false)//si no estan incluidas las retenciones, cta banco = montopagar
                ArregloImprimir[n]['monto']=MontoPagar;
              else
                ArregloImprimir[n]['monto']=formatNumberDec(MontoPagar-Suma,2);
              n++;
              }
            }
    
          me.ArregloDetallesFiltrado[SelectSolicitud]=copy(ArregloImprimir);
    
          if(K==0){
            for(i=0;i<n;i++){
              me.ArregloTodos[K]=copy(ArregloImprimir[i]);
              K++;
              }
            }
          else{
            for(i=0;i<n;i++){
              sw=false;
              for(j=0;j<K;j++)
                if(me.ArregloTodos[j]["id_accion_subespecifica"]==ArregloImprimir[i]["id_accion_subespecifica"] && me.ArregloTodos[j]["subcuenta"]==ArregloImprimir[i]["subcuenta"] && me.ArregloTodos[j]["columna"]==ArregloImprimir[i]["columna"]){
                  me.ArregloTodos[j]["monto"]=formatNumberDec(me.ArregloTodos[j]["monto"]*1.0+ArregloImprimir[i]['monto']*1.0,2);
                  sw=true;
                  }
              if(sw==false){
                me.ArregloTodos[K]=copy(ArregloImprimir[i]);
                K++;
                }
              }
            }
    
          }
    
      me.ArregloTodosK=K;
      me.CambioSelectDetalles();    
    },    
    
    /**
    * Necesario para ordenar los detalles en el siguiente orden: P (Parciales=Mobimientos presupuestarios), D (Debitos), C (Creditos)
    * @param {Caracter}
    */
    PrioridadOrdenarColumna: function(V){
      var me = this;
      if(V=="P") return 0;
      if(V=="D") return 1;
      return 2;
    },    
    
    
    
    /**
    * Guarda los datos en la BD
    */
    onSave: function(){
      var me = this;
      var msg="";
      
      var _id_persona							= Ext.String.trim(strtoupper(me.$("PERSONA_ID").value));
      var _id_banco_cuenta				= Ext.String.trim(strtoupper(me.$("ID_CTA").value));
      var _fecha									= Ext.String.trim(strtoupper(me.$("FECHA").value));
      var _numero									= Ext.String.trim(strtoupper(me.$("N_CHEQUE").value));
      var _concepto								= Ext.String.trim(strtoupper(me.$("CONCEPTO").value));
      var _monto=0;
      var _id_cuenta_contable			= "";
      //if(me.TipoModificar!=-1)
      //	_monto				= me.CalcularMonto();
    
      if(!_id_persona){
        msg="Por favor seleccione el proveedor o beneficiario.";
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      if(!_id_banco_cuenta){
        msg="Por favor seleccione la cuenta bancaria.";
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      if(!_fecha){
        msg="Por favor introduzca la fecha.";
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      if(!isValidDate(_fecha)){
        me.Mensaje("La fecha es incorrecta. Debe estar en formato DD/MM/AAAA.","ROJO");
        me.MensajeListado("");
        return;
        }
      _fecha=unformatDate(_fecha);
      
      if(!_numero){
        msg="Por favor introduzca el número del cheque.";
        if(me.getCmp('btnTransferencia').pressed)
          msg="Por favor introduzca el número de transferencia.";        
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      if(!_concepto){
        msg="Por favor introduzca el concepto.";
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      
      if(me.$("TOTAL_DEBITOS").value!=me.$("TOTAL_CREDITOS").value){
        msg="Error. No coinciden los totales contables en los detalles del cheque.";
        me.Mensaje(msg,"ROJO");
        me.MensajeListado("");
        return;
        }
      
      
      me.DesactivarFormulario();
      
      var _detalle={};	
      
      
      
      
      if(me.TipoModificar!=-1){
        _detalle.presupuestario=[];
        _detalle.contable=[];
        _detalle.comprobante_previo_monto_pagado=[];
        
        //asociar el cheque a las ordedes de pago seleccionadas
        for(var i=0;i<me.TamArreglo;i++)
          if(me.Arreglo[i][0]==true)
            _detalle.comprobante_previo_monto_pagado.push({
              id_comprobante: me.Arreglo[i][1],
              monto_pagado: me.Arreglo[i][7]
            });
        
        
        //agregar detalles presupuestarios y contables
        for(i=0;i<me.ArregloTodosK;i++){
          if(me.ArregloTodos[i]["columna"]=="P"){//presupuesto			
            _detalle.presupuestario.push({
              id_accion_subespecifica: me.ArregloTodos[i]["id_accion_subespecifica"],
              id_cuenta_presupuestaria: me.ArregloTodos[i]["subcuenta"],
              operacion: 'P',
              monto: me.ArregloTodos[i]["monto"]
            });
          }
          else{//contabilidad
            _id_cuenta_contable=me.ArregloTodos[i]["subcuenta"];
            if(me.ArregloTodos[i]["subcuenta"]=='x'){
              _monto=me.ArregloTodos[i]["monto"];
              _id_cuenta_contable=me.$("CTA_CODIGO_CONTABLE").value;
            }
            
            _detalle.contable.push({
              id_cuenta_contable: _id_cuenta_contable,
              operacion: me.ArregloTodos[i]["columna"]=="D"?"D":"H",
              monto: me.ArregloTodos[i]["monto"]			
            });
          }
        }
      }
      
      var _id_banco_movimiento_tipo="";
      if(me.getCmp('btnCheque').pressed)
        _id_banco_movimiento_tipo=3;//cheque orden de pago
      else if(me.getCmp('btnTransferencia').pressed)
        _id_banco_movimiento_tipo=9;//transferencia orden de pago
      
      _detalle.comprobante_bancario={
        id_banco_cuenta: _id_banco_cuenta,
        id_banco_movimiento_tipo: _id_banco_movimiento_tipo,//cheque orden de pago o transferencia orden de pago
        numero: _numero,
        monto: _monto
      };
      
      
      
      
      var _id_comprobante="";
      if(me.IDSeleccionActualLista>0) 
        _id_comprobante=me.IDSeleccionActualLista;
      
      if(_id_comprobante){
        if(!confirm("¿Esta seguro que desea guardar los cambios?")){
          me.ActivarFormulario();
          return;
          }
        }
      
      _detalle.file='t';
      if(Ext.String.trim(me.$("ARCHIVO_ADJUNTO").value)=="")
        _detalle.file='f';
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        //form: me.$("FORMULARIO"),
        //isUpload: true,
        params:{
          action: 'onSave',
          id: _id_comprobante,
          tipo: 'MB',
          fecha:_fecha,
          concepto:_concepto,
          contabilizado: 'f',
          id_persona: _id_persona,          
          detalle: Ext.encode(_detalle)
        },
        success:function(request){
          console.log(request);
          me.GuardarMensaje(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
    
    /**
    * Muestra el mensaje despues de guardar los datos
    * @param {Array} req respuesta luego de guardar los datos 1=exito, !1=fracaso
    */
    GuardarMensaje: function(req){
      var me = this;
      me.ActivarFormulario();
      var respuesta = eval("("+req.responseText+")");
      if(respuesta.success){
        me.onNew();
        me.Mensaje(respuesta.message,"VERDE");
        }
      else
        me.Mensaje(respuesta.message,"ROJO");
    },
    
    /*Al presionar enter buscamos directamente en el listado*/
    PresionarEnter: function(ev){
      var me = this;
      if(me.$("BUSCAR_CHECKBOX").checked){
        if(ev.getKey()==ev.ENTER)
          me.onSearch();
        return;
        }
      me.onSearch();
    },
    
    /**
    * Es llamada cuando se introduce texto en el INPUT TEXT buscar de la pestaña lista
    */
    onSearch: function(){
      var me = this;
      
      var _operacion_codigo=['CH'];
      if(me.getCmp('btnCheque').pressed){
        _operacion_codigo=['CH'];
        me.$("PAGO_TIPO").innerHTML="Cheque&nbsp;No.&nbsp;";
        me.$$('PAGO_ADJUNTO').set({style: {'display': "none"}});        
      }
      else if(me.getCmp('btnTransferencia').pressed){
        _operacion_codigo=['PT'];
        me.$("PAGO_TIPO").innerHTML="Transferencia&nbsp;No.&nbsp;";
        me.$$('PAGO_ADJUNTO').set({style: {'display': ""}});
      }     
      
      
      me.OcultarBotones();
    
      me.IDSeleccionActualLista=-1;
      me.TipoModificar=-1;
      me.$("FORMULARIO").reset();
      me.$("MONTO").value="0,00";
    
      me.$("TABLA_LISTA_SOLICITUDES").innerHTML=" ";
      me.$("TABLA_LISTA_DETALLES").innerHTML=" ";
      me.$("SELECT_DETALLES").innerHTML="<OPTION value=''>CHEQUE</OPTION>";
      me.$("TOTAL_PARCIALES").value="0,00";
      me.$("TOTAL_DEBITOS").value="0,00";
      me.$("TOTAL_CREDITOS").value="0,00";
    
      
      
      
    
      me.ActivarFormulario();
      me.DesactivarBotonModificar();
      me.DesactivarBotonEliminar();
      me.ActivarBotonGuardar();
    
      var CadenaBuscar=Ext.String.trim(strtoupper(me.$("LISTADO_TXT_BUSCAR").value));
      if(CadenaBuscar!="")
        if(me.BuscarListado_CadenaBuscar==CadenaBuscar)
          return;
      me.BuscarListado_CadenaBuscar=CadenaBuscar;    
      
      me.$("TABLA_LISTA").innerHTML=""; //Mostrar icono cargando   
    
      
      var _tipo_persona=me.SW_PERSONA;
        
      var _mostrar={
        'mes':me.$("LISTADO_MES_FILTRAR").value,		
        'tipo':['MB'],
        'operacion_codigo': _operacion_codigo,
        'persona': 't',
        'tipo_persona': _tipo_persona
      };
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onList',
          mostrar: Ext.encode(_mostrar),
          text:CadenaBuscar,
          start: '0',
          limit : 'ALL',
          sort:'[{"property":"fecha","direction":"DESC"},{"property":"correlativo","direction":"DESC"}]'	
        },
        success:function(request){
          var result=Ext.JSON.decode(request.responseText);
          me.MostrarListado(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
      
    /**
    * Muestra el listado (Crea tabla dinamicamente)
    * @param {Array} req Datos provenientes de la BD
    */
    MostrarListado: function(req){
      var me = this; 
      var respuesta = req.responseText;
      var resultado = eval("(" + respuesta + ")");
      resultado=resultado["result"];
      
      var n=resultado.length;
    
      var CadAux1, CadAux2;
      
      var TextoBuscar=Ext.String.trim(strtoupper(me.$("LISTADO_TXT_BUSCAR").value));
      var Contenido=" ";
      var FuncionOnclick="";
      var FuncionOnDblclick="";
      var FuncionOnMouseOver="";
      var FuncionOnMouseOut="";
      var CadAux1, CadAux2, CadAux3, CadAux4, CadAux5, CadAux6, Aux, Aux2;
    
      for(var i=0;i< n; i++){
        
        estado="";
        color_estado="";
        if(resultado[i]['contabilizado']=="t" && resultado[i]['anulado']=='t'){
          color_estado="#000000";//negro (anulado)
          estado=3;
        }
        else if(resultado[i]['contabilizado']=="t"){
          color_estado="#48DC0E";//verde (contabilizado)
          estado=2;
        }
        else{
          color_estado="#FF5454";//rojo (sin contabilizar)
          estado=0;
        }
        
        //FuncionOnclick="me.SeleccionarElementoTabla('"+resultado[i]['id']+"',"+estado+")";
        //FuncionOnDblclick="me.TabPane.setSelectedIndex(0);";
        //FuncionOnMouseOver="pintarFila(\"FCOP"+resultado[i]['id']+"\")";
        //FuncionOnMouseOut="despintarFila(\"FCOP"+resultado[i]['id']+"\")";
    
    
        //Contenido+="<TR id='FCOP"+resultado[i]['id']+"' onclick=\""+FuncionOnclick+"\" ondblclick='"+FuncionOnDblclick+"' onmouseover='"+FuncionOnMouseOver+"' onmouseout='"+FuncionOnMouseOut+"'>";
        Contenido+="<TR id='FCOP"+resultado[i]['id']+"' v='{\"id\":\""+resultado[i]['id']+"\",\"estado\":\""+estado+"\"}'>";
        
        if(!resultado[i]['persona']) 
          resultado[i]['persona']="";
        
        
        if(me.$("SOMBRA_CHECKBOX").checked && TextoBuscar!=""){
          //CadAux1=str_replace(completarCodigoCeros(resultado[i]['id_cheque'],NDigitos_Codigo_VoucherCheque),"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
          CadAux2=str_replace(resultado[i]['numero'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
          CadAux3=str_replace(resultado[i]['fecha'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
          CadAux4=str_replace(resultado[i]['concepto'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
          CadAux5=str_replace(resultado[i]['persona'],"<strong>"+TextoBuscar+"</strong>",TextoBuscar);
          CadAux6=formatNumber(resultado[i]['monto']);
          }
        else{
          //CadAux1=completarCodigoCeros(resultado[i]['id_cheque'],NDigitos_Codigo_VoucherCheque);
          CadAux2=resultado[i]['numero'];
          CadAux3=resultado[i]['fecha'];
          CadAux4=resultado[i]['concepto'];
          CadAux5=resultado[i]['persona'];
          CadAux6=formatNumber(resultado[i]['monto']);
          }
    //		Aux="";
    // 		if(resultado[i]['entregado']=="t")	Aux="checked";
        
        Contenido+="<TD class='FilaEstilo' style='width: 5px;'><DIV style='width: 5px; background-color: "+color_estado+";'>&nbsp;</DIV></TD>";
    
        //Contenido+="<TD width='1%' class='FilaEstilo'><INPUT id='CBL"+resultado[i]['id']+"' type='checkbox' "+Aux+" onchange='me.ListadoAlternarCustoria("+resultado[i]['id']+")' title='"+me.MsgCustodia(resultado[i]['entregado']=="t"?1:0)+"'></TD>";
        Contenido+="<TD width='10%' >"+CadAux2+"</TD>";
        Contenido+="<TD width='10%' align='center'>"+CadAux3+"</TD>";
        Contenido+="<TD width='25%' class='continua'>"+CadAux5+"</TD>";
        Contenido+="<TD class='continua' style='padding-left: 5px;'>"+CadAux4+"</TD>";		
        Contenido+="<TD width='10%' align='right'>"+CadAux6+"</TD>";
    
        Contenido+="</TR>";
        }
    
      me.$("TABLA_LISTA").innerHTML=Contenido;
      
      //agregar las funciones onclick al listado
      Ext.each(me.getEl().query("#TABLA_LISTA tr"),function(fila){
        Ext.get(fila).on("click", function(){
          var v=Ext.decode(fila.getAttribute("v"));
          me.SeleccionarElementoTabla(v.id,v.estado);
        });        
        Ext.get(fila).on("dblclick", function(){
          me.TabPane.setSelectedIndex(0);
        }); 
      });      
    },
      
    /**
    * Es llamada cuando se hace click sobre algun elemento de la tabla.
    * Esta manda los datos para el formulario que se encuentra en la pestaña 'entrada de datos'
    */
    SeleccionarElementoTabla: function(IDSeleccion,estado){
      var me = this;
      if(me.IDSeleccionActualLista==IDSeleccion)
        return;
      if(me.IDSeleccionActualLista!=-1)
        me.$("FCOP"+me.IDSeleccionActualLista).removeAttribute("selected");     
      me.IDSeleccionActualLista=IDSeleccion;
      me.$("FCOP"+me.IDSeleccionActualLista).setAttribute("selected",""); 
      
      me.$("SELECT_DETALLES").innerHTML="<OPTION value=''>CHEQUE</OPTION>";
    
      me.TipoModificar=-1;  
    
      
      me.OcultarBotones();
      
      
      me.DesactivarFormulario();
      me.DesactivarBotonGuardar();
      me.DesactivarBotonModificar();
      me.DesactivarBotonEliminar();
      me.ActivarBotonImprimir();
      
      
      me.Mensaje("");
      me.MensajeListado("");
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onGet',
          id: me.IDSeleccionActualLista	
        },
        success:function(request){
          var resultado=Ext.JSON.decode(request.responseText);
          
          me.$("COMPROBANTE").innerHTML=resultado[0]["tipo"]+"-"+resultado[0]["correlativo"];
          
          me.$("FECHA").value=resultado[0]["fecha"];
          me.$("CONCEPTO").value=resultado[0]["concepto"];
          
          //cargar proveedor/beneficiario
          me.$("PERSONA_ID").value=resultado[0]["detalle_persona"][0]["id"];
          me.$("PERSONA_IDENTIFICACION").value=resultado[0]["detalle_persona"][0]["identificacion"];
          me.$("PERSONA_DENOMINACION").value=resultado[0]["detalle_persona"][0]["denominacion"];
          
          //numero de cuenta
          me.$("ID_CTA").value=resultado[0]["detalle_comprobante_bancario"][0]["id_banco_cuenta"];
          me.$("NCTA").value=resultado[0]["detalle_comprobante_bancario"][0]["numero_cuenta"];
          me.$("DESCRIPCION_NCTA").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_denominacion"];
          me.$("CTA_CODIGO_CONTABLE").value=resultado[0]["detalle_comprobante_bancario"][0]["id_cuenta_contable"];
          me.$("CUENTA_CONTABLE").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_contable"];
          me.$("CTA_DENOMINACION_CONTABLE").value=resultado[0]["detalle_comprobante_bancario"][0]["denominacion_contable"];
          me.$("TIPO_CTA").value=resultado[0]["detalle_comprobante_bancario"][0]["cuenta_tipo"];
          me.$("BANCO").value=resultado[0]["detalle_comprobante_bancario"][0]["banco"];
          
          //otros datos del cheque
          me.$("N_CHEQUE").value=resultado[0]["detalle_comprobante_bancario"][0]["numero"];
          me.$("MONTO").value=resultado[0]["detalle_comprobante_bancario"][0]["monto"];
          
          //mostrar archivos adjuntos
          me.$("ARCHIVO_ADJUNTO").value="";
          if(resultado[0]["file"].length>0)            
            me.$("ARCHIVO_ADJUNTO").value=resultado[0]["file"][0];
          
          //cargar ordenes de pago asociadas al cheque
          var _mostrar={
            id: resultado[0]["id"]
          };
          
          _tmp=Ext.Ajax.request({
            async: false,
            url:"module/comprobante/",
            params:{
              action: 'onList_OP_cheque',
              mostrar: Ext.encode(_mostrar),
              text:'',
              start: '0',
              limit : 'ALL',
              sort:'[{"property":"fecha","direction":"ASC"}]'		
            }
          });
          if(_tmp.statusText=="OK"){
            var _retorno=Ext.decode(_tmp.responseText);
            _retorno=_retorno["result"];
            //console.log(_retorno);
            for(var i=0;i<_retorno.length;i++){
              me.Arreglo[i]=[];
              me.Arreglo[i][0]=true;//check
              me.Arreglo[i][1]=_retorno[i]['id'];
              me.Arreglo[i][8]=_retorno[i]['correlativo'];
              me.Arreglo[i][2]=_retorno[i]['fecha'];
              me.Arreglo[i][3]=formatNumberDec(_retorno[i]['monto'],2);
              me.Arreglo[i][4]=strtoupper(_retorno[i]['concepto']);
              me.Arreglo[i][5]="";
              me.Arreglo[i][6]=formatNumberDec(_retorno[i]['monto_pagado_acumulado'],2);
              me.Arreglo[i][7]=formatNumberDec(_retorno[i]['monto_pagado'],2);;
            }
          me.TamArreglo=_retorno.length;								
          me.MostrarListadoSolicitudes();
          }
          
          //cargar y mostrar detalles presupuestarios y contables
          me.ArregloTodos=[];								
          K=0;
          for(var i=0;i<resultado[0]["detalle_presupuestario"].length;i++){
            me.ArregloTodos[K]=[];
            me.ArregloTodos[K]["id_solicitud_pago"]=resultado[0]["id"];
            me.ArregloTodos[K]["cuenta"]=resultado[0]["detalle_presupuestario"][i]["estructura_presupuestaria"];
            me.ArregloTodos[K]["subcuenta"]=resultado[0]["detalle_presupuestario"][i]["id_cuenta_presupuestaria"];
            me.ArregloTodos[K]["subcuenta_mostrar"]=resultado[0]["detalle_presupuestario"][i]["cuenta_presupuestaria"];
            me.ArregloTodos[K]["denominacion_subcuenta"]=resultado[0]["detalle_presupuestario"][i]["denominacion"];
            me.ArregloTodos[K]["columna"]="P";
            me.ArregloTodos[K]["monto"]=resultado[0]["detalle_presupuestario"][i]["monto"];
            me.ArregloTodos[K]["id_accion_subespecifica"]=resultado[0]["detalle_presupuestario"][i]["id_accion_subespecifica"];
            K++;
          }
          
          for(var i=0;i<resultado[0]["detalle_contable"].length;i++){
            me.ArregloTodos[K]=[];
            me.ArregloTodos[K]["id_solicitud_pago"]=resultado[0]["id"];
            me.ArregloTodos[K]["cuenta"]="";
            me.ArregloTodos[K]["subcuenta"]=resultado[0]["detalle_contable"][i]["id_cuenta_contable"];
            me.ArregloTodos[K]["subcuenta_mostrar"]=resultado[0]["detalle_contable"][i]["cuenta_contable"];
            me.ArregloTodos[K]["denominacion_subcuenta"]=resultado[0]["detalle_contable"][i]["denominacion"];
            me.ArregloTodos[K]["columna"]=resultado[0]["detalle_contable"][i]["operacion"]=="D"?"D":"C";
            me.ArregloTodos[K]["monto"]=resultado[0]["detalle_contable"][i]["monto"];
            me.ArregloTodos[K]["id_accion_subespecifica"]="";
            K++;
          }
          me.ArregloTodosK=K;
          me.CambioSelectDetalles();
          
          
          //segun el estado de la orden, activar los botones de contabilizar, reversar y anular
          me.OcultarBotones();
          switch(estado){
            //SIN CONTABILIZAR (ROJO)
            case 0://activar el boton de modificar, mostrar el boton de contabilizar															
              me.ActivarBotonModificar();
              me.$("BOTON_CONTABLIZAR").style.display="";
              break;
            //CONTABILIZADO (VERDE)
            case 2://mostrar el boton de reversar y anular
              me.$("BOTON_REVERSAR").style.display="";
              me.$("BOTON_ANULAR").style.display="";
              break;
            case 3:
              
              break;
          }
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
    
    /**
    * Es llamada cuando se presiona sobre el boton limpiar.
    * Este borra el contenido de INPUT TEXT buscar y muestra el listado completo
    */
    LimpiarInputTextBuscarListado: function(){
      var me = this;
      me.IDSeleccionActualLista=-1;
      me.NombreBanco="";
      me.DesactivarBotonImprimir()
      me.DesactivarBotonModificar();
      me.DesactivarBotonEliminar();
      me.ActivarBotonGuardar();
      me.ActivarFormulario();
      me.$("FORMULARIO").reset();
      
      me.$("CTA_CODIGO_CONTABLE").value="";
      me.$("CUENTA_CONTABLE").value="";
      me.$("CTA_DENOMINACION_CONTABLE").value="";
      
      me.$("LISTADO_TXT_BUSCAR").value="";
      me.Mensaje("");
      me.MensajeListado("");
      me.onSearch();
    },
    
    /**
    * Es llamada cuando se presiona el boton de modificar
    */
    onEdit: function(){
      var me = this;
      me.OcultarBotones();
      me.DesactivarBotonModificar();
      me.ActivarBotonGuardar();
      
      me.CargarSolicitudes();
      
      me.TipoModificar=1;
      me.$("CHECK_ALL").disabled=false;
      me.ActivarFormulario(me.TipoModificar);
    },
    
    /**
    * Es llamada cuando se presiona el boton de eliminar. Esta hace un borrado logico.
    */
    Eliminar: function(){
      var me = this;
      //OJO. NUNCA DEBERIA CUMPLIRSE, PORQUE EL BOTON ELIMINAR ESTA DESACTIVADO. NO HAY ELEMENTO SELECIONADO.
      if(me.IDSeleccionActualLista==-1)
        return;
      alert("La acción se encuentra deshabilitada");
      return;
      if(!confirm("¿Esta seguro que quiere eliminarlo?"))
        return;
      
      alert("La acción se encuentra deshabilitada. Consulte al administrador del sistema.");      
    },
    
    
    OcultarBotones: function(){
      var me = this;
      me.$("BOTON_ANULAR").style.display="none";
      me.$("BOTON_CONTABLIZAR").style.display="none";
      me.$("BOTON_REVERSAR").style.display="none";	
    },
    
    onContabilizar: function(){
      var me = this;
      me.Mensaje("");
      var _id_comprobante="";
      if(me.IDSeleccionActualLista>0) 
        _id_comprobante=me.IDSeleccionActualLista;
      else
        return;
      
      //ocultar el boton de contabilizar
      me.OcultarBotones();
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onSet_Contabilizar',
          id: _id_comprobante,
          contabilizado: 't'	
        },
        success:function(request){
          me.GuardarMensaje(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
      
    onReversar: function(){
      var me = this;
      me.Mensaje("");
      var _id_comprobante="";
      if(me.IDSeleccionActualLista>0) 
        _id_comprobante=me.IDSeleccionActualLista;
      else
        return;
      
      //ocultar el boton de contabilizar
      me.OcultarBotones();
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onSet_Contabilizar',
          id: _id_comprobante,
          contabilizado: 'f'	
        },
        success:function(request){
          me.GuardarMensaje(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
      
    onAnular: function(){
      var me = this;
      me.Mensaje("");
      var _id_comprobante="";
      if(me.IDSeleccionActualLista>0) 
        _id_comprobante=me.IDSeleccionActualLista;
      else
        return;
      
    
      //pedir fecha de anulacion
      //var _fecha=me.$("FECHA_ACTUAL_SIGAFS").value;
      var _fecha=siga.get({action: "date", format: "d/m/Y"}).result;
      while(true){
        _fecha=prompt("Introduzca la fecha de anulación (DD/MM/AAAA).", _fecha);
        if(_fecha==null)//si es cancelar
          return;
        if(isValidDate(_fecha))//si es valida
          break;
        alert("La fecha introducida es invalida.");
        }
      _fecha=unformatDate(_fecha);
      
      //dar la posibilidad de anular o no los compromisos previos
      
      me.Mensaje("Anulando. Por favor espere...");
      me.OcultarBotones();
    
      var _detalle={};
      _detalle.comprobante_previo='liberar';
      
      
      Ext.Ajax.request({
        method: 'POST',
        url:'module/comprobante/',
        params:{
          action: 'onAnular',
          id: _id_comprobante,
          fecha: _fecha,
          detalle: Ext.encode(_detalle)
        },
        success:function(request){
          me.GuardarMensaje(request);
        },
        failure:function(request){
          alert('Error!\nStatusText='+request.statusText+'\nContents='+request.responseText);
        }
      });
    },
    
    /**
    * Visualiza el cheque
    */
    onDisplay: function(){
      var me = this;
      if(me.IDSeleccionActualLista==-1)
        return;
      window.open("report/pago.php?id="+me.IDSeleccionActualLista);
    },
    
});