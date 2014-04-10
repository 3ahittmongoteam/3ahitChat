<?php
	require_once("settings.php");
	require_once("inc/server/chat/server.chat.php");
	require_once("inc/server/chat/class.user.php");
	require_once("inc/server/chat/class.command-manager.php");
	require_once("inc/server/chat/class.Log.php");
	require_once("inc/server/chat/class.channel.php");
	$sv = new ChatServer();
	$sv->run();
?>