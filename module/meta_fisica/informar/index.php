<?php
include("../../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;


class MODULO{
  public static function onInit(){
    $access=SIGA::access("meta_fisica/informar");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet_Responsable":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Responsable($access));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet($access,SIGA::param("id_meta_fisica"),SIGA::param("mes")));
        break;
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave($access,
                                       SIGA::param("id_meta_fisica"),
                                       SIGA::param("mes"),
                                       SIGA::param("cantidad"),
                                       SIGA::param("cantidad_masculino"),
                                       SIGA::param("cantidad_femenino"),
                                       SIGA::param("comunidad"),
                                       SIGA::param("logros"),
                                       SIGA::param("obstaculos")
                                       ));
        break;
      case "onFile_Adjuntar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onFile_Adjuntar($access,SIGA::param("id_meta_fisica"),SIGA::param("mes"),$_FILES["upload"]));
        break;
      /*case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      
      
      case "onDelete":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete($access,SIGA::param("id")));
        break;  */
      
      
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        print self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        print self::onJavascript($access);
        break;
    }    
  }  
  
  public static function onCss($access){
    if(!$access) return;
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }
  
  public static function onGet_Responsable($access){
    if(!$access) return;
    $db=SIGA::DBController();
    $sql="select
            f.id
          from
            modulo_base.usuario as u,
            modulo_nomina.ficha as f
          where
            u.usuario like '".SIGA::user()."' and
            u.id_persona_responsable=f.id_persona";
    $persona=$db->Execute($sql);    
    return $persona;
  }
  
  public static function onGet($access,$id_meta_fisica,$mes){
    if(!$access) return;
    $db=SIGA::DBController();
    $sql="select * from modulo_planificacion.meta_fisica_informe where id_meta_fisica=$id_meta_fisica and mes=$mes";
    $retorno=$db->Execute($sql);    
    return $retorno;
  }
  
  /*public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_planificacion.unidad_medida WHERE UPPER(unidad_medida) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  */
  
  public static function createPath($id_meta_fisica,$mes){
    $carpeta_base=SIGA::databasePath()."/meta_fisica/";
    if(!file_exists($carpeta_base))
    	mkdir($carpeta_base,0755);
    $carpeta_base.="$id_meta_fisica/";
    if(!file_exists($carpeta_base))
    	mkdir($carpeta_base,0755);
    $carpeta_base.="$mes/";
    if(!file_exists($carpeta_base))
    	mkdir($carpeta_base,0755);
    return $carpeta_base;
  }
  
  public static function onSave($access,
                                $id_meta_fisica,
                                $mes,
                                $cantidad,
                                $cantidad_masculino,
                                $cantidad_femenino,
                                $comunidad,
                                $logros,
                                $obstaculos){
    
    $db=SIGA::DBController();
    
    
    
    
    if(!($access=="rw"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
    
    $db->Execute("BEGIN WORK");
    $result=$db->Delete("modulo_planificacion.meta_fisica_informe","id_meta_fisica='$id_meta_fisica' and mes='$mes'");
    
    $result=$db->Insert("modulo_planificacion.meta_fisica_informe",array(
                                                                         "id_meta_fisica"=>"$id_meta_fisica",
                                                                         "mes"=>"$mes",
                                                                         "cantidad"=>"$cantidad",
                                                                         "cantidad_masculino"=>"$cantidad_masculino",
                                                                         "cantidad_femenino"=>"$cantidad_femenino",
                                                                         "comunidad"=>"'$comunidad'",
                                                                         "logros"=>"'$logros'",
                                                                         "obstaculos"=>"'$obstaculos'"
                                                                         ));
    
    
    self::createPath($id_meta_fisica,$mes);
    
    if(!$result){
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_planificacion.meta_fisica_informe.");
    }
    $db->Execute("COMMIT WORK");    
    return array("success"=>true,"message"=>"Datos guardados con éxito.");
  
  }
  
  public static function onFile_Adjuntar($access, $id_meta_fisica, $mes, $FILES){
    if($access!="rw")
      return array("success"=>false, "message"=>"El usuario no tiene acceso al módulo.");    
    $carpeta_base=createPath($id_meta_fisica,$mes);
    
    for($i=0;$i<count($FILES['tmp_name']);$i++){
      move_uploaded_file($FILES['tmp_name'][$i], $carpeta_base.$FILES['name'][$i]);
    }
    return array("success"=>true, "message"=>"");
  }
  
  /*
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos."); 
    $result=$db->Delete("modulo_planificacion.unidad_medida","id='$id'");
    if(!$result)                  
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_planificacion.unidad_medida.");      
    return array("success"=>true,"message"=>"Datos eliminado con exito.");
  } */
}

MODULO::onInit();
?>