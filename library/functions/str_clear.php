<?php
function str_clear($str){
  return trim(str_replace(array("\"","'","/*","*/","--"),"",$str));
}
?>