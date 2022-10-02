<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
error_reporting(0);
ini_set('display_errors', 'Off');
set_time_limit(-1);
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../class/nomina.class.php");
include_once("../library/phpexcel/PHPExcel.php");


SIGA::$DBMode=PGSQL_ASSOC;
$db=SIGA::DBController();


$id_nomina=SIGA::paramGet("id_nomina");
$id_periodo=SIGA::paramGet("id_periodo");

$periodo=$db->Execute("SELECT fecha_culminacion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");
$fecha_culminacion=$periodo[0]["fecha_culminacion"];


$nomina=$db->Execute("SELECT id, codigo, nomina FROM modulo_nomina.nomina WHERE id in ($id_nomina)");




$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);

for($i=0; $i<count($nomina); $i++){
  $ln=1;
  $activeSheet = $excel->createSheet($i);
  $activeSheet->setTitle($nomina[$i]["codigo"]);
  //buscar los conceptos del periodo
  $sql="SELECT C.id, C.codigo, C.concepto, C.identificador, C.tipo  FROM modulo_nomina.concepto_periodo CP, modulo_nomina.concepto C WHERE C.id=CP.id_concepto AND CP.id_periodo='$id_periodo' AND CP.id_nomina = '".$nomina[$i]["id"]."'";
  $concepto=$db->Execute($sql);
  for($c=0; $c<count($concepto); $c++){
    //buscar definicion (formula) del concepto
    $sql="
      SELECT definicion
      FROM modulo_nomina.concepto_formula
      WHERE fecha<='$fecha_culminacion' AND id_concepto=".$concepto[$c]["id"]."
      ORDER BY fecha DESC
      LIMIT 1
    ";
    $tmp=$db->Execute($sql);
    $concepto[$c]["definicion"]="";
    if(isset($tmp[0]["definicion"]) and $tmp[0]["definicion"])
      $concepto[$c]["definicion"]=$tmp[0]["definicion"];
  }

  $activeSheet->setCellValueExplicit("A$ln","CÉDULA",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln","NOMBRES Y APELLIDOS",PHPExcel_Cell_DataType::TYPE_STRING);
  $col_index = 2;//Comenzar en la columna C
  for($c=0; $c<count($concepto); $c++){
    if(nomina::es_formula($concepto[$c]["definicion"]))
      continue;
    $col_letter = PHPExcel_Cell::stringFromColumnIndex($col_index);

    $activeSheet->setCellValueExplicit("{$col_letter}{$ln}",$concepto[$c]["codigo"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->getComment("{$col_letter}{$ln}")->getText()->createTextRun("CONCEPTO ".$concepto[$c]["codigo"].": ".$concepto[$c]["concepto"])->getFont()->setBold(true);
    $activeSheet->getComment("{$col_letter}{$ln}")->setWidth("400px");

    $col_index++;
  }
  $activeSheet->getStyle("A{$ln}:{$col_letter}{$ln}")->getFont()->setBold(true);

  $ln++;


  //buscar las personas en la nomina y periodo
  $sql="
    SELECT DISTINCT
      FC.id_ficha,
      P.identificacion_tipo as nacionalidad,
      P.identificacion_numero as cedula,
      replace(P.denominacion,';',' ') as nombres_apellidos
    FROM
      modulo_base.persona as P,
      modulo_nomina.ficha AS F,
      modulo_nomina.ficha_concepto as FC
    WHERE
      P.tipo='N' AND
      P.id=F.id_persona AND
      F.id=FC.id_ficha AND
      FC.id_periodo=$id_periodo AND
      FC.id_nomina='".$nomina[$i]["id"]."'
    ORDER BY
      cedula
  ";
  //print $sql;
  $persona=$db->Execute($sql);

  for($p=0; $p<count($persona); $p++){
    $activeSheet->setCellValueExplicit("A$ln",$persona[$p]["nacionalidad"].$persona[$p]["cedula"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("B$ln",$persona[$p]["nombres_apellidos"],PHPExcel_Cell_DataType::TYPE_STRING);

    //colocar el valor de cada concepto en la celda
    $col_index = 2;//Comenzar en la columna C
    for($c=0; $c<count($concepto); $c++){
      if(nomina::es_formula($concepto[$c]["definicion"]))
        continue;
      $col_letter = PHPExcel_Cell::stringFromColumnIndex($col_index);

      $sql="SELECT valor FROM modulo_nomina.ficha_concepto WHERE id_ficha='".$persona[$p]["id_ficha"]."' AND id_periodo='$id_periodo' AND id_nomina='".$nomina[$i]["id"]."' AND id_concepto='".$concepto[$c]["id"]."'";
      //print $sql;exit;
      $tmp=$db->Execute($sql);
      $valor="";
      if(isset($tmp[0]["valor"]) AND $tmp[0]["valor"]>0){
        $valor=$tmp[0]["valor"];
        $activeSheet->setCellValueExplicit("{$col_letter}{$ln}",number_format($valor,2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $activeSheet->getStyle("{$col_letter}{$ln}")->getNumberFormat()->setFormatCode('#,##0.00');
      }


      $col_index++;
    }



    $ln++;
  }

  $activeSheet->getColumnDimension("A")->setAutoSize(true);
  $activeSheet->getColumnDimension("B")->setAutoSize(true);

  $activeSheet->freezePane("A2");
}


$excel->setActiveSheetIndex(0);


$nombre_reporte="formato_importar_conceptos";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.str_replace(" ", "_", $nombre_reporte).'.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);

$writer->save('php://output');

?>