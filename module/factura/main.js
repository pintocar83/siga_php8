siga.define('factura', {
    extend: 'siga.windowFrame',
    title: 'Factura',
    width: 700,
    height: 500,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=factura"});
        me.callParent(arguments);
    }
});