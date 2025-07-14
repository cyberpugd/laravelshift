if(window.ActiveXObject) {
     nameCtrl = new ActiveXObject("Name.NameCtrl");
} else {
     try {
          nameCtrl = new ActiveXObject("Name.NameCtrl");
     } catch (e){
          nameCtrl = (function(b){
               var c = null;
               try {
                    c = document.getElementById(b);
                    if (!Boolean(c) && (Boolean(navigator.mimeTypes) && navigator.mimeTypes[b] && navigator.mimeTypes[b].enabledPlugin)) {
                         var a = document.createElement("object");
                         a.id = b;
                         a.type = b;
                         a.width = "0";
                         a.height = "0";
                         a.style.setProperty("visibility", "hidden", "");
                         document.body.appendChild(a);
                         c = document.getElementById(b)
                    }
               } catch (d) {
                    c = null
               }
               return c
          })("application/x-sharepoint-uc");
     }
}
if(nameCtrl && nameCtrl.PresenceEnabled){
     nameCtrl.OnStatusChange = function(userName, status, id){
     document.getElementById(id).classList.remove("status-available","status-offline","status-away","status-inacall","status-outofoffice","status-busy","status-donotdisturb");
     switch (status) {
          case 0:
               //available
               document.getElementById(id).classList.add('status-available');
               break;
          case 1:
               // offline
               document.getElementById(id).classList.add('status-offline');
               break;
          case 2:
          case 4:
          case 16:
               //away
               document.getElementById(id).classList.add('status-away');
               break;
          case 3:
          case 5:
               //inacall
               document.getElementById(id).classList.add('status-inacall');
               break;
          case 6:
          case 7:
          case 8:
               document.getElementById(id).classList.add('status-outofoffice');
               break;
          case 10:
               //busy
               document.getElementById(id).classList.add('status-busy');
               break;
          case 9:
          case 15:
               //donotdisturb
               document.getElementById(id).classList.add('status-donotdisturb');
               break;
     }
};

}

//# sourceMappingURL=lync-presence.js.map
