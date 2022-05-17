<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();



$CONSULTA=$db->Execute("select
                          *,
                          case when tipo ilike 'J' then
                            identificacion_tipo||'-'||lpad(left(text(identificacion_numero),length(text(identificacion_numero))-1),7,'0')||'-'||right(text(identificacion_numero),1)
                          else
                            identificacion_tipo||'-'||lpad(text(identificacion_numero),8,'0')
                          end as identificacion,                            
                          replace(denominacion,';',' ') as persona
                        from
                          modulo_base.persona as P                                                
                        order by
                          P.identificacion_tipo,
                          P.identificacion_numero
                        ");

                        //print_r($CONSULTA);exit;
                        
if(count($CONSULTA)==0){
  print "No se encontraron datos.";
  exit;
}

$MARGEN_LEFT=10;
$MARGEN_TOP=5;
$ANCHO=260;

$TITULO_PRINCIPAL="LISTADO DE PERSONAS NATURALES/JURIDICAS";


$t_n=10;
$t_rif=20;
$t_persona=60;
$t_tel=25;
$t_correo=40;
$t_direccion=$ANCHO-($t_n+$t_rif+$t_persona+$t_tel+$t_correo);



$CABECERA[0]=array(
                   array("id"=>"#","nombre"=>"Nº","ancho"=>$t_n,"alinear"=>"C"),
                   array("id"=>"identificacion","nombre"=>"RIF","ancho"=>$t_rif,"alinear"=>"L"),
                   array("id"=>"persona","nombre"=>"Denominación","ancho"=>$t_persona,"alinear"=>"L"),
                   array("id"=>"telefono","nombre"=>"Teléfono","ancho"=>$t_tel,"alinear"=>"L"),
                   array("id"=>"correo","nombre"=>"Correo","ancho"=>$t_correo,"alinear"=>"L"),
                   array("id"=>"direccion","nombre"=>"Dirección","ancho"=>$t_direccion,"alinear"=>"L")                   
                  );

include("template/pdf_reporte_1.class.php");


$pdf=new PDF_REPORTE_1("L","mm","letter");
$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$pdf->PrintData();



$pdf->AliasNbPages();
$pdf->Output("listado_personas.pdf","I");

?>