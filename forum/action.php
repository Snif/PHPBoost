<?php
/*##################################################
 *                                action.php
 *                            -------------------
 *   begin                : August 14, 2005
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

require_once('../includes/begin.php'); 
require_once('../forum/forum_begin.php');
speed_bar_generate($SPEED_BAR, $CONFIG_FORUM['forum_name'], 'index.php' . SID);
require_once('../includes/header_no_display.php');

//Variable GET.
$idt_get = !empty($_GET['id']) ? numeric($_GET['id']) : '';
$idm_get = !empty($_GET['idm']) ? numeric($_GET['idm']) : '';
$del = !empty($_GET['del']) ? true : false;
$track = !empty($_GET['t']) ? numeric($_GET['t']) : '';	
$untrack = !empty($_GET['ut']) ? numeric($_GET['ut']) : '';	
$alert = !empty($_GET['a']) ? numeric($_GET['a']) : '';	
$read = !empty($_GET['read']) ? true : false;;
$msg_d = !empty($_GET['msg_d']) ? true : false;
$lock_get = !empty($_GET['lock']) ? securit($_GET['lock']) : '';
//Variable $_POST
$poll = !empty($_POST['valid_forum_poll']) ? true : false; //Sondage forum.
$massive_action_type = !empty($_POST['massive_action_type']) ? trim($_POST['action_type']) : ''; //Op�ration de masse.

//Instanciation de la class du forum.
include_once('../forum/forum.class.php');
$forumfct = new Forum;

if( !empty($idm_get) && $del ) //Suppression d'un message/topic.
{
	//Info sur le message.	
	$msg = $sql->query_array('forum_msg', 'user_id', 'idtopic', "WHERE id = '" . $idm_get . "'", __LINE__, __FILE__);
	
	//On va chercher les infos sur le topic	
	$topic = $sql->query_array('forum_topics', 'user_id', 'idcat', 'first_msg_id', 'last_msg_id', 'last_timestamp', "WHERE id = '" . $msg['idtopic'] . "'", __LINE__, __FILE__);

	//Si on veut supprimer le premier message, alors son rippe le topic entier (admin et modo seulement).
	if( !empty($msg['idtopic']) && $topic['first_msg_id'] == $idm_get )
	{
		if( !empty($msg['idtopic']) && ($groups->check_auth($CAT_FORUM[$topic['idcat']]['auth'], EDIT_CAT_FORUM) || $session->data['user_id'] == $topic['user_id']) ) //Autoris� � supprimer?
			$forumfct->del_topic($msg['idtopic']); //Suppresion du topic.
		else
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
		
		header('location:' . HOST . DIR . '/forum/forum' . transid('.php?id=' . $topic['idcat'], '-' . $topic['idcat'] . '.php', '&'));
		exit;
	}
	elseif( !empty($msg['idtopic']) && $topic['first_msg_id'] != $idm_get ) //Suppression d'un message.
	{	
		if( !empty($topic['idcat']) && ($groups->check_auth($CAT_FORUM[$topic['idcat']]['auth'], EDIT_CAT_FORUM) || $session->data['user_id'] == $msg['user_id']) ) //Autoris� � supprimer?
			list($nbr_msg, $previous_msg_id) = $forumfct->del_msg($idm_get, $msg['idtopic'], $topic['idcat'], $topic['first_msg_id'], $topic['last_msg_id'], $topic['last_timestamp'], $msg['user_id']);
		else
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
		
		if( $nbr_msg === false && $previous_msg_id === false ) //Echec de la suppression.
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
		
		//On compte le nombre de messages du topic avant l'id supprim�.
		$last_page = ceil( $nbr_msg/ $CONFIG_FORUM['pagination_msg'] );
		$last_page_rewrite = ($last_page > 1) ? '-' . $last_page : '';
		$last_page = ($last_page > 1) ? '&pt=' . $last_page : '';
			
		header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $msg['idtopic'] . $last_page, '-' . $msg['idtopic'] . $last_page_rewrite . '.php', '&') . '#m' . $previous_msg_id);
		exit;
	}
	else //Non autoris�, on redirige.
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}	
}
elseif( !empty($idt_get) )
{		
	//On va chercher les infos sur le topic	
	$topic = $sql->query_array('forum_topics', 'user_id', 'idcat', 'title', 'subtitle', 'nbr_msg', 'last_msg_id', 'first_msg_id', 'last_timestamp', 'status', "WHERE id = '" . $idt_get . "'", __LINE__, __FILE__);

	if( !$groups->check_auth($CAT_FORUM[$topic['idcat']]['auth'], READ_CAT_FORUM) )
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
	//On encode l'url pour un �ventuel rewriting, c'est une op�ration assez gourmande
	$rewrited_cat_title = ($CONFIG['rewrite'] == 1) ? '+' . url_encode_rewrite($CAT_FORUM[$topic['idcat']]['name']) : '';
	//On encode l'url pour un �ventuel rewriting, c'est une op�ration assez gourmande
	$rewrited_title = ($CONFIG['rewrite'] == 1) ? '+' . url_encode_rewrite($topic['title']) : '';
	
	//Changement du statut (display_msg) du sujet.
	if( $msg_d )
	{
		//V�rification de l'appartenance du sujet au membres, ou modo.
		$check_mbr = $sql->query("SELECT user_id FROM ".PREFIX."forum_topics WHERE id = '" . $idt_get . "'", __LINE__, __FILE__);
		if( (!empty($check_mbr) && $session->data['user_id'] == $check_mbr) || $groups->check_auth($CAT_FORUM[$topic['idcat']]['auth'], EDIT_CAT_FORUM) )
		{
			$sql->query_inject("UPDATE ".PREFIX."forum_topics SET display_msg = 1 - display_msg WHERE id = '" . $idt_get . "'", __LINE__, __FILE__);
			
			header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $idt_get, '-' . $idt_get . $rewrited_title . '.php', '&'));
			exit;
		}	
		else
		{	
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}
	}	
	elseif( $poll && $session->data['user_id'] !== -1 ) //Enregistrement vote du sondage
	{
		$info_poll = $sql->query_array('forum_poll', 'voter_id', 'votes', "WHERE idtopic = '" . $idt_get . "'", __LINE__, __FILE__);
		//Si l'utilisateur n'est pas dans le champ on prend en compte le vote.
		if( !in_array($session->data['user_id'], explode('|', $info_poll['voter_id'])) )
		{		
			//On concat�ne avec les votans existants.
			$add_voter_id = "voter_id = CONCAT(voter_id, '|" . $session->data['user_id'] . "'),"; 
				
			$array_votes = explode('|', $info_poll['votes']);
				
			$id_answer = isset($_POST['radio']) ? numeric($_POST['radio']) : '-1'; //R�ponse simple.
			if( $id_answer >= 0 ) 
			{	
				if( isset($array_votes[$id_answer]) )
					$array_votes[$id_answer]++;
			}
			else //R�ponses multiples.
			{
				//On boucle pour v�rifier toutes les r�ponses du sondage.
				$nbr_answer = count($array_votes);
				for( $i = 0; $i < $nbr_answer; $i++)
				{
					if( isset($_POST[$i]) ) 
						$array_votes[$i]++;
				}
			}
				
			$sql->query_inject("UPDATE ".PREFIX."forum_poll SET " . $add_voter_id . " votes = '" . implode('|', $array_votes) . "' WHERE idtopic = '" . $idt_get . "'", __LINE__, __FILE__);
		}
		
		header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $idt_get, '-' . $idt_get . $rewrited_title . '.php', '&'));
		exit;
	}
	elseif( !empty($lock_get) )
	{
		//Si l'utilisateur a le droit de d�placer le topic, ou le verrouiller.
		if( $groups->check_auth($CAT_FORUM[$topic['idcat']]['auth'], EDIT_CAT_FORUM) )
		{
			if( $lock_get === 'true' ) //Verrouillage du topic.
			{
				//Instanciation de la class du forum.
				include_once('../forum/forum.class.php');
				$forumfct = new Forum;
			
				$forumfct->lock_topic($idt_get);
			
				header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $idt_get, '-' . $idt_get  . $rewrited_title . '.php', '&'));
				exit;			
			}
			elseif( $lock_get === 'false' )  //D�verrouillage du topic.
			{
				//Instanciation de la class du forum.
				include_once('../forum/forum.class.php');
				$forumfct = new Forum;
				
				$forumfct->unlock_topic($idt_get);
			
				header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $idt_get, '-' . $idt_get  . $rewrited_title . '.php', '&'));
				exit;
			}
		}
		else
		{
			$errorh->error_handler('e_auth', E_USER_REDIRECT); 
			exit;
		}		
	}
	else
	{
		$errorh->error_handler('e_auth', E_USER_REDIRECT); 
		exit;
	}
}
elseif( !empty($track) && $session->check_auth($session->data, 0) ) //Ajout du sujet aux sujets suivis.
{
	$forumfct->track_topic($track); //Ajout du sujet aux sujets suivis.
	
	header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $track, '-' . $track . '.php', '&') . '#quote');
	exit;
}
elseif( !empty($untrack) && $session->check_auth($session->data, 0) ) //Retrait du sujet, aux sujets suivis.
{
	$forumfct->untrack_topic($untrack); //Retrait du sujet aux sujets suivis.
	
	header('location:' . HOST . DIR . '/forum/topic' . transid('.php?id=' . $untrack, '-' . $untrack . '.php', '&') . '#quote');
	exit;
}
elseif( $read ) //Marquer comme lu.
{
	if( !$session->check_auth($session->data, 0) ) //R�serv� aux membres.
	{
		header('location: ' . HOST . DIR . '/member/error.php'); 
		exit;
	}
			
	//Calcul du temps de p�remption, ou de derni�re vue des messages.
	$check_last_view_forum = $sql->query("SELECT COUNT(*) FROM ".PREFIX."member_extend WHERE user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__);

	//Modification du last_view_forum, si le membre est d�j� dans la table
	if( !empty($check_last_view_forum) )
		$sql->query_inject("UPDATE ".LOW_PRIORITY." ".PREFIX."member_extend SET last_view_forum = '" .  time(). "' WHERE user_id = '" . $session->data['user_id'] . "'", __LINE__, __FILE__); 	
	else
		$sql->query_inject("INSERT INTO ".PREFIX."member_extend (user_id,last_view_forum) VALUES ('" . $session->data['user_id'] . "', '" .  time(). "')", __LINE__, __FILE__); 	

		header('location:' . HOST . DIR . '/forum/index.php' . SID2);
	exit;
}
else
{
	header('Location:' . HOST . DIR . '/forum/index.php' . SID2);
	exit;
}

require_once('../includes/footer_no_display.php');

?>