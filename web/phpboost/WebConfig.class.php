<?php
/*##################################################
 *                               WebConfig.class.php
 *                            -------------------
 *   begin                : August 21, 2014
 *   copyright            : (C) 2014 Julien BRISWALTER
 *   email                : julienseth78@phpboost.com
 *
 *
 ###################################################
 *
 * This program is a free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

 /**
 * @author Julien BRISWALTER <julienseth78@phpboost.com>
 */

class WebConfig extends AbstractConfigData
{
	const ITEMS_NUMBER_PER_PAGE = 'items_number_per_page';
	const COLUMNS_NUMBER_PER_LINE = 'columns_number_per_line';
	const CATEGORY_DISPLAY_TYPE = 'category_display_type';
	const SORT_TYPE = 'sort_type';
	const SORT_MODE = 'sort_mode';
	const PARTNERS_NUMBER_IN_MENU = 'partners_number_in_menu';
	const COMMENTS_ENABLED = 'comments_enabled';
	const NOTATION_ENABLED = 'notation_enabled';
	const NOTATION_SCALE = 'notation_scale';
	const ROOT_CATEGORY_DESCRIPTION = 'root_category_description';
	const AUTHORIZATIONS = 'authorizations';
	
	const DISPLAY_SUMMARY = 'summary';
	const DISPLAY_ALL_CONTENT = 'all_content';
	
	const SORT_NAME = 'name';
	const SORT_CREATION_DATE = 'creation_date';
	const SORT_NOTATION = 'average_notes';
	const SORT_VIEWS = 'number_views';
	
	const ASC = 'ASC';
	const DESC = 'DESC';
	
	public function get_items_number_per_page()
	{
		return $this->get_property(self::ITEMS_NUMBER_PER_PAGE);
	}
	
	public function set_items_number_per_page($value)
	{
		$this->set_property(self::ITEMS_NUMBER_PER_PAGE, $value);
	}
	
	public function get_columns_number_per_line()
	{
		return $this->get_property(self::COLUMNS_NUMBER_PER_LINE);
	}
	
	public function set_columns_number_per_line($value)
	{
		$this->set_property(self::COLUMNS_NUMBER_PER_LINE, $value);
	}
	
	public function get_category_display_type()
	{
		return $this->get_property(self::CATEGORY_DISPLAY_TYPE);
	}
	
	public function set_category_display_type($value)
	{
		$this->set_property(self::CATEGORY_DISPLAY_TYPE, $value);
	}
	
	public function is_category_displayed_summary()
	{
		return $this->get_property(self::CATEGORY_DISPLAY_TYPE) == self::DISPLAY_SUMMARY;
	}
	
	public function get_sort_type()
	{
		return $this->get_property(self::SORT_TYPE);
	}
	
	public function set_sort_type($value)
	{
		$this->set_property(self::SORT_TYPE, $value);
	}
	
	public function get_sort_mode()
	{
		return $this->get_property(self::SORT_MODE);
	}
	
	public function set_sort_mode($value)
	{
		$this->set_property(self::SORT_MODE, $value);
	}
	
	public function get_partners_number_in_menu()
	{
		return $this->get_property(self::PARTNERS_NUMBER_IN_MENU);
	}
	
	public function set_partners_number_in_menu($value)
	{
		$this->set_property(self::PARTNERS_NUMBER_IN_MENU, $value);
	}
	
	public function enable_comments()
	{
		$this->set_property(self::COMMENTS_ENABLED, true);
	}
	
	public function disable_comments()
	{
		$this->set_property(self::COMMENTS_ENABLED, false);
	}
	
	public function are_comments_enabled()
	{
		return $this->get_property(self::COMMENTS_ENABLED);
	}
	
	public function enable_notation()
	{
		$this->set_property(self::NOTATION_ENABLED, true);
	}
	
	public function disable_notation()
	{
		$this->set_property(self::NOTATION_ENABLED, false);
	}
	
	public function is_notation_enabled()
	{
		return $this->get_property(self::NOTATION_ENABLED);
	}
	
	public function get_notation_scale()
	{
		return $this->get_property(self::NOTATION_SCALE);
	}
	
	public function set_notation_scale($value)
	{
		$this->set_property(self::NOTATION_SCALE, $value);
	}
	
	public function get_root_category_description()
	{
		return $this->get_property(self::ROOT_CATEGORY_DESCRIPTION);
	}
	
	public function set_root_category_description($value)
	{
		$this->set_property(self::ROOT_CATEGORY_DESCRIPTION, $value);
	}
	
	public function get_authorizations()
	{
		return $this->get_property(self::AUTHORIZATIONS);
	}
	
	public function set_authorizations(Array $authorizations)
	{
		$this->set_property(self::AUTHORIZATIONS, $authorizations);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get_default_values()
	{
		return array(
			self::ITEMS_NUMBER_PER_PAGE => 15,
			self::COLUMNS_NUMBER_PER_LINE => 3,
			self::CATEGORY_DISPLAY_TYPE => self::DISPLAY_SUMMARY,
			self::SORT_TYPE => self::SORT_NAME,
			self::SORT_MODE => self::ASC,
			self::PARTNERS_NUMBER_IN_MENU => 5,
			self::COMMENTS_ENABLED => true,
			self::NOTATION_ENABLED => true,
			self::NOTATION_SCALE => 5,
			self::ROOT_CATEGORY_DESCRIPTION => LangLoader::get_message('root_category_description', 'config', 'web'),
			self::AUTHORIZATIONS => array('r-1' => 1, 'r0' => 5, 'r1' => 13)
		);
	}
	
	/**
	 * Returns the configuration.
	 * @return WebConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'web', 'config');
	}
	
	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('web', self::load(), 'config');
	}
}
?>
