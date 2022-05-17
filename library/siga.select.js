Ext.define('siga.select', {
    alias: 'widget.siga.select',
    extend: 'Ext.form.FieldContainer',
    mixins: {
        field: 'Ext.form.field.Field'
    },
    layout: 'fit',
    fieldLabel: '',
    labelAlign: 'top',    
    width: 500,
    widthButton: 80,
    
    valueField: null,
    displayField: null,

    listeners: {
        render: function(c) {
            var me=this;
            Ext.destroy(me.getCmp("title"));
            Ext.create('Ext.tip.ToolTip', {                
                target: c.getEl(),
                id: me._("title"),
                autoDestroy: true,
                html: 'Sin selección'
            });
        }
    },
    
    internal:{
        text: null,
        page: 1,
        limit: null,
        valueField: '',
        columns: {field: [], title: [], width: [], sort: [], align: []},
        url: '',
        actionOnList: '',
        actionOnGet: '',
        output: 0,
        //0=los campos de la lista en el display en una sola linea.
        //1=los campos de la lista uno debajo de otro como campos de texto
        //2=los campos de la lista uno debajo de otro con un fieldset y mostrando los campos como texto con los titulos.
        //3=muestra en un fieldset el campo display del retorno del actionOnGet
        //4=muestra en un label el campo display del retorno del actionOnGet
        //5=muestra en el displayField[0] campo display del retorno del actionOnGet
        onAccept: null,
        onBeforeAccept: null,
        viewConfig: null,
        clearBtn: false,
        result: null,
        onGetFn: null,
    },
    
    _: function(id){
        var me=this;
        return me.id+"-"+id;
    },
    
    getCmp: function(id){
        var me=this;
        return Ext.getCmp(me._(String(id)));
    },
    
    initComponent: function(){
        var me = this;
        
        me.items = me.items || [];
        
        me.valueField = Ext.create('Ext.form.field.Hidden', Ext.apply({
            isFormField:true,
            submitValue:false,
            readOnly: true,
            parent: me,
            value: me.value,
            listeners:{
                change: function( e, newValue, oldValue, eOpts ){
                    me.change( e, newValue, oldValue, eOpts );
                    }
            }
        }));
        
        me.items.push(me.valueField);
        
        var _width100p=me.width-me.widthButton-(me.labelAlign=='top'?0:(me.labelWidth+me.labelPad));
        var _widthCol="";
        me.displayField=new Array();
        
        var button={
            xtype: 'button',
            text: 'Buscar',
            iconCls: 'siga-icon-16 icon-find',
            width: me.widthButton,
            listeners: {
                click: function() {
                    var _opt={};
                    _opt.internal={};
                    _opt.internal.parent=me;
                    if(me.internal.width) 
                        _opt.width=me.internal.width;
                    if(me.internal.height) 
                        _opt.height=me.internal.height;
                    
                    //var selector=new Ext.form.eWindowSelect(_opt);
                    var selector=Ext.create("siga.windowSelect",_opt);
                    selector.show();
                    selector.search();
                }
            }
        };
        
        var button_clear={
            xtype: 'button',
            text: 'Limpiar',
            iconCls: 'siga-icon-16 icon-clear',
            width: me.widthButton,
            listeners: {
                click: function() {
                    me.setValue("");
                }
            }
        };
        
        var container={
            xtype: 'container',            
            layout: 'hbox',
            items:[]
        };
        
        if(!me.internal.output) me.internal.output=0;
        if(!me.internal.clearBtn) me.internal.clearBtn=false;

        switch(me.internal.output){
            case 0:
                var sub_container={
                    xtype: 'container',
                    layout: 'hbox',
                    flex: 1,
                    items:[]
                    };
                
                for(var i=0;i<me.internal.columns.title.length;i++){
                    me.displayField[i] = Ext.create('Ext.form.field.Text', Ext.apply({
                        width: me.internal.columns.width[i],
                        isFormField:false,
                        submitValue:false,
                        readOnly: true,                        
                        //style: 'margin-top: 1px;'
                        }));
                    sub_container.items.push(me.displayField[i]);
                }
                
                container.items.push(sub_container);
                container.items.push(button);
                if(me.internal.clearBtn)
                    container.items.push(button_clear);
                me.items.push(container);

            break; 
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                me.displayField[0] = Ext.create('Ext.form.field.Text', Ext.apply({
                    isFormField:false,
                    submitValue:false,
                    readOnly: true,
                    flex: 1,
                    //style: 'margin-top: 1px;'
                    }));
                
                container.items.push(me.displayField[0]);
                container.items.push(button);
                if(me.internal.clearBtn)
                    container.items.push(button_clear);

                me.items.push(container);
                
                switch(me.internal.output){
                    case 1:
                        for(var i=1;i<me.internal.columns.title.length;i++){      
                            me.displayField[i] = Ext.create('Ext.form.field.Text', Ext.apply({
                                width: '100%',
                                isFormField:false,
                                submitValue:false,
                                readOnly: true,
                                style: 'margin-top: 2px;'
                                }));
                            me.items.push(me.displayField[i]);            
                        }
                    break;
                    case 2:
                    case 3:
                        me.items.push({
                            xtype: 'fieldset',
                            title: 'Detalles',
                            collapsible: true,
                            flex: 1,
                            items :[{
                                xtype: 'label',
                                id: me._('display'),
                                text: ''
                            }]
                        });                        
                    break;
                    case 4:
                        me.items.push({
                            xtype: 'label',
                            id: me._('display'),
                            flex: 1,
                            style: 'padding-left: 20px;',
                            text: ''
                        });     
                    break;
                    case 5:
                    break;
                }
                break;       
            
        }        
     
        me.callParent(arguments);
        
        if(!me.internal.output)
            me.internal.output=0;
        me.internal.text='';
        me.internal.page=1;
        if(!me.internal.limit)
            me.internal.limit=100;
        me.setValue(me.value);
    },    
    
    getValue: function(){
        var me=this;
        return me.valueField.getValue();
    },
    
    change: function( e, newValue, oldValue, eOpts ){
        var me=e.parent;        
        var v=newValue;
        switch(me.internal.output){
            case 3:
            case 4:
                me.displayField[0].setValue('');
                var _display="";
                me.getCmp("display").setText(_display,false);
                break;
            case 5:
                me.displayField[0].setValue('');
                break;
            case 2:
                me.displayField[0].setValue('');
                var _display="";
                _display+="<table cellspacing='0' cellpadding='0'>";
                for(var i=0;i<me.internal.columns.title.length;i++)
                    _display+="<tr><td><b>"+me.internal.columns.title[i]+"</b></td><td style='padding-left:10px;'></td></tr>";
                _display+="</table>";
                me.getCmp("display").setText(_display,false);
                break;            
            case 1:
            case 0:
            default:
                for(var i=0;i<me.internal.columns.title.length;i++)
                    me.displayField[i].setValue('');
                break;
        }
        
        if(v==null)
            return;
        if(Ext.String.trim(v)=="")
            return;

        Ext.Ajax.request({
            url:me.internal.url,
            params: Ext.JSON.decode("{action: '"+me.internal.actionOnGet+"', "+me.internal.valueField+": '"+v+"'}"),
            success:function(resp){
                var retorno=Ext.JSON.decode(resp.responseText);
                me.internal.result=retorno;
                if(me.internal.onGetFn) me.internal.onGetFn(retorno);

                switch(me.internal.output){
                    case 3:
                    case 4:
                        if(!retorno || retorno.length==0){
                            me.valueField.setValue('');
                            me.displayField[0].setValue("");
                        }
                        me.displayField[0].setValue(retorno[0][me.internal.columns.field[0]]);
                        var _display=retorno[0]["display"];
                        me.getCmp("display").setText(_display,false);
                        break;
                    case 5:
                        if(!retorno || retorno.length==0){
                            me.valueField.setValue('');
                            me.displayField[0].setValue("");
                        }
                        me.displayField[0].setValue(retorno[0]["display"]);                        
                        break;
                    case 2:
                        if(!retorno || retorno.length==0){
                            me.valueField.setValue('');
                            me.displayField[0].setValue("");
                        }
                        me.displayField[0].setValue(retorno[0][me.internal.columns.field[0]]);
                        var _display="";
                        _display+="<table cellspacing='0' cellpadding='0'>";
                        for(var i=0;i<me.internal.columns.title.length;i++)
                            _display+="<tr><td><b>"+me.internal.columns.title[i]+"</b></td><td style='padding-left:10px;'>"+retorno[0][me.internal.columns.field[i]]+"</td></tr>";
                        _display+="</table>";
                        me.getCmp("display").setText(_display,false);
                        
                        break;
                    case 1:
                    case 0:
                    default:
                        if(!retorno || retorno.length==0){
                            me.valueField.setValue('');
                            for(var i=0;i<me.internal.columns.title.length;i++)
                                me.displayField[i].setValue('');
                            return;
                        }                        
                        for(var i=0;i<me.internal.columns.title.length;i++)
                            me.displayField[i].setValue(retorno[0][me.internal.columns.field[i]]);
                        break;
                }
                //me.getCmp("title").update("");
                //if(retorno[0]["title"])
                //alert(retorno[0]["title"]);
                //alert(me.getCmp("title").id);
                if(retorno[0]["title"])
                    me.getCmp("title").update(retorno[0]["title"]);
                else{
                    var _display="";
                    _display+="<table cellspacing='0' cellpadding='0'>";
                    for(var i=0;i<me.internal.columns.title.length;i++)
                        _display+="<tr><td><b>"+me.internal.columns.title[i]+":</b></td><td style='padding-left:10px;'>"+retorno[0][me.internal.columns.field[i]]+"</td></tr>";
                    _display+="</table>";
                    me.getCmp("title").update(_display);
                }
            },
            failure:function(result, request){
               alert("error en la consulta");
            }
        });
    },
    
    setValue: function(v){
        var me=this;
        me.value=v;
        me.valueField.setValue('');
        me.valueField.setValue(v);
    }
});

