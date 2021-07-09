<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2021 07 09
 * @since       PHPBoost 5.1 - 2017 09 28
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class AdminSandboxConfigController extends AdminModuleController
{
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonSubmit
	 */
	private $submit_button;

	private $lang;

	/**
	 * @var GoogleMapsConfig
	 */
	private $config;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->build_form();

		$view = new StringTemplate('# INCLUDE MESSAGE_HELPER # # INCLUDE FORM #');

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->save();

			$this->form->get_field_by_id('superadmin_name')->set_hidden(!$this->config->get_superadmin_enabled());

			$view->put('MESSAGE_HELPER', MessageHelper::display(LangLoader::get_message('warning.success.config', 'warning-lang'), MessageHelper::SUCCESS, 5));
		}

		$view->put('FORM', $this->form->display());

		return new AdminSandboxDisplayResponse($view, $this->lang['sandbox.config.module.title']);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'sandbox');
		$this->config = SandboxConfig::load();
	}

	private function build_form()
	{
		$menu_lang = LangLoader::get('menu-lang');
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTML('config', $this->lang['sandbox.config.module.title']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldSimpleSelectChoice('menu_opening_type', $menu_lang['menu.push.opening.type'], $this->config->get_menu_opening_type(),
			array(
				new FormFieldSelectChoiceOption($menu_lang['menu.push.opening.type.top'], SandboxConfig::TOP_MENU, array('data_option_icon' => 'fa fa-arrow-down')),
				new FormFieldSelectChoiceOption($menu_lang['menu.push.opening.type.right'], SandboxConfig::RIGHT_MENU, array('data_option_icon' => 'fa fa-arrow-left')),
				new FormFieldSelectChoiceOption($menu_lang['menu.push.opening.type.bottom'], SandboxConfig::BOTTOM_MENU, array('data_option_icon' => 'fa fa-arrow-up')),
				new FormFieldSelectChoiceOption($menu_lang['menu.push.opening.type.left'], SandboxConfig::LEFT_MENU, array('data_option_icon' => 'fa fa-arrow-right'))
			),
			array('select_to_list' => true)
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('expansion_type', $menu_lang['menu.push.expansion.type'], $this->config->get_expansion_type(),
			array(
				new FormFieldSelectChoiceOption($menu_lang['menu.push.expansion.type.overlap'], SandboxConfig::OVERLAP, array('data_option_icon' => 'fa fa-sign-in-alt')),
				new FormFieldSelectChoiceOption($menu_lang['menu.push.expansion.type.expand'], SandboxConfig::EXPANSION, array('data_option_icon' => 'fa fa-chevron-down')),
				new FormFieldSelectChoiceOption($menu_lang['menu.push.expansion.type.none'], SandboxConfig::NO_EXPANSION, array('data_option_icon' => 'fa fa-times-circle'))
			),
			array('select_to_list' => true)
		));

		$fieldset->add_field(new FormFieldCheckbox('disabled_body', $menu_lang['menu.push.disable.body'], $this->config->get_disabled_body(),
			array('class' => 'custom-checkbox')
		));

		$fieldset->add_field(new FormFieldCheckbox('pushed_content', $menu_lang['menu.push.push.content'], $this->config->get_pushed_content(),
			array('class' => 'custom-checkbox')
		));

		$fieldset->add_field(new FormFieldCheckbox('superadmin_enabled', $this->lang['sandbox.superadmin.enabled'], $this->config->get_superadmin_enabled(),
			array(
				'class' => 'custom-checkbox',
				'events' => array('click' => '
					if (HTMLForms.getField("superadmin_enabled").getValue()) {
						HTMLForms.getField("superadmin_name").enable();
					} else {
						HTMLForms.getField("superadmin_name").disable();
					}'
				)
			)
		));

		$fieldset->add_field(new FormFieldAjaxSearchUserAutoComplete('superadmin_name', $this->lang['sandbox.superadmin.id'], $this->config->get_superadmin_name(),
			array('hidden' => !$this->config->get_superadmin_enabled()),
			array(new SandboxConstraintUserIsAdmin)
		));

		$fieldset_authorizations = new FormFieldsetHTML('authorizations', LangLoader::get_message('form.authorizations', 'form-lang'));
		$form->add_fieldset($fieldset_authorizations);

		$auth_settings = new AuthorizationsSettings(array(
			new ActionAuthorization($this->lang['config.authorizations.read'], SandboxAuthorizationsService::READ_AUTHORIZATIONS)
		));

		$auth_settings->build_from_auth_array($this->config->get_authorizations());
		$fieldset_authorizations->add_field(new FormFieldAuthorizationsSetter('authorizations', $auth_settings));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function save()
	{
		$this->config->set_superadmin_enabled($this->form->get_value('superadmin_enabled'));
		$this->config->set_superadmin_name($this->form->get_value('superadmin_name'));
		$this->config->set_menu_opening_type($this->form->get_value('menu_opening_type')->get_raw_value());
		$this->config->set_expansion_type($this->form->get_value('expansion_type')->get_raw_value());
		$this->config->set_disabled_body($this->form->get_value('disabled_body'));
		$this->config->set_pushed_content($this->form->get_value('pushed_content'));

		$this->config->set_authorizations($this->form->get_value('authorizations')->build_auth_array());

		SandboxConfig::save();
	}
}
?>
