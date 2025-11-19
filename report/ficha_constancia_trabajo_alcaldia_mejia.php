<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");


include_once("../library/fpdf/1.7/WriteTag.php");

include_once("../library/functions/letra_numero.php");
include_once("../library/functions/letra_mes.php");
include_once("../library/functions/formatDate.php");

include_once("../class/nomina.class.php");
include_once("../class/ficha.class.php");

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

$detalle_ficha=ficha::onGet($nacionalidad,$cedula);
$id_ficha=$detalle_ficha[0]["id"];

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");

//buscar el ultimo periodo cerrado donde estuvo la persona
$sql="
    SELECT DISTINCT P.id, P.codigo, P.fecha_culminacion
    FROM modulo_nomina.periodo P, modulo_nomina.ficha_concepto FC
    WHERE P.cerrado AND P.id=FC.id_periodo AND FC.id_ficha='$id_ficha'
    ORDER BY P.fecha_culminacion desc, P.codigo asc
    LIMIT 1
";
//print $sql;
$periodo=$db->Execute($sql);
if(!isset($periodo[0][0])){
    print "No encontro el ultimo periodo.";
    exit;
}
$id_periodo=$periodo[0]["id"];
$fecha_culminacion=$periodo[0]["fecha_culminacion"];





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
$nomina=$db->Execute($sql);    

if(!isset($nomina) or count($nomina)==0){
    header('Content-Type: text/html; charset=utf-8');
    print "La persona no fue encontrada en el sistema de nómina.";
    exit;
}

$nombres_apellidos=trim($detalle_ficha[0]["primer_nombre"]." ".$detalle_ficha[0]["segundo_nombre"]." ".$detalle_ficha[0]["primer_apellido"]." ".$detalle_ficha[0]["segundo_apellido"]);
$cargo=$cargo[0]["cargo_denominacion"];


$sueldo_normal=0;
$primas=0;
$diferencia_sueldo=0;
$prima_responsabilidad=0;
$prima_antiguedad=0;
$prima_prof=0;
$prima_hijos=0;

for($i=0;$i<count($nomina);$i++)
{
  $concepto=nomina::ficha_concepto($nomina[$i]["id_nomina"],$id_periodo,$id_ficha);
  $concepto=$concepto["concepto"];
  for($k=0;$k<count($concepto);$k++)
  {
    if(in_array($concepto[$k]["identificador"],["SUELDO_MENSUAL"]))//if($concepto[$k]["identificador"]=="SUELDO_BASICO")
      $sueldo_normal+=$concepto[$k]["valor_final"];
    //else if(in_array($concepto[$k]["identificador"],["PRIMA_PROF","PRIMA_HIJOS"]))
    //  $primas+=$concepto[$k]["valor_final"];
    else if($concepto[$k]["identificador"]=="DIFERENCIA_SUELDO")
      $diferencia_sueldo+=$concepto[$k]["valor_final"];

    if($concepto[$k]["identificador"]=="PRIMA_RESPONSABILIDAD")
      $prima_responsabilidad+=$concepto[$k]["valor_final"];

    if($concepto[$k]["identificador"]=="PRIMA_ANTIGUEDAD")
      $prima_antiguedad+=$concepto[$k]["valor_final"];

    if($concepto[$k]["identificador"]=="PRIMA_PROF")
      $prima_prof+=$concepto[$k]["valor_final"];

    if($concepto[$k]["identificador"]=="PRIMA_HIJOS")
      $prima_hijos+=$concepto[$k]["valor_final"];
  }
}

$sueldo_normal=($sueldo_normal-$diferencia_sueldo);
/*suma de primas.*/


if(empty($prima_antiguedad) & empty($prima_responsabilidad) & empty($prima_prof) & empty($prima_hijos))
{
  $id_periodo=$id_periodo-1;

  $sql="select
        distinct id_nomina
      from
        modulo_nomina.ficha_concepto as FC
      where
        FC.id_ficha=$id_ficha AND
        FC.id_periodo=$id_periodo";
  $nomina=$db->Execute($sql);  

  for($i=0;$i<count($nomina);$i++)
  {
    $concepto=nomina::ficha_concepto($nomina[$i]["id_nomina"],$id_periodo,$id_ficha);
    $concepto=$concepto["concepto"];
    for($k=0;$k<count($concepto);$k++)
    {
      if($concepto[$k]["identificador"]=="PRIMA_RESPONSABILIDAD")
        $prima_responsabilidad+=$concepto[$k]["valor_final"];

      if($concepto[$k]["identificador"]=="PRIMA_ANTIGUEDAD")
        $prima_antiguedad+=$concepto[$k]["valor_final"];

       if($concepto[$k]["identificador"]=="PRIMA_PROF")
        $prima_prof+=$concepto[$k]["valor_final"];

      if($concepto[$k]["identificador"]=="PRIMA_HIJOS")
        $prima_hijos+=$concepto[$k]["valor_final"];
    }
  }
}

if(!empty($prima_antiguedad))
{
  $sueldo_normal=$sueldo_normal+$prima_antiguedad;
}

