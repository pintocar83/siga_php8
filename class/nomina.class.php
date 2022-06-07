<?php
include_once(SIGA::path()."/library/functions/formatDate.php");
function nf($v){
  return number_format($v,2,".","");
}
class nomina{

  public static function cuenta_contable_abono(){
    return "211010100000";
  }

  public static function cuenta_contable_cxc(){
    return "112049900001";
  }

  public static function ficha_ordenar($a,$b){
    $v=$a["orden"]-$b["orden"];
    if($v==0) return $a["cedula"]-$b["cedula"];
    return $v;
  }

  public static function contar_lunes($fecha_inicio,$fecha_culminacion){
    $t_f0=strtotime($fecha_inicio);
    $t_f1=strtotime($fecha_culminacion);

    $total_dias=($t_f1-$t_f0)/86400;
    $contar=floor($total_dias/7);
    $resto=$total_dias%7;
    $sinceLastDay=floor(date("N",$t_f1)-1);//1 indica q es lunes

    if($sinceLastDay<0) $sinceLastDay+=7;
    if($resto>=$sinceLastDay) $contar++;

    return $contar;
  }

  public static function es_formula($definicion){
    if(!$definicion)
      return false;
    if(is_numeric($definicion))
      return false;
    return true;
  }

  public static function replace_token($str, $array_values){
    preg_match_all("/{[^}]*}/", $str, $token);
    for($i=0, $token=$token[0];$i<count($token) and $token;$i++)
      $str=str_replace($token[$i],$array_values[trim($token[$i],"{}")],$str);
    return $str;
  }

  public static function formula_tokens($formula){
    preg_match_all("/[A-Z_]+[0-9]*/", $formula, $token);
    $token=$token[0];
    $token_final=array();
    $k=0;
    //limpiar repetidos y quitar SI
    for($i=0;$i<count($token);$i++){
      if($token[$i]=="SI") continue;
      $sw=0;
      for($j=0;$j<$k;$j++)
        if($token[$i]==$token_final[$j]){
          $sw=1;
          break;
        }
      if($sw==0){
        $token_final[$k]=$token[$i];
        $k++;
      }
    }
    return $token_final;
  }

  public static function formula_tokens_indefinidos($conceptos_indentificador,$formula){
    $retorno="";
    if(!self::es_formula($formula)) return "";
    $tokens=self::formula_tokens($formula);
    if(count($tokens)==0) return "";
    for($i=0;$i<count($tokens);$i++){
      $encontro=false;
      for($j=0;$j<count($conceptos_indentificador);$j++){
        if($tokens[$i]==$conceptos_indentificador[$j]){
          $encontro=true;
          break;
          }
        }
      if(!$encontro)
        $retorno.="\"".$tokens[$i]."\" ";
      }
    return $retorno;
  }

  public static function formula_resolver($definida){

    $retorno["msj"]="";
    $x=0;
    $sw=true;
    $definida_contar=array();
    foreach($definida as $d => $valor_formula)
      $definida_contar[$d]=0;

    while($sw){
      //si x es igual a la cantidad de variables
      //salir, pq ya no hay nada que reemplazar en las formulas
      if($x==count($definida)) break;
      $x=0;
      //para cada formula
      foreach($definida as $d => $valor_formula){
        //buscar las variables de la formula
        $variables_formula=self::formula_tokens($valor_formula);
        //si no existe variables en la formula, incrementar x
        if(count($variables_formula)==0)
          $x++;
        //para cada variable de la formula
        for($i=0;$i<count($variables_formula);$i++){
          //si no existe la variable, cerrar por variable indefinida
          if(!isset($definida[$variables_formula[$i]])){
            //buscar si existe en el listado de conceptos
            $definida[$variables_formula[$i]]="0.00";
            $definida_contar[$variables_formula[$i]]=0;
            //$retorno["msj"]= "\"".$variables_formula[0][$i]."\" ES INDEFINIDA.";
            //$sw=false;
            //break;
            }
          //buscar si existe $d en $definida[$d]
          //si existe cerrar por redundancia ciclica
          if(substr_count($definida[$d],$d)>0){
            $retorno["msj"]= "!Redundancia ciclida ".$d."";
            $sw=false;
            break;
            }
          $definida[$d]=str_replace($variables_formula[$i],"nf(".trim($definida[$variables_formula[$i]],"'").")",$definida[$d]);
          }

        $definida_contar[$d]++;
        if($definida_contar[$d]>50)
          $sw=false;
        }
      }
    if(!$retorno["msj"])
      $retorno["msj"]="OK";

      foreach($definida as $d => $valor_formula){
        if($retorno["msj"]=="OK"){
          //print($valor_formula."|");
          //@eval("\$tmp=($valor_formula);");
          $tmp=@eval("return $valor_formula;");
          if($tmp===false){
            $tmp=0;
            $retorno["msj"]="!Error al evaluar [$d]=$valor_formula";
          }

          $retorno["retorno"][$d]=number_format($tmp,2,'.','');
        }
        else
          $retorno["retorno"][$d]='';
      }
    //print_r($retorno);
    return $retorno;
  }


  public static function formula_resolver_original($definida){
    $retorno["msj"]="";
    $x=0;
    $sw=true;
    $definida_contar=array();
    foreach($definida as $d => $valor_formula)
      $definida_contar[$d]=0;

    while($sw){
      //si x es igual a la cantidad de variables
      //salir, pq ya no hay nada que reemplazar en las formulas
      if($x==count($definida)) break;
      $x=0;

      //para cada formula
      foreach($definida as $d => $valor_formula){
        //buscar las variables de la formula
        $variables_formula=self::formula_tokens($valor_formula);
        //si no existe variables en la formula, incrementar x
        if(count($variables_formula)==0)
          $x++;
        //para cada variable de la formula
        for($i=0;$i<count($variables_formula);$i++){
          //si no existe la variable, cerrar por variable indefinida
          if(!isset($definida[$variables_formula[$i]])){
            //buscar si existe en el listado de conceptos
            $definida[$variables_formula[$i]]="0.00";
            $definida_contar[$variables_formula[$i]]=0;
            //$retorno["msj"]= "\"".$variables_formula[0][$i]."\" ES INDEFINIDA.";
            //$sw=false;
            //break;
            }
          //buscar si existe $d en $definida[$d]
          //si existe cerrar por redundancia ciclica
          if(substr_count($definida[$d],$d)>0){
            $retorno["msj"]= "Redundancia ciclida ".$d."";
            $sw=false;
            break;
            }
          $definida[$d]=str_replace($variables_formula[$i],"(".trim($definida[$variables_formula[$i]],"'").")",$definida[$d]);
          }

        $definida_contar[$d]++;
        if($definida_contar[$d]>50)
          $sw=false;
        }
      }
    if(!$retorno["msj"])
      $retorno["msj"]="OK";

      foreach($definida as $d => $valor_formula){
        if($retorno["msj"]=="OK"){
          //print($valor_formula."|");
          eval("\$tmp=($valor_formula);");
          $retorno["retorno"][$d]=number_format($tmp,2,'.','');
          }
        else
          $retorno["retorno"][$d]='';
      }
    //print_r($retorno);
    return $retorno;
  }

  public static function constante_sistema($fecha_inicio,$fecha_culminacion){
    //NUMERO_LUNES | N_LUNES | NUM_LUNES
    $lunes_periodo=self::contar_lunes($fecha_inicio,$fecha_culminacion);
    //LUNES_MES
    include_once(SIGA::path()."/library/functions/dias_meses.php");
    $fecha=explode("-",$fecha_inicio);
    $lunes_mes=self::contar_lunes($fecha[0]."-".$fecha[1]."-01",$fecha[0]."-".$fecha[1]."-".dias_meses($fecha[0])[$fecha[1]*1-1]);
    $definida=array();
    $definida["NUM_LUNES_PERIODO"]=$definida["N_LUNES_PERIODO"]=$definida["NUMERO_LUNES_PERIODO"]="$lunes_periodo";
    $definida["NUM_LUNES_MES"]=$definida["N_LUNES_MES"]=$definida["NUMERO_LUNES_MES"]="$lunes_mes";
    $definida["NUM_DIAS_PERIODO"]=abs(floor((strtotime($fecha_culminacion)-strtotime($fecha_inicio))/(60*60*24))+1);
    return $definida;
  }

  public static function ficha_concepto($id_nomina,$id_periodo,$id_ficha){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];
    //AGREGAS LAS DEFINICIONES DEL SISTEMA (NUMERO_LUNES)
    $definida=array();
    $definida=self::constante_sistema($fecha_inicio,$fecha_culminacion);

    //buscar antiguedad de la persona
    include_once("ficha.class.php");
    $ficha_antiguedad=ficha::onGet_Antiguedad($id_ficha,$fecha_culminacion);
    $definida["ANTIGUEDAD"]=$ficha_antiguedad["antiguedad_anio"];

    //buscar conceptos ficha
    $ficha_concepto=$db->Execute("SELECT *
                                  FROM modulo_nomina.ficha_concepto as FC, modulo_nomina.concepto AS C, modulo_nomina.concepto_formula as CF
                                  WHERE
                                    C.id=FC.id_concepto and
                                    C.id=CF.id_concepto AND
                                    FC.id_nomina=$id_nomina AND
                                    FC.id_periodo=$id_periodo AND
                                    FC.id_ficha=$id_ficha AND
                                    CF.fecha = (SELECT fecha FROM modulo_nomina.concepto_formula WHERE fecha<='$fecha_culminacion' AND id_concepto=C.id ORDER BY fecha DESC LIMIT 1)
                                  ORDER BY C.orden, C.codigo");




    for($i=0;$i<count($ficha_concepto);$i++){
      $definida[$ficha_concepto[$i]["identificador"]]=self::es_formula($ficha_concepto[$i]["definicion"])?$ficha_concepto[$i]["definicion"]:number_format($ficha_concepto[$i]["valor"],2,'.','');
      //si es aporte patronal, agregar la formula al listado para resolverla
      if($ficha_concepto[$i]["tipo"]=="AP"){
        $ficha_concepto[$i]["identificador_ap"]=$ficha_concepto[$i]["identificador"]."_AP";
        $definida[$ficha_concepto[$i]["identificador_ap"]]=self::es_formula($ficha_concepto[$i]["definicion_ap"])?$ficha_concepto[$i]["definicion_ap"]:number_format($ficha_concepto[$i]["valor"],2,'.','');
      }
    }
    $definida=self::formula_resolver($definida);

    $retorno["antiguedad_anio_dia"]=$ficha_antiguedad["antiguedad_anio_dia"];
    $retorno["total_asignacion"]=0;
    $retorno["total_deduccion"]=0;
    $retorno["total_ap"]=0;

    for($i=0;$i<count($ficha_concepto);$i++){
      if($definida["msj"]!="OK"){
        $ficha_concepto[$i]["valor_final"]=$definida["msj"];
        continue;
      }
      $ficha_concepto[$i]["valor_final"]=$definida["retorno"][$ficha_concepto[$i]["identificador"]];
      $ficha_concepto[$i]["valor_final_msj"]=$definida["msj"];

      switch($ficha_concepto[$i]["tipo"]){
        case "A":
        case "RD":
          $retorno["total_asignacion"]+=$ficha_concepto[$i]["valor_final"];
          break;
        case "AP":
          //si es aporte patronal, retornar el valor en 'valor_final_ap'
          $ficha_concepto[$i]["valor_final_ap"]=$definida["retorno"][$ficha_concepto[$i]["identificador_ap"]];
          $retorno["total_ap"]+=$ficha_concepto[$i]["valor_final_ap"];
        case "D":
        case "RA":
          $retorno["total_deduccion"]+=$ficha_concepto[$i]["valor_final"];
          break;
      }
    }
    $retorno["concepto"]=$ficha_concepto;
    $retorno["total_neto"]=$retorno["total_asignacion"]-$retorno["total_deduccion"];

    return $retorno;
  }

