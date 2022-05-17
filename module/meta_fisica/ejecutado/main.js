siga.define('meta_fisica/ejecutado', {
  extend: 'siga.window',
  title: 'Meta Física / Actividades', 
  width: 650,
  height: 670,
  
  
  initComponent: function(){
    var me = this;
    me.internal.id_accion_especifica=null;
    
    me.itemsToolbar=[
      me.btnNew(),
      me.btnSave(),
      me.btnDelete(),
      me.btnCopyPaste(),
      {
        xtype: 'button',
        id: me._('btnPdf'),
        width: 65,
        height: 45,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-pdf',
        iconAlign: 'top',
        tooltip: 'Visualizar en PDF',
        menu: [
            {                                    
                text: 'Lista de Participantes',
                //tooltip: 'Lista de participantes',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_participantes.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Lista de Asistencia',
                //tooltip: 'Lista de Asistencia',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_asistencia.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Lista de Calificaciones',
                //tooltip: 'Lista de Calificaciones',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_calificaciones.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Certificados - Formato 1 (Solo una contraportada)',
                //tooltip: 'Certificados - Formato 1 (Solo una contraportada)',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_certificados.php?formato=A&id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Certificados - Formato 2 (Contraportada por cada certificado)',
                //tooltip: 'Certificados - Formato 2 (Contraportada por cada certificado)',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_certificados.php?formato=B&id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Acta de Entraga de Certificados',
                //tooltip: 'Acta de Entraga de Certificados',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_curso_actacertificados.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            },
            {
                text: 'Afiche Publicitario',
                //tooltip: 'Afiche Publicitario',
                listeners: {
                    click: function(){
                        me.setMessage();
                        if(!me.getCmp("id").getValue()){
                          me.setMessage("Debe seleccionar un curso antes para visualizar los reportes.","red");
                          return;
                        }
                        window.open("report/academia/pdf_afiche.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            }
        ]
      },
      {
        xtype:'tbspacer',
        flex:1
      },
      me.btnPrevious(),
      me.btnNext()
    ];
    
    me.items=[
      {
        xtype:'hidden',
        id: me._('id'),
        name: 'id',
        anchor: "50%",
        fieldLabel: 'ID',
        value: ''
      },
      //tipo de meta/actividad
      {
        xtype:'combobox',
        id: me._('tipo'),
        name: 'tipo',
        anchor: "50%",
        fieldLabel: 'Tipo',
        store: {
          fields: ['id', 'nombre'],
          data : [
            {"id":"1", "nombre":"POA"},
            {"id":"2", "nombre":"NO POA"}
          ]                      
        },
        displayField: 'nombre',
        valueField: 'id',
        allowBlank: false,
        forceSelection: true,                    
        value: '1',
        editable: false,
        listeners:{
          change: function(e, newValue, oldValue, eOpts){
            if (newValue=="1"){
              me.getCmp("accion_container").show();
              me.getCmp("accion_denominacion").show();  
            }
            else{
              me.getCmp("accion_container").hide();
              me.getCmp("accion_denominacion").hide();  
            }
          }
        }
      },     
      
      {
          xtype: 'fieldcontainer',
          fieldLabel: 'Acción',
          id: me._("accion_container"),
          layout: 'hbox',
          items:[
            //combo accion centralizada
              {
                  xtype: 'combobox',
                  id: me._('id_accion_centralizada'),
                  tpl: '<tpl for="."><div class="x-boundlist-item" title="{denominacion_centralizada}">{tipo_codigo_centralizada}</div></tpl>', 
                  fieldLabel: '',
                  editable: false,
                  width: 100,
                  queryMode: 'local',
                  store: {
                      fields: ['id','tipo_codigo_centralizada','denominacion_centralizada'],
                      autoLoad: false,
                      pageSize: 100,
                      proxy: {
                          type:'ajax',
                          url: 'module/estructura_presupuestaria/',
                          actionMethods:  {read: "POST"},//actionMethods:'POST',
                          timeout: 3600000,
                          reader: {
                              type: 'json',
                              rootProperty: 'result',
                              totalProperty:'total'
                          },
                          extraParams: {
                              action: 'onList_AccionCentralizada_AP',
                              text: '',
                              id: '',
                              sort: '[{"property":"tipo_codigo_centralizada","direction":"ASC"}]'
                          }
                      },
                      listeners: {
                          load: function(store, records, successful){
                              me.getCmp("id_accion_centralizada").setValue(records[0].get("id"));
                          },
                          /*beforeload: function(store,operation,eOpts){
                              if(!me.getCmp("id_fuente_recursos").getValue())
                                  return false;
                              return true;
                          }*/
                      }
                  },
                  displayField: 'tipo_codigo_centralizada',
                  valueField: 'id',
                  allowBlank: false,
                  forceSelection: true,
                  //value: '1',
                  listeners:{
                      change: function(e, newValue, oldValue, eOpts){
                          //colorcar el title del elemento seleccionado
                          var r=e.getStore().getById(e.getValue());
                          if(!r) return;
                          e.getEl().set({title:r.data.denominacion_centralizada});
                          //cargar el combobox del accion especifica
                          me.getCmp("id_accion_especifica").getStore().removeAll();
                          me.getCmp("id_accion_especifica").getStore().load();
                      }
                  }
              },
              //combo sub especifica
              {
                  xtype: 'combobox',
                  id: me._('id_accion_especifica'),
                  name: 'id_accion_especifica',
                  tpl: '<tpl for="."><div class="x-boundlist-item" title="{denominacion_especifica}">{codigo_especifica}</div></tpl>', 
                  fieldLabel: '',
                  editable: false,
                  width: 50,
                  margin: "0 0 0 10",
                  queryMode: 'local',
                  store: {
                      fields: ['id','codigo_especifica','denominacion_especifica'],
                      autoLoad: false,
                      pageSize: 100,
                      proxy: {
                          type:'ajax',
                          url: 'module/estructura_presupuestaria/',
                          actionMethods:  {read: "POST"},//actionMethods:'POST',
                          timeout: 3600000,
                          reader: {
                              type: 'json',
                              rootProperty: 'result',
                              totalProperty:'total'
                          },
                          extraParams: {
                              action: 'onList_AccionEspecifica_AP',
                              text: '',
                              id: '',
                              sort: '[{"property":"codigo_especifica","direction":"ASC"}]'                                                    
                          }
                      },
                      listeners: {
                          load: function(store, records, successful){
                            me.getCmp("id_accion_especifica").setValue(me.internal.id_accion_especifica==null?records[0].get("id"):me.internal.id_accion_especifica);
                                                         
                              
                          },
                          beforeload: function(store,operation,eOpts){
                              if(!me.getCmp("id_accion_centralizada").getValue())
                                  return false;
                              store.proxy.extraParams.id_accion_centralizada=me.getCmp('id_accion_centralizada').getValue();
                              return true;
                          }
                      }
                  },
                  displayField: 'codigo_especifica',
                  valueField: 'id',
                  allowBlank: false,
                  forceSelection: true,
                  //value: '1',
                  listeners:{
                      change: function(e, newValue, oldValue, eOpts){
                          //colorcar el title del elemento seleccionado
                          var r=e.getStore().getById(e.getValue());
                          if(!r) return;
                          e.getEl().set({title:r.data.denominacion_especifica});
                          me.getCmp("accion_denominacion").setValue(r.data.denominacion_especifica);
                          //cargar el combobox del accion subespecifica
                          //me.getCmp("id_accion_subespecifica").getStore().removeAll();
                          //me.getCmp("id_accion_subespecifica").getStore().load();
                      }
                  }
              }
          ]                                
      },
      //denominacion proyecto
      {
        xtype:'textarea',
        id: me._('accion_denominacion'),        
        anchor: "100%",
        fieldLabel: 'Denominación',
        value: '',
        readOnly: true,
        height: 30,
      },
      //actividad
      {
        xtype:'textarea',
        id: me._('actividad'),
        name: 'actividad',
        anchor: "100%",
        fieldLabel: 'Actividad',        
        fieldStyle: 'text-transform: uppercase;',
        value: ''
      },
      //Unidad / Coordinación
      {
        xtype: 'combobox',
        id: me._('id_unidad_coordinacion'),
        name: 'id_unidad_coordinacion',
        anchor: '100%',
        fieldLabel: 'Unidad/Coordinación',
        editable: false,
        queryMode: "local",
        store: {
            fields: ['id','coordinacion'],
            autoLoad: true,
            pageSize: 100,
            proxy: {
                type:'ajax',
                url: 'module/unidad_coordinacion/',
                actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
                timeout: 3600000,
                reader: {
                    type: 'json',
                    rootProperty: 'result',
                    totalProperty:'total'
                },
                extraParams: {
                    action: 'onListSelect'
                }
            },
            listeners: {
                load: function(store, records, successful){
                    me.internal.id_unidad_coordinacion=records[0].get("id");
                    me.getCmp("id_unidad_coordinacion").setValue(records[0].get("id"));
                }
            }
        },
        displayField: 'coordinacion',
        valueField: 'id',
        allowBlank: false,
        forceSelection: true
      },
      
      //responsable
      {
        xtype:'siga.select',
        id: me._('id_responsable'),
        name: 'id_responsable',
        anchor: "100%",
        fieldLabel: 'Responsable',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["identificacion","denominacion"], title: ["Cédula","Nombres / Apellidos"], width: ['25%','75%','',''], sort: ["ASC","ASC"]},
          url: 'module/ficha/',
          actionOnList:'onList',
          actionOnGet:'onGet'
        }
      },
      
      //Unidad de Medida
      {
        xtype: 'combobox',
        id: me._('id_unidad_medida'),
        name: 'id_unidad_medida',
        anchor: '100%',
        //flex: 1,
        //margin: '5px 0 0 0',
        fieldLabel: 'Unidad de Medida',
        editable: false,
        queryMode: "local",
        store: {
            fields: ['id','unidad_medida'],
            autoLoad: true,
            pageSize: 100,
            proxy: {
                type:'ajax',
                url: 'module/meta_fisica/unidad_medida/',
                actionMethods:  {read: "POST"},//actionMethods:'POST',actionMethods:'POST',
                timeout: 3600000,
                reader: {
                    type: 'json',
                    rootProperty: 'result',
                    totalProperty:'total'
                },
                extraParams: {
                    action: 'onList'                    
                }
            },
            listeners: {
                load: function(store, records, successful){
                    me.internal.id_unidad_coordinacion=records[0].get("id");
                    me.getCmp("id_unidad_medida").setValue(records[0].get("id"));
                }
            }
        },
        displayField: 'unidad_medida',
        valueField: 'id',
        allowBlank: false,
        forceSelection: true
      },
      
      //meta mensual
      {
        xtype:'fieldset',        
        title: '<b>Meta</b>',
        collapsible: false,
        layout:'hbox',
        padding: "0 0 10 0",
        items: [
          //columna 1
          {
            xtype: "container",
            flex: 1,
            defaults: me.getInternal("field_defaults"),
            layout: 'anchor',
            padding: "0 10 0 10",
            items:[
              {
                xtype: "textfield",
                fieldLabel: 'Enero',
                id: me._("meta_ene"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Febrero',
                id: me._("meta_feb"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Marzo',
                id: me._("meta_mar"),
                margin: 0,
                anchor: "100%"
              }
            ]
          },
          //columna 2
          {
            xtype: "container",
            flex: 1,
            defaults: me.getInternal("field_defaults"),
            layout: 'anchor',
            padding: "0 10 0 10",
            items:[
              {
                xtype: "textfield",
                fieldLabel: 'Abril',
                id: me._("meta_abr"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Mayo',
                id: me._("meta_may"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Junio',
                id: me._("meta_jun"),
                margin: 0,
                anchor: "100%"
              }
            ]
          },
          //columna 3
          {
            xtype: "container",
            flex: 1,
            defaults: me.getInternal("field_defaults"),
            layout: 'anchor',
            padding: "0 10 0 10",
            items:[
              {
                xtype: "textfield",
                fieldLabel: 'Julio',
                id: me._("meta_jul"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Agosto',
                id: me._("meta_ago"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Septiembre',
                id: me._("meta_sep"),
                margin: 0,
                anchor: "100%"
              }
            ]
          },
          //columna 4
          {
            xtype: "container",
            flex: 1,
            defaults: me.getInternal("field_defaults"),
            layout: 'anchor',
            padding: "0 10 0 10",
            items:[
              {
                xtype: "textfield",
                fieldLabel: 'Octubre',
                id: me._("meta_oct"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Noviembre',
                id: me._("meta_nov"),
                margin: 0,
                anchor: "100%"
              },
              {
                xtype: "textfield",
                fieldLabel: 'Diciembre',
                id: me._("meta_dic"),
                margin: 0,
                anchor: "100%"
              }
            ]
          },
        ],
      }
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("meta_fisica"));
    me.internal.sort=[{property: 'tipo', direction: 'DESC'},{property: 'accion_codigo', direction: 'DESC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','tipo','accion_codigo','actividad','coordinacion'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/meta_fisica/',
          actionMethods:  {read: "POST"},//actionMethods:'POST',
          timeout: 3600000,
          reader: {
              type: 'json',
              rootProperty: 'result',
              totalProperty:'total'
          },
          extraParams: {
              action: 'onList',
              text: ''
          }
      },
      listeners: {
        load: function(store, records, successful){
          me.getCmp('gridList').getSelectionModel().deselectAll();
        },
        beforeload: function(store,operation,eOpts){
          store.proxy.extraParams.text=me.getCmp('txtSearch').getValue();
        }
      }
    });    
    
    var columns=[
      {
        xtype: 'gridcolumn',
        dataIndex: 'tipo',
        text: 'Tipo',
        width: '10%',
        menuDisabled: true,
        sortable: false,
        renderer: function(value){
          return value==1?"POA":"NO POA";
        },
        cls: "x-column-header-sort-"+me.internal.sort[0].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'accion_codigo',
        text: 'Acción',
        width: '15%',
        menuDisabled: true,
        sortable: false,
        cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'actividad',
        text: 'Actividad',
        width: '50%',
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'coordinacion',
        text: 'Coordinacion',
        flex:1,
        //width: '15%',
        menuDisabled: true,
        sortable: false,
        //cls: "x-column-header-sort-"+me.internal.sort[1].direction,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      }/*,      
      {
        xtype: 'gridcolumn',
        dataIndex: 'cupos',
        text: 'Cupos',
        width: '8%',
        menuDisabled: true,
        sortable: false,
      },
      {
        xtype: 'gridcolumn',
        dataIndex: 'estado',
        text: 'Estado',
        //width: '14%',
        menuDisabled: true,
        sortable: false,
        flex: 1,
        listeners:{
          headerclick: function( ct, column, e, t, eOpts ){
            me.onClickHeader( ct, column, e, t, eOpts );
          }
        }
      }*/
    ];
    
    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('gridList').bindStore(store);
    me.getCmp('pagingList').bindStore(store);
  },
  
  init: function(){
    var me=this;
    me.getCmp("id_accion_centralizada").getStore().load();
    
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    if(!_id) return;
    
    me.internal.id_accion_especifica=null;
    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/meta_fisica/',
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
        console.log(result);
        
        me.internal.id_accion_especifica=null;
        me.getCmp('id').setValue(result[0]['id']);
        me.getCmp('tipo').setValue(result[0]['tipo']);
        if(result[0]['tipo']=="1" || result[0]['tipo']==1) {
          var id_accion_centralizada_anterior=me.getCmp('id_accion_centralizada').getValue();  
          me.getCmp('id_accion_centralizada').setValue(result[0]['id_accion_centralizada']);
          me.internal.id_accion_especifica=result[0]['id_accion_especifica'];
          if(id_accion_centralizada_anterior==result[0]['id_accion_centralizada']) {
            me.getCmp('id_accion_especifica').setValue(result[0]['id_accion_especifica']);
          }
        }
        
        me.getCmp('actividad').setValue(result[0]['actividad']);
        me.getCmp('id_unidad_coordinacion').setValue(result[0]['id_unidad_coordinacion']);
        me.getCmp('id_responsable').setValue(result[0]['id_responsable']);
        me.getCmp('id_unidad_medida').setValue(result[0]['id_unidad_medida']);
        
        var meta=Ext.decode(String(result[0]['meta']).replace("{","[").replace("}","]"));
        var meses=["meta_ene","meta_feb","meta_mar","meta_abr","meta_may","meta_jun","meta_jul","meta_ago","meta_sep","meta_oct","meta_nov","meta_dic"];
        for(var i=0;i<meses.length;i++)
          me.getCmp(meses[i]).setValue(meta[i]);
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
    
    var _meta="ARRAY["+
      me.getCmp("meta_ene").getValue()+","+
      me.getCmp("meta_feb").getValue()+","+
      me.getCmp("meta_mar").getValue()+","+
      me.getCmp("meta_abr").getValue()+","+
      me.getCmp("meta_may").getValue()+","+
      me.getCmp("meta_jun").getValue()+","+
      me.getCmp("meta_jul").getValue()+","+
      me.getCmp("meta_ago").getValue()+","+
      me.getCmp("meta_sep").getValue()+","+
      me.getCmp("meta_oct").getValue()+","+
      me.getCmp("meta_nov").getValue()+","+
      me.getCmp("meta_dic").getValue()+
    "]";
    
    me.getCmp('tab_data').submit({
      method: 'POST',
      url:'module/meta_fisica/ejecutado/',
      params:{
        action: 'onSave',
        meta: _meta
      },
      waitMsg: 'Guardando... por favor espere!',
      success: function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.onNew();
        me.setMessage(result.message,"green");
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
      url:'module/meta_fisica/',
      params:{
        action: 'onDelete',
        id: _id
      },
      success:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);
        if(result.success){
          me.onNew();
          me.setMessage(result.message,"green");
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
  },
  
  onCopy: function(){
    var me=this;
    me.internal.toCopy=[
      me.getCmp("codigo").getValue(),
      me.getCmp("id_curso").getValue(),
      me.getCmp("id_turno").getValue(),
      me.getCmp("fecha_inicio").getValue(),
      me.getCmp("fecha_culminacion").getValue(),
      me.getCmp("cupos").getValue(),
      me.getCmp("id_sala").getValue(),
      me.getCmp("id_instructor").getValue(),
      me.getCmp("id_estado").getValue(),
      me.getCmp("id_impreso").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;
    me.getCmp("codigo").setValue(me.internal.toCopy[0]);
    me.getCmp("id_curso").setValue(me.internal.toCopy[1]);
    me.getCmp("id_turno").setValue(me.internal.toCopy[2]);
    me.getCmp("fecha_inicio").setValue(me.internal.toCopy[3]);
    me.getCmp("fecha_culminacion").setValue(me.internal.toCopy[4]);
    me.getCmp("cupos").setValue(me.internal.toCopy[5]);
    me.getCmp("id_sala").setValue(me.internal.toCopy[6]);
    me.getCmp("id_instructor").setValue(me.internal.toCopy[7]);
    me.getCmp("id_estado").setValue(me.internal.toCopy[8]);
    me.getCmp("id_impreso").setValue(me.internal.toCopy[9]);
  },
  
});
