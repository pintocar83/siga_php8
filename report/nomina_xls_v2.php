<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_time_limit(-1);

include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/str_clear.php");
include_once("../library/functions/sql_query_total.php");
include_once("../library/functions/column_hash.php");
include_once("../library/fpdf/1.7/rotation.php");
include_once("../library/phpexcel/PHPExcel.php");
//include_once("../class/nomina_v2.class.php");
include_once("../class/nomina.class.php");


$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");



$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}
//$id_periodo=$id_periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));

$filtro_ficha_id=SIGA::paramGet("filtro_ficha_id");
if($filtro_ficha_id){
		$filtro_ficha_id=explode(",",$filtro_ficha_id);	
}

/*

$periodo=$db->Execute("SELECT codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");
$nomina_concepto=$db->Execute("SELECT distinct CP.id_concepto, C.concepto, C.tipo, C.orden
															 FROM modulo_nomina.concepto_periodo as CP, modulo_nomina.concepto as C
															 WHERE C.id=CP.id_concepto AND CP.id_periodo=$id_periodo AND (C.tipo<>'' OR C.identificador='SUELDO_NORMAL')
															 ORDER BY C.orden");


*/

$text="";
$by="";


global $periodo;
$periodo=array();
for($i=0; $i<count($id_periodo); $i++){
	$periodo[$i]=$db->Execute("SELECT P.codigo, P.fecha_inicio, P.fecha_culminacion, P.descripcion, PT.denominacion as nomina_tipo FROM modulo_nomina.periodo as P, modulo_nomina.periodo_tipo as PT WHERE PT.tipo=P.tipo and P.id='".str_clear($id_periodo[$i])."'");
	if(!isset($periodo[$i][0])) exit;
	$periodo[$i]=$periodo[$i][0];
	$periodo[$i]["concepto"]=$db->Execute("SELECT distinct CP.id_concepto, C.concepto, C.tipo, C.orden, C.identificador
															 FROM modulo_nomina.concepto_periodo as CP, modulo_nomina.concepto as C
															 WHERE C.id=CP.id_concepto AND CP.id_periodo=".str_clear($id_periodo[$i])." AND (C.tipo<>'' OR C.identificador='SUELDO_NORMAL')
															 ORDER BY C.orden");

	$periodo[$i]["ficha"]=nomina::onListFichaPeriodo(str_clear(implode(",", $id_nomina)),str_clear($id_periodo[$i]),0,'ALL',["id_ficha"=>$filtro_ficha_id])["result"];

	$periodo[$i]["nota"]=$db->Execute("SELECT nota FROM modulo_nomina.periodo_nota WHERE id_nomina IN (".str_clear(implode(",", $id_nomina)).") and id_periodo=".str_clear($id_periodo[$i]));
}






//print_r($periodo);exit;



//exit;
//print_r($periodo);exit;
global $periodo, $ln, $activeSheet, $columna, $total_columna, $columna_base, $suma_columna, $suma_columna_unidad, $n_columna, $ultima_columna, $nomina_anterior, $concepto, $ancho, $t_n, $t_cedula, $t_nombre, $t_cargo, $t_antiguedad, $t_neto, $t_asignaciones, $t_deducciones, $t_separacion, $t_firma, $t_col, $p, $border;
global $sw_deducciones, $sw_aportes;
global $config;



//$config        = SIGA::databaseConfig(array("organismo","organismo_rif","organismo_direccion","nomina/report/nomina"));
$config_report = isset($config["nomina/report/nomina"])?json_decode($config["nomina/report/nomina"],true):NULL;
//configuracon por defecto del reporte
$config=array(
	"aporte_patronal"   => true, //false=no agrega el aporte patronal a la hoja de calculo, true||"visible" = visible, "hide"=ocultar columnas del aporte patronal
	"total_unidad"      => false,
	"pagina"            => 41, //1=carta, 5=legal, 41=Oficio
	"fijar"             => "D6",
	"dividir_impresion" => false//para dividir la impresion en dos partes Asignaciones y Deducciones
);

if($config_report){
	if(isset($config_report["aporte_patronal"]))	$config["aporte_patronal"]   = $config_report["aporte_patronal"];
	if(isset($config_report["total_unidad"]))	    $config["total_unidad"]      = $config_report["total_unidad"];
	if(isset($config_report["pagina"]))	            $config["pagina"]            = $config_report["pagina"];
	if(isset($config_report["fijar"]))	            $config["fijar"]             = $config_report["fijar"];
	if(isset($config_report["dividir_impresion"]))	$config["dividir_impresion"] = $config_report["dividir_impresion"];
}



function Cabecera(){
	global $config, $periodo, $ln, $activeSheet, $columna, $n_columna, $ultima_columna, $concepto, $ancho, $t_n, $t_cedula, $t_nombre, $t_cargo, $t_antiguedad, $t_neto, $t_asignaciones, $t_deducciones, $t_separacion, $t_firma, $t_col, $p, $border;
	global $sw_deducciones, $sw_aportes;
	global $columna_base;
	
	$n_columna=0;
	$id_sueldo_normal="";
	$id_tiempo_servicio_otro="";	

	$activeSheet->getRowDimension($ln)->setRowHeight(120);

	$activeSheet->setCellValueExplicit("A$ln","Nº",PHPExcel_Cell_DataType::TYPE_STRING);	
	$activeSheet->setCellValueExplicit("B$ln","CÉDULA",PHPExcel_Cell_DataType::TYPE_STRING);	
	$activeSheet->setCellValueExplicit("C$ln","NOMBRES Y APELLIDO",PHPExcel_Cell_DataType::TYPE_STRING);	
	$activeSheet->setCellValueExplicit("D$ln","CARGO",PHPExcel_Cell_DataType::TYPE_STRING);

	$activeSheet->setCellValueExplicit("E$ln","GENERO",PHPExcel_Cell_DataType::TYPE_STRING);	
	$activeSheet->setCellValueExplicit("F$ln","INGRESO",PHPExcel_Cell_DataType::TYPE_STRING);	

	$activeSheet->setCellValueExplicit("G$ln","AÑOS",PHPExcel_Cell_DataType::TYPE_STRING);	
	$activeSheet->setCellValueExplicit("H$ln","DÍAS",PHPExcel_Cell_DataType::TYPE_STRING);	

	//$columna_base=6;
	$n_columna=0;
	for($i=0;$i<count($concepto);$i++){
		if($concepto[$i]["tipo"]=="A" or $concepto[$i]["tipo"]=="RD" or $concepto[$i]["tipo"]=="_"){
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln",$concepto[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);			
			$columna[$concepto[$i]["id_concepto"]]=$n_columna;
			$n_columna++;
		}
		else if($concepto[$i]["tipo"]==""){
			if($concepto[$i]["concepto"]=="SUELDO_MENSUAL"){
				$id_sueldo_normal=$concepto[$i]["id_concepto"];
			}
			else if($concepto[$i]["identificador"]=="TIEMPO_SERVICIO_OTRO"){
				$id_tiempo_servicio_otro=$concepto[$i]["id_concepto"];
			}
		}
	}
	
	//columa del sueldo normal
	if($id_sueldo_normal){
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln","SUELDO NORMAL",PHPExcel_Cell_DataType::TYPE_STRING);	
		$columna[$id_sueldo_normal]=$n_columna;
		$n_columna++;
	}			
	
	//columa total asignaciones
	$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln","ASIGNACIONES",PHPExcel_Cell_DataType::TYPE_STRING);
	$columna["A"]=$n_columna;
	$n_columna++;
	
	//columna de deducciones
	$sw_deducciones=false;
	for($i=0;$i<count($concepto);$i++)
		if($concepto[$i]["tipo"]=="D" or $concepto[$i]["tipo"]=="AP" or $concepto[$i]["tipo"]=="RA"){
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln",$concepto[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);
			$columna[$concepto[$i]["id_concepto"]]=$n_columna;
			$n_columna++;
			$sw_deducciones=true;
		}
	
	if($sw_deducciones){
		//columa total deducciones
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln","DEDUCCIONES",PHPExcel_Cell_DataType::TYPE_STRING);
		$columna["D"]=$n_columna;
		$n_columna++;
		
		//columa total
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln","TOTAL",PHPExcel_Cell_DataType::TYPE_STRING);
		$columna["N"]=$n_columna;
		$n_columna++;
	}		
	
	//columna de aporte patron
	if($config["aporte_patronal"]){
		$sw_aportes=false;
		for($i=0;$i<count($concepto);$i++)
			if($concepto[$i]["tipo"]=="AP"){
				$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln",$concepto[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);
				$columna["AP_".$concepto[$i]["id_concepto"]]=$n_columna;
				$n_columna++;
				$sw_aportes=true;
			}
		
		//columa total
		if($sw_aportes){
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$n_columna)."$ln","TOTAL APORTES",PHPExcel_Cell_DataType::TYPE_STRING);
			$columna["AP"]=$n_columna;
			$n_columna++;
		}
	}
	

	$ultima_columna=column_hash($columna_base+$n_columna-1);
	$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFont()->setBold(true);
	$activeSheet->getStyle("E$ln:$ultima_columna$ln")->getAlignment()->setTextRotation(90);
	$activeSheet->getStyle("E$ln:$ultima_columna$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getAlignment()->setWrapText(true);
	$activeSheet->getStyle("A$ln:$ultima_columna$ln")->applyFromArray($border);

	$activeSheet->mergeCells("A2:${ultima_columna}2");
	$activeSheet->getStyle("A2")->getFont()->setBold(true);
	$activeSheet->getStyle("A2")->getFont()->setSize(14);
	$activeSheet->setCellValueExplicit("A2","NÓMINA:   ".$periodo[$p]["nomina_tipo"].".",PHPExcel_Cell_DataType::TYPE_STRING);

	$activeSheet->mergeCells("A3:${ultima_columna}3");
	$activeSheet->getStyle("A3")->getFont()->setBold(true);
	$activeSheet->getStyle("A3")->getFont()->setSize(14);
	$activeSheet->setCellValueExplicit("A3","PERIODO: ".$periodo[$p]["descripcion"].".",PHPExcel_Cell_DataType::TYPE_STRING);



	$ln++;
}

