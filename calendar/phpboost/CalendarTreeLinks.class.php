<?php
/**
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2020 12 20
 * @since       PHPBoost 4.0 - 2013 11 26
 * @contributor xela <xela@phpboost.com>
 * @contributor Sebastien LARTIGUE <babsolune@phpboost.com>
*/

class CalendarTreeLinks extends DefaultTreeLinks
{
	public function __construct()
	{
		parent::__construct('calendar');
	}

	protected function get_add_item_url()
	{
		$requested_date = $this->get_requested_date();

		return CalendarUrlBuilder::u_add_item($requested_date['year'], $requested_date['month'], $requested_date['day']);
	}

	protected function get_module_additional_items_actions_tree_links(&$tree)
	{
		$requested_date = $this->get_requested_date();
		$module_id = 'calendar';
		$lang = LangLoader::get('common', $module_id);

		$tree->add_link(new ModuleLink($lang['my.items'], CalendarUrlBuilder::u_member_items(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->contribution() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id)->moderation()));
		$tree->add_link(new ModuleLink($lang['calendar.events.list'], CalendarUrlBuilder::u_items_list($requested_date['year'], $requested_date['month'], $requested_date['day']), $this->get_authorizations()->read()));
	}

	private function get_requested_date()
	{
		$request = AppContext::get_request();
		return array(
			'year'  => $request->get_getint('year', date('Y')),
			'month' => $request->get_getint('month', date('n')),
			'day'   => $request->get_getint('day', date('j'))
		);
	}
}
?>
