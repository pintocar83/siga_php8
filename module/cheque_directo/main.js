siga.define('cheque_directo', {
    extend: 'siga.windowFrame',
    title: 'Banco - Cheque (directo)',
    width: 850,
    height: 570,
    
    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=cheque_directo"});
        me.callParent(arguments);
    }  
});