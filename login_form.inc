
<h2>Login or Register</h2>

<fieldset id="login_form" class="right"><legend>Login</legend>
  <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <div><label for="uid">User name:</label>
    <input type="text" required="required" name="uid" /></div>
    <div><label for="pwd">Password:</label>
    <input type="password" required="required" name="pwd" /></div>
    <div><input type="submit" class="button" value="Log in" /></div>
  </form>
  <p><a href="reset_password.php">Forgot password?</a></p>
</fieldset>

<fieldset id="rego_form"><legend>Register</legend>
  <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="rego" value="1" />
    <div><label for="new_uid">User name:</label>
    <input type="text" required="required" name="new_uid" /></div>
    <div><label for="new_pwd">Password:</label>
    <input id="new_pwd" type="password" required="required" name="new_pwd" /></div>
    <div><label for="conf_pwd">Confirm password:</label>
    <input id="conf_pwd" type="password" required="required" name="conf_pwd" /></div>
    <div><label for="new_email">email address:</label>
    <input type="email" required="required" name="new_email" /></div>
    <div><input type="submit" class="button" value="register" /></div>
  </form>
</fieldset>
