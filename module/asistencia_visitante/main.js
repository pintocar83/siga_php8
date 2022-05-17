siga.define('asistencia_visitante', {
    extend: 'siga.windowForm',
    title: 'Sistema de Asistencia - Registros de Visitantes',    
    width: 900,
    height: 650,
    
    listeners:{
        beforedestroy: function(){
            var me=this;
            if(me.internal.id_interval) 
                clearInterval(me.internal.id_interval);
        }
    },
    
    initComponent: function(){
        var me = this;
        
        _defaults=me.getInternal("field_defaults");
        me.internal._defaults=_defaults;
        me.setInternal({
            field_defaults: {
            }
        });
        
        me.itemsToolbar=[
            me.btnNew(),
            me.btnSave(),
            me.btnDelete(),
            {
                xtype: 'button',
                id: me._("btnSalida"),
                height: 45,
                width: 55,
                text: 'Salida',
                tooltip: "Registrar Salida",
                cls: 'siga-btn-base',
                disabledCls: 'siga-btn-disabled',
                iconCls: 'siga-btn-base-icon icon-persona_salida',
                iconAlign: 'top',
                listeners: {
                    click: function(){
                        me.setMessage();
                        me.onSalidaPersona();
                    }
                }
            },
        ];
        
        var fecha=siga.timer.result.fecha;
        
        me.items=[
            {
                xtype: "container",
                flex: 1,
                layout: {
                    type: 'hbox',
                    align: 'stretch'
                },
                items:[
                    {
                        xtype: "container",
                        flex: 1,
                        style: "background-color: #FFF; padding: 5px 40px 0px 40px;",
                        items: [
                            //webcam
                            {
                                xtype: "container",
                                layout: {
                                    type: 'vbox',
                                    align: 'center'
                                },
                                items:[
                                    {
                                        xtype : "component",
                                        id: me._("canvas_stream"),
                                        autoEl : {
                                            tag : "canvas",                
                                            width: 240,
                                            height: 180,
                                            //onclick: "siga.getCmp('modulo_asistencia/asistencia_visitante').capture();",
                                            style: "cursor: pointer; border: 1px solid #C9C9C9; border-radius: 5px; margin-top: 5px;",
                                            title: "Pulsa para detener o iniciar captura"
                                        }
                                    },
                                    {
                                        xtype: 'label',
                                        id: me._('camara_estatus'),
                                        style:'text-align: center; font-size: 11px; color: white; font-weight: bold; text-shadow: 0px 0px 8px rgba(0, 0, 0, 1);',
                                        margin: '-20px 0px 10px 0px;',
                                        padding: 0,
                                        html: "&nbsp;",
                                        width: '100%',
                                        flex: 1,
                                    },
                                ]   
                            },
                            //Cedula
                            {
                                xtype: 'container',                        
                                items: [
                                    {
                                        xtype:'label',
                                        text: 'Cédula',
                                        cls: _defaults.labelCls,
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',                    
                                        items:[
                                            {
                                                xtype:'combobox',
                                                id: me._('nacionalidad'),
                                                name: 'nacionalidad',
                                                width: 50,
                                                fieldLabel: '',
                                                store: {
                                                    fields: ['id', 'nombre'],
                                                    data : [
                                                        {"id":"V", "nombre":"V"},
                                                        {"id":"E", "nombre":"E"},
                                                        {"id":"P", "nombre":"P"}
                                                    ]                      
                                                },
                                                displayField: 'nombre',
                                                valueField: 'id',
                                                allowBlank: false,
                                                forceSelection: true,
                                                editable: false,
                                                value: 'V',
                                                listeners: {
                                                    change: function(e, The, eOpts ){
                                                      me.onBuscarCedula();
                                                    }
                                                }
                                            },
                                            {
                                                xtype:'textfield',
                                                id: me._('cedula'),
                                                name: 'cedula',
                                                width: 100,
                                                fieldLabel: '',
                                                value: '',
                                                listeners: {
                                                    specialkey: function(field, e){
                                                        if (e.getKey() == e.ENTER) 
                                                        me.onBuscarCedula();
                                                    },
                                                    blur: function(e, The, eOpts ){
                                                        me.onBuscarCedula();
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'button',
                                                id: me._('btnFind'),
                                                text: 'Buscar',
                                                tooltip: 'Buscar',
                                                iconCls: 'siga-icon-16 icon-find',
                                                width: 80,
                                                listeners: {
                                                    click: function(){
                                                        me.onBuscarCedula();
                                                    }
                                                }
                                            },
                                        ]
                                    }
                                ]
                            },
                            //Nombres / Apellidos
                            {
                                xtype: 'container',
                                margin: '5px 0 0 0',
                                items:[
                                    {
                                        xtype:'label',
                                        text: 'Nombres / Apellidos',
                                        cls: _defaults.labelCls,
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        items:[
                                            {
                                                xtype:'textfield',
                                                id: me._('primer_nombre'),
                                                name: 'primer_nombre',
                                                flex: 1,
                                                fieldLabel: '',
                                                value: '',
                                                readOnly: false,
                                            },
                                            {
                                                xtype:'textfield',
                                                id: me._('segundo_nombre'),
                                                name: 'segundo_nombre',
                                                flex: 1,
                                                fieldLabel: '',
                                                value: '',
                                                readOnly: false,
                                            },
                                            {
                                                xtype:'textfield',
                                                id: me._('primer_apellido'),
                                                name: 'primer_apellido',
                                                flex: 1,
                                                fieldLabel: '',
                                                value: '',
                                                readOnly: false,
                                            },
                                            {
                                                xtype:'textfield',
                                                id: me._('segundo_apellido'),
                                                name: 'segundo_apellido',
                                                flex: 1,
                                                fieldLabel: '',
                                                value: '',
                                                readOnly: false,
                                            }
                                        ]
                                    }
                                ]
                            },
                            //Fecha de nacimiento y Genero
                            {
                                xtype: 'container',
                                defaults: _defaults,
                                layout: 'hbox',
                                items: [
                                    {
                                        xtype:'datefield',
                                        id: me._('fecha_nacimiento'),
                                        name: 'fecha_nacimiento',                    
                                        flex: 1,
                                        fieldLabel: 'Fecha de Nacimiento',
                                        readOnly: false,
                                        value: '',
                                        margin: '5px 0 0 0',
                                        submitFormat: 'Y-m-d',
                                    },
                                    {
                                        xtype:'combobox',
                                        id: me._('genero'),
                                        name: 'genero',
                                        style: '',
                                        margin: '5px 0 0 40px',
                                        flex: 1,
                                        fieldLabel: 'Genero',
                                        readOnly: false,
                                        store: {
                                            fields: ['id', 'nombre'],
                                            data : [
                                                {"id":" ", "nombre":""},
                                                {"id":"M", "nombre":"MASCULINO"},
                                                {"id":"F", "nombre":"FEMENINO"}
                                            ]                      
                                        },
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        allowBlank: false,
                                        forceSelection: true,
                                        editable: false,
                                        value: 'M'
                                    },
                                ]
                            },
                            //Telefono y Correo
                            {
                                xtype: 'container',
                                defaults: _defaults,
                                layout: 'anchor',
                                items: [
                                    {
                                        xtype:'textfield',
                                        id: me._('telefono'),
                                        name: 'telefono',                    
                                        flex: 1,
                                        fieldLabel: 'Teléfono',
                                        readOnly: false,
                                        value: '',
                                        margin: '5px 0 0 0',
                                        vtype: 'phone'
                                    },
                                    {
                                        xtype:'textfield',
                                        id: me._('correo'),
                                        name: 'correo',                    
                                        flex: 1,
                                        fieldLabel: 'Correo',
                                        readOnly: false,
                                        value: '',
                                        margin: '5px 0 0 0px',
                                    },
                                    //Unidad / Coordinación
                                    {
                                        xtype: 'combobox',
                                        id: me._('id_unidad_coordinacion'),
                                        name: 'id_unidad_coordinacion',
                                        flex: 1,
                                        margin: '5px 0 0 0',
                                        fieldLabel: 'Unidad/Coordinación',
                                        editable: false,
                                        queryMode: "local",
                                        store: {
                                            fields: ['id','coordinacion'],
                                            autoLoad: true,
                                            pageSize: 100,
                                            proxy: {
                                                type:'ajax',
                                                url: 'module/unidad_coordinacion/',
                                                actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
                                                timeout: 3600000,
                                                reader: {
                                                    type: 'json',
                                                    rootProperty: 'result',
                                                    totalProperty:'total'
                                                },
                                                extraParams: {
                                                    action: 'onListSelect'
                                                }
                                            },
                                            listeners: {
                                                load: function(store, records, successful){
                                                    me.internal.id_unidad_coordinacion=records[0].get("id");
                                                    me.getCmp("id_unidad_coordinacion").setValue(records[0].get("id"));
                                                }
                                            }
                                        },
                                        displayField: 'coordinacion',
                                        valueField: 'id',
                                        allowBlank: false,
                                        forceSelection: true
                                    },
                                    {
                                        xtype:'textarea',
                                        id: me._('motivo'),
                                        name: 'motivo',
                                        margin: '5px 0 0 0',
                                        fieldLabel: 'Motivo',
                                        fieldStyle: 'text-transform: uppercase;',
                                        value: '',
                                        flex: 1,
                                    },
                                ]
                            },
        
                        ]
                    },
                    {
                        xtype: "container",
                        flex: 1,
                        style: "padding: 10px 40px 0px 0px; background-color: #FFF;",
                        layout: "fit",
                        items:[
                            {
                                xtype: 'fieldset',
                                title: '<b>Registro Histórico</b>',
                                margin: 0,
                                padding: 5,
                                flex: 1,
                                layout: 'fit',
                                items: [
                                    
                                    {
                                        xtype: 'gridpanel',
                                        id: me._('gridList'),
                                        border: 1,
                                        viewConfig:{
                                            getRowClass: function(rec, rowIdx, params, store) {                    
                                                if(!rec.get('hora_salida'))
                                                    return 'asistencia_visitante-fila-resaltada';
                                                return '';
                                            }
                                        },
                                        columns: [
                                            {
                                                xtype: 'templatecolumn',
                                                //xtype: 'gridcolumn',
                                                //dataIndex: 'fecha',
                                                text: '<b>Entrada</b>',
                                                tpl: "<center>{fecha}<br><small>{hora_entrada}</small></center>",
                                                //tpl: "<table cellspacing='0' cellpadding='0' border='0'><tr><td rowspan='2'>{fecha}</td><td>{hora_entrada}</td></tr><tr><td>{hora_salida}</td></tr></table>",
                                                width: '19%',
                                                menuDisabled: true,
                                                sortable: false,
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                dataIndex: 'nacionalidad_cedula',
                                                text: '<b>Cédula</b>',
                                                width: '18%',
                                                menuDisabled: true,
                                                sortable: false,
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                dataIndex: 'nombres_apellidos',
                                                text: '<b>Nombres/Apellidos</b>',
                                                flex: 1,
                                                menuDisabled: true,
                                                sortable: false,
                                            },
                                            {
                                                xtype: 'templatecolumn',
                                                text: '<b>Salida</b>',
                                                tpl: "{hora_salida}",
                                                width: '20%',
                                                menuDisabled: true,
                                                sortable: false,
                                            }

                                        ],
                                        scroll: 'vertical',
                                        listeners: {
                                            select: function(dataview, record, item, index, e){
                                                me.onGet(dataview, record, item, index, e);
                                            }
                                        },
                                        dockedItems: [
                                            {
                                                xtype:'container',
                                                layout: "hbox",
                                                margin: "0 0 5 0",
                                                dock: 'top',
                                                items: [
                                                    {
                                                        xtype:'datefield',
                                                        id: me._('fecha_inicio'),
                                                        fieldLabel: '<b>Fecha Inicio</b>',
                                                        separador: '',
                                                        submitFormat: 'Y-m-d',
                                                        dock: 'top',
                                                        labelAlign: 'top',
                                                        labelSeparator: '',
                                                        flex: 1,
                                                        labelCls: 'siga-field-label',
                                                        hideLabel: false,
                                                        submitValue: false,
                                                        value: fecha,
                                                        isFormField: false
                                                        
                                                    },
                                                    {
                                                        xtype:'datefield',
                                                        id: me._('fecha_culminacion'),
                                                        fieldLabel: '<b>Fecha Culminación</b>',
                                                        separador: '',
                                                        submitFormat: 'Y-m-d',
                                                        dock: 'top',
                                                        labelAlign: 'top',
                                                        labelSeparator: '',
                                                        margin: "0 0 0 10",
                                                        flex: 1,
                                                        labelCls: 'siga-field-label',
                                                        hideLabel: false,
                                                        submitValue: false,
                                                        value: fecha,
                                                        isFormField: false
                                                    },
                                                ]
                                            },                                            
                                            {
                                                xtype: 'container',
                                                layout: 'hbox',
                                                dock: 'bottom',
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        id: me._('txtSearch'),
                                                        hideLabel: false,
                                                        flex: 1,
                                                        submitValue: false,
                                                        isFormField: false,
                                                        emptyText: 'Escriba el texto a buscar',
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
                                            },
                                        ],
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ];
        me.callParent(arguments);
        
        var store= new Ext.data.Store({
            pageSize: 100,
            fields: ['id','fecha','nacionalidad_cedula','nombres_apellidos','hora_salida'],
            autoLoad: false,
            remoteSort: true,
            sorters: [{property: 'fecha_hora', direction: 'DESC'}],
            proxy: {
                type:'ajax',
                url: 'module/asistencia_visitante/',
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
                    //me.getCmp('gridList').getSelectionModel().deselectAll();
                },
                beforeload: function(store,operation,eOpts){
                    store.proxy.extraParams.text=me.getCmp('txtSearch').getValue();
                    if(!me.getCmp('fecha_inicio').getValue())
                        me.getCmp('fecha_inicio').setValue(fecha);
                    if(!me.getCmp('fecha_culminacion').getValue())
                        me.getCmp('fecha_culminacion').setValue(fecha);
                        
                    //console.log(Ext.Date.format(me.getCmp('fecha_inicio').getValue(), 'Y-m-d'));  
                    //console.log(me.getCmp('fecha_inicio').getValue());
                    store.proxy.extraParams.fecha_inicio=Ext.Date.format(me.getCmp('fecha_inicio').getValue(), 'Y-m-d');
                    store.proxy.extraParams.fecha_culminacion=Ext.Date.format(me.getCmp('fecha_culminacion').getValue(), 'Y-m-d');
                    
                    me.clearForm();
                }
            }
        });
        
        me.getCmp('gridList').reconfigure(store);
        me.getCmp('pagingList').bindStore(store);
        
    },
    
    init: function(){
        var me=this;
        me.onSearch();
        me.initWebCam();
        me.setMessage("");
    },
    
    onSearch: function(){
        var me=this;
        me.internal.id=null;
        me.getCmp('gridList').getSelectionModel().deselectAll();
        me.getCmp('gridList').getStore().load();
    },

    
    initWebCam: function(){
        var me=this;
        me.internal.video=document.createElement("video");
        me.internal.video.setAttribute('width', 320);//resolucion para la camara 
        me.internal.video.setAttribute('height', 240);
        
        Ext.get(me._("canvas_stream")).on("click", function(){me.capture();}, me);
        

        navigator.getMedia = ( navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
        if(navigator.getMedia){
            me.context="getusermedia";
            navigator.getMedia(
                {video:true, audio:false},
                function(stream){
                    if(navigator.mozGetUserMedia){
                        me.internal.video.mozSrcObject = stream;
                    }
                    else{
                        var vendorURL = window.URL || window.webkitURL;
                        me.internal.video.src = vendorURL.createObjectURL(stream);
                    }
                    me.internal.video.play();
                },
                function(err) {
                    console.log("An error occured! " + err);
                }
            );
            
            me.internal.video.addEventListener('play',function(){me.captureStart();},false);
            return;
        }
        
        alert("El navegador no soporta la opción 'navigator.getUserMedia'");
    },
    
    captureStart: function(){
        var me=this;
        if(me.internal.id_interval) 
            clearInterval(me.internal.id_interval);
        me.getCmp('camara_estatus').setText("&nbsp;",false);
        me.internal.id_interval=setInterval(function(){
            var canvas=document.getElementById(me._("canvas_stream"));
            var context=canvas.getContext('2d');
            context.drawImage(me.internal.video, 0, 0, 240, 180);            
            return;            
        },200); 
    },
    
    
    capture: function(){
        var me=this;
        if(me.internal.id) {
            return;
        }
        
        if(me.internal.id_interval==null){
            me.captureStart();
        }
        else{
            clearInterval(me.internal.id_interval);
            me.getCmp('camara_estatus').setText("captura realizada",false);
            me.internal.id_interval=null;
        }
    },
    

    onBuscarCedula: function(){
        var me=this;
        
        //el siguiente codigo es para evitar la doble busqueda al perder foco en los campos
        if(me.getInternal().buscar_nacionalidad==me.getCmp('nacionalidad').getValue() &&
           me.getInternal().buscar_cedula==me.getCmp('cedula').getValue())
            return;    
    
        me.setInternal({
            buscar_nacionalidad: me.getCmp('nacionalidad').getValue(),
            buscar_cedula: me.getCmp('cedula').getValue()
        });
        //fin evitar doble busqueda
        //me.capture();
        
        Ext.Ajax.request({
            method: 'POST',
            url:'module/persona/',
            params:{
                action: 'onGet_PersonaCNE',
                nacionalidad: me.getCmp('nacionalidad').getValue(),
                cedula: me.getCmp('cedula').getValue()
            },
            success: function(request){
                me.onDisplayForm(request);
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);  
                me.setMessage(result.message,"red");
            }
        });
    },
    
    onDisplayForm: function(request){
        var me=this;
        var result=Ext.JSON.decode(request.responseText);
        
        
        
        //me.getCmp('nacionalidad').enable();
        //me.getCmp('cedula').enable();
        //me.getCmp('btnFind').enable();
        
        me.getCmp('btnSalida').disable();
        me.getCmp('motivo').setValue("");
        
        if(!result || result.length==0){
            var canvas=document.getElementById(me._("canvas_stream"));
            var context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            
            me.getCmp('primer_nombre').setValue("");
            me.getCmp('segundo_nombre').setValue("");
            me.getCmp('primer_apellido').setValue("");
            me.getCmp('segundo_apellido').setValue("");
            me.getCmp('fecha_nacimiento').setValue("");
            me.getCmp('genero').setValue("");
            me.getCmp('telefono').setValue("");
            me.getCmp('correo').setValue("");
            
            me.setInternal({
              buscar_nacionalidad: '',
              buscar_cedula: ''
            });
            return;
        }
        
        me.getCmp('nacionalidad').setValue(result[0]['nacionalidad']);
        me.getCmp('cedula').setValue(result[0]['cedula']);
        me.getCmp('primer_nombre').setValue(result[0]['primer_nombre']);
        me.getCmp('segundo_nombre').setValue(result[0]['segundo_nombre']);
        me.getCmp('primer_apellido').setValue(result[0]['primer_apellido']);
        me.getCmp('segundo_apellido').setValue(result[0]['segundo_apellido']);
        me.getCmp('fecha_nacimiento').setValue(result[0]['fecha_nacimiento']);
        me.getCmp('genero').setValue(result[0]['genero']);
        me.getCmp('telefono').setValue(result[0]['telefono']);
        me.getCmp('correo').setValue(result[0]['correo']);
        
        
        if(result[0]['id']) {
            var canvas=document.getElementById(me._("canvas_stream"));
            var context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
        
            me.internal.id=result[0]['id'];
            me.getCmp('id_unidad_coordinacion').setValue(result[0]['id_unidad_coordinacion']);
            
            me.getCmp('nacionalidad').disable();
            me.getCmp('cedula').disable();
            me.getCmp('btnFind').disable();
            if(!result[0]['hora_salida'])
                me.getCmp('btnSalida').enable();
            
            me.getCmp('motivo').setValue(result[0]['motivo']);
            me.getCmp('camara_estatus').setText("&nbsp;",false);
            
            clearInterval(me.internal.id_interval);
            me.internal.id_interval=null;
            if(result[0]['imagen']){
                var img = new Image();
                img.src=result[0]['imagen'];
                context.drawImage(img,0,0);
            }
            
        }
        else{
            me.getCmp('motivo').focus();
        }
    },
    
    onNew: function(){
        var me=this;
        me.clearForm();        
        me.onSearch();
    },
    
    clearForm: function(){
        var me=this;
        me.internal.id=null;
        me.setInternal({
            buscar_nacionalidad: "",
            buscar_cedula: ""
        });
        //limpiar la imagen anterior
        var canvas=document.getElementById(me._("canvas_stream"));
        var context = canvas.getContext('2d');
        //fin limpiar la imagen anterior
        context.clearRect(0, 0, canvas.width, canvas.height);
        
        
        me.getCmp('nacionalidad').enable();
        me.getCmp('cedula').enable();
        me.getCmp('btnFind').enable();
        me.getCmp('btnSalida').disable();
        
        me.captureStart();
        me.getCmp('tab_data').getForm().reset();
        if (me.internal.id_unidad_coordinacion)
            me.getCmp("id_unidad_coordinacion").setValue(me.internal.id_unidad_coordinacion);
    },
    
    onGet: function(dataview, record, item, index, e){
        var me=this;
        
        me.internal.id=record.get("id");
        
        Ext.Ajax.request({
            method: 'POST',
            url:'module/asistencia_visitante/',
            params:{
                action: 'onGet',
                id: me.internal.id
            },
            success: function(request){
                me.onDisplayForm(request);
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);  
                me.setMessage(result.message,"red");
            }
        });
        
        
        
    },
    
    save: function(){
        var me=this;
        
        if(me.internal.id_interval!=null){
            me.setMessage("Debe realizar la captura de la camara para guardar el registro.","red");
            return;
        }
        
        if(!me.getCmp('nacionalidad').getValue().trim()){
            me.setMessage("Debe seleccionar la nacionalidad.","red");
            return;
        }
        
        if(!me.getCmp('cedula').getValue().trim()){
            me.setMessage("Debe ingresar el número de cédula.","red");
            return;
        }
        
        if(!Ext.isNumeric(me.getCmp('cedula').getValue().trim())){
            me.setMessage("El valor del campo cédula debe ser númerico.","red");
            return;
        }
        
        if(!me.getCmp('id_unidad_coordinacion').getValue().trim()){
            me.setMessage("Debe seleccionar la unidad o coordinación.","red");
            return;
        }
        
        if(!me.getCmp('motivo').getValue().trim()){
            me.setMessage("Debe ingresar el motivo de la visita.","red");
            return;
        }

        if(!me.getCmp("tab_data").getForm().isValid()){
            me.setMessage("Existen datos inválidos en el formulario.","red");
            return;
        }
        
        var canvas=document.getElementById(me._("canvas_stream"));
        //var data = canvas.toDataURL('image/png');
        var data = canvas.toDataURL("image/jpeg", 0.70);
        
        
        me.getCmp('tab_data').submit({
            method: 'POST',
            url: 'module/asistencia_visitante/',
            params:{
                action: 'onSave',
                id: !me.internal.id?"":me.internal.id,
                nacionalidad: me.getCmp('nacionalidad').getValue(),
                cedula: me.getCmp('cedula').getValue(),
                imagen: data
            },
            waitMsg: 'Guardando... por favor espere!',
            success: function(form,o){
                Ext.MessageBox.hide();
                var result=o.result;        
                if(result.success) {
                    me.onNew();
                    me.setMessage(result.message,"green");
                }
                else
                    me.setMessage(result.message,"red");
            },
            failure:function(form,o){
                //me.getCmp('gridList').getStore().load();
                Ext.MessageBox.hide();
                var result = Ext.decode(o.response.responseText);
                me.setMessage(result.message,"red");
            }
        });
        
    },
    
    
    onSave: function(){
        var me=this;   
        
        if(me.internal.id){
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
    
    onDelete: function(){
        var me=this;
        
        if(!me.internal.id) return;
        
        Ext.Ajax.request({
            method: 'POST',
            url:'module/asistencia_visitante/',
            params:{
                action: 'onDelete',
                id: me.internal.id
            },
            success: function(request){
                me.onNew();
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);  
                me.setMessage(result.message,"red");
            }
        });
    },
    
    onSalidaPersona: function(){
        var me=this;
        
        if(!me.internal.id)
            return;
        
        Ext.Ajax.request({
            method: 'POST',
            url:'module/asistencia_visitante/',
            params:{
                action: 'onSalidaPersona',
                id: me.internal.id
            },
            success: function(request){
                me.onSearch();
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);  
                me.setMessage(result.message,"red");
            }
        });
    },
    
    
  });