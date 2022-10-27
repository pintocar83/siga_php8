Ext.define('siga.video', {
  alias: 'widget.siga.video',
  extend: 'Ext.form.FieldContainer',
  mixins: {
    field: 'Ext.form.field.Field'
  },
  layout: 'absolute',
  fieldLabel: '',
  labelAlign: 'top',
  width: 320,
  height: 240,
  delay: 200,
  video: null,
  videoStyle: "",
  internal: {
    delay_id: null
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

    if(!me.resolution) me.resolution={};
    if(!me.resolution.width) me.resolution.width=320;
    if(!me.resolution.height) me.resolution.height=240;

    me.items = me.items || [];
    me.items=[
      {
        xtype : "component",
        id: me._("video_stream"),
        x: 0,
        y: 0,

        autoEl : {
          tag : "video",
          width: 0,
          height: 0,
          style: me.videoStyle
        }
      }
    ];

    me.callParent(arguments);
  },

  /*
  init: function(){
    var me=this;

    var h=me.height-me.labelEl.getHeight();
    var scale=h/me.height;
    var w=me.width*scale;

    me.video=document.getElementById(me._("video_stream"));

    Ext.get(me.video).set({
      style: {
        width: w+"px",
        height: h+"px"
      },
      width: me.resolution.width,
      height: me.resolution.height
    });

    if(me.onClick){
      Ext.get(me.video).set({
        title: "Click para realizar captura"
      });
      Ext.get(me.video).on("click", me.onClick, me);
    }

    navigator.getMedia = ( navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
    //navigator.getMedia = navigator.mediaDevices.getUserMedia;
    if(navigator.getMedia){
      me.context="getusermedia";
      navigator.getMedia(
        {video:true, audio:false},
        function(stream){
          if(navigator.mozGetUserMedia){
            me.video.mozSrcObject = stream;
          }
          else{
            var vendorURL = window.URL || window.webkitURL;
            var video_src = vendorURL.createObjectURL(stream);
            me.video.src = video_src;
          }
          me.play();
        },
        function(err) {
          alert("An error occured! " + err);
        }
      );
      return;
    }
    alert("El navegador no soporta la opción 'navigator.getUserMedia'");
  },
  */

  init: function(){
    var me=this;

    var h=me.height-me.labelEl.getHeight();
    var scale=h/me.height;
    var w=me.width*scale;

    me.video=document.getElementById(me._("video_stream"));

    Ext.get(me.video).set({
      style: {
        width: w+"px",
        height: h+"px"
      },
      width: me.resolution.width,
      height: me.resolution.height
    });

    if(me.onClick){
      Ext.get(me.video).set({
        title: "Click para realizar captura"
      });
      Ext.get(me.video).on("click", me.onClick, me);
    }

    if (navigator.mediaDevices === undefined) {
      navigator.mediaDevices = {};
    }


    if (navigator.mediaDevices.getUserMedia === undefined) {
      navigator.mediaDevices.getUserMedia = function(constraints) {
        var getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
        if (!getUserMedia) {
          return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
        }

        return new Promise(function(resolve, reject) {
          getUserMedia.call(navigator, constraints, resolve, reject);
        });
      }
    }

    var front=false;//no usar la camara frontal
    navigator.mediaDevices.getUserMedia({
      audio: false,
      video: {
        facingMode: (front? "user" : "environment")
      }
    })
    .then(function(stream) {
      if("srcObject" in me.video) {
        me.video.srcObject = stream;
      }
      else {
        me.video.src = window.URL.createObjectURL(stream);
      }
      me.video.onloadedmetadata = function(e) {
        me.video.play();
      };

      /*
      me.video.addEventListener('play', function(){
        me.interval_timer=setInterval(function(){
          var canvas=document.getElementById("AMAXONIA-WEBCAM-STREAM");
          var context=canvas.getContext('2d');
          context.drawImage(me.video, 0, 0, me.container_width, me.container_height);

          return;
          //convertir la imagen en binaria
          var limit=75;
          var imageData = context.getImageData(0, 0, me.container_width,me.container_height);
          var data = imageData.data;
          for (var i=0; i<data.length; i+=4)
          data[i] = data[i+1] = data[i+2] = (data[i]+data[i+1]+data[i+2])/3;
          // overwrite original image
          context.putImageData(imageData, 0, 0);
          //fin convertir la imagen en binaria
        },200);
      },false);
      */
    })
    .catch(function(err) {
      console.log(err.name + ": " + err.message);
    });
  },

  start: function(){
    var me=this;
    if(me.internal.delay_id==null) return;
    me.internal.delay_id=setTimeout(function(){me.stream(me);me.start();},me.delay);
  },

  stop: function(){
    var me=this;
    clearTimeout(me.internal.delay_id);
    me.internal.delay_id=null;
  },

  pause: function(){
    var me=this;
    me.stop();
    me.video.pause();
  },

  play: function(){
    var me=this;
    me.internal.delay_id=-1;
    me.video.play();
    me.start();
  },

  capture: function(){
    var me=this;
    if(me.internal.delay_id==null)
      me.play();
    else
      me.pause();
  },

  stream: function(){
    var me=this;
  },

  onClick: function(){
    var me=this;
    me.capture();
  },

  destroy: function(){
    var me=this;
    me.stop();
  }
});