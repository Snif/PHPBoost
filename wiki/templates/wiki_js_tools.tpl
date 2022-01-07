<script>
	var tinymce_editor       = {C_TINYMCE_EDITOR};
	var enter_text           = "{@wiki.warning.link.name}";
	var title_link           = "{@wiki.link.title}";
	var enter_paragraph_name = "{@wiki.warning.paragraph.name}";
	var title_paragraph      = "{@wiki.paragraph.name}";
</script>

<script src="{PICTURES_DATA_PATH}/js/wiki# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PICTURES_DATA_PATH}/js/bbcode.wiki# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>

<div class="bbcode wiki-extended-bbcode">
	<nav class="bbcode-containers">
		<ul id="wiki-paragraph-container" class="bbcode-container cell-flex cell-tile cell-modal modal-container">
			<li class="bbcode-elements">
				<span data-modal data-target="block-link" class="bbcode-button" aria-label="{@wiki.insert.link}">
					<i class="fa fa-link" aria-hidden="true"></i>
				</span>
				<div id="block-link" class="modal modal-animation">
					<div class="close-modal">{@common.close}</div>
					<div class="content-panel cell">
						<div class="cell-header"><span class="cell-name">{@wiki.insert.link}</span></div>
						<div class="cell-form">
							<label for="bb_wiki_link" class="cell-label">{@wiki.link.title}</label>
							<div class="cell-input">
								<input id="bb_wiki_link" type="text">
							</div>
						</div>
						<div class="cell-footer cell-input">
							<span class="button hide-modal" onclick="insert_wiki_link();">{@form.insert}</span>
						</div>
					</div>
				</div>
			</li>
			<!-- <li class="bbcode-elements">
				<span data-modal data-target="block-paragraph-1" class="bbcode-button" aria-label="{L_PARAGRAPH_1}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">1</span>
					</span>
				</span>
				<div id="block-paragraph-1" class="modal modal-animation">
					<div class="close-modal">{@common.close}</div>
					<div class="content-panel cell">
						<div class="cell-header"><span class="cell-name">{L_PARAGRAPH_1}</span></div>
						<div class="cell-form">
							<label for="bb_wiki_paragraph-1" class="cell-label">{@wiki.paragraph.name}</label>
							<div class="cell-input">
								<input class="bb-wiki-paragraph" id="bb_wiki_paragraph-{LEVEL}" type="text">
							</div>
						</div>
						<div class="cell-footer cell-input">
							<span class="button hide-modal" onclick="insert_wiki_paragraph(1);">{@form.insert}</span>
						</div>
					</div>
				</div>
			</li> -->
			<li class="bbcode-elements">
				<a href="javascript:insert_paragraph(1);" class="bbcode-button" aria-label="{L_PARAGRAPH_1}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">1</span>
					</span>
				</a>
			</li>
			<li class="bbcode-elements">
				<a href="javascript:insert_paragraph(2);" class="bbcode-button" aria-label="{L_PARAGRAPH_2}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">2</span>
					</span>
				</a>
			</li>
			<li class="bbcode-elements">
				<a href="javascript:insert_paragraph(3);" class="bbcode-button" aria-label="{L_PARAGRAPH_3}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">3</span>
					</span>
				</a>
			</li>
			<li class="bbcode-elements">
				<a href="javascript:insert_paragraph(4);" class="bbcode-button" aria-label="{L_PARAGRAPH_4}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">4</span>
					</span>
				</a>
			</li>
			<li class="bbcode-elements">
				<a href="javascript:insert_paragraph(5);" class="bbcode-button" aria-label="{L_PARAGRAPH_5}">
					<span class="stacked">
						<i class="fa fa-fw fa-newspaper" aria-hidden="true"></i>
						<span class="stack-event stack-circle stack-sub stack-right bgc-full link-color stack-wiki">5</span>
					</span>
				</a>
			</li>
			<li class="bbcode-elements">
				<a href="https://www.phpboost.com/wiki/utilisation-du-module-wiki#paragraph-creer-un-sommaire" class="bbcode-button offload" aria-label="{@wiki.help.tags}"><i class="far fa-fw fa-question-circle" aria-hidden="true"></i></a>
			</li>
		</ul>
	</nav>
</div>
<noscript><div>{@wiki.no.js.insert.link}</div></noscript>
