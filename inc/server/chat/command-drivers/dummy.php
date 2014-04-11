<?php
/*
 * This command driver example shows large parts of the command driver API.
 */
class dummy implements ICommand {
	private $counter = 0;

	public function matches($command) {
		return $command == 'dummy' || $command == 'mummy';
	}
	
	public function execute($server, $user, $args, $origin) {
		$header = array('Error'=>'CommandError');
		$server->send($user, 'You idiot! Used ' . $args[$origin] . ' ' . $this->counter . ' time.', $header);
		return true;
	}
}
?>