
USE product_api;

INSERT IGNORE INTO categories (name) VALUES
('Electronics'),
('Accessories'),
('Computers');

INSERT INTO products (name, price, stock, category_id)
SELECT 'Laptop', 899.99, 10, id
FROM categories WHERE name = 'Computers';

INSERT INTO products (name, price, stock, category_id)
SELECT 'Headphones', 59.99, 25, id
FROM categories WHERE name = 'Accessories';

INSERT INTO products (name, price, stock, category_id)
SELECT 'Smartphone', 499.99, 15, id
FROM categories WHERE name = 'Electronics';
