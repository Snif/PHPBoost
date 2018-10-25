<?php
/*##################################################
 *                      NewsletterHomeController.class.php
 *                            -------------------
 *   begin                : March 13, 2011
 *   copyright            : (C) 2011 Kévin MASSY
 *   email                : soldier.weasel@gmail.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class NewsletterHomeController extends ModuleController
{
	private $lang;
	private $full_view;
	private $view;
	private $nbr_streams_per_page = 25;

	public function execute(HTTPRequestCustom $request)
	{
		$this->check_authorizations();
		
		$this->init();
		
		$this->build_form($request);

		return $this->generate_response($request);
	}

	private function build_form(HTTPRequestCustom $request)
	{
		$pagination = $this->get_pagination();
		
		$result = PersistenceContext::get_querier()->select('SELECT @id_stream:= id, newsletter_streams.*,
			(SELECT COUNT(*)
			FROM ' . NewsletterSetup::$newsletter_table_subscriptions . ' subscriptions
			LEFT JOIN ' . NewsletterSetup::$newsletter_table_subscribers . ' subscribers ON subscriptions.subscriber_id = subscribers.id
			LEFT JOIN ' . DB_TABLE_MEMBER . ' member ON subscribers.user_id = member.user_id
			WHERE (subscribers.mail <> \'\' OR member.email <> \'\') AND subscriptions.stream_id = @id_stream
			) AS subscribers_number
		FROM ' . NewsletterSetup::$newsletter_table_streams . ' newsletter_streams
		LIMIT :number_items_per_page OFFSET :display_from',
			array(
				'number_items_per_page' => $pagination->get_number_items_per_page(),
				'display_from' => $pagination->get_display_from()
		));
		
		while ($row = $result->fetch())
		{
			if (NewsletterAuthorizationsService::id_stream($row['id'])->read())
			{
				$this->view->assign_block_vars('streams_list', array(
					'C_VIEW_ARCHIVES' => NewsletterAuthorizationsService::id_stream($row['id'])->read_archives(),
					'C_VIEW_SUBSCRIBERS' => NewsletterAuthorizationsService::id_stream($row['id'])->read_subscribers(),
					'IMAGE' => Url::to_rel($row['image']),
					'NAME' => $row['name'],
					'DESCRIPTION' => $row['description'],
					'SUBSCRIBERS_NUMBER' => $row['subscribers_number'],
					'U_VIEW_ARCHIVES' => NewsletterUrlBuilder::archives($row['id'], $row['rewrited_name'])->absolute(),
					'U_VIEW_SUBSCRIBERS' => NewsletterUrlBuilder::subscribers($row['id'], $row['rewrited_name'])->absolute(),
				));
			}
		}
		
		$this->view->put_all(array(
			'C_SUSCRIBE' => NewsletterAuthorizationsService::check_authorizations()->subscribe(),
			'C_STREAMS' => $result->get_rows_count() != 0,
			'C_PAGINATION' => $pagination->has_several_pages(),
			'PAGINATION' => $pagination->display()
		));
		
		$result->dispose();
		
		$this->full_view->put('TEMPLATE', $this->view);
	}
	
	private function check_authorizations()
	{
		if (!NewsletterAuthorizationsService::default_authorizations()->read())
		{
			NewsletterAuthorizationsService::get_errors()->read();
		}
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'newsletter');
		$this->full_view = new FileTemplate('newsletter/NewsletterBody.tpl');
		$this->full_view->add_lang($this->lang);
		$this->view = new FileTemplate('newsletter/NewsletterHomeController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function get_pagination()
	{
		$nbr_streams = PersistenceContext::get_querier()->count(NewsletterSetup::$newsletter_table_streams);
		
		$page = AppContext::get_request()->get_getint('page', 1);
		$pagination = new ModulePagination($page, $nbr_streams, $this->nbr_streams_per_page);
		$pagination->set_url(NewsletterUrlBuilder::home('%d'));
		
		if ($pagination->current_page_is_empty() && $page > 1)
		{
			$error_controller = PHPBoostErrors::unexisting_page();
			DispatchManager::redirect($error_controller);
		}
		
		return $pagination;
	}

	private function generate_response(HTTPRequestCustom $request)
	{
		$page = $request->get_getint('page', 1);
		
		$response = new SiteDisplayResponse($this->full_view);
		$breadcrumb = $response->get_graphical_environment()->get_breadcrumb();
		$breadcrumb->add($this->lang['newsletter'], NewsletterUrlBuilder::home()->absolute());
		$breadcrumb->add($this->lang['newsletter.list_newsletters'], NewsletterUrlBuilder::home($page)->absolute());
		
		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['newsletter.list_newsletters'], $this->lang['newsletter'], $page);
		$graphical_environment->get_seo_meta_data()->set_canonical_url(NewsletterUrlBuilder::home($page));
		
		return $response;
	}
	
	public static function get_view()
	{
		$object = new self();
		$object->check_authorizations();
		$object->init();
		$object->build_form(AppContext::get_request());
		return $object->full_view;
	}
}
?>