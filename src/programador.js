const cron = require('node-cron');
const { enviarMensaje } = require('./mensaje.js');
const conectarBaseDatos = require('../js/Conexion.js');

const MSG_SALUDOS = 'Hola, recuerde que mañana tiene hora ¿va a asistir? responda Si o No';
const MSG_ASISTIRA = 'Perfecto, lo esperamos mañana';
const MSG_NO_ASISTIRA = 'Gracias por responder';

let respuestas = 0;
let pacientesSinResponder = 0; // Variable para llevar un registro de los pacientes que no han respondido
let connection;

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
        estado = 'asistirá';
    } else if (respuesta === 'no') {
        estado = 'no asistirá';
    }

    try {
        const [result] = await connection.execute('UPDATE hora SET Asistencia = ? WHERE Rut_Paciente = ?', [estado, rut]);
        if (result && result.affectedRows > 0) {
            console.log(`Estado del paciente con RUT ${rut} actualizado a ${estado}`);
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

    const tiempo = '0 16 14 * * *';

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
