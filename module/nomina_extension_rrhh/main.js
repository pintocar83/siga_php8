siga.require.js("library/ag-grid/ag-grid-enterprise.min.js");

siga.define('nomina_extension_rrhh', {
  cls: 'nomina_extension_rrhh',
  extend: 'siga.windowBase',
  title: 'Nómina - Extensión RRHH',
  width: 850,
  height: 570,
  maximizable:true,
  resizable: true,

  listeners: {
    beforeclose: function(w,o){
      var me=this;
      if(me.internal.dataModified && Object.keys(me.internal.dataModified).length > 0){
        return confirm("Existen cambios realizados sin guardar. Desea salir sin guardarlos?");
      }
      return true;
    },
  },

  initComponent: function(){
    var me = this;

    me.setInternal({
      id_hoja: '',
      data: [],
    });

    //var response = Ext.Ajax.request({async: false,url: "module/nomina_extension_rrhh/form.php"});
    me.itemsToolbar=[
      {
        xtype: 'button',
        id: me._('btnSeleccionar'),
        height: 45,
        width: 65,
        text: 'Seleccionar',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_extension_rrhh_seleccionar',
        iconAlign: 'top',
        tooltip: 'Seleccionar Hoja',
        listeners: {
          click: function(){
            me.onSeleccionar();
          }
        }
      },
      {
        xtype: 'button',
        id: me._('btnSave'),
        height: 45,
        width: 65,
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
      },
      {
        xtype: 'button',
        id: me._('btnAdministrarHoja'),
        height: 45,
        width: 65,
        text: 'Hojas',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_extension_rrhh_administrar_hoja',
        iconAlign: 'top',
        tooltip: 'Administrar Hojas',
        listeners: {
          click: function(){
            siga.open("nomina_extension_rrhh/hoja");
          }
        }
      },
      //me.btnNew(),
      //me.btnEdit(),
      //me.btnDelete(),
      //me.btnDisplay(),
      {
        xtype: "tbspacer",
        flex:1
      },
      {
        xtype: 'button',
        id: me._('btnDescargarXLS'),
        height: 45,
        width: 65,
        text: 'XLS',
        cls: 'siga-btn-base',
        iconCls: 'siga-btn-base-icon icon-nomina_extension_rrhh_xls',
        iconAlign: 'top',
        tooltip: 'Descargar en formato Excel',
        listeners: {
          click: function(){
            if(!me.internal.gridOptions) return;
            me.internal.gridOptions.api.exportDataAsExcel();
          }
        }
      },
    ];

    me.items=[
      {
        xtype: "component",
        html: `
          <style>
            .ag-theme-alpine {
              --ag-borders: solid 1px;
              --ag-grid-size: 3px;
              --ag-list-item-height: 20px;
              --ag-font-size: 10px;
              --ag-font-family: tahoma, arial, verdana, sans-serif;
            }
            .ag-theme-alpine .ag-row-group-expanded.ag-row.ag-row-level-0,
            .ag-theme-alpine .ag-row-group-contracted.ag-row.ag-row-level-0 {
              background-color: #434343;
              color: white;
              font-weight: 600;
            }
            .ag-theme-alpine .ag-row-group-expanded.ag-row.ag-row-level-0 .ag-group-expanded,
            .ag-theme-alpine .ag-row-group-contracted.ag-row.ag-row-level-0 .ag-group-contracted {
              color: white;
            }
            .ag-theme-alpine .ag-header-cell-label {
              font-size: 10px;
            }
            .ag-theme-alpine .ag-cell-value.align-center,
            .ag-theme-alpine .ag-cell-value.align-center input.ag-input-field-input {
              text-align: center;
            }
            .ag-theme-alpine .ag-cell-value.align-right,
            .ag-theme-alpine .ag-cell-value.align-right input.ag-input-field-input {
              text-align: right;
            }
           .ag-theme-alpine .column-editable {
              background-color: #ffeb3b0f;
            }
            .ag-theme-alpine {
                --ag-row-hover-color: rgb(255 229 0 / 10%);;
            }
            .ag-theme-alpine .ag-cell:not(:first-child) {
              border-left: 1px solid #dde2eb;
            }
          </style>
          <div id="agGridExtensionRRHH" class="ag-theme-alpine" style="height: 100%">
        `
      }
    ];

    me.callParent(arguments);
  },

  $: function(id){
    var me = this;
    return me.getEl().getById(id,true);
  },

  $$: function(id){
    var me = this;
    return Ext.get(me.$(id));
  },

  init: function(){
    var me = this;
    me.maximize();

    $(window).bind("beforeunload", function(){
      if(me.internal.dataModified && Object.keys(me.internal.dataModified).length > 0){
        return confirm("Existen cambios sin guardar y se perderan. Desea continuar?");
      }
    });

    //me.onNew();
  },

  /**
  * Nueva definicion
  */
  onNew: function(){
    var me = this;


  },

  onSeleccionar: function(){
    var me=this;

    var campo={
      fieldLabel: 'Hojas de Trabajo',
      setValue: function(v){
        me.onGet(v);
      },
      internal:{
        page:1,
        limit: 100,
        valueField: 'id',
        columns: {field: ["codigo","descripcion"], title: ["Código","Descripción"], width: ['15%','85%'], sort: ["ASC",'NULL']},
        url: 'module/nomina_extension_rrhh/hoja/',
        actionOnList:'onList',
        actionOnGet:'onGet',
      }
    };

    var _opt={};
    _opt.internal={};
    _opt.internal.parent=campo;
    var selector=Ext.create("siga.windowSelect",_opt);
    selector.show();
    selector.search();
  },

  onGet: function(id_hoja){
    var me=this;
    me.internal.id_hoja = id_hoja;

    if(me.internal && me.internal.gridOptions && me.internal.gridOptions.api && me.internal.gridOptions.api.destroy){
      me.internal.gridOptions.api.destroy();
    }
    me.internal.dataModified={};

    var msgWait=Ext.Msg.wait('Cargando. Por favor espere...', me.getTitle(),{text:''});
    msgWait.setAlwaysOnTop(true);

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/',
      params:{
        action: 'onGet',
        id_hoja: id_hoja,
      },
      success:function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);
        me.internal.data = result;
        me.onRecargar();
      },
      failure:function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);
      }
    });
  },

  onRecargar: function(){
    var me=this;
    me.internal.dataModified={};

    for(var i=0; i<me.internal.data["columna"].length; i++) {
      if(me.internal.data["columna"][i]["valueFormatter"]){
        let format = me.internal.data["columna"][i]["valueFormatter"].split("|");
        switch(format[0]){
          case "numeric":
            me.internal.data["columna"][i]["valueFormatter"] = (params) => {
              return formatNumber(params.value)
            }
            break;
          case "%":
          case "percent":
            me.internal.data["columna"][i]["valueFormatter"] = (params) => {
              return params.value > 0 ? params.value+"%" : params.value
            }
            break;
          case "date":
            me.internal.data["columna"][i]["valueFormatter"] = (params) => {
              if(format.length===2){
                return params.value ? Ext.Date.format(new Date(params.value + " 00:00:00"), format[1]) : params.value
              }
              return params.value ? formatDate(params.value) : params.value
            }
            //me.internal.data["columna"][i]["cellDataType"] = "dateString";
            //me.internal.data["columna"][i]["filter"] = "agDateColumnFilter";
            //me.internal.data["columna"][i]["floatingFilterComponentParams"] = false;
            /*
            me.internal.data["columna"][i]["filter"] = "agSetColumnFilter";
            me.internal.data["columna"][i]["filterParams"] = {
              textFormatter: (value) => {
                console.log("value",value);
                return value;
              }
            };*/
            break;
        }
      }
    }

    console.log(me.internal.data);

    me.internal.gridOptions = {
      columnDefs: me.internal.data["columna"],
      defaultColDef: {
        flex: 1,
        minWidth: 30,
        sortable: true,
        resizable: true,
        enableRowGroup: true,
        enablePivot: true,
        enableValue: true,
        filter: true,
        floatingFilter: true,
        wrapHeaderText: true,
        autoHeaderHeight: true,
      },
      sideBar: {
        toolPanels: ['columns'],
      },
      rowGroupPanelShow: 'always',
      pivotPanelShow: 'always',
      groupDisplayType: 'groupRows',
      groupDefaultExpanded: -1,
      animateRows: true,
      rowData: me.internal.data["data"],
      enableRangeSelection: true,
      //editType: 'fullRow',
      onGridReady: (params) => {
        //me.autoSizeAllColumns();
      },
      //onCellEditRequest: (event) => {
      //  console.log('onCellEditRequest', event);
      //},
      onCellValueChanged: (event) => {
        var data = event.data;
        if(!me.internal.dataModified){
          me.internal.dataModified={};
        }

        const rowId      = event.data.id;
        const id_columna = String(event.column.colId).substring(7);
        const id_nomina  = event.data.id_nomina;
        const id_ficha   = event.data.id_ficha;
        const value      = event.value;

        const hash = rowId + "|" + id_columna;

        me.internal.dataModified[hash] = {
          id_nomina: id_nomina,
          id_ficha: id_ficha,
          id_columna: id_columna,
          valor: value
        };
        me.setGuardarCambios(true);
      },
      onRowValueChanged: (event) => {
        var data = event.data;
        console.log(
          'onRowValueChanged:', event
        );
      }
    };

    var gridDiv = document.querySelector('#agGridExtensionRRHH');
    new agGrid.Grid(gridDiv, me.internal.gridOptions);

    //me.internal.gridOptions.api.setRowData(me.internal.data["data"]);
    //me.autoSizeAllColumns();
  },

  autoSizeAllColumns: function(skipHeader) {
    var me=this;
    const allColumnIds = [];
    me.internal.gridOptions.columnApi.getColumns().forEach((column) => {
      allColumnIds.push(column.getId());
    });

    me.internal.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
  },

  onSave: function(){
    var me=this;

    if(!me.internal.id_hoja){
      return;
    }

    var msgWait=Ext.Msg.wait('Guardando. Por favor espere...', me.getTitle(),{text:''});
    msgWait.setAlwaysOnTop(true);

    var data = [];
    if(me.internal.dataModified){
      for(key in me.internal.dataModified){
        data.push(me.internal.dataModified[key]);
      }
    }

    var ag_grid_state = me.internal.gridOptions.columnApi.getColumnState();

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/',
      params:{
        action: 'onSave',
        id_hoja: me.internal.id_hoja,
        data: Ext.JSON.encode(data),
        ag_grid_state: Ext.JSON.encode(ag_grid_state)
      },
      success: function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);

        if(result.success){
          //me.setMessage(result.message,"green");
          me.internal.dataModified = [];
          me.setGuardarCambios(false);
        }
        else{
          //me.setMessage(result.message,"red");
          alert(result.message);
        }
      },
      failure:function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);
        me.setMessage(result.message,"red");
      }
    });



  },


  setGuardarCambios: function(sw){
    var me=this;
    if(sw===true){
      me.getCmp("btnSave").setText("<b>Guardar</b>");
      me.getCmp("btnSave").addCls("btn_blink");
    }
    else if(sw===false){
      me.getCmp("btnSave").setText("Guardar");
      me.getCmp("btnSave").removeCls("btn_blink");
    }

  },
});



/*

GUARDAR Y RESTAURAR ESTADO DE LAS COLUMNAS

function saveState() {
  window.colState = gridOptions.columnApi.getColumnState();
  console.log('column state saved');
}

function restoreState() {
  if (!window.colState) {
    console.log('no columns state to restore by, you must save state first');
    return;
  }
  gridOptions.columnApi.applyColumnState({
    state: window.colState,
    applyOrder: true,
  });
  console.log('column state restored');
}

function resetState() {
  gridOptions.columnApi.resetColumnState();
  console.log('column state reset');
}

*/

