-- Bảng banner cho trang chủ (Admin > Banner)
-- Chạy file này trong MySQL nếu chưa có bảng site_banners

CREATE TABLE IF NOT EXISTS `site_banners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL COMMENT 'main, mid, ad1, ad2, ad3',
  `image_url` varchar(500) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
