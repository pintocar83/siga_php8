siga.define('orden_pago/forma_pago', {
  extend: 'siga.windowBase',
  title: 'Forma de Pago',
  renderTo: Ext.getBody(),
  modal: true,
  width: 630,
  height: 310,

  /*bodyStyle: {
    "background": "#e8e8e8",
    "border": "none",
    "padding": "20px 20px 0px 20px",
  },*/

  listeners: {
    beforeClose: function(){
      var me = this;
        //me.onSaveDB();
      return true;
    }
  },

  initComponent: function(){
    var me = this;

    me.itemsToolbar=null;

    me.internal.cuenta_destino=[];
    me.internal.cuenta_destino_menu=null;

    if(!siga.banco_data){
      _tmp=Ext.Ajax.request({
        async: false,
        url:"module/banco/",
        params: {
          action: 'onList',
          sort: '[{"property": "codigo", "direction": "ASC"}]',
          start: 0,
          limit: 'ALL'
        }
      });
      if(_tmp.statusText=="OK"){
        siga.banco_data=Ext.JSON.decode(_tmp.responseText)?.result;
      }
    }

    me.items=[
      {
        xtype: 'tabpanel',
        id: me._('tabs'),
        margin: 20,
        collapsed: false,
        frameHeader: false,
        activeTab: 0,
        plain: true,
        items: [
          {
            xtype: 'form',
            frame: false,
            id: me._('tab_data'),
            frameHeader: false,
            autoScroll:true,
            layout: 'anchor',
            title: 'Entrada de datos',
            defaults: me.getInternal("field_defaults"),
            items: [
              {
                xtype: 'label',
                id: me._('message'),
                style:'margin: 5px 0px 0px 0px; text-align: center; font-style: italic;',
                html: "&nbsp;",
                width: '100%',
                flex: 1,
              },
              {
                xtype:'combobox',
                id: me._('id_banco_cuenta_origen'),
                width: 500,
                fieldLabel: 'Cuenta Origen',
                queryMode: 'local',
                store: {
                  fields: ['id','denominacion'],
                  autoLoad: true,
                  pageSize: 1000,
                  proxy: {
                    type:'ajax',
                    url: 'module/banco_cuenta/',
                    actionMethods: {read: "POST"},
                    timeout: 3600000,
                    reader: {
                      type: 'json',
                      rootProperty: 'result',
                      totalProperty:'total'
                    },
                    extraParams: {
                      action: 'onList',
                      text: '',
                      sort: '[{"property": "denominacion", "direction": "ASC"}]'
                    }
                  }
                },
                valueField: 'id',
                tpl: Ext.create('Ext.XTemplate',
                  '<ul class="x-list-plain"><tpl for=".">',
                  '<li role="option" class="x-boundlist-item"><div style="line-height: 125%; padding: 3px 0; border-bottom: 1px dotted lightgrey;"><b>{numero_cuenta}</b><br /><small>{denominacion}</small></div></li>',
                  '</tpl></ul>'
                  ),
                displayTpl: Ext.create('Ext.XTemplate',
                  '<tpl for=".">',
                  '{numero_cuenta} - {denominacion}',
                  '</tpl>'
                  ),
                value: "",
                allowBlank: true,
                editable: false,
                forceSelection: true,
              },
              {
                xtype: 'fieldcontainer',
                fieldLabel: 'Cuenta Destino',
                layout: 'hbox',
                items: [
                  {
                    xtype: "textfield",
                    id: me._('cuenta_destino'),
                    value: '',
                    flex: 1
                  },
                  {
                    xtype: 'button',
                    id: me._('btn_seleccionar_cuenta_destino'),
                    tooltip: 'Seleccionar Cuenta',
                    iconCls: 'siga-icon-16 icon-find',
                    listeners: {
                      click: function(){
                        me.onSeleccionarCuentaDestino();
                      }
                    }
                  },
                  {
                    xtype: 'button',
                    tooltip: 'Limpiar',
                    iconCls: 'siga-icon-16 icon-clear',
                    listeners: {
                      click: function(){
                        me.onLimpiarCuentaDestino();
                      }
                    }
                  }

                ]
              },
              {
                xtype: "container",
                layout: "hbox",
                margin: '5 0 0 0',
                defaults: me.getInternal("field_defaults"),
                items: [
                  /*{
                    xtype: "datefield",
                    id: me._('fecha'),
                    fieldLabel: "Fecha",
                    submitFormat: 'Y-m-d',
                    value: now("Y-m-d"),
                    width: 110,
                  },*/
                  {
                    xtype:'combobox',
                    id: me._('forma_pago'),
                    flex: 1,
                    fieldLabel: 'Forma de Pago',
                    queryMode: 'local',
                    margin: '0 40 0 40',
                    store: {
                      fields: ['id', 'denominacion'],
                      data: [
                        {id: 'cheque', denominacion: 'CHEQUE'},
                        {id: 'transferencia', denominacion: 'TRANSFERENCIA'},
                        {id: 'deposito', denominacion: 'DEPOSITO'},
                        {id: 'pago_movil', denominacion: 'PAGO MOVIL'},
                        {id: 'efectivo', denominacion: 'EFECTIVO'}
                      ]
                    },
                    valueField: 'id',
                    displayField: 'denominacion',
                    value: "transferencia",
                    allowBlank: false,
                    editable: false,
                    forceSelection: true,
                  },
                  {
                    xtype:'numberfield',
                    id: me._('monto'),
                    width: 110,
                    maxWidth: 110,
                    fieldLabel: 'Monto',
                    margin: '0 40 0 0',
                    value: 0,
                    minValue: 0,
                    fieldStyle: 'text-align: right',
                    decimalSeparator: ".",
                    decimalPrecision: 2,
                    hideTrigger: true,
                    keyNavEnabled: false,
                    mouseWheelEnabled: false,
                    valueToRaw: function (value){
                      var tmp=String(value).split(".");
                      return tmp[0]+"."+(((tmp.length==2?tmp[1]:"0")+"00").substring(0,2));
                    }
                  },
                ]
              },
              {
                xtype: 'container',
                layout: 'hbox',
                margin: "30 40 0 40",
                items:[
                  {
                    xtype:'tbspacer',
                    flex:1
                  },
                  {
                    xtype: 'button',
                    text: 'Cerrar',
                    tooltip: 'Cerrar',
                    iconCls: 'icon-close',
                    width: 80,
                    listeners: {
                      click: function(){
                        me.close();
                      }
                    }
                  },
                  {
                    xtype: 'button',
                    text: 'Aceptar',
                    tooltip: 'Aceptar',
                    iconCls: 'siga-icon-16 icon-accept',
                    width: 80,
                    margin: "0 0 0 10",
                    listeners: {
                      click: function(){
                        me.onAccept();
                      }
                    }
                  },
                ]
              }
            ]
          }
        ]
      }
    ];



    me.callParent(arguments);

    if(me.parameter.cuenta_destino)
      me.internal.cuenta_destino=me.parameter.cuenta_destino;

    me.getCmp("monto").setValue(me.parameter.monto ? me.parameter.monto : 0);

    if(me.parameter.cuenta_origen_id){
      me.getCmp("id_banco_cuenta_origen").setValue(me.parameter.cuenta_origen_id);
    }
    if(me.parameter.cuenta_destino_numero){
      me.getCmp("cuenta_destino").setValue(me.parameter.cuenta_destino_numero);
    }
    if(me.parameter.forma_pago){
      me.getCmp("forma_pago").setValue(me.parameter.forma_pago);
    }
  },


  onNew: function() {
    var me=this;

    //me.getCmp("fecha").setValue("");
    me.getCmp("cuenta_destino").setValue("");
    me.getCmp("monto").setValue(0);

  },

  onAccept: function(){
    const me=this;
    if(me.parameter.onAccept){
      //var fecha=Ext.Date.format(me.getCmp("fecha").getValue(),"Y-m-d");
      var forma_pago=me.getCmp("forma_pago").getValue();
      var id_cuenta_origen=me.getCmp("id_banco_cuenta_origen").getValue();
      var cuenta_origen = null;
      if(id_cuenta_origen){
        cuenta_origen = me.getCmp("id_banco_cuenta_origen").store.getData().getByKey(id_cuenta_origen);

      }

      var monto = me.getCmp("monto").getValue();
      var cuenta_destino = Ext.String.trim(me.getCmp("cuenta_destino").getValue());

      var banco_destino=siga?.banco_data?.find((v)=>cuenta_destino?.substr(0,4)===v['codigo'])?.banco;

      me.parameter.onAccept({
        //fecha: fecha,
        forma_pago: forma_pago,
        cuenta_origen: {
          id: id_cuenta_origen,
          numero: cuenta_origen ? cuenta_origen.get('numero_cuenta') : '',
          banco: cuenta_origen ? cuenta_origen.get('banco') : '',
        },
        cuenta_destino: {
          numero: cuenta_destino,
          banco: banco_destino ?? '',
        },
        monto: monto,
      });
      me.close();
    }
  },

  onSeleccionarCuentaDestino: function() {
    const me=this;

    if(!me.internal.cuenta_destino || me.internal.cuenta_destino.length===0)
      return;

    if(!me.internal.cuenta_destino_menu){
      var items=me.internal.cuenta_destino.map((row)=>{
        return {
          xtype: 'menuitem',
          text: row,
          handler: function(){
            me.getCmp("cuenta_destino").setValue(row);
          }
        }
      });

      me.internal.cuenta_destino_menu=Ext.create("Ext.menu.Menu",{
        renderTo: Ext.getBody(),
        floating: true,
        ignoreParentClicks: true,
        items: items
      });
      me.internal.cuenta_destino_menu.alignTo(me.getCmp("btn_seleccionar_cuenta_destino"),'tr-br?',[0,0]);

    }

    if(me.internal.cuenta_destino_menu && me.internal.cuenta_destino_menu.isVisible())
      me.internal.cuenta_destino_menu.hide();

    if(me.internal.cuenta_destino_menu.isVisible())
      me.internal.cuenta_destino_menu.hide();
    else{
      me.internal.cuenta_destino_menu.show();
    }

  },

  onLimpiarCuentaDestino: function(){
    const me=this;
    me.getCmp("cuenta_destino").setValue('');
  },

});