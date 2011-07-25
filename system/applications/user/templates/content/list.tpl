{$:FORM_LIST}
<hr />
<form method="post" action="">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th>Delete</th>
			<th>ID</th>
			<th>Name</th>
		</tr>
		{$LIST:RECORDS}
	</table>
	<input type="submit" value="Delete Selected Users" />
</form>