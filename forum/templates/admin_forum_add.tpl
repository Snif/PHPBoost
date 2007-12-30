		<link href="{MODULE_DATA_PATH}/forum.css" rel="stylesheet" type="text/css" media="screen, handheld">
		<script type="text/javascript">
		<!--
			function check_select_multiple(id, status)
			{
				var i;		
				for(i = -1; i <= 2; i++)
				{
					if( document.getElementById(id + 'r' + i) )
						document.getElementById(id + 'r' + i).selected = status;
				}				
				document.getElementById(id + 'r3').selected = true;
				
				for(i = 0; i < {NBR_GROUP}; i++)
				{	
					if( document.getElementById(id + 'g' + i) )
						document.getElementById(id + 'g' + i).selected = status;		
				}	
			}		
			function check_select_multiple_ranks(id, start)
			{
				var i;				
				for(i = start; i <= 2; i++)
				{	
					if( document.getElementById(id + i) )
						document.getElementById(id + i).selected = true;			
				}
			}
			function change_parent_cat(value)
			{			
				if( value > 0 )
				{
					disabled = 0;
					var i;
					var array_id = new Array('wr', 'xr', 'wg', 'xg');
					for(j = 0; j <= 5; j++)
					{
						for(i = -1; i <= 2; i++)
						{	
							if( document.getElementById(array_id[j] + i) )
								document.getElementById(array_id[j] + i).disabled = '';
						}
						for(i = 0; i < {NBR_GROUP}; i++)
						{	
							if( document.getElementById(array_id[j] + i) )
								document.getElementById(array_id[j] + i).disabled = '';	
						}
					}
					document.getElementById('wr3').selected = true;
					document.getElementById('xr3').selected = true;
				}
				else
				{
					disabled = 1;
					var i;
					var array_id = new Array('wr', 'xr', 'wg', 'xg');
					for(j = 0; j <= 5; j++)
					{
						for(i = -1; i <= 2; i++)
						{	
							if( document.getElementById(array_id[j] + i) )
							{
								document.getElementById(array_id[j] + i).disabled = 'disabled';	
								document.getElementById(array_id[j] + i).selected = '';
							}		
						}
						for(i = 0; i < {NBR_GROUP}; i++)
						{	
							if( document.getElementById(array_id[j] + i) )
							{
								document.getElementById(array_id[j] + i).disabled = 'disabled';	
								document.getElementById(array_id[j] + i).selected = '';
							}			
						}
					}
				}
			}
		-->
		</script>
		<div id="admin_quick_menu">
			<ul>
				<li class="title_menu">{L_FORUM_MANAGEMENT}</li>
				<li>
					<a href="admin_forum.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum.php" class="quick_link">{L_CAT_MANAGEMENT}</a>
				</li>
				<li>
					<a href="admin_forum_add.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_add.php" class="quick_link">{L_ADD_CAT}</a>
				</li>
				<li>
					<a href="admin_forum_config.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_config.php" class="quick_link">{L_FORUM_CONFIG}</a>
				</li>
				<li>
					<a href="admin_forum_groups.php"><img src="forum.png" alt="" /></a>
					<br />
					<a href="admin_forum_groups.php" class="quick_link">{L_FORUM_GROUPS}</a>
				</li>
			</ul>
		</div>

		<div id="admin_contents">
					
			# START error_handler #
			<div class="error_handler_position">
				<span id="errorh"></span>
				<div class="{error_handler.CLASS}" style="width:500px;margin:auto;padding:15px;">
					<img src="../templates/{THEME}/images/{error_handler.IMG}.png" alt="" style="float:left;padding-right:6px;" /> {error_handler.L_ERROR}
					<br />	
				</div>
			</div>	
			# END error_handler #
				
			<form action="admin_forum_add.php" method="post" onsubmit="return check_form_list();" class="fieldset_content">
				<fieldset>
					<legend>{L_ADD_CAT}</legend>
					<p>{L_REQUIRE}</p>
					<dl>
						<dt><label for="name">* {L_NAME}</label></dt>
						<dd><label><input type="text" maxlength="100" size="35" id="name" name="name" class="text" /></label></dd>
					</dl>
					<dl>
						<dt><label for="category">* {L_PARENT_CATEGORY}</label></dt>
						<dd><label>
							<select name="category" id="category" onchange="change_parent_cat(this.options[this.selectedIndex].value)">
								{CATEGORIES}
							</select>
						</label></dd>
					</dl>
					<dl>
						<dt><label for="desc">{L_DESC}</label></dt>
						<dd><label><input type="text" maxlength="150" size="55" name="desc" id="desc" class="text" /></label></dd>
					</dl>
					<dl>
						<dt><label for="aprob">{L_APROB}</label></dt>
						<dd>
							<label><input type="radio" name="aprob" id="aprob" checked="checked" value="1" /> {L_YES}</label>
							<label><input type="radio" name="aprob" value="0" /> {L_NO}</label>
						</dd>
					</dl>
					<dl>
						<dt><label for="status">{L_STATUS}</label></dt>
						<dd>
							<label><input type="radio" name="status" id="status" checked="checked" value="1" /> {L_UNLOCK}</label>
							<label><input type="radio" name="status" value="0" /> {L_LOCK}</label>
						</dd>
					</dl>
					<dl>
						<dt><label>{L_AUTH_READ}</label></dt>
						<dd><label>
							<span class="text_small">({L_EXPLAIN_SELECT_MULTIPLE})</span>
							<br />
							{AUTH_READ}
							<br />
							<a href="javascript:check_select_multiple('r', true);">{L_SELECT_ALL}</a>
							&nbsp;/&nbsp;
							<a href="javascript:check_select_multiple('r', false);">{L_SELECT_NONE}</a>
						</label></dd>
					</dl>
					<dl>
						<dt><label>{L_AUTH_WRITE}</label></dt>
						<dd><label>
							<span class="text_small">({L_EXPLAIN_SELECT_MULTIPLE})</span>
							<br />
							{AUTH_WRITE}
							<br />
							<a href="javascript:check_select_multiple('w', true);">{L_SELECT_ALL}</a>
							&nbsp;/&nbsp;
							<a href="javascript:check_select_multiple('w', false);">{L_SELECT_NONE}</a>
						</label></dd>
					</dl>
					<dl>
						<dt><label>{L_AUTH_EDIT}</label></dt>
						<dd><label>
							<span class="text_small">({L_EXPLAIN_SELECT_MULTIPLE})</span>
							<br />
							{AUTH_EDIT}
							<br />
							<a href="javascript:check_select_multiple('x', true);">{L_SELECT_ALL}</a>
							&nbsp;/&nbsp;
							<a href="javascript:check_select_multiple('x', false);">{L_SELECT_NONE}</a>
						</label></dd>
					</dl>
				</fieldset>
								
				<fieldset class="fieldset_submit">
				<legend>{L_ADD}</legend>
					<input type="submit" name="add" value="{L_ADD}" class="submit" />
					&nbsp;&nbsp; 
					<input type="reset" value="{L_RESET}" class="reset" />
				</fieldset>
			</form>
		</div>
			