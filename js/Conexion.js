const mysql = require('mysql2/promise');

async function conectarBaseDatos() {
    const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '',
        database: 'Medirecord',
    });
    return connection;
}

module.exports = conectarBaseDatos;
