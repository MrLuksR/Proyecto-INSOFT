// FUNCIONES DE VALIDACIÓN DE FORMULARIOS

// Validar cédula
function validCi(ci) {
    let rec = ci.split(""); // rec de cédula recortada
    let valores = []; // Valores de cálculo

    // Agregar valores multiplicados al array
    valores.push(rec[0] * 2);
    valores.push(rec[1] * 9);
    valores.push(rec[2] * 8);
    valores.push(rec[3] * 7);
    valores.push(rec[4] * 6);
    valores.push(rec[5] * 3);
    valores.push(rec[6] * 4);

    let suma = 0;
    for (let i = 0; i < valores.length; i++)
        suma += valores[i];

    let mod = suma % 10;
    let verif = 10 - mod;

    if (verif == 10 && rec[7] == 0)
        return true;

    if (verif == rec[7])
        return true;
    else
        return false;
}

/*
    La contraseña deberá tener al menos 8
    carácteres, uno especial y al menos 1
    mayúscula.
*/

// Validar correo
function validCorreo(correo) {
    let reg = /^[a-zA-Z0-0._%+-]+@[a-zA-Z0-0.-]+\.[a-zA-Z]{2,}$/; // Regex utilizado para correos

    if (reg.test(correo))
        return true;
    else
        return false;
}

// Validar contraseña
function validContra(contra) {
    let regMay = / [A-Z] /; // Regex para mayúscula
    let regEsp = / [!@#$%^&*(),.?":{}|<>] /; // Regex para carácteres especiales
    let len = contra.length; // Longitud de contraseña

    if (len < 8) // No debe tener menos de 8 caracteres
        return 1;
    else if (regMay.test(contra)) // Debe tener al menos 1 mayúscula
        return 2;
    else if (regEsp.test(contra)) // Debe tener por lo menos 1 caracter especial
        return 3;
    else
        return 0; // En cualquier otro caso, envía 0 para aprobar la contraseña
}


// Listener de Cédula
const inputCi = document.getElementById("cedula");
const msgCi = document.getElementById("mensajeCi");

inputCi.addEventListener("input", function(){
    let len = inputCi.value.length;
    if (!validCi(inputCi.value) && len >= 7){
        msgCi.classList.add("mostrar");
    }
    else if (len == 0){
        msgCi.classList.remove("mostrar");
    }
    else if (validCi(inputCi.value)){
        msgCi.classList.remove("mostrar");
    }
});