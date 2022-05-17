siga.define('reporte_retencion', {
    extend: 'siga.windowFrame',
    title: 'Retenciones IVA / ISLR - Reportes',
    width: 650,
    height: 400,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=reporte_retencion"});
        me.callParent(arguments);
    } 
});