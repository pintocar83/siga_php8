/**
 *  Definición de la interfaz 'Sistema de Asistencia - Ingresar/Editar Hora'.
 *
 *  La interfaz permite registrar de forma manual las horas (de las personas) en el sistema de asistencia.
 *  Llamado:
 *    siga.open("modulo_asistencia/ingresar_hora",{id_persona:_id_persona,fecha:_fecha,hora:_hora})  [para ingresar o modificar horas]
 *    el llamado se realiza desde 'modulo_asistencia/reportes/asistencia_mensual.php'
 *
 *  @author Carlos Pinto <pintocar83@gmail.com>
 *  @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 *  @copyright Copyright (c) 2014, FUNDACITE Sucre 
 *  
 *  @version 2014.09.09
 */
siga.define('asistencia_ingresar_hora', {
    extend: 'siga.windowForm',
    title: 'Sistema de Asistencia - Ingresar/Editar Hora',    
    width: 250,
    height: 230,
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
                width: 230,
                fieldLabel: 'ID',
                value: '',
                submitValue: false,
            },
            {
                xtype:'datefield',
                id: me._('fecha'),
                name: 'fecha',
                fieldLabel: 'Fecha',
                submitFormat: 'Y-m-d',
                disabled: true,
                value: '',
                anchor: "100%",
                submitValue: false,
            },
            {
                xtype: "container",
                layout: "hbox",
                anchor: "100%",
                defaults: me.getInternal("field_defaults"),
                items: [
                    {
                        xtype: 'numberfield',
                        id: me._('hora'),
                        name: 'hora',
                        width: 50,
                        fieldLabel: 'Hora',
                        margin: "5 0 0 0",
                        value: 8,
                        maxValue: 12,
                        minValue: 1,
                        submitValue: false,
                        allowBlank: false,
                        valueToRaw: function (value){
                            return (value<10?'0':'') + value;
                        }
                    },
                    {
                        xtype: 'numberfield',
                        id: me._('minuto'),
                        name: 'minuto',
                        width: 50,
                        fieldLabel: '&nbsp;',
                        margin: "5 0 0 0",
                        value: 0,
                        maxValue: 59,
                        minValue: 0,
                        step: 5,
                        submitValue: false,
                        allowBlank: false,
                        valueToRaw: function (value){
                            return (value<10?'0':'') + value;
                        }
                    },
                    {
                        xtype: 'combobox',
                        id: me._('tipo'),
                        name: 'tipo',
                        flex: 1,
                        fieldLabel: '&nbsp;',
                        margin: "5 0 0 0",
                        submitValue: false,
                        store: {
                          fields: ['id','nombre'],
                          data : [                        
                                {'id':'am','nombre':'AM'},
                                {'id':'pm','nombre':'PM'},
                            ]
                        },
                        editable: false,
                        displayField: 'nombre',
                        valueField: 'id',
                        allowBlank: false,
                        forceSelection: true,                    
                        value: 'am'
                    }
                ]
            
            },
            
        ];
  
        me.callParent(arguments);
           
        me.setAccess(siga.access('asistencia_ingresar_hora'));
        
        if(!me.parameter.hora){
            me.getCmp("btnDelete").setVisible(false);
            me.parameter.hora="";
        }
        
    },
    
    init: function(){
        var me=this;
        if(!me.parameter.id_persona || !me.parameter.fecha)
          me.close();
        
        me.getCmp("fecha").setValue(me.parameter.fecha);
        if(me.parameter.hora){
            var h=me.parameter.hora.split(':');
            if(h[0]*1>12){
                me.getCmp("tipo").setValue("pm");
                me.getCmp("hora").setValue(h[0]*1-12);
            }
            else{
                me.getCmp("tipo").setValue("am");
                me.getCmp("hora").setValue(h[0]*1);
            }
            me.getCmp("minuto").setValue(h[1]*1);
        }
    },
    

    onSave: function(){
        var me=this;
        var _hora=me.parameter.hora;        
        if(_hora){
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
        
        
        _hora_nueva=(me.getCmp('hora').getValue()<10?'0':'')+me.getCmp('hora').getValue()+":";
        _hora_nueva+=(me.getCmp('minuto').getValue()<10?'0':'')+me.getCmp('minuto').getValue()+":00 ";
        _hora_nueva+=me.getCmp('tipo').getValue();
        
        if(!me.getCmp("tab_data").isValid()) return;

        me.getCmp('tab_data').submit({
            method: 'POST',
            url:'module/asistencia_registro/registrar.php',
            params:{
                manual: true,
                id_persona: me.parameter.id_persona,
                fecha: me.parameter.fecha,
                hora: _hora_nueva,
                hora_anterior: me.parameter.hora
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
        var _hora=me.parameter.hora;        
        if(!_hora) return;
        
        me.getCmp('tab_data').submit({
            method: 'POST',
            url:'module/asistencia_registro/registrar.php',
            params:{
                id_persona: me.parameter.id_persona,
                fecha: me.parameter.fecha,
                hora: '',
                hora_anterior: me.parameter.hora
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
    }
});