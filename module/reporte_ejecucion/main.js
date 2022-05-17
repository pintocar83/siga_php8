siga.define('reporte_ejecucion', {
    extend: 'siga.windowFrame',
    title: 'Presupuesto de Gastos - Ejecución', 
    width: 900,
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
        
        me.setInternal({url: "module/sigafs/?q=reporte_ejecucion"});
        me.callParent(arguments);      
    }
});