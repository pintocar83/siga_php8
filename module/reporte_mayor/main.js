siga.define('reporte_mayor', {
    extend: 'siga.windowFrame',
    title: 'Presupuesto de Gastos - Mayor Analítico',    
    width: 650,
    height: 400,
    
    initComponent: function(){
        var me = this;        
        me.setInternal({url: "module/sigafs/?q=reporte_mayor"});
        me.callParent(arguments);      
    }  
});