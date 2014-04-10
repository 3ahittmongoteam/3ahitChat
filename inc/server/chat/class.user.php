<?php
class User{
  private $id;
  
  public $name;  
  public $ip;  
  public $handshake = false;
  public $socket;
  public $privileges;
  public $channel;
  
  public function __construct($socket, $name="", $ip="unknown"){
	$this->socket = $socket;
	$this->name = $name;
	$this->ip = $ip;
	
    $this->id = substr(((string)$socket), strlen((string)$socket)-2);	
  }
  public function getID(){
	return $this->id;
  }    
}
?>