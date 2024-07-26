function validarCedula(cedula) {
    let total = 0;
    let longitud = cedula.length;
    let longcheck = longitud - 1;

    if (cedula !== "" && longitud === 10) {
        for (let i = 0; i < longcheck; i++) {
            if (i % 2 === 0) {
                let aux = cedula.charAt(i) * 2;
                if (aux > 9) aux -= 9;
                total += aux;
            } else {
                total += parseInt(cedula.charAt(i));
            }
        }

        total = total % 10 ? 10 - total % 10 : 0;

        if (cedula.charAt(longitud - 1) == total) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function validarFormulario() {
    const cedula = document.getElementById("cedula").value;
    if (!validarCedula(cedula)) {
        alert("Cédula inválida");
        return false;
    }
    return true;
}
