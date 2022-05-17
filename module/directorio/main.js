siga.define('directorio', {
  extend: 'siga.windowForm',
  title: 'Directorio Telefónico', 
  width: 850,
  height: 540,
  
  initComponent: function(){
    var me = this;
    
    me.itemsToolbar=[
      {
        xtype: 'button',
        id: me._('btnPersonal'),
        height: 45,
        width: 70,
        text: 'Personal',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-ficha',
        iconAlign: 'top',
        tooltip: 'Personal de la Institución',
        toggleGroup: "persona",
        listeners: {
          click: function(){
            me.getCmp('btnPersonal').toggle(true);
            me.onSearch();
          }
        }
      },
      {
        xtype: 'button',
        id: me._('btnProveedores'),
        height: 45,
        width: 70,
        text: 'Proveedores',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-proveedor',
        iconAlign: 'top',
        tooltip: 'Proveedores',
        toggleGroup: "persona",
        listeners: {
            click: function(){
              me.getCmp('btnProveedores').toggle(true);
              me.onSearch();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnBeneficiarios'),
        height: 45,
        width: 70,
        text: 'Beneficiarios',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-beneficiario',
        iconAlign: 'top',
        tooltip: 'Beneficiarios',
        toggleGroup: "persona",
        listeners: {
            click: function(){
              me.getCmp('btnBeneficiarios').toggle(true);
              me.onSearch();
            }
        }
      },
    ];
    
    me.items=[      
      {
        xtype: 'gridpanel',
        id: me._('gridList'),
        border: 1,
        preventHeader: true,
        anchor: "100%",
        height: 380,
        scroll: 'vertical',
        style: "padding-top: 15px;",
        columns: [
          {
            xtype: 'rownumberer',
            width: '5%',
            align: 'center'
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'identificacion',
            id: me._('gridcolumn_identificacion'),
            text: '',
            width: '11%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'denominacion',
            id: me._('gridcolumn_denominacion'),
            text: '',
            width: '30%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'extension',
            id: me._('gridcolumn_extension'),
            text: '<b>Extensión</b>',
            width: '10%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'telefono',
            text: '<b>Teléfono</b>',
            width: '25%',
            menuDisabled: true,
            sortable: false,
          },          
          {
            xtype: 'gridcolumn',
            dataIndex: 'correo',
            text: '<b>Correo</b>',
            flex: 1,
            menuDisabled: true,
            sortable: false,
          }
        ],
        dockedItems: [
            {
                xtype: 'pagingtoolbar',
                id: me._('pagingList'),
                afterPageText: 'de {0}',
                beforePageText: 'P\u00e1gina',
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: 'No hay datos que mostrar',
                firstText: 'Primera p\u00e1gina',
                dock: 'bottom',
                listeners: {
                    render: function( cmp, eOpts ){                               
                      //me.onSearch();//temporal, error en extjs6 -> no carga el paginado cuando el elemento esta oculto.
                    }
                },
            }
        ],
        store: {
          fields: ['id_persona','identificacion','denominacion','telefono','correo','extension','identificacion_tipo','identificacion_numero'],
          autoLoad: false,
          pageSize: 100,
          remoteSort: true,
          //sorters: [{property: 'identificacion_tipo', direction: 'ASC'},{property: 'identificacion_numero', direction: 'ASC'}],
          sorters: [{property: 'denominacion', direction: 'ASC'}],
          proxy: {
              type:'ajax',
              url: 'module/directorio/',
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
                tipo:''
              }
          },
          listeners: {
            load: function(store, records, successful){
              //me.getCmp('gridList').getSelectionModel().deselectAll();
            },
            beforeload: function(store,operation,eOpts){
              var tipo="";
              var text_identificacion="";
              var text_denominacion="";              
              if(me.getCmp('btnPersonal').pressed){
                tipo="F";
                text_identificacion="<b>Cédula</b>";
                text_denominacion="<b>Nombres/Apellidos</b>";
                me.getCmp("gridcolumn_extension").setVisible(true);
              }
              else if(me.getCmp('btnProveedores').pressed){
                tipo="J";
                text_identificacion="<b>RIF</b>";
                text_denominacion="<b>Denominación</b>";
                me.getCmp("gridcolumn_extension").setVisible(false);
              }
              else if(me.getCmp('btnBeneficiarios').pressed){
                tipo="N";
                text_identificacion="<b>Cédula</b>";
                text_denominacion="<b>Nombres/Apellidos</b>";
                me.getCmp("gridcolumn_extension").setVisible(false);
              }
              
              me.getCmp("gridcolumn_identificacion").setText(text_identificacion);
              me.getCmp("gridcolumn_denominacion").setText(text_denominacion);
                
              store.proxy.extraParams.tipo=tipo;
              store.proxy.extraParams.text=me.getCmp('txtSearch').getValue();
            }
          }
        },
        listeners: {
          itemdblclick: function(dataview, record, item, index, e){
            var text_identificacion="";
            var text_denominacion="";
            if(me.getCmp('btnPersonal').pressed || me.getCmp('btnBeneficiarios').pressed){                
              text_identificacion="Cédula";
              text_denominacion="Nombres/Apellidos";
            }
            else if(me.getCmp('btnProveedores').pressed){
              text_identificacion="RIF";
              text_denominacion="Denominación";
            }
            var add="";
            if(record.get('extension')) {
              add="<br><b>Extensión: </b>"+record.get('extension');
            }
            
            Ext.Msg.alert(me.title, "<p style='line-height: 140%; margin: 0px 20px 0px 20px;'><b>"+text_identificacion+":</b> "+record.get('identificacion')+"<br><b>"+text_denominacion+": </b>"+record.get('denominacion')+"<br><b>Teléfono: </b>"+record.get('telefono')+"<br><b>Correo: </b>"+record.get('correo')+add+"</p>");
            
            
          }
        }
      },
      {
        xtype: 'container',
        id: me._('container'),
        margin: "0 40 0 40",
        layout: {
          align: 'middle',
          type: 'hbox'
        },
        dock: 'bottom',
        items: [
          {
            xtype: 'textfield',
            id: me._('txtSearch'),
            hideLabel: false,
            flex: 1,
            emptyText: 'Escriba el nombre y presione enter para buscar',
            listeners: {
              specialkey: function(field, e){
                if (e.getKey() == e.ENTER) 
                  me.onSearch();
              }
            }
          },
          {
            xtype: 'button',
            id: me._('btnSearch'),
            text: 'Buscar',
            tooltip: 'Buscar',
            iconCls: 'siga-icon-16 icon-find',
            width: 80,
            listeners: {
              click: function(){
                me.onSearch();
              }
            }
          },
          {
            xtype: 'button',
            id: me._('btnClear'),
            text: 'Limpiar',
            tooltip: 'Limpiar',
            iconCls: 'siga-icon-16 icon-clear',
            width: 80,
            listeners: {
              click: function(){
                me.getCmp('txtSearch').setValue('');
                me.onSearch();
              }
            }
          }
        ]
      },
    ];
    
    me.callParent(arguments);
    me.getCmp('pagingList').bindStore(me.getCmp('gridList').store);    
  },
  
  init: function(){
    var me=this;
    me.getCmp("btnPersonal").toggle();
    me.onSearch();
    me.getCmp('txtSearch').focus();
  },
  
  onSearch: function(){
    var me=this;
    if(!siga.value("sesion_abierta"))
      me.setMessage("Debe iniciar sesión para ver los teléfonos y correos en el directorio.","red");
    me.getCmp('gridList').store.loadPage(1);
  }  
});