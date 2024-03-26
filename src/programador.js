const cron = require('node-cron');
const mysql = require('mysql2/promise');
const { enviarMensaje } = require('./mensaje.js');

const MSG_SALUDOS = 'Hola, recuerde que mañana tiene hora medica ¿va a asistir? responda Si o No';
const MSG_ASISTIRA = 'Perfecto, lo esperamos mañana';
const MSG_NO_ASISTIRA = 'Gracias por responder';

let respuestas = 0;

async function obtenerTelefonosDePacientes() {
    const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'Medirecord',
    });

    try {
        const [rows] = await connection.execute('SELECT telefono FROM paciente');
        if (rows && rows.length > 0) {
            return rows.map(row => row.telefono); // Retorna un array con todos los números de teléfono
        } else {
            console.log('No se encontraron números de teléfono en la consulta.');
            return [];
        }
    } catch (error) {
        console.log('Error en la consulta a la base de datos:', error);
        return [];
    }
}

async function programador_tareas(cliente) {
    const tiempo = '0 26 17 * * *'; // Ejecutar todos los días a las 20:27

    if (cron.validate(tiempo)) {
        console.log('Cron inicializado');
        cron.schedule(tiempo, async () => {
            try {
                const numeros = await obtenerTelefonosDePacientes();

                for (const numero of numeros) {
                    const numeroConFormato = '569' + numero + '@c.us';
                    await enviarMensaje(cliente, numeroConFormato, MSG_SALUDOS);
                    console.log('Mensaje enviado a:', numeroConFormato);
                }
            } catch (error) {
                console.log('Error en cron:', error);
            }
        });

        // Manejar eventos de mensajes
        cliente.on('message', async (message) => {
            try {
                manejarRespuesta(cliente, message);
            } catch (error) {
                console.log('Error al manejar la respuesta:', error);
            }
        });
    }
}

function manejarRespuesta(cliente, message) {
    if (message.body.toLowerCase() === 'si') {
        message.reply(MSG_ASISTIRA);
        respuestas++;
        if (respuestas === 1) {
            console.log('Aplicación finalizada');
            cliente.destroy(); // Cerrar el cliente de WhatsApp
            process.exit();
        }
    } else if (message.body.toLowerCase() === 'no') {
        message.reply(MSG_NO_ASISTIRA);
        respuestas++;
        if (respuestas === 1) {
            console.log('Aplicación finalizada');
            cliente.destroy(); // Cerrar el cliente de WhatsApp
            process.exit();
        }
    }
}

module.exports = {
    programador_tareas,
};
