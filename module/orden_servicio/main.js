siga.define('orden_servicio', {
    extend: 'siga.windowFrame',
    title: 'Orden de Servicio',
    width: 850,
    height: 590,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=orden_compra&sw=OS"});        
        me.callParent(arguments);      
    }
});