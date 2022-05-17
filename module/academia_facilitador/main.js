siga.define('academia_facilitador', {
  extend: 'siga.window',
  title: 'Academia de Software Libre - Facilitador',      
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
        xtype:'siga.select',
        id: me._('id_persona'),
        name: 'id_persona',
        anchor: "100%",
        fieldLabel: 'Persona',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["identificacion","denominacion"], title: ["Cédula","Nombres / Apellidos"], width: ['25%','75%'], sort: ["ASC","ASC"]},
          url: 'module/persona/',
          actionOnList:'onList_PersonaNatural',
          actionOnGet:'onGet_PersonaNatural',
          output: 0,
        }
      },
      {
        xtype:'textarea',
        id: me._('notas'),
        name: 'notas',
        fieldLabel: 'Notas / Observaciones',
        value: ''
      }      
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("academia_facilitador"));
    
    me.internal.sort=[{property: 'nombres_apellidos', direction: 'ASC'},{property: 'nacionalidad_cedula', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','nacionalidad_cedula','nombres_apellidos'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/academia_facilitador/',
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
        dataIndex: 'nacionalidad_cedula',
        text: '<b>Cédula</b>',
        width: '15%',
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
        dataIndex: 'nombres_apellidos',
        text: '<b>Nombres / Apellidos</b>',
        flex: 1,
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
    var id_persona=record.get("id_persona");
    var notas=record.get("notas");
    
    me.getCmp('id').setValue(id);
    me.getCmp('id_persona').setValue(id_persona);
    me.getCmp('notas').setValue(notas);
    },

  onSave: function(){
    var me=this;
    
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    var _id_persona=Ext.String.trim(me.getCmp("id_persona").getValue());
    var _notas=Ext.String.trim(me.getCmp("notas").getValue());
    
    if(!_id_persona){
      me.setMessage("Debe seleccionar la persona.","red");
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
      url:'module/academia_facilitador/',
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
        url:'module/academia_facilitador/',
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
    /*me.internal.toCopy=[
      me.getCmp("nombre").getValue()
      ];*/
  },
  
  onPaste: function(){
    var me=this;
    //me.getCmp("nombre").setValue(me.internal.toCopy[0]);
  },
  
});
