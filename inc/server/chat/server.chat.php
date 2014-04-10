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
					if($bytes == 0){ disconnect($SingleSocket); }
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
		  $found=null;
		  $n=count($users);
		  for($i=0;$i<$n;$i++){
			if($users[$i]->socket==$socket){ $found=$i; break; }
		  }
		  if(!$nomessage) { 
			say(getuserbysocket($socket)->name." disconnected!");
			sendall(NULL, getuserbysocket($socket)->name." disconnected!");
		  }
		  if(!is_null($found)){ array_splice($users,$found,1); }
		  $index = array_search($socket,$sockets);
		  socket_close($socket);
		  if($index>=0){ array_splice($sockets,$index,1); }
	}
	public function disconnectId($IDToDisconnect){
		  $found=null;
		  $n=count($users);
		  for($i=0;$i<$n;$i++){
			if($users[$i]->socket==$socket){ $found=$i; break; }
		  }
		  if(!$nomessage) { 
			say(getuserbysocket($socket)->name." disconnected!");
			sendall(NULL, getuserbysocket($socket)->name." disconnected!");
		  }
		  if(!is_null($found)){ array_splice($users,$found,1); }
		  $index = array_search($socket,$sockets);
		  socket_close($socket);
		  if($index>=0){ array_splice($sockets,$index,1); }
	}
	public function disconnectName($NameToDisconnect){
		  $found=null;
		  $n=count($users);
		  for($i=0;$i<$n;$i++){
			if($users[$i]->socket==$socket){ $found=$i; break; }
		  }
		  if(!$nomessage) { 
			say(getuserbysocket($socket)->name." disconnected!");
			sendall(NULL, getuserbysocket($socket)->name." disconnected!");
		  }
		  if(!is_null($found)){ array_splice($users,$found,1); }
		  $index = array_search($socket,$sockets);
		  socket_close($socket);
		  if($index>=0){ array_splice($sockets,$index,1); }
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
	
	
	
	//Gibt Text in die Console aus
	public function say($msg=""){ echo $msg."\n"; }
}
?>