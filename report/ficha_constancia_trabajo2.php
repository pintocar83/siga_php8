<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");


include_once("../library/fpdf/1.7/WriteTag.php");

include_once("../library/functions/letra_numero.php");
include_once("../library/functions/letra_mes.php");


$nacionalidad=SIGA::paramGet("nacionalidad");
$cedula=SIGA::paramGet("cedula");



if($nacionalidad and $cedula){
    $access=SIGA::access("ficha");
    if(!$access){
        header('Content-Type: text/html; charset=utf-8');
        print "No tiene acceso al módulo 'modulo_nomina/ficha'";
        exit;
    }
    $db=SIGA::DBController();
}
else{
    if(!SIGA::user()){
        header('Content-Type: text/html; charset=utf-8');
        print "Debe iniciar sesión para solicitar el reporte.";
        exit;
    }
    $db=SIGA::DBController();
    $sql="select
        identificacion_tipo,
        identificacion_numero
      from
        modulo_base.usuario as u,
        modulo_base.persona as p
      where
        u.usuario like '".SIGA::user()."' and
        u.id_persona_responsable=p.id";
    $persona=$db->Execute($sql);
    
    $nacionalidad=$persona[0]["identificacion_tipo"];
    $cedula=$persona[0]["identificacion_numero"];
}

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");

