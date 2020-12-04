<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 04
 * @since       PHPBoost 4.0 - 2014 08 24
 * @contributor Arnaud GENET <elenwii@phpboost.com>
 * @contributor Mipel <mipel@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class DownloadFormController extends ModuleController
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
	private $common_lang;

	private $config;

	private $item;
	private $is_new_item;

	public function execute(HTTPRequestCustom $request)
	{
		$this->init();

		$this->check_authorizations();

		$this->build_form($request);

		$view = new StringTemplate('# INCLUDE FORM #');
		$view->add_lang($this->lang);

		if ($this->submit_button->has_been_submited() && $this->form->validate())
		{
			$this->form->get_field_by_id('file_size')->set_hidden($this->is_file_size_automatic());
			$this->form->get_field_by_id('file_size_unit')->set_hidden($this->is_file_size_automatic());
			$this->save();
			$this->redirect();
		}

		$view->put('FORM', $this->form->display());

		return $this->generate_response($view);
	}

	private function init()
	{
		$this->config = DownloadConfig::load();
		$this->lang = LangLoader::get('common', 'download');
		$this->common_lang = LangLoader::get('common');
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$form = new HTMLForm(__CLASS__);

		$fieldset = new FormFieldsetHTMLHeading('download', $this->get_downloadfile()->get_id() === null ? $this->lang['download.add.item'] : $this->lang['download.edit.item']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldTextEditor('title', $this->common_lang['form.name'], $this->get_downloadfile()->get_title(), array('required' => true)));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
		{
			$search_category_children_options = new SearchCategoryChildrensOptions();
			$search_category_children_options->add_authorizations_bits(Category::CONTRIBUTION_AUTHORIZATIONS);
			$search_category_children_options->add_authorizations_bits(Category::WRITE_AUTHORIZATIONS);
			$fieldset->add_field(CategoriesService::get_categories_manager()->get_select_categories_form_field('id_category', $this->common_lang['form.category'], $this->get_downloadfile()->get_id_category(), $search_category_children_options));
		}

		$fieldset->add_field(new FormFieldUploadFile('url', $this->common_lang['form.url'], $this->get_downloadfile()->get_url()->relative(), array('required' => true)));

		$fieldset->add_field(new FormFieldCheckbox('determine_file_size_automatically_enabled', $this->lang['download.form.file.size.auto'], $this->is_file_size_automatic(),
			array(
				'events' => array('click' => '
					if (HTMLForms.getField("determine_file_size_automatically_enabled").getValue()) {
						HTMLForms.getField("file_size").disable();
						HTMLForms.getField("file_size_unit").disable();
					} else {
						HTMLForms.getField("file_size").enable();
						HTMLForms.getField("file_size_unit").enable();
					}'
				)
			)
		));

		if (!empty($this->get_downloadfile()->get_size()))
		{
			$formated_file_size = explode(' ', $this->get_downloadfile()->get_formated_size());
			$file_size = $formated_file_size[0];
			$file_size_unit = $formated_file_size[1];
		}
		else
			$file_size = $file_size_unit = 0;

		$fieldset->add_field(new FormFieldDecimalNumberEditor('file_size', $this->lang['download.form.file.size'], $file_size,
			array(
				'min' => 0, 'step' => 0.01, 'required' => true,
				'hidden' => ($request->is_post_method() ? $request->get_postbool(__CLASS__ . '_determine_file_size_automatically_enabled', false) : $this->is_file_size_automatic())
			)
		));

		$fieldset->add_field(new FormFieldSimpleSelectChoice('file_size_unit', $this->lang['download.form.file.size.unit'], $file_size_unit,
			array(
				new FormFieldSelectChoiceOption('', ''),
				new FormFieldSelectChoiceOption($this->common_lang['unit.kilobytes'], $this->common_lang['unit.kilobytes']),
				new FormFieldSelectChoiceOption($this->common_lang['unit.megabytes'], $this->common_lang['unit.megabytes']),
				new FormFieldSelectChoiceOption($this->common_lang['unit.gigabytes'], $this->common_lang['unit.gigabytes'])
			),
			array(
				'required' => true,
				'hidden' => ($request->is_post_method() ? $request->get_postbool(__CLASS__ . '_determine_file_size_automatically_enabled', false) : $this->is_file_size_automatic())
			)
		));

		if ($this->get_downloadfile()->get_id() !== null && $this->get_downloadfile()->get_downloads_number() > 0)
		{
			$fieldset->add_field(new FormFieldCheckbox('reset_downloads_number', $this->lang['download.form.reset.downloads.number']));
		}

		$fieldset->add_field(new FormFieldRichTextEditor('contents', $this->common_lang['form.description'], $this->get_downloadfile()->get_contents(), array('rows' => 15, 'required' => true)));

		$fieldset->add_field(new FormFieldCheckbox('summary_enabled', $this->common_lang['form.short_contents.enabled'], $this->get_downloadfile()->is_summary_enabled(),
			array(
				'description' => StringVars::replace_vars($this->common_lang['form.short_contents.enabled.description'], array('number' => DownloadConfig::CHARACTERS_NUMBER_TO_CUT)),
				'events' => array('click' => '
					if (HTMLForms.getField("summary_enabled").getValue()) {
						HTMLForms.getField("summary").enable();
					} else {
						HTMLForms.getField("summary").disable();
					}'
				)
			)
		));

		$fieldset->add_field(new FormFieldRichTextEditor('summary', $this->common_lang['form.description'], $this->get_downloadfile()->get_summary(),
			array('hidden' => ($request->is_post_method() ? !$request->get_postbool(__CLASS__ . '_summary_enabled', false) : !$this->get_downloadfile()->is_summary_enabled()))
		));

		if ($this->config->is_author_displayed())
		{
			$fieldset->add_field(new FormFieldCheckbox('author_custom_name_enabled', $this->common_lang['form.author_custom_name_enabled'], $this->get_downloadfile()->is_author_custom_name_enabled(),
				array(
					'events' => array('click' => '
						if (HTMLForms.getField("author_custom_name_enabled").getValue()) {
							HTMLForms.getField("author_custom_name").enable();
						} else {
							HTMLForms.getField("author_custom_name").disable();
						}'
					)
				)
			));

			$fieldset->add_field(new FormFieldTextEditor('author_custom_name', $this->common_lang['form.author_custom_name'], $this->get_downloadfile()->get_author_custom_name(),
				array('hidden' => ($request->is_post_method() ? !$request->get_postbool(__CLASS__ . '_author_custom_name_enabled', false) : !$this->get_downloadfile()->is_author_custom_name_enabled()))
			));
		}

		$options_fieldset = new FormFieldsetHTML('options', $this->common_lang['form.options']);
		$form->add_fieldset($options_fieldset);

		$options_fieldset->add_field(new FormFieldUploadPictureFile('thumbnail', $this->common_lang['form.picture'], $this->get_downloadfile()->get_thumbnail()->relative()));

		$options_fieldset->add_field(new FormFieldTextEditor('software_version', $this->lang['download.version'], $this->get_downloadfile()->get_software_version()));

		$options_fieldset->add_field(KeywordsService::get_keywords_manager()->get_form_field($this->get_downloadfile()->get_id(), 'keywords', $this->common_lang['form.keywords'],
			array('description' => $this->common_lang['form.keywords.description'])
		));

		$options_fieldset->add_field(new FormFieldSelectSources('sources', $this->common_lang['form.sources'], $this->get_downloadfile()->get_sources()));

		if (DownloadAuthorizationsService::check_authorizations($this->get_downloadfile()->get_id_category())->moderation())
		{
			$publication_fieldset = new FormFieldsetHTML('publication', $this->common_lang['form.approbation']);
			$form->add_fieldset($publication_fieldset);

			$publication_fieldset->add_field(new FormFieldDateTime('creation_date', $this->common_lang['form.date.creation'], $this->get_downloadfile()->get_creation_date(),
				array('required' => true)
			));

			if (!$this->get_downloadfile()->is_visible())
			{
				$publication_fieldset->add_field(new FormFieldCheckbox('update_creation_date', $this->common_lang['form.update.date.creation'], false,
					array('hidden' => $this->get_downloadfile()->get_status() != DownloadFile::NOT_APPROVAL)
				));
			}

			$publication_fieldset->add_field(new FormFieldSimpleSelectChoice('approbation_type', $this->common_lang['form.approbation'], $this->get_downloadfile()->get_approbation_type(),
				array(
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.not'], DownloadFile::NOT_APPROVAL),
					new FormFieldSelectChoiceOption($this->common_lang['form.approbation.now'], DownloadFile::APPROVAL_NOW),
					new FormFieldSelectChoiceOption($this->common_lang['status.approved.date'], DownloadFile::APPROVAL_DATE),
				),
				array(
					'events' => array('change' => '
						if (HTMLForms.getField("approbation_type").getValue() == 2) {
							jQuery("#' . __CLASS__ . '_start_date_field").show();
							HTMLForms.getField("end_date_enabled").enable();
							if (HTMLForms.getField("end_date_enabled").getValue()) {
								HTMLForms.getField("end_date").enable();
							}
						} else {
							jQuery("#' . __CLASS__ . '_start_date_field").hide();
							HTMLForms.getField("end_date_enabled").disable();
							HTMLForms.getField("end_date").disable();
						}'
					)
				)
			));

			$publication_fieldset->add_field($start_date = new FormFieldDateTime('start_date', $this->common_lang['form.date.start'], ($this->get_downloadfile()->get_start_date() === null ? new Date() : $this->get_downloadfile()->get_start_date()),
				array('hidden' => ($request->is_post_method() ? ($request->get_postint(__CLASS__ . '_approbation_type', 0) != DownloadFile::APPROVAL_DATE) : ($this->get_downloadfile()->get_approbation_type() != DownloadFile::APPROVAL_DATE)))
			));

			$publication_fieldset->add_field(new FormFieldCheckbox('end_date_enabled', $this->common_lang['form.date.end.enable'], $this->get_downloadfile()->is_end_date_enabled(),
				array(
					'hidden' => ($request->is_post_method() ? ($request->get_postint(__CLASS__ . '_approbation_type', 0) != DownloadFile::APPROVAL_DATE) : ($this->get_downloadfile()->get_approbation_type() != DownloadFile::APPROVAL_DATE)),
					'events' => array('click' => '
						if (HTMLForms.getField("end_date_enabled").getValue()) {
							HTMLForms.getField("end_date").enable();
						} else {
							HTMLForms.getField("end_date").disable();
						}'
					)
				)
			));

			$publication_fieldset->add_field($end_date = new FormFieldDateTime('end_date', $this->common_lang['form.date.end'], ($this->get_downloadfile()->get_end_date() === null ? new Date() : $this->get_downloadfile()->get_end_date()),
				array('hidden' => ($request->is_post_method() ? !$request->get_postbool(__CLASS__ . '_end_date_enabled', false) : !$this->get_downloadfile()->is_end_date_enabled()))
			));

			$end_date->add_form_constraint(new FormConstraintFieldsDifferenceSuperior($start_date, $end_date));
		}

		$this->build_contribution_fieldset($form);

		$fieldset->add_field(new FormFieldHidden('referrer', $request->get_url_referrer()));

		$this->submit_button = new FormButtonDefaultSubmit();
		$form->add_button($this->submit_button);
		$form->add_button(new FormButtonReset());

		$this->form = $form;
	}

	private function build_contribution_fieldset($form)
	{
		if ($this->is_contributor_member())
		{
			$fieldset = new FormFieldsetHTML('contribution', LangLoader::get_message('contribution', 'user-common'));
			$fieldset->set_description(MessageHelper::display($this->lang['download.form.contribution.explain'] . ' ' . LangLoader::get_message('contribution.explain', 'user-common'), MessageHelper::WARNING)->render());
			$form->add_fieldset($fieldset);

			$fieldset->add_field(new FormFieldRichTextEditor('contribution_description', LangLoader::get_message('contribution.description', 'user-common'), '', array('description' => LangLoader::get_message('contribution.description.explain', 'user-common'))));
		}
	}

	private function is_contributor_member()
	{
		return (!DownloadAuthorizationsService::check_authorizations()->write() && DownloadAuthorizationsService::check_authorizations()->contribution());
	}

	private function is_file_size_automatic()
	{
		return $this->get_downloadfile()->get_id() === null || $this->get_downloadfile()->get_size() == 0 || ($this->get_downloadfile()->get_size() ==  Url::get_url_file_size($this->get_downloadfile()->get_url()));
	}

	private function get_downloadfile()
	{
		if ($this->item === null)
		{
			$id = AppContext::get_request()->get_getint('id', 0);
			if (!empty($id))
			{
				try {
					$this->item = DownloadService::get_downloadfile('WHERE download.id=:id', array('id' => $id));
				} catch (RowNotFoundException $e) {
					$error_controller = PHPBoostErrors::unexisting_page();
					DispatchManager::redirect($error_controller);
				}
			}
			else
			{
				$this->is_new_item = true;
				$this->item = new DownloadFile();
				$this->item->init_default_properties(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY));
			}
		}
		return $this->item;
	}

	private function check_authorizations()
	{
		$item = $this->get_downloadfile();

		if ($item->get_id() === null)
		{
			if (!$item->is_authorized_to_add())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		else
		{
			if (!$item->is_authorized_to_edit())
			{
				$error_controller = PHPBoostErrors::user_not_authorized();
				DispatchManager::redirect($error_controller);
			}
		}
		if (AppContext::get_current_user()->is_readonly())
		{
			$controller = PHPBoostErrors::user_in_read_only();
			DispatchManager::redirect($controller);
		}
	}

	private function save()
	{
		$item = $this->get_downloadfile();

		$item->set_title($this->form->get_value('title'));
		$item->set_rewrited_title(Url::encode_rewrite($item->get_title()));

		if (CategoriesService::get_categories_manager()->get_categories_cache()->has_categories())
			$item->set_id_category($this->form->get_value('id_category')->get_raw_value());

		$item->set_url(new Url($this->form->get_value('url')));
		$item->set_contents($this->form->get_value('contents'));
		$item->set_summary(($this->form->get_value('summary_enabled') ? $this->form->get_value('summary') : ''));
		$item->set_thumbnail(new Url($this->form->get_value('thumbnail')));
		$item->set_software_version($this->form->get_value('software_version'));

		if ($this->config->is_author_displayed())
			$item->set_author_custom_name(($this->form->get_value('author_custom_name') && $this->form->get_value('author_custom_name') !== $item->get_author_user()->get_display_name() ? $this->form->get_value('author_custom_name') : ''));

		if ($this->form->get_value('determine_file_size_automatically_enabled'))
		{
			$file_size = Url::get_url_file_size($item->get_url());
			$file_size = (empty($file_size) && $item->get_size()) ? $item->get_size() : $file_size;
		}
		else
		{
			$units = array($this->common_lang['unit.bytes'], $this->common_lang['unit.kilobytes'], $this->common_lang['unit.megabytes'], $this->common_lang['unit.gigabytes']);
			$power = array_search($this->form->get_value('file_size_unit')->get_raw_value(), $units);
			$file_size = (int)($this->form->get_value('file_size') * pow(1024, $power));
		}

		$item->set_size($file_size);

		$item->set_sources($this->form->get_value('sources'));

		if ($item->get_id() !== null && $item->get_downloads_number() > 0 && $this->form->get_value('reset_downloads_number'))
		{
			$item->set_downloads_number(0);
		}

		if (!DownloadAuthorizationsService::check_authorizations($item->get_id_category())->moderation())
		{
			$item->clean_start_and_end_date();

			if (DownloadAuthorizationsService::check_authorizations($item->get_id_category())->contribution() && !DownloadAuthorizationsService::check_authorizations($item->get_id_category())->write())
				$item->set_approbation_type(DownloadFile::NOT_APPROVAL);
		}
		else
		{

			if ($this->form->get_value('update_creation_date'))
			{
				$item->set_creation_date(new Date());
			}
			else
			{
				$item->set_creation_date($this->form->get_value('creation_date'));
			}

			$item->set_approbation_type($this->form->get_value('approbation_type')->get_raw_value());
			if ($item->get_approbation_type() == DownloadFile::APPROVAL_DATE)
			{
				$deferred_operations = $this->config->get_deferred_operations();

				$old_start_date = $item->get_start_date();
				$start_date = $this->form->get_value('start_date');
				$item->set_start_date($start_date);

				if ($old_start_date !== null && $old_start_date->get_timestamp() != $start_date->get_timestamp() && in_array($old_start_date->get_timestamp(), $deferred_operations))
				{
					$key = array_search($old_start_date->get_timestamp(), $deferred_operations);
					unset($deferred_operations[$key]);
				}

				if (!in_array($start_date->get_timestamp(), $deferred_operations))
					$deferred_operations[] = $start_date->get_timestamp();

				if ($this->form->get_value('end_date_enabled'))
				{
					$old_end_date = $item->get_end_date();
					$end_date = $this->form->get_value('end_date');
					$item->set_end_date($end_date);

					if ($old_end_date !== null && $old_end_date->get_timestamp() != $end_date->get_timestamp() && in_array($old_end_date->get_timestamp(), $deferred_operations))
					{
						$key = array_search($old_end_date->get_timestamp(), $deferred_operations);
						unset($deferred_operations[$key]);
					}

					if (!in_array($end_date->get_timestamp(), $deferred_operations))
						$deferred_operations[] = $end_date->get_timestamp();
				}
				else
				{
					$item->clean_end_date();
				}

				$this->config->set_deferred_operations($deferred_operations);
				DownloadConfig::save();
			}
			else
			{
				$item->clean_start_and_end_date();
			}
		}

		if ($this->is_new_item)
		{
			$id = DownloadService::add($item);
		}
		else
		{
			$item->set_updated_date(new Date());
			$id = $item->get_id();
			DownloadService::update($item);
		}

		$this->contribution_actions($item, $id);

		KeywordsService::get_keywords_manager()->put_relations($id, $this->form->get_value('keywords'));

		DownloadService::clear_cache();
	}

	private function contribution_actions(DownloadFile $item, $id)
	{
		if ($item->get_id() === null)
		{
			if ($this->is_contributor_member())
			{
				$contribution = new Contribution();
				$contribution->set_id_in_module($id);
				$contribution->set_description(stripslashes($this->form->get_value('contribution_description')));
				$contribution->set_entitled($item->get_title());
				$contribution->set_fixing_url(DownloadUrlBuilder::edit($id)->relative());
				$contribution->set_poster_id(AppContext::get_current_user()->get_id());
				$contribution->set_module('download');
				$contribution->set_auth(
					Authorizations::capture_and_shift_bit_auth(
						CategoriesService::get_categories_manager()->get_heritated_authorizations($item->get_id_category(), Category::MODERATION_AUTHORIZATIONS, Authorizations::AUTH_CHILD_PRIORITY),
						Category::MODERATION_AUTHORIZATIONS, Contribution::CONTRIBUTION_AUTH_BIT
					)
				);
				ContributionService::save_contribution($contribution);
			}
		}
		else
		{
			$corresponding_contributions = ContributionService::find_by_criteria('download', $id);
			if (count($corresponding_contributions) > 0)
			{
				foreach ($corresponding_contributions as $contribution)
				{
					$contribution->set_status(Event::EVENT_STATUS_PROCESSED);
					ContributionService::save_contribution($contribution);
				}
			}
		}
		$item->set_id($id);
	}

	private function redirect()
	{
		$item = $this->get_downloadfile();
		$category = $item->get_category();

		if ($this->is_new_item && $this->is_contributor_member() && !$item->is_visible())
		{
			DispatchManager::redirect(new UserContributionSuccessController());
		}
		elseif ($item->is_visible())
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(DownloadUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()), StringVars::replace_vars($this->lang['download.message.success.add'], array('title' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : DownloadUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title())), StringVars::replace_vars($this->lang['download.message.success.edit'], array('title' => $item->get_title())));
		}
		else
		{
			if ($this->is_new_item)
				AppContext::get_response()->redirect(DownloadUrlBuilder::display_pending(), StringVars::replace_vars($this->lang['download.message.success.add'], array('title' => $item->get_title())));
			else
				AppContext::get_response()->redirect(($this->form->get_value('referrer') ? $this->form->get_value('referrer') : DownloadUrlBuilder::display_pending()), StringVars::replace_vars($this->lang['download.message.success.edit'], array('title' => $item->get_title())));
		}
	}

	private function generate_response(View $view)
	{
		$item = $this->get_downloadfile();

		$location_id = $item->get_id() ? 'download-edit-'. $item->get_id() : '';

		$response = new SiteDisplayResponse($view, $location_id);
		$graphical_environment = $response->get_graphical_environment();

		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['module.title'], DownloadUrlBuilder::home());

		if ($item->get_id() === null)
		{
			$graphical_environment->set_page_title($this->lang['download.add.item'], $this->lang['module.title']);
			$breadcrumb->add($this->lang['download.add.item'], DownloadUrlBuilder::add($item->get_id_category()));
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['download.add.item']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(DownloadUrlBuilder::add($item->get_id_category()));
		}
		else
		{
			if (!AppContext::get_session()->location_id_already_exists($location_id))
				$graphical_environment->set_location_id($location_id);

			$graphical_environment->set_page_title($this->lang['download.edit.item'], $this->lang['module.title']);
			$graphical_environment->get_seo_meta_data()->set_description($this->lang['download.edit.item']);
			$graphical_environment->get_seo_meta_data()->set_canonical_url(DownloadUrlBuilder::edit($item->get_id()));

			$categories = array_reverse(CategoriesService::get_categories_manager()->get_parents($item->get_id_category(), true));
			foreach ($categories as $id => $category)
			{
				if ($category->get_id() != Category::ROOT_CATEGORY)
					$breadcrumb->add($category->get_name(), DownloadUrlBuilder::display_category($category->get_id(), $category->get_rewrited_name()));
			}
			$category = $item->get_category();
			$breadcrumb->add($item->get_title(), DownloadUrlBuilder::display($category->get_id(), $category->get_rewrited_name(), $item->get_id(), $item->get_rewrited_title()));
			$breadcrumb->add($this->lang['download.edit.item'], DownloadUrlBuilder::edit($item->get_id()));
		}

		return $response;
	}
}
?>
