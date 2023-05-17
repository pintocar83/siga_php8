<?php
$keys=[
  "123456"
];
if(!in_array($_POST["key"],$keys)){
  print "Invalid Key";
  exit;
}
echo "SERVER FILE UPLOAD - ";
echo move_uploaded_file($_FILES["upload"]["tmp_name"], "../../../backup_uploader/".$_FILES["upload"]["name"].".backup")
? "OK" : "ERROR" ;
?>