//buscar el ultimo periodo cerrado en la nómina
$periodo=$db->Execute("SELECT id, fecha_culminacion
                       FROM modulo_nomina.periodo
                       WHERE tipo='Q' and cerrado AND EXTRACT(MONTH FROM fecha_inicio)=EXTRACT(MONTH FROM fecha_culminacion)
                       ORDER BY fecha_culminacion desc, codigo asc
                       LIMIT 1");
if(!isset($periodo[0][0])){
    print "No encontro el ultimo periodo.";
    exit;
}
$id_periodo=$periodo[0]["id"];
$fecha_culminacion=$periodo[0]["fecha_culminacion"];

include_once("../class/nomina.class.php");
include_once("../class/ficha.class.php");






$detalle_ficha=ficha::onGet($nacionalidad,$cedula);
$id_ficha=$detalle_ficha[0]["id"];

//BUSCAR CARGO DE LA FICHA PARA EL PERIODO ACTUAL
$cargo=$db->Execute("SELECT
                        cargo,
                        denominacion as cargo_denominacion,
                        orden
                      FROM modulo_nomina.cargo
                      WHERE id = (select id_cargo
                                   from modulo_nomina.ficha_cargo
                                   where id_ficha=$id_ficha and fecha <= '$fecha_culminacion'
                                   order by fecha desc
                                   limit 1)");   



//buscar la nómina a la cual pertenece

$sql="select
        distinct id_nomina
      from
        modulo_nomina.ficha_concepto as FC
      where
        FC.id_ficha=$id_ficha AND
        FC.id_periodo=$id_periodo";
        
        //print $sql;
$nomina=$db->Execute($sql);    

if(!isset($nomina) or count($nomina)==0){
    header('Content-Type: text/html; charset=utf-8');
    print "La persona no fue encontrada en el sistema de nómina.";
    exit;
}

$nombres_apellidos=trim($detalle_ficha[0]["primer_nombre"]." ".$detalle_ficha[0]["segundo_nombre"]." ".$detalle_ficha[0]["primer_apellido"]." ".$detalle_ficha[0]["segundo_apellido"]);
$cargo=$cargo[0]["cargo_denominacion"];


$sueldo_normal=0;
$diferencia_sueldo=0;
for($i=0;$i<count($nomina);$i++){
  $concepto=nomina::ficha_concepto($nomina[$i]["id_nomina"],$id_periodo,$id_ficha);
  $concepto=$concepto["concepto"];
  for($k=0;$k<count($concepto);$k++){
    if($concepto[$k]["identificador"]=="SUELDO_NORMAL")
      $sueldo_normal+=$concepto[$k]["valor_final"];
    else if(substr_compare($concepto[$k]["identificador"],"DIFERENCIA_",0,11)==0)
      $diferencia_sueldo+=$concepto[$k]["valor_final"];
  }
}
//print $diferencia_sueldo;exit;
//$sueldo_normal=($sueldo_normal-$diferencia_sueldo)*2;
$sueldo_normal=($sueldo_normal-$diferencia_sueldo)*4;

$formato="A";
$formato="B";//con antiguedad

$tam_fn=7;
$j=0;
$pdf = new PDF_WriteTag("P","mm","Letter");
$pdf->SetAutoPageBreak(false);



$pdf->AddPage("P");
//cintillo
    
$pdf->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);

$pdf->SetFont('helvetica','B',12);
$rif_len=strlen($organismo[0]["identificacion"])-1;
$rif=$organismo[0]["identificacion"][0]."-".substr($organismo[0]["identificacion"],1,$rif_len-1)."-".$organismo[0]["identificacion"][$rif_len];
$pdf->Text(25,28,utf8_decode("RIF: $rif"));

$pdf->SetFont('helvetica','BI',24);
$pdf->SetXY(9,40);
$pdf->MultiCell(196,6,utf8_decode("C O N S T A N C I A"),'','C');

$pdf->SetStyle("parrafo", "helvetica", "I", 13, "0, 0, 0", 15);
$pdf->SetStyle("negrita", "helvetica", "BI", 13, "0, 0, 0");


$pdf->SetXY(22,65);

$persona="la persona";
if(isset($detalle_ficha[0]["genero"])){
    if($detalle_ficha[0]["genero"]=="M") $persona="el ciudadano";
    elseif($detalle_ficha[0]["genero"]=="F") $persona="la ciudadana";
}

switch($formato){
    case "B":
        $antiguedad="";
        if(isset($detalle_ficha[0]["antiguedad_simple"]) and $detalle_ficha[0]["antiguedad_simple"])
            $antiguedad=$detalle_ficha[0]["antiguedad_simple"];
        //$antiguedad="5 años y 264 días";
        $texto= "Quien suscribe Soc. Enrique José Ortiz Rodríguez, titular de la cédula de identidad ".
                "Nº 8.647.822, Presidente de la Fundación para el Desarrollo de la Ciencia y Tecnología en el Estado Sucre (FUNDACITE SUCRE), ".
                "por medio de la presente hace constar que $persona: <negrita>$nombres_apellidos,</negrita> titular de la cédula de identidad <negrita>Nº ".$nacionalidad."-".number_format($cedula,0,"",".").",</negrita> ".
                "ha prestado servicio en la Institución durante $antiguedad. Actualmente desempeña el cargo de $cargo y devenga una remuneración mensual de ".letra_numero($sueldo_normal,true)." Céntimos (Bs ".number_format($sueldo_normal,2,",",".").").";
    break;
    case "A":
    default:
        $texto= "Quien suscribe Soc. Enrique José Ortiz Rodríguez, titular de la cédula de identidad ".
                "Nº 8.647.822, Presidente de la Fundación para el Desarrollo de la Ciencia y Tecnología en el Estado Sucre (FUNDACITE SUCRE), ".
                "por medio de la presente hace constar que la persona: <negrita>$nombres_apellidos</negrita>, titular de la cédula de identidad <negrita>Nº ".$nacionalidad."-".number_format($cedula,0,"",".")."</negrita>, ".
                "labora en esta institución desempeñando el cargo de $cargo y devenga una remuneración mensual de ".letra_numero($sueldo_normal,true)." Céntimos (Bs ".number_format($sueldo_normal,2,",",".").").";
}


$pdf->WriteTag(170,9,utf8_decode(
    "<parrafo>$texto</parrafo>".
    "<parrafo>Se expide la presente constancia a petición de parte interesada, en Cumaná, a los ".letra_numero(date("d"))." días del mes de ".letra_mes(date("m"))." de ".date("Y").".</parrafo>"),'','J');



$pdf->SetFont('helvetica','BI',13);
$pdf->SetXY(22,220);
$pdf->MultiCell(170,6,utf8_decode("Soc. Enrique José Ortiz Rodríguez"),'','C');

$pdf->SetFont('helvetica','',13);
$pdf->SetXY(22,220+6);
$pdf->MultiCell(170,6,utf8_decode("Presidente de la FUNDACITE Sucre"),'','C');

$pdf->SetFont('helvetica','',12);
$pdf->SetXY(22,220+6*2);
$pdf->MultiCell(170,5,utf8_decode("Resolución Nº 211 de fecha 18/11/2013 publicado en la Gaceta Oficial de la República Bolivariana de Venezuela Nº 40.297 de fecha 19/11/2013"),'','C');

$pdf->SetFont('helvetica','I',9);
$pdf->SetXY(22,260);
$pdf->MultiCell(170,4,utf8_decode("Av. Monseñor Alfredo Rodríguez Figueroa, vía El Peñon, Urb. Cristobal Colón, Cumaná 6101, Edo. Sucre, Venezuela.\nTelf. (0293)441.20.63 - http://www.fundacite-sucre.gob.ve/"),'','C');




$pdf->SetDrawColor(160,0,0);
$pdf->Line(15, 30, 15, 260-5);
$pdf->Line(15+3, 30+3, 15+3, 260+3-5);

$pdf->Line(15, 260-5, 195, 260-5);
$pdf->Line(15+3, 260+3-5, 195+3, 260+3-5);

$pdf->Output();

?>