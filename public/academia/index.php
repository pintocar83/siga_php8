<?php
header('Content-Type: text/html; charset=utf-8');

$accion=$_REQUEST["accion"];

?>
<!doctype html>
<html>
  <head>     
    <title>Academia de Software Libre FUNDACITE Sucre</title>
    <style>
      body{
        font-family: sans-serif;
        font-size: 16px;
        padding-top: 50px;
        padding-bottom: 50px;
      }
	 
      .contenedor{
        width: 900px;
        border: 3px none #D88484;
        position: relative;
        left: 50%;
        margin-left: -450px;
        box-shadow: 0 0 34px #8E8E8E;
      }
      
      .titulo_asl{
        font-weight: bold;
        font-size: 28px;
        text-align: center;
        color: #2E4B77;
      }
      
      .piepagina{
        background-color: #f6f6f6;      
        background-repeat: repeat-x;
        background-position: bottom;
        border-top: 3px solid #CCCCCC;
        width: 100%;
        font-size: x-small;
        text-align: center;
        color: #474747;
      }
      
      
      .btn {
        text-align: center;
        text-decoration: none;
        margin: 10px 30px 10px 30px;
        
        font-size: 16px;
        display: block;
        outline: 0;
        color: white;
        background: #4472B9;
        white-space: nowrap;
        border: 5px solid #4472B9 !important;
        font-family: sans-serif;
        font-weight: bold;
        font-style: normal;
        padding: 9px 16px !important;
        line-height: 1.4;
        position: relative;
        border-radius: 10px;
        -webkit-box-shadow: 5px 5px 0 0  rgba(0, 0, 0, 0.15);
        box-shadow: 5px 5px 0 0  rgba(0, 0, 0, 0.15);
        -webkit-transition: 0.1s;
        transition: 0.1s;
        }
        
      .btn:hover{
        background: #5385D1;
      }
      
      #contenido{
        padding: 0px 50px 0px 50px;
      }
      
    </style>
    <script src="library/jquery.min.js"></script>
    <script>

    </script>
    </head>
  <body onload="">
    <div class="contenedor">
      <img src="images/cintillo_actual.jpg" width="100%" />
      <table width="100%" border="0" onclick="onLoad()">
        <tr>
          <td width="60%" class="titulo_asl">
						<!--<a class="btn" href="encuesta/">-->
						<a class="btn" href="index.php?q=encuesta">
							Encuesta
						</a>
						
						<a class="btn" href="index.php?q=preinscribir">
							Preinscripción
						</a>
						
					</td>
          <td style="padding-right: 20px;"><img src="images/logo_asl.jpg" width="100%"  /></td>
        </tr>
      </table>
      <br />
			<div style="padding: 0px 50px 0px 50px;">
			<?php
				switch($_GET["q"]){
					case "encuesta": include("encuesta_paso1.php"); break;
					case "cursos": include("cursos.php"); break;
					case "preinscribir": include("preinscribir.php"); break;
				}
			
			?>
			</div>
      <br />
      <div class="piepagina">Av. Monseñor Alfredo Rodríguez Figueroa vía El Peñón, Urb. Cristóbal Colón.<br>Cumaná, Estado Sucre, Venezuela. Teléfonos: (0293)467.25.31</div>
    </div>
  </body>
</html>