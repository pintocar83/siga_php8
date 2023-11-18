<?php
include_once(SIGA::path()."/class/nomina.class.php");

class nomina_extension_rrhh {
  public static function onGenerar($access, $id_hoja){
    SIGA::$DBMode=PGSQL_ASSOC;
    $db=SIGA::DBController();

    $sql="SELECT * FROM modulo_nomina.extension_rrhh_hoja WHERE id='$id_hoja'";
    $hoja = $db->Execute($sql);
    if(!isset($hoja[0])){
      return [];
    }

    $hoja = $hoja[0];
    $id_periodo = str_replace(['{','}'], "", $hoja["id_periodo"]);
    $id_nomina = str_replace(['{','}'], "", $hoja["id_nomina"]);

    //buscar periodo ordenado de mas reciente a mas antiguo por fecha_culminacion
    $sql = "SELECT * FROM modulo_nomina.periodo WHERE id IN ($id_periodo) ORDER BY fecha_culminacion DESC";
    $periodo = $db->Execute($sql);
    if(!isset($periodo[0])){
      return [];
    }

    $fecha_culminacion = $periodo[0]["fecha_culminacion"];

    //borrar los valores de las columnas de tipo conceptos (calculados desde nomina), y mantener los ingresados a mano
    $sql = "
      DELETE FROM modulo_nomina.extension_rrhh_hoja_valor
      WHERE
        id_hoja='$id_hoja' AND
        id_columna IN ( SELECT id
                        FROM modulo_nomina.extension_rrhh_hoja_columna
                        WHERE tipo ILIKE 'concepto' )
    ";
    $db->Execute($sql);

    //borrar las filas para regenerarlas
    $sql = "
      DELETE FROM modulo_nomina.extension_rrhh_hoja_fila WHERE id_hoja='$id_hoja'
    ";
    $db->Execute($sql);

    //insertar a las filas por personas (ficha) y nomina
    $sql = "
      INSERT INTO modulo_nomina.extension_rrhh_hoja_fila(id_hoja, id_nomina, id_ficha)
      SELECT DISTINCT
        $id_hoja id_hoja,
        id_nomina,
        id_ficha
      FROM
        modulo_nomina.ficha_concepto
      WHERE
        id_periodo IN ($id_periodo) AND
        id_nomina IN ($id_nomina)
    ";
    $db->Execute($sql);

    $sql = "SELECT * FROM modulo_nomina.extension_rrhh_hoja_columna ORDER BY orden, id";
    $columna = $db->Execute($sql);

    $sql = "SELECT * FROM modulo_nomina.extension_rrhh_hoja_fila WHERE id_hoja = '$id_hoja'";
    $fila = $db->Execute($sql);

    //Por cada ficha, buscar los conceptos en la nomina y periodo
    for($f=0; $f<count($fila); $f++){
      $fila[$f]["concepto_periodo"] = [];

      for($p=0; $p<count($periodo); $p++){
        $fila[$f]["concepto_periodo"][$p] = nomina::ficha_concepto($fila[$f]["id_nomina"],$periodo[$p]["id"],$fila[$f]["id_ficha"]);
      }

      for($c=0; $c<count($columna); $c++){
        $valor = NULL;
        if($columna[$c]["tipo"] == "concepto"){
          $valor = self::columna_concepto($columna[$c]["operacion"], json_decode($columna[$c]["valor"], true), $fila[$f]["concepto_periodo"]);
        }

        if($valor !== NULL){
          $db->Insert("modulo_nomina.extension_rrhh_hoja_valor", [
            "id_hoja" => "'$id_hoja'",
            "id_nomina" => "'".$fila[$f]["id_nomina"]."'",
            "id_ficha" => "'".$fila[$f]["id_ficha"]."'",
            "id_columna" => "'".$columna[$c]["id"]."'",
            "valor" => "'".$db->EscapeString($valor)."'"
          ]);
        }
      }
    }

    return ["success"=>true, "message"=>"Generación de la hoja realizada con éxito."];
  }

  public static function columna_concepto($operacion, $id_concepto, $concepto_periodo){
    switch($operacion){
      case "MAX":               return self::concepto_max($id_concepto, $concepto_periodo);
      case "MAX_SUM":           return self::concepto_max_sum($id_concepto, $concepto_periodo);
      case "SUM":               return self::concepto_sum($id_concepto, $concepto_periodo);
    }
    return NULL;
  }

