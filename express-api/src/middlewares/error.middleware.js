
export const errorHandler = (err, req, res, next) => {
    console.error(err.stack);

    // Invalid JWT
    if (err.name === 'JsonWebTokenError') {
        return res.status(401).json({
            success: false,
            error: {
                status: 401,
                message: 'Invalid token'
            }
        });
    }

    // Expired JWT
    if (err.name === 'TokenExpiredError') {
        return res.status(401).json({
            success: false,
            error: {
                status: 401,
                message: 'Token expired'
            }
        });
    }

    // Duplicate database entry
    if (err.code === 'ER_DUP_ENTRY') {
        return res.status(409).json({
            success: false,
            error: {
                status: 409,
                message: 'Resource already exists'
            }
        });
    }

    // Determine status code from error message
    const message = err.message || 'Internal server error';

    const statusCode = message.toLowerCase().includes('not found') ||
        message.toLowerCase().includes('non trouvé')
        ? 404
        : 400;

    // Internal server errors
    if (statusCode === 400 && err.status >= 500) {
        return res.status(500).json({
            success: false,
            error: {
                status: 500,
                message: 'Internal Server Error'
            }
        });
    }

    res.status(statusCode).json({
        success: false,
        error: {
            status: statusCode,
            message
        }
    });
};
