<?php
include_once("../../library/db.controller.php");
include_once("library/siga.config.php");
include_once("../../library/siga.class.php");

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$db=SIGA::DBController("siga_online");

$cursos=$db->Execute("SELECT *
                      FROM modulo_asl.curso_aperturado as ca
                      WHERE estado=1
                      ORDER BY ca.codigo DESC");
  
  
  

?>
<!doctype html>
<html>
<head>
  <title>Encuesta</title>
  <meta charset="utf-8" />
  <style>
  input,select,textarea {
    background-color : #FFFFFF;
    border: 1px solid #566272;
  }
  
  input:focus,select:focus,textarea:focus {
    background-color : #FFFFFF;
    border: 1px solid #ACC5E9;
  }
    
  input:disabled,select:disabled,textarea:disabled {
    background-color : #BABABA;
    border: 1px solid #AEAEAE;
  }
  
  button {
    background-color : #FFFFFF;
    color : #47545E;
    border: 1px solid #566272;
    font-weight : bold;
  }
  
  button:hover {
    background-color : #FFF8C9;
    color : #000000;
    border: 1px solid #566272;
    font-weight : bold;
  }
  
  .title_field{
    font-size : 12px;
    font-weight: bold;  
  }
  
  .cx{
    text-align: left;
    text-decoration: none;
    margin: 20px 50px 20px 50px;
    
    font-size: 18px;
    /*display: block;*/
    outline: 0;
    color: white;
    background: #5385D1;
    /*white-space: nowrap;*/
    border: 2px solid #4472B9 !important;
    font-family: sans-serif;
    font-weight: bold;
    font-style: normal;
    /*padding: 9px 16px !important;*/
    line-height: 1.4;
    position: relative;
    border-radius: 10px;
    -webkit-box-shadow: 5px 5px 0 0  rgba(0, 0, 0, 0.15);
    box-shadow: 5px 5px 0 0  rgba(0, 0, 0, 0.15);
    -webkit-transition: 0.1s;
    transition: 0.1s;
  }
  
  
  </style>
</head>
<body>
  <div align="center">
  <div align="center" style="margin-top: 50px; width: 500px;" class="cx">
    <div style="text-align: center; border-bottom: 2px solid #4472B9;"><b>INGRESO A LA ENCUESTA</b></div>
    <div style="background: #F2F2F2; color: black; padding-top: 1px; border-radius: 0px 0px 10px 10px; font-size: 16px;">
    <p style="font-weight: normal; text-align: center; font-style: italic; font-size: 14px;">Ingrese el curso realizado, cedula y clave de acceso<br />para realizar la encuesta.</p>
    <form action="encuesta_paso2.php" method="post">
      <table align="center">
        <tr>
          <td>
            <b>Curso</b>
          </td>
          <td>
            <select name="id_curso_aperturado" style="width: 100%;">
              <?php
              for($i=0;$i<count($cursos);$i++){
                print "<option value='".$cursos[$i]["id"]."'>".$cursos[$i]["codigo"]."</option>";
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>
            <b>Cédula</b>
          </td>
          <td>
            <select name="nacionalidad">
              <option>V</option>
              <option>E</option>
              <option>S/N</option>
            </select>
            <input type="text" name="cedula" value="" autocomplete="off" />
          </td>
        </tr>
        <tr>
          <td>
            <b>Clave</b>
          </td>
          <td>
            <input type="password" name="clave" value="" style="width: 100%;" />
          </td>
        </tr>
        <tr>          
          <td colspan="2" align="center"><br /><button type="submit">Ingresar</button></td>
        </tr>
      </table>
      <br />
    </form>
    </div>
  </div>

  </div>
</body>
</html>