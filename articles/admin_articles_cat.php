<?php
/*##################################################
 *                               admin_articles_cat.php
 *                            -------------------
 *   begin                : August 27, 2007
 *   copyright          : (C) 2007 Viarre R�gis
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

require_once('../admin/admin_begin.php');
load_module_lang('articles'); //Chargement de la langue du module.
define('TITLE', $LANG['administration']);
require_once('../admin/admin_header.php');
require_once('articles_constants.php');

$id = retrieve(GET, 'id', 0,TINTEGER);
$cat_to_del = retrieve(GET, 'del', 0,TINTEGER);
$cat_to_del_post = retrieve(POST, 'cat_to_del', 0,TINTEGER);
$new_cat = retrieve(GET, 'new', false);
$id_edit = retrieve(GET, 'edit', 0,TINTEGER);
$error = retrieve(GET, 'error', '');

require_once('articles_cats.class.php');
$articles_categories = new ArticlesCats();

$tpl = new Template('articles/admin_articles_cat.tpl');
$Errorh->set_template($tpl);

require_once('admin_articles_menu.php');
$tpl->assign_vars(array('ADMIN_MENU' => $admin_menu));

if ($cat_to_del > 0)
{
	$array_cat = array();
	$nbr_cat = (int)$Sql->query("SELECT COUNT(*) FROM " . DB_TABLE_ARTICLES . " WHERE idcat = '" . $cat_to_del . "'", __LINE__, __FILE__);
	$articles_categories->build_children_id_list($cat_to_del, $array_cat, RECURSIVE_EXPLORATION, DO_NOT_ADD_THIS_CATEGORY_IN_LIST);

	if (empty($array_cat) && $nbr_cat === 0)
	{

		$articles_categories->delete($cat_to_del);
		
		// Feeds Regeneration
		import('content/feed/Feed');
		Feed::clear_cache('articles');

		redirect(url(HOST . SCRIPT . '?error=e_success#errorh'), '', '&');
	}
	else
	{
		$tpl->assign_vars(array(
			'EMPTY_CATS' => count($ARTICLES_CAT) < 2 ? true : false,
			'L_REMOVING_CATEGORY' => $ARTICLES_LANG['removing_category'],
			'L_EXPLAIN_REMOVING' => $ARTICLES_LANG['explain_removing_category'],
			'L_DELETE_CATEGORY_AND_CONTENT' => $ARTICLES_LANG['delete_category_and_its_content'],
			'L_MOVE_CONTENT' => $ARTICLES_LANG['move_category_content'],
			'L_SUBMIT' => $LANG['delete']
		));

		$tpl->assign_block_vars('removing_interface', array(
			'IDCAT' => $cat_to_del,
		));
		
		$articles_categories->build_select_form(0, 'idcat', 'idcat', $cat_to_del, 0, array(), RECURSIVE_EXPLORATION, $tpl);
	}
}
elseif ($new_cat XOR $id_edit > 0)
{
	$tpl->assign_vars(array(
		'KERNEL_EDITOR' => display_editor(),
		'L_CATEGORY' => $LANG['category'],
		'L_REQUIRE' => $LANG['require'],
		'L_PREVIEW' => $LANG['preview'],
		'L_RESET' => $LANG['reset'],
		'L_SUBMIT' => $id_edit > 0 ? $LANG['edit'] : $LANG['add'],
		'L_NAME' => $ARTICLES_LANG['category_name'],
		'L_LOCATION' => $ARTICLES_LANG['category_location'],
		'L_DESCRIPTION' => $ARTICLES_LANG['category_desc'],
		'L_IMAGE' => $ARTICLES_LANG['category_image'],
		'L_SPECIAL_AUTH' => $ARTICLES_LANG['special_auth'],
		'L_SPECIAL_AUTH_EXPLAIN' => $ARTICLES_LANG['special_auth_explain'],
		'L_AUTH_READ' => $ARTICLES_LANG['auth_read'],
		'L_AUTH_WRITE' => $ARTICLES_LANG['auth_write'],
		'L_AUTH_MODERATION' => $ARTICLES_LANG['auth_moderate'],
		'L_AUTH_CONTRIBUTION' => $ARTICLES_LANG['auth_contribute'],
		'L_REQUIRE_TITLE' => $LANG['required_field'].' : '.$ARTICLES_LANG['category_name'],
		'L_OR_DIRECT_PATH' => $ARTICLES_LANG['or_direct_path'],
		'L_ARTICLES_MODELS'=>$ARTICLES_LANG['articles_models'],
		'L_MODELS'=>$ARTICLES_LANG['models'],
		'L_MODELS_EXPLAIN'=>$ARTICLES_LANG['models_explain'],
		'L_CAT_ICON'=>$ARTICLES_LANG['cat_icon'],
		'L_MODELS_DESCRIPTION'=>$ARTICLES_LANG['model_desc'],
	));

	if ($id_edit > 0 && array_key_exists($id_edit, $ARTICLES_CAT))
	{
		$special_auth = $ARTICLES_CAT[$id_edit]['auth'] !== $CONFIG_ARTICLES['global_auth'] ? true : false;
		$ARTICLES_CAT[$id_edit]['auth'] = $special_auth ? $ARTICLES_CAT[$id_edit]['auth'] : $CONFIG_ARTICLES['global_auth'];

		// category icon
		$img_direct_path = (strpos($ARTICLES_CAT[$id_edit]['image'], '/') !== false);
		$image_list = '<option value=""' . ($img_direct_path ? ' selected="selected"' : '') . '>--</option>';
		import('io/filesystem/Folder');
		$image_folder_path = new Folder('./');
		foreach ($image_folder_path->get_files('`\.(png|jpg|bmp|gif|jpeg|tiff)$`i') as $images)
		{
			$image = $images->get_name();
			$selected = $image == $ARTICLES_CAT[$id_edit]['image'] ? ' selected="selected"' : '';
			$image_list .= '<option value="' . $image . '"' . ($img_direct_path ? '' : $selected) . '>' . $image . '</option>';
		}
		
		// models
		$result = $Sql->query_while("SELECT id, name,description
		FROM " . DB_TABLE_ARTICLES_MODEL 
		, __LINE__, __FILE__);
		$models='';
		while ($row = $Sql->fetch_assoc($result))
		{
			if($row['id'] == $ARTICLES_CAT[$id_edit]['models'])
			{
				$models.='<option value="' . $row['id'] . '" selected="selected">' . $row['name']. '</option>';
				$model_desc=$row['description'];
			}
			else
				$models.='<option value="' . $row['id'] . '">' . $row['name']. '</option>';
		}
		
		$tpl->assign_block_vars('edition_interface', array(
			'NAME' => $ARTICLES_CAT[$id_edit]['name'],
			'DESCRIPTION' => unparse($ARTICLES_CAT[$id_edit]['description']),
			'MODELS'=>$models,
			'MODELE_DESCRIPTION'=>second_parse($model_desc),
			'IMG_PATH' => $img_direct_path ? $ARTICLES_CAT[$id_edit]['image'] : '',
			'IMG_ICON' => !empty($ARTICLES_CAT[$id_edit]['image']) ? '<img src="' . $ARTICLES_CAT[$id_edit]['image'] . '" alt="" class="valign_middle" />' : '',		
			'IMG_LIST'=>$image_list,
			'CATEGORIES_TREE' => $articles_categories->build_select_form($ARTICLES_CAT[$id_edit]['id_parent'], 'id_parent', 'id_parent', $id_edit),
			'IDCAT' => $id_edit,
			'JS_SPECIAL_AUTH' => $special_auth ? 'true' : 'false',
			'DISPLAY_SPECIAL_AUTH' => $special_auth ? 'block' : 'none',
			'SPECIAL_CHECKED' => $special_auth ? 'checked="checked"' : '',
			'AUTH_READ' => Authorizations::generate_select(AUTH_ARTICLES_READ, $ARTICLES_CAT[$id_edit]['auth']),
			'AUTH_WRITE' => Authorizations::generate_select(AUTH_ARTICLES_WRITE, $ARTICLES_CAT[$id_edit]['auth']),
			'AUTH_CONTRIBUTION' => Authorizations::generate_select(AUTH_ARTICLES_CONTRIBUTE, $ARTICLES_CAT[$id_edit]['auth']),
			'AUTH_MODERATION' => Authorizations::generate_select(AUTH_ARTICLES_MODERATE, $ARTICLES_CAT[$id_edit]['auth']),
		));
	}
	else
	{
		$id_edit = 0;
		$img_default = '../articles/articles.png';
		$img_default_name = 'articles.png';
		$image_list = '<option value="'.$img_default.'" selected="selected">'.$img_default_name.'</option>';
		import('io/filesystem/Folder');
		$image_folder_path = new Folder('./');
		foreach ($image_folder_path->get_files('`\.(png|jpg|bmp|gif|jpeg|tiff)$`i') as $images)
		{
			$image = $images->get_name();
			if($image != $img_default_name)
			$image_list .= '<option value="' . $image . '">' . $image . '</option>';
		}
		
		// models
		$result = $Sql->query_while("SELECT id, name,description
		FROM " . DB_TABLE_ARTICLES_MODEL 
		, __LINE__, __FILE__);
		$models='';
		while ($row = $Sql->fetch_assoc($result))
		{
			if($row['id'] == 1)
			{
				$models.='<option value="' . $row['id'] . '" selected="selected">' . $row['name']. '</option>';
				$model_desc=$row['description'];
			}
			else
				$models.='<option value="' . $row['id'] . '">' . $row['name']. '</option>';
		}
			
		$tpl->assign_block_vars('edition_interface', array(
			'NAME' => '',
			'DESCRIPTION' => '',
			'IMG_PATH' => '',
			'IMG_ICON' => '',	
			'IMG_LIST' => $image_list,
			'MODELS'=>$models,
			'MODELE_DESCRIPTION'=>second_parse($model_desc),
			'IMG_PREVIEW' => second_parse_url($img_default),
			'CATEGORIES_TREE' => $articles_categories->build_select_form($id_edit, 'id_parent', 'id_parent'),
			'IDCAT' => $id_edit,
			'JS_SPECIAL_AUTH' => 'false',
			'DISPLAY_SPECIAL_AUTH' => 'none',
			'SPECIAL_CHECKED' => '',
			'AUTH_READ' => Authorizations::generate_select(AUTH_ARTICLES_READ, $CONFIG_ARTICLES['global_auth']),
			'AUTH_WRITE' => Authorizations::generate_select(AUTH_ARTICLES_WRITE, $CONFIG_ARTICLES['global_auth']),
			'AUTH_CONTRIBUTION' => Authorizations::generate_select(AUTH_ARTICLES_CONTRIBUTE, $CONFIG_ARTICLES['global_auth']),
			'AUTH_MODERATION' => Authorizations::generate_select(AUTH_ARTICLES_MODERATE, $CONFIG_ARTICLES['global_auth']),			
		));

	}
}
elseif (retrieve(POST,'submit',false))
{
	$error_string = 'e_success';
	//Deleting a category
	if (!empty($cat_to_del_post))
	{
		$delete_content =(retrieve(POST,'action','move') == 'move') ? false : true;
		$id_parent = retrieve(POST, 'idcat', 0,TINTEGER);
		
		$Session->csrf_get_protect();
		
		if ($delete_content)
		$articles_categories->delete_category_recursively($cat_to_del_post);
		else
		$articles_categories->delete_category_and_move_content($cat_to_del_post, $id_parent);
	}
	else
	{
		$id_cat = retrieve(POST, 'idcat', 0,TINTEGER);
		$id_parent = retrieve(POST, 'id_parent', 0,TINTEGER);
		$name = retrieve(POST, 'name', '');
		$icon=retrieve(POST, 'icon', '', TSTRING);
		$models=retrieve(POST, 'models', 1, TINTEGER);	
		
		if(retrieve(POST,'icon_path',false))
		$icon=retrieve(POST,'icon_path','');
			
		$description = retrieve(POST, 'description', '', TSTRING_PARSE);
		$auth = !empty($_POST['special_auth']) ? addslashes(serialize(Authorizations::build_auth_array_from_form(AUTH_ARTICLES_READ, AUTH_ARTICLES_CONTRIBUTE, AUTH_ARTICLES_WRITE, AUTH_ARTICLES_MODERATE))) : '';

		if (empty($name))
		redirect(url(HOST . SCRIPT . '?error=e_required_fields_empty#errorh'), '', '&');
		if ($id_cat > 0)
		$error_string = $articles_categories->Update_category($id_cat, $id_parent, $name, $description, $icon, $auth,$models);
		else
		$error_string = $articles_categories->add($id_parent, $name, $description, $icon, $auth,$models);
	}

	// Feeds Regeneration
	import('content/feed/Feed');
	Feed::clear_cache('articles');

	$Cache->Generate_module_file('articles');
	redirect(url(HOST . SCRIPT . '?error=' . $error_string  . '#errorh'), '', '&');
}
else
{
	if (!empty($error))
	{
		switch ($error)
		{
			case 'e_required_fields_empty' :
				$Errorh->handler($ARTICLES_LANG['required_fields_empty'], E_USER_WARNING);
				break;
			case 'e_unexisting_category' :
				$Errorh->handler($ARTICLES_LANG['unexisting_category'], E_USER_WARNING);
				break;
			case 'e_new_cat_does_not_exist' :
				$Errorh->handler($ARTICLES_LANG['new_cat_does_not_exist'], E_USER_WARNING);
				break;
			case 'e_infinite_loop' :
				$Errorh->handler($ARTICLES_LANG['infinite_loop'], E_USER_WARNING);
				break;
			case 'e_success' :
				$Errorh->handler($ARTICLES_LANG['successful_operation'], E_USER_SUCCESS);
				break;
		}
	}

	$cat_config = array(
		'xmlhttprequest_file' => 'xmlhttprequest_cats.php',
		'administration_file_name' => 'admin_articles_cat.php',
		'url' => array(
			'unrewrited' => 'articles.php?cat=%d',
			'rewrited' => 'articles-%d+%s.php'),
	);

	$articles_categories->set_display_config($cat_config);

	$tpl->assign_block_vars('categories_management', array(
		'L_CATS_MANAGEMENT' => $ARTICLES_LANG['category_articles'],
		'CATEGORIES' => $articles_categories->build_administration_interface()
	));
}

$tpl->parse();
require_once('../admin/admin_footer.php');

?>