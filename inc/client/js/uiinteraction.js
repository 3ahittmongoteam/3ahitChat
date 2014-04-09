/*
 * backend connection functions are supposed to connect the UI with the
 * client side backend.
 */
var sidebar;

function initUI() {
	sidebar = document.getElementById("sidebar");
	addChannel("default");
	addChannel("channel 4me");
	addChannel("channel 4you");
	addChannel("channel 4her");
	addChannel("channel 4him");
	addChannel("channel 4them");
	on_channelClick(sidebar.children[0]);
}

function on_channelClick(element) {
	var channels = sidebar.children;
	if(element.className != "channel marked")
		notifyChannelChange(element.innerHTML);
	for(var i = 0; i < channels.length; i++)
		channels[i].className = "channel";
	element.className = "channel marked";
}

function addChannel(name) {
	var channel = document.createElement("div");
	var channelName = document.createTextNode(name);
	channel.appendChild(channelName);
	channel.className = "channel";
	//Simple javascript event sender hack
	var obj = {
		handleEvent: function() {
			on_channelClick(this.me);
		},
		me: channel
	};
	channel.addEventListener("click", obj, false);
	sidebar.appendChild(channel);
}

function notifyChannelChange(name) {} //backend connection