  public static function fichas($id_nomina,$id_periodo,$filtro_busqueda=NULL){
    return self::onListFichaPeriodo($id_nomina,$id_periodo,0,"ALL",$filtro_busqueda)["result"];


    /*

    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];

    $ficha=$db->Execute("SELECT
                            F.id,
                            F.id as id_ficha,
                            P.identificacion_tipo as nacionalidad,
                            P.identificacion_numero as cedula,
                            split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
                            replace(P.denominacion,';',' ') as nombres_apellidos
                          FROM
                            modulo_nomina.ficha AS F,
                            modulo_base.persona as P
                          WHERE
                            F.id_persona=P.id AND
                            F.id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)

                          ");

    for($i=0;$i<count($ficha);$i++){
      $id_ficha=$ficha[$i]["id"];
      //BUSCAR CARGO DE LA FICHA PARA EL PERIODO ACTUAL
      $ficha_cargo=$db->Execute("SELECT
                                    cargo,
                                    denominacion as cargo_denominacion,
                                    orden
                                  FROM modulo_nomina.cargo
                                  WHERE id = ( select id_cargo
                                               from modulo_nomina.ficha_cargo
                                               where id_ficha=$id_ficha and fecha <= '$fecha_culminacion'
                                               order by fecha desc
                                               limit 1)");

      if(isset($ficha_cargo[0]))
        $ficha_cargo=array("cargo"=>$ficha_cargo[0]["cargo"],"cargo_denominacion"=>$ficha_cargo[0]["cargo_denominacion"],"orden"=>(int)$ficha_cargo[0]["orden"]);
      else
        $ficha_cargo=array("cargo"=>"","cargo_denominacion"=>"","orden"=>0);

      $ficha[$i]+=$ficha_cargo;
      //FIN BUSCAR CARGO

      $ficha_concepto=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
      $ficha[$i]+=$ficha_concepto;
    }
    usort($ficha, array(__CLASS__,'ficha_ordenar'));
    return $ficha;*/
  }

  public static function onGet($access,$id_nomina,$id_periodo){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion, cerrado, contabilizado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];
    //print_r($periodo);
    $return=array();
    $return["concepto"]=$db->Execute("SELECT
                                          *
                                        FROM
                                          modulo_nomina.concepto AS C,
                                          modulo_nomina.concepto_formula as CF,
                                          modulo_nomina.concepto_periodo as CP
                                        WHERE
                                          C.id=CF.id_concepto AND
                                          CF.fecha = (SELECT fecha
                                                      FROM modulo_nomina.concepto_formula
                                                      WHERE fecha<='$fecha_culminacion' AND id_concepto=C.id
                                                      ORDER BY fecha DESC
                                                      LIMIT 1) AND
                                          C.id=CP.id_concepto AND
                                          CP.id_periodo=$id_periodo AND CP.id_nomina=$id_nomina
                                        ORDER BY C.orden, C.codigo");
    //print_r($return["concepto"]);
    $k=0;
    $conceptos_identificador=array();
    $conceptos_identificador_sistema=self::constante_sistema($fecha_inicio,$fecha_culminacion);

    foreach($conceptos_identificador_sistema as $d => $valor_formula){
      $conceptos_identificador[$k]=$d;
      $k++;
      }

    for($i=0;$i<count($return["concepto"]);$i++,$k++)
      $conceptos_identificador[$k]=$return["concepto"][$i]["identificador"];

    for($i=0;$i<count($return["concepto"]);$i++){
      $return["concepto"][$i]["es_formula"]=self::es_formula($return["concepto"][$i]["definicion"]);
      $return["concepto"][$i]["indefinido"]=self::formula_tokens_indefinidos($conceptos_identificador,$return["concepto"][$i]["definicion"]);
    }

    $return["ficha"]=self::fichas($id_nomina,$id_periodo);
    $return["cerrado"]=$periodo[0]["cerrado"];
    $return["contabilizado"]=$periodo[0]["contabilizado"];
    return $return;
  }

  public static function onSave($access,$id_nomina,$id_periodo,$data){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      exit;
    }

