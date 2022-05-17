<?php
class item{
  public static function onGetCodigo($id_item_tipo){
    $db=SIGA::DBController();    
    $sql="SELECT codigo FROM modulo_base.item WHERE id_item_tipo='$id_item_tipo' ORDER BY codigo DESC LIMIT 1";
    $return=$db->Execute($sql);
    if(isset($return[0][0]))
      $codigo=$return[0][0]*1+1;
    else
      $codigo=1;
    $codigo=str_pad($codigo,4,"0",STR_PAD_LEFT);
    return "$codigo";
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();    
    $sql="SELECT * FROM modulo_base.item WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($id_item_tipo,$disponibilidad,$text,$start,$limit,$sort='',$group=''){
    $db=SIGA::DBController();    
    $add="";
    $id_item_tipo=json_decode($id_item_tipo,true);
    if(is_array($id_item_tipo)){
      $add.="(";
      for($i=0;$i<count($id_item_tipo);$i++){        
        $add.="I.id_item_tipo='".$id_item_tipo[$i]."' ";
        if($i<count($id_item_tipo)-1)
          $add.="OR ";
      }
      $add.=") AND ";
    }
    else{
      $add.="I.id_item_tipo='$id_item_tipo' AND";
    }
    $add_disponibilidad="";
    if($disponibilidad and $id_item_tipo=="1")
      $add_disponibilidad="modulo_inventario.disponibilidad_material(I.id) as disponibilidad, ";
    
    $sql="SELECT
            IT.tipo as item_tipo,
            I.id,
            I.codigo,
            I.item as denominacion,
            $add_disponibilidad
            I.id_cuenta_presupuestaria || ' ' || CP.denominacion as cuenta_presupuestaria
          FROM
            modulo_base.item as I,
            modulo_base.item_tipo as IT,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
            IT.id=I.id_item_tipo AND
            I.activo AND
            $add
            I.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            (
              TEXT(I.codigo) ILIKE '$text%' OR
              I.item ILIKE '%$text%' OR
              (I.id_cuenta_presupuestaria || ' ' || CP.denominacion) ILIKE '%$text%'
            )
            ";
    
    
    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,
                                $id_item_tipo,
                                $id,
                                $codigo,
                                $denominacion,
                                $id_cuenta_presupuestaria,
                                $aplica_iva){
    $db=SIGA::DBController();
    
    //validación de la información
    if(!$codigo)
      return array("success"=>false,"message"=>"Error. El campo código está vacio.");
        
    if(!is_numeric($id_item_tipo))
      return array("success"=>false,"message"=>"Error. Tipo de item no especificado.");
        
    if(!$denominacion)
      return array("success"=>false,"message"=>"Error. El campo denominación está vacío.");

    if(!$id_cuenta_presupuestaria)
      return array("success"=>false,"message"=>"Error. El campo partida presupuestaria está vacío.");
    
    //verificar si existe el codigo
    $existe=$db->Execute("SELECT count(*) FROM modulo_base.item WHERE codigo='".$codigo."' AND id_item_tipo='$id_item_tipo' AND text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false,"message"=>"Error. El código $codigo ya existe. Se generó un nuevo código, vuelva a guardar los datos.", "action"=>1);
    
    $data=array("id_item_tipo"=>"'$id_item_tipo'",
                "codigo"=>"'".$codigo."'",
                "item"=>"'$denominacion'",
                "id_cuenta_presupuestaria"=>"'$id_cuenta_presupuestaria'",
                "aplica_iva"=>"'$aplica_iva'"
                );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.item",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.item",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_base.item'");

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();     
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos.");    
    $result=$db->Delete("modulo_base.item","id='$id'");
    if(!$result)
      return array("success"=>false,"message"=>"Error al eliminar en la tabla: 'modulo_base.item'");    
    return array("success"=>true,"message"=>"Registro eliminado con éxito.");
  }
}
?>