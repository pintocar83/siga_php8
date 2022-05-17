/**
 *  Definición de la interfaz 'Sistema de Asistencia - Ingresar/Editar Nota'.
 *
 *  La interfaz permite registrar a las notas (a las personas) en el sistema de asistencia.
 *  Llamado:
 *    siga.open("modulo_asistencia/ingresar_nota",{id_persona:_id_persona,fecha:_fecha}) [ingresar nueva nota] ó
 *    siga.open("modulo_asistencia/ingresar_nota",{accion:"buscar", id_nota:_id_nota})   [modificar nota existente]
 *    ambas son llamadas desde 'asistencia_consulta/asistencia_mensual.php'
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
siga.define('asistencia_ingresar_nota', {
    extend: 'siga.windowForm',
    title: 'Sistema de Asistencia - Ingresar/Editar Nota',    
    width: 450,
    height: 300,
    minimizable: false,
    modal: true,
    
    initComponent: function(){
        var me = this;
      
        me.itemsToolbar=[
            {
                xtype: 'button',
                id: me._('btnSave'),
                width: 55,
                height: 45,
                text: 'Guardar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-save',
                iconAlign: 'top',
                tooltip: 'Guardar',
                listeners: {
                    click: function(){
                        me.setMessage();
                        me.onSave();    
                    }
                }
            },
            {
                xtype: 'button',
                id: me._('btnDelete'),
                width: 55,
                height: 45,
                text: 'Eliminar',
                cls: 'siga-btn-base',
                iconCls: 'siga-btn-base-icon icon-delete',
                iconAlign: 'top',
                tooltip: 'Eliminar',
                listeners: {
                    click: function(){
                        me.setMessage();
                        Ext.MessageBox.confirm( 'Eliminar',
                                                '\u00BFEst\u00e1 seguro de eliminar el registro?',
                                                function(btn,text){
                                                    if (btn == 'yes')
                                                        me.onDelete();
                                                    }
                                                );
                    }
                }
            },
        ];
      
        me.items=[
            {
                xtype:'hidden',
                id: me._('id'),
                name: 'id',
                fieldLabel: 'ID',
                value: ''
            },
            {
                xtype: 'combobox',
                id: me._('tipo'),
                name: 'tipo',
                fieldLabel: 'Tipo',          
                store: {
                  fields: ['nombre'],
                  data : [                        
                        {'id':'PERSONAL','nombre':'PERSONAL'},
                        {'id':'LABORAL','nombre':'LABORAL'},
                        {'id':'OTRO','nombre':'OTRO'},
                    ]
                },
                displayField: 'nombre',
                valueField: 'id',
                allowBlank: false,
                forceSelection: true,                    
                value: 'PERSONAL',
                editable: false
            },
            {
                xtype:'textarea',
                id: me._('descripcion'),
                name: 'descripcion',
                fieldLabel: 'Descripción',
                value: '',
            }
            
            
        ];
  
        me.callParent(arguments);
        me.setAccess(siga.access('asistencia_ingresar_nota'));
        
        if(!me.parameter.id_nota)
            me.getCmp('btnDelete').hide();
    },
    
    init: function(){
      var me=this;
      if(me.parameter.id_nota){
        switch(me.parameter.accion){
            case "buscar":
                me.onGet();
                break;
        }        
      }
    },
    
    onGet: function(){
        var me=this;
        var _id=me.parameter.id_nota;
        if(!_id) return;
        
        me.getCmp('id').setValue(_id);
    
        Ext.Ajax.request({
            method: 'POST',
            url:'module/asistencia_ingresar_nota/',
            params:{
                action: 'onGet',
                id: _id
            },
            success:function(request){
                var result=Ext.JSON.decode(request.responseText);
                if(!result || result.length==0){
                  alert("Error al cargar los datos.");
                  return;
                }        
                me.getCmp('id').setValue(result[0]['id']);
                me.getCmp('tipo').setValue(result[0]['tipo']);
                me.getCmp('descripcion').setValue(result[0]['descripcion']);
                me.parameter.id_persona=result[0]['id_persona'];
                me.parameter.fecha=result[0]['fecha'];
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);      
                me.setMessage(result.message,"red");
            }
        });
    },
    
    onSave: function(){
        var me=this;
        var _id=Ext.String.trim(me.getCmp("id").getValue());        
        if(_id){
          Ext.MessageBox.confirm("Guardar",
                                 "¿Desea modificar el registro actual?",
                                 function(btn,text){
                                    if (btn == 'yes')
                                        me.save();
                                    });
        }
        else{
            me.save();
        }
      },
      
      save: function(){
        var me=this;
        me.getCmp('tab_data').submit({
            method: 'POST',
            url:'module/asistencia_ingresar_nota/',
            params:{
                action: 'onSave',
                id_persona: me.parameter.id_persona,
                fecha: me.parameter.fecha
            },
            waitMsg: 'Guardando... por favor espere!',
            success: function(form,o){
                Ext.MessageBox.hide();
                var result=o.result;
                //si esta abierta la ventana de reportes, recargarla para mostrar la información actualizada
                if(siga.getCmp("asistencia_consulta"))
                    siga.getCmp("asistencia_consulta").reload();
                
                me.close();
            },
            failure:function(form,o){
                Ext.MessageBox.hide();
                var result = Ext.decode(o.response.responseText);
                me.setMessage(result.message,"red");
            }
        });
      },
    
    onDelete: function(){
        var me=this;
        var _id=Ext.String.trim(me.getCmp("id").getValue());
        if(!_id) return;
        
        Ext.MessageBox.wait('Eliminando... por favor espere!');
        Ext.Ajax.request({
            method: 'POST',
            url:'module/asistencia_ingresar_nota/',
            params:{
                action: 'onDelete',
                id: _id
            },
            success:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);
                if(result.success){
                //si esta abierta la ventana de reportes, recargarla para mostrar la información actualizada
                if(siga.getCmp("asistencia_consulta"))
                    siga.getCmp("asistencia_consulta").reload();
                
                me.close();
                }
                else{
                    me.setMessage(result.message,"red");
                }          
            },
            failure:function(request){
                Ext.MessageBox.hide();
                var result=Ext.JSON.decode(request.responseText);      
                me.setMessage(result.message,"red");
            }
        });
      }
});