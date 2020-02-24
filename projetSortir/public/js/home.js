
function registerEvent(eventId) {
    $.ajax({
        url : 'http://localhost:8000/event/register',
        type : 'POST',
        data: 'eventId='+eventId,
        success : function(json, status){
            (new App.Flash()).success(json.msg);
            console.log("json: "+JSON.stringify(json));
            console.log("json.nbRegister: "+json.nbRegister);
            document.getElementById("nbRegister_"+eventId).textContent = json.nbRegister;
            document.getElementById("cross_menu_"+eventId).classList.remove('hide');
            var registerButton = document.getElementById("register_button_"+eventId);
            registerButton.textContent = "Se désister";
            registerButton.onclick = function(){
                deregisterEvent(eventId)
            };
        },
        error : function(json, status){
            (new App.Flash()).danger(json.msg);
            document.getElementById("nbRegister_"+eventId).textContent = json.nbRegister;
        }
    });
}

function deregisterEvent(eventId) {
    $.ajax({
        url: 'http://localhost:8000/event/deregister',
        type: 'POST',
        data: 'eventId=' + eventId,
        success: function (json, status) {
            (new App.Flash()).success(json.msg);
            console.log("json: "+JSON.stringify(json));
            document.getElementById("nbRegister_" + eventId).innerHTML = json.nbRegister;
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

function cancelEvent(eventId ,comment, url) {
    $.ajax({
        url: url,
        type: 'POST',
        data: 'eventId=' + eventId+'&comment=' + comment,
        success: function (json, status) {
            (new App.Flash()).success(json.msg);
           document.getElementById("cancel_button_"+eventId).classList.add("hide");
        },
        error: function (json, status, error, res) {
            (new App.Flash()).danger(json.msg);
        }
    });
}