-- Thêm các cột thiếu vào bảng products (chạy từng dòng trong phpMyAdmin/MySQL)
-- Nếu báo "Duplicate column name" thì cột đó đã có, bỏ qua dòng đó.

ALTER TABLE `products` ADD COLUMN `thumbnail` VARCHAR(500) NULL DEFAULT NULL;
ALTER TABLE `products` ADD COLUMN `is_new` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `products` ADD COLUMN `is_best_seller` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `products` ADD COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'active';
