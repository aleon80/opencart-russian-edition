<?php
class ControllerExtensionModuleAssemblyConfiguratorAssemblyConfiguratorExtension extends Controller {
	private $error = [];
	private $dir_modifications = DIR_STORAGE . 'ocn/extensions/';

	public function index() {
		$this->load->language('extension/module/assembly_configurator/assembly_configurator_extensions');

		$data = [];

		return $this->load->view('extension/module/assembly_configurator/assembly_configurator_extensions', $data);
	}
}
