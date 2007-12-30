		<div id="admin_quick_menu">
			<ul>
				<li class="title_menu"><?php echo isset($this->_var['L_ARTICLES_MANAGEMENT']) ? $this->_var['L_ARTICLES_MANAGEMENT'] : ''; ?></li>
				<li>
					<a href="admin_articles.php"><img src="articles.png" alt="" /></a>
					<br />
					<a href="admin_articles.php" class="quick_link"><?php echo isset($this->_var['L_ARTICLES_MANAGEMENT']) ? $this->_var['L_ARTICLES_MANAGEMENT'] : ''; ?></a>
				</li>
				<li>
					<a href="admin_articles_add.php"><img src="articles.png" alt="" /></a>
					<br />
					<a href="admin_articles_add.php" class="quick_link"><?php echo isset($this->_var['L_ARTICLES_ADD']) ? $this->_var['L_ARTICLES_ADD'] : ''; ?></a>
				</li>
				<li>
					<a href="admin_articles_cat.php"><img src="articles.png" alt="" /></a>
					<br />
					<a href="admin_articles_cat.php" class="quick_link"><?php echo isset($this->_var['L_ARTICLES_CAT']) ? $this->_var['L_ARTICLES_CAT'] : ''; ?></a>
				</li>
				<li>
					<a href="admin_articles_cat_add.php"><img src="articles.png" alt="" /></a>
					<br />
					<a href="admin_articles_cat_add.php" class="quick_link"><?php echo isset($this->_var['L_ARTICLES_CAT_ADD']) ? $this->_var['L_ARTICLES_CAT_ADD'] : ''; ?></a>
				</li>
				<li>
					<a href="admin_articles_config.php"><img src="articles.png" alt="" /></a>
					<br />
					<a href="admin_articles_config.php" class="quick_link"><?php echo isset($this->_var['L_ARTICLES_CONFIG']) ? $this->_var['L_ARTICLES_CONFIG'] : ''; ?></a>
				</li>
			</ul>
		</div>
		
		<div id="admin_contents">

			<?php if( !isset($this->_block['list']) || !is_array($this->_block['list']) ) $this->_block['list'] = array();
