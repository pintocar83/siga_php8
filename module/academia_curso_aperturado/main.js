siga.define('academia_curso_aperturado', {
  extend: 'siga.window',
  title: 'Academia de Software Libre - Abrir/Cerrar Cursos', 
  width: 650,
  height: 590,
  
  initComponent: function(){
    var me = this;
    
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
                        window.open("module/academia_reportes/pdf/curso_participantes.php?id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/curso_asistencia.php?id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/curso_calificaciones.php?id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/curso_certificados.php?formato=A&id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/curso_certificados.php?formato=B&id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/curso_actacertificados.php?id_curso="+me.getCmp("id").getValue());
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
                        window.open("module/academia_reportes/pdf/afiche.php?id_curso="+me.getCmp("id").getValue());
                    }
                }
            }
        ]
      },
      {
        xtype:'tbspacer',
        flex:1
      },
      me.getInternal("me.internal.btnPrevious"),
      me.getInternal("me.internal.btnNext")
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
      {
        xtype:'textfield',
        id: me._('codigo'),
        name: 'codigo',
        anchor: "50%",
        fieldLabel: 'Código',
        value: ''
      },
      {
        xtype:'siga.select',
        id: me._('id_curso'),
        name: 'id_curso',
        anchor: "100%",
        fieldLabel: 'Curso',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["denominacion","duracion_h"], title: ["Curso","Duración"], width: ['80%','20%'], sort: ["ASC","ASC"]},
          url: 'module/academia_cursos/',
          actionOnList:'onList',
          actionOnGet:'onGet',
          output:0
        }
      },
      {
        xtype:'siga.select',
        id: me._('id_turno'),
        name: 'id_turno',
        anchor: "100%",
        fieldLabel: 'Turno',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["denominacion"], title: ["Turno"], width: ['100%'], sort: ["ASC"]},
          url: 'module/academia_turno/',
          actionOnList:'onList',
          actionOnGet:'onGet'
        }
      },
      {
        xtype: "container",        
        anchor: "100%",
        layout: "hbox",
        defaults: me.getInternal("field_defaults"),
        items:[
          {
            xtype:'datefield',
            id: me._('fecha_inicio'),
            name: 'fecha_inicio',
            fieldLabel: 'Inicio',
            submitFormat: 'Y-m-d',
            value: '',
            width: "50%",
            margin: '5px 20px 0px 0px;'
            //style:'margin: 5px 20px 0px 0px;',
          },
          {
            xtype:'datefield',
            id: me._('fecha_culminacion'),
            name: 'fecha_culminacion',
            fieldLabel: 'Culminación',
            submitFormat: 'Y-m-d',
            value: '',
            width: "50%",
            margin: '5px 0px 0px 20px;'
            //style:'margin: 5px 0px 0px 20px;',
          }
        ]      
      },
      {
        xtype:'textfield',
        id: me._('cupos'),
        name: 'cupos',
        anchor: "100%",
        fieldLabel: 'Cupos',
        value: ''
      },
      {
        xtype:'siga.select',
        id: me._('id_sala'),
        name: 'id_sala',
        anchor: "100%",
        fieldLabel: 'Sala',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["denominacion"], title: ["Sala"], width: ['100%'], sort: ["ASC"]},
          url: 'module/academia_sala/',
          actionOnList:'onList',
          actionOnGet:'onGet'
        }
      },
      {
        xtype:'siga.select',
        id: me._('id_instructor'),
        name: 'id_instructor',
        anchor: "100%",
        fieldLabel: 'Facilitador',
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["nacionalidad_cedula","nombres_apellidos"], title: ["Cédula","Nombres / Apellidos"], width: ['25%','75%'], sort: ["ASC","ASC"]},
          url: 'module/academia_facilitador/',
          actionOnList:'onList',
          actionOnGet:'onGet'
        }
      },
      {
        xtype:'siga.select',
        id: me._('id_instructor_secundario'),
        name: 'id_instructor_secundario',
        anchor: "100%",
        fieldLabel: 'Facilitador Secundario',
        value: '',
        internal:{
          clearBtn: true,
          valueField: 'id',
          columns: {field: ["nacionalidad_cedula","nombres_apellidos"], title: ["Cédula","Nombres / Apellidos"], width: ['30%','70%'], sort: ["ASC","ASC"]},
          url: 'module/academia_facilitador/',
          actionOnList:'onList',
          actionOnGet:'onGet'
        }
      },
      {
        xtype: "container",        
        anchor: "100%",
        layout: "hbox",
        defaults: me.getInternal("field_defaults"),
        items:[
          {
            xtype:'combobox',
            id: me._('id_estado'),
            name: 'id_estado',
            width: "35%",
            fieldLabel: 'Estado',
            store: {
              fields: ['id', 'nombre'],
              data : [
                {"id":"1", "nombre":"ABIERTO"},
                {"id":"0", "nombre":"CERRADO"}
              ]                      
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true,                    
            value: '1',
            //style:'margin: 5px 20px 0px 0px;',
            margin: '5px 20px 0px 0px;',
            editable: false,
          },
          {
            xtype:'combobox',
            id: me._('id_impreso'),
            name: 'id_impreso',
            width: "35%",
            fieldLabel: 'Certificados Impresos',
            store: {
              fields: ['id', 'nombre'],
              data : [
                {"id":"0", "nombre":"NO"},
                {"id":"1", "nombre":"SI"}
              ]                      
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true,                    
            value: '0',
            //style:'margin: 5px 20px 0px 20px;',
            margin: '5px 20px 0px 20px;',
            editable: false,
          },
          {
            xtype:'textfield',
            id: me._('encuesta_clave'),
            name: 'encuesta_clave',
            flex: 1,
            maxLength: 4,
            enforceMaxLength: true,
            //style:'margin: 5px 0px 0px 20px;',
            margin: '5px 0px 0px 20px;',
            fieldLabel: 'Clave para Encuesta',
            value: ''
          },
        ]      
      },

    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("academia_curso_aperturado"));
    me.internal.sort=[{property: 'fecha_inicio', direction: 'DESC'},{property: 'codigo', direction: 'DESC'}];
    
    var store= new Ext.data.Store({
      pageSize: 100,
      fields: ['id','codigo','curso','turno','fecha_inicio','cupos','estado'],
      autoLoad: true,
      remoteSort: true,
      sorters: me.internal.sort,
      proxy: {
          type:'ajax',
          url: 'module/academia_curso_aperturado/',
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
        dataIndex: 'fecha_inicio',
        text: 'Inicio',
        width: '13%',
        menuDisabled: true,
        sortable: false,
        renderer: function(value){
          return formatDate(value);
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
        dataIndex: 'codigo',
        text: 'Código',
        width: '20%',
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
        dataIndex: 'curso',
        text: 'Curso',
        width: '30%',
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
        dataIndex: 'turno',
        text: 'Turno',
        width: '15%',
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
      }
    ];
    
    me.getCmp('gridList').reconfigure(store,columns);
    me.getCmp('gridList').bindStore(store);
    me.getCmp('pagingList').bindStore(store);
  },
  
  init: function(){
    var me=this;
    
  },
  
  onGet: function(dataview, record, item, index, e){
    var me=this;
    var _id=record.get("id");
    if(!_id) return;
    
    me.getCmp('id').setValue(_id);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/academia_curso_aperturado/',
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
        me.getCmp('codigo').setValue(result[0]['codigo']);
        me.getCmp('id_curso').setValue(result[0]['id_curso']);
        me.getCmp('id_turno').setValue(result[0]['id_turno']);
        me.getCmp('fecha_inicio').setValue(result[0]['fecha_inicio']);
        me.getCmp('fecha_culminacion').setValue(result[0]['fecha_culminacion']);
        me.getCmp('cupos').setValue(result[0]['cupos']);
        me.getCmp('id_sala').setValue(result[0]['id_sala']);
        me.getCmp('id_instructor').setValue(result[0]['id_instructor']);
        me.getCmp('id_instructor_secundario').setValue(result[0]['id_instructor_secundario']);
        me.getCmp('id_estado').setValue(result[0]['estado']);
        me.getCmp('id_impreso').setValue(result[0]['impreso']);
        me.getCmp('encuesta_clave').setValue(result[0]['encuesta_clave']);
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
      url:'module/academia_curso_aperturado/',
      params:{
        action: 'onSave'
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
      url:'module/academia_curso_aperturado/',
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
      me.getCmp("id_instructor_secundario").getValue(),
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
    me.getCmp("id_instructor_secundario").setValue(me.internal.toCopy[8]);
    me.getCmp("id_estado").setValue(me.internal.toCopy[9]);
    me.getCmp("id_impreso").setValue(me.internal.toCopy[10]);
  },
  
});
