<?php
/**
 * This factory provides the <code>DBConnection</code> and the <code>SQLQuerier</code>
 * for the right sgbd.
 * @package     IO
 * @subpackage  DB\factory
 * @copyright   &copy; 2005-2020 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Loic ROUCHON <horn@phpboost.com>
 * @version     PHPBoost 6.0 - last update: 2014 12 22
 * @since       PHPBoost 3.0 - 2009 11 10
*/

class PDOMySQLDBFactory implements DBMSFactory
{
	public function new_db_connection()
	{
		return new PDODBConnection();
	}

	public function new_sql_querier(DBConnection $db_connection)
	{
		return new PDOQuerier($db_connection, $this->new_query_translator());
	}

	public function new_dbms_util(SQLQuerier $querier)
	{
		return new MySQLDBMSUtils($querier);
	}

	private function new_query_translator()
	{
		return new MySQLQueryTranslator();
	}
}
?>
