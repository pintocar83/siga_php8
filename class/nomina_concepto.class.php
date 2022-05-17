<?php
class nomina_concepto{
  public static function onGet_Correlativo(){
    $db=SIGA::DBController();
    
    $sql="SELECT codigo FROM modulo_nomina.concepto ORDER BY codigo DESC LIMIT 1";
    $return=$db->Execute($sql);
    if(isset($return[0][0]))
      $codigo=$return[0][0]*1+1;
    else
      $codigo=1;
    $codigo=str_pad($codigo,3,"0",STR_PAD_LEFT);
    return "$codigo";
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT
            *
          FROM
            modulo_nomina.concepto as C
          WHERE
            C.id='$id'";
    return $db->Execute($sql);
  }  
  
  public static function onList($text,$start,$limit,$sort,$tipo=NULL){
    $db=SIGA::DBController();

    $add="";
    if($tipo){
      $tipo=explode(",", $tipo);
      $add.=" C.tipo in ('".implode("','", $tipo)."') AND ";
    }

    $sql="SELECT
            *
          FROM
            modulo_nomina.concepto as C
          WHERE
            $add
            C.activo AND
            (
              C.concepto ILIKE '%$text%' OR
              C.codigo ILIKE '%$text%' OR
              C.identificador ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_Afectacion($id_concepto,$start,$limit,$sort){
    $db=SIGA::DBController();
    $sql="SELECT
            N.codigo||' '||N.nomina as codigo_nomina,
            N.nomina,            
            CPC.*,
            to_char(CPC.fecha,'DD/MM/YYYY') as fecha_formateada,
            _formatear_cuenta_presupuestaria(CPC.id_cuenta_presupuestaria) as cuenta_presupuestaria,
            _formatear_cuenta_presupuestaria(CPC.id_cuenta_presupuestaria_ap) as cuenta_presupuestaria_ap,
            _formatear_cuenta_contable(CPC.id_cuenta_contable) as cuenta_contable,
            _formatear_cuenta_contable(CPC.id_cuenta_contable_ap) as cuenta_contable_ap
          FROM
            modulo_nomina.concepto_presupuesto_contabilidad as CPC,
            modulo_nomina.nomina as N
          WHERE
            CPC.id_concepto=$id_concepto AND
            CPC.id_nomina=N.id
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_Formula($id_concepto,$start,$limit,$sort){
    $db=SIGA::DBController();
    $sql="SELECT
            *,
            to_char(fecha,'DD/MM/YYYY') as fecha_formateada
          FROM
            modulo_nomina.concepto_formula
          WHERE
            id_concepto=$id_concepto
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id,$codigo,$concepto,$identificador,$tipo,$orden){
    $db=SIGA::DBController();
    
    //validación de la información
    if(!$codigo)
      return array("success"=>false,"message"=>"Error. El campo código está vacio.");
        
    if(!$concepto)
      return array("success"=>false,"message"=>"Error. El campo concepto está vacío.");

    if(!$identificador)
      return array("success"=>false,"message"=>"Error. El campo identificador está vacío.");
    
    if(!$tipo or strtoupper($tipo)=="NULL")
      $tipo="";    
    
    if(!is_numeric($orden))
      return array("success"=>false,"message"=>"Error. El orden del concepto es inválido.");
    
    //verificar si existe el codigo
    $existe=$db->Execute("SELECT count(*) FROM modulo_nomina.concepto WHERE codigo='".$codigo."' AND text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false,"message"=>"Error. El código $codigo ya existe. Se generó un nuevo código, vuelva a guardar los datos.", "action"=>1);
    
    $data=array(
                "codigo"=>"'".$codigo."'",
                "concepto"=>"'$concepto'",
                "identificador"=>"'$identificador'",
                "tipo"=>"'$tipo'",
                "orden"=>"'$orden'"
                );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_nomina.concepto",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_nomina.concepto",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.concepto'");

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onSave_Afectacion( $access,
                                            $id_concepto,
                                            $id_nomina,
                                            $fecha,
                                            $id_cuenta_presupuestaria,
                                            $id_cuenta_presupuestaria_ap,
                                            $id_cuenta_contable,
                                            $id_cuenta_contable_ap
                                            ){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
    
    $db->Delete("modulo_nomina.concepto_presupuesto_contabilidad","id_concepto='$id_concepto' AND id_nomina='$id_nomina' AND fecha='$fecha'");
    
    $id_cuenta_presupuestaria=!$id_cuenta_presupuestaria?"null":"'$id_cuenta_presupuestaria'";
    $id_cuenta_presupuestaria_ap=!$id_cuenta_presupuestaria_ap?"null":"'$id_cuenta_presupuestaria_ap'";
    $id_cuenta_contable=!$id_cuenta_contable?"null":"'$id_cuenta_contable'";
    $id_cuenta_contable_ap=!$id_cuenta_contable_ap?"null":"'$id_cuenta_contable_ap'";
    
    $result=$db->Insert("modulo_nomina.concepto_presupuesto_contabilidad",array(
                                                                                "id_concepto"=>"'".$id_concepto."'",
                                                                                "id_nomina"=>"'$id_nomina'",
                                                                                "fecha"=>"'$fecha'",
                                                                                "id_cuenta_presupuestaria"=>"$id_cuenta_presupuestaria",
                                                                                "id_cuenta_presupuestaria_ap"=>"$id_cuenta_presupuestaria_ap",
                                                                                "id_cuenta_contable"=>"$id_cuenta_contable",
                                                                                "id_cuenta_contable_ap"=>"$id_cuenta_contable_ap"
                                                                                ));

    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.concepto_presupuesto_contabilidad'");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onSave_Formula($access,
                                        $id_concepto,
                                        $fecha,
                                        $definicion,
                                        $definicion_ap
                                        ){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
    
    $db->Delete("modulo_nomina.concepto_formula","id_concepto='$id_concepto' AND fecha='$fecha'");
    
    $result=$db->Insert("modulo_nomina.concepto_formula",array(
                                                                "id_concepto"=>"'".$id_concepto."'",
                                                                "fecha"=>"'$fecha'",
                                                                "definicion"=>"'$definicion'",
                                                                "definicion_ap"=>"'$definicion_ap'"
                                                                ));

    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.concepto_formula'");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete_Afectacion( $access,
                                              $id_concepto,
                                              $id_nomina,
                                              $fecha
                                              ){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    
    $result=$db->Delete("modulo_nomina.concepto_presupuesto_contabilidad","id_concepto='$id_concepto' AND id_nomina='$id_nomina' AND fecha='$fecha'");

    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.concepto_presupuesto_contabilidad'");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete_Formula($access,
                                          $id_concepto,
                                          $fecha
                                          ){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    
    $result=$db->Delete("modulo_nomina.concepto_formula","id_concepto='$id_concepto' AND fecha='$fecha'");

    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.concepto_formula'");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  
}  
?>