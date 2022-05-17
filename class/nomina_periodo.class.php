<?php
class nomina_periodo{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.periodo WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  } 
  
  public static function onList($id_nomina,$tipo,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $data=SIGA::data();
    
    $sql="
      SELECT 
        P.*, 
        P.codigo||'  '||to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY') as periodo,
        T.denominacion tipo_denominacion
      FROM 
        modulo_nomina.periodo as P 
        LEFT JOIN modulo_nomina.periodo_tipo as T on T.tipo=P.tipo 
      WHERE EXTRACT(YEAR FROM P.fecha_inicio)=$data";
          
    if($id_nomina){
      $sql="SELECT P.*, P.codigo||'  '||to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY') as periodo
          FROM modulo_nomina.periodo as P, modulo_nomina.nomina as N
          WHERE P.tipo = N.tipo and N.id='$id_nomina'";
    }
    if($tipo){
      $sql="SELECT P.*, P.codigo||'  '||to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY') as periodo
          FROM modulo_nomina.periodo as P WHERE P.tipo='$tipo'";
      $sql="SELECT
              P.*,
              to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY') as fecha,
              P.codigo||'  '||P.descripcion as periodo
              --P.codigo||'  '||P.descripcion ||' ['||to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY')||']' as periodo
              --P.codigo||'  '||to_char(P.fecha_inicio,'DD/MM/YYYY')||' - '||to_char(P.fecha_culminacion,'DD/MM/YYYY')||' '||P.descripcion as periodo
          FROM modulo_nomina.periodo as P 
          WHERE P.tipo='$tipo' AND EXTRACT(YEAR FROM P.fecha_inicio)=$data";
    }
    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id,$codigo,$descripcion,$fecha_inicio,$fecha_culminacion,$tipo,$cerrado){
    $db=SIGA::DBController();
    
    //validación de la información
    if(!$codigo)
      return array("success"=>false,"message"=>"Error. El campo código está vacio.");
        
    if(!$descripcion)
      return array("success"=>false,"message"=>"Error. El campo descripción está vacío.");

    if(!$fecha_inicio)
      return array("success"=>false,"message"=>"Error. El campo fecha de inicio está vacío.");

    if(!$fecha_culminacion)
      return array("success"=>false,"message"=>"Error. El campo fecha de culminacion está vacío."); 
        
    $data=array(
      "codigo"            => "'$codigo'",
      "descripcion"       => "'$descripcion'",
      "fecha_inicio"      => "'$fecha_inicio'",
      "fecha_culminacion" => "'$fecha_culminacion'",
      "tipo"              => "'$tipo'",
      "cerrado"           => "'$cerrado'"
    );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_nomina.periodo",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_nomina.periodo",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)      
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.periodo'");

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }

    $db->Delete("modulo_nomina.concepto_periodo", "id_periodo='$id'");
    $db->Delete("modulo_nomina.ficha_concepto ",  "id_periodo='$id'");
    $db->Delete("modulo_nomina.periodo_nota ",    "id_periodo='$id'");
    
    $result=$db->Delete("modulo_nomina.periodo","id='$id'");
    if(!$result){                    
      return array("success"=>false,"message"=>"Error al borrar en la tabla: 'modulo_nomina.periodo'","error"=>$db->GetMsgError());
    }
    return ["success"=> true, "message" =>"Registro eliminado con éxito."];
  }  

  public static function onDuplicar($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }
  
    $result=$db->Execute("
      INSERT INTO modulo_nomina.periodo(
        codigo,
        descripcion,
        fecha_inicio,
        fecha_culminacion,
        tipo,
        cerrado
      ) 
      SELECT 
        '999' codigo,
        descripcion || ' *** DUPLICADO ***',
        fecha_inicio,
        fecha_culminacion,
        tipo,
        'f' cerrado
      FROM modulo_nomina.periodo WHERE id='$id'
      RETURNING id
    ");


    if(!$result){                    
      return array("success"=>false,"message"=>"Error al duplicar en la tabla: 'modulo_nomina.periodo'","error"=>$db->GetMsgError());
    }

    $id_periodo=$result[0]["id"];
    if($id_periodo){
      $db->Execute("
        INSERT INTO modulo_nomina.concepto_periodo(
          id_periodo,
          id_concepto,
          id_nomina
        ) 
        SELECT 
          '$id_periodo',
          id_concepto,
          id_nomina
        FROM modulo_nomina.concepto_periodo WHERE id_periodo='$id'
      ");

      $db->Execute("
        INSERT INTO modulo_nomina.concepto_periodo(id_periodo,id_concepto,id_nomina) 
        SELECT '$id_periodo' id_periodo, id_concepto, id_nomina FROM modulo_nomina.concepto_periodo WHERE id_periodo='$id'
      ");

      $db->Execute("
        INSERT INTO modulo_nomina.ficha_concepto(id_periodo,id_ficha,id_concepto,id_nomina, valor) 
        SELECT '$id_periodo' id_periodo, id_ficha, id_concepto, id_nomina, valor FROM modulo_nomina.ficha_concepto WHERE id_periodo='$id'
      ");

      $db->Execute("
        INSERT INTO modulo_nomina.periodo_nota(id_periodo,id_nomina,nota) 
        SELECT '$id_periodo' id_periodo, id_nomina, nota FROM modulo_nomina.periodo_nota WHERE id_periodo='$id'
      ");
    }


    return ["success"=> true, "message" =>"Periodo duplicado con éxito.", "id"=>$result[0]["id"]];
  }  
}  
?>