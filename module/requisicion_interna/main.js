siga.define('requisicion_interna', {
  extend: "siga.window",
  title: 'Solicitud de Materiales, Bienes y/o Servicios',      
  maximizable:false,
  width: 750,
  height: 590,
  resizable: false,
  initComponent: function(){
    var me = this;
    
    var _defaults=me.getInternal("field_defaults");
    //_defaults.margin='5px 0px 0px 0px;';
    
    //me.reset();
    me.internal.esEditable=true;
    me.internal.gridList.features=[{
      ftype: 'grouping',
      groupHeaderTpl: '{name}',
      collapsible : false,
    }];    
    
    me.itemsToolbar=[
      me.btnNew(),
      me.btnSave(),
      me.btnDelete(),
      me.btnCopyPaste(),      
      {
        xtype: 'button',
        id: me._('btnEnviar'),
        height: 45,
        width: 55,
        text: 'Enviar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-documentsend',
        iconAlign: 'top',
        tooltip: 'Enviar Solicitud',
        listeners: {
          click: function(){            
            var id=me.getCmp("id").getValue();
            if(!id){
              me.setMessage("Debe seleccionar la solicitud a enviar.","red");
              return;
            }
            me.save(true);
          }
        }
      },
      {
        xtype: 'button',
        id: me._('btnVisualizar'),
        height: 45,
        width: 55,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-pdf',
        iconAlign: 'top',
        tooltip: 'Visualizar Solicitud',
        listeners: {
          click: function(){            
            var id=me.getCmp("id").getValue();
            if(!id){
              me.setMessage("Solo puede visualizar solicitudes guardadas","red");
              return;
            }
            window.open("modulo_inventario/reportes/pdf_requisicion.php?id="+id);
          }
        }
      },
      {
        xtype:'tbspacer',
        flex:1
      },      
      me.btnPrevious(),
      me.btnNext()      
    ];
    
    
    
    
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        //width: 230,
        //isFormField:true,
        fieldLabel: 'ID',
        value: ''
      },
      //{
      //  xtype:'tbspacer',
      //  flex:1,
      //  width: '100%'
      //},
      {
        xtype: "container",
        anchor: "100%",
        layout: "hbox",
        defaults: _defaults,
        items: [
          {
            xtype: 'combobox',
            id: me._('id_item_tipo'),
            name: 'id_item_tipo',
            //style: "margin: 0px 0px 0px 0px;",
            margin: '0px 0px 0px 0px',
            width: 230,
            fieldLabel: 'Tipo de Solicitud',
            editable: false,
            store: {
              fields: ['id','tipo'],
              autoLoad: true,
              pageSize: 100,
              proxy: {
                type:'ajax',
                url: 'module/item_tipo/',
                actionMethods:  {read: "POST"},//actionMethods:'POST',
                timeout: 3600000,
                reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
                },
                extraParams: {
                  action: 'onList',
                  text: '',
                  id: ''
                }
              },
              listeners: {
                load: function(store, records, successful){
                  me.getCmp("id_item_tipo").setValue("1");
                },
                beforeload: function(store,operation,eOpts){
                }
              }
            },
            displayField: 'tipo',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true,
            value: '1',
            listeners:{
              change: function(){
                me.onGetCorrelativo();
                me.getCmp("lista_items").getStore().removeAll();
              }
            }
          },
          {
            xtype:'textfield',
            id: me._('correlativo'),
            name: 'correlativo',
            width: 150,
            flex: 1,
            margin: '0px 0px 0px 40px',
            fieldLabel: 'Correlativo',
            readOnly: true,
            isFormField: false,
            value: ''
          },
          {
            xtype:'textfield',
            id: me._('estado'),
            width: 150,
            margin: '0px 0px 0px 40px',
            fieldLabel: 'Estado',
            value: '',
            isFormField: false,
            disabled: true
          }          
        ]        
      },
      //{
      //  xtype:'tbspacer',
      //  flex:1,
      //  width: '100%'
      //},
      {
        xtype: "container",
        anchor: "100%",
        layout: "hbox",
        defaults: _defaults,
        items: [
          {
            xtype:'datefield',
            id: me._('fecha'),
            name: 'fecha',
            fieldLabel: 'Fecha',
            submitFormat: 'Y-m-d',
            value: now("Y-m-d"),
            width: 230,
            margin: '0px 0px 0px 0px',
          },
          {
            xtype: 'combobox',
            id: me._('id_unidad_coordinacion'),
            name: 'id_unidad_coordinacion',
            width: 340,
            flex: 1,
            margin: '0px 0px 0px 40px',
            fieldLabel: 'Unidad/Coordinación',
            editable: false,
            store: {
              fields: ['id','coordinacion'],
              autoLoad: true,
              pageSize: 100,
              proxy: {
                type:'ajax',
                url: 'module/unidad_coordinacion/',
                actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
                timeout: 3600000,
                reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
                },
                extraParams: {
                  action: 'onListSelect'
                }
              },
              listeners: {
                load: function(store, records, successful){
                  me.getCmp("id_unidad_coordinacion").setValue(records[0].get("id"));
                }
              }
            },
            displayField: 'coordinacion',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true
          }
        ]
      },

      
      
      //{
      //  xtype:'tbspacer',
      //  flex:1,
      //  width: '100%'
      //},
      {
        xtype:'textarea',
        id: me._('concepto'),
        name: 'concepto',
        fieldLabel: 'Concepto',
        fieldStyle: 'text-transform: uppercase;',
        value: '',
        height: 67,
        width: 610
      },
      
      {
        xtype: 'gridpanel',
        id: me._('lista_items'),
        border: 1,
        preventHeader: true,
        scroll: "vertical",
        width: 610,
        selModel:{
          mode: "MULTI"
        },
        height: 180,
        plugins: [
          Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            pluginId: me._("celledit"),
            listeners: {
              beforeedit: function(obj) {
                if(me.internal.esEditable)
                  return true;
                return false;
              }   
            }
          })
        ],
        columns: [
          new Ext.grid.RowNumberer({columnWidth: '4%', text: 'Nº', style: 'font-weight: bold;', align: 'center'}),
          {
            xtype: 'gridcolumn',
            dataIndex: 'codigo',
            text: 'Código',
            style: 'font-weight: bold;',
            columnWidth: '10%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'item',
            text: 'Item',
            style: 'font-weight: bold;',
            //columnWidth: "50%",
            flex: 1,
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            text: 'Cantidad',
            //width: '40%',
            style: 'font-weight: bold;',
            menuDisabled: true,
            sortable: false,
            columns: [
              {
                xtype: 'gridcolumn',
                dataIndex: 'cantidad',                
                text: 'Solicitada',
                columnWidth: "20%",
                //flex: 1,
                style: 'font-weight: bold;',
                menuDisabled: true,
                sortable: false,
                align: 'center',
                editor: {
                  xtype: 'numberfield',
                  minValue: 1
                  },
                renderer: function(value) {
                  return value+" Unidad"+(value==1?"":"es");
                }
              },
              {
                xtype: 'gridcolumn',
                dataIndex: 'cantidad_aprobada',
                id: me._("columna_aprobado"),
                text: 'Aprobada',
                columnWidth: "20%",
                style: 'font-weight: bold;',
                menuDisabled: true,
                sortable: false,
                align: 'center',
                renderer: function(value) {
                  return value+" Unidad"+(value==1?"":"es");
                }
              }
            ]
          }
        ],
        store: {
          fields: ['id_item','codigo','item','cantidad','cantidad_aprobada'],
          data: [],
        },
        fbar: [
          {
            xtype: 'button',
            id: me._('btnAgregar'),
            style: 'margin: 0px 0px 0px 0px;',
            text: 'Agregar',
            tooltip: 'Agregar',
            iconCls: 'siga-icon-16 icon-add',
            width: 80,
            listeners: {
              click: function(){
                //crear la ventana para seleccionar los items
                var selector=new Ext.form.eWindowSelect({
                  parent:{
                    fieldLabel: "Agregar items",
                    internal:{
                      valueField: 'id',
                      columns: {field: ["codigo","denominacion","cuenta_presupuestaria"], title: ["Código","Denominación","Partida Presupuestaria"], width: ['10%','50%','40%'], sort: ["ASC","ASC","ASC"]},
                      url: 'module/item/?id_item_tipo='+me.getCmp("id_item_tipo").getValue(),
                      actionOnList:'onList',
                      page: 1,
                      limit: 100,
                      onBeforeAccept: function(dataview, record, item, index, e){
                        //verificar si el item no se encuentra en el listado, si se encuentra mostrar una alerta y no cerrar la ventana de seleccion del item
                        var id_item=record.get("id");
                        var n=me.getCmp("lista_items").getStore().getCount();
                        for(var k=0;k<n;k++)
                          if(id_item==me.getCmp("lista_items").getStore().getAt(k).data.id_item){
                            Ext.MessageBox.alert("Agregar ítems", "El ítem ya se encuentra agregado en el listado.");
                            return false;
                          }
                        return true;
                      }
                    },
                    setValue: function(id_item){
                      me.agregarItem(id_item,'1');
                    }
                  }
                });
                //mostrar la ventana
                selector.show();
                //cargar el listado
                selector.search();
              }
            }
          },
          {
            xtype: 'button',
            id: me._('btnQuitar'),
            style: 'margin: 0px 0px 0px 5px;',
            text: 'Quitar',
            tooltip: 'Quitar',
            iconCls: 'siga-icon-16 icon-remove',
            width: 80,
            listeners: {
              click: function(){
                var seleccion = me.getCmp('lista_items').getSelectionModel().getSelection();
                if(seleccion)
                  me.getCmp("lista_items").getStore().remove(seleccion);
              }
            }
          },
          {
            xtype:'tbspacer',        
            //width: 310,
            flex: 1
          },
          {
            xtype: 'button',
            id: me._('btnObservaciones'),
            style: 'margin: 0px 0px 0px 0px;',
            text: 'Observaciones',
            tooltip: 'Observaciones agregadas a la requisición.',
            //iconCls: 'siga-icon-16 icon-remove',
            width: 100,
            listeners: {
              click: function(){
                var id=me.getCmp("id").getValue();
                if(!id) return;
                Ext.Ajax.request({
                  method: 'POST',
                  url:'module/requisicion_interna/',
                  params:{
                    action: 'onListObservacion',
                    id: id
                  },
                  success:function(request){
                    var result=Ext.JSON.decode(request.responseText);
                    var mostrar="";
                    
                    if(!result || result.length==0){
                      Ext.Msg.show({
                        title:'Observaciones',
                        msg: "No hay observaciones",
                        buttons:  parent.Ext.Msg.OK
                      });
                      return;
                    }
                    mostrar="<table width='100%' cellpadding='3'>";
                    mostrar+="<tr style='background-color: #000; color: #FFF; text-align: center;'>";
                    mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Nº</b></td>";
                    mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Fecha</b></td>";
                    mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Observación</b></td>";
                    mostrar+="<td style='padding: 5px 10px 5px 10px;'><b>Persona</b></td>";
                    mostrar+="</tr>";
                    
                    for(var i=0;i<result.length;i++){
                      mostrar+="<tr style='background-color: #FFF;'>";
                      mostrar+="<td style='text-align: center;'><b>"+(i+1)+"</b></td>";
                      mostrar+="<td>"+result[i]["fecha"]+"</td>";
                      mostrar+="<td>"+result[i]["observacion"]+"</td>";                   
                      mostrar+="<td>"+result[i]["persona"]+"</td>";                   
                      mostrar+="</tr>";
                    }
                    
                    mostrar+="</table>";
                    
                    
                    Ext.Msg.show({
                      title:'Observaciones',
                      msg: mostrar,
                      buttons:  parent.Ext.Msg.OK
                    });
                      
                  },
                  failure:function(request){
                    var result=Ext.JSON.decode(request.responseText);      
                    me.setMessage(result.message,"red");
                  }
                });
                
                
              }
            }
          }
          
        ]
      },

    ];

    me.callParent(arguments);
    //me.setAccess(define['modulo_inventario/requisicion->access']);
    me.internal.sort=[{property: 'fecha', direction: 'DESC'},{property: 'correlativo', direction: 'DESC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','tipo','correlativo','fecha','concepto','estado'],
      autoLoad: false,      
      remoteSort: true,
      remoteGroup: false,
      groupField: 'tipo',
      sorters: me.internal.sort,
      proxy: {
        type:'ajax',
        url: 'module/requisicion_interna/',
        actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
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
        dataIndex: 'correlativo',
        text: 'Número',
        width: '11%',
        menuDisabled: true,
        sortable: false,
        renderer: function(value){
          return Ext.String.leftPad(value,10,'0');
        },        
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'fecha',
        text: 'Fecha',
        width: '10%',
        menuDisabled: true,
        sortable: false,
        renderer: function(value){
          return formatDate(value);
        },
        cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'concepto',
        text: 'Concepto',
        flex: 1,
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'estado',
        text: 'Estado',
        width: '15%',
        menuDisabled: true,
        sortable: false,
        renderer: function(value){
          return me.getEstado(value);
        },
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          },
        }
      }
    ];
    
    
    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('gridList').bindStore(store);
    me.getCmp('pagingList').bindStore(store);
  },
  
  init: function(){
    var me=this;
    me.onNew();
  },
  
  setAccess: function(_access){
    var me=this;    
    switch(_access){
      case "rw":
        me.getCmp('btnSave').setDisabled(false);
        me.getCmp('btnDelete').setDisabled(false);
        me.getCmp('btnEnviar').setDisabled(false);
        break;
      case "r":
        me.getCmp('btnSave').setDisabled(true);
        me.getCmp('btnDelete').setDisabled(true);
        me.getCmp('btnEnviar').setDisabled(true);
        break;
      case "a":
        me.getCmp('btnDelete').setDisabled(true);
        break;
    }
  },
  
  onNew: function(){
    var me=this;
    me.getCmp('tabs').setActiveTab(0);
    //me.setAccess(define['modulo_inventario/requisicion->access']);
    
    var _id_unidad_coordinacion=me.getCmp("id_unidad_coordinacion").getValue();
    
    me.getCmp('estado').setValue("");
    me.internal.esEditable=true;
    me.getCmp('tab_data').getForm().reset();
    
    me.getCmp('id_item_tipo').fireEvent('change');    
    
    //mantener la seleccion anterior
    me.getCmp("id_unidad_coordinacion").setValue(_id_unidad_coordinacion);
    me.getCmp("columna_aprobado").setVisible(false);
    me.getCmp('btnAgregar').setDisabled(false);
    me.getCmp('btnQuitar').setDisabled(false);
    
    //recargar el listado
    me.onSearch();
  },
  
  onGetCorrelativo: function(){
    var me=this;
    var id_item_tipo=me.getCmp("id_item_tipo").getValue();
    if(!id_item_tipo)
      return;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/requisicion_interna/',
      params:{
        action: 'onGetCorrelativo',
        id_item_tipo: id_item_tipo
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        me.getCmp("correlativo").setValue(Ext.String.leftPad(result[0]["correlativo"],10,'0'));
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
    
    
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    if(!_id) return;
    
    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/requisicion_interna/',
      params:{
        action: 'onGet',
        id: _id
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        if(!result || result.length==0){
          alert("Error al cargar los datos.");
          return;
        }
        
        me.setAccess(define['modulo_inventario/requisicion->access']);
        me.getCmp('btnSave').setDisabled(true);
        me.getCmp('btnDelete').setDisabled(true);
        me.getCmp('btnEnviar').setDisabled(true);
        me.getCmp('btnAgregar').setDisabled(true);
        me.getCmp('btnQuitar').setDisabled(true);
        me.internal.esEditable=false;

        switch(result[0]['estado']){
          case "B":
            //Activar el boton de eliminar
            if(define['modulo_inventario/requisicion->access']=="rw"){
              me.getCmp('btnDelete').setDisabled(false);
              me.getCmp('btnSave').setDisabled(false);
              me.getCmp('btnEnviar').setDisabled(false);
              me.getCmp('btnAgregar').setDisabled(false);
              me.getCmp('btnQuitar').setDisabled(false);
              me.internal.esEditable=true;
            }
            break;
          case "C":
            //Activar el boton de guardar
            if(define['modulo_inventario/requisicion->access']=="rw"){
              me.getCmp('btnSave').setDisabled(false);
              me.getCmp('btnEnviar').setDisabled(false);
              me.getCmp('btnAgregar').setDisabled(false);
              me.getCmp('btnQuitar').setDisabled(false);
              me.internal.esEditable=true;
            }
            break;
        }
        
        me.getCmp('id').setValue(result[0]['id']);
        me.getCmp('id_item_tipo').setValue(result[0]['id_item_tipo']);
        me.getCmp("correlativo").setValue(Ext.String.leftPad(result[0]["correlativo"],10,'0'));
        me.getCmp('estado').setValue(me.getEstado(result[0]['estado']));
        me.getCmp('fecha').setValue(result[0]['fecha']);
        me.getCmp('id_unidad_coordinacion').setValue(result[0]['id_unidad_coordinacion']);        
        me.getCmp('concepto').setValue(result[0]['concepto']);
        
        if(result[0]["estado"]=="D")
          me.getCmp("columna_aprobado").setVisible(true);
        else
          me.getCmp("columna_aprobado").setVisible(false);
        
        me.getCmp("lista_items").getStore().removeAll();
        for(var i=0;i<result[0]['items'].length;i++)
          me.agregarItem(result[0]['items'][i]['id_item'],result[0]['items'][i]['cantidad'],result[0]['items'][i]['cantidad_aprobada']);
        
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
    var _id=me.getCmp("id").getValue().trim();
    
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
  
  save: function(_enviar){
    var me=this;
    
    var _items=[];
    for(var i=0;i<me.getCmp('lista_items').getStore().getCount();i++){
      _items.push({
                    id_item: me.getCmp('lista_items').getStore().getAt(i).data.id_item,
                    cantidad: me.getCmp('lista_items').getStore().getAt(i).data.cantidad
                 });
    }
    
    if(!_enviar)
      _enviar=false;
    
    Ext.MessageBox.wait('Guardando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/requisicion_interna/',
      params:{
        action: 'onSave',
        id: me.getCmp('id').getValue(),
        id_item_tipo: me.getCmp('id_item_tipo').getValue(),
        id_unidad_coordinacion: me.getCmp('id_unidad_coordinacion').getValue(),
        fecha: me.getCmp('fecha').getValue(),
        concepto: me.getCmp('concepto').getValue(),
        enviar: _enviar,
        items: Ext.JSON.encode(_items)
      },
      success:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);
        if(!result || result.length==0){
          alert("Error al cargar los datos.");
          return;
        }
        if(result.success){
          me.onNew();
          me.setMessage(result.message,"green");
        }
        else{
          me.setMessage(result.message,"red");
          if(result.messageDB)
            console.log("["+me.id+"]\n"+result.message+"\n"+result.messageDB);
        }
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
        if(result.messageDB)
          console.log("["+me.id+"]\n"+result.message+"\n"+result.messageDB);
      }
    });
  },
  
  onDelete: function(){
    var me=this;
    var _id=me.getCmp("id").getValue().trim();
    if(!_id) return;
    
    Ext.MessageBox.wait('Eliminando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/requisicion_interna/',
      params:{
        action: 'onDelete',
        id: _id
      },
      success:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);
        if(result.success){
          me.onNew();
          me.setMessage(result.message,"green");
        }
        else{
          me.setMessage(result.message,"red");
        }          
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
  },
  
  onCopy: function(){
    var me=this;
    var _items=[];
    for(var i=0;i<me.getCmp('lista_items').getStore().getCount();i++){
      _items.push({
                    id_item: me.getCmp('lista_items').getStore().getAt(i).data.id_item,
                    cantidad: me.getCmp('lista_items').getStore().getAt(i).data.cantidad
                 });
    }
    
    me.internal.toCopy=[
      me.getCmp("id_item_tipo").getValue(),
      me.getCmp("fecha").getValue(),
      me.getCmp("id_unidad_coordinacion").getValue(),
      me.getCmp("concepto").getValue(),
      _items
      ];
  },
  
  onPaste: function(){
    var me=this;
    me.getCmp("id_item_tipo").setValue(me.internal.toCopy[0]);
    me.getCmp("fecha").setValue(me.internal.toCopy[1]);
    me.getCmp("id_unidad_coordinacion").setValue(me.internal.toCopy[2]);
    me.getCmp("concepto").setValue(me.internal.toCopy[3]);
    
    me.getCmp("lista_items").getStore().removeAll();
    for(var i=0;i<me.internal.toCopy[4].length;i++)
      me.agregarItem(me.internal.toCopy[4][i].id_item,me.internal.toCopy[4][i].cantidad);
  },
  
  agregarItem: function(id_item, cantidad, cantidad_aprobada){
    var me=this;
    
    if(!cantidad_aprobada)
      cantidad_aprobada=0;
    
    //buscar id_item en la base de datos
    Ext.Ajax.request({
      method: 'POST',
      url:'module/requisicion_interna/',
      params:{
        action: 'onGet',
        id: id_item
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        
        var n=me.getCmp("lista_items").getStore().getCount();
        me.getCmp("lista_items").getStore().insert(
          n,
          {
            id_item:result[0]["id"],
            codigo:result[0]["codigo"],
            item:result[0]["item"],
            cantidad: cantidad*1,
            cantidad_aprobada: cantidad_aprobada*1
          });
        me.getCmp('lista_items').getStore().sort({property : 'codigo', direction: 'ASC'});
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
  },
  
  getEstado: function(v){
    switch(v){
      case 'B': return "BORRADOR";
      case 'E': return "ENVIADO";
      case 'R': return "REPROBADO";
      case 'C': return "DEVUELTO";
      case 'D': return "APROBADO";
      }
    return v;
  }
  
});