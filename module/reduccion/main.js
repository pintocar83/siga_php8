siga.define('reduccion', {
    extend: 'siga.windowFrame',
    title: 'Modificación Presupuestaria - Reducción',
    width: 850,
    height: 580,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=reduccion"});
        me.callParent(arguments);
    }
});