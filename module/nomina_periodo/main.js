siga.define("nomina_periodo",{
  extend: 'siga.window',
  title: 'Nómina - Período',
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

    me.itemsToolbar=[
      me.btnNew(),
      me.btnSave(),
      me.btnDelete(),
      me.btnDuplicar(),
      {
          xtype:'tbspacer',
          flex:1
      },
      me.btnPrevious(),
      me.btnNext()
    ];

    me.internal.gridList.features=[{
        ftype: 'grouping',
        groupHeaderTpl: "{name}",
        //groupHeaderTpl: "<tpl if='tipo == \"CA\"'>background: black;</tpl> {name}",
        collapsible : false,
    }];
    me.internal.gridList.groupField='tipo_denominacion';


    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        value: ''
      },
      {
        xtype: "container",
        layout: "hbox",
        defaults: _defaults,
        items: [
          {
            xtype:'textfield',
            id: me._('codigo'),
            name: 'codigo',
            fieldLabel: 'Código',
            value: '',
            width: 180,
            margin: "5 0 0 0",
          },
          {
            xtype: "tbspacer",
            flex: 1,
          },
          {
            xtype:'combobox',
            id: me._('cerrado'),
            name: 'cerrado',
            margin: "5 0 0 0",
            width: 250,
            fieldLabel: 'Estatus',
            store: {
              fields: ['id', 'nombre'],
              data : [
                {"id":"t", "nombre":"CERRADO"},
                {"id":"f", "nombre":"ABIERTO"}
              ]
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: false,
            editable: false,
            forceSelection: true,
            value: 'f'
          },
        ]
      },
      {
        xtype: "container",
        layout: "hbox",
        defaults: _defaults,
        items: [
          {
            xtype:'datefield',
            id: me._('fecha_inicio'),
            name: 'fecha_inicio',
            margin: "5 0 0 0",
            fieldLabel: 'Fecha Inicio',
            submitFormat: 'Y-m-d',
            value: '',
            width: 180,
          },
          {
            xtype:'datefield',
            id: me._('fecha_culminacion'),
            name: 'fecha_culminacion',
            margin: "5 65 0 40",
            fieldLabel: 'Fecha Culminación',
            submitFormat: 'Y-m-d',
            value: '',
            width: 180,
          },
          {
            xtype:'combobox',
            id: me._('tipo'),
            name: 'tipo',
            fieldLabel: 'Tipo de Nómina/Periodo',
            //anchor: '100%',
            //flex: 1,
            width: 250,
            margin: "5 0 0 0",
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
              }
            }
          },
        ]
      },

      {
        xtype:'textfield',
        id: me._('descripcion'),
        name: 'descripcion',
        fieldLabel: 'Periodo',
        fieldStyle: "text-transform: uppercase;",
        value: ''
      },



    ];

    me.callParent(arguments);


    //me.setAccess(siga.getAccess("modulo_base/usuarios"));

    me.internal.sort=[{property: 'tipo', direction: 'ASC'},{property: 'codigo', direction: 'ASC'}];

    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','codigo','descripcion','estatus'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      groupField: me.internal.gridList.groupField,
      proxy: {
          type:'ajax',
          url: 'module/nomina_periodo/',
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
        align: 'center',
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'descripcion',
        text: '<b>Descripción</b>',
        flex: 1,
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'fecha_inicio',
        text: '<b>Inicio</b>',
        width: '12%',
        menuDisabled: true,
        sortable: false,
        align: 'center',
        renderer: function(value){
          return formatDate(value);
        },
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'fecha_culminacion',
        text: '<b>Culminación</b>',
        width: '12%',
        menuDisabled: true,
        sortable: false,
        align: 'center',
        renderer: function(value){
          return formatDate(value);
        },
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'cerrado',
        text: '<b>Estatus</b>',
        width: '12%',
        menuDisabled: true,
        sortable: false,
        align: 'center',
        renderer: function(value){
          return (value=="t" || value=="true")?"CERRADO":"ABIERTO";
        },
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
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

    me.onNew();




  },

  onNew: function(){
    var me=this;


    me.getCmp("id").setValue("");
    me.getCmp("codigo").setValue("");
    me.getCmp("descripcion").setValue("");
    me.getCmp("fecha_inicio").setValue("");
    me.getCmp("fecha_culminacion").setValue("");
    me.getCmp("tipo").setValue("Q");
    me.getCmp("cerrado").setValue("f");

    me.onSearch();

    me.onGet_Codigo();

  },

  onGet_Codigo: function(){
    var me=this;
    return;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo/',
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
      url:'module/nomina_periodo/',
      params:{
        action: 'onGet',
        id: _id
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);


        me.getCmp("id").setValue(result[0]["id"]);
        me.getCmp("codigo").setValue(result[0]["codigo"]);
        me.getCmp("descripcion").setValue(result[0]["descripcion"]);
        me.getCmp("fecha_inicio").setValue(result[0]["fecha_inicio"]);
        me.getCmp("fecha_culminacion").setValue(result[0]["fecha_culminacion"]);
        me.getCmp("tipo").setValue(result[0]["tipo"]);
        me.getCmp("cerrado").setValue(result[0]["cerrado"]);



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
    var _descripcion=Ext.String.trim(me.getCmp("descripcion").getValue());
    var _fecha_inicio=me.getCmp("fecha_inicio").getValue();
    var _fecha_culminacion=me.getCmp("fecha_culminacion").getValue();
    var _tipo=Ext.String.trim(me.getCmp("tipo").getValue());
    var _cerrado=Ext.String.trim(me.getCmp("cerrado").getValue());

    Ext.MessageBox.wait();
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo/',
      params:{
        action: 'onSave',
        id: _id,
        codigo: _codigo,
        descripcion: _descripcion,
        fecha_inicio: _fecha_inicio,
        fecha_culminacion: _fecha_culminacion,
        tipo: _tipo,
        cerrado: _cerrado
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
  },

  btnDelete: function(){
    var me=this;
    return {
      xtype: 'button',
      id: me._('btnDelete'),
      height: 45,
      width: 57,
      text: 'Eliminar',
      cls: 'siga-btn-base',
      focusCls: '',
      disabledCls: 'siga-btn-disabled',
      iconCls: 'siga-btn-base-icon icon-delete',
      iconAlign: 'top',
      listeners: {
        click: function(){
          if(!me.getCmp('gridList').getSelectionModel().hasSelection())
            return;
          me.setMessage();
          Ext.MessageBox.confirm( 'Eliminar',
                                  'Se borrará el período y todos los datos asociados a esta nómina.<br>\u00BFDesea continuar?',
                                  function(btn,text){
                                      if (btn == 'yes')
                                          me.onDelete();
                                      }
                                  );
        }
      }
    };
  },

  onDelete: function(){
    var me=this;
    var _id=me.getCmp("id").getValue().trim();
    if(!_id) return;

    Ext.MessageBox.wait('Eliminando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo/',
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

  btnDuplicar: function(){
    var me=this;
    return {
      xtype: 'button',
      id: me._('btnCopyPaste'),
      height: 45,
      width: 57,
      text: 'Duplicar',
      cls: 'siga-btn-base',
      focusCls: '',
      disabledCls: 'siga-btn-disabled',
      iconCls: 'siga-btn-base-icon icon-copypaste',
      iconAlign: 'top',
      tooltip: 'Duplicar periodo.',
      listeners: {
        click: function(){
          if(!me.getCmp('gridList').getSelectionModel().hasSelection())
            return;
          me.setMessage();
          Ext.MessageBox.confirm( 'Duplicar',
                                  'Se duplicará el período.<br>\u00BFDesea continuar?',
                                  function(btn,text){
                                      if (btn == 'yes')
                                          me.onDuplicar();
                                      }
                                  );
        }
      }
    };
  },

  onDuplicar: function(){
    var me=this;
    var _id=me.getCmp("id").getValue().trim();
    if(!_id) return;

    Ext.MessageBox.wait('Procesando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo/',
      params:{
        action: 'onDuplicar',
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

});