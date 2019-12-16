<?php
/**
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2019 11 07
 * @since       PHPBoost 4.0 - 2013 11 24
 * @contributor xela <xela@phpboost.com>
*/

class MediaTreeLinks implements ModuleTreeLinksExtensionPoint
{
	public function get_actions_tree_links()
	{
		$module_id = 'media';
		
		$lang = LangLoader::get('common', $module_id);
		$tree = new ModuleTreeLinks();

		$manage_categories_link = new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), CategoriesUrlBuilder::manage_categories($module_id), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->manage_categories());
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('categories.manage', 'categories-common'), CategoriesUrlBuilder::manage_categories($module_id), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->manage_categories()));
		$manage_categories_link->add_sub_link(new ModuleLink(LangLoader::get_message('category.add', 'categories-common'), CategoriesUrlBuilder::add_category(AppContext::get_request()->get_getint('id_category', Category::ROOT_CATEGORY), $module_id), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->manage_categories()));
		$tree->add_link($manage_categories_link);

		$manage_media_link = new ModuleLink($lang['media.manage'], MediaUrlBuilder::manage(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->moderation());
		$manage_media_link->add_sub_link(new ModuleLink($lang['media.manage'], MediaUrlBuilder::manage(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->moderation()));
		$manage_media_link->add_sub_link(new ModuleLink($lang['media.actions.add'], MediaUrlBuilder::add(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->moderation()));
		$tree->add_link($manage_media_link);

		$tree->add_link(new AdminModuleLink(LangLoader::get_message('configuration', 'admin-common'), MediaUrlBuilder::configuration()));

		if (!CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->moderation())
		{
			$tree->add_link(new ModuleLink($lang['media.actions.add'], MediaUrlBuilder::add(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->contribution()));
		}

		$tree->add_link(new ModuleLink(LangLoader::get_message('module.documentation', 'admin-modules-common'), ModulesManager::get_module('media')->get_configuration()->get_documentation(), CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->write() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->contribution() || CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, $module_id, 'idcat')->moderation()));

		return $tree;
	}
}
?>
