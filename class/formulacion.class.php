<?php
class formulacion{
  public static function onGet( $anio,
                                $tipo,
                                $id_accion_subespecifica){
    $db=SIGA::DBController();

    $sql="SELECT
            F.*,
            FD.*,
            CP.denominacion,
            CP.padre,
            _formatear_cuenta_presupuestaria(FD.id_cuenta_presupuestaria) as cuenta_presupuestaria,
            FD.monto[1] as monto_ene,
            FD.monto[2] as monto_feb,
            FD.monto[3] as monto_mar,
            FD.monto[4] as monto_abr,
            FD.monto[5] as monto_may,
            FD.monto[6] as monto_jun,
            FD.monto[7] as monto_jul,
            FD.monto[8] as monto_ago,
            FD.monto[9] as monto_sep,
            FD.monto[10] as monto_oct,
            FD.monto[11] as monto_nov,
            FD.monto[12] as monto_dic,
            (FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
            F.id_accion_subespecifica='$id_accion_subespecifica'
          ORDER BY
            FD.id_cuenta_presupuestaria";
    $return=$db->Execute($sql);
    if(isset($return[0]["id_comprobante_apertura"])){
      $tmp=$db->Execute("select tipo||lpad(text(correlativo),10,'0') from modulo_base.comprobante where id=".$return[0]["id_comprobante_apertura"]);
      $return[0]["comprobante_apertura"]=$tmp[0][0];
    }
    return $return;
  }



  public static function onSave($access,
                                $anio,
                                $tipo,
                                $id_accion_subespecifica,
                                $data,
                                $asignar){
    $db=SIGA::DBController();

    if(!($access=="rw"))
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");

    //buscar si existe la formulacion
    $formulacion=$db->Execute( "SELECT id, id_comprobante_apertura
                                FROM modulo_base.formulacion
                                WHERE
                                  anio='$anio' AND
                                  tipo='$tipo' AND
                                  id_accion_subespecifica='$id_accion_subespecifica'");
    //si no existe, insertarlo
    if(!isset($formulacion[0][0])){
      $formulacion=$db->Execute( "INSERT INTO modulo_base.formulacion(anio,tipo,id_accion_subespecifica)
                                  VALUES('$anio','$tipo','$id_accion_subespecifica') RETURNING id");
      if(!isset($formulacion[0][0]))
        return array("success"=>false,"message"=>"Error. No pudo obtener el identificador de la formulación.");
    }
    $id_formulacion=$formulacion[0][0];

    $db->Execute("BEGIN WORK");

    //borrar el detalle anterior
    $result=$db->Delete("modulo_base.formulacion_detalle","id_formulacion=$id_formulacion");
    if(!$result){
      $mensajeDB=$db->GetMsgErrorClear();
      $db->Execute("ROLLBACK WORK");
      return array("success"=>false,"message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
    }
    //actualizar detalle
    $data=json_decode($data,true);
    for($i=0;$i<count($data);$i++){
      $result=$db->Insert("modulo_base.formulacion_detalle",
                          array(
                                "id_formulacion"=>"'$id_formulacion'",
                                "id_cuenta_presupuestaria"=>"'".str_clear($data[$i]["id_cuenta_presupuestaria"])."'",
                                "monto_real"=>"'".str_clear($data[$i]["monto_real"])."'",
                                "monto_estimado"=>"'".str_clear($data[$i]["monto_estimado"])."'",
                                "monto"=>"".str_clear($data[$i]["monto"]).""
                                ));
      if(!$result){
        $mensajeDB=$db->GetMsgErrorClear();
        $db->Execute("ROLLBACK WORK");
        return array("success"=>false,"message"=>"$mensajeDB", "messageDB"=>"$mensajeDB");
      }
    }

    $db->Execute("COMMIT WORK");

    //si asignar
    if($asignar){
      include_once("../../class/estructura_presupuestaria.class.php");
      include_once("../../class/comprobante.class.php");

      //buscar el codigo del proyecto
      $estructura_presupuestaria=estructura_presupuestaria::onGet_Codigo($id_accion_subespecifica);
      if(isset($estructura_presupuestaria[0][0]))
        $estructura_presupuestaria=$estructura_presupuestaria[0][0];



      //segun el tipo F=formulacion R=reformulacion
      $tipo_nombre=($tipo[0]=="F"?"FORMULACIÓN":"REFORMULACIÓN");

      $comprobante_id="";
      if(isset($formulacion[0]["id_comprobante_apertura"]))
        $comprobante_id=$formulacion[0]["id_comprobante_apertura"];
      $comprobante_tipo="AP";
      //$comprobante_fecha=date("Y-m-d");
      $comprobante_fecha=SIGA::data()."-01-01";
      $comprobante_concepto="ASIGNACIÓN DE $tipo_nombre DE PRESUPUESTO DE GASTOS $estructura_presupuestaria.";
      $comprobante_contabilizado="t";
      $comprobante_id_persona="null";

      $comprobante_detalle=array();
      $comprobante_detalle["presupuestario"]=array();
      for($i=0;$i<count($data);$i++){
        $comprobante_detalle["presupuestario"][$i]=array(
                                              "id_accion_subespecifica"=>"$id_accion_subespecifica",
                                              "id_cuenta_presupuestaria"=>"".str_clear($data[$i]["id_cuenta_presupuestaria"])."",
                                              "operacion"=>"AP",
                                              "monto"=>"(SELECT SUM(valores) FROM UNNEST(".str_clear($data[$i]["monto"]).") valores)"
                                            );
      }

      $result=comprobante::onSave($access,
                                  $comprobante_id,
                                  $comprobante_tipo,
                                  $comprobante_fecha,
                                  $comprobante_concepto,
                                  $comprobante_contabilizado,
                                  $comprobante_id_persona,
                                  $comprobante_detalle);
      if($result["success"]){
        //si es primera vez que se asigna $id=""
        //actualizar id_comprobante_apertura en la tabla modulo_base.formulacion
        $result=$db->Update("modulo_base.formulacion",array("id_comprobante_apertura"=>"'".$result["id"]."'"),"id='$id_formulacion'");
        if(!$result)
          return array("success"=>false,"message"=>"Error. No pudo asociar la formulacion al comprobante generado.");
      }
      else{
        return array("success"=>false,"message"=>$result["message"]);
      }
    }

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
}

?>