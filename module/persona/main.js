siga.define('persona', {
  extend: 'siga.window',
  title: 'Personas: Naturales / Jurídicas', 
  width: 750,
  height: 630,
  
  initComponent: function(){
    var me = this;
    
    _defaults=me.getInternal("field_defaults");
    me.internal._defaults=_defaults;
    
    me.itemsToolbar=[
      {
        xtype: 'buttongroup',
        title: 'Persona',
        items: [        
          {
            xtype: 'button',
            id: me._('btnNatural'),
            height: 45,
            width: 70,
            text: 'Natural',
            cls: 'siga-btn-base',
            focusCls: '',
            iconCls: 'siga-btn-base-icon icon-persona-natural',
            iconAlign: 'top',
            tooltip: 'Persona Natural',
            toggleGroup: "persona",
            listeners: {
                click: function(){
                  me.getCmp('btnNatural').toggle(true);
                  me.onNew();
                }
            }
          },
          {
            xtype: 'button',
            id: me._('btnJuridica'),
            height: 45,
            width: 70,
            text: 'Jurídica',
            cls: 'siga-btn-base',
            focusCls: '',
            iconCls: 'siga-btn-base-icon icon-persona-juridica',
            iconAlign: 'top',
            tooltip: 'Persona Jurídica',
            toggleGroup: "persona",
            listeners: {
                click: function(){
                  me.getCmp('btnJuridica').toggle(true);
                  me.onNew();
                }
            }
          }
        ]
      },
      {
        xtype: 'buttongroup',
        title: 'Acciones',
        flex: 1,
        layout: 'hbox',
        items: [        
          me.btnNew(),
          me.btnSave(),
          me.btnDelete(),
          {
            xtype:'tbspacer',
            flex:1
          },
          me.btnPrevious(),
          me.btnNext()
        ]
      }
    ];
    
    
    //fileUpload: true, formulario
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        value: ''
      },
      {
        xtype: 'container',
        layout: 'hbox',  
        items: [
          //campo cedula+buscar
          {
            xtype: 'container',
            items: [
              {
                xtype:'label',
                id: me._("label_identificacion"),
                text: ' ',            
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
          {
            xtype:'tbspacer',
            flex:1
          },
          {
            xtype: 'fieldset',
            title: '<b>Tipo de Contribuyente</b>',
            margin: '5 0 0 0',
            layout: 'hbox',
            items: [
              {
                xtype: "radiofield",
                id: me._('tipo_contribuyente1'),
                name: 'tipo_contribuyente',
                boxLabel: 'Especial',
                inputValue: 'E',                
                margin: "0 10 0 10"
              },
              {
                xtype: "radiofield",
                id: me._('tipo_contribuyente2'),
                name: 'tipo_contribuyente',
                boxLabel: 'Ordinario',
                inputValue: 'O',
                checked: true,
                margin: "0 10 0 10"
              },             
            ]
          }
        ]
      },   
      //segunda fila Nombres / Apellidos
      {
        xtype: 'container',
        id: me._("container_nombres_apellidos"),
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
                emptyText:"Primer Nombre"
              },
              {
                xtype:'textfield',
                id: me._('segundo_nombre'),
                name: 'segundo_nombre',
                flex: 1,
                fieldLabel: '',
                value: '',
                emptyText:"Segundo Nombre"
              },
              {
                xtype:'textfield',
                id: me._('primer_apellido'),
                name: 'primer_apellido',
                flex: 1,
                fieldLabel: '',
                value: '',
                emptyText:"Primer Apellido"
              },
              {
                xtype:'textfield',
                id: me._('segundo_apellido'),
                name: 'segundo_apellido',
                flex: 1,
                fieldLabel: '',
                value: '',
                emptyText:"Segundo Apellido"
              }
            ]
          }
        ]
      },
      {
        xtype:'textfield',
        id: me._('denominacion'),
        name: 'denominacion',
        flex: 1,
        fieldLabel: 'Razón Social',
        value: '',
      },
      //Domicilio
      {
        xtype:'textarea',
        id: me._('domicilio'),
        name: 'domicilio',                    
        flex: 1,
        fieldLabel: 'Domicilio',
        value: '',
        grow: true,
        growMin:2
      },
      //Telefono y Extensión
      {
        xtype: 'container',
        id: me._('container_correo_telefono'),
        defaults: _defaults,
        layout: 'hbox',
        items: [
          {
            xtype:'textfield',
            id: me._('correo'),
            name: 'correo',                    
            flex: 1,
            fieldLabel: 'Correo',
            value: '',
            margin: '5px 20px 0 0px',
          },
          {
            xtype:'textfield',
            id: me._('telefono'),
            name: 'telefono',                    
            flex: 1,
            fieldLabel: 'Teléfono',
            value: '',
            margin: '5px 0 0 20px',
            vtype: 'phone'
          }
        ]
      },
      {
        xtype: 'fieldset',
        title: '<b>Dirección de Despacho</b>',
        id: me._("container_direccion_despacho"),
        flex: 1,
        defaults: _defaults,
        layout: 'anchor',
        cls: "persona-fieldset",
        collapsible: true,
        collapsed: false,
        //checkboxToggle: true,
        items: [
          {
            xtype:'textarea',
            id: me._('despacho_direccion'),
            name: 'despacho_direccion',                    
            flex: 1,
            fieldLabel: 'Dirección',
            value: '',
            grow: true,
            growMin:2,
            margin: '5 0 0 0',
          },
          
          
          {
            xtype: 'container',
            defaults: _defaults,
            layout: 'hbox',
            margin: 0,
            items: [
              {
                xtype:'combobox',
                id: me._('despacho_estado'),
                name: 'despacho_estado',
                flex: 1,
                fieldLabel: 'Estado',
                margin: '5 0 10 0',
                store: {
                  fields: ['id', 'nombre'],
                  data : [
                  ]                  
                },
                editable: false,
                displayField: 'nombre',
                valueField: 'id',
                allowBlank: false,
                forceSelection: true,                    
                value: '',
                listeners: {
                  change: function(e, The, eOpts ){
                    
                  }
                }
              },
              {
                xtype:'combobox',
                id: me._('despacho_municipo'),
                name: 'despacho_municipio',
                flex: 1,
                fieldLabel: 'Municipio',
                margin: '5 20 10 20',
                store: {
                  fields: ['id', 'nombre'],
                  data : [
                  ]                  
                },
                editable: false,
                displayField: 'nombre',
                valueField: 'id',
                allowBlank: false,
                forceSelection: true,                    
                value: '',
                listeners: {
                  change: function(e, The, eOpts ){
                    
                  }
                }
              },
              {
                xtype:'textfield',
                id: me._('ciudad'),
                name: 'ciudad',                    
                flex: 1,
                fieldLabel: 'Ciudad',
                value: '',
                margin: '5 0 10 0',
              },
              
            ]
          },      
        ]
      },
      
      {
        xtype: 'fieldset',
        title: '<b>Contacto Administrativo</b>',
        id: me._("container_contacto1"),
        flex: 1,
        defaults: _defaults,
        layout: 'hbox',
        cls: "persona-fieldset",
        collapsible: true,
        collapsed: false,
        //checkboxToggle: true,
        items: [
          {
            xtype:'textfield',
            id: me._('contacto1_nombre'),
            name: 'contacto1_nombre',                    
            flex: 1,
            fieldLabel: 'Nombre / Apellido',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto1_cargo'),
            name: 'contacto1_cargo',                    
            flex: 1,
            fieldLabel: 'Cargo',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto1_correo'),
            name: 'contacto1_correo',                    
            flex: 1,
            fieldLabel: 'Correo',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto1_telefono'),
            name: 'contacto1_telefono',                    
            flex: 1,
            fieldLabel: 'Teléfono',
            value: '',
            margin: '5 0 10 0',
          },
        ]
      },
      {
        xtype: 'fieldset',
        title: '<b>Contacto Comercial</b>',
        id: me._("container_contacto2"),
        flex: 1,
        defaults: _defaults,
        layout: 'hbox',
        cls: "persona-fieldset",
        collapsible: true,
        collapsed: false,
        //checkboxToggle: true,
        items: [
          {
            xtype:'textfield',
            id: me._('contacto2_nombre'),
            name: 'contacto2_nombre',                    
            flex: 1,
            fieldLabel: 'Nombre / Apellido',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto2_cargo'),
            name: 'contacto2_cargo',                    
            flex: 1,
            fieldLabel: 'Cargo',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto2_correo'),
            name: 'contacto2_correo',                    
            flex: 1,
            fieldLabel: 'Correo',
            value: '',
            margin: '5 20 10 0',
          },
          {
            xtype:'textfield',
            id: me._('contacto2_telefono'),
            name: 'contacto2_telefono',                    
            flex: 1,
            fieldLabel: 'Teléfono',
            value: '',
            margin: '5 0 10 0',
          },
        ]
      }
 
    ];
    
    me.callParent(arguments);
    
    
    //me.setAccess(siga.getAccess("modulo_base/usuarios"));
    
    me.internal.sort=[{property: 'activo', direction: 'DESC'},{property: 'identificacion', direction: 'ASC'},{property: 'denominacion', direction: 'ASC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','identificacion','denominacion','activo','nacionalidad','cedula'],
      autoLoad: false,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/ficha/',
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
        dataIndex: 'identificacion',
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
        dataIndex: 'denominacion',
        text: '<b>Nombres/Apellidos</b>',
        width: '75%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[1].direction,
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
        menuDisabled: true,
        sortable: false,
        flex: 1,
        cls: "x-column-header-sort-"+me.internal.sort[2].direction,
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
    me.getCmp("btnNatural").toggle();
    me.onNew();
    
    //me.getCmp("upload").setDisabled(true);
    
  },
  
  onNew: function(){
    var me=this;
    
    me.onChangeTipo();
    
    return;
    me.setInternal({
      buscar_nacionalidad: '',
      buscar_cedula: ''
    });
    me.getCmp("container_ingreso_egreso").removeAll();
    me.onAddFechaIngresoEgreso(0);    
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    me.getCmp("foto").setSrc("image/photo-default.png");
    me.getCmp('archivos').setRootNode({expanded: true, children: []});
    me.getCmp('archivos').expandAll();
    me.onSearch();
  },
  
  onChangeTipo: function(){
    var me=this;
    
    me.getCmp('container_direccion_despacho').collapse();      
    me.getCmp('container_contacto1').collapse();
    me.getCmp('container_contacto2').collapse();
    
    if(me.getCmp('btnNatural').pressed) {
      me.getCmp('label_identificacion').setText("Cédula");
      me.getCmp('container_correo_telefono').show();
      me.getCmp('container_nombres_apellidos').show();
      
      me.getCmp('denominacion').hide();
      me.getCmp('container_direccion_despacho').hide();      
      me.getCmp('container_contacto1').hide();
      me.getCmp('container_contacto2').hide();
    }
    else if(me.getCmp('btnJuridica').pressed){
      me.getCmp('label_identificacion').setText("RIF");
      me.getCmp('container_correo_telefono').hide();
      me.getCmp('container_nombres_apellidos').hide();
      
      me.getCmp('denominacion').show();
      me.getCmp('container_direccion_despacho').show();
      me.getCmp('container_contacto1').show();
      me.getCmp('container_contacto2').show();
    }
    
    
    
    
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
            listeners: {
              blur: function(e, The, eOpts ){
                if(me.getCmp('fecha_ingreso_'+i).getValue()){
                  me.getCmp('fecha_egreso_'+i).setDisabled(false);                  
                }
                else{
                  me.getCmp('fecha_egreso_'+i).setDisabled(true);   
                }
              }
            }
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
            disabled: true,            
            listeners: {
              blur: function(e, The, eOpts ){
                if(me.getCmp('fecha_egreso_'+i).getValue()){
                  if(me.getCmp('subcontainer_ingreso_egreso_'+next)){
                    return;
                  }
                  me.onAddFechaIngresoEgreso(next);
                }
                else{
                  if(me.getCmp('subcontainer_ingreso_egreso_'+next)){                                
                    me.getCmp("container_ingreso_egreso").remove(me.getCmp('subcontainer_ingreso_egreso_'+next));
                  }
                }
              }
            }
          }
        ]
      }
    ]);
  },
  
  onSetFoto: function(foto){
    var me=this;
    if(foto)
      me.getCmp("foto").setSrc("module/ficha/?action=onGet_Archivo&archivo="+foto);
    else
      me.getCmp("foto").setSrc("image/photo-default.png");
  },
  
  onDisplayForm: function(request){
    var me=this;
    var result=Ext.JSON.decode(request.responseText);
    
    me.getCmp("foto").setSrc("image/photo-default.png");
    
    
    if(!result || result.length==0){ 
      me.getCmp('primer_nombre').setValue("");
      me.getCmp('segundo_nombre').setValue("");
      me.getCmp('primer_apellido').setValue("");
      me.getCmp('segundo_apellido').setValue("");
      me.getCmp('fecha_nacimiento').setValue("");
      me.getCmp('genero').setValue("");
      me.getCmp('telefono').setValue("");
      me.getCmp('extension').setValue("");
      me.getCmp('correo').setValue("");
      me.getCmp('codigo').setValue("");      
      me.getCmp('activo').setValue("");      
      
      me.getCmp('archivos').setRootNode({expanded: true, children: []});
      
      me.setInternal({
        buscar_nacionalidad: '',
        buscar_cedula: ''
      });      
      
      return;
    }
    
    
    me.getCmp('id').setValue(result[0]['id']);  
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
    
    var fecha_ingreso=result[0]['fecha_ingreso'];
    var fecha_egreso=result[0]['fecha_egreso'];
    
    me.getCmp("container_ingreso_egreso").removeAll();
   
    for(var i=0;i<fecha_ingreso.length;i++){
      me.onAddFechaIngresoEgreso(i);
      me.getCmp('fecha_ingreso_'+i).setValue(fecha_ingreso[i]);
      me.getCmp('fecha_ingreso_'+i).fireEvent("blur");
    }
    
    for(var i=0;i<fecha_egreso.length;i++){
      me.getCmp('fecha_egreso_'+i).setValue(fecha_egreso[i]);
      me.getCmp('fecha_egreso_'+i).fireEvent("blur");
    }
    
    if(!fecha_ingreso) {
      me.onAddFechaIngresoEgreso(0);
    }
    
    me.getCmp('antiguedad').setValue(result[0]['antiguedad']);
    
    me.getCmp('codigo').setValue(result[0]['codigo']);
    me.getCmp('activo').setValue(result[0]['activo']);
    
    me.onSetFoto(result[0]['foto']);
    
    if(!result[0]['archivos'])
      result[0]['archivos']=[];
    
    result[0]['archivos'].unshift({
      leaf: false,
      text: "Automático / Sistema",
      expanded: true,
      children: [
        {
          leaf: true,
          link: "report/ficha_constancia_trabajo.php?nacionalidad="+result[0]['nacionalidad']+"&cedula="+result[0]['cedula'],
          path: "",
          text: "Constancia de Trabajo"
        },
        {
          leaf: true,
          link: "report/ficha_arc.php?nacionalidad="+result[0]['nacionalidad']+"&cedula="+result[0]['cedula'],
          path: "",
          text: "Planilla AR-C"
        },
        {
          leaf: true,
          link: "report/ficha_carnet.php?nacionalidad="+result[0]['nacionalidad']+"&cedula="+result[0]['cedula'],
          path: "",
          text: "Carnet"
        }]
    });
      
    me.getCmp('archivos').setRootNode({expanded: true, children: result[0]['archivos']});      
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;    
    var _id=record.get("id");
    var _nacionalidad=record.get("nacionalidad");
    var _cedula=record.get("cedula");
    
    me.setInternal({
      buscar_nacionalidad: "",
      buscar_cedula: ""
    });
    
    me.getCmp('id').setValue(_id);
    me.getCmp('nacionalidad').setValue(_nacionalidad);
    me.getCmp('cedula').setValue(_cedula);

    me.onBuscarCedula();
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
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/ficha/',
      params:{
        action: 'onGet',
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
  
  onSave: function(){
    var me=this;
    
    me.getCmp('tab_data').submit({
      method: 'POST',
      url: 'module/ficha/',
      params:{
        action: 'onSave'
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
        Ext.MessageBox.hide();
        var result = Ext.decode(o.response.responseText);
        me.setMessage(result.message,"red");
      }
    });
    
  },
  
  onList_Archivo: function(){
    var me=this;
    Ext.Ajax.request({
      method: 'POST',
      url:'module/ficha/',
      params:{
        action: 'onList_Archivo',
        path: me.getCmp('nacionalidad').getValue().trim()+me.getCmp('cedula').getValue().trim()+"/"
      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);
        me.getCmp('archivos').setRootNode({expanded: true, children: result["archivos"]});
        me.onSetFoto(result["foto"]);
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);  
        me.setMessage(result.message,"red");
      }
    });
  },
  
  onDelete: function(){
    var me=this;
    me.setMessage("La acción se encuentra desactivada. Consulte al administrador del sistema.","red");
    me.getCmp('tabs').setActiveTab(0);
  }
  
});


