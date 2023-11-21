<?php
class nomina_extension_rrhh_hoja{
  public static function onCorrelativo(){
    $db=SIGA::DBController();

    $sql="SELECT codigo FROM modulo_nomina.extension_rrhh_hoja ORDER BY codigo DESC LIMIT 1";
    $return=$db->Execute($sql);
    if(isset($return[0][0])){
      $tmp=explode("-",$return[0][0]);
      if(count($tmp)===2){
        $numero=$tmp[count($tmp)-1];
        $length_numero=strlen($numero);
        $codigo=$tmp[0]."-".str_pad($numero*1+1,$length_numero,"0",STR_PAD_LEFT);
        return "$codigo";
      }
      else{
        $codigo=$return[0][0]*1+1;
      }
    }
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
            modulo_nomina.extension_rrhh_hoja as C
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
            C.*,
            C.codigo || ' - ' || C.descripcion codigo_description
          FROM
            modulo_nomina.extension_rrhh_hoja as C
          WHERE
            $add
            (
              C.descripcion ILIKE '%$text%' OR
              C.codigo ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0]["total"];
    return $return;
  }

  public static function onSave($access,$id,$codigo,$descripcion,$tipo,$id_periodo,$id_nomina,$activo,$id_hoja_plantilla=NULL){
    $db=SIGA::DBController();

    //validación de la información
    if(!$codigo)
      return array("success"=>false,"message"=>"Error. El campo código está vacio.");

    if(!$descripcion)
      return array("success"=>false,"message"=>"Error. El campo descripcion está vacío.");

    //verificar si existe el codigo
    $existe=$db->Execute("SELECT count(*) FROM modulo_nomina.extension_rrhh_hoja WHERE codigo='".$codigo."' AND text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false,"message"=>"Error. El código $codigo ya existe. Se generó un nuevo código, vuelva a guardar los datos.", "action"=>1);

    $data=[
      "codigo"=>"'$codigo'",
      "descripcion"=>"'$descripcion'",
      "tipo"=>"'$tipo'",
      "id_periodo"=>"ARRAY[$id_periodo]",
      "id_nomina"=>"ARRAY[$id_nomina]",
      "activo"=>"'$activo'"
    ];

    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_nomina.extension_rrhh_hoja",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      //$result=$db->Insert("modulo_nomina.extension_rrhh_hoja",$data);
      $result=$db->Execute("INSERT INTO modulo_nomina.extension_rrhh_hoja(codigo,descripcion,tipo,id_periodo,id_nomina,activo)
                           VALUES('$codigo','$descripcion','$tipo',ARRAY[$id_periodo],ARRAY[$id_nomina],'$activo') RETURNING id");
      if(isset($result[0]["id"])){
        $id = $result[0]["id"];

        include_once(SIGA::path()."/class/nomina_extension_rrhh.class.php");
        nomina_extension_rrhh::onGenerar($access, $id, $id_hoja_plantilla);
      }
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.extension_rrhh_hoja'", "error"=>$db->GetMsgErrorClear());

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }

    $db->Delete("modulo_nomina.extension_rrhh_hoja_fila", "id_hoja='$id'");
    $db->Delete("modulo_nomina.extension_rrhh_hoja_valor ", "id_hoja='$id'");

    $result=$db->Delete("modulo_nomina.extension_rrhh_hoja","id='$id'");
    if(!$result){
      return array("success"=>false,"message"=>"Error al borrar en la tabla: 'modulo_nomina.extension_rrhh_hoja'","error"=>$db->GetMsgError());
    }
    return ["success"=> true, "message" =>"Registro eliminado con éxito."];
  }

  public static function onDuplicar($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para realizar esta acción.'}";
      return;
    }

    $codigo = self::onCorrelativo();


    $result=$db->Execute("INSERT INTO modulo_nomina.extension_rrhh_hoja(codigo,descripcion,tipo,id_periodo,id_nomina,id_hoja_plantilla,activo)
                           SELECT '$codigo', descripcion || ' (DUPLICADO)', tipo, id_periodo, id_nomina, id_hoja_plantilla, activo
                           FROM modulo_nomina.extension_rrhh_hoja WHERE id='$id'
                           RETURNING id");
    if(!isset($result[0]["id"])){
      return ["success"=> false, "message" =>"Error al duplicar registro."];
    }
    $id_duplicado = $result[0]["id"];

    $db->Execute("INSERT INTO modulo_nomina.extension_rrhh_hoja_fila(id_hoja, id_nomina, id_ficha)
                  SELECT '$id_duplicado', id_nomina, id_ficha FROM modulo_nomina.extension_rrhh_hoja_fila WHERE id_hoja='$id'");

    $db->Execute("INSERT INTO modulo_nomina.extension_rrhh_hoja_valor(id_hoja, id_nomina, id_ficha, id_columna, valor)
                  SELECT '$id_duplicado', id_nomina, id_ficha, id_columna, valor FROM modulo_nomina.extension_rrhh_hoja_valor WHERE id_hoja='$id'");

    return ["success"=> true, "message" =>"Registro duplicado con éxito.", "id" => $id_duplicado];
  }
}
?>