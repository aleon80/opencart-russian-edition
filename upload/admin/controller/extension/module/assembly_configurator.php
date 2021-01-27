<?php
class ControllerExtensionModuleAssemblyConfigurator extends Controller {
	private $errors = [];
	private $user_token;
	private $version = '3.0.3.6.RE9';
	private $author_name = 'Hkr';
	private $author_link = 'https://forum.opencart.name/members/hkr.3/';
	private $extension_link = 'https://forum.opencart.name/resources/';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user_token = $this->session->data['user_token'];
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

			// Apply
			if (isset($this->request->post['apply']) && $this->request->post['apply']) {
				$this->response->redirect($this->getFullLink('extension/module/assembly_configurator'));
			}

			$this->response->redirect($this->getFullLink('marketplace/extension', ['type' => 'module']));
		}

		// Errors
		if (isset($this->errors['warning'])) {
			$data['error_warning'] = $this->errors['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = $this->getBreadcrumbs('extension/module/assembly_configurator');
		$data['data_version'] = $this->version;

		// Urls
		$data['url_action'] = $this->getFullLink('extension/module/assembly_configurator');
		$data['url_cancel'] = $this->getFullLink('marketplace/extension', ['type' => 'module']);

		// Main
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Views
		$data['view_tab_general'] = $this->viewTabGeneral();
		$data['view_tab_modifications'] = $this->viewTabModifications();
		$data['view_tab_extensions'] = $this->viewTabExtensions();
		$data['view_tab_info'] = $this->viewTabInfo();

		$this->response->setOutput($this->load->view('extension/module/assembly_configurator/assembly_configurator', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/assembly_configurator')) {
			$this->errors['warning'] = $this->language->get('error_permission');
		}

		return !$this->errors;
	}

	private function viewTabGeneral() {
		$data['module_assembly_configurator_status'] = isset($this->request->post['module_assembly_configurator_status'])
			? $this->request->post['module_assembly_configurator_status']
			: $this->config->get('module_assembly_configurator_status');

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_general', $data);
	}

	private function viewTabModifications() {
		$data = [];

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_modifications', $data);
	}

	private function viewTabExtensions() {
		$data = [];

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_extensions', $data);
	}

	private function viewTabInfo() {
		$data = [
			'data_extension_name' => $this->language->get('heading_title'),
			'url_extension_link' => $this->extension_link,
			'data_author_name' => $this->author_name,
			'url_author_link' => $this->author_link,
			'data_version' => $this->version,
		];

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_info', $data);
	}

	private function getBreadcrumbs($module) {
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->getFullLink('common/dashboard')
			],
			[
				'text' => $this->language->get('text_extension'),
				'href' => $this->getFullLink('marketplace/extension', ['type' => 'module'])
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->getFullLink($module)
			]
		];
	}

	private function getFullLink($module, $params = []) {
		$url = '';
		foreach ($params as $key => $value) {
			$url .= '&' . $key . '=' . $value;
		}
		$url .= '&user_token=' . $this->user_token;

		return $this->url->link($module, $url, true);
	}
}
