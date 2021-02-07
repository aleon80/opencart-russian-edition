<?php
class ControllerExtensionModuleAssemblyConfigurator extends Controller {
	private $errors = [];
	private $user_token;
	private $version = '3.0.0.0';
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
		$this->load->language('extension/module/assembly_configurator/assembly_configurator');
		$this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('view/javascript/ocn/assembly_configurator.js');
		$this->document->addStyle('view/stylesheet/ocn/assembly_configurator.css');
		$this->load->model('setting/setting');

		$data['breadcrumbs'] = $this->getBreadcrumbs('extension/module/assembly_configurator');
		$data['data_version'] = $this->version;

		// Urls
		$data['url_cancel'] = $this->getFullLink('marketplace/extension', ['type' => 'module']);

		// Main
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Views
		$data['view_tab_general'] = $this->viewTabGeneral();
		$data['view_tab_info'] = $this->viewTabInfo();

		$data['view_tab_modifications'] = $this->load->controller('extension/module/assembly_configurator/assembly_configurator_modification');
		$data['view_tab_extensions'] = $this->load->controller('extension/module/assembly_configurator/assembly_configurator_extension');

		$this->response->setOutput($this->load->view('extension/module/assembly_configurator/assembly_configurator', $data));
	}

	private function viewTabGeneral() {
		$data['module_assembly_configurator_status'] = isset($this->request->post['module_assembly_configurator_status'])
			? $this->request->post['module_assembly_configurator_status']
			: $this->config->get('module_assembly_configurator_status');

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_general', $data);
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
			'data_version_re' => VERSION_RE,
			'data_version_oc' => VERSION,
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
