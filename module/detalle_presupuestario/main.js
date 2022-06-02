siga.define('detalle_presupuestario', {
    extend: 'siga.windowBase',
    title: 'Detalle Presupuestario',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 330,

    initComponent: function(){
        var me = this;

        me.setInternal({
            data: {
                id_accion_subespecifica: "",
                id_cuenta_presupuestaria: "",
                cuenta_presupuestaria: "",
                denominacion_presupuestaria: "",
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
                                fieldLabel: 'Estructura Presupuestaria',
                                layout: 'hbox',
                                items:[
                                    {
                                        xtype: 'combobox',
                                        id: me._('id_accion_centralizada'),
                                        name: 'id_accion_centralizada',
                                        tpl: '<tpl for="."><div class="x-boundlist-item" title="{denominacion_centralizada}">{tipo_codigo_centralizada}</div></tpl>',
                                        fieldLabel: '',
                                        editable: false,
                                        width: 100,
                                        queryMode: 'local',
                                        store: {
                                            fields: ['id','tipo_codigo_centralizada','denominacion_centralizada'],
                                            autoLoad: false,
                                            pageSize: 100,
                                            proxy: {
                                                type:'ajax',
                                                url: 'module/estructura_presupuestaria/',
                                                actionMethods:  {read: "POST"},//actionMethods:'POST',
                                                timeout: 3600000,
                                                reader: {
                                                    type: 'json',
                                                    rootProperty: 'result',
                                                    totalProperty:'total'
                                                },
                                                extraParams: {
                                                    action: 'onList_AccionCentralizada_AP',
                                                    text: '',
                                                    id: '',
                                                    sort: '[{"property":"tipo_codigo_centralizada","direction":"ASC"}]'
                                                }
                                            },
                                            listeners: {
                                                load: function(store, records, successful){
                                                    me.getCmp("id_accion_centralizada").setValue(records[0].get("id"));
                                                },
                                                /*beforeload: function(store,operation,eOpts){
                                                    if(!me.getCmp("id_fuente_recursos").getValue())
                                                        return false;
                                                    return true;
                                                }*/
                                            }
                                        },
                                        displayField: 'tipo_codigo_centralizada',
                                        valueField: 'id',
                                        allowBlank: false,
                                        forceSelection: true,
                                        //value: '1',
                                        listeners:{
                                            change: function(e, newValue, oldValue, eOpts){
                                                //colorcar el title del elemento seleccionado
                                                var r=e.getStore().getById(e.getValue());
                                                if(!r) return;
                                                e.getEl().set({title:r.data.denominacion_centralizada});
                                                //cargar el combobox del accion especifica
                                                me.getCmp("id_accion_especifica").getStore().removeAll();
                                                me.getCmp("id_accion_especifica").getStore().load();
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combobox',
                                        id: me._('id_accion_especifica'),
                                        name: 'id_accion_especifica',
                                        tpl: '<tpl for="."><div class="x-boundlist-item" title="{denominacion_especifica}">{codigo_especifica}</div></tpl>',
                                        fieldLabel: '',
                                        editable: false,
                                        width: 50,
                                        margin: "0 0 0 10",
                                        queryMode: 'local',
                                        store: {
                                            fields: ['id','codigo_especifica','denominacion_especifica'],
                                            autoLoad: false,
                                            pageSize: 100,
                                            proxy: {
                                                type:'ajax',
                                                url: 'module/estructura_presupuestaria/',
                                                actionMethods:  {read: "POST"},//actionMethods:'POST',
                                                timeout: 3600000,
                                                reader: {
                                                    type: 'json',
                                                    rootProperty: 'result',
                                                    totalProperty:'total'
                                                },
                                                extraParams: {
                                                    action: 'onList_AccionEspecifica_AP',
                                                    text: '',
                                                    id: '',
                                                    sort: '[{"property":"codigo_especifica","direction":"ASC"}]'
                                                }
                                            },
                                            listeners: {
                                                load: function(store, records, successful){
                                                    me.getCmp("id_accion_especifica").setValue(records[0].get("id"));
                                                },
                                                beforeload: function(store,operation,eOpts){
                                                    if(!me.getCmp("id_accion_centralizada").getValue())
                                                        return false;
                                                    store.proxy.extraParams.id_accion_centralizada=me.getCmp('id_accion_centralizada').getValue();
                                                    return true;
                                                }
                                            }
                                        },
                                        displayField: 'codigo_especifica',
                                        valueField: 'id',
                                        allowBlank: false,
                                        forceSelection: true,
                                        //value: '1',
                                        listeners:{
                                            change: function(e, newValue, oldValue, eOpts){
                                                //colorcar el title del elemento seleccionado
                                                var r=e.getStore().getById(e.getValue());
                                                if(!r) return;
                                                e.getEl().set({title:r.data.denominacion_especifica});
                                                //cargar el combobox del accion subespecifica
                                                me.getCmp("id_accion_subespecifica").getStore().removeAll();
                                                me.getCmp("id_accion_subespecifica").getStore().load();
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combobox',
                                        id: me._('id_accion_subespecifica'),
                                        name: 'id_accion_subespecifica',
                                        tpl: '<tpl for="."><div class="x-boundlist-item" title="{denominacion_subespecifica}">{codigo_subespecifica}</div></tpl>',
                                        fieldLabel: '',
                                        editable: false,
                                        width: 50,
                                        margin: "0 0 0 10",
                                        queryMode: 'local',
                                        store: {
                                            fields: ['id','codigo_subespecifica','denominacion_subespecifica'],
                                            autoLoad: false,
                                            pageSize: 100,
                                            proxy: {
                                                type:'ajax',
                                                url: 'module/estructura_presupuestaria/',
                                                actionMethods:  {read: "POST"},//actionMethods:'POST',
                                                timeout: 3600000,
                                                reader: {
                                                    type: 'json',
                                                    rootProperty: 'result',
                                                    totalProperty:'total'
                                                },
                                                extraParams: {
                                                    action: 'onList_AccionSubEspecifica_AP',
                                                    text: '',
                                                    id: '',
                                                    sort: '[{"property":"codigo_subespecifica","direction":"ASC"}]'
                                                }
                                            },
                                            listeners: {
                                                load: function(store, records, successful){
                                                    me.getCmp("id_accion_subespecifica").setValue(records[0].get("id"));
                                                },
                                                beforeload: function(store,operation,eOpts){
                                                    if(!me.getCmp("id_accion_especifica").getValue())
                                                        return false;
                                                    store.proxy.extraParams.id_accion_especifica=me.getCmp('id_accion_especifica').getValue();
                                                    return true;
                                                }
                                            }
                                        },
                                        displayField: 'codigo_subespecifica',
                                        valueField: 'id',
                                        allowBlank: false,
                                        forceSelection: true,
                                        //value: '1',
                                        listeners:{
                                            change: function(e, newValue, oldValue, eOpts){
                                                me.internal.data.id_accion_subespecifica=e.getValue();
                                                //colorcar el title del elemento seleccionado
                                                var r=e.getStore().getById(e.getValue());
                                                if(!r) return;
                                                e.getEl().set({title:r.data.denominacion_subespecifica});

                                            }
                                        }
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldcontainer',
                                fieldLabel: 'Cuenta Presupuestaria',
                                layout: 'hbox',
                                items:[
                                    {
                                        xtype: 'textfield',
                                        id: me._('cuenta_presupuestaria'),
                                        name: 'cuenta_presupuestaria',
                                        width: '18%',
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: me._('denominacion_presupuestaria'),
                                        name: 'denominacion_presupuestaria',
                                        flex: 1,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'button',
                                        tooltip: 'Cuentas en uso',
                                        iconCls: 'siga-icon-16 icon-find',
                                        listeners: {
                                            click: function(){
                                                siga.open("disponibilidad",{
                                                    id_accion_subespecifica: me.internal.data.id_accion_subespecifica,
                                                    modal: true,
                                                    onAccept: function(_me, _dataview, _record, _item, _index, _e){
                                                        me.onAceptarCuentaPresupuestaria(_record.get("id_cuenta_presupuestaria"));
                                                        return true;
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'button',
                                        tooltip: 'Todas las cuentas',
                                        iconCls: 'siga-icon-16 icon-find-all',
                                        listeners: {
                                            click: function(){
                                                var selector=Ext.create("siga.windowSelect", {
                                                    internal:{
                                                        parent: {
                                                            fieldLabel: 'Cuentas Presupuestarias',
                                                            internal:{
                                                                page:1,
                                                                limit: 100,
                                                                valueField: 'id_cuenta_presupuestaria',
                                                                columns: {field: ["cuenta_presupuestaria","denominacion","padre"], title: ["Cuenta Presupuestaria","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
                                                                url: 'module/cuenta_presupuestaria/?filtro=4%',
                                                                actionOnList: 'onList',
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
                                                                },
                                                                onAccept: function(){}

                                                            },
                                                            setValue: function(v){
                                                                me.onAceptarCuentaPresupuestaria(v);
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
                                anchor: '60%',
                                queryMode: 'local',
                                store: {
                                    fields: ['operacion','denominacion_operacion'],
                                    autoLoad: true,
                                    pageSize: 100,
                                    proxy: {
                                        type:'ajax',
                                        url: 'module/detalle_presupuestario/',
                                        actionMethods:  {read: "POST"},//actionMethods:'POST',
                                        timeout: 3600000,
                                        reader: {
                                            type: 'json',
                                            rootProperty: 'result',
                                            totalProperty:'total'
                                        },
                                        extraParams: {
                                            action: 'onList_Operacion'
                                        }
                                    },
                                    listeners: {
                                        load: function(store, records, successful){
                                            if(me.parameter.operacion)
                                                me.internal.data.operacion=me.parameter.operacion[0];
                                            else
                                                me.internal.data.operacion=records[0].get("operacion");
                                            me.getCmp("operacion").setValue(me.internal.data.operacion);
                                        },
                                        beforeload: function(store,operation,eOpts){
                                            if(me.parameter.operacion)
                                                store.proxy.extraParams.operacion=Ext.encode(me.parameter.operacion);
                                        }
                                    }
                                },
                                displayField: 'denominacion_operacion',
                                valueField: 'operacion',
                                allowBlank: false,
                                forceSelection: true,
                                listeners:{
                                    change: function(e, newValue, oldValue, eOpts){
                                        me.internal.data.operacion=e.getValue();
                                    }
                                }
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
                                                if(!me.internal.data.id_cuenta_presupuestaria){
                                                    me.setMessage("Error. Debe seleccionar la cuenta presupuestaria.","red");
                                                    return;
                                                }
                                                me.internal.data.monto=me.getCmp("monto").getValue();
                                                if(!me.internal.data.monto)
                                                    me.internal.data.monto=0;

                                                me.internal.data.operacion=me.getCmp("operacion").getValue();

                                                //buscar el formato de la estructura presupuestaria
                                                var respuesta=Ext.Ajax.request({
                                                    async: false,
                                                    url: "module/estructura_presupuestaria/",
                                                    params:{
                                                        action:'onGet_Codigo',
                                                        id_accion_subespecifica: me.internal.data.id_accion_subespecifica
                                                        }
                                                    }
                                                );
                                                me.internal.data.estructura_presupuestaria="";
                                                if(respuesta.statusText=="OK")
                                                    me.internal.data.estructura_presupuestaria=Ext.decode(respuesta.responseText)[0][0];

                                                if(!me.internal.data.estructura_presupuestaria) {
                                                    me.setMessage("Error. No pudo obtener el código de la estructura presupuestaria.","red");
                                                    return;
                                                }

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

    init: function(){
        var me=this;
        me.getCmp("id_accion_centralizada").getStore().load();
    },

    onLimpiar: function(){
        var me=this;
        me.internal.data.id_cuenta_presupuestaria="";
        me.internal.data.denominacion_presupuestaria="";
        me.internal.data.cuenta_presupuestaria="";

        me.internal.data.id_cuenta_contable="";
        me.internal.data.denominacion_contable="";
        me.internal.data.cuenta_contable="";

        me.getCmp("cuenta_presupuestaria").setValue("");
        me.getCmp("denominacion_presupuestaria").setValue("");
        me.setMessage();
    },

    onAceptarCuentaPresupuestaria: function(v){
        var me=this;
        me.onLimpiar();

        var resp=Ext.Ajax.request({
            async: false,
            url: "module/convertidor/",
            //params: Ext.JSON.decode("{action: 'onGet', id_cuenta_presupuestaria: '"+v+"'}")
            params: {action: 'onGet', id_cuenta_presupuestaria: v}
        });
        if(resp.statusText=="OK"){
            var retorno=Ext.JSON.decode(resp.responseText);
            if(!retorno || retorno.length==0) {
                me.setMessage("Error. La cuenta presupuestaria seleccionada no pudo ser encontrada.","red");
                return;
            }
            me.getCmp("cuenta_presupuestaria").setValue(retorno[0]['cuenta_presupuestaria']);
            me.getCmp("denominacion_presupuestaria").setValue(retorno[0]['denominacion_presupuestaria']);

            me.internal.data.id_cuenta_presupuestaria=retorno[0]['id_cuenta_presupuestaria'];
            me.internal.data.denominacion_presupuestaria=retorno[0]['denominacion_presupuestaria'];
            me.internal.data.cuenta_presupuestaria=retorno[0]['cuenta_presupuestaria'];


            if(!retorno[0]['id_cuenta_contable']) {
                me.setMessage("Advertencia. La cuenta presupuestaria seleccionada no se encuentra asociada a la cuenta contable.","red",30000);
            }
            else{
                me.internal.data.id_cuenta_contable=retorno[0]['id_cuenta_contable'];
                me.internal.data.denominacion_contable=retorno[0]['denominacion_contable'];
                me.internal.data.cuenta_contable=retorno[0]['cuenta_contable'];
            }
        }
        else
            me.setMessage("Error. No pudo realizar la consulta.","red",30000);
    }
});
