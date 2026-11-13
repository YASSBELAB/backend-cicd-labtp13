import request from 'supertest';
import { afterAll, beforeAll, describe, it } from '@jest/globals';
import app from '../src/app.js';
import pool from '../src/config/db.js';

describe('API Security (API_KEY & JWT) + Products/Categories/Auth', () => {
  let jwtToken;
  const apiKey = process.env.API_KEY;
  const testUser = {
    username: `mocha_user_${Date.now()}`,
    email: `mocha_${Date.now()}@example.com`,
    password: 'Password123!'
  };

  beforeAll(async () => {
    await request(app)
      .post('/api/auth/register')
      .set('Authorization', `Bearer ${apiKey}`)
      .send(testUser)
      .expect(201);

    const loginRes = await request(app)
      .post('/api/auth/login')
      .set('Authorization', `Bearer ${apiKey}`)
      .send({
        email: testUser.email,
        password: testUser.password
      })
      .expect(200);

    jwtToken = loginRes.body.token;
  });

  afterAll(async () => {
    if (jwtToken) {
      await request(app)
        .delete('/api/auth/me')
        .set('Authorization', `Bearer ${jwtToken}`);
    }
    await pool.end();
  });

  it('rejects unauthenticated requests to all three APIs with 401', async () => {
    await request(app).get('/api/products').expect('Content-Type', /json/).expect(401);
    await request(app).get('/api/categories').expect('Content-Type', /json/).expect(401);
    await request(app).post('/api/auth/login').send({}).expect('Content-Type', /json/).expect(401);
  });

  it('rejects invalid Bearer API_KEY with 401', async () => {
    await request(app)
      .get('/api/categories')
      .set('Authorization', 'Bearer wrong-api-key')
      .expect('Content-Type', /json/)
      .expect(401);
  });

  it('GET /api/products responds with json (200 with API_KEY)', async () => {
    await request(app)
      .get('/api/products')
      .set('Authorization', `Bearer ${apiKey}`)
      .expect('Content-Type', /json/)
      .expect(200);
  });

  it('GET /api/categories responds with json (200 with API_KEY)', async () => {
    await request(app)
      .get('/api/categories')
      .set('Authorization', `Bearer ${apiKey}`)
      .expect('Content-Type', /json/)
      .expect(200);
  });

  it('GET /api/products responds with json (200 with valid JWT)', async () => {
    await request(app)
      .get('/api/products')
      .set('Authorization', `Bearer ${jwtToken}`)
      .expect('Content-Type', /json/)
      .expect(200);
  });
});
