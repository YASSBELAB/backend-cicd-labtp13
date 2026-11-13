import 'dotenv/config';
import { readFile } from 'node:fs/promises';
import mysql from 'mysql2/promise';

const connection = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true
});

try {
    const schema = await readFile(new URL('../sql/create_database.sql', import.meta.url), 'utf8');
    await connection.query(schema);
    console.log('Express test database schema initialized.');
} finally {
    await connection.end();
}
