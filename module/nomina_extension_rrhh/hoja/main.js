siga.define("nomina_extension_rrhh/hoja",{
  extend: 'siga.window',
  title: 'Nómina - Extensión RRHH / Administrar Hojas',
  width: 900,
  height: 700,

  onPreload: function(){
    var me=this;

    _tmp=Ext.Ajax.request({
      async: false,
      url:"module/nomina_extension_rrhh/hoja/",
      params: {
        action: 'onInit'
      }
    });

    if(_tmp.statusText=="OK"){
      me.internal.data.preload=Ext.JSON.decode(_tmp.responseText);
    }

    //ACTUALIZAR CAMPOS DEL TAB ENTRADA DE DATOS
    me.getCmp("tipo").getStore().setData(me.internal.data.preload["periodo_tipo"]);


  },

  initComponent: function(){
    var me = this;

    me.setInternal({
      data: {
        preload: {},
      },
    });

    _defaults=me.getInternal("field_defaults");

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
        anchor: "40%",
        maxLength: 10,
        enforceMaxLength: true,
      },
      {
        xtype:'textfield',
        id: me._('descripcion'),
        name: 'descripcion',
        fieldLabel: 'Descripción',
        fieldStyle: "text-transform: uppercase;",
        value: '',
        maxLength: 200,
        enforceMaxLength: true,
      },
      {
        xtype:'combobox',
        id: me._('tipo'),
        name: 'tipo',
        fieldLabel: 'Tipo de Nómina/Periodo',
        labelAlign: 'top',
        labelSeparator: '',
        labelStyle: 'font-weight: bold;',
        anchor: '100%',
        queryMode: "local",
        store: {
          fields: ['tipo','denominacion'],
          data: []
        },
        displayField: 'denominacion',
        valueField: 'tipo',
        allowBlank: false,
        forceSelection: true,
        editable: false,
        value: 'Q',
        listeners: {
          change: function(e, The, eOpts ){
            var tipo = me.getCmp("tipo").getValue();

            var data_periodo=me.getDataPeriodo({tipo: tipo});
            me.getCmp('id_periodo').getStore().setData(data_periodo);

            var data_nomina=me.getDataNomina({tipo: tipo});
            me.getCmp('id_nomina').getStore().setData(data_nomina);
          }
        }
      },
      {
        xtype: 'tagfield',
        id: me._('id_periodo'),
        name: 'id_periodo',
        anchor: '100%',
        fieldLabel: 'Periodo',
        labelAlign: 'top',
        labelSeparator: '',
        labelStyle: 'font-weight: bold;',
        cls: 'nomina_tagfield_fullwidth',
        editable: false,
        queryMode: "local",
        multiSelect: true,
        displayTpl: '<tpl for=".">{codigo} {descripcion}</tpl>',
        tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
        store: {
          fields: ['id','periodo'],
          data: []
        },
        displayField: 'periodo',
        valueField: 'id',
        allowBlank: true,
        forceSelection: true,
      },
      {
        xtype: 'tagfield',
        id: me._('id_nomina'),
        name: 'id_nomina',
        anchor: '100%',
        fieldLabel: 'Nómina',
        //fieldLabel: 'Nómina <div class="enlace_etiqueta_nomina"><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onNominaSeleccionarTodo()\" title="Seleccionar Todos">[&#x2714;]<a/><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onNominaSeleccionarNada()\" title="Seleccionar Ninguno">[&#x2716;]</a></div>',
        labelAlign: 'top',
        labelSeparator: '',
        labelStyle: 'font-weight: bold;',
        cls: 'nomina_tagfield_fullwidth',
        editable: false,
        queryMode: "local",
        multiSelect: true,
        store: {
          fields: ['id','codigo_nomina'],
          data: []
        },
        displayField: 'codigo_nomina',
        valueField: 'id',
        allowBlank: true,
        forceSelection: true,
        filterPickList: true,
        //hideTrigger: true,
        listeners: {
          change: function(){
            //me.onSeleccionarNominaHeight();
          }
        }
      },
      {
        xtype:'combobox',
        id: me._('activo'),
        name: 'activo',
        flex: 1,
        anchor: "25%",
        fieldLabel: 'Activo',
        store: {
          fields: ['id', 'nombre'],
          data : [
            {"id":"t", "nombre":"SI"},
            {"id":"f", "nombre":"NO"}
          ]
        },
        editable: false,
        displayField: 'nombre',
        valueField: 'id',
        allowBlank: false,
        forceSelection: true,
        value: 't'
      }
    ];

    me.callParent(arguments);


    //me.setAccess(siga.getAccess("modulo_base/usuarios"));

    me.internal.sort=[{property: 'codigo', direction: 'ASC'}];

    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','codigo','descripcion','activo'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/nomina_extension_rrhh/hoja/',
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
        dataIndex: 'activo',
        text: '<b>Activo</b>',
        width: '10%',
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[2].direction,
        renderer: function(value){
          if(value=='t' || value===true)
            return "SI";
          if(value=='f' || value===false)
            return "NO";
          return "";
        },
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        },
      }

    ];

    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('pagingList').bindStore(store);
    me.onPreload();
  },

  init: function(){
    var me=this;

    me.onNew();




  },

  onNew: function(){
    var me=this;
    me.getCmp('tab_data').getForm().reset();
    me.getCmp("tipo").setValue("Q");
    me.getCmp("tipo").fireEvent("change");

    me.getCmp('tabs').setActiveTab(0);

    me.onSearch();

    me.onGet_Codigo();
  },

  onGet_Codigo: function(){
    var me=this;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/hoja/',
      params:{
        action: 'onCorrelativo'
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
      url:'module/nomina_extension_rrhh/hoja/',
      params:{
        action: 'onGet',
        id: _id
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);

        me.getCmp("id").setValue(result[0]["id"]);
        me.getCmp("codigo").setValue(result[0]["codigo"]);
        me.getCmp("descripcion").setValue(result[0]["descripcion"]);
        me.getCmp("tipo").setValue(result[0]["tipo"]);
        me.getCmp("tipo").fireEvent("change");

        var id_periodo = String(result[0]["id_periodo"]).replace(/[{}]/gi,'').split(',');
        var id_nomina = String(result[0]["id_nomina"]).replace(/[{}]/gi,'').split(',');

        me.getCmp("id_periodo").setValue(id_periodo);
        me.getCmp("id_nomina").setValue(id_nomina);
        me.getCmp("activo").setValue(result[0]["activo"]);
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
    var _tipo=Ext.String.trim(me.getCmp("tipo").getValue());
    var _id_periodo=me.getCmp("id_periodo").getValue();
    var _id_nomina=me.getCmp("id_nomina").getValue();
    var _activo=me.getCmp("activo").getValue();

    var msgWait=Ext.Msg.wait('Guardando. Por favor espere...', me.getTitle(),{text:''});
    msgWait.setAlwaysOnTop(true);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/hoja/',
      params:{
        action: 'onSave',
        id: _id,
        codigo: _codigo,
        descripcion: _descripcion,
        tipo: _tipo,
        id_periodo: _id_periodo.join(","),
        id_nomina: _id_nomina.join(","),
        activo: _activo
      },
      success: function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);

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
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);
        me.setMessage(result.message,"red");
      }
    });



  },

  getDataNomina: function(o){
    var me=this;

    if(o.tipo){
      var tmp=[];
      for(var i = 0; i<me.internal.data.preload["nomina"].length; i++)
        if(me.internal.data.preload["nomina"][i]["tipo"]==o.tipo)
          tmp.push(me.internal.data.preload["nomina"][i]);
      return tmp;
    }
    return me.internal.data.preload["nomina"];
  },

  getDataPeriodo: function(o){
    var me=this;

    if(o.tipo){
      var tmp=[];
      for(var i = 0; i<me.internal.data.preload["periodo"].length; i++)
        if(me.internal.data.preload["periodo"][i]["tipo"]==o.tipo)
          tmp.push(me.internal.data.preload["periodo"][i]);
      return tmp;
    }
    return me.internal.data.preload["periodo"];
  },
});