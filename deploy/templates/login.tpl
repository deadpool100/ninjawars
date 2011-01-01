<h1>Login</h1>

{if $logged_out}
<div class='notice'>You logged out! Log in again below if you want.</div>
{/if}

{if $login_error}
	  <!-- This section only gets displayed in the event of an incorrect login -->
      <div id='login-error' class="error">
        {$login_error|escape}
      </div>
{/if}


{if $is_logged_in}
<div>
  You are already logged in!
</div>
{else}

<style type='text/css'>
{literal}
.right-side{
	display:block;
	text-align:left;
}
.left-side{
	display:block;
	text-align:left;
}
.central{
	text-align:center;
}
.left-side a{
	font-size:.8em;
}
.line{
	margin-bottom: .5em;
	font-size: 1.4em;
}
.outer-box{
	display:inline-block;
	margin-left:auto;
	margin-right:auto;
	border: 10px rgb(20, 20, 20) solid;
	background-color:rgb(50, 50, 50);
	padding:1em 4em;
}
.outer-outer-box{
	display:inline-block;
	border: 5px rgb(10, 10, 10) solid;
}
{/literal}
</style>

<div class='login-page' style='margin: .3em auto .3em;text-align:center;'>
  <form id="login-form" action="login.php" method="post">
	  <input type="hidden" name="ref" value="{$referrer|escape}">
	    <div class='outer-outer-box'>
	  	<div class='outer-box'>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Ninja Name or Email</span>
	      <input tabindex=1 name="user" type="text" value='{$stored_username|escape}' class='right-side'>
		  </div>
	    </label>
	    <label>
	      <div class='line'>
	      <span class='left-side'>Password <a tabindex=4 href='account_issues.php'>(Forgot password?)</a></span>
	      <input tabindex=2 name="pass" type="password" class='right-side'>
	      </div>
	    </label>
	    <div class='left-side'>
		    <input tabindex=3 name="login_request" type="submit" value="Login" style='font-size:2em;width:100%;'>
		</div>
	    </div>
	    </div>
	</form>
</div>

{/if}

<div id='login-bottom-bar-container' style='margin: 5em auto .5em;width:96%;padding:.2em;border: 1px solid #993300;'>
	<div id="login-problems" style='padding: 0 auto 0;text-align:center;background-color: rgba(30, 30, 30, 0.70);'>
	  <span class="signup-link">
		<a target="main" href="signup.php?referrer={$referrer|escape}">Become a Ninja!</a> |
	  </span>
	  <span>
		<a href="account_issues.php" target="main" class="blend side">Login or Signup Problems?</a>
	  </span>
	</div>
</div>
