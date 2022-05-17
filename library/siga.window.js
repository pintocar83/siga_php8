Ext.define('siga.window',{
    extend: 'siga.windowBase',
    
    initComponent: function(){
        var me = this;
        
        if(!me.itemsToolbar)
          me.itemsToolbar=[
              me.btnNew(),
              me.btnSave(),
              me.btnDelete(),
              me.btnCopyPaste(),
              {
                  xtype:'tbspacer',
                  flex:1
              },
              me.btnPrevious(),
              me.btnNext()
          ];
        
        me.addItemMessage();
        
        me.items=[                
            {
                xtype: 'tabpanel',
                id: me._('tabs'),
                margin: 20,
                collapsed: false,
                frameHeader: false,
                activeTab: 0,
                plain: true,
                items: [
                    {
                        xtype: 'form',
                        frame: false,
                        id: me._('tab_data'),
                        frameHeader: false,
                        autoScroll:true,
                        layout: 'anchor',
                        title: 'Entrada de datos',
                        defaults: me.getInternal("field_defaults"),
                        items: me.items
                    },
                    {
                        xtype: 'panel',
                        id: me._('tab_list'),
                        title: 'Listado',
                        layout: 'fit',
                        items: [
                            {
                                xtype: 'gridpanel',
                                id: me._('gridList'),
                                border: 0,
                                preventHeader: true,
                                columns: [],
                                features: me.getInternal("gridList").features,
                                scroll: 'vertical',
                                dockedItems: [
                                    {
                                        xtype: 'pagingtoolbar',
                                        id: me._('pagingList'),
                                        afterPageText: 'de {0}',
                                        beforePageText: 'P\u00e1gina',
                                        displayInfo: true,
                                        displayMsg: 'Mostrando {0} - {1} de {2}',
                                        emptyMsg: 'No hay datos que mostrar',
                                        firstText: 'Primera p\u00e1gina',
                                        dock: 'bottom',
                                        listeners: {
                                            render: function( cmp, eOpts ){                               
                                                me.onSearch();//temporal, error en extjs6 -> no carga el paginado cuando el elemento esta oculto.
                                            }
                                        },
                                    }
                                ],
                                listeners: {
                                    select: function(dataview, record, item, index, e){
                                        me.onGet(dataview, record, item, index, e);
                                    },
                                    itemdblclick: function(dataview, record, item, index, e){
                                        me.getCmp('tabs').setActiveTab(0);
                                    }
                                }
                            }
                        ],
                        dockedItems: [
                            {
                                xtype: 'container',
                                id: me._('container'),
                                layout: {
                                    align: 'middle',
                                    type: 'hbox'
                                },
                                dock: 'bottom',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: me._('txtSearch'),
                                        hideLabel: false,
                                        flex: 1,
                                        listeners: {
                                            specialkey: function(field, e){
                                                if (e.getKey() == e.ENTER) 
                                                    me.onSearch();
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'button',
                                        id: me._('btnSearch'),
                                        text: 'Buscar',
                                        tooltip: 'Buscar',
                                        iconCls: 'siga-icon-16 icon-find',
                                        width: 80,
                                        listeners: {
                                            click: function(){
                                                me.onSearch();
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'button',
                                        id: me._('btnClear'),
                                        text: 'Limpiar',
                                        tooltip: 'Limpiar',
                                        iconCls: 'siga-icon-16 icon-clear',
                                        width: 80,
                                        listeners: {
                                            click: function(){
                                                me.getCmp('txtSearch').setValue('');
                                                me.onSearch();
                                            }
                                        }
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ];
        
        me.callParent(arguments);
    },
    
    onSearch: function(){
        var me=this;        
        me.getCmp('gridList').store.load();
    },

    onNew: function(){
        var me=this;
        me.getCmp('tabs').setActiveTab(0);
        me.getCmp('tab_data').getForm().reset();
        me.onSearch();
    },
    
    onGet: function(dataview, record, item, index, e){
    },
    
    onSave: function(){
    },
    
    onDelete: function(){
    },
    
    onDuplicate: function(){
        var me=this;
        me.onCopy();
        me.onNew();
        me.onPaste();
        me.setMessage("Se duplicó la información del formulario sobre un registro nuevo.","green");
    },
    
    onCopy: function(){
    },
    
    onPaste: function(){
    },
    
    onPrevious: function(){
        var me=this;        
        var grid = me.getCmp('gridList');
        var store = me.getCmp('gridList').store;
        var selModel = grid.getSelectionModel();
        var selectedRecord = selModel.getLastSelected();
        var recordIndex = store.indexOf(selectedRecord);
        var previousRecord = store.getAt(recordIndex-1);
        if(recordIndex==0)
            if(store.currentPage-1 > 0){
                store.loadPage(store.currentPage-1);
                selModel.select(store.pageSize-1);
                return;
            }
        selModel.select(previousRecord);
    },
    
    onNext: function(){
        var me=this;      
        var grid = me.getCmp('gridList');
        var store = me.getCmp('gridList').store;
        var selModel = grid.getSelectionModel();
        var selectedRecord = selModel.getLastSelected();
        if(selectedRecord==null) return;
        var recordIndex = store.indexOf(selectedRecord);
        var nextRecord = store.getAt(recordIndex + 1);
        if(recordIndex+1 >= store.pageSize)
            if(!((store.currentPage+1)>Math.ceil(store.getTotalCount() / store.pageSize))){
                store.loadPage(store.currentPage+1);
                selModel.select(0);
                return;
            }
        selModel.select(nextRecord);
    },
    
    onClickHeader: function( ct, column, e, t, eOpts ){
        var me=this;
        for(var i=0;i<me.internal.sort.length;i++){
            if(me.internal.sort[i].property==column.dataIndex){
                if(me.internal.sort[i].direction=="ASC"){
                    me.internal.sort[i].direction="DESC";
                    column.removeCls('x-column-header-sort-ASC');
                    column.addCls('x-column-header-sort-DESC');
                }
                else if(me.internal.sort[i].direction=="DESC"){
                    column.removeCls('x-column-header-sort-DESC');                    
                    Ext.Array.erase(me.internal.sort,i,1);
                }
                else{
                }
                me.getCmp('gridList').store.sort(me.internal.sort);
                return;
            }
        }        
        //si no existe, agregarlo
        me.internal.sort.push({property: String(column.dataIndex), direction: 'ASC'});
        column.addCls('x-column-header-sort-ASC');
        me.getCmp('gridList').store.sort(me.internal.sort);
    },
    
    onMenuHeader: function(cmp, menu, eOpts){
        menu.removeAll();
    },
    
    onColumnMove: function( ct, column, fromIdx, toIdx, eOpts ){
        var me=this;
        var _tmpsort=new Array();
        for(var i=0;i<ct.getColumnCount();i++){
            var h=ct.getHeaderAtIndex(i);
            for(var j=0;j<me.internal.sort.length;j++){
                if(me.internal.sort[j].property==h.dataIndex){
                    _tmpsort.push({property: String(me.internal.sort[j].property), direction: String(me.internal.sort[j].direction)});
                    break;
                }
            }
        }
        me.getCmp('gridList').store.sort(me.internal.sort);
    }
    
});


