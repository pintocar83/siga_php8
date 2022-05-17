/**
 *  Definición de la interfaz 'Sistema de Asistencia - Reportes PDF'.
 *
 *  La interfaz permite visualizar los siguientes reportes en formato PDF:
 *    -Listado mensual de trabajadores con hora de entrada y salida.
 *    -Listado mensual de trabajadores con hora de entrada, salida e indicadores.
 *    -Detalle mensual por trabajador.
 *  Llamado:
 *    siga.open("modulo_asistencia/reportes_pdf")
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
siga.define('reporte_asistencia', {
    extend: 'siga.windowForm',
    title: 'Sistema de Asistencia - Reportes PDF',
    width: 600,
    height: 400,    
    
    initComponent: function(){
        var me = this;
        
        me.itemsToolbar=[
            {
                xtype: 'button',
                id: me._('btnVisualizar'),
                width: 55,
                height: 45,
                text: 'Visualizar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-pdf',
                iconAlign: 'top',
                tooltip: 'Visualizar en PDF',
                listeners: {
                    click: function(){
                        var id_ficha=-1;
                        if(me.getCmp("id_ficha").getValue()>0)
                            id_ficha=me.getCmp("id_ficha").getValue();
                        
                        if(me.getCmp("reporteA").getValue()){
                            window.open("report/asistencia_a.php?anio="+me.getCmp("data").getValue()+"&mes="+me.getCmp("mes").getValue()+"&id_ficha="+id_ficha);
                        }
                        if(me.getCmp("reporteB").getValue()){
                            window.open("report/asistencia_b.php?anio="+me.getCmp("data").getValue()+"&mes="+me.getCmp("mes").getValue()+"&id_ficha="+id_ficha);
                        }
                        if(me.getCmp("reporteC").getValue()){
                            window.open("report/asistencia_c.php?anio="+me.getCmp("data").getValue()+"&mes="+me.getCmp("mes").getValue()+"&id_ficha="+id_ficha);
                        }                        
                    }
                }
            }
        ];
        
        me.items=[
            {
                xtype: "container",
                anchor: "100%",
                layout: "hbox",
                defaults: me.getInternal("field_defaults"),
                items:[
                    {
                        xtype: 'combobox',
                        id: me._('data'),
                        width: "50%",
                        style:'margin: 5px 20px 0px 0px;',                        
                        fieldLabel: 'Datos', 
                        isFormField:false,
                        fieldCls: 'reportes_pdf-input-data',                    
                        store: {
                            fields: ['id', 'nombre'],
                            data : siga.value("datos")
                        },
                        displayField: 'nombre',
                        valueField: 'id',
                        allowBlank: false,
                        forceSelection: true,
                        editable:false,
                        value: siga.value("anio")
                    },
                    {
                        xtype: 'combobox',
                        id: me._('mes'),
                        width: "50%",
                        style:'margin: 5px 0px 0px 20px;',                        
                        fieldLabel: 'Mes',
                        isFormField:false,
                        fieldCls: '',                    
                        store: {
                            fields: ['id', 'nombre'],
                            data : siga.value("meses")
                        },
                        displayField: 'nombre',
                        valueField: 'id',
                        allowBlank: false,
                        forceSelection: true,
                        editable:false,
                        value: siga.value("mes_actual")
                    }
                ]
            },
            {
                xtype:'siga.select',
                id: me._('id_ficha'),
                name: 'id_ficha',
                anchor: "100%",
                fieldLabel: 'Persona',
                value: '',
                internal:{
                  valueField: 'id',
                  columns: {field: ["identificacion","denominacion"], title: ["Identificación","Denominación"], width: ['25%','75%'], sort: ["ASC"]},
                  url: 'module/ficha/',
                  actionOnList:'onList',
                  actionOnGet:'onGet_Select',
                  output:0,
                  clearBtn: true
                }
            },
            {
                xtype: 'radiofield',
                boxLabel  : '<b>Listado mensual de trabajadores con hora de entrada y salida.</b>',
                id: me._('reporteA'),
                name: 'reporte',
                style:'margin: 25px 0px 0px 40px;',                
                inputValue: 'a',
                anchor: "100%",
                checked: false
            },
            {
                xtype: 'radiofield',
                boxLabel  : '<b>Listado mensual de trabajadores con hora de entrada, salida e indicadores.</b>',
                id: me._('reporteB'),
                name: 'reporte',     
                inputValue: 'b',
                anchor: "100%",
                checked: true
            },
            {
                xtype: 'radiofield',
                boxLabel  : '<b>Detalle mensual por trabajador.</b>',
                id: me._('reporteC'),
                name: 'reporte',
                inputValue: 'c',
                anchor: "100%",         
            }
        ];
        
        me.callParent(arguments);      
    }    
});