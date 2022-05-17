siga.define('meta_fisica/unidad_medida', {
  extend: 'siga.window',
  title: 'Meta Física - Unidades de Medida',      
  maximizable:false,
  width: 600,
  height: 450,
  resizable: false,
  initComponent: function(){
    var me = this;
    
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        value: ''
      },
      {
        xtype:'textarea',
        id: me._('unidad_medida'),
        name: 'unidad_medida',
        fieldLabel: 'Unidad de Medida',
        value: ''
      }      
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("meta_fisica/unidad_medida"));
    
    me.internal.sort=[{property: 'unidad_medida', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','unidad_medida'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/meta_fisica/unidad_medida/',
          actionMethods: {read: "POST"},//actionMethods:'POST',
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
        dataIndex: 'unidad_medida',
        text: '<b>Unidad de Medida</b>',
        width: '100%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      }
    ];
    
    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('pagingList').bindStore(store);
  },
  
  init: function(){
    var me=this;
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;
    var id=record.get("id");
    var nombre=record.get("unidad_medida");
    
    me.getCmp('id').setValue(id);
    me.getCmp('unidad_medida').setValue(nombre);
    },

  onSave: function(){
    var me=this;
    
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    var _unidad_medida=Ext.String.trim(me.getCmp("unidad_medida").getValue());
    
    if(!_unidad_medida){
      me.setMessage("Debe completar el campo unidad de medida.","red");
      return;
    }    
    
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
      url:'module/meta_fisica/unidad_medida/',
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
        var result=o.result;
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
        url:'module/meta_fisica/unidad_medida/',
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
      me.getCmp("unidad_medida").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;
    me.getCmp("unidad_medida").setValue(me.internal.toCopy[0]);
  },
  
});