Ext.define('siga.windowSelect', {
    extend: 'siga.windowBase',
    width: 762,
    height: 426,
    activeItem: 0,
    modal: true,
    renderTo: Ext.getBody(),
    gridList: null,
    pagingList: null,
    txtSearch: null,
    
    listeners: {
        beforeclose: function(){
            var me=this;            
            me.internal.parent.internal.page=me.gridList.store.currentPage;
            //me.getCmp("title").destroy();
        }
    },

    initComponent: function() {
        var me = this;

        me.setInternal({
            sort: null
        //    parent: null
        });

        me.title=me.internal.parent.fieldLabel;
        
        me.pagingList = Ext.create('Ext.toolbar.Paging', Ext.apply({
            afterPageText: 'de {0}',
            beforePageText: 'P\u00e1gina',
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: 'No hay datos que mostrar',
            firstText: 'Primera p\u00e1gina',
            dock: 'bottom'
        }));
        var gridList_features=null;
        if(me.internal.parent.internal.gridList)
            if(me.internal.parent.internal.gridList.features)
                gridList_features=me.internal.parent.internal.gridList.features;
        
        me.gridList = Ext.create('Ext.grid.Panel', Ext.apply({
            preventHeader: true,
            border: 0,
            columns: [],
            dockedItems: [me.pagingList],
            features: gridList_features,
            scroll: 'vertical',
            listeners: {
                itemdblclick: function(dataview, record, item, index, e){
                    if(me.internal.parent.internal.onBeforeAccept){
                        if(!me.internal.parent.internal.onBeforeAccept(dataview, record, item, index, e))//si retorna falso salir, deberia usarse para verificar algo
                            return;
                    }
                    me.internal.parent.setValue(record.get(me.internal.parent.internal.valueField));
                    
                    me.close();
                    if(me.internal.parent.internal.onAccept)
                        me.internal.parent.internal.onAccept();
                }
            },
            viewConfig: me.internal.parent.internal.viewConfig
        }));
        
        //clearOtherSortStates
        me.items=[me.gridList];
        
        me.txtSearch = Ext.create('Ext.form.field.Text', Ext.apply({
            hideLabel: false,
            flex: 1,
            emptyText: 'Escriba el texto y presione enter para buscar',
            listeners: {
                specialkey: function(field, e){
                    if (e.getKey() == e.ENTER) 
                        me.search();
                }
            }
        }));
        
        me.dockedItems=[{
            xtype: 'container',
            layout: {
                align: 'middle',
                type: 'hbox'
            },
            dock: 'bottom',
            items: [
                me.txtSearch,
                {
                    xtype: 'button',
                    text: 'Buscar',
                    tooltip: 'Buscar',
                    iconCls: 'siga-icon-16 icon-find',
                    width: 80,
                    listeners: {
                        click: function(){
                            me.search();
                        }
                    }
                },
                {
                    xtype: 'button',
                    text: 'Limpiar',
                    tooltip: 'Limpiar',
                    iconCls: 'siga-icon-16 icon-clear',
                    width: 80,
                    listeners: {
                        click: function(){
                            me.txtSearch.setValue('');
                            me.search();
                        }
                    }
                }
            ]
        }];

        me.callParent(arguments);
        
        //me.internal.sort=[{property: 'fecha_inicio', direction: 'DESC'},{property: 'codigo', direction: 'DESC'}];
        me.internal.sort=me.sort();

        me.configureList();        
        me.txtSearch.setValue(me.internal.parent.internal.text);
        me.search();
    },
    
    sort: function(){
        var me=this;
        var _sort=new Array();
        
        if(me.internal.parent.internal.gridList)
            if(me.internal.parent.internal.gridList.groupField)
                _sort.push({property: me.internal.parent.internal.gridList.groupField, direction: "ASC"});
        
        for(var i=0;i<me.internal.parent.internal.columns.title.length;i++){
            //si es NULL no tomalo encuenta
            if(String(me.internal.parent.internal.columns.sort[i]).toUpperCase()=="NULL") 
                continue;
            _sort.push({property: me.internal.parent.internal.columns.field[i], direction: me.internal.parent.internal.columns.sort[i]});
        }
        return _sort;
    },
    
    configureList: function(){
        var me=this;
        
        var gridList_groupField=null;
        if(me.internal.parent.internal.gridList)
            if(me.internal.parent.internal.gridList.groupField)
                gridList_groupField=me.internal.parent.internal.gridList.groupField;
        
        var _extraParams={
          action: me.internal.parent.internal.actionOnList,
          text: ''
        };
        
        if(me.internal.params)
          _extraParams=Ext.Object.merge(_extraParams,me.internal.params);
        if(me.internal.parent.internal.params)
          _extraParams=Ext.Object.merge(_extraParams,me.internal.parent.internal.params);
          
        var store= new Ext.data.Store({
            pageSize: me.internal.parent.internal.limit,
            currentPage: me.internal.parent.internal.page,
            fields: Ext.Array.merge(me.internal.parent.internal.columns["field"],me.internal.parent.internal.valueField),
            autoLoad: false,
            remoteSort: true,
            sorters: me.sort(),
            groupField: gridList_groupField,
            proxy: {
                type:'ajax',
                url: me.internal.parent.internal.url,
                actionMethods:  {read: "POST"},//actionMethods:'POST',
                timeout: 3600000,
                reader: {
                    type: 'json',
                    rootProperty: 'result',
                    totalProperty:'total'
                },
                extraParams: _extraParams
            },
            listeners: {
                load: function(store, records, successful){
                },
                beforeload: function(store,operation,eOpts){
                    me.internal.parent.internal.text=me.txtSearch.getValue();
                    if(me.internal.parent.internal.extraParams){
                        me.internal.parent.internal.extraParams.text=me.txtSearch.getValue();
                        me.internal.parent.internal.extraParams.action=me.internal.parent.internal.actionOnList;
                        store.proxy.extraParams=me.internal.parent.internal.extraParams;
                    }
                    else                    
                        store.proxy.extraParams.text=me.txtSearch.getValue();
                    
                }
            }
        });
        
        var columns=Array();
        for(var i=0;i<me.internal.parent.internal.columns.title.length;i++){
            var align="left";
            if(me.internal.parent.internal.columns.align)
                if(me.internal.parent.internal.columns.align[i])
                    align=me.internal.parent.internal.columns.align[i];
            
            var _cls="x-column-header-sort-"+String(me.internal.parent.internal.columns.sort[i]).toUpperCase();
            if(String(me.internal.parent.internal.columns.sort[i]).toUpperCase()=="NULL") 
                _cls="";
            
            columns[i]={
                xtype: 'gridcolumn',                
                dataIndex: me.internal.parent.internal.columns.field[i],
                text: "<b>"+me.internal.parent.internal.columns.title[i]+"</b>",
                align: align,
                menuDisabled: true,
                sortable: false,
                cls: _cls,
                listeners:{
                    headerclick: function( ct, column, e, t, eOpts ){
                        //si es "NULL" en parent.internal.columns.sort no ordenar por esa columna
                        for(var i=0;i<me.internal.parent.internal.columns.field.length;i++)
                            if(column.dataIndex==me.internal.parent.internal.columns.field[i]) {
                                if(String(me.internal.parent.internal.columns.sort[i]).toUpperCase()=="NULL"){
                                    return;
                                }
                            }
                        
                        for(var i=0;i<me.internal.sort.length;i++){
                            if(me.internal.sort[i].property==column.dataIndex){
                                if(String(me.internal.sort[i].direction).toUpperCase()=="ASC"){
                                    me.internal.sort[i].direction="DESC";
                                    column.removeCls('x-column-header-sort-ASC');
                                    column.addCls('x-column-header-sort-DESC');
                                }
                                else if(String(me.internal.sort[i].direction).toUpperCase()=="DESC"){
                                    column.removeCls('x-column-header-sort-DESC');                    
                                    Ext.Array.erase(me.internal.sort,i,1);
                                } 
                                else{
                                }
                                me.gridList.store.sort(me.internal.sort);
                                return;
                            }
                        }
                        me.internal.sort.push({property: String(column.dataIndex), direction: 'ASC'});
                        column.addCls('x-column-header-sort-ASC');
                        me.gridList.store.sort(me.internal.sort);
                    }
                }
            };
            if(me.internal.parent.internal.columns.width)
                if(me.internal.parent.internal.columns.width[i])
                    columns[i].width=me.internal.parent.internal.columns.width[i];            
            if(i==me.internal.parent.internal.columns.title.length-1)
                columns[i].flex=1;
        }
        
        me.gridList.reconfigure(store,columns);
        //me.gridList.bindStore(store);
        me.pagingList.bindStore(store);
    },
    
    search : function(){
        var me=this;
        me.internal.parent.internal.page=1;
        me.gridList.store.loadPage(me.internal.parent.internal.page);
        me.txtSearch.focus();
    }
});


