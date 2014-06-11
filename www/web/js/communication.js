function Communication(host, port) {
	this.host = host;
	this.port = port;
}

Communication.prototype.onPacket = function(header, message) {}

Communication.prototype.sendPacket = function(header, message) {
	var packet = "";
	for(var key in header)
		packet += key + ":" + header[key] + "\n";
	if(packet == "")
		packet += "\n";
	packet += "\n";
	
	packet += message;
	this.socket.send(packet);
}

Communication.prototype.init = function() {
	this.socket = new WebSocket("ws://" + this.host + ":" + this.port);
	this.socket.onopen = this.open;
	this.socket.onmessage = this.message;
	this.socket.onerror = this.error;
}

Communication.prototype.open = function(event) {
	
}

Communication.prototype.message = function(event) {
	var sections = event.data.split("\n\n");
	var header = encodeHeader(sections[0]);
	var message = "";
	for(var i = 1; i < sections.length; i++)
		message += sections[i];
	this.onPacket(header, message);
}

Communication.prototype.error = function(event) {
	alert("Could not connect...");
}

function encodeHeader(headerStr) {
	var header = [];
	var pairs = headerStr.split("\n");
	for(var i = 0; i < pairs.length; i++) {
		var keyValue = pairs[i].split(":");
		header[keyValue[0]] = keyValue[1];
	}
	return header;
}