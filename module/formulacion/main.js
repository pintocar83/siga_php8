siga.define('formulacion', {
    extend: 'siga.windowFrame',
    title: 'Formulación del Presupuesto de Gastos',
    width: 1000,
    height: 640,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=formulacion"});
        me.callParent(arguments);
    }
});