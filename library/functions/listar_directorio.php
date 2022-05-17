<?php 
function listar_directorio($dir){   
  $result=array();
  $cdir=scandir($dir);
  foreach($cdir as $key => $value){ 
    if(!in_array($value,array(".",".."))){ 
      if(is_dir($dir . DIRECTORY_SEPARATOR . $value))   
        //$result[$value]=listar_directorio($dir . DIRECTORY_SEPARATOR . $value);
        $result[]=array(
          "iconCls"=>"perfil-data-nodo",
          "text"=>"$value",
          "file"=> $dir . DIRECTORY_SEPARATOR . $value,
          "leaf"=>false,
          "children"=> listar_directorio($dir . DIRECTORY_SEPARATOR . $value)
        );
      else 
        //$result[]=$value;
        $result[]=array(
          "iconCls"=>"perfil-tipo-nodo",
          "text"=>"$value",
          "file"=>$dir . DIRECTORY_SEPARATOR . $value,
          "leaf"=>true,
        );
    }
  }
  return $result; 
}

//print_r(listar_directorio("/var/www/siga/data/persona/V16315637"));
//array("iconCls"=>"perfil-tipo-nodo","text"=>$tmp[$j],"leaf"=>true);
/*

for($j=0;$j<count($tmp);$j++)
        $hojas[]=array("iconCls"=>"perfil-tipo-nodo","text"=>$tmp[$j],"leaf"=>true);
      
      $nodos[]=array(
        "iconCls"=>"perfil-data-nodo",
        "text"=>$perfil[$i]["anio"],
        "leaf"=>false,
        "children"=>$hojas,
        "checked"=> ($perfil[$i]["activo"]=='t')?true:false
      );

*/
?>

