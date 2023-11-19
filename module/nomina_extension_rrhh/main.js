siga.require.js("library/ag-grid/ag-grid-enterprise.min.js");

siga.define('nomina_extension_rrhh', {
  extend: 'siga.windowBase',
  title: 'Nómina - Extensión RRHH',
  width: 850,
  height: 570,
  maximizable:true,
  resizable: true,

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
      me.btnSave(),
      //me.btnNew(),
      //me.btnEdit(),
      //me.btnDelete(),
      //me.btnDisplay(),
      {
        xtype: "tbspacer",
        flex:1
      },
    ];

    me.items=[
      {
        xtype: "component",
        html: `
          <style>
            .ag-theme-alpine {
              --ag-borders: solid 1px;
              --ag-grid-size: 4px;
              --ag-list-item-height: 20px;
              --ag-font-size: 11px;
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
            .ag-cell-value.align-center,
            .ag-cell-value.align-center input.ag-input-field-input {
              text-align: center;
            }
            .ag-cell-value.align-right,
            .ag-cell-value.align-right input.ag-input-field-input {
              text-align: right;
            }
          </style>
          <div id="myGrid" class="ag-theme-alpine" style="height: 100%">
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

    //me.onNew();
  },

  /**
  * Nueva definicion
  */
  onNew: function(){
    var me = this;

    const gridOptions = {
      columnDefs: [
        { field: 'country', rowGroup: true, hide: true },
        { field: 'year', rowGroup: true, hide: true },
        {
          field: 'athlete',
          minWidth: 250,
          cellRenderer: (params) => {
            return `<span style="margin-left: 60px">${params.value}</span>`;
          },
        },
        { field: 'sport', minWidth: 200 },
        { field: 'gold' },
        { field: 'silver' },
        { field: 'bronze' },
      ],
      defaultColDef: {
        flex: 1,
        minWidth: 100,
        sortable: true,
        resizable: true,
        columnHoverHighlight: true,
      },
      groupDisplayType: 'groupRows',
      animateRows: true,
      //suppressRowHoverHighlight: true,
      columnHoverHighlight: true,
    };

    /*var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    fetch('https://www.ag-grid.com/example-assets/olympic-winners.json')
      .then((response) => response.json())
      .then((data) => gridOptions.api.setRowData(data));

    */
    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    fetch('https://www.ag-grid.com/example-assets/olympic-winners.json')
      .then((response) => response.json())
      .then(function (data) {
        gridOptions.api.setRowData(data);
      });

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

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/',
      params:{
        action: 'onGet',
        id_hoja: id_hoja,
      },
      success:function(request){
        var result=Ext.JSON.decode(request.responseText);
        me.internal.data = result;
        me.onRecargar();
      },
      failure:function(request){
        var result=Ext.JSON.decode(request.responseText);
      }
    });
  },

  onRecargar: function(){
    var me=this;

    console.log(me.internal.data);

    me.internal.gridOptions = {
      columnDefs: me.internal.data["columna"],
      defaultColDef: {
        flex: 1,
        minWidth: 100,
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
      },
      onRowValueChanged: (event) => {
        var data = event.data;
        console.log(
          'onRowValueChanged:', event
        );
      }
    };

    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, me.internal.gridOptions);

    //me.internal.gridOptions.api.setRowData(me.internal.data["data"]);
    me.autoSizeAllColumns();
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

    Ext.Ajax.request({
      method: 'POST',
      url:'module/nomina_extension_rrhh/',
      params:{
        action: 'onSave',
        id_hoja: me.internal.id_hoja,
        data: Ext.JSON.encode(data),
        ag_grid_state: []
      },
      success: function(request){
        msgWait.close();
        var result=Ext.JSON.decode(request.responseText);

        if(result.success){
          //me.setMessage(result.message,"green");
          me.internal.dataModified = [];
          me.onNew();
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

