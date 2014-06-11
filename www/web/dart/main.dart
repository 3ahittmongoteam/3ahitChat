library ahittchat_client;

import "dart:html";

part "Gui.dart";
part "Client.dart";

void main() {
  Gui gui = new Gui();
  gui.init();
  Client client = new Client("ws://" + window.location.host + "/ws");
}