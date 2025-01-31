siga.define('nomina_cargo', {
  extend: 'siga.window',
  maximizable:false,
  width: 750,
  height: 420,
  resizable: false,
  title: "Nómina - Administrar Cargos",

  initComponent: function(){
    var me = this;

    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        fieldLabel: 'ID',
        value: ''
      },
      {
        xtype: "container",
        margin: "5 0 0 0",
        layout: "hbox",
        defaults: me.getInternal("field_defaults"),
        items: [
          {
            xtype:'textfield',
            id: me._('cargo'),
            name: 'cargo',
            fieldLabel: 'Cargo',
            fieldStyle: 'text-transform: uppercase;',
            value: '',
            flex:1
          },
          {
            xtype:'textfield',
            id: me._('orden'),
            name: 'orden',
            width: 80,
            fieldLabel: 'Orden',
            value: ''
          },
        ]
      },
      {
        xtype:'textfield',
        id: me._('denominacion'),
        name: 'denominacion',
        anchor: "100%",
        fieldLabel: 'Demominación',
        value: ''
      },
    ];


    me.callParent(arguments);
    me.setAccess(siga.getAccess('nomina'));

    me.internal.sort=[{property: 'orden', direction: 'ASC'}, {property: 'cargo', direction: 'ASC'}];

    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','cargo','denominacion'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/nomina_cargo/',
          actionMethods: {read: "POST"},//actionMethods:'POST',
          timeout: 3600000,
          reader: {
              type: 'json',
              rootProperty: 'result',
              totalProperty:'total'
          },
          extraParams: {
              action: 'onList',
              id_item_tipo: '',
              text: ''
          }
      },
      listeners: {
        load: function(store, records, successful){
          me.getCmp('gridList').getSelectionModel().deselectAll();
        },
        beforeload: function(store,operation,eOpts){
          store.proxy.extraParams.id_item_tipo=me.internal.id_item_tipo;
          store.proxy.extraParams.text=me.getCmp('txtSearch').getValue();
        }
      }
    });

    var columns=[
      {
        xtype: 'gridcolumn',
        dataIndex: 'cargo',
        text: '<b>Cargo</b>',
        flex: 1,
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'denominacion',
        text: '<b>Demominación</b>',
        width: '50%',
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
        dataIndex: 'orden',
        text: '<b>Orden</b>',
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
    ];

    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('gridList').bindStore(store);
    me.getCmp('pagingList').bindStore(store);
  },

  init: function(){
    var me=this;

  },

  onNew: function(){
    var me=this;
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    me.onSearch();
  },

  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    if(!_id) return;

    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_cargo/',
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
        me.getCmp('cargo').setValue(result[0]['cargo']);
        me.getCmp('denominacion').setValue(result[0]['denominacion']);
        me.getCmp('orden').setValue(result[0]['orden']);
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
    me.getCmp('tab_data').submit({
      method: 'POST',
      url:'module/nomina_cargo/',
      params:{
        action: 'onSave'
      },
      waitMsg: 'Guardando... por favor espere!',
      success: function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.onNew();
        me.setMessage(result.message,"green");
      },
      failure:function(form,o){
        Ext.MessageBox.hide();
        var result = Ext.decode(o.response.responseText);
        me.setMessage(result.message,"red");
      }
    });
  },

  onDelete: function(){
    var me=this;
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    if(!_id) return;

    Ext.MessageBox.wait('Eliminando... por favor espere!');
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_cargo/',
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
      me.getCmp("cargo").getValue(),
      me.getCmp("denominacion").getValue(),
      me.getCmp("orden").getValue()
    ];
  },

  onPaste: function(){
    var me=this;
    me.getCmp("cargo").setValue(me.internal.toCopy[0]);
    me.getCmp("denominacion").setValue(me.internal.toCopy[1]);
    me.getCmp("orden").setValue(me.internal.toCopy[2])
  }

});
