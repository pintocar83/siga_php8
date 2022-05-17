siga.define('traspaso', {
    extend: 'siga.windowFrame',
    title: 'Modificación Presupuestaria - Traspasos',
    width: 850,
    height: 580,

    initComponent: function(){
        var me = this;
        me.setInternal({url: "module/sigafs/?q=traspaso"});
        me.callParent(arguments);
    }
});