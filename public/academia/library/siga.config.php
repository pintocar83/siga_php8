<?php
class SIGA_CONFIG {
  public static $database_default=NULL;
  
  public static $database= array(
    "siga_online"=> array(//identificador de la base de datos
          "display"=>"f",
          "description"=>"FUNDACITE Sucre (para consulta pública)", //descripcion de la base de datos
          "data"=>array("2007","2008","2009","2010","2011","2012","2013","2014","2015","2016"), //años disponibles para la base de datos
          "driver"=>"postgres", //driver
          "server"=>"localhost", //servidor
          "name"=>"siga", //nombre de la base de datos
          "user"=>"siga_online", //usuario
          "password"=>"%pg0n1in3%" //contraseña
          )
  );  
}

//definir la base de datos por defecto (la primera del arreglo $database)
reset(SIGA_CONFIG::$database);
SIGA_CONFIG::$database_default=key(SIGA_CONFIG::$database);
?>