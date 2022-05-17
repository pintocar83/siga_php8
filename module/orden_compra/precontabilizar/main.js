siga.define('orden_compra/precontabilizar', {
    extend: 'siga.windowFrame',
    title: 'Orden de Compra (Pre-contabilizar)',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 300,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=orden_compra_precontabilizar"});
        me.callParent(arguments);
    }
});