<?php
header('Content-Type: text/html; charset=utf-8');
$navegador = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stristr($navegador,"msie") or stristr($navegador,"konqueror")){
	echo '<DIV align="center"><strong>LA APLICACI&Oacute;N NO ES COMPATIBLE CON ESTE NAVEGADOR.</strong><br><br>NAVEGADORES PROBADOS:<br> ICEWEASEL<br>FIREFOX<br>EPIPHANY<br>ICEAPE<br>OPERA<br>GOOGLE CHROME</DIV>';
	exit;
	}

include("library/include.php");

include_once(SIGA::databasePath()."/config/config.php");

?>
<!doctype html>
<html>
  <head>
    <title><?php print $siga_title;?></title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <style type="text/css">
      html{
        height: 100%;
        font: 13px/100% Arial, Helvetica, sans-serif;
      }

      body{
        padding: 0px; margin: 0px;
        overflow: hidden;
        background-image: url(<?php print isset(SIGA::userPreferences()["background"])?SIGA::userPreferences()["background"]:"image/background/1.jpg"?>);
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: fixed !important;
        width: 100%;
        height: 100%;
      }

      #siga-loading-mask{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #FFFFFF;
        z-index: 30000;
      }

      #siga-loading-message{
        display: block;
        color: #000000;
        position: absolute;
        bottom: 5px;
        right: 10px;
        font: 10px sans-serif;
      }

      #siga-progressbar{
        position: absolute;
        width: 300px;
        height: 10px;
        margin-left: -150px;
        top: 40%;
        left: 50%;
        -webkit-appearance: progress-bar;
        box-sizing: border-box;
        display: inline-block;
        border: 1px solid #999;
        padding: 1px;
        background-color: #FFF;
      }

      #siga-progressbar::-webkit-progress-value{
        background-color: #F46161;
        background-repeat: repeat-x;
        background-position: 0px 0px;
        background-size: 16px 10px;
        background-image: linear-gradient(315deg, transparent, transparent 33%, rgba(0, 0, 0, 0.12) 33%, rgba(0, 0, 0, 0.12) 66%, transparent 66%, transparent);
      }

      #siga-progressbar::-webkit-progress-bar{
        background-color: #FFF;
      }

      #siga-progressbar::-moz-progress-bar{
        background-color: #F46161;
        background-repeat: repeat-x;
        background-position: 0px 0px;
        background-size: 16px 10px;
        background-image: linear-gradient(315deg, transparent, transparent 33%, rgba(0, 0, 0, 0.12) 33%, rgba(0, 0, 0, 0.12) 66%, transparent 66%, transparent);
      }

      #siga-loading-module{
        width: 27px;
        position: absolute;
        top: 0px;
        left: 50%;
        margin-left: -14px;
        vertical-align: middle;
        display: none;
      }
    </style>
  </head>
  <body>
    <div id="siga-loading-mask"><progress id="siga-progressbar" value="0" max="100"></progress><span id="siga-loading-message">Cargando. Por favor espere...</span></div>

    <!-- BARRA SUPERIOR -->
    <div id='siga-top'>
      <div class="info">
        <span class="data">
          <?php if(SIGA::user()):?>
          <span><b>Usuario:</b> <?php echo SIGA::userName();?></span>
          <span id="siga-infodatabase" style="cursor: pointer;" onclick="siga.database.open(event)"><b>Datos:</b> <?php echo SIGA::dataName();?></span>
          <?php endif;?>
          <span id="siga-infotime" style="padding-left: 10px; cursor: pointer;" onclick="siga.calendar.open()">&nbsp;</span>
        </span>
      </div>
    </div>
    <!-- MENU LOGICA (BOTON)-->
    <div id="siga-apps-button" class="normal" onclick="siga.menu.show(true)">Aplicaciones</div>
    <img id="siga-loading-module" src='image/loading-bubbles.svg' />
    <!-- CONTENEDOR DE LAS APLICACIONES -->
    <DIV id="siga-window-container">
      <div id="siga-apps-title"></div>
    </DIV>
    <!-- MENU LOGICA (CONTENIDO)-->
    <div id="siga-apps">
      <div id="siga-apps-center" class="center">
        <?php
        for($i=0;$i<count($siga_apps);$i++):
          print "<div id='siga-apps-center-".$siga_apps[$i]["id"]."' style='display: ".($i==0?"block":"none")."'>";
          foreach($siga_apps[$i]["option"] as $clave => $valor):
            print "<div class='title'>$clave</div>";
            for($j=0;$j<count($valor);$j++){
              if(!isset($valor[$j]["id"])) continue;

              $add_class="disabled";
              $onclick="";
              if(SIGA::access($valor[$j]["id"])!="" or (isset($valor[$j]["public"])?$valor[$j]["public"]:false)){
                $add_class="";
                $onclick="siga.open('".$valor[$j]["id"]."')";
              }

              print "<div class='icon $add_class' onclick=\"$onclick\"><span><img src='".$valor[$j]["icon"]."' /><br>".$valor[$j]["name"]."</span></div>";
            }
          endforeach;
          print "</div>";
        endfor;
        ?>
      </div>
      <div id="siga-apps-left" class="left">
        <?php
        for($i=0;$i<count($siga_apps);$i++)
          print "<div id='siga-apps-left-".$siga_apps[$i]["id"]."' class='icon ".($i==0?"selected":"")."' onclick=\"siga.menu.option('".$siga_apps[$i]["id"]."')\"><span><img src='".$siga_apps[$i]["icon"]."'/><br />".$siga_apps[$i]["name"]."</span></div>";
        ?>
        <!--<div id='siga-apps-left-logout' class='icon' onclick="siga.menu.hide(); siga.open('modulo_base/logout')"><span><img src='image/menu/icon-cerrar_sesion.png'/><br />Cerrar Sesión</span></div>-->
      </div>
      <div class="right"></div>
    </div>
    <!--<div id="siga-loading-module">Cargando. Por favor espere...</div>-->
  </body>
