import swaggerUi from 'swagger-ui-express';
import yaml from 'js-yaml';
import fs from 'fs';

const spec = yaml.load(fs.readFileSync('./docs/express-openapi.yaml', 'utf8'));

export default (app) => {
  app.use(
    '/api-docs',
    swaggerUi.serve,
    swaggerUi.setup(spec, {
      explorer: true,
      swaggerOptions: {
        persistAuthorization: true
      }
    })
  );
};
