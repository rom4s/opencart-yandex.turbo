<?php
class ModelExtensionFeedYandexTurbo extends Model {
	public function getProducts($limit = 0, $start = 0) {
		$start = (int)$start;
		$limit = (int)$limit;
		$lcid = (int)$this->config->get('config_language_id');

		$query = "
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
		";

		if( $start < 0 )
			$start = 0;

		if( $limit < 1 )
			$limit = 250;

		if( $limit > 0 )
			$query .= " LIMIT {$start}, {$limit}";

		$query = $this->db->query( $query );
		return $query->rows;
	}
}
