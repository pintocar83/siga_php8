siga.define('cuenta_contable', {
    extend: 'siga.windowFrame',
    title: 'Plan de Cuentas Contables',
    width: 700,
    height: 470,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=cuenta_contable"});
        me.callParent(arguments);
    }
});