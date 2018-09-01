<?php

class ControllerFeedYandexTurbo extends Controller {

    private $error = array();

    public function index() {
        $this->response->redirect($this->url->link('extension/feed/yandex_turbo', 'token=' . $this->session->data['token'], 'SSL'));
    }

	public function install() {
		$this->load->model('extension/feed/yandex_turbo');

		$this->model_extension_feed_yandex_turbo->install();
	}

	public function uninstall() {
		$this->load->model('extension/feed/yandex_turbo');

		$this->model_extension_feed_yandex_turbo->uninstall();
	}
}