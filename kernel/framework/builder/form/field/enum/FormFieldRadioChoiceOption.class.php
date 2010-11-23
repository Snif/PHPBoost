<?php
/*##################################################
 *                        FormFieldRadioOption.class.php
 *                            -------------------
 *   begin                : May 01, 2009
 *   copyright            : (C) 2009 Viarre R�gis
 *   email                : crowkait@phpboost.com
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
 * @author R�gis Viarre <crowkait@phpboost.com>
 * @desc This class manage radio input field options.
 * @package {@package}
 */
class FormFieldRadioChoiceOption extends AbstractFormFieldEnumOption
{
	public function __construct($label, $raw_value)
	{
		parent::__construct($label, $raw_value);
	}

	/**
	 * @return string The html code for the radio input.
	 */
	public function display()
	{
		$tpl_src = '<label><input type="radio" name="${escape(NAME)}" value="${escape(VALUE)}" # IF C_CHECKED # checked="checked" # ENDIF #/> {LABEL}</label>';
		
		$tpl = new StringTemplate($tpl_src);
		
		$tpl->put_all(array(
			'NAME' => $this->get_field_id(),
			'VALUE' => $this->get_raw_value(),
			'C_CHECKED' => $this->is_active(),
			'LABEL' => $this->get_label()
		));
		
		return $tpl;
	}
}

?>