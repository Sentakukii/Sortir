if (typeof App === typeof undefined) {
    var App = {};
}

if (typeof App.Flash === typeof undefined) {
    App.Flash = function () {
        function getContainer() {
            return document.getElementById("flash_messages");
        }

        function make(type, msg) {
            var div = document.createElement('div');

            div.className = 'col-md-5 offset-md-7 alert alert-' + type;
            div.textContent = msg;
            getContainer().innerHTML="";
            getContainer().append(div);
        }

        return ['success', 'danger', 'warning', 'info']
                .reduce(function (acc, elt) {
                acc[elt] = function (msg) { make(elt, msg) };
                return acc;
            }, {});
    };
}

$(document).ready(function() {
    if (document.querySelector('input[type="file"]')) {
        document.querySelector('input[type="file"]').addEventListener("change", function () {
            var fullPath = document.querySelector('input[type="file"]').value;
            if (fullPath) {
                var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                var filename = fullPath.substring(startIndex);
                if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                    filename = filename.substring(1);
                }
                document.querySelector('input[type="file"] + label').textContent = filename;
            }
        });
    }
});


function removeEntity(entityId, nameTr) {
    if(confirm("Êtes-vous sur d'effectuer cette suppression ? ")) {
        $.ajax({
            url: urlRemove,
            type: 'POST',
            data: 'id=' + entityId,
            success: function (json, status) {
                (new App.Flash()).success(json.msg);
                var tr = document.getElementById(nameTr+entityId);
                tr.parentNode.removeChild(tr);
            },
            error: function (response) {
                var json= response.responseJSON;
                (new App.Flash()).danger(json.msg);
            }
        });
    }
}

