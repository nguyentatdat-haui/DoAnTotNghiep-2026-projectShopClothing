-- Support order items without variant (product only): add product_id to order_items
-- Run once. Safe to run if column already exists (ignore error).

ALTER TABLE order_items
ADD COLUMN product_id INT UNSIGNED NULL DEFAULT NULL AFTER order_id,
ADD KEY idx_order_items_product_id (product_id);

-- Optional: link to products
-- ALTER TABLE order_items ADD CONSTRAINT fk_order_items_product
--   FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;
