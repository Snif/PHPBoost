<?php
/**
 * @package     Content
 * @subpackage  Comments\form
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2018 11 09
 * @since       PHPBoost 3.0 - 2011 09 25
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class EditCommentBuildForm extends AbstractCommentsBuildForm
{
	private $id_comment = 0;
	private $user;
	private $lang;
	private $comments_lang;
	private $comments_configuration;
	private $topic_path;

	public static function create($id_comment, $topic_path)
	{
		$instance = new self($id_comment, $topic_path);

		$instance->create_form();

		if ($instance->has_been_submited())
		{
			$instance->handle_submit();
		}

		return $instance;
	}

	public function __construct($id_comment, $topic_path)
	{
		$this->id_comment = $id_comment;
		$this->user = AppContext::get_current_user();
		$this->lang = LangLoader::get('main');
		$this->topic_path = $topic_path;
		$this->comments_lang = LangLoader::get('comments-common');
		$this->comments_configuration = CommentsConfig::load();
	}

	protected function create_form()
	{
		$form = new HTMLForm('comments', REWRITED_SCRIPT . '#comments-list');
		$fieldset = new FormFieldsetHTML('edit_comment', $this->comments_lang['comment.edit']);
		$form->add_fieldset($fieldset);

		$fieldset->add_field(new FormFieldRichTextEditor('message', $this->lang['message'], $this->get_contents(), array(
			'formatter' => $this->get_formatter(),
			'rows' => 10, 'cols' => 47, 'required' => $this->lang['require_text']),
			array((!$this->user->is_moderator() && !$this->user->is_admin() ? new FormFieldConstraintMaxLinks($this->comments_configuration->get_max_links_comment()) : ''))
		));

		$form->add_button($submit_button = new FormButtonDefaultSubmit());
		$form->add_button(new FormButtonReset());

		$this->set_form($form);
		$this->set_submit_button($submit_button);

		return $form;
	}

	protected function handle_submit()
	{
		$form = $this->get_form();
		CommentsManager::edit_comment($this->id_comment, $form->get_value('message'));
		AppContext::get_response()->redirect(CommentsUrlBuilder::comment_added($this->topic_path, $this->id_comment));
	}

	private function get_formatter()
	{
		$formatter = AppContext::get_content_formatting_service()->get_default_factory();
		$formatter->set_forbidden_tags($this->comments_configuration->get_forbidden_tags());
		return $formatter;
	}

	private function get_contents()
	{
		$comment = CommentsCache::load()->get_comment($this->id_comment);
		return $comment['message'];
	}
}
?>
