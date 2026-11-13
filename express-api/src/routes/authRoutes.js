
import { Router } from 'express';

import {
    register,
    login,
    getMe,
    refresh,
    deleteMe
} from '../controllers/authController.js';

import { authenticate } from '../middlewares/auth.js';

import {
    registerValidation,
    handleValidation
} from '../middlewares/validators.js';

const router = Router();

// Register
router.post(
    '/register',
    registerValidation,
    handleValidation,
    register
);

// Login
router.post('/login', login);

// Refresh access token
router.post('/refresh', refresh);

// Get authenticated user's profile
router.get('/me', authenticate, getMe);

// Delete authenticated user's account
router.delete('/me', authenticate, deleteMe);

export default router;
