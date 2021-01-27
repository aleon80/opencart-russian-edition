<?php
class ControllerExtensionModuleAssemblyConfiguratorAssemblyConfiguratorModification extends Controller {
	private $dir_modifications = DIR_STORAGE . 'ocn/modifications/';

	public function index() {
		$this->load->language('extension/module/assembly_configurator/assembly_configurator_modification');

		$files = new FilesystemIterator($this->dir_modifications);
		$xmls = new RegexIterator($files, '/.(xml)$/');

		$data = [
			'url_install' => $this->getFullLink('extension/module/assembly_configurator/install'),
			'xmls' => $xmls,
			'modifications' => $this->prepareModifications($xmls)
		];

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_modifications', $data);
	}

	private function prepareModifications($files) {
		$data = [];
		foreach($files as $file) {
			$fileFullPath = $this->dir_modifications . $file->getFilename();

			if (is_file($fileFullPath)) {
				$xml = file_get_contents($fileFullPath);
				if ($xml) {
					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->loadXml($xml);
					$data[$file->getFilename()] = [
						'name'    => $dom->getElementsByTagName('name')->item(0)->nodeValue,
						'code'    => $dom->getElementsByTagName('code')->item(0)->nodeValue,
						'author'  => $dom->getElementsByTagName('author')->item(0)->nodeValue,
						'version' => $dom->getElementsByTagName('version')->item(0)->nodeValue,
						'link'    => $dom->getElementsByTagName('link')->item(0)->nodeValue,
						'xml'     => $xml,
						'status'  => 1
					];
				}
			}
		}

		return $data;
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
