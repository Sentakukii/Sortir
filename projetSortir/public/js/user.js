
function removeUser(userId) {
    if(confirm("Êtes-vous sur de supprimer cette utilisateur ? ")) {
        $.ajax({
            url: urlRemove,
            type: 'POST',
            data: 'userId=' + userId,
            success: function (json, status) {
                (new App.Flash()).success(json.msg);
                document.removeChild(document.getElementById("row_event_"+userId));
            },
            error: function (response) {
                var json= response.responseJSON;
                (new App.Flash()).danger(json.msg);
            }
        });
    }
}