<?php
//recibe el rango de paginas en los formatos
// 1,2,5,9   o   1-3    o    1-2,5-6,10,11,12,20-50
// y retorna un arreglo con el listado de paginas
function rangoPaginas($p){
  $a=explode(",",$p);
  $r=array();
  for($i=0;$i<count($a);$i++){
    $a2=explode("-",$a[$i]);
    switch(count($a2)){
      case 1:
        $r[]=$a2[0];
        break;
      case 2:
        if($a2[0]<=$a2[1])
          for($j=$a2[0];$j<=$a2[1];$j++)
            $r[]=$j;
        else
          for($j=$a2[0];$j>=$a2[1];$j--)
            $r[]=$j;
        break;
      default:
        return NULL;
    }
  }
  return $r;
}
?>