siga.define('requisicion_externa', {
    extend: 'siga.windowFrame',
    title: 'Requisición Externa (Materiales, Suministros y Servicios)',
    width: 800,
    height: 500,        
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=requisicion"});
      me.callParent(arguments);
    }
});