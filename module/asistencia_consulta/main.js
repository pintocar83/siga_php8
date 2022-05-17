/**
 *  Definición de la interfaz 'Sistema de Asistencia - Consultar'.
 *
 *  La interfaz permite visualizar el personal activo en el sistema de asistencia,
 *  así como tambien sus registros de entradas y salidas.
 *  Además permite ingresar las horas de forma manual y notas en días específicos,
 *  para este último es necesario que el usuario tenga acceso de escritura a:
 *    modulo_asistencia/ingresar_hora y
 *    modulo_asistencia/ingresar_nota.
 *  La interfaz esta compuesta de una barra de herramientas y un iframe que carga el archivo
 *  modulo_asistencia/reportes/asistencia_mensual.php que contiene la información mostrada.
 *  Llamado: 
 *    siga.open("modulo_asistencia/reportes")
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
siga.define('asistencia_consulta', {
    extend: 'siga.windowBase',
    title: 'Sistema de Asistencia - Consultar',
    maximizable: true,
    resizable: true,
    width: 920,
    height: 680,
    
    initComponent: function(){
        var me = this;          
    
        me.itemsToolbar=[
            {
                xtype: 'button',
                id: me._('btnAsistencia'),                    
                width: 55,
                height: 45,
                text: 'Registrar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-asistencia-clasico',
                iconAlign: 'top',
                tooltip: 'Registrar Asistencia',
                
                listeners: {
                    click: function(){
                        siga.open("asistencia_registro");
                    }
                }
            }, 
            {
                xtype: 'button',
                id: me._('btnValidar'),                    
                width: 55,
                height: 45,
                text: 'Validar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-asistencia-validar',
                iconAlign: 'top',
                tooltip: 'Validar Registros de Asistencia',
                
                listeners: {
                    click: function(){
                        siga.open("asistencia_validar");
                    }
                }
            },            
            {
                xtype: 'button',
                id: me._('btnReload'),
                width: 55,
                height: 45,
                text: 'Recargar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-reload',
                iconAlign: 'top',
                tooltip: 'Recargar Página',
                listeners: {
                    click: function(){
                      me.reload();
                    }
                }
            },
            {
                xtype: 'button',
                id: me._('btnPDF'),
                width: 55,
                height: 45,
                text: 'Visualizar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-pdf',
                iconAlign: 'top',
                tooltip: 'Visualizar en PDF',
                listeners: {
                    click: function(){
                      siga.open("reporte_asistencia");
                    }
                }
            }
        ];
        
        me.items=[
            {
                xtype : "component",
                id: me._("frame"),
                flex: 1,
                autoEl : {
                    tag : "iframe",
                    src : "module/asistencia_consulta/form.php",
                    style: "border: none;"
                }
            }                
        ];

        me.callParent(arguments);      
    },
    
    init: function(){
        var me=this;
        me.maximize();
    },
    
    reload: function(){
        var me=this;
        var url=Ext.get(me._("frame")).dom.contentDocument.URL;
        Ext.get(me._("frame")).set({style:{display:"none"}});
        Ext.get(me._("frame")).set({src:url});
        Ext.get(me._("frame")).set({style:{display:""}});
    }
    
});