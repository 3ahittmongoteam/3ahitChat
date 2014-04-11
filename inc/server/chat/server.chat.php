<?php
define("DEBUGBOOL", false);
class ChatServer{
	private $mastersocket;
	private $masterpasswort;
	private $cmdManager;
	private $sockets;
	
	public $users = array();
	public $channels = array();
	public $char_limit = MAX_SIZE;
	public $global_limit = MAX_CLIENT_GLOBAL;
	
	//Baut auf den Mastersocket einen Socket, der bereit zum hören ist, aber noch nicht aktiviert ist
	public function __construct(){
		debug("Chat-Server constructed");
		$this->cmdManager = new CommandManager($this);		
	}

	public function run(){	
		$this->mastersocket = $this->buildServer(SOCKET_ADDRESS,SOCKET_PORT);		
		$this->sockets = array($this->mastersocket);
		socket_listen($this->mastersocket,MAX_CLIENT_GLOBAL) or die("socket_listen() failed");		
		$this->console("\n" . "Server Started : ".date('Y-m-d H:i:s'));
		$this->console("Master socket  : ".$this->mastersocket);
		$this->console("Listening on   : ".SOCKET_ADDRESS." port ".SOCKET_PORT);
		
		while(true){
			set_time_limit(0);
			ob_implicit_flush();						
			
			$changed = $this->sockets;
			$e = $a = NULL;
			@socket_select($changed,$e, $a,0);
			
			foreach($changed as $SingleSocket){
				if($SingleSocket == $this->mastersocket){
					$newClientOnMasterSocket = socket_accept($this->mastersocket);
					if($newClientOnMasterSocket == false){ $this->console("socket_accept() failed"); continue; }
					else { 
						$this->connect($newClientOnMasterSocket); 
					}
				} else {
					$bytes = @socket_recv($SingleSocket, $buffer, 1024, 0);
					if($bytes == 0){ debug($this->getUserBySocket($SingleSocket)->name . " disconnected!"); $this->disconnectSocket($SingleSocket); }
					else{						
						debug("Message recived");
						$user = $this->getUserBySocket($SingleSocket);
						if(!$user->handshake){ debug("Initializing handshake"); $this->dohandshake($user,$buffer); }
						else { $this->processMessage($user,$buffer); }
					}
				}
			}
		}	
	}
	
	private function processMessage($sender, $msg){
		$value = unpack('H*', $msg[0]);
		$opcode =  base_convert($value[1], 16, 10);
		if($opcode == 136 ||$opcode == 8) return $this->disconnectSocket($sender->socket);
		$msg = $this->decode($msg);
		
		$whole = explode("\n\n", $msg);
		if(count($whole) < 2) { return $this->console("Bad Package: No Headers were transmited"); }
		$action = $whole[1];
		$header = $whole[0];
		if(strlen($action) > $this->char_limit) return $this->send($sender, "", array("Error"=>"TextTooLong","MaxAnzahl"=>$char_limit));
		
		if($this->cmdManager->processCommand($sender, $action)) return $this->console("Command executed: ". $action);

		switch($action){
			case ""  : $this->send($sender,"", array("Error"=>"NoMessage"));  break;
		default      : $this->sendAll($sender,$action);   					  break;
	  }
		
	}
	
	private function connect($socketToConnect){
		debug("New user");
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
		socket_close($socketToDisconnect);
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
		socket_close($socketFromUser);
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
		socket_close($socketFromUser);
		if($index>=0 && !is_null($found)){ 
			array_splice($this->sockets,$index,1); 
			array_splice($this->users,$found,1);
			return true;
		}		
		return false;
	}
	public function disconnectUser($UserToDisconnect){
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
		$uarr = array();
		foreach($this->users as $singleUser){
			if($singleUser->ip == $ipToFind)
				array_push($uarr, $singleUser);
		}
		return $uarr;
	}
	
