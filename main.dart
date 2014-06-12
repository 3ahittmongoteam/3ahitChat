library ahittchat_server;

import "dart:io";
import "package:http_server/http_server.dart";

VirtualDirectory vdir;

void main() {
  print("Starting ahittchat server...");
  vdir = new VirtualDirectory('www/build/web/')
    ..jailRoot = true
    ..allowDirectoryListing = false;
  HttpServer.bind('127.0.0.1', 8080)
    .then((HttpServer server) {
      print('Listening for connections...');
      server.listen((HttpRequest request) {
        if(request.uri.path == '/ws/') {
          print('WebSocket request.');
          WebSocketTransformer.upgrade(request).then((WebSocket websocket) {
            websocket.listen(_handleMessage);
          }, onError: (error) => print('Wrong WebSocket request: $error'));
        }else if(request.uri.path == '/') {
          request.response
            ..redirect(new Uri.http(request.uri.authority, request.uri.path 
                + 'index.html'))
            ..close();
        }else {
          print('Http request.');
          vdir.serveRequest(request);
        }
      });
    }, onError: (error) => print('Error starting webserver: $error'));
}

void _handleMessage(message) {
  print(message);
}