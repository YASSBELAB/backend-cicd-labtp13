
import jwt from 'jsonwebtoken';
import pool from '../config/db.js';

const JWT_SECRET = process.env.JWT_SECRET;

// AUTHENTICATE
export const authenticate = async (req, res, next) => {
    if (req.apiKeyAuthenticated && req.user) {
        return next();
    }
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({
            error: 'Access token missing'
        });
    }

    const token = authHeader.split(' ')[1];

    try {
        const decoded = jwt.verify(token, JWT_SECRET);

        // Check if the user still exists in the database
        const [users] = await pool.execute(
            'SELECT id, role FROM users WHERE id = ?',
            [decoded.id]
        );

        if (users.length === 0) {
            return res.status(401).json({
                error: 'Account no longer exists'
            });
        }

        req.user = {
            id: users[0].id,
            role: users[0].role
        };

        next();

    } catch (err) {
        if (
            err.name === 'JsonWebTokenError' ||
            err.name === 'TokenExpiredError'
        ) {
            return res.status(401).json({
                error: 'Invalid or expired token'
            });
        }

        next(err);
    }
};

// AUTHORIZE
export const authorize = (...roles) => {
    return (req, res, next) => {
        if (!req.user) {
            return res.status(401).json({
                error: 'User not authenticated'
            });
        }

        if (!roles.includes(req.user.role)) {
            return res.status(403).json({
                error: 'Forbidden: insufficient permissions'
            });
        }

        next();
    };
};
