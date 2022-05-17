siga.define('nomina_escala_salarial', {
  extend: 'siga.window',
  maximizable:false,
  width: 650,
  height: 420,
  resizable: false,
  title: "Escala Salarial",
  
  initComponent: function(){
    var me = this;

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
      {
        xtype:'textfield',
        id: me._('escala'),        
        name: 'escala',
        fieldLabel: 'Escala Salarial',
        fieldStyle: 'text-transform: uppercase;',
        value: '',
        anchor: "100%",//width: 510
      },
      {
        xtype:'textfield',
        id: me._('sueldo_basico'),
        name: 'sueldo_basico',
        anchor: "50%",//width: 230,
        fieldLabel: 'Sueldo Básico',
        value: ''
      },     
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess('item'));
    //me.setAccess(define['modulo_base/item->access']);
    me.internal.sort=[{property: 'escala', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','escala','sueldo_basico'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/nomina_escala_salarial/',
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
        dataIndex: 'escala',
        text: '<b>Escala Salarial</b>',
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
        dataIndex: 'sueldo_basico',
        text: '<b>Sueldo Básico</b>',
        width: '40%',
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
      url:'module/nomina_escala_salarial/',
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
        me.getCmp('escala').setValue(result[0]['escala']);
        me.getCmp('sueldo_basico').setValue(result[0]['sueldo_basico']);
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
      url:'module/nomina_escala_salarial/',
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
      url:'module/nomina_escala_salarial/',
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
      me.getCmp("escala").getValue(),
      me.getCmp("sueldo_basico").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;    
    me.getCmp("escala").setValue(me.internal.toCopy[0]);
    me.getCmp("sueldo_basico").setValue(me.internal.toCopy[1])
  }
  
});
