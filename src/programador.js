const cron = require('node-cron');
const mysql = require('mysql2/promise');
const { enviarMensaje } = require('./mensaje.js');


const MSG_SALUDOS = 'Hola, recuerde que mañana tiene hora ¿va a asistir? responda Si o No'
const MSG_ASISTIRA = 'Perfecto, lo esperamos mañana'
const MSG_NO_ASISTIRA = 'Gracias por responder'

let respuestas = 0;

async function obtenerTelefonoDePaciente() {
    const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'somecloud',
    });

    try {
        const [rows] = await connection.execute('SELECT telefono FROM paciente');
// while for file=0;file>=(rown.length;fila)
        if (rows && rows.length > 0 && rows[0].telefono) {
            return rows[0].telefono;
        } else {
            console.log('No se encontró un número de teléfono en la consulta.');
            return null;
        }
    } catch (error) {
        console.log('Error en la consulta a la base de datos:', error);
        return null;
    }
}



async function programador_tareas(cliente) {
    const tiempo = '0 35 14 * * *';
    if (cron.validate(tiempo)) {
        console.log('Cron inicializado');
        cron.schedule(tiempo, async () => {
            try {
                const numero = await obtenerTelefonoDePaciente();

                if (numero !== null) {
                    const numeroConFormato ='569'+ numero + '@c.us';
                    await enviarMensaje(cliente, numeroConFormato, MSG_SALUDOS);
                    console.log(numeroConFormato); 
                    console.log('Mensaje enviado');

                    // Manejar eventos de mensajes después de enviar el mensaje
                    cliente.on('message', async (message) => {
                        try {
                            manejarRespuesta(message);
                        } catch (error) {
                            console.log('Error al manejar la respuesta:', error);
                        }
                    });
                } 
            } catch (error) {
                console.log('Error en cron:', error);
            }
        });
        // Manejar eventos de mensajes
    }
}



 function manejarRespuesta(message) {
    if (message.body === 'Si' || message.body === 'si') {
         message.reply(MSG_ASISTIRA);
        respuestas++;
        if (respuestas === 1) {
            console.log('Aplicacion finalizada');
            process.exit();
        }
    } else if (message.body === 'No' || message.body === 'no') {
        message.reply(MSG_NO_ASISTIRA);
        respuestas++;
        if (respuestas === 1) {
            console.log('Aplicacion finalizada');
            process.exit();
        }
    }
}

module.exports = {
    programador_tareas,
};