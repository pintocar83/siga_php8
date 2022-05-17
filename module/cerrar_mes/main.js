siga.define('cerrar_mes', {
    extend: 'siga.windowFrame',
    title: 'Cierre de Meses',
    width: 550,
    height: 355,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=cerrar_mes"});
        me.callParent(arguments);
    }
});