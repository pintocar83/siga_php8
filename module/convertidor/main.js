siga.define('convertidor', {
    extend: 'siga.windowFrame',
    title: 'Convertidor General de Cuentas',
    width: 940,
    height: 570,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=convertidor"});
        me.callParent(arguments);
    }
});