siga.define('movimiento_material', {
  //extend: 'Ext.window.eWindow',
  //extend: 'siga.windowBase',
  extend: 'siga.window',
  title: 'Inventario - Movimiento de Materiales y Suministros',      
  maximizable:false,
  width: 750,
  height: 550,
  resizable: false,
  initComponent: function(){
    var me = this;
    //me.reset();
    
    me.internal.gridList.features=[{
      ftype: 'grouping',
      groupHeaderTpl: '{name}',
      collapsible : false,
    }];
    
    me.itemsToolbar=[
      me.btnNew(),
      me.btnSave(),
      me.btnDelete(),
      //me.btnCopyPaste(),
      {
        xtype: 'button',
        id: me._('btnVisualizar'),
        height: 45,
        width: 55,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-pdf',
        iconAlign: 'top',
        tooltip: 'Visualizar Movimiento de Inventario',
        listeners: {
          click: function(){            
            var id=me.getCmp("id").getValue();
            if(!id){
              me.setMessage("Solo puede visualizar los movimientos guardadas","red");
              return;
            }
            window.open("modulo_inventario/reportes/pdf_movimiento_material.php?id="+id);
          }
        }
      },
      {
        xtype:'tbspacer',
        flex:1
      },
      me.btnPrevious(),
      me.btnNext(),
    ];
    
    me.internal.cantidad_numberfield=new Ext.form.NumberField({
      allowBlank: false,
      minValue: 1,
    });
    
    me.internal.unidad_medida_combobox=new Ext.form.ComboBox({
      store: {
        fields: ['id','medida'],
        autoLoad: true,
        pageSize: 100,
        proxy: {
          type:'ajax',
          url: 'module/unidad_medida/',
          actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
          timeout: 3600000,
          reader: {
            type: 'json',
            root: 'result',
            totalProperty:'total'
          },
          extraParams: {
            action: 'onListSelect'
          }
        }
      },
      displayField: 'medida',
      valueField: 'id',
      allowBlank: false,
      forceSelection: true,
      editable: false,
    });    
    
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        width: 230,
        fieldLabel: 'ID',
        value: ''
      },
      {
        xtype:'hidden',
        id: me._('id_item_tipo'),
        name: 'id_item_tipo',
        fieldLabel: 'ID ITEM TIPO',
        value: '1',
        isFormField: false
      },
      {
        xtype: "container",
        anchor: "100%",
        layout: "hbox",
        //margin: "0",
        //defaults: define["siga_field_defaults"],
        defaults: me.getInternal("field_defaults"),
        items: [
          {
            xtype:'combobox',
            id: me._('tipo'),
            name: 'tipo',
            margin: "5px 0px 0px 0px",
            width: 175,
            fieldLabel: 'Tipo',
            store: {
              fields: ['id', 'nombre'],
              data : [
                {"id":"E", "nombre":"ENTRADA"},
                {"id":"S", "nombre":"SALIDA"}
              ]                      
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: false,
            editable: false,
            forceSelection: true,                    
            value: 'E',
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
            margin: "5px 40px 0px 40px",
            width: 175,
            flex: 1,
            fieldLabel: 'Correlativo',
            readOnly: true,
            isFormField: false,
            value: ''
          },
          {
            xtype:'datefield',
            id: me._('fecha'),
            name: 'fecha',
            margin: "5px 0px 0px 0px",
            fieldLabel: 'Fecha',
            submitFormat: 'Y-m-d',
            value: now("Y-m-d"),
            width: 180,
          },
          
          
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
        isFormField: false,
        border: 1,
        preventHeader: true,
        scroll: 'vertical',
        width: 610,
        selModel:{
          mode: "MULTI"
        },
        height: 200,
        //selType: 'cellmodel',
        plugins: [
          Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
          })
        ],
        columns: [
          new Ext.grid.RowNumberer({width: '5%', text: 'Nº', style: 'font-weight: bold;', align: 'center'}),
          {
            xtype: 'gridcolumn',
            dataIndex: 'codigo',
            text: 'Código',
            style: 'font-weight: bold;',
            width: '11%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'item',
            text: 'Item',
            style: 'font-weight: bold;',            
            flex: 1,
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'cantidad',
            text: 'Cantidad',
            style: 'font-weight: bold;',
            menuDisabled: true,
            sortable: false,
            width: '15%',
            align: 'center',
            editor: me.internal.cantidad_numberfield
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'id_unidad_medida',
            text: 'Unidad de Medida',
            style: 'font-weight: bold;',
            width: '20%',
            menuDisabled: true,
            sortable: false,
            editor: me.internal.unidad_medida_combobox,
            renderer: function(value){
              var store=me.internal.unidad_medida_combobox.getStore();
              var index = store.find('id', value);
              var rec = store.getAt(index);
              return rec.get('medida');
            }
          },
        ],
        store: {
          fields: ['id_item','codigo','item','cantidad','id_unidad_medida'],
          data: [],
        },
        fbar:[
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
                //crear la ventana para seleccionar los perfiles de acceso
                var selector=Ext.create("siga.windowSelect",{
                  width: 850,
                  height: 400,
                  internal:{
                    parent:{
                      fieldLabel: "Agregar items",
                      internal:{
                        valueField: 'id',
                        columns: {
                          field: ["codigo","denominacion","disponibilidad","cuenta_presupuestaria"],
                          title: ["Código","Denominación","Disponibilidad","Partida Presupuestaria"],
                          width: ['8%','45%','13%','34%'],
                          sort:  ["ASC","ASC","ASC","ASC"],
                          align: ["","","right",""]
                        },
                        url: 'module/item/?id_item_tipo='+me.getCmp("id_item_tipo").getValue()+"&disponibilidad=true",
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
                        me.agregarItem(id_item,1,'1');
                      }
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
          }  
        ]
      }
    ];
    
    
    me.callParent(arguments);
    

    //me.setAccess(define['modulo_inventario/movimiento_material->access']);
    me.internal.sort=[{property: 'fecha', direction: 'DESC'},{property: 'correlativo', direction: 'DESC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','tipo','correlativo','fecha','concepto','tipo_denominacion'],
      autoLoad: false,
      groupField: 'tipo_denominacion',
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/movimiento_material/',
          actionMethods: {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
          timeout: 3600000,
          reader: {
              type: 'json',
              root: 'result',
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
        width: '12%',
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
  
  onNew: function(){
    var me=this;
    
    me.getCmp('tabs').setActiveTab(0);  
    me.getCmp('tab_data').getForm().reset();
    me.getCmp("lista_items").getStore().removeAll();

    me.onGetCorrelativo();
    
    //recargar el listado
    me.onSearch();
  },
  
  onGetCorrelativo: function(){
    var me=this;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/movimiento_material/',
      params:{
        action: 'onGetCorrelativo',
        tipo: me.getCmp("tipo").getValue()
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
      url:'module/movimiento_material/',
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
        me.getCmp('id').setValue(result[0]['id']);
        me.getCmp('tipo').setValue(result[0]['tipo']);
        me.getCmp("correlativo").setValue(Ext.String.leftPad(result[0]["correlativo"],10,'0'));
        me.getCmp('fecha').setValue(result[0]['fecha']);
        me.getCmp('concepto').setValue(result[0]['concepto']);

        me.getCmp("lista_items").getStore().removeAll();
        for(var i=0;i<result[0]['items'].length;i++)
          me.agregarItem(result[0]['items'][i]['id_item'],result[0]['items'][i]['cantidad'],result[0]['items'][i]['id_unidad_medida']);
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
  
  save: function(){
    var me=this;
    
    var _items=[];
    for(var i=0;i<me.getCmp('lista_items').getStore().getCount();i++){
      _items.push({
                    id_item: me.getCmp('lista_items').getStore().getAt(i).data.id_item,
                    cantidad: me.getCmp('lista_items').getStore().getAt(i).data.cantidad,
                    id_unidad_medida: me.getCmp('lista_items').getStore().getAt(i).data.id_unidad_medida
                  });
    }
    
    Ext.MessageBox.wait('Guardando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/movimiento_material/',
      params:{
        action: 'onSave',
        id: me.getCmp('id').getValue(),
        tipo: me.getCmp('tipo').getValue(),
        fecha: me.getCmp('fecha').getValue(),
        concepto: me.getCmp('concepto').getValue(),
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
      url:'module/movimiento_material/',
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
    me.internal.toCopy=[
      //me.getCmp("codigo").getValue(),
      //me.getCmp("id_curso").getValue(),
      //me.getCmp("id_turno").getValue(),
      //me.getCmp("fecha_inicio").getValue(),
      //me.getCmp("fecha_culminacion").getValue(),
      //me.getCmp("cupos").getValue(),
      //me.getCmp("id_sala").getValue(),
      //me.getCmp("id_instructor").getValue(),
      //me.getCmp("id_estado").getValue(),
      //me.getCmp("id_impreso").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;
    //me.getCmp("codigo").setValue(me.internal.toCopy[0]);
    //me.getCmp("id_curso").setValue(me.internal.toCopy[1]);
    //me.getCmp("id_turno").setValue(me.internal.toCopy[2]);
    //me.getCmp("fecha_inicio").setValue(me.internal.toCopy[3]);
    //me.getCmp("fecha_culminacion").setValue(me.internal.toCopy[4]);
    //me.getCmp("cupos").setValue(me.internal.toCopy[5]);
    //me.getCmp("id_sala").setValue(me.internal.toCopy[6]);
    //me.getCmp("id_instructor").setValue(me.internal.toCopy[7]);
    //me.getCmp("id_estado").setValue(me.internal.toCopy[8]);
    //me.getCmp("id_impreso").setValue(me.internal.toCopy[9]);
  },
  
  agregarItem: function(id_item, cantidad, id_unidad_medida){
    var me=this;
    
    //buscar id_item en la base de datos
    Ext.Ajax.request({
      method: 'POST',
      url:'module/item/',
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
            id_unidad_medida: id_unidad_medida
          });
        me.getCmp('lista_items').getStore().sort({property : 'codigo', direction: 'ASC'});
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
  }
  
});