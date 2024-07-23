const cron = require('node-cron');
const { enviarMensaje } = require('./mensaje.js');
const conectarBaseDatos = require('../js/Conexion.js');

const MSG_SALUDOS =  (nombre, dia, hora, especialidad) => `
Hola ${nombre},
Soy el sistema de Recordatorio de Horas Médicas de la Clinica. Queremos recordarle que tiene una cita programada para mañana, 
${dia} a las ${hora} para una consulta de ${especialidad}.
¿Podría confirmarnos su asistencia? Por favor, responda con "Sí" o "No".
Saludos cordiales,
Centro de Salud
`;
const MSG_ASISTIRA = 'Perfecto, lo esperamos en su cita.';
const MSG_NO_ASISTIRA = 'Gracias por responder, intentaremos reprogramar su cita.';

let respuestas = 0;
let pacientesSinResponder = 0;
let connection;

async function finalizarAplicacion(cliente) {
    try {
        const [lastEnvio] = await connection.execute(
            'SELECT MAX(ID) as lastEnvio FROM Historial_Mensajes'
        );
        const lastIdEnvio = lastEnvio[0].lastEnvio;

        const [rows] = await connection.execute(
            'SELECT Rut_Paciente FROM Hora WHERE Asistencia = "Por Confirmar" AND ID_Envio = ?',
            [lastIdEnvio]
        );

        if (rows && rows.length > 0) {
            console.log('Los siguientes pacientes no han respondido:');
            rows.forEach(row => {
                console.log(row.Rut_Paciente);
            });
        } else {
            console.log('Todos los pacientes han respondido.');
        }

        console.log('Finalizando la aplicación ');
        cliente.destroy();
        process.exit();
    } catch (error) {
        console.log('Error al finalizar la aplicación:', error);
    }
}

async function obtenerPacientes() {
    try {
        const [rows] = await connection.execute(
            'SELECT p.Rut, p.Telefono, p.Nombre, h.Dia, h.Hora_Agandada, h.Especialidad FROM Paciente p JOIN Hora h ON p.Rut = h.Rut_Paciente WHERE h.Asistencia = "Por Confirmar"'
        );
        if (rows && rows.length > 0) {
            pacientesSinResponder = rows.length;
            return rows.filter(row => validarNumeroTelefono(row.Telefono));
        } else {
            console.log('No se encontraron pacientes en la consulta.');
            return [];
        }
    } catch (error) {
        console.log('Error en la consulta a la base de datos:', error);
        return [];
    }
}

async function actualizarEstadoPaciente(rut, respuesta) {
    let estado = '';

    if (respuesta === 'si') {
        estado = 'Asistirá';
    } else if (respuesta === 'no') {
        estado = 'No Asistirá';
    }

    try {
        const [lastEnvio] = await connection.execute(
            'SELECT MAX(ID) as lastEnvio FROM Historial_Mensajes'
        );
        const lastIdEnvio = lastEnvio[0].lastEnvio;

        const [result] = await connection.execute(
            'UPDATE Hora SET Asistencia = ? WHERE Rut_Paciente = ? AND ID_Envio = ?',
            [estado, rut, lastIdEnvio]
        );

        if (result && result.affectedRows > 0) {
            console.log(`La Asistencia del paciente con el RUT ${rut} se actualizó a ${estado}`);
            pacientesSinResponder--;
        } else {
            console.log(`No se pudo actualizar el estado del paciente con RUT ${rut}`);
        }
    } catch (error) {
        console.log('Error al actualizar el estado del paciente en la base de datos:', error);
    }
}

async function programador_tareas(cliente) {
    connection = await conectarBaseDatos();

    const tiempo = '0 00 19 * * *';

    if (cron.validate(tiempo)) {
        console.log('Cron inicializado');
        cron.schedule(tiempo, async () => {
            try {
                const pacientes = await obtenerPacientes();

                for (const paciente of pacientes) {
                    const numeroConFormato = '569' + paciente.Telefono + '@c.us';
                    const mensaje = MSG_SALUDOS(paciente.Nombre, paciente.Dia, paciente.Hora_Agandada, paciente.Especialidad);
                    await enviarMensaje(cliente, numeroConFormato, mensaje);
                    console.log('Mensaje enviado a:', numeroConFormato);
                }
            } catch (error) {
                console.log('Error en cron:', error);
            }
        });

        cliente.on('message', async (message) => {
            try {
                manejarRespuesta(cliente, message);
            } catch (error) {
                console.log('Error al manejar la respuesta:', error);
            }
        });

        cron.schedule('0 10 19 * * *', () => {
            finalizarAplicacion(cliente);
        });
    }
}

async function manejarRespuesta(cliente, message) {
    const respuesta = message.body.toLowerCase();

    if (respuesta === 'si' || respuesta === 'no') {
        message.reply(respuesta === 'si' ? MSG_ASISTIRA : MSG_NO_ASISTIRA);
        respuestas++;

        try {
            const telefono = message.from.split('@')[0].slice(3);
            const [rows] = await connection.execute('SELECT Rut FROM Paciente WHERE Telefono = ?', [telefono]);

            if (rows && rows.length > 0) {
                const rut = rows[0].Rut;
                await actualizarEstadoPaciente(rut, respuesta);
            } else {
                console.log('No se encontró el paciente en la base de datos');
            }

            if (pacientesSinResponder === 0) {
                console.log('Todos los pacientes han respondido. Aplicación finalizada');
                cliente.destroy();
                process.exit();
            }
        } catch (error) {
            console.log('Error en la consulta a la base de datos:', error);
        }
    }
}

function validarNumeroTelefono(numero) {
    return /^\d{8}$/.test(numero);
}

module.exports = {
    programador_tareas,
};
