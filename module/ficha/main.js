siga.define('ficha', {
  extend: 'siga.window',
  title: 'Nómina - Ficha',
  width: 850,
  height: 720,

  initComponent: function(){
    var me = this;

    _defaults=me.getInternal("field_defaults");
    me.internal._defaults=_defaults;
    me.setInternal({
      field_defaults: {
      }
    });
    /*
    me.setInternal({
      ventanaUploadFile: Ext.create('siga.windowForm', {
        title: 'Cargar Archivos al Expediente',
        width: 500,
        height: 250,
        modal: true,

        items: [
          {
            xtype: 'filefield',
            id: me._('uploadx'),
            name: 'upload[]',

            buttonConfig: {
              text: 'Agregar',
              iconCls: 'siga-icon-16 icon-add',
              width: 150,
              height: 150
            },
            width: 150,
            height: 150,
            //buttonOnly: true,
            hideLabel: true,
            listeners: {
              change: function(fb, v){
                console.log(v);

              }
            }
          }
        ],

        listeners: {
          show: function(){
            var _me=this;
            console.log(me.getCmp('uploadx').fileInputEl);
            me.getCmp('uploadx').fileInputEl.set({multiple: true});
            //me.getCmp('uploadx').el.dom.multiple=true;
            //me.internal.ventanaUploadFile.getComponent('uploadx').set({ multiple: true });
            //_me.items[0].set({ multiple: true });
          }
        }



      })
    });*/

    me.storeParentesco = Ext.create('Ext.data.Store', {
      fields: ['id','parentesco'],
      autoLoad: true,
      pageSize: 100,
      proxy: {
        type:'ajax',
        url: 'module/grupo_familiar_parentesco/',
        actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
        timeout: 3600000,
        reader: {
          type: 'json',
          rootProperty: 'result',
          totalProperty:'total'
        },
        extraParams: {
          action: 'onList'
        }
      },
      listeners: {
        load: function(store, records, successful){
          //me.internal.id_unidad_coordinacion=records[0].get("id");
          //me.getCmp("id_unidad_coordinacion").setValue(records[0].get("id"));
        }
      }
    });


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
        anchor: '100%',
        layout: {
          type: 'hbox',
          align: 'stretch'
        },
        items: [
          {
            xtype: 'container',
            flex: 3,
            //style: 'border: 1px solid black;',
            layout: 'anchor',
            padding: '0 20px 0 40px',
            items: [
              //primera fila, campo cedula+buscar
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
              //segunda fila Nombres / Apellidos
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
                      },
                      {
                        xtype:'textfield',
                        id: me._('segundo_nombre'),
                        name: 'segundo_nombre',
                        flex: 1,
                        fieldLabel: '',
                        value: ''
                      },
                      {
                        xtype:'textfield',
                        id: me._('primer_apellido'),
                        name: 'primer_apellido',
                        flex: 1,
                        fieldLabel: '',
                        value: ''
                      },
                      {
                        xtype:'textfield',
                        id: me._('segundo_apellido'),
                        name: 'segundo_apellido',
                        flex: 1,
                        fieldLabel: '',
                        value: ''
                      }
                    ]
                  }
                ]
              },
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
              //Telefono y Extensión
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
                    value: '',
                    margin: '5px 0 0 0px',
                  }
                ]
              },
              {
                xtype: 'container',
                layout: 'fit',
                id: me._('container_ingreso_egreso'),
                items: []
              },

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
                    //disabled: true,
                    readOnly: true,
                    fieldLabel: '<span style="color:gray;">Antiguedad en la Institución</span>',
                    value: '',
                    margin: '5px 0 0 0px',
                  }
                ]
              },
              {
                xtype: 'container',
                defaults: _defaults,
                layout: 'hbox',
                items: [
                  {
                    xtype:'textfield',
                    id: me._('codigo'),
                    name: 'codigo',
                    flex: 1,
                    fieldLabel: 'Código de Asistencia',
                    value: '',
                    margin: '5px 0 0 0px',
                  },
                  {
                    xtype:'combobox',
                    id: me._('activo'),
                    name: 'activo',
                    flex: 1,
                    //anchor: "100%",
                    margin: '5px 0px 0px 40px',
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
                ]
              },



            ]
          },
          {
            xtype: 'container',
            flex: 2,
            //style: 'border: 1px solid black;',
            padding: '0 40px 0 0',
            //defaults: _defaults,
            items: [
              {
                xtype: 'fieldset',
                title: '<b>Expediente</b>',
                margin: 0,
                flex: 1,
                layout: {
                  type: 'vbox',
                  align: 'center'
                },
                height: 315,
                items: [
                  {
                    xtype: "image",
                    id: me._("foto"),
                    src: "",
                    //width: "60%",
                    height: 120
                  },
                  {
                    xtype: 'form',
                    id: me._("form_upload"),
                    layout: "fit",
                    flex: 1,
                    width: "100%",
                    border: 0,
                    items:[
                      {
                        xtype: 'treepanel',
                        id: me._('archivos'),
                        isFormField:false,
                        autoScroll: true,
                        title: '',
                        rootVisible: false,
                        //si eliminosmos el layout muestra un scroll horizontal cuando el treepanel se encuentra vacio (por resolver, problema de extjs)
                        /*layout: {
                          type: 'hbox',
                          align: 'stretch'
                        },*/
                        useArrows: true,
                        flex: 1,
                        width: "100%",
                        fbar:[
                          {
                            xtype: "tbspacer",
                            flex: 1
                          },
                          {
                            xtype: 'filefield',
                            id: me._("upload"),
                            name: 'upload[]',
                            buttonConfig: {
                              text: 'Agregar',
                              iconCls: 'siga-icon-16 icon-add',
                              width: 80,
                            },
                            setDisabled: function(){}, disable: function(){}, //temporalmente, error presentado a la hora de desactivar el campo
                            width: 80,
                            buttonOnly: true,
                            hideLabel: true,
                            listeners: {
                              change: function(fb, v){
                                var path="";
                                var seleccion=me.getCmp("archivos").getSelection();
                                if(seleccion && seleccion.length==1)
                                  path=seleccion[0]["data"]["path"];
                                else
                                  path=me.getCmp('nacionalidad').getValue()+me.getCmp('cedula').getValue().trim()+"/";

                                if(!me.getCmp('cedula').getValue().trim()){
                                  me.setMessage("Debe seleccionar a la persona para cargar los archivos.","red");
                                  return;
                                }

                                var form = me.getCmp("form_upload").getForm();
                                if(form.isValid()){
                                  form.submit({
                                    url: 'module/ficha/',
                                    params:{
                                      action: 'onUpload_Archivo',
                                      path: path
                                    },
                                    waitMsg: 'Cargando Archivo al Expediente...',
                                    success: function(form, o) {
                                      me.onList_Archivo();
                                    },
                                    failure:function(form,o){
                                      me.onList_Archivo();
                                    }
                                  });
                                }
                              }
                            }
                          },
                          /*{
                            text:'Quitar',
                            iconCls: 'siga-icon-16 icon-remove',
                            width: 80,
                            handler:function(){
                              var seleccion=me.getCmp("archivos").getSelection();
                              if(!(seleccion && seleccion.length==1))
                                return;

                              Ext.MessageBox.confirm( "Eliminar",
                                                      "¿Desea eliminar el archivo <b>\xAB"+seleccion[0]["data"]["text"]+"\xBB</b> del expediente?",
                                                      function(btn,text){
                                                        if(btn == 'yes'){
                                                          Ext.Ajax.request({
                                                            method: 'POST',
                                                            url:'module/ficha/',
                                                            params:{
                                                              action: 'onDelete_Archivo',
                                                              archivo: seleccion[0]["data"]["path"]+seleccion[0]["data"]["text"]
                                                            },
                                                            success: function(request){
                                                              var result=Ext.JSON.decode(request.responseText);
                                                              me.onList_Archivo();
                                                            },
                                                            failure:function(request){
                                                              var result=Ext.JSON.decode(request.responseText);
                                                              me.setMessage(result.message,"red");
                                                              me.onList_Archivo();
                                                            }
                                                          });
                                                        }
                                                      });
                            }
                          },*/
                          {
                            xtype: "tbspacer",
                            flex: 1
                          }
                        ],
                        listeners: {
                          itemdblclick: function( el, record, item, index, e, eOpts ){
                            if(record.get("leaf") && record.get("link"))
                              window.open(record.get("link"));
                          },
                          itemcontextmenu: function( el, record, item, index, e, eOpts ){
                            e.stopEvent();
                            var menu = Ext.create('Ext.menu.Menu', {
                              items: [
                                {
                                  text: 'Agregar',
                                  handler: function(){
                                    me.getCmp("upload").fileInputEl.dom.click();
                                  }
                                },
                                {
                                  text: 'Eliminar',
                                  handler: function(){
                                    if(record.get('children'))
                                      if(record.get('children').length!=0){
                                        me.setMessage("La carpeta debe esta vacia para poder eliminarla.","red");
                                        return;
                                      }

                                    var _archivo=record.get('path')+(record.get('leaf')?record.get('text'):"");

                                    Ext.MessageBox.confirm( "Eliminar",
                                                            "¿Desea eliminar el archivo <b>\xAB"+record.get('text')+"\xBB</b> del expediente?",
                                                            function(btn,text){
                                                              if(btn == 'yes'){
                                                                Ext.Ajax.request({
                                                                  method: 'POST',
                                                                  url:'module/ficha/',
                                                                  params:{
                                                                    action: 'onDelete_Archivo',
                                                                    archivo: _archivo
                                                                  },
                                                                  success: function(request){
                                                                    var result=Ext.JSON.decode(request.responseText);
                                                                    me.onList_Archivo();
                                                                  },
                                                                  failure:function(request){
                                                                    var result=Ext.JSON.decode(request.responseText);
                                                                    me.setMessage(result.message,"red");
                                                                    me.onList_Archivo();
                                                                  }
                                                                });
                                                              }
                                                            });
                                  }
                                },
                                {
                                  text: 'Renombrar',
                                  handler: function() {
                                    Ext.Msg.prompt(me.title, 'Renombrar el archivo <b>\xAB'+record.get('text')+"\xBB</b> a:", function(btn, text){
                                      if(btn == 'ok'){
                                        if(record.get('text')==text || !text.trim()) return;

                                        var _archivo=record.get('path')+(record.get('leaf')?record.get('text'):"");
                                        var _archivo_nuevo=record.get('leaf')?(record.get('path')+text):record.get('parent')+text;

                                        Ext.Ajax.request({
                                          method: 'POST',
                                          url:'module/ficha/',
                                          params:{
                                            action: 'onRename_Archivo',
                                            archivo: _archivo,
                                            archivo_nuevo: _archivo_nuevo
                                          },
                                          success: function(request){
                                            var result=Ext.JSON.decode(request.responseText);
                                            me.onList_Archivo();
                                          },
                                          failure:function(request){
                                            var result=Ext.JSON.decode(request.responseText);
                                            me.setMessage(result.message,"red");
                                            me.onList_Archivo();
                                          }
                                        });
                                      }
                                    },
                                    null,
                                    false,
                                    record.get('text')
                                    );
                                  }
                                },
                                {
                                  text: 'Nueva Carpeta',
                                  handler: function() {
                                    Ext.Msg.prompt(me.title, "Ingrese el nombre de la carpeta:", function(btn, text){
                                      if(btn == 'ok'){
                                        if(!text.trim()) return;

                                        Ext.Ajax.request({
                                          method: 'POST',
                                          url:'module/ficha/',
                                          params:{
                                            action: 'onAdd_Carpeta',
                                            carpeta: record.get('path')+text
                                          },
                                          success: function(request){
                                            var result=Ext.JSON.decode(request.responseText);
                                            me.setMessage(result.message,result.success?"green":"red");
                                            me.onList_Archivo();
                                          },
                                          failure:function(request){
                                            var result=Ext.JSON.decode(request.responseText);
                                            me.setMessage(result.message,"red");
                                            me.onList_Archivo();
                                          }
                                        });
                                      }
                                    },
                                    null,
                                    false,
                                    "Carpeta Nueva"
                                    );
                                  }
                                },
                              ]
                            });
                            menu.showAt(e.getX(),e.getY());
                          }
                        }
                      }


                    ]
                  },





                ]
              }

            ]
          },
        ]
      },
      {
        xtype: 'tabpanel',
        id: me._('subtabs'),
        margin: 20,
        collapsed: false,
        frameHeader: true,
        activeTab: 0,
        plain: false,
        items: [
          {
            xtype: 'form',
            frame: false,
            id: me._('tab_extra'),
            frameHeader: false,
            autoScroll:true,
            layout: 'anchor',
            title: 'Información Extra',
            defaults: me.getInternal("field_defaults"),
            layout: 'anchor',
            padding: '0 20px 20px 20px',
            items: [

              {
                xtype: 'container',
                defaults: _defaults,
                layout: 'hbox',
                items: [
                  //Cuenta Nomina
                  {
                    xtype:'textfield',
                    id: me._('cuenta_nomina'),
                    name: 'cuenta_nomina',
                    flex: 0.70,
                    fieldLabel: 'Cuenta Nomina',
                    value: '',
                    margin: '5px 0 0 0px',
                  },
                  //Escala de Sueldo
                  {
                    xtype: 'combobox',
                    id: me._('id_escala_salarial'),
                    name: 'id_escala_salarial',
                    fieldLabel: 'Escala Salarial',
                    margin: '5px 0 0 40px',
                    flex: 0.30,
                    editable: false,
                    queryMode: "local",
                    //rootProperty: "result",
                    displayTpl: '<tpl for=".">{escala} - {sueldo_basico}</tpl>',
                    tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{escala}</b> <small style="float: right;">{sueldo_basico}</small></li></tpl></ul>',
                    store: {
                      fields: ['id','escala_sueldo_basico'],
                      autoLoad: true,
                      pageSize: 100,
                      proxy: {
                        type:'ajax',
                        url: 'module/nomina_escala_salarial/',
                        actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
                        timeout: 3600000,
                        reader: {
                          type: 'json',
                          rootProperty: 'result',
                          totalProperty:'total'
                        },
                        extraParams: {
                          action: 'onList'
                        }
                      },
                      listeners: {
                        load: function(store, records, successful){
                          //me.internal.id_unidad_coordinacion=records[0].get("id");
                          //me.getCmp("id_unidad_coordinacion").setValue(records[0].get("id"));
                        }
                      }
                    },
                    //displayField: 'escala_sueldo_basico',
                    valueField: 'id',
                    allowBlank: true,
                    forceSelection: true
                  },


                ]
              },

              //Antiguedad APN
              {
                xtype: 'container',
                defaults: _defaults,
                layout: 'hbox',
                items: [
                  {
                    xtype: 'container',
                    defaults: _defaults,
                    layout: 'hbox',
                    margin: '5px 0 0 0px',
                    flex: 0.70,
                    items: [
                      {
                        xtype:'numberfield',
                        id: me._('antiguedad_apn'),
                        name: 'antiguedad_apn',
                        flex: 0.75,
                        fieldLabel: 'Antiguedad Administración Pública <small>(Otros Años)</small>',
                        value: '',
                        margin: '0 0 0 0',
                        minValue: 0,
                        allowDecimals: false,
                        allowNegative: false,
                        listeners: {
                          change: function(){
                            me.updateAntiguedadTotal();
                          }
                        }
                      },
                      {
                        xtype:'textfield',
                        id: me._('antiguedad_total'),
                        name: 'antiguedad_total',
                        flex: 0.25,
                        fieldLabel: '<span style="color:gray;">Total <small>(Años)</small></span>',
                        value: '',
                        margin: '0 0 0 10px',
                        readOnly: true,
                        allowDecimals: false,
                        allowNegative: false
                      },
                    ]
                  },
                  {
                    xtype:'numberfield',
                    id: me._('profesionalizacion_porcentaje'),
                    name: 'profesionalizacion_porcentaje',
                    flex: 0.30,
                    fieldLabel: 'Profesionalización <small>(%)</small>',
                    value: '',
                    margin: '5px 0 0 40px',
                    minValue: 0,
                    allowDecimals: false,
                    allowNegative: false
                  }
                ]
              },



              {
                xtype: 'combobox',
                id: me._('id_periodo'),
                name: 'id_periodo',
                anchor: '100%',
                margin: {bottom: '0px'},
                fieldLabel: 'Periodo - Nóminas <small style="color:gray;">(Indica en que nóminas se encuentra la persona en un periodo específico)</small>',
                labelAlign: 'top',
                labelSeparator: '',
                labelStyle: 'font-weight: bold;',
                editable: false,
                queryMode: "local",
                displayTpl: '<tpl for=".">{codigo} {descripcion}</tpl>',
                tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
                store: {
                  fields: ['id','periodo'],
                  autoLoad: true,
                  pageSize: 1000,
                  proxy: {
                    type:'ajax',
                    url: 'module/nomina_periodo/',
                    actionMethods: {read: "POST"},//actionMethods:'POST',
                    timeout: 3600000,
                    reader: {
                      type: 'json',
                      rootProperty: 'result',
                      totalProperty:'total'
                    },
                    extraParams: {
                      action: 'onList',
                      text: '',
                      id: '',
                      sort: '[{"property": "codigo", "direction": "ASC"}]'
                    }
                  },
                  listeners: {
                    load: function(store, records, successful){
                      me.getCmp("id_periodo").reset();
                      me.id_periodo_default="";
                      if(records.length>0){
                        me.id_periodo_default=records[records.length-1].get("id");
                        me.getCmp("id_periodo").setValue(me.id_periodo_default);
                      }

                    },
                    beforeload: function(store,operation,eOpts){
                      store.proxy.extraParams.tipo='Q';
                    }
                  }
                },
                listeners: {
                  change: function(){
                    me.changePeriodo();
                  }
                },
                displayField: 'periodo',
                valueField: 'id',
                allowBlank: false,
                forceSelection: true,
              },
              {
                xtype: 'container',
                id: me._('nomina_periodo'),
                style: "font-size:11px; color:gray;background-color:#e7e7e7; padding:5px 8px; border: 1px #b5b8c8 solid; border-top: 0;",
                html: "<b>N/A</b>"
              }
            ]
          },
          {
            xtype: 'panel',
            id: me._('tab_carga'),
            title: 'Carga Familiar',
            layout: 'fit',
            height: 170,
            dockedItems: [
              {
                xtype: 'container',
                layout: {
                  align: 'middle',
                  type: 'hbox'
                },
                dock: 'bottom',
                style: "background: #d0d0d0;",
                items: [
                  {
                    xtype: 'button',
                    text: 'Agregar',
                    tooltip: 'Agregar',
                    iconCls: 'siga-icon-16 icon-add',
                    width: 80,
                    listeners: {
                      click: function(){
                        var grid  = me.getCmp("gridCargaFamiliar");
                        var store = grid.getStore();
                        var index = store.getCount();

                        store.insert(index,{id_grupo_familiar: '', nacionalidad:'' ,cedula: '', nombres_apellidos: '', genero: '', fecha_nacimiento: '', edad: '', id_parentesco: ''})

                        grid.getPlugin('rowediting').startEdit(index);
                      }
                    }
                  },
                  {
                    xtype: 'button',
                    text: 'Quitar',
                    tooltip: 'Quitar',
                    iconCls: 'siga-icon-16 icon-remove',
                    width: 80,
                    listeners: {
                      click: function(){
                        var grid = me.getCmp("gridCargaFamiliar");
                        var selModel = grid.getSelectionModel();
                        var record = selModel.getSelection();
                        var store = grid.getStore();
                        store.remove(record);
                      }
                    }
                  },
                  {
                    xtype: "tbspacer",
                    flex: 1
                  }
                ]
              }
            ],
            items: [
              {
                xtype: 'gridpanel',
                id: me._('gridCargaFamiliar'),
                border: 0,
                preventHeader: true,
                plugins: {
                  rowediting: {
                    clicksToMoveEditor: 1,
                    autoCancel: false,
                    pluginId: 'rowediting'
                  }
                },
                store: {
                  fields: ['id_grupo_familiar','nacionalidad','cedula','nombres_apellidos','genero','fecha_nacimiento','edad','id_parentesco'],
                  data: []
                },
                columns: [
                {
                  xtype: 'rownumberer',
                  dataIndex: 'n',
                  text: "<b>Nº</b>",
                  width: 30,
                  sortable: false,
                  locked: true,
                  lockable: true,
                  draggable: false,
                  resizable: false,
                  align: "center"
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'cedula',
                    text: '<b>Cédula</b>',
                    width: '12%',
                    menuDisabled: true,
                    sortable: false,
                    editor: true
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'nombres_apellidos',
                    text: '<b>Nombres / Apellidos</b>',
                    //width: '15%',
                    flex: 1,
                    menuDisabled: true,
                    sortable: false,
                    editor: true
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'genero',
                    text: '<b>Genero</b>',
                    width: '12%',
                    menuDisabled: true,
                    sortable: false,
                    editor: {
                      xtype:'combobox',
                      store: {
                        fields: ['id', 'nombre'],
                        data : [
                          {"id":"M", "nombre":"MASCULINO"},
                          {"id":"F", "nombre":"FEMENINO"}
                        ]
                      },
                      displayField: 'nombre',
                      valueField: 'id',
                      allowBlank: true,
                      forceSelection: true,
                      editable: false,
                      value: 'M'
                    },
                    renderer: function(v) {
                      if(v=="M") return "MASCULINO";
                      if(v=="F") return "FEMENINO";
                      return v;
                    }
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'fecha_nacimiento',
                    text: '<b>Fecha Nacimiento</b>',
                    width: '18%',
                    menuDisabled: true,
                    sortable: false,
                    editor: {
                      xtype:'datefield'
                    },
                    //renderer: Ext.util.Format.dateRenderer('d/m/Y')
                    renderer: function(value){
                      if(typeof(value) === 'string')
                        value=value+" 00:00:00";
                      console.log(value);
                      return Ext.util.Format.date(value,'d/m/Y');
                    }
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'edad',
                    text: '<b>Edad</b>',
                    width: '10%',
                    menuDisabled: true,
                    sortable: false,
                    align: "center",
                    renderer: function(value, metaData , record, rowIndex, colIndex, store, view){
                      var fecha_nacimiento=record.get("fecha_nacimiento");
                      if(!fecha_nacimiento)
                        return "";
                      var edad=Ext.Date.diff(new Date(fecha_nacimiento),new Date(siga.timer.result.fecha+" 24:00:00"),Ext.Date.YEAR);
                      var tiempo="años";
                      if(edad==0){
                        edad=Ext.Date.diff(new Date(fecha_nacimiento),new Date(siga.timer.result.fecha+" 24:00:00"),Ext.Date.MONTH);
                        tiempo="meses";

                        if(edad==0){
                          edad=Ext.Date.diff(new Date(fecha_nacimiento),new Date(siga.timer.result.fecha+" 24:00:00"),Ext.Date.DAY);
                          tiempo="dias";
                          if(edad<=0){
                            return "";
                          }
                          else if(edad==1)
                            tiempo="dia";
                        }
                        else if(edad==1){
                          tiempo="mes";
                        }
                      }
                      else if(edad==1){
                        tiempo="año";
                      }
                      if(edad<=0) return "";
                      return edad+" "+tiempo;
                    }
                  },
                  {
                    xtype: 'gridcolumn',
                    dataIndex: 'id_parentesco',
                    text: '<b>Parentesco</b>',
                    width: '20%',
                    menuDisabled: true,
                    sortable: false,
                    editor: {
                      xtype:'combobox',
                      store: me.storeParentesco,
                      displayField: 'parentesco',
                      valueField: 'id',
                      allowBlank: true,
                      forceSelection: true,
                      editable: false,
                      value: ''
                    },
                    renderer: function(v){
                      if(!v) return "";
                      var idx = me.storeParentesco.find('id',v);
                      var rec = me.storeParentesco.getAt(idx);
                      return rec.get('parentesco');
                    }
                  },
                ],
                scroll: 'vertical',
                listeners: {
                  select: function(dataview, record, item, index, e){

                  },
                  itemdblclick: function(dataview, record, item, index, e){

                  }
                }
              }

            ]
          }
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

    me.onNew();

    me.getCmp("upload").setDisabled(true);

  },

  onNew: function(){
    var me=this;
    me.setInternal({
      buscar_nacionalidad: '',
      buscar_cedula: ''
    });

    me.getCmp("container_ingreso_egreso").removeAll();
    me.getCmp("gridCargaFamiliar").getStore().removeAll();
    me.onAddFechaIngresoEgreso(0);
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    me.getCmp("foto").setSrc("image/photo-default.png");
    me.getCmp('archivos').setRootNode({expanded: true, children: []});
    me.getCmp('archivos').expandAll();
    if(me.id_periodo_default){
      me.getCmp("id_periodo").setValue(me.id_periodo_default);
    }
    me.onSearch();
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
              },
              change: function(){
                me.calculateAntiguedadInstitucion();
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
              },
              change: function(){
                me.calculateAntiguedadInstitucion();
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
    me.getCmp("gridCargaFamiliar").getStore().removeAll();


    if(!result || result.length==0){
      me.antiguedad_anio=0;
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
      me.getCmp('cuenta_nomina').setValue("");
      me.getCmp('id_escala_salarial').setValue("");
      me.getCmp('antiguedad_apn').setValue("0");
      me.getCmp('antiguedad_total').setValue("0");
      me.getCmp('profesionalizacion_porcentaje').setValue("0");
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
    me.getCmp('cuenta_nomina').setValue(result[0]['cuenta_nomina']);
    me.getCmp('id_escala_salarial').setValue(result[0]['id_escala_salarial']);
    me.getCmp('antiguedad_apn').setValue(result[0]['antiguedad_apn']);
    me.getCmp('profesionalizacion_porcentaje').setValue(result[0]['profesionalizacion_porcentaje']);

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
    me.antiguedad_anio=0;
    if(result[0]['antiguedad_anio'])
      me.antiguedad_anio=result[0]['antiguedad_anio']*1;

    me.getCmp('antiguedad_total').setValue(result[0]['antiguedad_apn']*1+me.antiguedad_anio*1);

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

    //carga familiar
    if(result[0]['grupo_familiar'] && result[0]['grupo_familiar'].length>0){
      var store=me.getCmp("gridCargaFamiliar").getStore();
      for(var i = 0; i < result[0]['grupo_familiar'].length; i++) {
        store.insert(i,{
          id_grupo_familiar:   result[0]['grupo_familiar'][i]["id"],
          nacionalidad:        result[0]['grupo_familiar'][i]["nacionalidad"],
          cedula:              result[0]['grupo_familiar'][i]["cedula"],
          nombres_apellidos:   result[0]['grupo_familiar'][i]["nombres_apellidos"],
          genero:              result[0]['grupo_familiar'][i]["genero"],
          fecha_nacimiento:    result[0]['grupo_familiar'][i]["fecha_nacimiento"],
          edad:                '',
          id_parentesco:       result[0]['grupo_familiar'][i]["id_parentesco"]
        });
      }
    }

    me.changePeriodo();
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

    me.getCmp("gridCargaFamiliar").getPlugin('rowediting').completeEdit();

    var grupo_familiar=[];
    for (var i=0; i<me.getCmp("gridCargaFamiliar").getStore().getCount(); i++) {
      var tmp=me.getCmp("gridCargaFamiliar").getStore().getAt(i).getData();

      tmp['cedula']            = Ext.String.trim(tmp['cedula']);
      tmp['nombres_apellidos'] = Ext.String.trim(tmp['nombres_apellidos']);
      tmp['genero']            = Ext.String.trim(tmp['genero']);
      tmp['id_parentesco']     = Ext.String.trim(tmp['id_parentesco']);

      if(!tmp['nombres_apellidos']){
        me.setMessage("Debe ingresar nombres y apellidos para la carga familiar, en la fila #"+(i+1),"red");
        return;
      }
      if(!tmp['genero']){
        me.setMessage("Debe seleccionar el genero para la carga familiar, en la fila #"+(i+1),"red");
        return;
      }
      if(!tmp['fecha_nacimiento']){
        me.setMessage("Debe ingresar la fecha de nacimiento para la carga familiar, en la fila #"+(i+1),"red");
        return;
      }
      if(!tmp['id_parentesco']){
        me.setMessage("Debe seleccionar el parentesco para la carga familiar, en la fila #"+(i+1),"red");
        return;
      }

      grupo_familiar.push({
        id: tmp['id_grupo_familiar'],
        id_parentesco: tmp['id_parentesco'],
        nacionalidad: tmp['nacionalidad'],
        cedula: tmp['cedula'],
        nombres_apellidos: tmp['nombres_apellidos'],
        genero: tmp['genero'],
        fecha_nacimiento: Ext.util.Format.date(tmp['fecha_nacimiento'],'Y-m-d')
      });
    }

    me.getCmp('tab_data').submit({
      method: 'POST',
      url: 'module/ficha/',
      params:{
        action: 'onSave',
        grupo_familiar: Ext.JSON.encode(grupo_familiar)
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
  },

  changePeriodo: function(){
    var me=this;
    me.getCmp("nomina_periodo").setHtml("<b>N/A</b>");

    var id_ficha=me.getCmp('id').getValue();
    var id_periodo=me.getCmp('id_periodo').getValue();
    if(!id_periodo){
      return;
    }
    me.id_periodo_default=id_periodo;

    if(!id_ficha){
      return;
    }


    Ext.Ajax.request({
      method: 'POST',
      url:'module/ficha/',
      params:{
        action: 'onNominaPeriodo',
        id_ficha: id_ficha,
        id_periodo: id_periodo

      },
      success: function(request){
        var result=Ext.JSON.decode(request.responseText);
        console.log(result);
        me.getCmp("nomina_periodo").setHtml("");
        var tmp="";
        for(var i=0; i<result.length; i++){
          tmp+=`
            <div>
              <b>`+result[i]["codigo"]+`</b>
              `+result[i]["nomina"]+`
              <br>
            </div>
          `;
        }
        if(!tmp){
          tmp=`
            <div>
              <b>N/A</b>
              <br>
            </div>
          `;
        }
        me.getCmp("nomina_periodo").setHtml(tmp);

      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
        //me.setMessage(result.message,"red");
      }
    });

  },

  updateAntiguedadTotal: function(){
    var me=this;
    var v=me.getCmp('antiguedad_apn').getValue()*1;
    me.getCmp('antiguedad_total').setValue(v+me.antiguedad_anio);
  },

  calculateAntiguedadInstitucion: function(){
    var me=this;
    me.antiguedad_anio=0;
    //var edad=Ext.Date.diff(new Date(fecha_nacimiento),new Date(siga.timer.result.fecha+" 24:00:00"),Ext.Date.YEAR);
    var n=me.getCmp("container_ingreso_egreso").items.length;
    for(var i=0; i<n; i++){
      var fi=me.getCmp("fecha_ingreso_"+i).getValue();
      if(!fi) continue;
      var ff=me.getCmp("fecha_egreso_"+i).getValue();
      if(!ff){
        ff=new Date(siga.timer.result.fecha+" 24:00:00");
      }
      if(isNaN(ff)){
        continue;
      }
      console.log(ff);
      var diff=Ext.Date.diff(fi,ff,Ext.Date.YEAR);
      if(diff>0){
        me.antiguedad_anio+=diff;
      }
    }

    me.getCmp("antiguedad").setValue(me.antiguedad_anio+" años");
    me.updateAntiguedadTotal();
  },

});


