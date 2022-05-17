siga.define('orden_pago', {
    extend: 'siga.windowFrame',
    title: 'Orden de Pago',
    width: 850,
    height: 580,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=orden_pago"});
      me.callParent(arguments);      
    }    
});