</html>
<script type='text/javascript'>
  var siga={};
  siga._value={};
  siga.value=function(key,value){
    if(typeof(value)!=="undefined") siga._value[key]=value;
    return siga._value[key];
  };

  siga.value("pie_aplicaciones","");
  siga.value("title_login","<?php print $siga_title_login;?>");
  siga.value("title_logout","<?php print $siga_title_logout;?>");
  siga.value("folder","<?php print SIGA::databasePath(false)."/config";?>");
  siga.value("datos",<?php print json_encode(SIGA::$data);?>);
  siga.value("data_disponible",<?php print json_encode(SIGA::dataAvailable());?>);
  siga.value("anio","<?php print (SIGA::data()?SIGA::data():date("Y"));?>");
  siga.value("sesion_abierta",<?php print SIGA::user()?'true':'false';?>);
  siga.value("meses",<?php print json_encode(meses());?>);
  siga.value("mes_actual","<?php print date("m");?>");


  siga.css=function(o){
    //o.url, o.onLoad
    var css = document.createElement('link');
    css.setAttribute("href", o.url);
    css.setAttribute("type", "text/css");
    css.setAttribute("rel", "stylesheet");
    if("onload" in css === true){
      css.onload=function(){o.onLoad();}
      document.getElementsByTagName('head')[0].appendChild(css);
    }
    else{
      document.getElementsByTagName('head')[0].appendChild(css);
      o.onLoad();
    }
  };

  siga.js=function(o){
    //o.url, o.onLoad
    var script=document.createElement('script');
    if(script.readyState){ // IE
      script.onreadystatechange=function(){
        if(script.readyState==='loaded' || script.readyState==='complete'){
          script.onreadystatechange = null;
          o.onLoad();
        }
      };
    }
    else //Others
      script.onload=function(){o.onLoad();};
    script.src = o.url;
    document.getElementsByTagName('head')[0].appendChild(script);
  };

  siga.boot=function(o){
    if(typeof o.file!=='object' || o.file.length==0){
      o.onLoad();
      return;
    }
    var _file=o.file.shift();
    document.getElementById('siga-progressbar').value=_file.progress;
    document.getElementById('siga-loading-message').innerHTML = _file.msg;
    var _o={url: _file.url, onLoad: function(){siga.boot({file: o.file, onLoad: o.onLoad});}};
    switch(_file.type){
      case "js":  siga.js(_o);  break;
      case "css": siga.css(_o); break;
    }
  };

  siga.ajax=function(url,params){
		var request = ((window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
    request.open("POST", url, false); //<-- false makes it a synchonous request!
    request.setRequestHeader("Content-type","application/x-www-form-urlencoded;charset=utf-8");
    request.send(params);
		return request.responseText;
  }

  window.onload=function(){
    var version="2022.9";//para que el navegardor actualice los js y tome los cambios realizados
    siga.version=version;
    siga.boot({
      file:[
        {url: "css/siga.css?version="+version, type: 'css', progress: 10, msg: "Hojas de Estilos en Cascada (Interfaz)..."},
        {url: "css/icons.css?version="+version, type: 'css', progress: 30, msg: "Hojas de Estilos en Cascada (Iconos)..."},
        {url: "library/extjs/7.0.0/theme-gray/resources/theme-gray-all_1.css", type: 'css', progress: 40, msg: "Hojas de Estilos en Cascada (ExtJS 7)..."},
        {url: "library/extjs/7.0.0/theme-gray/resources/theme-gray-all_2.css", type: 'css', progress: 40, msg: "Hojas de Estilos en Cascada (ExtJS 7)..."},
        {url: "library/jquery/jquery-2.1.4.min.js", type: 'js', progress: 42, msg: "JQuery 2.1.4"},
        {url: "library/extjs/7.0.0/ext-all.js", type: 'js', progress: 45, msg: "ExtJS 7..."},
        {url: "library/extjs/7.0.0/locale-es.js", type: 'js', progress: 70, msg: "ExtJS 7(Lenguaje)..."},
        {url: "library/functions.js?version="+version, type: 'js', progress: 75, msg: "Funciones Básicas..."},
        {url: "library/siga.windowBase.js?version="+version, type: 'js', progress: 80, msg: "Configuración de Ventanas (base)..."},
        {url: "library/siga.windowFrame.js?version="+version, type: 'js', progress: 84, msg: "Configuración de Ventanas (frame)..."},
        {url: "library/siga.windowForm.js?version="+version, type: 'js', progress: 86, msg: "Configuración de Ventanas (form)..."},
        {url: "library/siga.window.js?version="+version, type: 'js', progress: 90, msg: "Configuración de Ventanas (predeterminado)..."},
        {url: "library/siga.select.js?version="+version, type: 'js', progress: 95, msg: "Configuración de Campos (select)..."},
        {url: "library/siga.video.js?version="+version, type: 'js', progress: 96, msg: "Configuración de Campos (webcam)..."},
        {url: "library/siga.js?version="+version, type: 'js', progress: 100, msg: "Gestor de Módulos..."}
      ],
      onLoad: function(){
        Ext.Ajax.setTimeout(15*60000);//15min
        Ext.application({
          name   : 'SIGA',
          launch : function(){
            siga.timer.init();
            //siga.desktopIcon.add("Versión Anterior<br><small>ExtJS 6</small>","image/menu/icon-sistema-recarga.png","window.location.href='index_extjs6.php'");
            //siga.desktopIcon.add("Registro de Asistencia","image/menu/icon-asistencia.png","siga.open('modulo_asistencia/asistencia_clasico')");
            //siga.menu.show(true);

            if(siga.value("sesion_abierta")){
              //siga.jappix();
            }
            else{
              siga.open("login");
            }

            Ext.get('siga-loading-mask').remove();


            //
			//var response = Ext.Ajax.request({async: false,url: "library/extjs/6.0.0/ext-all.js"});
			//localStorage.setItem("SIGA::extjs", response.responseText);
            //Ext.get('loading-mask').fadeOut({duration: 500, remove: true});
          }
        });
      }
    });
  };


</script>
