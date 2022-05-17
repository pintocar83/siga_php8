siga.define('academia_preinscribir', {
    extend: 'siga.windowFrame',
    title: 'Academia de Software Libre - Preinscribir',    
    width: 800,
    height: 600,
    bodyStyle: "background: white;",
    
    initComponent: function(){
        var me = this;
        
        me.setInternal({
           url: "public/academia/preinscribir.php"
        });
        
        me.callParent(arguments);
    }   
    
});