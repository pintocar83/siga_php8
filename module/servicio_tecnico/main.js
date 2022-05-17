siga.define('servicio_tecnico', {
  extend: 'siga.windowFrame',
  title: 'Servicio Técnico',
  width: 920,
  height: 680,
  
  initComponent: function(){
    var me = this;
    me.setInternal({url: "module/servicio_tecnico/core/index.php"});      
    me.callParent(arguments);      
  },
  
  init: function(){
    var me=this;
    me.maximize();
  }
});