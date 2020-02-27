
function removeUser(userId) {
    if(confirm("Êtes-vous sur de supprimer cette utilisateur ? ")) {
        $.ajax({
            url: urlRemove,
            type: 'POST',
            data: 'userId=' + userId,
            success: function (json, status) {
                (new App.Flash()).success(json.msg);
                var tr = document.getElementById("row_event_"+userId);
                tr.parentNode.removeChild(tr);
            },
            error: function (response) {
                var json= response.responseJSON;
                (new App.Flash()).danger(json.msg);
            }
        });
    }
}

function desactivateUser(userId) {
        $.ajax({
            url: urlDesactivate,
            type: 'POST',
            data: 'userId=' + userId,
            success: function (json, status) {
                (new App.Flash()).success(json.msg);
                var link = document.getElementById("link_activate_"+userId);
                link.textContent="Activer";
                link.onclick = function(){
                    activateUser(userId);
                }
            },
            error: function (response) {
                var json= response.responseJSON;
                (new App.Flash()).danger(json.msg);
            }
        });

}

function activateUser(userId) {
    $.ajax({
        url: urlActivate,
        type: 'POST',
        data: 'userId=' + userId,
        success: function (json, status) {
            (new App.Flash()).success(json.msg);
            var link = document.getElementById("link_activate_"+userId);
            link.textContent="Désactiver";
            link.onclick = function(){
                desactivateUser(userId);
            }
        },
        error: function (response) {
            var json= response.responseJSON;
            (new App.Flash()).danger(json.msg);
        }
    });

}