siga.define('cheque_orden_pago', {
    extend: 'siga.windowFrame',
    title: 'Banco - Cheque (orden de pago)',
    width: 850,
    height: 550,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=cheque_orden_pago"});
        me.callParent(arguments);
    } 
});