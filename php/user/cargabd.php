<?php
session_start();
require_once '../Conexion.php';

if (isset($_POST["data"]) && isset($_SESSION['ID'])) {
    $data = json_decode($_POST["data"], true);

    if (!empty($data)) {
        $ID_Usuario = $_SESSION['ID'];
        date_default_timezone_set('America/Santiago');
        $nuevaFechaHora = new DateTime();
        $nuevaFechaHora->add(new DateInterval('PT1M'));
        $fechaHoraActual = $nuevaFechaHora->format('Y-m-d H:i:s');

        if ($conexion->connect_error) {
            die("Error de conexión a la base de datos: " . $conexion->connect_error);
        }

        // Iniciar una transacción
        $conexion->begin_transaction();

        try {
            $sqlID = "SELECT MAX(ID) AS MaxID FROM Historial_Mensajes";
            $resultID = $conexion->query($sqlID);
            if ($resultID) {
                $rowID = $resultID->fetch_assoc();
                $ID = $rowID["MaxID"];
                $ID++; // Incrementar el valor para el próximo ID
            } else {
                // En caso de que no haya registros en Historial_Mensajes, establecer un valor inicial
                $ID = 1;
            }

            // Insertar un nuevo registro en la tabla Historial_Mensajes
            $sqlEnvio = "INSERT INTO Historial_Mensajes (ID, ID_funcionario, Hora_carga, Fecha_envio) VALUES (?, ?, NOW(), ?)";
            $stmtEnvio = $conexion->prepare($sqlEnvio);
            $stmtEnvio->bind_param("iis", $ID, $ID_Usuario, $fechaHoraActual);

            if (!$stmtEnvio->execute()) {
                throw new Exception("Error al insertar datos en la tabla 'Historial_Mensajes': " . $stmtEnvio->error);
            }

            $pacientesActualizacion = array();
            $alertas = array();

            foreach ($data as $columna) {
                $numero = $columna[1];
                $nombre = $columna[2];
                $rut = $columna[3];
                $dia = $columna[4];
                $hora = $columna[5];
                $medico = $columna[6];
                $especialidad = $columna[7];
                $tipoconsulta = $columna[8];
                $email = $columna[11];

                $stmtVerificar = $conexion->prepare("SELECT nombre, telefono, Corre_electronico FROM paciente WHERE rut = ?");
                $stmtVerificar->bind_param("s", $rut);
                $stmtVerificar->execute();
                $resultado = $stmtVerificar->get_result();

                if ($resultado->num_rows > 0) {
                    $datosPacienteExistente = $resultado->fetch_assoc();
                    $datosiguales = false;
                    $campoActualizar = '';
                    $nuevoValor = '';

                    if ($datosPacienteExistente["telefono"] != $numero) {
                        $campoActualizar = 'telefono';
                        $nuevoValor = $numero;
                        $datosiguales = true;
                    }
                    if ($datosPacienteExistente["nombre"] != $nombre) {
                        $campoActualizar = 'nombre';
                        $nuevoValor = $nombre;
                        $datosiguales = true;
                    }
                    if ($datosPacienteExistente["Corre_electronico"] != $email) {
                        $campoActualizar = 'Corre_electronico';
                        $nuevoValor = $email;
                        $datosiguales = true;
                    }

                    if ($datosiguales) {
                        $pacientesActualizacion[] = array(
                            "rut" => $rut,
                            "viejo" => $datosPacienteExistente[$campoActualizar],
                            "nuevo" => $nuevoValor,
                        );
                        $stmtActualizar = $conexion->prepare("UPDATE paciente SET $campoActualizar = ? WHERE rut = ?");
                        $stmtActualizar->bind_param("ss", $nuevoValor, $rut);

                        if (!$stmtActualizar->execute()) {
                            throw new Exception("Error al actualizar los datos del paciente: " . $stmtActualizar->error);
                        }
                    }
                } else {
                    $stmtInsertarPaciente = $conexion->prepare("INSERT INTO paciente (rut, nombre, Telefono, Corre_electronico) VALUES (?, ?, ?, ?)");
                    $stmtInsertarPaciente->bind_param("ssss", $rut, $nombre, $numero, $email);

                    if (!$stmtInsertarPaciente->execute()) {
                        throw new Exception("Error al insertar datos en la tabla 'paciente': " . $stmtInsertarPaciente->error);
                    }
                }

                $stmtHoraVerificar = $conexion->prepare("SELECT * FROM hora WHERE Rut_Paciente = ? AND Profesional = ? AND Dia = ? AND Hora_Agandada = ?");
                $stmtHoraVerificar->bind_param("ssss", $rut, $medico, $dia, $hora);
                $stmtHoraVerificar->execute();
                $resultadoHora = $stmtHoraVerificar->get_result();

                if ($resultadoHora->num_rows == 0) {
                    $stmtHora = $conexion->prepare("INSERT INTO hora (Rut_Paciente, Profesional, Tipo_Atencion, Especialidad, Dia, Hora_Agandada, Asistencia, Fecha_envio, ID_envio) VALUES (?, ?, ?, ?, ?, ?, 'Por Confirmar', NOW(), ?)");
                    $stmtHora->bind_param("ssssssi", $rut, $medico, $tipoconsulta, $especialidad, $dia, $hora, $ID);

                    if (!$stmtHora->execute()) {
                        throw new Exception("Error al insertar datos en la tabla 'Hora': " . $stmtHora->error);
                    } else {
                        $alertas[] = "Datos insertados correctamente para el paciente con RUT: $rut";
                    }
                } else {
                    $alertas[] = "El paciente con RUT: $rut ya tiene una cita registrada.";
                }
            }

            // Confirmar la transacción
            $conexion->commit();

            $command = 'npm run dev';

            /*$output = shell_exec($command);

            $pacientesActualizadosStr = "";*/

            
            if (!empty($pacientesActualizacion)) {
                $pacientesActualizadosStr .= "Pacientes con datos actualizados:\n";
                foreach ($pacientesActualizacion as $paciente) {
                    $pacientesActualizadosStr .= "RUT: " . $paciente["rut"] . "\n";
                    $pacientesActualizadosStr .= "Campo anterior: " . $paciente["viejo"] . "\n";
                    $pacientesActualizadosStr .= "Campo nuevo: " . $paciente["nuevo"] . "\n";
                    $pacientesActualizadosStr .= "---------------\n";
                }
            }

            if (!empty($alertas)) {
                foreach ($alertas as $alerta) {
                    echo "<script>alert('$alerta');</script>";
                }
            }

            echo $pacientesActualizadosStr;

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conexion->rollback();
            echo "Error en la transacción: " . $e->getMessage();
        }

        $conexion->close();
    }
}
?>


  