  public static function concepto_max($id_concepto, $concepto_periodo){
    $max = 0;
    for($p=0; $p<count($concepto_periodo); $p++) {
      for($c=0; $c<count($concepto_periodo[$p]["concepto"]); $c++) {
        if(in_array($concepto_periodo[$p]["concepto"][$c]["id_concepto"], $id_concepto) &&
          $concepto_periodo[$p]["concepto"][$c]["valor"] > $max){
          $max = $concepto_periodo[$p]["concepto"][$c]["valor"];
        }
      }
    }
    return $max == 0 ? NULL : $max;
  }

  public static function concepto_max_sum($id_concepto, $concepto_periodo){
    $max = 0;
    for($p=0; $p<count($concepto_periodo); $p++) {
      $sum = 0;
      for($c=0; $c<count($concepto_periodo[$p]["concepto"]); $c++) {
        if(in_array($concepto_periodo[$p]["concepto"][$c]["id_concepto"], $id_concepto)){
          $sum += $concepto_periodo[$p]["concepto"][$c]["valor"];
        }
      }

      if($sum > $max){
        $max = $sum;
      }
    }
    return $max == 0 ? NULL : $max;
  }

  public static function concepto_sum($id_concepto, $concepto_periodo){
    $sum = 0;
    for($p=0; $p<count($concepto_periodo); $p++) {
      for($c=0; $c<count($concepto_periodo[$p]["concepto"]); $c++) {
        if(in_array($concepto_periodo[$p]["concepto"][$c]["id_concepto"], $id_concepto)){
          $sum += $concepto_periodo[$p]["concepto"][$c]["valor"];
        }
      }
    }
    return $sum == 0 ? NULL : $sum;
  }

  public static function onGet($access, $id_hoja){
    SIGA::$DBMode=PGSQL_ASSOC;
    $db=SIGA::DBController();

    $sql="SELECT * FROM modulo_nomina.extension_rrhh_hoja WHERE id='$id_hoja'";
    $hoja = $db->Execute($sql);
    if(!isset($hoja[0])){
      return [];
    }

    $hoja = $hoja[0];
    $id_periodo = str_replace(['{','}'], "", $hoja["id_periodo"]);
    $id_nomina = str_replace(['{','}'], "", $hoja["id_nomina"]);

    //buscar periodo ordenado de mas reciente a mas antiguo por fecha_culminacion
    $sql = "SELECT * FROM modulo_nomina.periodo WHERE id IN ($id_periodo) ORDER BY fecha_culminacion DESC";
    $periodo = $db->Execute($sql);
    if(!isset($periodo[0])){
      return [];
    }

    $fecha_culminacion = $periodo[0]["fecha_culminacion"];


    $sql = "SELECT * FROM modulo_nomina.extension_rrhh_hoja_columna ORDER BY orden, id";
    $columna = $db->Execute($sql);

    $sql = "
      SELECT
        FILA.id,
        FILA.id_nomina,
        FILA.id_ficha,
        N.codigo || ' ' || N.nomina as nomina,
        P.identificacion_tipo as nacionalidad,
        P.identificacion_numero as cedula,
        split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
        replace(P.denominacion,';',' ') as nombres_apellidos,
        C.cargo,
        C.denominacion as cargo_denominacion,
        F.fecha_ingreso,
        F.cuenta_nomina,
        F.activo,
        PN.genero,
        ES.escala as escala_salarial
      FROM modulo_nomina.extension_rrhh_hoja_fila FILA
        INNER JOIN modulo_nomina.nomina          N ON N.id = FILA.id_nomina
        INNER JOIN modulo_nomina.ficha           F ON F.id = FILA.id_ficha
        LEFT JOIN modulo_nomina.escala_salarial ES ON ES.id = F.id_escala_salarial
        LEFT JOIN modulo_base.persona            P ON F.id_persona = P.id
        LEFT JOIN modulo_base.persona_natural   PN ON P.id = PN.id_persona
        LEFT JOIN modulo_nomina.cargo            C ON C.id=(
                                                        select id_cargo
                                                        from modulo_nomina.ficha_cargo
                                                        where id_ficha=F.id and fecha <= '$fecha_culminacion'
                                                        order by fecha desc
                                                        limit 1)
      WHERE
        FILA.id_hoja = '$id_hoja'
    ";

    //print $sql;

    $data = [];

    $fila = $db->Execute($sql);
    for($i=0; $i<count($fila); $i++) {
      $data[$i] = [
        "id"        => $fila[$i]["id"],
        "id_nomina" => $fila[$i]["id_nomina"],
        "id_ficha"  => $fila[$i]["id_ficha"]
      ];

      for($j=0; $j<count($columna); $j++) {
        $field = "column_".$columna[$j]["id"];
        if($columna[$j]["tipo"]=="ficha"){
          $field_value = $columna[$j]["valor"];
          $data[$i]["$field"] = $fila[$i]["$field_value"];
        }
        else if($columna[$j]["tipo"]=="#"){
          $data[$i]["$field"] = $i + 1;
        }

      }


      $sql="
        SELECT id_columna, valor
        FROM modulo_nomina.extension_rrhh_hoja_valor
        WHERE
          id_hoja   = '{$id_hoja}' AND
          id_nomina = '{$fila[$i]['id_nomina']}' AND
          id_ficha  = '{$fila[$i]['id_ficha']}'
      ";

      $columna_valor = $db->Execute($sql);
      for($j=0; $j<count($columna_valor); $j++) { 
        $field = "column_".$columna_valor[$j]["id_columna"];
        $data[$i]["$field"] = $columna_valor[$j]["valor"];
      }
    }

    return [
      "hoja" => $hoja,
      "columna" => self::ag_grid_column($columna),
      "data" => $data
    ];
  }

