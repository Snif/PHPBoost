<?php
/*##################################################
 *                               admin_download_management.php
 *                            -------------------
 *   begin                : July 10, 2005
 *   copyright          : (C) 2005 Viarre R�gis
 *   email                : crowkait@phpboost.com
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

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

require_once('../includes/admin_begin.php');
load_module_lang('download', $CONFIG['lang']); //Chargement de la langue du module.
define('TITLE', $LANG['administration']);
require_once('../includes/admin_header.php');

//On recup�re les variables.
$id = isset($_GET['id']) ? numeric($_GET['id']) : '' ;
$id_post = isset($_POST['id']) ? numeric($_POST['id']) : '' ;
$del = !empty($_GET['delete']) ? true : false;

if( !empty($id) && !$del )
{
	$template->set_filenames(array(
		'admin_download_management' => '../templates/' . $CONFIG['theme'] . '/download/admin_download_management.tpl'
	 ));

	$row = $sql->query_array('download', '*', "WHERE id = '" . $id . "'", __LINE__, __FILE__);
	
	$idcat = $row['idcat'];

	$template->assign_vars(array(
		'TITLE' => $row['title'],
		'COMPT' => !empty($row['compt']) ? $row['compt'] : 0,
		'USER_ID' => $row['user_id'],
		'CONTENTS' => unparse($row['contents']),
		'URL' => $row['url'],
		'SIZE' => $row['size'],
		'UNIT_SIZE' => $LANG['unit_megabytes'],
		'DATE' => gmdate_format('date_format_short', $row['timestamp']),
		'L_REQUIRE_DESC' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_REQUIRE_URL' => $LANG['require_url'],
		'L_REQUIRE_CAT' => $LANG['require_cat'],
		'L_EDIT_FILE' => $LANG['edit_file'],
		'L_YES' => $LANG['yes'],
		'L_NO' => $LANG['no'],
		'L_DOWNLOAD_DATE' => $LANG['download_date'],
		'L_RELEASE_DATE' => $LANG['release_date'],
		'L_IMMEDIATE' => $LANG['immediate'],
		'L_UNAPROB' => $LANG['unaprob'],
		'L_UNTIL' => $LANG['until'],
		'L_DESC' => $LANG['description'],
		'L_DOWNLOAD' => $LANG['download'],
		'L_SIZE' => $LANG['size'],
		'L_URL' => $LANG['url'],
		'L_TITLE' => $LANG['title'],
		'L_CATEGORY' => $LANG['category'],
		'L_REQUIRE' => $LANG['require'],
		'L_DOWNLOAD_ADD' => $LANG['download_add'],
		'L_DOWNLOAD_MANAGEMENT' => $LANG['download_management'],
		'L_DOWNLOAD_CAT' => $LANG['cat_management'],
		'L_DOWNLOAD_CONFIG' => $LANG['download_config'],
		'L_UPDATE' => $LANG['update'],
		'L_RESET' => $LANG['reset'],
		'L_PREVIEW' => $LANG['preview']
	));
	
	$template->assign_block_vars('download', array(
		'TITLE' => $row['title'],
		'IDURL' => $row['id'],
		'CONTENTS' => unparse($row['contents']),
		'CURRENT_DATE' => gmdate_format('date_format_short', $row['timestamp']),
		'DAY_RELEASE_S' => !empty($row['start']) ? gmdate_format('d', $row['start']) : '',
		'MONTH_RELEASE_S' => !empty($row['start']) ? gmdate_format('m', $row['start']) : '',
		'YEAR_RELEASE_S' => !empty($row['start']) ? gmdate_format('Y', $row['start']) : '',
		'DAY_RELEASE_E' => !empty($row['end']) ? gmdate_format('d', $row['end']) : '',
		'MONTH_RELEASE_E' => !empty($row['end']) ? gmdate_format('m', $row['end']) : '',
		'YEAR_RELEASE_E' => !empty($row['end']) ? gmdate_format('Y', $row['end']) : '',
		'DAY_DATE' => !empty($row['timestamp']) ? gmdate_format('d', $row['timestamp']) : '',
		'MONTH_DATE' => !empty($row['timestamp']) ? gmdate_format('m', $row['timestamp']) : '',
		'YEAR_DATE' => !empty($row['timestamp']) ? gmdate_format('Y', $row['timestamp']) : '',
		'USER_ID' => $row['user_id'],
		'VISIBLE_WAITING' => (($row['visible'] == 2 || !empty($row['end'])) ? 'checked="checked"' : ''),
		'VISIBLE_ENABLED' => (($row['visible'] == 1 && empty($row['end'])) ? 'checked="checked"' : ''),
		'VISIBLE_UNAPROB' => (($row['visible'] == 0) ? 'checked="checked"' : ''),
		'START' => ((!empty($row['start'])) ? gmdate_format('date_format_short', $row['start']) : ''),
		'END' => ((!empty($row['end'])) ? gmdate_format('date_format_short', $row['end']) : ''),
		'HOUR' => gmdate_format('H', $row['timestamp']),
		'MIN' => gmdate_format('i', $row['timestamp']),
		'DATE' => gmdate_format('date_format_short', $row['timestamp'])	
	));
	
	//Cat�gories.		
	$i = 0;
	$result = $sql->query_while("SELECT id, name FROM ".PREFIX."download_cat", __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$selected = ($row['id'] == $idcat) ? 'selected="selected"' : '';
		$template->assign_block_vars('download.select', array(
			'CAT' => '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>'
		));
		$i++;
	}	
	$sql->close($result);
	
	//Gestion erreur.
	$get_error = !empty($_GET['error']) ? securit($_GET['error']) : '';
	if( $get_error == 'incomplete' )
		$errorh->error_handler($LANG['e_incomplete'], E_USER_NOTICE, NO_LINE_ERROR, NO_FILE_ERROR, 'download.');
	elseif( $i == 0 ) //Aucune cat�gorie => alerte.	 
		$errorh->error_handler($LANG['require_cat_create'], E_USER_WARNING, NO_LINE_ERROR, NO_FILE_ERROR, 'download.');	
		
	include_once('../includes/bbcode.php');

	$template->pparse('admin_download_management'); 
}	
elseif( !empty($_POST['previs']) && !empty($id_post) )
{
	$template->set_filenames(array(
		'admin_download_management' => '../templates/' . $CONFIG['theme'] . '/download/admin_download_management.tpl'
	 ));
	 
	$title = !empty($_POST['title']) ? trim($_POST['title']) : '';
	$compt = isset($_POST['compt']) ? numeric($_POST['compt']) : 0;
	$contents = !empty($_POST['contents']) ? trim($_POST['contents']) : '';
	$user_id = !empty($_POST['user_id']) ? numeric($_POST['user_id']) : 0;
	$url = !empty($_POST['url']) ? trim($_POST['url']) : '';
	$size = !empty($_POST['size']) ? numeric($_POST['size'], 'float') : 0;
	$idcat = !empty($_POST['idcat']) ? numeric($_POST['idcat']) : 0;
	$current_date = !empty($_POST['current_date']) ? trim($_POST['current_date']) : '';
	$start = !empty($_POST['start']) ? trim($_POST['start']) : 0;
	$end = !empty($_POST['end']) ? trim($_POST['end']) : 0;
	$hour = !empty($_POST['hour']) ? trim($_POST['hour']) : 0;
	$min = !empty($_POST['min']) ? trim($_POST['min']) : 0;	
	$get_visible = !empty($_POST['visible']) ? numeric($_POST['visible']) : 0;
	
	$cat = $sql->query("SELECT name FROM ".PREFIX."download_cat WHERE id = '" . $idcat . "'", __LINE__, __FILE__);
		
	$start_timestamp = strtotimestamp($start, $LANG['date_format_short']);
	$end_timestamp = strtotimestamp($end, $LANG['date_format_short']);
	$current_date_timestamp = strtotimestamp($current_date, $LANG['date_format_short']);
	
	$visible = 1;		
	if( $get_visible == 2 )
	{		
		if( $start_timestamp > time() )
			$visible = 2;
		else
			$start = '';
	
		if( $end_timestamp > time() && $end_timestamp > $start_timestamp )
			$visible = 2;
		else
			$end = '';
	}
	else
	{
		$start = '';
		$end = '';
	}
	
	$template->assign_block_vars('download', array(
		'IDURL' => $id_post,
		'TITLE' => stripslashes($title),
		'CONTENTS' => stripslashes($contents),
		'USER_ID' => $user_id,
		'CURRENT_DATE' => $current_date,
		'START' => ((!empty($start) && $visible == 2) ? $start : ''),
		'END' => ((!empty($end) && $visible == 2) ? $end : ''),
		'HOUR' => $hour,
		'MIN' => $min,
		'DAY_RELEASE_S' => !empty($start_timestamp) ? gmdate_format('d', $start_timestamp) : '',
		'MONTH_RELEASE_S' => !empty($start_timestamp) ? gmdate_format('m', $start_timestamp) : '',
		'YEAR_RELEASE_S' => !empty($start_timestamp) ? gmdate_format('Y', $start_timestamp) : '',
		'DAY_RELEASE_E' => !empty($end_timestamp) ? gmdate_format('d', $end_timestamp) : '',
		'MONTH_RELEASE_E' => !empty($end_timestamp) ? gmdate_format('m', $end_timestamp) : '',
		'YEAR_RELEASE_E' => !empty($end_timestamp) ? gmdate_format('Y', $end_timestamp) : '',
		'DAY_DATE' => !empty($current_date_timestamp) ? gmdate_format('d', $current_date_timestamp) : '',
		'MONTH_DATE' => !empty($current_date_timestamp) ? gmdate_format('m', $current_date_timestamp) : '',
		'YEAR_DATE' => !empty($current_date_timestamp) ? gmdate_format('Y', $current_date_timestamp) : '',
		'VISIBLE_WAITING' => (($visible == 2) ? 'checked="checked"' : ''),
		'VISIBLE_ENABLED' => (($visible == 1) ? 'checked="checked"' : ''),
		'VISIBLE_UNAPROB' => (($visible == 0) ? 'checked="checked"' : '')
	));
	
	$pseudo = $sql->query("SELECT login FROM ".PREFIX."member WHERE user_id = '" . $user_id . "'", __LINE__, __FILE__);
	//Pr�visualisation
	$template->assign_block_vars('download.preview', array(
		'USER_ID' => $user_id,
		'TITLE' => stripslashes($title),
		'CONTENTS' => second_parse(stripslashes(parse($contents))),
		'PSEUDO' => $pseudo,
		'DATE' => gmdate_format('date_format_short'),
		'IDURL' => $id_post,
		'IDCAT' => $idcat,
		'CAT' => $cat,
		'COMPT' => $compt,
		'MODULE_DATA_PATH' => $template->module_data_path('download')
	));
	
	$template->assign_vars(array(
		'LANG' => $CONFIG['lang'],
		'ID' => $id,
		'TITLE' => stripslashes($title),
		'CONTENTS' => stripslashes($contents),
		'URL' => stripslashes($url),
		'SIZE' => $size,
		'UNIT_SIZE' => $LANG['unit_megabytes'],
		'COMPT' => $compt,
		'L_REQUIRE_DESC' => $LANG['require_text'],
		'L_REQUIRE_TITLE' => $LANG['require_title'],
		'L_REQUIRE_URL' => $LANG['require_url'],
		'L_REQUIRE_CAT' => $LANG['require_cat'],
		'L_EDIT_FILE' => $LANG['edit_file'],
		'L_DESC' => $LANG['description'],
		'L_DATE' => $LANG['date'],
		'L_COM' => $LANG['com'],
		'L_VIEWS' => $LANG['views'],
		'L_NOTE' => $LANG['note'],
		'L_CATEGORY' => $LANG['categorie'],
		'L_DOWNLOAD_ADD' => $LANG['download_add'],
		'L_DOWNLOAD_MANAGEMENT' => $LANG['download_management'],
		'L_DOWNLOAD_CAT' => $LANG['cat_management'],
		'L_DOWNLOAD_CONFIG' => $LANG['download_config'],
		'L_YES' => $LANG['yes'],
		'L_NO' => $LANG['no'],
		'L_DOWNLOAD_DATE' => $LANG['download_date'],
		'L_RELEASE_DATE' => $LANG['release_date'],
		'L_IMMEDIATE' => $LANG['immediate'],
		'L_UNAPROB' => $LANG['unaprob'],
		'L_UNTIL' => $LANG['until'],
		'L_DESC' => $LANG['description'],
		'L_DOWNLOAD' => $LANG['download'],
		'L_SIZE' => $LANG['size'],
		'L_URL' => $LANG['url'],
		'L_TITLE' => $LANG['title'],
		'L_CATEGORY' => $LANG['category'],
		'L_REQUIRE' => $LANG['require'],
		'L_UPDATE' => $LANG['update'],
		'L_RESET' => $LANG['reset'],
		'L_PREVIEW' => $LANG['preview']
	));
	
	//Cat�gories.
	$i = 0;	
	$result = $sql->query_while("SELECT id, name FROM ".PREFIX."download_cat", __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		$selected = ($row['id'] == $idcat) ? 'selected="selected"' : '';
		$template->assign_block_vars('download.select', array(
			'CAT' => '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>'
		));
		$i++;
	}
	$sql->close($result);
	
	if( $i == 0 ) //Aucune cat�gorie => alerte.	 
		$errorh->error_handler($LANG['require_cat_create'], E_USER_WARNING, NO_LINE_ERROR, NO_FILE_ERROR, 'download.');
		
	include_once('../includes/bbcode.php');

	$template->pparse('admin_download_management'); 
}	
elseif( !empty($_POST['valid']) && !empty($id_post) ) //inject
{
	$title = !empty($_POST['title']) ? securit($_POST['title']) : '';	
	$compt = isset($_POST['compt']) ? numeric($_POST['compt']) : '0';
	$contents = !empty($_POST['contents']) ? parse($_POST['contents']) : '';
	$url = !empty($_POST['url']) ? securit($_POST['url']) : '';
	$size = isset($_POST['size']) ? numeric($_POST['size'], 'float') : 0;
	$idcat = !empty($_POST['idcat']) ? numeric($_POST['idcat']) : '';
	$current_date = !empty($_POST['current_date']) ? trim($_POST['current_date']) : '';
	$start = !empty($_POST['start']) ? trim($_POST['start']) : 0;
	$end = !empty($_POST['end']) ? trim($_POST['end']) : 0;
	$hour = !empty($_POST['hour']) ? trim($_POST['hour']) : 0;
	$min = !empty($_POST['min']) ? trim($_POST['min']) : 0;
	$get_visible = !empty($_POST['visible']) ? numeric($_POST['visible']) : 0;
	
	//On met � jour la config de base du sondage
	if( !empty($title) && !empty($contents) && !empty($url) && !empty($idcat) )
	{
		$start_timestamp = strtotimestamp($start, $LANG['date_format_short']);
		$end_timestamp = strtotimestamp($end, $LANG['date_format_short']);
		
		$visible = 1;		
		if( $get_visible == 2 )
		{	
			if( $start_timestamp > time() )
				$visible = 2;
			elseif( $start_timestamp == 0 )
				$visible = 1;
			else //Date inf�rieur � celle courante => inutile.
				$start_timestamp = 0;

			if( $end_timestamp > time() && $end_timestamp > $start_timestamp && $start_timestamp != 0 )
				$visible = 2;
			elseif( $start_timestamp != 0 ) //Date inf�rieur � celle courante => inutile.
				$end_timestamp = 0;
		}
		elseif( $get_visible == 1 )
		{	
			$start_timestamp = 0;
			$end_timestamp = 0;
		}
		else
		{	
			$visible = 0;
			$start_timestamp = 0;
			$end_timestamp = 0;
		}
		
		$timestamp = strtotimestamp($current_date, $LANG['date_format_short']);
		if( $timestamp > 0 )
		{
			//Ajout des heures et minutes
			$timestamp += ($hour * 3600) + ($min * 60);
			$timestamp = ' , timestamp = \'' . $timestamp . '\'';
		}
		else
			$timestamp = ' , timestamp = \'' . time() . '\'';
			
		$sql->query_inject("UPDATE ".PREFIX."download SET title = '" . $title . "', contents = '" . $contents . "', url = '" . $url . "', size = '" . $size . "', idcat = '" . $idcat . "', visible = '" . $visible . "', start = '" .  $start_timestamp . "', end = '" . $end_timestamp . "'" . $timestamp . ", compt = '" . $compt . "' WHERE id = '" . $id_post . "'", __LINE__, __FILE__);	
		
		include_once('../includes/rss.class.php'); //Flux rss reg�n�r�!
		$rss = new Rss('download/rss.php');
		$rss->cache_path('../cache/');
		$rss->generate_file('javascript', 'rss_download');
		$rss->generate_file('php', 'rss2_download');
		
		header('location:' . HOST . SCRIPT);
		exit;
	}
	else
	{
		header('location:' . HOST . DIR . '/download/admin_download.php?id= ' . $id_post . '&error=incomplete#errorh');
		exit;
	}
}
elseif( $del && !empty($id) ) //Suppression du fichier.
{
	//On supprime dans la bdd.
	$sql->query_inject("DELETE FROM ".PREFIX."download WHERE id = '" . $id . "'", __LINE__, __FILE__);	

	//On supprimes les �ventuels commentaires associ�s.
	$sql->query_inject("DELETE FROM ".PREFIX."com WHERE idprov = '" . $id . "' AND script = 'download'", __LINE__, __FILE__);
	
	include_once('../includes/rss.class.php'); //Flux rss reg�n�r�!
	$rss = new Rss('download/rss.php');
	$rss->cache_path('../cache/');
	$rss->generate_file('javascript', 'rss_download');
	$rss->generate_file('php', 'rss2_download');
	
	header('location:' . HOST . SCRIPT);
	exit;
}
else			
{	
	$template->set_filenames(array(
		'admin_download_management' => '../templates/' . $CONFIG['theme'] . '/download/admin_download_management.tpl'
	 ));

	$nbr_dl = $sql->count_table('download', __LINE__, __FILE__);
	
	//On cr�e une pagination si le nombre de fichier est trop important.
	include_once('../includes/pagination.class.php'); 
	$pagination = new Pagination();
		
	$template->assign_vars(array(			
		'THEME' => $CONFIG['theme'],
		'LANG' => $CONFIG['lang'],
		'PAGINATION' => $pagination->show_pagin('admin_download.php?p=%d', $nbr_dl, 'p', 25, 3),
		'L_DEL_ENTRY' => $LANG['del_entry'],
		'L_DOWNLOAD_ADD' => $LANG['download_add'],
		'L_DOWNLOAD_MANAGEMENT' => $LANG['download_management'],
		'L_DOWNLOAD_CAT' => $LANG['cat_management'],
		'L_DOWNLOAD_CONFIG' => $LANG['download_config'],
		'L_CATEGORY' => $LANG['category'],
		'L_PSEUDO' => $LANG['pseudo'],
		'L_SIZE' => $LANG['size'],
		'L_TITLE' => $LANG['title'],
		'L_APROB' => $LANG['aprob'],
		'L_UPDATE' => $LANG['update'],
		'L_DELETE' => $LANG['delete'],
		'L_DATE' => $LANG['date']
	));

	$template->assign_block_vars('list', array(
	));
	
	$result = $sql->query_while("SELECT d.id, d.idcat, d.title, d.timestamp, d.visible, d.start, d.end, d.size, dc.name, m.login 
	FROM ".PREFIX."download d 
	LEFT JOIN ".PREFIX."download_cat dc ON dc.id = d.idcat
	LEFT JOIN ".PREFIX."member m ON d.user_id = m.user_id
	ORDER BY d.timestamp DESC 
	" . $sql->sql_limit($pagination->first_msg(25, 'p'), 25), __LINE__, __FILE__);
	while( $row = $sql->sql_fetch_assoc($result) )
	{
		if( $row['visible'] == 2 )
			$aprob = $LANG['waiting'];			
		elseif( $row['visible'] == 1 )
			$aprob = $LANG['yes'];
		else
			$aprob = $LANG['no'];
			
		//On reccourci le lien si il est trop long pour �viter de d�former l'administration.
		$title = html_entity_decode($row['title']);
		$title = strlen($title) > 45 ? substr($title, 0, 45) . '...' : $title;

		$visible = '';
		if( $row['start'] > 0 )
			$visible .= gmdate_format('date_format_short', $row['start']);
		if( $row['end'] > 0 && $row['start'] > 0 )
			$visible .= ' ' . strtolower($LANG['until']) . ' ' . gmdate_format('date_format_short', $row['end']);
		elseif( $row['end'] > 0 )
			$visible .= $LANG['until'] . ' ' . gmdate_format('date_format_short', $row['end']);
		
		$template->assign_block_vars('list.download', array(
			'TITLE' => $title,
			'IDCAT' => $row['idcat'],
			'ID' => $row['id'],
			'CAT' => $row['name'],
			'PSEUDO' => !empty($row['login']) ? $row['login'] : $LANG['guest'],		
			'DATE' => gmdate_format('date_format_short', $row['timestamp']),
			'SIZE' => ($row['size'] >= 1) ? number_round($row['size'], 1) . ' ' . $LANG['unit_megabytes'] : number_round($row['size']*1024, 1) . ' ' . $LANG['unit_kilobytes'],
			'APROBATION' => $aprob,
			'VISIBLE' => ((!empty($visible)) ? '(' . $visible . ')' : '')			
		));	
	
	}
	$sql->close($result);
	
	include_once('../includes/bbcode.php');
	
	$template->pparse('admin_download_management'); 
}

require_once('../includes/admin_footer.php');

?>