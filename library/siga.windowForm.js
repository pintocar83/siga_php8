Ext.define('siga.windowForm',{
    extend: 'siga.windowBase',
    
    initComponent: function(){
        var me = this;
        
        me.addItemMessage();        
        
        me.items=[
            {
                xtype: 'form',
                frame: false,
                id: me._('tab_data'),
                frameHeader: false,
                autoScroll:true,
                layout: 'anchor',
                defaults: me.getInternal("field_defaults"),
                items: me.items
            }
        ];
        
        me.callParent(arguments);
    }   
});