<?php
class User{
  private var $id;
  
  public var $name;  
  public var $ip;  
  public var $handshake;
  public var $socket;
  
  
  public function __construct($socket, $name, $ip="unknown"){
	$this->socket = $socket;
	$this->name = $name;
	$this->ip = $ip;
	
	$match = "";
    if(preg_match("/(\d{2,4})/",(string)$socket, $match)){ $r=$match[1]; }
    $this->id = intval($r);
    }	
  }
  public function getID(){
	return $this->id;
  }
    
}
?>