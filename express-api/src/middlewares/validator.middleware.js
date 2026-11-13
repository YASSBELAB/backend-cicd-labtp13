
import { body, validationResult } from 'express-validator';

export const validateProduct = [
  body('name')
    .notEmpty()
    .withMessage('Le nom est obligatoire')
    .isString()
    .withMessage('Le nom doit être une chaîne de caractères'),

  body('price')
    .isFloat({ gt: 0 })
    .withMessage('Le prix doit être un nombre positif'),

  body('stock')
    .isInt({ min: 0 })
    .withMessage('Le stock doit être un entier positif'),

  (req, res, next) => {
    const errors = validationResult(req);

    if (!errors.isEmpty()) {
      return res.status(400).json({
        success: false,
        errors: errors.array()
      });
    }

    next();
  }
];
