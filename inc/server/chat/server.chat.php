<?php
require_once("class.user.php");
require_once("class.command-manager.php");
require_once("class.Log.php");

class Server(){
	private $mastersocket;
	private $masterpasswort;
	private $cmdManager;
	private $sockets;
	private $users = new array();
	
	//Baut auf den Mastersocket einen Socket, der bereit zum hören ist, aber noch nicht aktiviert ist
	public function __construct(){
		$this->mastersocket = buildServer(SOCKET_ADDRESS,SOCKET_PORT);
		$this->cmdManager = new CommandManager($this);
		$this->sockets = =new array($this->mastersocket);
	}

	public function run(){
		socket_listen($this->mastersocket,MAX_CLIENT) or die("socket_listen() failed");		
		say(PHP_EOL . "Server Started : ".date('Y-m-d H:i:s'));
		say("Master socket  : ".$master);
		say("Listening on   : ".SOCKET_ADDRESS." port ".SOCKET_PORT);
		
		while(true){
			set_time_limit(0);
			ob_implicit_flush();						
			
			$changed = $this->sockets;
			@socket_select($changed,NULL,NULL,NULL);
			
			foreach($changed as $SingleSocket){
				if($SingleSocket == $this->mastersocket){
					$newClientOnMasterSocket = socket_accept($this->mastersocket);
					if($newClientOnMasterSocket == false){ say("socket_accept() failed"); continue; }
					else { 
						connect($newClientOnMasterSocket); 
					}
				} else {
					$bytes = @socket_recv($SingleSocket, $buffer, MAX_SIZE, 0);
					if($bytes == 0){ disconnectSocket($SingleSocket); }
					else{
						$user = getUserBySocket($socket);
						if(!$user->handshake){dohandshake($user,$buffer); }
						else { process($user,$buffer); }
					}
				}
			}
		}	
	}
	
	private function connect($socketToConnect){
		$user = new User($socketToConnect);
		array_push($this->users, $user);
		array_push($this->sockets, $socketToConnect);
	}
	
	public function disconnectSocket($socketToDisconnect){
		if(!isset($socketToDisconnect) || empty($socketToDisconnect)) return false;
		  
		//Delete from User Array
		$found=null;
		$n=count($this->users);
		for($i=0;$i<$n;$i++){
			if($this->users[$i]->socket == $socketToDisconnect){ $found=$i; break; }
		}
		  
		//Delete from Socket Array
		$index = array_search($socketToDisconnect,$this->sockets);
		socket_close($socketToDisconnect) or return false;
		if($index >= 0 && !is_null($found)){ 
			array_splice($this->sockets,$index,1);
			array_splice($this->users,$found,1);	
			return true;			
		}
		return false;		
	}
	public function disconnectId($IDToDisconnect){
		if(!isset($socketToDisconnect) || empty($socketToDisconnect)) return false;
		  		  
		$socketFromUser = NULL;
		$found=null;
		$n=count($this->users);
		  
		  
		for($i=0;$i<$n;$i++){
			if($this->users[$i]->id == $IDToDisconnect){ 
				$found = $i; 
				$socketFromUser = $this->users[$i]->socket; 
				break; 
			}
		}
		$index = array_search($socketFromUser,$this->sockets);
		socket_close($socketFromUser) or return false;
		if($index>=0 && !is_null($found)){ 
			array_splice($this->sockets,$index,1); 
			array_splice($this->users,$found,1);
			return true;
		}		
		return false;
	}
	public function disconnectName($NameToDisconnect){
		if(!isset($NameToDisconnect) || empty($NameToDisconnect)) return false;
		  		  
		$socketFromUser = NULL;
		$found=null;
		$n=count($this->users);
		  
		  
		for($i=0;$i<$n;$i++){
			if($this->users[$i]->name == $NameToDisconnect){ 
				$found = $i; 
				$socketFromUser = $this->users[$i]->socket; 
				break; 
			}
		}
		$index = array_search($socketFromUser,$this->sockets);
		socket_close($socketFromUser) or return false;
		if($index>=0 && !is_null($found)){ 
			array_splice($this->sockets,$index,1); 
			array_splice($this->users,$found,1);
			return true;
		}		
		return false;
	}
	public function disconnectUser($UserToDisconnect){,
		if(!isset($UserToDisconnect) || empty($UserToDisconnect)) return false;
		
		$userindex = array_search($UserToDisconnect,$this->users);		
		$socketFromUser = $this->users[$userindex]->socket;		
		
		$socketindex = array_search($socketFromUser,$this->sockets);		
		socket_close($socketFromUser);
		
		if($userindex>=0 && $socketindex>=0){ 
			array_splice($this->users,$userindex,1); 
			array_splice($this->sockets,$socketindex,1);
			return true;
		}
		return false;
	}
	
