<?php
include_once("bisiesto.php");
function dias_meses($anio){
  return array( 31,
                bisiesto($anio)?29:28,
                31,
                30,
                31,
                30,
                31,
                31,
                30,
                31,
                30,
                31);
}
?>