<?php
class comprobante_tipo{
  public static function onGet($tipo){
    $db=SIGA::DBController();
    $sql="SELECT denominacion FROM modulo_base.comprobante_tipo WHERE tipo='$tipo'";
    return $db->Execute($sql);
  }  
}  
?>