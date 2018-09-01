<?php
class ControllerExtensionFeedYandexTurbo extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/feed/yandex_turbo');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->model('extension/feed/yandex_turbo');

		$bIsNewOpenCart = ( version_compare(VERSION, '2.3') >= 0 );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
		{
			// Save categories
			if (isset($this->request->post['yandex_categories']))
			{
				$this->model_extension_feed_yandex_turbo->saveCategories(@$this->request->post['yandex_categories']);
				unset($this->request->post['yandex_categories']);
			}
			
			// Items limit on feed page
			if (isset($this->request->post['yandex_turbo_limit'])) {
				$tval = (int)$this->request->post['yandex_turbo_limit'];
				if( $tval < 0 ) $tval = 0;

				$this->request->post['yandex_turbo_limit'] = $tval;
			}

			$this->model_setting_setting->editSetting('yandex_turbo', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link(
				$bIsNewOpenCart ? 'extension/extension' : 'extension/feed',
				'token=' . $this->session->data['token'] . '&type=feed', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_yandex_categories'] = $this->language->get('entry_yandex_categories');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_data_feed'] = $this->language->get('entry_data_feed');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_limit'] = $this->language->get('entry_limit');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_category_add'] = $this->language->get('button_category_add');

		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$data['text_save_selected_c'] = $this->language->get('text_save_selected_c');

		$data['lang_products_count'] = $this->language->get('product_count');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link(
				$bIsNewOpenCart ? 'extension/extension' : 'extension/feed',
				'token=' . $this->session->data['token'] . '&type=feed', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/yandex_turbo', 'token=' . $this->session->data['token'], true)
		);

		/* ---------- */

		$data['categories'] = $this->model_catalog_category->getCategories(0);
		$data['yandex_categories'] = $this->model_extension_feed_yandex_turbo->getYaCategories();

		if (isset($this->request->post['yandex_turbo_status'])) {
			$data['yandex_turbo_status'] = $this->request->post['yandex_turbo_status'];
		} else {
			$data['yandex_turbo_status'] = $this->config->get('yandex_turbo_status');
		}

		if (isset($this->request->post['yandex_turbo_limit'])) {
			$data['yandex_turbo_limit'] = (int)$this->request->post['yandex_turbo_limit'];
		} else {
			$data['yandex_turbo_limit'] = (int)$this->config->get('yandex_turbo_limit');
		}

		$data['yandex_turbo_count'] = $this->model_extension_feed_yandex_turbo->getProductsCount();

		$data['data_feed_ar'] = array();
		$ic = $data['yandex_turbo_count'] / $data['yandex_turbo_limit'] + ($data['yandex_turbo_count'] % $data['yandex_turbo_limit'] ? 1 : 0);
	
		for( $i = 1; $i <= $ic; ++$i )
		{
			$data['data_feed_ar'][] = HTTP_CATALOG . 'index.php?route=extension/feed/yandex_turbo&p=' . $i;
		}

		/* ---------- */

		$data['action'] = $this->url->link('extension/feed/yandex_turbo', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link(
			$bIsNewOpenCart ? 'extension/extension' : 'extension/feed',
			'token=' . $this->session->data['token'] . '&type=feed', true);

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view(
			'extension/feed/yandex_turbo' . ($bIsNewOpenCart ? '' : '.tpl'), $data
		));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/yandex_turbo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/feed/yandex_turbo');

		$this->model_extension_feed_yandex_turbo->install();
	}

	public function uninstall() {
		$this->load->model('extension/feed/yandex_turbo');

		$this->model_extension_feed_yandex_turbo->uninstall();
	}

	public function saveCategories() {
		$this->load->language('extension/feed/yandex_turbo');

		$json = array();

		if (!$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/feed/yandex_turbo');

			$this->model_extension_feed_yandex_turbo->saveCategories(@$this->request->post['categories']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getProductsCount() {
		$json = array();

		$this->load->model('extension/feed/yandex_turbo');

		$json['success'] = true;
		$json['count'] = $this->model_extension_feed_yandex_turbo->getProductsCount();
		$json['pages'] = array();

		$count = $this->model_extension_feed_yandex_turbo->getProductsCount();
		$limit = (int)$this->config->get('yandex_turbo_limit');
		if( !$limit )
			$limit = 1000;

		$ic = ( $count / $limit + ($count % $limit ? 1 : 0) );
		for( $i = 1; $i <= $ic; ++$i )
		{
			$json['pages'][] = HTTP_CATALOG . 'index.php?route=extension/feed/yandex_turbo&p=' . $i;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
