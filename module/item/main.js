siga.define('item', {
  extend: 'siga.window',
  maximizable:false,
  width: 650,
  height: 420,
  resizable: false,
  
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
        id: me._('codigo'),
        name: 'codigo',
        anchor: "50%",//width: 230,
        fieldLabel: 'Código',
        readOnly: true,
        value: ''
      }, 
      {
        xtype:'textarea',
        id: me._('denominacion'),        
        name: 'denominacion',
        fieldLabel: 'Denominación',
        fieldStyle: 'text-transform: uppercase;',
        value: '',
        height: 67,
        anchor: "100%",//width: 510
      },
      {
        xtype:'siga.select',
        id: me._('id_cuenta_presupuestaria'),
        name: 'id_cuenta_presupuestaria',
        anchor: "100%",//width: 510,
        fieldLabel: 'Partida Presupuestaria',
        value: '',
        internal:{
          valueField: 'id_cuenta_presupuestaria',
          columns: {field: ["cuenta_presupuestaria","denominacion","padre"], title: ["Partida","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
          url: 'module/cuenta_presupuestaria/',
          actionOnList: 'onList',
          actionOnGet: 'onGet',
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
          }
        }
      },
      {
        xtype:'combobox',
        id: me._('aplica_iva'),
        name: 'aplica_iva',
        anchor: "50%",//width: 230,
        fieldLabel: 'Aplica I.V.A.',
        store: {
          fields: ['id', 'nombre'],
          data : [            
            {"id":"t", "nombre":"SI"},
            {"id":"f", "nombre":"NO"}
          ]                      
        },
        displayField: 'nombre',
        valueField: 'id',
        allowBlank: false,
        forceSelection: true,                    
        value: 't',
        editable: false
      }      
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess('item'));
    //me.setAccess(define['modulo_base/item->access']);
    me.internal.sort=[{property: 'denominacion', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','codigo','denominacion','cuenta_presupuestaria'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/item/',
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
        dataIndex: 'codigo',
        text: 'Código',
        width: '10%',
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
        text: 'Denominación',
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
      {
        xtype: 'gridcolumn',
        dataIndex: 'cuenta_presupuestaria',
        text: 'Partida Presupuestaria',
        width: '50%',
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[2].direction,
        flex: 1,
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
    
  },
  
  
  onGetCodigo: function(){
    var me=this;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/item/',
      params:{
        action: 'onGetCodigo',
        id_item_tipo: me.internal.id_item_tipo
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        me.getCmp("codigo").setValue(result);
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
    
    
  },
  
  onNew: function(){
    var me=this;
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    me.onGetCodigo();
    me.onSearch();
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    if(!_id) return;
    
    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/item/',
      params:{
        action: 'onGet',
        id_item_tipo: me.internal.id_item_tipo,
        id: _id
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        if(!result || result.length==0){
          alert("Error al cargar los datos.");
          return;
        }        
        me.getCmp('id').setValue(result[0]['id']);
        me.internal.id_item_tipo=result[0]['id_item_tipo'];
        me.getCmp('codigo').setValue(result[0]['codigo']);
        me.getCmp('denominacion').setValue(result[0]['item']);
        me.getCmp('id_cuenta_presupuestaria').setValue(result[0]['id_cuenta_presupuestaria']);
        me.getCmp('aplica_iva').setValue(result[0]['aplica_iva']);
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
      url:'module/item/',
      params:{
        action: 'onSave',
        id_item_tipo: me.internal.id_item_tipo
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
        if(result.action==1)
          me.onGetCodigo();
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
      url:'module/item/',
      params:{
        action: 'onDelete',
        id_item_tipo: me.internal.id_item_tipo,
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
      me.getCmp("denominacion").getValue(),
      me.getCmp("id_cuenta_presupuestaria").getValue(),
      me.getCmp("aplica_iva").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;    
    me.getCmp("denominacion").setValue(me.internal.toCopy[0]);
    me.getCmp("id_cuenta_presupuestaria").setValue(me.internal.toCopy[1]);
    me.getCmp("aplica_iva").setValue(me.internal.toCopy[2]);
  }
  
});
