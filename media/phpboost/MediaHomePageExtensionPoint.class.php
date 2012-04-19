<?php
/*##################################################
 *                     MediaHomePageExtensionPoint.class.php
 *                            -------------------
 *   begin                : February 07, 2012
 *   copyright            : (C) 2012 Julien BRISWALTER
 *   email                : julien.briswalter@gmail.com
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

class MediaHomePageExtensionPoint implements HomePageExtensionPoint
{
	private $sql_querier;

    public function __construct()
    {
        $this->sql_querier = PersistenceContext::get_sql();
	}
	
	public function get_home_page()
	{
		return new DefaultHomePage($this->get_title(), $this->get_view());
	}
	
	private function get_title()
	{
		global $MEDIA_LANG;
		
		return $MEDIA_LANG['media'];
	}
	
	private function get_view()
	{
		global $MEDIA_CATS, $LANG, $MEDIA_LANG, $MEDIA_CONFIG, $Cache, $id_cat, $id_media, $User, $auth_write, $Session, $Bread_crumb, $level;
		
		require_once(PATH_TO_ROOT . '/media/media_begin.php');
		
		$tpl = new FileTemplate('media/media.tpl');
		
		// Display caterories and media files.
		if (empty($id_media) && $id_cat >= 0)
		{
			//if the category doesn't exist or is not visible
			if (empty($MEDIA_CATS[$id_cat]) || $MEDIA_CATS[$id_cat]['visible'] === false || !$User->check_auth($MEDIA_CATS[$id_cat]['auth'], MEDIA_AUTH_READ))
			{
				$controller = PHPBoostErrors::unexisting_category();
				DispatchManager::redirect($controller);
			}

			bread_crumb($id_cat);

			define('TITLE', $MEDIA_CATS[$id_cat]['name']);

			require_once('../kernel/header.php');

			$i = 1;
			//List of children categories
			foreach ($MEDIA_CATS as $id => $array)
			{
				if ($id != 0 && $array['visible'] && $array['id_parent'] == $id_cat && $User->check_auth($array['auth'], MEDIA_AUTH_READ))
				{
					if ($i % $MEDIA_CONFIG['nbr_column'] == 1)
					{
						$tpl->assign_block_vars('row', array());
					}

					$tpl->assign_block_vars('row.list_cats', array(
						'ID' => $id,
						'NAME' => $array['name'],
						'WIDTH' => floor(100 / (float)$MEDIA_CONFIG['nbr_column']),
						'SRC' => !empty($array['image']) ? $array['image'] : 'media_mini.png',
						'IMG_NAME' => addslashes($array['name']),
						'NUM_MEDIA' => ($array['active'] & MEDIA_NBR) !== 0 ? sprintf(($array['num_media'] > 1 ? $MEDIA_LANG['num_medias'] : $MEDIA_LANG['num_media']), $array['num_media']) : '',
						'U_CAT' => url('media.php?cat=' . $id, 'media-0-' . $id . '+' . Url::encode_rewrite($array['name']) . '.php'),
						'U_ADMIN_CAT' => url('admin_media_cats.php?edit=' . $id)
					));

					$i++;
				}
			}

			$tpl->put_all(array(
				'C_CATEGORIES' => true,
				'TITLE' => $MEDIA_CATS[$id_cat]['name'],
				'C_ADMIN' => $User->check_level(User::ADMIN_LEVEL),
				'C_MODO' => $User->check_level(User::MODERATOR_LEVEL),
				'U_ADMIN_CAT' => $id_cat == 0 ? 'admin_media_config.php' : 'admin_media_cats.php?edit=' . $id_cat,
				'C_ADD_FILE' => $User->check_auth($MEDIA_CATS[$id_cat]['auth'], MEDIA_AUTH_WRITE) || $User->check_auth($MEDIA_CATS[$id_cat]['auth'], MEDIA_AUTH_CONTRIBUTION),
				'U_ADD_FILE' => 'media_action.php?add=' . $id_cat,
				'L_ADD_FILE' => $MEDIA_LANG['add_media'],
				'C_DESCRIPTION' => !empty($MEDIA_CATS[$id_cat]['desc']),
				'DESCRIPTION' => FormatingHelper::second_parse($MEDIA_CATS[$id_cat]['desc']),
				'C_SUB_CATS' => $i > 1
			));

			//Contenu de la cat�gorie
			if ($MEDIA_CATS[$id_cat]['num_media'] > 0)
			{
				$get_sort = retrieve(GET, 'sort', '');
				$get_mode = retrieve(GET, 'mode', '');
				$mode = ($get_mode == 'asc') ? 'ASC' : 'DESC';
				$unget = (!empty($get_sort) && !empty($mode)) ? '?sort=' . $get_sort . '&amp;mode=' . $get_mode : '';
				$selected_fields = array('alpha' => '', 'date' => '', 'nbr' => '', 'note' => '', 'com' => '', 'asc' => '', 'desc' => '');

				switch ($get_sort)
				{
					case 'alpha':
						$sort = 'name';
						$selected_fields['alpha'] = ' selected="selected"';
						break;
					default:
					case 'date':
						$sort = 'timestamp';
						$selected_fields['date'] = ' selected="selected"';
						break;
					case 'nbr':
						$sort = 'counter';
						$selected_fields['nbr'] = ' selected="selected"';
						break;
					case 'note':
						$sort = 'average_notes';
						$selected_fields['note'] = ' selected="selected"';
						break;
					case 'com':
						$sort = 'nbr_com';
						$selected_fields['com'] = ' selected="selected"';
						break;
				}

				if ($mode == 'ASC')
				{
					$selected_fields['asc'] = ' selected="selected"';
				}
				else
				{
					$selected_fields['desc'] = ' selected="selected"';
				}

				$tpl->put_all(array(
					'L_ALPHA' => $MEDIA_LANG['sort_title'],
					'L_DATE' => $LANG['date'],
					'L_NBR' => $MEDIA_LANG['sort_popularity'],
					'L_NOTE' => $LANG['note'],
					'L_COM' => $LANG['com'],
					'L_DESC' => $LANG['desc'],
					'L_ASC' => $LANG['asc'],
					'L_ORDER_BY' => $LANG['orderby'],
					'L_CONFIRM_DELETE_FILE' => str_replace('\'', '\\\'', $MEDIA_LANG['confirm_delete_media']),
					'SELECTED_ALPHA' => $selected_fields['alpha'],
					'SELECTED_DATE' => $selected_fields['date'],
					'SELECTED_NBR' => $selected_fields['nbr'],
					'SELECTED_NOTE' => $selected_fields['note'],
					'SELECTED_COM' => $selected_fields['com'],
					'SELECTED_ASC' => $selected_fields['asc'],
					'SELECTED_DESC' => $selected_fields['desc'],
					'A_COM' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_COM) !== 0,
					'A_NOTE' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_NOTE) !== 0,
					'A_USER' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_USER) !== 0,
					'A_COUNTER' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_COUNT) !== 0,
					'A_DATE' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_DATE) !== 0,
					'A_DESC' => ($MEDIA_CATS[$id_cat]['active'] & MEDIA_DL_DESC) !== 0,
					'A_BLOCK' => ($MEDIA_CATS[$id_cat]['active'] & (MEDIA_DL_DATE + MEDIA_DL_COUNT + MEDIA_DL_COM + MEDIA_DL_NOTE + MEDIA_DL_USER)) !== 0
				));

				//On cr�e une pagination si le nombre de fichiers est trop important.
				
				$Pagination = new DeprecatedPagination();

				$tpl->put_all(array(
					'PAGINATION' => $Pagination->display(url('media.php' . (!empty($unget) ? $unget . '&amp;' : '?') . 'cat=' . $id_cat . '&amp;p=%d', 'media-0-' . $id_cat . '-%d' . '+' . Url::encode_rewrite($MEDIA_CATS[$id_cat]['name']) . '.php' . $unget), $MEDIA_CATS[$id_cat]['num_media'], 'p', $MEDIA_CONFIG['pagin'], 3),
					'C_FILES' => true,
					'TARGET_ON_CHANGE_ORDER' => ServerEnvironmentConfig::load()->is_url_rewriting_enabled() ? 'media-0-' . $id_cat . '.php?' : 'media.php?cat=' . $id_cat . '&'
				));

				$result = $this->sql_querier->query_while("SELECT v.id, v.iduser, v.name, v.timestamp, v.counter, v.infos, v.contents, mb.login, mb.level, notes.average_notes
					FROM " . PREFIX . "media AS v
					LEFT JOIN " . DB_TABLE_MEMBER . " AS mb ON v.iduser = mb.user_id
					LEFT JOIN " . DB_TABLE_AVERAGE_NOTES . " notes ON v.id = notes.id_in_module AND notes.module_name = 'media'
					WHERE idcat = '" . $id_cat . "' AND infos = '" . MEDIA_STATUS_APROBED . "'
					ORDER BY " . $sort . " " . $mode .
					$this->sql_querier->limit($Pagination->get_first_msg($MEDIA_CONFIG['pagin'], 'p'), $MEDIA_CONFIG['pagin']), __LINE__, __FILE__);

				while ($row = $this->sql_querier->fetch_assoc($result))
				{
					$notation->set_id_in_module($row['id']);
					
					$tpl->assign_block_vars('file', array(
						'NAME' => $row['name'],
						'IMG_NAME' => str_replace('"', '\"', $row['name']),
						'C_DESCRIPTION' => !empty($row['contents']),
						'DESCRIPTION' => FormatingHelper::second_parse($row['contents']),
						'POSTER' => !empty($row['login']) ? sprintf($MEDIA_LANG['media_added_by'], $row['login'], UserUrlBuilder::profile($row['iduser'])->absolute(), $level[$row['level']]) : $LANG['guest'],
						'DATE' => sprintf($MEDIA_LANG['add_on_date'], gmdate_format('date_format_short', $row['timestamp'])),
						'COUNT' => sprintf($MEDIA_LANG['view_n_times'], $row['counter']),
						'NOTE' => NotationService::display_static_image($notation, $row['average_notes']),
						'U_MEDIA_LINK' => url('media.php?id=' . $row['id'], 'media-' . $row['id'] . '-' . $id_cat . '+' . Url::encode_rewrite($row['name']) . '.php'),
						'U_ADMIN_UNVISIBLE_MEDIA' => url('media_action.php?unvisible=' . $row['id'] . '&amp;token=' . $Session->get_token()),
						'U_ADMIN_EDIT_MEDIA' => url('media_action.php?edit=' . $row['id']),
						'U_ADMIN_DELETE_MEDIA' => url('media_action.php?del=' . $row['id'] . '&amp;token=' . $Session->get_token()),
						'U_COM_LINK' => '<a href="'. PATH_TO_ROOT .'/media/media' . url('.php?id=' . $row['id'] . '&amp;com=0', '-' . $row['id'] . '-' . $id_cat . '+' . Url::encode_rewrite($row['name']) . '.php?com=0') .'">'. CommentsService::get_number_and_lang_comments('media', $row['id']) . '</a>'
					));
				}

				$this->sql_querier->query_close($result);
			}
			else
			{
				$tpl->put_all(array(
					'L_NO_FILE_THIS_CATEGORY' => $MEDIA_LANG['none_media'],
					'C_NO_FILE' => true
				));
			}
		}
		// Display the media file.
		elseif ($id_media > 0)
		{
			$result = $this->sql_querier->query_while("SELECT v.*, mb.login, mb.level	FROM " . PREFIX . "media AS v LEFT JOIN " . DB_TABLE_MEMBER . " AS mb ON v.iduser = mb.user_id	WHERE id = '" . $id_media . "'", __LINE__, __FILE__);
			$media = $this->sql_querier->fetch_assoc($result);
			$this->sql_querier->query_close($result);
			
			if (empty($media) || ($media['infos'] & MEDIA_STATUS_UNVISIBLE) !== 0)
			{
				$controller = new UserErrorController(LangLoader::get_message('error', 'errors'), 
					$LANG['e_unexist_media']);
				DispatchManager::redirect($controller);
			}
			elseif (!$User->check_auth($MEDIA_CATS[$media['idcat']]['auth'], MEDIA_AUTH_READ))
			{
				$error_controller = PHPBoostErrors::unexisting_page();
				DispatchManager::redirect($error_controller);
			}

			bread_crumb($media['idcat']);
			$Bread_crumb->add($media['name'], url('media.php?id=' . $id_media, 'media-' . $id_media . '-' . $media['idcat'] . '+' . Url::encode_rewrite($media['name']) . '.php'));

			define('TITLE', $media['name']);
			require_once('../kernel/header.php');

			//MAJ du compteur.
			$this->sql_querier->query_inject("UPDATE " . LOW_PRIORITY . " " . PREFIX . "media SET counter = counter + 1 WHERE id = " . $id_media, __LINE__, __FILE__);

			$notation->set_id_in_module($id_media);
			$nbr_notes = NotationService::get_former_number_notes($notation);
			
			$tpl->put_all(array(
				'C_DISPLAY_MEDIA' => true,
				'C_MODO' => $User->check_level(User::MODERATOR_LEVEL),
				'ID_MEDIA' => $id_media,
				'NAME' => $media['name'],
				'CONTENTS' => FormatingHelper::second_parse($media['contents']),
				'COUNT' => $media['counter'],
				'KERNEL_NOTATION' => NotationService::display_active_image($notation),
				'HITS' => ((int)$media['counter']+1) > 1 ? sprintf($MEDIA_LANG['n_times'], ((int)$media['counter']+1)) : sprintf($MEDIA_LANG['n_time'], ((int)$media['counter']+1)),
				'NUM_NOTES' => (int)$nbr_notes > 1 ? sprintf($MEDIA_LANG['num_notes'], (int)$nbr_notes) : sprintf($MEDIA_LANG['num_note'], (int)$nbr_notes),
				'U_COM' => '<a href="'. PATH_TO_ROOT .'/media/media' . url('.php?id=' . $id_media . '&amp;com=0', '-' . $id_media . '-' . $media['idcat'] . '+' . Url::encode_rewrite($media['name']) . '.php?com=0') .'">'. CommentsService::get_number_and_lang_comments('media', $id_media) . '</a>',
				'L_DATE' => $LANG['date'],
				'L_SIZE' => $LANG['size'],
				'L_MEDIA_INFOS' => $MEDIA_LANG['media_infos'],
				'DATE' => gmdate_format('date_format', $media['timestamp']),
				'URL' => $media['url'],
				'MIME' => $media['mime_type'],
				'WIDTH' => $media['width'],
				'HEIGHT' => $media['height'],
				'HEIGHT_P' => $media['height'] + 50,
				'L_VIEWED' => $LANG['view'],
				'L_BY' => $LANG['by'],
				'BY' => !empty($media['login']) ? sprintf($MEDIA_LANG['media_added'], $media['login'], UserUrlBuilder::profile($media['iduser'])->absolute(), $level[$media['level']]) : $LANG['guest'],
				'L_CONFIRM_DELETE_MEDIA' => str_replace('\'', '\\\'', $MEDIA_LANG['confirm_delete_media']),
				'U_UNVISIBLE_MEDIA' => url('media_action.php?unvisible=' . $id_media . '&amp;token=' . $Session->get_token()),
				'U_EDIT_MEDIA' => url('media_action.php?edit=' . $id_media),
				'U_DELETE_MEDIA' => url('media_action.php?del=' . $id_media . '&amp;token=' . $Session->get_token()),
				'U_POPUP_MEDIA' => url('media_popup.php?id=' . $id_media),
				'C_DISPLAY' => (($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_DATE) !== 0 || ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_USER) !== 0 || ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_COUNT) !== 0 || ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_NOTE) !== 0),
				'A_COM' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_COM) !== 0,
				'A_NOTE' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_NOTE) !== 0,
				'A_USER' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_USER) !== 0,
				'A_COUNTER' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_COUNT) !== 0,
				'A_DATE' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_DATE) !== 0,
				'A_DESC' => ($MEDIA_CATS[$media['idcat']]['active'] & MEDIA_DV_DESC) !== 0,
				'L_CONFIRM_DELETE_FILE' => str_replace('\'', '\\\'', $MEDIA_LANG['confirm_delete_media'])
			));

			if (empty($mime_type_tpl[$media['mime_type']]))
			{
				$tpl->put('media_format', new FileTemplate('media/format/media_other.tpl'));
			}
			else
			{
				$tpl->put('media_format', new FileTemplate('media/' . $mime_type_tpl[$media['mime_type']]));
			}
			
			//Affichage commentaires.
			if (isset($_GET['com']))
			{
				$comments_topic = new CommentsTopic();
				$comments_topic->set_module_id('media');
				$comments_topic->set_id_in_module($id_media);
				$comments_topic->set_url(new Url('/media/media.php?id='. $id_media . '&com=0'));
				$tpl->put_all(array(
					'COMMENTS' => CommentsService::display($comments_topic)->render()
				));
			}
		}

		return $tpl;
	}
}
?>