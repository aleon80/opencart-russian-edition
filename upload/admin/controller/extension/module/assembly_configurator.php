<?php
class ControllerExtensionModuleAssemblyConfigurator extends Controller {
	private $user_token;
	private $assembly_configurator;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->user_token = $this->session->data['user_token'];

		$this->load->model('setting/modification');
		$this->assembly_configurator = $this->model_setting_modification->getModificationByCode('ocn__assembly_configurator');
	}

	public function install() {
		$this->load->model('setting/setting');
		$data = [
			'module_assembly_configurator_status' => 0
		];
		$this->model_setting_setting->editSetting('module_assembly_configurator', $data);

		$this->load->model('setting/modification');
		$file_full_path = DIR_STORAGE . 'ocn/ocn__assembly_configurator.xml';
		if (is_file($file_full_path)) {
			$xml = file_get_contents($file_full_path);
			if ($xml) {
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->loadXml($xml);
				$modification = [
					'name' => $dom->getElementsByTagName('name')->item(0)->nodeValue,
					'code' => $dom->getElementsByTagName('code')->item(0)->nodeValue,
					'author' => $dom->getElementsByTagName('author')->item(0)->nodeValue,
					'version' => $dom->getElementsByTagName('version')->item(0)->nodeValue,
					'link' => $dom->getElementsByTagName('link')->item(0)->nodeValue,
					'xml' => $xml,
					'status' => 1,
				];
				$this->model_setting_modification->addModification($modification);
			}
		}
	}

	public function uninstall()
	{
		$this->load->model('setting/modification');
		$this->model_setting_modification->deleteModification($this->assembly_configurator['modification_id']);
	}

	public function index() {
		$this->load->language('extension/module/assembly_configurator/assembly_configurator');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('view/javascript/ocn/assembly_configurator.js');
		$this->document->addStyle('view/stylesheet/ocn/assembly_configurator.css');
		$this->load->model('setting/setting');

		$data['breadcrumbs'] = $this->getBreadcrumbs('extension/module/assembly_configurator');
		$data['data_version'] = $this->getVersion();

		// Urls
		$data['url_developer'] = $this->getFullLink('common/developer');
		$data['url_modification'] = $this->getFullLink('marketplace/modification/refresh', ['configurator' => 'on']);
		$data['url_back'] = $this->getFullLink('marketplace/extension', ['type' => 'module']);

		// Main
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Views
		$data['view_tab_info'] = $this->load->controller('extension/module/assembly_configurator/assembly_configurator_info');
		$data['view_tab_modifications'] = $this->load->controller('extension/module/assembly_configurator/assembly_configurator_modification');
		$data['view_tab_extensions'] = $this->load->controller('extension/module/assembly_configurator/assembly_configurator_extension');

		$this->response->setOutput($this->load->view('extension/module/assembly_configurator/assembly_configurator', $data));
	}

	public function getVersion() {
		return $this->assembly_configurator['version'];
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
