<?php
header('Content-Type: text/html; charset=utf-8');

?>
<!doctype html>
<html>
<head>
  <style>
    
    body{
      font-family: sans-serif;
    }
    
    
    .contenedor_video_foto{
      text-align: center;
    }
    .contenedor_titulo{
      font-size: 18px;
      font-family: sans-serif;
      color: #282828;
      font-weight: bold;
      /*font-style: italic;*/
      margin-bottom: 5px;
    }
    
    input[type=button] {
      font-size: 11px;
      padding:0px 15px;
      background:#EFEFEF;
      border:1px solid #D3D3D3;
      cursor:pointer;
      -webkit-border-radius: 5px;
      border-radius: 5px;
    }
    
    input[type=button]:hover, .buttons_hover {
        background-color:rgba(255,204,0,0.8);
    }
    
  </style>
  <script type="text/javascript">
    var siga=parent.siga;
    var Ext=parent.Ext;

    function postData(url,params){
      var request = ((window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
      request.open("POST", url, false); //<-- false makes it a synchonous request!
      request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      request.send(params);
      return request.responseText;//return Ext.decode(request.responseText);
      }
    function getData(url){
      var request = ((window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
      request.open("GET", url, false); //<-- false makes it a synchonous request!
      request.send(null);
      return request.responseText;//return Ext.decode(request.responseText);
      } 
    
    //function toBinaryImage(pixels) {
    //            var data = pixels.data;
    //            for (var i=0; i<data.length; i+=4)
    //            {
    //                var grey = (data[i]+data[i+1]+data[i+2])/3;
    //                if (grey > limit)
    //                {
    //                    data[i] = data[i+1] = data[i+2] = 0;
    //                }
    //                else
    //                {
    //                    data[i] = data[i+1] = data[i+2] = 255;
    //                }
    //            }
    //        }
    
    var WEBCAM={
      video: null,
      width: 500, //320
      height: 375, //240
      context: "",
      canvas: null,
      
      imgDataPrevio: null,
      intervalTimeOut: 1000,
      intervalHandler: null,
      autocerrar: 0,
      
      nuevo_registro: {
        id_persona: null,
        fecha: null,
        hora: null
      },
      
      init: function(){
        var me=this;
        me.video=document.createElement("video");
        //me.video.setAttribute('width', me.width);
        //me.video.setAttribute('height', me.height);
        me.video.setAttribute('width', 320);//resolucion para la camara 
        me.video.setAttribute('height', 240);
        me.canvas=document.createElement("canvas");
        me.canvas.setAttribute('width', me.width);
        me.canvas.setAttribute('height', me.height);

        navigator.getMedia = ( navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
        if(navigator.getMedia){
          me.context="getusermedia";
          navigator.getMedia(
            {
              video: true,
              audio: false
            },
            function(stream){
              if (navigator.mozGetUserMedia){
                me.video.mozSrcObject = stream;
              }
              else{
                var vendorURL = window.URL || window.webkitURL;
                me.video.src = vendorURL.createObjectURL(stream);
              }
              me.video.play();
            },
            function(err) {
              console.log("An error occured! " + err);
            }
          );
          
          me.video.addEventListener('play', function(){
            setInterval(function(){
              var canvas=document.getElementById("DISPLAY_STREAM");
              var context=canvas.getContext('2d');
              context.drawImage(WEBCAM.video, 0, 0, WEBCAM.width, WEBCAM.height);
              
              //return;
              //convertir la imagen en binaria
              var limit=75;
              var imageData = context.getImageData(0, 0, me.width,me.height);
              var data = imageData.data;
              for (var i=0; i<data.length; i+=4)
                data[i] = data[i+1] = data[i+2] = (data[i]+data[i+1]+data[i+2])/3;
              // overwrite original image
              context.putImageData(imageData, 0, 0); 
              //fin convertir la imagen en binaria
            },200);
            
            //me.capture();
          },false);
          return;
        }
        
        alert("El navegador no soporta la opci??n 'navigator.getUserMedia'");
      },
      
      displayAsistencia: function(result){
        var me=this;
        
        document.getElementById("imagen_persona").setAttribute("src","../../image/photo-default.png"); 
        document.getElementById("DISPLAY_CODIGO").style.display="";
        document.getElementById("DISPLAY_REGISTRO").style.display="none";
        document.getElementById("codigo").style.visibility="";
        
        document.getElementById("nombre_apellido").innerHTML="";
        document.getElementById("cedula").innerHTML="";
        document.getElementById("registro_nuevo_fecha").innerHTML="";
        document.getElementById("registro_nuevo_hora").innerHTML="";
        document.getElementById("registro_nuevo_alerta").innerHTML="";
        
        document.getElementById("registro_previo_fecha").innerHTML="";
        document.getElementById("registro_previo_hora").innerHTML="";
        //document.getElementById("registro_previo_hora_s").innerHTML="";
        
        var canvas=document.getElementById("DISPLAY_STREAM");
        //var context=canvas.getContext('2d');
        //var imageData = context.getImageData(0, 0, me.width,me.height);
        //var data = imageData.data;
        var data = canvas.toDataURL('image/png');
        
        //si la imagen enviada al servidor es valida
        if(result!="null"){
          result=parent.Ext.JSON.decode(result);
          
          document.getElementById("imagen_capturada").setAttribute("src",data);          
          document.getElementById("imagen_persona").setAttribute("src",result["imagen"]);
          document.getElementById("nombre_apellido").innerHTML=result["nombre_apellido"];
          document.getElementById("cedula").innerHTML=result["cedula"];
          document.getElementById("registro_nuevo_fecha").innerHTML=result["registro_nuevo"]["fecha_mostrar"];
          document.getElementById("registro_nuevo_hora").innerHTML=result["registro_nuevo"]["hora_mostrar"];
          if(result["registro_nuevo"]["alerta"])
            document.getElementById("registro_nuevo_alerta").innerHTML="Jornada laboral comprendida entre 08:00am y 4:30pm.<br>Tiempo de retraso "+result["registro_nuevo"]["alerta"]+".";
          
          for(var i=0;i<result["registro_previo"].length;i++){
            if(i==0 || (i>0 && result["registro_previo"][i]["fecha"]!=result["registro_previo"][i-1]["fecha"]))
              document.getElementById("registro_previo_fecha").innerHTML+=parent.formatDate(result["registro_previo"][i]["fecha"])+"<br>";
            else
              document.getElementById("registro_previo_fecha").innerHTML+="&nbsp;<br>";
            
            if(result["registro_previo"][i]["hora"])
              document.getElementById("registro_previo_hora").innerHTML+=result["registro_previo"][i]["hora"]+"<br>";
            else
              document.getElementById("registro_previo_hora").innerHTML+="&nbsp;<br>";
            /*
            if(result["registro_previo"][i]["h_salida"])
              document.getElementById("registro_previo_hora_s").innerHTML+=result["registro_previo"][i]["h_salida"]+"<br>";
            else
              document.getElementById("registro_previo_hora_s").innerHTML+="&nbsp;<br>";
            */
          }
          
          
          
          
          
          document.getElementById("DISPLAY_CODIGO").style.display="none";//document.getElementById("WEBCAM_STREAM").style.display="none";
          document.getElementById("DISPLAY_REGISTRO").style.display="";
          document.getElementById("codigo").style.visibility="hidden";
          
          
          me.nuevo_registro.id_persona=result["id_persona"];
          me.nuevo_registro.fecha=result["registro_nuevo"]["fecha"];
          me.nuevo_registro.hora=result["registro_nuevo"]["hora"];
          me.nuevo_registro.imagen=data;
          
          me.autocerrar=560;
          me.bucleAutoCerrar();
          return true;
        }
        return false;
      },
      
      stopInterval: function(){
        var me=this;
        //clearInterval(me.intervalHandler);
        clearTimeout(me.intervalHandler);
        
      },
      
      registrarAsistencia: function(){
        var me=this;
        var result=postData("registrar.php","id_persona="+me.nuevo_registro.id_persona+"&fecha="+me.nuevo_registro.fecha+"&hora="+me.nuevo_registro.hora+"&imagen="+me.nuevo_registro.imagen);
        if(result!="true") {alert("Error al registrar datos.");}
        
        document.getElementById("imagen_persona").setAttribute("src","../../image/photo-default.png"); 
        document.getElementById("DISPLAY_CODIGO").style.display="";
        document.getElementById("DISPLAY_REGISTRO").style.display="none";
        document.getElementById("codigo").style.visibility="";
        document.getElementById("codigo").focus();
      },
      
      bucleAutoCerrar: function(){
        var me=this;
        document.getElementById("cuenta_atras").innerHTML=me.autocerrar;
        if(me.autocerrar<=0){
          me.registrarAsistencia();
          return;
        }
        me.autocerrar--;
        setTimeout('WEBCAM.bucleAutoCerrar()',1000);
        document.getElementById("btn_aceptar").focus();
      },
      
    };
    
    function onkeypressenter(event){
      if (event.which == 13 || event.keyCode == 13) {
        if(String(document.getElementById("codigo").value).length!=4) return false;
        var result=postData("procesar.php","codigo="+document.getElementById("codigo").value);
        WEBCAM.displayAsistencia(result);
        if(result=="null"){
          parent.Ext.Msg.alert(
            "Sistema de Asistencia",
            "El c??digo introducido no es v??lido.",
            function(){
              document.getElementById("codigo").value="";
              document.getElementById("codigo").focus();
              setTimeout('document.getElementById("codigo").focus()',1000);
            });
        }
        else{
          document.getElementById("codigo").value="";
          document.getElementById("codigo").focus();
        }
        
        
        return false;
      }
      return true;
    }
    
    function onLoad(){
      document.getElementById('codigo').focus();
      document.getElementById('logo').src="../../"+siga.value("folder")+"/logo_01.jpg";
      WEBCAM.init();
    }
    
  </script>
  
  
</head>
<body onload="onLoad()">
  <div class="contenedor_video_foto">
    <div class="contenedor_titulo">Sistema para el Control de Asistencia</div>
    <!--Contenedor principal-->
    <div style="background-color: #FFFFFF; width: 500px; border: 2px solid #282828; left: 50%; margin-left: -250px; position: relative;">
      <div style="width: 100%; height: 100%;">        
        <!--Informaci??n de la barra superior-->
        <div style="background-color: #282828; color: #FFF; font: 10px sans-serif; padding: 2px;">
          Para marcar la asistencia ingrese el c??digo asignado.
        </div>        
        <!--Donde se mostrar?? el video de la camara-->
        <div id="DISPLAY_CODIGO" style="width: 500px; height: 375px;">
          <!--<div id="DISPLAY_STREAM" style="width: 250px; height: 150px; background: black; margin: auto auto; margin-top: 5px;"></div>-->
          <div style="width: 250px; height: 150px; padding-top: 5px; margin: auto auto; background: url(../../image/photo-default.png) no-repeat center top; background-size: contain;">
            <canvas id="DISPLAY_STREAM" width="250" height="150" style="display: none;"></canvas>
          </div>
          <table width="100%" border="0" style="padding-top: 5px;">
            <tr>
              <!--<td width="150"><img src='../images/dedometro.png' width='120' /></td>-->
              <td>
                <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">Control de Entrada/Salida</div>
                <input id="codigo" type="text" placeholder="c??digo" value="" autocomplete="off" onkeypress="return onkeypressenter(event)" title="Ingrese el c??digo de dedometro, para ingresar la asistencia." style='background-color: #FFF; border: 1px solid #BFBFBF; font: 10px sans-serif; width: 100px; text-align: center;'/>
                <br />
                <br />
                <input type="button" value="Registrar" onclick="onkeypressenter({keyCode:13})" />
                <input type="button" value="Limpiar" onclick="document.getElementById('codigo').value='';document.getElementById('codigo').focus();" />
                <input type="button" value="Consultar" onclick="siga.open('asistencia_consulta')"/>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <br />
                <img id='logo' src='' width='200' />
              </td>
            </tr>
          </table>
          <img src='../../image/menu/icon-asistencia_registro.png' width='120'  style='position: absolute; top: 130px; left: 10px;'/>
        </div>
        
        <!--Donde se mostrar?? la infromaci??n de la asistencia ingresada-->
        <div id="DISPLAY_REGISTRO"  style="width: 500px; height: 375px; background-color: #FFF; display: none; font-family: sans-serif;">
          <table width='100%'>
            <tr>
              <td width="90%">
                <div style="font: bold 22px sans-serif;" id='nombre_apellido'></div>
                <div style="font: bold 14px sans-serif; margin-bottom: 20px;" id='cedula'></div>
                <span style="font-size: 10px; color: #37559B; font-weight: normal; line-height: 100%;" id='registro_nuevo_fecha'></span> <br>
                <b><span style="font-size: 28px; color: #37559B; line-height: 100%;" id='registro_nuevo_hora'></span></b> <br>
                <div style="font-size: 11px; color: #FF0000; font-weight: bold; line-height: 110%; margin-top: 10px;" id='registro_nuevo_alerta'></div><br>
                <br>
              </td>
              <td>
                <img id="imagen_capturada" src="" width="100" height="50" style="display: none;" />
                <img id="imagen_persona" src="" width="100" />
              </td>
            </tr>
          </table>
          <div align="center">
            <div style="font: 12px sans-serif; color: #939393; width: 80%;" align="center">
              <b>Registro Previo</b> <br>
              <table align="center" width="100%" border = "0" cellpadding="3" cellspacing="0" style="border: 0px solid #000;">
                <tr>
                  <td width="50%" style="background-color: #EDEDED; font-weight: bold; text-align: center;">Fecha</td>
                  <td width="" style="background-color: #EDEDED; font-weight: bold; text-align: center;">Horas</td>
                </tr>
              </table>
              <div style="height: 100px; width: 100%; overflow: auto;" align="center">
                <table align="center" width="100%" border = "0" cellpadding="3" cellspacing="2" style="border: 0px solid #000;">              
                  <tr valign="top">
                    <td width="50%" align = "center" style="background-color: #FCFCFC;" id='registro_previo_fecha'></td>
                    <td width="" align = "center" style="background-color: #FCFCFC;" id='registro_previo_hora'></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <br>
          <span style="font-size: 11px; font-style: italic; color: #9E9E9E; position: absolute; bottom: 0px; left: 0px; width: 100%; padding-bottom: 5px;">Este mensaje se cerrar?? autom??ticamente en <span id="cuenta_atras"></span> segundos.<br>No cierre la ventana hasta finalizar.<br><input id="btn_aceptar" type="button" value="Aceptar" onclick="WEBCAM.autocerrar=0;" /></span> 
        </div>
        <!--Fin DISPLAY_REGISTRO-->
      </div>
    </div>
    <!--Fin Contenedor principal-->
  </div>
  <!--Fin contenedor_video_foto-->
</body>
</html>