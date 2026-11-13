
import 'dotenv/config';
import pool from './config/db.js';

try {
    const connection = await pool.getConnection();

    console.log('MySQL connection successful!');

    connection.release();
} catch (error) {
    console.error('MySQL connection failed:', error.message);
} finally {
    await pool.end();
}
