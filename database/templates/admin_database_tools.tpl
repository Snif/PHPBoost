		<script type="text/javascript">
		<!--
		function Confirm_truncate_table() {
			return confirm("{L_CONFIRM_TRUNCATE_TABLE}");
		}
		-->	
		</script>
				
		<div id="admin_quick_menu">
				<ul>
					<li class="title_menu">{L_DATABASE_MANAGEMENT}</li>
				<li>
					<a href="admin_database.php"><img src="database.png" alt="" /></a>
					<br />
					<a href="admin_database.php" class="quick_link">{L_DB_TOOLS}</a>
				</li>
				<li>
					<a href="admin_database.php?query=1"><img src="database.png" alt="" /></a>
					<br />
					<a href="admin_database.php?query=1" class="quick_link">{L_QUERY}</a>
				</li>
			</ul>
		</div>
		
		<div id="admin_contents">
			<div style="width:95%;margin:auto;">	
				<div class="block_contents1" style="padding:5px;padding-bottom:7px;margin-bottom:5px">
					- <a class="small" href="admin_database.php#tables">{L_DATABASE_MANAGEMENT}</a> - <a class="small" href="admin_database_tools.php?table={TABLE_NAME}&amp;action=structure">{TABLE_NAME}</a>
				</div>
				<menu class="dynamic_menu group">
					<ul>
						<li>
							<a href="admin_database_tools.php?table={TABLE_NAME}&amp;action=structure"><img src="./database_mini.png"/> {L_TABLE_STRUCTURE}</a>
						</li>
						<li>
							<a href="admin_database_tools.php?table={TABLE_NAME}&amp;action=data"><img src="{PATH_TO_ROOT}/templates/default/images/admin/themes_mini.png"/> {L_TABLE_DISPLAY}</a>
						</li>
						<li>
							<a href="admin_database_tools.php?table={TABLE_NAME}&amp;action=query"><img src="{PATH_TO_ROOT}/templates/default/images/admin/tools_mini.png"/> SQL</a>
						</li>
						<li>
							<a href="admin_database_tools.php?table={TABLE_NAME}&amp;action=insert"><img src="{PATH_TO_ROOT}/templates/default/images/admin/extendfield_mini.png"/> {L_INSERT}</a>
						</li>
						<li>
							<a href="admin_database.php?table={TABLE_NAME}&amp;action=backup_table"><img src="{PATH_TO_ROOT}/templates/default/images/admin/cache_mini.png"/> {L_BACKUP}</a>
						</li>
						<li>
							<a onclick="javascript:return Confirm_truncate_table()" style="color:red;" href="admin_database_tools.php?table={TABLE_NAME}&amp;action=truncate&amp;token={TOKEN}"><img src="{PATH_TO_ROOT}/templates/{THEME}/images/upload/trash_mini.png"/> {L_TRUNCATE}</a>
						</li>
						<li>
							<a style="color:red;" href="admin_database_tools.php?table={TABLE_NAME}&amp;action=drop&amp;token={TOKEN}" class="icon-delete" data-confirmation="delete-element">{L_DELETE}</a>
						</li>
					</ul>
				</menu>
			</div>
			
			# IF C_DATABASE_TABLE_STRUCTURE #
			<table class="module_table">
				<tr>
					<th colspan="6" style="text-align:center;">
						{TABLE_NAME}
					</th>
				</tr>
				<tr style="text-align:center;">			
					<td class="row1">
						{L_TABLE_FIELD}
					</td>
					<td class="row1">
						{L_TABLE_TYPE}
					</td>
					<td class="row1">
						{L_TABLE_ATTRIBUTE}
					</td>
					<td class="row1">
						{L_TABLE_NULL}
					</td>
					<td class="row1">
						{L_TABLE_DEFAULT}
					</td>
					<td class="row1">
						{L_TABLE_EXTRA}
					</td>
				</tr>
				# START field #
				<tr>			
					<td class="row2">
						{field.FIELD_NAME}
					</td>			
					<td class="row2">
						{field.FIELD_TYPE}
					</td>
					<td class="row2">
						{field.FIELD_ATTRIBUTE}
					</td>
					<td class="row2">
						{field.FIELD_NULL}
					</td>
					<td class="row2">
						{field.FIELD_DEFAULT}
					</td>
					<td class="row2">
						{field.FIELD_EXTRA}
					</td>
				</tr>
				# END field #
			</table>
			
			<div style="width:95%;margin:auto;">
				<table class="module_table" style="float:left;width:100px;margin-right:15px">
					<tr>
						<th colspan="3" style="text-align:center;">
							{L_TABLE_INDEX}
						</th>
					</tr>
					<tr style="text-align:center;">			
						<td class="row1">
							{L_INDEX_NAME}
						</td>
						<td class="row1">
							{L_TABLE_TYPE}
						</td>
						<td class="row1">
							{L_TABLE_FIELD}
						</td>
					</tr>
					# START index #
					<tr>			
						<td class="row2">
							{index.INDEX_NAME}
						</td>			
						<td class="row2">
							{index.INDEX_TYPE}
						</td>
						<td class="row2">
							{index.INDEX_FIELDS}
						</td>
					</tr>
					# END index #
				</table>
			
				<table class="module_table" style="float:left;width:170px;margin-right:15px">
					<tr>
						<th style="text-align:center;" colspan="2">
							{L_SIZE}
						</th>
					</tr>
					<tr>
						<td class="row1" style="width:50px;">
							{L_TABLE_DATA}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_DATA}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_TABLE_INDEX}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_INDEX}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_TABLE_FREE}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_FREE}
						</td>
					</tr>					
					<tr>
						<td class="row3">
							{L_TABLE_TOTAL}
						</td>
						<td class="row3" style="text-align:right;">
							{TABLE_TOTAL_SIZE}
						</td>
					</tr>
					# IF TABLE_FREE #
					<tr>
						<td class="row3" colspan="2" style="text-align:center">
							<img src="./database_mini.png" alt="" class="valign_middle" /> <a href="admin_database_tools.php?table={TABLE_NAME}&amp;action=optimize">{L_OPTIMIZE}</a>
						</td>
					</tr>
					# ENDIF #
				</table>
				
				<table class="module_table" style="float:left;width:300px;margin-right:15px">
					<tr>
						<th style="text-align:center;" colspan="2">
							{L_STATISTICS}
						</th>
					</tr>
					<tr>
						<td class="row1" style="width:130px;">
							{L_TABLE_ROWS_FORMAT}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_ROW_FORMAT}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_TABLE_ROWS}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_ROWS}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_TABLE_ENGINE}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_ENGINE}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_TABLE_COLLATION}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_COLLATION}
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_SIZE}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_TOTAL_SIZE}
						</td>
					</tr>
					# IF C_AUTOINDEX #
					<tr>
						<td class="row1">
							{L_AUTOINCREMENT}
						</td>
						<td class="row2" style="text-align:right;">
							{TABLE_AUTOINCREMENT}
						</td>
					</tr>
					# ENDIF #
					<tr>
						<td class="row1">
							{L_CREATION_DATE}
						</td>
						<td class="row2" style="text-align:right;">
							<span class="smaller">{TABLE_CREATION_DATE}</span>
						</td>
					</tr>
					<tr>
						<td class="row1">
							{L_LAST_UPDATE}
						</td>
						<td class="row2" style="text-align:right;">
							<span class="smaller">{TABLE_LAST_UPDATE}</span>
						</td>
					</tr>
				</table>
				<div class="spacer"></div>
			</div>
			# ENDIF #
			
			
			# IF C_DATABASE_TABLE_DATA #
			<script type="text/javascript">
			<!--
			function Confirm_del_entry() {
				return confirm("{L_CONFIRM_DELETE_ENTRY}");
			}
			-->	
			</script>
			<div class="block_container" style="width:98%;margin-top:28px;" id="executed_query">
				<div class="block_top">
					{L_RESULT}
				</div>
				<div class="block_contents">
					<fieldset style="background-color:white;margin:0px">
						<legend><strong>{L_EXECUTED_QUERY}:</strong></legend>
						<p style="color:black;font-size:10px;">{QUERY_HIGHLIGHT}</p>
					</fieldset>
					
					<br />
					# IF PAGINATION #<strong>{L_PAGE}</strong> : {PAGINATION} # ENDIF #
					
					<div style="width:99%;margin:auto;overflow:auto;padding:0px 2px">
						<table class="module_table">
							# START line #
							<tr>
								# START line.field #
								<td class="{line.field.CLASS}" style="{line.field.STYLE}">
									{line.field.FIELD}
								</td>
								# END line.field #
							</tr>
							# END line #
						</table>
					</div>
					<br />
					# IF PAGINATION #<strong>{L_PAGE}</strong> : {PAGINATION} # ENDIF #
				</div>
			</div>			
			# ENDIF #
			
			
			# IF C_DATABASE_TABLE_QUERY #
			<script type="text/javascript">
			<!--
			function check_form(){
				var query = document.getElementById('query').value;
				var query_lowercase = query.toLowerCase();
				var check_query = false;
				var keyword = new Array('delete', 'drop', 'truncate');
				
				if( query == "" ) {
					alert("{L_REQUIRE}");
					return false;
				}
				
				//V�rification de la requ�te => alerte si elle contient un des mots cl�s DELETE, DROP ou TRUNCATE.
				for(i = 0; i < keyword.length; i++)
				{
					if( typeof(strpos(query_lowercase, keyword[i])) != 'boolean' )
					{
						check_query = true;
						break;
					}
				}
				if( check_query )
				{
					return confirm("{L_CONFIRM_QUERY}\n" + query);
				}
				return true;
			}
			-->	
			</script>
			
			<form action="admin_database_tools.php?table={TABLE_NAME}&action=query&amp;token={TOKEN}#executed_query" method="post" onsubmit="return check_form();">
				<div class="block_container" style="margin-top:28px;">
					<div class="block_top">
						{L_QUERY}
					</div>
					<div class="block_contents2">
						<span id="errorh"></span>
						<div class="warning">
							{L_EXPLAIN_QUERY}
						</div>
					</div>
					<div class="block_top">
						* {L_EXECUTED_QUERY}
					</div>
					<div class="block_contents2">
						<textarea rows="12" id="query" name="query">{QUERY}</textarea>
					</div>
					<fieldset class="fieldset_submit" style="margin:0">
						<legend>{L_EXECUTE}</legend>
						<button type="submit" name="submit" value="true">{L_EXECUTE}</button>
					</fieldset>
				</div>
			</form>
			
			# IF C_QUERY_RESULT #
			<div class="block_container" style="width:98%;margin-top:0" id="executed_query">
				<div class="block_top">
					{L_RESULT}
				</div>
				<div class="block_contents2">
					<fieldset style="background-color:white;margin:0px">
						<legend><strong>{L_EXECUTED_QUERY}:</strong></legend>
						<p style="color:black;font-size:10px;">{QUERY_HIGHLIGHT}</p>
					</fieldset>
					
					<div style="width:99%;margin:auto;overflow:auto;padding:18px 2px">
						<table class="module_table">
							# START line #
							<tr>
								# START line.field #
								<td class="{line.field.CLASS}" style="{line.field.STYLE}">
									{line.field.FIELD}
								</td>
								# END line.field #
							</tr>
							# END line #
						</table>
					</div>
				</div>				
			</div>
			# ENDIF #			
			# ENDIF #
			
			# IF C_DATABASE_UPDATE_FORM #
			<br />
			<form action="admin_database_tools.php?table={TABLE_NAME}&amp;field={FIELD_NAME}&amp;value={FIELD_VALUE}&amp;action={ACTION}&amp;token={TOKEN}#executed_query" method="post" onsubmit="return check_form();">
				<table class="module_table">
					<tr style="text-align:center;">			
						<td class="row3 text_strong">
							{L_FIELD_FIELD}
						</td>
						<td class="row3 text_strong">
							{L_FIELD_TYPE}
						</td>
						<td class="row3 text_strong">
							{L_FIELD_NULL}
						</td>
						<td class="row3 text_strong">
							{L_FIELD_VALUE}
						</td>
					</tr>
					# START fields #
					<tr>
						<td class="row1">
							{fields.FIELD_NAME}
						</td>
						<td class="row1">
							{fields.FIELD_TYPE}
						</td>
						<td class="row2">
							{fields.FIELD_NULL}
						</td>
						<td class="row2">
							# IF fields.C_FIELD_FORM_EXTEND #
							<textarea rows="6" cols="37" name="{fields.FIELD_NAME}">{fields.FIELD_VALUE}</textarea>
							# ELSE #
							<input type="text" size="30" name="{fields.FIELD_NAME}" value="{fields.FIELD_VALUE}">
							# ENDIF #
						</td>
					</tr>
					# END fields #
				</table>
				<fieldset class="fieldset_submit" style="margin:0">
					<legend>{L_EXECUTE}</legend>
					<button type="submit" name="submit" value="true">{L_EXECUTE}</button>
				</fieldset>
			</form>	
			# ENDIF #
		</div>
		