<?php
/**
 * @copyright 	&copy; 2005-2019 PHPBoost
 * @license 	https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version   	PHPBoost 5.2 - last update: 2018 10 29
 * @since   	PHPBoost 3.0 - 2011 02 08
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class AddNewsletterController extends ModuleController
{
	private $lang;
	/**
	 * @var HTMLForm
	 */
	private $form;
	/**
	 * @var FormButtonDefaultSubmit
	 */
	private $submit_button;

	private $send_test_button;

	public function execute(HTTPRequestCustom $request)
	{
		if (!NewsletterAuthorizationsService::default_authorizations()->create_newsletters())
		{
			NewsletterAuthorizationsService::get_errors()->create_newsletters();
		}

		$type = $request->get_value('type', '');

		$this->init();
		$this->build_form($type);

		$tpl = new StringTemplate('# INCLUDE MSG # # INCLUDE FORM #');
		$tpl->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->send_mail($type);
			$tpl->put('MSG', MessageHelper::display($this->lang['newsletter.message.success.add'], MessageHelper::SUCCESS, 5));
		}
		else if ($this->send_test_button->has_been_submited() && $this->form->validate())
		{
			$this->send_test($type);
			$tpl->put('MSG', MessageHelper::display($this->lang['newsletter.success-send-test'], MessageHelper::SUCCESS, 5));
		}

		$tpl->put('FORM', $this->form->display());

		return $this->build_response($tpl, $type);
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'newsletter');
	}

	private function build_form($type)
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTMLHeading('add-newsletter', $this->lang['newsletter-add']);
		$form->add_fieldset($fieldset);

		if (NewsletterConfig::load()->get_mail_sender())
		{
			$fieldset->add_field(new FormFieldMultipleCheckbox('newsletter_choice', $this->lang['add.choice_streams'], array(), $this->get_streams(),
				array('required' => true)
			));

			$fieldset->add_field(new FormFieldTextEditor('title', $this->lang['newsletter.title'], NewsletterConfig::load()->get_newsletter_name(), array(
				'required' => true)
			));

			$fieldset->add_field($this->return_editor($type));

			$this->submit_button = new FormButtonDefaultSubmit();
			$this->send_test_button = new FormButtonSubmit($this->lang['add.send_test'], 'send_test');
			$form->add_button($this->submit_button);
			$form->add_button($this->send_test_button);
			$form->add_button(new FormButtonReset());
		}
		else
		{
			$fieldset->add_field(new FormFieldHTML('mail_sender_not_configured_msg', MessageHelper::display($this->lang['error.sender-mail-not-configured' . (AppContext::get_current_user()->is_admin() ? '-for-admin' : '')], MessageHelper::WARNING)->render()));

			$this->submit_button = new FormButtonDefaultSubmit();
		}

		$this->form = $form;
	}

	private function send_mail($type)
	{
		$streams = array();
		foreach ($this->form->get_value('newsletter_choice') as $field => $option)
		{
			$streams[] = $option->get_id();
		}

		NewsletterService::add_newsletter(
			$streams,
			$this->form->get_value('title'),
			$this->form->get_value('contents'),
			$type
		);
	}

	private function send_test($type)
	{
		$newsletter_config = NewsletterConfig::load();
		$subscribers[] = array('user_id' => AppContext::get_current_user()->get_id());
		NewsletterMailFactory::send_mail(
			$subscribers,
			$type,
			$newsletter_config->get_mail_sender(),
			$this->form->get_value('title'),
			$this->form->get_value('contents')
		);
	}

	private function build_response(View $view, $type)
	{
		$body_view = new FileTemplate('newsletter/NewsletterBody.tpl');
		$body_view->add_lang($this->lang);
		$body_view->put('TEMPLATE', $view);
		$response = new SiteDisplayResponse($body_view);
		$breadcrumb = $response->get_graphical_environment()->get_breadcrumb();
		$breadcrumb->add($this->lang['newsletter'], NewsletterUrlBuilder::home()->rel());
		$breadcrumb->add($this->lang['newsletter-add'], NewsletterUrlBuilder::add_newsletter()->rel());
		$breadcrumb->add($this->lang['newsletter.types.' . $type], NewsletterUrlBuilder::add_newsletter($type)->rel());
		$response->get_graphical_environment()->set_page_title($this->lang['newsletter-add'], $this->lang['newsletter']);
		return $response;
	}

	private function get_streams()
	{
		$streams = array();
		$newsletter_streams = NewsletterStreamsCache::load()->get_streams();
		foreach ($newsletter_streams as $id => $stream)
		{
			if ($id != Category::ROOT_CATEGORY && NewsletterAuthorizationsService::id_stream($id)->subscribe())
				$streams[] = new FormFieldMultipleCheckboxOption($id, $stream->get_name());
		}
		return $streams;
	}

	public function return_editor($type)
	{
		if ($type == 'bbcode')
		{
			return new FormFieldRichTextEditor('contents', $this->lang['newsletter.contents'], '', array(
				'rows' => 10, 'cols' => 47, 'required' => true)
			);
		}
		else
		{
			return new FormFieldMultiLineTextEditor('contents', $this->lang['newsletter.contents'], '', array(
				'rows' => 10, 'cols' => 47, 'required' => true)
			);
		}
	}
}
?>
