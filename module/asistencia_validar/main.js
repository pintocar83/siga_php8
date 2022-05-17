siga.define('asistencia_validar', {
    extend: 'siga.windowFrame',
    title: 'Sistema de Asistencia - Validar',
    //maximizable: true,
    resizable: true,    
    width: 550,
    height: 600,
    
    initComponent: function(){
      var me = this;
    
      me.setInternal({
        url: "module/asistencia_validar/form.php"
      });
      
      me.callParent(arguments);      
    },  
    
    init: function(){
      var me=this;
      //me.maximize();
    }    
});