<?php
/*##################################################
 *                               gallery.class.php
 *                            -------------------
 *   begin                : August 16, 2005
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

class Gallery
{	
	var $error = ''; //Gestion des erreurs.
	
	//Constructeur
	function gallery() 
	{
	}
	
	//Arguments de l'image, hauteur, largeur, extension.
	function arg_pics($path)
	{
		global $errorh, $LANG;
		
		//V�rification du chargement de la librairie GD.
		if( !@extension_loaded('gd') ) 
			$errorh->error_handler($LANG['e_no_gd'], E_USER_ERROR, __LINE__, __FILE__);
		
		if( function_exists('getimagesize') ) 
		{
			list($width, $height, $type) = @getimagesize($path);
			$weight = @filesize($path);
			$weight = !empty($weight) ? $weight : 0;			
			
			//On prepare les valeurs de remplacement, pour d�t�rminer le type de l'image.
			$array_type = array( 1 => 'gif', 2 => 'jpg', 3 => 'png');
			if( isset($array_type[$type]) )
				return array($width, $height, $weight, $array_type[$type]);
			else
				$this->error = 'e_unsupported_format';
		}
		else
			$errorh->error_handler($LANG['e_no_getimagesize'], E_USER_ERROR, __LINE__, __FILE__);
	}
	
	//Calcul des dimensions avec respect des proportions.
	function get_resize_properties($width_s, $height_s, $width_max = 0, $height_max = 0)
	{
		global $CONFIG_GALLERY;
		
		$width_max = ($width_max == 0) ? $CONFIG_GALLERY['width'] : $width_max;
		$height_max = ($height_max == 0) ? $CONFIG_GALLERY['height'] : $height_max;
		if( $width_s > $width_max || $height_s > $height_max ) 
		{
			if( $width_s > $height_s )
			{
				$ratio = $width_s / $height_s;
				$width = $width_max;
				$height = ceil($width / $ratio);
			}
			else
			{
				$ratio = $height_s / $width_s;
				$height = $height_max;
				$width = ceil($height / $ratio);
			}
		}
		else
		{
			$width = $width_s;
			$height = $height_s;
		}
		
		return array($width, $height);
	}
	
	//Redimensionnement
	function resize_pics($path, $width_max = 0, $height_max = 0)
	{
		global $LANG;
			
		if( file_exists($path) )
		{	
			list($width_s, $height_s, $weight, $ext) = $this->arg_pics($path);
			//Calcul des dimensions avec respect des proportions.
			list($width, $height) = $this->get_resize_properties($width_s, $height_s, $width_max, $height_max);
			
			$source = false;
			switch($ext) //Cr�ation de l'image suivant l'extension.
			{
				case 'jpg':
					$source = @imagecreatefromjpeg($path);
					break;
				case 'gif':
					$source = @imagecreatefromgif($path);
					break;
				case 'png':
					$source = @imagecreatefrompng($path);
					break;
				default: 
					$this->error = 'e_unsupported_format';
					$source = false;
			}
			
			if( !$source )
			{
				$path_mini = str_replace('pics', 'pics/thumbnails', $path);
				$this->create_pics_error($path_mini, $width, $height);	
				$this->error = 'e_unabled_create_pics';
			}
			else
			{
				//Pr�paration de l'image redimensionn�e.
				if( !function_exists('imagecreatetruecolor') )
				{	
					$thumbnail = @imagecreate($width, $height);
					if( $thumbnail === false )				
						$this->error = 'e_unabled_create_pics';
				}
				else
				{	
					$thumbnail = @imagecreatetruecolor($width, $height);
					if( $thumbnail === false )				
						$this->error = 'e_unabled_create_pics';
				}
				
				//Redimensionnement.
				if( !function_exists('imagecopyresampled') )
				{	
					if( @imagecopyresized($thumbnail, $source, 0, 0, 0, 0, $width, $height, $width_s, $height_s) === false )				
						$this->error = 'e_error_resize';
				}
				else
				{	
					if( @imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $width_s, $height_s) === false )				
						$this->error = 'e_error_resize';
				}
			}
			
			//Cr�ation de l'image.
			if( empty($this->error) )
				$this->create_pics($thumbnail, $source, $path, $ext);
		}
		else
		{
			$path_mini = str_replace('pics', 'pics/thumbnails', $path);
			$this->create_pics_error($path_mini, $width_max, $height_max);	
			$this->error = 'e_unabled_create_pics';
		}
	}
	
	//Cr�ation de l'image.
	function create_pics($thumbnail, $source, $path, $ext)
	{
		global $CONFIG_GALLERY;
		
		$path_mini = str_replace('pics', 'pics/thumbnails', $path);
		if( function_exists('imagegif') && $ext === 'gif' ) 
			imagegif($thumbnail, $path_mini);
		elseif( function_exists('imagejpeg') && $ext === 'jpg' ) 
			imagejpeg($thumbnail, $path_mini, $CONFIG_GALLERY['quality']);
		elseif( function_exists('imagepng')  && $ext === 'png' ) 
			imagepng($thumbnail, $path_mini);
		else 
			$this->error = 'e_no_graphic_support';

		switch($ext) //Cr�ation de l'image suivant l'extension.
		{
			case 'jpg':
				@imagejpeg($source, $path);
				break;
			case 'gif':
				@imagegif($source, $path);
				break;
			case 'png':
				@imagepng($source, $path);
				break;
			default: 
				$this->error = 'e_no_graphic_support';
		}
	}
		
	//Cr�ation de l'image d'erreur
	function create_pics_error($path, $width, $height)
	{
		global $CONFIG_GALLERY, $LANG; 
		
		$width = ($width == 0) ? $CONFIG_GALLERY['width'] : $width;
		$height = ($height == 0) ? $CONFIG_GALLERY['height'] : $height;
			
		$font = '../includes/data/fonts/impact.ttf';		
		$font_size = 12;

		$thumbnail = @imagecreate($width, $height);
		if( $thumbnail === false )				
			$this->error = 'e_unabled_create_pics';
		$background = @imagecolorallocate($thumbnail, 255, 255, 255);
		$text_color = @imagecolorallocate($thumbnail, 0, 0, 0);

		//Centrage du texte.	
		$array_size_ttf = imagettfbbox($font_size, 0, $font, $LANG['e_error_img']);
		$text_width = abs($array_size_ttf[2] - $array_size_ttf[0]);
		$text_height = abs($array_size_ttf[7] - $array_size_ttf[1]);
		$text_x = ($width/2) - ($text_width/2);
		$text_y = ($height/2) + ($text_height/2);

		//Ecriture du code.
		imagettftext($thumbnail, $font_size, 0, $text_x, $text_y, $text_color, $font, $LANG['e_error_img']);
		@imagejpeg($thumbnail, $path, 75);
	}

	//Incrustation du logo (possible en transparent si jpg).
	function incrust_pics($path)
	{
		global $CONFIG_GALLERY, $LANG;
		
		if( $CONFIG_GALLERY['activ_logo'] == '1' && is_file($CONFIG_GALLERY['logo']) ) //Incrustation du logo.
		{
			list($width_s, $height_s, $weight_s, $ext_s) = $this->arg_pics($CONFIG_GALLERY['logo']);
			list($width, $height, $weight, $ext) = $this->arg_pics($path);
			
			if( $width_s <= $width && $height_s <= $height )
			{
				switch($ext_s) //Cr�ation de l'image suivant l'extension.
				{
					case 'jpg':
						$source = @imagecreatefromjpeg($CONFIG_GALLERY['logo']);
						break;
					case 'gif':
						$source = @imagecreatefromgif($CONFIG_GALLERY['logo']);
						break;
					case 'png':
						$source = @imagecreatefrompng($CONFIG_GALLERY['logo']);
						break;
					default: 
						$this->error = 'e_unsupported_format';
						$source = false;
				}
				
				if( !$source )
				{
					$path_mini = str_replace('pics', 'pics/thumbnails', $path);
					list($width_mini, $height_mini, $weight_mini, $ext_mini) = $this->arg_pics($path_mini);
					$this->create_pics_error($path_mini, $width_mini, $height_mini);	
					$this->error = 'e_unabled_create_pics';
				}
				else
				{
					switch($ext) //Cr�ation de l'image suivant l'extension.
					{
						case 'jpg':
							$destination = @imagecreatefromjpeg($path);
							break;
						case 'gif':
							$destination = @imagecreatefromgif($path);
							break;
						case 'png':
							$destination = @imagecreatefrompng($path);
							break;
						default: 
							$this->error = 'e_unsupported_format';
					}
					
					if( function_exists('imagecopymerge') )
					{
						// On veut placer le logo en bas � droite, on calcule les coordonn�es o� on doit placer le logo sur la photo
						$destination_x = $width - $width_s - $CONFIG_GALLERY['d_width'];
						$destination_y =  $height - $height_s - $CONFIG_GALLERY['d_height'];
						
						if( @imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $width_s, $height_s, (100 - $CONFIG_GALLERY['trans'])) === false )
							$this->error = 'e_unabled_incrust_logo';
							
						switch($ext) //Cr�ation de l'image suivant l'extension.
						{
							case 'jpg':
								imagejpeg($destination);
								break;
							case 'gif':
								imagegif($destination);
								break;
							case 'png':
								imagepng($destination);
								break;
							default: 
								$this->error = 'e_unabled_create_pics';
						}
					}
					else
						$this->error = 'e_unabled_incrust_logo';
				}
			}
			else
				readfile($path); //On affiche simplement.
		}
		else
			readfile($path); //On affiche simplement.
	}
	
	//Insertion base de donn�e
	function add_pics($idcat, $name, $path, $user_id)
	{
		global $CAT_GALLERY, $sql;
		
		$CAT_GALLERY[0]['id_left'] = 0;
		$CAT_GALLERY[0]['id_right'] = 0;
		
		//Parent de la cat�gorie cible
		$list_parent_cats_to = '';
		$result = $sql->query_while("SELECT id 
		FROM ".PREFIX."gallery_cats 
		WHERE id_left <= '" . $CAT_GALLERY[$idcat]['id_left'] . "' AND id_right >= '" . $CAT_GALLERY[$idcat]['id_right'] . "'", __LINE__, __FILE__);
		while( $row = $sql->sql_fetch_assoc($result) )
		{
			$list_parent_cats_to .= $row['id'] . ', ';
		}
		$sql->close($result);
		$list_parent_cats_to = trim($list_parent_cats_to, ', ');
		
		if( empty($list_parent_cats_to) )
			$clause_parent_cats_to = " id = '" . $idcat . "'";
		else
			$clause_parent_cats_to = " id IN (" . $list_parent_cats_to . ")";
		
		$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_aprob = nbr_pics_aprob + 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);		
		
		list($width, $height, $weight, $ext) = $this->arg_pics('pics/' . $path);	
		$sql->query_inject("INSERT INTO ".PREFIX."gallery (idcat, name, path, width, height, weight, user_id, aprob, views, timestamp, users_note, nbrnote, note, nbr_com) VALUES('" . $idcat . "', '" . $name . "', '" . $path . "', '" . $width . "', '" . $height . "', '" . $weight ."', '" . $user_id . "', 1, 0, '" . time() . "', '', 0, 0, 0)", __LINE__, __FILE__);
		
		return $sql->sql_insert_id("SELECT MAX(id) FROM ".PREFIX."gallery");
	}
	
	//Supprime une image
	function del_pics($id_pics)
	{
		global $CAT_GALLERY, $sql;
		
		$CAT_GALLERY[0]['id_left'] = 0;
		$CAT_GALLERY[0]['id_right'] = 0;
		
		$info_pics = $sql->query_array("gallery", "path", "idcat", "aprob", "WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		if( !empty($info_pics['path']) )
		{
			$sql->query_inject("DELETE FROM ".PREFIX."gallery WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);	
		
			//Parent de la cat�gorie cible
			$list_parent_cats_to = '';
			$result = $sql->query_while("SELECT id 
			FROM ".PREFIX."gallery_cats 
			WHERE id_left <= '" . $CAT_GALLERY[$info_pics['idcat']]['id_left'] . "' AND id_right >= '" . $CAT_GALLERY[$info_pics['idcat']]['id_right'] . "'", __LINE__, __FILE__);
			while( $row = $sql->sql_fetch_assoc($result) )
			{
				$list_parent_cats_to .= $row['id'] . ', ';
			}
			$sql->close($result);
			$list_parent_cats_to = trim($list_parent_cats_to, ', ');
			
			if( empty($list_parent_cats_to) )
				$clause_parent_cats_to = " id = '" . $info_pics['idcat'] . "'";
			else
				$clause_parent_cats_to = " id IN (" . $list_parent_cats_to . ")";
				
			if( $info_pics['aprob'] )
				$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_aprob = nbr_pics_aprob - 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
			else
				$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_unaprob = nbr_pics_unaprob - 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
		}
		
		//Suppression physique.
		delete_file('pics/' . $info_pics['path']);
		delete_file('pics/thumbnails/' . $info_pics['path']);
	}
	
	//Renomme une image.
	function rename_pics($id_pics, $name, $previous_name)
	{
		global $sql;
		
		$sql->query_inject("UPDATE ".PREFIX."gallery SET name = '" . $name . "' WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		return stripslashes((strlen(html_entity_decode($name)) > 22) ? htmlentities(substr(html_entity_decode($name), 0, 22)) . '...' : $name);
	}
	
	//Approuve une image.
	function aprob_pics($id_pics)
	{
		global $CAT_GALLERY, $sql;
		
		$CAT_GALLERY[0]['id_left'] = 0;
		$CAT_GALLERY[0]['id_right'] = 0;
		
		$idcat = $sql->query("SELECT idcat FROM ".PREFIX."gallery WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		//Parent de la cat�gorie cible
		$list_parent_cats_to = '';
		$result = $sql->query_while("SELECT id 
		FROM ".PREFIX."gallery_cats 
		WHERE id_left <= '" . $CAT_GALLERY[$idcat]['id_left'] . "' AND id_right >= '" . $CAT_GALLERY[$idcat]['id_right'] . "'", __LINE__, __FILE__);
		while( $row = $sql->sql_fetch_assoc($result) )
		{
			$list_parent_cats_to .= $row['id'] . ', ';
		}
		$sql->close($result);
		$list_parent_cats_to = trim($list_parent_cats_to, ', ');
		
		if( empty($list_parent_cats_to) )
			$clause_parent_cats_to = " id = '" . $idcat . "'";
		else
			$clause_parent_cats_to = " id IN (" . $list_parent_cats_to . ")";
			
		$aprob = $sql->query("SELECT aprob FROM ".PREFIX."gallery WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		if( $aprob )
		{	
			$sql->query_inject("UPDATE ".PREFIX."gallery SET aprob = 0 WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_unaprob = nbr_pics_unaprob + 1, nbr_pics_aprob = nbr_pics_aprob - 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
		}
		else
		{
			$sql->query_inject("UPDATE ".PREFIX."gallery SET aprob = 1 WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_unaprob = nbr_pics_unaprob - 1, nbr_pics_aprob = nbr_pics_aprob + 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
		}
		
		return $aprob;
	}
	
	//D�placement d'une image.
	function move_pics($id_pics, $id_move)
	{
		global $CAT_GALLERY, $sql;
		
		$idcat = $sql->query("SELECT idcat FROM ".PREFIX."gallery WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		if( empty($idcat) )
		{
			$CAT_GALLERY[$idcat]['id_left'] = 0;
			$CAT_GALLERY[$idcat]['id_right'] = 0;
		}
		
		//Parent de la cat�gorie parente
		$list_parent_cats = '';
		$result = $sql->query_while("SELECT id 
		FROM ".PREFIX."gallery_cats 
		WHERE id_left <= '" . $CAT_GALLERY[$idcat]['id_left'] . "' AND id_right >= '" . $CAT_GALLERY[$idcat]['id_right'] . "'", __LINE__, __FILE__);
		while( $row = $sql->sql_fetch_assoc($result) )
		{
			$list_parent_cats .= $row['id'] . ', ';
		}
		$sql->close($result);
		$list_parent_cats = trim($list_parent_cats, ', ');
		
		if( empty($list_parent_cats) )
			$clause_parent_cats = " id = '" . $idcat . "'";
		else
			$clause_parent_cats = " id IN (" . $list_parent_cats . ")";
		
		//Parent de la cat�gorie cible
		$list_parent_cats_to = '';
		$result = $sql->query_while("SELECT id 
		FROM ".PREFIX."gallery_cats 
		WHERE id_left <= '" . $CAT_GALLERY[$id_move]['id_left'] . "' AND id_right >= '" . $CAT_GALLERY[$id_move]['id_right'] . "'", __LINE__, __FILE__);
		while( $row = $sql->sql_fetch_assoc($result) )
		{
			$list_parent_cats_to .= $row['id'] . ', ';
		}
		$sql->close($result);
		$list_parent_cats_to = trim($list_parent_cats_to, ', ');
		
		if( empty($list_parent_cats_to) )
			$clause_parent_cats_to = " id = '" . $id_move . "'";
		else
			$clause_parent_cats_to = " id IN (" . $list_parent_cats_to . ")";
			
		$aprob = $sql->query("SELECT aprob FROM ".PREFIX."gallery WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		
		if( $aprob )
		{	
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_aprob = nbr_pics_aprob - 1 WHERE " . $clause_parent_cats, __LINE__, __FILE__);
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_aprob = nbr_pics_aprob + 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
		}
		else
		{
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_unaprob = nbr_pics_unaprob - 1 WHERE " . $clause_parent_cats, __LINE__, __FILE__);
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_unaprob = nbr_pics_unaprob + 1 WHERE " . $clause_parent_cats_to, __LINE__, __FILE__);
		}
		$sql->query_inject("UPDATE ".PREFIX."gallery SET idcat = '" . $id_move . "' WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
	}
	
	//Note une image.
	function note_pics($id_pics, $note, $user_id)
	{
		global $sql;
		
		$info_pics = $sql->query_array('gallery', 'id', 'users_note', 'nbrnote', 'note', "WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
		if( !in_array($user_id, explode('/', $info_pics['users_note'])) && !empty($info_pics['id']) )
		{			
			$note = (($info_pics['note'] * $info_pics['nbrnote']) + $note)/($info_pics['nbrnote'] + 1);			
			$users_note = !empty($info_pics['users_note']) ? $info_pics['users_note'] . '/' . $user_id : $user_id; //On ajoute l'id de l'utilisateur.
			
			$sql->query_inject("UPDATE ".PREFIX."gallery SET note = '" . $note . "', nbrnote = nbrnote + 1, users_note = '" . $users_note . "' WHERE id = '" . $id_pics . "'", __LINE__, __FILE__);
			return 'get_note = ' . $note . ';get_nbrnote = ' . ($info_pics['nbrnote']+1) . ';';
		}
		else	
			return -1;
	}
	
	//V�rifie si le membre peut uploader une image
	function auth_upload_pics($user_id, $level)
	{
		global $CONFIG_GALLERY;
		
		switch( $level )
		{
			case 2:
			$pics_quota = 10000;
			break;
			case 1:
			$pics_quota = $CONFIG_GALLERY['limit_modo'];
			break;
			default:
			$pics_quota = $CONFIG_GALLERY['limit_member'];
		}

		if( $this->get_nbr_upload_pics($user_id) >= $pics_quota )
			return false;
			
		return true;
	}
	
	//Compte le nombre d'images upload�e par un membre.
	function get_nbr_upload_pics($user_id)
	{
		global $sql;
		
		return $sql->query("SELECT COUNT(*) FROM ".PREFIX."gallery WHERE user_id = '" . $user_id . "'", __LINE__, __FILE__);
	}
	
	//Header image.
	function send_header($ext)
	{
		global $LANG;
		
		switch( $ext )
		{
			case 'png':
				$header = header('Content-type: image/png');
				break;
			case 'gif':
				$header = header('Content-type: image/gif');
				break;
			case 'jpg':
				$header = header('Content-type: image/jpeg');
				break;
			default:
				$header = '';
				$this->error = $LANG['e_unable_display_pics'];
		}
		return $header;
	}
	
	//Recompte le nombre d'images de chaque cat�gories
	function count_cat_pics()
	{
		global $CAT_GALLERY, $sql;
		
		$CAT_GALLERY[0]['id_left'] = 0;
		$CAT_GALLERY[0]['id_right'] = 0;
		
		$info_cat = array();
		$result = $sql->query_while("SELECT idcat, COUNT(*) as nbr_pics_aprob 
		FROM ".PREFIX."gallery 
		WHERE aprob = 1 AND idcat > 0
		GROUP BY idcat", __LINE__, __FILE__);
		while($row = $sql->sql_fetch_assoc($result) )
			$info_cat[$row['idcat']]['aprob'] = $row['nbr_pics_aprob'];
		$sql->close($result);
		
		$result = $sql->query_while("SELECT idcat, COUNT(*) as nbr_pics_unaprob 
		FROM ".PREFIX."gallery 
		WHERE aprob = 0 AND idcat > 0
		GROUP BY idcat", __LINE__, __FILE__);
		while($row = $sql->sql_fetch_assoc($result) )
			$info_cat[$row['idcat']]['unaprob'] = $row['nbr_pics_unaprob'];
		$sql->close($result);
		
		$result = $sql->query_while("SELECT id, id_left, id_right
		FROM ".PREFIX."gallery_cats", __LINE__, __FILE__);
		while($row = $sql->sql_fetch_assoc($result) )
		{			
			$nbr_pics_aprob = 0;
			$nbr_pics_unaprob = 0;
			foreach($info_cat as $key => $value)
			{			
				if( $CAT_GALLERY[$key]['id_left'] >= $row['id_left'] && $CAT_GALLERY[$key]['id_right'] <= $row['id_right'] )
				{	
					$nbr_pics_aprob += isset($info_cat[$key]['aprob']) ? $info_cat[$key]['aprob'] : 0;
					$nbr_pics_unaprob += isset($info_cat[$key]['unaprob']) ? $info_cat[$key]['unaprob'] : 0; 
				}
			}
			$sql->query_inject("UPDATE ".PREFIX."gallery_cats SET nbr_pics_aprob = '" . $nbr_pics_aprob . "', nbr_pics_unaprob = '" . $nbr_pics_unaprob . "' WHERE id = '" . $row['id'] . "'", __LINE__, __FILE__);	
		}
		$sql->close($result);
	}
	
	//Vidange des miniatures du FTP et de la bdd => r�g�n�r�e plus tard lors des affichages..
	function clear_cache()
	{
		$dir = 'pics/thumbnails/';
		if( is_dir($dir) ) //Si le dossier existe
		{		
			$j = 0;
			$array_pics = array();
			$dh = @opendir($dir);
			while( !is_bool($pics = readdir($dh)) )
			{	
				if( $j > 1 && $pics != 'index.php' && $pics != 'Thumbs.db' )
					$array_pics[] = $pics; //On cr�e un array, avec les different fichiers.
				$j++;
			}	
			@closedir($dh); //On ferme le dossier
			
			foreach($array_pics as  $key => $pics)
				$this->delete_file($dir . $pics);
		}
	}
	
	//Suppression d'une image.
	function delete_file($path)
	{
		if( function_exists('unlink') )
			return @unlink($path); //On supprime le fichier.
		else //Fonction d�sactiv�e.	
		{	
			$this->error = 'e_delete_thumbnails';
			return false;
		}		
	}
}
?>