<?php
class SIGA_CONFIG {

  public static $data= array(
                              array("id"=>"2007","nombre"=>"Año 2007"),
                              array("id"=>"2008","nombre"=>"Año 2008"),
                              array("id"=>"2009","nombre"=>"Año 2009"),
                              array("id"=>"2010","nombre"=>"Año 2010"),
                              array("id"=>"2011","nombre"=>"Año 2011"),
                              array("id"=>"2012","nombre"=>"Año 2012"),
                              array("id"=>"2013","nombre"=>"Año 2013"),
                              array("id"=>"2014","nombre"=>"Año 2014"),
                              array("id"=>"2015","nombre"=>"Año 2015"),
                              array("id"=>"2016","nombre"=>"Año 2016"),
                              array("id"=>"2017","nombre"=>"Año 2017"),
                              array("id"=>"2018","nombre"=>"Año 2018"),
                              array("id"=>"2019","nombre"=>"Año 2019"),
                              array("id"=>"2020","nombre"=>"Año 2020"),
                              array("id"=>"2021","nombre"=>"Año 2021"),
                              array("id"=>"2022","nombre"=>"Año 2022")
                            );


  public static $database_default=NULL;

  public static $database= array(
    "fundacite_sucre"=> array(//identificador de la base de datos
          "display"=>"t",
          "description"=>"Fundacite Sucre", //descripcion de la base de datos
          "data"=>array("2007","2008","2009","2010","2011","2012","2013","2014","2015","2016","2017","2018","2018","2019","2020","2021","2022"), //años disponibles para la base de datos
          "driver"=>"postgres", //driver
          "server"=>"localhost", //servidor
          "name"=>"siga_fundacitesucre", //nombre de la base de datos
          "user"=>"siga", //usuario
          "password"=>"siga", //contraseña
          "port"=>"5432"
          ),
    "siga_online"=> array(//identificador de la base de datos
          "display"=>"f",
          "description"=>"FUNDACITE Sucre (para consulta pública)", //descripcion de la base de datos
          "data"=>array("2007","2008","2009","2010","2011","2012","2013","2014","2015","2016","2017","2018","2018"), //años disponibles para la base de datos
          "driver"=>"postgres", //driver
          "server"=>"localhost", //servidor
          "name"=>"siga", //nombre de la base de datos
          "user"=>"siga_online", //usuario
          "password"=>"siga_online" //contraseña
          ),
    "base"=> array(//identificador de la base de datos
          "display"=>"f",
          "description"=>"Base de datos del Registro Público - CNE", //descripcion de la base de datos
          "data"=>array(), //años disponibles para la base de datos
          "driver"=>"sqlite3", //driver
          "server"=>"", //servidor
          )
  );

}

//definir la base de datos por defecto (la primera del arreglo $database)
reset(SIGA_CONFIG::$database);
SIGA_CONFIG::$database_default=key(SIGA_CONFIG::$database);

define("UPLOADER_KEY", "123456");
?>
