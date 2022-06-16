siga.define('cargo', {
    extend: 'siga.windowFrame',
    title: 'Cargos / Impuestos',
    width: 800,
    height: 515,

    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=cargo"});
      me.callParent(arguments);
    }
});