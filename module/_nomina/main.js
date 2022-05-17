siga.define('nomina', {
  extend: 'siga.windowBase',
  title: 'Nómina - Hoja de Cálculo',
  width: 780,
  height: 570,
  maximizable:true,
  resizable: true,

  initComponent: function(){
    var me = this;
    
    me.setInternal({
      ventanaSeleccionarNomina: null,
      ventanaVisualizar: null,
      messageTimeOutHandler: null,
      items:[],
      toCopy:[],
      itemsToolbar:[],
      data: {
        concepto: [],
        ficha: []        
      },
      columnaSeleccionada: null,
      cerrado: null
    });
    
    
    
    
    //VENTANA PARA CAMBIAR/SELECCIONAR NOMINA
    me.internal.ventanaSeleccionarNomina=Ext.create('Ext.window.Window', {
      title: 'Seleccionar Nómina',
      minimizable: false,      
      maximizable: false,
      closable: false,
      modal: true,
      width: 550,
      height: 280,
      resizable: false,
      layout: 'anchor',
      bodyStyle: 'padding: 5px 20px 0px 20px; background-color: #FFF;',
      internal:{
        tipo: '',
        id_periodo: '',
        id_nomina: ''
      },
      listeners: {
        beforeclose: function(w,o){
          me.onRecargar();
          me.internal.ventanaSeleccionarNomina.hide();
          //me.onUpdate_MenuPersonaCambiarNomina();
          return false;
        }
      },
      
      items:[
        {
          xtype: 'label',
          id: me._('messageVentanaSeleccionarNomina'),
          style:'margin: 5px 0px 0px 0px; text-align: center; font-style: italic;',
          html: "&nbsp;",
          anchor: '100%'
        },
        
        {
          xtype:'combobox',
          id: me._('tipoVentanaSeleccionarNomina'),
          name: 'tipoVentanaSeleccionarNomina',
          fieldLabel: 'Tipo de Nómina/Periodo',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          anchor: '100%',
          queryMode: "local",
          store: {
            fields: ['tipo','denominacion'],
            autoLoad: true,
            pageSize: 1000,
            proxy: {
              type:'ajax',
              url: 'module/nomina_periodo_tipo/',
              actionMethods: {read: "POST"},
              timeout: 3600000,
              reader: {
                type: 'json',
                rootProperty: 'result',
                totalProperty:'total'
              },
              extraParams: {
                action: 'onList_Activo',
                text: '',
                sort: '[{"property": "denominacion", "direction": "ASC"}]'
              }
            },
            listeners: {
              load: function(store, records, successful){
                if(records.length>0)
                  me.getCmp("tipoVentanaSeleccionarNomina").setValue(records[0].get("tipo"));
              }
            }
          },
          displayField: 'denominacion',
          valueField: 'tipo',
          allowBlank: false,
          forceSelection: true,
          editable: false,
          value: '',
          listeners: {
            afterrender: function(e, eOpts ){
              e.setValue("Q");
            },
            change: function(e, The, eOpts ){
              me.getCmp('id_periodo').getStore().load();
              me.getCmp('id_nomina').getStore().load();
            }
          }
        },
        
        {
          xtype: 'combobox',
          id: me._('id_periodo'),
          name: 'id_periodo',
          anchor: '100%',
          fieldLabel: 'Periodo',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          editable: false,
          queryMode: "local",
          displayTpl: '<tpl for=".">{codigo} {descripcion}</tpl>',
          tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
          store: {
            fields: ['id','periodo'],
            autoLoad: false,
            pageSize: 1000,
            proxy: {
              type:'ajax',
              url: 'module/nomina_periodo/',
              actionMethods: {read: "POST"},//actionMethods:'POST',
              timeout: 3600000,
              reader: {
                type: 'json',
                rootProperty: 'result',
                totalProperty:'total'
              },
              extraParams: {
                action: 'onList',
                text: '',
                id: '',
                sort: '[{"property": "codigo", "direction": "ASC"}]'
              }
            },
            listeners: {
              load: function(store, records, successful){
                me.getCmp("id_periodo").reset();
                if(records.length>0)
                  me.getCmp("id_periodo").setValue(records[records.length-1].get("id"));
                
                //if(!me.internal.ventanaSeleccionarNomina.id_periodo)
                //  me.internal.ventanaSeleccionarNomina.id_periodo
              },
              beforeload: function(store,operation,eOpts){      
                store.proxy.extraParams.tipo=me.getCmp('tipoVentanaSeleccionarNomina').getValue();
              }
            }
            
          },
          displayField: 'periodo',
          valueField: 'id',
          allowBlank: false,
          forceSelection: true,
        },
        
        {
          xtype: 'combobox',
          id: me._('id_nomina'),
          name: 'id_nomina',
          anchor: '100%',
          fieldLabel: 'Nómina',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          editable: false,
          queryMode: "local",          
          store: {
            fields: ['id','codigo_nomina'],
            autoLoad: false,
            pageSize: 1000,
            proxy: {
              type:'ajax',
              url: 'module/nomina/',
              actionMethods: {read: "POST"},//actionMethods:'POST',
              timeout: 3600000,
              reader: {
                type: 'json',
                rootProperty: 'result',
                totalProperty:'total'
              },
              extraParams: {
                action: 'onList',
                text: '',
                //id_periodo: '',
                sort: '[{"property": "codigo", "direction": "ASC"}]'
              }
            },
            listeners: {
              load: function(store, records, successful){
                //seleccionar el primer elemento del select nómina
                
                if(records.length>0)
                  me.getCmp("id_nomina").setValue(records[0].get("id"));
                return;
                if(records.length>0 && !me.getCmp('id_nomina').getValue())
                  me.getCmp("id_nomina").setValue(records[0].get("id"));
              },
              beforeload: function(store,operation,eOpts){
                //store.proxy.extraParams.id_periodo=me.getCmp('id_periodo').getValue();
                store.proxy.extraParams.tipo=me.getCmp('tipoVentanaSeleccionarNomina').getValue();
              }
            }
          },
          displayField: 'codigo_nomina',
          valueField: 'id',
          allowBlank: false,
          forceSelection: true,
          listeners:{
            change: function(){
              //me.getCmp('messageVentanaSeleccionarNomina').setText("&nbsp;",false);
              //me.getCmp('id_periodo').getStore().load();
            }
          }
        },
        {
          xtype: 'container',
          anchor: '100%',
          layout: 'hbox',
          style: 'padding-top: 25px;',
          items: [
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: 'Aceptar',
              width: 150,
              listeners: {
                click: function(){
                  if(me.getCmp("id_periodo").getValue()){
                    if(!me.getCmp("id_nomina").getValue()){
                      me.getCmp('messageVentanaSeleccionarNomina').setText("<div style='color: red;'>Debe seleccionar la nómina.</div>",false);
                      return;
                    }
                    me.onCargarNomina();
                    me.internal.ventanaSeleccionarNomina.hide();
                    me.onUpdate_MenuPersonaCambiarNomina();
                  }
                  else{
                    me.getCmp('messageVentanaSeleccionarNomina').setText("<div style='color: red;'>Debe seleccionar el periodo.</div>",false);
                  }
                }
              }              
            },
            {
              xtype: 'tbspacer',
              flex: 1
            }
          ]
        }
      ]      
    });
    //FIN VENTANA PARA CAMBIAR/SELECCIONAR NOMINA
    
    
    //VENTANA PARA VISUALIZAR REPORTES    
    me.internal.ventanaVisualizar=Ext.create('siga.windowForm', {
      title: 'Visualizar Nóminas',
      minimizable: false,      
      maximizable: false,
      modal: true,
      width: 750,
      height: 350,
      resizable: false,

      listeners: {
        beforeclose: function(w,o){
          me.internal.ventanaVisualizar.hide();
          return false;
        },
        beforeshow: function(){
          me.getCmp("publicar").hide();
          me.getCmp("txt").hide();
          switch(me.getCmp('btnVisualizar').internal.reporte) {
            case "nomina_recibo_pago":                      me.getCmp("publicar").show();   break;
            case "nomina_resumen_presupuestario_contable":  me.getCmp("txt").show();        break;
          }
          
        }
      },

      
      itemsToolbar:[
        {
          xtype: 'button',
          height: 45,
          width: 55,
          text: 'PDF',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-pdf',
          iconAlign: 'top',
          tooltip: 'Visualizar en Formato PDF',
          listeners: {
              click: function(){
                var tmp=me.getCmp('id_nomina_visualizar').getValue();
                tmp=tmp["id_nomina_visualizar"];
                
                var id_nomina="";                  
                if((typeof tmp)=="object"){                    
                  for(var i=0;i<tmp.length;i++)
                    id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                }
                else
                  id_nomina=tmp;
                
                window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina);
              }
          }
        },
        {
          xtype: 'button',
          id: me._("publicar"),
          height: 45,
          width: 55,
          text: 'Publicar',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-nomina_persona_carpeta',
          iconAlign: 'top',
          tooltip: 'Publicar en carpeta del personal',
          listeners: {
              click: function(){
                if(me.getCmp('btnVisualizar').internal.reporte!="nomina_recibo_pago") return;
                Ext.MessageBox.confirm("Publicar recibos de pago en los expedientes del personal",
                  '<b>\u00BFEst\u00e1 seguro que desea publicar los recibos de pago en los expedientes del personal?</b><br> Período: '+me.getCmp('id_periodo_visualizar').getRawValue()+'',
                  function(btn,text){
                    if(btn == 'yes'){
                      var tmp=me.getCmp('id_nomina_visualizar').getValue();
                      tmp=tmp["id_nomina_visualizar"];
                      
                      var id_nomina="";                  
                      if((typeof tmp)=="object"){                    
                        for(var i=0;i<tmp.length;i++)
                          id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                      }
                      else
                        id_nomina=tmp;                      
                      window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina+"&generar=1");
                      
                    }
                  });                
              }
          }
        },
        {
          xtype: 'button',
          id: me._("txt"),
          height: 45,
          width: 55,
          text: 'TXT',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-txt',
          iconAlign: 'top',
          tooltip: 'Texto Plano',
          listeners: {
              click: function(){
                if(me.getCmp('btnVisualizar').internal.reporte!="nomina_resumen_presupuestario_contable") return;
                var tmp=me.getCmp('id_nomina_visualizar').getValue();
                tmp=tmp["id_nomina_visualizar"];
                
                var id_nomina="";
                if((typeof tmp)=="object"){
                  for(var i=0;i<tmp.length;i++)
                    id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                }
                else
                  id_nomina=tmp;
                
                window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+"_txt.php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina);
              }
          }
        },
      ],
      
      items:[
        {
          xtype:'combobox',
          id: me._('tipo'),
          name: 'tipo',
          fieldLabel: 'Tipo de Nómina/Periodo',
          anchor: '100%',
          queryMode: "local",
          store: {
            fields: ['tipo','denominacion'],
            autoLoad: true,
            pageSize: 1000,
            proxy: {
              type:'ajax',
              url: 'module/nomina_periodo_tipo/',
              actionMethods: {read: "POST"},
              timeout: 3600000,
              reader: {
                type: 'json',
                rootProperty: 'result',
                totalProperty:'total'
              },
              extraParams: {
                action: 'onList_Activo',
                text: '',
                sort: '[{"property": "denominacion", "direction": "ASC"}]'
              }
            },
            listeners: {
              load: function(store, records, successful){
                if(records.length>0)
                  me.getCmp("tipo").setValue(records[0].get("tipo"));
              },
              beforeload: function(store,operation,eOpts){                
              }
            }
          },
          displayField: 'denominacion',
          valueField: 'tipo',
          allowBlank: false,
          forceSelection: true,
          editable: false,
          value: '',
          listeners: {
            afterrender: function(e, eOpts ){
              e.setValue("Q");
            },
            change: function(e, The, eOpts ){
              me.getCmp('id_periodo_visualizar').getStore().load();
              
              me.getCmp('id_nomina_visualizar').removeAll();
              //cargar las nominas en forma de checkbox
              Ext.Ajax.request({
                method: 'POST',
                url:'module/nomina/',
                params:{
                  action: 'onList',
                  tipo: me.getCmp('tipo').getValue(),
                  start: '0',
                  limit: 'ALL',
                  sort: '[{"property": "codigo", "direction": "ASC"}]'
                },
                success:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                  var result=result["result"];
                  for(var i=0;i<result.length;i++)
                    me.getCmp('id_nomina_visualizar').add({boxLabel: result[i]["codigo_nomina"], name: 'id_nomina_visualizar', inputValue: result[i]["id"], checked: true});
                },
                failure:function(request){
                  var result=Ext.JSON.decode(request.responseText);      
                  //me.setMessage(result.message,"red");
                }
              });
            }
          }
        },
        {
          xtype: "container",
          layout: "hbox",
          anchor: "100%",   
          items: [
            {
              xtype: "checkboxgroup",
              id: me._('id_nomina_visualizar'),
              name: 'id_nomina_visualizar',
              style: "margin-bottom: 25px;",
              flex: 1,
              fieldLabel: 'Nómina',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              columns: 1,
              vertical: true,
              items:[]
            },
            {
              xtype: 'tagfield',
              id: me._('id_periodo_visualizar'),
              name: 'id_periodo',
              width: 340,
              //hideTrigger: true,
              fieldLabel: 'Periodo',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              editable: false,
              filterPickList: true,
              multiSelect: true,
              queryMode: "local",
              tipTpl: '<b>{codigo}</b> {descripcion} <small>({fecha})</small>',
              labelTpl: '{codigo} - {descripcion}',
              tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
          
              store: {
                fields: ['id','periodo'],
                autoLoad: false,
                pageSize: 1000,
                proxy: {
                  type:'ajax',
                  url: 'module/nomina_periodo/',
                  actionMethods: {read: "POST"},
                  timeout: 3600000,
                  reader: {
                    type: 'json',
                    rootProperty: 'result',
                    totalProperty:'total'
                  },
                  extraParams: {
                    action: 'onList',
                    text: '',
                    sort: '[{"property": "codigo", "direction": "ASC"}]'
                  }
                },
                listeners: {
                  load: function(store, records, successful){
                    me.getCmp("id_periodo_visualizar").reset();
                    if(records.length>0)
                      me.getCmp("id_periodo_visualizar").setValue(records[records.length-1].get("id"));
                  },
                  beforeload: function(store,operation,eOpts){      
                    store.proxy.extraParams.tipo=me.getCmp('tipo').getValue();
                  }
                }
              },
              displayField: 'periodo',
              valueField: 'id',
              allowBlank: false,
              forceSelection: true
            },
          ]        
        },

        /*
        {
          xtype: 'container',
          anchor: '100%',
          layout: 'hbox',
          style: 'padding-top: 25px;',
          items: [
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: 'PDF',
              width: 80,
              listeners: {
                click: function(){
                  var tmp=me.getCmp('id_nomina_visualizar').getValue();
                  tmp=tmp["id_nomina_visualizar"];
                  
                  var id_nomina="";                  
                  if((typeof tmp)=="object"){                    
                    for(var i=0;i<tmp.length;i++)
                      id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                  }
                  else
                    id_nomina=tmp;
                  
                  window.open("modulo_nomina/reportes/pdf_"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina);
                }
              }              
            },
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: 'CSV',
              width: 80,
              listeners: {
                click: function(){
                  console.log(me.getCmp('id_nomina_visualizar').getValue());
                  
                  
                  
                }
              }              
            },
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: 'TXT',
              width: 80,
              listeners: {
                click: function(){
                  var tmp=me.getCmp('id_nomina_visualizar').getValue();
                  tmp=tmp["id_nomina_visualizar"];
                  var id_nomina="";
                  for(var i=0;i<tmp.length;i++)
                    id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                  
                  window.open("modulo_nomina/reportes/txt_"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina);
                }
              }              
            },
            {
              xtype: 'tbspacer',
              flex: 1
            }
          ]
        }*/
      ]      
    });
    me.internal.ventanaVisualizar.setInternal({itemSelection:0});
    //FIN VENTANA PARA VISUALIZAR REPORTES
    
    
    
    
    
    //Barra de herramientas
    me.internal.itemsToolbar=[
      {
        xtype: 'button',
        id: me._('btnSeleccionar'),
        height: 45,
        width: 55,
        text: 'Nóminas',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_seleccionar',
        iconAlign: 'top',
        tooltip: 'Seleccionar Nómina',
        listeners: {
            click: function(){
              //me.getCmp('id_nomina').getStore().load();
              //me.getCmp('id_periodo').getStore().load();
              me.internal.ventanaSeleccionarNomina.show();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnPersona'),
        width: 65,
        height: 45,
        text: 'Persona',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-persona',
        disabledCls: 'siga-btn-disabled',
        iconAlign: 'top',
        tooltip: 'Persona',
        menu: [
          {                                    
            text: 'Agregar',
            listeners: {
              click: function(){
                if(!me.onNominaSeleccionada())       
                  return;
                
                var id_nomina=me.getCmp('id_nomina').getValue();
                var id_periodo=me.getCmp('id_periodo').getValue();
                
                var selector=Ext.create("siga.windowSelect", {
                  internal:{
                    parent: {
                      fieldLabel: 'Agregar Persona',
                      internal:{
                        page:1,
                        limit: 100,
                        valueField: 'id',
                        columns: {
                          field: ["identificacion","denominacion"],
                          title: ["Código","Concepto"],
                          width: ['30%','60%'],
                          sort:  ["ASC",'ASC'],
                          align: ["left","left"],
                        },
                        url: 'module/ficha/',
                        extraParams: {
                          id_nomina: id_nomina,
                          id_periodo: id_periodo
                        },
                        actionOnList: "onList_Agregar",
                        actionOnGet: 'onGet',
                      },
                      setValue: function(id_ficha){
                        console.log("id_ficha="+id_ficha);
                        var resp=Ext.Ajax.request({
                          async: false,
                          url: 'module/ficha/',
                          params: {
                            action: "onAgregar",
                            id_nomina: id_nomina,
                            id_periodo: id_periodo,
                            id_ficha: id_ficha
                            
                          }
                        });
                        if(resp.statusText=="OK"){                    
                          me.onRecargar();
                        }                    
                        return true;
                      }
                    }
                  }
                });
                selector.show();
                selector.search();
              }
            }
          },
          {
            text: 'Quitar',
            listeners: {
              click: function(){
                  
              }
            }
          },
          {
            id: me._('btnPersonaCambiarCargo'),
            text: 'Cambiar Cargo',
            menu: []
          },
          {
            id: me._('btnPersonaCambiarNomina'),
            text: 'Cambiar Nómina',
            menu: []
          },
          {
            id: me._('btnPersonaCambiarEP'),
            text: 'Cambiar Estructura Presupuestaria',
            menu: []
          },
          {
            text: 'Administrar Fichas',
            listeners: {
              click: function(){
                siga.open("modulo_nomina/ficha");
              }
            }
          }
        ]
      },
      {
        xtype: 'button',
        id: me._('btnConcepto'),
        width: 65,
        height: 45,
        text: 'Concepto',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_concepto',
        iconAlign: 'top',
        tooltip: 'Concepto',
        menu: [
          {                                    
            text: 'Agregar',
            listeners: {
              click: function(){
                if(!me.onNominaSeleccionada())       
                  return;
                
                var ids=[];                
                for(var i=0;i<me.internal.data.concepto.length;i++)
                  ids[i]=me.internal.data.concepto[i]["id_concepto"];
                
                var selector=Ext.create("siga.windowSelect", {
                  internal: {
                    parent: {
                      fieldLabel: 'Agregar Concepto',
                      internal:{
                        page:1,
                        limit: 100,
                        valueField: 'id',
                        columns: {
                          field: ["codigo","concepto","identificador","tipo"],
                          title: ["Código","Concepto","Identificador","Tipo"],
                          width: ['10%','60%','25%','5%'],
                          sort:  ["ASC",'NULL','NULL','NULL'],
                          align: ["center","left","left","center"],
                        },
                        url: 'module/nomina_concepto_periodo/',
                        extraParams: {
                          ids: Ext.JSON.encode(ids)
                        },
                        actionOnList: "onList_Agregar",
                        actionOnGet: 'onGet',
                      },
                      setValue: function(id_concepto){
                        var id_nomina=me.getCmp('id_nomina').getValue();
                        var id_periodo=me.getCmp('id_periodo').getValue();
                        
                        var resp=Ext.Ajax.request({
                          async: false,
                          url: 'module/nomina_concepto_periodo/',
                          params: {
                            action: "onAgregar",
                            id_nomina: id_nomina,
                            id_periodo: id_periodo,
                            id_concepto: id_concepto
                          }
                        });
                        if(resp.statusText=="OK"){                    
                          me.onRecargar();
                        }                    
                        return true;
                      }
                    }
                  }
                });
                selector.show();
                selector.search();
              }
            }
          },
          {
            text: 'Quitar',
            listeners: {
              click: function(){
                if(me.internal.columnaSeleccionada==null)
                  return;                
                var id_nomina=me.getCmp('id_nomina').getValue();
                var id_periodo=me.getCmp('id_periodo').getValue();
                var id_concepto=me.internal.columnaSeleccionada.dataIndex;
                
                var resp=Ext.Ajax.request({
                  async: false,
                  url: 'module/nomina_concepto_periodo/',
                  params: {
                    action: "onQuitar",
                    id_nomina: id_nomina,
                    id_periodo: id_periodo,
                    id_concepto: id_concepto
                  }
                });
                if(resp.statusText=="OK"){                    
                  me.onRecargar();
                }
              }
            }
          },
          {
            text: 'Administrar',
            listeners: {
              click: function(){
                siga.open("nomina_concepto");
              }
            }
          }
        ]
      },  
      {
        xtype: 'button',
        id: me._('btnRecargar'),
        height: 45,
        width: 55,
        text: 'Recargar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-reload',
        iconAlign: 'top',
        tooltip: 'Recargar',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onRecargar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnNotas'),
        height: 45,
        width: 55,
        text: 'Notas',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nota',
        iconAlign: 'top',
        tooltip: 'Notas',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onNota();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnCerrarPeriodo'),
        height: 45,
        width: 55,
        text: 'Cerrar P.',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-cerrar_periodo',
        iconAlign: 'top',
        tooltip: 'Cerrar Período',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onCerrarPeriodo();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnContabilizar'),
        height: 45,
        width: 55,
        text: 'Contabilizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-contabilizar',
        iconAlign: 'top',
        tooltip: 'Contabilizar Período',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onContabilizar();
            }
        }
      },
      {
        xtype: 'tbspacer',
        flex: 1
      },
      {
        xtype: 'button',
        id: me._('btnVisualizar'),
        width: 65,
        height: 45,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-display',
        iconAlign: 'top',
        tooltip: 'Visualizar',
        internal: {
          reporte: ""
        },
        menu: [
          {                                    
            text: 'Nómina',
            listeners: {
              click: function(){                
                me.getCmp('btnVisualizar').internal.reporte="nomina";
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Nómina");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {                                    
            text: 'Recibos de Pago',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_recibo_pago";
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Recibos de Pago");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          /*{                                    
            text: 'Constancias de Trabajo',
            listeners: {
              click: function(){
                window.open("report/pdf_constancia_trabajo.php");
              }
            }
          },*/
          {
            text: 'Listado de Firmas',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_firma";
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Listado de Firmas");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Listado de Conceptos',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_concepto";
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Listado de Conceptos");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Resumen Presupuestario/Contable',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_resumen_presupuestario_contable";
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});  
                me.internal.ventanaVisualizar.setTitle("Visualizar - Resumen Presupuestario/Contable");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Resumen de Deducciones y Aportes Patronales',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_aportes";
                me.internal.ventanaVisualizar.setInternal({itemSelection: null});             
                me.internal.ventanaVisualizar.setTitle("Visualizar - Resumen de Deducciones y Aportes Patronales");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Comprobante Presupuestario/Contable Generado',
            listeners: {
              click: function(){
                if(!me.onNominaSeleccionada()) return;
                if(me.internal.contabilizado==null){
                  Ext.Msg.alert(me.title,"La nómina no se encuentra contabilizada.");
                  return;
                }
                window.open("report/comprobante.php?id="+me.internal.contabilizado);
              }
            }
          }
        ]
      },
    ];
    //FIN Barra de herramientas
    
    
    me.internal.items=[      
      {
        xtype: 'gridpanel',
        id: me._('gridList'),
        border: 0,
        preventHeader: true,
        width: 400,
        viewConfig: {
          markDirty: false
        },/*
        features: [{
          ftype: 'summary'
        }],*/
        plugins: {
          ptype: 'cellediting',
          clicksToEdit: 2,
          listeners: {
            edit: function(editor, e){
              if(e.value*1.0==e.originalValue*1.0)
                return;
              _tmp=Ext.Ajax.request({
                async: false,
                url:"module/nomina/",
                params: {
                  action: 'onSave',
                  id_nomina: me.getCmp("id_nomina").getValue(),
                  id_periodo: me.getCmp("id_periodo").getValue(),
                  data: Ext.JSON.encode([{id_ficha: e.record.get("id_ficha"), id_concepto: e.field, valor: e.value}])
                }
              });
              
              if(_tmp.statusText=="OK"){
                var data=Ext.JSON.decode(_tmp.responseText);
                me.onActualizarFilaFicha(e.record,data[0]);
              }
            }
          }
        },
        /*
        plugins: [
          Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            pluginId: me._("celledit"),
            listeners: {
              edit: function(editor, e){
                if(e.value*1.0==e.originalValue*1.0)
                  return;
                _tmp=Ext.Ajax.request({
                  async: false,
                  url:"module/nomina/",
                  params: {
                    action: 'onSave',
                    id_nomina: me.getCmp("id_nomina").getValue(),
                    id_periodo: me.getCmp("id_periodo").getValue(),
                    data: Ext.JSON.encode([{id_ficha: e.record.get("id_ficha"), id_concepto: e.field, valor: e.value}])
                  }
                });
                
                if(_tmp.statusText=="OK"){
                  var data=Ext.JSON.decode(_tmp.responseText);
                  me.onActualizarFilaFicha(e.record,data[0]);
                }
              }
            }
          })
        ],*/
        selType : 'cellmodel',
        columnLines: true,
        enableLocking: true,
        height: 380,
        columns: [],
        listeners: {
          columnmove: function(ct, column, fromIdx, toIdx, eOpts ){
            
            
            //console.log(fromIdx+" -> "+toIdx);
            
            
            
          },
          cellcontextmenu: function(dataview, td, cellIndex, record, tr, rowIndex, e, eOpts ){
            e.stopEvent();
            if(me.internal.cerrado==true){
              return;
            }
            
            var columna=dataview.getGridColumns();
            var id_concepto=columna[cellIndex].dataIndex;
            //verificar si la columna corresponde a un concepto, si es un numero es concepto
            if(!Ext.isNumeric(id_concepto)) {
              return;
            }
            var id_nomina=me.getCmp('id_nomina').getValue();
            var id_periodo=me.getCmp('id_periodo').getValue();
            var id_ficha=record.get('id_ficha');            

            var menu_celda = Ext.create('Ext.menu.Menu', {              
              items: [
                {
                  text: 'Agregar',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAdd',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode([id_ficha]),
                        id_concepto: id_concepto
                      }
                    });
                    
                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);                      
                      me.onActualizarFilaFicha(record,data[0]); 
                    }
                  }
                },
                {
                  text: 'Quitar',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onRemove',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode([id_ficha]),
                        id_concepto: id_concepto
                      }
                    });
                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);                      
                      me.onActualizarFilaFicha(record,data[0]); 
                    }
                  }
                },
                {
                  xtype: "menuseparator"
                },
                {
                  text: 'Agregar a Todos',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAdd',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode(['*']),
                        id_concepto: id_concepto
                      }
                    });
                    
                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.getCmp('gridList').getStore().load();
                    }
                  }
                },
                
              ]
            });
            menu_celda.show();  //e.getXY()          
            menu_celda.showAt(Ext.get(td.id).getX(), Ext.get(td.id).getY()-menu_celda.getHeight());
          }
        }
      }
    ];
    
    
    
    
    me.dockedItems=[
      {
        xtype: 'toolbar',
        id: me._('toolbarMain'),
        style: 'margin: 0px; padding: 0px;',
        flex: 1,
        dock: 'top',
        items: me.internal.itemsToolbar
      },
      {
        xtype: 'pagingtoolbar',
        id: me._('pagingList'),
        afterPageText: 'de {0}',
        beforePageText: 'P\u00e1gina',
        displayInfo: true,
        displayMsg: 'Mostrando {0} - {1} de {2}',
        emptyMsg: 'No hay datos que mostrar',
        firstText: 'Primera p\u00e1gina',
        dock: 'bottom'
      },
      {
        xtype: 'toolbar',
        style: 'margin: 0px; padding: 0px;',
        flex: 1,
        dock: 'bottom',
        items: [ 
          {
            xtype: 'label',
            id: me._('lblNominaActual'),
            style:'padding: 2px;',
            html: '&nbsp;'
          },
          {
            xtype: 'tbspacer',
            flex: 1
          }
        ]
      },      
    ];
    
    me.items=[
      {
        xtype: 'form',
        frame: false,
        id: me._('tab_data'),
        frameHeader: false,
        autoScroll:true,
        layout: 'fit',
        defaults: {
          style:'margin: 0px 0px 0px 0px;',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          hideLabel: false,
          width: 200
        },
        items: me.internal.items                    
      }
    ];
    
    
    
    me.callParent(arguments);
    //me.setAccess(define['modulo_nomina/hoja_trabajo->access']);
    
    
    
    
    
  },
  
  onLoad_MenuPersonaCambiarCargo: function(){
    var me=this;
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onList_Cargo',
        text: '',
        start: 0,
        limit: 'ALL',
        sort: '[{"property": "denominacion", "direction": "ASC"}]'
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        result=result["result"];

        for(var i=0;i<result.length;i++){
          me.getCmp("btnPersonaCambiarCargo").menu.add({
            text: result[i]["denominacion"],
            internal:{id: result[i]["id"]},
            listeners: {
              click: function(el){            
                var seleccion=me.getCmp("gridList").getSelection();
                if(seleccion.length==0){
                  Ext.MessageBox.alert("Persona - Cambiar Cargo","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
                  return;
                }
                if(seleccion.length!=1){
                  Ext.MessageBox.alert("Persona - Cambiar Cargo","<b>Debe seleccionar solo una persona del listado.</b>");
                  return;
                }
                
                Ext.MessageBox.confirm(
                  "Persona - Cambiar Cargo",
                  '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["persona"]+'" para el cargo: "'+el.text+'"?</b><br> ',
                  function(btn,text){
                    if(btn == 'yes'){
                      var id_ficha=seleccion[0].data["id_ficha"];
                      var id_periodo=me.getCmp("id_periodo").getValue();                      
                      var id_cargo=el.internal.id;
                      
    
                      me.getCmp('btnPersona').setDisabled(true);
                      me.getCmp('btnConcepto').setDisabled(true);
                      me.getCmp('btnCerrarPeriodo').setDisabled(true);
                      me.getCmp('btnContabilizar').setDisabled(true);
                      
                      Ext.Ajax.request({
                        method: 'POST',
                        url:'module/nomina/',
                        params:{
                          action: 'onPersona_CambiarCargo',
                          id_ficha: id_ficha,
                          id_periodo: id_periodo,
                          id_cargo: id_cargo
                        },
                        success:function(request){
                          var result=Ext.JSON.decode(request.responseText);                                    
                          Ext.MessageBox.alert("Persona - Cambiar Cargo",result["message"]);
                          me.onRecargar();
                        },
                        failure:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                        }
                      });
                    }
                  });
                
              }
            }
          });
        }
        
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
    
    
    
  },
  
  onLoad_MenuPersonaCambiarEP: function(){
    var me=this;
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/estructura_presupuestaria/',
      params:{
        action: 'onList_AP',
        text: '',
        start: 0,
        limit: 'ALL',
        sort: '[{"property": "estructura_presupuestaria", "direction": "ASC"}]'
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        result=result["result"];

        for(var i=0;i<result.length;i++){
          me.getCmp("btnPersonaCambiarEP").menu.add({
            text: result[i]["estructura_presupuestaria"],
            tooltip: "<b>Acción Centralizada:</b> "+result[i]["denominacion_centralizada"]+"<br><b>Específica:</b> "+result[i]["denominacion_especifica"]+"<br><b>Sub-Específica:</b> "+result[i]["denominacion_subespecifica"],
            internal:{id: result[i]["id_accion_subespecifica"]},
            listeners: {
              click: function(el){            
                var seleccion=me.getCmp("gridList").getSelection();
                if(seleccion.length==0){
                  Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
                  return;
                }
                if(seleccion.length!=1){
                  Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria","<b>Debe seleccionar solo una persona del listado.</b>");
                  return;
                }
                
                Ext.MessageBox.confirm(
                  "Persona - Cambiar Estructura Presupuestaria",
                  '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["persona"]+'" para la Estructura Presupuestaria: "'+el.text+'"?</b><br> ',
                  function(btn,text){
                    if(btn == 'yes'){
                      var id_ficha=seleccion[0].data["id_ficha"];
                      var id_periodo=me.getCmp("id_periodo").getValue();                      
                      var id_accion_subespecifica=el.internal.id;
    
                      me.getCmp('btnPersona').setDisabled(true);
                      me.getCmp('btnConcepto').setDisabled(true);
                      me.getCmp('btnCerrarPeriodo').setDisabled(true);
                      me.getCmp('btnContabilizar').setDisabled(true);
                      
                      Ext.Ajax.request({
                        method: 'POST',
                        url:'module/nomina/',
                        params:{
                          action: 'onPersona_CambiarEP',
                          id_ficha: id_ficha,
                          id_periodo: id_periodo,
                          id_accion_subespecifica: id_accion_subespecifica
                        },
                        success:function(request){
                          var result=Ext.JSON.decode(request.responseText);                                    
                          Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria",result["message"]);
                          me.onRecargar();
                        },
                        failure:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                        }
                      });
                      
                    }
                  });
                
              }
            }
          });
        }
        
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
    
    
    
  },
  
  onUpdate_MenuPersonaCambiarNomina: function(){
    var me=this;
    var nomina=me.getCmp("id_nomina").getStore().getData().items;
    me.getCmp("btnPersonaCambiarNomina").menu.removeAll();
    for(var i=0;i<nomina.length;i++){
      if(me.getCmp("id_nomina").getValue()==nomina[i].data["id"]) continue; //no mostrar la misma nómina seleccionada
      
      me.getCmp("btnPersonaCambiarNomina").menu.add({
        text: nomina[i].data["codigo_nomina"],
        internal:{id: nomina[i].data["id"]},
        listeners: {
          click: function(el){            
            var seleccion=me.getCmp("gridList").getSelection();
            if(seleccion.length==0){
              Ext.MessageBox.alert("Persona - Cambiar Nómina","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
              return;
            }
            if(seleccion.length!=1){
              Ext.MessageBox.alert("Persona - Cambiar Nómina","<b>Debe seleccionar solo una persona del listado.</b>");
              return;
            }
            
            Ext.MessageBox.confirm(
              "Persona - Cambiar Nómina",
              '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["persona"]+'" para la nomina: "'+el.text+'"?</b><br> ',
              function(btn,text){
                if(btn == 'yes'){
                  var id_ficha=seleccion[0].data["id_ficha"];
                  var id_periodo=me.getCmp("id_periodo").getValue();
                  var id_nomina_anterior=me.getCmp("id_nomina").getValue();
                  var id_nomina=el.internal.id;

                  me.getCmp('btnPersona').setDisabled(true);
                  me.getCmp('btnConcepto').setDisabled(true);
                  me.getCmp('btnCerrarPeriodo').setDisabled(true);
                  me.getCmp('btnContabilizar').setDisabled(true);
                  
                  Ext.Ajax.request({
                    method: 'POST',
                    url:'module/nomina/',
                    params:{
                      action: 'onPersona_CambiarNomina',
                      id_ficha: id_ficha,
                      id_periodo: id_periodo,
                      id_nomina: id_nomina,
                      id_nomina_anterior: id_nomina_anterior
                    },
                    success:function(request){
                      var result=Ext.JSON.decode(request.responseText);                                    
                      Ext.MessageBox.alert("Persona - Cambiar Nómina",result["message"]);
                      me.onRecargar();
                    },
                    failure:function(request){
                      var result=Ext.JSON.decode(request.responseText);
                    }
                  });
                }
              });
            
          }
        }
      });
    }
  },
  
  
  init: function(){
    var me=this;
    me.onLoad_MenuPersonaCambiarCargo();
    me.onLoad_MenuPersonaCambiarEP();
    me.maximize();    
  },
  
  setAccess: function(_access){
    var me=this;        
    
  },   
  
  //_: function(id){
  //  var me=this;
  //  return me.id+"-"+id;
  //},
  //
  //getCmp: function(id){
  //  var me=this;
  //  return Ext.getCmp(me._(String(id)));
  //},
  
  //
  //setMessage: function(_text,_color,_time){
  //  var me=this;
  //  
  //  window.clearTimeout(me.internal.messageTimeOutHandler);
  //  
  //  if(!_text){
  //    me.getCmp('message').setText('&nbsp;',false);
  //    return;
  //  }        
  //  if(!_color)
  //    _color="black";
  //  me.getCmp('message').setText("<div style='color: "+_color+";'>"+_text+"</div>",false);
  //  if(!_time)
  //    _time=10000;
  //  me.internal.messageTimeOutHandler=setTimeout(function(){
  //    me.setMessage();
  //    },_time);
  //},
  
  onNominaSeleccionada: function(){
    var me=this;
    if(!me.getCmp('id_periodo').getValue()) {
      Ext.Msg.alert(me.title,"Primero debe seleccionar la nómina y el periodo.");
      return false;
    }
    return true;
  },
  
  onRecargar: function(){
    var me=this;
    //me.getCmp('gridList').store.load();
    me.onCargarNomina();
  },
  
  onActualizarFilaFicha: function(registro, data){
    var me=this;
    var columna=me.getCmp('gridList').headerCt.getGridColumns();
    //si la data devuelta no corresponde al registro, salir
    if(registro.get("id_ficha")!=data["id_ficha"])
      return;
    
    //limpiar columnas numericas, (datos calculados)
    for(var i=0;i<columna.length;i++){
      if(columna[i].dataIndex=="n" || columna[i].dataIndex=="persona") continue;
      registro.data[columna[i].dataIndex]="";    
    }
    
    //colocar la información actualizada
    for(var i=0;i<data["concepto"].length;i++){
      registro.data[data["concepto"][i]["id"]]=data["concepto"][i]["valor_final"];
      if(data["concepto"][i]["tipo"]=="AP") 
        registro.data[data["concepto"][i]["id"]+"_ap"]=data["concepto"][i]["valor_final_ap"];
    }
    
    //colocar los totales
    registro.data["total_asignacion"]=data["total_asignacion"];    
    registro.data["total_deduccion"]=data["total_deduccion"];    
    registro.data["total_neto"]=data["total_neto"];    
    registro.data["total_ap"]=data["total_ap"];
    
    //actualizar la vista
    //me.getCmp('gridList').getView().refresh();
    var index=me.getCmp('gridList').getStore().indexOf(registro);
    me.getCmp('gridList').getView().refreshNode(index);
    
  },
  
  /*
  configurarColumnas: function(){
    var me=this;
    
    
    var style='';
    var style_tipo='';
    var texto_tipo='';
    var title='';
    
    var columns=[];
    var columns_calc=[];
    var columns_asig=[];
    var columns_deduc=[];
    var columns_ap=[];
    var fields=["id_ficha","persona","total_asignacion","total_deduccion","total_neto","mensaje","total_ap"];
    
    columns.push(
      {
          xtype: 'rownumberer',
          dataIndex: 'n',
          text: "<b>Nº</b>",
          width: 30,
          sortable: false,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false,
          align: "center"
      }
    );        
    columns.push(
        {
          xtype: 'templatecolumn',
          dataIndex: "persona",
          text: "<b>PERSONA</b>",
          tpl: "<div style='width: 100%;'><div style='width:80%; float: left;'>{persona}</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' onclick=\"siga.getCmp('"+Ext.getClassName(me)+"').alert('{mensaje}');\" title=\"alert('{mensaje}');\" /></tpl></div>",
          width: 220,
          menuDisabled: true,
          sortable: false,
          height: 250,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );
    
    for(var i=0;i<me.internal.data.concepto.length;i++){
      style='';
      style_tipo='';
      texto_tipo='';
      
      fields.push(me.internal.data.concepto[i]["id_concepto"]);
      
      if(me.internal.data.concepto[i]["indefinido"]){
        style='error_formula';
        title=""+me.internal.data.concepto[i]["indefinido"]+" SE ENCUENTRA INDEFINIDA EN LA FORMULA.";
      }
      
      var _editor=null;
      var _cls_formula="";
      if(!me.internal.data.concepto[i]["es_formula"] && me.internal.cerrado==false) {            
        _editor={
          xtype: 'numberfield',
          minValue: 0,
          allowBlank: false,
          allowDecimals: true,
          decimalPrecision: 2,
          decimalSeparator: '.',
          hideTrigger: true,
          keyNavEnabled: false,
          mouseWheelEnabled: false,
          fieldStyle: "font-size: 9px; padding: 0px; text-align: right;"
        };
      }
      else{
        _cls_formula=" formula";
      }
      
      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":
          texto_tipo='ASIGNACIÓN';
          texto_tipo='[A]';
          style_tipo='formula_tipo_asignacion';   
          break; 
        case "RD":
          style_tipo='formula_tipo_asignacion';
          texto_tipo='REINTEGRO DEDUCCÓN';
          texto_tipo='[RD]';
          break;
        case "D":
          texto_tipo='DEDUCCIÓN';              
          texto_tipo='[D]';              
          style_tipo='formula_tipo_deduccion';
          break;
        case "AP":
          texto_tipo='APORTE PATRONAL';
          texto_tipo='[AP]';
          style_tipo='formula_tipo_deduccion';
        case "RA":
          texto_tipo='REINTEGRO ASIGNACIÓN';            
          texto_tipo='[RA]';
          style_tipo='formula_tipo_deduccion';
          break;
      }
      
      var column_tmp=
        {
          xtype: 'gridcolumn',
          dataIndex: me.internal.data.concepto[i]["id_concepto"],
          html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' onclick=\"siga.window.getCmp('"+me.self.getName()+"').onEditarConcepto("+me.internal.data.concepto[i]["id_concepto"]+")\" /></div><div class='text_vertical'>"+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_indeficador'>"+me.internal.data.concepto[i]["identificador"]+"</div><div class='text_formula "+style+"' title='"+title+"' onclick='alert(\""+me.internal.data.concepto[i]["id"]+"\")'>"+me.internal.data.concepto[i]["definicion"]+"</div></div>",
          width: 53,
          menuDisabled: true,
          sortable: false,
          height: 250,
          locked: false,
          lockable: false,
          resizable: false,
          align: 'right',
          cls: "hoja_trabajo_header",
          tdCls: 'hoja_trabajo_cell'+_cls_formula,
          editor: _editor,
          summaryType: function(record, data_index){
            var suma=0;
            for(var s=0;s<record.length;s++){
              v=record[s].get(data_index);
              if(v=="") continue;
              suma+=v*1.00;
            }
            return suma;
          },
          summaryRenderer: function(value, summaryData, dataIndex) {            
            return Ext.util.Format.number(value, '0,0.00'); 
          },
          renderer: function(value) {
            return Ext.util.Format.number(value, '0,0.00');
          },
          listeners: {
            headerclick: function(ct, column, e, t, eOpts){
              if(me.internal.cerrado==true){
                return;
              }
              //console.log(ct);
              //column.tdCls="nomina_columna_seleccionada";
              if(me.internal.columnaSeleccionada!=null){
                me.internal.columnaSeleccionada.removeCls("nomina_columna_seleccionada");
                if(me.internal.columnaSeleccionada.dataIndex==column.dataIndex){
                  me.internal.columnaSeleccionada=null;
                  return;
                }
              }
              column.addCls("nomina_columna_seleccionada");
              me.internal.columnaSeleccionada=column;
            }
          }
        };
      
      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":        
        case "RD":
          columns_asig.push(column_tmp);
          break;
        case "AP":
          fields.push(me.internal.data.concepto[i]["id_concepto"]+"_ap");
          columns_ap.push({
            xtype: 'gridcolumn',
            dataIndex: me.internal.data.concepto[i]["id_concepto"]+"_ap",
            //disabled: true,
            html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' /></div><div class='text_vertical'>"+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_formula "+style+"' title='"+title+"'>"+me.internal.data.concepto[i]["definicion_ap"]+"</div></div>",
            width: 53,
            menuDisabled: true,
            sortable: false,
            height: 250,
            locked: false,
            lockable: false,
            resizable: false,
            align: 'right',
            cls: "hoja_trabajo_header",
            tdCls: 'hoja_trabajo_cell'+_cls_formula,
            summaryType: function(record, data_index){
              var suma=0;
              for(var s=0;s<record.length;s++){
                v=record[s].get(data_index);
                if (v=="") continue;
                suma+=v*1.00;
              }
              return suma;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {            
              return Ext.util.Format.number(value, '0,0.00'); 
            },
            renderer: function(value) {
              return Ext.util.Format.number(value, '0,0.00');
            }
          });
        case "D":
        case "RA":
          columns_deduc.push(column_tmp);          
          break;
        default:
          columns_calc.push(column_tmp);
      }
    }
    
    if(columns_calc.length>0) 
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group_none", menuDisabled: true, text: "", sortable: false, draggable: false, sealed: true, columns: columns_calc});
    
     
    if(columns_asig.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, text: columns_asig.length>1?"ASIGNACIONES":"ASIG.", sortable: false, draggable: false, sealed: true, columns: columns_asig});

    //columna de totales
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_asignacion",
        html: "<div class='text_vertical'>TOTAL ASIGNACIONES</div>",
        width: 60,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_asignacion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    if(columns_deduc.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", menuDisabled: true, text: columns_deduc.length>1?"DEDUCCIONES":"DEDUC.", style: 'font-size: 9px; font-weight: bold;', sortable: false, draggable: false, sealed: true, columns: columns_deduc});

    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_deduccion",
        html: "<div class='text_vertical'>TOTAL DEDUCCIONES</div>",
        width: 60,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_deduccion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_neto",
        html: "<div class='text_vertical'>TOTAL</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    if(columns_ap.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, disabled: true, text: columns_ap.length>2?"APORTE PATRONAL":"AP", sortable: false, draggable: false, sealed: true, columns: columns_ap});
    
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_ap",
        html: "<div class='text_vertical'>TOTAL APORTES</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    
    return {fields: fields, columns: columns};
    
  },
  */
  
  /*
  onCargarNomina: function(){
    var me=this;
    var id_periodo=me.getCmp('id_periodo').getValue();
    var id_nomina=me.getCmp('id_nomina').getValue();
    
    if(!me.onNominaSeleccionada())       
      return;
    
    me.getCmp('btnPersona').setDisabled(true);
    me.getCmp('btnConcepto').setDisabled(true);
    me.getCmp('btnCerrarPeriodo').setDisabled(true);
    me.getCmp('btnNotas').setDisabled(true);
    me.getCmp('btnContabilizar').setDisabled(true);
    
    me.internal.columnaSeleccionada=null;
    me.internal.cerrado=null;
    me.getCmp('lblNominaActual').setText("<div style='font-size: 11px; line-height: 120%;'><b>Nómina:</b> "+me.getCmp('id_nomina').getRawValue()+". <b>Periodo:</b> "+me.getCmp('id_periodo').getRawValue()+".</div>",false);
    
    
    me.getCmp('gridList').getStore().removeAll(true);
    me.getCmp('gridList').reconfigure();
    
    

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onGet',
        id_nomina: id_nomina,
        id_periodo: id_periodo
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        
        me.internal.cerrado=result["cerrado"]=='t'?true:false;
        me.internal.contabilizado=result["contabilizado"];
        me.internal.data.concepto=result["concepto"];
        me.internal.data.ficha=result["ficha"];
        
        me.getCmp('btnPersona').setDisabled(me.internal.cerrado);
        me.getCmp('btnConcepto').setDisabled(me.internal.cerrado);
        me.getCmp('btnCerrarPeriodo').setDisabled(me.internal.cerrado);
        me.getCmp('btnNotas').setDisabled(me.internal.cerrado);
        me.getCmp('btnContabilizar').setDisabled(me.internal.contabilizado==null?false:true);

        
        var config=me.configurarColumnas();
        
        var _data=[];
        for(var i=0;i<result["ficha"].length;i++){
          _data[i]={};
          _data[i]["mensaje"]="";
          _data[i]["id_ficha"]=result["ficha"][i]["id"];
          _data[i]["persona"]=result["ficha"][i]["nombre_apellido"]+" <span class='nomina_cargo'>("+result["ficha"][i]["cargo"]+")</span>";
          
          for(var j=0;j<result["ficha"][i]["concepto"].length;j++){
            _data[i][result["ficha"][i]["concepto"][j]["id"]]=result["ficha"][i]["concepto"][j]["valor_final"];
            if(result["ficha"][i]["concepto"][j]["tipo"]=="AP")
              _data[i][result["ficha"][i]["concepto"][j]["id"]+"_ap"]=result["ficha"][i]["concepto"][j]["valor_final_ap"];
              
            //verificar si el concepto existe en config.fields
            var encontro=false;
            for(var k=0;k<config.fields.length;k++){
              //console.log(result["ficha"][i]["concepto"][j]["id"]+"=="+config.fields[k]);
              if(result["ficha"][i]["concepto"][j]["id"]==config.fields[k]) {
                encontro=true;
                break;
              }
            }
            if(!encontro){
              _data[i]["mensaje"]+=result["ficha"][i]["concepto"][j]["codigo"]+" "+result["ficha"][i]["concepto"][j]["concepto"]+".<br>";
            }            
          }
          if(_data[i]["mensaje"]){
            _data[i]["mensaje"]="<b>La persona tiene los siguientes conceptos, lo cuales no se encuentran asociados a la nómina actual:<br></b><p style=\\'font-size: 10px; padding-left: 20px;\\'>"+_data[i]["mensaje"]+"</p>";
          }
          
          _data[i]["total_asignacion"]=result["ficha"][i]["total_asignacion"];
          _data[i]["total_deduccion"]=result["ficha"][i]["total_deduccion"];
          _data[i]["total_neto"]=result["ficha"][i]["total_neto"];
          _data[i]["total_ap"]=result["ficha"][i]["total_ap"];
        }

        me.getCmp('gridList').reconfigure({
          fields: config.fields,
          data: _data
        },config.columns);
        
        me.getCmp('pagingList').bindStore(me.getCmp('gridList').getStore());

        
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText); 
      }
    });
  },*/
  
    configurarColumnas: function(){
    var me=this;
    
    
    var style='';
    var style_tipo='';
    var texto_tipo='';
    var title='';
    
    var columns=[];
    var columns_calc=[];
    var columns_asig=[];
    var columns_deduc=[];
    var columns_ap=[];
    var fields=["id_ficha","persona","total_asignacion","total_deduccion","total_neto","mensaje","total_ap"];
    
    columns.push(
      {
          xtype: 'rownumberer',
          dataIndex: 'n',
          text: "<b>Nº</b>",
          width: 30,
          sortable: false,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false,
          align: "center"
      }
    );        
    columns.push(
        {
          xtype: 'templatecolumn',
          text: "<b>PERSONA</b>",
          tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido} <span class='nomina_cargo'>({cargo})</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
          width: 220,
          menuDisabled: true,
          sortable: false,
          height: 250,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );

    columns.push(
        {
          xtype: 'templatecolumn',
          html: "<div class='text_vertical'><b>ESTRUCTURA<br>PRESUPUESTARIA</b></div>",
          //dataIndex: 'estructura_presupuestaria',
          //tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido} <span class='nomina_cargo'>({cargo})</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
          tpl: "<div style='width: 100%;' data-qtip='<b>Acción Centralizada:</b> {denominacion_centralizada}<br><b>Específica:</b> {denominacion_especifica}<br><b>Sub-Específica:</b> {denominacion_subespecifica}'><span class='nomina_estructura_presupuestaria'>{estructura_presupuestaria}</span></div>",
          width: 95,
          menuDisabled: true,
          sortable: false,
          height: 250,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );
    
    for(var i=0;i<me.internal.data.concepto.length;i++){
      style='';
      style_tipo='';
      texto_tipo='';
      
      fields.push(me.internal.data.concepto[i]["id_concepto"]);
      
      if(me.internal.data.concepto[i]["indefinido"]){
        style='error_formula';
        title=""+me.internal.data.concepto[i]["indefinido"]+" SE ENCUENTRA INDEFINIDA EN LA FORMULA.";
      }
      
      var _editor=null;
      var _cls_formula="";
      if(!me.internal.data.concepto[i]["es_formula"] && me.internal.cerrado==false) {            
        _editor={
          xtype: 'numberfield',
          minValue: 0,
          allowBlank: false,
          allowDecimals: true,
          decimalPrecision: 2,
          decimalSeparator: '.',
          hideTrigger: true,
          keyNavEnabled: false,
          mouseWheelEnabled: false,
          fieldStyle: "font-size: 9px; padding: 0px; text-align: right;"
        };
      }
      else{
        _cls_formula=" formula";
      }
      
      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":
          texto_tipo='ASIGNACIÓN';
          texto_tipo='[A]';
          style_tipo='formula_tipo_asignacion';   
          break; 
        case "RD":
          style_tipo='formula_tipo_asignacion';
          texto_tipo='REINTEGRO DEDUCCÓN';
          texto_tipo='[RD]';
          break;
        case "D":
          texto_tipo='DEDUCCIÓN';              
          texto_tipo='[D]';              
          style_tipo='formula_tipo_deduccion';
          break;
        case "AP":
          texto_tipo='APORTE PATRONAL';
          texto_tipo='[AP]';
          style_tipo='formula_tipo_deduccion';
        case "RA":
          texto_tipo='REINTEGRO ASIGNACIÓN';            
          texto_tipo='[RA]';
          style_tipo='formula_tipo_deduccion';
          break;
      }
      
      var column_tmp=
        {
          xtype: 'gridcolumn',
          dataIndex: me.internal.data.concepto[i]["id_concepto"],
          html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' onclick=\"siga.window.getCmp('"+me.self.getName()+"').onEditarConcepto("+me.internal.data.concepto[i]["id_concepto"]+")\" /></div><div class='text_vertical'>"+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_indeficador'>"+me.internal.data.concepto[i]["identificador"]+"</div><div class='text_formula "+style+"' title='"+title+"' onclick='alert(\""+me.internal.data.concepto[i]["id"]+"\")'>"+me.internal.data.concepto[i]["definicion"]+"</div></div>",
          width: 53,
          menuDisabled: true,
          sortable: false,
          height: 250,
          locked: false,
          lockable: false,
          resizable: false,
          align: 'right',
          cls: "hoja_trabajo_header",
          tdCls: 'hoja_trabajo_cell'+_cls_formula,
          editor: _editor,
          summaryType: function(record, data_index){
            var suma=0;
            for(var s=0;s<record.length;s++){
              v=record[s].get(data_index);
              if(v=="") continue;
              suma+=v*1.00;
            }
            return suma;
          },
          summaryRenderer: function(value, summaryData, dataIndex) {            
            return Ext.util.Format.number(value, '0,0.00'); 
          },
          renderer: function(value) {
            return Ext.util.Format.number(value, '0,0.00');
          },
          listeners: {
            headerclick: function(ct, column, e, t, eOpts){
              if(me.internal.cerrado==true){
                return;
              }
              //console.log(ct);
              //column.tdCls="nomina_columna_seleccionada";
              if(me.internal.columnaSeleccionada!=null){
                me.internal.columnaSeleccionada.removeCls("nomina_columna_seleccionada");
                if(me.internal.columnaSeleccionada.dataIndex==column.dataIndex){
                  me.internal.columnaSeleccionada=null;
                  return;
                }
              }
              column.addCls("nomina_columna_seleccionada");
              me.internal.columnaSeleccionada=column;
            }
          }
        };
      
      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":        
        case "RD":
          columns_asig.push(column_tmp);
          break;
        case "AP":
          fields.push(me.internal.data.concepto[i]["id_concepto"]+"_ap");
          columns_ap.push({
            xtype: 'gridcolumn',
            dataIndex: me.internal.data.concepto[i]["id_concepto"]+"_ap",
            //disabled: true,
            html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' /></div><div class='text_vertical'>"+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_formula "+style+"' title='"+title+"'>"+me.internal.data.concepto[i]["definicion_ap"]+"</div></div>",
            width: 53,
            menuDisabled: true,
            sortable: false,
            height: 250,
            locked: false,
            lockable: false,
            resizable: false,
            align: 'right',
            cls: "hoja_trabajo_header",
            tdCls: 'hoja_trabajo_cell'+_cls_formula,
            summaryType: function(record, data_index){
              var suma=0;
              for(var s=0;s<record.length;s++){
                v=record[s].get(data_index);
                if (v=="") continue;
                suma+=v*1.00;
              }
              return suma;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {            
              return Ext.util.Format.number(value, '0,0.00'); 
            },
            renderer: function(value) {
              return Ext.util.Format.number(value, '0,0.00');
            }
          });
        case "D":
        case "RA":
          columns_deduc.push(column_tmp);          
          break;
        default:
          columns_calc.push(column_tmp);
      }
    }
    
    if(columns_calc.length>0) 
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group_none", menuDisabled: true, text: "", sortable: false, draggable: false, sealed: true, columns: columns_calc});
    
     
    if(columns_asig.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, text: columns_asig.length>1?"ASIGNACIONES":"ASIG.", sortable: false, draggable: false, sealed: true, columns: columns_asig});

    //columna de totales
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_asignacion",
        html: "<div class='text_vertical'>TOTAL ASIGNACIONES</div>",
        width: 60,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_asignacion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    if(columns_deduc.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", menuDisabled: true, text: columns_deduc.length>1?"DEDUCCIONES":"DEDUC.", style: 'font-size: 9px; font-weight: bold;', sortable: false, draggable: false, sealed: true, columns: columns_deduc});

    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_deduccion",
        html: "<div class='text_vertical'>TOTAL DEDUCCIONES</div>",
        width: 60,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_deduccion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_neto",
        html: "<div class='text_vertical'>TOTAL</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    if(columns_ap.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, disabled: true, text: columns_ap.length>2?"APORTE PATRONAL":"AP", sortable: false, draggable: false, sealed: true, columns: columns_ap});
    
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_ap",
        html: "<div class='text_vertical'>TOTAL APORTES</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 250,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00'); 
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );
    
    
    return {fields: fields, columns: columns};
    
  },
  
  onCargarNomina: function(){
    var me=this;
    var id_periodo=me.getCmp('id_periodo').getValue();
    var id_nomina=me.getCmp('id_nomina').getValue();
    
    if(!me.onNominaSeleccionada())       
      return;
    
    me.getCmp('btnPersona').setDisabled(true);
    me.getCmp('btnConcepto').setDisabled(true);
    me.getCmp('btnCerrarPeriodo').setDisabled(true);
    me.getCmp('btnNotas').setDisabled(true);
    me.getCmp('btnContabilizar').setDisabled(true);
    
    me.internal.columnaSeleccionada=null;
    me.internal.cerrado=null;
    me.getCmp('lblNominaActual').setText("<div style='font-size: 11px; line-height: 120%;'><b>Nómina:</b> "+me.getCmp('id_nomina').getRawValue()+". <b>Periodo:</b> "+me.getCmp('id_periodo').getRawValue()+".</div>",false);
    
    me.getCmp('gridList').getStore().removeAll(true);
    me.getCmp('gridList').reconfigure();

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onListConceptoPeriodo',
        id_nomina: id_nomina,
        id_periodo: id_periodo
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        
        me.internal.cerrado=result["cerrado"]=='t'?true:false;
        me.internal.contabilizado=result["contabilizado"];
        me.internal.data.concepto=result["concepto"];
        me.internal.data.ficha=result["ficha"];
        
        me.getCmp('btnPersona').setDisabled(me.internal.cerrado);
        me.getCmp('btnConcepto').setDisabled(me.internal.cerrado);
        me.getCmp('btnCerrarPeriodo').setDisabled(me.internal.cerrado);
        me.getCmp('btnNotas').setDisabled(me.internal.cerrado);
        me.getCmp('btnContabilizar').setDisabled(me.internal.contabilizado==null?false:true);

        
        var config=me.configurarColumnas();
        
        var store= new Ext.data.Store({
          pageSize: 50,
          fields: config.fields,
          autoLoad: false,
          remoteSort: false,
          //sorters: me.internal.sort,
          proxy: {
              type:'ajax',
              url: 'module/nomina/',
              actionMethods: { read: 'POST' },//actionMethods:  {create: "POST", read: "POST", update: "POST", destroy: "POST"},
              timeout: 3600000,
              reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
              },
              extraParams: {
                  action: 'onListFichaPeriodo',
                  //text: ''
              }
          },
          listeners: {
            load: function(store, records, successful){
              //CREAR MENSAJE DE ALERTA PARA LAS PERSONAS QUE TENGAN CONCEPTOS Y NO SE ENCUENTRE LA COLUMNA
              
              for(var i=0;i<records.length;i++){
                records[i].set("mensaje","");
                var concepto=records[i].get("concepto");
                for(var j=0;j<concepto.length;j++){
                  var encontro=false;
                  for(var k=0;k<config.fields.length;k++){         
                    if(concepto[j]["id"]==config.fields[k]){
                        encontro=true;
                        break;
                      }
                  }//FIN for(var k=0;k<config.fields.length;k++)
                  if(!encontro){
                    records[i].set("mensaje",
                                   records[i].get("mensaje")+
                                   concepto[j]["codigo"]+" "+
                                   concepto[j]["concepto"]+"<br>"
                    );
                  }
                }//FIN for(var j=0;j<concepto_id.length;j++)
                
                if(records[i].get("mensaje")){
                  records[i].set("mensaje","<b>La persona tiene los siguientes conceptos, lo cuales no se encuentran asociados a la nómina actual:<br></b><p>"+records[i].get("mensaje")+"</p>");
                }
                
              }//FIN for(var i=0;i<records.length;i++)              
              
              me.getCmp('gridList').getSelectionModel().deselectAll();
            },
            beforeload: function(store,operation,eOpts){
              store.proxy.extraParams.id_nomina=me.getCmp('id_nomina').getValue();
              store.proxy.extraParams.id_periodo=me.getCmp('id_periodo').getValue();
            }
          }
        });
        
        me.getCmp('gridList').reconfigure(store,config.columns);
    
        me.getCmp('pagingList').bindStore(store);
        
        
        me.getCmp('gridList').getStore().load();
        
        
        
        
        
        
        /*var _data=[];
        for(var i=0;i<result["ficha"].length;i++){
          _data[i]={};
          _data[i]["mensaje"]="";
          _data[i]["id_ficha"]=result["ficha"][i]["id"];
          _data[i]["persona"]=result["ficha"][i]["nombre_apellido"]+" <span class='nomina_cargo'>("+result["ficha"][i]["cargo"]+")</span>";
          
          for(var j=0;j<result["ficha"][i]["concepto"].length;j++){
            _data[i][result["ficha"][i]["concepto"][j]["id"]]=result["ficha"][i]["concepto"][j]["valor_final"];
            if(result["ficha"][i]["concepto"][j]["tipo"]=="AP")
              _data[i][result["ficha"][i]["concepto"][j]["id"]+"_ap"]=result["ficha"][i]["concepto"][j]["valor_final_ap"];
              
            //verificar si el concepto existe en config.fields
            var encontro=false;
            for(var k=0;k<config.fields.length;k++){
              //console.log(result["ficha"][i]["concepto"][j]["id"]+"=="+config.fields[k]);
              if(result["ficha"][i]["concepto"][j]["id"]==config.fields[k]) {
                encontro=true;
                break;
              }
            }
            if(!encontro){
              _data[i]["mensaje"]+=result["ficha"][i]["concepto"][j]["codigo"]+" "+result["ficha"][i]["concepto"][j]["concepto"]+".<br>";
            }            
          }
          if(_data[i]["mensaje"]){
            _data[i]["mensaje"]="<b>La persona tiene los siguientes conceptos, lo cuales no se encuentran asociados a la nómina actual:<br></b><p style=\\'font-size: 10px; padding-left: 20px;\\'>"+_data[i]["mensaje"]+"</p>";
          }
          
          _data[i]["total_asignacion"]=result["ficha"][i]["total_asignacion"];
          _data[i]["total_deduccion"]=result["ficha"][i]["total_deduccion"];
          _data[i]["total_neto"]=result["ficha"][i]["total_neto"];
          _data[i]["total_ap"]=result["ficha"][i]["total_ap"];
        }

        me.getCmp('gridList').reconfigure({
          fields: config.fields,
          data: _data
        },config.columns);
        
        me.getCmp('pagingList').bindStore(me.getCmp('gridList').getStore());

        */
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText); 
      }
    });
  },
  
  onEditarConcepto: function(id_concepto) {
    var me=this;
    alert(id_concepto);
    
    
  },
  
  onCerrarPeriodo: function(){
    var me=this;
    if(!me.onNominaSeleccionada())       
      return;
    
    var id_periodo=me.getCmp('id_periodo').getValue();
    
    Ext.MessageBox.confirm( 'Cerrar Período',
                            '<b>\u00BFEst\u00e1 seguro de cerrar el período?</b><br> '+me.getCmp('id_periodo').getRawValue()+'',
                            function(btn,text){
                              if(btn == 'yes'){
                                me.getCmp('btnPersona').setDisabled(true);
                                me.getCmp('btnConcepto').setDisabled(true);
                                me.getCmp('btnCerrarPeriodo').setDisabled(true);
                                me.getCmp('btnContabilizar').setDisabled(true);
                                
                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/nomina/',
                                  params:{
                                    action: 'onClose',
                                    id_periodo: id_periodo
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);                                    
                                    Ext.MessageBox.alert("Cerrar Período",result["message"]);
                                    //recargar el listado de periodos, para mostrar el nuevo periodo creado
                                    me.getCmp('id_periodo').getStore().load();
                                    me.onRecargar();                                    
                                    window.open("report/nomina_recibo_pago.php?id_periodo="+id_periodo+"&generar=1");                                    
                                  },
                                  failure:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                  }
                                });
                              }
                            });
  },
  
  onNota: function(){
    var me=this;
    if(!me.onNominaSeleccionada())       
      return;
    
    var id_periodo=me.getCmp('id_periodo').getValue();
    var id_nomina=me.getCmp('id_nomina').getValue();
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo_nota/',
      params:{
        action: 'onGet',
        id_periodo: id_periodo,
        id_nomina: id_nomina
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        var nota="";
        if(result.length==1) {
          nota=result[0]["nota"];
        }
        var prompt=Ext.Msg.prompt(
          me.title,
          //'<b>Notas para la nómina actual.</b><br>Nómina: '+me.getCmp('id_nomina').getRawValue()+'<br>Período: '+me.getCmp('id_periodo').getRawValue()+'.',
          '<b>Notas para la nómina actual.</b>',
          function(btn, text){
            if(btn == 'ok'){
              //INICIO guardar datos en la BD
              Ext.Ajax.request({
                method: 'POST',
                url:'module/nomina_periodo_nota/',
                params:{
                  action: 'onSave',
                  id_periodo: id_periodo,
                  id_nomina: id_nomina,
                  nota: text
                },
                success:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                },
                failure:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                }
              });
              //FIN guardar datos en la BD
            }
          },
          me,
          5,
          nota
        );
        
        prompt.setWidth(500);
        prompt.center();
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
  },
  
  onContabilizar: function(){
    var me=this;
    if(!me.onNominaSeleccionada())       
      return;
    
    var id_periodo=me.getCmp('id_periodo').getValue();
    
    Ext.MessageBox.confirm( 'Contabilizar Período',
                            '<b>\u00BFEst\u00e1 seguro que desea contabilizar el período?</b><br> '+me.getCmp('id_periodo').getRawValue()+'',
                            function(btn,text){
                              if(btn == 'yes'){
                                //pedir fecha de contabilizacion
                                var _fecha=siga.get({action: "date", format: "d/m/Y"});
                                _fecha=_fecha["result"];
                                while(true){
                                  _fecha=prompt("Introduzca la fecha de contabilización (DD/MM/AAAA).", _fecha);
                                  if(_fecha==null)//si es cancelar
                                    return;
                                  if(isValidDate(_fecha))//si es valida
                                    break;
                                  alert("La fecha introducida es invalida.");
                                  }
                                _fecha=unformatDate(_fecha);
                                
                                me.getCmp('btnPersona').setDisabled(true);
                                me.getCmp('btnConcepto').setDisabled(true);
                                me.getCmp('btnCerrarPeriodo').setDisabled(true);
                                me.getCmp('btnContabilizar').setDisabled(true);
                                
                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/nomina/',
                                  params:{
                                    action: 'onContabilizar',
                                    id_periodo: id_periodo,
                                    fecha: _fecha
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);                                    
                                    Ext.MessageBox.alert("Contabilizar Período",result["message"]);
                                    me.onRecargar();
                                  },
                                  failure:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                  }
                                });
                              }
                            });
  },
  
  alert: function(msj){
    var me=this;
    //alert(msj);
    //Ext.Msg.alert(me.title,msj,null,me.getId());return;
    //var msg=Ext.Msg.alert(me.title,msj);
    //console.log(msj);
    
    //var msg=Ext.Msg.show({title: me.title});
    /*var win=Ext.create("Ext.window.MessageBox",{
      title: me.title,
      width: 600,
      height: 400,
      renderTo: me.getId(),
      //renderTo: Ext.getBody(),
      modal: false,
      html: msj
    });*/
    Ext.Msg.show({
      renderTo: me.getId(),
      modal:false,
      message: msj,
      modal: true,
    });
    //msg.toFront(true);
    //msg.show();
    //Ext.MessageBox.alert(me.title,msj).toFront();
  },
  
  
  

});








