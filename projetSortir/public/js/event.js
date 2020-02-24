
function submitForm(index){
    document.getElementById("event_form_state").selectedIndex = index;
    document.getElementById("form_event").submit();
}

function switchModeLocation(){
    if(document.getElementsByClassName("select_location")[0].classList.contains("hide")) {
        changeModeLocation(0);
    }else {
        changeModeLocation(1);
    }
}

function switchModeCity(){
    if(document.getElementsByClassName("select_city")[0].classList.contains("hide")) {
        changeModeCity(0);
    }else {
        changeModeCity(1);
    }
}

/**
 *
 * @param mode   0 = switch to select location  1 =  switch to add location
 */
function changeModeLocation(mode){
    var selects = document.getElementsByClassName("select_location");
    var adds = document.getElementsByClassName(("add_location"));
    var cross = document.getElementById("cross_plus_location");
    var type = document.getElementById("event_form_type_location");

    //switch to select location and city
    if(mode == 0) {
        for (var i = 0; i < adds.length ; i++) {
            adds[i].classList.add("hide")
        }
        for (var i = 0; i < selects.length ; i++) {
            selects[i].classList.remove("hide")
        }
        var inputs = $('.add_location input');
        for (var i = 0 ; i < inputs.length ; i++){
            inputs[i].value="";
        }
        type.value = 0;

        changeModeCity(0);

        cross.classList.add("plus");
        cross.classList.remove("minus");

        //switch to add location
    } else if (mode == 1){
        for (var i = 0; i < selects.length ; i++) {
            selects[i].classList.add("hide")
        }
        for (var i = 0; i < adds.length ; i++) {
            adds[i].classList.remove("hide")
        }
        type.value = 1;

        cross.classList.add("minus");
        cross.classList.remove("plus");
    }
}

/**
 *
 * @param mode   0 = switch to add city  1 =  switch to select city
 */
function changeModeCity(mode) {
    var selects = document.getElementsByClassName("select_city");
    var adds = document.getElementsByClassName(("add_city"));
    var cross = document.getElementById("cross_plus_city");
    var type = document.getElementById("event_form_type_city");

    //switch to add city
    if (mode == 0) {
        for (var i = 0; i < adds.length; i++) {
            adds[i].classList.add("hide")
        }
        for (var i = 0; i < selects.length; i++) {
            selects[i].classList.remove("hide")
        }
        var inputs = $('.add_city input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = "";
        }
        type.value = 0;

        cross.classList.add("plus");
        cross.classList.remove("minus");

        //switch to add city and location
    } else if (mode == 1) {
        for (var i = 0; i < selects.length; i++) {
            selects[i].classList.add("hide")
        }
        for (var i = 0; i < adds.length; i++) {
            adds[i].classList.remove("hide")
        }
        type.value = 1;

        changeModeLocation(1);
        cross.classList.add("minus");
        cross.classList.remove("plus");
    }
}
function getLocations(select , url) {
    $.ajax({
        url : url,
        type : 'POST',
        data: 'cityId='+select.value,
        success : function(json, status){

           var locationsId = json.locationsId;
           var locationsName = json.locationsName;
           select = document.getElementById("event_form_location")
           select.innerHTML="";
           for(var i =0 ; i < locationsId.length ; i++) {
               var option = document.createElement("option");
               var locationName = locationsName[i];
               var locationId = locationsId[i];
               option.text = locationName;
               option.value = locationId;
               select.add(option);
           }
        },
        error : function(json, status){
            (new App.Flash()).danger(json.msg);
        }
    });


}