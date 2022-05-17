siga.define('cuenta_presupuestaria', {
    extend: 'siga.windowFrame',
    title: 'Plan de Único de Cuentas',
    width: 700,
    height: 470,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=cuenta_presupuestaria"});
        me.callParent(arguments);
    }
});