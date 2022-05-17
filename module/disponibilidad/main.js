siga.define('disponibilidad', {
    extend: 'siga.windowSelect',
    title: 'Disponibilidad Presupuestaria',
    modal: false,
    width: 800,
    height: 500,
    initComponent: function(){
        var me = this;
        
        var _id_cuenta_presupuestaria="";
        var _id_accion_subespecifica="";
        if(me.parameter){
            if(me.parameter.id_cuenta_presupuestaria) 
                _id_cuenta_presupuestaria=me.parameter.id_cuenta_presupuestaria;
            if(me.parameter.id_accion_subespecifica) 
                _id_accion_subespecifica=me.parameter.id_accion_subespecifica;
            if(me.parameter.modal){
                me.modal=true;
                me.renderTo=null;
                me.constrainTo=null;
            }
        }
        
        me.internal.parent={
            fieldLabel: me.title,                                                        
            internal:{
                page:1,
                limit: 1000,
                valueField: 'id_cuenta_presupuestaria',
                columns: {
                    field: ["cuenta_presupuestaria","denominacion","disponibilidad","estructura_presupuestaria","id_accion_subespecifica"],
                    title: ["Cuenta","Denominación","Disponibilidad"],
                    width: ['12%','75%','13%'],
                    sort:  ["ASC","NULL","NULL"],
                    align: ["left","left","right"]
                },
                url: 'module/detalle_presupuestario/',
                extraParams:{
                    id_cuenta_presupuestaria: _id_cuenta_presupuestaria,
                    id_accion_subespecifica: _id_accion_subespecifica
                  },
                gridList:{
                    features:[{
                        ftype: 'grouping',
                        groupHeaderTpl: '{name}',
                        collapsible : false,
                    }],
                    groupField: 'estructura_presupuestaria'
                },
                actionOnList: 'onList_CuentaPresupuestariaDisponibilidad',
                onBeforeAccept: function(dataview, record, item, index, e){
                    if(me.parameter)
                        if(me.parameter.onAccept)
                            return me.parameter.onAccept(me, dataview, record, item, index, e);
                    return false;
                }
            },
            setValue: function(v){}
        };
        me.callParent(arguments);
    }
});
