document.addEventListener("DOMContentLoaded", function () {
  // Só pede permissão se o usuário nunca foi perguntado (status = 'default')
  // Se foi denegado (status = 'denied'), não tenta pedir novamente
  if (Notification.permission === "default") Notification.requestPermission();
});

function notifyMe(icon, title, mensagem, link) {
  if (!Notification) {
    alert(
      "O navegador que você está utilizando não possui o notifications. Tente o Chrome",
    );
    return;
  }

  // Só cria notificação se a permissão foi concedida
  if (Notification.permission === "granted") {
    var notification = new Notification(title, {
      icon: icon,
      body: mensagem,
    });

    // notification.onclick = function(){
    // 	window.open(link);
    // };
  }
  // Se foi denegado, não tenta pedir novamente - apenas não mostra a notificação
}
