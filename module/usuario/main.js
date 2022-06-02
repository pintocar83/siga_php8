siga.define('usuario', {
  extend: 'siga.window',
  title: 'Administración - Usuarios',
  width: 800,
  height: 500,

  initComponent: function(){
    var me = this;

    me.items=[
      {
        xtype:'container',
        layout: 'hbox',
        anchor: "100%",
        //style : "padding: 5px 40px 0px 40px;",
        items:[
          {
            xtype:'container',
            width: "50%",
            height: 310,
            style : "padding: 0px; margin: 0px;",
            layout: "anchor",
            defaults: me.getInternal("field_defaults"),
            items:[
              {
                xtype:'hidden',
                id: me._('id'),
                name: 'id',
                value: ''
              },
              {
                xtype:'siga.select',
                id: me._('id_persona_responsable'),
                name: 'id_persona_responsable',
                fieldLabel: 'Responsable',
                anchor: "100%",//width: 300,
                //style:'margin: 0px 20px 0px 0px;',
                margin: '0px 0px 0px 0px',
                value: '',
                internal:{
                  valueField: 'id',
                  columns: {field: ["identificacion","denominacion"], title: ["Identificación","Denominación"], width: ['25%','75%'], sort: ["ASC"]},
                  url: 'module/persona/',
                  actionOnList:'onListSelect',
                  actionOnGet:'onGetSelect',
                  output:1,
                }
              },
              {
                xtype:'textfield',
                id: me._('usuario'),
                name: 'usuario',
                fieldLabel: 'Usuario',
                value: '',
                anchor: "100%",
                //style:'margin: 5px 20px 0px 0px;',
                margin: '5px 0px 0px 0px',
              },
              {
                xtype:'combobox',
                id: me._('clave_type'),
                name: 'clave_type',
                anchor: "100%",
                //style:'margin: 5px 20px 0px 0px;',
                margin: '5px 0px 0px 0px',
                fieldLabel: 'Tipo de contraseña',
                store: {
                  fields: ['id', 'nombre'],
                  data : [
                    {"id":"plain",  "nombre":"TEXTO PLANO"},
                    {"id":"md5",    "nombre":"MD5"},
                    {"id":"sha1",   "nombre":"SHA1"},
                    {"id":"sha256", "nombre":"SHA256"},
                    {"id":"sha512", "nombre":"SHA512"},
                    {"id":"ldap_fundacite", "nombre":"LDAP - FUNDACITE SUCRE"},
                  ]
                },
                displayField: 'nombre',
                valueField: 'id',
                allowBlank: false,
                forceSelection: true,
                value: 'sha512',
                editable: false,
                listeners:{
                  change: function(){
                    switch(me.getCmp('clave_type').getValue()){
                      case "plain":
                      case "md5":
                      case "sha1":
                      case "sha256":
                      case "sha512":
                        me.getCmp('clave_value').setDisabled(false);
                        me.getCmp('clave_value_re').setDisabled(false);
                        break;
                      case "ldap_fundacite":
                        me.getCmp('clave_value').setValue("");
                        me.getCmp('clave_value_re').setValue("");
                        me.getCmp('clave_value').setDisabled(true);
                        me.getCmp('clave_value_re').setDisabled(true);
                      break;
                    }
                  }
                }
              },
              {
                xtype:'textfield',
                inputType: 'password',
                id: me._('clave_value'),
                name: 'clave_value',
                fieldLabel: 'Contraseña',
                value: '',
                anchor: "100%",
                //style:'margin: 5px 20px 0px 0px;',
                margin: '5px 0px 0px 0px',
              },
              {
                xtype:'textfield',
                inputType: 'password',
                id: me._('clave_value_re'),
                name: 'clave_value_re',
                fieldLabel: 'Contraseña (confirmación)',
                value: '',
                anchor: "100%",
                //style:'margin: 5px 20px 0px 0px;',
                margin: '5px 0px 0px 0px',
              },
              {
                xtype:'combobox',
                id: me._('activo'),
                name: 'activo',
                anchor: "100%",
                //style:'margin: 5px 20px 0px 0px;',
                margin: '5px 0px 0px 0px',
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
              },
            ]
          },//fin container 1
          {
            xtype:'container',
            width: "50%",
            height: 310,
            layout: "anchor",
            style : "padding: 0px; margin: 0px;",
            defaults: me.getInternal("field_defaults"),
            items:[
              {
                xtype: 'label',
                html: "<b>Perfil de acceso</b>",
                anchor: "100%",
                //style:'margin: 5px 0px 0px 20px;',
                margin: '5px 0px 0px 40px',
              },
              {
                xtype: 'treepanel',
                id: me._('usuario_perfil'),
                isFormField:false,
                autoScroll: true,
                title: '',
                rootVisible: false,
                //si eliminosmos el layout muestra un scroll horizontal cuando el treepanel se encuentra vacio (por resolver, problema de extjs)
                layout: {
                  type: 'hbox',
                  align: 'stretch'
                },
                useArrows: true,
                anchor: "100%",
                //style:'margin: 1px 0px 0px 20px;',
                margin: '0px 0px 0px 40px',
                height: 275,
                fbar:[
                  {
                    xtype: 'combobox',
                    id: me._('data'),
                    flex: 1,
                    isFormField:false,
                    fieldCls: 'login-input-base login-input-data',
                    store: {
                      fields: ['id', 'nombre'],
                      data : siga.value("datos")
                    },
                    displayField: 'nombre',
                    valueField: 'id',
                    allowBlank: false,
                    forceSelection: true,
                    value: siga.value("anio"),
                    editable: false
                  },
                  {
                    text:'Agregar',
                    iconCls: 'siga-icon-16 icon-add',
                    handler:function(){
                      var arbol=me.getCmp('usuario_perfil').getRootNode();
                      //obtener los nodos de la raiz
                      var nodo=arbol.childNodes;
                      //obtener el año seleccionado del select
                      var anio_select=me.getCmp('data').getValue();

                      //buscar el nodo del año seleccionado y guardarlo en i_nodo, sino es -1 no lo encontro
                      var i_nodo=-1;
                      for(var k=0;k<nodo.length;k++)
                        if(anio_select==nodo[k].data.text){
                          i_nodo=k;
                          break;
                        }

                      //si no existe el nodo del año seleccionado, crearlo en la raiz
                      if(i_nodo==-1){
                        arbol.appendChild({
                          iconCls: 'perfil-data-nodo',
                          text: anio_select,
                          name: anio_select,
                          leaf: false,
                          checked: true
                        });
                        //reordenar el arbol
                        me.getCmp('usuario_perfil').store.sort({property : 'text', direction: 'ASC'});

                        nodo=arbol.childNodes;
                        //buscar nuevamente la posición donde agrego el nuevo nodo
                        i_nodo=-1;
                        for(var k=0;k<nodo.length;k++)
                          if(anio_select==nodo[k].data.text){
                            i_nodo=k;
                            break;
                          }
                      }//fin i_nodo==-1

                      //no deberia ocurrir nunca, pq se agrego el nodo que no existía
                      if(i_nodo==-1) return;

                      //crear la ventana para seleccionar los perfiles de acceso
                      var selector=Ext.create("siga.windowSelect", {
                        internal: {
                          parent:{
                            fieldLabel: "Perfiles de acceso",
                            internal:{
                              valueField: 'perfil',
                              columns: {field: ["perfil","acceso"], title: ["Denominación","Acceso"], width: ['40%','60%'], sort: ["ASC"]},
                              url: 'module/usuario_perfil_acceso/',
                              actionOnList:'onListSelect',
                              actionOnGet:'onGetSelect',
                              page: 1,
                              limit: 100,
                              viewConfig:{
                                getRowClass: function(rec, rowIdx, params, store) {
                                  return "fila-expandida";
                                }
                              },
                            },
                            setValue: function(v){
                              //buscar si no existe, para no agregarlo
                              var hijos=nodo[i_nodo].childNodes;
                              for(var k=0;k<hijos.length;k++)
                                if(v==hijos[k].data.text)
                                  return;
                              var n = nodo[i_nodo].appendChild({
                                iconCls: 'perfil-tipo-nuevo-nodo',
                                text: v,
                                leaf: true
                              });
                              //reordenar el arbol
                              me.getCmp('usuario_perfil').store.sort({property : 'text', direction: 'ASC'});
                              nodo[i_nodo].expand();
                            }
                          }
                        }
                      });
                      //mostrar la ventana
                      selector.show();
                      //cargar el listado
                      selector.search();
                    }
                  },
                  {
                    text:'Quitar',
                    iconCls: 'siga-icon-16 icon-remove',
                    handler:function(){
                      var nodo = me.getCmp('usuario_perfil').getSelectionModel().getSelection();
                      nodo[0].removeAll();
                      nodo[0].remove(true);
                    }
                  }
                ]
              },
            ]
          }//fin container 2
        ]
      }
    ];


    me.callParent(arguments);
    me.setAccess(siga.getAccess("usuario"));

    me.internal.sort=[{property: 'usuario', direction: 'ASC'},{property: 'responsable', direction: 'ASC'},{property: 'activo', direction: 'ASC'}];

    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','usuario','responsable','activo'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/usuario/',
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
        dataIndex: 'usuario',
        text: '<b>Usuario</b>',
        width: '30%',
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
        dataIndex: 'responsable',
        text: '<b>Responsable</b>',
        width: '60%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
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
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
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
    //activar el evento change, para actualizar los campos de contraseña, segun la seleccion
    me.getCmp('clave_type').fireEvent('change');
  },

  onNew: function(){
    var me=this;
    me.getCmp('tabs').setActiveTab(0);
    me.getCmp('tab_data').getForm().reset();
    //limpiar el arbol de los perfiles de acceso (treepanal)
    me.getCmp('usuario_perfil').setRootNode({expanded: true, children: []});
    //recargar el listado
    me.onSearch();
  },

  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/usuario/',
      params:{
        action: 'onGet',
        id: _id
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        if(!result || result.length==0){
          alert("Error al cargar los datos.");
          return;
        }
        me.getCmp('id').setValue(result[0]['id']);
        me.getCmp('usuario').setValue(result[0]['usuario']);
        me.getCmp('id_persona_responsable').setValue(result[0]['id_persona_responsable']);
        me.getCmp('clave_type').setValue(result[0]['clave_type']);
        me.getCmp('activo').setValue(result[0]['activo']);
        me.getCmp('usuario_perfil').setRootNode({expanded: true, children: result[0]['perfil']});
        me.getCmp('usuario_perfil').expandAll();
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

    var _id_persona_responsable=Ext.String.trim(me.getCmp("id_persona_responsable").getValue());
    if(!_id_persona_responsable){
      me.setMessage("Error. El campo responsable se encuentra vacío.","red");
      return;
    }

    var _usuario=Ext.String.trim(me.getCmp("usuario").getValue());
    if(!_usuario){
      me.setMessage("Debe llenar el campo usuario.","red");
      return;
    }

    var _clave_value=Ext.String.trim(me.getCmp("clave_value").getValue());
    var _clave_value_re=Ext.String.trim(me.getCmp("clave_value_re").getValue());
    if(_clave_value!=_clave_value_re){
      me.setMessage("Las contraseñas no coinciden.","red");
      return;
    }

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
    //generar la estructura a guardar del perfil.
    var arbol=me.getCmp('usuario_perfil').getRootNode();
    var nodo=arbol.childNodes;
    var perfil_data=[];
    for(var k=0;k<nodo.length;k++){
      var acceso="";
      var hijos=nodo[k].childNodes;
      for(var j=0;j<hijos.length;j++)
        acceso+=hijos[j].data.text+((j<(hijos.length-1))?"\n":"");
      perfil_data[k]={anio: nodo[k].data.text, perfil: acceso, activo: (nodo[k].data.checked==true?'t':'f')};
    }

    me.getCmp('tab_data').submit({
      method: 'POST',
      url:'module/usuario/',
      params:{
        action: 'onSave',
        perfil_data: Ext.JSON.encode(perfil_data)
      },
      waitMsg: 'Guardando... por favor espere!',
      success: function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.onNew();
        me.setMessage(result.message,"green");
      },
      failure:function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.setMessage(result.message,"red");
        if(result.messageDB)
          console.log("["+me.id+"]\n"+result.message+"\n"+result.messageDB);
      }
    });
  },

  onDelete: function(){
    var me=this;
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    if(!_id) return;

    Ext.MessageBox.wait('Eliminando... por favor espere!');
      Ext.Ajax.request({
        method: 'POST',
        url:'module/usuario/',
        params:{
          action: 'onDelete',
          id: _id
        },
        success:function(request){
          Ext.MessageBox.hide();
          var result=Ext.JSON.decode(request.responseText);
          if(result.success){
            me.onNew();
            me.setMessage(result.message,"green");
          }
          else{
            me.setMessage(result.message,"red");
          }
        },
        failure:function(request){
          Ext.MessageBox.hide();
          var result=Ext.JSON.decode(request.responseText);
          me.setMessage(result.message,"red");
        }
      });
  },

  onCopy: function(){
    var me=this;
    me.internal.toCopy=[
      me.getCmp("nombre").getValue()
      ];
  },

  onPaste: function(){
    var me=this;
    me.getCmp("nombre").setValue(me.internal.toCopy[0]);
  },

});

