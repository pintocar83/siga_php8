<?php
class DBController{
  private $Driver;
  private $Result;
  private $MsgError;
	public $Connection;
	public $mode = NULL;

  public function Connect($driver, $server, $name, $user, $password, $port=""){
	  try{
			$this->Connection=NULL;
      $this->DBDriver=$driver;
			switch($this->DBDriver){
				case "postgres":
				case "pgsql":
					$this->Connection = pg_connect("host=$server dbname=$name user=$user password=$password".($port?" port=".$port:""));
					pg_set_client_encoding($this->Connection,"UTF8");
					break;
				case "mysql":
					$this->Connection = mysqli_connect($server.($port?":".$port:""), $user, $password, $name) or die('Could not connect to server.' );
					$this->Execute("SET NAMES 'UTF8'");
					break;
				case "sqlite3":
					$this->Connection = new SQLite3($server);
					break;
			}
		  if(!$this->Connection){
			  throw new Exception("Error al establecer conexión.");
			}
		}
	catch(Exception $e){
		die($e->getMessage());
		}
	}

	public function ExecutePGSQL($sql){
    $this->Logs($sql);
		$this->MsgError="";
		try{
			ini_set("display_errors","Off");
			$this->Result=pg_query($this->Connection, $sql);
			ini_set("display_errors","On");
			if(!$this->Result){
				throw new Exception("No se pudo realizar la consulta.");
			}
		}
		catch( Exception $e ){
			$this->MsgError=pg_last_error($this->Connection);
			return NULL;
		}
		$return=array();
		while($row = pg_fetch_array($this->Result, NULL, $this->mode?$this->mode:PGSQL_BOTH))
			$return[]=$row;
		return $return;
  }

	public function ExecuteMYSQL($sql){
    $this->Logs($sql);
		$this->MsgError="";
		ini_set("display_errors","Off");
		try{
			$this->Result=mysqli_query($this->Connection, $sql);
			if(!$this->Result){
				throw new Exception("No se pudo realizar la consulta.");
			}
		}
		catch( Exception $e ){
			$this->MsgError=mysqli_error($this->Connection);
			return NULL;
		}
		$return=array();
		while($row = mysqli_fetch_array($this->Result))
			$return[]=$row;
		ini_set("display_errors","On");
		return $return;
  }

  public function ExecuteSQLITE3($sql){
    $this->Logs($sql);
		$this->MsgError="";
		ini_set("display_errors","On");
		try{
			$this->Result=$this->Connection->query($sql);
			if(!$this->Result){
				throw new Exception("No se pudo realizar la consulta.");
			}
		}
		catch( Exception $e ){
			$this->MsgError=$this->Connection->lastErrorMsg();
			return NULL;
		}
		$return=array();
		while($row = $this->Result->fetchArray())
			$return[]=$row;
		ini_set("display_errors","On");
		return $return;
  }

	public function Execute($sql){
		switch($this->DBDriver){
				case "postgres":
				case "pgsql":
					return 	$this->ExecutePGSQL($sql);
				case "mysql":
					return 	$this->ExecuteMYSQL($sql);
				case "sqlite3":
					return 	$this->ExecuteSQLITE3($sql);
			}
			return NULL;
  }

  public function EscapeString($str){
		switch($this->DBDriver){
				case "sqlite":
					return  sqlite_escape_string($str);
				case "postgres":
				case "pgsql":
					return 	pg_escape_string($this->Connection, $str);
				case "mysql":
					return 	mysql_escape_string($str);
			}
		return $str;
  }

  public function Insert($tabla,$columnaValor){
	  $col='(';
	  $val='(';
	  //while(list($clave,$valor)=each($columnaValor)){
	  foreach($columnaValor as $clave => $valor){
		  $col.=$clave.', ';
		  $val.=$valor.", ";
		}
	  $col=substr($col,0,strlen($col)-2);
	  $val=substr($val,0,strlen($val)-2);
	  $col.=')';
	  $val.=')';
	  $sql="INSERT INTO $tabla $col VALUES $val";
	  return $this->Execute($sql)===NULL?FALSE:TRUE;
	}

  public function Update($tabla, $columnaValor, $condicion){
	  $cadena="";
	  //while(list($clave,$valor)=each($columnaValor))
	  foreach($columnaValor as $clave => $valor)
		  $cadena.=$clave."=".$valor.", ";
	  $cadena=substr($cadena,0,strlen($cadena)-2);
	  $sql="UPDATE $tabla SET $cadena WHERE $condicion";
	  return $this->Execute($sql)===NULL?FALSE:TRUE;
	}

  public function Delete($tabla, $condicion="TRUE"){
	  $sql='DELETE FROM '.$tabla.' WHERE '.$condicion;
	  return $this->Execute($sql)===NULL?FALSE:TRUE;
  }

  public function Close(){
		switch($this->DBDriver){
				case "postgres":
				case "pgsql":
					return pg_close($this->Connection);
				case "mysql":
					return mysqli_close($this->Connection);
				case "sqlite3":
					return 	$this->Connection->close();
			}
			return NULL;
  }

  public function GetMsgError(){
    return $this->MsgError;
  }

	public function GetMsgErrorClear(){
		$tmp=explode("CONTEXT:", $this->MsgError);
    return addslashes(str_replace("\n"," ",$tmp[0]));
  }

  public function Logs($sql){
	  //$f=fopen("/var/www/sql.txt","a");
	  //fputs($f,$sql."\n");
	}
}
?>