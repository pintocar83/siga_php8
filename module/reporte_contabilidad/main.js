siga.define('reporte_contabilidad', {
    extend: 'siga.windowFrame',
    title: 'Contabilidad - Reportes',
    width: 700,
    height: 410,
    
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
        
        me.setInternal({url: "module/sigafs/?q=reporte_contabilidad"});
        me.callParent(arguments);
    }
});