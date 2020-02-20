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
