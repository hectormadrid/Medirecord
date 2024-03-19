<?php
if (isset($_POST["data"])) {
    $data = json_decode($_POST["data"], true);

    if (!empty($data)) {
        // Conexión a la base de datos (debes configurarla según tu entorno)
        $conexion = new mysqli("localhost", "root", "", "Medirecord");

        $ID_Usuario = 1234;
        date_default_timezone_set('America/Santiago');
        $nuevaFechaHora = new DateTime();
        $nuevaFechaHora->add(new DateInterval('PT1M'));
        $fechaHoraActual = $nuevaFechaHora->format('Y-m-d H:i:s');

        $sqlID = "SELECT MAX(ID) AS MaxID FROM Envio_Mensaje";
        $resultID = $conexion->query($sqlID);

        if ($resultID) {
            $rowID = $resultID->fetch_assoc();
            $ID = $rowID["MaxID"];
            $ID++; // Incrementar el valor para el próximo ID
        } else {
            // En caso de que no haya registros en Envio_Mensaje, establecer un valor inicial
            $ID = 1;
        }

        // Insertar un nuevo registro en la tabla Envio_Mensaje
        $sqlEnvio = "INSERT INTO Envio_Mensaje (ID, ID_Usuario, Hora_carga, Fecha_envio) VALUES ('$ID', '$ID_Usuario', NOW(), '$fechaHoraActual')";

        if ($conexion->query($sqlEnvio) !== TRUE) {
            echo "Error al insertar datos en la tabla 'Envio_Mensaje': " . $conexion->error;
            exit();
        }

        if ($conexion->connect_error) {
            die("Error de conexión a la base de datos: " . $conexion->connect_error);
        }
        $pacientesActualizacion = array();

        foreach ($data as $columna) {
            $numero = $columna[1];
            $nombre = $columna[2];
            $rut = $columna[3];
            $dia = $columna[4];
            $hora = $columna[5];
            $medico = $columna[6];
            $especialidad = $columna[7];
            $tipoconsulta = $columna[8];
            $email =  $columna[11];

            // Verificar si el paciente ya existe en la tabla 'paciente'
            $sqlVerificar = "SELECT rut,nombre ,telefono , Corre_electronico FROM paciente WHERE rut = '$rut'";
            $resultado = $conexion->query($sqlVerificar);

            if ($resultado->num_rows > 0) {

                $datosPacienteExistente = $resultado->fetch_assoc();
                $datosiguales = false;
                if ($datosPacienteExistente["telefono"] != $numero) {
                    $sql = 'telefono';
                    $res = $numero;
                    $datosiguales = true;
                }
                if ($datosPacienteExistente["nombre"] != $nombre) {
                    $sql = 'nombre';
                    $res = $nombre;
                    $datosiguales = true;
                }
                if ($datosiguales) {
                    $pacientesActualizacion[] = array(
                        "rut" => $rut,
                        "viejo" => $datosPacienteExistente[$sql],
                        "nuevo" => $res,
                    );
                    // Actualiza los datos del paciente
                    $sqlActualizar = "UPDATE paciente SET $sql = '$res' WHERE rut = '$rut'";
                    if ($conexion->query($sqlActualizar) !== TRUE) {
                        echo "Error al actualizar los datos del paciente: " . $conexion->error;
                        exit();
                    }
                }
            } else {
                $sqlInsertarPaciente = "INSERT INTO paciente (rut, nombre, Telefono, Corre_electronico  ) VALUES ('$rut', '$nombre', '$numero', '$email')";
                if ($conexion->query($sqlInsertarPaciente) !== TRUE) {
                    echo "Error al insertar datos en la tabla 'paciente': " . $conexion->error;
                    exit();
                }
            }

            $sqlhora = "INSERT INTO hora (Rut_Paciente, Profesional, Tipo_Atencion,Especialidad,Dia,Hora_Agandada,Asistencia,Fecha_envio,ID_envio) VALUES ('$rut', '$medico', '$tipoconsulta','$especialidad','$dia','$hora','Por Confirmar', now(),$ID)";
            if ($conexion->query($sqlhora) !== TRUE) {
                echo "Error al insertar datos en la tabla 'Hora': " . $conexion->error;
                exit();
            }
            // Insertar los datos en otras tablas según tus requerimientos
        }
    }

    $command = 'npm run dev';
    $output = shell_exec($command);
    
    // Inicializa una cadena para almacenar la lista de pacientes actualizados
    $pacientesActualizadosStr = "";

    // Mostrar la lista de pacientes que se actualizaron y almacenarla en la cadena
    if (!empty($pacientesActualizacion)) {
        $pacientesActualizadosStr .= "Pacientes con datos actualizados :\n";
        foreach ($pacientesActualizacion as $paciente) {
            $pacientesActualizadosStr .= "RUT: " . $paciente["rut"] . "\n";
            $pacientesActualizadosStr .= "Campo anterior: " . $paciente["viejo"] . "\n";
            $pacientesActualizadosStr .= "Campo nuevo: " . $paciente["nuevo"] . "\n";
            $pacientesActualizadosStr .= "---------------\n";
        }
    }

    echo $pacientesActualizadosStr;

    $conexion->close();
} 


?>