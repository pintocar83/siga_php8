siga.require.js('library/zbar-processor.min.js');

siga.define('lector_qr', {
    extend: 'siga.windowBase',
    title: 'Lector QR',
    width: 800,
    height: 600,

    initComponent: function(){
      var me = this;

      me.dockedItems=[{
        xtype: 'toolbar',
        id: me._("toolbar"),
        style: 'margin: 0px; padding: 0px;',
        hidden: true,
        flex: 1,
        dock: 'top',
        items: {
          xtype: 'button',
          height: 45,
          width: 55,
          text: 'Escanear',
          cls: 'siga-btn-base',
          iconCls: 'siga-btn-base-icon icon-lector_qr',
          iconAlign: 'top',
          listeners: {
            click: function(){
              me.closeView();
            }
          }
        }
      }];

      me.items=[
        {
          xtype: "siga.video",
          id: me._("camara"),
          width: 800,
          height: 600,
          //resolution: {width: 320, height: 240},
          resolution: {width: 640, height: 480},
          delay: 100,
          stream: function(e){
            var context=me.internal.canvas.getContext('2d');
            context.drawImage(e.video, 0, 0, e.resolution.width, e.resolution.height);
            var imageData = context.getImageData(0, 0, e.resolution.width, e.resolution.height);

            for (var i=0; i<imageData.data.length; i+=4){
              var grey = (imageData.data[i]+imageData.data[i+1]+imageData.data[i+2])/3;
              imageData.data[i] = imageData.data[i+1] = imageData.data[i+2] = grey;
            }

            var result=zbarProcessImageData(imageData);
            console.log("zbarProcessImageData: ",result);
            if (result.length>0) {
              me.openView(result);
            }
          }
        },
        {
          xtype: "container",
          id: me._("view"),
          layout: {
              type: 'vbox',
              align : 'stretch',
              pack  : 'start',
          },
          flex: 1,
          items: [
            {
              xtype: "textfield",
              id: me._("direction"),
              readOnly: true,
            },
            {
              xtype : "component",
              id: me._("frame"),
              flex: 1,
              autoEl : {
                tag : "iframe",
                src : "",
                style: "border: none; background: white;"
              }
            }
          ]
        }
      ];

      me.callParent(arguments);
    },


    init: function(){
      var me=this;

      me.internal.canvas=document.createElement("canvas");
      me.internal.canvas.width=me.getCmp("camara").resolution.width;
      me.internal.canvas.height=me.getCmp("camara").resolution.height;

      me.getCmp("camara").init();
    },

    setDirection: function(type,direction){
      var me=this;
      me.getCmp("direction").setValue("["+type+"] "+direction);
      Ext.get(me._("frame")).set({style:{display:"none"}});
      Ext.get(me._("frame")).set({src:direction});
      Ext.get(me._("frame")).set({style:{display:""}});
    },

    openView: function(result){
      var me=this;

      var type=result[0][0];
      var url=result[0][2];

      if(Ext.form.field.VTypes.url(url)){
        me.getCmp("camara").pause();
        me.getCmp("camara").hide();
        me.getCmp("toolbar").show();

        me.setDirection(type,url);
        me.getCmp("view").show();
        return;
      }

      alert("No es una url valida: "+url);
    },

    closeView: function(){
      var me=this;
      me.getCmp("camara").play();

      Ext.get(me._("frame")).set({src:""});

      me.getCmp("toolbar").hide();
      me.getCmp("camara").show();

      me.getCmp("view").hide();
    }
});