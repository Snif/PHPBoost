<?php
/*##################################################
 *                             Module.class.php
 *                            -------------------
 *   begin                : December 12, 2009
 *   copyright            : (C) 2009 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
 *
 *
 *###################################################
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
 *###################################################
 */

class Module
{
    private $module_id;
    private $activated;
    private $authorizations;
	private $installed_version;
	
	const ACCESS_AUTHORIZATION = 1;
	
	public function __construct($module_id, $activated = false, array $authorizations = array())
	{
        $this->module_id = $module_id;
		$this->installed_version = $this->get_configuration()->get_version();
        $this->activated = $activated;
        $this->authorizations = $authorizations;
	}

    public function get_id()
    {
        return $this->module_id;
    }

    public function is_activated()
    {
        return $this->activated;
    }

    public function get_authorizations()
    {
        return $this->authorizations;
    }
	
	public function get_installed_version()
	{
		return $this->installed_version;
	}

    public function set_activated($activated)
    {
        $this->activated = $activated;
    }

    public function set_authorizations($authorizations)
    {
        $this->authorizations = $authorizations;
    }
	
	public function set_installed_version($installed_version)
    {
        $this->installed_version = $installed_version;
    }

    /**
     * @return ModuleConfiguration
     */
    public function get_configuration()
    {
        return ModuleConfigurationManager::get($this->module_id);
    }
    
    public function check_auth()
    {
    	return AppContext::get_current_user()->check_auth($this->authorizations, self::ACCESS_AUTHORIZATION);
    }
}
?>
