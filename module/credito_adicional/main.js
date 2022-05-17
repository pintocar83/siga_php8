siga.define('credito_adicional', {
    extend: 'siga.windowFrame',
    title: 'Modificación Presupuestaria - Crédito Adicional',
    width: 850,
    height: 580,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=credito_adicional"});
        me.callParent(arguments);      
    }    
});