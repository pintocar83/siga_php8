siga.define('academia_reportes', {
    extend: 'siga.windowFrame',
    title: 'Academia de Software Libre - Reportes',
    maximizable: true,
    resizable: true,
    width: 920,
    height: 680,

    initComponent: function(){
        var me = this;
      
        me.setInternal({
            url: "module/academia_reportes/form.php"
        });      
      
        me.callParent(arguments);
    },
   
    init: function(){
        var me=this;
        me.maximize();
    }
    
});