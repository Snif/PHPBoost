<?php
/**
 * @copyright   &copy; 2005-2021 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 12 01
 * @since       PHPBoost 4.1 - 2015 09 18
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class AdminReCaptchaConfig extends DefaultAdminModuleController
{
	public function execute(HTTPRequestCustom $request)
	{
		$this->build_form();

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();
			$this->execute_edit_config_hook();
			$this->view->put('MESSAGE_HELPER', MessageHelper::display($this->lang['warning.success.config'], MessageHelper::SUCCESS, 5));
		}

		$this->view->put('CONTENT', $this->form->display());

		return $this->build_response($this->view);
	}

	private function build_form()
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('config', $this->lang['config.title']);
		$form->add_fieldset($fieldset);

		$this->display_fields($fieldset);

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function display_fields(FormFieldset $fieldset)
	{
		$fieldset->add_field(new FormFieldFree('explain', '', $this->lang['config.recaptcha-explain']));

		$fieldset->add_field(new FormFieldTextEditor('site_key', $this->lang['config.site_key'], $this->config->get_site_key(),
			array('required' => true),
			array(new FormFieldConstraintLengthMin(30))
		));

		$fieldset->add_field(new FormFieldPasswordEditor('secret_key', $this->lang['config.secret_key'], $this->config->get_secret_key(),
			array('required' => true),
			array(new FormFieldConstraintLengthMin(30))
		));

		$fieldset->add_field(new FormFieldCheckbox('invisible_mode_enabled', $this->lang['config.invisible_mode_enabled'], $this->config->is_invisible_mode_enabled(),
			array(
				'class' => 'custom-checkbox',
				'description' => $this->lang['config.invisible_mode_enabled.explain']
			)
		));
	}

	private function save()
	{
		$this->config->set_site_key($this->form->get_value('site_key'));
		$this->config->set_secret_key($this->form->get_value('secret_key'));
		if ($this->form->get_value('invisible_mode_enabled'))
			$this->config->enable_invisible_mode();
		else
			$this->config->disable_invisible_mode();

		ReCaptchaConfig::save();
	}

	protected function execute_edit_config_hook()
	{
		HooksService::execute_hook_action('edit_config', self::$module_id, array('title' => StringVars::replace_vars($this->lang['form.module.title'], array('module_name' => self::get_module_configuration()->get_name())), 'url' => ModulesUrlBuilder::configuration()->rel()));
	}

	private function build_response(View $view)
	{
		$title = $this->lang['form.configuration'];

		$response = new AdminMenuDisplayResponse($view);
		$response->set_title($title);
		$response->add_link($this->lang['config.title'], DispatchManager::get_url('/ReCaptcha', '/admin/config/'));
		$env = $response->get_graphical_environment();
		$env->set_page_title($title);

		return $response;
	}

	public static function get_form_fields(FormFieldset $fieldset)
	{
		$object = new self('ReCaptcha');
		return $object->display_fields($fieldset);
	}

	public static function save_config(HTMLForm $form)
	{
		$object = new self('ReCaptcha');
		$object->form = $form;
		$object->save();
	}
}
?>
