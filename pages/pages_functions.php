<?php
/*##################################################
 *                              pages_functions.php
 *                            -------------------
 *   begin                : August 15, 2007
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

if( defined('PHP_BOOST') !== true)	exit;

//Cat�gories (affichage si on connait la cat�gorie et qu'on veut reformer l'arborescence)
function display_cat_explorer($id, &$cats, $display_select_link = 1)
{
	global $_PAGES_CATS;
		
	if( $id > 0)
	{
		$id_cat = $id;
		//On remonte l'arborescence des cat�gories afin de savoir quelle cat�gorie d�velopper
		do
		{
			$cats[] = (int)$_PAGES_CATS[$id_cat]['id_parent'];
			$id_cat = (int)$_PAGES_CATS[$id_cat]['id_parent'];
		}	
		while( $id_cat > 0 );
	}
	

	//Maintenant qu'on connait l'arborescence on part du d�but
	$cats_list = '<ul style="margin:0;padding:0;list-style-type:none;line-height:normal;">' . show_cat_contents(0, $cats, $id, $display_select_link) . '</ul>';
	
	//On liste les cat�gories ouvertes pour la fonction javascript
	$opened_cats_list = '';
	foreach( $cats as $key => $value )
	{
		if( $key != 0 )
			$opened_cats_list .= 'cat_status[' . $key . '] = 1;' . "\n";
	}
	return '<script type="text/javascript">
	<!--
' . $opened_cats_list . '
	-->
	</script>
	' . $cats_list;
	
}

//Fonction r�cursive pour l'affichage des cat�gories
function show_cat_contents($id_cat, $cats, $id, $display_select_link)
{
	global $_PAGES_CATS, $sql, $template;
	$line = '';
	foreach( $_PAGES_CATS as $key => $value )
	{
		//Si la cat�gorie appartient � la cat�gorie explor�e
		if( $value['id_parent']  == $id_cat )
		{
			if( in_array($key, $cats) ) //Si cette cat�gorie contient notre cat�gorie, on l'explore
			{
				$line .= '<li><a href="javascript:show_cat_contents(' . $key . ', ' . ($display_select_link != 0 ? 1 : 0) . ');"><img src="' . $template->module_data_path('pages') . '/images/minus.png" alt="" id="img2_' . $key . '" style="vertical-align:middle" /></a> <a href="javascript:show_cat_contents(' . $key . ', ' . ($display_select_link != 0 ? 1 : 0) . ');"><img src="' . $template->module_data_path('pages') . '/images/opened_cat.png" alt="" id="img_' . $key . '" style="vertical-align:middle" /></a>&nbsp;<span id="class_' . $key . '" class="' . ($key == $id ? 'pages_selected_cat' : '') . '"><a href="javascript:' . ($display_select_link != 0 ? 'select_cat' : 'open_cat') . '(' . $key . ');">' . $value['name'] . '</a></span><span id="cat_' . $key . '">
				<ul style="margin:0;padding:0;list-style-type:none;line-height:normal;padding-left:30px;">'
				. show_cat_contents($key, $cats, $id, $display_select_link) . '</ul></span></li>';
			}
			else
			{
				//On compte le nombre de cat�gories pr�sentes pour savoir si on donne la possibilit� de faire un sous dossier
				$sub_cats_number = $sql->query("SELECT COUNT(*) FROM ".PREFIX."pages_cats WHERE id_parent = '" . $key . "'", __LINE__, __FILE__);
				//Si cette cat�gorie contient des sous cat�gories, on propose de voir son contenu
				if( $sub_cats_number > 0 )
					$line .= '<li><a href="javascript:show_cat_contents(' . $key . ', ' . ($display_select_link != 0 ? 1 : 0) . ');"><img src="' . $template->module_data_path('pages') . '/images/plus.png" alt="" id="img2_' . $key . '" style="vertical-align:middle" /></a> <a href="javascript:show_cat_contents(' . $key . ', ' . ($display_select_link != 0 ? 1 : 0) . ');"><img src="' . $template->module_data_path('pages') . '/images/closed_cat.png" alt="" id="img_' . $key . '" style="vertical-align:middle" /></a>&nbsp;<span id="class_' . $key . '" class="' . ($key == $id ? 'pages_selected_cat' : '') . '"><a href="javascript:' . ($display_select_link != 0 ? 'select_cat' : 'open_cat') . '(' . $key . ');">' . $value['name'] . '</a></span><span id="cat_' . $key . '"></span></li>';
				else //Sinon on n'affiche pas le "+"
					$line .= '<li style="padding-left:17px;"><img src="' . $template->module_data_path('pages') . '/images/closed_cat.png" alt=""  style="vertical-align:middle" />&nbsp;<span id="class_' . $key . '" class="' . ($key == $id ? 'pages_selected_cat' : '') . '"><a href="javascript:' . ($display_select_link != 0 ? 'select_cat' : 'open_cat') . '(' . $key . ');">' . $value['name'] . '</a></span></li>';
			}
		}
	}
	return "\n" . $line;
}

//Fonction qui d�termine toutes les sous-cat�gories d'une cat�gorie (r�cursive)
function pages_find_subcats(&$array, $id_cat)
{
	global $_PAGES_CATS;
	//On parcourt les cat�gories et on d�termine les cat�gories filles
	foreach( $_PAGES_CATS as $key => $value )
	{
		if( $value['id_parent'] == $id_cat )
		{
			$array[] = $key;
			//On rappelle la fonction pour la cat�gorie fille
			pages_find_subcats($array, $key);
		}
	}
}

//G�n�ration d'une liste � s�lection multiple des rangs et groupes
function generate_select_groups($array_auth, $auth_id, $auth_level)
{
	global $array_groups, $array_ranks, $LANG;
	
	$j = 0;
	//Liste des rangs
	$select_groups = '<select id="groups_auth' . $auth_id . '" name="groups_auth' . $auth_id . '[]" size="8" multiple="multiple" onclick="document.getElementById(\'' . $auth_id . 'r3\').selected = true;"><optgroup label="' . $LANG['ranks'] . '">';
	foreach($array_ranks as $idgroup => $group_name)
	{
		$selected = '';	
		if( isset($array_auth['r' . $idgroup]) && ((int)$array_auth['r' . $idgroup] & (int)$auth_level) !== 0 )
			$selected = 'selected="selected"';
						
		$select_groups .=  '<option value="r' . $idgroup . '" id="' . $auth_id . 'r' . $j . '" ' . $selected . ' onclick="check_select_multiple_ranks(\'' . $auth_id . 'r\', ' . $j . ')">' . $group_name . '</option>';
		$j++;
	}
	$select_groups .=  '</optgroup>';
	
	//Liste des groupes.
	$j = 0;
	$select_groups .= '<optgroup label="' . $LANG['groups'] . '">';
	foreach($array_groups as $idgroup => $group_name)
	{
		$selected = '';		
		if( isset($array_auth[$idgroup]) && ((int)$array_auth[$idgroup] & (int)$auth_level) !== 0 )
			$selected = 'selected="selected"';

		$select_groups .= '<option value="' . $idgroup . '" id="' . $auth_id . 'g' . $j . '" ' . $selected . '>' . $group_name . '</option>';
		$j++;
	}
	$select_groups .= '</optgroup></select>';
	
	return $select_groups;
}

//Fonction "parse" pour les pages laissant passer le html tout en rempla�ant les caract�res sp�ciaux par leurs entit�s html correspondantes
function pages_parse($contents)
{
	$contents = preg_replace('`\[link=([a-z0-9+#-]+)\](.+)\[/link\]`isU', '<a href="$1">$2</a>', $contents);
	$contents = ' ' . trim($contents) . ' '; //Ajout des espaces multiples, et ajout d'espaces pour �viter l'absence de parsage lorsqu'un s�parateur de mot est �xig�.
	
	//D�but de la fonction parse en manuel car il faut ignorer " et ' dans htmlentities()
	
	//Protection des donn�es.	
	$contents = htmlentities($contents, ENT_NOQUOTES);
	$contents = strip_tags($contents);
	$contents = nl2br($contents);

	$contents = parse($contents, array(), false);
	$contents = htmlspecialchars_decode($contents, ENT_NOQUOTES);
	
	return (string) $contents;
}

//Fonction unparse
function pages_unparse($contents)
{
	$contents = link_unparse($contents);
	return html_entity_decode(unparse($contents));
}

//Second parse -> � l'affichage
function pages_second_parse($contents)
{
	global $CONFIG;
	//On unparse d'abord la balise link, sinon c'est url qui la prendra
	$contents = preg_replace_callback('`\[code\](.+)\[/code\]`isU', 'link_unparse', $contents);
	$contents = second_parse($contents);
	if( $CONFIG['rewrite'] == 0 ) //Pas de rewriting	
		return preg_replace('`<a href="([a-z0-9+#-]+)">(.*)</a>`sU', '<a href="pages.php?title=$1">$2</a>', $contents);
	else
		return $contents;
}

//On remplace la balise link
function link_unparse($contents)
{
	$contents = is_array($contents) ? $contents[0] : $contents;
	return preg_replace('`<a href="([a-z0-9+#-]+)">(.*)</a>`sU', "[link=$1]$2[/link]", $contents);
}

?>