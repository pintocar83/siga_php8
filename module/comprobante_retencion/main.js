siga.define('comprobante_retencion', {
    extend: 'siga.windowFrame',
    title: 'Comprobante de Retención',
    width: 800,
    height: 550,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=comprobante_retencion"});
      me.callParent(arguments);      
    }
});