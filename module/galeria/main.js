siga.define('galeria', {
  extend: 'siga.windowFrame',
  title: 'Galeria Multimedia',
  maximizable: true,
  width: 920,
  height: 680,
  
  initComponent: function(){
    var me = this;
    me.setInternal({url: "module/galeria/core/index.php"});
    me.callParent(arguments);
  },
  
  init: function(){
    var me=this;
    me.maximize();
  }
});