foreach($this->_block['list'] as $list_key => $list_value) {
$_tmpb_list = &$this->_block['list'][$list_key]; ?>
			<script type="text/javascript">
			<!--
			function Confirm() {
			return confirm("<?php echo isset($this->_var['L_CONFIRM_DEL_ARTICLE']) ? $this->_var['L_CONFIRM_DEL_ARTICLE'] : ''; ?>");
			}
			-->
			</script>
			<table  class="module_table">
				<tr style="text-align:center;">
					<th style="width:35%">
						<?php echo isset($this->_var['L_TITLE']) ? $this->_var['L_TITLE'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_CATEGORY']) ? $this->_var['L_CATEGORY'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_PSEUDO']) ? $this->_var['L_PSEUDO'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_DATE']) ? $this->_var['L_DATE'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_APROB']) ? $this->_var['L_APROB'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_UPDATE']) ? $this->_var['L_UPDATE'] : ''; ?>
					</th>
					<th>
						<?php echo isset($this->_var['L_DELETE']) ? $this->_var['L_DELETE'] : ''; ?>
					</th>
				</tr>
				
				<?php if( !isset($_tmpb_list['articles']) || !is_array($_tmpb_list['articles']) ) $_tmpb_list['articles'] = array();
foreach($_tmpb_list['articles'] as $articles_key => $articles_value) {
$_tmpb_articles = &$_tmpb_list['articles'][$articles_key]; ?>
				<tr style="text-align:center;"> 
					<td class="row2">
						<a href="../articles/articles.php?cat=<?php echo isset($_tmpb_articles['IDCAT']) ? $_tmpb_articles['IDCAT'] : ''; ?>&amp;id=<?php echo isset($_tmpb_articles['ID']) ? $_tmpb_articles['ID'] : ''; ?>"><?php echo isset($_tmpb_articles['TITLE']) ? $_tmpb_articles['TITLE'] : ''; ?></a>
					</td>
					<td class="row2"> 
						<?php echo isset($_tmpb_articles['U_CAT']) ? $_tmpb_articles['U_CAT'] : ''; ?>
					</td>
					<td class="row2"> 
						<?php echo isset($_tmpb_articles['PSEUDO']) ? $_tmpb_articles['PSEUDO'] : ''; ?>
					</td>		
					<td class="row2">
						<?php echo isset($_tmpb_articles['DATE']) ? $_tmpb_articles['DATE'] : ''; ?>
					</td>
					<td class="row2">
						<?php echo isset($_tmpb_articles['APROBATION']) ? $_tmpb_articles['APROBATION'] : ''; ?> 
						<br />
						<span class="text_small"><?php echo isset($_tmpb_articles['VISIBLE']) ? $_tmpb_articles['VISIBLE'] : ''; ?></span>
					</td>
					<td class="row2"> 
						<a href="admin_articles.php?id=<?php echo isset($_tmpb_articles['ID']) ? $_tmpb_articles['ID'] : ''; ?>"><img src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/<?php echo isset($this->_var['LANG']) ? $this->_var['LANG'] : ''; ?>/edit.png" alt="<?php echo isset($this->_var['L_UPDATE']) ? $this->_var['L_UPDATE'] : ''; ?>" title="<?php echo isset($this->_var['L_UPDATE']) ? $this->_var['L_UPDATE'] : ''; ?>" /></a>
					</td>
					<td class="row2">
						<a href="admin_articles.php?delete=1&amp;id=<?php echo isset($_tmpb_articles['ID']) ? $_tmpb_articles['ID'] : ''; ?>" onClick="javascript:return Confirm();"><img src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/<?php echo isset($this->_var['LANG']) ? $this->_var['LANG'] : ''; ?>/delete.png" alt="<?php echo isset($this->_var['L_DELETE']) ? $this->_var['L_DELETE'] : ''; ?>" title="<?php echo isset($this->_var['L_DELETE']) ? $this->_var['L_DELETE'] : ''; ?>" /></a>
					</td>
				</tr>
				<?php } ?>

			</table>

			<br /><br />
			<p style="text-align: center;"><?php echo isset($this->_var['PAGINATION']) ? $this->_var['PAGINATION'] : ''; ?></p>
			<?php } ?>


			<?php if( !isset($this->_block['articles']) || !is_array($this->_block['articles']) ) $this->_block['articles'] = array();
