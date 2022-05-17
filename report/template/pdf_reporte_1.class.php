<?php
/*
Debe estar definido las siguientes variables
$CONSULTA => Contiene la informacion o datos a mostrar.
$CABECERA => Contiene la configuracion de la cabecera de la tabla, ver ejemplo en: modulo_base/retencion_emitida.php
$TITULO_PRINCIPAL => Titulo a mostrar en el reporte
$TITULO_SECUNDARIO => Subtituto del reporte

$MARGEN_LEFT => Margen izquierdo de la página
$MARGEN_TOP
$ANCHO

*/





$default_cabecera_fondo=array(220,220,220);
$default_fondo=array(255,255,255);
$default_cabecera_color=$default_color=array(0,0,0);
$default_cabecera_font_size=8;
$default_font_size=7.5;
$default_fila_alto=4.5;
$SUMA=array();

class PDF_REPORTE_1 extends FPDF{
	function Header(){
    global $default_cabecera_fondo, $default_cabecera_color, $default_cabecera_font_size, $default_fila_alto;
    global $MARGEN_LEFT, $MARGEN_TOP, $ANCHO, $TITULO_PRINCIPAL, $TITULO_SECUNDARIO, $CABECERA;
    global $organismo;
    
    if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
      $this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$MARGEN_LEFT,$MARGEN_TOP,$ANCHO);
    elseif(SIGA::databasePath()."/config/logo_02.jpg")
      $this->Image(SIGA::databasePath()."/config/logo_02.jpg",$MARGEN_LEFT,$MARGEN_TOP,40);
		    
    $this->Ln(18);
    $this->SetFont('helvetica','',8);
    $this->Cell($ANCHO,5,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'R');
    $this->SetFont('helvetica','B',14);
    $this->Cell($ANCHO,5,$TITULO_PRINCIPAL,'',1,"C");
    $this->SetFont('helvetica','',9);
    $this->Cell($ANCHO,5,$TITULO_SECUNDARIO,'',1,"C");
    $this->SetFont('helvetica','B',$default_cabecera_font_size);
    $this->Ln(3);
    for($c=0;$c<count($CABECERA);$c++){
      for($i=0;$i<count($CABECERA[$c]);$i++){
        $borde="LRTB";
        if(isset($CABECERA[$c][$i]["borde"]))
          $borde=$CABECERA[$c][$i]["borde"];
          
        if(isset($CABECERA[$c][$i]["fondo"]))
          $this->SetFillColor($CABECERA[$c][$i]["fondo"][0],$CABECERA[$c][$i]["fondo"][1],$CABECERA[$c][$i]["fondo"][2]);
        else
          $this->SetFillColor($default_cabecera_fondo[0],$default_cabecera_fondo[1],$default_cabecera_fondo[2]);
        
        if(isset($CABECERA[$c][$i]["color"]))
          $this->SetTextColor($CABECERA[$c][$i]["color"][0],$CABECERA[$c][$i]["color"][1],$CABECERA[$c][$i]["color"][2]);
        else
          $this->SetTextColor($default_cabecera_color[0],$default_cabecera_color[1],$default_cabecera_color[2]);
        
        $this->Cell($CABECERA[$c][$i]["ancho"],
										$default_fila_alto,
										utf8_decode($CABECERA[$c][$i]["nombre"]),
										$borde,
										($i<(count($CABECERA[$c])-1)?0:1),
										"C",
										1
										);
      }   
    }
  }
  
  function PrintData(){
    global $default_cabecera_fondo, $default_cabecera_color, $default_cabecera_font_size, $default_fila_alto, $default_color, $default_fondo, $default_font_size;
    global $MARGEN_LEFT, $MARGEN_TOP, $ANCHO, $TITULO_PRINCIPAL, $TITULO_SECUNDARIO, $CABECERA;
    global $CONSULTA, $SUMA;
    $n=count($CABECERA)-1;
    $this->SetTextColor($default_color[0],$default_color[1],$default_color[2]);
    $this->SetFillColor($default_fondo[0],$default_fondo[1],$default_fondo[2]);
    $this->SetFont('helvetica','',$default_font_size);
    for($k=0;$k<count($CONSULTA);$k++){
      for($i=0;$i<count($CABECERA[$n]);$i++){
        $borde="LRTB";
        if(isset($CABECERA[$n][$i]["borde"]))
          $borde=$CABECERA[$n][$i]["borde"];
        
				$id=$CABECERA[$n][$i]["id"];
				if($id!="#"){
					$texto=$CONSULTA[$k][$id];
					if(isset($CABECERA[$n][$i]["formato"]))
						if($CABECERA[$n][$i]["formato"]=="numerico"){
							$texto=number_format($CONSULTA[$k][$id],2,",",".");
							if(!isset($SUMA[$id]))
								$SUMA[$id]=0;
							$SUMA[$id]+=$CONSULTA[$k][$id];
						}
				}
				else{
					$texto=$k+1;
				}

        
        $this->Cell(
                    $CABECERA[$n][$i]["ancho"],//ancho
                    $default_fila_alto,//alto
                    utf8_decode($texto),//texto
                    $borde,//borde
                    ($i<(count($CABECERA[$n])-1)?0:1),//salto de linea
                    isset($CABECERA[$n][$i]["alinear"])?$CABECERA[$n][$i]["alinear"]:"L",//alineacion
                    1//aplica fondo
                    );
      }
    }
  }
}







?>