if(!empty($prima_responsabilidad))
{
  $sueldo_normal=$sueldo_normal+$prima_responsabilidad;
}

if(!empty($prima_prof))
{
  $sueldo_normal=$sueldo_normal+$prima_prof;
}

if(!empty($prima_hijos))
{
  $sueldo_normal=$sueldo_normal+$prima_hijos;
}

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
//$pdf->Text(25,35,utf8_decode("RIF: $rif"));

$pdf->SetFont('helvetica','BI',20);
$pdf->SetXY(9,40);
$pdf->MultiCell(196,6,utf8_decode("C O N S T A N C I A   D E   T R A B A J O"),'','C');

$pdf->SetStyle("parrafo", "helvetica", "I", 13, "0, 0, 0", 15);
$pdf->SetStyle("negrita", "helvetica", "BI", 13, "0, 0, 0");


$pdf->SetXY(22,65);

$persona="la persona";
if(isset($detalle_ficha[0]["genero"])){
    if($detalle_ficha[0]["genero"]=="M") $persona="el ciudadano";
    elseif($detalle_ficha[0]["genero"]=="F") $persona="la ciudadana";
}

$ingreso="";

if(isset($detalle_ficha[0]["fecha_ingreso"])){
  $ingreso=$detalle_ficha[0]["fecha_ingreso"];
  $ingreso=$detalle_ficha[0]["fecha_ingreso"][0];
  $ingreso=formatDate($ingreso);
}

switch($formato){
    case "B":
        $antiguedad="";
        if(isset($detalle_ficha[0]["antiguedad_simple"]) and $detalle_ficha[0]["antiguedad_simple"])
            $antiguedad=$detalle_ficha[0]["antiguedad_simple"];
        $texto= "Quien suscribe, Lcdo. Carlos Julio Velasquez, Director de Personal de la Alcadía Bolivariana del Municipio Mejía del Estado Sucre, ".
                "por medio de la presente hace constar que $persona: <negrita>$nombres_apellidos,</negrita> titular de la cédula de identidad <negrita>Nº ".$nacionalidad."-".number_format($cedula,0,"",".").",</negrita> ".
                "ingreso a la institución desempeñando el cargo de $cargo desde el $ingreso, devengando un sueldo mensual de ".letra_numero($sueldo_normal,true)." Céntimos (Bs ".number_format($sueldo_normal,2,",",".").").";
    break;
    case "A":
    default:
        $texto= "Quien suscribe Lcdo. Carlos Julio Velasquez, titular de la cédula de identidad ".
                "Nº 18.581.526, Director de Personal de la Alcadía Bolivariana del Municipio Mejía, ".
                "por medio de la presente hace constar que la persona: <negrita>$nombres_apellidos</negrita>, titular de la cédula de identidad <negrita>Nº ".$nacionalidad."-".number_format($cedula,0,"",".")."</negrita>, ".
                "labora en esta institución desempeñando el cargo de $cargo y devenga una remuneración mensual de ".letra_numero($sueldo_normal,true)." Céntimos (Bs ".number_format($sueldo_normal,2,",",".").").";
}


$pdf->WriteTag(170,9,utf8_decode(
    "<parrafo>$texto</parrafo>".
    "<parrafo>Se expide la presente constancia a petición de parte interesada, en San Antonio del Golfo, a los ".letra_numero(date("d"))." días del mes de ".letra_mes(date("m"))." de ".date("Y").".</parrafo>"),'','J');



$pdf->SetFont('helvetica','BI',13);
$pdf->SetXY(22,200);
$pdf->MultiCell(170,6,utf8_decode("Lcdo. Carlos Julio Velasquez"),'','C');

$pdf->SetFont('helvetica','',12);
$pdf->SetXY(22,200+6);
$pdf->MultiCell(170,6,utf8_decode("C.I: 18.581.526\nDirector de Personal\nSegún Resolución 019/2025\nDe fecha 28/08/2025"),'','C');

$pdf->SetFont('helvetica','',10);
$pdf->SetXY(22,224+6*2);
$pdf->MultiCell(170,5,utf8_decode("Calle Santa Teresa - Frente a la Plaza Bolivar - San Antonio del Golfo, Municipio Mejía del Estado Sucre. Telf: (0293)829.32.40 / Correo: alcaldiamejiarh13@gmail.com / Rif: $rif\n"),'','C');

$pdf->SetFont('helvetica','I',9);
$pdf->SetXY(22,260);
//$pdf->MultiCell(170,4,utf8_decode("Av. Monseñor Alfredo Rodríguez Figueroa, vía El Peñon, Urb. Cristobal Colón, Cumaná 6101, Edo. Sucre, Venezuela.\nTelf. (0293)467.25.31 - http://www.fundacite-sucre.gob.ve/"),'','C');




$pdf->SetDrawColor(160,0,0);
$pdf->Line(15, 10+30, 15, 260-5);
$pdf->Line(15+3, 10+30+3, 15+3, 260+3-5);

$pdf->Line(15, 260-5, 195, 260-5);
$pdf->Line(15+3, 260+3-5, 195+3, 260+3-5);

$pdf->Output();

?>