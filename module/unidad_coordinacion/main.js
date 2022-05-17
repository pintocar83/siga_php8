siga.define('unidad_coordinacion', {
  extend: 'siga.window',
  title: 'Unidad / Coordinación',      
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
        id: me._('coordinacion'),
        name: 'coordinacion',
        fieldLabel: 'Unidad / Coordinación',
        value: ''
      }      
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("unidad_coordinacion"));
    
    me.internal.sort=[{property: 'coordinacion', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','coordinacion'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/unidad_coordinacion/',
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
        dataIndex: 'coordinacion',
        text: '<b>Unidad / Coordinación</b>',
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
    var coordinacion=record.get("coordinacion");
    
    me.getCmp('id').setValue(id);
    me.getCmp('coordinacion').setValue(coordinacion);
    },

  onSave: function(){
    var me=this;
    
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    var _coordinacion=Ext.String.trim(me.getCmp("coordinacion").getValue());
    
    if(!_coordinacion){
      me.setMessage("Debe completar el campo unidad/coordinación.","red");
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
      url:'module/unidad_coordinacion/',
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
        url:'module/unidad_coordinacion/',
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
      me.getCmp("coordinacion").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;
    me.getCmp("coordinacion").setValue(me.internal.toCopy[0]);
  },
  
});
