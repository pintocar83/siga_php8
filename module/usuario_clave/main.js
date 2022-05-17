siga.define('usuario_clave', {
  extend: 'siga.windowFrame',
  title: 'Cambiar Contraseña',
    width: 500,
    height: 300,
  
  initComponent: function(){
    var me = this;
    me.setInternal({url: "module/usuario_clave/form.php"});
    me.callParent(arguments);
  }
});