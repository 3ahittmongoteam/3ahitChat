/*
 * backend connection functions are supposed to connect the UI with the
 * client side backend.
 */
var sidebar;
var channelList = [];
var settingBubbleBlock = false;
var settingBubble;
var settingBubbleArrow;
var settingButton;

function initUI() {
	sidebar = document.getElementById("sidebar");
	settingBubble = document.getElementById("settingbubble");
	settingBubbleArrow = document.getElementById("settingbubblearrow");
	settingButton = document.getElementById("settingButton");
	addChannel("default", 0);
	addChannel("channel 4me", 1);
	addChannel("channel 4you", 2);
	addChannel("channel 4her", 3);
	addChannel("channel 4him", 4);
	addChannel("channel 4them", 5);
}

function on_channelClick(element, globalID) {
	var channels = sidebar.children;
	if(element.className != "channel marked")
		notifyChannelChange(globalID);
	for(var i = 0; i < channels.length; i++)
		channels[i].className = "channel";
	element.className = "channel marked";
}

function addChannel(name, globalID) {
	var channel = document.createElement("div");
	var channelName = document.createTextNode(name);
	channel.appendChild(channelName);
	channel.className = "channel";
	//Simple javascript event sender hack
	var obj = {
		handleEvent: function() {
			on_channelClick(this.me, this.ID);
		},
		me: channel,
		ID: globalID
	};
	channel.addEventListener("click", obj, false);
	channelList[globalID] = channel;
	sidebar.appendChild(channel);
	on_channelClick(channel, globalID);
}

function on_SettingButtonClick() {
	settingBubble.style.display = "block";
	settingBubbleArrow.style.display = "block";
	settingBubbleBlock = true;
}

function on_BodyClick() {
	if(settingBubbleBlock)
		settingBubbleBlock = false;
	else {
		settingBubble.style.display = "none";
		settingBubbleArrow.style.display = "none";
	}
}

function goFullScreen() {
	var body = document.getElementsByTagName("body")[0];
	if(body.requestFullscreen) {
		body.requestFullscreen();
	}else if (body.msRequestFullscreen) {
		body.msRequestFullscreen();
	}else if (body.mozRequestFullScreen) {
		body.mozRequestFullScreen();
	}else if (body.webkitRequestFullscreen) {
		body.webkitRequestFullscreen();
	}
}

function keepBubbleAlive() {
	settingBubbleBlock = true;
}

function notifyChannelChange(globalID) {} //backend connection