<?php
/*##################################################
 *              AbstractDisplayGraphicalEnvironment.class.php
 *                            -------------------
 *   begin                : October 06, 2009
 *   copyright            : (C) 2009 Benoit Sautel
 *   email                : ben.popeye@phpboost.com
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

 /**
 * @package {@package}
 * @desc
 * @author Benoit Sautel <ben.popeye@phpboost.com>
 */
 abstract class AbstractDisplayGraphicalEnvironment extends AbstractGraphicalEnvironment
{
	private $css_files = array();
	
	private $page_title = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function add_css_file($file_path)
	{
		$this->css_files[] = $file_path;
	}
	
	protected function get_theme_css_files_html_code()
	{
		$css_cache = new CSSCacheManager();
		$css_cache->set_files(ThemesCssFilesCache::load()->get_files_for_theme(get_utheme()));
		$css_cache->set_cache_file_location(PATH_TO_ROOT . '/cache/css/css-cache-theme-' . get_utheme() .'.css');
		$css_cache->execute();
		$html_code = '<link rel="stylesheet" href="' . $css_cache->get_cache_file_location() . 
				'" type="text/css" media="screen, print, handheld" />';
		return $html_code;
	}
	
	protected function get_modules_css_files_html_code()
	{
		$css_cache = new CSSCacheManager();
		$css_cache->set_files($this->css_files);
		$css_cache->set_cache_file_location(PATH_TO_ROOT . '/cache/css/css-cache-modules-' . get_utheme() .'.css');
		$css_cache->execute();
		$html_code = '<link rel="stylesheet" href="' . $css_cache->get_cache_file_location() . 
				'" type="text/css" media="screen, print, handheld" />';
		return $html_code;
	}

	protected function add_modules_css_files()
	{
		$css_files_cache = ModulesCssFilesCache::load();
		try
		{
			$css_files = $css_files_cache->get_files_for_theme(get_utheme());
			foreach ($css_files as $file)
			{
				$this->add_css_file($file);
			}
		}
		catch(PropertyNotFoundException $ex)
		{
		}
	}
	
	public function get_page_title()
	{
		return $this->page_title;
	}
	
	public function set_page_title($title) 
	{
		$this->page_title = $title;
		defined('TITLE') or define('TITLE', $title);
	}
}

?>