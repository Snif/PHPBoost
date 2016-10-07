<?php
/*##################################################
 *		                    SearchConfig.class.php
 *                            -------------------
 *   begin                : April 10, 2010
 *   copyright            : (C) 2010 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
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
 * @desc This class represents the search module's configuration.
 * @author Loic Rouchon <loic.rouchon@phpboost.com>
 */
class SearchConfig extends AbstractConfigData
{
	const weightings = 'weightings';
	const nb_results_per_page = '_nb_results_per_page';
	const cache_lifetime = 'cache_lifetime';
	const cache_max_uses = 'cache_max_uses';
	const unauthorized_providers = 'unauthorized_providers';
	
	const AUTHORIZATIONS = 'authorizations';

	/**
	 * Returns the search providers weighting
	 * @return SearchWeightings
	 */
	public function get_weightings()
	{
		return SearchWeightings::load($this->get_property(self::weightings));
	}

	/**
	 * Sets the search providers weighting
	 * @param SearchWeightings
	 */
	public function set_weightings(SearchWeightings $search_weightings)
	{
		$this->set_property(self::weightings, $search_weightings->get_weightings());
	}

	/**
	 * Returns all the search providers weighting sorted by localized name
	 * @return string[] The sorted search providers weighting
	 */
	public function get_weightings_sorted_by_localized_name()
	{
		$modules_weighting = self::get_weightings()->get_modules_weighting();
		$weightings_sorted_by_localized_name = array();
		
		foreach (ModulesManager::get_installed_modules_map_sorted_by_localized_name() as $id => $module)
		{
			if (in_array($module->get_id(), array_keys($modules_weighting)))
				$weightings_sorted_by_localized_name[$module->get_id()] = $modules_weighting[$module->get_id()];
		}
		
		return $weightings_sorted_by_localized_name;
	}

	/**
	 * Returns the number of result per page to display
	 * @return int the number of result per page to display
	 */
	public function get_nb_results_per_page()
	{
		return $this->get_property(self::nb_results_per_page);
	}

	/**
	 * Sets the number of result per page to display
	 * @param int $nb_results_per_page the number of result per page to display
	 */
	public function set_nb_results_per_page($nb_results_per_page)
	{
		$this->set_property(self::nb_results_per_page, $nb_results_per_page);
	}

	/**
	 * Returns the cache lifetime in minutes
	 * @return int the cache lifetime in minutes
	 */
	public function get_cache_lifetime()
	{
		return $this->get_property(self::cache_lifetime);
	}

	/**
	 * Sets the cache lifetime in minutes
	 * @param int $cache_lifetime the cache lifetime in minutes
	 */
	public function set_cache_lifetime($cache_lifetime)
	{
		$this->set_property(self::cache_lifetime, $cache_lifetime);
	}

	/**
	 * Returns the cache lifetime in minutes
	 * @return int the cache lifetime in minutes
	 */
	public function get_cache_max_uses()
	{
		return $this->get_property(self::cache_max_uses);
	}

	/**
	 * Sets the cache max uses
	 * @param int $cache_max_uses the cache max uses
	 */
	public function set_cache_max_uses($cache_max_uses)
	{
		$this->set_property(self::cache_max_uses, $cache_max_uses);
	}

	/**
	 * Returns the unauthorized search providers ids
	 * @return string[] The unauthorized search providers ids
	 */
	public function get_unauthorized_providers()
	{
		return $this->get_property(self::unauthorized_providers);
	}

	/**
	 * Sets the unauthorized search providers
	 * @param string[] $unauthorized_providers The unauthorized search providers ids
	 */
	public function set_unauthorized_providers(array $unauthorized_providers)
	{
		$this->set_property(self::unauthorized_providers, $unauthorized_providers);
	}

	/**
	 * Returns all the unauthorized search providers ids (unauthorized in search config and module not authorized for reading)
	 * @return string[] The unauthorized search providers ids
	 */
	public function get_all_unauthorized_providers()
	{
		$modules_without_read_authorization[] = array();
		
		foreach (ModulesManager::get_installed_modules_map_sorted_by_localized_name() as $id => $module)
		{
			$authorizations_class = ucfirst($module->get_id()) . 'AuthorizationsService';
			if (class_exists($authorizations_class) && method_exists($authorizations_class, 'check_authorizations') && method_exists($authorizations_class, 'read') && !$authorizations_class::check_authorizations()->read())
				$modules_without_read_authorization[] = $module->get_id();
		}
		
		return array_merge($this->get_property(self::unauthorized_providers), $modules_without_read_authorization);
	}
	
	/**
	 * Returns the authorizations of the module
	 * @return string[] The authorizations
	 */
	public function get_authorizations()
	{
		return $this->get_property(self::AUTHORIZATIONS);
	}

	/**
	 * Sets the authorizations of the module
	 * @param string[] $array The authorizations
	 */
	public function set_authorizations(Array $array)
	{
		$this->set_property(self::AUTHORIZATIONS, $array);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_values()
	{
		return array(
			self::weightings => array(),
			self::nb_results_per_page => 15,
			self::cache_lifetime => 30,
			self::cache_max_uses => 200,
			self::unauthorized_providers => array(),
			self::AUTHORIZATIONS => array('r-1' => 1, 'r0' => 1, 'r1' => 1)
		);
	}

	/**
	 * Returns the configuration.
	 * @return SearchModuleConfig
	 */
	public static function load()
	{
		return ConfigManager::load(__CLASS__, 'search', 'config');
	}

	/**
	 * Saves the configuration in the database. Has it become persistent.
	 */
	public static function save()
	{
		ConfigManager::save('search', self::load(), 'config');
	}
}
?>