function TotalNomina(){
	global $activeSheet, $ln, $n_columna, $suma_columna, $total_columna, $columna_base, $ultima_columna, $border, $nomina_anterior;
	$activeSheet->mergeCells("A$ln:H$ln");
	$activeSheet->setCellValueExplicit("A$ln","TOTAL: $nomina_anterior",PHPExcel_Cell_DataType::TYPE_STRING);

	for($k=0;$k<$n_columna;$k++){
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$k)."$ln",number_format($suma_columna[$k],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$total_columna[$k]+=$suma_columna[$k];
	}

	$activeSheet->getStyle("A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFont()->setBold(true);
	$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFont()->setSize(9);
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getNumberFormat()->setFormatCode('#,##0.00');
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->applyFromArray($border);
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');

	for($k=0;$k<$n_columna;$k++)
		$suma_columna[$k]=0;
	$ln++;
}


function TotalUnidad(){
	global $config, $activeSheet, $ln, $n_columna, $suma_columna_unidad, $total_columna, $columna_base, $ultima_columna, $border;
	if(!$config["total_unidad"]) return;

	for($k=0;$k<$n_columna;$k++){
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$k)."$ln",number_format($suma_columna_unidad[$k],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
	}

	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getNumberFormat()->setFormatCode('#,##0.00');
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getFont()->setBold(true);
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getFont()->setSize(9);
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->applyFromArray($border);
	$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');

	for($k=0;$k<$n_columna;$k++)
		$suma_columna_unidad[$k]=0;
	$ln++;
	$activeSheet->getRowDimension($ln)->setRowHeight(5);
	$ln++;
}




$nombre_reporte="NOMINA";
$columna_base=8;
$ultima_columna="A";

$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);


