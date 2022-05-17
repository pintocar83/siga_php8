siga.define('login', {
    extend: 'siga.windowBase',
    title: 'Inicio de Sesión',
    width: 600,
    height: 350,
    modal: true,
    minimizable: false,
    renderTo: Ext.getBody(),
    layout: 'vbox',
    bodyStyle: {
      "background-image": "url("+siga.value('folder')+"/login-bg.png)",
      "padding": "20px 20px 0px 200px",
      "background-repeat": "no-repeat",
      "background-position": "left top"
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
                fieldCls: 'login-input-base login-input-user',
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
                fieldCls: 'login-input-base login-input-password',
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
                        fieldCls: 'login-input-base login-input-data',                    
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
                                if (data.length>0) 
                                    me.getCmp('data').setValue(data[data.length-1]["id"]);
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
    }    
});