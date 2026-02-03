function switchOnOff(radioBros) {

    if (document.getElementById("pianoText").classList.contains("disabled")) {
        document.getElementById("pianoText").toggleAttribute("disabled");
    } else {
        document.getElementById("pianoText").toggleAttribute("disabled");
    }

    [].slice.call(document.getElementsByClassName("inputAddOns")).forEach(element => {
        if (radioBros == 1 && element.classList.contains("d-none")) {
            element.classList.replace("d-none", "d-flex");
        } else if (radioBros == 0 && element.classList.contains("d-flex")) {
            element.classList.replace("d-flex", "d-none");
        }
    });
}

function switchOnOffAsc(btnGroup){
    [].slice.call(document.getElementsByClassName("inputAsc")).forEach(element => {
        if (btnGroup == 1 && element.classList.contains("d-none")) {
            element.classList.replace("d-none", "d-flex");
        } else if (btnGroup == 0 && element.classList.contains("d-flex")) {
            element.classList.replace("d-flex", "d-none");
        }
    });

    if(document.getElementById("swAsc").classList.contains("rounded-bottom-0")){
        document.getElementById("swAsc").classList.remove("rounded-bottom-0");
    }else{
        document.getElementById("swAsc").classList.add("rounded-bottom-0");
    }
}

function increase() {
    [].slice.call(document.getElementsByClassName("progress-bar")).forEach(element => {
        if(element.style.width == "0%"){
            element.style.width = "25%";
        }else if(element.style.width == "25%"){
            element.style.width = "50%";
        }else if(element.style.width == "50%"){
            element.style.width = "75%";
        }else if(element.style.width == "75%"){
            element.style.width = "100%";
        }
    });
}