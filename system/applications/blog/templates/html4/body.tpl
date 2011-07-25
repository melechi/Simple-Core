<div id="breadcrumbs">
	{$:BREADCRUMBS}
</div>
<hr />
<div id="messageBox">
{IF:COUNT($:FEEDBACK[errors])==true}{EACH:$:FEEDBACK[errors]=MESSAGE}
	<p class="error">{MESSAGE}</p>{ENDEACH}{ENDIF}
{IF:COUNT($:FEEDBACK[messages])==true}{EACH:$:FEEDBACK[messages]=MESSAGE}
	<p class="message">{MESSAGE}</p>{ENDEACH}{ENDIF}
</div>
		<div id='content-wrapper'>
			<div id='crosscol-wrapper' style='text-align:center'>
				<div class='crosscol section' id='crosscol'></div>
			</div>
			<div id='main-wrapper'>
				<div class='main section' id='main'>
					<div class='blog-posts hfeed'>
					{TEMPLATE:$:CONTENT}
					</div>
					<div class='blog-pager' id='blog-pager'>
						<span id='blog-pager-older-link'><a class='blog-pager-older-link' href=
						'http://melechi.blogspot.com/2008/11/object-orientated-javascript-part-1.html'
						id='Blog1_blog-pager-older-link' title='Older Post' name=
						"Blog1_blog-pager-older-link">Older Post</a></span> <a class=
						'home-link' href='http://melechi.blogspot.com/'>Home</a>
					</div>
					<div class='clear'></div>
					<div class='post-feeds'>
						<div class='feed-links'>
						Subscribe to: <a class='feed-link' href=
						'http://melechi.blogspot.com/feeds/7767244037163398438/comments/default'
						target='_blank' type='application/atom+xml'>Post Comments (Atom)</a>
						</div>
					</div>
				</div>
			</div>
			<div id='sidebar-wrapper'>
			<div class='sidebar section' id='sidebar'>
				<div class='widget'>
					<h2>Blog Archive</h2>
					<div id='ArchiveList'>
						<div id='BlogArchive1_ArchiveList'>
							TODO
						</div>
						<div class='clear'></div>
					</div>
				</div>
				<div class='widget'>
					<h2>About Me</h2>
					TODO
					<div class='clear'></div>
				</div>
				<div class='widget'>
					<h2>Links</h2>
					TODO
					<div class='clear'></div>
				</div>
			</div>
			</div><!-- spacer for skins that want sidebar and main to be the same height-->
			<div class='clear'>
			&nbsp;
			</div>
		</div><!-- end content-wrapper -->