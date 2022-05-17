siga.require("item");

siga.define('servicios', {
  extend: 'item',
  title: "Servicios",
  
  initComponent: function(){
    var me = this;
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess('servicios'));
    me.internal.id_item_tipo="3";
    me.getCmp("id_cuenta_presupuestaria").internal.actionOnList="onList_403";
    me.onGetCodigo();
  },
  
  onHelp: function(){
    siga.help("help/?id=servicios");
  }
});