<?php
/*##################################################
 *                            site_map.class.php
 *                            -------------------
 *   begin                : February 3rd 2009
 *   copyright            : (C) 2009 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
 *
 *
 *
 ###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
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

//Imports of every class of this package
import('content/sitemap/module_map');
import('content/sitemap/site_map_link');
import('content/sitemap/site_map_section');
import('content/sitemap/site_map_export_config');

//For who is the site map?
/**
 * The site map will be seen by every body, only the public elements must appear
 */
define('SITE_MAP_AUTH_GUEST', false);
/**
 * The site map is for the current user. It must contain only what the user can see, but it can be private.
 */
define('SITE_MAP_AUTH_USER', true);

//In which context will be used the site map?
/**
 * It will be a page of the site containing the site map
 */
define('SITE_MAP_USER_MODE', true);
/**
 * It will be for the search engines (sitemap.xml), all the pages which don't need to be present in the search engines results
 * can be forgotten in that case.
 */
define('SITE_MAP_SEARCH_ENGINE_MODE', false);

//Actualization frequencies
define('SITE_MAP_FREQ_ALWAYS', 'always');
define('SITE_MAP_FREQ_HOURLY', 'hourly');
define('SITE_MAP_FREQ_DAILY', 'daily');
define('SITE_MAP_FREQ_WEEKLY', 'weekly');
define('SITE_MAP_FREQ_MONTHLY', 'monthly');
define('SITE_MAP_FREQ_YEARLY', 'yearly');
define('SITE_MAP_FREQ_NEVER', 'never');
define('SITE_MAP_FREQ_DEFAULT', SITE_MAP_FREQ_MONTHLY);

//Link priority
define('SITE_MAP_PRIORITY_MAX', '1');
define('SITE_MAP_PRIORITY_HIGH', '0.75');
define('SITE_MAP_PRIORITY_AVERAGE', '0.5');
define('SITE_MAP_PRIORITY_LOW', '0.25');
define('SITE_MAP_PRIORITY_MIN', '0');

/**
 * @package content
 * @subpackage sitemap
 * @author Beno�t Sautel <ben.popeye@phpboost.com>
 * @desc Describes the map of the site. Can be exported according to any text form by using a template configuration.
 * A site map contains some links, some link sections and some module maps (which also contain links and sections).
 */
class SiteMap
{
    /**
     * @desc Builds a SiteMap object with its elements
     * @param SiteMapElement[] $elements List of the elements it contains
     */
    function SiteMap($site_name = '', $elements = null)
    {
        if (is_array($elements))
        {
            $this->elements = $elements;
        }
        $this->set_site_name($site_name);
    }

    /**
     * @desc Returns the name of the site 
     * @return string name
     */
    function get_site_name()
    {
        return $this->site_name;
    }
    
    /**
     * @desc Sets the name of the site. The default value is the name of the site taken from the site configuration. 
     * @param string $site_name name of the site
     */
    function set_site_name($site_name)
    {
        global $CONFIG;
        if (!empty($site_name))
        {
            $this->site_name = $CONFIG['site_name'];
        }
        elseif (empty($this->site_name))
        {
            $this->site_name = $CONFIG['site_name'];
        }
    }
    
    /**
     * @desc Adds an element to the elements list of the SiteMap
     * @param SiteMapElement $element The element to add
     */
    function add($element)
    {
        $this->elements[] = $element;
    }

    /**
     * @desc Exports a SiteMap. You will be able to use the following variables into the templates used to export:
     * <ul>
     * 	<li>C_SITE_MAP which is a condition indicating if it's a site map (useful if you want to use a sigle template
     * for the whole export configuration)</li>
     * 	<li>SITE_NAME which contains the name of the site</li>
     * 	<li>A loop "element" in which the code of each element is in the variable CODE</li>
     * </ul>
     * @param SiteMapExportConfig $export_config Export configuration
     * @return string The exported code of the SiteMap
     */
    function export(&$export_config)
    {
        //We get the stream in which we are going to write
        $template = $export_config->get_site_map_stream();

        $template->assign_vars(array(
		    'C_SITE_MAP' => true,
            'SITE_NAME' => htmlspecialchars($this->site_name, ENT_QUOTES)
        ));

        //Let's export all the element it contains
        foreach ($this->elements as $element)
        {
            $template->assign_block_vars('element', array(
				'CODE' => $element->export($export_config)
            ));
        }
        return $template->parse(TEMPLATE_STRING_MODE);
    }

    /**
     * @desc Adds to the site map all maps of the installed modules
     */
    function build_modules_maps()
    {
        import('modules/modules_discovery_service');
         
        $Modules = new ModulesDiscoveryService();
        foreach ($Modules->get_available_modules('get_module_map') as $module)
        {
            $module_map = $module->get_module_map(SITE_MAP_AUTH_USER);
            $this->add($module_map);
        }
    }

    /**
     * @desc Adds to the site map all the kernel links.
     */
    function build_kernel_map($mode = SITE_MAP_USER_MODE, $auth_mode = SITE_MAP_AUTH_GUEST)
    {
        global $CONFIG, $LANG, $User;
         
        //We consider the kernel as a module
        $kernel_map = new ModuleMap(new SiteMapLink($LANG['home'], new Url($CONFIG['start_page'])));
         
        //The site description
        $kernel_map->set_description(nl2br($CONFIG['site_desc']));
         
        //All the links which not need to be present in the search engine results.
        if ($mode == SITE_MAP_USER_MODE)
        {
            $kernel_map->add(new SiteMapLink($LANG['members_list'], new Url('/member/member.php')));
             
            //Member space
            if ($auth_mode == SITE_MAP_AUTH_USER && $User->check_level(MEMBER_LEVEL))
            {
                //We create a section for that
                $member_space_section = new SiteMapSection(new SiteMapLink($LANG['my_private_profile'],
                new Url('/member/' . url('member.php?id=' . $User->get_id() . '&amp;view=1', 'member-' . $User->get_id() . '.php?view=1'))));
                 
                //Profile edition
                $member_space_section->add(new SiteMapLink($LANG['profile_edition'],
                    new Url('/member/' . url('member.php?id=' . $User->get_id() . '&amp;edit=1', 'member-' . $User->get_id() . '.php?edit=1'))));
                 
                //Private messaging
                $member_space_section->add(new SiteMapLink($LANG['private_messaging'],
                    new Url('/member/' . url('pm.php?pm=' . $User->get_id(), 'pm-' . $User->get_id() . '.php'))));
                 
                //Contribution panel
                $member_space_section->add(new SiteMapLink($LANG['contribution_panel'], new Url('/member/contribution_panel.php')));
                 
                //Administration panel
                if ($User->check_level(ADMIN_LEVEL))
                {
                    $member_space_section->add(new SiteMapLink($LANG['admin_panel'], new Url('/admin/admin_index.php')));
                }
                 
                //We add it to the kernel map
                $kernel_map->add($member_space_section);
            }
        }
         
        //The kernel map is added to the site map
        $this->add($kernel_map);
    }

    ## Private elements ##
    /**
    * @var SiteMapElement[] Elements contained by the site map
    */
    var $elements = array();
    /**
     * @var string name of the site
     */
    var $site_name = '';
}

?>
