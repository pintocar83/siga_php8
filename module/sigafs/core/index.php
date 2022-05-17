<!doctype html>
<html>
  <head>
    <link type="text/css" rel="stylesheet" href="comunes/tabpane.css">
    <link type="text/css" rel="stylesheet" href="comunes/estilo_contenido_ventana.css"> 
    <?php
    $form=$_GET["form"];
    include("comunes/inc_jscalendar-1.0.php");
    //include_once("comunes/funciones_php.php");
    ?>
    <script type="text/javascript" src="comunes/tabpane.js" ></script>
    <script type="text/javascript" src="comunes/x.js"></script>
    <script type="text/javascript" src="comunes/xtrim.js"></script>
    <!--<script type="text/javascript" src="comunes/xstyle.js"></script>-->
    <script type="text/javascript" src="comunes/manipularDom.js"></script>
    <script type="text/javascript" src="comunes/AjaxRequest.js"></script>
    <script type="text/javascript" src="comunes/funciones_generales.js"></script>
    <script type="text/javascript">
    setActiveStyleSheet('', 'Aqua');
    
    var SIGA=parent;
    var siga=parent.siga;
    var Ext=parent.Ext;
    
    
    function Form_LISTA_CUENTAS_PRESUPUESTARIAS__Abrir(CAMPO_ID_CUENTA_PRESUPUESTARIA,CAMPO_DENOMINACION,FILTRO,CALLBACK) {
      var campo_cuenta_presupuestaria={
      fieldLabel: 'Cuentas Presupuestarias',
      setValue: function(v){},
      internal:{
        page:1,
        limit: 100,
        valueField: 'id_cuenta_presupuestaria',
        columns: {field: ["cuenta_presupuestaria","denominacion","padre"], title: ["Cuenta Presupuestaria","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
        url: '../../modulo_presupuesto/cuenta_presupuestaria.php?filtro='+FILTRO,
        actionOnList: 'onList',
        actionOnGet: 'onGet',
        viewConfig:{
          getRowClass: function(rec, rowIdx, params, store) {                    
            if(rec.get('padre')=='t')
              return 'fila-padre';
            return 'fila-hija';
          }
        },
        onBeforeAccept: function(dataview, record, item, index, e){
          if(record.get('padre')=='t')
            return false;
          
          xGetElementById(CAMPO_ID_CUENTA_PRESUPUESTARIA).value=record.get("id_cuenta_presupuestaria");
          xGetElementById(CAMPO_DENOMINACION).value=record.get("denominacion");
          
          if(CALLBACK)
            if(eval(CALLBACK)==false)
              return false;          
          return true;
        },			
        onAccept: function(){}
      }
    };
    var selector=new Ext.form.eWindowSelect({parent: campo_cuenta_presupuestaria});
    selector.show();
    selector.search();
    }
    
    function Form_LISTA_CUENTAS_CONTABLES__Abrir(CAMPO_ID,CAMPO_DENOMINACION,FILTRO,FILTRO_PRESUPUESTO,CALLBACK){
      var campo={
      fieldLabel: 'Cuentas Contables',
      setValue: function(v){},
      internal:{
        page:1,
        limit: 100,
        valueField: 'id_cuenta_contable',
        columns: {field: ["cuenta_contable","denominacion"], title: ["Cuenta Contable","Denominación"], width: ['25%','75%'], sort: ["ASC","ASC"]},
        url: '../../modulo_contabilidad/cuenta_contable.php?filtro='+FILTRO,
        actionOnList: 'onList',
        actionOnGet: 'onGet',        
        onBeforeAccept: function(dataview, record, item, index, e){ 
          xGetElementById(CAMPO_ID).value=record.get("id_cuenta_contable");
          xGetElementById(CAMPO_DENOMINACION).value=record.get("denominacion");
          if(CALLBACK)
            if(eval(CALLBACK)==false)
              return false;          
          return true;
        },			
        onAccept: function(){}
      }
    };
    var selector=new Ext.form.eWindowSelect({parent: campo});
    selector.show();
    selector.search();
    }
    
    
    function Form_LISTA_PROVEEDOR__Abrir(CAMPO_ID,CAMPO_RIF,CAMPO_DENOMINACION,CALLBACK,CAMPO_CUENTA_CONTABLE,_MostrarSolo){
      var campo={
        fieldLabel: 'Lista de Proveedores',
        setValue: function(v){
          var resp=SIGA.Ext.Ajax.request({
            async: false,
            url:campo.internal.url,
            params: SIGA.Ext.JSON.decode("{action: '"+campo.internal.actionOnGet+"', "+campo.internal.valueField+": '"+v+"'}")
          });
          if(resp.statusText=="OK"){
            var retorno=SIGA.Ext.JSON.decode(resp.responseText);
            xGetElementById(CAMPO_ID).value=retorno[0]['id'];
            if(CAMPO_RIF) 
              xGetElementById(CAMPO_RIF).value=retorno[0]['identificacion'];
            if(CAMPO_DENOMINACION) 
              xGetElementById(CAMPO_DENOMINACION).value=retorno[0]['denominacion'];
            if(CAMPO_CUENTA_CONTABLE)
              xGetElementById(CAMPO_CUENTA_CONTABLE).value=retorno[0]['id_cuenta_contable'];
            if(CALLBACK)
              if(eval(CALLBACK)==false)
                return false;          
            return true;
          }
          return false;
        },
        internal:{
          page:1,
          limit: 100,
          valueField: 'id',
          //columns: {field: ["identificacion_tipo","identificacion_numero","denominacion","identificacion"], title: ["Nac.","Cédula","Denominación"], width: ['5%','20%','75%'], sort: ["DESC",'ASC']},
          columns: {field: ["identificacion","denominacion"], title: ["RIF","Denominación"], width: ['20%','80'], sort: ["ASC",'NULL']},
          url: 'modulo_base/persona.php',
          actionOnList:'onList_PersonaJuridica',
          actionOnGet:'onGet_Select',
        }
      };
      var selector=new SIGA.Ext.form.eWindowSelect({parent: campo});
      selector.show();
      selector.search();
      }

    function Form_LISTA_BENEFICIARIO__Abrir(CAMPO_ID,CAMPO_CEDULA,CAMPO_DENOMINACION,CALLBACK,CAMPO_CUENTA_CONTABLE,_MostrarSolo){
      var campo={
        fieldLabel: 'Lista de Beneficiarios',
        setValue: function(v){
          var resp=SIGA.Ext.Ajax.request({
            async: false,
            url:campo.internal.url,
            params: Ext.JSON.decode("{action: '"+campo.internal.actionOnGet+"', "+campo.internal.valueField+": '"+v+"'}")
          });
          if(resp.statusText=="OK"){
            var retorno=SIGA.Ext.JSON.decode(resp.responseText);
            xGetElementById(CAMPO_ID).value=retorno[0]['id'];
            if(CAMPO_CEDULA) 
              xGetElementById(CAMPO_CEDULA).value=retorno[0]['identificacion'];
            if(CAMPO_DENOMINACION) 
              xGetElementById(CAMPO_DENOMINACION).value=retorno[0]['denominacion'];
            if(CAMPO_CUENTA_CONTABLE) 
              xGetElementById(CAMPO_CUENTA_CONTABLE).value=retorno[0]['id_cuenta_contable'];
            if(CALLBACK)
              if(eval(CALLBACK)==false)
                return false;          
            return true;
          }
          return false;
        },
        internal:{
          page:1,
          limit: 100,
          valueField: 'id',
          //columns: {field: ["identificacion_tipo","identificacion_numero","denominacion","identificacion"], title: ["Nac.","Cédula","Denominación"], width: ['5%','20%','75%'], sort: ["DESC",'ASC']},
          columns: {field: ["identificacion","denominacion"], title: ["Cédula","Denominación"], width: ['20%','80'], sort: ["ASC",'NULL']},
          url: 'modulo_base/persona.php',
          actionOnList:'onList_PersonaNatural',
          actionOnGet:'onGet_Select',
        }
      };
      var selector=new Ext.form.eWindowSelect({parent: campo});
      selector.show();
      selector.search();
      }
      
    function Form_LISTA_CUENTAS_BANCARIAS__Abrir(CAMPO_ID,CAMPO_CTA_N,CAMPO_DESCRIPCION,CAMPO_CTA_TIPO,CAMPO_BANCO,CAMPO_ID_CUENTA_CONTABLE,CAMPO_CUENTA_CONTABLE,CAMPO_CUENTA_CONTABLE_DENOMINACION,CALLBACK){
      //Form_LISTA_CUENTAS_BANCARIAS__Abrir('ID_CTA_BUSCAR_FBM','NCTA_BUSCAR_FBM','DESCRIPCION_NCTA_BUSCAR_FBM','TIPO_CTA_BUSCAR_FBM','BANCO_BUSCAR_FBM','CTA_CODIGO_CONTABLE_BUSCAR_FBM','Form_BANCO_MOVIMIENTO__LimpiarInputTextBuscarListado();')
      
      var campo={
      fieldLabel: 'Banco - Listado de Cuentas',
      setValue: function(v){},
      internal:{
        page:1,
        limit: 100,
        valueField: 'id',
        columns: {
          field: ["numero_cuenta","denominacion","cuenta_contable","denominacion_contable","banco","tipo","id_cuenta_contable"],
          title: ["Número","Descripción","Cuenta Contable","Denominación"],
          width: ['15%','36%','13%','36%'],
          sort: ["","ASC","",""]
        },
        gridList:{
          features:[{
            ftype: 'grouping',
            groupHeaderTpl: '{name}',
            collapsible : false,
          }],
          groupField: 'banco'
        },
        url: '../../modulo_base/banco_cuenta.php',
        actionOnList: 'onList',
        actionOnGet: 'onGet',
        onBeforeAccept: function(dataview, record, item, index, e){ 
          xGetElementById(CAMPO_ID).value=record.get("id");
          if(CAMPO_CTA_N) 
            xGetElementById(CAMPO_CTA_N).value=record.get("numero_cuenta");
          if(CAMPO_DESCRIPCION) 
            xGetElementById(CAMPO_DESCRIPCION).value=record.get("denominacion");
          if(CAMPO_CTA_TIPO) 
            xGetElementById(CAMPO_CTA_TIPO).value=record.get("tipo");
          if(CAMPO_BANCO)
            xGetElementById(CAMPO_BANCO).value=record.get("banco");
          if(CAMPO_ID_CUENTA_CONTABLE)
            xGetElementById(CAMPO_ID_CUENTA_CONTABLE).value=record.get("id_cuenta_contable");
          if(CAMPO_CUENTA_CONTABLE)
            xGetElementById(CAMPO_CUENTA_CONTABLE).value=record.get("cuenta_contable");
          if(CAMPO_CUENTA_CONTABLE_DENOMINACION)
            xGetElementById(CAMPO_CUENTA_CONTABLE_DENOMINACION).value=record.get("denominacion_contable");
          
          if(CALLBACK)
            if(eval(CALLBACK)==false)
              return false;          
          return true;
        },			
        onAccept: function(){}
      }
    };
    var selector=new Ext.form.eWindowSelect({width: 920, height: 400, parent: campo});
    selector.show();
    selector.search();
      
      
      
      
    }
    
    function onGetComprobantes(_ids){
      var _tmp=null;
      var _comprobante=[];
      for(var i=0;i<_ids.length;i++){
        _tmp=Ext.Ajax.request({
          async: false,
          url:"modulo_base/comprobante.php",
          params: {
            action: 'onGet',
            id: _ids[i]
          }
        });
        if(_tmp.statusText=="OK"){
          var _retorno=Ext.JSON.decode(_tmp.responseText);
          _comprobante[i]=_retorno[0];					
        }				
      }
      return _comprobante;
    }
    
    </script>
    <?
    print "<script type='text/javascript' src='$form.js'></script>";
    ?>
  </head>
  <body>
    <input type="hidden" id="FECHA_ACTUAL_SIGAFS" value="<?php print date('d/m/Y');?>" />
    <?php
    include("$form.php");
    ?>
  </body>
</html>
<script>
  //onsubmit="return false;", para evitar reenvio del formulario al presionar enter
  var _formulario_sigafs=document.getElementsByTagName("form");
  for(var i=0;i<_formulario_sigafs.length;i++)
    _formulario_sigafs[i].setAttribute("onsubmit","return false;");
</script>
