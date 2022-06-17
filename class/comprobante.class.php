<?php
class comprobante{
  public static function onGet_Correlativo($tipo){
    $db=SIGA::DBController();

    $sql="SELECT _if(max(correlativo) is null, 1, max(correlativo)+1) as correlativo FROM modulo_base.comprobante WHERE tipo='$tipo' and EXTRACT(YEAR FROM fecha)=".SIGA::data()."";
    return $db->Execute($sql);
  }

  public static function onGet($id,$detalle=true){
    $db=SIGA::DBController();

    $return=$db->Execute("SELECT
                            C.id,
                            C.tipo,
                            lpad(text(C.correlativo),10,'0') as correlativo,
                            to_char(C.fecha,'DD/MM/YYYY') as fecha,
                            C.concepto,
                            C.contabilizado,
                            C.id_persona,
                            CT.denominacion as denominacion_tipo
                          FROM modulo_base.comprobante as C, modulo_base.comprobante_tipo as CT
                          WHERE C.id='$id' AND C.tipo=CT.tipo");
    if(isset($return[0]) and $detalle){
      $return[0]["detalle_persona"]=$db->Execute("SELECT
                                                    P.id,
                                                    P.tipo,
                                                    (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
                                                    replace(P.denominacion,';',' ') as denominacion,
                                                    PT.id_cuenta_contable
                                                  FROM modulo_base.persona as P, modulo_base.persona_tipo as PT
                                                  WHERE P.id='".$return[0]["id_persona"]."' AND P.tipo=PT.tipo");

      $return[0]["detalle_presupuestario"]=$db->Execute("SELECT
                                                          *,
                                                          _formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
                                                          _formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria
                                                        FROM modulo_base.detalle_presupuestario AS DP, modulo_base.cuenta_presupuestaria as CP
                                                        WHERE DP.id_comprobante='$id' AND DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria");

      $return[0]["detalle_contable"]=$db->Execute("SELECT
                                                      *,
                                                      _formatear_cuenta_contable(DC.id_cuenta_contable) as cuenta_contable
                                                    FROM modulo_base.detalle_contable AS DC, modulo_base.cuenta_contable as CC
                                                    WHERE DC.id_comprobante='$id' AND DC.id_cuenta_contable=CC.id_cuenta_contable");

      $return[0]["detalle_item"]=$db->Execute("SELECT
                                                      I.id as id_item,
                                                      I.codigo,
                                                      I.item,
                                                      I.id_item_tipo,
                                                      I.id_cuenta_presupuestaria,
                                                      CTI.aplica_iva,
                                                      CTI.cantidad,
                                                      CTI.costo,
                                                      CTI.descuento,
                                                      CTI.id_unidad_medida,
                                                      UM.medida
                                                    FROM modulo_base.comprobante_tiene_item AS CTI, modulo_base.item as I, modulo_base.unidad_medida as UM
                                                    WHERE CTI.id_comprobante='$id' AND CTI.id_item=I.id AND CTI.id_unidad_medida=UM.id");

      $return[0]["detalle_requisicion_externa"]=$db->Execute("SELECT *
                                                              FROM modulo_base.comprobante_tiene_requisicion_externa AS CTRE
                                                              WHERE CTRE.id_comprobante='$id'");

      $return[0]["detalle_cargo"]=$db->Execute("SELECT
                                                      C.id as id_cargo,
                                                      lpad(text(C.id),3,'0') as correlativo,
                                                      C.denominacion as cargo,
                                                      C.formula,
                                                      C.iva,
                                                      C.id_cuenta_presupuestaria,
                                                      CTC.monto
                                                    FROM modulo_base.comprobante_tiene_cargo AS CTC, modulo_base.cargo as C
                                                    WHERE CTC.id_comprobante='$id' AND CTC.id_cargo=C.id");

      $return[0]["detalle_retencion"]=$db->Execute("SELECT
                                                      R.id as id_retencion,
                                                      lpad(text(R.id),3,'0') as correlativo,
                                                      R.denominacion as retencion,
                                                      R.formula,
                                                      R.id_cuenta_contable,
                                                      CTR.monto,
                                                      _formatear_cuenta_contable(R.id_cuenta_contable) as cuenta_contable,
                                                      CC.denominacion as denominacion_contable
                                                    FROM
                                                      modulo_base.comprobante_tiene_retencion AS CTR,
                                                      modulo_base.retencion as R,
                                                      modulo_base.cuenta_contable as CC
                                                    WHERE
                                                      CTR.id_comprobante='$id' AND
                                                      CTR.id_retencion=R.id AND
                                                      R.id_cuenta_contable=CC.id_cuenta_contable
                                                      ");

      $return[0]["detalle_extra"]=$db->Execute("SELECT dato, valor FROM modulo_base.comprobante_datos WHERE id_comprobante='$id'");

      $return[0]["detalle_comprobante_previo"]=$db->Execute("SELECT id_comprobante_previo FROM modulo_base.comprobante_previo WHERE id_comprobante='$id'");
      $return[0]["detalle_comprobante_previo_monto_pagado"]=$db->Execute("SELECT id_comprobante_previo, monto_pagado FROM modulo_base.comprobante_previo_monto_pagado WHERE id_comprobante='$id'");
      $return[0]["detalle_comprobante_posterior"]=$db->Execute("SELECT id_comprobante as id_comprobante_posterior FROM modulo_base.comprobante_previo WHERE id_comprobante_previo='$id'");
      $return[0]["detalle_comprobante_bancario"]=$db->Execute("SELECT
                                                                  CB.monto,
                                                                  CB.numero,
                                                                  CB.id_banco_cuenta,
                                                                  CB.id_banco_movimiento_tipo,
                                                                  BMT.codigo as operacion_codigo,
                                                                  BMT.operacion,
                                                                  BMT.denominacion as operacion_denominacion,
                                                                  BC.numero_cuenta,
                                                                  BC.denominacion as cuenta_denominacion,
                                                                  BC.id_cuenta_contable,
                                                                  BCT.denominacion as cuenta_tipo,
                                                                  B.banco,
                                                                  _formatear_cuenta_contable(BC.id_cuenta_contable) as cuenta_contable,
                                                                  CC.denominacion as denominacion_contable
                                                                FROM
                                                                  modulo_base.comprobante_bancario AS CB,
                                                                  modulo_base.banco_movimiento_tipo as BMT,
                                                                  modulo_base.banco_cuenta as BC,
                                                                  modulo_base.banco_cuenta_tipo as BCT,
                                                                  modulo_base.banco as B,
                                                                  modulo_base.cuenta_contable as CC
                                                                WHERE
                                                                  CB.id_comprobante='$id' AND
                                                                  CB.id_banco_movimiento_tipo=BMT.id AND
                                                                  CB.id_banco_cuenta=BC.id AND
                                                                  BC.id_banco_cuenta_tipo=BCT.id AND
                                                                  BC.id_banco=B.id AND
                                                                  BC.id_cuenta_contable=CC.id_cuenta_contable
                                                                  ");

      $return[0]["file"]=array();
      $path=SIGA::databasePath()."/comprobante/$id/";
      if(file_exists($path)){
        $dir=scandir($path);
        foreach($dir as $key => $value){
          if(!in_array($value,array(".",".."))){
            $return[0]["file"][]=$value;
          }
        }
      }
    }
    return $return;
  }

  public static function onGet_Archivo($access, $archivo){
    if(!$access) exit;
    $carpeta_base=SIGA::databasePath()."/comprobante/";
    $finfo = new finfo(FILEINFO_MIME);
    $type  = $finfo->file($carpeta_base.$archivo);
    header("Content-Type: $type");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: inline; filename='".basename($archivo)."'");
    readfile($carpeta_base.$archivo);
  }

  public static function onList($text,$start,$limit,$sort,$mostrar){
    $db=SIGA::DBController();




    $add="";
    //mostrar un mes específico
    if(isset($mostrar["mes"])){
      if($mostrar["mes"]!=="")
        $add.=" AND EXTRACT(MONTH FROM C.fecha)=".str_clear($mostrar["mes"]);
    }

    //mostrar una fecha específica
    if(isset($mostrar["fecha_inicio"]) and isset($mostrar["fecha_culminacion"])){
      if($mostrar["fecha_inicio"]!=="" and $mostrar["fecha_culminacion"]!=="")
        $add.=" AND C.fecha BETWEEN '".str_clear($mostrar["fecha_inicio"])."' AND '".str_clear($mostrar["fecha_culminacion"])."'";
    }

    //mostrar contabilizados o no
    if(isset($mostrar["contabilizado"])){
      if($mostrar["contabilizado"]!==""){
        if($mostrar["contabilizado"]===1 or $mostrar["contabilizado"]==='1' or $mostrar["contabilizado"]===true or $mostrar["contabilizado"]==='true' or $mostrar["contabilizado"]==='t')
          $add.=" AND C.contabilizado";
        else
          $add.=" AND NOT C.contabilizado";
      }
    }

    $add_banco_mov_columna="";
    $add_banco_mov_tabla="";
    $add_banco_mov_union="";
    $add_banco_mov_buscar="";
    //mostrar solo los tipos incluidos en la lista
    if(isset($mostrar["tipo"])){
      if($mostrar["tipo"]!==""){
        if(count($mostrar["tipo"])===1 and str_clear($mostrar["tipo"][0])==="MB"){
          $add.=" AND C.tipo IN ('MB','CA')";
        }
        else if(count($mostrar["tipo"])>0){
          $tmp="";
          for($i=0;$i<count($mostrar["tipo"]);$i++){
            $tipo[$i]=str_clear($mostrar["tipo"][$i]);
            $tmp.=" '".$mostrar["tipo"][$i]."'";
            if($i<count($mostrar["tipo"])-1)
              $tmp.=",";
          }
          $add.=" AND C.tipo IN ($tmp)";
        }
        if(count($mostrar["tipo"])===1 and str_clear($mostrar["tipo"][0])==="MB"){
          //si es mostrar MB (movimiento bancario), unir a la tabla comprobante bancario, banco_movimiento_tipo
          $add_banco_mov_columna="CB.numero, CB.monto, BMT.codigo as operacion_codigo, BMT.operacion,";
          $add_banco_mov_tabla="modulo_base.comprobante_bancario as CB, modulo_base.banco_movimiento_tipo as BMT,";
          $add_mb="";
          if(isset($mostrar["operacion_codigo"]))
            if($mostrar["operacion_codigo"]!==""){
              if(count($mostrar["operacion_codigo"])>0){
                $tmp="";
                for($i=0;$i<count($mostrar["operacion_codigo"]);$i++){
                  $tipo[$i]=str_clear($mostrar["operacion_codigo"][$i]);
                  $tmp.=" '".$mostrar["operacion_codigo"][$i]."'";
                  if($i<count($mostrar["operacion_codigo"])-1)
                    $tmp.=",";
                }
                $add_mb.=" AND BMT.codigo IN ($tmp)";
              }
            }
          $add_banco_mov_union="C.id=CB.id_comprobante AND CB.id_banco_movimiento_tipo=BMT.id $add_mb AND";
          if(isset($mostrar["id_banco_cuenta"]))
            $add_banco_mov_union.=" CB.id_banco_cuenta='".str_clear($mostrar["id_banco_cuenta"])."' AND";
          $add_banco_mov_buscar="CB.numero ILIKE '%$text%' OR";

          //enviar saldo anterior, debe estar definido fecha_inicio, si consulta por mes hallas la fecha anterior al mes
          $fecha_inicio="";
          if(isset($mostrar["fecha_inicio"]))
            $fecha_inicio=str_clear($mostrar["fecha_inicio"]);
          else if(isset($mostrar["mes"]))
            if($mostrar["mes"]!=="")
              $fecha_inicio=SIGA::data()."-".str_clear($mostrar["mes"])."-01";

          //si hay fecha de inicio y cuenta bancaria en especifico
          if($fecha_inicio!=="" and isset($mostrar["id_banco_cuenta"])){
            $return["saldo_previo"]=$db->Execute("select sum(case when BMT.operacion = 'D' then CB.monto else -CB.monto end) as monto
                                                  from
                                                    $add_banco_mov_tabla
                                                    modulo_base.comprobante as C
                                                  where
                                                    EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                                    $add_banco_mov_union
                                                    C.fecha < '$fecha_inicio'
                                                    ");
            $return["saldo_previo"]=is_numeric($return["saldo_previo"][0][0])?$return["saldo_previo"][0][0]:0;
          }
        }
      }
    }



    //mostrar los anulados
    if(isset($mostrar["anulado"])){
      if($mostrar["anulado"]!==""){
        $tmp="(select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0";
        if($mostrar["anulado"]===1 or $mostrar["anulado"]==='1' or $mostrar["anulado"]===true or $mostrar["anulado"]==='true' or $mostrar["anulado"]==='t')
          $add.=" AND $tmp";
        else
          $add.=" AND NOT ($tmp)";
      }
    }

    //mostrar asociados o no
    if(isset($mostrar["asociado"])){
      if($mostrar["asociado"]!==""){
        $tmp="(select count(*) from modulo_base.comprobante_previo as CP where C.id=CP.id_comprobante_previo)>0";
        if($mostrar["asociado"]===1 or $mostrar["asociado"]==='1' or $mostrar["asociado"]===true or $mostrar["asociado"]==='true' or $mostrar["asociado"]==='t')
          $add.=" AND $tmp";
        else
          $add.=" AND NOT ($tmp)";
      }
    }

    //mostrar una persona en específico
    if(isset($mostrar["id_persona"])){
      if($mostrar["id_persona"]!="")
        $add.=" AND C.id_persona=".str_clear($mostrar["id_persona"]);
    }

    //mostrar por tipo de persona (N=natural J=Juridica)
    if(isset($mostrar["tipo_persona"])){
      if($mostrar["tipo_persona"]==="N")
        $add.=" AND P.tipo='N'";
      else if($mostrar["tipo_persona"]==="J")
        $add.=" AND P.tipo='J'";
    }

    //mostrar nombre de la persona en el resultado
    $add_persona_columna="";
    $add_persona_tabla="";
    $add_persona_union="";
    $add_persona_buscar="";
    if(isset($mostrar["persona"])){
      if($mostrar["persona"]!==""){
        if($mostrar["persona"]===1 or $mostrar["persona"]==='1' or $mostrar["persona"]===true or $mostrar["persona"]==='true' or $mostrar["persona"]==='t'){
          $add_persona_columna="replace(P.denominacion,';',' ') as persona,";
          $add_persona_tabla="LEFT JOIN modulo_base.persona as P ON C.id_persona=P.id";
          $add_persona_union="";
          $add_persona_buscar="replace(P.denominacion,';',' ') ILIKE '%$text%' OR";
        }
      }
    }




    //Mostrar los comprobante incluidos en $id_comprobante
    if(isset($mostrar["id_comprobante"])){
      if($mostrar["id_comprobante"]!=="" and count($mostrar["id_comprobante"])>0){
        $tmp="";
        for($i=0;$i<count($mostrar["id_comprobante"]);$i++){
          $mostrar["id_comprobante"][$i]=str_clear($mostrar["id_comprobante"][$i]);
          $tmp.="'".$mostrar["id_comprobante"][$i]."'";
          if($i<count($mostrar["id_comprobante"])-1)
            $tmp.=",";
        }
        $add.=" OR C.id IN ($tmp)";
      }
    }

    $sql="SELECT
            C.id,
            C.tipo,
            lpad(text(C.correlativo),10,'0') as correlativo,
            to_char(C.fecha,'DD/MM/YYYY') as fecha,
            $add_persona_columna
            $add_banco_mov_columna
            C.concepto,
            C.contabilizado,
            case when (select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0 then 't' else 'f' end as anulado,
            CT.denominacion as tipo_denominacion
          FROM
            modulo_base.comprobante as C $add_persona_tabla,
            $add_banco_mov_tabla
            modulo_base.comprobante_tipo as CT
          WHERE
            $add_banco_mov_union
            C.tipo=CT.tipo AND
            EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
            (
              (
                C.tipo ILIKE '%$text%' OR
                lpad(text(C.correlativo),10,'0') LIKE '%$text%' OR
                C.tipo || '-' || lpad(text(C.correlativo),10,'0') LIKE '%$text%' OR
                C.concepto ILIKE '%$text%' OR
                $add_persona_buscar
                $add_banco_mov_buscar
                to_char(C.fecha,'DD/MM/YYYY') like '%$text%'
              )
              $add
            )";

    /*
    $sql="SELECT
            C.id,
            C.tipo,
            lpad(text(C.correlativo),10,'0') as correlativo,
            to_char(C.fecha,'DD/MM/YYYY') as fecha,
            $add_persona_columna
            $add_banco_mov_columna
            C.concepto,
            C.contabilizado,
            CT.denominacion as tipo_denominacion
          FROM
            modulo_base.comprobante as C,
            $add_persona_tabla
            $add_banco_mov_tabla
            modulo_base.comprobante_tipo as CT
          WHERE
            $add_persona_union
            $add_banco_mov_union
            C.tipo=CT.tipo AND
            EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
            (
              (
                C.tipo ILIKE '%$text%' OR
                lpad(text(C.correlativo),10,'0') LIKE '%$text%' OR
                C.concepto ILIKE '%$text%' OR
                $add_persona_buscar
                $add_banco_mov_buscar
                to_char(C.fecha,'DD/MM/YYYY') like '%$text%'
              )
              $add
            )";
    */

    //print_r($mostrar);
    //print_r($sql);
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_OP_pendiente($text,$start,$limit,$sort,$mostrar){
    $db=SIGA::DBController();


    $id_persona="";
    if(isset($mostrar["id_persona"]))
      $id_persona=$mostrar["id_persona"];

    $id="null";
    if(isset($mostrar["id"]))
      if($mostrar["id"]!=="")
        $id=$mostrar["id"];

    $sql="with consulta as (
            SELECT
              C.id,
              C.tipo,
              lpad(text(C.correlativo),10,'0') as correlativo,
              to_char(C.fecha,'DD/MM/YYYY') as fecha,
              C.concepto,
              (select sum(DC.monto) from modulo_base.detalle_contable as DC where C.id=DC.id_comprobante and DC.operacion='H') as monto,
              (
                SELECT sum(CP2.monto_pagado)
                FROM modulo_base.comprobante_previo_monto_pagado as CP2
                WHERE C.id=CP2.id_comprobante_previo
              ) as monto_pagado_acumulado
            FROM
              modulo_base.comprobante as C
                LEFT JOIN modulo_base.comprobante_datos as CD on CD.id_comprobante=C.id
            WHERE
              C.id_persona=$id_persona AND
              --EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
              C.tipo='OP' AND
              C.contabilizado AND
              NOT (CD.dato = 'pagado' AND CD.valor = 'true') AND
              NOT (select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0 --AND --no anulado
            )
          select
            *,
            (select CP3.monto_pagado from  modulo_base.comprobante_previo_monto_pagado as CP3 where id=CP3.id_comprobante_previo and id_comprobante=$id) as monto_pagado
          from
            consulta
          where
            monto<>monto_pagado_acumulado or
            monto_pagado_acumulado is null or
            id IN (select id_comprobante_previo from modulo_base.comprobante_previo where id_comprobante=$id)";
    //SELECT id_comprobante as id_comprobante_posterior FROM modulo_base.comprobante_previo WHERE id_comprobante_previo='$id'
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    //print sql_query_total($sql);
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=isset($return["total"][0][0])?$return["total"][0][0]:"";
    return $return;
  }


  public static function onList_OP_cheque($text,$start,$limit,$sort,$mostrar){
    $db=SIGA::DBController();


    $id="";
    if(isset($mostrar["id"]))
      $id=$mostrar["id"];

    $sql="SELECT
            C.id,
            C.tipo,
            lpad(text(C.correlativo),10,'0') as correlativo,
            to_char(C.fecha,'DD/MM/YYYY') as fecha,
            C.concepto,
            (select sum(DC.monto) from modulo_base.detalle_contable as DC where C.id=DC.id_comprobante and DC.operacion='H') as monto,
            (
              SELECT sum(CP2.monto_pagado)
              FROM modulo_base.comprobante_previo_monto_pagado as CP2
              WHERE C.id=CP2.id_comprobante_previo
            ) as monto_pagado_acumulado,
            CP.monto_pagado
          FROM
            modulo_base.comprobante as C,
            modulo_base.comprobante_previo_monto_pagado CP
          WHERE
            CP.id_comprobante=$id and
            CP.id_comprobante_previo=C.id
            ";
    //SELECT id_comprobante as id_comprobante_posterior FROM modulo_base.comprobante_previo WHERE id_comprobante_previo='$id'
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_OC_OS_OP($text,$start,$limit,$sort,$mostrar){
    $db=SIGA::DBController();

    $add="";

     //mostrar un mes específico
    if(isset($mostrar["mes"])){
      if($mostrar["mes"]!="")
        $add.=" AND EXTRACT(MONTH FROM C.fecha)=".str_clear($mostrar["mes"]);
    }

    //mostrar solo los tipos incluidos en la lista
    if(isset($mostrar["tipo"])){
      if($mostrar["tipo"]!=="" and count($mostrar["tipo"])>0){
        $tmp="";
        for($i=0;$i<count($mostrar["tipo"]);$i++){
          $tipo[$i]=str_clear($mostrar["tipo"][$i]);
          $tmp.=" '".$mostrar["tipo"][$i]."'";
          if($i<count($mostrar["tipo"])-1)
            $tmp.=",";
        }
        $add.=" AND C.tipo IN ($tmp)";
      }
    }

    $sql="SELECT
            C.id,
            C.tipo,
            lpad(text(C.correlativo),10,'0') as correlativo,
            to_char(C.fecha,'DD/MM/YYYY') as fecha,
            C.concepto,
            C.contabilizado,
            replace(P.denominacion,';',' ') as persona,
            case when (select count(*) from modulo_base.detalle_presupuestario as DP where DP.id_comprobante=C.id)>0 then 't' else 'f' end as detalle_presupuestario,
            case when (select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0 then 't' else 'f' end as anulado
          FROM
            modulo_base.comprobante as C,
            modulo_base.persona as P
          WHERE
            P.id=C.id_persona AND
            EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
            (
              C.tipo ILIKE '%$text%' OR
              lpad(text(C.correlativo),10,'0') LIKE '%$text%' OR
              C.concepto ILIKE '%$text%' OR
              replace(P.denominacion,';',' ') ILIKE '%$text%' OR
              to_char(C.fecha,'DD/MM/YYYY') like '%$text%'
            )
            $add";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onSet_Contabilizar($access, $id, $contabilizado){
    $db=SIGA::DBController();


    if($contabilizado===1 or $contabilizado==='1' or $contabilizado===true or $contabilizado==='true' or $contabilizado==='t')
      $contabilizado="t";
    else
      $contabilizado="f";

    //Modificar registro
    $result=$db->Update("modulo_base.comprobante",array("contabilizado"=>"'$contabilizado'"),"id='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }

  private static function sortDP($a,$b){
    return strcmp($a["operacion"],$b["operacion"]);
  }

  public static function onSave($access,
                                $id,
                                $tipo,
                                $fecha,
                                $concepto,
                                $contabilizado,
                                $id_persona,
                                $detalle){
    $db=SIGA::DBController();


    //usar str_clear para los valores dentro de $detalle, dado que esta información es enviada directamente desde el cliente sin limpiar.
    //y de no usarse pueden hacer inyección sql

    $anio=explode("-",$fecha)[0];
    if($anio!=SIGA::data())
      return array("success"=>false, "message"=>"Error. La fecha del comprobante no corresponde con el año de trabajo actual.");

    //si es cheque, verificar si no existe un cheque con el mismo numero en la cuenta bancaria
    if(isset($detalle["comprobante_bancario"])){
      $codigo_operacion=$db->Execute("SELECT codigo FROM modulo_base.banco_movimiento_tipo WHERE id='".str_clear($detalle["comprobante_bancario"]["id_banco_movimiento_tipo"])."'");
      $codigo_operacion=$codigo_operacion[0][0];

      //si es cheque con orden de pago o cheque directo, verificar si existe
      if($codigo_operacion=="CH" or $codigo_operacion=="PD"){
        $existe=$db->Execute("SELECT count(*) n
                              FROM modulo_base.comprobante_bancario
                              WHERE
                                id_banco_cuenta='".str_clear($detalle["comprobante_bancario"]["id_banco_cuenta"])."' AND
                                numero='".str_clear($detalle["comprobante_bancario"]["numero"])."' AND id_comprobante<>'$id'");

        if(isset($existe[0]["n"]) and $existe[0]["n"]>0)
          return array("success"=>false, "message"=>"Error. El cheque No ".str_clear($detalle["comprobante_bancario"]["numero"])." ya se encuentra registrado.");
      }
    }



    if(!$id_persona)
      $id_persona="null";
    if(!$contabilizado)
      $contabilizado="t";




    $db->Execute("BEGIN WORK");

    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      }
      $data=array("tipo"=>"'$tipo'",
                  "fecha"=>"'$fecha'",
                  "concepto"=>"'$concepto'",
                  "contabilizado"=>"'$contabilizado'",
                  "id_persona"=>"$id_persona"
                  );

      //Modificar registro
      $result=$db->Update("modulo_base.comprobante",$data,"id='$id'");
      //Si hay error al modificar
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      }

      $correlativo=self::onGet_Correlativo($tipo);
      if(!isset($correlativo[0][0])){
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"Error. No se puedo determinar el siguiente correlativo.");
      }
      $correlativo=$correlativo[0][0];



      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_base.comprobante(tipo,correlativo,fecha,concepto,contabilizado,id_persona)
                           VALUES('$tipo','$correlativo','$fecha','$concepto','$contabilizado',$id_persona) RETURNING id");

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
      $id=$result[0][0];
    }

    //ingresar los detalles
    //$detalle=json_decode($detalle,true);
    //DETALLES PRESUPUESTARIOS
    if(isset($detalle["presupuestario"])){
      //ordenar el arreglo DP, colocar primero las operaciones de aumento (AU, AP y luego el resto)
      usort($detalle["presupuestario"], array(__CLASS__,'sortDP'));

      //1)eliminar en la tabla los registros no presentes en $detalle["presupuestario"]
      $presente="";
      for($i=0;$i<count($detalle["presupuestario"]);$i++){
        $presente.="(".
                      str_clear($detalle["presupuestario"][$i]["id_accion_subespecifica"]).",".
                      "'".str_clear($detalle["presupuestario"][$i]["id_cuenta_presupuestaria"])."'".",".
                      "'".str_clear($detalle["presupuestario"][$i]["operacion"])."'".
                    ")";
        if($i<count($detalle["presupuestario"])-1)
          $presente.=",";
      }
      if(!$presente)
        $result=$db->Delete("modulo_base.detalle_presupuestario","id_comprobante=$id");
      else
        $result=$db->Delete("modulo_base.detalle_presupuestario","id_comprobante=$id AND
                            (id_accion_subespecifica,id_cuenta_presupuestaria,operacion) NOT IN ($presente)");
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }



      //2) Modificar o Ingresar los registros
      for($i=0;$i<count($detalle["presupuestario"]);$i++){
        //2.1 buscar si existe el registro
        $existe=$db->Execute("SELECT count(*)
                              FROM modulo_base.detalle_presupuestario
                              WHERE id_comprobante='$id' AND (id_accion_subespecifica,id_cuenta_presupuestaria,operacion)=".
                                  "(".
                                    str_clear($detalle["presupuestario"][$i]["id_accion_subespecifica"]).",".
                                    "'".str_clear($detalle["presupuestario"][$i]["id_cuenta_presupuestaria"])."'".",".
                                    "'".str_clear($detalle["presupuestario"][$i]["operacion"])."'".
                                  ")");
        if($existe[0][0]>0){//2.1.1 si existe, modificar
          $result=$db->Update("modulo_base.detalle_presupuestario",
                              array ("monto"=>"".str_clear($detalle["presupuestario"][$i]["monto"]).""),
                              "id_comprobante='$id' AND (id_accion_subespecifica,id_cuenta_presupuestaria,operacion)=".
                                  "(".
                                    str_clear($detalle["presupuestario"][$i]["id_accion_subespecifica"]).",".
                                    "'".str_clear($detalle["presupuestario"][$i]["id_cuenta_presupuestaria"])."'".",".
                                    "'".str_clear($detalle["presupuestario"][$i]["operacion"])."'".
                                  ")");
        }
        else{//2.1.2 si no, ingresar
          $result=$db->Insert("modulo_base.detalle_presupuestario",
                              array (
                                    "id_comprobante"=>"'$id'",
                                    "id_accion_subespecifica"=>"'".str_clear($detalle["presupuestario"][$i]["id_accion_subespecifica"])."'",
                                    "id_cuenta_presupuestaria"=>"'".str_clear($detalle["presupuestario"][$i]["id_cuenta_presupuestaria"])."'",
                                    "operacion"=>"'".str_clear($detalle["presupuestario"][$i]["operacion"])."'",
                                    "monto"=>"".str_clear($detalle["presupuestario"][$i]["monto"])."")
                                    );
        }
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }

    }//FIN DETALLES PRESUPUESTARIOS

    //DETALLES CONTABLES
    if(isset($detalle["contable"])){
      //1)eliminar en la tabla los registros no presentes en $detalle["contable"]
      $presente="";
      for($i=0;$i<count($detalle["contable"]);$i++){
        $presente.="(".
                      "'".str_clear($detalle["contable"][$i]["id_cuenta_contable"])."'".",".
                      "'".str_clear($detalle["contable"][$i]["operacion"])."'".
                    ")";
        if($i<count($detalle["contable"])-1)
          $presente.=",";
      }
      if(!$presente)
        $result=$db->Delete("modulo_base.detalle_contable","id_comprobante=$id");
      else
        $result=$db->Delete("modulo_base.detalle_contable","id_comprobante=$id AND
                            (id_cuenta_contable,operacion) NOT IN ($presente)");
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }

      //2) Modificar o Ingresar los registros
      for($i=0;$i<count($detalle["contable"]);$i++){
        //2.1 buscar si existe el registro
        $existe=$db->Execute("SELECT count(*)
                              FROM modulo_base.detalle_contable
                              WHERE id_comprobante='$id' AND (id_cuenta_contable,operacion)=".
                                  "(".
                                    "'".str_clear($detalle["contable"][$i]["id_cuenta_contable"])."'".",".
                                    "'".str_clear($detalle["contable"][$i]["operacion"])."'".
                                  ")");
        if($existe[0][0]>0){//2.1.1 si existe, modificar
          $result=$db->Update("modulo_base.detalle_contable",
                              array ("monto"=>"".str_clear($detalle["contable"][$i]["monto"]).""),
                              "id_comprobante='$id' AND (id_cuenta_contable,operacion)=".
                                  "(".
                                    "'".str_clear($detalle["contable"][$i]["id_cuenta_contable"])."'".",".
                                    "'".str_clear($detalle["contable"][$i]["operacion"])."'".
                                  ")");
        }
        else{//2.1.2 si no, ingresar
          $result=$db->Insert("modulo_base.detalle_contable",
                              array (
                                    "id_comprobante"=>"'$id'",
                                    "id_cuenta_contable"=>"'".str_clear($detalle["contable"][$i]["id_cuenta_contable"])."'",
                                    "operacion"=>"'".str_clear($detalle["contable"][$i]["operacion"])."'",
                                    "monto"=>"".str_clear($detalle["contable"][$i]["monto"])."")
                                    );
        }
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }




    }//FIN DETALLES CONTABLES

    //ITEM DEL COMPROBANTE
    //Item asociados al comprobante (orden de compra y orden de servicio) tipo=["OC","OS"]
    if(isset($detalle["item"])){
      $result=$db->Delete("modulo_base.comprobante_tiene_item","id_comprobante=$id");
      for($i=0;$i<count($detalle["item"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_tiene_item",
                              array (
                                    "id_comprobante"  =>  "'$id'",
                                    "id_item"         =>  "'".str_clear($detalle["item"][$i]["id_item"])."'",
                                    "id_unidad_medida"=>  "'".str_clear($detalle["item"][$i]["id_unidad_medida"])."'",
                                    "cantidad"        =>  "'".str_clear($detalle["item"][$i]["cantidad"])."'",
                                    "costo"           =>  "'".str_clear($detalle["item"][$i]["costo"])."'",
                                    "descuento"       =>  "'".($detalle["item"][$i]["descuento"])."'",
                                    "aplica_iva"      =>  "'".str_clear($detalle["item"][$i]["aplica_iva"])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN ITEM DEL COMPROBANTE

    //CARGOS DEL COMPROBANTE
    //Cargos asociados al comprobante (orden de compra y orden de servicio) tipo=["OC","OS"]
    if(isset($detalle["cargo"])){
      $result=$db->Delete("modulo_base.comprobante_tiene_cargo","id_comprobante=$id");
      for($i=0;$i<count($detalle["cargo"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_tiene_cargo",
                              array (
                                    "id_comprobante"  =>  "'$id'",
                                    "id_cargo"        =>  "'".str_clear($detalle["cargo"][$i]["id_cargo"])."'",
                                    "monto"           =>  "'".str_clear($detalle["cargo"][$i]["monto"])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN ITEM DEL COMPROBANTE

    //REQUISICIONES EXTERNAS ASOCIADAS
    if(isset($detalle["requisicion_externa"])){
      $result=$db->Delete("modulo_base.comprobante_tiene_requisicion_externa","id_comprobante=$id");
      for($i=0;$i<count($detalle["requisicion_externa"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_tiene_requisicion_externa",
                              array (
                                    "id_comprobante"          =>  "'$id'",
                                    "id_requisicion_externa"  =>  "'".str_clear($detalle["requisicion_externa"][$i]["id_requisicion_externa"])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN REQUISICIONES EXTERNAS ASOCIADAS

    //Guarda archivos adjuntos al comprobante
    //modulo pago por transferencia
    if(isset($detalle["file"])):
      $file=array();
      if($_FILES and count($_FILES)>0){
        foreach($_FILES as $key => $_file){
          for($i=0;$i<count($_file['tmp_name']);$i++){
            if($_file['tmp_name'][$i]=="") continue;
            $file[]=array("tmp_name"=>$_file['tmp_name'][$i],"name"=>$_file['name'][$i]);
          }
        }
      }
      //borrar archivos de la carpeta
      if($detalle["file"]=='f' or count($file)>0) {
        $path=SIGA::databasePath()."/comprobante/$id/";
        if(file_exists($path)){
          $dir=scandir($path);
          foreach($dir as $key => $value)
            if(!in_array($value,array(".","..")))
              unlink($path.$value);
          rmdir($path);
        }
      }

      $path=SIGA::databasePath()."/comprobante/";
      if(count($file)>0){
        if(!file_exists("$path"))       mkdir("$path",0755);
        if(!file_exists("$path/$id/"))  mkdir("$path/$id/",0755);
        for($i=0;$i<count($file);$i++)
          move_uploaded_file($file[$i]['tmp_name'], "$path/$id/".$file[$i]['name']);
      }
    endif;


    //INFORMACIÓN EXTRA EN EL COMPROBANTE
    //GUARDA EN LA TABLA modulo_base.comprobante_datos
    if(isset($detalle["extra"])){
      $result=$db->Delete("modulo_base.comprobante_datos","id_comprobante=$id");
      foreach($detalle["extra"] as $dato => $valor){
        $dato=str_clear($dato);
        $valor=str_clear($valor);
        $result=$db->Insert("modulo_base.comprobante_datos",
                            array (
                                  "id_comprobante"=>"'$id'",
                                  "dato"=>"'$dato'",
                                  "valor"=>"'$valor'")
                                  );

        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN INFORMACIÓN EXTRA EN EL COMPROBANTE

    if(isset($detalle["comprobante_previo_monto_pagado"]) and !isset($detalle["comprobante_previo"])){
      $detalle["comprobante_previo"]=array();
      for($i=0;$i<count($detalle["comprobante_previo_monto_pagado"]);$i++)
        $detalle["comprobante_previo"][$i]=$detalle["comprobante_previo_monto_pagado"][$i]["id_comprobante"];
    }


    //ASOCIACIÓN DEL COMPROBANTE CON OTROS (comprobante_previo)
    if(isset($detalle["comprobante_previo"])){
      $result=$db->Delete("modulo_base.comprobante_previo_monto_pagado","id_comprobante=$id");
      $result=$db->Delete("modulo_base.comprobante_previo","id_comprobante=$id");
      for($i=0;$i<count($detalle["comprobante_previo"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_previo",
                              array (
                                    "id_comprobante"        =>  "'$id'",
                                    "id_comprobante_previo" =>  "'".str_clear($detalle["comprobante_previo"][$i])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN ASOCIACIÓN DEL COMPROBANTE CON OTROS (comprobante_previo)

    //ASOCIACIÓN DEL COMPROBANTE CHEQUE CON ORDEN DE PAGO (comprobante_previo_monto_pagado)
    if(isset($detalle["comprobante_previo_monto_pagado"])){
      $result=$db->Delete("modulo_base.comprobante_previo_monto_pagado","id_comprobante=$id");
      for($i=0;$i<count($detalle["comprobante_previo_monto_pagado"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_previo_monto_pagado",
                              array (
                                    "id_comprobante"        =>  "'$id'",
                                    "id_comprobante_previo" =>  "'".str_clear($detalle["comprobante_previo_monto_pagado"][$i]["id_comprobante"])."'",
                                    "monto_pagado"          =>  "'".str_clear($detalle["comprobante_previo_monto_pagado"][$i]["monto_pagado"])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //ASOCIACIÓN DEL COMPROBANTE CHEQUE CON ORDEN DE PAGO (comprobante_previo_monto_pagado)



    //RETENCIONES ASOCIADAS
    if(isset($detalle["retencion"])){
      $result=$db->Delete("modulo_base.comprobante_tiene_retencion","id_comprobante=$id");
      for($i=0;$i<count($detalle["retencion"]);$i++){
        $result=$db->Insert("modulo_base.comprobante_tiene_retencion",
                              array (
                                    "id_comprobante"  =>  "'$id'",
                                    "id_retencion"    =>  "'".str_clear($detalle["retencion"][$i]["id_retencion"])."'",
                                    "monto"           =>  "'".str_clear($detalle["retencion"][$i]["monto"])."'"
                                    ));
        if(!$result){
          $mensajeDB=$db->GetMsgErrorClear();
          $db->Execute("ROLLBACK WORK");
          return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
        }
      }
    }
    //FIN RETENCIONES ASOCIADAS

    //COMPROBANTE BANCARIO (DEJAR ESTE PASO AL FINAL POR EL RETORNO DEL onSave EN CASO DE SER TRANSFERENCIA)
    if(isset($detalle["comprobante_bancario"])){
      if(isset($detalle["comprobante_bancario"]["transferencia"]))
        if($detalle["comprobante_bancario"]["transferencia"]!==""){//si es transferencia
          //buscar el id operacion ND para el principal (movimiento de origen)
          $id_ND=$db->Execute("select id from modulo_base.banco_movimiento_tipo where codigo='ND'");
          $id_ND=$id_ND[0][0];
          $detalle["comprobante_bancario"]["id_banco_movimiento_tipo"]=$id_ND;
        }

      //buscar si existe el comprobante bancario
      $result=$db->Execute("select count(*) from modulo_base.comprobante_bancario where id_comprobante=$id");
      if($result[0][0]==0){ //si es nuevo, insertar directamente
        $result=$db->Insert("modulo_base.comprobante_bancario",
                              array (
                                    "id_comprobante"           =>  "'$id'",
                                    "id_banco_cuenta"          =>  "'".str_clear($detalle["comprobante_bancario"]["id_banco_cuenta"])."'",
                                    "id_banco_movimiento_tipo" =>  "'".str_clear($detalle["comprobante_bancario"]["id_banco_movimiento_tipo"])."'",
                                    "numero"                   =>  "'".str_clear($detalle["comprobante_bancario"]["numero"])."'",
                                    "monto"                    =>  "'".str_clear($detalle["comprobante_bancario"]["monto"])."'"
                                    ));
      }
      else{//si existe, modificar el registros
        $result=$db->Update("modulo_base.comprobante_bancario",
                              array (
                                    "id_banco_cuenta"          =>  "'".str_clear($detalle["comprobante_bancario"]["id_banco_cuenta"])."'",
                                    "id_banco_movimiento_tipo" =>  "'".str_clear($detalle["comprobante_bancario"]["id_banco_movimiento_tipo"])."'",
                                    "numero"                   =>  "'".str_clear($detalle["comprobante_bancario"]["numero"])."'",
                                    "monto"                    =>  "'".str_clear($detalle["comprobante_bancario"]["monto"])."'"
                                    ),"id_comprobante=$id");
      }

      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }

      //si es transferencia, generar el otro comprobante y asociarlo
      if(isset($detalle["comprobante_bancario"]["transferencia"]))
        if($detalle["comprobante_bancario"]["transferencia"]!==""){
          //buscar el id operacion NC
          $id_NC=$db->Execute("select id from modulo_base.banco_movimiento_tipo where codigo='NC'");
          $id_NC=$id_NC[0][0];

          $detalle_2=array();
          $detalle_2["comprobante_bancario"]["transferencia"]="";
          $detalle_2["comprobante_bancario"]["id_banco_cuenta"]=$detalle["comprobante_bancario"]["transferencia"]["id_banco_cuenta"];
          $detalle_2["comprobante_bancario"]["id_banco_movimiento_tipo"]=$id_NC;
          $detalle_2["comprobante_bancario"]["numero"]=$detalle["comprobante_bancario"]["numero"];
          $detalle_2["comprobante_bancario"]["monto"]=$detalle["comprobante_bancario"]["monto"];
          $detalle_2["comprobante_previo"][0]=$id;

          return self::onSave($access,
                              "",
                              "MB",
                              $fecha,
                              str_clear($detalle["comprobante_bancario"]["transferencia"]["concepto"]),
                              $contabilizado,
                              $id_persona,
                              $detalle_2
                              );
        }
    }
    //FIN COMPROBANTE BANCARIO


    //CARGAR LOS ARCHIVOS ASOCIADOS AL COMPROBANTE
    //$_FILES


    //$db->Execute("ROLLBACK WORK");return array("success"=>false, "message"=>"Prueba para revertir (fue success)");



    //$db->Execute("ROLLBACK WORK");
    $db->Execute("COMMIT WORK");
    return array("success"=>true, "message"=>'Datos guardados con exito.',"id"=>"$id");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();

    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");

    $db->Execute("BEGIN WORK");
    //borrar detalles presupuestarios
    $result=$db->Delete("modulo_base.detalle_presupuestario","id_comprobante='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    //borrar detalles contables
    $result=$db->Delete("modulo_base.detalle_contable","id_comprobante='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    //borrar comprobante_bancario
    $result=$db->Delete("modulo_base.comprobante_bancario","id_comprobante='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    //borrar informacion extra
    $result=$db->Delete("modulo_base.comprobante_datos","id_comprobante='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    //borrar comprobante
    $result=$db->Delete("modulo_base.comprobante","id='$id'");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }

    $db->Execute("COMMIT WORK");
    return array("success"=>true, "message"=>'Registro eliminado con exito.',"id"=>"$id");
  }

  public static function onAnular($access, $id, $fecha, $acciones=""){
    $db=SIGA::DBController();

    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para anular comprobantes.");

    //buscar informacion del comprobante
    $comprobante=self::onGet($id);

    //verificar si el comprobante tiene sucesores, de ser el caso. no permitir la anulacion, a menos que ya se encuentre anulado (el sucesor)
    //para cada comprobante posterior
    $cantidad=0;
    for($i=0;$i<count($comprobante[0]["detalle_comprobante_posterior"]);$i++){
      $id_comprobante_posterior=$comprobante[0]["detalle_comprobante_posterior"][$i][0];
      //verificar si el comprobante se encuentra anulado
      $result=$db->Execute("select count(*)
                           from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2
                           where CP.id_comprobante_previo='$id_comprobante_posterior' and CP.id_comprobante=C2.id and C2.tipo='CA'");
      if($result[0][0]>0)
        $cantidad++;
    }
    if(count($comprobante[0]["detalle_comprobante_posterior"])!=$cantidad){
      return array("success"=>false, "message"=>'Error. No se pudo anular, el comprobante tiene comprobantes sucesores asociados.');
    }



    $id_previo=$id;
    $id="";
    $tipo="CA";
    $concepto="ANULACIÓN ".$comprobante[0]["denominacion_tipo"]." [".$comprobante[0]["tipo"]."]".$comprobante[0]["correlativo"]." DE FECHA ".$comprobante[0]["fecha"]." - ".$comprobante[0]["concepto"];
    $contabilizado="t";
    $id_persona=$comprobante[0]["id_persona"];
    $detalle=array();
    $detalle["presupuestario"]=array();
    $detalle["contable"]=array();

    //invertir detalle presupuestario
    for($i=0;$i<count($comprobante[0]["detalle_presupuestario"]);$i++){
      $detalle["presupuestario"][$i]=array(
                                       "id_accion_subespecifica"=>$comprobante[0]["detalle_presupuestario"][$i]["id_accion_subespecifica"],
                                       "id_cuenta_presupuestaria"=>$comprobante[0]["detalle_presupuestario"][$i]["id_cuenta_presupuestaria"],
                                       "operacion"=>$comprobante[0]["detalle_presupuestario"][$i]["operacion"],
                                       "monto"=>($comprobante[0]["detalle_presupuestario"][$i]["monto"]*(-1))
                                       );
    }
    //invertir detalle contable
    for($i=0;$i<count($comprobante[0]["detalle_contable"]);$i++){
      $detalle["contable"][$i]=array(
                                    "id_cuenta_contable"=>$comprobante[0]["detalle_contable"][$i]["id_cuenta_contable"],
                                    "operacion"=>(strtoupper($comprobante[0]["detalle_contable"][$i]["operacion"])=="D"?"H":"D"),
                                    "monto"=>$comprobante[0]["detalle_contable"][$i]["monto"]
                                    );
    }
    //invertir detalle bancario
    if(isset($comprobante[0]["detalle_comprobante_bancario"][0])){
      $id_NC=$db->Execute("select id from modulo_base.banco_movimiento_tipo where codigo='NC'");
      $id_NC=$id_NC[0][0];
      $detalle["comprobante_bancario"]=array("id_banco_cuenta"=>"".$comprobante[0]["detalle_comprobante_bancario"][0]["id_banco_cuenta"]."",
                                             "id_banco_movimiento_tipo"=>"$id_NC",
                                             "numero"=>$comprobante[0]["detalle_comprobante_bancario"][0]["numero"],
                                             "monto"=>$comprobante[0]["detalle_comprobante_bancario"][0]["monto"]
                                             );
    }


    $result_1=self::onSave($access,$id,$tipo,$fecha,$concepto,$contabilizado,$id_persona,$detalle);
    if($result_1["success"]){
      //asociar los dos comprobantes el anterior y el nuevo
      $result_2=$db->Insert("modulo_base.comprobante_previo",
                            array (
                                  "id_comprobante_previo"=>"'$id_previo'",
                                  "id_comprobante"=>$result_1["id"]
                                  ));

      if(!$result_2){
        $mensajeDB=$db->GetMsgErrorClear();
        return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }

      //segun el tipo de comprobante original hacer otras operaciones
      //en caso de Orden de pago, liberar las ordenes de compro o servicios asociadas a el
      //tipo=OP
      $accion=0;
      if(isset($acciones["comprobante_previo"])){
        switch($acciones["comprobante_previo"]){
          case "liberar":
            if($comprobante[0]["tipo"]=="OP" or $comprobante[0]["tipo"]=="MB")
              $accion=1;
          break;
          case "anular":
            if($comprobante[0]["tipo"]=="OP")
              $accion=2;
          break;
        }
      }

      switch($accion){
        case 1:
          $result_2=$db->Delete("modulo_base.comprobante_previo_monto_pagado","id_comprobante='$id_previo'");
          if(!$result_2){
            $mensajeDB=$db->GetMsgErrorClear();
            return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
          }
          $result_2=$db->Delete("modulo_base.comprobante_previo","id_comprobante='$id_previo'");
          if(!$result_2){
            $mensajeDB=$db->GetMsgErrorClear();
            return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
          }
          break;
        case 2:
          for($i=0;$i<count($comprobante[0]["detalle_comprobante_previo"]);$i++){
            $result_2=self::onAnular($access, $comprobante[0]["detalle_comprobante_previo"][$i]["id_comprobante_previo"], $fecha);
            if(!$result_2){
              $mensajeDB=$db->GetMsgErrorClear();
              return array("success"=>false, "message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
            }
          }
          break;
      }

    }
    return $result_1;
  }

}

?>
