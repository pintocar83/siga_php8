siga.define('requisicion_externa/listado', {
    extend: 'siga.windowFrame',
    title: 'Listado de Requisiciones Externas (Materiales, Suministros y Servicios)',
    renderTo: Ext.getBody(),
    modal: true,
    width: 700,
    height: 360,
    initComponent: function(){
        var me = this;        
        var id_comprobante="";
        var tipo="";
        if(me.parameter.id_comprobante)
          id_comprobante=me.parameter.id_comprobante;
        if(me.parameter.tipo)
          tipo=me.parameter.tipo;
        
        me.setInternal({url: "module/sigafs/?q=requisicion_listado&tipo="+tipo+"&id_comprobante="+id_comprobante});
        me.callParent(arguments);
    }
});