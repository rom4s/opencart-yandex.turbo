<?php
class ModelExtensionFeedYandexTurbo extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "yandex_turbo_category_to_category` (
				`yandex_turbo_category_id` INT(11) NOT NULL,
				`category_id` INT(11) NOT NULL,
				PRIMARY KEY (`yandex_turbo_category_id`, `category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "yandex_turbo_category_to_category`");
	}

	public function getYaCategories() {
		$query = "
			SELECT `category_id` 
			FROM  `" . DB_PREFIX . "yandex_turbo_category_to_category`
		";

		$query = $this->db->query( $query );
		return array_map( function( &$arCategory ){ return $arCategory['category_id']; }, $query->rows );
	}

	public function getProductsCount() {
		$lcid = (int)$this->config->get('config_language_id');

		$query = "
			SELECT COUNT(*) as `total` FROM (
				SELECT `" . DB_PREFIX . "product`.`product_id`, `image`, `" . DB_PREFIX . "product`.`price` AS `price`, `tax_class_id`, `date_added`, `date_modified`,
					`meta_title`, `name`, `meta_h1`, `description`, `meta_description`,
					`" . DB_PREFIX . "product_special`.`price` AS `special`
				FROM `" . DB_PREFIX . "product`
				LEFT OUTER JOIN `" . DB_PREFIX . "product_description`
					ON `" . DB_PREFIX . "product`.`product_id` = `" . DB_PREFIX . "product_description`.`product_id`
				LEFT OUTER JOIN `" . DB_PREFIX . "product_to_category`
					ON `" . DB_PREFIX . "product`.`product_id` = `" . DB_PREFIX . "product_to_category`.`product_id`
				LEFT OUTER JOIN `" . DB_PREFIX . "product_special`
					ON `" . DB_PREFIX . "product`.`product_id` = `" . DB_PREFIX . "product_special`.`product_id`
				WHERE `category_id` IN (
					SELECT `category_id` 
					FROM  `" . DB_PREFIX . "yandex_turbo_category_to_category` 
				) AND `status` = 1 AND `language_id` = {$lcid}
				GROUP BY `oc_product`.`product_id`
				ORDER BY `date_added` ASC
			) t1;
		";

		$query = $this->db->query( $query );
		return $query->row['total'];
	}

	public function saveCategories($arCategories) {
		( $arCategories ) || $arCategories = array();
		$sInsert = '';
		foreach ($arCategories as $nCategory) {
			if( $sInsert && $sInsert[0] ) $sInsert .= ',';
			$sInsert .= '(';
			$sInsert .= (int)$nCategory;
			$sInsert .= ')';
		}

		// Clear first...
		$this->db->query("TRUNCATE " . DB_PREFIX . "yandex_turbo_category_to_category");
		
		// Insert if exists categories...
		if( $sInsert && $sInsert[0] )
			$this->db->query("INSERT INTO " . DB_PREFIX . "yandex_turbo_category_to_category (category_id) VALUES {$sInsert}");
	}
}
