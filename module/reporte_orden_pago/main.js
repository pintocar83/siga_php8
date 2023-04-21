siga.define('reporte_orden_pago', {
    extend: 'siga.windowFrame',
    title: 'Ordenes de Pago - Reportes',
    width: 600,
    height: 350,
    
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
        
        me.setInternal({url: "module/sigafs/?q=reporte_orden_pago"});
        me.callParent(arguments);
    }
});