		<script type="text/javascript">
		<!--
		function check_form_convers(){
			if(document.getElementById('login').value == "") {
				alert("{L_REQUIRE_RECIPIENT}");
				return false;
		    }
			if(document.getElementById('contents').value == "") {
				alert("{L_REQUIRE_MESSAGE}");
				return false;
		    }
			if(document.getElementById('title').value == "") {
				alert("{L_REQUIRE_TITLE}");
				return false;
		    }
			return true;
		}

		function check_form_pm(){
			if(document.getElementById('contents').value == "") {
				alert("{L_REQUIRE_MESSAGE}");
				return false;
		    }
			return true;
		}

		function Confirm_pm() {
			return confirm("{L_DELETE_MESSAGE}");
		}

		function XMLHttpRequest_search()
		{
			var xhr_object = null;
			var filename = "../includes/xmlhttprequest.php?pm=1";
			var login = document.getElementById("login").value;
			var data = null;
			
			if(window.XMLHttpRequest) // Firefox
			   xhr_object = new XMLHttpRequest();
			else if(window.ActiveXObject) // Internet Explorer
			   xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
			else // XMLHttpRequest non support� par le navigateur
			    return;
			
			if( login != "" )
			{
				data = "login=" + login;
			   
				xhr_object.open("POST", filename, true);

				xhr_object.onreadystatechange = function() 
				{
					if( xhr_object.readyState == 4 ) 
					{
						document.getElementById("xmlhttprequest_result_search").innerHTML = xhr_object.responseText;
						show_div("xmlhttprequest_result_search");
					}
				}

				xhr_object.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				xhr_object.send(data);
			}	
			else
			{
				alert("{L_REQUIRE_RECIPIENT}");
			}	
		}

		function insert_XMLHttpRequest(login)
		{
			document.getElementById("login").value = login;
		}

		-->
		</script>

		
		# START convers #
		<script type="text/javascript">
		<!--
			function check_convers(status, id)
			{
				var i;
				for(i = 0; i < {convers.NBR_PM}; i++)
				{	
					if( document.getElementById(id + i) ) 
						document.getElementById(id + i).checked = status;
				}
				document.getElementById('checkall').checked = status;
				document.getElementById('validc').checked = status;
			}	 
		-->
		</script>
		# START convers.error_handler #
		<span id="errorh"></span>
		<div class="{convers.error_handler.CLASS}" style="width:500px;margin:auto;padding:15px;">
			<img src="../templates/{THEME}/images/{convers.error_handler.IMG}.png" alt="" style="float:left;padding-right:6px;" /> {convers.error_handler.L_ERROR}
			<br />	
		</div>
		<br />		
		# END convers.error_handler #
		
		<form action="pm{convers.U_MEMBER_ACTION_PM}" method="post" onsubmit="javascript:return Confirm_pm();">
			<div class="module_position">					
				<div class="module_top_l"></div>		
				<div class="module_top_r"></div>
				<div class="module_top">&bull; {convers.U_MEMBER_VIEW} &raquo; {convers.U_PM_BOX} &raquo; {convers.U_POST_NEW_CONVERS}</div>
				<div class="module_contents">					
					<div style="float:left;">{L_PRIVATE_MSG}: {convers.PM_POURCENT}</div>
					<div style="float:right;">{convers.U_MARK_AS_READ}</div>
					<br /><br />
					
					<table class="module_table">	
						<tr>
							<th style="text-align:center;width:20px;">
								<input type="checkbox" id="checkall" onClick="check_convers(this.checked, 'd');">
							</th>
							<th colspan="2" style="text-align:center;">
								{L_TITLE}
							</th>
							<th style="text-align:center;width:110px;">
								{L_PARTICIPANTS}
							</th>
							<th style="text-align:center;width:80px;">
								{L_MESSAGE}
							</th>
							<th style="text-align:center;width:120px;">
								{L_LAST_MESSAGE}
							</th>
						</tr>
						
						# START convers.list #		
						<tr class="row2">
							<td style="width:20px;text-align:center;">
								<input type="checkbox" id="d{convers.list.INCR}" name="{convers.list.ID}" />
							</td>
							<td class="text_small" style="width:40px;text-align:center;">
								{convers.list.ANNOUNCE}
							</td>
							<td style="padding:4px;">
								{convers.list.ANCRE} <a href="pm{convers.list.U_CONVERS}">{convers.list.TITLE}</a> &nbsp;<span class="text_small">[{convers.list.U_AUTHOR}]</span>
								<br />
								&nbsp;{convers.list.PAGINATION_PM}
							</td>
							<td style="text-align:center;">
								{convers.list.U_PARTICIPANTS}
							</td>
							<td style="text-align:center">
								{convers.list.MSG}
							</td>
							<td class="text_small" style="text-align:center;">
								{convers.list.U_LAST_MSG}
							</td>
						</tr>	
						# END convers.list #
								
						# START convers.no_pm #	
						<tr>
							<td style="text-align:center;" colspan="6" class="row2">
								<strong>{convers.no_pm.L_NO_PM}</strong>
							</td>
						</tr>
						# END convers.no_pm #	
						<tr>
							<td colspan="6" class="row3">
								<div style="float:left;">&nbsp;<input type="checkbox" id="validc" onClick="check_convers(this.checked, 'd');"> &nbsp;<input type="submit" name="valid" value="{L_DELETE}" class="submit" /></div>
								<div style="float:right;">{convers.PAGINATION}&nbsp;</div>
							</td>
						</tr>
					</table>					
					<br />
					<table class="module_table">
						<tr> 		
							<td style="width:33%;text-align:center"> 
								<img style="vertical-align:middle;" src="../templates/{THEME}/images/announce.gif" alt="" /> {L_READ} 
							</td>
							<td style="width:34%;text-align:center"> 
								<img style="vertical-align:middle;" src="../templates/{THEME}/images/announce_track.gif" alt="" /> {L_TRACK}		
							</td>
							<td style="width:33%;text-align:center"> 
								<img style="vertical-align:middle;" src="../templates/{THEME}/images/new_announce.gif" alt="" /> {L_NOT_READ}		
							</td>
						</tr>
					</table>
				</div>
				<div class="module_bottom_l"></div>		
				<div class="module_bottom_r"></div>
				<div class="module_bottom">&bull; {convers.U_MEMBER_VIEW} &raquo; {convers.U_PM_BOX} &raquo; {convers.U_POST_NEW_CONVERS}</div>
			</div>
		</form>
		# END convers #


		
		# START pm #
		<div class="msg_position">
			<div class="msg_top_l"></div>			
			<div class="msg_top_r"></div>
			<div class="msg_top">
				<div style="float:left;">
					&bull; {pm.U_MEMBER_VIEW} &raquo; {pm.U_PM_BOX} &raquo; {pm.U_TITLE_CONVERS}
				</div>
				<div style="float:right;">
					{pm.PAGINATION}
				</div>
			</div>	
		</div>		
		# START pm.msg #		
		<div class="msg_position">
			<div class="msg_container">				
				<div class="msg_top_row">
					<span id="m{pm.msg.ID}"></span>
					<div class="msg_pseudo_mbr">
					{pm.msg.USER_ONLINE} <a class="msg_link_pseudo" href="../member/member{pm.msg.U_MEMBER_ID}">{pm.msg.USER_PSEUDO}</a>
					</div>
					<div style="float:left;">&nbsp;&nbsp;<a href="{pm.msg.U_ANCHOR}"><img src="../templates/{THEME}/images/ancre.png" alt="{pm.msg.ID}" /></a> {pm.msg.DATE}</div>
					<div style="float:right;">{pm.msg.U_QUOTE}&nbsp; {pm.msg.EDIT}{pm.msg.DEL}&nbsp;&nbsp;</div>
				</div>
				<div class="msg_contents_container">
					<div class="msg_info_mbr">
						<p style="text-align:center;">{pm.msg.USER_RANK}</p>
						<p style="text-align:center;">{pm.msg.USER_IMG_ASSOC}</p>
						<p style="text-align:center;">{pm.msg.USER_AVATAR}</p>
						<p style="text-align:center;">{pm.msg.USER_GROUP}</p>
						{pm.msg.USER_SEX}
						{pm.msg.USER_DATE}<br />
						{pm.msg.USER_MSG}<br />
						{pm.msg.USER_LOCAL}
					</div>
					<div class="msg_contents">
						<div class="msg_contents_overflow">
							{pm.msg.CONTENTS}
						</div>
					</div>
				</div>
			</div>	
			<div class="msg_sign">				
				<div class="msg_sign_overflow">
					{pm.msg.USER_SIGN}
				</div>				
				<hr />
				<div style="float:left;">
					{pm.msg.U_MEMBER_PM} {pm.msg.USER_MAIL} {pm.msg.USER_MSN} {pm.msg.USER_YAHOO} {pm.msg.USER_WEB}
				</div>
				<div style="float:right;font-size:10px;">
					{pm.msg.WARNING}
				</div>&nbsp;
			</div>	
		</div>
		# END pm.msg #
		<div class="msg_position">		
			<div class="msg_bottom_l"></div>		
			<div class="msg_bottom_r"></div>
			<div class="msg_bottom">
				<div style="float:left;">
					&bull; {pm.U_MEMBER_VIEW} &raquo; {pm.U_PM_BOX} &raquo; {pm.U_TITLE_CONVERS}
				</div>
				<div style="float:right;">
					{pm.PAGINATION}
				</div>
			</div>
		</div>
		<br />
		# END pm #



		# START show_pm #
		<div class="module_position">					
			<div class="module_top_l"></div>		
			<div class="module_top_r"></div>
			<div class="module_top">&bull; {show_pm.U_MEMBER_VIEW} &raquo; {show_pm.U_PM_BOX} &raquo; {show_pm.U_TITLE_CONVERS}</div>
			<div class="module_contents">
				<table class="module_table">
					<tr>
						<th>
							<div style="float:left;">{L_PREVIEW}</div>
							<div style="float:right;">{show_pm.DATE}</div>	
						</th>
					</tr>
					<tr>	
						<td class="row2">
							{show_pm.CONTENTS}<br /><br /><br />
							<hr /><img src="../templates/{THEME}/images/{LANG}/pm.png" />
						</td>
					</tr>	
				</table>
			</div>
			<div class="module_bottom_l"></div>		
			<div class="module_bottom_r"></div>
			<div class="module_bottom">&bull; {show_pm.U_MEMBER_VIEW} &raquo; {show_pm.U_PM_BOX} &raquo; {show_pm.U_TITLE_CONVERS}</div>
		</div>
		# END show_pm #



		# START post_pm #
		# START post_pm.error_handler #
		<br />
		<span id="errorh"></span>
		<div class="{post_pm.error_handler.CLASS}" style="width:500px;margin:auto;padding:15px;">
			<img src="../templates/{THEME}/images/{post_pm.error_handler.IMG}.png" alt="" style="float:left;padding-right:6px;" /> {post_pm.error_handler.L_ERROR}
			<br />	
		</div>
		<br />		
		# END post_pm.error_handler #
		<span id="quote"></span>			
		<div style="font-size: 10px;text-align:center;padding-bottom: 2px;">{L_RESPOND}</div>
		<form action="pm{post_pm.U_PM_ACTION_POST}" method="post" onsubmit="return check_form_msg();" style="width:80%;margin:auto">						
			# INCLUDE handle_bbcode #		
			<label><textarea type="text" class="post" rows="15" cols="66" id="contents" name="contents">{post_pm.CONTENTS}</textarea> </label>
			<div style="padding:17px;">					
				<fieldset class="fieldset_submit">
				<legend>{L_SUBMIT}</legend>
					<input type="submit" name="pm" value="{L_SUBMIT}" class="submit" />
					&nbsp;&nbsp; 									
					<script type="text/javascript">
					<!--				
					document.write('<input value="{L_PREVIEW}" onclick="XMLHttpRequest_preview(this.form);" type="button" class="submit" />');
					-->
					</script>				
					<noscript><input value="{L_PREVIEW}" type="submit" name="prw" class="submit" /></noscript>
					&nbsp;&nbsp;
					<input type="reset" value="{L_RESET}" class="reset" />				
				</fieldset>	
			</div>				
		</form>
		# END post_pm #

		

		# START edit_pm #
		<form action="pm{edit_pm.U_ACTION_EDIT}" method="post" onsubmit="return check_form_convers();">
			<div class="module_position">					
				<div class="module_top_l"></div>		
				<div class="module_top_r"></div>
				<div class="module_top">&bull; {edit_pm.U_MEMBER_VIEW} &raquo; {edit_pm.U_PM_BOX}</div>
				<div class="module_contents">	
					# START edit_pm.show_pm #		
					<table class="module_table">
						<tr>
							<th>
								<div style="float:left;">{L_PREVIEW}</div>
								<div style="float:right;">{edit_pm.show_pm.DATE}</div>		
							</th>
						</tr>
						<tr>	
							<td class="row2">														
								{edit_pm.show_pm.CONTENTS}
								<br /><br /><br />
								<hr /><img src="../templates/{THEME}/images/{LANG}/pm.png" />
							</td>
						</tr>	
					</table>
					# END edit_pm.show_pm #
										
					<div class="fieldset_content">
						<fieldset>
							<legend>{L_EDIT}</legend>
							<p>{L_REQUIRE}</p>
							# START edit_pm.title #
							<dl>
								<dt><label for="title">* {L_TITLE}</label></dt>
								<dd><label><input type="text" size="50" maxlength="100" id="title" name="title" value="{edit_pm.title.TITLE}" class="text" /></label></dd>
							</dl>
							# END edit_pm.title #
							<br />
							<label for="contents">* {L_MESSAGE}</label>
							# INCLUDE handle_bbcode #
							<textarea type="text" rows="25" cols="66" id="contents" name="contents">{edit_pm.CONTENTS}</textarea>
							<br />
						</fieldset>
						
						<fieldset class="fieldset_submit">
							<legend>{L_SUBMIT}</legend>
							<input type="submit" name="{SUBMIT_NAME}" value="{L_SUBMIT}" class="submit" />
							&nbsp;&nbsp; 
							<script type="text/javascript">
							<!--				
							document.write('<input value="{L_PREVIEW}" onclick="XMLHttpRequest_preview(this.form);" type="button" class="submit" />');
							-->
							</script>						
							<noscript><input value="{L_PREVIEW}" type="submit" name="prw" class="submit" /></noscript>								
							&nbsp;&nbsp; 
							<input type="reset" value="{L_RESET}" class="reset" />
						</fieldset>	
					</div>	
				</div>
				<div class="module_bottom_l"></div>		
				<div class="module_bottom_r"></div>
				<div class="module_bottom">&bull; {edit_pm.U_MEMBER_VIEW} &raquo; {edit_pm.U_PM_BOX}</div>
			</div>
		</form>
		# END edit_pm #

		

		# START post_convers #		
		<form action="pm{post_convers.U_ACTION_CONVERS}" method="post" onsubmit="return check_form_convers();">
			<div class="module_position">					
					<div class="module_top_l"></div>		
					<div class="module_top_r"></div>
					<div class="module_top">&bull; {post_convers.U_MEMBER_VIEW} &raquo; {post_convers.U_PM_BOX}</div>
					<div class="module_contents">	
						# START post_convers.error_handler #
						<br />
						<span id="errorh"></span>
						<div class="{post_convers.error_handler.CLASS}" style="width:500px;margin:auto;padding:15px;">
							<img src="../templates/{THEME}/images/{post_convers.error_handler.IMG}.png" alt="" style="float:left;padding-right:6px;" /> {post_convers.error_handler.L_ERROR}
							<br />	
						</div>
						<br />		
						# END post_convers.error_handler #
						
						# START post_convers.show_convers #		
						<table class="module_table">
							<tr>
								<th>
									<div style="float:left;">{L_PREVIEW}</div>
									<div style="float:right;">{post_convers.show_convers.DATE}</div>		
								</th>
							</tr>
							<tr>	
								<td class="row2">														
									{post_convers.show_convers.CONTENTS}
									<br /><br /><br />
									<hr /><img src="../templates/{THEME}/images/{LANG}/pm.png" />
								</td>
							</tr>	
						</table>
						# END post_convers.show_convers #	
						
						<div class="fieldset_content">
							<fieldset>
								<legend>{L_POST_NEW_CONVERS}</legend>
								<p>{L_REQUIRE}</p>
								# START post_convers.user_id_dest #
								<dl>
									<dt><label for="login">* {L_RECIPIENT}</label></dt>
									<dd><label>
										<input type="text" size="20 maxlenght="25" id="login" name="login" value="{post_convers.LOGIN}" class="text" />
										<input value="{L_SEARCH}" onclick="XMLHttpRequest_search(this.form);" type="button" class="submit">
										<div id="xmlhttprequest_result_search" style="display:none;" class="xmlhttprequest_result_search"></div>
										# START post_convers.user_id_dest.search #
											{search.RESULT}
										# END post_convers.user_id_dest.search #
									</label></dd>
								</dl>		
								# END post_convers.user_id_dest #
								<dl>
									<dt><label for="title">* {L_TITLE}</label></dt>
									<dd><label><input type="text" size="50" maxlength="100" id="title" name="title" value="{post_convers.TITLE}" class="text" /></label></dd>
								</dl>
								<br />
								<label for="contents">* {L_MESSAGE}</label>
								# INCLUDE handle_bbcode #
								<textarea type="text" rows="25" cols="66" id="contents" name="contents">{edit_pm.CONTENTS}</textarea>
								<br />
							</fieldset>
							
							<fieldset class="fieldset_submit">
								<legend>{L_SUBMIT}</legend>
								<input type="submit" name="convers" value="{L_SUBMIT}" class="submit" />
									&nbsp;&nbsp; 
									<script type="text/javascript">
									<!--				
									document.write('<input value="{L_PREVIEW}" onclick="XMLHttpRequest_preview(this.form);" type="button" class="submit" />');
									-->
									</script>
									<noscript><input value="{L_PREVIEW}" type="submit" name="prw_convers" class="submit" /></noscript>
									&nbsp;&nbsp; 
									<input type="reset" value="{L_RESET}" class="reset" />
							</fieldset>	
						</div>
					</div>
					<div class="module_bottom_l"></div>		
					<div class="module_bottom_r"></div>
					<div class="module_bottom">&bull; {post_convers.U_MEMBER_VIEW} &raquo; {post_convers.U_PM_BOX}</div>
				</div>
			</div>
		</form>
		# END post_convers #
		