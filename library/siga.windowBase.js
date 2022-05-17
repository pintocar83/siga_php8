Ext.define('siga.windowBase', {
    extend: 'Ext.window.Window',
    minimizable: true,
    resizable: false,
    constrain: true,
    constrainTo: 'siga-window-container',
    constrainHeader: false,
    renderTo: 'siga-window-container',    
    layout: 'fit',

    internal: {},
    
    addItemMessage: function(){
        var me = this;
        if(!me.items) return;        
        me.items.unshift({
            xtype: 'label',
            id: me._('message'),
            style:'text-align: center; font-style: italic;',
            margin: '5px 0px 0px 0px;',
            padding: 0,
            html: "&nbsp;",
            width: '100%',
            flex: 1,
        });
        me.internal.messageTimeOutHandler=null;
    },
   
    setInternal: function(o){
        var me = this;        
        for(var key in o)
            me.internal[key]=o[key];
    },
    
    getInternal: function(key){
        var me = this;
        if(!key) return me.internal;
        return me.internal[key];
    },
    
    btnNew: function(){
      var me=this;
      return {
        xtype: 'button',                
        id: me._('btnNew'),
        height: 45,
        width: 57,
        text: 'Nuevo',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-new',         
        iconAlign: 'top',
        listeners: {
          click: function(){
            me.setMessage();
            me.onNew();    
          }
        }
      };
    },
    
    btnSave: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnSave'),
        height: 45,
        width: 57,
        text: 'Guardar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-save',
        iconAlign: 'top',
        listeners: {
            click: function(){
                me.setMessage();
                me.onSave();    
            }
        }
      };
    },
    
    btnEdit: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnEdit'),
        height: 45,
        width: 57,
        text: 'Modificar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-edit',
        iconAlign: 'top',
        listeners: {
          click: function(){
            me.setMessage();
            me.onEdit();    
          }
        }
      };
    },
    
    btnDelete: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnDelete'),
        height: 45,
        width: 57,
        text: 'Eliminar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-delete',
        iconAlign: 'top',        
        listeners: {
          click: function(){
            if(!me.getCmp('gridList').getSelectionModel().hasSelection())
              return;
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
      };
    },
    
    btnCopyPaste: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnCopyPaste'),
        height: 45,
        width: 65,
        text: 'Copiar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-copypaste',
        iconAlign: 'top',
        tooltip: 'Copiar, pegar o duplicar la información del formulario.',
        menu: [
          {                                    
            text: 'Copiar',
            iconCls: 'siga-icon-16 icon-copy',
            tooltip: 'Copia los datos del formulario.',
            listeners: {
              click: function(){
                me.setMessage();
                me.onCopy();
              }
            }
          },
          {
            text: 'Pegar',
            iconCls: 'siga-icon-16  icon-paste',
            tooltip: 'Pega en el formulario los datos previamente copiados.',
            listeners: {
              click: function(){
                me.setMessage();
                me.onPaste();
              }
            }
          },
          {                                    
            text: 'Duplicar',
            iconCls: 'siga-icon-16  icon-copypaste',
            tooltip: 'Copia la información del formulario y lo pega sobre uno nuevo.',
            listeners: {
              click: function(){
                me.setMessage();
                me.onDuplicate();
              }
            }
          },
        ]
      };
    },
    
    btnPrevious: function(){
      var me=this;
      return {
        xtype: 'button',
        height: 45,
        width: 57,
        text: 'Previo',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-previous',
        iconAlign: 'top',
        tooltip: 'Registro Previo',
        listeners: {
          click: function(){
            me.onPrevious();
          }
        }
      };
    },
    
    btnNext: function(){
      var me=this;
      return {
        xtype: 'button',
        height: 45,
        width: 57,
        text: 'Siguiente',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-next',
        iconAlign: 'top',
        tooltip: 'Registro Siguiente',
        listeners: {
          click: function(){
            me.onNext();
          }
        }
      };
    },
    
    btnDisplay: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnDisplay'),
        height: 45,
        width: 57,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-display',
        iconAlign: 'top',
        listeners: {
          click: function(){
            me.setMessage();
            me.onDisplay();    
          }
        }
      };
    },
    
    btnPDF: function(){
      var me=this;
      return {
        xtype: 'button',
        id: me._('btnPDF'),
        height: 45,
        width: 57,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        focusCls: '',
        disabledCls: 'siga-btn-disabled',
        iconCls: 'siga-btn-base-icon icon-pdf',         
        iconAlign: 'top',
        tooltip: 'Visualizar en PDF',
        listeners: {
          click: function(){
            me.setMessage();
            me.onPDF();    
          }
        }
      };
    },
    
        
    constructor: function(){
      var me=this;
      me.internal={};
      me.setInternal({
        field_defaults: {
          //style:'margin: 5px 40px 0px 40px;',
          margin: '5px 40px 0px 40px;',
          labelAlign: 'top',
          labelSeparator: '',
          labelCls: 'siga-field-label',
          hideLabel: false,
          //fieldStyle: 'margin: 0px;',
          anchor: "100%"
        },
        gridList: {
          features: null
        }
      });
      
      me.callParent(arguments);
    },    
    
    initComponent: function() {
        var me = this;
        if(me.modal){
            me.renderTo=Ext.getBody();
            me.minimizable=false;
        }
        
        if(me.itemsToolbar)
            me.dockedItems=[{
                xtype: 'toolbar',
                style: 'margin: 0px; padding: 0px;',
                flex: 1,
                dock: 'top',
                items: me.itemsToolbar
            }];
        
        me.tools=[];
        if(me.onHelp)
            me.tools.push({type: 'help', tooltip: 'Ayuda', callback: me.onHelp});
        
        me.callParent(arguments);
    },
    
    _: function(id){
        var me=this;
        return me.id+"-"+id;
    },
    
    getCmp: function(id){
        var me=this;
        return Ext.getCmp(me._(String(id)));
    },
    
    init: function(){
    },
    
    setMessage: function(_text,_color,_time){
        var me=this;
        
        window.clearTimeout(me.internal.messageTimeOutHandler);
        
        if(!me.getCmp('message')) return;
        
        if(!_text){            
            me.getCmp('message').setText('&nbsp;',false);
            return;
        }        
        if(!_color)
            _color="black";
        me.getCmp('message').setText("<div style='color: "+_color+";'>"+_text+"</div>",false);
        if(!_time)
            _time=10000;
        me.internal.messageTimeOutHandler=setTimeout(function(){
            me.setMessage();
        },_time);
    },
    
    setAccess: function(_access){
        var me=this;        
        switch(_access){
            case "rw":                
                break;
            case "r":
                if(me.getCmp('btnSave'))
                    me.getCmp('btnSave').setDisabled(true);
                if(me.getCmp('btnDelete'))
                    me.getCmp('btnDelete').setDisabled(true);
                break;
            case "a":
                if(me.getCmp('btnDelete'))
                    me.getCmp('btnDelete').setDisabled(true);
                break;
        }
    }
});