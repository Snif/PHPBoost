<?php
/*##################################################
 *                              action.php
 *                            -------------------
 *   begin                : May 07, 2007
 *   copyright          : (C) 2007 Sautel Benoit
 *   email                : ben.popeye@phpboost.com
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

require_once('../includes/begin.php'); 
include_once('../wiki/wiki_functions.php'); 
load_module_lang('wiki', $CONFIG['lang']);

if( !$groups->check_auth($SECURE_MODULE['wiki'], ACCESS_MODULE) )
{
	$errorh->error_handler('e_auth', E_USER_REDIRECT); 
	exit;
}
require('../wiki/wiki_auth.php');

$id_auth = !empty($_POST['id_auth']) ? numeric($_POST['id_auth']) : 0;
$id_status = !empty($_POST['id_status']) ? numeric($_POST['id_status']) : 0;
$type_status = !empty($_POST['status']) ? securit($_POST['status']) : '';
$id_change_status = !empty($_POST['id_change_status']) ? numeric($_POST['id_change_status']) : 0;
$contents = !empty($_POST['contents']) ? wiki_parse($_POST['contents']) : '';
$move = !empty($_POST['id_to_move']) ? numeric($_POST['id_to_move']) : 0;
$new_cat = !empty($_POST['new_cat']) ? numeric($_POST['new_cat']) : 0;
$id_to_rename = !empty($_POST['id_to_rename']) ? numeric($_POST['id_to_rename']) : 0;
$new_title = !empty($_POST['new_title']) ? securit($_POST['new_title']) : '';
$create_redirection_while_renaming = !empty($_POST['create_redirection_while_renaming']) ? true : false;
$create_redirection = !empty($_POST['create_redirection']) ? numeric($_POST['create_redirection']) : 0;
$redirection_title = !empty($_POST['redirection_title']) ? securit($_POST['redirection_title']) : '';
$del_redirection = !empty($_GET['del_redirection']) ? numeric($_GET['del_redirection']) : 0;
$restore = !empty($_GET['restore']) ? numeric($_GET['restore']) : 0;
$del_archive = !empty($_GET['del_contents']) ? numeric($_GET['del_contents']) : 0;
$del_article = !empty($_GET['del_article']) ? numeric($_GET['del_article']) : 0;
$del_to_remove = !empty($_POST['id_to_remove']) ? numeric($_POST['id_to_remove']) : 0;
$report_cat = !empty($_POST['report_cat']) ? numeric($_POST['report_cat']) : 0;
$remove_action = !empty($_POST['action']) ? securit($_POST['action']) : ''; //Action � faire lors de la suppression

if( $id_auth > 0 )
{
	if( !$groups->check_auth($_WIKI_CONFIG['auth'], WIKI_RESTRICTION) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	$encoded_title = $sql->query("SELECT encoded_title FROM ".PREFIX."wiki_articles WHERE id = '" . $id_auth . "'", __LINE__, __FILE__);
	if( empty($encoded_title) )
	{
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php'), '', '&');
		exit;
	}
	if( !empty($_POST['default']) ) //Configuration par d�faut
	{
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET auth = '' WHERE id= '" . $id_auth . "'", __LINE__, __FILE__);
	}
	else
	{
		$auth_restore_archive = isset($_POST['groups_auth3']) ? $_POST['groups_auth3'] : '';
		$auth_delete_archive = isset($_POST['groups_auth4']) ? $_POST['groups_auth4'] : '';
		$auth_edit = isset($_POST['groups_auth5']) ? $_POST['groups_auth5'] : '';
		$auth_delete = isset($_POST['groups_auth6']) ? $_POST['groups_auth6'] : '';
		$auth_rename = isset($_POST['groups_auth7']) ? $_POST['groups_auth7'] : '';
		$auth_redirect = isset($_POST['groups_auth8']) ? $_POST['groups_auth8'] : '';
		$auth_move = isset($_POST['groups_auth9']) ? $_POST['groups_auth9'] : '';
		$auth_status = isset($_POST['groups_auth10']) ? $_POST['groups_auth10'] : '';
		$auth_com = isset($_POST['groups_auth11']) ? $_POST['groups_auth11'] : '';
		
		//G�n�ration du tableau des droits.
		$array_auth_all = $groups->return_array_auth($auth_restore_archive, $auth_delete_archive, $auth_edit, $auth_delete, $auth_rename, $auth_redirect, $auth_move, $auth_status, $auth_com);
				
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET auth = '" . addslashes(serialize($array_auth_all)) . "' WHERE id= '" . $id_auth . "'", __LINE__, __FILE__);
	}

	//Redirection vers l'article
	header('Location: ' . transid('wiki.php?title=' . $encoded_title, $encoded_title, '&'));
	exit;
}
if( $id_change_status > 0 )
{
	$type_status = ($type_status == 'radio_undefined') ? 'radio_undefined' : 'radio_defined';
	
	//Si il s'agit d'un statut personnalis�
	if( $type_status == 'radio_undefined' && $contents != '' )
	{
		$id_status = -1;
	}
	elseif( $type_status == 'radio_defined' && $id_status > 0 && is_array($LANG['wiki_status_list'][$id_status - 1]) )
	{
		$contents = '';
	}
	else
		$id_status = 0;
		
	$article_infos = $sql->query_array("wiki_articles", "encoded_title", "auth", "WHERE id = '" . $id_change_status . "'", __LINE__, __FILE__);
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();
	
	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_STATUS)) && ($general_auth || $groups->check_auth($article_auth , WIKI_STATUS))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	if( !empty($article_infos['encoded_title']) )//Si l'article existe
	{
		//On met � jour dans la base de donn�es
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET defined_status = '" . $id_status . "', undefined_status = '" . $contents . "' WHERE id = '" . $id_change_status . "'", __LINE__, __FILE__);
		//Redirection vers l'article
		header('Location: ' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
		exit;
	}
}
elseif( $move > 0 ) //D�placement d'un article
{
	$article_infos = $sql->query_array("wiki_articles", "is_cat", "encoded_title", "id_cat", "auth", "WHERE id = '" . $move . "'", __LINE__, __FILE__);
	if(  empty($article_infos['encoded_title']) )//Ce n'est pas un article ou une cat�gorie
	{
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php'), '', '&');
		exit;
	}
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();
	
	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_MOVE)) && ($general_auth || $groups->check_auth($article_auth , WIKI_MOVE))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	if( $article_infos['is_cat'] == 0 )//Article: il ne peut pas y avoir de probl�me
	{
		if( array_key_exists($new_cat, $_WIKI_CATS) || $new_cat == 0 )//Si la nouvelle cat�gorie existe
		{
			$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET id_cat = '" . $new_cat . "' WHERE id = '" . $move . "'", __LINE__, __FILE__);
			$cache->generate_module_file('wiki');
		}
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
		exit;
	}
	//Cat�gorie: on v�rifie qu'on ne la place pas dans elle-m�me ou dans une de ses cat�gories filles
	elseif( $article_infos['is_cat'] == 1 )
	{
		//On fait un tableau contenant la liste des sous cat�gories de cette cat�gorie
		$sub_cats = array();
		wiki_find_subcats($sub_cats, $article_infos['id_cat']);
		$sub_cats[] = $article_infos['id_cat'];

		if( !in_array($new_cat, $sub_cats) ) //Si l'ancienne cat�gorie ne contient pas la nouvelle (sinon boucle infinie)
		{
			$sql->query_inject("UPDATE ".PREFIX."wiki_cats SET id_parent = '" . $new_cat . "' WHERE id = '" . $article_infos['id_cat'] . "'", __LINE__, __FILE__);
			$cache->generate_module_file('wiki');
			//on redirige vers l'article
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
			exit;
		}
		else //On redirige vers une page d'erreur
		{
			header('Location: ' . HOST . DIR . '/wiki/' .  transid('property.php?move=' . $move  . '&error=e_cat_contains_cat', '', '&') . '#errorh');
			exit;
		}
	}
}
elseif( $id_to_rename > 0 && !empty($new_title) ) //Renommer un article
{
	$article_infos = $sql->query_array("wiki_articles", "*", "WHERE id = '" . $id_to_rename . "'", __LINE__, __FILE__);
		
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();

	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_RENAME)) && ($general_auth || $groups->check_auth($article_auth , WIKI_RENAME))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	$already_exists = $sql->query("SELECT COUNT(*) FROM ".PREFIX."wiki_articles WHERE encoded_title = '" . url_encode_rewrite($new_title) . "'", __LINE__, __FILE__);

	if( empty($article_infos['encoded_title']) )//L'article n'existe pas
	{
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
		exit;
	}
	elseif( url_encode_rewrite($new_title) == $article_infos['encoded_title'] )//Si seul le titre change mais pas le titre encod�
	{
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET title = '" . $new_title . "' WHERE id = '" . $id_to_rename . "'", __LINE__, __FILE__);
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
		exit;
	}
	elseif( $already_exists > 0 ) //Si le titre existe d�j� erreur, on le signale
	{
		header('Location: ' . HOST . DIR . '/wiki/' . transid('property.php?rename=' . $id_to_rename  . '&error=title_already_exists', '', '&') . '#errorh');
		exit;
	}
	elseif( $already_exists == 0 )
	{
		if( $create_redirection_while_renaming ) //On cr�e un nouvel article
		{
			//On ajoute un article
			$sql->query_inject("INSERT INTO ".PREFIX."wiki_articles (id_contents, title, encoded_title, hits, id_cat, is_cat, defined_status, undefined_status, redirect, auth) VALUES ('" . $article_infos['id_contents'] . "', '" . $new_title . "', '" . url_encode_rewrite($new_title) . "', '" . $article_infos['hits'] . "', '" . $article_infos['id_cat'] . "', '" . $article_infos['is_cat'] . "', '" . $article_infos['defined_status'] . "', '" . $article_infos['undefied_status'] . "', 0, '" . $article_infos['auth'] . "')", __LINE__, __FILE__);
			$new_id_article = $sql->sql_insert_id("SELECT MAX(id_contents) FROM ".PREFIX."wiki_contents");
			
			//On met � jour la table contents
			$sql->query_inject("UPDATE ".PREFIX."wiki_contents SET id_article = '" . $new_id_article . "' WHERE id_article = '" . $id_to_rename . "'", __LINE__, __FILE__);
			//On inscrit la redirection � l'ancien article
			$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET redirect = '" . $new_id_article . "', id_contents = 0 WHERE id = '" . $id_to_rename . "'", __LINE__, __FILE__);
			//On redirige les �ventuelles redirections vers cet article sur son nouveau nom
			$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET redirect = '" . $new_id_article . "' WHERE redirect = '" . $id_to_rename . "'", __LINE__, __FILE__);
			//Si c'est une cat�gorie on change l'id d'article associ�
			if( $article_infos['is_cat'] == 1 )
			{
				$sql->query_inject("UPDATE ".PREFIX."wiki_cats SET article_id = '" . $new_id_article . "' WHERE id = '" . $article_infos['id_cat'] . "'", __LINE__, __FILE__);
				$cache->generate_module_file('wiki');
			}
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($new_title), url_encode_rewrite($new_title), '&'));
			exit;
			
		}
		else //On met � jour l'article
		{
			$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET title = '" . $new_title . "', encoded_title = '" . url_encode_rewrite($new_title) . "' WHERE id = '" . $id_to_rename . "'", __LINE__, __FILE__);
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($new_title), url_encode_rewrite($new_title), '&'));
			exit;
		}
	}
}
elseif( $del_redirection > 0 )//Supprimer une redirection
{
	$is_redirection = $sql->query("SELECT redirect FROM ".PREFIX."wiki_articles WHERE id = '" . $del_redirection . "'", __LINE__, __FILE__);
	if( $is_redirection > 0 )
	{
		$article_infos = $sql->query_array("wiki_articles", "encoded_title", "auth", "WHERE id = '" . $is_redirection . "'", __LINE__, __FILE__);
		
		$general_auth = empty($article_infos['auth']) ? true : false;
		$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();
	
		if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_REDIRECT)) && ($general_auth || $groups->check_auth($article_auth , WIKI_REDIRECT))) )
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
		
		$sql->query_inject("DELETE FROM ".PREFIX."wiki_articles WHERE id = '" . $del_redirection . "'", __LINE__, __FILE__);
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
		exit;
	}
}
elseif( $create_redirection > 0 && !empty($redirection_title) )
{
	$article_infos = $sql->query_array('wiki_articles', '*', "WHERE id = '" . $create_redirection . "'", __LINE__, __FILE__);
	
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();

	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_REDIRECT)) && ($general_auth || $groups->check_auth($article_auth , WIKI_REDIRECT))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	$num_title = $sql->query("SELECT COUNT(*) FROM ".PREFIX."wiki_articles WHERE encoded_title =  '" . url_encode_rewrite($redirection_title) . "'", __LINE__, __FILE__);

	if( !empty($article_infos['encoded_title']) )
	{
		if( $num_title == 0 ) //Si aucun article existe
		{
			$sql->query_inject("INSERT INTO ".PREFIX."wiki_articles (title, encoded_title, redirect) VALUES ('" . $redirection_title . "', '" . url_encode_rewrite($redirection_title) . "', '" . $create_redirection . "')", __LINE__, __FILE__);
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($redirection_title), url_encode_rewrite($redirection_title), '&'));
			exit;
		}
		else
		{
			header('Location: ' . HOST . DIR . '/wiki/' . transid('property.php?create_redirection=' . $create_redirection  . '&error=title_already_exists', '', '&') . '#errorh');
			exit;
		}
	}
}
//Restauration d'une archive
elseif( !empty($restore) ) //on restaure un ancien article
{
	//On cherche l'article correspondant
	$id_article = $sql->query("SELECT id_article FROM ".PREFIX."wiki_contents WHERE id_contents = " . $restore, __LINE__, __FILE__);
	if( !empty($id_article) )
	{
		//On r�cup�re l'ancien id du contenu
		$article_infos = $sql->query_array('wiki_articles', 'id_contents', 'encoded_title', 'auth', 'WHERE id = ' . $id_article, __LINE__, __FILE__);
		
		$general_auth = empty($article_infos['auth']) ? true : false;
		$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();
	
		if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_DELETE_ARCHIVE)) && ($general_auth || $groups->check_auth($article_auth , WIKI_DELETE_ARCHIVE))) )
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
		
		//On met � jour la table articles avec le nouvel id
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET id_contents = " . $restore . " WHERE id = " . $id_article, __LINE__, __FILE__);
		//On met le nouvel id comme actif
		$sql->query_inject("UPDATE ".PREFIX."wiki_contents SET activ = 1 WHERE id_contents = " . $restore, __LINE__, __FILE__);
		//L'ancien id devient archive
		$sql->query_inject("UPDATE ".PREFIX."wiki_contents SET activ = 0 WHERE id_contents = " . $article_infos['id_contents'], __LINE__, __FILE__);
	}
	
	header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'] , '&'));
	exit;
}
//Suppression d'une archive
elseif( $del_archive > 0 )
{
	$contents_infos = $sql->query_array("wiki_contents", "activ", "id_article", "WHERE id_contents = '" . $del_archive . "'", __LINE__, __FILE__);
	$article_infos = $sql->query_array("wiki_articles", "encoded_title", "auth", "WHERE id = '" . $contents_infos['id_article'] . "'", __LINE__, __FILE__);
	
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();

	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_DELETE_ARCHIVE)) && ($general_auth || $groups->check_auth($article_auth , WIKI_DELETE_ARCHIVE))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	if( $is_activ == 0 ) //C'est une archive -> on peut supprimer
		$sql->query_inject("DELETE FROM ".PREFIX."wiki_contents WHERE id_contents = '" . $del_archive . "'", __LINE__, __FILE__);
	if( !empty($article_infos['encoded_title']) ) //on redirige vees l'article
	{
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . $article_infos['encoded_title'], $article_infos['encoded_title'], '&'));
		exit;
	}
}
elseif( $del_article > 0 ) //Suppression d'un article
{
	$article_infos = $sql->query_array("wiki_articles", "auth", "encoded_title", "id_cat", "WHERE id = '" . $del_article . "'", __LINE__, __FILE__);
	
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();

	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_DELETE)) && ($general_auth || $groups->check_auth($article_auth , WIKI_DELETE))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	//On rippe l'article
	$sql->query_inject("DELETE FROM ".PREFIX."wiki_articles WHERE id = '" . $del_article . "'", __LINE__, __FILE__);
	$sql->query_inject("DELETE FROM ".PREFIX."wiki_contents WHERE id_article = '" . $del_article . "'", __LINE__, __FILE__);
	$sql->query_inject("DELETE FROM ".PREFIX."com WHERE script = 'wiki' AND idprov = '" . $del_article . "'", __LINE__); 
	
	if( array_key_exists($article_infos['id_cat'], $_WIKI_CATS) )//Si elle  a une cat�gorie parente
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($_WIKI_CATS[$article_infos['id_cat']]['name']), url_encode_rewrite($_WIKI_CATS[$article_infos['id_cat']]['name']), '&'));
	else
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
	exit;
}
elseif( $del_to_remove > 0 && $report_cat >= 0 ) //Suppression d'une cat�gorie
{
	$remove_action = ($remove_action == 'move_all') ? 'move_all' : 'remove_all';
	
	$article_infos = $sql->query_array("wiki_articles", "encoded_title", "id_cat", "auth", "WHERE id = '" . $del_to_remove . "'", __LINE__, __FILE__);
	
	$general_auth = empty($article_infos['auth']) ? true : false;
	$article_auth = !empty($article_infos['auth']) ? unserialize($article_infos['auth']) : array();

	if( !((!$general_auth || $groups->check_auth($_WIKI_CONFIG['auth'], WIKI_DELETE)) && ($general_auth || $groups->check_auth($article_auth , WIKI_DELETE))) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	
	$sub_cats = array();
	//On fait un tableau contenant la liste des sous cat�gories de cette cat�gorie
	wiki_find_subcats($sub_cats, $article_infos['id_cat']);
	$sub_cats[] = $article_infos['id_cat']; //On rajoute la cat�gorie que l'on supprime
	
	if( empty($article_infos['encoded_title']) ) //si l'article n'existe pas on redirige vers l'index
		header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
	
	if( $remove_action == 'move_all' ) //V�rifications pr�liminaires si on va tout supprimer
	{	
		//Si la nouvelle cat�gorie n'est pas une cat�gorie
		if( !array_key_exists($report_cat, $_WIKI_CATS) && $report_cat > 0 )
		{
			header('Location: ' . HOST . DIR . '/wiki/' . transid('property.php?del=' . $del_to_remove . '&error=e_not_a_cat#errorh', '', '&'));
			exit;
		}
			
		//Si on ne la d�place pas dans une de ses cat�gories filles
		if( ($report_cat > 0 && in_array($report_cat, $sub_cats)) || $report_cat == $article_infos['id_cat'] )//Si on veut reporter dans une cat�gorie parente
		{
			header('Location: ' . HOST . DIR . '/wiki/' . transid('property.php?del=' . $del_to_remove . '&error=e_cat_contains_cat#errorh', '','&'));
			exit;
		}
	}

	//Quoi qu'il arrive on supprime l'article associ�
	$sql->query_inject("DELETE FROM ".PREFIX."wiki_contents WHERE id_article = '" . $del_to_remove . "'", __LINE__, __FILE__);	
	$sql->query_inject("DELETE FROM ".PREFIX."wiki_articles WHERE id = '" . $del_to_remove . "'", __LINE__, __FILE__);
	
	$sql->query_inject("DELETE FROM ".PREFIX."wiki_cats WHERE id = '" . $article_infos['id_cat'] . "'", __LINE__, __FILE__);
	$sql->query_inject("DELETE FROM ".PREFIX."com WHERE script = 'wiki' AND idprov = '" . $del_to_remove . "'", __LINE__);
	
	if( $remove_action == 'remove_all' ) //On supprime le contenu de la cat�gorie
	{
		foreach( $sub_cats as $id ) //Chaque sous-cat�gorie
		{
			$result = $sql->query_while("SELECT id FROM ".PREFIX."wiki_articles WHERE id_cat = '" . $id . "'", __LINE__, __FILE__);
			while( $row = $sql->sql_fetch_assoc($result) ) //On supprime toutes les archives de chaque article avant de le supprimer lui-m�me
			{
				$sql->query_inject("DELETE FROM ".PREFIX."wiki_contents WHERE id_article = '" . $row['id'] . "'", __LINE__, __FILE__);
				$sql->query_inject("DELETE FROM ".PREFIX."com WHERE script = 'wiki' AND idprov = '" . $row['id'] . "'", __LINE__);
			}
				
			$sql->close($result);
			
			$sql->query_inject("DELETE FROM ".PREFIX."wiki_articles WHERE id_cat = '" . $id . "'", __LINE__, __FILE__);
			$sql->query_inject("DELETE FROM ".PREFIX."wiki_cats WHERE id = '" . $id . "'", __LINE__, __FILE__);
		}
		$cache->generate_module_file('wiki');
		
		//On redirige soit vers l'article parent soit vers la cat�gorie
		if( array_key_exists($article_infos['id_cat'], $_WIKI_CATS) && $_WIKI_CATS[$article_infos['id_cat']]['id_parent'] > 0 )
		{
			$title = $_WIKI_CATS[$_WIKI_CATS[$article_infos['id_cat']]['id_parent']]['name'];
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($title), url_encode_rewrite($title), '&'));
			exit;
		}
		else
		{
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
			exit;
		}
	}
	elseif( $remove_action == 'move_all' ) //On d�place le contenu de la cat�gorie
	{
		$sql->query_inject("UPDATE ".PREFIX."wiki_articles SET id_cat = '" . $report_cat . "' WHERE id_cat = '" . $article_infos['id_cat'] . "'", __LINE__, __FILE__);
		$sql->query_inject("UPDATE ".PREFIX."wiki_cats SET id_parent = '" . $report_cat . "' WHERE id_parent = '" . $article_infos['id_cat'] . "'", __LINE__, __FILE__);
		$cache->generate_module_file('wiki');
		
		if( array_key_exists($report_cat, $_WIKI_CATS) )
		{
			$title = $_WIKI_CATS[$report_cat]['name'];
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php?title=' . url_encode_rewrite($title), url_encode_rewrite($title), '&'));
			exit;
		}
		else
		{
			header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
			exit;
		}
	}
}

//On redirige vers l'index si on n'est rentr� dans aucune des conditions ci-dessus
header('Location: ' . HOST . DIR . '/wiki/' . transid('wiki.php', '', '&'));
exit;

?>