<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
error_reporting(E_ALL);
ini_set("display_errors","On");

ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);
set_time_limit(-1);
date_default_timezone_set('America/Caracas');

$param = getopt("m::",["method::"]);
$method="uploader";
if(isset($param["method"]) && in_array($param["method"],["email","uploader"]))
  $method=$param["method"];

if(file_exists("../library/siga.config.php"))
  include("../library/siga.config.php");
else{
  print "No existe siga.config.php";
  exit;
}

$pg_dump_path = '/usr/bin/pg_dump';
$pg_dump_list = [
  "C:/Bitnami/wappstack-8.1.8-0/postgresql/bin/pg_dump.exe",
  "C:/Bitnami/wappstack-8.1.6-0/postgresql/bin/pg_dump.exe",
  "C:/Bitnami/wappstack-8.0.13-0/postgresql/bin/pg_dump.exe",
  "C:/Bitnami/wappstack-8.0.6-0/postgresql/bin/pg_dump.exe"
];

for($i=0; $i<count($pg_dump_list); $i++) {
  if(file_exists($pg_dump_list[$i])){
    $pg_dump_path = $pg_dump_list[$i];
    break;
  }
}

if(!file_exists($pg_dump_path)){
  print "No existe $pg_dump_path";
  exit;
}
$database_index=SIGA_CONFIG::$database_default;
$database=SIGA_CONFIG::$database[$database_index];
$send_to="pintocar83@gmail.com";
//print_r($database);

putenv('PGPASSWORD=' . $database['password']);
putenv('PGUSER=' . $database['user']);
putenv('PGHOST=' . $database['server']);
putenv('PGDATABASE=' . $database['name']);
if(isset($database['port']) and $database['port']){
  putenv('PGPORT=' . $database['port']);
}


$path="../data/{$database_index}/backup";
$datetime=date("Y-m-d_H:i:s");
$filename=str_replace(["-",":"],"",$datetime)."_".$database['name'].".sql.gz";

if(!file_exists("$path")){
  mkdir("$path",0777,true);
}

$cmd = "$pg_dump_path -Z 9 --file={$path}/{$filename}";
//print $cmd;
passthru($cmd);

if(!file_exists("{$path}/{$filename}")){
  print "No puedo crear el backup";
  exit;
}

switch($method){
  case "email":
    require '../library/phpmailer/Exception.php';
    require '../library/phpmailer/PHPMailer.php';
    require '../library/phpmailer/SMTP.php';

    $mail = new PHPMailer(true);

    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->SMTPDebug = 0;
    $mail->Host = 'dsp.com.ve';
    $mail->Port = 465;
    $mail->isSMTP();
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = true;
    $mail->Username = 'backup@dsp.com.ve';
    $mail->Password = 'a,(AV!gO3sBO';
    $mail->setFrom('backup@dsp.com.ve', 'DSP::AUTO-Backup');

    $mail->isHTML(true);

    $mail->addAddress($send_to, "");

    $mail->Subject = utf8_decode("Backup SIGA - ".$database["description"]. " [".str_replace("_"," ",$datetime)."]");
    $mail->msgHTML("<b>Respaldo de la BD: $filename</b>");
    $mail->addAttachment("{$path}/{$filename}","{$filename}");

    if(!$mail->send()){
      print "Error al enviar el corrreo: ".$mail->ErrorInfo;
    }
    else{
      print "Envio realizado...";
    }
  break;
  case "uploader":
    print "Uploader...";
    // SERVER A - UPLOAD FILE VIA CURL POST
    // (A) SETTINGS
    $url = "http://localhost/E/siga_php8/task/backup_receiver.php"; // where to upload file to
    $file = "{$path}/{$filename}"; // file to upload
    $upname = "$filename"; // file name to be uploaded as

    // (B) NEW CURL FILE
    $cf = new CURLFile($file, mime_content_type($file), $upname);

    // (C) CURL INIT
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
      "upload" => $cf, // attach file upload
      "key" => UPLOADER_KEY
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // (D) CURL RUN
    // (D1) GO!
    $result = curl_exec($ch);

    // (D2) CURL ERROR
    if (curl_errno($ch)) {
      echo "CURL ERROR - " . curl_error($ch);
    }

    // (D3) CURL OK - DO YOUR "POST UPLOAD" HERE
    else {
      // $info = curl_getinfo($ch);
      // print_r($info);
      echo $result;
    }

    // (D4) DONE
    curl_close($ch);
  break;
}

?>