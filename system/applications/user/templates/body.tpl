<div id="breadcrumbs">
	{$:BREADCRUMBS}
</div>
<br />
<hr />
<div id="messageBox">
{IF:COUNT($:FEEDBACK[errors])==true}{EACH:$:FEEDBACK[errors]=MESSAGE}
	<p class="error">{MESSAGE}</p>{ENDEACH}{ENDIF}
{IF:COUNT($:FEEDBACK[messages])==true}{EACH:$:FEEDBACK[messages]=MESSAGE}
	<p class="message">{MESSAGE}</p>{ENDEACH}{ENDIF}
</div>
<div id="bodyContent">
<br />
		{TEMPLATE:$:CONTENT}
</div>