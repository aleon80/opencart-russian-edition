<?php
class ControllerExtensionModuleAssemblyConfigurator extends Controller {
	private $errors = [];
	private $user_token;
	private $version = '3.0.3.6.RE9';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user_token = 'user_token=' . $this->session->data['user_token'];
	}

	public function install() {
		$this->load->model('setting/setting');

		$data = [
			'module_assembly_configurator_status' => 0
		];

		$this->model_setting_setting->editSetting('module_assembly_configurator', $data);
	}

	public function index() {
		$this->load->language('extension/module/assembly_configurator');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_assembly_configurator', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			],
			[
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/assembly_configurator', 'user_token=' . $this->session->data['user_token'], true)
			]
		];

		$data['action'] = $this->url->link('extension/module/assembly_configurator', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_assembly_configurator_status'])) {
			$data['module_assembly_configurator_status'] = $this->request->post['module_assembly_configurator_status'];
		} else {
			$data['module_assembly_configurator_status'] = $this->config->get('module_assembly_configurator_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/assembly_configurator', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/assembly_configurator')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}