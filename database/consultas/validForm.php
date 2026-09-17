<?php
    // FUNCIONES DE VALIDACIÓN DE FORMULARIOS

    // Validar cédula
    function validCi($ci):bool{
        $rec = str_split($ci); // $rec de recortada
        $valores = []; // Valores de cálculo

        // Agregar valores multiplicados al array
        $valores[] = $rec[0] * 2;
        $valores[] = $rec[1] * 9;
        $valores[] = $rec[2] * 8;
        $valores[] = $rec[3] * 7;
        $valores[] = $rec[4] * 6;
        $valores[] = $rec[5] * 3;
        $valores[] = $rec[6] * 4;

        $suma = 0;
        for ($i = 0; $i < count($valores); $i ++)
            $suma += $valores[$i];

        $mod = $suma % 10;
        $verif = 10 - $mod;

        if ($verif == 10 && $rec[7] == 0)
            return true;

        if ($verif == $rec[7])
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
    function validCorreo($correo):bool{
        $reg = "/^[a-zA-Z0-0._%+-]+@[a-zA-Z0-0.-]+\.[a-zA-Z]{2,}$/"; // Regex utilizado para correos

        if (preg_match($reg, $correo))
            return true;
        else
            return false;
    }

    // Validar contraseña
    function validContra($contra):int{
        $regMay = "/ [A-Z] /"; // Regex para mayúscula
        $regEsp = '/ [!@#$%^&*(),.?":{}|<>] /' // Regex para carácteres especiales
        $len = strlen($contra); // Longitud de contraseña agregada

        if ($len < 8) // No debe tener menos de 8 caracteres
            return 1;
        else if (preg_match($regMay, $contra)) // Debe tener al menos 1 mayúscula
            return 2;
        else if (preg_match($regEsp, $contra)) // Debe tener por lo menos 1 caracter especial
            return 3;
        else
            return 0; // En cualquier otro caso, envía 0 para aprobar la contraseña
    }
?>