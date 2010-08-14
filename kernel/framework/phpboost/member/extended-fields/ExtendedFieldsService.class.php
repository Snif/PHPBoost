<?php
/*##################################################
 *         ExtendedFieldsService.class.php
 *                            -------------------
 *   begin                : August 14, 2010
 *   copyright            : (C) 2010 K�vin MASSY
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
 
class ExtendedFieldsService
{
	/*
	 * This function required object ExtendedFields containing the name, field name, position, content, field type, possible values, default values, required and regex.
	 */
	public static function add(ExtendedFields $extended_field)
	{
		$name = $extended_field->get_name();
		$type_field = $extended_field->get_field_type();
		if (!empty($name) && !empty($type_field))
		{
			if (!ExtendedFieldsTableService::check_field_exist_by_field_name($extended_field)) 
			{		
				ExtendedFieldsTableService::add_extended_field($extended_field);
				
				ExtendFieldsCache::invalidate();
			}
			else
			{
				// The field are already exist
				new Exception('The field are already exist.');
			}
		}
		else
		{	
			// All fields not completed !
			new Exception('Please complete all fields!');
		}
	}
	
	/*
	 * This function required object ExtendedFields containing the id, name, field name, content, field type, possible values, default values, required and regex.
	 */
	public static function update(ExtendedFields $extended_field)
	{
		$name = $extended_field->get_name();
		$type_field = $extended_field->get_field_type();
		if (!empty($name) && !empty($type_field))
		{
			if (ExtendedFieldsTableService::check_field_exist_by_id($extended_field))
			{
				ExtendedFieldsTableService::update_extended_field($extended_field);
				
				ExtendFieldsCache::invalidate();
			}
			else
			{
				// The field are already exist
				new Exception('The field are already exist.');
			}
		}
		else
		{
			// All fields not completed !
			new Exception('Please complete all required fields!');
		}
	}
	
	/*
	 * This function required object ExtendedFields containing the id
	 */
	public static function delete(ExtendedFields $extended_field)
	{
		if (ExtendedFieldsTableService::check_field_exist_by_id($extended_field))
		{
			ExtendedFieldsTableService::delete_extended_field($extended_field);
			
			ExtendFieldsCache::invalidate();
		}
		else
		{
			// The field is not exited
			new Exception('The field is not exited !');
		}	
	}
}

?>