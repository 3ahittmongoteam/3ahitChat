part of ahittchat_client;

class Gui {
  DivElement _sidebar = querySelector("#sidebar");
  DivElement _settingBubble = querySelector("#settingBubble");
  DivElement _settingBubbleArrow = querySelector("#settingBubbleArrow");
  DivElement _settingButton = querySelector("#settingButton");
  AnchorElement _fullscreen = querySelector("#fullscreen");
  BodyElement _body = document.body;
  bool _settingBubbleBlock = false;
  
  void init() {
    _settingButton.onClick.listen(_on_settingButton_click);
    _body.onClick.listen(_on_body_click);
    _fullscreen.onClick.listen((MouseEvent e) => _body.requestFullscreen());
    _settingBubble.onClick.listen((MouseEvent e) => _settingBubbleBlock = true);
  }
  
  void _on_settingButton_click(MouseEvent e) {
    _settingBubble.style.display = "block";
    _settingBubbleArrow.style.display = "block";
    _settingBubbleBlock = true;
  }
  
  void _on_body_click(MouseEvent e) {
    if(_settingBubbleBlock)
      _settingBubbleBlock = false;
    else {
      _settingBubble.style.display = "none";
      _settingBubbleArrow.style.display = "none";
    }
  }
}