siga.require("item");

siga.define('materiales', {
  extend: 'item',
  title: "Materiales y/o Suministros",
  
  initComponent: function(){
    var me = this;
    me.callParent(arguments);
    me.setAccess(siga.getAccess('materiales'));
    me.internal.id_item_tipo="1";
    me.getCmp("id_cuenta_presupuestaria").internal.actionOnList="onList_402_4010710_4010726";
    me.onGetCodigo();
  },
  
  onHelp: function(){
    siga.help("help/?id=materiales");
  }
});