for($p=0;$p<count($periodo);$p++):
	$ficha=$periodo[$p]["ficha"];
//print_r($ficha);exit;
	$concepto=$periodo[$p]["concepto"];

	$activeSheet = $excel->createSheet($p);
	$activeSheet->setShowGridlines(false);
	$activeSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$activeSheet->getPageSetup()->setFitToPage(true);
	$activeSheet->getPageSetup()->setFitToWidth(1);
	$activeSheet->getPageSetup()->setFitToHeight(0);
	$activeSheet->getPageSetup()->setPaperSize($config["pagina"]);
	$activeSheet->getPageSetup()->setHorizontalCentered(true);
	$activeSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 5);
	$activeSheet->getPageSetup()->setColumnsToRepeatAtLeftByStartAndEnd('A', 'D');

	$activeSheet->getPageMargins()->setLeft(0.4);
	$activeSheet->getPageMargins()->setRight(0.4);
	$activeSheet->getPageMargins()->setTop(0.5);
	$activeSheet->getPageMargins()->setBottom(0.5);
	$activeSheet->freezePane($config["fijar"]);
	//$activeSheet->setTitle(utf8_encode($periodo[$p]["descripcion"]));

	$c=1;
	$ln=5;
	
	Cabecera();

	$total_columna=array();
	for($k=0;$k<$n_columna;$k++)
		$total_columna[$k]=0;

	$suma_columna=array();
	$suma_columna_unidad=array();
	for($k=0;$k<$n_columna;$k++){
		$suma_columna[$k]=0;
		$suma_columna_unidad[$k]=0;
	}
	
	$numero_nomina=0;
	$nomina_anterior="";
	$unidadfuncional_anterior="";
	for($j=0;$j<count($ficha);$j++){

		//FILA CON EL NOMBRE DE LA NÓMINA
		$sw_total_aporte=false;
		if($nomina_anterior!=$ficha[$j]["nomina"]){
			if($j!=0) {
				TotalUnidad();
				TotalNomina();
				$sw_total_aporte=true;
			}

			
			$nomina_anterior=$ficha[$j]["nomina"];
			$unidadfuncional_anterior="";
			$numero_nomina++;


			$activeSheet->getRowDimension($ln)->setRowHeight(25);
			$activeSheet->mergeCells("A$ln:$ultima_columna$ln");
			$activeSheet->getStyle("A$ln")->getFont()->setBold(true);
			$activeSheet->getStyle("A$ln")->getFont()->setSize(14);
			$activeSheet->setCellValueExplicit("A$ln",$ficha[$j]["nomina"],PHPExcel_Cell_DataType::TYPE_STRING);	
			$ln++;
		}

		//FILA CON EL NOMBRE DE LA UNIDAD FUNCIONAL
		/*if($unidadfuncional_anterior!=$ficha[$j]["unidad_funcional"]){
			$unidadfuncional_anterior=$ficha[$j]["unidad_funcional"];

			if($j!=0 and $sw_total_aporte==false) 
				TotalUnidad();		

			$activeSheet->mergeCells("A$ln:$ultima_columna$ln");
			$activeSheet->getStyle("A$ln")->getFont()->setBold(true);
			$activeSheet->getStyle("A$ln")->getFont()->setSize(9);
			$activeSheet->setCellValueExplicit("A$ln",$ficha[$j]["unidad_funcional"],PHPExcel_Cell_DataType::TYPE_STRING);	
			$activeSheet->getStyle("A$ln:$ultima_columna$ln")->applyFromArray($border);
			$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');
			$ln++;
		}*/

		//COLUMNAS DE LA NOMINA
		//NUMERO EN LISTA
		$activeSheet->setCellValueExplicit("A$ln","$c",PHPExcel_Cell_DataType::TYPE_STRING);	
		//NOMBRE/APELLIDO	
		$activeSheet->setCellValueExplicit("B$ln",$ficha[$j]["nacionalidad"].$ficha[$j]["cedula"],PHPExcel_Cell_DataType::TYPE_STRING);
		$activeSheet->setCellValueExplicit("C$ln",$ficha[$j]["nombre_apellido"],PHPExcel_Cell_DataType::TYPE_STRING);
		//CARGO
		//$activeSheet->setCellValueExplicit("D$ln",$ficha[$j]["cargo"],PHPExcel_Cell_DataType::TYPE_STRING);
		$activeSheet->setCellValueExplicit("D$ln",$ficha[$j]["cargo"]." - ".$ficha[$j]["escala_salarial"],PHPExcel_Cell_DataType::TYPE_STRING);
		
		//GENERO
		$genero=$ficha[$j]["genero"];
		if($ficha[$j]["genero"]=="M")
			$genero="M";
		else if($ficha[$j]["genero"]=="F")
			$genero="F";
		$activeSheet->setCellValueExplicit("E$ln",$genero,PHPExcel_Cell_DataType::TYPE_STRING);

		//FECHA INGRESO
		$activeSheet->setCellValueExplicit("F$ln",$ficha[$j]["fecha_ingreso"],PHPExcel_Cell_DataType::TYPE_STRING);

		//ANTIGUEDAD (AÑOS / DIAS)
		$activeSheet->setCellValueExplicit("G$ln",$ficha[$j]["antiguedad_anio_dia"][0],PHPExcel_Cell_DataType::TYPE_STRING);
		$activeSheet->setCellValueExplicit("H$ln",$ficha[$j]["antiguedad_anio_dia"][1],PHPExcel_Cell_DataType::TYPE_STRING);
		$activeSheet->getStyle("E$ln:H$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//PARA CADA COLUMNA CREAR LA CELDA VACIA
		//COLOCAR EN LA CELDA CORRESPONDINTE EL VALOR DEL CONCEPTO
		for($k=0;$k<count($ficha[$j]["concepto"]);$k++){
			if(!isset($columna[$ficha[$j]["concepto"][$k]["id"]]))
				continue;
			$x=$columna[$ficha[$j]["concepto"][$k]["id"]];
			$suma_columna[$x]+=$ficha[$j]["concepto"][$k]["valor_final"];
			$suma_columna_unidad[$x]+=$ficha[$j]["concepto"][$k]["valor_final"];
			if($config["aporte_patronal"]){
				if($ficha[$j]["concepto"][$k]["tipo"]=="AP"){
					$x_ap=$columna["AP_".$ficha[$j]["concepto"][$k]["id"]];
					$suma_columna[$x_ap]+=$ficha[$j]["concepto"][$k]["valor_final_ap"];
					$suma_columna_unidad[$x_ap]+=$ficha[$j]["concepto"][$k]["valor_final_ap"];

					$activeSheet->getStyle(column_hash($columna_base+$x_ap)."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
					$activeSheet->setCellValueExplicit(column_hash($columna_base+$x_ap)."$ln",number_format($ficha[$j]["concepto"][$k]["valor_final_ap"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
				}
			}
			$activeSheet->getStyle(column_hash($columna_base+$x)."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$x)."$ln",number_format($ficha[$j]["concepto"][$k]["valor_final"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}
		
		//TOTAL ASIGNACIONES
		$activeSheet->getStyle(column_hash($columna_base+$columna["A"])."$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');
		$activeSheet->getStyle(column_hash($columna_base+$columna["A"])."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
		$activeSheet->setCellValueExplicit(column_hash($columna_base+$columna["A"])."$ln",number_format($ficha[$j]["total_asignacion"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
		
		if($sw_deducciones){
			//TOTAL DEDUCCIONES
			$activeSheet->getStyle(column_hash($columna_base+$columna["D"])."$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');
			$activeSheet->getStyle(column_hash($columna_base+$columna["D"])."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$columna["D"])."$ln",number_format($ficha[$j]["total_deduccion"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
			//TOTAL NETO
			$activeSheet->getStyle(column_hash($columna_base+$columna["N"])."$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');
			$activeSheet->getStyle(column_hash($columna_base+$columna["N"])."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$columna["N"])."$ln",number_format($ficha[$j]["total_neto"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}			
		
		//TOTAL AP
		if($sw_aportes){
			$activeSheet->getStyle(column_hash($columna_base+$columna["AP"])."$ln")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAE7E7E7');
			$activeSheet->getStyle(column_hash($columna_base+$columna["AP"])."$ln")->getNumberFormat()->setFormatCode('#,##0.00');
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$columna["AP"])."$ln",number_format($ficha[$j]["total_ap"],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}

		$activeSheet->getStyle("A$ln:$ultima_columna$ln")->applyFromArray($border);
		$activeSheet->getStyle("A$ln:F$ln")->getFont()->setSize(11);
		$activeSheet->getStyle("D$ln")->getFont()->setSize(8);
		$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getFont()->setSize(9);

		$suma_columna[$columna["A"]]+=$ficha[$j]["total_asignacion"];
		$suma_columna_unidad[$columna["A"]]+=$ficha[$j]["total_asignacion"];
		if($sw_deducciones){
			$suma_columna[$columna["D"]]+=$ficha[$j]["total_deduccion"];
			$suma_columna[$columna["N"]]+=$ficha[$j]["total_neto"];
			$suma_columna_unidad[$columna["D"]]+=$ficha[$j]["total_deduccion"];
			$suma_columna_unidad[$columna["N"]]+=$ficha[$j]["total_neto"];
		}
		
		if($sw_aportes){
			$suma_columna[$columna["AP"]]+=$ficha[$j]["total_ap"];
			$suma_columna_unidad[$columna["AP"]]+=$ficha[$j]["total_ap"];
		}
		$c++;
		$ln++;
	}
	
	TotalNomina();

	//TOTAL GENERAL DE NOMINAS
	if($numero_nomina>1){
		$activeSheet->mergeCells("A$ln:H$ln");
		$activeSheet->setCellValueExplicit("A$ln","TOTAL NÓMINAS",PHPExcel_Cell_DataType::TYPE_STRING);

		for($k=0;$k<$n_columna;$k++)
			$activeSheet->setCellValueExplicit(column_hash($columna_base+$k)."$ln",number_format($total_columna[$k],2,".",""),PHPExcel_Cell_DataType::TYPE_NUMERIC);	


		$activeSheet->getStyle("A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFont()->setBold(true);
		$activeSheet->getStyle("A$ln:$ultima_columna$ln")->getFont()->setSize(9);
		$activeSheet->getStyle("I$ln:$ultima_columna$ln")->getNumberFormat()->setFormatCode('#,##0.00');

		/*$activeSheet->getStyle("G$ln:$ultima_columna$ln")->getNumberFormat()->setFormatCode('#,##0.00');
		$activeSheet->getStyle("G$ln:$ultima_columna$ln")->getFont()->setBold(true);
		$activeSheet->getStyle("G$ln:$ultima_columna$ln")->getFont()->setSize(9);*/
		$ln++;
	}
	

	$activeSheet->getColumnDimension("A")->setAutoSize(true);
	$activeSheet->getColumnDimension("B")->setAutoSize(true);
	$activeSheet->getColumnDimension("C")->setAutoSize(true);
	$activeSheet->getColumnDimension("D")->setAutoSize(true);
	$activeSheet->getColumnDimension("E")->setAutoSize(true);
	$activeSheet->getColumnDimension("F")->setAutoSize(true);
	$activeSheet->getColumnDimension("G")->setWidth(5);
	$activeSheet->getColumnDimension("H")->setWidth(5);
	for($i=$columna_base;$i<($columna_base+$n_columna);$i++){
		$activeSheet->getColumnDimension(column_hash($i))->setAutoSize(true);
	}

	if(isset($columna["A"]))
	$activeSheet->getStyle(column_hash($columna_base+$columna["A"])."5:".column_hash($columna_base+$columna["A"])."$ln")->getFont()->setBold(true);
	if(isset($columna["D"]))
	$activeSheet->getStyle(column_hash($columna_base+$columna["D"])."5:".column_hash($columna_base+$columna["D"])."$ln")->getFont()->setBold(true);
	if(isset($columna["N"]))
	$activeSheet->getStyle(column_hash($columna_base+$columna["N"])."5:".column_hash($columna_base+$columna["N"])."$ln")->getFont()->setBold(true);
	if(isset($columna["AP"]))
	if($config["aporte_patronal"])
		$activeSheet->getStyle(column_hash($columna_base+$columna["AP"])."5:".column_hash($columna_base+$columna["AP"])."$ln")->getFont()->setBold(true);

	//NOTAS
	$nota="";
	for($n=0;$n<count($periodo[$p]["nota"]);$n++){
		$tmp=trim($periodo[$p]["nota"][$n]["nota"],"\n\r ");
		if($tmp)
			$nota.=$tmp."\n";
	}	

	$activeSheet->mergeCells("A$ln:$ultima_columna$ln");
	$activeSheet->getStyle("A$ln")->getFont()->setSize(10);	
	$activeSheet->setCellValueExplicit("A$ln","NOTAS: \n".$nota,PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->getStyle("A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $activeSheet->getStyle("A$ln")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$activeSheet->getRowDimension($ln)->setRowHeight(50);

	if(isset($columna["N"]))
	if($config["aporte_patronal"]=="hide")
		for($i=$columna["N"]+1;$i<$n_columna;$i++)
			$activeSheet->getColumnDimension(column_hash($columna_base+$i))->setVisible(false);


	//DIVIDIR AREA DE IMPRESION EN 2 PARTES, ASIGNACIONES Y DEDUCCIONES
	/*if($config["dividir_impresion"]){
		$activeSheet->getPageSetup()->setPrintArea("A1:".column_hash($columna_base+$columna["A"])."$ln,".column_hash($columna_base+$columna["A"]+1)."1:".column_hash($columna_base+$columna["AP"])."$ln");
	}*/

endfor;


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.str_replace(" ", "_", $nombre_reporte).'.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>