<table id="table">
	# IF C_ARCHIVES #
	<thead>
		<tr>
			# IF NOT C_SPECIFIC_STREAM #
			<th>
				<a href="{SORT_STREAM_TOP}" aria-label="${LangLoader::get_message('sort.asc', 'common')}"><i class="fa fa-table-sort-up" aria-hidden="true" title="${LangLoader::get_message('sort.asc', 'common')}"></i></a>
				{@archives.stream_name}
				<a href="{SORT_STREAM_BOTTOM}" aria-label="${LangLoader::get_message('sort.desc', 'common')}"><i class="fa fa-table-sort-down" aria-hidden="true" title="${LangLoader::get_message('sort.desc', 'common')}"></i></a>
			</th>
			# ENDIF #
			<th>
				<a href="{SORT_SUBJECT_TOP}" aria-label="${LangLoader::get_message('sort.asc', 'common')}"><i class="fa fa-table-sort-up" aria-hidden="true" title="${LangLoader::get_message('sort.asc', 'common')}"></i></a>
				{@archives.name}
				<a href="{SORT_SUBJECT_BOTTOM}" aria-label="${LangLoader::get_message('sort.desc', 'common')}"><i class="fa fa-table-sort-down" aria-hidden="true" title="${LangLoader::get_message('sort.desc', 'common')}"></i></a>
			</th>
			<th>
				<a href="{SORT_DATE_TOP}" aria-label="${LangLoader::get_message('sort.asc', 'common')}"><i class="fa fa-table-sort-up" aria-hidden="true" title="${LangLoader::get_message('sort.asc', 'common')}"></i></a>
				{@archives.date}
				<a href="{SORT_DATE_BOTTOM}" aria-label="${LangLoader::get_message('sort.desc', 'common')}"><i class="fa fa-table-sort-down" aria-hidden="true" title="${LangLoader::get_message('sort.desc', 'common')}"></i></a>
			</th>
			<th>
				<a href="{SORT_SUBSCRIBERS_TOP}" aria-label="${LangLoader::get_message('sort.asc', 'common')}"><i class="fa fa-table-sort-up" aria-hidden="true" title="${LangLoader::get_message('sort.asc', 'common')}"></i></a>
				{@archives.nbr_subscribers}
				<a href="{SORT_SUBSCRIBERS_BOTTOM}" aria-label="${LangLoader::get_message('sort.desc', 'common')}"><i class="fa fa-table-sort-down" aria-hidden="true" title="${LangLoader::get_message('sort.desc', 'common')}"></i></a>
			</th>
			# IF C_MODERATE #
			<th></th>
			# ENDIF #
		</tr>
	</thead>
		<tbody>
		# START archives_list #
			<tr>
				# IF NOT C_SPECIFIC_STREAM #
				<td>
					<a href="{archives_list.U_VIEW_STREAM}">{archives_list.STREAM_NAME}</a>
				</td>
				# ENDIF #
				<td>
					<a href="{archives_list.U_VIEW_ARCHIVE}">{archives_list.SUBJECT}</a>
				</td>
				<td>
					{archives_list.DATE}
				</td>
				<td>
					{archives_list.NBR_SUBSCRIBERS}
				</td>
				# IF C_MODERATE #
				<td>
					<a href="{archives_list.U_DELETE_ARCHIVE}" aria-label="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="fa fa-delete" aria-hidden="true" title="${LangLoader::get_message('delete', 'common')}"></i></a>
				</td>
				# ENDIF #
			</tr>
		# END archives_list #
	</tbody>
	# ELSE #
	<tbody>
		<tr>
			<td>
				<span class="text-strong">{@archives.not_archives}</span>
			</td>
		</tr>
	</tbody>
	# ENDIF #
	# IF C_PAGINATION #
	<tfoot>
		<tr>
			<td colspan="{NUMBER_COLUMN}">
				# INCLUDE PAGINATION #
			</td>
		</tr>
	</tfoot>
	# ENDIF #

</table>
