<?php
define("SERVER_DATABASE_HOST", $_SERVER['DATABASE_HOST']);
define("UPLOADER_KEY", "123456");

class SIGA_CONFIG {
  public static $data=[];

  public static $database_default=NULL;

  public static $database= array(
    "fundacite_sucre"=> array(//identificador de la base de datos
          "display"=>"t",
          "description"=>"Fundacite Sucre", //descripcion de la base de datos
          "driver"=>"postgres", //driver
          "server"=> SERVER_DATABASE_HOST ?? "localhost", //servidor
          "name"=>"siga_fundacite_sucre", //nombre de la base de datos
          "user"=>"siga", //usuario
          "password"=>"siga", //contraseña
          "port"=>"5432"
          ),
    "siga_online"=> array(//identificador de la base de datos
          "display"=>"f",
          "description"=>"FUNDACITE Sucre (para consulta pública)", //descripcion de la base de datos
          "driver"=>"postgres", //driver
          "server"=>"localhost", //servidor
          "name"=>"siga", //nombre de la base de datos
          "user"=>"siga_online", //usuario
          "password"=>"siga_online" //contraseña
          ),
    "base"=> array(//identificador de la base de datos
          "display"=>"f",
          "description"=>"Base de datos del Registro Público - CNE", //descripcion de la base de datos
          "driver"=>"sqlite3", //driver
          "server"=>"", //servidor
          )
  );

}

//definir la base de datos por defecto (la primera del arreglo $database)
reset(SIGA_CONFIG::$database);
SIGA_CONFIG::$database_default=key(SIGA_CONFIG::$database);

?>
