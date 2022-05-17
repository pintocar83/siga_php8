siga.define('comprobante_previo_listado', {
    extend: 'siga.windowFrame',
    title: 'Listado de Compromisos',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 360,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=comprobante_previo"});
        me.callParent(arguments);      
    }
});