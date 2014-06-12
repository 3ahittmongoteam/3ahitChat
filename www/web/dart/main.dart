library ahittchat_client;

import "dart:html";

part "Gui.dart";
part "Client.dart";

void main() {
  Gui gui = new Gui();
  gui.init();
  Client client = new Client("ws://127.0.0.1:8080/ws/");
}