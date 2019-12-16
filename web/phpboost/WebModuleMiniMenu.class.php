<?php
/**
 * @copyright   &copy; 2005-2019 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Julien BRISWALTER <j1.seth@phpboost.com>
 * @version     PHPBoost 5.2 - last update: 2019 11 09
 * @since       PHPBoost 4.1 - 2014 08 21
*/

class WebModuleMiniMenu extends ModuleMiniMenu
{
	public function get_default_block()
	{
		return self::BLOCK_POSITION__RIGHT;
	}

	public function get_menu_id()
	{
		return 'module-mini-web';
	}

	public function get_menu_title()
	{
		return LangLoader::get_message('partners', 'common', 'web');
	}

	public function is_displayed()
	{
		return CategoriesAuthorizationsService::check_authorizations(Category::ROOT_CATEGORY, 'web')->read();
	}

	public function get_menu_content()
	{
		//Create file template
		$tpl = new FileTemplate('web/WebModuleMiniMenu.tpl');

		//Assign the lang file to the tpl
		$tpl->add_lang(LangLoader::get('common', 'web'));

		//Assign common menu variables to the tpl
		MenuService::assign_positions_conditions($tpl, $this->get_block());

		//Load module cache
		$web_cache = WebCache::load();

		$partners_weblinks = $web_cache->get_partners_weblinks();

		$tpl->put('C_PARTNERS', !empty($partners_weblinks));

		foreach ($partners_weblinks as $partner)
		{
			$partner_picture = new Url($partner['partner_picture']);
			$picture = $partner_picture->rel();

			$tpl->assign_block_vars('partners', array(
				'C_HAS_PARTNER_PICTURE' => !empty($picture),
				'NAME' => $partner['name'],
				'U_PARTNER_PICTURE' => $picture,
				'U_VISIT' => WebUrlBuilder::visit($partner['id'])->rel()
			));
		}

		return $tpl->render();
	}
}
?>
