siga.define('proveedor', {
    extend: 'siga.windowFrame',
    title: 'Personas Jurídicas / Proveedores',
    width: 700,
    height: 450,
    
    initComponent: function(){
      var me = this;
      me.setInternal({url: "module/sigafs/?q=proveedor"});
      me.callParent(arguments);
    },
    
    onHelp: function(){
        siga.help("help/?id=proveedores");
    }
});