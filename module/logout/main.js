siga.define('logout', {
    extend: 'siga.windowBase',
    title: '<img src="favicon.svg" style="position: absolute; top:-3px; left:3px; width: 24px; height: 24px; cursor: pointer;" data-qtip="<b>Autor:</b> Carlos Pinto - Diseños y Sistemsa Pinto F.C." /><div style="padding-left:30px;">Cerrar Sesión</div>',
    width: 440,
    height: 210,
    modal: true,
    minimizable: false,
    renderTo: Ext.getBody(),
    layout: 'vbox',
    bodyStyle: {
      "background-image": "url(module/logout/image/logout-bg.png)",
      //"background-image": "url("+siga.value('folder')+"/logout-bg.png)",
      "padding": "0px 0px 0px 0px",
      "background-repeat": "no-repeat",
      "background-position": "left top",
      "border-color": "#e8e8e8"
    },

    initComponent: function(){
        var me = this;

        me.setInternal({
            timeOut: 0,
            timeOutHandler: null
        });

        me.items=[
            {
                xtype: 'label',
                html: siga.value('title_logout'),
                cls: 'logout-label-welcome',
                width: '100%'
            },
            {
                xtype: 'label',
                cls:'logout-label-question',
                text: "¿Desea cerrar de la sesión actual?",
                width: '100%'
            },
            {
                xtype: 'label',
                id: me._('message'),
                cls:'logout-label-close',
                text: "",
                width: '100%'
            },
            {
                xtype: 'container',
                style: 'margin-top: 20px;',
                width: '100%',
                layout: 'hbox',
                items:[
                    {
                        xtype:'tbspacer',
                        flex:1
                    },
                    {
                        xtype: 'button',
                        text: 'Cancelar',
                        tooltip: 'Cancelar',
                        iconCls: 'icon-close',
                        disabledCls: 'icon-close-disabled',
                        width: 100,
                        listeners: {
                            click: function(){
                                me.close();
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
                        text: 'Cerrar Sesión',
                        tooltip: 'Cerrar Sesión',
                        iconCls: 'siga-icon-16 icon-accept',
                        width: 100,
                        listeners: {
                            click: function(){
                                me.closeSession();
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
        me.setInternal({ timeOut: 30 });
        me.autoCloseSession();
    },

    listeners:{
        close:function(){
            var me=this;
            me.setInternal({ timeOut: -1 });
            window.clearTimeout(me.getInternal("timeOutHandler"));
        }
    },

    autoCloseSession: function(){
        var me=this;
        if(me.getInternal("timeOut")==-1) return;
        me.getCmp('message').setText('La sesión se cerrará automaticamente en '+me.getInternal("timeOut")+' segundos.',false);

        me.setInternal({
            timeOutHandler: setTimeout(function(){
                me.setInternal({ timeOut: me.getInternal("timeOut")-1 });

                if(me.getInternal("timeOut")<=0){
                    me.closeSession();
                    return;
                }
            me.autoCloseSession();
            },1000)
        });
    },

    closeSession: function(){
        var me=this;
        Ext.Ajax.request({
            url: 'module/logout/',
            method : 'POST',
            params: {
                action: 'logout'
            },
            success: function(response){
                var result=Ext.JSON.decode(response.responseText);
                if(result.success){
                    window.location.reload();
                    return;
                }
                me.setMessage(result.message,'red');
            }
        });
    }
});