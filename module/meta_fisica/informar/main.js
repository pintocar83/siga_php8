siga.define('meta_fisica/informar', {
  extend: 'siga.windowForm',
  title: 'Meta Física - Informar Actividad',
  maximizable:false,
  width: 820,
  height: 600,
  resizable: false,
  initComponent: function(){
    var me = this;
    
    me.itemsToolbar=[
      me.btnSave()
    ];
    
    
    me.items=[
      {
        xtype:'siga.select',
        id: me._('id_responsable'),
        name: 'id_responsable',
        anchor: "100%",
        fieldLabel: 'Responsable',
        disabled: true,
        value: '',
        internal:{
          valueField: 'id',
          columns: {field: ["identificacion","denominacion"], title: ["Cédula","Nombres / Apellidos"], width: ['25%','75%','',''], sort: ["ASC","ASC"]},
          url: 'module/ficha/',
          actionOnList:'onList',
          actionOnGet:'onGet',
          onAccept: function(){
            me.setResponsableMetaFisica(me.getCmp("id_responsable").getValue());
          }
        }
      },
      {
        xtype: "container",
        layout: "hbox",
        anchor: "100%",
        defaults: me.getInternal("field_defaults"),
        items: [
          {
            xtype:'siga.select',
            id: me._('id_meta_fisica'),
            name: 'id_meta_fisica',
            anchor: "100%",
            fieldLabel: 'Meta Física / Actividad',
            value: '',
            margin: "0 20 0 0",
            flex: 1,
            internal:{
              valueField: 'id',
              columns: {field: ["codigo","actividad"], title: ["Código","Actividad"], width: ['25%','75%','',''], sort: ["ASC","ASC"]},
              url: 'module/meta_fisica/',
              actionOnList:'onList',
              actionOnGet:'onGet',              
              onAccept: function(){
                me.onGet();
              },
              onGetFn: function(result){
                me.getCmp("unidad_medida").setHtml("");
                if(result[0])
                  if(result[0]["unidad_medida"])
                    me.getCmp("unidad_medida").setHtml(result[0]["unidad_medida"]);
              }
            }
          },
          {
            xtype:'combobox',
            id: me._('mes'),
            name: 'mes',
            margin: 0,
            width: 150,
            fieldLabel: 'Mes del Informe',
            store: {
              fields: ['id', 'nombre'],
              data : [
                {"id":"0", "nombre":"ENERO"},
                {"id":"1", "nombre":"FEBRERO"},
                {"id":"2", "nombre":"MARZO"},
                {"id":"3", "nombre":"ABRIL"},
                {"id":"4", "nombre":"MAYO"},
                {"id":"5", "nombre":"JUNIO"},
                {"id":"6", "nombre":"JULIO"},
                {"id":"7", "nombre":"AGOSTO"},
                {"id":"8", "nombre":"SEPTIEMBRE"},
                {"id":"9", "nombre":"OCTUBRE"},
                {"id":"10", "nombre":"NOVIEMBRE"},
                {"id":"11", "nombre":"DICIEMBRE"},
              ]                      
            },
            displayField: 'nombre',
            valueField: 'id',
            allowBlank: false,
            forceSelection: true,                    
            value: "",
            editable: false,
            listeners:{
              change: function(e, newValue, oldValue, eOpts){
                me.onGet();
              }
            }
          },
          
        ]
      },
      {
        xtype: "container",
        layout: "hbox",
        anchor: "100%",
        defaults: me.getInternal("field_defaults"),        
        items: [
          {
            xtype: "textfield",
            fieldLabel: 'Cantidad',
            id: me._("cantidad"),
            name: "cantidad",
            margin: "0 10 0 0",
            width: 150,
          },
          {
            xtype: "container",            
            id: me._("unidad_medida"),
            margin: "17 0 0 0",
            flex: 1,
            style: "font-weight: bold; font-size: 12px;",
            html: ""
          }          
        ]
      },
      {
        xtype: "tbspacer",
        flex: 1,
        height: 10,
      },
      {
        xtype: "textfield",
        fieldLabel: 'Comunidad Atendida',
        id: me._("comunidad"),
        name: "comunidad",
        flex: 1,
      },
      {
        xtype:'fieldset',        
        title: '<b>Personas Atendidas en la Comunidad</b>',
        collapsible: false,
        layout:'hbox',
        padding: "5 0 10 0",
        defaults: {
          labelSeparator: '',
          labelWidth: 60,
        },
        items: [
          {
            xtype: "textfield",
            fieldLabel: '<b>Masculino</b>',
            id: me._("cantidad_masculino"),
            name: "cantidad_masculino",
            margin: "0 80 0 40",
            flex: 1
          },
          {
            xtype: "textfield",
            fieldLabel: '<b>Femenino</b>',
            id: me._("cantidad_femenino"),
            name: "cantidad_femenino",
            margin: "0 40 0 0",
            flex: 1
          }
        ]
      },
      {
        xtype: "tbspacer",
        flex: 1,
        height: 10,
      },
      {
        xtype: 'tabpanel',
        id: me._('separador'),
        //margin: 20,
        collapsed: false,
        frameHeader: false,
        activeTab: 0,
        plain: true,
        height: 220,
        items: [
          {
            xtype: 'form',
            frame: false,
            id: me._('tab_logros'),
            //frameHeader: false,
            //autoScroll:true,
            layout: 'fit',
            title: 'Logros',
            items: [
              {
                xtype:'textarea',
                id: me._('logros'),
                name: 'logros',
                fieldLabel: '',
                value: '',
                flex: 1
              } 
            ]
          },
          {
            xtype: 'panel',
            id: me._('tab_obstaculos'),
            title: 'Obstaculos',
            layout: 'fit',
            items: [
              {
                xtype:'textarea',
                id: me._('obstaculos'),
                name: 'obstaculos',
                fieldLabel: '',
                value: '',
                flex: 1
              } 
            ]
          },
          {
            xtype: 'panel',
            id: me._('tab_adjunto'),
            title: 'Memoria Fotográfica / Documentos',
            layout: 'vbox',
            items: [
              {
                xtype: "dataview",
                flex: 1,
                width: "100%",
                scrollable: 'y',
                style: "display: flex; flex-flow: row wrap;",
                tpl: '<tpl for=".">'+
                          '<div style="margin-bottom: 10px; width: 110px; margin: 2px 3px; text-align: center;" class="thumb-wrap">'+
                            '<img src="{src}" width="100%" />'+
                            '<br/><span style="font-size: 9px;">{nombre}</span>'+
                          '</div>'+
                      '</tpl>',
                itemSelector: 'div.thumb-wrap',
                emptyText: 'No images available',
                store: {
                  data: [
                      { src:'http://www.sencha.com/img/20110215-feat-drawing.png', nombre:'Drawing & Charts' },
                      { src:'http://www.sencha.com/img/20110215-feat-data.png',    nombre:'Advanced Data'    },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-html5.png',   nombre:'Overhauled Theme' },
                      { src:'http://www.sencha.com/img/20110215-feat-perf.png',    nombre:'Performance Tuned'}
                  ]
                }
              },
              {
                xtype: "toolbar",
                //height: 20,
                width: "100%",
                items: [
                  {                      
                    text: 'Agregar'
                  },
                  {                      
                    text: 'Quitar'
                  },
                  {                      
                    text: 'Renombrar'
                  },
                ]
              }
            ]
          }
        ]
      }
      /*{
        xtype:'textarea',
        id: me._('unidad_medidaxxx'),
        name: 'unidad_medidaxxx',
        fieldLabel: 'xxxxxxxx',
        value: ''
      }    */  
    ];
    
    
    me.callParent(arguments);
    me.setAccess(siga.getAccess("meta_fisica/unidad_medida"));
    
    
  },
  
  init: function(){
    var me=this;
    me.getCmp("mes").setValue(siga.get({action:"date",format:"m"}).result-1);
    
    /*me.getCmp("id_meta_fisica").valueField.onAfter("change",function(){
      alert("xxxx");
      console.log(me.getCmp("id_meta_fisica").internal.result);
    });*/
    
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/meta_fisica/informar/',
      params:{
        action: 'onGet_Responsable'
      },
      success:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);
        me.getCmp("id_responsable").setValue(result[0]["id"]);
        me.setResponsableMetaFisica(result[0]["id"]);
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });    
  },
  
  setResponsableMetaFisica: function(id){
    var me=this;
    if(!me.getCmp("id_meta_fisica").internal.params)
      me.getCmp("id_meta_fisica").internal.params={};
    me.getCmp("id_meta_fisica").internal.params.id_responsable=id;
  },
  
  onGet: function(){
    var me=this;
    
    var id_meta_fisica=me.getCmp("id_meta_fisica").getValue();
    var mes=me.getCmp("mes").getValue();
    if(!id_meta_fisica) return;
    if(!(mes>=0 && mes <=11)) return;
    
    //me.setMessage("Cargar Informacion en formulario","orange");
    
    
    Ext.Ajax.request({
      method: 'POST',
      url:'module/meta_fisica/informar/',
      params:{
        action: 'onGet',
        id_meta_fisica: id_meta_fisica,
        mes: mes
      },
      success:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);
        
        me.getCmp("cantidad").setValue("0");
        me.getCmp("cantidad_masculino").setValue("0");
        me.getCmp("cantidad_femenino").setValue("0");
        me.getCmp("comunidad").setValue("");
        me.getCmp("logros").setValue("");
        me.getCmp("obstaculos").setValue("");
        
        if(!result) return;
        if(!(result.length>0)) return;
        
        me.getCmp("cantidad").setValue(result[0]["cantidad"]);
        me.getCmp("cantidad_masculino").setValue(result[0]["cantidad_masculino"]);
        me.getCmp("cantidad_femenino").setValue(result[0]["cantidad_femenino"]);
        me.getCmp("comunidad").setValue(result[0]["comunidad"]);
        me.getCmp("logros").setValue(result[0]["logros"]);
        me.getCmp("obstaculos").setValue(result[0]["obstaculos"]);
        
        me.onCargarDocumentos();
      },
      failure:function(request){
        Ext.MessageBox.hide();
        var result=Ext.JSON.decode(request.responseText);      
        me.setMessage(result.message,"red");
      }
    });    
    
    
  },
  
  onCargarDocumentos: function(){
    
    
    
  },
  
  onSave: function(){
    var me=this;
    
    var id_meta_fisica=Ext.String.trim(me.getCmp("id_meta_fisica").getValue());
    var mes=Ext.String.trim(me.getCmp("mes").getValue());
    var cantidad=Ext.String.trim(me.getCmp("cantidad").getValue())*1;
    var cantidad_masculino=Ext.String.trim(me.getCmp("cantidad_masculino").getValue())*1;
    var cantidad_femenino=Ext.String.trim(me.getCmp("cantidad_femenino").getValue())*1;

    if(!id_meta_fisica){
      me.setMessage("Debe completar seleccionar la Meta Física y/o Actividad.","red");
      return;
    }
    if(!(mes>=0 && mes <=11)){
      me.setMessage("Debe completar seleccionar el mes.","red");
      return;
    }
    
    if(!(cantidad>=0)){
      me.setMessage("El campo cantidad debe ser mayor o igual a cero.","red");
      return;
    }
    
    if(!(cantidad_masculino>=0)){
      me.setMessage("El campo cantidad de personas atendidas (masculino) debe ser mayor o igual a cero.","red");
      return;
    }
    
    if(!(cantidad_femenino>=0)){
      me.setMessage("El campo cantidad de personas atendidas (femenino) debe ser mayor o igual a cero.","red");
      return;
    }
    
    me.getCmp('tab_data').submit({
      method: 'POST',
      url:'module/meta_fisica/informar/',
      params:{
        action: 'onSave'
      },
      waitMsg: 'Guardando... por favor espere!',
      success: function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.onGet();
        me.setMessage(result.message,"green");
      },
      failure:function(form,o){
        Ext.MessageBox.hide();
        var result=o.result;
        me.setMessage(result.message,"red");
      }
    });
    
    
  },
  

  /*
  onDelete: function(){
    var me=this;
    var _id=Ext.String.trim(me.getCmp("id").getValue());
    if(!_id) return;
    
    Ext.MessageBox.wait('Eliminando... por favor espere!');
      Ext.Ajax.request({
        method: 'POST',
        url:'module/meta_fisica/unidad_medida/',
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
      me.getCmp("unidad_medida").getValue()
      ];
  },
  
  onPaste: function(){
    var me=this;
    me.getCmp("unidad_medida").setValue(me.internal.toCopy[0]);
  },*/
  
});
