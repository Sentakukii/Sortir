
function registerEvent(eventId , url) {
    $.ajax({
        url : url,
        type : 'POST',
        data: 'eventId='+eventId,
        success : function(json, status){
            (new App.Flash()).success(json.msg);
            document.getElementById("nbRegister_"+eventId).textContent = json.nbRegister;
            document.getElementById("cross_menu_"+eventId).classList.remove('hide');
            var registerButton = document.getElementById("register_button_"+eventId);
            registerButton.textContent = "Se désister";
            registerButton.onclick = function(){deregisterEvent(eventId)};
        },
        error : function(json, status){
            (new App.Flash()).danger(json.msg);
            document.getElementById("nbRegister_"+eventId).textContent = json.nbRegister;
        }
    });
}

function deregisterEvent(eventId ,url) {
    $.ajax({
        url: url,
        type: 'POST',
        data: 'eventId=' + eventId,
        success: function (json, status) {
            (new App.Flash()).success(json.msg);
            document.getElementById("nbRegister_" + eventId).innerHTML = json.success;
            document.getElementById("cross_menu_" + eventId).classList.add('hide');
            var registerButton = document.getElementById("register_button_" + eventId);
            registerButton.textContent = "S'inscrire";
            registerButton.onclick = function () {
                registerEvent(eventId)
            };
        },
        error: function (json, status, error, res) {
            (new App.Flash()).danger(json.msg);
            document.getElementById("nbRegister_" + eventId).textContent = json.nbRegister;
        }
    });
}