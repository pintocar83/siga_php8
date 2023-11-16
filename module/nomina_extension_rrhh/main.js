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
      me.btnNew(),
      me.btnSave(),
      me.btnEdit(),
      me.btnDelete(),
      me.btnDisplay(),
      {
        xtype: "tbspacer",
        flex:1
      },
    ];

    me.items=[
      {
        xtype: "component",
        html: `<div id="myGrid" class="ag-theme-alpine" style="height: 100%">`
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

    const gridOptions1 = {
      columnDefs: [
        // set filters
        { field: 'athlete', filter: 'agSetColumnFilter'},
        {
          field: 'country',
          filter: 'agSetColumnFilter',
          filterParams: {
            applyMiniFilterWhileTyping: true,
          },
        },

        // number filters
        { field: 'gold', filter: 'agNumberColumnFilter' },
        { field: 'silver', filter: 'agNumberColumnFilter' },
        { field: 'bronze', filter: 'agNumberColumnFilter' },
      ],
      defaultColDef: {
        flex: 1,
        minWidth: 200,
        resizable: true,
        floatingFilter: true,
      },
    };

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
      },
      groupDisplayType: 'groupRows',
      animateRows: true,
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
      },
      groupDisplayType: 'groupRows',
      animateRows: true,
    };

    var gridDiv = document.querySelector('#myGrid');
    new agGrid.Grid(gridDiv, me.internal.gridOptions);

    me.internal.gridOptions.api.setRowData(me.internal.data["data"]);
  },


});