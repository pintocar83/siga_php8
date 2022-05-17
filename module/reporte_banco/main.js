/**
 *  Definición de la interfaz 'Banco - Reportes'.
 *
 *  La interfaz permite visualizar los siguientes reportes en formato PDF:
 *    -Estado de cuenta.
 *    
 *  Llamado:
 *    siga.open("modulo_banco/reportes")
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2015, FUNDACITE Sucre 
 *  
 *  @version 2015.01.29
 */
siga.define('reporte_banco', {
    extend: 'siga.windowForm',
    title: 'Banco - Reportes',
    width: 650,
    height: 380,

    initComponent: function(){
        var me = this;
        
        me.itemsToolbar=[
            me.btnPDF()
        ];
        
        me.items=[
            {
                xtype: 'radiofield',
                boxLabel  : '<b>Estado de cuenta.</b>',
                id: me._('reporteA'),
                name: 'reporte',
                style:'margin: 20px 0px 0px 40px;',                
                inputValue: 'a',
                anchor: "100%",
                checked: true
            },
            {
                xtype: "container",        
                anchor: "100%",
                layout: "hbox",
                margin: 0,
                defaults: me.getInternal("field_defaults"), 
                items:[
                    {
                        xtype:'datefield',
                        id: me._('fecha_inicio'),
                        name: 'fecha_inicio',
                        fieldLabel: 'Desde',
                        submitFormat: 'Y-m-d',
                        value: '',
                        width: "50%",
                        style:'margin: 5px 20px 0px 0px;',
                    },
                    {
                        xtype:'datefield',
                        id: me._('fecha_culminacion'),
                        name: 'fecha_culminacion',
                        fieldLabel: 'Hasta',
                        submitFormat: 'Y-m-d',
                        value: '',
                        width: "50%",
                        style:'margin: 5px 0px 0px 20px;',
                    }
                ]      
            },
            {
                xtype:'siga.select',
                id: me._('id_banco_cuenta'),
                name: 'id_banco_cuenta',
                anchor: "100%",
                fieldLabel: 'Cuenta Bancaria',
                value: '',
                internal:{
                    width: 920,
                    height: 400,
                    valueField: 'id',
                    
                    columns: {
                      field: ["numero_cuenta","denominacion","cuenta_contable","denominacion_contable","banco"],
                      title: ["Número","Descripción","Cuenta Contable","Denominación"],
                      width: ['15%','36%','13%','36%'],
                      sort: ["","ASC","",""]
                    },
                    gridList:{
                      features:[{
                        ftype: 'grouping',
                        groupHeaderTpl: '{name}',
                        collapsible : false,
                      }],
                      groupField: 'banco'
                    },
                    url: 'module/banco_cuenta/',
                    actionOnList: 'onList',
                    actionOnGet: 'onGet',
                    
                    output:2,
                    clearBtn: true
                }
            },
            
        ];
        
        me.callParent(arguments);      
    },
    
    init: function(){
        var me=this;
        if(me.parameter){
            if(me.parameter.id_banco_cuenta)
                me.getCmp("id_banco_cuenta").setValue(me.parameter.id_banco_cuenta);
            if(me.parameter.fecha_inicio)
                me.getCmp("fecha_inicio").setValue(me.parameter.fecha_inicio);
            if(me.parameter.fecha_culminacion)
                me.getCmp("fecha_culminacion").setValue(me.parameter.fecha_culminacion);
        }
    },
    
    onPDF: function(){
        var me=this;
        var id_banco_cuenta=-1;
        if(me.getCmp("id_banco_cuenta").getValue()>0)
            id_banco_cuenta=me.getCmp("id_banco_cuenta").getValue();
        else{
            me.setMessage("Debe seleccionar la cuenta bancaria.","red");
            return;
        }

        if(me.getCmp("reporteA").getValue()){
            window.open("report/estado_cuenta.php?fecha_inicio="+me.getCmp("fecha_inicio").getRawValue()+"&fecha_culminacion="+me.getCmp("fecha_culminacion").getRawValue()+"&id_banco_cuenta="+id_banco_cuenta);
        }
    }
    
});