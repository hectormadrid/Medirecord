const cron = require('node-cron');
const { enviarMensaje } = require('./mensaje.js');
const conectarBaseDatos = require('../js/Conexion.js');

const MSG_SALUDOS = 'Hola, recuerde que mañana tiene hora ¿va a asistir? responda Si o No';
const MSG_ASISTIRA = 'Perfecto, lo esperamos mañana';
const MSG_NO_ASISTIRA = 'Gracias por responder';

let respuestas = 0;
let pacientesSinResponder = 0; // Variable para llevar un registro de los pacientes que no han respondido
let connection;

async function finalizarAplicacion(cliente) {
    try {
        // Obtener el último ID_Envio
        const [lastEnvio] = await connection.execute(
            'SELECT MAX(ID) as lastEnvio FROM Envio_Mensaje'
        );

        const lastIdEnvio = lastEnvio[0].lastEnvio;

        // Obtener los RUT de los pacientes que tienen estado 'Por Confirmar' para el último ID_Envio
        const [rows] = await connection.execute(
            'SELECT Rut_Paciente FROM hora WHERE Asistencia = "Por Confirmar" AND ID_Envio = ?',
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
        cliente.destroy(); // Destruye la conexión con el cliente de WhatsApp
        process.exit(); // Finaliza el proceso Node.js
    } catch (error) {
        console.log('Error al finalizar la aplicación:', error);
    }
}

async function obtenerPacientes() {
    try {
        const [rows] = await connection.execute('SELECT rut, telefono FROM paciente');
        if (rows && rows.length > 0) {
            pacientesSinResponder = rows.length; // Inicializar con la cantidad de pacientes
            return rows.filter(row => validarNumeroTelefono(row.telefono));
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
        // Obtener el último ID_Envio
        const [lastEnvio] = await connection.execute(
            'SELECT MAX(ID) as lastEnvio FROM Envio_Mensaje'
        );

        const lastIdEnvio = lastEnvio[0].lastEnvio;

        
        const [result] = await connection.execute(
            'UPDATE hora SET Asistencia = ? WHERE Rut_Paciente = ? AND ID_Envio = ?',
            [estado, rut, lastIdEnvio]
        );

        if (result && result.affectedRows > 0) {
            console.log("||||||||||");
            console.log(`La Asistencia del paciente con el RUT ${rut} se  Actualizo a ${estado}`);
           
            pacientesSinResponder--; // Decrementar la cantidad de pacientes sin responder
        } else {
            console.log(`No se pudo actualizar el estado del paciente con RUT ${rut}`);
        }
    } catch (error) {
        console.log('Error al actualizar el estado del paciente en la base de datos:', error);
    }
}


async function programador_tareas(cliente) {
    connection = await conectarBaseDatos();
// programar tarea para inicar el recordatorio 
    const tiempo = '0 55 19 * * *';

    if (cron.validate(tiempo)) {
        console.log('Cron inicializado');
        cron.schedule(tiempo, async () => {
            try {
                const pacientes = await obtenerPacientes();

                for (const paciente of pacientes) {
                    const numeroConFormato = '569' + paciente.telefono + '@c.us';
                    await enviarMensaje(cliente, numeroConFormato, MSG_SALUDOS);
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
           // Programar tarea cron para finalizar la aplicación 
           cron.schedule('0 05 20 * * *', () => {
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
            const telefono = message.from.split('@')[0].slice(3); // Obtener el teléfono como string
            const [rows] = await connection.execute('SELECT Rut FROM paciente WHERE Telefono = ?', [telefono]);
            
            if (rows && rows.length > 0) {
                const rut = rows[0].Rut;
                await actualizarEstadoPaciente(rut, respuesta);
            } else {
                console.log('No se encontró el paciente en la base de datos');
            }
                // Verificar si todos los pacientes han respondido
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
