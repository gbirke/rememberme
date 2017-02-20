<p xmlns="http://www.w3.org/1999/html">You are logged in as <strong><?php echo $_SESSION['username']; ?></strong></p>
<p>Your session ID is  <strong><?php echo session_id(); ?></strong></p>

<?php if (!empty($_COOKIE['REMEMBERME'])) : ?>
        <p>The <strong>remember me cookie is active</strong>.
          Cookie value is <code><?php echo $_COOKIE['REMEMBERME']; ?></code></p>
        <p>Close the browser window and reopen it to test the "remember me" functionality.</p>
<?php else : ?>
        <p>The <strong>remember me cookie is not active</strong>.</p>
<?php endif; ?>     

<?php if (!empty($_SESSION['remembered_by_cookie'])) : ?>
        <p><strong>You were logged in with the "Remember me" cookie</strong>. In a real application
          you should ask the user for his credentials before allowing him anything
          "dangerous" like changing the login information, accessing sensitive data
          or making a payment.</p>
<?php else : ?>
    <p><strong>You logged in through the login form</strong>, the "Remember me" cookie was <em>not</em> used for authentication.</p>
<?php endif; ?>


<p>If you want to test the warning when a possible identity theft is detected, try the following steps:</p>
<ol>
  <li>Login to this page with a non-Firefox Browser. In the following steps I
    will call that browser "Chrome" :) <br>
    Make sure to check the "Remember me" checkbox when logging in.</li>
  <li>Copy the cookie value from above into the clipboard.</li>
  <li>Quit Chrome to end the session.</li>
  <li>Start Firefox and install the <a href="https://addons.mozilla.org/de/firefox/addon/6683/">Firecookie</a> extension if needed.</li>
  <li>Show this example page in Firefox. You should see the login form. If you see this text, log out.</li>
  <li>Create the <code>PHP_REMEMBERME</code> cookie with the value you copied.</li>
  <li>Refresh the page - you are now logged in and should see this text. You have stolen the login credential from Chrome!</li>
  <li>Start Chrome and try to show this page - you should get a warning instead of the login dialog.</li>
  <li>Refresh this page in Firefox - You are logged out.</li>
</ol>
<p><a href="/logout">Log out in this browser window (clear the remember me cookie).</a></p>
<p><a href="/completelogout">Log out from <strong>all</strong>
  sessions in all browser windows where the "Remember me" cookie is active.</a></p>