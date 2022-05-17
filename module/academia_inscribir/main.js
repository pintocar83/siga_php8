siga.define('academia_inscribir', {
  extend: 'siga.windowForm',
  title: 'Academia de Software Libre - Inscribir',  
  width: 780,
  height: 570,

  initComponent: function(){
    var me = this;
    
    me.items=[
      {
        xtype:'siga.select',
        id: me._('id_curso_aperturado'),
        name: 'id_curso_aperturado',
        anchor: "100%",//width: 680,
        fieldLabel: 'Curso',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["codigo","curso","turno","fecha_inicio"], title: ["Código","Curso","Turno","Fecha"], width: ['20%','40%','25%','15%'], sort: ["ASC","ASC","ASC","ASC"]},
          url: 'module/academia_curso_aperturado/',
          actionOnList:'onList_eSelect',
          actionOnGet:'onGet_eSelect',
          output:5,
          onAccept: function(){
            me.onRecargar();
          }
        }
        
      },
      {
        xtype: 'gridpanel',
        id: me._('gridList'),
        border: 1,
        preventHeader: true,
        anchor: "100%",//width: 680,
        selModel:{
          mode: "MULTI"
        },
        height: 380,
        scroll: 'vertical',
        columns: [
          new Ext.grid.RowNumberer(),
          {
            xtype: 'gridcolumn',
            dataIndex: 'persona',
            text: 'Cédula / Nombres / Apellidos',
            width: '30%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'institucion',
            text: 'Institución',
            width: '43%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'estado',
            text: 'Estado',
            width: '12%',
            menuDisabled: true,
            sortable: false,
          },
          {
            xtype: 'gridcolumn',
            dataIndex: 'calificacion',
            text: 'Calificación',
            width: '10%',
            flex: 1,
            menuDisabled: true,
            sortable: false,
          }
        ],
        store: {
          fields: ['id','persona','institucion','estado','calificacion','cedula','nacionalidad'],
          autoLoad: true,
          pageSize: 100,
          proxy: {
              type:'ajax',
              url: 'module/academia_inscribir/',
              actionMethods:  {read: "POST"},//actionMethods:'POST',
              timeout: 3600000,
              reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
              },
              extraParams: {
                  action: 'onList',
                  text: '',
                  id: ''
              }
          },
          listeners: {
            load: function(store, records, successful){
              me.getCmp('gridList').getSelectionModel().deselectAll();
            },
            beforeload: function(store,operation,eOpts){
              store.proxy.extraParams.id=me.getCmp('id_curso_aperturado').getValue();
            }
          }
        },
        listeners: {
          select: function(dataview, record, item, index, e){
            },
          itemdblclick: function(dataview, record, item, index, e){
            me.onModificar(dataview, record, item, index, e);            
            }
        }
      }
    ];
    
    me.itemsToolbar=[
      {
        xtype: 'button',
        id: me._('btnAgregar'),
        height: 45,
        width: 55,
        text: 'Agregar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-persona_agregar',
        iconAlign: 'top',
        tooltip: 'Agregar Persona',
        listeners: {
            click: function(){
                me.setMessage();
                me.onAgregar();    
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnQuitar'),
        height: 45,
        width: 55,
        text: 'Quitar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-persona_quitar',
        iconAlign: 'top',
        tooltip: 'Quitar Personas',
        listeners: {
            click: function(){
                me.setMessage();
                me.onQuitar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnRefresh'),
        height: 45,
        width: 55,
        text: 'Recargar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-reload',
        iconAlign: 'top',        
        tooltip: 'Recargar Lista',
        listeners: {
            click: function(){
              me.setMessage();
              me.onRecargar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnPdf'),
        width: 65,
        height: 45,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-display',
        iconAlign: 'top',
        tooltip: 'Visualizar',
        menu: [
            {                                    
                text: 'Lista de Participantes',
                //tooltip: 'Lista de participantes',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_participantes.php?id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Lista de Asistencia',
                //tooltip: 'Lista de Asistencia',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_asistencia.php?id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Lista de Calificaciones',
                //tooltip: 'Lista de Calificaciones',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_calificaciones.php?id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Certificados - Formato 1 (Solo una contraportada)',
                //tooltip: 'Certificados - Formato 1 (Solo una contraportada)',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_certificados.php?formato=A&id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Certificados - Formato 2 (Contraportada por cada certificado)',
                //tooltip: 'Certificados - Formato 2 (Contraportada por cada certificado)',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_certificados.php?formato=B&id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Acta de Entraga de Certificados',
                //tooltip: 'Acta de Entraga de Certificados',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/curso_actacertificados.php?id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            },
            {
                text: 'Afiche Publicitario',
                //tooltip: 'Afiche Publicitario',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id_curso_aperturado").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("module/academia_reportes/pdf/afiche.php?id_curso="+me.getCmp("id_curso_aperturado").getValue());
                    }
                }
            }
        ]
      },
      {
        xtype:'tbspacer',
        flex:1
      },
      {
        xtype: 'button',
        id: me._('btnRestaurar'),
        height: 45,
        width: 55,
        text: 'Restaurar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-restaurar',
        iconAlign: 'top',
        tooltip: 'Restaurar Estado a Inscrito',
        listeners: {
            click: function(){
              me.setMessage();
              me.onRestaurar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnAprobar'),
        height: 45,
        width: 55,
        text: 'Aprobar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-aprobar',
        iconAlign: 'top',
        tooltip: 'Aprobar Personas',
        listeners: {
            click: function(){
              me.setMessage();
              me.onAprobar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnReprobar'),
        height: 45,
        width: 55,
        text: 'Reprobar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-reprobar',
        iconAlign: 'top',
        tooltip: 'Reprobar Personas',
        listeners: {
            click: function(){
              me.setMessage();
              me.onReprobar();
            }
        }
      }
      
      
      
    ];
    
    me.callParent(arguments);   
    me.setAccess(siga.getAccess("academia_inscribir"));
  },
  
  init: function(){
    var me=this;
  },
  
  setAccess: function(_access){
    var me=this;        
    switch(_access){
      case "rw":
          
          break;
      case "r":          
          me.getCmp('btnAgregar').setDisabled(true);          
          me.getCmp('btnQuitar').setDisabled(true);
          me.getCmp('btnAprobar').setDisabled(true);
          me.getCmp('btnReprobar').setDisabled(true);
          me.getCmp('btnRestaurar').setDisabled(true);
          break;
      case "a":          
          me.getCmp('btnQuitar').setDisabled(true);
          me.getCmp('btnAprobar').setDisabled(true);
          me.getCmp('btnReprobar').setDisabled(true);
          me.getCmp('btnRestaurar').setDisabled(true);
          break;
    }
  },   
  
  onAgregar: function(){
    var me=this;
    var id_curso_aperturado=Ext.String.trim(me.getCmp('id_curso_aperturado').getValue());
    if(!id_curso_aperturado){
      me.setMessage("Debe seleccionar un curso antes de agregar las personas.","red");
      return;
    }
    
    var form=Ext.create("academia_inscribir/agregar_persona");
    form.internal.parent=me;
    form.internal.id_curso_aperturado=id_curso_aperturado;
    form.show();
    form.focus();
    form.init();
  },
  
  onModificar: function(dataview, record, item, index, e){
    var me=this;
    var form=Ext.create("academia_inscribir/agregar_persona");
    form.internal.parent=me;
    form.internal.id_curso_aperturado=me.getCmp('id_curso_aperturado').getValue();
    form.internal.nacionalidad=record.get("nacionalidad");
    form.internal.cedula=record.get("cedula");
    form.show();
    form.focus();
    form.init();
  },
  
  onQuitar: function(){
    var me=this;
    var mensaje="";
    var data;
    var id_eliminar=[];
    var selecion_actual = me.getCmp('gridList').getSelectionModel().getSelection();
    if(selecion_actual.length==0)
      return;
    
    for(var i=0;i<selecion_actual.length;i++){
      data=selecion_actual[i].getData();
      id_eliminar.push(data.id);
      mensaje+=data.persona+"<br />";
    }
    
    Ext.MessageBox.confirm( "Quitar Personas",
                            "<b>¿Desea quitar las siguientes personas del curso actual?</b><br /><br /> "+mensaje,
                            function(btn,text){
                              if (btn == 'yes'){
                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/academia_inscribir/',
                                  params:{
                                    action: 'onDelete',
                                    id_curso_aperturado: me.getCmp('id_curso_aperturado').getValue(),
                                    id_inscrito: Ext.JSON.encode(id_eliminar)
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                    me.setMessage(result.message,"green");
                                    me.onRecargar();
                                  },
                                  failure:function(request){
                                    //Ext.MessageBox.hide();
                                    var result=Ext.JSON.decode(request.responseText);      
                                    me.setMessage(result.message,"red");
                                    me.onRecargar();
                                  }
                                });
                              }
                            });
  },
  
  onRecargar: function(){
    var me=this;
    me.getCmp('gridList').store.load();
    
  },
  
  onRestaurar: function(){
    var me=this;
    
    var mensaje="";
    var data;
    var id_restaurar=[];
    var selecion_actual = me.getCmp('gridList').getSelectionModel().getSelection();
    if(selecion_actual.length==0)
      return;
    
    for(var i=0;i<selecion_actual.length;i++){
      data=selecion_actual[i].getData();
      id_restaurar.push(data.id);
      mensaje+=data.persona+"<br />";
    }
    
    Ext.MessageBox.confirm( "Restaurar Estado a Inscrito",
                            "¿Desea restaurar el estado a 'Inscrito' de las personas seleccionadas?<br /><br /> ",
                            function(btn,text){
                              if (btn == 'yes'){
                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/academia_inscribir/',
                                  params:{
                                    action: 'onRestaurar',
                                    id_curso_aperturado: me.getCmp('id_curso_aperturado').getValue(),
                                    id_inscrito: Ext.JSON.encode(id_restaurar)
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                    me.setMessage(result.message,"green");
                                    me.onRecargar();
                                  },
                                  failure:function(request){
                                    //Ext.MessageBox.hide();
                                    var result=Ext.JSON.decode(request.responseText);      
                                    me.setMessage(result.message,"red");
                                    me.onRecargar();
                                  }
                                });
                              }
                            });
  },
  
  onAprobar: function(){
    var me=this;
    
    var mensaje="";
    var data;
    var id_aprobar=[];
    var selecion_actual = me.getCmp('gridList').getSelectionModel().getSelection();
    if(selecion_actual.length==0)
      return;
    
    for(var i=0;i<selecion_actual.length;i++){
      data=selecion_actual[i].getData();
      id_aprobar.push(data.id);
      mensaje+=data.persona+"<br />";
    }
    
    Ext.MessageBox.prompt("Aprobar Personas",
                          "Introduzca la calificación para las personas seleccionadas<br /><br /> ",
                          function(btn, text){
                            if(btn == 'ok'){
                              if(!Ext.String.trim(text)){
                                me.onAprobar();
                                return;
                              }
                              Ext.Ajax.request({
                                method: 'POST',
                                url:'module/academia_inscribir/',
                                params:{
                                  action: 'onAprobar',
                                  id_curso_aperturado: me.getCmp('id_curso_aperturado').getValue(),
                                  id_inscrito: Ext.JSON.encode(id_aprobar),
                                  calificacion: text
                                },
                                success:function(request){
                                  var result=Ext.JSON.decode(request.responseText);
                                  me.setMessage(result.message,"green");
                                  me.onRecargar();
                                },
                                failure:function(request){
                                  //Ext.MessageBox.hide();
                                  var result=Ext.JSON.decode(request.responseText);      
                                  me.setMessage(result.message,"red");
                                  me.onRecargar();
                                }
                              });
                            }
                          },
                        null,
                        null,
                        "100");
  },

  onReprobar: function(){
    var me=this;
    var me=this;
    
    var mensaje="";
    var data;
    var id_reprobar=[];
    var selecion_actual = me.getCmp('gridList').getSelectionModel().getSelection();
    if(selecion_actual.length==0)
      return;
    
    for(var i=0;i<selecion_actual.length;i++){
      data=selecion_actual[i].getData();
      id_reprobar.push(data.id);
      mensaje+=data.persona+"<br />";
    }
    
    Ext.MessageBox.prompt("Reprobar Personas",
                          "Introduzca la calificación para las personas seleccionadas<br /><br /> ",
                          function(btn, text){
                            if(btn == 'ok'){
                              if(!Ext.String.trim(text)){
                                me.onReprobar();
                                return;
                              }
                              
                              Ext.Ajax.request({
                                method: 'POST',
                                url:'module/academia_inscribir/',
                                params:{
                                  action: 'onReprobar',
                                  id_curso_aperturado: me.getCmp('id_curso_aperturado').getValue(),
                                  id_inscrito: Ext.JSON.encode(id_reprobar),
                                  calificacion: text
                                },
                                success:function(request){
                                  var result=Ext.JSON.decode(request.responseText);
                                  me.setMessage(result.message,"green");
                                  me.onRecargar();
                                },
                                failure:function(request){
                                  //Ext.MessageBox.hide();
                                  var result=Ext.JSON.decode(request.responseText);      
                                  me.setMessage(result.message,"red");
                                  me.onRecargar();
                                }
                              });
                            }
                          },
                        null,
                        null,
                        "0");
  }
  
  
  
});








