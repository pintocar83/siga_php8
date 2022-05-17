siga.define('academia_inscribir/agregar_persona', {
  extend: 'siga.windowForm',
  title: 'Academia de Software Libre - Inscribir Persona',      
  width: 730,
  height: 600,
  modal: true,
  
  initComponent: function(){
    var me = this;
    
    me.itemsToolbar=[
      me.btnSave()
    ];
    
    me.items=[
      {
        xtype: 'container',
        layout: 'column',
        defaults: {
          layout: '100%',          
        },        
        width: (50+100+80+80),
        items: [
            {
              xtype:'label',
              text: 'Cédula',
              style: "font-weight: bold; margin-top: 5px;",
              width: '100%'
            },
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
                  {"id":"P", "nombre":"P"},
                  {"id":" ", "nombre":"S/N"}
                ]                      
              },
              displayField: 'nombre',
              valueField: 'id',
              allowBlank: false,
              forceSelection: true,                    
              value: 'V',
              listeners: {
                blur: function(e, The, eOpts ){
                  me.onBuscar();
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
                    me.onBuscar();
                },
                blur: function(e, The, eOpts ){
                  me.onBuscar();
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
                  me.onBuscar();
                }
              }
            }            
          ]
      },
      {
        xtype: 'container',
        layout: 'column',
        //style: 'border: 1px solid black;',
        defaults: {
          layout: '100%'
        },        
        width: (50+100+80+80)*2,
        items: [
          {
            xtype:'label',
            text: 'Nombres / Apellidos',
            style: "font-weight: bold; margin-top: 5px;",
            width: 310
          },
          {
            xtype:'label',
            html: '<b>Nombres / Apellidos</b> <small>(Sistema de preinscripción)</small>',
            style: "margin-top: 5px; margin-left: 20px;",
            width: 290
          },
          {
            xtype: 'container',
            layout: 'hbox',
            width: 310,
            items:[
              {
                xtype:'textfield',
                id: me._('primer_nombre'),
                name: 'primer_nombre',
                width: '25%',
                //readOnly: true,
                //width: 77,
                fieldLabel: '',
                value: ''
              },
              {
                xtype:'textfield',
                id: me._('segundo_nombre'),
                name: 'segundo_nombre',
                //readOnly: true,
                //width: 77,
                width: '25%',
                fieldLabel: '',
                value: ''
              },
              {
                xtype:'textfield',
                id: me._('primer_apellido'),
                name: 'primer_apellido',
                //readOnly: true,
                //width: 77,
                width: '25%',
                fieldLabel: '',
                value: ''
              },
              {
                xtype:'textfield',
                id: me._('segundo_apellido'),
                name: 'segundo_apellido',
                //readOnly: true,
                //width: 79,
                //width: '25%',
                flex: 1,
                fieldLabel: '',
                value: ''
              }
              
            ]   
          },
          {
            xtype:'textfield',
            id: me._('preinscrito_nombres_apellidos'),
            name: 'preinscrito_nombres_apellidos',
            readOnly: true,
            width: 290,
            fieldLabel: '',
            value: '',
            style: "margin-left: 20px;"
          }
        ]
      },
      {
        xtype: 'container',
        layout: 'column',
        defaults: {
          layout: '100%'
        },        
        width: (50+100+80+80)*2,
        items: [
          {
            xtype:'label',
            text: 'Teléfono',            
            style: "font-weight: bold; margin-top: 5px;",
            width: 310
          },
          {
            xtype:'label',
            html: '<b>Teléfono</b> <small>(Sistema de preinscripción)</small>',
            style: "margin-top: 5px; margin-left: 20px;",
            width: 290
          },
          {
            xtype:'textfield',
            id: me._('telefono'),
            name: 'telefono',
            //readOnly: true,
            width: 310,
            fieldLabel: '',
            value: '',
            vtype: 'phone'
          },
          {
            xtype:'textfield',
            id: me._('preinscrito_telefono'),
            name: 'preinscrito_telefono',
            readOnly: true,
            width: 290,
            fieldLabel: '',
            value: '',
            style: "margin-left: 20px;"
          }
        ]
      },
      {
        xtype: 'container',
        layout: 'column',
        defaults: {
          layout: '100%'
        },        
        width: (50+100+80+80)*2,
        items: [
          {
            xtype:'label',
            text: 'Correo',
            style: "font-weight: bold; margin-top: 5px;",
            width: 310
          },
          {
            xtype:'label',
            html: '<b>Correo</b> <small>(Sistema de preinscripción)</small>',
            style: "margin-top: 5px; margin-left: 20px;",
            width: 290
          },
          {
            xtype:'textfield',
            id: me._('correo'),
            name: 'correo',
            //readOnly: true,
            width: 310,
            fieldLabel: '',
            value: '',
            //vtype: 'email'
          },
          {
            xtype:'textfield',
            id: me._('preinscrito_correo'),
            name: 'preinscrito_correo',
            readOnly: true,
            width: 290,
            fieldLabel: '',
            value: '',
            style: "margin-left: 20px;"
          }
        ]
      },
      {
        xtype: 'container',
        layout: 'column',
        defaults: {
          layout: '100%'
        },        
        width: (50+100+80+80)*2,
        items: [
          {
            xtype:'label',
            text: 'Institución / Organización',
            style: "font-weight: bold; margin-top: 5px;",
            width: 310
          },
          {
            xtype:'label',
            html: '<b>Institución / Organización</b> <small>(Sistema de preinscripción)</small>',
            style: "margin-top: 5px; margin-left: 20px;",
            width: 290
          },
          {
            xtype: 'container',
            layout: "anchor",
            width: 310,
            items:[
              {
                xtype:'siga.select',
                id: me._('id_institucion'),
                name: 'id_institucion',
                anchor: "100%",
                fieldLabel: 'Institución / Organización',
                hideLabel: true,
                value: '',
                internal:{
                  valueField: 'id',
                  columns: {field: ["nombre"], title: ["Institución"], width: ['100%'], sort: ["ASC"]},
                  url: 'module/academia_dependencia/',
                  actionOnList:'onList',
                  actionOnGet:'onGet'
                }
              },
            ]
          },
          {
            xtype:'textfield',
            id: me._('preinscrito_institucion'),
            name: 'preinscrito_institucion',
            readOnly: true,
            width: 290,
            fieldLabel: '',
            value: '',
            style: "margin-left: 20px;"
          }
        ]
      },

      {
        xtype:'tbspacer',
        flex:1,
        height: 10,
      },
      {
        xtype: 'tabpanel',
        id: me._('tabs'),
        //margin: 20,
        //style: "margin-top: 5px;",
        //marginTop: 20,
        width: 310*2,
        height: 220,
        collapsed: false,
        frameHeader: false,
        activeTab: 0,
        plain: true,
        items: [                        
          {
            xtype: 'panel',
            id: me._('tab_cursos_realizados'),
            title: 'Cursos Realizados',
            layout: {
              type: 'fit'
            },
            items: [
              {
                xtype: 'gridpanel',
                id: me._('grid_cursos_realizados'),
                border: 0,
                preventHeader: true,
                scroll: 'vertical',
                columns: [
                  new Ext.grid.RowNumberer(),
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'codigo',
                    text: 'Código',
                    width: '28%',
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'curso',
                    text: 'Curso',
                    width: '35%',
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'fecha',
                    text: 'Fecha',
                    width: '22%',
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'estado',
                    text: 'Estado',
                    width: '10%',
                    menuDisabled: true,
                    sortable: false,
                    flex: 1
                  }
                ],
                store: {
                  fields: ['codigo','curso','fecha','estado','calificacion'],
                }
              }
              
            ]
          },
          {
            xtype: 'panel',
            id: me._('tab_cursos_preinscritos'),
            title: 'Cursos Preinscritos',
            layout: {
              type: 'fit'
            },
            autoScroll: true,
            items: [
              {
                xtype: 'gridpanel',
                id: me._('grid_cursos_preinscritos'),
                border: 0,
                preventHeader: true,
                scroll: 'vertical',
                columns: [
                  new Ext.grid.RowNumberer(),
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'fecha',
                    text: 'Fecha',
                    width: 150,
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'curso',
                    text: 'Curso',
                    flex: 1,
                    menuDisabled: true,
                    sortable: false,
                  }
                ],
                store: {
                  fields: ['fecha','curso'],
                }
              }
              
            ]
          },
          {
            xtype: 'panel',
            id: me._('tab_registro_llamadas'),
            title: 'Registro de Llamadas',
            layout: {
              type: 'fit'
            },
            items: [
              {
                xtype: 'gridpanel',
                id: me._('grid_llamadas'),
                border: 0,
                preventHeader: true,
                scroll: 'vertical',
                columns: [
                  new Ext.grid.RowNumberer(),
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'fecha',
                    text: 'Fecha',
                    width: 120,
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'telefono',
                    text: 'Teléfono',
                    width: 80,
                    menuDisabled: true,
                    sortable: false,
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'motivo',
                    text: 'Motivo',
                    width: 200,
                    menuDisabled: true,
                    sortable: false,
                    tdCls : 'cell-asl_multiline'
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'resultado',
                    text: 'Resultado',
                    flex: 1,
                    menuDisabled: true,
                    sortable: false,
                    tdCls : 'cell-asl_multiline'
                  }
                ],
                store: {
                  fields: ['fecha','telefono','motivo','resultado'],
                }
              }
            ]
          },
          
        ]
      }
      
      
    ];
    
    me.callParent(arguments);    
    
    
    
  },
  
  init: function(){
    var me=this;
    if(me.internal.nacionalidad && me.internal.cedula){
      me.getCmp("nacionalidad").setValue(me.internal.nacionalidad);
      me.getCmp("cedula").setValue(me.internal.cedula);
      me.getCmp("nacionalidad").setDisabled(true);
      me.getCmp("cedula").setDisabled(true);
      me.onBuscar();
    }
  },
  
  onBuscar: function(){
    var me=this;
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/academia_inscribir/',
      params:{
        action: 'onGetPersona',
        nacionalidad: me.getCmp('nacionalidad').getValue(),
        cedula: me.getCmp('cedula').getValue()
      },
      success:function(request){//alert(request.responseText);
        var result=Ext.JSON.decode(request.responseText);
    
        me.getCmp('primer_nombre').setValue(result["persona"]["primer_nombre"]);
        me.getCmp('segundo_nombre').setValue(result["persona"]["segundo_nombre"]);
        me.getCmp('primer_apellido').setValue(result["persona"]["primer_apellido"]);
        me.getCmp('segundo_apellido').setValue(result["persona"]["segundo_apellido"]);

        
        me.getCmp('telefono').setValue(result["persona"]["telefono"]);
        me.getCmp('correo').setValue(result["persona"]["correo"]);
        me.getCmp('id_institucion').setValue(result["id_institucion"]);
        
        if(result["preinscrito"]){
          me.getCmp('preinscrito_nombres_apellidos').setValue(result["preinscrito"]["nombres_apellidos"]);
          me.getCmp('preinscrito_telefono').setValue(result["preinscrito"]["telefono"]);
          me.getCmp('preinscrito_correo').setValue(result["preinscrito"]["correo"]);
          me.getCmp('preinscrito_institucion').setValue(result["preinscrito"]["institucion"]);
        }
        else{
          me.getCmp('preinscrito_nombres_apellidos').setValue("");
          me.getCmp('preinscrito_telefono').setValue("");
          me.getCmp('preinscrito_correo').setValue("");
          me.getCmp('preinscrito_institucion').setValue("");
        }
        
        if(!result["cursos_realizados"])
          result["cursos_realizados"]=[];
        if(!result["cursos_preinscritos"])
          result["cursos_preinscritos"]=[];
        if(!result["llamadas"])
          result["llamadas"]=[];

        me.getCmp('grid_cursos_realizados').store.loadData(result["cursos_realizados"], false);
        me.getCmp('grid_cursos_preinscritos').store.loadData(result["cursos_preinscritos"], false);
        me.getCmp('grid_llamadas').store.loadData(result["llamadas"], false);
        
        //me.setMessage(result.message,"green");
      },
      failure:function(request){
        //Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
    
    
    
  },

  onSave: function(){
    var me=this;
    
    if(!me.getCmp("tab_data").getForm().isValid()){
      me.setMessage("Exiten datos invalidos en el formulario.","red");
      return;
    }
    
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/academia_inscribir/',
      params:{
        action: 'onAgregarPersona',
        id_curso_aperturado:me.internal.id_curso_aperturado,
        nacionalidad: me.getCmp('nacionalidad').getValue(),
        cedula: me.getCmp('cedula').getValue(),
        primer_nombre: me.getCmp('primer_nombre').getValue(),
        segundo_nombre: me.getCmp('segundo_nombre').getValue(),
        primer_apellido: me.getCmp('primer_apellido').getValue(),
        segundo_apellido: me.getCmp('segundo_apellido').getValue(),
        telefono: me.getCmp('telefono').getValue(),
        correo: me.getCmp('correo').getValue(),
        id_institucion: me.getCmp('id_institucion').getValue()
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        //Ext.MessageBox.hide();
        if(result.success==false){
          me.setMessage(result.message,"red");
          return;
        }
        
        
        if(me.internal.parent)
          me.internal.parent.onRecargar();
        me.close();
      },
      failure:function(request){
        //Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });
  } 
  
});








