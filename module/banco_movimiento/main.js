siga.define('banco_movimiento', {
    extend: 'siga.windowFrame',
    title: 'Banco - Movimientos',
    width: 850,
    height: 580,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=banco_movimiento"});      
      me.callParent(arguments);      
    }
});