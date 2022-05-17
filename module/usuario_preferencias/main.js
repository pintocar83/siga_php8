siga.define('usuario_preferencias', {
    extend: 'siga.windowForm',
    title: 'Preferencias de Usuario',
    width: 560,
    height: 330,
    
    initComponent: function(){
        var me = this;
        
        me.items=[
            {
                xtype: "dataview",
                id: me._("vista"),
                margin: '10 0 0 20',
                tpl: [
                    '<tpl for=".">',
                        '<div class="usuario_preferencias-thumb">',
                        '<div><img src="{file}"></div>',
                        '</div>',
                    '</tpl>',
                ],
                flex: 1,
                multiSelect: false,
                border: 1,
                
                store: {
                    fields: ['file'],
                    data : [
                        {file: 'image/background/1.jpg'},
                        {file: 'image/background/2.jpg'},
                        {file: 'image/background/3.jpg'},
                        {file: 'image/background/4.jpg'},
                        {file: 'image/background/5.jpg'},
                        {file: 'image/background/6.jpg'},
                    ]
                },
                
                itemSelector: 'div.usuario_preferencias-thumb',
                trackOver: true,
                listeners:{
                    itemclick: function(el, record, item, index, e, eOpts){
                        var file=record.get("file");
                        Ext.getBody().setStyle({'background-image': "url("+file+")"});
                        
                        Ext.Ajax.request({
                            method: 'POST',
                            url:'module/usuario_preferencias/',
                            params:{
                                action: 'onSave',
                                background: file
                            },
                            success: function(request){
                            },
                            failure:function(request){
                            }
                        });
                    }
                }
            }
        ];
        
        me.callParent(arguments);
    }
});