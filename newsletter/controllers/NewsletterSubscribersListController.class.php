<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Kevin MASSY <reidlos@phpboost.com>
 * @version     PHPBoost 5.3 - last update: 2020 02 17
 * @since       PHPBoost 3.0 - 2011 03 11
 * @contributor Julien BRISWALTER <j1.seth@phpboost.com>
*/

class NewsletterSubscribersListController extends ModuleController
{
	private $lang;
	private $view;
	private $stream;

	private $elements_number = 0;
	private $ids = array();

	public function execute(HTTPRequestCustom $request)
	{
		$this->stream = NewsletterStreamsCache::load()->get_stream($request->get_int('id_stream', 0));

		if ($this->stream->get_id() == 0)
		{
			AppContext::get_response()->redirect(NewsletterUrlBuilder::home());
		}

		$this->check_authorizations();

		$this->init();

		$current_page = $this->build_table();

		$this->execute_multiple_delete_if_needed($request);

		return $this->generate_response($current_page);
	}

	private function check_authorizations()
	{
		if (!NewsletterAuthorizationsService::id_stream($this->stream->get_id())->read_subscribers())
		{
			NewsletterAuthorizationsService::get_errors()->read_subscribers();
		}
	}

	private function init()
	{
		$this->lang = LangLoader::get('common', 'newsletter');
		$this->view = new StringTemplate('# INCLUDE table #');
	}

	private function build_table()
	{
		$moderation_authorization = NewsletterAuthorizationsService::id_stream($this->stream->get_id())->moderation_subscribers();

		$columns = array(
			new HTMLTableColumn($this->lang['subscribers.pseudo'], 'name'),
			new HTMLTableColumn($this->lang['subscribers.mail'], 'user_mail')
		);

		if ($moderation_authorization)
			$columns[] = new HTMLTableColumn(LangLoader::get_message('actions', 'admin-common'), '', array('sr-only' => true));

		$table_model = new SQLHTMLTableModel(NewsletterSetup::$newsletter_table_subscribers, 'table', $columns, new HTMLTableSortingRule('name', HTMLTableSortingRule::ASC));

		$table_model->add_permanent_filter('id IN (' . implode(',', array_keys(NewsletterService::list_subscribers_by_stream($this->stream->get_id()))) . ')');

		$table = new HTMLTable($table_model);

		$results = array();
		$result = $table_model->get_sql_results('subscribers LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON subscribers.user_id = member.user_id', array('*', 'COALESCE(NULLIF(subscribers.mail, \'\'), member.email) AS user_mail', 'COALESCE(NULLIF(member.display_name, \'\'), "' . LangLoader::get_message('visitor', 'user-common') . '") AS name'));
		foreach ($result as $row)
		{
			if ($row['user_mail'])
			{
				$user = new User();
				if (!empty($row['user_id']))
					$user->set_properties($row);
				else
					$user->init_visitor_user();
			
				$this->elements_number++;
				$this->ids[$this->elements_number] = $row['id'];
				
				$user_group_color = User::get_group_color($user->get_groups(), $user->get_level(), true);
				$author = $user->get_id() !== User::VISITOR_LEVEL ? new LinkHTMLElement(UserUrlBuilder::profile($user->get_id()), $user->get_display_name(), (!empty($user_group_color) ? array('style' => 'color: ' . $user_group_color) : array()), UserService::get_level_class($user->get_level())) : $row['name'];

				$table_row = array(
					new HTMLTableRowCell($author),
					new HTMLTableRowCell($row['user_mail'])
				);

				if ($moderation_authorization)
				{
					$edit_link = ($row['user_id'] == User::VISITOR_LEVEL) ? new EditLinkHTMLElement(NewsletterUrlBuilder::edit_subscriber($row['id'])) : false;
					$delete_link = new DeleteLinkHTMLElement(NewsletterUrlBuilder::delete_subscriber($row['id'], $this->stream->get_id()));
					$table_row[] = new HTMLTableRowCell(($edit_link ? $edit_link->display() : '') . $delete_link->display());
				}

				$results[] = new HTMLTableRow($table_row);
			}
		}
		$table->set_rows($table_model->get_number_of_matching_rows(), $results);

		$this->view->put('table', $table->display());

		return $table->get_page_number();
	}

	private function execute_multiple_delete_if_needed(HTTPRequestCustom $request)
	{
		if ($request->get_string('delete-selected-elements', false))
		{
			for ($i = 1 ; $i <= $this->elements_number ; $i++)
			{
				if ($request->get_value('delete-checkbox-' . $i, 'off') == 'on')
				{
					if (isset($this->ids[$i]) && NewsletterAuthorizationsService::id_stream($this->stream->get_id())->moderation_subscribers())
					{
						$parameters = array(
							'id' => $this->ids[$i],
							'id_stream' => $this->stream->get_id()
						);
						PersistenceContext::get_querier()->delete(NewsletterSetup::$newsletter_table_subscriptions, 'WHERE subscriber_id = :id AND stream_id = :id_stream', $parameters);

						if (PersistenceContext::get_querier()->count(NewsletterSetup::$newsletter_table_subscriptions, 'WHERE subscriber_id = :id', $parameters) == 0)
						{
							PersistenceContext::get_querier()->delete(NewsletterSetup::$newsletter_table_subscribers, 'WHERE id = :id', $parameters);
						}
					}
				}
			}

			NewsletterStreamsCache::invalidate();

			AppContext::get_response()->redirect(NewsletterUrlBuilder::subscribers($this->stream->get_id(), $this->stream->get_rewrited_name()), LangLoader::get_message('process.success', 'status-messages-common'));
		}
	}

	private function generate_response($page = 1)
	{
		$body_view = new FileTemplate('newsletter/NewsletterBody.tpl');
		$body_view->add_lang($this->lang);
		$body_view->put('TEMPLATE', $this->view);
		$response = new SiteDisplayResponse($body_view);
		
		$graphical_environment = $response->get_graphical_environment();
		
		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['newsletter'], NewsletterUrlBuilder::home());
		$page_name = $this->lang['newsletter.subscribers'] . ' : ' . $this->stream->get_name();
		$breadcrumb->add($page_name, NewsletterUrlBuilder::subscribers($this->stream->get_id(), $this->stream->get_rewrited_name()));

		$graphical_environment->set_page_title($page_name, $this->lang['newsletter'], $page);
		$graphical_environment->get_seo_meta_data()->set_description(StringVars::replace_vars($this->lang['newsletter.seo.suscribers.list'], array('name' => $this->stream->get_name())), $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(NewsletterUrlBuilder::subscribers($this->stream->get_id(), $this->stream->get_rewrited_name()));

		return $response;
	}
}
?>