foreach($this->_block['articles'] as $articles_key => $articles_value) {
$_tmpb_articles = &$this->_block['articles'][$articles_key]; ?>
			<script type="text/javascript" src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/calendar.js"></script>
			<script type="text/javascript">
			<!--
			function check_form()
			{
				if(document.getElementById('title').value == "") {
					alert("<?php echo isset($this->_var['L_REQUIRE_TITLE']) ? $this->_var['L_REQUIRE_TITLE'] : ''; ?>");
					return false;
				}
				if(document.getElementById('idcat').value == "") {
					alert("<?php echo isset($this->_var['L_REQUIRE_CAT']) ? $this->_var['L_REQUIRE_CAT'] : ''; ?>");
					return false;
				}
				if(document.getElementById('contents').value == "") {
					alert("<?php echo isset($this->_var['L_REQUIRE_TEXT']) ? $this->_var['L_REQUIRE_TEXT'] : ''; ?>");
					return false;
				}
				return true;
			}
			function change_icon(img_path)
			{
				document.getElementById('icon_img').innerHTML = '<img src="' + img_path + '" alt="" class="valign_middle" />';
			}
			-->
			</script>
						
			<?php if( !isset($_tmpb_articles['preview']) || !is_array($_tmpb_articles['preview']) ) $_tmpb_articles['preview'] = array();
foreach($_tmpb_articles['preview'] as $preview_key => $preview_value) {
$_tmpb_preview = &$_tmpb_articles['preview'][$preview_key]; ?>
			<table class="module_table">
					<tr> 
						<th colspan="2">
							<?php echo isset($this->_var['L_PREVIEW']) ? $this->_var['L_PREVIEW'] : ''; ?>
						</th>
					</tr>
					<tr> 
						<td>
							<br />
							<div class="module_position">					
								<div class="module_top_l"></div>		
								<div class="module_top_r"></div>
								<div class="module_top">
									<div style="float:left">
										<?php echo isset($_tmpb_preview['TITLE']) ? $_tmpb_preview['TITLE'] : ''; ?>
									</div>
									<div style="float:right">
										<?php echo isset($this->_var['L_COM']) ? $this->_var['L_COM'] : ''; ?> (0)
									</div>
								</div>
								<div class="module_contents">
									<?php echo isset($_tmpb_preview['CONTENTS']) ? $_tmpb_preview['CONTENTS'] : ''; ?>
								</div>
								<div class="module_bottom_l"></div>		
								<div class="module_bottom_r"></div>
								<div class="module_bottom">
									<div style="float:left" class="text_small">
										&nbsp;
									</div>
									<div style="float:right" class="text_small">
										<?php echo isset($this->_var['L_WRITTEN_BY']) ? $this->_var['L_WRITTEN_BY'] : ''; ?>: <?php echo isset($_tmpb_preview['PSEUDO']) ? $_tmpb_preview['PSEUDO'] : ''; ?>, <?php echo isset($this->_var['L_ON']) ? $this->_var['L_ON'] : ''; ?>: <?php echo isset($_tmpb_preview['DATE']) ? $_tmpb_preview['DATE'] : ''; ?>
									</div>
								</div>
							</div>
							<br />
						</td>
					</tr>
			</table>	
			<br /><br /><br />
			<?php } ?>

			<?php if( !isset($_tmpb_articles['error_handler']) || !is_array($_tmpb_articles['error_handler']) ) $_tmpb_articles['error_handler'] = array();
foreach($_tmpb_articles['error_handler'] as $error_handler_key => $error_handler_value) {
$_tmpb_error_handler = &$_tmpb_articles['error_handler'][$error_handler_key]; ?>
			<div class="error_handler_position">
					<span id="errorh"></span>
					<div class="<?php echo isset($_tmpb_error_handler['CLASS']) ? $_tmpb_error_handler['CLASS'] : ''; ?>" style="width:500px;margin:auto;padding:15px;">
						<img src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/<?php echo isset($_tmpb_error_handler['IMG']) ? $_tmpb_error_handler['IMG'] : ''; ?>.png" alt="" style="float:left;padding-right:6px;" /> <?php echo isset($_tmpb_error_handler['L_ERROR']) ? $_tmpb_error_handler['L_ERROR'] : ''; ?>
						<br />	
					</div>
			</div>
			<?php } ?>
			
			<form action="admin_articles.php" name="form" method="post" onsubmit="return check_form();" class="fieldset_content">
				<fieldset>
					<legend><?php echo isset($this->_var['L_EDIT_ARTICLE']) ? $this->_var['L_EDIT_ARTICLE'] : ''; ?></legend>
					<p><?php echo isset($this->_var['L_REQUIRE']) ? $this->_var['L_REQUIRE'] : ''; ?></p>
					<dl>
						<dt><label for="title">* <?php echo isset($this->_var['L_TITLE']) ? $this->_var['L_TITLE'] : ''; ?></label></dt>
						<dd><label><input type="text" size="65" maxlength="100" id="title" name="title" value="<?php echo isset($_tmpb_articles['TITLE']) ? $_tmpb_articles['TITLE'] : ''; ?>" class="text" /></label></dd>
					</dl>
					<dl>
						<dt><label for="idcat">* <?php echo isset($this->_var['L_CATEGORY']) ? $this->_var['L_CATEGORY'] : ''; ?></label></dt>
						<dd><label>
							<select id="idcat" name="idcat">				
								<?php echo isset($_tmpb_articles['CATEGORIES']) ? $_tmpb_articles['CATEGORIES'] : ''; ?>		
							</select>
						</label></dd>
					</dl>
					<dl>
						<dt><label for="icon"><?php echo isset($this->_var['L_ARTICLE_ICON']) ? $this->_var['L_ARTICLE_ICON'] : ''; ?></label></dt>
						<dd><label>
							<select name="icon" id="icon" onchange="change_icon(this.options[this.selectedIndex].value)" onclick="change_icon(this.options[this.selectedIndex].value)">
								<?php echo isset($_tmpb_articles['IMG_LIST']) ? $_tmpb_articles['IMG_LIST'] : ''; ?>
							</select>
							<span id="icon_img"><?php echo isset($_tmpb_articles['IMG_ICON']) ? $_tmpb_articles['IMG_ICON'] : ''; ?></span>
							<br />
							<span class="text_small"><?php echo isset($this->_var['L_OR_DIRECT_PATH']) ? $this->_var['L_OR_DIRECT_PATH'] : ''; ?></span> <input type="text" class="text" name="icon_path" value="<?php echo isset($_tmpb_articles['IMG_PATH']) ? $_tmpb_articles['IMG_PATH'] : ''; ?>" onblur="if( this.value != '' )change_icon(this.value)" />
						</label></dd>
					</dl>
					<br />
					<label for="contents">* <?php echo isset($this->_var['L_TEXT']) ? $this->_var['L_TEXT'] : ''; ?></label>
					<label>
						<?php $this->tpl_include('handle_bbcode'); ?>
						<textarea type="text" rows="30" cols="90" id="contents" name="contents"><?php echo isset($_tmpb_articles['CONTENTS']) ? $_tmpb_articles['CONTENTS'] : ''; ?></textarea> 
						<p style="text-align:center;"><?php echo isset($this->_var['L_EXPLAIN_PAGE']) ? $this->_var['L_EXPLAIN_PAGE'] : ''; ?></p>
					</label>
					<br />
					<dl class="overflow_visible">
						<dt><label for="release_date">* <?php echo isset($this->_var['L_RELEASE_DATE']) ? $this->_var['L_RELEASE_DATE'] : ''; ?></label></dt>
						<dd>
							<label><input type="radio" value="2" name="visible" <?php echo isset($_tmpb_articles['VISIBLE_WAITING']) ? $_tmpb_articles['VISIBLE_WAITING'] : ''; ?> />
							<input type="text" size="8" maxlength="8" id="start" name="start" value="<?php echo isset($_tmpb_articles['START']) ? $_tmpb_articles['START'] : ''; ?>" class="text" />					
							<div style="position:relative;z-index:100;top:6px;float:left;display:none;" id="calendar1">
								<div id="start_date" class="calendar_block" style="width:204px;" onmouseover="hide_calendar(1, 1);" onmouseout="hide_calendar(1, 0);">							
								</div>
							</div>
							<a onClick="xmlhttprequest_calendar('start_date', '?input_field=start&amp;field=start_date&amp;d=<?php echo isset($_tmpb_articles['DAY_RELEASE_S']) ? $_tmpb_articles['DAY_RELEASE_S'] : ''; ?>&amp;m=<?php echo isset($_tmpb_articles['MONTH_RELEASE_S']) ? $_tmpb_articles['MONTH_RELEASE_S'] : ''; ?>&amp;y=<?php echo isset($_tmpb_articles['YEAR_RELEASE_S']) ? $_tmpb_articles['YEAR_RELEASE_S'] : ''; ?>');display_calendar(1);" onmouseover="hide_calendar(1, 1);" onmouseout="hide_calendar(1, 0);" style="cursor:pointer;"><img style="vertical-align:middle;" src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/calendar.png" alt="" /></a>
							
							<?php echo isset($this->_var['L_UNTIL']) ? $this->_var['L_UNTIL'] : ''; ?>&nbsp;
							
							<input type="text" size="8" maxlength="8" id="end" name="end" value="<?php echo isset($_tmpb_articles['END']) ? $_tmpb_articles['END'] : ''; ?>" class="text" /> 					
							<div style="position:relative;z-index:100;top:6px;margin-left:155px;float:left;display:none;" id="calendar2">
								<div id="end_date" class="calendar_block" style="width:204px;" onmouseover="hide_calendar(2, 1);" onmouseout="hide_calendar(2, 0);">							
								</div>
							</div>
							<a onClick="xmlhttprequest_calendar('end_date', '?input_field=end&amp;field=end_date&amp;d=<?php echo isset($_tmpb_articles['DAY_RELEASE_E']) ? $_tmpb_articles['DAY_RELEASE_E'] : ''; ?>&amp;m=<?php echo isset($_tmpb_articles['MONTH_RELEASE_E']) ? $_tmpb_articles['MONTH_RELEASE_E'] : ''; ?>&amp;y=<?php echo isset($_tmpb_articles['YEAR_RELEASE_E']) ? $_tmpb_articles['YEAR_RELEASE_E'] : ''; ?>');display_calendar(2);" onmouseover="hide_calendar(2, 1);" onmouseout="hide_calendar(2, 0);" style="cursor:pointer;"><img style="vertical-align:middle;" src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/calendar.png" alt="" /></a></label>
							<br />
							<label><input type="radio" id="release_date" value="1" name="visible" <?php echo isset($_tmpb_articles['VISIBLE_ENABLED']) ? $_tmpb_articles['VISIBLE_ENABLED'] : ''; ?> /> <?php echo isset($this->_var['L_IMMEDIATE']) ? $this->_var['L_IMMEDIATE'] : ''; ?></label></label>
							<br />
							<label><input type="radio" value="0" name="visible" <?php echo isset($_tmpb_articles['VISIBLE_UNAPROB']) ? $_tmpb_articles['VISIBLE_UNAPROB'] : ''; ?> /> <?php echo isset($this->_var['L_UNAPROB']) ? $this->_var['L_UNAPROB'] : ''; ?></label>
						</dd>
					</dl>
					<dl class="overflow_visible">
						<dt><label for="current_date">* <?php echo isset($this->_var['L_ARTICLES_DATE']) ? $this->_var['L_ARTICLES_DATE'] : ''; ?></label></dt>
						<dd><label>
							<input type="text" size="8" maxlength="8" id="current_date" name="current_date" value="<?php echo isset($_tmpb_articles['CURRENT_DATE']) ? $_tmpb_articles['CURRENT_DATE'] : ''; ?>" class="text" /> 
							<div style="position:relative;z-index:100;top:6px;float:left;display:none;" id="calendar3">
								<div id="current" class="calendar_block" style="width:204px;" onmouseover="hide_calendar(3, 1);" onmouseout="hide_calendar(3, 0);">							
								</div>
							</div>
							<a onClick="xmlhttprequest_calendar('current', '?input_field=current_date&amp;field=current&amp;d=<?php echo isset($_tmpb_articles['DAY_DATE']) ? $_tmpb_articles['DAY_DATE'] : ''; ?>&amp;m=<?php echo isset($_tmpb_articles['MONTH_DATE']) ? $_tmpb_articles['MONTH_DATE'] : ''; ?>&amp;y=<?php echo isset($_tmpb_articles['YEAR_DATE']) ? $_tmpb_articles['YEAR_DATE'] : ''; ?>');display_calendar(3);" onmouseover="hide_calendar(3, 1);" onmouseout="hide_calendar(3, 0);" style="cursor:pointer;"><img style="vertical-align:middle;" src="../templates/<?php echo isset($this->_var['THEME']) ? $this->_var['THEME'] : ''; ?>/images/calendar.png" alt="" /></a>
							<?php echo isset($this->_var['L_AT']) ? $this->_var['L_AT'] : ''; ?>
							<input type="text" size="2" maxlength="2" name="hour" value="<?php echo isset($_tmpb_articles['HOUR']) ? $_tmpb_articles['HOUR'] : ''; ?>" class="text" /> H <input type="text" size="2" maxlength="2" name="min" value="<?php echo isset($_tmpb_articles['MIN']) ? $_tmpb_articles['MIN'] : ''; ?>" class="text" />
						</label></dd>
					</dl>
				</fieldset>			
				<fieldset class="fieldset_submit">
					<legend><?php echo isset($this->_var['L_UPDATE']) ? $this->_var['L_UPDATE'] : ''; ?></legend>
					<input type="hidden" name="id" value="<?php echo isset($_tmpb_articles['IDARTICLES']) ? $_tmpb_articles['IDARTICLES'] : ''; ?>" />
					<input type="submit" name="valid" value="<?php echo isset($this->_var['L_UPDATE']) ? $this->_var['L_UPDATE'] : ''; ?>" class="submit" />
					&nbsp;&nbsp; 
					<input type="submit" name="previs" value="<?php echo isset($this->_var['L_PREVIEW']) ? $this->_var['L_PREVIEW'] : ''; ?>" class="submit" />
					<input type="reset" value="<?php echo isset($this->_var['L_RESET']) ? $this->_var['L_RESET'] : ''; ?>" class="reset" />				
				</fieldset>	
			</form>
			<?php } ?>
	</div>
	