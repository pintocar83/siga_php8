siga.define('comprobante/cargo', {
    extend: 'siga.windowFrame',
    title: 'Cargos',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 360,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=comprobante_cargo"});
        me.callParent(arguments);      
    } 
});