<?php

class estructura_presupuestaria{
  
  public static function onGet_Codigo($id_accion_subespecifica){
    $db=SIGA::DBController();    
    $return=$db->Execute("SELECT _formatear_estructura_presupuestaria($id_accion_subespecifica) as estructura_presupuestaria");
    return $return;
  }
  
  public static function onGet_IdCodigo($codigo){
    $db=SIGA::DBController();    
    $return=$db->Execute("SELECT 
                            F.id_accion_subespecifica
                          FROM
                            modulo_base.formulacion as F
                          WHERE 
                            F.anio=".SIGA::data()." and 
                            not F.id_comprobante_apertura is null and
                            _formatear_estructura_presupuestaria(F.id_accion_subespecifica) ilike '$codigo'");
    return $return;
  }
  
  public static function onList_AP($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    /*$sql="select
            F.id_comprobante_apertura,
            F.id_accion_subespecifica,
            _formatear_estructura_presupuestaria(F.id_accion_subespecifica) as estructura_presupuestaria,
            ASE.denominacion_subespecifica,
            AE.denominacion_especifica,
            A.denominacion_centralizada
          FROM 
            modulo_base.formulacion AS F,
            modulo_base.accion_subespecifica ASE,
            modulo_base.accion_especifica AE,
            modulo_base.accion_centralizada A
          WHERE
            A.id=AE.id_accion_centralizada AND
            AE.id=ASE.id_accion_especifica AND  
            ASE.id=F.id_accion_subespecifica AND
            F.anio=".SIGA::data()." and
            F.tipo='F' and
            not F.id_comprobante_apertura is null";*/
    $sql="select 
            F.id_comprobante_apertura,
            F.id_accion_subespecifica,
            _formatear_estructura_presupuestaria(F.id_accion_subespecifica) as estructura_presupuestaria,
            ASE.denominacion_subespecifica,
            AE.denominacion_especifica,
            A.denominacion_centralizada
        from modulo_base.accion_centralizada A 
            join modulo_base.accion_especifica AE on  AE.id_accion_centralizada=A.id
            join modulo_base.accion_subespecifica ASE on ASE.id_accion_especifica=AE.id
            join modulo_base.formulacion AS F on  F.id_accion_subespecifica=ASE.id 
        where 
            F.anio=".SIGA::data()." and
            F.tipo='F' and
            not F.id_comprobante_apertura is null";

    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_APNomina($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="
        select 
            ASE.id id_accion_subespecifica,
            _formatear_estructura_presupuestaria(ASE.id) as estructura_presupuestaria,
            ASE.denominacion_subespecifica,
            AE.denominacion_especifica,
            A.denominacion_centralizada,
            F.anio
        from modulo_base.accion_centralizada A 
            join modulo_base.accion_especifica AE on  AE.id_accion_centralizada=A.id
            join modulo_base.accion_subespecifica ASE on ASE.id_accion_especifica=AE.id
            join modulo_base.formulacion AS F on  F.id_accion_subespecifica=ASE.id 
        where
            F.anio in (".SIGA::data().",".(SIGA::data()-1).",".(SIGA::data()+1).") and
            F.tipo='F'
       ";
            
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  //ACCION CENTRALIZADA
  public static function onList_AccionCentralizada($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT
            *,
            tipo||codigo_centralizada as tipo_codigo_centralizada
          FROM
            modulo_base.accion_centralizada
          WHERE
            (
              denominacion_centralizada ILIKE '%$text%' OR
              tipo||codigo_centralizada ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  //ACCION CENTRALIZADA
  public static function onList_AccionCentralizada_AP($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT DISTINCT
            AC.id,
            AC.tipo,
            AC.codigo_centralizada,
            AC.denominacion_centralizada,
            AC.tipo||AC.codigo_centralizada as tipo_codigo_centralizada
          FROM
            modulo_base.accion_centralizada as AC,
            modulo_base.accion_especifica as AE,
            modulo_base.accion_subespecifica as ASE,
            modulo_base.formulacion as F
          WHERE 
            F.anio='".SIGA::data()."' and not F.id_comprobante_apertura is null and F.id_accion_subespecifica=ASE.id and ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id AND
            (
              AC.denominacion_centralizada ILIKE '%$text%' OR
              AC.tipo||AC.codigo_centralizada ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave_AccionCentralizada( $access,
                                                    $id_accion_centralizada,
                                                    $tipo,
                                                    $codigo,
                                                    $denominacion){
    $db=SIGA::DBController(); 
    //verificar si el tipo y codigo no existen
    /*$existe=$db->Execute("select count(*) from modulo_base.accion_centralizada where tipo='$tipo' and codigo_centralizada='$codigo' and text(id)<>'$id_accion_centralizada'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=> "Error. El código $tipo$codigo ya existe."");*/
    
    $data=array("tipo"=>"'$tipo'", "codigo_centralizada"=>"'$codigo'", "denominacion_centralizada"=>"'$denominacion'");
    
    if($id_accion_centralizada!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para modificar datos.');

      //Modificar registro
      $result=$db->Update("modulo_base.accion_centralizada",
                          $data,
                          "id='$id_accion_centralizada'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para guardar datos.');
      //Insertar registro
      $result=$db->Insert("modulo_base.accion_centralizada",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)             
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_centralizada', "messageDB"=>"".$db->GetMsgErrorClear()."");
    
    return array("success"=>true, "message"=> 'Datos guardados con exito.');
  }
  
  public static function onDelete_AccionCentralizada($access,$id_accion_centralizada){
    $db=SIGA::DBController();
     
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para eliminar datos.');
    //verificar si tiene acciones especificas asociados
    $existe=$db->Execute("select count(*) from modulo_base.accion_especifica where id_accion_centralizada='$id_accion_centralizada'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=> 'Error. La acción centralizada posee acciones específicas.');
    
    $result=$db->Delete("modulo_base.accion_centralizada","id='$id_accion_centralizada'");
    if(!$result)
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_centralizada', "messageDB"=>"".$db->GetMsgErrorClear()."");

    return array("success"=>true, "message"=> 'Registro eliminado con éxito.');
  }
  
  //ACCION ESPECIFICA
  public static function onList_AccionEspecifica($id_accion_centralizada,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT
            *
          FROM
            modulo_base.accion_especifica
          WHERE
            id_accion_centralizada='$id_accion_centralizada' AND
            (
              denominacion_especifica ILIKE '%$text%' OR              
              codigo_especifica ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_AccionEspecifica_AP($id_accion_centralizada,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT DISTINCT
            AE.id,
            AE.id_accion_centralizada,
            AE.codigo_especifica,
            AE.denominacion_especifica
          FROM            
            modulo_base.accion_especifica as AE,
            modulo_base.accion_subespecifica as ASE,
            modulo_base.formulacion as F
          WHERE 
            F.anio='".SIGA::data()."' and not F.id_comprobante_apertura is null and F.id_accion_subespecifica=ASE.id and ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada='$id_accion_centralizada' AND 
            (
              AE.denominacion_especifica ILIKE '%$text%' OR
              AE.codigo_especifica ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave_AccionEspecifica( $access,
                                                  $id_accion_centralizada,
                                                  $id_accion_especifica,
                                                  $codigo,
                                                  $denominacion){
    $db=SIGA::DBController();
    //verificar si el tipo y codigo no existen
    $existe=$db->Execute("select count(*) from modulo_base.accion_especifica where id_accion_centralizada='$id_accion_centralizada' and codigo_especifica='$codigo' and text(id)<>'$id_accion_especifica'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=> 'Error. El código $codigo ya existe.');

    $data=array("codigo_especifica"=>"'$codigo'", "denominacion_especifica"=>"'$denominacion'","id_accion_centralizada"=>"'$id_accion_centralizada'");
    
    if($id_accion_especifica!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para modificar datos.');

      //Modificar registro
      $result=$db->Update("modulo_base.accion_especifica",
                          $data,
                          "id='$id_accion_especifica'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para guardar datos.');
      
      //Insertar registro
      $result=$db->Insert("modulo_base.accion_especifica",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)                    
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_especifica', "messageDB"=>"".$db->GetMsgErrorClear()."");

    return array("success"=>true, "message"=> 'Datos guardados con exito.');
  }
  
  public static function onDelete_AccionEspecifica($access,$id_accion_especifica){
    $db=SIGA::DBController();     
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para eliminar datos.');

    //verificar si tiene acciones subespecificas asociadas
    $existe=$db->Execute("select count(*) from modulo_base.accion_subespecifica where id_accion_especifica='$id_accion_especifica'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=> 'Error. La acción específica posee acciones subespecíficas.');

    $result=$db->Delete("modulo_base.accion_especifica","id='$id_accion_especifica'");
    if(!$result)
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_especifica', "messageDB"=>"".$db->GetMsgErrorClear()."");

    return array("success"=>true, "message"=> 'Registro eliminado con éxito.');
  }
  
  //ACCION SUBESPECIFICA
  public static function onList_AccionSubEspecifica($id_accion_especifica,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT
            *
          FROM
            modulo_base.accion_subespecifica
          WHERE
            id_accion_especifica='$id_accion_especifica' AND
            (
              denominacion_subespecifica ILIKE '%$text%' OR              
              codigo_subespecifica ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_AccionSubEspecifica_AP($id_accion_especifica,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();        
    $sql="SELECT DISTINCT
            ASE.id,
            ASE.id_accion_especifica,
            ASE.codigo_subespecifica,
            ASE.denominacion_subespecifica
          FROM      
            modulo_base.accion_subespecifica as ASE,
            modulo_base.formulacion as F
          WHERE 
            F.anio='".SIGA::data()."' and not F.id_comprobante_apertura is null and F.id_accion_subespecifica=ASE.id and ASE.id_accion_especifica='$id_accion_especifica' AND 
            (
              ASE.denominacion_subespecifica ILIKE '%$text%' OR
              ASE.codigo_subespecifica ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave_AccionSubEspecifica(  $access,
                                                      $id_accion_especifica,
                                                      $id_accion_subespecifica,
                                                      $codigo,
                                                      $denominacion){
    $db=SIGA::DBController();
    //verificar si el tipo y codigo no existen
    $existe=$db->Execute("select count(*) from modulo_base.accion_subespecifica where id_accion_especifica='$id_accion_especifica' and codigo_subespecifica='$codigo' and text(id)<>'$id_accion_subespecifica'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=> 'Error. El código $codigo ya existe.');

    $data=array("codigo_subespecifica"=>"'$codigo'", "denominacion_subespecifica"=>"'$denominacion'", "id_accion_especifica"=>"'$id_accion_especifica'");
    
    if($id_accion_subespecifica!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para modificar datos.');

      //Modificar registro
      $result=$db->Update("modulo_base.accion_subespecifica",
                          $data,
                          "id='$id_accion_subespecifica'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para guardar datos.');
      
      //Insertar registro
      $result=$db->Insert("modulo_base.accion_subespecifica",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)               
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_subespecifica', "messageDB"=>"".$db->GetMsgErrorClear()."");

    return array("success"=>true, "message"=> 'Datos guardados con exito.');
  }
  
  public static function onDelete_AccionSubEspecifica($access,$id_accion_subespecifica){
    $db=SIGA::DBController();     
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=> 'Error. El usuario no tiene permiso para eliminar datos.');
    
    $result=$db->Delete("modulo_base.accion_subespecifica","id='$id_accion_subespecifica'");
    if(!$result)
      return array("success"=>false, "message"=> 'Error al guardar en la tabla: modulo_base.accion_subespecifica', "messageDB"=>"".$db->GetMsgErrorClear()."");

    return array("success"=>true, "message"=> 'Registro eliminado con éxito.');
  }  
}
?>