	public function getUserBySocket($socketToFind){
		foreach($this->users as $singleUser){
			if($singleUser->socket == $socketToFind)
				return $singleUser;
		}
	}
	public function getUserByID($IDToFind){
		foreach($this->users as $singleUser){
			if($singleUser->id == $IDToFind)
				return $singleUser;
		}
	}
	public function getUserByName($NameToFind){
		foreach($this->users as $singleUser){
			if($singleUser->name == $NameToFind)
				return $singleUser;
		}
	}
	public function getUsersByIP($ipToFind){
		$uarr = new array();
		foreach($this->users as $singleUser){
			if($singleUser->ip == $ipToFind)
				array_push($uarr, $singleUser);
		}
		return $uarr;
	}
	
	//Returns a assoziativ array with the connected Users.
	//The key of the array is the user ID and the value is the name.
	public function getConnectedUsers(){
		$uarr = new array();
		foreach($this->users as $user){
			$uarr[$user->id] = $user->name;
		}
		return $uarr;
	}
	
	private function buildServer($address,$port){
		if(SOCKET_MASTER_PASSWORT == ""){
			$this->masterpasswort=NULL;
		} else {
			$this->masterpasswort = SOCKET_MASTER_PASSWORT;
		}   
		$socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
		socket_bind($socket, $address, $port)                    or die("socket_bind() failed");
		return $socket;
	}
	
	private function dohandshake($user,$buffer){
	  global $users;
	  global $roomLimit;
	  console("\nRequesting handshake...");
	  list($resource,$host,$origin,$strkey,$data) = getheaders($buffer);
	  console("Handshaking...");

		$accept_key = $strkey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
		$accept_key = sha1($accept_key, true);
		$accept_key = base64_encode($accept_key);

	  $upgrade  = "HTTP/1.1 101 Switching Protocols\r\n" .
				  "Upgrade: WebSocket\r\n" .
				  "Connection: Upgrade\r\n" .
				  "Sec-WebSocket-Accept: ". $accept_key . "\r\n" .
				  "Sec-WebSocket-Origin: " . $origin . "\r\n" .
				  "Sec-WebSocket-Location: ws://" . $host . $resource . "\r\n\r\n";
	  socket_write($user->socket,$upgrade,strlen($upgrade));
	  $user->handshake=true;
	  console($upgrade);
	  console("Done handshaking...");
		if(count($users) > $roomLimit || count($users) > 128)
		{
			say("Server Room Full");
			$tofull = "You could not connect, because the room is full(".(count($users)-1)."/".($roomLimit).")";
			var_dump($roomLimit,count($users) );
			send($user->socket,$tofull); 
			return disconnect($user->socket, true);
		}
	  return true;
	}

	private function getheaders($req){
	  $r=$h=$o=null;
	  if(preg_match("/GET (.*) HTTP/"   ,$req,$match)){ $r=$match[1]; }
	  if(preg_match("/Host: (.*)\r\n/"  ,$req,$match)){ $h=$match[1]; }
	  if(preg_match("/Origin: (.*)\r\n/",$req,$match)){ $o=$match[1]; }
	  if(preg_match("/Sec-WebSocket-Key: (.*)\r\n/",$req,$match)){ $key=$match[1]; }
	  if(preg_match("/\r\n(.*?)\$/",$req,$match)){ $data=$match[1]; }
	  return array($r,$h,$o,$key,$data);
	}
	
	
	//Gibt Text in die Console aus
	public function console($msg=""){ echo $msg."\n"; }
}
?>