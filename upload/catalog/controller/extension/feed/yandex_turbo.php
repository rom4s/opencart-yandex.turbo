<?php
class ControllerExtensionFeedYandexTurbo extends Controller {
	public function index() {
		if ( !$this->config->get('yandex_turbo_status') )
			return;

		$output  = '<?xml version="1.0" encoding="UTF-8" ?>';
		$output .= '<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">';
		$output .= '  <channel>';
		$output .= '  <title>' . $this->config->get('config_name') . '</title>';
		$output .= '  <description>' . $this->config->get('config_meta_description') . '</description>';
		$output .= '  <link>' . $this->config->get('config_url') . '</link>';

		$this->load->model('extension/feed/yandex_turbo');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$product_data = array();

		$limit = (int)@$this->config->get('yandex_turbo_limit');
		$p = (int)@$this->request->get['p'];

		if( $p <= 0 )
			$p = 1;

		if( $limit < 0 )
			$limit = 1000;

		$start = ($p - 1) * $limit;
		$arProducts = $this->model_extension_feed_yandex_turbo->getProducts($limit, $start);

		$currencies = array(
			'USD',
			'RUB',
		);

		if ( in_array($this->session->data['currency'], $currencies) ) {
			$currency_code = $this->session->data['currency'];
			$currency_value = $this->currency->getValue($this->session->data['currency']);
		} else {
			$currency_code = 'USD';
			$currency_value = $this->currency->getValue($currency_code);
		}

		foreach ($arProducts as $arProduct) {
			$output .= '<item turbo="true">';

			$output .= '<title><![CDATA[' . $arProduct['meta_title'] ? $arProduct['meta_title'] : $arProduct['name'] . ']]></title>';
			$output .= '<link>' . $this->url->link('product/product', 'product_id=' . $arProduct['product_id']) . '</link>';
			$output .= '<pubDate>' . ( strpos($arProduct['date_modified'], '0000') === false ? $arProduct['date_modified'] : $arProduct['date_added'] ) . '</pubDate>';
			$output .= '<turbo:content><![CDATA['
				. '<header><h1>' . ($arProduct['meta_h1'] ? $arProduct['meta_h1'] : $arProduct['name']) . '</h1>'
				. '<figure><img src="' . $this->model_tool_image->resize($arProduct['image'], 300, 300) . '"/></figure></header>'
				. html_entity_decode($arProduct['description'], ENT_QUOTES, 'UTF-8') . ']]>'; // strip_tags(
			$output .= '<p>Цена: ';
			$output .= $this->currency->format($this->tax->calculate($arProduct['special'] ? $arProduct['special'] : $arProduct['price'], $arProduct['tax_class_id']), $currency_code, $currency_value, false);
			$output .= " {$currency_code}</p>";

			$output .= '</turbo:content>';
			$output .= '</item>';
		}

		$output .= '  </channel>';
		$output .= '</rss>';

		$this->response->addHeader('Content-type: text/html; charset=utf-8');
		$this->response->setOutput($output);
	}
}