	//Returns a assoziativ array with the connected Users.
	//The key of the array is the user ID and the value is the name.
	public function getConnectedUsers(){
		$uarr = array();
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
		debug("Requesting handshake...");
		list($resource,$host,$origin,$strkey,$data) = $this->getheaders($buffer);
		debug("Handshaking...");

		$accept_key = $strkey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
		$accept_key = sha1($accept_key, true);
		$accept_key = base64_encode($accept_key);

		$upgrade= "HTTP/1.1 101 Switching Protocols\r\n" .
				  "Upgrade: WebSocket\r\n" .
				  "Connection: Upgrade\r\n" .
				  "Sec-WebSocket-Accept: ". $accept_key . "\r\n" .
				  "Sec-WebSocket-Origin: " . $origin . "\r\n" .
				  "Sec-WebSocket-Location: ws://" . $host . $resource . "\r\n\r\n";
		socket_write($user->socket,$upgrade,strlen($upgrade));
		$user->handshake=true;
		debug($upgrade);
		debug("Done handshaking...");
		if(count($this->users) > MAX_CLIENT_GLOBAL)
		{
			$this->console("Server Room Full");
			send($user,"", array("error"=>"RoomFull", "maxUser"=>$global_limit)); 
			return disconnectSocket($user->socket, true);
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
	private function wrap($msg=""){ 
	$formated = array();
	$formated[0] = chr(129);
	if(strlen($msg) <= 125){
		$formated[1] = chr(strlen($msg));
	} else if(strlen($msg) >= 126 && strlen($msg) <= 65535) {
		$formated[1] = chr(126);
		$formated[2] = chr((strlen($msg) >> 8) & 255);
		$formated[3] = chr((strlen($msg)     ) & 255);
	} else {
		$formated[1] = chr(127);
		$formated[2] = chr((strlen($msg) >> 56) & 255);
		$formated[3] = chr((strlen($msg) >> 48) & 255);
		$formated[4] = chr((strlen($msg) >> 40) & 255);
		$formated[5] = chr((strlen($msg) >> 32) & 255);
		$formated[6] = chr((strlen($msg) >> 24) & 255);
		$formated[7] = chr((strlen($msg) >> 16) & 255);
		$formated[8] = chr((strlen($msg) >>  8) & 255);
		$formated[9] = chr((strlen($msg)      ) & 255);
	}
	
	for ($it = 0; $it < strlen($msg); $it++){
		array_push($formated, $msg[$it]);
	}
	
	$ret = $formated[0];
	if(strlen($msg) <= 125){
		$ret .= $formated[1];
		$ret .= implode(array_slice($formated, 2));
	} else if(strlen($msg) >= 126 && strlen($msg) <= 65535) {
		$ret .= $formated[1];
		$ret .= $formated[2];
		$ret .= $formated[3];
		$ret .= implode(array_slice($formated, 4));
	} else {
		$ret .= $formated[1];
		$ret .= $formated[2];
		$ret .= $formated[3];
		$ret .= $formated[4];
		$ret .= $formated[5];
		$ret .= $formated[6];
		$ret .= $formated[7];
		$ret .= $formated[8];
		$ret .= $formated[9];
		$ret .= implode(array_slice($formated, 10));
	}
	return $ret;
}
	private function decode($msg=""){

		$value = unpack('H*', $msg[1]);
		$datalength =  base_convert($value[1], 16, 10) & 127;
		
		$maskstart = 2;
		if($datalength == 126)
			$maskstart = 4;
		if($datalength == 127)
			$maskstart = 10;

		$mask = array(	$msg[$maskstart + 0],
						$msg[$maskstart + 1],
						$msg[$maskstart + 2],
						$msg[$maskstart + 3]);
		for($a = 0; $a < 4; $a++)
		{		
			$value = unpack('H*', $mask[$a]);
			$mask[$a] = base_convert($value[1], 16, 10);
		}
		$i = $maskstart + 4;
		$index = 0;
		$output = "";
		$curr ="";
		if($i == strlen($msg))
		{
			$this->console("No message recived");
			return "";
		}
		while($i < strlen($msg))
		{
			$curr = $msg[$i++];
			$value = unpack('H*', $curr);
			$curr =  base_convert($value[1], 16, 10);	
			$rdy = chr((int)$curr ^ (int)$mask[$index++ % 4]);		
			$output = $output . htmlentities($rdy);
		}
		return $output;
	}
	
	function send($empfaenger,  $msg, $header=NULL){
		$premsg = "";
		if(isset($header) && !empty($header))
			foreach($header as $key=>$value){
				$premsg .= $key.":".$value."\n";
			}
		$premsg .= "\n";
		if($header == NULL)
			$premsg .= "\n";			
		$this->console($empfaenger->name . ": ". $msg);
		$msg = $this->wrap($premsg . $msg);
		socket_write($empfaenger->socket,$msg,strlen($msg));
	}
 
	function sendAll($sender,  $msg, $header=NULL){
		foreach($this->users as $u){
			$this->send($u, $msg, $header);
		}	
	}
 
	//Gibt Text in die $this->console aus
	public function console($msg=""){ echo $msg."\n"; }
}
function debug($msg){ if(DEBUGBOOL) echo "$msg\n"; }
?>