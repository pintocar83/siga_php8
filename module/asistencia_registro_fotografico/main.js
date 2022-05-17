/**
 *  Definición de la interfaz 'Sistema de Asistencia - Registro Clásico'.
 *
 *  Permite registrar (por medio del código) la hora de entrada y salida del personal.
 *  La intefaz esta compuesta de un iframe que carga archivo 'modulo_asistencia/core/index_clasico.php'
 *  Llamado:
 *    siga.open("modulo_asistencia/asistencia_clasico")
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */

siga.define('asistencia_registro_fotografico', {
    extend: 'siga.windowBase',
    title: 'Sistema de Asistencia - Registro Fotográfico',    
    resizable: false,    
    width: 600,
    height: 540,
    bodyStyle: "background-color: white;",
    layout: {
        type: 'vbox',
        align: 'stretch',
        pack: 'start',
    },
    
    initComponent: function(){
        var me = this;        
        
        me.items=[               
            {
                xtype: "label",
                height: 45,
                html: '<img src="'+siga.value('folder')+'/cintillo_actual.jpg" width="100%" height="40" />',                 
            },
            {
                xtype: "component",
                id: me._("contenedor"),
                flex:1,
                autoEl: {
                    tag: "iframe",
                    style: "border: none;",
                    src: "module/asistencia_registro/form_fotografico.php"
                }
            },
            {
                xtype: 'label',
                text: siga.value("pie_aplicaciones"),
                cls: 'siga-pie_aplicaciones'
            }
        ];
        me.callParent(arguments);      
    }    
});
