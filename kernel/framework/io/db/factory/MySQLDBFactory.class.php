<?php
/*##################################################
 *                           MySQLDBFactory.class.php
 *                            -------------------
 *   begin                : November 9, 2009
 *   copyright            : (C) 2009 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
 *
 *
 ###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author loic rouchon <loic.rouchon@phpboost.com>
 * @package io
 * @subpackage db/factory
 * @desc this factory provides the <code>DBConnection</code> and the <code>SQLQuerier</code>
 * for the right sgbd.
 */
class MySQLDBFactory implements DBMSFactory
{
	public static function new_db_connection()
	{
		return new MySQLDBConnection();
	}

	public static function new_sql_querier(DBConnection $db_connection)
	{
		return new MySQLQuerier($db_connection, self::new_query_translator());
	}

	public static function new_dbms_util(SQLQuerier $querier)
	{
		return new MySQLDBMSUtils($querier);
	}

	private static function new_query_translator()
	{
		return new MySQLQueryTranslator();
	}
}

?>