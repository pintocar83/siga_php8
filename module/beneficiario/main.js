siga.define('beneficiario', {
    extend: 'siga.windowFrame',
    title: 'Personas Naturales / Beneficiarios',
    width: 700,
    height: 450,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=beneficiario"});
      me.callParent(arguments);      
    },
    
    onHelp: function(){
        siga.help("help/?id=beneficiarios");
    }
});