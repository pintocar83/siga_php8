<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set("display_errors","On");

ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);
set_time_limit(-1);

if(file_exists("../library/siga.config.php"))
  include("../library/siga.config.php");
else{
  print "No existe siga.config.php";
  exit;
}

$pg_dump_path = '/usr/bin/pg_dump';
if(file_exists("C:/Bitnami/wappstack-8.1.6-0/postgresql/bin/pg_dump.exe")){
  $pg_dump_path = "C:/Bitnami/wappstack-8.1.6-0/postgresql/bin/pg_dump.exe";
}
else if(file_exists("C:/Bitnami/wappstack-8.0.13-0/postgresql/bin/pg_dump.exe")){
  $pg_dump_path = "C:/Bitnami/wappstack-8.0.13-0/postgresql/bin/pg_dump.exe";
}

$database=SIGA_CONFIG::$database[SIGA_CONFIG::$database_default];
$send_to="pintocar83@gmail.com";
//print_r($database);

putenv('PGPASSWORD=' . $database['password']);
putenv('PGUSER=' . $database['user']);
putenv('PGHOST=' . $database['server']);
putenv('PGDATABASE=' . $database['name']);
if(isset($database['port']) and $database['port']){
  putenv('PGPORT=' . $database['port']);
}

if(!file_exists("backup")){
  mkdir("backup",0777,true);
}

$path="backup";
$filename=date("Y-m-d_His")."_".$database['name'].".sql.gz";

$cmd = "$pg_dump_path -Z 9 --file={$path}/{$filename}";
//print $cmd;
passthru($cmd);

if(file_exists("{$path}/{$filename}")){
  require '../library/phpmailer/Exception.php';
  require '../library/phpmailer/PHPMailer.php';
  require '../library/phpmailer/SMTP.php';

  $mail = new PHPMailer(true);

	$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->Host = 'dsp.com.ve';
	$mail->Port = 465;
  $mail->isSMTP();
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$mail->SMTPAuth = true;
	$mail->Username = 'backup@dsp.com.ve';
	$mail->Password = 'a,(AV!gO3sBO';
	$mail->setFrom('backup@dsp.com.ve', 'DSP::BD-Backup');

  $mail->isHTML(true);

  $mail->addAddress($send_to, "");

  $mail->Subject = utf8_decode("Backup ".$database["description"]);
	$mail->msgHTML("<b>Respaldo de la BD: $filename</b>");
	$mail->addAttachment("{$path}/{$filename}","{$filename}");

  if(!$mail->send()){
    print "Error al enviar el corrreo: ".$mail->ErrorInfo;
  }
  else{
    print "Envio realizado...";
  }
}

?>