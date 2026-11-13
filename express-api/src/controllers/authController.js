
import jwt from 'jsonwebtoken';
import pool from '../config/db.js';
import {
    hashPassword,
    verifyPassword
} from '../utils/password.js';

// JWT configuration
const JWT_SECRET = process.env.JWT_SECRET;
const JWT_REFRESH_SECRET = process.env.JWT_REFRESH_SECRET;

const JWT_EXPIRES_IN = process.env.JWT_EXPIRES_IN || '1h';
const JWT_REFRESH_EXPIRES_IN =
    process.env.JWT_REFRESH_EXPIRES_IN || '7d';

// LOG FAILED LOGIN ATTEMPT
const logFailedLogin = async (email, ipAddress) => {
    try {
        await pool.execute(
            `INSERT INTO login_attempts
             (email, ip_address, status)
             VALUES (?, ?, 'FAILED')`,
            [email || null, ipAddress || null]
        );
    } catch (error) {
        console.error(
            'Failed to log login attempt:',
            error.message
        );
    }
};

// REGISTER
export const register = async (req, res, next) => {
    try {
        const { username, email, password } = req.body;

        const [existing] = await pool.execute(
            'SELECT id FROM users WHERE email = ? OR username = ?',
            [email, username]
        );

        if (existing.length > 0) {
            return res.status(409).json({
                error: 'User already exists'
            });
        }

        const hashedPassword = await hashPassword(password);

        const [result] = await pool.execute(
            'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
            [username, email, hashedPassword]
        );

        res.status(201).json({
            message: 'User created successfully',
            id: result.insertId
        });

    } catch (error) {
        next(error);
    }
};

// LOGIN
export const login = async (req, res, next) => {
    try {
        const { email, password } = req.body;

        const [users] = await pool.execute(
            'SELECT * FROM users WHERE email = ?',
            [email]
        );

        // User does not exist
        if (users.length === 0) {
            await logFailedLogin(email, req.ip);

            return res.status(401).json({
                error: 'Invalid credentials'
            });
        }

        const user = users[0];

        const isMatch = await verifyPassword(
            password,
            user.password
        );

        // Incorrect password
        if (!isMatch) {
            await logFailedLogin(email, req.ip);

            return res.status(401).json({
                error: 'Invalid credentials'
            });
        }

        const payload = {
            id: user.id,
            role: user.role
        };

        // Access token
        const token = jwt.sign(
            payload,
            JWT_SECRET,
            { expiresIn: JWT_EXPIRES_IN }
        );

        // Refresh token
        const refreshToken = jwt.sign(
            payload,
            JWT_REFRESH_SECRET,
            { expiresIn: JWT_REFRESH_EXPIRES_IN }
        );

        res.json({
            token,
            refreshToken,
            role: user.role
        });

    } catch (error) {
        next(error);
    }
};

// REFRESH ACCESS TOKEN
export const refresh = async (req, res, next) => {
    try {
        const { refreshToken } = req.body;

        if (!refreshToken) {
            return res.status(401).json({
                error: 'Refresh token missing'
            });
        }

        const decoded = jwt.verify(
            refreshToken,
            JWT_REFRESH_SECRET
        );

        const payload = {
            id: decoded.id,
            role: decoded.role
        };

        const newToken = jwt.sign(
            payload,
            JWT_SECRET,
            { expiresIn: JWT_EXPIRES_IN }
        );

        res.json({
            token: newToken
        });

    } catch (error) {
        return res.status(401).json({
            error: 'Invalid or expired refresh token'
        });
    }
};

// GET MY PROFILE
export const getMe = async (req, res, next) => {
    try {
        const userId = req.user.id;

        const [users] = await pool.execute(
            `SELECT id, username, email, role, created_at
             FROM users
             WHERE id = ?`,
            [userId]
        );

        if (users.length === 0) {
            return res.status(404).json({
                error: 'User not found'
            });
        }

        res.json(users[0]);

    } catch (error) {
        next(error);
    }
};

// DELETE MY ACCOUNT
export const deleteMe = async (req, res, next) => {
    try {
        const userId = req.user.id;

        const [result] = await pool.execute(
            'DELETE FROM users WHERE id = ?',
            [userId]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({
                error: 'User not found'
            });
        }

        res.status(200).json({
            message: 'Your account has been deleted successfully'
        });

    } catch (error) {
        next(error);
    }
};
