siga.define('factura', {
    extend: 'siga.windowFrame',
    title: 'Factura',
    width: 900,
    height: 520,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=factura"});
        me.callParent(arguments);
    }
});