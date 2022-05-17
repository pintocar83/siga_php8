siga.require("item");

siga.define('bienes', {
  extend: 'item',
  title: "Bienes",
  
  initComponent: function(){
    var me = this;
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess('bienes'));
    me.internal.id_item_tipo="2";
    me.getCmp("id_cuenta_presupuestaria").internal.actionOnList="onList_404";
    me.onGetCodigo();
  },
  
  onHelp: function(){
    siga.help("help/?id=bienes");
  }
});