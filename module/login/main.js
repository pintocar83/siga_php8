siga.define('login', {
    extend: 'siga.windowBase',
    title: '<img src="favicon.svg" style="position: absolute; top:-3px; left:3px; width: 24px; height: 24px; cursor: pointer;" data-qtip="<b>Autor:</b> Carlos Pinto - Diseños y Sistemsa Pinto F.C." onclick=\"siga.dsp_author()\" /><div style="padding-left:30px;">Inicio de Sesión</div>',
    width: 600,
    height: 350,
    modal: true,
    minimizable: false,
    renderTo: Ext.getBody(),
    layout: 'vbox',
    bodyStyle: {
      "background-image": "url("+(localStorage.getItem("login/database")?"data/"+localStorage.getItem("login/database")+"/config":siga.value('folder'))+"/login-bg.png)",
      "padding": "20px 20px 0px 200px",
      "background-repeat": "no-repeat",
      "background-position": "left top",
      "border-color": "#e8e8e8"
    },

    initComponent: function(){
        var me = this;

        me.defaults=me.getInternal("field_defaults");

        me.items=[
            {
                xtype: 'label',
                html: siga.value('title_login'),
                style: "padding: 0px;",
                width: 300,
                cls: 'login-label-welcome'
            },
            {
                xtype: 'label',
                id: me._('message'),
                style: "padding-top: 5px;",
                cls:'login-label-message',
                html: "&nbsp;",
                width: 300,
            },
            {
                xtype: 'textfield',
                id: me._('username'),
                fieldLabel: 'Usuario',
                value: '',
                cls: 'login-input-base login-input-user',
                //fieldCls: 'login-input-base login-input-user',
                width: 300,
                listeners:{
                    specialkey: function(field, e){
                        if (e.getKey() == e.ENTER){
                            me.getCmp('password').focus();
                            me.getCmp('password').selectText();
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                id: me._('password'),
                fieldLabel: 'Contraseña',
                value: '',
                inputType: 'password' ,
                width: 300,
                cls: 'login-input-base login-input-password',
                //fieldCls: 'login-input-base login-input-password',
                listeners:{
                    specialkey: function(field, e){
                        if (e.getKey() == e.ENTER)
                            me.login();
                    }
              }
            },
            {
                xtype: 'fieldcontainer',
                width: 300,
                fieldLabel: 'Base de Datos',
                layout: 'hbox',


                items:[
                    {
                        xtype: 'combobox',
                        id: me._('database'),
                        width: 205,
                        cls: 'login-input-base login-input-data',
                        //fieldCls: 'login-input-base login-input-data',
                        queryMode: "local",
                        store: {
                          fields: ['id','description','data'],
                          autoLoad: true,
                          proxy: {
                            type:'ajax',
                            url: 'module/login/',
                            actionMethods: {read: "POST"},
                            timeout: 3600000,
                            reader: {
                              type: 'json',
                              rootProperty: 'result',
                              totalProperty:'total'
                            },
                            extraParams: {
                              action: 'onDBAvailable'
                            }
                          },
                          listeners: {
                            load: function(store, records, successful){
                                if(records.length>0){
                                    var _database = localStorage.getItem("login/database");
                                    for(var i=0; i<records.length && _database; i++){
                                        if(_database === records[i].get("id")){
                                            me.getCmp("database").setValue(_database);
                                            return;
                                        }
                                    }

                                    me.getCmp("database").setValue(records[0].get("id"));
                                }
                            }
                          }
                        },
                        editable: false,
                        displayField: 'description',
                        valueField: 'id',
                        allowBlank: false,
                        forceSelection: true,
                        value: '',
                        listeners: {
                            change: function(e, The, eOpts ){
                                me.getCmp('data').setValue("");
                                var data=me.getCmp("database").getStore().getById(me.getCmp("database").getValue()).get("data");
                                me.getCmp('data').setStore({fields: ['id', 'nombre'], data: data});

                                me.body.applyStyles({"background-image": "url(data/"+me.getCmp("database").getValue()+"/config/login-bg.png)"});

                                if (data.length>0){
                                    var _data = localStorage.getItem("login/data");
                                    for(var i=0; i<data.length && _data; i++){
                                        if(_data === data[i]["id"]){
                                            me.getCmp("data").setValue(_data);
                                            return;
                                        }
                                    }
                                    me.getCmp('data').setValue(data[data.length-1]["id"]);
                                }

                            }
                        }
                    },
                    {
                        xtype: "tbspacer",
                        width: 10
                    },
                    {
                        xtype: 'combobox',
                        id: me._('data'),
                        cls: 'login-input-data-year',
                        width: 85,
                        editable: false,
                        displayField: 'nombre',
                        valueField: 'id',
                        allowBlank: false,
                        forceSelection: true,
                        value: ''
                    },
                ]


            },

            {
                xtype: 'container',
                width: 300,
                style: 'padding-top: 30px;',
                layout: 'hbox',
                items:[
                    {
                        xtype:'tbspacer',
                        flex:1
                    },
                    {
                        xtype: 'button',
                        text: 'Limpiar',
                        tooltip: 'Limpiar',
                        iconCls: 'siga-icon-16 icon-clear',
                        width: 100,
                        listeners: {
                            click: function(){
                                me.clear();
                            }
                        }
                    },
                    {
                        xtype:'tbspacer',
                        flex:0,
                        width: 20,
                    },
                    {
                        xtype: 'button',
                        text: 'Iniciar Sesión',
                        tooltip: 'Iniciar Sesión',
                        iconCls: 'siga-icon-16 icon-accept',
                        width: 100,
                        listeners: {
                            click: function(){
                                me.login();
                            }
                        }
                    },
                    {
                        xtype:'tbspacer',
                        flex:1
                    }
                ]
            }
        ];

        me.callParent(arguments);
    },

    init: function(){
        var me=this;
        me.getCmp('username').focus();
        me.getCmp('username').selectText();
    },

    login: function(){
        var me=this;

        var _username=me.getCmp('username').getValue();
        var _password=me.getCmp('password').getValue();
        var _database=me.getCmp('database').getValue();
        var _data=me.getCmp('data').getValue();

        if(!_username){
            me.setMessage("Introduzca el nombre de usuario.",'red');
            return;
        }

        if(!_password){
            me.setMessage("Introduzca la contraseña de usuario.",'red');
            return;
        }

        Ext.Ajax.request({
            url: 'module/login/',
            method : 'POST',
            params: {
              action: 'onLogin',
              username: _username,
              password: _password,
              database: _database,
              data: _data
            },
            success: function(response){
              var result=Ext.JSON.decode(response.responseText);
              if(result.success){
                localStorage.setItem("login/database",_database);
                localStorage.setItem("login/data",_data);
                window.location.reload();
              }

              me.getCmp('username').focus();
              me.getCmp('username').selectText();
              me.setMessage(result.message,'red');
            }
        });
    },

    clear: function(){
        var me=this;
        me.setMessage("");
        me.getCmp('username').setValue("");
        me.getCmp('password').setValue("");
        me.getCmp('username').focus();
    },

    onHelp: function(){
        siga.help("help/?id=inicio");
    }
});