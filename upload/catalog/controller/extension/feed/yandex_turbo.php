<?php
class ControllerExtensionFeedYandexTurbo extends Controller {
	public function index()
	{
		if ( !$this->config->get('yandex_turbo_status') )
			return;
		
		$sCodeA = $this->config->get('yandex_turbo_code');
		$sCodeB = @$this->request->get['code'];

		if( $sCodeA && ( !$sCodeB || $sCodeA !== $sCodeB ) )
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

		$bCategory = @$this->request->get['categories'] === '1';
		$limit = (int)@$this->config->get('yandex_turbo_limit');
		$p = (int)@$this->request->get['p'];

		if( $p <= 0 )
			$p = 1;

		if( $limit < 0 )
			$limit = 1000;

		$start = ($p - 1) * $limit;

		$output .= (!$bCategory) ? $this->__getProducts($limit, $start) : $this->__getCategories($limit, $start);

		$output .= '  </channel>';
		$output .= '</rss>';

		$this->response->addHeader('Content-type: text/html; charset=utf-8');
		$this->response->setOutput($output);
	}

	private function __getCategories(&$limit, &$start)
	{
		$output = '';

		if( !$this->config->get('yandex_turbo_for_categories') )
			return $_output;

		$arCategories = $this->model_extension_feed_yandex_turbo->getCategories($limit, $start);

		foreach ($arCategories as $arCategory)
		{
			$output .= '<item turbo="true">';
			$output .= '<title><![CDATA[' . $arCategory['name'] . ']]></title>';
			
			$link_path = $this->__getCategoryLinkPPath($arCategory['parent_id'], $arCategories);
			if( $link_path ) $link_path .= '_';
			$link_path .= $arCategory['category_id'];
			
			$output .= '<link>' . $this->url->link('product/category', 'path=' . $link_path ) . '</link>';

			$output .= '<pubDate>' . ( strpos($arCategory['date_modified'], '0000') === false ? $arCategory['date_modified'] : $arCategory['date_added'] ) . '</pubDate>';
			$output .= '<turbo:content><![CDATA[';
			$output .= '<header>';
			$output .= '<h1>' . $arCategory['name'] . '</h1>';
			if($arCategory['image'])
				$output .= '<figure><img src="' . $this->model_tool_image->resize($arCategory['image'], 300, 300) . '"/></figure>';
			$output .= '</header>';

			$subCategories = $this->__getCategorySub($arCategory['category_id'], $arCategories);
			if( $subCategories && $subCategories[0] )
			{
				$output .= '<p>Разделы:</p>';
				$output .= $subCategories;
			}
			else
			{
				$output .= '<p>&nbsp;</p>';
			}
	
			$output .= ']]></turbo:content>';
			$output .= '</item>';
		};

		return $output;
	}

	private function __getCategoryLinkPPath($parent, &$arCategories, $limit = 15)
	{
		$retval = '';

		if( !(int)$parent )
			return $retval;
		
		$retval .= $parent;

		if( !(int)$limit )
			return $retval;

		foreach ($arCategories as $arCategory)
		{
			if($arCategory['category_id'] == $parent && $arCategory['parent_id'])
			{
				$retval .= '_';
				$retval .= $this->__getCategoryLinkPPath( $arCategory['parent_id'], $arCategories, --$limit );
				break;
			}
		}
	
		return $retval;
	}

	private function __getCategorySub(&$parent, &$arCategories)
	{
		$output = '';

		foreach ($arCategories as $arCategory)
		{
			if( $arCategory['parent_id'] != $parent )
				continue;
			
			$output .= '<p><a href="' . $this->url->link('product/category', 'path=' . $arCategory['category_id']) . '">'. $arCategory['name'] . '</a></p>';
		}

		return $output;
	}

	private function __getProducts(&$limit, &$start)
	{
		$output = '';

		$arProducts = $this->model_extension_feed_yandex_turbo->getProducts($limit, $start);
		// for path
		$arCategories = $this->model_extension_feed_yandex_turbo->getCategories($limit, $start);

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

		$phone = $this->config->get('yandex_turbo_phone');

		foreach ($arProducts as $arProduct)
		{
			$link_path = $this->__getCategoryLinkPPath($arProduct['category_id'], $arCategories);
			$output .= '<item turbo="true">';
			$output .= '<title><![CDATA[' . $arProduct['meta_title'] ? $arProduct['meta_title'] : $arProduct['name'] . ']]></title>';
			$output .= '<link>' . $this->url->link('product/product', 'path='. $link_path .'&product_id=' . $arProduct['product_id'] ) . '</link>';
			$output .= '<pubDate>' . ( strpos($arProduct['date_modified'], '0000') === false ? $arProduct['date_modified'] : $arProduct['date_added'] ) . '</pubDate>';
			$output .= '<turbo:content><![CDATA['
				. '<header><h1>' . ($arProduct['meta_h1'] ? $arProduct['meta_h1'] : $arProduct['name']) . '</h1>'
				. '<figure><img src="' . $this->model_tool_image->resize($arProduct['image'], 300, 300) . '"/></figure></header>'
				. html_entity_decode($arProduct['description'], ENT_QUOTES, 'UTF-8') . ']]>';
			$output .= '<p>Цена: от ';
			$output .= $this->currency->format($this->tax->calculate($arProduct['special'] ? $arProduct['special'] : $arProduct['price'], $arProduct['tax_class_id']), $currency_code, $currency_value, false);
			$output .= " {$currency_code}</p>";

			if( $phone )
				$output .= '<button formaction="tel:'. $phone .'" data-background-color="#e00020" data-color="white" data-primary="true">Позвонить</button>';

			$output .= '</turbo:content>';
			$output .= '</item>';
		}

		return $output;
	}
}
