siga.define('reporte_formulacion', {
    extend: 'siga.windowFrame',
    title: 'Formulación del Presupuesto de Gastos - Reportes',    
    width: 850,
    height: 530,
    
    initComponent: function(){
      var me = this;
      if(me.parameter) {
        if(me.parameter.modal) {
            me.modal=me.parameter.modal;
            if(me.parameter.modal==true) {
                me.minimizable=false;
                me.renderTo=Ext.getBody();
            }
        }
      }
      
      me.setInternal({url: "module/sigafs/?q=reporte_formulacion"});
      me.callParent(arguments);      
    } 
});