import crypto from 'crypto';
import jwt from 'jsonwebtoken';
import pool from '../config/db.js';

const tokensMatch = (provided, expected) => {
    const a = Buffer.from(provided, 'utf8');
    const b = Buffer.from(expected, 'utf8');
    return a.length === b.length && crypto.timingSafeEqual(a, b);
};

/**
 * Secures API routes using `Authorization: Bearer <API_KEY>` (or a valid JWT).
 */
export const requireApiAuth = async (req, res, next) => {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({
            error: 'Access token or API key missing'
        });
    }

    const token = authHeader.slice(7).trim();
    const configuredApiKey = process.env.API_KEY;

    if (configuredApiKey && tokensMatch(token, configuredApiKey)) {
        req.apiKeyAuthenticated = true;
        req.user = { id: 0, role: 'ADMIN' };
        return next();
    }

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

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
        return next();
    } catch (err) {
        if (
            err.name === 'JsonWebTokenError' ||
            err.name === 'TokenExpiredError'
        ) {
            return res.status(401).json({
                error: 'Invalid or expired API key / token'
            });
        }

        return next(err);
    }
};
