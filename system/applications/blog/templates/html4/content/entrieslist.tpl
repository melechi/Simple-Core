{EACH:$:BLOGENTRIES=*}
<div class="post hentry uncustomized-post-template">
	<h3 class="post-title entry-title">
		<a href="{*:blog_entry_url}">{*:blog_entry_title}</a>
	</h3>
	<div class="post-header-line-1"></div>
	<div class="post-body entry-content">
		{*:blog_entry_body}
		<div style="clear: both;"></div>
	</div>
	<div class="post-footer">
		<div class="post-footer-line post-footer-line-1">
			<span class="post-author vcard">Posted by: <a href="user/{*:blog_entry_user_id}/"></a>{*:blog_entry_user_name} On: 
				<span class="post-timestamp">
					<a class="timestamp-link" href="{*:blog_entry_url}" rel="bookmark" title="permanent link">
						<abbr class="published">{*:blog_entry_timestamp}</abbr>
					</a>
				</span>
			</span>
			<div class="post-footer-line post-footer-line-2">
				<span class="post-labels">Labels: {*:blog_entry_labels}</span>
			</div>
			<div class="post-footer-line post-footer-line-3"></div>
		</div>
	</div>
</div>
{ENDEACH}