-- Cho phép order_items.product_variant_id = NULL (sản phẩm không có biến thể)
-- Chạy một lần trong MySQL.

ALTER TABLE order_items
MODIFY COLUMN product_variant_id INT UNSIGNED NULL DEFAULT NULL;
