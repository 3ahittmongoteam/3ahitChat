part of ahittchat_client;

class Client {
  Map<int, String> _channelOwners = new Map<int, String>();
  Map<int, String> _channelNames = new Map<int, String>();
  Map<int, DivElement> _channelDivs = new Map<int, DivElement>();
  Map<int, DivElement> _messageContents = new Map<int, DivElement>();
  int _aktiveChannel;
  WebSocket _socket;
  
  Client(String url) {
    _socket = new WebSocket(url);
    _socket.onMessage.listen(_on_message);
    _socket.onOpen.listen((e) => print("WebSocket connected."));
  }
  
  void _on_message(MessageEvent e) {
    
  }
}
