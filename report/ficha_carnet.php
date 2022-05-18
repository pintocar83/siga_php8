<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");


include_once("../library/fpdf/1.84/WriteTag.php");

include_once("../library/functions/letra_numero.php");
include_once("../library/functions/letra_mes.php");
include_once("../library/phpqrcode/1.1.4/phpqrcode.php");

$nacionalidad=SIGA::paramGet("nacionalidad");
$cedula=SIGA::paramGet("cedula");



if($nacionalidad and $cedula){
    $access=SIGA::access("ficha");
    if(!$access){
        header('Content-Type: text/html; charset=utf-8');
        print "No tiene acceso al módulo 'ficha'";
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

$organismo=$db->Execute("SELECT
                            P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");





$fecha_culminacion=date("Y-m-d");

include_once("../class/ficha.class.php");



$detalle_ficha=ficha::onGet($nacionalidad,$cedula);//print_r($detalle_ficha);exit;
$id_ficha=$detalle_ficha[0]["id"];


//BUSCAR CARGO DE LA FICHA PARA LA FECHA ACTUAL
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

if(!isset($cargo[0]["cargo_denominacion"])){
  print "Debe asignarle un cargo para generar el carnet.";
  exit;
}
//SIGA::databasePath()."/config/"

$foto=SIGA::databasePath()."/persona/".$detalle_ficha[0]["foto"];

if(!file_exists($foto)){
  print "La persona no posee foto.";
  exit;
}


$identificacion=$nacionalidad."-".number_format($cedula,0,"",".");
$nombre_apellido=trim($detalle_ficha[0]["primer_nombre"]." ".$detalle_ficha[0]["primer_apellido"]);
//$nombres_apellidos=trim($detalle_ficha[0]["primer_nombre"]." ".$detalle_ficha[0]["segundo_nombre"]." ".$detalle_ficha[0]["primer_apellido"]." ".$detalle_ficha[0]["segundo_apellido"]);
$cargo=$cargo[0]["cargo_denominacion"];




$tamano["pagina"]['ancho']=55;
$tamano["pagina"]['alto']=85;

$tamaño["foto"]['ancho']=23;
//calcula el alto de la foto en base al ancho y a la escala
$mm_px=3.779528;//1mm = 3.779528px
$tamano_foto=getimagesize($foto);
//tamaño de la foto en mm
$tamano_foto_w=$tamano_foto[0]/$mm_px;
$tamano_foto_h=$tamano_foto[1]/$mm_px;

$iteracion=0;
//ajustar el alto de la foto (hacia abajo), si este sobre pasa los 26mm
while(1){
  $escala=$tamaño["foto"]['ancho']/$tamano_foto_w;
  $alto_escala=$tamano_foto_h*$escala;
  //print "<br>ANCHO: ".$tamaño["foto"]['ancho']."  ALTO: ".$alto_escala;
  if($alto_escala>=26){
    $tamaño["foto"]['ancho']--;    
  }
  else{
    break;
  }
  $iteracion++;
  if($iteracion>=10) break;
}

//print $alto_escala;exit;
//fin calcular
$tamaño["foto"]['alto']=$alto_escala;

$posicion["foto"]['x']=($tamano["pagina"]['ancho']-$tamaño["foto"]['ancho'])/2;
$posicion["foto"]['y']=46.7-$tamaño["foto"]['alto'];//46.7 es el top inferior de la foto

$posicion["nombre"]['x']=12;
$posicion["nombre"]['y']=49.4;

$posicion["identificacion"]['x']=12;
$posicion["identificacion"]['y']=56.5;

$posicion["cargo"]['y']=68;

$pdf = new PDF_WriteTag('P','mm',array($tamano["pagina"]['ancho'],$tamano["pagina"]['alto']));
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

$pdf->Image($foto,$posicion["foto"]['x'],$posicion["foto"]['y'],$tamaño["foto"]['ancho'],$tamaño["foto"]['alto']);


$pdf->Image(SIGA::databasePath()."/config/plantilla_carnet.png",0,0,$tamano["pagina"]['ancho'],$tamano["pagina"]['alto'],'PNG');

$pdf->SetTextColor(117,117,117);

$pdf->SetFont('helvetica','B',12);
$pdf->SetXY($posicion["nombre"]['x'],$posicion["nombre"]['y']);
$texto_ancho=$pdf->GetStringWidth(utf8_decode(mb_convert_case($nombre_apellido,MB_CASE_TITLE,'UTF-8')));
if($texto_ancho>35) $pdf->SetFont('helvetica','B',11);
$pdf->Cell($tamano["pagina"]['ancho']-$posicion["nombre"]['x'],10,utf8_decode(mb_convert_case($nombre_apellido,MB_CASE_TITLE,'UTF-8')),'',0,'L');

$pdf->SetFont('helvetica','B',12);
$pdf->SetXY($posicion["identificacion"]['x'],$posicion["identificacion"]['y']);
$pdf->Cell($tamano["pagina"]['ancho']-$posicion["identificacion"]['x'],10,utf8_decode($identificacion),'',0,'L');


$pdf->SetFont('helvetica','B',14);
$pdf->SetXY(0,$posicion["cargo"]['y']);
$pdf->Cell($tamano["pagina"]['ancho'],10,utf8_decode($cargo),'',0,'C');

//PARTE POSTERIOR
$pdf->SetStyle("parrafo", "helvetica", "", 8.2, "0, 0, 0", 0);
$pdf->SetStyle("negrita", "helvetica", "BI", 8.2, "0, 0, 0");

$pdf->AddPage();
$pdf->SetXY(3,5);
$pdf->WriteTag($tamano["pagina"]['ancho']-3*2,3.5,utf8_decode(
    "<parrafo>Este carnet pertenece a <negrita>FUNDACITE SUCRE,</negrita> ente adscrito al <negrita>Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología.</negrita> Se agradece a las autoridades competentes brindar toda la colaboración necesaria al funcionario o funcionaria que lo porte.</parrafo>"),'','J');

$pdf->SetFont('helvetica','B',12);
$pdf->SetXY(0,35);
$pdf->Cell($tamano["pagina"]['ancho'],5,utf8_decode("INTRANSFERIBLE"),'',1,'C');

$pdf->SetFont('helvetica','B',7.5);
$pdf->SetX(3);
$pdf->MultiCell($tamano["pagina"]['ancho']-3*2,3,utf8_decode("En caso de extravio favor reportarlo al siguiente número telefónico: (0293)467.25.31"),'','J');


$url="http://www.fundacite-sucre.gob.ve/query?".base64_encode("FICHA=$nacionalidad$cedula");
QRcode::png($url,"tmp/qrcode_$nacionalidad$cedula.png",QR_ECLEVEL_L,4);
$pdf->Image("tmp/qrcode_$nacionalidad$cedula.png",($tamano["pagina"]['ancho']-30)/2,50,30,30);

$pdf->SetFont('helvetica','B',10);
$pdf->SetXY(0,78);
$pdf->Cell($tamano["pagina"]['ancho'],5,utf8_decode("$nacionalidad$cedula"),'',0,'C');


$pdf->Output("CARNET_".$nacionalidad.$cedula."_".strtoupper(str_replace(" ","_",$nombre_apellido)).".pdf","I");



?>