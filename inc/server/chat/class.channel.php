<?php
require_once("../../../settings.php");
class Channel {
	private static $idcounter = 0;
	private $id;
	private $name;
	private $maxUser = MAX_CLIENT_CHANNEL_DEFAULT;
	private $owner;
	private $server;
	
	public function __construct($server, $name, $owner) {
		$this->id = self::$idcounter;
		self::$idcounter++;
		$this->name = $name;
		$this->owner = $owner;
		$this->server = $server;
	}
	
	//Unstable interface
	public function removeUsers() {
		foreach($this->server->users as $user)
			if($user->channel == $this->id) {
				$user->channel = 0;
				$this->server->send($user->socket, 'CHANNEL', 'You got moved to channel ' . 
						$this->server->channels[0]->name);
			}
	}
}
?>