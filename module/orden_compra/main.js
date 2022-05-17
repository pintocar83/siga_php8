siga.define('orden_compra', {
    extend: 'siga.windowFrame',
    title: 'Orden de Compra',
    width: 850,
    height: 570,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=orden_compra&sw=OC"});        
        me.callParent(arguments);
    }
});