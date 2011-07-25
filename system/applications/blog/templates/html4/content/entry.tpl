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
			<span class="post-author vcard">Posted: 
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
		
		<div class="comments" id="comments">
			<a name="comments" id="comments"></a>
			<h4>{*:blog_entry_numComments} comments:</h4>
			<dl class="avatar-comment-indent" id="comments-block"></dl>
			<p class="comment-footer">
				<a href="https://www.blogger.com/comment.g?blogID=28184760&amp;postID=7767244037163398438" onclick="">Post a Comment</a>
			</p>
			<div id="backlinks-container">
				<div id="Blog1_backlinks-container"></div>
			</div>
		</div>
		
	</div>
</div>
{ENDEACH}