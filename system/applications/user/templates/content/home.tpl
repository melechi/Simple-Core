{IF:$:AUTHENTICATED==false}
<p>Welcome Unauthenticated User. Please <a href="{URL:/login/}">Login</a> or <a href="{URL:/register/}">Register</a> for a FREE account.</p>
{ELSE}
<p>Welcome {$:EMAIL}, you are Authenticated.</p>
{ENDIF}