  public static function ag_grid_column($columna){
    $return = [];
    for($i=0; $i<count($columna); $i++) {
      $ag_grid_state = $columna[$i]["ag_grid_state"] ? json_decode($columna[$i]["ag_grid_state"], true) : [];
      $return[$i] = array_merge([
        "field" => "column_".$columna[$i]["id"],
        "headerName" => $columna[$i]["nombre"]
      ], $ag_grid_state);

      if(!isset($return[$i]["floatingFilterComponentParams"]))
        $return[$i]["floatingFilterComponentParams"]=[];

      switch($columna[$i]["tipo"]){
        case "ficha":
          $return[$i]["editable"]=false;
          $return[$i]["filter"]="agTextColumnFilter";
          $return[$i]["floatingFilterComponentParams"]["suppressFilterButton"] = true;
        case "text":
          $return[$i]["editable"]=true;
          $return[$i]["filter"]="agTextColumnFilter";
        break;
        case "select":
          $return[$i]["editable"]=true;
          $return[$i]["filter"]="agSetColumnFilter";
        break;
        case "concepto":
          $return[$i]["editable"]=false;
          $return[$i]["filter"]="agNumberColumnFilter";
        break;
      }

    }
    return $return;
  }

  public static function onSave($access, $id_hoja, $data, $ag_grid_state){
    SIGA::$DBMode=PGSQL_ASSOC;
    $db=SIGA::DBController();

    for($i=0; $i<count($data); $i++) {
      $id_nomina   = $data[$i]['id_nomina'];
      $id_ficha    = $data[$i]['id_ficha'];
      $id_columna  = $data[$i]['id_columna'];
      $valor       = $data[$i]['valor'];

      $sql="
        SELECT id
        FROM modulo_nomina.extension_rrhh_hoja_valor
        WHERE 
          id_hoja='$id_hoja' AND
          id_nomina='$id_nomina' AND
          id_ficha='$id_ficha' AND
          id_columna='$id_columna'
      ";

      $tmp = $db->Execute($sql);
      $id = isset($tmp[0]["id"]) ? $tmp[0]["id"] : NULL;

      if($id){
        $db->Update("modulo_nomina.extension_rrhh_hoja_valor",[
          "valor"=>"'".$db->EscapeString($valor)."'"
        ],"id=$id");
      }
      else{
        $db->Insert("modulo_nomina.extension_rrhh_hoja_valor",[
          "id_hoja"    => "$id_hoja",
          "id_nomina"  => "$id_nomina",
          "id_ficha"   => "$id_ficha",
          "id_columna" => "$id_columna",
          "valor"      =>"'".$db->EscapeString($valor)."'"
        ]);
      }

    }

    return ["success" => true];
  }

}
?>
