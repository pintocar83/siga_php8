siga.define('comprobante', {
    extend: 'siga.windowFrame',
    title: 'Comprobante Presupuestario/Contable',    
    width: 850,
    height: 570,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=comprobante"});
        me.callParent(arguments);
    }
});