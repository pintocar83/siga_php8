<?php
class usuario{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT id, usuario, clave as clave_type, id_persona_responsable, activo FROM modulo_base.usuario WHERE id='$id'";
    $return=$db->Execute($sql);
    
    $clave=json_decode($return[0]["clave_type"],true);
    $return[0]["clave_type"]=$return[0][2]=$clave["type"];
    
    //buscar los años y perfiles para el usuario
    $sql="SELECT * FROM modulo_base.usuario_perfil WHERE id_usuario='$id' ORDER BY anio";
    $perfil=$db->Execute($sql);
    
    $nodos=array();
    for($i=0;$i<count($perfil);$i++){
      $hojas=array();
      $tmp=explode("\n",$perfil[$i]["perfil"]);
      for($j=0;$j<count($tmp);$j++)
        $hojas[]=array("iconCls"=>"perfil-tipo-nodo","text"=>$tmp[$j],"leaf"=>true);
      
      $nodos[]=array(
        "iconCls"=>"perfil-data-nodo",
        "text"=>$perfil[$i]["anio"],
        "leaf"=>false,
        "children"=>$hojas,
        "checked"=> ($perfil[$i]["activo"]=='t')?true:false
      );
    }    
    $return[0]["perfil"]=$nodos;
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();  
    $sql="SELECT
            U.id,
            U.usuario,
            replace(P.denominacion,';',' ') as responsable,
            CASE WHEN U.activo THEN 'SI' ELSE 'NO' END as activo
          FROM
            modulo_base.usuario as U,
            modulo_base.persona as P
          WHERE
            U.id_persona_responsable=P.id AND
            (
            usuario ILIKE '%$text%' OR
            replace(P.denominacion,';',' ') ILIKE '%$text%'            
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id,$id_persona_responsable,$usuario,$clave_type,$clave_value,$activo,$perfil_data){
    $db=SIGA::DBController();
    
    if(!$id_persona_responsable)
      return array("success"=>false,"message"=>"Error. El campo responsable se encuentra vacío.");
    
    if(!$usuario)
      return array("success"=>false,"message"=>"Error. El campo usuario se encuentra vacío.");
    
    //verificar si el usuario ya existe
    $existe=$db->Execute("SELECT count(*) FROM modulo_base.usuario WHERE text(id)<>'$id' AND usuario='$usuario'");
    if($existe[0][0]>0)
      return array("success"=>false,"message"=>"Error. El usuario «${usuario}» ya existe.");      
    
    
    //buscar el tipo de clave anterior
    if($id!=""){//si es modificar
      $clave_anterior=$db->Execute("SELECT clave FROM modulo_base.usuario WHERE id='$id'");
      $clave_anterior=json_decode($clave_anterior[0][0],true);
      //$clave_anterior["type"]
      //$clave_anterior["value"]
      if($clave_anterior["type"]!=$clave_type){
        if($clave_value=="" and substr($clave_type,0,5)!="ldap_"){//si no ingresa la clave, salir. dado que no se puede dejar la clave anterior pq cambio el tipo
          return array("success"=>false,"message"=>"Error. Se modificó el tipo de contraseña, ingrese la nueva contraseña.");    
        }
        $sw=true;
      }
      else{//si es el mismo tipo de clave        
        if($clave_value=="")//no modificar la clave
          $sw=false;        
        else//modificar la clave
          $sw=true;  
      }
    }
    else//si es nuevo
      $sw=true;    
    
    if($sw==true){
      $value="";
      switch($clave_type){
        case "plain":
          $value=$clave_value;
          break;
        case "md5": 
        case "sha1":
        case "sha256":
        case "sha512":
          $value=hash($clave_type,$clave_value);
          break;
      }
    }
    else
      $value=isset($clave_anterior["value"])?$clave_anterior["value"]:'';
    
    $clave="{\"type\": \"$clave_type\", \"value\":\"$value\"}";
    
    $data=array(
                "usuario"=>"'$usuario'",
                "clave"=>"'$clave'",
                //"tema"=>"'default'",
                "id_persona_responsable"=>"'$id_persona_responsable'",
                "activo"=>"'$activo'"
                );

    if($id!=""){//si es modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");  
      }
      //Modificar registro
      $result=$db->Update("modulo_base.usuario",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");  
      }
      //Insertar registro
      $result=$db->Insert("modulo_base.usuario",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result){
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_base.usuario", "messageDB"=>$db->GetMsgErrorClear());  
    }
    
    //modificar los permisos en usuario_perfil    
    $db->Delete("modulo_base.usuario_perfil","id_usuario=(select id from modulo_base.usuario where usuario like '$usuario')");
    $perfil_data=json_decode($perfil_data,true);
    
    for($i=0;$i<count($perfil_data);$i++){
      $data_perfil=array(
                         "anio"=>"'".$perfil_data[$i]["anio"]."'",
                         "id_usuario"=>"(select id from modulo_base.usuario where usuario like '$usuario')",
                         "perfil"=>"'".$perfil_data[$i]["perfil"]."'",
                         "activo"=>"'".$perfil_data[$i]["activo"]."'"                         
                         );
      $result=$db->Insert("modulo_base.usuario_perfil",$data_perfil);
    }
    
    return array("success"=>true,"message"=>"Datos guardados con exito.");  
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();

    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos.");  
    }
    
    $db->Delete("modulo_base.usuario_perfil","id_usuario='$id'");
    $result=$db->Delete("modulo_base.usuario","id='$id'");
    if(!$result){              
      return array("success"=>false,"message"=>"Error al eliminar en la tabla: modulo_base.usuario", "messageDB"=>$db->GetMsgErrorClear());
    }
    return array("success"=>true,"message"=>"Registro eliminado con éxito.");  
  }  
}  
?>
