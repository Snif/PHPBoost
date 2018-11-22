		<script>
		<!--
			function add_filter(nbr_filter)
			{
				if (typeof this.max_filter_p == 'undefined' )
					this.max_filter_p = nbr_filter;
				else
					this.max_filter_p++;

				var new_id = this.max_filter_p + 1;
				document.getElementById('add_filter' + this.max_filter_p).innerHTML +=
					'<p id="filter' + new_id + '">{PATH_TO_ROOT} / <select name="filter_module' + new_id + '" id="filter_module' + new_id + '">' +
					# START modules #
					'<option value="{modules.ID}">{modules.ID}</option>' +
					# END modules #
					'</select> / <input type="text" name="f' + new_id + '" id="f' + new_id + '" value="">' +
					' &nbsp;<a href="javascript:delete_filter(' + new_id + ');" aria-label="' + ${escapejs(LangLoader::get_message('delete', 'common'))} + '"><i class="fa fa-delete" aria-hidden="true" title="' + ${escapejs(LangLoader::get_message('delete', 'common'))} + '"></i></a>' +
					'</p><span id="add_filter' + new_id + '"></span>';
			}
			function delete_filter(id) {
				document.getElementById('f' + id).value = '_deleted';
				document.getElementById('filter_module' + id).value = '';
				document.getElementById('filter' + id).style.display = 'none';
			}
		-->
		</script>

		<fieldset>
			<legend>{@filters}</legend>
			<p>{@links_menus_filters_explain}</p>
			<div class="fieldset-inset">
				<div class="form-element full-field right">
					<label>{@filters}</label>
					<div class="form-field">
						# START filters #
						<p id="filter{filters.ID}">
							{PATH_TO_ROOT} /
							<select name="filter_module{filters.ID}" id="filter_module{filters.ID}">
								# START filters.modules #
								<option value="{filters.modules.ID}"{filters.modules.SELECTED}>{filters.modules.ID}</option>
								# END filters.modules #
							</select>
							/ <input type="text" name="f{filters.ID}" id="f{filters.ID}" value="{filters.FILTER}">
							&nbsp;<a href="javascript:delete_filter({filters.ID});" aria-label="${LangLoader::get_message('delete', 'common')}"><i class="fa fa-delete" aria-hidden="true" title="${LangLoader::get_message('delete', 'common')}"></i></a>
						</p>
						# END filters #

						<span id="add_filter{NBR_FILTER}"></span>
						<p class="center">
							<a href="javascript:add_filter({NBR_FILTER})" aria-label="{@add_filter}"><i class="fa fa-plus" aria-hidden="true" title="{@add_filter}"></i></a>
						</p>
					</div>
				</div>
			</div>
	    </fieldset>
