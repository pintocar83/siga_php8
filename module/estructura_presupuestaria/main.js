siga.define('estructura_presupuestaria', {
    extend: 'siga.windowFrame',
    title: 'Estructura Presupuestaria',
    width: 890,
    height: 480,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=estructura_presupuestaria"});
        me.callParent(arguments);
    }
});