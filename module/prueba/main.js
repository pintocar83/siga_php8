
siga.define('prueba', {
    require: ['library/zbar-processor.min.js'],
    extend: 'siga.windowForm',
    title: 'Ventana de prueba',
    width: 550,
    height: 600,
    
    initComponent: function(){
      var me = this;
      
      me.items=[
        {
          xtype: "textfield",
          fieldLabel: "Campo1"
          
        },
        {
          xtype: "siga.video",
          id: me._("camara_web"),
          fieldLabel: "Camara Web",
          //anchor: '300 200',
          width: 400,
          height: 300,
          //resolution: {width: 320, height: 240},
          resolution: {width: 640, height: 480},
          delay: 100,
          stream: function(e){
            var canvas=document.createElement("canvas");
            canvas.width=e.resolution.width;
            canvas.height=e.resolution.height;            
            
            var context=canvas.getContext('2d');
            context.drawImage(e.video, 0, 0, e.resolution.width, e.resolution.height);
            var imageData = context.getImageData(0, 0, e.resolution.width, e.resolution.height);

            for (var i=0; i<imageData.data.length; i+=4){
              var grey = (imageData.data[i]+imageData.data[i+1]+imageData.data[i+2])/3;
              imageData.data[i] = imageData.data[i+1] = imageData.data[i+2] = grey;
            }
            
            var result=zbarProcessImageData(imageData);
            if (result.length>0) {
              e.capture();
              console.log(result);
              alert(result[0][2]);
              //e.stop();
              
            }
            
            return;
            var canvas=e.getCanvas();
            var context=canvas.getContext('2d');
            //convertir la imagen a escala de grises
            var imageData = context.getImageData(0, 0, me.width,me.height);
            var data = imageData.data;
            for (var i=0; i<data.length; i+=4){
              var grey = (data[i]+data[i+1]+data[i+2])/3;
              data[i] = data[i+1] = data[i+2] = grey;
            }
            //sobre escribir la imagen original
            context.putImageData(imageData, 0, 0); 
            //fin convertir la imagen a escala de grises      
            
            //var imageData = canvas.toDataURL('image/png');
            //console.log(data);
            //console.log(siga.get({action: 'cmd[zbarimg]', image: imageData}));
            
            
            
            
          }
        },
        {
          xtype: "textfield",
          fieldLabel: "Campo2"
          
        },
        
        
      ];
      
      
      
      me.callParent(arguments);
    },
    
    
    init: function(){
      var me=this;
      console.log("Init Prueba");
      me.getCmp("camara_web").init();
    },
});