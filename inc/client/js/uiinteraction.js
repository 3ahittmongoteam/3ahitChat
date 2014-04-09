/*
 * backend connection functions are supposed to connect the UI with the
 * client side backend.
 */
var sidebar;
var channelList = [];

function initUI() {
	sidebar = document.getElementById("sidebar");
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

function notifyChannelChange(globalID) {} //backend connection