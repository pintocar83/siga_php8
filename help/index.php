<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
      #contenedor{
        max-width: 30em;
        margin: 50px auto;
        /*text-align: justify;*/
      }
      
      #contenedor img{
        max-width: 100%;
      }
      
      #contenedor a, #contenedor a:visited{
        color: #0071C1;
        opacity: 0.8;
      }
      
      #contenedor a:hover{        
        opacity: 1;
      }
      
      #nav-bar{
        width: 100%;
        position: fixed;
        bottom: 5px;
        right: 0px;
        padding: 0 15px 5px 0;
        opacity: 0;
        text-align: right;
      }
      
      #nav-bar:hover{
        opacity: 1;
      }
      
      #nav-bar img{
        width: 48px;
        cursor: pointer;
        opacity: 0.7;
      }
      
      #nav-bar img:hover{
        opacity: 1;
      }
      
      .u{
        font-style: normal;
        text-transform: uppercase;
        font-style: normal;
        /*font-size: 22px;*/
        
      }
      
      .titulo1{
        text-align: center;        
      }
      
      /*p::first-letter {*/
      /*  margin-left: 1.5em;*/
      /*}*/
      
      /*p:has(>em){*/
      /*  text-align: center;*/
      /*  color: yellow;*/
      /*}*/
      
      /*.u{*/
      /*  font-size: 24px;*/
      /*  font-style: normal;*/
      /*  font-weight: bold;        */
      /*}*/
      /**/
      /*p:has(.ul){*/
      /*  text-align: center;*/
      /*  color: yellow;*/
      /*}*/
      
    </style>
    <script src="../library/jquery/jquery-2.1.4.min.js"></script>
    <script>
      window.onload=function(){        
        $("#contenedor").load("core/doku.php?id=<?php print $_REQUEST["id"];?>&do=export_xhtml",function(){
          //alert($("#contenedor.dokuwiki_export").text());
          document.title=$(".dokuwiki.export").text().replace(/(\n)+/g,"\n").split("\n")[1];
          
          
          var tagA=document.getElementsByTagName("a");
          for(var i=0;i<tagA.length;i++)
            tagA[i].href=tagA[i].href.replace("core/doku.php","index.php");
          
          //$("#nav-bar").delay(500).fadeIn(50);
          
          //$("p:has(em.u)").addClass("titulo1");
          
        });
      }
    </script>    
  </head>
  <body>
    <div id="contenedor"></div>
    <div id="nav-bar">      
      <img src="images/up.png" title="Volver Arriba" onclick="window.location.href='#top'" />
      <img src="images/left.png" title="Atras" onclick="window.history.back()" />
      <img src="images/home.png" title="Inicio" onclick="window.location='index.php'" />
    </div>
  </body>
</html>
<style>  
  html, body{
    background: #FFF !important;
    /*color: #000 !important;*/
    font: 1.10em/1.4 Helvetiva, Arial, sans-serif !important;
    overflow-y: visible;
  }
</style>