    $return=array();
    for($i=0;$i<count($data);$i++){
      $id_ficha=$data[$i]["id_ficha"];
      $id_concepto=$data[$i]["id_concepto"];
      $valor=$data[$i]["valor"];
      //borrar registros existentes
      $db->Delete("modulo_nomina.ficha_concepto","id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");

      if(trim($valor)!='')
      if(is_numeric($valor))
        $db->Insert("modulo_nomina.ficha_concepto",array(
                                         "id_nomina"=>"$id_nomina",
                                         "id_periodo"=>"$id_periodo",
                                         "id_ficha"=>"$id_ficha",
                                         "id_concepto"=>"$id_concepto",
                                         "valor"=>"'$valor'"));
      $return[$i]["id_ficha"]=$id_ficha;
      $return[$i]+=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
    }
    return $return;
  }

  public static function onAdd($access,$id_nomina,$id_periodo,$ids_ficha,$id_concepto){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_culminacion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      exit;
    }
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];

    //buscar el concepto a agregar
    $concepto=$db->Execute("SELECT
                              definicion
                            FROM
                              modulo_nomina.concepto AS C,
                              modulo_nomina.concepto_formula as CF
                            WHERE
                              C.id=$id_concepto AND
                              C.id=CF.id_concepto AND
                              CF.fecha = (SELECT fecha
                                          FROM modulo_nomina.concepto_formula
                                          WHERE fecha<='$fecha_culminacion' AND id_concepto=C.id
                                          ORDER BY fecha DESC
                                          LIMIT 1)");

    $valor=$concepto[0]["definicion"];
    if(self::es_formula($valor)) $valor="0";

    if(count($ids_ficha)===1 and $ids_ficha[0]==='*'){
      $F=$db->Execute("SELECT id
                    FROM modulo_nomina.ficha
                    WHERE id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)");
      $ids_ficha=array();
      for($i=0;$i<count($F);$i++)
        $ids_ficha[$i]=$F[$i]["id"];
    }

    $return=array();
    for($i=0;$i<count($ids_ficha);$i++){
      $id_ficha=$ids_ficha[$i];
      //si es agregar a todos, verificar si existe valor del concepto para la ficha, si existe valor saltar.
      $valor_actual=$db->Execute("SELECT valor FROM modulo_nomina.ficha_concepto WHERE id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");
      if(isset($valor_actual[0]["valor"]))
        if($valor_actual[0]["valor"]>0) continue;

      //borrar registros existentes
      $db->Delete("modulo_nomina.ficha_concepto","id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");

      $db->Insert("modulo_nomina.ficha_concepto",array(
                                        "id_nomina"=>"$id_nomina",
                                        "id_periodo"=>"$id_periodo",
                                        "id_ficha"=>"$id_ficha",
                                        "id_concepto"=>"$id_concepto",
                                         "valor"=>"$valor"));

      $return[$i]["id_ficha"]=$id_ficha;
      $return[$i]+=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
    }
    return $return;
  }

  public static function onAddEscala($access,$id_nomina,$id_periodo,$ids_ficha,$id_concepto){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_culminacion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      exit;
    }


    $concepto=$db->Execute("SELECT
                              identificador
                            FROM
                              modulo_nomina.concepto
                            WHERE
                              id=$id_concepto");
    if(!isset($concepto[0]["identificador"])) return [];

    $config=$db->Execute("SELECT
                              definicion
                            FROM
                              modulo_nomina.escala_salarial_configuracion
                            WHERE
                              campo ilike 'sueldo_basico'");
    if(!isset($config[0]["definicion"])) return ["b"];
    $config=explode(",", $config[0]["definicion"]);

    if(!in_array($concepto[0]["identificador"],$config)) return ["c"];


    if(count($ids_ficha)===1 and $ids_ficha[0]==='*'){
      $F=$db->Execute("SELECT id
                    FROM modulo_nomina.ficha
                    WHERE id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)");
      $ids_ficha=array();
      for($i=0;$i<count($F);$i++)
        $ids_ficha[$i]=$F[$i]["id"];
    }

    $return=array();
    for($i=0;$i<count($ids_ficha);$i++){
      $id_ficha=$ids_ficha[$i];
      //buscar el valor de la escala para la persona
      $escala=$db->Execute("SELECT sueldo_basico from modulo_nomina.ficha F, modulo_nomina.escala_salarial ES where F.id='$id_ficha' and F.id_escala_salarial=ES.id");
      $valor=0;
      if(isset($escala[0]["sueldo_basico"]))
        if(is_numeric($escala[0]["sueldo_basico"]))
          $valor=$escala[0]["sueldo_basico"];


      //borrar registros existentes
      $db->Delete("modulo_nomina.ficha_concepto","id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");

      $db->Insert("modulo_nomina.ficha_concepto",array(
                                        "id_nomina"=>"$id_nomina",
                                        "id_periodo"=>"$id_periodo",
                                        "id_ficha"=>"$id_ficha",
                                        "id_concepto"=>"$id_concepto",
                                         "valor"=>"$valor"));

      $return[$i]["id_ficha"]=$id_ficha;
      $return[$i]+=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
    }
    return $return;
  }

  public static function onAddValorFicha($access,$id_nomina,$id_periodo,$ids_ficha,$id_concepto){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_culminacion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      exit;
    }

    //buscar el identificar del concepto
    $concepto=$db->Execute("SELECT
                              identificador
                            FROM
                              modulo_nomina.concepto
                            WHERE
                              id=$id_concepto");
    if(!isset($concepto[0]["identificador"])) return [];

    //buscar la configuracion de los conceptos ficha:%
    $config=$db->Execute("SELECT
                              dato,
                              valor
                            FROM
                              modulo_nomina.nomina_configuracion
                            WHERE
                              dato ilike 'ficha:%'");

    //buscar para cual caso aplica el concepto_identificar -> ficha


    if(!isset($config[0]["definicion"])) return ["b"];
    $config=explode(",", $config[0]["definicion"]);

    if(!in_array($concepto[0]["identificador"],$config)) return ["c"];


    if(count($ids_ficha)===1 and $ids_ficha[0]==='*'){
      $F=$db->Execute("SELECT id
                    FROM modulo_nomina.ficha
                    WHERE id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)");
      $ids_ficha=array();
      for($i=0;$i<count($F);$i++)
        $ids_ficha[$i]=$F[$i]["id"];
    }

    $return=array();
    for($i=0;$i<count($ids_ficha);$i++){
      $id_ficha=$ids_ficha[$i];
      //buscar el valor de la escala para la persona
      $escala=$db->Execute("SELECT sueldo_basico from modulo_nomina.ficha F, modulo_nomina.escala_salarial ES where F.id='$id_ficha' and F.id_escala_salarial=ES.id");
      $valor=0;
      if(isset($escala[0]["sueldo_basico"]))
        if(is_numeric($escala[0]["sueldo_basico"]))
          $valor=$escala[0]["sueldo_basico"];


      //borrar registros existentes
      $db->Delete("modulo_nomina.ficha_concepto","id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");

      $db->Insert("modulo_nomina.ficha_concepto",array(
                                        "id_nomina"=>"$id_nomina",
                                        "id_periodo"=>"$id_periodo",
                                        "id_ficha"=>"$id_ficha",
                                        "id_concepto"=>"$id_concepto",
                                         "valor"=>"$valor"));

      $return[$i]["id_ficha"]=$id_ficha;
      $return[$i]+=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
    }
    return $return;
  }

  public static function onRemove($access,$id_nomina,$id_periodo,$ids_ficha,$id_concepto){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      exit;
    }

    if(count($ids_ficha)===1 and $ids_ficha[0]==='*'){
      $F=$db->Execute("SELECT id
                        FROM modulo_nomina.ficha
                        WHERE id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)");
      $ids_ficha=array();
      for($i=0;$i<count($F);$i++)
        $ids_ficha[$i]=$F[$i]["id"];
    }

    $return=array();
    for($i=0;$i<count($ids_ficha);$i++){
      $id_ficha=$ids_ficha[$i];
      $db->Delete("modulo_nomina.ficha_concepto","id_nomina=$id_nomina and id_periodo=$id_periodo and id_ficha=$id_ficha and id_concepto=$id_concepto");
      $return[$i]["id_ficha"]=$id_ficha;
      $return[$i]+=self::ficha_concepto($id_nomina,$id_periodo,$id_ficha);
    }
    return $return;
  }


  public static function onClose($access,$id_periodo,$extra=""){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para cerrar el período.");
      exit;
    }

    $db=SIGA::DBController();

    //buscar periodo actual y calcular el periodo siguiente correspondiente
    $periodo=$db->Execute("SELECT codigo, tipo, fecha_inicio, fecha_culminacion, descripcion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");

    if($periodo[0]["cerrado"]==='t'){
      return array("success"=>false, "message"=>"El período se encuentra cerrado.");
      exit;
    }
    $n_dias=NULL;
    $tipo=$periodo[0]['tipo'];

    switch($tipo){
      case "Q"://quincenal
        $fecha=explode("-",$periodo[0]['fecha_inicio']);
        $codigo=str_pad(intval($periodo[0]['codigo'])+1, 3, "0", STR_PAD_LEFT);
        $tipo="Q";

        $n_dias=abs(floor((strtotime($periodo[0]['fecha_culminacion'])-strtotime($periodo[0]['fecha_inicio']))/(60*60*24))+1);
        if($n_dias==7){
          $fecha_i=date("Y-m-d",strtotime($periodo[0]['fecha_culminacion'])+60*60*24);
          $fecha_c=date("Y-m-d",strtotime($periodo[0]['fecha_culminacion'])+60*60*24*7);
        }
        else{
          if($fecha[2]=="01"){
            $dia="16";
            $mes=$fecha[1];
            $anio=$fecha[0];
            $dm=dias_meses($anio);
            $fecha_i="$anio-$mes-$dia";
            $fecha_c="$anio-$mes-".$dm[intval($mes)-1];
          }
          else if($fecha[2]=="16"){
            $dia="01";
            $mes=str_pad(intval($fecha[1])+1, 2, "0", STR_PAD_LEFT);
            $anio=$fecha[0];
            if($mes=="13"){//si el mes siguientes es superior a 12 (diciembre), significa q viene enero
              $codigo="001";
              $dia="01";
              $mes="01";
              $anio=intval($fecha[0])+1;
            }
            $fecha_i="$anio-$mes-$dia";
            $fecha_c="$anio-$mes-15";
          }
          else{
            //error, no debería cumplirse
            return array("success"=>false, "message"=>"Error en los parametros para la creación del siguiente periodo. Tipo: 'Q'. ");
            exit;
          }

        }

        break;
      case "M"://mensual
        $fecha=explode("-",$periodo[0]['fecha_inicio']);
        $codigo=str_pad(intval($periodo[0]['codigo'])+1, 3, "0", STR_PAD_LEFT);
        $tipo="M";

        $dia="01";
        $mes=intval($fecha[1])+1;
        $anio=intval($fecha[0]);
        if($mes>=12){
          $mes=1;
          $anio++;
        }
        $mes=str_pad($mes, 2, "0", STR_PAD_LEFT);
        $dm=dias_meses($anio);
        $fecha_i="$anio-$mes-$dia";
        $fecha_c="$anio-$mes-".$dm[intval($mes)-1];
        break;
      case "S"://semestral
        $fecha=explode("-",$periodo[0]['fecha_inicio']);
        $tipo="S";
        if($fecha[1]=="01"){//si es el 1er semestre
          $codigo="002";
          $anio=$fecha[0];
          $fecha_i="$anio-07-01";
          $fecha_c="$anio-12-31";
        }
        else if($fecha[1]=="07"){//si es el 2do semestre
          $codigo="001";
          $anio=intval($fecha[0])+1;
          $fecha_i="$anio-01-01";
          $fecha_c="$anio-06-30";
        }
        else{
          //error, no debería cumplirse
          return array("success"=>false, "message"=>"Error en los parametros para la creación del siguiente periodo. Tipo: 'S'. ");
          exit;
        }
        break;
      default:
        //leer estos valores de la interfaz
        $codigo="002";
        $fecha_i="2015-11-16";
        $fecha_c="2015-11-30";

        return array("success"=>false, "message"=>"Error en los parametros para la creación del siguiente periodo. Tipo: '".$periodo[0]['tipo']."'. ");
        exit;
    }



    //verificar si el periodo a insertar no existe
    $existe=$db->Execute("SELECT count(*) modulo_nomina.periodo WHERE codigo='$codigo' AND tipo='$tipo' AND fecha_inicio='$fecha_i' AND fecha_culminacion='$fecha_c'");
    if(isset($existe[0][0])){
      return array("success"=>false, "message"=>"No se puedo cerrar, el périodo $codigo ya existe. ");
      exit;
    }

    include_once(SIGA::path()."/library/functions/letra_mes.php");
    if($n_dias==7){
      $fecha=explode("-",$fecha_i);//2018-10-01
      $n=intval(($fecha[2]*1/7)+1);
      $mes=strtoupper(letra_mes($fecha[1]));
      $descripcion="'$mes ".$fecha[0]." - SEMANA #$n'";
    }
    else{
      //ingresar la descripcion
      $array_descripcion=array( "ENERO $anio - QUINCENA #1",       "ENERO $anio - QUINCENA #2",
                                "FEBRERO $anio - QUINCENA #1",     "FEBRERO $anio - QUINCENA #2",
                                "MARZO $anio - QUINCENA #1",       "MARZO $anio - QUINCENA #2",
                                "ABRIL $anio - QUINCENA #1",       "ABRIL $anio - QUINCENA #2",
                                "MAYO $anio - QUINCENA #1",        "MAYO $anio - QUINCENA #2",
                                "JUNIO $anio - QUINCENA #1",       "JUNIO $anio - QUINCENA #2",
                                "JULIO $anio - QUINCENA #1",       "JULIO $anio - QUINCENA #2",
                                "AGOSTO $anio - QUINCENA #1",      "AGOSTO $anio - QUINCENA #2",
                                "SEPTIEMBRE $anio - QUINCENA #1",  "SEPTIEMBRE $anio - QUINCENA #2",
                                "OCTUBRE $anio - QUINCENA #1",     "OCTUBRE $anio - QUINCENA #2",
                                "NOVIEMBRE $anio - QUINCENA #1",   "NOVIEMBRE $anio - QUINCENA #2",
                                "DICIEMBRE $anio - QUINCENA #1",   "DICIEMBRE $anio - QUINCENA #2",
                                "ENERO ".($anio+1)." - QUINCENA #1",
                                //PARA PERIODO MENSUALES DE LA CESTATICKET SOCIALISTA
                                "ENERO $anio - CESTATICKET SOCIALISTA",
                                "FEBRERO $anio - CESTATICKET SOCIALISTA",
                                "MARZO $anio - CESTATICKET SOCIALISTA",
                                "ABRIL $anio - CESTATICKET SOCIALISTA",
                                "MAYO $anio - CESTATICKET SOCIALISTA",
                                "JUNIO $anio - CESTATICKET SOCIALISTA",
                                "JULIO $anio - CESTATICKET SOCIALISTA",
                                "AGOSTO $anio - CESTATICKET SOCIALISTA",
                                "SEPTIEMBRE $anio - CESTATICKET SOCIALISTA",
                                "OCTUBRE $anio - CESTATICKET SOCIALISTA",
                                "NOVIEMBRE $anio - CESTATICKET SOCIALISTA",
                                "DICIEMBRE $anio - CESTATICKET SOCIALISTA",
                                "ENERO ".($anio+1)." - CESTATICKET SOCIALISTA"
                                );

      $descripcion="null";
      for($d=0;$d<count($array_descripcion)-1;$d++){
        if($array_descripcion[$d]==$periodo[0]['descripcion']){
          $descripcion="'".$array_descripcion[$d+1]."'";
          break;
        }
      }

    }

    /*print_r( $array_descripcion);

    print $descripcion;

    exit;*/
    $db->Execute("BEGIN WORK");
    //Insertar registro (periodo nuevo)
    $result=$db->Execute("INSERT INTO modulo_nomina.periodo(codigo,tipo,fecha_inicio,fecha_culminacion,descripcion)
                         VALUES('$codigo','$tipo','$fecha_i','$fecha_c',$descripcion) RETURNING id");

    //Si hay error al insertar
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    //buscar el id de registro recien ingresado
    if(!isset($result[0][0])){
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"Error al obtener el identificador del comprobante.");
    }
    $id_periodo_nuevo=$result[0][0];

    //buscar información del periodo actual, para copiar al nuevo periodo
    //buscar los conceptos para cada ficha en el periodo actual
    $ficha_concepto=$db->Execute("SELECT id_nomina, id_ficha, id_concepto, valor FROM modulo_nomina.ficha_concepto WHERE id_periodo=$id_periodo");
    if(!$ficha_concepto){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    for($i=0;$i<count($ficha_concepto);$i++){
      $result=$db->Insert("modulo_nomina.ficha_concepto",
                              array (
                                    "id_nomina"=>$ficha_concepto[$i]["id_nomina"],
                                    "id_periodo"=>"$id_periodo_nuevo",
                                    "id_ficha"=>$ficha_concepto[$i]["id_ficha"],
                                    "id_concepto"=>$ficha_concepto[$i]["id_concepto"],
                                    "valor"=>$ficha_concepto[$i]["valor"]
                                    ));

      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
    }

    $concepto_periodo=$db->Execute("SELECT id_nomina, id_concepto FROM modulo_nomina.concepto_periodo WHERE id_periodo=$id_periodo");
    if(!$concepto_periodo){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    for($i=0;$i<count($concepto_periodo);$i++){
      $result=$db->Insert("modulo_nomina.concepto_periodo",
                              array (
                                    "id_nomina"=>$concepto_periodo[$i]["id_nomina"],
                                    "id_periodo"=>"$id_periodo_nuevo",
                                    "id_concepto"=>$concepto_periodo[$i]["id_concepto"]
                                    ));

      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
    }

    //copiar las notas a nuevo periodo
    $periodo_nota=$db->Execute("SELECT id_nomina, nota FROM modulo_nomina.periodo_nota WHERE id_periodo=$id_periodo");
    for($i=0;$i<count($periodo_nota);$i++){
      $result=$db->Insert("modulo_nomina.periodo_nota",
                              array (
                                    "id_nomina"=>$periodo_nota[$i]["id_nomina"],
                                    "id_periodo"=>"$id_periodo_nuevo",
                                    "nota"=>"'".$periodo_nota[$i]["nota"]."'"
                                    ));

      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error al copiar las notas a nuevo periodo.", "messageDB"=>"$mensajeDB");
      }
    }

    //cerrar periodo
    $db->Update("modulo_nomina.periodo",array("cerrado"=>"true"),"id=$id_periodo");


    //$db->Execute("ROLLBACK WORK");
    $db->Execute("COMMIT WORK");
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }

  public static function detalle_presupuestario_contable($periodo,$nomina,$tipo="CCP"){
    $db=SIGA::DBController();
    $detalle=array();
    $cuenta_bancaria=array();

    for($i=0;$i<count($nomina);$i++):
      $nomina[$i]["ficha"]=self::fichas($nomina[$i]["id"],$periodo["id"]);


      if($tipo=="CCP" or $tipo=="P" or $tipo=="P-CXC"){//generar movimeinto bancario
        //buscar cuenta bancaria y contable de la nómina
        if($tipo=="P-CXC"){
          $cta_cxc=self::cuenta_contable_cxc();
          if(!isset($detalle[$cta_cxc]))
            $detalle[$cta_cxc]=array("D"=>0,"H"=>0);
        }
        else{
          $nomina[$i]["banco_cuenta"]=$db->Execute("SELECT
                                                      BC.id,
                                                      BC.id_cuenta_contable,
                                                      BC.numero_cuenta
                                                    FROM
                                                      modulo_nomina.nomina_banco_cuenta AS NBC,
                                                      modulo_base.banco_cuenta as BC
                                                    WHERE
                                                      NBC.id_nomina=".$nomina[$i]["id"]." AND NBC.id_banco_cuenta=BC.id");

          if(isset($nomina[$i]["banco_cuenta"][0])){
            $nomina[$i]["banco_cuenta"]=$nomina[$i]["banco_cuenta"][0];
            if(!isset($nomina[$i]["banco_cuenta"]["id_cuenta_contable"])){
              return array("success"=>false, "message"=> "La nomina id: ".$nomina[$i]["id"].", no tiene cuenta bancaria asociada. ");
            }
            if(!isset($detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]])){
              $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
              $cuenta_bancaria[$nomina[$i]["banco_cuenta"]["id"]."|".$nomina[$i]["banco_cuenta"]["numero_cuenta"]]=0;
            }
          }
        }
      }
      else{//if($tipo=="CC")
        $nomina[$i]["banco_cuenta"]=array();
        $nomina[$i]["banco_cuenta"]["id_cuenta_contable"]=self::cuenta_contable_abono();//SUELDOS, SALARIOS Y OTRAS REMUNERACIONES POR PAGAR
        if(!isset($detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]))
          $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
      }


      //buscar los concepto existentes para cada ficha
      for($j=0;$j<count($nomina[$i]["ficha"]);$j++):
        //accion del proyecto por el cual esta la persona
        $ficha_ep=$db->Execute("SELECT id_accion_subespecifica
                                    FROM modulo_nomina.ficha_estructura_presupuestaria
                                    WHERE
                                      id_ficha=".$nomina[$i]["ficha"][$j]["id"]." AND
                                      fecha<='".$periodo["fecha_culminacion"]."'
                                    ORDER BY fecha
                                    DESC LIMIT 1");
        if(!isset($ficha_ep[0])){
          return array("success"=>false, "message"=> "La ficha 'id_ficha=".$nomina[$i]["ficha"][$j]["id"]."' no tiene estructura presupuestaria.");
        }
        $id_accion_subespecifica=$ficha_ep[0]["id_accion_subespecifica"];


        if($tipo=="CCP" or $tipo=="P" or $tipo=="P-CXC"){
          //sumar el neto
          if($tipo=="P-CXC"){
            $cta_cxc=self::cuenta_contable_cxc();
            $detalle[$cta_cxc]["H"]+=$nomina[$i]["ficha"][$j]["total_neto"];
          }
          else{
            $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]["H"]+=$nomina[$i]["ficha"][$j]["total_neto"];
            $cuenta_bancaria[$nomina[$i]["banco_cuenta"]["id"]."|".$nomina[$i]["banco_cuenta"]["numero_cuenta"]]+=$nomina[$i]["ficha"][$j]["total_neto"];
          }
        }
        else{
          //sumar total asignaciones
          $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]["H"]+=$nomina[$i]["ficha"][$j]["total_asignacion"]+$nomina[$i]["ficha"][$j]["total_ap"];
        }

        $ficha_concepto=$nomina[$i]["ficha"][$j]["concepto"];
        for($k=0;$k<count($ficha_concepto);$k++):
          if(!($ficha_concepto[$k]["tipo"]=="" or $ficha_concepto[$k]["tipo"]=="_")){
            //buscar si existe en el arreglo $concepto

            $tmp=$db->Execute("SELECT id_cuenta_presupuestaria, id_cuenta_presupuestaria_ap, id_cuenta_contable, id_cuenta_contable_ap
                                                        FROM modulo_nomina.concepto_presupuesto_contabilidad
                                                        WHERE
                                                          id_nomina=".$nomina[$i]["id"]." AND
                                                          id_concepto=".$ficha_concepto[$k]["id"]." AND
                                                          fecha<='".$periodo["fecha_culminacion"]."'
                                                        ORDER BY fecha
                                                        DESC LIMIT 1");
            if(!isset($tmp[0])){
              return array("success"=>false, "message"=> "Concepto indefinido: ".$ficha_concepto[$k]['concepto'].".\nTabla: modulo_nomina.concepto_presupuesto_contabilidad\n id_nomina: ".$nomina[$i]["id"]."\n id_concepto: ".$ficha_concepto[$k]["id"]."");
            }
            $tmp=$tmp[0];

            if(!isset($detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]) and $tmp["id_cuenta_presupuestaria"]!="") $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]=0;
            if(!isset($detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]) and $tmp["id_cuenta_presupuestaria_ap"]!="") $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]=0;
            if(!isset($detalle[$tmp["id_cuenta_contable"]]) and $tmp["id_cuenta_contable"]!="") $detalle[$tmp["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
            if(!isset($detalle[$tmp["id_cuenta_contable_ap"]]) and $tmp["id_cuenta_contable_ap"]!="") $detalle[$tmp["id_cuenta_contable_ap"]]=array("D"=>0,"H"=>0);


            switch($ficha_concepto[$k]["tipo"]){
              case "A":
                if($tmp["id_cuenta_presupuestaria"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]+=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA PARA LA ASIGNACIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "RA":
                if($tmp["id_cuenta_presupuestaria"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]-=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA PARA EL REINTEGRO DE ASIGNACIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "AP":
                if($tmp["id_cuenta_presupuestaria_ap"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]+=$ficha_concepto[$k]["valor_final_ap"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA (PATRON) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");

                if($tipo=="CCP" or $tipo=="P" or $tipo=="P-CXC"){
                  if($tmp["id_cuenta_contable"])
                    $detalle[$tmp["id_cuenta_contable"]]["H"]+=$ficha_concepto[$k]["valor_final"];
                  else
                    return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE (TRABAJADOR) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");

                  if($tmp["id_cuenta_contable_ap"])
                    $detalle[$tmp["id_cuenta_contable_ap"]]["H"]+=$ficha_concepto[$k]["valor_final_ap"];
                  else
                    return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE (PATRON) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                }
                break;

              case "D":
                if($tipo=="CCP" or $tipo=="P" or $tipo=="P-CXC"){
                  if($tmp["id_cuenta_contable"])
                    $detalle[$tmp["id_cuenta_contable"]]["H"]+=$ficha_concepto[$k]["valor_final"];
                  else
                    return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE PARA LA DEDUCCIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                }
                break;
              case "RD":
                if($tipo=="CCP" or $tipo=="P" or $tipo=="P-CXC"){
                  if($tmp["id_cuenta_contable"])
                    $detalle[$tmp["id_cuenta_contable"]]["D"]+=$ficha_concepto[$k]["valor_final"];
                  else
                    return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE PARA EL REINTEGRO DE DEDUCCIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                }
                break;
            }
          }
        endfor;
      endfor;
    endfor;

    if($tipo=="CCP" or $tipo=="P")
      if(count($cuenta_bancaria)!=1)
        return array("success"=>false, "message"=> "Error. Es necesario asociar una cuenta bancaria, consulte al administrador del sistema.");


    function ordenar_detalle_presupuestario_contable($a,$b){
      $a=explode("|",$a);
      $b=explode("|",$b);
      if(count($a)==2 and count($b)==2)//detalle presupuestario
        return strcmp($a[1],$b[1]);
      if(count($a)==1 and count($b)==1)//detalle contable
        return strcmp($a[0],$b[0]);
      if(count($a)==1)
        return 1;
      return -1;
    }

    uksort($detalle,"ordenar_detalle_presupuestario_contable");

    $retorno=array();

    switch($tipo){
      case "CCP": $tipo_describe="COMPROMETIDO/CAUSADO/PAGADO";break;
      case "CC":  $tipo_describe="COMPROMETIDO/CAUSADO";break;
      case "P":   $tipo_describe="PAGADO";break;
      case "P-CXC":   $tipo_describe="PAGADO CxC";break;
    }
    $retorno["concepto"]="";
    for($i=0;$i<count($nomina);$i++)
      $retorno["concepto"].=$nomina[$i]["nomina"].($i<count($nomina)-1?", ":"");
    $retorno["concepto"]="CANCELACIÓN DE LA NOMINA [$tipo_describe]: ".$retorno["concepto"].". CORRESPONDIENTE A: ".$periodo["descripcion"]." (PERIODO ".$periodo["codigo"].", DEL ".formatDate($periodo["fecha_inicio"])." AL ".formatDate($periodo["fecha_culminacion"]).").\n";

    $retorno["detalle"]=array();

    if($tipo=="CCP" or $tipo=="P"){
      $retorno["detalle"]["comprobante_bancario"]=array();
      //buscar id nota de debito
      $id_ND=$db->Execute("select id from modulo_base.banco_movimiento_tipo where codigo='ND'");
      $id_ND=$id_ND[0][0];
      $retorno["detalle"]["comprobante_bancario"]["id_banco_movimiento_tipo"]=$id_ND;
      $retorno["detalle"]["comprobante_bancario"]["operacion"]="ND";
      foreach($cuenta_bancaria as $clave => $valor){
        $clave=explode("|",$clave);
        $retorno["detalle"]["comprobante_bancario"]["id_banco_cuenta"]=$clave[0];
        $retorno["detalle"]["comprobante_bancario"]["numero_cuenta"]=$clave[1];
        $retorno["detalle"]["comprobante_bancario"]["numero"]="0";
        $retorno["detalle"]["comprobante_bancario"]["monto"]=number_format($valor,2,".","");
      }
    }

    //
    $retorno["detalle"]["presupuestario"]=array();
    $p=0;
    foreach ($detalle as $clave => $valor){
      $clave=explode("|",$clave);
      if(count($clave)!=2) continue;
      //$clave = id_accion_subespecifica|id_cuenta_presupuestaria
      $id_accion_subespecifica=$clave[0];
      $id_cuenta_presupuestaria=$clave[1];

      //buscar id_accion_subespecifica|id_cuenta_presupuestaria
      $accpro=$db->Execute("SELECT _formatear_estructura_presupuestaria($id_accion_subespecifica) as estructura_presupuestaria");
      $cuenta=$db->Execute("SELECT
                                CP.denominacion,
                                _formatear_cuenta_presupuestaria(CP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                                C.id_cuenta_contable
                            FROM
                              modulo_base.cuenta_presupuestaria as CP
                              LEFT JOIN modulo_base.convertidor as C ON CP.id_cuenta_presupuestaria = C.id_cuenta_presupuestaria
                            WHERE
                              CP.id_cuenta_presupuestaria='$id_cuenta_presupuestaria'");
      if($tipo=="P"){
        if(!isset($detalle[self::cuenta_contable_abono()]))
          $detalle[self::cuenta_contable_abono()]=array("D"=>0,"H"=>0);
        $detalle[self::cuenta_contable_abono()]["D"]+=$valor;
      }
      else{
        if(isset($cuenta[0]["id_cuenta_contable"])){
          if(!isset($detalle[$cuenta[0]["id_cuenta_contable"]]))
            $detalle[$cuenta[0]["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
          $detalle[$cuenta[0]["id_cuenta_contable"]]["D"]+=$valor;
        }
      }

      $operacion=$tipo;
      if($tipo=="P-CXC")
        $operacion="P";

      $retorno["detalle"]["presupuestario"][$p]=array(
                                                      "id_accion_subespecifica"=>"$id_accion_subespecifica",
                                                      "estructura_presupuestaria"=>$accpro[0]["estructura_presupuestaria"],
                                                      "id_cuenta_presupuestaria"=>"$id_cuenta_presupuestaria",
                                                      "cuenta_presupuestaria"=>$cuenta[0]["cuenta_presupuestaria"],
                                                      "denominacion"=>$cuenta[0]["denominacion"],
                                                      "operacion"=>$operacion,
                                                      "monto"=>number_format($valor,2,".","")
                                                      );
      $p++;
    }

    $retorno["detalle"]["contable"]=array();
    $c=0;
    foreach ($detalle as $clave => $valor){
      $clave=explode("|",$clave);
      if(count($clave)!=1) continue;
      $id_cuenta_contable=$clave[0];
      //para el caso P || P-CxC buscar la cuenta contable 611000000000 y reemplazarla por cuenta_contable_abono()
      if($tipo=="P" || $tipo=="P-CXC"){
        if($id_cuenta_contable=="611000000000"){
          $id_cuenta_contable=self::cuenta_contable_abono();
        }
      }


      $cuenta=$db->Execute("SELECT id_cuenta_contable, denominacion,  _formatear_cuenta_contable(id_cuenta_contable) as cuenta_contable FROM modulo_base.cuenta_contable WHERE id_cuenta_contable='$id_cuenta_contable'");

      if($valor["D"]!=0){
        $retorno["detalle"]["contable"][$c]=array(
                                                  "id_cuenta_contable"=>"$id_cuenta_contable",
                                                  "cuenta_contable"=>$cuenta[0]["cuenta_contable"],
                                                  "denominacion"=>$cuenta[0]["denominacion"],
                                                  "operacion"=>"D",
                                                  "monto"=>number_format($valor["D"],2,".","")
                                                  );
        $c++;
      }

      if($valor["H"]!=0){
        $retorno["detalle"]["contable"][$c]=array(
                                                  "id_cuenta_contable"=>"$id_cuenta_contable",
                                                  "cuenta_contable"=>$cuenta[0]["cuenta_contable"],
                                                  "denominacion"=>$cuenta[0]["denominacion"],
                                                  "operacion"=>"H",
                                                  "monto"=>number_format($valor["H"],2,".","")
                                                  );
        $c++;
      }
    }




    $retorno["success"]=true;

    return $retorno;
  }

  public static function detalle_presupuestario_contable__alcaldia_mejia($periodo,$nomina,$buscar){
  //NULL=TODO, AP=APORTE PATRONAL, !AP=SIN APORTE PATRONAL

    $db=SIGA::DBController();
    $detalle=array();
    $cuenta_bancaria=array();
    if(!($buscar==="AP" or $buscar==="!AP")) $buscar=NULL;

    for($i=0;$i<count($nomina);$i++):
      $nomina[$i]["ficha"]=self::fichas($nomina[$i]["id"],$periodo["id"]);


      //buscar cuenta bancaria y contable de la nómina
      $nomina[$i]["banco_cuenta"]=$db->Execute("SELECT
                                                  BC.id,
                                                  BC.id_cuenta_contable,
                                                  BC.numero_cuenta
                                                FROM
                                                  modulo_nomina.nomina_banco_cuenta AS NBC,
                                                  modulo_base.banco_cuenta as BC
                                                WHERE
                                                  NBC.id_nomina=".$nomina[$i]["id"]." AND NBC.id_banco_cuenta=BC.id");

      if(isset($nomina[$i]["banco_cuenta"][0])){
        $nomina[$i]["banco_cuenta"]=$nomina[$i]["banco_cuenta"][0];
        if(!isset($nomina[$i]["banco_cuenta"]["id_cuenta_contable"])){
          return array("success"=>false, "message"=> "La nomina id: ".$nomina[$i]["id"].", no tiene cuenta bancaria asociada. ");
        }
        if(!isset($detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]])){
          $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
          $cuenta_bancaria[$nomina[$i]["banco_cuenta"]["id"]."|".$nomina[$i]["banco_cuenta"]["numero_cuenta"]]=0;
        }
      }

      //buscar los concepto existentes para cada ficha
      for($j=0;$j<count($nomina[$i]["ficha"]);$j++):
        //accion del proyecto por el cual esta la persona
        $ficha_ep=$db->Execute("SELECT id_accion_subespecifica
                                    FROM modulo_nomina.ficha_estructura_presupuestaria
                                    WHERE
                                      id_ficha=".$nomina[$i]["ficha"][$j]["id"]." AND
                                      fecha<='".$periodo["fecha_culminacion"]."'
                                    ORDER BY fecha
                                    DESC LIMIT 1");
        if(!isset($ficha_ep[0])){
          return array("success"=>false, "message"=> "La ficha 'id_ficha=".$nomina[$i]["ficha"][$j]["id"]."' no tiene estructura presupuestaria.");
        }
        $id_accion_subespecifica=$ficha_ep[0]["id_accion_subespecifica"];
        $id_accion_subespecifica_original=$id_accion_subespecifica;
        //sumar el neto
        if($buscar!=="AP"){
          $detalle[$nomina[$i]["banco_cuenta"]["id_cuenta_contable"]]["H"]+=$nomina[$i]["ficha"][$j]["total_neto"];
          $cuenta_bancaria[$nomina[$i]["banco_cuenta"]["id"]."|".$nomina[$i]["banco_cuenta"]["numero_cuenta"]]+=$nomina[$i]["ficha"][$j]["total_neto"];
        }
        $ficha_concepto=$nomina[$i]["ficha"][$j]["concepto"];
        for($k=0;$k<count($ficha_concepto);$k++):
          if(!($ficha_concepto[$k]["tipo"]=="" or $ficha_concepto[$k]["tipo"]=="_")){
            //buscar si existe en el arreglo $concepto

            $tmp=$db->Execute("SELECT id_cuenta_presupuestaria, id_cuenta_presupuestaria_ap, id_cuenta_contable, id_cuenta_contable_ap
                                                        FROM modulo_nomina.concepto_presupuesto_contabilidad
                                                        WHERE
                                                          id_nomina=".$nomina[$i]["id"]." AND
                                                          id_concepto=".$ficha_concepto[$k]["id"]." AND
                                                          fecha<='".$periodo["fecha_culminacion"]."'
                                                        ORDER BY fecha
                                                        DESC LIMIT 1");
            if(!isset($tmp[0])){
              return array("success"=>false, "message"=> "Concepto indefinido: ".$ficha_concepto[$k]['concepto'].".\nTabla: modulo_nomina.concepto_presupuesto_contabilidad\n id_nomina: ".$nomina[$i]["id"]."\n id_concepto: ".$ficha_concepto[$k]["id"]."");
            }
            $tmp=$tmp[0];
            $id_accion_subespecifica=$id_accion_subespecifica_original;
            if(in_array(substr($tmp["id_cuenta_presupuestaria"],0,5),["40103","40104","40106"]))
              $id_accion_subespecifica=46;//colocar partidas 40103 (prima prof, hijos y antiguedad) y 40104 (bono alimentacion) por ACC001-15-01
            if(in_array(substr($tmp["id_cuenta_presupuestaria_ap"],0,5),["40106"]))
              $id_accion_subespecifica=46;//colocar partidas 40103 (prima prof, hijos y antiguedad) y 40104 (bono alimentacion) por ACC001-15-01
            if(in_array(substr($tmp["id_cuenta_presupuestaria"],0,7),["4010794","4010795","4010796","4010797","4010799"]))
              $id_accion_subespecifica=46;//colocar partidas 4010796 por ACC001-15-01
            if(in_array(substr($tmp["id_cuenta_presupuestaria_ap"],0,7),["4010768","4010757","4010707","4010723","4010768","4010796","40107","4010795","4010797","4010794","4010799"]))
              $id_accion_subespecifica=46;//colocar partidas 4010796 por ACC001-15-01

            if(!isset($detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]) and $tmp["id_cuenta_presupuestaria"]!="") $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]=0;
            if(!isset($detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]) and $tmp["id_cuenta_presupuestaria_ap"]!="") $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]=0;
            if(!isset($detalle[$tmp["id_cuenta_contable"]]) and $tmp["id_cuenta_contable"]!="") $detalle[$tmp["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
            if(!isset($detalle[$tmp["id_cuenta_contable_ap"]]) and $tmp["id_cuenta_contable_ap"]!="") $detalle[$tmp["id_cuenta_contable_ap"]]=array("D"=>0,"H"=>0);


            switch($ficha_concepto[$k]["tipo"]){
              case "A":
                if($buscar==="AP") break;
                if($tmp["id_cuenta_presupuestaria"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]+=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA PARA LA ASIGNACIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "RA":
                if($buscar==="AP") break;
                if($tmp["id_cuenta_presupuestaria"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria"]]-=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA PARA EL REINTEGRO DE ASIGNACIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "AP":
                if($buscar!=="AP"){
                  if($tmp["id_cuenta_contable"])
                    $detalle[$tmp["id_cuenta_contable"]]["H"]+=$ficha_concepto[$k]["valor_final"];
                  else
                    return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE (TRABAJADOR) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                }
                if($buscar!==NULL and $buscar!=="AP") break;
                if($tmp["id_cuenta_presupuestaria_ap"])
                  $detalle["$id_accion_subespecifica|".$tmp["id_cuenta_presupuestaria_ap"]]+=$ficha_concepto[$k]["valor_final_ap"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA PRESUPUESTARIA (PATRON) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                if($tmp["id_cuenta_contable_ap"])
                  $detalle[$tmp["id_cuenta_contable_ap"]]["H"]+=$ficha_concepto[$k]["valor_final_ap"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE (PATRON) PARA EL APORTE PATRONAL (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "D":
                if($buscar==="AP") break;
                if($tmp["id_cuenta_contable"])
                  $detalle[$tmp["id_cuenta_contable"]]["H"]+=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE PARA LA DEDUCCIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
              case "RD":
                if($buscar==="AP") break;
                if($tmp["id_cuenta_contable"])
                  $detalle[$tmp["id_cuenta_contable"]]["D"]+=$ficha_concepto[$k]["valor_final"];
                else
                  return array("success"=>false, "message"=> "DEBE DEFINIR LA CUENTA CONTABLE PARA EL REINTEGRO DE DEDUCCIÓN (id_nomina=".$nomina[$i]["id"].", id_concepto=".$ficha_concepto[$k]['id'].")");
                break;
            }

          }
        endfor;
      endfor;
    endfor;

    if(count($cuenta_bancaria)!=1)
      return array("success"=>false, "message"=> "Error. Es necesario asociar una cuenta bancaria, consulte al administrador del sistema.");


    function ordenar_detalle_presupuestario_contable($a,$b){
      $a=explode("|",$a);
      $b=explode("|",$b);
      if(count($a)==2 and count($b)==2)//detalle presupuestario
        return strcmp($a[1],$b[1]);
      if(count($a)==1 and count($b)==1)//detalle contable
        return strcmp($a[0],$b[0]);
      if(count($a)==1)
        return 1;
      return -1;
    }

    uksort($detalle,"ordenar_detalle_presupuestario_contable");

    $retorno=array();

    $retorno["concepto"]="";
    for($i=0;$i<count($nomina);$i++)
      $retorno["concepto"].=$nomina[$i]["nomina"].($i<count($nomina)-1?", ":"");
    $retorno["concepto"]="CANCELACIÓN DE LA NOMINA: ".$retorno["concepto"].". CORRESPONDIENTE A: ".$periodo["descripcion"]." (PERIODO ".$periodo["codigo"].", DEL ".formatDate($periodo["fecha_inicio"])." AL ".formatDate($periodo["fecha_culminacion"]).").\n";

    $retorno["detalle"]=array();

    $retorno["detalle"]["comprobante_bancario"]=array();
    //buscar id nota de debito
    $id_ND=$db->Execute("select id from modulo_base.banco_movimiento_tipo where codigo='ND'");
    $id_ND=$id_ND[0][0];
    $retorno["detalle"]["comprobante_bancario"]["id_banco_movimiento_tipo"]=$id_ND;
    $retorno["detalle"]["comprobante_bancario"]["operacion"]="ND";
    foreach($cuenta_bancaria as $clave => $valor){
      $clave=explode("|",$clave);
      $retorno["detalle"]["comprobante_bancario"]["id_banco_cuenta"]=$clave[0];
      $retorno["detalle"]["comprobante_bancario"]["numero_cuenta"]=$clave[1];
      $retorno["detalle"]["comprobante_bancario"]["numero"]="0";
      $retorno["detalle"]["comprobante_bancario"]["monto"]=number_format($valor,2,".","");
    }

    //
    $retorno["detalle"]["presupuestario"]=array();
    $p=0;
    foreach ($detalle as $clave => $valor){
      $clave=explode("|",$clave);
      if(count($clave)!=2) continue;
      //$clave = id_accion_subespecifica|id_cuenta_presupuestaria
      $id_accion_subespecifica=$clave[0];
      $id_cuenta_presupuestaria=$clave[1];

      //buscar id_accion_subespecifica|id_cuenta_presupuestaria
      $accpro=$db->Execute("SELECT _formatear_estructura_presupuestaria($id_accion_subespecifica) as estructura_presupuestaria");
      $cuenta=$db->Execute("SELECT
                                CP.denominacion,
                                _formatear_cuenta_presupuestaria(CP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                                C.id_cuenta_contable
                            FROM
                              modulo_base.cuenta_presupuestaria as CP
                              LEFT JOIN modulo_base.convertidor as C ON CP.id_cuenta_presupuestaria = C.id_cuenta_presupuestaria
                            WHERE
                              CP.id_cuenta_presupuestaria='$id_cuenta_presupuestaria'");

      if(isset($cuenta[0]["id_cuenta_contable"])){
        if(!isset($detalle[$cuenta[0]["id_cuenta_contable"]]))
          $detalle[$cuenta[0]["id_cuenta_contable"]]=array("D"=>0,"H"=>0);
        $detalle[$cuenta[0]["id_cuenta_contable"]]["D"]+=$valor;
      }

      if($valor==0)
        continue;

      $retorno["detalle"]["presupuestario"][$p]=array(
                                                      "id_accion_subespecifica"=>"$id_accion_subespecifica",
                                                      "estructura_presupuestaria"=>$accpro[0]["estructura_presupuestaria"],
                                                      "id_cuenta_presupuestaria"=>"$id_cuenta_presupuestaria",
                                                      "cuenta_presupuestaria"=>$cuenta[0]["cuenta_presupuestaria"],
                                                      "denominacion"=>$cuenta[0]["denominacion"],
                                                      "operacion"=>"CCP",
                                                      "monto"=>number_format($valor,2,".","")
                                                      );
      $p++;
    }

    $retorno["detalle"]["contable"]=array();
    $c=0;
    foreach ($detalle as $clave => $valor){
      $clave=explode("|",$clave);
      if(count($clave)!=1) continue;
      $id_cuenta_contable=$clave[0];
      $cuenta=$db->Execute("SELECT id_cuenta_contable, denominacion,  _formatear_cuenta_contable(id_cuenta_contable) as cuenta_contable FROM modulo_base.cuenta_contable WHERE id_cuenta_contable='$id_cuenta_contable'");

      if($valor["D"]!=0){
        $retorno["detalle"]["contable"][$c]=array(
                                                  "id_cuenta_contable"=>"$id_cuenta_contable",
                                                  "cuenta_contable"=>$cuenta[0]["cuenta_contable"],
                                                  "denominacion"=>$cuenta[0]["denominacion"],
                                                  "operacion"=>"D",
                                                  "monto"=>number_format($valor["D"],2,".","")
                                                  );
        $c++;
      }

      if($valor["H"]!=0){
        $retorno["detalle"]["contable"][$c]=array(
                                                  "id_cuenta_contable"=>"$id_cuenta_contable",
                                                  "cuenta_contable"=>$cuenta[0]["cuenta_contable"],
                                                  "denominacion"=>$cuenta[0]["denominacion"],
                                                  "operacion"=>"H",
                                                  "monto"=>number_format($valor["H"],2,".","")
                                                  );
        $c++;
      }
    }

    $retorno["success"]=true;

    return $retorno;
  }

  public static function onContabilizar($access,$id_periodo,$fecha,$tipo="CCP"){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para contabilizar el período.");
      exit;
    }

    $db=SIGA::DBController();

    //buscar periodo información del periodo
    $periodo=$db->Execute("SELECT cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");

    if($periodo[0]["cerrado"]!=='t'){
      return array("success"=>false, "message"=>"El período se encuentra abierto, debe cerrarlo antes de contabilizar.");
    }

    //buscar detalles del periodo
    $periodo=$db->Execute("SELECT id, codigo, fecha_inicio, fecha_culminacion, tipo, descripcion, contabilizado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $periodo=$periodo[0];
    //buscar nominas del periodo
    $nomina=$db->Execute("SELECT id, codigo, nomina FROM modulo_nomina.nomina WHERE tipo='".$periodo["tipo"]."'");

    $result=self::detalle_presupuestario_contable($periodo,$nomina,$tipo);

    if(!$result["success"])
      return array("success"=>false, "message"=>$result["message"]);



    $id='';
    $concepto=$result["concepto"];
    $contabilizado='t';
    $id_persona='';
    $detalle=array();
    $detalle["presupuestario"]=array();
    for($i=0;$i<count($result["detalle"]["presupuestario"]);$i++){
      $detalle["presupuestario"][$i]=array(
                                           "id_accion_subespecifica"=>$result["detalle"]["presupuestario"][$i]["id_accion_subespecifica"],
                                           "id_cuenta_presupuestaria"=>$result["detalle"]["presupuestario"][$i]["id_cuenta_presupuestaria"],
                                           "operacion"=>$result["detalle"]["presupuestario"][$i]["operacion"],
                                           "monto"=>$result["detalle"]["presupuestario"][$i]["monto"]
                                           );
    }

    $detalle["contable"]=array();
    for($i=0;$i<count($result["detalle"]["contable"]);$i++){
      $detalle["contable"][$i]=array(
                                    "id_cuenta_contable"=>$result["detalle"]["contable"][$i]["id_cuenta_contable"],
                                    "operacion"=>$result["detalle"]["contable"][$i]["operacion"],
                                    "monto"=>$result["detalle"]["contable"][$i]["monto"]
                                    );
    }

    if($tipo=="CCP" or $tipo=="P"){
      $cmp_tipo='MB';
      $detalle["comprobante_bancario"]=array();
      $detalle["comprobante_bancario"]["id_banco_cuenta"]=$result["detalle"]["comprobante_bancario"]["id_banco_cuenta"];
      $detalle["comprobante_bancario"]["id_banco_movimiento_tipo"]=$result["detalle"]["comprobante_bancario"]["id_banco_movimiento_tipo"];
      $detalle["comprobante_bancario"]["numero"]=$result["detalle"]["comprobante_bancario"]["numero"];
      $detalle["comprobante_bancario"]["monto"]=$result["detalle"]["comprobante_bancario"]["monto"];
    }
    else {
      $cmp_tipo="PC";
    }

    if(($tipo=="P" or $tipo=="P-CXC") and $periodo["contabilizado"]){
      $detalle["comprobante_previo"]=array($periodo["contabilizado"]);
    }

    $organismo=$db->Execute("SELECT id_persona FROM modulo_base.organismo");
    if(isset($organismo[0]["id_persona"]))
      $id_persona=$organismo[0]["id_persona"];

    include_once("comprobante.class.php");
    $result_comprobante=comprobante::onSave($access,
                                            $id,
                                            $cmp_tipo,
                                            $fecha,
                                            $concepto,
                                            $contabilizado,
                                            $id_persona,
                                            $detalle);

    if(!$result_comprobante["success"])
      return array("success"=>false, "message"=>$result_comprobante["message"]);

    $id_comprobante=$result_comprobante["id"];
    if($tipo!="P")
      $db->Update("modulo_nomina.periodo",array("contabilizado"=>"$id_comprobante"),"id=$id_periodo");

    return array("success"=>true, "message"=>"La nómina se contabilizó sin problemas.");
  }

  public static function onList($id_periodo,$tipo,$text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT *, codigo||' '||nomina as codigo_nomina FROM modulo_nomina.nomina WHERE activo AND UPPER(nomina) LIKE UPPER('%$text%')";

    if($id_periodo){
      $sql="SELECT N.*, N.codigo||' '||N.nomina as codigo_nomina
            FROM modulo_nomina.nomina as N
            WHERE N.activo AND UPPER(N.nomina) LIKE UPPER('%$text%') AND N.tipo=(select tipo from modulo_nomina.periodo where id=$id_periodo)";
    }
    if($tipo){
      $sql="SELECT N.*, N.codigo||' '||N.nomina as codigo_nomina
            FROM modulo_nomina.nomina as N
            WHERE N.activo AND UPPER(N.nomina) LIKE UPPER('%$text%') AND N.tipo='$tipo'";
    }


    //$return["result"]=$db->Execute($sql);
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onPersona_CambiarNomina($access,$id_ficha,$id_periodo,$id_nomina,$id_nomina_anterior){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para realizar el cambio.");
      exit;
    }
    $db=SIGA::DBController();
    $db->Update("modulo_nomina.ficha_concepto",array("id_nomina"=>"$id_nomina"),"id_ficha=$id_ficha and id_periodo=$id_periodo and id_nomina=$id_nomina_anterior");

    //agregar conceptos del encabezado de nomina
    $sql="
      INSERT INTO modulo_nomina.concepto_periodo(id_concepto,id_nomina,id_periodo)
      select distinct FC.id_concepto, $id_nomina, $id_periodo
      from modulo_nomina.ficha_concepto FC
      where FC.id_nomina=$id_nomina and FC.id_periodo=$id_periodo and
      not FC.id_concepto in (select CP.id_concepto from modulo_nomina.concepto_periodo CP where CP.id_nomina=$id_nomina and CP.id_periodo=$id_periodo)
      ";
    $db->Execute($sql);

    return array("success"=>true, "message"=>"La modificación se realizó sin problemas.");
  }

  public static function onPersona_QuitarInactivo($access,$id_periodo,$id_nomina){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para realizar el cambio.");
      exit;
    }
    $db=SIGA::DBController();

    $sql="
      DELETE FROM modulo_nomina.ficha_concepto FC
      WHERE
        FC.id_periodo='$id_periodo' and
        FC.id_nomina in ($id_nomina) and
        FC.id_ficha IN (select F.id from modulo_nomina.ficha F where not F.activo)
    ";

    $db->Execute($sql);

    return array("success"=>true, "message"=>"Operación realizada con éxito.");
  }

  public static function onPersona_CambiarNominaInactivo($access,$id_periodo,$id_nomina,$id_nomina_anterior){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para realizar el cambio.");
      exit;
    }
    $db=SIGA::DBController();
    $db->Update("modulo_nomina.ficha_concepto",["id_nomina"=>"$id_nomina"],
      "id_ficha IN (select F.id from modulo_nomina.ficha F where not F.activo) and
      id_periodo=$id_periodo and
      id_nomina in ($id_nomina_anterior)
    ");

    //agregar conceptos del encabezado de nomina
    $sql="
      INSERT INTO modulo_nomina.concepto_periodo(id_concepto,id_nomina,id_periodo)
      select distinct FC.id_concepto, $id_nomina, $id_periodo
      from modulo_nomina.ficha_concepto FC
      where FC.id_nomina=$id_nomina and FC.id_periodo=$id_periodo and
      not FC.id_concepto in (select CP.id_concepto from modulo_nomina.concepto_periodo CP where CP.id_nomina=$id_nomina and CP.id_periodo=$id_periodo)
      ";
    $db->Execute($sql);

    return array("success"=>true, "message"=>"Operación realizada con éxito.");
  }

  public static function onList_Cargo($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.cargo WHERE activo";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onPersona_CambiarCargo($access,$id_ficha,$id_periodo,$id_cargo){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para realizar el cambio.");
      exit;
    }
    $db=SIGA::DBController();
    //buscar periodo información del periodo
    $periodo=$db->Execute("SELECT fecha_inicio, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      return array("success"=>false, "message"=>"El período se encuentra cerrado, no se puede ejecutar la operación.");
    }
    //borrar el cargo para el periodo
    $db->Delete("modulo_nomina.ficha_cargo","id_ficha=$id_ficha and fecha='".$periodo[0]["fecha_inicio"]."'");
    $db->Insert("modulo_nomina.ficha_cargo",
                array(
                      "id_ficha"=>"$id_ficha",
                      "id_cargo"=>"$id_cargo",
                      "fecha"=>"'".$periodo[0]["fecha_inicio"]."'"
                      ));


    return array("success"=>true, "message"=>"La modificación se realizó sin problemas.");
  }

  public static function onPersona_CambiarEP($access,$id_ficha,$id_periodo,$id_accion_subespecifica){
    if($access!="rw"){
      return array("success"=>false, "message"=>"No tiene permisos para realizar el cambio.");
      exit;
    }
    $db=SIGA::DBController();
    //buscar periodo información del periodo
    $periodo=$db->Execute("SELECT fecha_inicio, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    if($periodo[0]["cerrado"]==='t'){
      return array("success"=>false, "message"=>"El período se encuentra cerrado, no se puede ejecutar la operación.");
    }
    //borrar la ep para el periodo
    $db->Delete("modulo_nomina.ficha_estructura_presupuestaria","id_ficha=$id_ficha and fecha='".$periodo[0]["fecha_inicio"]."'");
    $db->Insert("modulo_nomina.ficha_estructura_presupuestaria",
                array(
                      "id_ficha"=>"$id_ficha",
                      "id_accion_subespecifica"=>"$id_accion_subespecifica",
                      "fecha"=>"'".$periodo[0]["fecha_inicio"]."'"
                      ));

    return array("success"=>true, "message"=>"La modificación se realizó sin problemas.");
  }


    public static function onListFichaPeriodo($id_nomina,$id_periodo,$start,$limit,$filtro_busqueda=NULL){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];

    //print "XXXXXXXXXXXX";
    //print_r($filtro_busqueda);exit;

    $add="";
    $filtro_id_ficha=[];
    if($filtro_busqueda){
      $add_filtro="";
      if(isset($filtro_busqueda["cedula"]) and $filtro_busqueda["cedula"])
        $add_filtro.="AND concat(P.identificacion_tipo,P.identificacion_numero) ilike '%".$filtro_busqueda["cedula"]."%' ";
      if(isset($filtro_busqueda["nombre_apellido"]) and $filtro_busqueda["nombre_apellido"])
        $add_filtro.="AND (split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) ilike '%".$filtro_busqueda["nombre_apellido"]."%') ";
      if(isset($filtro_busqueda["id_cargo"]) and $filtro_busqueda["id_cargo"])
        $add_filtro.="AND C.id in (".implode(",",$filtro_busqueda["id_cargo"]).") ";
      if(isset($filtro_busqueda["id_escala_salarial"]) and $filtro_busqueda["id_escala_salarial"])
        $add_filtro.="AND F.id_escala_salarial in (".implode(",",$filtro_busqueda["id_escala_salarial"]).") ";
      if(isset($filtro_busqueda["ingreso_mes"]) and $filtro_busqueda["ingreso_mes"])
        $add_filtro.="AND extract(month from F.fecha_ingreso[1]) in (".implode(",",$filtro_busqueda["ingreso_mes"]).") ";
      if(isset($filtro_busqueda["estatus"]) and $filtro_busqueda["estatus"]=="I")
        $add_filtro.="AND NOT F.activo ";
      if(isset($filtro_busqueda["estatus"]) and $filtro_busqueda["estatus"]=="A")
        $add_filtro.="AND F.activo ";

      if($add_filtro){
        $sql="
          SELECT DISTINCT
            F.id
          FROM
            modulo_nomina.nomina as N,
            modulo_nomina.ficha_concepto as FC,
            modulo_base.persona as P,
            modulo_nomina.ficha AS F LEFT JOIN
            modulo_nomina.cargo as C ON C.id=(
                                              select id_cargo
                                              from modulo_nomina.ficha_cargo
                                              where id_ficha=F.id and fecha <= '$fecha_culminacion'
                                              order by fecha desc
                                              limit 1)

          WHERE
            F.id_persona=P.id AND
            F.id=FC.id_ficha AND
            FC.id_periodo=$id_periodo AND
            FC.id_nomina in ($id_nomina) AND
            FC.id_nomina=N.id
            $add_filtro
        ";
        //print $sql;
        $tmp=$db->Execute($sql);
        $filtro_id_ficha=[];
        for($f=0; $f<count($tmp); $f++) {
          $filtro_id_ficha[]=$tmp[$f]["id"];
        }
        if(count($filtro_id_ficha)===0)
          $add.="AND FALSE";
        else
          $add.="AND F.id in (".implode(",",$filtro_id_ficha).")";
      }

      if(isset($filtro_busqueda["id_ficha"]) and $filtro_busqueda["id_ficha"] and count($filtro_busqueda["id_ficha"])>0){
        $add="AND F.id in (".implode(",",$filtro_busqueda["id_ficha"]).")";
      }
    }

    /*
    $sql="SELECT
            F.id,
            F.id as id_ficha,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
            replace(P.denominacion,';',' ') as nombres_apellidos,
            C.cargo,
            C.denominacion as cargo_denominacion,
            C.orden,
            F.fecha_ingreso,
            F.cuenta_nomina,
            F.id_escala_salarial,
            F.activo,
            (select escala from modulo_nomina.escala_salarial as ES where ES.id=F.id_escala_salarial) as escala_salarial
          FROM
            modulo_base.persona as P,
            modulo_nomina.ficha AS F LEFT JOIN
            modulo_nomina.cargo as C ON C.id=(
                                              select id_cargo
                                              from modulo_nomina.ficha_cargo
                                              where id_ficha=F.id and fecha <= '$fecha_culminacion'
                                              order by fecha desc
                                              limit 1)

          WHERE
            F.id_persona=P.id AND
            F.id in (select distinct id_ficha from modulo_nomina.ficha_concepto where id_periodo=$id_periodo and id_nomina=$id_nomina)
            $add
          ";*/

    $sql="SELECT DISTINCT
            FC.id_nomina,
            N.codigo || ' ' || N.nomina as nomina,
            F.id,
            F.id as id_ficha,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
            replace(P.denominacion,';',' ') as nombres_apellidos,
            C.cargo,
            C.denominacion as cargo_denominacion,
            C.orden,
            F.fecha_ingreso,
            F.cuenta_nomina,
            F.id_escala_salarial,
            F.activo,
            PN.genero,
            (select escala from modulo_nomina.escala_salarial as ES where ES.id=F.id_escala_salarial) as escala_salarial
          FROM
            modulo_nomina.nomina as N,
            modulo_nomina.ficha_concepto as FC,
            modulo_base.persona as P LEFT JOIN
              modulo_base.persona_natural as PN ON P.id=PN.id_persona,
            modulo_nomina.ficha AS F LEFT JOIN
              modulo_nomina.cargo as C ON C.id=(
                                              select id_cargo
                                              from modulo_nomina.ficha_cargo
                                              where id_ficha=F.id and fecha <= '$fecha_culminacion'
                                              order by fecha desc
                                              limit 1)

          WHERE
            F.id_persona=P.id AND
            F.id=FC.id_ficha AND
            FC.id_periodo=$id_periodo AND
            FC.id_nomina in ($id_nomina) AND
            FC.id_nomina=N.id
            $add
          ";

    $ficha=$db->Execute($sql." ORDER BY nomina, cedula  LIMIT $limit OFFSET $start");

    $result=array();

    for($i=0;$i<count($ficha);$i++){
      $result[$i]=array();
      $result[$i]["id_nomina"]=$ficha[$i]["id_nomina"];
      $result[$i]["nomina"]=$ficha[$i]["nomina"];
      $result[$i]["id"]=$ficha[$i]["id"];
      $result[$i]["id_ficha"]=$ficha[$i]["id_ficha"];
      $result[$i]["nacionalidad"]=$ficha[$i]["nacionalidad"];
      $result[$i]["cedula"]=$ficha[$i]["cedula"];
      $result[$i]["nombre_apellido"]=$ficha[$i]["nombre_apellido"];
      $result[$i]["nombres_apellidos"]=$ficha[$i]["nombres_apellidos"];
      $result[$i]["activo"]=$ficha[$i]["activo"];
      $result[$i]["genero"]=$ficha[$i]["genero"];
      $result[$i]["cargo"]=$ficha[$i]["cargo"];
      $result[$i]["cargo_denominacion"]=$ficha[$i]["cargo_denominacion"];
      $result[$i]["orden"]=$ficha[$i]["orden"];
      $result[$i]["fecha_ingreso"]=str_replace(["{","}"],"",$ficha[$i]["fecha_ingreso"]);
      if($result[$i]["fecha_ingreso"]){
        $tmp=explode(",",$result[$i]["fecha_ingreso"]);
        $result[$i]["fecha_ingreso"]=end($tmp);
        $result[$i]["fecha_ingreso"]=formatDate($result[$i]["fecha_ingreso"]);
      }
      $result[$i]["cuenta_nomina"]=$ficha[$i]["cuenta_nomina"];
      $result[$i]["escala_salarial"]=$ficha[$i]["escala_salarial"];
      //buscar si la ficha existe en otras nominas en el mismo periodo
      $result[$i]["otra_nomina"]=$db->Execute("select distinct FC.id_nomina, N.codigo, N.nomina from modulo_nomina.ficha_concepto FC, modulo_nomina.nomina N where FC.id_periodo=$id_periodo and not FC.id_nomina in ($id_nomina) and FC.id_ficha='".$ficha[$i]["id_ficha"]."' and N.id=FC.id_nomina");
      $result[$i]["activo_otra_nomina"]="";
      for($k=0;$k<count($result[$i]["otra_nomina"]);$k++){
        $result[$i]["activo_otra_nomina"].="&nbsp;&nbsp;".$result[$i]["otra_nomina"][$k]["codigo"]." ".$result[$i]["otra_nomina"][$k]["nomina"]."<br>";
      }
      if($result[$i]["activo_otra_nomina"]){
        $result[$i]["activo_otra_nomina"]="Personal Nomina:<br>".$result[$i]["activo_otra_nomina"];
      }

      $ficha_ep=$db->Execute("SELECT
                                _formatear_estructura_presupuestaria(FEP.id_accion_subespecifica) as estructura_presupuestaria,
                                ASE.denominacion_subespecifica,
                                AE.denominacion_especifica,
                                A.denominacion_centralizada
                              FROM
                                modulo_nomina.ficha_estructura_presupuestaria FEP,
                                modulo_base.accion_subespecifica ASE,
                                modulo_base.accion_especifica AE,
                                modulo_base.accion_centralizada A
                              WHERE FEP.id_accion_subespecifica=ASE.id AND ASE.id_accion_especifica=AE.id AND AE.id_accion_centralizada=A.id AND
                                id_ficha=".$result[$i]["id"]." AND
                                fecha<='$fecha_culminacion'
                              ORDER BY fecha
                              DESC LIMIT 1");
      $result[$i]["estructura_presupuestaria"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["estructura_presupuestaria"]:"";
      $result[$i]["denominacion_especifica"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_especifica"]:"";
      $result[$i]["denominacion_subespecifica"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_subespecifica"]:"";
      $result[$i]["denominacion_centralizada"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_centralizada"]:"";

      //$ficha_concepto=self::ficha_concepto($id_nomina,$id_periodo,$ficha[$i]["id"]);
      $ficha_concepto=self::ficha_concepto($ficha[$i]["id_nomina"],$id_periodo,$ficha[$i]["id"]);


      $result[$i]["concepto"]=array();
      for($j=0;$j<count($ficha_concepto["concepto"]);$j++){
        $result[$i][$ficha_concepto["concepto"][$j]["id"]]=$ficha_concepto["concepto"][$j]["valor_final"];
        if($ficha_concepto["concepto"][$j]["tipo"]=="AP")
          $result[$i][$ficha_concepto["concepto"][$j]["id"]."_ap"]=$ficha_concepto["concepto"][$j]["valor_final_ap"];
        $result[$i]["concepto"][$j]=array(
                                          "id"=>$ficha_concepto["concepto"][$j]["id"],
                                          "codigo"=>$ficha_concepto["concepto"][$j]["codigo"],
                                          "tipo"=>$ficha_concepto["concepto"][$j]["tipo"],
                                          "concepto"=>$ficha_concepto["concepto"][$j]["concepto"],
                                          "identificador"=>$ficha_concepto["concepto"][$j]["identificador"],
                                          "orden"=>$ficha_concepto["concepto"][$j]["orden"],
                                          "valor_final"=>$ficha_concepto["concepto"][$j]["valor_final"],
                                          "valor_final_ap"=>isset($ficha_concepto["concepto"][$j]["valor_final_ap"])?$ficha_concepto["concepto"][$j]["valor_final_ap"]:""
                                          );
      }
      $result[$i]["antiguedad_anio_dia"]=$ficha_concepto["antiguedad_anio_dia"];
      $result[$i]["total_asignacion"]=$ficha_concepto["total_asignacion"];
      $result[$i]["total_deduccion"]=$ficha_concepto["total_deduccion"];
      $result[$i]["total_neto"]=$ficha_concepto["total_neto"];
      $result[$i]["total_ap"]=$ficha_concepto["total_ap"];


      $result[$i]["mes_ingreso"]=0;
      $tmp1=explode("-", $fecha_inicio);
      $tmp2=explode("-", $result[$i]["fecha_ingreso"]);
      if(isset($tmp1[1]) and isset($tmp2[1]))
        if($tmp1[1]==$tmp2[1])
          $result[$i]["mes_ingreso"]=1;

      if($ficha_concepto["antiguedad_anio_dia"][0]==0) $result[$i]["mes_ingreso"]=0;
    }

    $return["result"]=$result;
    //$return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$db->Execute("select count(*) from ($sql) as total");
    $return["total"]=$return["total"][0][0];
    $return["filtro_ficha_id"]=$filtro_id_ficha;
    return $return;
  }

  public static function onListFichaPeriodo_original($id_nomina,$id_periodo,$start,$limit,$filtro_busqueda=NULL){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];

    $sql="SELECT DISTINCT
            FC.id_nomina,
            N.codigo || ' ' || N.nomina as nomina,
            F.id,
            F.id as id_ficha,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
            replace(P.denominacion,';',' ') as nombres_apellidos,
            C.cargo,
            C.denominacion as cargo_denominacion,
            C.orden
          FROM
            modulo_nomina.nomina as N,
            modulo_nomina.ficha_concepto as FC,
            modulo_base.persona as P,
            modulo_nomina.ficha AS F LEFT JOIN
            modulo_nomina.cargo as C ON C.id=(
                                              select id_cargo
                                              from modulo_nomina.ficha_cargo
                                              where id_ficha=F.id and fecha <= '$fecha_culminacion'
                                              order by fecha desc
                                              limit 1)

          WHERE
            F.id_persona=P.id AND
            F.id=FC.id_ficha AND
            FC.id_periodo=$id_periodo AND
            FC.id_nomina in ($id_nomina) AND
            FC.id_nomina=N.id
          ";

    $ficha=$db->Execute($sql." ORDER BY nomina, nombre_apellido  LIMIT $limit OFFSET $start");

    $result=array();

    for($i=0;$i<count($ficha);$i++){
      $result[$i]=array();
      $result[$i]["id_nomina"]=$ficha[$i]["id_nomina"];
      $result[$i]["nomina"]=$ficha[$i]["nomina"];
      $result[$i]["id"]=$ficha[$i]["id"];
      $result[$i]["id_ficha"]=$ficha[$i]["id_ficha"];
      $result[$i]["nacionalidad"]=$ficha[$i]["nacionalidad"];
      $result[$i]["cedula"]=$ficha[$i]["cedula"];
      $result[$i]["nombre_apellido"]=$ficha[$i]["nombre_apellido"];
      $result[$i]["nombres_apellidos"]=$ficha[$i]["nombres_apellidos"];
      $result[$i]["cargo"]=$ficha[$i]["cargo"];
      $result[$i]["cargo_denominacion"]=$ficha[$i]["cargo_denominacion"];
      $result[$i]["orden"]=$ficha[$i]["orden"];


      $ficha_ep=$db->Execute("SELECT
                                _formatear_estructura_presupuestaria(FEP.id_accion_subespecifica) as estructura_presupuestaria,
                                ASE.denominacion_subespecifica,
                                AE.denominacion_especifica,
                                A.denominacion_centralizada
                              FROM
                                modulo_nomina.ficha_estructura_presupuestaria FEP,
                                modulo_base.accion_subespecifica ASE,
                                modulo_base.accion_especifica AE,
                                modulo_base.accion_centralizada A
                              WHERE FEP.id_accion_subespecifica=ASE.id AND ASE.id_accion_especifica=AE.id AND AE.id_accion_centralizada=A.id AND
                                id_ficha=".$result[$i]["id"]." AND
                                fecha<='$fecha_culminacion'
                              ORDER BY fecha
                              DESC LIMIT 1");
      $result[$i]["estructura_presupuestaria"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["estructura_presupuestaria"]:"";
      $result[$i]["denominacion_especifica"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_especifica"]:"";
      $result[$i]["denominacion_subespecifica"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_subespecifica"]:"";
      $result[$i]["denominacion_centralizada"]=isset($ficha_ep[0]["estructura_presupuestaria"])?$ficha_ep[0]["denominacion_centralizada"]:"";

      $ficha_concepto=self::ficha_concepto($ficha[$i]["id_nomina"],$id_periodo,$ficha[$i]["id"]);


      $result[$i]["concepto"]=array();
      for($j=0;$j<count($ficha_concepto["concepto"]);$j++){
        $result[$i][$ficha_concepto["concepto"][$j]["id"]]=$ficha_concepto["concepto"][$j]["valor_final"];
        if($ficha_concepto["concepto"][$j]["tipo"]=="AP")
          $result[$i][$ficha_concepto["concepto"][$j]["id"]."_ap"]=isset($ficha_concepto["concepto"][$j]["valor_final_ap"])?$ficha_concepto["concepto"][$j]["valor_final_ap"]:"";
        $result[$i]["concepto"][$j]=array(
                                          "id"=>$ficha_concepto["concepto"][$j]["id"],
                                          "codigo"=>$ficha_concepto["concepto"][$j]["codigo"],
                                          "tipo"=>$ficha_concepto["concepto"][$j]["tipo"],
                                          "concepto"=>$ficha_concepto["concepto"][$j]["concepto"],
                                          "identificador"=>$ficha_concepto["concepto"][$j]["identificador"],
                                          "orden"=>$ficha_concepto["concepto"][$j]["orden"],
                                          "valor_final"=>$ficha_concepto["concepto"][$j]["valor_final"],
                                          "valor_final_ap"=>isset($ficha_concepto["concepto"][$j]["valor_final_ap"])?$ficha_concepto["concepto"][$j]["valor_final_ap"]:""
                                          );
      }
      $result[$i]["antiguedad_anio_dia"]=$ficha_concepto["antiguedad_anio_dia"];
      $result[$i]["total_asignacion"]=$ficha_concepto["total_asignacion"];
      $result[$i]["total_deduccion"]=$ficha_concepto["total_deduccion"];
      $result[$i]["total_neto"]=$ficha_concepto["total_neto"];
      $result[$i]["total_ap"]=$ficha_concepto["total_ap"];

    }

    $return["result"]=$result;
    $return["total"]=$db->Execute("select count(*) from ($sql) as total");
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onListConceptoPeriodo($access,$id_nomina,$id_periodo){
    $db=SIGA::DBController();

    $periodo=$db->Execute("SELECT fecha_inicio, fecha_culminacion, cerrado, contabilizado FROM modulo_nomina.periodo WHERE id=$id_periodo");
    $fecha_inicio=$periodo[0]["fecha_inicio"];
    $fecha_culminacion=$periodo[0]["fecha_culminacion"];
    //print_r($periodo);
    $return=array();
    $return["concepto"]=$db->Execute("SELECT DISTINCT
                                          C.id,
                                          C.orden,
                                          C.codigo,
                                          C.tipo,
                                          C.identificador,
                                          C.concepto,
                                          CF.definicion,
                                          CF.definicion_ap,
                                          CP.id_concepto
                                        FROM
                                          modulo_nomina.concepto AS C,
                                          modulo_nomina.concepto_formula as CF,
                                          modulo_nomina.concepto_periodo as CP
                                        WHERE
                                          C.id=CF.id_concepto AND
                                          CF.fecha = (SELECT fecha
                                                      FROM modulo_nomina.concepto_formula
                                                      WHERE fecha<='$fecha_culminacion' AND id_concepto=C.id
                                                      ORDER BY fecha DESC
                                                      LIMIT 1) AND
                                          C.id=CP.id_concepto AND
                                          CP.id_periodo=$id_periodo AND CP.id_nomina IN ($id_nomina)
                                        ORDER BY C.orden, C.codigo");


    $k=0;
    $conceptos_identificador=array();
    $conceptos_identificador_sistema=self::constante_sistema($fecha_inicio,$fecha_culminacion);

    foreach($conceptos_identificador_sistema as $d => $valor_formula){
      $conceptos_identificador[$k]=$d;
      $k++;
      }

    for($i=0;$i<count($return["concepto"]);$i++,$k++)
      $conceptos_identificador[$k]=$return["concepto"][$i]["identificador"];

    for($i=0;$i<count($return["concepto"]);$i++){
      $return["concepto"][$i]["es_formula"]=self::es_formula($return["concepto"][$i]["definicion"]);
      $return["concepto"][$i]["indefinido"]=self::formula_tokens_indefinidos($conceptos_identificador,$return["concepto"][$i]["definicion"]);
    }

    $return["cerrado"]=$periodo[0]["cerrado"];
    $return["contabilizado"]=$periodo[0]["contabilizado"];
    return $return;
  }

}
?>
