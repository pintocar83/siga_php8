siga.define("nomina_concepto",{
  extend: 'siga.window',
  title: 'Nómina - Concepto', 
  width: 850,
  height: 600,
  
  initComponent: function(){
    var me = this;
    _defaults=me.getInternal("field_defaults");
    /*_defaults=me.getInternal("field_defaults");
    me.internal._defaults=_defaults;
    
    
    me.setInternal({
      field_defaults: {        
      }
    });*/
    
    me.internal.windowFormula=Ext.create("siga.windowBase",{
      modal: true,
      title: me.title,
      closeAction: 'hide',
      width: 700,
      height: 450,
      bodyStyle: "background-color: white;",
      layout: "anchor",
      defaults: _defaults,
      scrollable: true,
      items: [
        {
          xtype: "container",
          //margin: "30 0 0 0",
          margin: '15 40 0 40',
          layout: "hbox",
          items: [
            {
              xtype:'datefield',
              id: me._('ventana_formula_fecha'),
              fieldLabel: 'Fecha',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              submitFormat: 'Y-m-d',
              value: '',
              width: 150,
              //margin: '15 40 0 40',
            },
            {
                xtype:'tbspacer',
                flex:1
            },
            {
              xtype:'combobox',
              id: me._('ventana_formula_tipo'),
              name: 'tipo',
              fieldLabel: 'Tipo de Periodo',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              width: 250,
              queryMode: "local",
              store: {
                fields: ['tipo','denominacion'],
                data: [],
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
                  
                }
              }
            },
          ]
        },

        {
          xtype:'textarea',
          id: me._('ventana_formula_definicion'),
          fieldLabel: 'Formula',
          value: '',
          grow: true,
          margin: '5 40 0 40',
          fieldStyle: 'text-transform: uppercase;',
        },
        {
          xtype:'textarea',
          id: me._('ventana_formula_definicion_ap'),
          fieldLabel: 'Formula (Aporte Patronal)',
          value: '',
          grow: true,
          margin: '5 40 0 40',
          fieldStyle: 'text-transform: uppercase;',
        },
        
        {
          xtype: "container",
          margin: "30 0 0 0",
          layout: "hbox",
          items: [
            {
                xtype:'tbspacer',
                flex:1
            },
            {
              xtype: "button",
              text: "<b>ACEPTAR</b>",              
              width: 200,
              handler: function(){
                if(!me.getCmp('ventana_formula_fecha').getValue()){
                  Ext.Msg.alert(me.title, '<b>Debe ingresar la fecha.</b>');
                  return;
                }
                if(!me.getCmp('ventana_formula_fecha').isValid()){
                  Ext.Msg.alert(me.title, '<b>La fecha es inválida.</b>');
                  return;
                }
                
                var fecha=Ext.util.Format.date(me.getCmp('ventana_formula_fecha').getValue(),'Y-m-d');
                
                var resp=Ext.Ajax.request({
                    async: false,
                    url: "module/nomina_concepto/",
                    params: {
                      action: 'onSave_Formula',
                      id_concepto: me.internal.windowFormula.internal.id_concepto,
                      fecha: fecha,
                      formula_tipo: me.getCmp("ventana_formula_tipo").getValue(),
                      definicion: me.getCmp("ventana_formula_definicion").getValue(),
                      definicion_ap: me.getCmp("ventana_formula_definicion_ap").getValue()
                    }
                });
                if(resp.statusText=="OK"){
                  var retorno=Ext.JSON.decode(resp.responseText);
                  
                  if(retorno.success){
                    me.internal.windowFormula.close();
                    me.getCmp("grid_formula").getStore().load();
                  }
                  else
                    Ext.Msg.alert(me.title, '<b>'+retorno.message+'</b>');
                }
              }   
            },
            {
                xtype:'tbspacer',
                flex:1
            }
          ]
        }
      ],
      
      onLimpiar: function(){
        me.getCmp("ventana_formula_fecha").setValue('');
        me.getCmp("ventana_formula_definicion").setValue('');
        me.getCmp("ventana_formula_definicion_ap").setValue('');
      },
      
      
      
    });
    
    me.internal.windowAfectacion=Ext.create("siga.windowBase",{
      modal: true,
      title: me.title,
      closeAction: 'hide',
      width: 700,
      height: 330,
      bodyStyle: "background-color: white;",
      layout: "anchor",
      defaults: _defaults,
      items: [
        {
          xtype: "container",        
          anchor: "100%",
          layout: "hbox",
          margin: "10 0 0 0",
          defaults: me.getInternal("field_defaults"),
          items:[
            {
              xtype: 'combobox',
              id: me._('ventana_afectacion_id_nomina'),
              margin: "5 20 0 40",
              flex:1,
              fieldLabel: 'Nómina',
              editable: false,
              queryMode: "local",          
              store: {
                fields: ['id','codigo_nomina'],
                autoLoad: true,
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
                      me.getCmp("ventana_afectacion_id_nomina").setValue(records[0].get("id"));                
                  },
                  beforeload: function(store,operation,eOpts){
                  }
                }
              },
              displayField: 'codigo_nomina',
              valueField: 'id',
              allowBlank: false,
              forceSelection: true,
              listeners:{
                change: function(){
                }
              }
            },
            {
              xtype:'datefield',
              id: me._('ventana_afectacion_fecha'),
              fieldLabel: 'Fecha',
              submitFormat: 'Y-m-d',
              value: '',
              width: "30%",
              margin: '5 40 0 20'
            }
          ]      
        },
        {
          xtype: 'fieldcontainer',
          id: me._("ventana_afectacion_container_cuenta_presupuestaria"),
          fieldLabel: 'Cuenta Presupuestaria',
          layout: 'hbox',          
          items:[
            {
              xtype: "hidden",
              id: me._('ventana_afectacion_id_cuenta_presupuestaria'),
            },
            {
              xtype: 'textfield',
              id: me._('ventana_afectacion_cuenta_presupuestaria'),
              width: '20%',
              readOnly: true
            },
            {
              xtype: 'textfield',
              id: me._('ventana_afectacion_denominacion_presupuestaria'),                                  
              flex: 1,
              readOnly: true
            },
            {
              xtype: 'button',
              tooltip: 'Cuentas en uso',
              iconCls: 'siga-icon-16 icon-find',
              listeners: {
                click: function(){
                  siga.open("disponibilidad",{                      
                      modal: true,
                      onAccept: function(_me, _dataview, _record, _item, _index, _e){
                        me.internal.windowAfectacion.onAceptarCuentaPresupuestaria(_record.get("id_cuenta_presupuestaria"));
                        return true;
                      }
                  });
                }
              }
            },
            {
              xtype: 'button',
              tooltip: 'Todas las cuentas',
              iconCls: 'siga-icon-16 icon-find-all',
              listeners: {
                click: function(){
                  var selector=Ext.create("siga.windowSelect", {
                      internal:{
                          parent: {
                              fieldLabel: 'Cuentas Presupuestarias',                                                        
                              internal:{
                                  page:1,
                                  limit: 100,
                                  valueField: 'id_cuenta_presupuestaria',
                                  columns: {field: ["cuenta_presupuestaria","denominacion","padre"], title: ["Cuenta Presupuestaria","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
                                  url: 'module/cuenta_presupuestaria/?filtro=4%',
                                  actionOnList: 'onList',
                                  viewConfig:{
                                      getRowClass: function(rec, rowIdx, params, store) {
                                          if(rec.get('padre')=='t')
                                              return 'fila-padre';
                                          return 'fila-hija';
                                      }
                                  },
                                  onBeforeAccept: function(dataview, record, item, index, e){
                                      if(record.get('padre')=='t')
                                          return false;
                                      return true;
                                  },
                              },
                              setValue: function(v){
                                me.internal.windowAfectacion.onAceptarCuentaPresupuestaria(v);
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
              xtype: 'button',
              tooltip: 'Limpiar',
              iconCls: 'siga-icon-16 icon-clear',
              listeners: {
                click: function(){                                                
                  me.internal.windowAfectacion.onLimpiarCuentaPresupuestaria();
                }
              }
            }
          ]
        },
        
        {
          xtype: 'fieldcontainer',
          id: me._("ventana_afectacion_container_cuenta_presupuestaria_ap"),
          fieldLabel: 'Cuenta Presupuestaria (Aporte Patronal)',
          layout: 'hbox',          
          items:[
            {
              xtype: "hidden",
              id: me._('ventana_afectacion_id_cuenta_presupuestaria_ap'),
            },
            {
              xtype: 'textfield',
              id: me._('ventana_afectacion_cuenta_presupuestaria_ap'),
              width: '20%',
              readOnly: true
            },
            {
              xtype: 'textfield',
              id: me._('ventana_afectacion_denominacion_presupuestaria_ap'),                                  
              flex: 1,
              readOnly: true
            },
            {
              xtype: 'button',
              tooltip: 'Cuentas en uso',
              iconCls: 'siga-icon-16 icon-find',
              listeners: {
                click: function(){
                  siga.open("disponibilidad",{                      
                      modal: true,
                      onAccept: function(_me, _dataview, _record, _item, _index, _e){
                        me.internal.windowAfectacion.onAceptarCuentaPresupuestariaAP(_record.get("id_cuenta_presupuestaria"));
                        return true;
                      }
                  });
                }
              }
            },
            {
              xtype: 'button',
              tooltip: 'Todas las cuentas',
              iconCls: 'siga-icon-16 icon-find-all',
              listeners: {
                click: function(){
                  var selector=Ext.create("siga.windowSelect", {
                      internal:{
                          parent: {
                              fieldLabel: 'Cuentas Presupuestarias',                                                        
                              internal:{
                                  page:1,
                                  limit: 100,
                                  valueField: 'id_cuenta_presupuestaria',
                                  columns: {field: ["cuenta_presupuestaria","denominacion","padre"], title: ["Cuenta Presupuestaria","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
                                  url: 'module/cuenta_presupuestaria/?filtro=4%',
                                  actionOnList: 'onList',
                                  viewConfig:{
                                      getRowClass: function(rec, rowIdx, params, store) {
                                          if(rec.get('padre')=='t')
                                              return 'fila-padre';
                                          return 'fila-hija';
                                      }
                                  },
                                  onBeforeAccept: function(dataview, record, item, index, e){
                                      if(record.get('padre')=='t')
                                          return false;
                                      return true;
                                  },
                              },
                              setValue: function(v){
                                me.internal.windowAfectacion.onAceptarCuentaPresupuestariaAP(v);
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
              xtype: 'button',
              tooltip: 'Limpiar',
              iconCls: 'siga-icon-16 icon-clear',
              listeners: {
                click: function(){                                                
                  me.internal.windowAfectacion.onLimpiarCuentaPresupuestariaAP();
                }
              }
            }
          ]
        },
        
        {
            xtype: 'fieldcontainer',
            id: me._("ventana_afectacion_container_cuenta_contable"),
            fieldLabel: 'Cuenta Contable',
            layout: 'hbox',
            items:[
                {
                  xtype: "hidden",
                  id: me._('ventana_afectacion_id_cuenta_contable'),
                },
                {
                    xtype: 'textfield',
                    id: me._('ventana_afectacion_cuenta_contable'),
                    width: '23%',
                    readOnly: true
                },
                {
                    xtype: 'textfield',
                    id: me._('ventana_afectacion_denominacion_contable'),                                  
                    flex: 1,
                    readOnly: true
                },
                {
                    xtype: 'button',
                    tooltip: 'Cuentas Contables',
                    iconCls: 'siga-icon-16 icon-find-all',
                    listeners: {
                        click: function(){
                            var selector=Ext.create("siga.windowSelect", {
                                internal: {
                                    parent: {
                                        fieldLabel: 'Cuentas Contables',                                                        
                                        internal:{
                                            page:1,
                                            limit: 300,
                                            valueField: 'id_cuenta_contable',
                                            columns: {field: ["cuenta_contable","denominacion"], title: ["Cuenta Contable","Denominación"], width: ['20%','80%'], sort: ["ASC",""]},
                                            url: 'module/cuenta_contable/',
                                            actionOnList: 'onList',
                                            onAccept: function(){},
                                            onBeforeAccept: function(dataview, record, item, index, e){                                                
                                                //me.internal.windowAfectacion.onAceptarCuentaContable(record.get("id_cuenta_contable"),record.get("cuenta_contable"),record.get("denominacion"));
                                                return true;
                                            },
                                        },
                                        setValue: function(v){
                                          me.internal.windowAfectacion.onAceptarCuentaContable(v);
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
                    xtype: 'button',
                    tooltip: 'Limpiar',
                    iconCls: 'siga-icon-16 icon-clear',
                    listeners: {
                        click: function(){                                                
                            me.internal.windowAfectacion.onLimpiarCuentaContable();
                        }
                    }
                }
                
                
            ]
        },
        
        {
            xtype: 'fieldcontainer',
            id: me._("ventana_afectacion_container_cuenta_contable_ap"),
            fieldLabel: 'Cuenta Contable (Aporte Patronal)',
            layout: 'hbox',
            items:[
                {
                  xtype: "hidden",
                  id: me._('ventana_afectacion_id_cuenta_contable_ap'),
                },
                {
                    xtype: 'textfield',
                    id: me._('ventana_afectacion_cuenta_contable_ap'),
                    width: '23%',
                    readOnly: true
                },
                {
                    xtype: 'textfield',
                    id: me._('ventana_afectacion_denominacion_contable_ap'),                                  
                    flex: 1,
                    readOnly: true
                },
                {
                    xtype: 'button',
                    tooltip: 'Cuentas Contables',
                    iconCls: 'siga-icon-16 icon-find-all',
                    listeners: {
                        click: function(){
                            var selector=Ext.create("siga.windowSelect", {
                                internal: {
                                    parent: {
                                        fieldLabel: 'Cuentas Contables',                                                        
                                        internal:{
                                            page:1,
                                            limit: 300,
                                            valueField: 'id_cuenta_contable',
                                            columns: {field: ["cuenta_contable","denominacion"], title: ["Cuenta Contable","Denominación"], width: ['20%','80%'], sort: ["ASC",""]},
                                            url: 'module/cuenta_contable/',
                                            actionOnList: 'onList',
                                            onAccept: function(){},
                                            onBeforeAccept: function(dataview, record, item, index, e){                                                
                                                //me.internal.windowAfectacion.onAceptarCuentaContable(record.get("id_cuenta_contable"),record.get("cuenta_contable"),record.get("denominacion"));
                                                return true;
                                            },
                                        },
                                        setValue: function(v){
                                          me.internal.windowAfectacion.onAceptarCuentaContableAP(v);
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
                    xtype: 'button',
                    tooltip: 'Limpiar',
                    iconCls: 'siga-icon-16 icon-clear',
                    listeners: {
                        click: function(){                                                
                            me.internal.windowAfectacion.onLimpiarCuentaContableAP();
                        }
                    }
                }
                
                
            ]
        },
        {
          xtype: "container",
          margin: "30 0 0 0",
          layout: "hbox",
          items: [
            {
                xtype:'tbspacer',
                flex:1
            },
            {
              xtype: "button",
              text: "<b>ACEPTAR</b>",              
              width: 200,
              handler: function(){
                
                if(!me.getCmp('ventana_afectacion_fecha').isValid()){
                  Ext.Msg.alert(me.title, '<b>La fecha es inválida.</b>');
                  return;
                }
                
                var fecha=Ext.util.Format.date(me.getCmp('ventana_afectacion_fecha').getValue(),'Y-m-d');
                
                var resp=Ext.Ajax.request({
                    async: false,
                    url: "module/nomina_concepto/",
                    params: {
                      action: 'onSave_Afectacion',
                      id_concepto: me.internal.windowAfectacion.internal.id_concepto,
                      id_nomina: me.getCmp("ventana_afectacion_id_nomina").getValue(),
                      fecha: fecha,
                      id_cuenta_presupuestaria: me.getCmp("ventana_afectacion_id_cuenta_presupuestaria").getValue(),
                      id_cuenta_presupuestaria_ap: me.getCmp("ventana_afectacion_id_cuenta_presupuestaria_ap").getValue(),
                      id_cuenta_contable: me.getCmp("ventana_afectacion_id_cuenta_contable").getValue(),
                      id_cuenta_contable_ap: me.getCmp("ventana_afectacion_id_cuenta_contable_ap").getValue(),
                    }
                });
                if(resp.statusText=="OK"){
                  var retorno=Ext.JSON.decode(resp.responseText);
                  
                  if(retorno.success){
                    me.internal.windowAfectacion.close();
                    me.getCmp("grid_afectacion").getStore().load();
                  }
                  else
                    Ext.Msg.alert(me.title, '<b>'+retorno.message+'</b>');
                }
              }   
            },
            {
                xtype:'tbspacer',
                flex:1
            }
          ]
        }
        
      ],
      
      onLimpiar: function(){
        me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(false);
        me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(false);
        me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(false);
        me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(false);
        switch(me.internal.windowAfectacion.internal.tipo_concepto){
          case "A":
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(true);
            break;
          case "AP":
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(false);
            break;
          case "D":
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(true);
            break;
          case "RA":
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(true);
            break;
          case "RD":
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_presupuestaria_ap").setDisabled(true);
            me.getCmp("ventana_afectacion_container_cuenta_contable").setDisabled(false);
            me.getCmp("ventana_afectacion_container_cuenta_contable_ap").setDisabled(true);
            break;
        }
        
        me.getCmp("ventana_afectacion_fecha").setValue('');
        me.internal.windowAfectacion.onLimpiarCuentaPresupuestaria();
        me.internal.windowAfectacion.onLimpiarCuentaPresupuestariaAP();
        me.internal.windowAfectacion.onLimpiarCuentaContable();
        me.internal.windowAfectacion.onLimpiarCuentaContableAP();
      },
      
      onLimpiarCuentaPresupuestaria: function(){
        me.getCmp("ventana_afectacion_id_cuenta_presupuestaria").setValue('');
        me.getCmp("ventana_afectacion_cuenta_presupuestaria").setValue('');
        me.getCmp("ventana_afectacion_denominacion_presupuestaria").setValue('');
      },      
      
      onAceptarCuentaPresupuestaria: function(id_cuenta_presupuestaria){        
        me.internal.windowAfectacion.onLimpiarCuentaPresupuestaria();        
        var resp=Ext.Ajax.request({
            async: false,
            url: "module/cuenta_presupuestaria/",
            params: {
              action: 'onGet',
              id_cuenta_presupuestaria: id_cuenta_presupuestaria
            }
        });
        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.internal.windowAfectacion.setMessage("Error. La cuenta presupuestaria seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("ventana_afectacion_id_cuenta_presupuestaria").setValue(retorno[0]['id_cuenta_presupuestaria']);
            me.getCmp("ventana_afectacion_cuenta_presupuestaria").setValue(retorno[0]['cuenta_presupuestaria']);
            me.getCmp("ventana_afectacion_denominacion_presupuestaria").setValue(retorno[0]['denominacion']);
        }
        else
            me.internal.windowAfectacion.setMessage("Error. No pudo realizar la consulta.","red",30000);
      },      

      onLimpiarCuentaContable: function(){
        me.getCmp("ventana_afectacion_id_cuenta_contable").setValue('');
        me.getCmp("ventana_afectacion_cuenta_contable").setValue('');
        me.getCmp("ventana_afectacion_denominacion_contable").setValue('');
      },      
      
      onAceptarCuentaContable: function(id_cuenta_contable){        
        me.internal.windowAfectacion.onLimpiarCuentaContable();        
        var resp=Ext.Ajax.request({
            async: false,
            url: "module/cuenta_contable/",
            params: {
              action: 'onGet',
              id_cuenta_contable: id_cuenta_contable
            }
        });
        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.internal.windowAfectacion.setMessage("Error. La cuenta contable seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("ventana_afectacion_id_cuenta_contable").setValue(retorno[0]['id_cuenta_contable']);
            me.getCmp("ventana_afectacion_cuenta_contable").setValue(retorno[0]['cuenta_contable']);
            me.getCmp("ventana_afectacion_denominacion_contable").setValue(retorno[0]['denominacion']);
        }
        else
            me.internal.windowAfectacion.setMessage("Error. No pudo realizar la consulta.","red",30000);
      },
      
      onLimpiarCuentaPresupuestariaAP: function(){
        me.getCmp("ventana_afectacion_id_cuenta_presupuestaria_ap").setValue('');
        me.getCmp("ventana_afectacion_cuenta_presupuestaria_ap").setValue('');
        me.getCmp("ventana_afectacion_denominacion_presupuestaria_ap").setValue('');
      },      
      
      onAceptarCuentaPresupuestariaAP: function(id_cuenta_presupuestaria){        
        me.internal.windowAfectacion.onLimpiarCuentaPresupuestariaAP();        
        var resp=Ext.Ajax.request({
            async: false,
            url: "module/cuenta_presupuestaria/",
            params: {
              action: 'onGet',
              id_cuenta_presupuestaria: id_cuenta_presupuestaria
            }
        });
        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.internal.windowAfectacion.setMessage("Error. La cuenta presupuestaria seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("ventana_afectacion_id_cuenta_presupuestaria_ap").setValue(retorno[0]['id_cuenta_presupuestaria']);
            me.getCmp("ventana_afectacion_cuenta_presupuestaria_ap").setValue(retorno[0]['cuenta_presupuestaria']);
            me.getCmp("ventana_afectacion_denominacion_presupuestaria_ap").setValue(retorno[0]['denominacion']);
        }
        else
            me.internal.windowAfectacion.setMessage("Error. No pudo realizar la consulta.","red",30000);
      },      

      onLimpiarCuentaContableAP: function(){
        me.getCmp("ventana_afectacion_id_cuenta_contable_ap").setValue('');
        me.getCmp("ventana_afectacion_cuenta_contable_ap").setValue('');
        me.getCmp("ventana_afectacion_denominacion_contable_ap").setValue('');
      },      
      
      onAceptarCuentaContableAP: function(id_cuenta_contable){        
        me.internal.windowAfectacion.onLimpiarCuentaContableAP();        
        var resp=Ext.Ajax.request({
            async: false,
            url: "module/cuenta_contable/",
            params: {
              action: 'onGet',
              id_cuenta_contable: id_cuenta_contable
            }
        });
        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.internal.windowAfectacion.setMessage("Error. La cuenta contable seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("ventana_afectacion_id_cuenta_contable_ap").setValue(retorno[0]['id_cuenta_contable']);
            me.getCmp("ventana_afectacion_cuenta_contable_ap").setValue(retorno[0]['cuenta_contable']);
            me.getCmp("ventana_afectacion_denominacion_contable_ap").setValue(retorno[0]['denominacion']);
        }
        else
            me.internal.windowAfectacion.setMessage("Error. No pudo realizar la consulta.","red",30000);
      },
      
      
    });
    
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        value: ''
      },
      {
        xtype:'textfield',
        id: me._('codigo'),
        name: 'codigo',
        fieldLabel: 'Código',
        value: '',
        anchor: "25%",
        readOnly: true,
      },
      {
        xtype:'textfield',
        id: me._('concepto'),
        name: 'concepto',
        fieldLabel: 'Concepto',
        fieldStyle: "text-transform: uppercase;",
        value: ''
      },
      {
        xtype: "container",
        layout: "hbox",
        defaults: _defaults,
        items: [
          {
            xtype:'textfield',
            id: me._('identificador'),
            name: 'identificador',
            fieldLabel: 'Identificador',
            fieldStyle: "text-transform: uppercase;",
            margin: "5 0 0 0",
            value: '',
            flex: 1,
          },
          {
            xtype:'combobox',
            id: me._('tipo'),
            name: 'tipo',
            fieldLabel: 'Tipo',
            margin: "5 40 0 40",
            width: 200,
            store: {
              fields: ['id', 'nombre'],
              data : [            
                {"id":"A", "nombre":"ASIGNACIÓN"},
                {"id":"D", "nombre":"DEDUCCIÓN"},
                {"id":"AP", "nombre":"APORTE PATRONAL"},
                {"id":"RA", "nombre":"REINTEGRO ASIGNACIÓN"},
                {"id":"RD", "nombre":"REINTEGRO DEDUCCIÓN"},
                {"id":"_", "nombre":"NINGUNO (VISIBLE)"},
                {"id":"null", "nombre":"NINGUNO"}
              ]                      
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: true,
            forceSelection: true,
            editable: false,
            value: 'A'
          },
          {
            xtype:'textfield',
            id: me._('orden'),
            name: 'orden',
            fieldLabel: 'Orden',
            margin: "5 0 0 0",
            width: 50,
            value: ''
          },
        ]
      },
      {
        xtype: 'tabpanel',
        id: me._('subtabs'),
        margin: "20 40 0 40",
        collapsed: false,
        frameHeader: false,
        activeTab: 0,
        plain: true,
        height: 270,
        items: [
          {
            xtype: 'gridpanel',
            id: me._('grid_afectacion'),
            border: 1,
            title: "Afectación Presupuestaria/Contable",
            anchor: "100%",
            features:[{
                ftype: 'grouping',
                groupHeaderTpl: '{name}',
                collapsible : false,
            }],
            viewConfig: {
              markDirty: false
            },
            listeners: {
              itemdblclick: function(dataview, record, item, index, e){                
                if(!me.getCmp("id").getValue()) return;
                me.internal.windowAfectacion.internal.id_concepto=record.get("id_concepto");
                me.internal.windowAfectacion.internal.tipo_concepto=me.getCmp("tipo").getValue();
                me.internal.windowAfectacion.onLimpiar();
                me.getCmp('ventana_afectacion_id_nomina').setValue(record.get("id_nomina"));
                me.getCmp('ventana_afectacion_fecha').setValue(record.get("fecha"));
                me.internal.windowAfectacion.onAceptarCuentaPresupuestaria(record.get("id_cuenta_presupuestaria"));
                me.internal.windowAfectacion.onAceptarCuentaPresupuestariaAP(record.get("id_cuenta_presupuestaria_ap"));
                me.internal.windowAfectacion.onAceptarCuentaContable(record.get("id_cuenta_contable"));
                me.internal.windowAfectacion.onAceptarCuentaContableAP(record.get("id_cuenta_contable_ap")); 
                me.internal.windowAfectacion.show();
              }
            },
            bbar: [
              {
                xtype: 'button',
                text: 'Agregar',
                width: 100,
                handler: function(){
                  if(!me.getCmp("id").getValue()) return;                  
                  me.internal.windowAfectacion.internal.id_concepto=me.getCmp("id").getValue();
                  me.internal.windowAfectacion.internal.tipo_concepto=me.getCmp("tipo").getValue();
                  me.internal.windowAfectacion.onLimpiar();
                  me.internal.windowAfectacion.show();
                }
              },
              {
                xtype: 'button',
                text: 'Quitar',
                width: 100,
                handler: function(){
                  if(!me.getCmp("id").getValue()) return;  
                  var seleccion=me.getCmp("grid_afectacion").getSelection();
                  if(seleccion.length==0){
                    Ext.MessageBox.alert(me.title,"<b>Debe seleccionar el item a eliminar en afectación presupuestaria/contable.</b>");
                    return;
                  }
                  if(seleccion.length!=1){
                    Ext.MessageBox.alert(me.title,"<b>Debe seleccionar solo un item a eliminar en afectación presupuestaria/contable.</b>");
                    return;
                  }
                  
                  var id_nomina=seleccion[0].data["id_nomina"];
                  var id_concepto=seleccion[0].data["id_concepto"];
                  var fecha=seleccion[0].data["fecha"];
                  

                  Ext.MessageBox.confirm('Eliminar',
                    '<b>\u00BFEst\u00e1 seguro de eliminar el registro?<br>Nómina: </b>'+seleccion[0].data["nomina"]+'.<br><b>Fecha: </b>'+formatDate(fecha)+".",
                    function(btn,text){
                      if (btn == 'yes'){
                        var resp=Ext.Ajax.request({
                          async: false,
                          url: "module/nomina_concepto/",
                          params: {
                            action: 'onDelete_Afectacion',
                            id_nomina: id_nomina,
                            id_concepto: id_concepto,
                            fecha: fecha
                          }
                        });
                        if(resp.statusText=="OK"){
                          var retorno=Ext.JSON.decode(resp.responseText);
                          me.getCmp("grid_afectacion").getStore().load();                            
                        }
                      }
                    }
                  );
                }
              }
            ],
            columns: [
              { text: '<small><b>Fecha</b></small>', dataIndex: 'fecha_formateada', menuDisabled: true, sortable: false, flex: 1 },
              { text: '<small><b>Cta Presupuestaria</b></small>', dataIndex: 'cuenta_presupuestaria', menuDisabled: true, sortable: false, width: "22%" },
              { text: '<small><b>Cta Presupuestaria (AP)</b></small>', dataIndex: 'cuenta_presupuestaria_ap', menuDisabled: true, sortable: false, width: "22%" },
              { text: '<small><b>Cta Contable</b></small>', dataIndex: 'cuenta_contable', menuDisabled: true, sortable: false, width: "22%" },
              { text: '<small><b>Cta Contable (AP)</b></small>', dataIndex: 'cuenta_contable_ap', menuDisabled: true, sortable: false, width: "22%" }
            ],
            store: {
              fields: ['codigo_nomina','fecha','fecha_formateada','id_cuenta_presupuestaria','id_cuenta_presupuestaria_ap','id_cuenta_contable','id_cuenta_contable_ap','nomina','cuenta_presupuestaria','cuenta_presupuestaria_ap','cuenta_contable','cuenta_contable_ap'],
              autoLoad: false,
              remoteSort: true,
              groupField: "codigo_nomina",
              sorters: [{property: 'codigo_nomina', direction: 'ASC'},{property: 'fecha', direction: 'DESC'}],
              proxy: {
                type:'ajax',
                url: 'module/nomina_concepto/',
                actionMethods: { read: 'POST' },
                timeout: 3600000,
                reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
                },
                extraParams: {
                  action: 'onList_Afectacion',
                  id_concepto: ''
                }
              },
              listeners: {
                load: function(store, records, successful){
                  //me.getCmp('gridList').getSelectionModel().deselectAll();
                },
                beforeload: function(store,operation,eOpts){
                  store.proxy.extraParams.id_concepto=me.getCmp('id').getValue();
                }
              }
            }
          },
          {
            xtype: 'gridpanel',
            id: me._('grid_formula'),
            border: 1,
            title: "Formula",
            anchor: "100%",            
            viewConfig: {
              markDirty: false
            },
            features:[{
                ftype: 'grouping',
                groupHeaderTpl: 'TIPO: {name}',
                collapsible : false,
            }],            
            listeners: {
              itemdblclick: function(dataview, record, item, index, e){                
                if(!me.getCmp("id").getValue()) return;
                me.internal.windowFormula.internal.id_concepto=record.get("id_concepto");
                me.internal.windowFormula.onLimpiar();
                me.getCmp('ventana_formula_fecha').setValue(record.get("fecha"));                
                me.getCmp('ventana_formula_tipo').setValue(record.get("formula_tipo") ?? '');                
                me.getCmp('ventana_formula_definicion').setValue(record.get("definicion"));
                me.getCmp('ventana_formula_definicion_ap').setValue(record.get("definicion_ap"));
                me.internal.windowFormula.show();
              }
            },
            bbar: [
              {
                xtype: 'button',
                text: 'Agregar',
                width: 100,
                handler: function(){
                  if(!me.getCmp("id").getValue()) return;                  
                  me.internal.windowFormula.internal.id_concepto=me.getCmp("id").getValue();
                  me.internal.windowFormula.onLimpiar();
                  me.internal.windowFormula.show();
                }
              },
              {
                xtype: 'button',
                text: 'Quitar',
                width: 100,
                handler: function(){
                  if(!me.getCmp("id").getValue()) return;  
                  var seleccion=me.getCmp("grid_formula").getSelection();
                  if(seleccion.length==0){
                    Ext.MessageBox.alert(me.title,"<b>Debe seleccionar el item a eliminar en formula.</b>");
                    return;
                  }
                  if(seleccion.length!=1){
                    Ext.MessageBox.alert(me.title,"<b>Debe seleccionar solo un item a eliminar en formula.</b>");
                    return;
                  }

                  var id_concepto=seleccion[0].data["id_concepto"];
                  var fecha=seleccion[0].data["fecha"];                  
                  var formula_tipo=seleccion[0].data["formula_tipo"];                  

                  Ext.MessageBox.confirm('Eliminar',
                    '<b>\u00BFEst\u00e1 seguro de eliminar el registro?<br><b>Fecha: </b>'+formatDate(fecha)+".",
                    function(btn,text){
                      if (btn == 'yes'){
                        var resp=Ext.Ajax.request({
                          async: false,
                          url: "module/nomina_concepto/",
                          params: {
                            action: 'onDelete_Formula',
                            id_concepto: id_concepto,
                            fecha: fecha,
                            formula_tipo: formula_tipo,
                          }
                        });
                        if(resp.statusText=="OK"){
                          var retorno=Ext.JSON.decode(resp.responseText);
                          me.getCmp("grid_formula").getStore().load();                            
                        }
                      }
                    }
                  );
                  
                }
              }
            ],
            columns: [
              { text: '<small><b>Fecha</b></small>', dataIndex: 'fecha_formateada', menuDisabled: true, sortable: false, flex: 1 },
              { text: '<small><b>Formula</b></small>', dataIndex: 'definicion', menuDisabled: true, sortable: false, width: "43%" },
              { text: '<small><b>Formula (AP)</b></small>', dataIndex: 'definicion_ap', menuDisabled: true, sortable: false, width: "43%" }              
            ],
            store: {
              fields: ['id_concepto','fecha','fecha_formateada','definicion','definicion_ap','formula_tipo','formula_tipo_denominacion'],
              autoLoad: false,
              remoteSort: true,              
              sorters: [{property: 'fecha', direction: 'DESC'}],
              groupField: 'formula_tipo_denominacion',
              proxy: {
                type:'ajax',
                url: 'module/nomina_concepto/',
                actionMethods: { read: 'POST' },
                timeout: 3600000,
                reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
                },
                extraParams: {
                  action: 'onList_Formula',
                  id_concepto: ''
                }
              },
              listeners: {
                load: function(store, records, successful){
                  //me.getCmp('gridList').getSelectionModel().deselectAll();
                },
                beforeload: function(store,operation,eOpts){
                  store.proxy.extraParams.id_concepto=me.getCmp('id').getValue();
                }
              }
            }
          }
          
          
        ]
      },
      
      

      
      
      
      
      
    ];
    
    me.callParent(arguments);
    
    
    //me.setAccess(siga.getAccess("modulo_base/usuarios"));
    
    me.internal.sort=[{property: 'concepto', direction: 'ASC'},{property: 'codigo', direction: 'ASC'},{property: 'identificador', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','codigo','concepto','identificador'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/nomina_concepto/',
          actionMethods: { read: 'POST' },//actionMethods:  {create: "POST", read: "POST", update: "POST", destroy: "POST"},
          timeout: 3600000,
          reader: {
              type: 'json',
              rootProperty: 'result',
              totalProperty:'total'
          },
          extraParams: {
              action: 'onList',
              text: ''
          }
      },
      listeners: {
        load: function(store, records, successful){
          me.getCmp('gridList').getSelectionModel().deselectAll();
        },
        beforeload: function(store,operation,eOpts){
          store.proxy.extraParams.text=me.getCmp('txtSearch').getValue();
        }
      }
    });    
    
    var columns=[      
      {
        xtype: 'gridcolumn',
        dataIndex: 'codigo',
        text: '<b>Código</b>',
        width: '10%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'concepto',
        text: '<b>Concepto</b>',
        flex: 1,
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'identificador',
        text: '<b>Identificador</b>',
        width: '25%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      }
      
    ];
    
    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('pagingList').bindStore(store);
    
  },
  
  init: function(){
    var me=this;

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo_tipo/',
      params:{
        action: 'onList_Activo',
        sort: '[{"property": "denominacion", "direction": "ASC"}]',
        limit: 'ALL',
        start: '0',
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);

        const tipos = [
          {
            tipo: '',
            denominacion: 'NO APLICA'
          },
          ...(result?.result ?? [])
        ]

        me.getCmp('ventana_formula_tipo').getStore().setData(tipos);
        me.getCmp("ventana_formula_tipo").setValue("");
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });

    
    me.onNew();     
  },
    
  onNew: function(){
    var me=this;
    
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    me.onSearch();
    
    me.onGet_Codigo();
    me.getCmp('grid_afectacion').getStore().removeAll();
    me.getCmp('grid_formula').getStore().removeAll();
  },
  
  onGet_Codigo: function(){
    var me=this;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_concepto/',
      params:{
        action: 'onGet_Correlativo'
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);
        me.getCmp("codigo").setValue(result);
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);  
        me.setMessage(result.message,"red");
      }
    });
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;    
    var _id=record.get("id");
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_concepto/',
      params:{
        action: 'onGet',
        id: _id
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);
        
        if(!result[0]["tipo"]) result[0]["tipo"]="null";
        
        me.getCmp("id").setValue(result[0]["id"]);
        me.getCmp("codigo").setValue(result[0]["codigo"]);
        me.getCmp("concepto").setValue(result[0]["concepto"]);
        me.getCmp("identificador").setValue(result[0]["identificador"]);
        me.getCmp("tipo").setValue(result[0]["tipo"]);
        me.getCmp("orden").setValue(result[0]["orden"]);
        
        me.getCmp("grid_afectacion").getStore().load();
        me.getCmp("grid_formula").getStore().load();
        
        
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);  
        me.setMessage(result.message,"red");
      }
    });
  },
  
  
  onSave: function(){
    var me=this;    
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    
    if(_id){
      Ext.MessageBox.confirm("Guardar",
                             "¿Desea modificar el registro actual?",
                             function(btn,text){
                                if (btn == 'yes')
                                  me.save();
                                });
    }
    else{
      me.save();
    }
  },
  
  save: function(){
    var me=this;
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    var _codigo=Ext.String.trim(me.getCmp("codigo").getValue());
    var _concepto=Ext.String.trim(me.getCmp("concepto").getValue());
    var _identificador=Ext.String.trim(me.getCmp("identificador").getValue());
    var _tipo=Ext.String.trim(me.getCmp("tipo").getValue());
    var _orden=Ext.String.trim(me.getCmp("orden").getValue());
    
    Ext.MessageBox.wait();
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_concepto/',
      params:{
        action: 'onSave',
        id: _id,
        codigo: _codigo,
        concepto: _concepto,
        identificador: _identificador,
        tipo: _tipo,
        orden: _orden
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);
        Ext.MessageBox.hide();
        
        if(result.success){
          me.setMessage(result.message,"green");
          me.onNew();
        }
        else{
          me.setMessage(result.message,"red");
          if(result.action==1)
            me.onGet_Codigo();
        }
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);  
        me.setMessage(result.message,"red");
      }
    });
    
    
    
  }
});