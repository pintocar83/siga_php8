siga.define('detalle_contable', {
    extend: 'siga.windowBase',
    title: 'Detalle Contable',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 285,

    initComponent: function(){
        var me = this;

        var _tooltip="";
        if(me.parameter)
            if(me.parameter.tooltip)
                _tooltip=me.parameter.tooltip;
        var _monto=0;
        if(me.parameter)
            if(me.parameter.monto)
                _monto=me.parameter.monto;

        me.setInternal({
            data:{
                id_cuenta_contable: "",
                cuenta_contable: "",
                denominacion_contable: "",
                monto: 0,
                operacion: ""
            }
        });

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
                                xtype: 'fieldcontainer',
                                fieldLabel: 'Cuenta Contable',
                                layout: 'hbox',
                                items:[
                                    {
                                        xtype: 'textfield',
                                        id: me._('cuenta_contable'),
                                        name: 'cuenta_contable',
                                        width: '23%',
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: me._('denominacion_contable'),
                                        name: 'denominacion_contable',
                                        flex: 1,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'button',
                                        //tooltip: 'Cuentas por pagar a proveedores/beneficiarios',
                                        tooltip: _tooltip,
                                        iconCls: 'icon-proveedor-beneficiario',
                                        listeners: {
                                            click: function(){
                                                if(me.parameter){
                                                    if(me.parameter.id_cuenta_contable){
                                                        me.getCmp("monto").setValue(_monto);
                                                        me.onSetCuentaContable(me.parameter.id_cuenta_contable);
                                                    }
                                                    if(me.parameter.operacion)
                                                        me.getCmp("operacion").setValue(me.parameter.operacion);
                                                    if(me.parameter.monto){
                                                        var _monto=0;
                                                        if(typeof(me.parameter.monto) == "function")
                                                            _monto=me.parameter.monto();
                                                        else
                                                            _monto=me.parameter.monto;
                                                        me.getCmp("monto").setValue(_monto);
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'button',
                                        tooltip: 'Cuentas Contables',
                                        iconCls: 'siga-icon-16 icon-find-all',
                                        listeners: {
                                            click: function(){
                                                var selector=Ext.create("siga.windowSelect", {
                                                    internal: {
                                                        parent: {
                                                            fieldLabel: 'Cuentas Contables',
                                                            internal:{
                                                                page:1,
                                                                limit: 300,
                                                                valueField: 'id_cuenta_contable',
                                                                columns: {field: ["cuenta_contable","denominacion"], title: ["Cuenta Contable","Denominación"], width: ['20%','80%'], sort: ["ASC",""]},
                                                                url: 'module/cuenta_contable/',
                                                                actionOnList: 'onList',
                                                                onAccept: function(){}
                                                            },
                                                            setValue: function(v){
                                                                me.onSetCuentaContable(v);
                                                            }
                                                        }
                                                    }
                                                });
                                                selector.show();
                                                selector.search();
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'button',
                                        tooltip: 'Limpiar',
                                        iconCls: 'siga-icon-16 icon-clear',
                                        listeners: {
                                            click: function(){
                                                me.onLimpiar();
                                            }
                                        }
                                    }


                                ]
                            },
                            {
                                xtype: 'combobox',
                                id: me._('operacion'),
                                name: 'operacion',
                                fieldLabel: 'Operación',
                                editable: false,
                                anchor: '40%',
                                queryMode: 'local',
                                store: {
                                    fields: ['id', 'denominacion'],
                                    data: [
                                        {id: 'D', denominacion: 'DEBE'},
                                        {id: 'H', denominacion: 'HABER'}
                                    ]
                                },
                                displayField: 'denominacion',
                                valueField: 'id',
                                value: 'D',
                                allowBlank: false,
                                forceSelection: true
                            },
                            {
                                xtype: 'numberfield',
                                id: me._('monto'),
                                name: 'monto',
                                fieldLabel: 'Monto',
                                anchor: '40%',
                                value: '0',
                                allowDecimals: true,
                                hideTrigger: true,
                                keyNavEnabled: false,
                                mouseWheelEnabled: false,
                                decimalSeparator: '.',
                                decimalPrecision: 2
                            },
                            {
                                xtype: 'container',
                                layout: 'hbox',
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
                                        text: 'Agregar',
                                        tooltip: 'Agregar',
                                        iconCls: 'siga-icon-16 icon-add',
                                        width: 80,
                                        margin: "0 0 0 10",
                                        listeners: {
                                            click: function(){
                                                if(!me.internal.data.id_cuenta_contable){
                                                    me.setMessage("Error. Debe seleccionar la cuenta contable.","red");
                                                    return;
                                                }
                                                me.internal.data.monto=me.getCmp("monto").getValue();
                                                if(!me.internal.data.monto)
                                                    me.internal.data.monto=0;

                                                me.internal.data.operacion=me.getCmp("operacion").getValue();

                                                if(me.parameter)
                                                    if(me.parameter.onAdd){
                                                        me.parameter.onAdd(me);
                                                        me.internal.data.monto=0;
                                                        me.getCmp("monto").setValue("0");
                                                        me.onLimpiar();
                                                    }
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
    },

    onLimpiar: function(){
        var me=this;

        me.internal.data.id_cuenta_contable="";
        me.internal.data.denominacion_contable="";
        me.internal.data.cuenta_contable="";

        me.getCmp("cuenta_contable").setValue("");
        me.getCmp("denominacion_contable").setValue("");
        me.setMessage();
    },

    onSetCuentaContable: function(v){
        var me=this;
        me.onLimpiar();

        var resp=Ext.Ajax.request({
            async: false,
            url: "module/cuenta_contable/",
            //params: Ext.JSON.decode("{action: 'onGet', id_cuenta_contable: '"+v+"'}")
            params: {action: 'onGet', id_cuenta_contable: v}
        });

        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.setMessage("Error. La cuenta contable seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("cuenta_contable").setValue(retorno[0]['cuenta_contable']);
            me.getCmp("denominacion_contable").setValue(retorno[0]['denominacion']);


            me.internal.data.id_cuenta_contable=retorno[0]['id_cuenta_contable'];
            me.internal.data.denominacion_contable=retorno[0]['denominacion'];
            me.internal.data.cuenta_contable=retorno[0]['cuenta_contable'];
        }
        else
            me.setMessage("Error. No pudo realizar la consulta.","red",30000);
    }
});