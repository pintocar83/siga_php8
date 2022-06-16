siga.define('retencion', {
    extend: 'siga.windowFrame',
    title: 'Retenciones',
    width: 800,
    height: 440,

    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=retencion"});
      me.callParent(arguments);
    }
});