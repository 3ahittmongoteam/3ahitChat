<?php
interface ICommand {
	public function matches($command);
	public function execute($server, $user, $args);
}
?>