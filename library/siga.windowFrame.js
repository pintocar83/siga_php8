Ext.define('siga.windowFrame',{
    extend: 'siga.windowBase',
    
    initComponent: function(){
        var me = this;
        
        me.items=[
            {
                xtype : "component",
                id: me._("frame"),
                flex: 1,
                autoEl : {
                    tag : "iframe",
                    src : me.getInternal("url"),
                    style: "border: none;"
                }
            }
        ];       
        me.callParent(arguments);
    }   
});







