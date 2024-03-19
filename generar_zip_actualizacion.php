<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
/*
Obtener archivos modificados desde un commit especifico a la actualidad
git diff d276cd5d24bf2a939d70e60181b8fe8e3f446bd4..HEAD --name-only
*/
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

//Hasta 2023-04-20
$files_history[]=[
  "module/sigafs/core/reporte_orden_pago.php",
  "module/sigafs/core/reporte_orden_pago.js",
  "module/sigafs/core/orden_pago.js",
  "module/sigafs/core/orden_pago.php",
  "module/reporte_orden_pago/index.php",
  "module/reporte_orden_pago/main.js",
  "report/orden_pago_listado_xls.php",
  "report/ficha_listado_xls.php",
  "module/ficha/main.js",
];

//Hasta 2023-04-26
$files_history[]=[
  "module/sigafs/core/reporte_orden_pago.php",
  "module/sigafs/core/reporte_orden_pago.js",
  "module/reporte_orden_pago/index.php",
  "module/reporte_orden_pago/main.js",
  "report/presupuesto_movimientos_listado_xls.php",
];

//Hasta 2023-07-07
$files_history[]=[
  "library/siga.class.php",
  "module/factura/main.js",
  "module/pago/form.php",
  "module/pago/main.js",
  "module/sigafs/core/factura.js",
  "module/sigafs/core/factura.php",
  "module/sigafs/core/reporte_ejecucion.js",
  "module/sigafs/core/reporte_ejecucion.php",
  "report/ejecucion_alcaldia_mejia.php",
  "report/orden_compra.php",
  "report/orden_pago.php",
  "report/requisicion_externa.php",
  "report/contabilidad_balance_general_b.php",
  "report/retencion_emitida_xls.php",
];

//Hasta 2023-12-08
$files_history[]=[
  "class/comprobante.class.php",
  "class/nomina.class.php",
  "class/ficha.class.php",
  "class/nomina_extension_rrhh.class.php",
  "class/nomina_extension_rrhh_hoja.class.php",
  "class/persona.class.php",
  "database/202310_create_tables_nomina_extension_rrhh.sql",
  "database/bd_comandos.txt",
  "database/database_data_inicial.sql",
  "image/menu/icon-nomina_extension_rrhh.png",
  "index.php",
  "library/ag-grid/ag-grid-enterprise.min.js",
  "library/phpexcel/PHPExcel/NamedRange.php",
  "library/siga.class.php",
  "library/siga.js",
  "module/anio_trabajo/index.php",
  "module/nomina/index.php",
  "module/nomina/main.js",
  "module/nomina_extension_rrhh/hoja/index.php",
  "module/nomina_extension_rrhh/hoja/main.css",
  "module/nomina_extension_rrhh/hoja/main.js",
  "module/nomina_extension_rrhh/image/icon-administrar_hoja.png",
  "module/nomina_extension_rrhh/image/icon-seleccionar.png",
  "module/nomina_extension_rrhh/image/icon-xls.png",
  "module/nomina_extension_rrhh/index.php",
  "module/nomina_extension_rrhh/main.css",
  "module/nomina_extension_rrhh/main.js",
  "module/nomina_periodo/main.js",
  "module/pago/form.php",
  "module/pago/main.js",
  "module/persona/index.php",
  "module/sigafs/core/proveedor.js",
  "module/sigafs/core/proveedor.php",
  "report/cheque.php",
  "report/contabilidad_balance_general_c.php",
  "report/nomina_concepto_x_mes.php",
  "report/orden_pago.php",
  "report/pago.php",
  "report/presupuesto_movimientos_listado_xls.php",
  "report/template/pdf_reporte_1.class.php",
  "module/sigafs/core/comprobante_retencion.js",
];

//Hasta 2024-01-22
$files_history[]=[
  "class/banco_cuenta.class.php",
  "class/comprobante.class.php",
  "class/persona.class.php",
  "module/pago/main.js",
  "module/persona/index.php",
  "module/sigafs/core/beneficiario.js",
  "module/sigafs/core/beneficiario.php",
];

//Hasta 2024-01-23
$files_history[]=[
  "class/banco_movimiento_tipo.class.php",
  "module/comprobante/index.php",
];

$files=array_values(array_unique($files_history[count($files_history)-1], SORT_REGULAR));

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