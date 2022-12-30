siga.define('nomina', {
  extend: 'siga.windowBase',
  title: 'Nómina - Hoja de Cálculo',
  width: 780,
  height: 570,
  maximizable:true,
  resizable: true,

  filtro_ficha_id: "",

  listeners: {
    afterrender: function(){
      var me=this;
      me.onActualizarDataPreload();
    },
  },

  initComponent: function(){
    var me = this;

    me.setInternal({
      ventanaSeleccionarNomina: null,
      ventanaVisualizar: null,
      messageTimeOutHandler: null,
      items:[],
      toCopy:[],
      itemsToolbar:[],
      data: {
        preload: {},
        concepto: [],
        ficha: []
      },
      columnaSeleccionada: null,
      cerrado: null,
      periodo_id: null,
      periodo_denominacion: null,
      nomina_id: null,
      nomina_denominacion: null,
    });

    //VENTANA PARA CAMBIAR/SELECCIONAR NOMINA
    me.internal.ventanaSeleccionarNomina=Ext.create('Ext.window.Window', {
      title: 'Seleccionar Nómina',
      minimizable: false,
      maximizable: false,
      closable: false,
      modal: true,
      width: 650,
      height: 270,
      resizable: true,
      layout: 'anchor',
      bodyStyle: 'padding: 5px 20px 0px 20px; background-color: #e8e8e8; border-color: #e8e8e8;',
      autoScroll: true,
      internal:{
        tipo: '',
        id_periodo: '',
        id_nomina: ''
      },
      listeners: {
        beforeclose: function(w,o){
          me.onRecargar();
          me.internal.ventanaSeleccionarNomina.hide();
          //me.onUpdate_MenuPersonaCambiarNomina();
          return false;
        },
        afterrender: function(){
          me.onSeleccionarNominaHeight();
        }
      },

      items:[
        {
          xtype: 'label',
          id: me._('messageVentanaSeleccionarNomina'),
          style:'margin: 5px 0px 0px 0px; text-align: center; font-style: italic;',
          html: "&nbsp;",
          anchor: '100%'
        },

        {
          xtype:'combobox',
          id: me._('tipoVentanaSeleccionarNomina'),
          name: 'tipoVentanaSeleccionarNomina',
          fieldLabel: 'Tipo de Nómina/Periodo',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          anchor: '100%',
          queryMode: "local",
          store: {
            fields: ['tipo','denominacion'],
            data: []
          },
          displayField: 'denominacion',
          valueField: 'tipo',
          allowBlank: false,
          forceSelection: true,
          editable: false,
          value: '',
          listeners: {
            change: function(e, The, eOpts ){
              var tipo = me.getCmp("tipoVentanaSeleccionarNomina").getValue();
              var data_periodo=me.getDataPeriodo({tipo: tipo});
              me.getCmp('id_periodo').getStore().setData(data_periodo);
              if(data_periodo.length>0){
                console.log(data_periodo, data_periodo[data_periodo.length-1]["id"]);
                me.getCmp("id_periodo").setValue(data_periodo[data_periodo.length-1]["id"]);
              }

              var data_nomina=me.getDataNomina({tipo: tipo});
              me.getCmp('id_nomina').getStore().setData(data_nomina);
              if(data_nomina.length>0){
                me.getCmp("id_nomina").setValue(data_nomina[0]["id"]);
              }
            }
          }
        },
        {
          xtype: 'combobox',
          id: me._('id_periodo'),
          name: 'id_periodo',
          anchor: '100%',
          fieldLabel: 'Periodo',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          editable: false,
          queryMode: "local",
          displayTpl: '<tpl for=".">{codigo} {descripcion}</tpl>',
          tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
          store: {
            fields: ['id','periodo'],
            data: []
          },
          displayField: 'periodo',
          valueField: 'id',
          allowBlank: true,
          forceSelection: true,
        },
        {
          xtype: 'tagfield',
          id: me._('id_nomina'),
          name: 'id_nomina',
          anchor: '100%',
          //fieldLabel: 'Nómina',
          fieldLabel: 'Nómina <div class="enlace_etiqueta_nomina"><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onNominaSeleccionarTodo()\" title="Seleccionar Todos">[&#x2714;]<a/><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onNominaSeleccionarNada()\" title="Seleccionar Ninguno">[&#x2716;]</a></div>',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          cls: 'seleccionar_nomina__nomina',
          editable: false,
          queryMode: "local",
          multiSelect: true,
          store: {
            fields: ['id','codigo_nomina'],
            data: []
          },
          displayField: 'codigo_nomina',
          valueField: 'id',
          allowBlank: true,
          forceSelection: true,
          filterPickList: true,
          hideTrigger: true,
          listeners: {
            change: function(){
              me.onSeleccionarNominaHeight();
            }
          }
        },
        {
          xtype: 'container',
          anchor: '100%',
          layout: 'hbox',
          style: 'padding-top: 25px; padding-bottom: 10px;',
          items: [
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: 'Aceptar',
              width: 150,
              listeners: {
                click: function(){
                  if(me.getCmp("id_periodo").getValue()){
                    if(!me.getCmp("id_nomina").getValue()){
                      me.getCmp('messageVentanaSeleccionarNomina').setText("<div style='color: red;'>Debe seleccionar la nómina.</div>",false);
                      return;
                    }
                    me.onUpdate_MenuPersonaCambiarNomina();
                    me.onCargarNomina();
                    me.internal.ventanaSeleccionarNomina.hide();
                  }
                  else{
                    me.getCmp('messageVentanaSeleccionarNomina').setText("<div style='color: red;'>Debe seleccionar el periodo.</div>",false);
                  }
                }
              }
            },
            {
              xtype: 'tbspacer',
              flex: 1
            }
          ]
        }
      ]
    });
    //FIN VENTANA PARA CAMBIAR/SELECCIONAR NOMINA


    //VENTANA PARA VISUALIZAR REPORTES
    me.internal.ventanaVisualizar=Ext.create('siga.windowForm', {
      title: 'Visualizar Nóminas',
      minimizable: false,
      maximizable: false,
      modal: true,
      width: 750,
      height: 450,
      resizable: false,

      listeners: {
        beforeclose: function(w,o){
          me.internal.ventanaVisualizar.hide();
          me.getCmp('btnVisualizar').internal.proyeccion=false;
          return false;
        },
        beforeshow: function(){
          me.getCmp("publicar").hide();
          me.getCmp("txt").hide();
          me.getCmp('formato_visualizar').hide();
          me.getCmp('id_concepto_formato_visualizar').hide();

          switch(me.getCmp('btnVisualizar').internal.reporte) {
            case "nomina_recibo_pago":                      me.getCmp("publicar").show();   break;
            case "nomina_resumen_presupuestario_contable":  me.getCmp("txt").show();        break;
            case "nomina_listado_banco_xls_formato_c":
              me.getCmp("txt").show();
              me.getCmp('formato_visualizar').show();
              if(me.getCmp('formato_visualizar').getValue()=="0"){
                me.getCmp('id_concepto_formato_visualizar').hide();
              }
              else{
                me.getCmp('id_concepto_formato_visualizar').show();
              }
            break;
          }

        }
      },


      itemsToolbar:[
        {
          xtype: 'button',
          height: 45,
          width: 55,
          text: 'PDF',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-pdf',
          iconAlign: 'top',
          tooltip: 'Visualizar en Formato PDF',
          listeners: {
              click: function(){
                var tmp=me.getCmp('id_nomina_visualizar').getValue();
                tmp=tmp["id_nomina_visualizar"];

                var id_nomina="";
                if((typeof tmp)=="object"){
                  for(var i=0;i<tmp.length;i++)
                    id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                }
                else
                  id_nomina=tmp;

                var add_url="";
                if(me.filtro_ficha_id && me.filtro_ficha_id.length>0)
                  add_url="&filtro_ficha_id="+me.filtro_ficha_id.join(",");
                if(me.getCmp('btnVisualizar').internal.reporte=="nomina_listado_banco_xls_formato_c"){
                  var formato=me.getCmp('formato_visualizar').getValue();
                  var id_concepto=me.getCmp('id_concepto_formato_visualizar').getValue();
                  if(id_concepto.length==0 && formato!="0"){
                    alert("Debe seleccionar los conceptos.");
                    return;
                  }
                  else if(formato=="0"){
                    id_concepto=[];
                  }

                  if(formato=="3"){//solo conceptos (separado)

                    for(var c=0; c<id_concepto.length; c++) {
                      add_url+="&formato="+formato+"&id_concepto="+id_concepto[c];
                      window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina+add_url);
                    }

                    return;
                  }
                  add_url+="&formato="+formato+"&id_concepto="+id_concepto.join(",");
                }

                if(me.getCmp('btnVisualizar').internal.proyeccion==true){
                  add_url+="&concepto_proyeccion=1";
                }

                window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina+add_url);
              }
          }
        },
        {
          xtype: 'button',
          id: me._("publicar"),
          height: 45,
          width: 55,
          text: 'Publicar',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-nomina_persona_carpeta',
          iconAlign: 'top',
          tooltip: 'Publicar en carpeta del personal',
          listeners: {
              click: function(){
                if(me.getCmp('btnVisualizar').internal.reporte!="nomina_recibo_pago") return;
                Ext.MessageBox.confirm("Publicar recibos de pago en los expedientes del personal",
                  '<b>\u00BFEst\u00e1 seguro que desea publicar los recibos de pago en los expedientes del personal?</b><br> Período: '+me.getCmp('id_periodo_visualizar').getRawValue()+'',
                  function(btn,text){
                    if(btn == 'yes'){
                      var tmp=me.getCmp('id_nomina_visualizar').getValue();
                      tmp=tmp["id_nomina_visualizar"];

                      var id_nomina="";
                      if((typeof tmp)=="object"){
                        for(var i=0;i<tmp.length;i++)
                          id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                      }
                      else
                        id_nomina=tmp;
                      window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+".php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina+"&generar=1");

                    }
                  });
              }
          }
        },
        {
          xtype: 'button',
          id: me._("txt"),
          height: 45,
          width: 55,
          text: 'TXT',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-txt',
          iconAlign: 'top',
          tooltip: 'Texto Plano',
          listeners: {
              click: function(){
                if(me.getCmp('btnVisualizar').internal.reporte!="nomina_resumen_presupuestario_contable") return;
                var tmp=me.getCmp('id_nomina_visualizar').getValue();
                tmp=tmp["id_nomina_visualizar"];

                var id_nomina="";
                if((typeof tmp)=="object"){
                  for(var i=0;i<tmp.length;i++)
                    id_nomina+=tmp[i]+((i<tmp.length-1)?",":"");
                }
                else
                  id_nomina=tmp;

                window.open("report/"+me.getCmp('btnVisualizar').internal.reporte+"_txt.php?id_periodo="+me.getCmp('id_periodo_visualizar').getValue()+"&id_nomina="+id_nomina);
              }
          }
        },
      ],

      items:[
        {
          xtype:'combobox',
          id: me._('tipo'),
          name: 'tipo',
          fieldLabel: 'Tipo de Nómina/Periodo',
          anchor: '100%',
          queryMode: "local",
          store: {
            fields: ['tipo','denominacion'],
            autoLoad: true,
            pageSize: 1000,
            proxy: {
              type:'ajax',
              url: 'module/nomina_periodo_tipo/',
              actionMethods: {read: "POST"},
              timeout: 3600000,
              reader: {
                type: 'json',
                rootProperty: 'result',
                totalProperty:'total'
              },
              extraParams: {
                action: 'onList_Activo',
                text: '',
                sort: '[{"property": "denominacion", "direction": "ASC"}]'
              }
            },
            listeners: {
              load: function(store, records, successful){
                if(records.length>0)
                  me.getCmp("tipo").setValue(records[0].get("tipo"));
              },
              beforeload: function(store,operation,eOpts){
              }
            }
          },
          displayField: 'denominacion',
          valueField: 'tipo',
          allowBlank: false,
          forceSelection: true,
          editable: false,
          value: '',
          listeners: {
            afterrender: function(e, eOpts ){
              e.setValue("Q");
            },
            change: function(e, The, eOpts ){
              me.getCmp('id_periodo_visualizar').getStore().load();

              me.getCmp('id_nomina_visualizar').removeAll();
              //cargar las nominas en forma de checkbox
              Ext.Ajax.request({
                method: 'POST',
                url:'module/nomina/',
                params:{
                  action: 'onList',
                  tipo: me.getCmp('tipo').getValue(),
                  start: '0',
                  limit: 'ALL',
                  sort: '[{"property": "codigo", "direction": "ASC"}]'
                },
                success:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                  var result=result["result"];
                  for(var i=0;i<result.length;i++)
                    me.getCmp('id_nomina_visualizar').add({boxLabel: result[i]["codigo_nomina"], name: 'id_nomina_visualizar', inputValue: result[i]["id"], checked: false});
                },
                failure:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                  //me.setMessage(result.message,"red");
                }
              });
            }
          }
        },
        {
          xtype: "container",
          layout: "hbox",
          anchor: "100%",
          items: [
            {
              xtype: "checkboxgroup",
              id: me._('id_nomina_visualizar'),
              name: 'id_nomina_visualizar',
              style: "margin-bottom: 25px;",
              flex: 1,
              fieldLabel: 'Nómina',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              columns: 1,
              vertical: true,
              items:[]
            },
            {
              xtype: "container",
              layout: "vbox",
              items: [
                {
                  xtype: 'tagfield',
                  id: me._('id_periodo_visualizar'),
                  name: 'id_periodo',
                  width: 340,
                  //hideTrigger: true,
                  fieldLabel: 'Periodo',
                  labelAlign: 'top',
                  labelSeparator: '',
                  labelStyle: 'font-weight: bold;',
                  editable: false,
                  filterPickList: true,
                  multiSelect: true,
                  queryMode: "local",
                  tipTpl: '<b>{codigo}</b> {descripcion} <small>({fecha})</small>',
                  labelTpl: '{codigo} - {descripcion}',
                  tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',

                  store: {
                    fields: ['id','periodo'],
                    autoLoad: false,
                    pageSize: 1000,
                    proxy: {
                      type:'ajax',
                      url: 'module/nomina_periodo/',
                      actionMethods: {read: "POST"},
                      timeout: 3600000,
                      reader: {
                        type: 'json',
                        rootProperty: 'result',
                        totalProperty:'total'
                      },
                      extraParams: {
                        action: 'onList',
                        text: '',
                        sort: '[{"property": "codigo", "direction": "ASC"}]'
                      }
                    },
                    listeners: {
                      load: function(store, records, successful){
                        me.getCmp("id_periodo_visualizar").reset();
                        if(records.length>0)
                          me.getCmp("id_periodo_visualizar").setValue(records[records.length-1].get("id"));
                      },
                      beforeload: function(store,operation,eOpts){
                        store.proxy.extraParams.tipo=me.getCmp('tipo').getValue();
                      }
                    }
                  },
                  displayField: 'periodo',
                  valueField: 'id',
                  allowBlank: false,
                  forceSelection: true
                },

                {
                  xtype: 'combobox',
                  id: me._('formato_visualizar'),
                  name: 'formato_visualizar',
                  width: 340,
                  //hideTrigger: true,
                  fieldLabel: 'Formato',
                  labelAlign: 'top',
                  labelSeparator: '',
                  labelStyle: 'font-weight: bold;',
                  editable: false,
                  filterPickList: true,
                  multiSelect: false,
                  queryMode: "local",
                  tipTpl: '{denominacion}',
                  labelTpl: '{denominacion}',
                  tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{denominacion}</li></tpl></ul>',
                  store: {
                    fields: ['id','denominacion'],
                    data: [
                      {id: '0', denominacion: 'COMPLETO'},
                      {id: '1', denominacion: 'EXCLUIR CONCEPTOS'},
                      {id: '2', denominacion: 'SOLO CONCEPTOS (CONSOLIDADO)'},
                      {id: '3', denominacion: 'SOLO CONCEPTOS (SEPARADO)'},
                    ]
                  },
                  displayField: 'denominacion',
                  valueField: 'id',
                  allowBlank: false,
                  value: '0',
                  defaultValue: '0',
                  forceSelection: true,
                  listeners:{
                    change: function(){
                      var v=me.getCmp("formato_visualizar").getValue();
                      if(v=="0"){
                        me.getCmp('id_concepto_formato_visualizar').hide();
                      }
                      else{
                        me.getCmp('id_concepto_formato_visualizar').show();
                      }
                    }
                  }
                },

                {
                  xtype: 'tagfield',
                  id: me._('id_concepto_formato_visualizar'),
                  name: 'id_concepto_formato_visualizar',
                  width: 340,
                  //hideTrigger: true,
                  fieldLabel: 'Conceptos',
                  labelAlign: 'top',
                  labelSeparator: '',
                  labelStyle: 'font-weight: bold;',
                  editable: false,
                  filterPickList: true,
                  multiSelect: true,
                  queryMode: "local",
                  tipTpl: '<b>{codigo}</b> {concepto} <small>({fecha})</small>',
                  labelTpl: '{codigo} - {concepto}',
                  tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {concepto} <small>({tipo})</small></li></tpl></ul>',

                  store: {
                    fields: ['id','concepto'],
                    autoLoad: true,
                    pageSize: 1000,
                    proxy: {
                      type:'ajax',
                      url: 'module/nomina_concepto/',
                      actionMethods: {read: "POST"},
                      timeout: 3600000,
                      reader: {
                        type: 'json',
                        rootProperty: 'result',
                        totalProperty:'total'
                      },
                      extraParams: {
                        action: 'onList',
                        text: '',
                        sort: '[{"property": "concepto", "direction": "ASC"}]',
                        tipo: 'A'
                      }
                    },
                  },
                  displayField: 'concepto',
                  valueField: 'id',
                  allowBlank: false,
                  forceSelection: true
                },



              ]
            },
          ]
        },
      ]
    });
    me.internal.ventanaVisualizar.setInternal({itemSelection:0});
    //FIN VENTANA PARA VISUALIZAR REPORTES


    //VENTANA IMPORTACION DE CONCEPTOS DESDE EXCEL
    me.internal.ventanaConceptoImportar=Ext.create('Ext.window.Window', {
      title: 'Concepto - Importar Excel',
      minimizable: false,
      maximizable: true,
      modal: true,
      width: 850,
      height: 420,
      bodyStyle: 'background-color: #e8e8e8; border-color: #e8e8e8;',
      layout: 'fit',
      resizable: true,
      autoScroll: true,

      listeners: {
        beforeclose: function(w,o){
          me.internal.ventanaConceptoImportar.data_importar=[];
          me.internal.ventanaConceptoImportar.hide();
          return false;
        },
        beforeshow: function(){
          me.internal.ventanaConceptoImportar.data_importar=[];

          me.getCmp("archivo_excel_concepto_importar").setValue("");
          me.getCmp("archivo_excel_concepto_importar").setRawValue("");
          if(me.getCmp("archivo_excel_concepto_importar").fileInputEl && me.getCmp("archivo_excel_concepto_importar").fileInputEl.dom)
            me.getCmp("archivo_excel_concepto_importar").fileInputEl.dom.value = '';

          me.getCmp("container_paso1_concepto_importar").show();
          me.getCmp("container_paso2_concepto_importar").hide();
        },
        afterrender: function(){
          me.onConceptoImportarHeightPaso1();
        }
      },

      items:[
        {
          xtype: 'container',
          id: me._('container_paso1_concepto_importar'),
          anchor: '100%',
          layout: 'anchor',
          style: "padding: 5px 20px 0px 20px;",
          items: [
            {
              xtype:'combobox',
              id: me._('tipo_nomina_concepto_importar'),
              name: 'tipo_nomina_concepto_importar',
              fieldLabel: 'Tipo de Nómina/Periodo',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              anchor: '100%',
              queryMode: "local",
              store: {
                fields: ['tipo','denominacion'],
                data: me.internal.data.preload["periodo_tipo"]
              },
              displayField: 'denominacion',
              valueField: 'tipo',
              allowBlank: false,
              forceSelection: true,
              editable: false,
              value: '',
              listeners: {
                change: function(e, The, eOpts ){
                  var tipo = me.getCmp("tipo_nomina_concepto_importar").getValue();
                  var data_periodo=me.getDataPeriodo({tipo: tipo});
                  me.getCmp('id_periodo_concepto_importar').getStore().setData(data_periodo);
                  if(data_periodo.length>0){
                    console.log(data_periodo, data_periodo[data_periodo.length-1]["id"]);
                    me.getCmp("id_periodo_concepto_importar").setValue(data_periodo[data_periodo.length-1]["id"]);
                  }

                  var data_nomina=me.getDataNomina({tipo: tipo});
                  me.getCmp('id_nomina_concepto_importar').getStore().setData(data_nomina);
                  if(data_nomina.length>0){
                    me.getCmp("id_nomina_concepto_importar").setValue(Ext.Array.map(data_nomina,function(item,index,Array){return item["id"]}));
                  }
                }
              }
            },
            {
              xtype: 'combobox',
              id: me._('id_periodo_concepto_importar'),
              name: 'id_periodo_concepto_importar',
              anchor: '100%',
              fieldLabel: 'Periodo',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              editable: false,
              queryMode: "local",
              displayTpl: '<tpl for=".">{codigo} {descripcion}</tpl>',
              tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item"><b>{codigo}</b> {descripcion} <small>({fecha})</small></li></tpl></ul>',
              store: {
                fields: ['id','periodo'],
                data: []
              },
              displayField: 'periodo',
              valueField: 'id',
              allowBlank: true,
              forceSelection: true,
            },
            {
              xtype: 'tagfield',
              id: me._('id_nomina_concepto_importar'),
              name: 'id_nomina_concepto_importar',
              anchor: '100%',
              fieldLabel: 'Nómina <div class="enlace_etiqueta_nomina"><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onConceptoImportarNominaSeleccionarTodo()\" title="Seleccionar Todos">[&#x2714;]<a/><a class="enlace_etiqueta_nomina" href="#" onclick=\"siga.getCmp(\'nomina\').onConceptoImportarNominaSeleccionarNada()\" title="Seleccionar Ninguno">[&#x2716;]</a></div>',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              cls: 'seleccionar_nomina__nomina',
              editable: false,
              queryMode: "local",
              multiSelect: true,
              filterPickList: true,
              store: {
                fields: ['id','codigo_nomina'],
                data: []
              },
              displayField: 'codigo_nomina',
              valueField: 'id',
              allowBlank: true,
              forceSelection: true,
              hideTrigger:true,
              listeners: {
                change: function(){
                  me.onConceptoImportarHeightPaso1();
                }
              }
            },
            {
              xtype:'filefield',
              id: me._('archivo_excel_concepto_importar'),
              name: 'archivo_excel_concepto_importar',
              fieldLabel: 'Excel a Importar',
              labelAlign: 'top',
              labelSeparator: '',
              labelStyle: 'font-weight: bold;',
              anchor: '100%',
              value: '',
              accept: '.xls, .xlsx'
            },
            {
              xtype: 'container',
              anchor: '100%',
              layout: {
                type: 'hbox',
                align: 'middle'
              },
              style: 'padding-top: 25px; padding-bottom: 10px;',
              items: [
                {
                  xtype:'fieldset',
                  columnWidth: 0.5,
                  title: '<b>Formato de Pre-Carga</b>',
                  collapsible: false,
                  defaults: {anchor: '100%'},
                  layout: 'anchor',
                  items :[
                    {
                      xtype: 'checkbox',
                      id: me._('valor_actual_concepto_importar'),
                      boxLabel: 'Incluir Valores Actuales',
                      checked: false
                    },
                    {
                      xtype: 'button',
                      text: '<b>Descargar</b>',
                      margin: '5 0 5 0',
                      width: 180,
                      listeners: {
                        click: function(){
                          me.onConceptoImportarDescargarFormato();
                        }
                      }
                    },
                  ]
                },
                {
                  xtype: 'tbspacer',
                  flex: 1
                },
                {
                  xtype: 'button',
                  text: '<b><big>Pre-Cargar</big></b>',
                  scale: 'medium',
                  width: 150,
                  listeners: {
                    click: function(){
                      me.onConceptoImportarPreCargar();
                    }
                  }
                }
              ]
            }
          ]
        },
        {
          xtype: 'container',
          id: me._('container_paso2_concepto_importar'),
          anchor: '100%',
          layout: 'vbox',
          hidden: true,
          items: [
            {
              xtype: 'gridpanel',
              id: me._('grid_concepto_importar'),
              selType : 'cellmodel',
              columnLines: true,
              enableLocking: true,
              flex: 1,
              width: '100%',
              columns: [],
              cls: "nomina_hoja_trabajo",
              features:[{
                  ftype: 'grouping',
                  groupHeaderTpl: "<div class='nomina_grupo'><div class='text'>{name}</div></div>",
                  collapsible : false
                }
              ],
              viewConfig:{
                getRowClass: function(rec, rowIdx, params, store) {
                  if(rec.get('activo')=='f' || rec.get('activo_otra_nomina'))
                    return 'fila-inactiva';
                  return 'fila-activa';
                }
              },
              listeners: {
              },
            },
            {
              xtype: 'container',
              width: '100%',
              height: 40,
              layout: 'hbox',
              style: 'padding-top: 10px;',
              items: [
                {
                  xtype: 'tbspacer',
                  flex: 1
                },
                {
                  xtype: 'button',
                  text: '<b>Regresar</b>',
                  width: 150,
                  listeners: {
                    click: function(){
                      me.onConceptoImportarRegresar();
                    }
                  }
                },
                {
                  xtype: 'tbspacer',
                  width: 20
                },
                {
                  xtype: 'button',
                  text: '<b>Aplicar</b>',
                  width: 150,
                  listeners: {
                    click: function(){
                      me.onConceptoImportarAplicar();
                    }
                  }
                },
                {
                  xtype: 'tbspacer',
                  flex: 1
                }
              ]
            }
          ]
        }
      ]
    });

    //VENTANA IMPORTACION DE CONCEPTOS DESDE EXCEL
    console.log("concepto_identificadores",me.internal.data.preload["concepto_identificadores"]);
    me.internal.ventanaConfigurarProyeccion=Ext.create('Ext.window.Window', {
      title: 'Proyección - Configurar',
      minimizable: false,
      //maximizable: true,
      modal: true,
      width: 500,
      height: 300,
      bodyStyle: 'background-color: #e8e8e8; border-color: #e8e8e8;',
      layout: 'anchor',
      resizable: true,
      autoScroll: true,
      padding: "20 20 0 20",

      listeners: {
        beforeclose: function(w,o){
          
          me.internal.ventanaConfigurarProyeccion.hide();
          return false;
        },
        beforeshow: function(){
          var msgWait=Ext.Msg.wait('Cargando configuración. Por favor espere...', me.internal.ventanaConfigurarProyeccion.getTitle(),{text:''});
          msgWait.setAlwaysOnTop(true);
    
          Ext.Ajax.request({
            method: 'POST',
            url:'module/nomina/',
            params:{
              action: 'onConfiguracionProyeccion_Get'
            },
            success:function(request){
              msgWait.close();
              var result=Ext.JSON.decode(request.responseText);
              me.getCmp("identificador_proyeccion").setValue(result["identificador"]);
              me.getCmp("porcentaje_proyeccion").setValue(result["porcentaje"]*100);
            },
            failure:function(request){
              msgWait.close();
              Ext.MessageBox.alert("Proyección - Configurar","Error al buscar configuracion.");
              var result=Ext.JSON.decode(request.responseText);
            }
          });
        },
        afterrender: function(){
          //console.log("concepto_identificadores",me.internal.data.preload["concepto_identificadores"]);
        }
      },

      items:[

        {
          xtype: 'tagfield',
          id: me._('identificador_proyeccion'),
          name: 'identificador_proyeccion',
          anchor: '100%',
          fieldLabel: 'Conceptos <small>(Identificador)</small>',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          editable: false,
          queryMode: "local",
          multiSelect: true,
          filterPickList: true,
          store: {
            fields: ['identificador'],
            data: me.internal.data.preload["concepto_identificadores"],
            //data: [{identificador: "hola"}]
          },
          displayField: 'identificador',
          valueField: 'identificador',
          allowBlank: true,
          forceSelection: true
        },
        {
          xtype:'numberfield',
          id: me._('porcentaje_proyeccion'),
          name: 'porcentaje_proyeccion',
          anchor: '100%',
          fieldLabel: 'Proyección <small>(%)</small>',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          value: '',
          minValue: 0,
          allowDecimals: true,
          allowNegative: false
        },
        {
          xtype: 'container',
          anchor: '100%',
          layout: {
            type: 'hbox',
            align: 'middle'
          },
          style: 'padding-top: 25px; padding-bottom: 10px;',
          items: [
            {
              xtype: 'tbspacer',
              flex: 1
            },
            {
              xtype: 'button',
              text: '<b><big>Guardar</big></b>',
              scale: 'medium',
              width: 150,
              listeners: {
                click: function(){
                  var msgWait=Ext.Msg.wait('Guardando configuración. Por favor espere...', me.internal.ventanaConfigurarProyeccion.getTitle(),{text:''});
                  msgWait.setAlwaysOnTop(true);

                  Ext.Ajax.request({
                    method: 'POST',
                    url:'module/nomina/',
                    params:{
                      action: 'onConfiguracionProyeccion_Save',
                      'identificador[]': me.getCmp("identificador_proyeccion").getValue(),
                      porcentaje: (me.getCmp("porcentaje_proyeccion").getValue()/100.00).toFixed(2)
                    },
                    success:function(request){
                      msgWait.close();
                      me.internal.ventanaConfigurarProyeccion.hide();
                      var result=Ext.JSON.decode(request.responseText);
                      Ext.MessageBox.alert("Proyección - Configurar",result["message"]);
                    },
                    failure:function(request){
                      msgWait.close();
                      Ext.MessageBox.alert("Proyección - Configurar","Error al guardar datos.");
                      var result=Ext.JSON.decode(request.responseText);
                    }
                  });
                }
              }
            },
            {
              xtype: 'tbspacer',
              flex: 1
            }
          ]
        }



      ]
    });



    //Barra de herramientas
    me.internal.itemsToolbar=[
      {
        xtype: 'button',
        id: me._('btnSeleccionar'),
        height: 45,
        width: 55,
        text: 'Nóminas',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_seleccionar',
        iconAlign: 'top',
        tooltip: 'Seleccionar Nómina',
        listeners: {
            click: function(){
              //me.getCmp('id_nomina').getStore().load();
              //me.getCmp('id_periodo').getStore().load();
              me.internal.ventanaSeleccionarNomina.show();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnPersona'),
        width: 65,
        height: 45,
        text: 'Persona',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-persona',
        disabledCls: 'siga-btn-disabled',
        iconAlign: 'top',
        tooltip: 'Persona',
        menu: [
          {
            id: me._('btnPersonaAgregar'),
            text: 'Agregar',
            menu: []
          },
          {
            id: me._('btnPersonaQuitar'),
            text: 'Quitar',
            listeners: {
              click: function(){
                me.onPersonaQuitar();
              }
            }
          },
          {
            id: me._('btnPersonaCambiarCargo'),
            text: 'Cambiar Cargo',
            menu: []
          },
          {
            id: me._('btnPersonaCambiarNomina'),
            text: 'Cambiar Nómina',
            menu: []
          },
          {
            id: me._('btnPersonaCambiarEP'),
            text: 'Cambiar Estructura Presupuestaria',
            menu: []
          },
          {
            id: me._('btnPersonalInactivo'),
            text: 'Personal Inactivo',
            menu: [
              {
                id: me._('btnPersonalInactivoQuitar'),
                text: 'Quitar de Nómina(s) Actual(es)',
                listeners: {
                  click: function(){
                    Ext.MessageBox.confirm(
                      "Personal Inactivo - Quitar de Nómina(s) Actual(es)",
                      '<b>\u00BFEst\u00e1 seguro quitar a todo el personal inactivo de la nómina actual?</b><br> ',
                      function(btn,text){
                        if(btn == 'yes'){
                          var id_nomina=me.getCmp('id_nomina').getValue().join(',');
                          var id_periodo=me.getCmp('id_periodo').getValue();

                          me.menuPersona({disabled: true});
                          me.menuConcepto({disabled: true});
                          me.menuPeriodo({disabled: true});
                          me.getCmp('btnContabilizar').setDisabled(true);

                          Ext.Ajax.request({
                            method: 'POST',
                            url:'module/nomina/',
                            params:{
                              action: 'onPersona_QuitarInactivo',
                              id_periodo: id_periodo,
                              id_nomina: id_nomina
                            },
                            success:function(request){
                              var result=Ext.JSON.decode(request.responseText);
                              Ext.MessageBox.alert("Personal Inactivo - Quitar Nómina Actual",result["message"]);
                              me.onRecargar();
                            },
                            failure:function(request){
                              var result=Ext.JSON.decode(request.responseText);
                            }
                          });
                        }
                      });
                  }
                }
              },
              {
                id: me._('btnPersonalInactivoCambiarNomina'),
                text: 'Cambiar Nómina',
                menu: []
              },
            ]
          },
          {
            text: 'Administrar Fichas',
            listeners: {
              click: function(){
                siga.open("ficha");
              }
            }
          },
          {
            text: 'Administrar Escala Salarial',
            listeners: {
              click: function(){
                siga.open("nomina_escala_salarial");
              }
            }
          }
        ]
      },
      {
        xtype: 'button',
        id: me._('btnConcepto'),
        width: 65,
        height: 45,
        text: 'Concepto',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_concepto',
        iconAlign: 'top',
        tooltip: 'Concepto',
        menu: [
          {
            text: 'Agregar',
            id: me._('btnConceptoAgregar'),
            listeners: {
              click: function(){
                if(!me.onNominaSeleccionada())
                  return;

                var ids=[];
                for(var i=0;i<me.internal.data.concepto.length;i++)
                  ids[i]=me.internal.data.concepto[i]["id_concepto"];

                var selector=Ext.create("siga.windowSelect", {
                  internal: {
                    parent: {
                      fieldLabel: 'Agregar Concepto',
                      internal:{
                        page:1,
                        limit: 100,
                        valueField: 'id',
                        columns: {
                          field: ["codigo","concepto","identificador","tipo"],
                          title: ["Código","Concepto","Identificador","Tipo"],
                          width: ['10%','60%','25%','5%'],
                          sort:  ["ASC",'NULL','NULL','NULL'],
                          align: ["center","left","left","center"],
                        },
                        url: 'module/nomina_concepto_periodo/',
                        extraParams: {
                          ids: Ext.JSON.encode(ids)
                        },
                        actionOnList: "onList_Agregar",
                        actionOnGet: 'onGet',
                      },
                      setValue: function(id_concepto){
                        var id_nomina=me.getCmp('id_nomina').getValue().join(",");
                        var id_periodo=me.getCmp('id_periodo').getValue();

                        var resp=Ext.Ajax.request({
                          async: false,
                          url: 'module/nomina_concepto_periodo/',
                          params: {
                            action: "onAgregar",
                            id_nomina: id_nomina,
                            id_periodo: id_periodo,
                            id_concepto: id_concepto
                          }
                        });
                        if(resp.statusText=="OK"){
                          me.onRecargar();
                        }
                        return true;
                      }
                    }
                  }
                });
                selector.show();
                selector.search();
              }
            }
          },
          {
            text: 'Quitar',
            id: me._('btnConceptoQuitar'),
            listeners: {
              click: function(){
                if(me.internal.columnaSeleccionada==null)
                  return;
                var id_nomina=me.getCmp('id_nomina').getValue().join(",");
                var id_periodo=me.getCmp('id_periodo').getValue();
                var id_concepto=me.internal.columnaSeleccionada.dataIndex;

                var resp=Ext.Ajax.request({
                  async: false,
                  url: 'module/nomina_concepto_periodo/',
                  params: {
                    action: "onQuitar",
                    id_nomina: id_nomina,
                    id_periodo: id_periodo,
                    id_concepto: id_concepto
                  }
                });
                if(resp.statusText=="OK"){
                  me.onRecargar();
                }
              }
            }
          },
          {
            text: 'Importar desde Excel',
            id: me._('btnConceptoImportar'),
            listeners: {
              click: function(){
                var id_nomina=me.getCmp('id_nomina').getValue().join(",");
                var id_periodo=me.getCmp('id_periodo').getValue();

                me.internal.ventanaConceptoImportar.show();
                //me.onConceptoImportarCargarGrid();
              }
            }
          },
          {
            text: 'Administrar',
            listeners: {
              click: function(){
                siga.open("nomina_concepto");
              }
            }
          }
        ]
      },
      {
        xtype: 'button',
        id: me._('btnRecargar'),
        height: 45,
        width: 55,
        text: 'Recargar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-reload',
        iconAlign: 'top',
        tooltip: 'Recargar',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onRecargar();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnNotas'),
        height: 45,
        width: 55,
        text: 'Notas',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nota',
        iconAlign: 'top',
        tooltip: 'Notas',
        listeners: {
            click: function(){
              //me.setMessage("");
              me.onNota();
            }
        }
      },
      {
        xtype: 'button',
        id: me._('btnCerrarPeriodo'),
        height: 45,
        width: 55,
        text: 'Período',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-cerrar_periodo',
        iconAlign: 'top',
        tooltip: 'Período',
        menu: [
          {
            text: 'Cerrar',
            id: me._('btnPeriodoCerrar'),
            listeners: {
              click: function(){
                me.onCerrarPeriodo();
              }
            }
          },
          {
            text: 'Administrar',
            listeners: {
              click: function(){
                siga.open("nomina_periodo");
              }
            }
          }
        ]
      },
      {
        xtype: 'button',
        id: me._('btnContabilizar'),
        height: 45,
        width: 65,
        text: 'Contabilizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-contabilizar',
        iconAlign: 'top',
        tooltip: 'Contabilizar Período',
        menu: [
          {
            id: me._('btnContabilizar_CCP'),
            text: 'Generar Comprometido/Causado/Pagado',
            listeners: {
              click: function(){
                me.onContabilizar("CCP");
              }
            }
          },
          {
            id: me._('btnContabilizar_CC'),
            text: 'Generar Comprometido/Causado',
            listeners: {
              click: function(){
                me.onContabilizar("CC");
              }
            }
          },
          {
            id: me._('btnContabilizar_P'),
            text: 'Generar Pagado - Banco',
            listeners: {
              click: function(){
                me.onContabilizar("P");
              }
            }
          },
          {
            id: me._('btnContabilizar_P_CXC'),
            text: 'Generar Pagado - CxC',
            listeners: {
              click: function(){
                me.onContabilizar("P-CXC");
              }
            }
          },
          {
            id: me._('btnContabilizar_CPP_NO_AP'),
            text: 'Generar Comprometido/Causado/Pagado [Sin Aportes Patronales]',
            listeners: {
              click: function(){
                me.onContabilizar("CCP-!AP");
              }
            }
          },
          {
            id: me._('btnContabilizar_CPP_AP'),
            text: 'Generar Comprometido/Causado/Pagado [Aportes Patronales]',
            listeners: {
              click: function(){
                me.onContabilizar("CCP-AP");
              }
            }
          },
        ]
      },
      {
        xtype: 'tbspacer',
        flex: 1
      },
      {
        xtype: 'button',
        id: me._('btnVisualizar'),
        width: 65,
        height: 45,
        text: 'Visualizar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-display',
        iconAlign: 'top',
        tooltip: 'Visualizar',
        internal: {
          reporte: ""
        },
        menu: [
          {
            text: 'Nómina',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_xls_v2";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Nómina");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Recibos de Pago',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_recibo_pago";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Recibos de Pago");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Listado de Firmas',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_firma";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Listado de Firmas");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Listado de Conceptos',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_concepto";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Listado de Conceptos");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'TXT Banco (Formato A)',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_banco_xls_formato_a";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - TXT Banco (Formato A)");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'TXT Banco (Formato B - Nuevo)',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_banco_xls_formato_b";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - TXT Banco (Formato B - Nuevo)");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'TXT Banco (Formato C - Patria)',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_banco_xls_formato_c";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - TXT Banco (Formato C - patria)");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Resumen Presupuestario/Contable',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_resumen_presupuestario_contable";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Resumen Presupuestario/Contable");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Resumen de Deducciones y Aportes Patronales',
            listeners: {
              click: function(){
                me.getCmp('btnVisualizar').internal.reporte="nomina_listado_aportes";
                me.getCmp('btnVisualizar').internal.proyeccion=false;
                me.internal.ventanaVisualizar.setInternal({itemSelection: null});
                me.internal.ventanaVisualizar.setTitle("Visualizar - Resumen de Deducciones y Aportes Patronales");
                me.internal.ventanaVisualizar.show();
              }
            }
          },
          {
            text: 'Comprobante Presupuestario/Contable Generado',
            listeners: {
              click: function(){
                if(!me.onNominaSeleccionada()) return;
                if(me.internal.contabilizado==null){
                  Ext.Msg.alert(me.title,"La nómina no se encuentra contabilizada.");
                  return;
                }

                window.open("report/comprobante.php?id="+
                  me.internal.contabilizado+
                  (me.internal.contabilizado_pagado?(","+me.internal.contabilizado_pagado):"")+
                  (me.internal.contabilizado_ap?","+me.internal.contabilizado_ap:"")
                );
              }
            }
          },
          {
            text: 'Proyección',
            menu: [
              {
                text: 'Nómina',
                listeners: {
                  click: function(){
                    me.getCmp('btnVisualizar').internal.reporte="nomina_xls_v2";
                    me.getCmp('btnVisualizar').internal.proyeccion=true;
                    me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                    me.internal.ventanaVisualizar.setTitle("Visualizar Proyección - Nómina");
                    me.internal.ventanaVisualizar.show();
                  }
                }
              },
              {
                text: 'Resumen Presupuestario/Contable',
                listeners: {
                  click: function(){
                    me.getCmp('btnVisualizar').internal.reporte="nomina_resumen_presupuestario_contable";
                    me.getCmp('btnVisualizar').internal.proyeccion=true;
                    me.internal.ventanaVisualizar.setInternal({itemSelection: 1});
                    me.internal.ventanaVisualizar.setTitle("Visualizar Proyección - Resumen Presupuestario/Contable");
                    me.internal.ventanaVisualizar.show();
                  }
                }
              },
              {
                xtype: 'menuseparator',
              },
              {
                text: 'Configurar',
                listeners: {
                  click: function(){
                    me.internal.ventanaConfigurarProyeccion.show();
                  }
                }
              }
            ]
          },

        ]
      },
    ];
    //FIN Barra de herramientas


    me.internal.items=[
      {
        xtype: 'gridpanel',
        id: me._('gridList'),
        border: 0,
        preventHeader: true,
        cls: "nomina_hoja_trabajo",
        width: 400,
        viewConfig: {
          markDirty: false
        },
        features:[
          {
            ftype: 'grouping',//onclick=\"siga.getCmp('nomina').onAddPersona({id_nomina})\"
            groupHeaderTpl: "<div class='nomina_grupo'><div class='text'>{name}</div></div>",
            collapsible : false
          }
        ],
        plugins: {
          ptype: 'cellediting',
          clicksToEdit: 2,
          listeners: {
            edit: function(editor, e){
              if(e.value*1.0==e.originalValue*1.0)
                return;
              e.record.commit();
              _tmp=Ext.Ajax.request({
                async: false,
                url:"module/nomina/",
                params: {
                  action: 'onSave',
                  //id_nomina: me.getCmp("id_nomina").getValue(),
                  id_nomina: e.record.get('id_nomina'),
                  id_periodo: me.getCmp("id_periodo").getValue(),
                  data: Ext.JSON.encode([{id_ficha: e.record.get("id_ficha"), id_concepto: e.field, valor: e.value}])
                }
              });

              if(_tmp.statusText=="OK"){
                var data=Ext.JSON.decode(_tmp.responseText);
                me.onActualizarFilaFicha(e.record,data[0]);
              }
            },
          }
        },
        selType : 'cellmodel',
        columnLines: true,
        enableLocking: true,
        height: 380,
        columns: [],
        viewConfig:{
          getRowClass: function(rec, rowIdx, params, store) {
            if((rec.get('activo')=='f' || rec.get('activo_otra_nomina')) && !me.internal.cerrado)
              return 'fila-inactiva';
            return 'fila-activa';
          }
        },
        listeners: {
          afterrender:function(){
            var me=this;
            var view = this.getView();
            //para corregir error al hacer scroll, prueba nuevamente con una version nueva de extjs para verficar coreccion de bug.
            view.normalView.on("scroll", function (e, t) {
              Ext.getDom(view.lockedView.id).scrollTop = Ext.getDom(view.normalView.id).scrollTop;
              //view.lockedView.el.dom.scrollTop=view.normalView.el.dom.scrollTop;
            });

            view.lockedView.getEl().on("scroll", function (e, t) {
              Ext.getDom(view.normalView.id).scrollTop = Ext.getDom(view.lockedView.id).scrollTop;
              //view.normalView.el.dom.scrollTop=view.lockedView.el.dom.scrollTop;
            });
          },

          columnmove: function(ct, column, fromIdx, toIdx, eOpts ){
          },
          cellcontextmenu: function(dataview, td, cellIndex, record, tr, rowIndex, e, eOpts ){
            e.stopEvent();
            e.stopPropagation();
            if(me.internal.cerrado==true){
              return;
            }

            var columna=dataview.getGridColumns();
            var id_concepto=columna[cellIndex].dataIndex;
            //verificar si la columna corresponde a un concepto, si es un numero es concepto
            if(!Ext.isNumeric(id_concepto)) {
              return;
            }
            //var id_nomina=me.getCmp('id_nomina').getValue();
            var id_periodo   = me.getCmp('id_periodo').getValue();
            var id_nomina    = record.get('id_nomina');
            var id_ficha     = record.get('id_ficha');
            var menu_celda = Ext.create('Ext.menu.Menu', {
              items: [
                {
                  text: 'Agregar',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAdd',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode([id_ficha]),
                        id_concepto: id_concepto
                      }
                    });

                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.onActualizarFilaFicha(record,data[0]);
                    }
                  }
                },
                {
                  text: 'Agregar (Valor Según Ficha)',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAddValorFicha',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode([id_ficha]),
                        id_concepto: id_concepto
                      }
                    });

                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.onActualizarFilaFicha(record,data[0]);
                    }
                  }
                },
                {
                  text: 'Quitar',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onRemove',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        id_ficha: Ext.JSON.encode([id_ficha]),
                        id_concepto: id_concepto
                      }
                    });
                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.onActualizarFilaFicha(record,data[0]);
                    }
                  }
                },
                {
                  xtype: "menuseparator"
                },
                {
                  text: 'Agregar a Todos',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAdd',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        //id_ficha: Ext.JSON.encode(['*']),
                        id_ficha: Ext.JSON.encode((me.filtro_ficha_id && me.filtro_ficha_id.length>0)?me.filtro_ficha_id:['*']),
                        id_concepto: id_concepto
                      }
                    });

                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.getCmp('gridList').getStore().load();
                    }
                  }
                },
                {
                  text: 'Agregar a Todos (Valor Según Ficha)',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onAddValorFicha',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        //id_ficha: Ext.JSON.encode(['*']),
                        id_ficha: Ext.JSON.encode((me.filtro_ficha_id && me.filtro_ficha_id.length>0)?me.filtro_ficha_id:['*']),
                        id_concepto: id_concepto
                      }
                    });

                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.getCmp('gridList').getStore().load();
                    }
                  }
                },
                {
                  text: 'Quitar a Todos',
                  handler: function() {
                    _tmp=Ext.Ajax.request({
                      async: false,
                      url:"module/nomina/",
                      params: {
                        action: 'onRemove',
                        id_nomina: id_nomina,
                        id_periodo: id_periodo,
                        //id_ficha: Ext.JSON.encode(['*']),
                        id_ficha: Ext.JSON.encode((me.filtro_ficha_id && me.filtro_ficha_id.length>0)?me.filtro_ficha_id:['*']),
                        id_concepto: id_concepto
                      }
                    });
                    if(_tmp.statusText=="OK"){
                      var data=Ext.JSON.decode(_tmp.responseText);
                      me.getCmp('gridList').getStore().load();
                    }
                  }
                },

              ]
            });
            menu_celda.show();
            menu_celda.showAt(Ext.get(td).getX(), Ext.get(td).getY()-menu_celda.getHeight());
          }
        }
      }
    ];




    me.dockedItems=[
      {
        xtype: 'toolbar',
        id: me._('toolbarMain'),
        style: 'margin: 0px; padding: 0px; border-bottom: 0;',
        flex: 1,
        dock: 'top',
        items: me.internal.itemsToolbar
      },
      {
        xtype: 'toolbar',
        id: me._('toolbarFilter'),
        style: 'margin: 0px; padding: 0px; border-top: 0;padding-top: 2px; padding-bottom: 3px;',
        cls: 'toolbar-filter',
        flex: 1,
        dock: 'top',
        layout: {
            type: 'hbox',
            align: 'begin'
        },
        items: [
          {
            xtype:'label',
            html: '<img src="module/nomina/image/icon-persona_buscar.svg" width="22" height="22" style="margin-left:3px;" />',
            width: 25,
            height: 22,
          },
          {
            xtype:'textfield',
            id: me._('filtro_busqueda_cedula'),
            name: 'filtro_busqueda_cedula',
            fieldLabel: 'Cédula',
            labelWidth: 30,
            width: 120,
            //labelAlign: 'top',
            //labelSeparator: '',
            labelStyle: 'font-weight: 500;font-size:9px;',
            value: '',
            listeners:{
              specialkey: function(field, e){
                if (e.getKey() == e.ENTER){
                  me.onFiltroBusqueda();
                }
              }
            }
          },

          {
            xtype:'textfield',
            id: me._('filtro_busqueda_nombre_apellido'),
            name: 'filtro_busqueda_nombre_apellido',
            fieldLabel: 'Nombres/Apellidos',
            labelWidth: 75,
            width: 220,
            //labelAlign: 'top',
            //labelSeparator: '',
            labelStyle: 'font-weight: 500;font-size:9px;',
            value: '',
            listeners:{
              specialkey: function(field, e){
                if (e.getKey() == e.ENTER){
                  me.onFiltroBusqueda();
                }
              }
            }
          },

          {
            xtype: 'tagfield',
            id: me._('filtro_busqueda_id_cargo'),
            name: 'filtro_busqueda_id_cargo',
            //hideTrigger: true,
            fieldLabel: 'Cargo',
            //labelAlign: 'top',
            //labelSeparator: '',
            labelWidth: 25,
            width: 200,
            labelStyle: 'font-weight: 500;font-size:9px;',
            editable: false,
            filterPickList: true,
            multiSelect: true,
            queryMode: "local",
            tipTpl: '{cargo}',
            labelTpl: '{cargo}',
            tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{cargo}</li></tpl></ul>',
            store: {
              fields: ['id','cargo'],
              data: []
            },
            displayField: 'cargo',
            valueField: 'id',
            allowBlank: true,
            forceSelection: true
          },

          {
            xtype: 'tagfield',
            id: me._('filtro_busqueda_id_escala_salarial'),
            name: 'filtro_busqueda_id_escala_salarial',
            //hideTrigger: true,
            fieldLabel: 'Escala',
            labelWidth: 25,
            width: 200,
            //labelAlign: 'top',
            //labelSeparator: '',
            labelStyle: 'font-weight: 500;font-size:9px;',
            editable: false,
            filterPickList: true,
            multiSelect: true,
            queryMode: "local",
            tipTpl: '{escala}',
            labelTpl: '{escala}',
            tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{escala}</li></tpl></ul>',
            store: {
              fields: ['id','escala'],
              autoLoad: true,
              //pageSize: 1000,
              proxy: {
                type:'ajax',
                url: 'module/nomina_escala_salarial/',
                actionMethods: {read: "POST"},//actionMethods:'POST',
                timeout: 3600000,
                reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
                },
                extraParams: {
                  action: 'onList',
                  text: '',
                  start: 0,
                  limit: 'ALL',
                  sort: '[{"property": "escala", "direction": "ASC"}]'
                }
              }
            },
            displayField: 'escala',
            valueField: 'id',
            allowBlank: true,
            forceSelection: true
          },

          {
            xtype: 'tagfield',
            id: me._('filtro_busqueda_ingreso_mes'),
            name: 'filtro_busqueda_ingreso_mes',
            //hideTrigger: true,
            fieldLabel: 'Ingreso',
            //labelAlign: 'top',
            //labelSeparator: '',
            labelWidth: 30,
            width: 200,
            minWidth: 200,
            flex:1,
            labelStyle: 'font-weight: 500;font-size:9px;',
            editable: false,
            filterPickList: true,
            multiSelect: true,
            queryMode: "local",
            tipTpl: '{denominacion}',
            labelTpl: '{denominacion}',
            tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{denominacion}</li></tpl></ul>',
            store: {
              fields: ['id','denominacion'],
              data: [
                {id: '01', denominacion: 'Enero'},
                {id: '02', denominacion: 'Febrero'},
                {id: '03', denominacion: 'Marzo'},
                {id: '04', denominacion: 'Abril'},
                {id: '05', denominacion: 'Mayo'},
                {id: '06', denominacion: 'Junio'},
                {id: '07', denominacion: 'Julio'},
                {id: '08', denominacion: 'Agosto'},
                {id: '09', denominacion: 'Septiembre'},
                {id: '10', denominacion: 'Octubre'},
                {id: '11', denominacion: 'Noviembre'},
                {id: '12', denominacion: 'Diciembre'}
              ]
            },
            displayField: 'denominacion',
            valueField: 'id',
            allowBlank: true,
            forceSelection: true
          },

          {
            xtype: 'combobox',
            id: me._('filtro_busqueda_estatus'),
            name: 'filtro_busqueda_estatus',
            //hideTrigger: true,
            fieldLabel: 'Estatus',
            labelWidth: 30,
            width: 120,
            //labelAlign: 'top',
            //labelSeparator: '',
            labelStyle: 'font-weight: 500;font-size:9px;',
            editable: false,
            filterPickList: true,
            multiSelect: false,
            queryMode: "local",
            tipTpl: '{estatus}',
            labelTpl: '{estatus}',
            tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{estatus}</li></tpl></ul>',
            store: {
              fields: ['id','estatus'],
              data: [
                {id: 'T', estatus: 'TODOS'},
                {id: 'A', estatus: 'ACTIVOS'},
                {id: 'I', estatus: 'INACTIVOS'},
              ]
            },
            displayField: 'estatus',
            valueField: 'id',
            allowBlank: false,
            value: 'T',
            defaultValue: 'T',
            forceSelection: true
          },

          {
            xtype: 'button',
            text: 'Limpiar',
            width: 50,
            listeners: {
              click: function(){
                me.getCmp("filtro_busqueda_cedula").setValue("");
                me.getCmp("filtro_busqueda_nombre_apellido").setValue("");
                me.getCmp("filtro_busqueda_id_cargo").setValue("");
                me.getCmp("filtro_busqueda_id_escala_salarial").setValue("");
                me.getCmp("filtro_busqueda_ingreso_mes").setValue("");
                me.getCmp("filtro_busqueda_estatus").setValue("T");
                me.onFiltroBusqueda();
              }
            }
          },
          {
            xtype: 'button',
            text: 'Buscar',
            width: 50,
            listeners: {
              click: function(){
                me.onFiltroBusqueda();
              }
            }
          },
        ]
      },
      {
        xtype: 'pagingtoolbar',
        id: me._('pagingList'),
        afterPageText: 'de {0}',
        beforePageText: 'P\u00e1gina',
        displayInfo: true,
        displayMsg: 'Mostrando {0} - {1} de {2}',
        emptyMsg: 'No hay datos que mostrar',
        firstText: 'Primera p\u00e1gina',
        dock: 'bottom'
      },
      {
        xtype: 'toolbar',
        style: 'margin: 0px; padding: 0px;',
        flex: 1,
        dock: 'bottom',
        items: [
          {
            xtype: 'label',
            id: me._('lblNominaActual'),
            style:'padding: 2px;',
            html: '&nbsp;'
          },
          {
            xtype: 'tbspacer',
            flex: 1
          }
        ]
      },
    ];

    me.items=[
      {
        xtype: 'form',
        frame: false,
        id: me._('tab_data'),
        frameHeader: false,
        autoScroll:true,
        layout: 'fit',
        defaults: {
          style:'margin: 0px 0px 0px 0px;',
          labelAlign: 'top',
          labelSeparator: '',
          labelStyle: 'font-weight: bold;',
          hideLabel: false,
          width: 200
        },
        items: me.internal.items
      }
    ];



    me.callParent(arguments);
  },

  onLoad_MenuPersonaCambiarCargo: function(){
    var me=this;

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onList_Cargo',
        text: '',
        start: 0,
        limit: 'ALL',
        sort: '[{"property": "denominacion", "direction": "ASC"}]'
      },
      success:function(request){
        if(!request.responseText) return;
        var result=Ext.JSON.decode(request.responseText);
        result=result["result"];

        me.getCmp('filtro_busqueda_id_cargo').getStore().setData(result);

        for(var i=0;i<result.length;i++){
          me.getCmp("btnPersonaCambiarCargo").menu.add({
            text: result[i]["denominacion"],
            internal:{id: result[i]["id"]},
            listeners: {
              click: function(el){
                var seleccion=me.getCmp("gridList").getSelection();
                if(seleccion.length==0){
                  Ext.MessageBox.alert("Persona - Cambiar Cargo","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
                  return;
                }
                if(seleccion.length!=1){
                  Ext.MessageBox.alert("Persona - Cambiar Cargo","<b>Debe seleccionar solo una persona del listado.</b>");
                  return;
                }

                Ext.MessageBox.confirm(
                  "Persona - Cambiar Cargo",
                  '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["nacionalidad"]+seleccion[0].data["cedula"]+" "+seleccion[0].data["nombre_apellido"]+'" para el cargo: "'+el.text+'"?</b><br> ',
                  function(btn,text){
                    if(btn == 'yes'){
                      var id_ficha=seleccion[0].data["id_ficha"];
                      var id_periodo=me.getCmp("id_periodo").getValue();
                      var id_cargo=el.internal.id;


                      me.menuPersona({disabled: true});
                      me.menuConcepto({disabled: true});
                      me.menuPeriodo({disabled: true});
                      //me.getCmp('btnContabilizar').setDisabled(true);

                      Ext.Ajax.request({
                        method: 'POST',
                        url:'module/nomina/',
                        params:{
                          action: 'onPersona_CambiarCargo',
                          id_ficha: id_ficha,
                          id_periodo: id_periodo,
                          id_cargo: id_cargo
                        },
                        success:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                          Ext.MessageBox.alert("Persona - Cambiar Cargo",result["message"]);
                          me.onRecargar();
                        },
                        failure:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                        }
                      });
                    }
                  });

              }
            }
          });
        }

      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });



  },

  onLoad_MenuPersonaCambiarEP: function(){
    var me=this;

    Ext.Ajax.request({
      method: 'POST',
      url:'module/estructura_presupuestaria/',
      params:{
        action: 'onList_APNomina',
        text: '',
        start: 0,
        limit: 'ALL',
        sort: '[{"property": "anio", "direction": "DESC"}, {"property": "estructura_presupuestaria", "direction": "ASC"}]'
      },
      success:function(request){
        if(!request) return;
        var result=Ext.JSON.decode(request.responseText);
        result=result["result"];

        for(var i=0;i<result.length;i++){

          var opcion_menu = {
            text: result[i]["estructura_presupuestaria"],
            tooltip: "<b>Acción Centralizada:</b> "+result[i]["denominacion_centralizada"]+"<br><b>Específica:</b> "+result[i]["denominacion_especifica"]+"<br><b>Sub-Específica:</b> "+result[i]["denominacion_subespecifica"],
            internal:{id: result[i]["id_accion_subespecifica"]},
            listeners: {
              click: function(el){
                var seleccion=me.getCmp("gridList").getSelection();
                if(seleccion.length==0){
                  Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
                  return;
                }
                if(seleccion.length!=1){
                  Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria","<b>Debe seleccionar solo una persona del listado.</b>");
                  return;
                }

                Ext.MessageBox.confirm(
                  "Persona - Cambiar Estructura Presupuestaria",
                  '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["nacionalidad"]+seleccion[0].data["cedula"]+" "+seleccion[0].data["nombre_apellido"]+'" para la Estructura Presupuestaria: "'+el.text+'"?</b><br> ',
                  function(btn,text){
                    if(btn == 'yes'){
                      var id_ficha=seleccion[0].data["id_ficha"];
                      var id_periodo=me.getCmp("id_periodo").getValue();
                      var id_accion_subespecifica=el.internal.id;

                      me.menuPersona({disabled: true});
                      me.menuConcepto({disabled: true});
                      me.menuPeriodo({disabled: true});
                      //me.getCmp('btnContabilizar').setDisabled(true);

                      Ext.Ajax.request({
                        method: 'POST',
                        url:'module/nomina/',
                        params:{
                          action: 'onPersona_CambiarEP',
                          id_ficha: id_ficha,
                          id_periodo: id_periodo,
                          id_accion_subespecifica: id_accion_subespecifica
                        },
                        success:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                          Ext.MessageBox.alert("Persona - Cambiar Estructura Presupuestaria",result["message"]);
                          me.onRecargar();
                        },
                        failure:function(request){
                          var result=Ext.JSON.decode(request.responseText);
                        }
                      });

                    }
                  });

              }
            }
          };

          if(siga.value("anio")!=result[i]["anio"]){
            if(!me.getCmp("btnPersonaCambiarEP_"+result[i]["anio"])){
              me.getCmp("btnPersonaCambiarEP").menu.add({
                id: me._("btnPersonaCambiarEP_"+result[i]["anio"]),
                text: result[i]["anio"],
                menu: []
              });
            }
            me.getCmp("btnPersonaCambiarEP_"+result[i]["anio"]).menu.add(opcion_menu);
          }
          else {
            me.getCmp("btnPersonaCambiarEP").menu.add(opcion_menu);
          }

        }

      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });



  },

  onUpdate_MenuPersonaCambiarNomina: function(){
    var me=this;
    //var nomina=me.getCmp("id_nomina").getStore().getData().items;
    var nomina=me.internal.data.preload["nomina"];
    me.getCmp("btnPersonaAgregar").menu.removeAll();
    me.getCmp("btnPersonaCambiarNomina").menu.removeAll();
    me.getCmp("btnPersonalInactivoCambiarNomina").menu.removeAll();
    //console.log("onUpdate_MenuPersonaCambiarNomina->nomina: ",nomina);
    for(var i=0;i<nomina.length;i++){
      //if(me.getCmp("id_nomina").getValue().join(",")==nomina[i].data["id"]) continue; //no mostrar la misma nómina seleccionada
      //console.log(nomina[i]);
      if($.inArray(nomina[i]["id"],me.getCmp("id_nomina").getValue())>=0){
        console.log("agregar persona");
        me.getCmp("btnPersonaAgregar").menu.add({
          text: nomina[i]["codigo_nomina"],
          //internal:{id: nomina[i]["id"]},
          internal:{id: nomina[i]["id"]},
          listeners: {
            click: function(el){
              if(!me.onNominaSeleccionada())
                return;

              var id_nomina=el.internal.id;
              var id_periodo=me.getCmp('id_periodo').getValue();

              var selector=Ext.create("siga.windowSelect", {
                internal:{
                  parent: {
                    fieldLabel: 'Agregar Persona',
                    internal:{
                      page:1,
                      limit: 100,
                      valueField: 'id',
                      columns: {
                        field: ["identificacion","denominacion"],
                        title: ["Código","Concepto"],
                        width: ['30%','60%'],
                        sort:  ["ASC",'ASC'],
                        align: ["left","left"],
                      },
                      url: 'module/ficha/',
                      extraParams: {
                        id_nomina: id_nomina,
                        id_periodo: id_periodo
                      },
                      actionOnList: "onList_Agregar",
                      actionOnGet: 'onGet',
                    },
                    setValue: function(id_ficha){
                      console.log("id_ficha="+id_ficha);
                      var resp=Ext.Ajax.request({
                        async: false,
                        url: 'module/ficha/',
                        params: {
                          action: "onAgregar",
                          id_nomina: id_nomina,
                          id_periodo: id_periodo,
                          id_ficha: id_ficha

                        }
                      });
                      if(resp.statusText=="OK"){
                        me.onRecargar();
                      }
                      return true;
                    }
                  }
                }
              });
              selector.show();
              selector.search();
            }
          }
        });
      }

      me.getCmp("btnPersonaCambiarNomina").menu.add({
        text: nomina[i]["codigo_nomina"],
        internal:{id: nomina[i]["id"]},
        listeners: {
          click: function(el){
            var seleccion=me.getCmp("gridList").getSelection();
            if(seleccion.length==0){
              Ext.MessageBox.alert("Persona - Cambiar Nómina","<b>Debe seleccionar la persona a la cual va a realizar el cambio.</b>");
              return;
            }
            if(seleccion.length!=1){
              Ext.MessageBox.alert("Persona - Cambiar Nómina","<b>Debe seleccionar solo una persona del listado.</b>");
              return;
            }
            if(seleccion[0].data["id_nomina"]==el.internal.id){
              Ext.MessageBox.alert('Persona - Cambiar Nómina','<b>La persona ya se encuentra en la nómina "'+el.text+'".</b>');
              return;
            }

            Ext.MessageBox.confirm(
              "Persona - Cambiar Nómina",
              '<b>\u00BFEst\u00e1 seguro cambiar a la persona: "'+seleccion[0].data["nacionalidad"]+seleccion[0].data["cedula"]+" "+seleccion[0].data["nombre_apellido"]+'" para la nomina: "'+el.text+'"?</b><br> ',
              function(btn,text){
                if(btn == 'yes'){
                  var id_ficha=seleccion[0].data["id_ficha"];
                  var id_periodo=me.getCmp("id_periodo").getValue();
                  //var id_nomina_anterior=me.getCmp("id_nomina").getValue();
                  var id_nomina_anterior=seleccion[0].data["id_nomina"];
                  var id_nomina=el.internal.id;

                  me.menuPersona({disabled: true});
                  me.menuConcepto({disabled: true});
                  me.menuPeriodo({disabled: true});
                  //me.getCmp('btnContabilizar').setDisabled(true);

                  Ext.Ajax.request({
                    method: 'POST',
                    url:'module/nomina/',
                    params:{
                      action: 'onPersona_CambiarNomina',
                      id_ficha: id_ficha,
                      id_periodo: id_periodo,
                      id_nomina: id_nomina,
                      id_nomina_anterior: id_nomina_anterior
                    },
                    success:function(request){
                      var result=Ext.JSON.decode(request.responseText);
                      Ext.MessageBox.alert("Persona - Cambiar Nómina",result["message"]);
                      me.onRecargar();
                    },
                    failure:function(request){
                      var result=Ext.JSON.decode(request.responseText);
                    }
                  });
                }
              });

          }
        }
      });

      me.getCmp("btnPersonalInactivoCambiarNomina").menu.add({
        text: nomina[i]["codigo_nomina"],
        internal:{id: nomina[i]["id"]},
        listeners: {
          click: function(el){

            Ext.MessageBox.confirm(
              "Personal Inactivo - Quitar Nómina Actual",
              '<b>\u00BFEst\u00e1 seguro de cambiar a todo el personal inactivo para la nomina: "'+el.text+'"?</b><br> ',
              //'<b>\u00BFEst\u00e1 seguro quitar a todo el personal inactivo de la nómina actual?</b><br> ',
              function(btn,text){
                if(btn == 'yes'){
                  var id_periodo=me.getCmp('id_periodo').getValue();
                  var id_nomina_anterior=me.getCmp("id_nomina").getValue().join(",");
                  var id_nomina=el.internal.id;

                  me.menuPersona({disabled: true});
                  me.menuConcepto({disabled: true});
                  me.menuPeriodo({disabled: true});
                  //me.getCmp('btnContabilizar').setDisabled(true);

                  Ext.Ajax.request({
                    method: 'POST',
                    url:'module/nomina/',
                    params:{
                      action: 'onPersona_CambiarNominaInactivo',
                      id_periodo: id_periodo,
                      id_nomina: id_nomina,
                      id_nomina_anterior: id_nomina_anterior
                    },
                    success:function(request){
                      var result=Ext.JSON.decode(request.responseText);
                      Ext.MessageBox.alert("Personal Inactivo - Cambiar Nómina",result["message"]);
                      me.onRecargar();
                    },
                    failure:function(request){
                      var result=Ext.JSON.decode(request.responseText);
                    }
                  });
                }
              }
            );
          }
        }
      });
    }
  },


  init: function(){
    var me=this;
    me.onLoad_MenuPersonaCambiarCargo();
    me.onLoad_MenuPersonaCambiarEP();
    me.maximize();
  },

  setAccess: function(_access){
    var me=this;

  },

  onNominaSeleccionada: function(){
    var me=this;
    if(!me.getCmp('id_periodo').getValue()) {
      Ext.Msg.alert(me.title,"Primero debe seleccionar la nómina y el periodo.");
      return false;
    }
    return true;
  },

  onRecargar: function(){
    var me=this;
    //me.getCmp('gridList').store.load();
    me.onCargarNomina();
  },

  onActualizarFilaFicha: function(registro, data){
    var me=this;
    var columna=me.getCmp('gridList').headerCt.getGridColumns();
    //si la data devuelta no corresponde al registro, salir
    if(registro.get("id_ficha")!=data["id_ficha"])
      return;

    //limpiar columnas numericas, (datos calculados)
    for(var i=0;i<columna.length;i++){
      if(columna[i].dataIndex=="n" || columna[i].dataIndex=="persona") continue;
      registro.data[columna[i].dataIndex]="";
    }

    //colocar la información actualizada
    for(var i=0;i<data["concepto"].length;i++){
      registro.data[data["concepto"][i]["id"]]=data["concepto"][i]["valor_final"];
      if(data["concepto"][i]["tipo"]=="AP")
        registro.data[data["concepto"][i]["id"]+"_ap"]=data["concepto"][i]["valor_final_ap"];
    }

    //colocar los totales
    registro.data["total_asignacion"]=data["total_asignacion"];
    registro.data["total_deduccion"]=data["total_deduccion"];
    registro.data["total_neto"]=data["total_neto"];
    registro.data["total_ap"]=data["total_ap"];

    //actualizar la vista
    //me.getCmp('gridList').getView().refresh();
    var index=me.getCmp('gridList').getStore().indexOf(registro);
    console.log("Actualizar Fila: ",index);
    me.getCmp('gridList').getView().refreshNode(index);
    //registro.set('block',false);
  },

  configurarColumnas: function(){
    var me=this;

    var style='';
    var style_tipo='';
    var texto_tipo='';
    var title='';

    var columns=[];
    var columns_calc=[];
    var columns_asig=[];
    var columns_deduc=[];
    var columns_ap=[];
    var fields=["id_ficha","cedula","persona","total_asignacion","total_deduccion","total_neto","mensaje","total_ap"];

    columns.push(
      {
          xtype: 'rownumberer',
          dataIndex: 'n',
          text: "<b>Nº</b>",
          width: 30,
          sortable: false,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false,
          align: "center"
      }
    );
    columns.push(
        {
          xtype: 'templatecolumn',
          text: "<b>CÉDULA</b>",
          //tpl: "<div style='width: 100%;position:relative;' data-qtip='{mensaje}'><tpl if='nacionalidad!=\"V\"'>{nacionalidad}</tpl>{cedula}<div style='width:100%; position:absolute; top:0;right:0;height:100%;'><tpl if='activo==\"f\"'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-amarillo.png' data-qtip='<b>Personal Inactivo<b>'/></tpl><tpl if='activo_otra_nomina'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-duplicado-amarillo.png' data-qtip='<b>{activo_otra_nomina}<b>'/></tpl></div></div>",
          tpl: !me.internal.cerrado?
            "<div style='width: 100%;position:relative;' data-qtip='{mensaje}'><tpl if='nacionalidad!=\"V\"'>{nacionalidad}</tpl>{cedula}<div style='width:100%; position:absolute; top:0;right:0;height:100%;'><tpl if='activo==\"f\"'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-amarillo.png' data-qtip='<b>Personal Inactivo<b>'/></tpl><tpl if='activo_otra_nomina'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-duplicado-amarillo.png' data-qtip='<b>{activo_otra_nomina}<b>'/></tpl></div></div>":
            "<div style='width: 100%;position:relative;' data-qtip='{mensaje}'><tpl if='nacionalidad!=\"V\"'>{nacionalidad}</tpl>{cedula}</div>",
          width: 100,
          menuDisabled: true,
          sortable: false,
          height: 181,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );
    columns.push(
        {
          xtype: 'templatecolumn',
          text: "<b>PERSONA</b>",
          tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido} <span class='nomina_cargo'>({cargo})</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
          width: 220,
          menuDisabled: true,
          sortable: false,
          height: 181,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );

    columns.push(
        {
          xtype: 'templatecolumn',
          html: "<div class='text_vertical'><b>ESCALA SALARIAL</b></div>",
          tpl: "<div style='width: 100%;'><span class=''>{escala_salarial}</span></div>",
          width: 70,
          menuDisabled: true,
          sortable: false,
          height: 181,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );

    columns.push(
        {
          xtype: 'templatecolumn',
          html: "<div class='text_vertical'><b>ESTRUCTURA<br>PRESUPUESTARIA</b></div>",
          //dataIndex: 'estructura_presupuestaria',
          //tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido} <span class='nomina_cargo'>({cargo})</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
          tpl: "<div style='width: 100%;' data-qtip='<b>Acción Centralizada:</b> {denominacion_centralizada}<br><b>Específica:</b> {denominacion_especifica}<br><b>Sub-Específica:</b> {denominacion_subespecifica}'><span class='nomina_estructura_presupuestaria'>{estructura_presupuestaria}</span></div>",
          width: 95,
          menuDisabled: true,
          sortable: false,
          height: 181,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );

    columns.push(
        {
          xtype: 'templatecolumn',
          html: "<div class='text_vertical'><b>INGRESO</b></div>",
          //dataIndex: 'estructura_presupuestaria',
          //tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido} <span class='nomina_cargo'>({cargo})</div><tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
          tpl: "<div style='width: 100%;'><span class='nomina_fecha_ingreso mes_ingreso_{mes_ingreso}'>{fecha_ingreso}</span></div>",
          width: 60,
          menuDisabled: true,
          sortable: false,
          height: 181,
          cls: "hoja_trabajo_header_fijo",
          tdCls: "hoja_trabajo_cell_fijo",
          locked: true,
          lockable: true,
          draggable: false,
          resizable: false
        }
    );

    for(var i=0;i<me.internal.data.concepto.length;i++){
      style='';
      style_tipo='';
      texto_tipo='';

      fields.push(me.internal.data.concepto[i]["id_concepto"]);

      if(me.internal.data.concepto[i]["indefinido"]){
        style='error_formula';
        title=""+me.internal.data.concepto[i]["indefinido"]+" SE ENCUENTRA INDEFINIDA EN LA FORMULA.";
      }

      var _editor=null;
      var _cls_formula="";
      if(!me.internal.data.concepto[i]["es_formula"] && me.internal.cerrado==false) {
        _editor={
          xtype: 'numberfield',
          minValue: 0,
          //allowBlank: false,
          allowBlank: true,
          allowDecimals: true,
          decimalPrecision: 2,
          decimalSeparator: '.',
          hideTrigger: true,
          keyNavEnabled: false,
          mouseWheelEnabled: false,
          fieldStyle: "font-size: 9px; padding: 0px; text-align: right;"
        };
      }
      else{
        _cls_formula=" formula";
      }

      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":
          texto_tipo='ASIGNACIÓN';
          texto_tipo='[A]';
          style_tipo='formula_tipo_asignacion';
          break;
        case "RD":
          style_tipo='formula_tipo_asignacion';
          texto_tipo='REINTEGRO DEDUCCÓN';
          texto_tipo='[RD]';
          break;
        case "D":
          texto_tipo='DEDUCCIÓN';
          texto_tipo='[D]';
          style_tipo='formula_tipo_deduccion';
          break;
        case "AP":
          texto_tipo='APORTE PATRONAL';
          texto_tipo='[AP]';
          style_tipo='formula_tipo_deduccion';
        case "RA":
          texto_tipo='REINTEGRO ASIGNACIÓN';
          texto_tipo='[RA]';
          style_tipo='formula_tipo_deduccion';
          break;
      }

      var column_tmp=
        {
          xtype: 'gridcolumn',
          dataIndex: me.internal.data.concepto[i]["id_concepto"],
          html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' onclick=\"siga.window.getCmp('"+me.self.getName()+"').onEditarConcepto("+me.internal.data.concepto[i]["id_concepto"]+")\" /></div><div class='text_vertical'><span class='concepto_codigo'>"+me.internal.data.concepto[i]["codigo"]+" ~ </span> "+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_indeficador'>"+me.internal.data.concepto[i]["identificador"]+"</div><div class='text_formula "+style+"' title='"+title+"' onclick='alert(\""+me.internal.data.concepto[i]["id"]+"\")'>"+me.internal.data.concepto[i]["definicion"]+"</div></div>",
          width: 65,
          menuDisabled: true,
          sortable: false,
          height: 181,
          locked: false,
          lockable: false,
          resizable: false,
          align: 'right',
          cls: "hoja_trabajo_header",
          tdCls: 'hoja_trabajo_cell'+_cls_formula,
          editor: _editor,
          summaryType: function(record, data_index){
            var suma=0;
            for(var s=0;s<record.length;s++){
              v=record[s].get(data_index);
              if(v=="") continue;
              suma+=v*1.00;
            }
            return suma;
          },
          summaryRenderer: function(value, summaryData, dataIndex) {
            return Ext.util.Format.number(value, '0,0.00');
          },
          renderer: function(value) {
            return Ext.util.Format.number(value, '0,0.00');
          },
          listeners: {
            headerclick: function(ct, column, e, t, eOpts){
              if(me.internal.cerrado==true){
                return;
              }
              //console.log(ct);
              //column.tdCls="nomina_columna_seleccionada";
              if(me.internal.columnaSeleccionada!=null){
                me.internal.columnaSeleccionada.removeCls("nomina_columna_seleccionada");
                if(me.internal.columnaSeleccionada.dataIndex==column.dataIndex){
                  me.internal.columnaSeleccionada=null;
                  return;
                }
              }
              column.addCls("nomina_columna_seleccionada");
              me.internal.columnaSeleccionada=column;
            }
          }
        };

      switch(me.internal.data.concepto[i]["tipo"]){
        case "A":
        case "RD":
          columns_asig.push(column_tmp);
          break;
        case "AP":
          fields.push(me.internal.data.concepto[i]["id_concepto"]+"_ap");
          columns_ap.push({
            xtype: 'gridcolumn',
            dataIndex: me.internal.data.concepto[i]["id_concepto"]+"_ap",
            //disabled: true,
            html: "<div class='columna_editar'><img src='image/icon/icon-edit.png' style='width: 16px; cursor: pointer;' /></div><div class='text_vertical'>"+me.internal.data.concepto[i]["concepto"]+"<span class='"+style_tipo+"'> "+texto_tipo+"</span><div class='text_formula "+style+"' title='"+title+"'>"+me.internal.data.concepto[i]["definicion_ap"]+"</div></div>",
            width: 53,
            menuDisabled: true,
            sortable: false,
            height: 181,
            locked: false,
            lockable: false,
            resizable: false,
            align: 'right',
            cls: "hoja_trabajo_header",
            tdCls: 'hoja_trabajo_cell'+_cls_formula,
            summaryType: function(record, data_index){
              var suma=0;
              for(var s=0;s<record.length;s++){
                v=record[s].get(data_index);
                if (v=="") continue;
                suma+=v*1.00;
              }
              return suma;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {
              return Ext.util.Format.number(value, '0,0.00');
            },
            renderer: function(value) {
              return Ext.util.Format.number(value, '0,0.00');
            }
          });
        case "D":
        case "RA":
          columns_deduc.push(column_tmp);
          break;
        default:
          columns_calc.push(column_tmp);
      }
    }

    if(columns_calc.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group_none", menuDisabled: true, text: "", sortable: false, draggable: false, sealed: true, columns: columns_calc});


    if(columns_asig.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, text: columns_asig.length>1?"ASIGNACIONES":"ASIG.", sortable: false, draggable: false, sealed: true, columns: columns_asig});

    //columna de totales
    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_asignacion",
        html: "<div class='text_vertical'>TOTAL ASIGNACIONES</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 181,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_asignacion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00');
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );

    if(columns_deduc.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", menuDisabled: true, text: columns_deduc.length>1?"DEDUCCIONES":"DEDUC.", style: 'font-size: 9px; font-weight: bold;', sortable: false, draggable: false, sealed: true, columns: columns_deduc});

    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_deduccion",
        html: "<div class='text_vertical'>TOTAL DEDUCCIONES</div>",
        width: 70,
        menuDisabled: true,
        sortable: false,
        height: 181,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_deduccion',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00');
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );

    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_neto",
        html: "<div class='text_vertical'>TOTAL</div>",
        width: 80,
        menuDisabled: true,
        sortable: false,
        height: 181,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00');
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );

    if(columns_ap.length>0)
      columns.push({xtype: 'gridcolumn', cls: "hoja_trabajo_header_group", style: 'overflow: hidden;', menuDisabled: true, disabled: true, text: columns_ap.length>2?"APORTE PATRONAL":"AP", sortable: false, draggable: false, sealed: true, columns: columns_ap});

    columns.push(
      {
        xtype: 'gridcolumn',
        dataIndex: "total_ap",
        html: "<div class='text_vertical'>TOTAL APORTES</div>",
        width: 80,
        menuDisabled: true,
        sortable: false,
        height: 181,
        disabled: true,
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell total_neto',
        draggable: false,
        resizable: false,
        align: 'right',
        summaryType: 'sum',
        summaryRenderer: function(value, summaryData, dataIndex) {
          return Ext.util.Format.number(value, '0,0.00');
        },
        renderer: function(value) {
          return Ext.util.Format.number(value, '0,0.00');
        }
      }
    );


    return {fields: fields, columns: columns};

  },

  onCargarNomina: function(){
    var me=this;

    me.internal.periodo_id=me.getCmp('id_periodo').getValue();
    me.internal.periodo_denominacion=me.getCmp('id_periodo').getRawValue();

    me.internal.nomina_id=me.getCmp('id_nomina').getValue().join(",");
    me.internal.nomina_denominacion=me.getCmp('id_nomina').getRawValue();

    if(!me.onNominaSeleccionada())
      return;

    me.menuPersona({disabled: true});
    me.menuConcepto({disabled: true});
    me.menuPeriodo({disabled: true});
    me.getCmp('btnNotas').setDisabled(true);

    me.internal.columnaSeleccionada=null;
    me.internal.cerrado=null;
    me.getCmp('lblNominaActual').setText("<div style='font-size: 11px; line-height: 120%;'><b>Periodo:</b> "+me.internal.periodo_denominacion+". <b>Nómina:</b> "+me.internal.nomina_denominacion+".</div>",false);

    //me.getCmp('gridList').getStore().removeAll(true);
    me.getCmp('gridList').getStore().removeAll();
    me.getCmp('gridList').reconfigure();

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onListConceptoPeriodo',
        id_nomina: me.internal.nomina_id,
        id_periodo: me.internal.periodo_id
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);

        me.internal.cerrado=result["cerrado"]=='t'?true:false;
        me.internal.contabilizado=result["contabilizado"];
        me.internal.contabilizado_ap=result["contabilizado_ap"];
        me.internal.contabilizado_pagado="";
        me.internal.data.concepto=result["concepto"];
        me.internal.data.ficha=result["ficha"];

        me.menuPersona( {disabled: me.internal.cerrado});
        me.menuConcepto({disabled: me.internal.cerrado});
        me.menuPeriodo( {disabled: me.internal.cerrado});
        me.getCmp('btnNotas').setDisabled(me.internal.cerrado);

        me.getCmp('btnContabilizar_CCP').setDisabled(true);
        me.getCmp('btnContabilizar_CC').setDisabled(true);
        me.getCmp('btnContabilizar_P').setDisabled(true);
        me.getCmp('btnContabilizar_P_CXC').setDisabled(true);
        me.getCmp('btnContabilizar_CPP_NO_AP').setDisabled(false);
        me.getCmp('btnContabilizar_CPP_AP').setDisabled(false);

        if(me.internal.contabilizado!==null){
          var comprobante=siga.onGetComprobante({id:[me.internal.contabilizado]});
          console.log(comprobante);
          if(comprobante.length>0){
            if(comprobante[0]["tipo"]=="MB"){//es del tipo CCP
              me.getCmp('btnContabilizar_CCP').setDisabled(true);
              me.getCmp('btnContabilizar_CC').setDisabled(true);
              me.getCmp('btnContabilizar_P').setDisabled(true);
              me.getCmp('btnContabilizar_P_CXC').setDisabled(true);
            }
            else if((comprobante[0]["tipo"]=="PC" && comprobante[0]["detalle_comprobante_previo"].length===1) || String(comprobante[0]["concepto"]).indexOf("[PAGADO CxC]")>=0){//es del tipo PC y tiene comprobante precio (es P-CxC)
              me.getCmp('btnContabilizar_CCP').setDisabled(true);
              me.getCmp('btnContabilizar_CC').setDisabled(true);
              me.getCmp('btnContabilizar_P').setDisabled(true);
              me.getCmp('btnContabilizar_P_CXC').setDisabled(true);
            }
            else if(comprobante[0]["tipo"]=="PC" && comprobante[0]["detalle_comprobante_posterior"].length===0){//es del tipo CC (si es pc y no tiene comprobantes asociados)
              me.getCmp('btnContabilizar_CCP').setDisabled(true);
              me.getCmp('btnContabilizar_CC').setDisabled(true);
              me.getCmp('btnContabilizar_P').setDisabled(false);
              me.getCmp('btnContabilizar_P_CXC').setDisabled(false);
            }
            if(comprobante[0]["detalle_comprobante_posterior"].length>0){
              me.internal.contabilizado_pagado=comprobante[0]["detalle_comprobante_posterior"][0]["id_comprobante_posterior"];
            }
          }
        }
        else {
          me.getCmp('btnContabilizar_CCP').setDisabled(true);
          me.getCmp('btnContabilizar_CC').setDisabled(false);
          me.getCmp('btnContabilizar_P').setDisabled(true);
          me.getCmp('btnContabilizar_P_CXC').setDisabled(true);
        }

        me.filtro_ficha_id="";

        var config=me.configurarColumnas();

        var store= new Ext.data.Store({
          pageSize: 50,
          fields: config.fields,
          autoLoad: false,
          remoteSort: false,
          groupField: "nomina",
          //sorters: me.internal.sort,
          proxy: {
              type:'ajax',
              url: 'module/nomina/',
              actionMethods: { read: 'POST' },//actionMethods:  {create: "POST", read: "POST", update: "POST", destroy: "POST"},
              timeout: 3600000,
              reader: {
                  type: 'json',
                  rootProperty: 'result',
                  totalProperty:'total'
              },
              extraParams: {
                  action: 'onListFichaPeriodo',
                  //text: ''
              }
          },
          listeners: {
            load: function(store, records, successful, operation){
              //CREAR MENSAJE DE ALERTA PARA LAS PERSONAS QUE TENGAN CONCEPTOS Y NO SE ENCUENTRE LA COLUMNA
              //console.log("operation: ",operation);
              store.suspendEvents();

              //console.log("operation.request: ",operation._response.responseJson["filtro_ficha_id"]);
              me.filtro_ficha_id="";
              if(operation && operation._response && operation._response.responseJson && operation._response.responseJson["filtro_ficha_id"])
                me.filtro_ficha_id=operation._response.responseJson["filtro_ficha_id"];
              //console.log("operation.request: ",store._response.responseJson["filtro_ficha_id"]);
              //console.log(operation.request.result);
              //var response_text = store.proxy.reader.rawData;
            //console.log(response_text);
              for(var i=0;i<records.length;i++){
                records[i].set("mensaje","");
                var concepto=records[i].get("concepto");
                for(var j=0;j<concepto.length;j++){
                  var encontro=false;
                  for(var k=0;k<config.fields.length;k++){
                    if(concepto[j]["id"]==config.fields[k]){
                        encontro=true;
                        break;
                      }
                  }//FIN for(var k=0;k<config.fields.length;k++)
                  if(!encontro){
                    records[i].set("mensaje",
                                   records[i].get("mensaje")+
                                   concepto[j]["codigo"]+" "+
                                   concepto[j]["concepto"]+"<br>"
                    );
                  }
                }//FIN for(var j=0;j<concepto_id.length;j++)

                if(records[i].get("mensaje")){
                  records[i].set("mensaje","<b>La persona tiene los siguientes conceptos, lo cuales no se encuentran asociados a la nómina actual:<br></b><p>"+records[i].get("mensaje")+"</p>");
                }

              }//FIN for(var i=0;i<records.length;i++)
              store.resumeEvents();
              me.getCmp('gridList').getSelectionModel().deselectAll();
            },
            beforeload: function(store,operation,eOpts){
              me.filtro_ficha_id="";
              store.proxy.extraParams.id_nomina=me.internal.nomina_id;
              store.proxy.extraParams.id_periodo=me.internal.periodo_id;
              store.proxy.extraParams.filtro_busqueda=Ext.JSON.encode(me.filtro_busqueda_data());
            }
          }
        });

        me.getCmp('gridList').reconfigure(store,config.columns);
        me.getCmp('pagingList').bindStore(store);
        me.getCmp('gridList').getStore().load();
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
  },

  onEditarConcepto: function(id_concepto) {
    var me=this;
    alert(id_concepto);
  },

  onCerrarPeriodo: function(){
    var me=this;
    if(!me.onNominaSeleccionada())
      return;

    var id_periodo=me.getCmp('id_periodo').getValue();

    Ext.MessageBox.confirm( 'Cerrar Período',
                            '<b>\u00BFEst\u00e1 seguro de cerrar el período?</b><br> '+me.getCmp('id_periodo').getRawValue()+'',
                            function(btn,text){
                              if(btn == 'yes'){
                                me.menuPersona({disabled: true});
                                me.menuConcepto({disabled: true});
                                me.menuPeriodo({disabled: true});
                                me.getCmp('btnContabilizar').setDisabled(true);

                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/nomina/',
                                  params:{
                                    action: 'onClose',
                                    id_periodo: id_periodo
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                    Ext.MessageBox.alert("Cerrar Período",result["message"]);
                                    //recargar el listado de periodos, para mostrar el nuevo periodo creado
                                    me.getCmp('id_periodo').getStore().load();
                                    me.onRecargar();
                                    window.open("report/nomina_recibo_pago.php?id_periodo="+id_periodo+"&generar=1");
                                  },
                                  failure:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                  }
                                });
                              }
                            });
  },

  onNota: function(){
    var me=this;
    if(!me.onNominaSeleccionada())
      return;

    var id_periodo=me.getCmp('id_periodo').getValue();
    var id_nomina=me.getCmp('id_nomina').getValue().join(",");

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_periodo_nota/',
      params:{
        action: 'onGet',
        id_periodo: id_periodo,
        id_nomina: id_nomina
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        var nota="";
        if(result.length==1) {
          nota=result[0]["nota"];
        }
        var prompt=Ext.Msg.prompt(
          me.title,
          //'<b>Notas para la nómina actual.</b><br>Nómina: '+me.getCmp('id_nomina').getRawValue()+'<br>Período: '+me.getCmp('id_periodo').getRawValue()+'.',
          '<b>Notas para la nómina actual.</b>',
          function(btn, text){
            if(btn == 'ok'){
              //INICIO guardar datos en la BD
              Ext.Ajax.request({
                method: 'POST',
                url:'module/nomina_periodo_nota/',
                params:{
                  action: 'onSave',
                  id_periodo: id_periodo,
                  id_nomina: id_nomina,
                  nota: text
                },
                success:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                },
                failure:function(request){
                  var result=Ext.JSON.decode(request.responseText);
                }
              });
              //FIN guardar datos en la BD
            }
          },
          me,
          5,
          nota
        );

        prompt.setWidth(500);
        prompt.center();
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
  },

  onContabilizar: function(tipo_contabilizacion){
    var me=this;
    if(!me.onNominaSeleccionada())
      return;

    var id_periodo=me.getCmp('id_periodo').getValue();

    Ext.MessageBox.confirm( 'Contabilizar Período',
                            '<b>\u00BFEst\u00e1 seguro que desea contabilizar el período?</b><br> '+me.getCmp('id_periodo').getRawValue()+'',
                            function(btn,text){
                              if(btn == 'yes'){
                                //pedir fecha de contabilizacion
                                var _fecha=siga.get({action: "date", format: "d/m/Y"});
                                _fecha=_fecha["result"];
                                while(true){
                                  _fecha=prompt("Introduzca la fecha de contabilización (DD/MM/AAAA).", _fecha);
                                  if(_fecha==null)//si es cancelar
                                    return;
                                  if(isValidDate(_fecha))//si es valida
                                    break;
                                  alert("La fecha introducida es invalida.");
                                  }
                                _fecha=unformatDate(_fecha);

                                me.menuPersona({disabled: true});
                                me.menuConcepto({disabled: true});
                                me.menuPeriodo({disabled: true});
                                me.getCmp('btnContabilizar').setDisabled(true);

                                Ext.Ajax.request({
                                  method: 'POST',
                                  url:'module/nomina/',
                                  params:{
                                    action: 'onContabilizar',
                                    id_periodo: id_periodo,
                                    fecha: _fecha,
                                    tipo: tipo_contabilizacion
                                  },
                                  success:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                    Ext.MessageBox.alert("Contabilizar Período",result["message"]);
                                    me.onRecargar();
                                  },
                                  failure:function(request){
                                    var result=Ext.JSON.decode(request.responseText);
                                  }
                                });
                              }
                            });
  },

  alert: function(msj){
    var me=this;
    Ext.Msg.show({
      renderTo: me.getId(),
      modal:false,
      message: msj,
      modal: true,
    });
  },

  onFiltroBusqueda: function(){
    var me=this;

    me.onRecargar();
  },

  filtro_busqueda_data: function(){
    var me=this;
    var cedula             = me.getCmp("filtro_busqueda_cedula").getValue();
    var nombre_apellido    = me.getCmp("filtro_busqueda_nombre_apellido").getValue();
    var id_cargo           = me.getCmp("filtro_busqueda_id_cargo").getValue();
    var id_escala_salarial = me.getCmp("filtro_busqueda_id_escala_salarial").getValue();
    var ingreso_mes        = me.getCmp("filtro_busqueda_ingreso_mes").getValue();
    var estatus            = me.getCmp("filtro_busqueda_estatus").getValue();
    var filtro_busqueda = {
      cedula: cedula,
      nombre_apellido: nombre_apellido,
      id_cargo: id_cargo,
      id_escala_salarial: id_escala_salarial,
      ingreso_mes: ingreso_mes,
      estatus: estatus,
    };
    return filtro_busqueda;
  },

  getDataNomina: function(o){
    var me=this;

    if(o.tipo){
      var tmp=[];
      for(var i = 0; i<me.internal.data.preload["nomina"].length; i++)
        if(me.internal.data.preload["nomina"][i]["tipo"]==o.tipo)
          tmp.push(me.internal.data.preload["nomina"][i]);
      return tmp;
    }
    return me.internal.data.preload["nomina"];
  },

  getDataPeriodo: function(o){
    var me=this;

    if(o.tipo){
      var tmp=[];
      for(var i = 0; i<me.internal.data.preload["periodo"].length; i++)
        if(me.internal.data.preload["periodo"][i]["tipo"]==o.tipo)
          tmp.push(me.internal.data.preload["periodo"][i]);
      return tmp;
    }
    return me.internal.data.preload["periodo"];
  },

  onConceptoImportarDescargarFormato: function(){
    var me=this;

    var id_periodo = me.getCmp("id_periodo_concepto_importar").getValue();
    var id_nomina  = me.getCmp("id_nomina_concepto_importar").getValue().join(",");
    var incluir_valores = me.getCmp("valor_actual_concepto_importar").getValue()?1:0;

    window.open("report/nomina_formato_concepto_importar.php?id_periodo="+id_periodo+"&id_nomina="+id_nomina+"&incluir_valores="+incluir_valores);
  },

  onConceptoImportarPreCargar: function(){
    var me=this;
    me.internal.ventanaConceptoImportar.data_importar=[];

    var file = me.getCmp("archivo_excel_concepto_importar").fileInputEl.dom.files[0];
    if(!file){
      return;
    }

    var extension=file.name.substring(file.name.lastIndexOf('.')+1);

    var id_periodo = me.getCmp("id_periodo_concepto_importar").getValue();
    var id_nomina  = me.getCmp("id_nomina_concepto_importar").getValue().join(",");

    var reader = new FileReader();
    reader.addEventListener("load", function (e) {
      var content = e.target.result.split(';base64,')[1];

      var msgWait=Ext.Msg.wait('Importando excel. Por favor espere...', me.internal.ventanaConceptoImportar.getTitle(),{text:''});
      msgWait.setAlwaysOnTop(true);
      Ext.Ajax.request({
        method: 'POST',
        url:'module/nomina/',
        params:{
          action: 'onAddConceptoExcelPreCargar',
          id_periodo: id_periodo,
          id_nomina: id_nomina,
          archivo_extension: extension,
          archivo_contenido: content
        },
        success:function(request){
          var result=Ext.JSON.decode(request.responseText);
          msgWait.close();
          me.onConceptoImportarCargarGrid(result);
        },
        failure:function(request){
          var result=Ext.JSON.decode(request.responseText);
          msgWait.close();
        }
      });

    }, false);

    reader.readAsDataURL(file);
  },

  onConceptoImportarHeightPaso1: function(){
    var me=this;
    if(!me.getCmp("id_nomina_concepto_importar").getEl()) return;

    var height=me.getCmp("id_nomina_concepto_importar").getHeight();
    me.internal.ventanaConceptoImportar.setHeight(320+height);
  },

  onConceptoImportarRegresar: function(){
    var me=this;

    me.getCmp("archivo_excel_concepto_importar").setValue("");
    me.getCmp("archivo_excel_concepto_importar").setRawValue("");
    if(me.getCmp("archivo_excel_concepto_importar").fileInputEl && me.getCmp("archivo_excel_concepto_importar").fileInputEl.dom)
      me.getCmp("archivo_excel_concepto_importar").fileInputEl.dom.value = '';

    me.internal.ventanaConceptoImportar.data_importar=[];
    me.getCmp("container_paso1_concepto_importar").show();
    me.getCmp("container_paso2_concepto_importar").hide();
    me.onConceptoImportarHeightPaso1();
  },

  onConceptoImportarCargarGrid: function(result){
    var me=this;

    me.getCmp("container_paso1_concepto_importar").hide();
    me.getCmp("container_paso2_concepto_importar").show();
    me.internal.ventanaConceptoImportar.setHeight(600);

    //var result = {"ficha":[{"id_ficha":"208","nacionalidad":"V","cedula":"4183374","nombre_apellido":"MERCEDES M. SANCHEZ DE I.","nombres_apellidos":"MERCEDES M.  SANCHEZ DE I. ","id_nomina":"1","nomina":"0001 EMPLEADOS FIJOS (TIEMPO COMPLETO)"}],"concepto":[{"id":"1","codigo":"001","concepto":"SUELDO B\u00c1SICO","identificador":"SUELDO_BASICO","tipo":"A","column":"B","success":true,"message":""},{"id":"93","codigo":"091","concepto":"TIEMPO DE SERVICIO (ADMINISTRACION PUBLICA)","identificador":"TIEMPO_SERVICIO_AP","tipo":"","column":"C","success":true,"message":""},{"id":"85","codigo":"084","concepto":"% PROFESIONALIZACION","identificador":"PORCENTAJE_PROFESIONALIZACION","tipo":"","column":"D","success":true,"message":""}],"ficha_concepto":[{"id_periodo":"253","id_nomina":"1","id_ficha":"208","id_concepto":"1","valor":"100"},{"id_periodo":"253","id_nomina":"1","id_ficha":"208","id_concepto":"93","valor":"200"},{"id_periodo":"253","id_nomina":"1","id_ficha":"208","id_concepto":"85","valor":"300"}]};

    var fields = ["id_nomina","nomina","id_ficha","nacionalidad","cedula","persona","mensaje"];
    var columns = [];

    columns.push({
      xtype: 'rownumberer',
      dataIndex: 'n',
      text: "<b>Nº</b>",
      width: 50,
      sortable: false,
      cls: "hoja_trabajo_header_fijo",
      tdCls: "hoja_trabajo_cell_fijo",
      locked: true,
      lockable: true,
      draggable: false,
      resizable: false,
      align: "center"
    });

    columns.push({
      xtype: 'templatecolumn',
      text: "<b>CÉDULA</b>",
      tpl: "<div style='width: 100%;position:relative;' data-qtip='{mensaje}'>{nacionalidad}{cedula}<div style='width:100%; position:absolute; top:0;right:0;height:100%;'><tpl if='activo==\"f\"'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-amarillo.png' data-qtip='<b>Personal Inactivo<b>'/></tpl><tpl if='activo_otra_nomina'><img style='float: right; width: 14px; cursor: pointer;' src='image/icon/icon-advertencia-duplicado-amarillo.png' data-qtip='<b>{activo_otra_nomina}<b>'/></tpl></div></div>",
      width: 100,
      menuDisabled: true,
      sortable: false,
      height: 181,
      cls: "hoja_trabajo_header_fijo",
      tdCls: "hoja_trabajo_cell_fijo",
      locked: true,
      lockable: true,
      draggable: false,
      resizable: false
    });

    columns.push({
      xtype: 'templatecolumn',
      text: "<b>PERSONA</b>",
      tpl: "<div style='width: 100%;' data-qtip='{mensaje}'><div style='width:80%; float: left;'>{nombre_apellido}<tpl if='mensaje'><img style='float: right; width: 12px; cursor: pointer;' src='image/icon/icon-advertencia.png' /></tpl></div>",
      width: 220,
      menuDisabled: true,
      sortable: false,
      height: 181,
      cls: "hoja_trabajo_header_fijo",
      tdCls: "hoja_trabajo_cell_fijo",
      locked: true,
      lockable: true,
      draggable: false,
      resizable: false
    });

    for(var i=0; i<result["concepto"].length; i++){
      var field="concepto_id_"+result["concepto"][i]["id"];
      fields.push(field);

      columns.push({
        xtype: 'gridcolumn',
        dataIndex: field,
        html: "<div class='text_vertical'><span class='concepto_codigo'>"+result["concepto"][i]["codigo"]+" ~ </span> "+result["concepto"][i]["concepto"]+"<div class='text_indeficador'>"+result["concepto"][i]["identificador"]+"</div></div>",
        width: 65,
        menuDisabled: true,
        sortable: false,
        height: 181,
        locked: false,
        lockable: false,
        resizable: false,
        align: 'right',
        cls: "hoja_trabajo_header",
        tdCls: 'hoja_trabajo_cell',
        renderer: function(data, metaData, record, rowIndex, colIndex, store, view) {
          if(!data) return '';
          var value   = data.value;
          var success = data.success;
          var message = data.message;

          if(message)
            metaData.tdAttr = 'data-qtip="'+message+'"';

          if(!success || success=='false')
            metaData.tdCls = 'celda-error';

          return $.isNumeric(value)?Ext.util.Format.number(value, '0,0.00'):value;
        }
      });
    }

    var data = [];
    for(var i=0; i<result["ficha"].length; i++){
      data[i]=result["ficha"][i];
      for(var j=0; j<result["ficha_concepto"].length; j++){
        if(result["ficha_concepto"][j]["id_ficha"]==data[i]["id_ficha"]){
          var field="concepto_id_"+result["ficha_concepto"][j]["id_concepto"];
          data[i][field]={
            value: result["ficha_concepto"][j]["valor"],
            success: result["ficha_concepto"][j]["success"],
            message: result["ficha_concepto"][j]["message"]
          };
        }
      }
    }

    var store= new Ext.data.Store({
      fields: fields,
      groupField: "nomina",
      autoLoad: false,
      remoteSort: false,
      data: data
    });

    me.getCmp('grid_concepto_importar').getStore().removeAll();
    me.getCmp('grid_concepto_importar').reconfigure(store,columns);
    me.internal.ventanaConceptoImportar.data_importar=result["ficha_concepto"];
  },

  onConceptoImportarAplicar: function(){
    var me=this;

    //verificar si el archivo esta correcto
    for(var i=0; i<me.internal.ventanaConceptoImportar.data_importar.length; i++){
      if(!me.internal.ventanaConceptoImportar.data_importar[i]["success"]){
        Ext.Msg.alert( me.internal.ventanaConceptoImportar.getTitle(), 'Existen errores en el archivo cargado, verifique la data.');
        return;
      }
    }

    var msgWait=Ext.Msg.wait('Aplicando los cambios. Por favor espere...', me.internal.ventanaConceptoImportar.getTitle(),{text:''});
    msgWait.setAlwaysOnTop(true);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina/',
      params:{
        action: 'onAddConceptoExcelAplicar',
        data: Ext.JSON.encode(me.internal.ventanaConceptoImportar.data_importar)
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        msgWait.close();

        if(!result.success){
          Ext.Msg.alert( me.internal.ventanaConceptoImportar.getTitle(), result.message);
          return;
        }
        me.internal.ventanaConceptoImportar.close();
        me.onRecargar();
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
        msgWait.close();
      }
    });
  },

  onSeleccionarNominaHeight: function(){
    var me=this;
    if(!me.getCmp("id_nomina").getEl()) return;

    var height=me.getCmp("id_nomina").getHeight();
    me.internal.ventanaSeleccionarNomina.setHeight(220+height);
  },

  onActualizarDataPreload: function(){
    var me=this;

    _tmp=Ext.Ajax.request({
      async: false,
      url:"module/nomina/",
      params: {
        action: 'onInit'
      }
    });

    if(_tmp.statusText=="OK"){
      me.internal.data.preload=Ext.JSON.decode(_tmp.responseText);
    }

    //ACTUALIZAR DATA EN VENTANA SELECCION DE NOMINA
    me.getCmp("tipoVentanaSeleccionarNomina").getStore().setData(me.internal.data.preload["periodo_tipo"]);
    if(!me.getCmp("tipoVentanaSeleccionarNomina").getValue()){
      me.getCmp("tipoVentanaSeleccionarNomina").setValue("Q");
    }
    else{
      me.getCmp("tipoVentanaSeleccionarNomina").fireEvent("change");
    }

    //ACTUALIZAR DATA EN VENTANA IMPORTAR CONCEPTOS
    me.getCmp("tipo_nomina_concepto_importar").getStore().setData(me.internal.data.preload["periodo_tipo"]);
    if(!me.getCmp("tipo_nomina_concepto_importar").getValue()){
      me.getCmp("tipo_nomina_concepto_importar").setValue("Q");
    }
    else{
      me.getCmp("tipo_nomina_concepto_importar").fireEvent("change");
    }

    //ACTUALIZAR DATA EN VENTANA CONFIGURAR PROYECCION
    me.getCmp("identificador_proyeccion").getStore().setData(me.internal.data.preload["concepto_identificadores"]);

  },

  onConceptoImportarNominaSeleccionarTodo: function(){
    var me=this;
    if(!(me.internal.data && me.internal.data.preload && me.internal.data.preload["nomina"]))
      return;

    var v=[];
    for(var i=0; i<me.internal.data.preload["nomina"].length; i++){
      v.push(me.internal.data.preload["nomina"][i]["id"])
    }
    me.getCmp("id_nomina_concepto_importar").setValue(v);
  },

  onConceptoImportarNominaSeleccionarNada: function(){
    var me=this;

    me.getCmp("id_nomina_concepto_importar").setValue([]);
  },

  onNominaSeleccionarTodo: function(){
    var me=this;
    if(!(me.internal.data && me.internal.data.preload && me.internal.data.preload["nomina"]))
      return;

    var v=[];
    for(var i=0; i<me.internal.data.preload["nomina"].length; i++){
      v.push(me.internal.data.preload["nomina"][i]["id"])
    }
    me.getCmp("id_nomina").setValue(v);
  },

  onNominaSeleccionarNada: function(){
    var me=this;

    me.getCmp("id_nomina").setValue([]);
  },

  menuConcepto: function(o){
    var me=this;
    if(o.disabled===true || o.disabled===false){
      me.getCmp('btnConceptoAgregar').setDisabled(o.disabled);
      me.getCmp('btnConceptoQuitar').setDisabled(o.disabled);
      me.getCmp('btnConceptoImportar').setDisabled(o.disabled);
    }
  },

  menuPeriodo: function(o){
    var me=this;
    if(o.disabled===true || o.disabled===false){
      me.getCmp('btnPeriodoCerrar').setDisabled(o.disabled);
    }
  },

  menuPersona: function(o){
    var me=this;
    if(o.disabled===true || o.disabled===false){
      me.getCmp('btnPersonaAgregar').setDisabled(o.disabled);
      me.getCmp('btnPersonaQuitar').setDisabled(o.disabled);
      me.getCmp('btnPersonaCambiarCargo').setDisabled(o.disabled);
      me.getCmp('btnPersonaCambiarNomina').setDisabled(o.disabled);
      me.getCmp('btnPersonaCambiarEP').setDisabled(o.disabled);
      me.getCmp('btnPersonalInactivo').setDisabled(o.disabled);
      me.getCmp('btnPersonalInactivoQuitar').setDisabled(o.disabled);
      me.getCmp('btnPersonalInactivoCambiarNomina').setDisabled(o.disabled);
    }
  },

  onPersonaQuitar: function(el){
    var me=this;
    var seleccion=me.getCmp("gridList").getSelection();
    if(seleccion.length==0){
      Ext.MessageBox.alert("Persona - Quitar","<b>Debe seleccionar la persona a la cual quitará de la nómina actual.</b>");
      return;
    }
    if(seleccion.length!=1){
      Ext.MessageBox.alert("Persona - Quitar","<b>Debe seleccionar solo una persona del listado.</b>");
      return;
    }

    Ext.MessageBox.confirm(
      "Persona - Quitar",
      '<b>\u00BFEst\u00e1 seguro de quitar a la persona: "'+seleccion[0].data["nacionalidad"]+seleccion[0].data["cedula"]+" "+seleccion[0].data["nombre_apellido"]+'" de la nómina: "'+seleccion[0].data["nomina"]+'"?</b><br> ',
      function(btn,text){
        if(btn == 'yes'){
          var id_ficha=seleccion[0].data["id_ficha"];
          var id_periodo=me.getCmp("id_periodo").getValue();
          var id_nomina=seleccion[0].data["id_nomina"];

          me.menuPersona({disabled: true});
          me.menuConcepto({disabled: true});
          me.menuPeriodo({disabled: true});

          var msgWait=Ext.Msg.wait('En proceso. Por favor espere...', me.getTitle(),{text:''});
          msgWait.setAlwaysOnTop(true);
          Ext.Ajax.request({
            method: 'POST',
            url:'module/nomina/',
            params:{
              action: 'onPersona_Quitar',
              id_ficha: id_ficha,
              id_periodo: id_periodo,
              id_nomina: id_nomina
            },
            success:function(request){
              msgWait.close();
              var result=Ext.JSON.decode(request.responseText);
              Ext.MessageBox.alert("Persona - Quitar",result["message"]);
              me.onRecargar();
            },
            failure:function(request){
              msgWait.close();
              var result=Ext.JSON.decode(request.responseText);
            }
          });
        }
      });
  },

});

