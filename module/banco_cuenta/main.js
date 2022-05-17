siga.define('banco_cuenta', {
    extend: 'siga.windowFrame',
    title: 'Banco - Cuenta',
    width: 800,
    height: 480,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=banco_cuenta"});      
      me.callParent(arguments);      
    }
});