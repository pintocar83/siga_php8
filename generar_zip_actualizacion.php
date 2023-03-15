<?php
//Desde 01/06/2022 al 03/10/2022
$files_history=[];

//Hasta 2022-10-03
$files_history[]=[
  "library/siga.js",
  "library/siga.class.php",
  "library/db.controller.php",
  "library/functions/remove_accent.php",
  "library/functions/sql_query_total.php",
  //"report/ejecucion.php",
  //"report/ejecucion_alcaldia_mejia.php",
  "index.php",
  "class/nomina.class.php",
  "class/nomina_periodo.class.php",
  "class/nomina_escala_salarial.class.php",
  "class/nomina_periodo_tipo.class.php",
  "class/cuenta_presupuestaria.class.php",
  "class/comprobante.class.php",
  "class/ficha.class.php",
  "class/formulacion.class.php",
  "class/cuenta_contable.class.php",
  "class/grupo_familiar_parentesco.class.php",

  "report/nomina_xls.php",
  "report/nomina_xls_v2.php",
  "report/nomina_formato_concepto_importar.php",
  "report/nomina_resumen_presupuestario_contable.php",

  "module/nomina/index.php",
  "module/nomina/main.css",
  "module/nomina/main.js",

  "module/ficha/index.php",
  "module/ficha/main.css",
  "module/ficha/main.js",

  "module/pago/index.php",
  "module/pago/main.css",
  "module/pago/main.js",
  
  "module/nomina_periodo_tipo/index.php",
  "module/cuenta_presupuestaria/index.php",
  "module/nomina_escala_salarial/index.php",
  "module/formulacion/index.php",

  "module/retencion/index.php",
  "module/retencion/main.css",
  "module/retencion/main.js",

  "module/cargo/index.php",
  "module/cargo/main.css",
  "module/cargo/main.js",

  "module/detalle_contable/main.js",
  "module/detalle_presupuestario/main.js",

  "module/sigafs/index.php",
  "module/sigafs/core/formulacion.js",
  "module/sigafs/core/formulacion.php",
  "module/sigafs/core/reporte_formulacion.js",
  "module/sigafs/core/reporte_formulacion.php",
  "module/sigafs/core/comprobante.js",
  "module/sigafs/core/comprobante.php",
  "module/sigafs/core/cargo.js",
  "module/sigafs/core/cargo.php",
  "module/sigafs/core/retencion.js",
  "module/sigafs/core/retencion.php",
  "module/sigafs/core/index.php",
  "module/sigafs/library/sigafs.js",

  "image/icon/icon-advertencia-amarillo.png",
  "image/icon/icon-advertencia-duplicado-amarillo.png",
  "image/menu/icon-cargo.png",
  "image/menu/icon-retencion.png",
];

//Hasta 2022-10-04
$files_history[]=[
  "module/nomina/index.php",
  "module/nomina/main.css",
  "module/nomina/main.js",
];

//Hasta 2022-11-03
$files_history[]=[
  "class/nomina.class.php",
  "report/nomina_xls_v2.php",
  "module/nomina/index.php",
  "module/nomina/main.css",
  "module/nomina/main.js",
];

//Hasta 2022-11-28
$files_history[]=[
  "module/sigafs/core/reporte_retencion.js",
  "module/sigafs/core/reporte_retencion.php",
  "report/retencion_emitida_xls.php"
];

//Hasta 2023-01-10
$files_history[]=[
  "class/nomina.class.php",
  "module/nomina/index.php",
  "module/nomina/main.js",
  "module/sigafs/core/cuenta_contable.js",
  "module/sigafs/core/cuenta_contable.php",
  "report/contabilidad_plan_cuentas_xls.php",
  "report/nomina_xls_v2.php",
  "report/nomina_listado_banco_xls_formato_c.php",
];

//Hasta 2023-02-13
$files_history[]=[
  "module/ficha/main.js",
  "report/ficha_cuenta_bancaria_xls.php",
];


$files=$files_history[count($files_history)-1];

$path=".";
$filename="actualizacion_archivos_".date("Ymd_His");


//si tiene la extension zip, comprimirlo
if(extension_loaded('zip')){
    $zip=new ZipArchive();
    if($zip->open("{$path}/{$filename}.zip", ZIPARCHIVE::CREATE)) {
      for($i=0; $i<count($files); $i++) { 
        $zip->addFile($files[$i]);
      }
      $zip->close();
    }
    print "Finish";
    exit;
}
print "Not Zip Extension";

?>