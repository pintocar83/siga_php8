siga.define('ficha_consulta', {
    extend: 'siga.windowBase',
    title: 'Mi Expediente',    
    width: 900,
    height: 650,

    layout: {
          type: 'hbox',
          align: 'stretch'
        },
    
    initComponent: function(){
        var me = this;
        
        _defaults=me.getInternal("field_defaults");
        me.internal._defaults=_defaults;
        me.setInternal({
            field_defaults: {
            }
        });
        
        me.items=[
            {
                xtype: "container",
                flex: 1,
                style: "background-color: #FFF; padding: 5px 40px 0px 40px;",
                items: [
                    //foto
                    {
                        xtype: "container",
                        layout: {
                            type: 'vbox',
                            align: 'center'
                        },
                        items:[
                            {
                                xtype: "image",
                                id: me._("foto"),
                                src: "",                        
                                height: 180
                            }
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
                                        xtype:'textfield',
                                        id: me._('nacionalidad'),                        
                                        width: 50,
                                        fieldLabel: '',
                                        value: '',
                                        readOnly: true,
                                    },
                                    {
                                        xtype:'textfield',
                                        id: me._('cedula'),
                                        name: 'cedula',
                                        width: 100,
                                        fieldLabel: '',
                                        value: '',
                                        readOnly: true,
                                    }
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
                                        readOnly: true,
                                    },
                                    {
                                        xtype:'textfield',
                                        id: me._('segundo_nombre'),
                                        name: 'segundo_nombre',
                                        flex: 1,
                                        fieldLabel: '',
                                        value: '',
                                        readOnly: true,
                                    },
                                    {
                                        xtype:'textfield',
                                        id: me._('primer_apellido'),
                                        name: 'primer_apellido',
                                        flex: 1,
                                        fieldLabel: '',
                                        value: '',
                                        readOnly: true,
                                    },
                                    {
                                        xtype:'textfield',
                                        id: me._('segundo_apellido'),
                                        name: 'segundo_apellido',
                                        flex: 1,
                                        fieldLabel: '',
                                        value: '',
                                        readOnly: true,
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
                                readOnly: true,
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
                                readOnly: true,
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
                    //Telefono y Extension                    
                    {
                        xtype: 'container',
                        defaults: _defaults,
                        layout: 'hbox',
                        items: [
                          {
                            xtype:'textfield',
                            id: me._('telefono'),
                            name: 'telefono',                    
                            flex: 4,
                            readOnly: true,
                            fieldLabel: 'Teléfono',
                            value: '',
                            margin: '5px 0 0 0',
                            vtype: 'phone'
                          },
                          {
                            xtype:'textfield',
                            id: me._('extension'),
                            name: 'extension',                    
                            flex: 1,
                            readOnly: true,
                            fieldLabel: 'Extensión',
                            value: '',
                            margin: '5px 0 0 40px',
                          },
                        ]
                      },
                      //Correo
                      {
                        xtype: 'container',
                        defaults: _defaults,
                        layout: 'anchor',
                        items: [
                          {
                            xtype:'textfield',
                            id: me._('correo'),
                            name: 'correo',                    
                            flex: 1,
                            fieldLabel: 'Correo',
                            readOnly: true,
                            value: '',
                            margin: '5px 0 0 0px',
                          }
                        ]
                    },
                    //Fecha de Ingreso / Egreso
                    {
                        xtype: 'container',                
                        layout: 'fit',
                        id: me._('container_ingreso_egreso'), 
                        items: []
                    },
                    //Antiguedad
                    {
                        xtype: 'container',
                        defaults: _defaults,
                        layout: 'anchor',
                        items: [
                            {
                                xtype:'textfield',
                                id: me._('antiguedad'),
                                name: 'antiguedad',                    
                                flex: 1,
                                readOnly: true,
                                //disabled: true,
                                fieldLabel: 'Tiempo de Servicio / Antiguedad',
                                value: '',
                                margin: '5px 0 0 0px',
                            }                 
                        ]
                    }
                ]
            },
            {
                xtype: "container",
                flex: 1,
                style: "padding: 10px 20px 20px 0px; background-color: #FFF;",
                layout: "fit",
                items:[                    
                    {
                        xtype: 'treepanel',
                        id: me._('archivos'),
                        title: '<b>Archivos</b>',
                        isFormField:false,
                        autoScroll: true,
                        rootVisible: false,
                        useArrows: true,
                        flex: 1,
                        listeners: {
                            itemdblclick: function( el, record, item, index, e, eOpts ){
                                if(record.get("leaf") && record.get("link"))                          
                                    window.open(record.get("link"));
                            },
                        }
                    }
                ]
            }
        ];
        me.callParent(arguments);      
    },
    
    init: function(){
        var me=this;   
        me.onLoad();
    },
    
    onAddFechaIngresoEgreso: function(i){
        var me=this;        
        var next=i+1;
        
        var fieldLabel_ingreso="";
        var fieldLabel_egreso="";
        var margin_ingreso='0 0 0 0';
        var margin_egreso='0 0 0 40px';
        
        if(i==0){
            fieldLabel_ingreso="Fecha de Ingreso";
            fieldLabel_egreso="Fecha de Egreso";
            margin_ingreso='5px 0 0 0';
            margin_egreso='5px 0 0 40px';
        }
        
        me.getCmp("container_ingreso_egreso").add([
            {
                xtype: 'container',
                layout: 'hbox',
                id: me._('subcontainer_ingreso_egreso_'+i),
                defaults: me.internal._defaults,
                items:[
                    {
                        xtype:'datefield',
                        id: me._('fecha_ingreso_'+i),
                        name: 'fecha_ingreso[]',                    
                        flex: 1,
                        fieldLabel: fieldLabel_ingreso,
                        value: '',
                        margin: margin_ingreso,
                        submitFormat: 'Y-m-d',
                        readOnly: true
                    },
                    {
                        xtype:'datefield',
                        id: me._('fecha_egreso_'+i),
                        name: 'fecha_egreso[]',                    
                        flex: 1,
                        fieldLabel: fieldLabel_egreso,
                        value: '',
                        margin: margin_egreso,
                        submitFormat: 'Y-m-d',
                        readOnly: true
                    }
                ]
            }
        ]);
    },
    
    onLoad: function(){
        var me=this;
        
        me.getCmp("foto").setSrc("image/photo-default.png");
        
        Ext.Ajax.request({
            method: 'POST',
            url:'module/ficha_consulta/',
            params:{
                action: 'onGet'
            },
            success: function(request){
                var result=Ext.JSON.decode(request.responseText);
                
                me.getCmp("foto").setSrc("module/ficha/?action=onGet_Archivo&archivo="+result[0]["foto"]);                
                me.getCmp('nacionalidad').setValue(result[0]['nacionalidad']);
                me.getCmp('cedula').setValue(result[0]['cedula']);
                me.getCmp('primer_nombre').setValue(result[0]['primer_nombre']);
                me.getCmp('segundo_nombre').setValue(result[0]['segundo_nombre']);
                me.getCmp('primer_apellido').setValue(result[0]['primer_apellido']);
                me.getCmp('segundo_apellido').setValue(result[0]['segundo_apellido']);
                me.getCmp('fecha_nacimiento').setValue(result[0]['fecha_nacimiento']);
                me.getCmp('genero').setValue(result[0]['genero']);
                me.getCmp('telefono').setValue(result[0]['telefono']);
                me.getCmp('extension').setValue(result[0]['extension']);
                me.getCmp('correo').setValue(result[0]['correo']);
                
                me.getCmp("container_ingreso_egreso").removeAll();
                var fecha_ingreso=result[0]['fecha_ingreso'];
                var fecha_egreso=result[0]['fecha_egreso'];
   
                for(var i=0;i<fecha_ingreso.length;i++){
                    me.onAddFechaIngresoEgreso(i);
                    me.getCmp('fecha_ingreso_'+i).setValue(fecha_ingreso[i]);
                }
                
                for(var i=0;i<fecha_egreso.length;i++){
                    me.getCmp('fecha_egreso_'+i).setValue(fecha_egreso[i]);
                }
                
                if(!fecha_ingreso) {
                    me.onAddFechaIngresoEgreso(0);
                }
                
                me.getCmp('antiguedad').setValue(result[0]['antiguedad']);
                
                if(!result[0]['archivos'])
                    result[0]['archivos']=[];
                
                result[0]['archivos'].unshift({
                    leaf: false,
                    text: "Automático / Sistema",
                    expanded: true,
                    children: [
                        {
                            leaf: true,
                            link: "report/ficha_constancia_trabajo.php",
                            path: "",
                            text: "Constancia de Trabajo"
                        },
                        {
                            leaf: true,
                            link: "report/ficha_arc.php",
                            path: "",
                            text: "Planilla AR-C"
                        },
                        {
                            leaf: true,
                            link: "report/ficha_arc.php?excluir=BONO_VACACIONAL,ODI,AGUINALDO",
                            path: "",
                            text: "Planilla AR-C / Excluir: Vacaciones, Aguinaldos y ODI"
                        },
                        {
                          leaf: true,
                          link: "report/ficha_carnet.php?nacionalidad="+result[0]['nacionalidad']+"&cedula="+result[0]['cedula'],
                          path: "",
                          text: "Carnet"
                        }]
                });
                
                me.getCmp('archivos').setRootNode({expanded: true, children: []});
                me.getCmp('archivos').setRootNode({expanded: true, children: result[0]['archivos']});
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);
                //me.setMessage(result.message,"red");
            }
        });
        
        
    }
    
    

    
  });