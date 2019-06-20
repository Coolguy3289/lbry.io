<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <title>Verify Your Identity</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>

    <link rel="icon" href="images/favicon.ico"/>

    <script src='https://www.google.com/recaptcha/api.js?' async defer></script>

    <script>
      const ENDPOINT = '<?php echo LBRY::getApiUrl('/user_email/confirm')?>'

      var verifyUser = function(temporary_auth_token, email, verification_token, recaptcha) {

          const url = `${ENDPOINT}?auth_token=${temporary_auth_token}&email=${encodeURIComponent(email)}&verification_token=${verification_token}&recaptcha=${recaptcha}`;
          fetch(url)
              .then(response => response.json())
              .then((response) => {
                  if (response.error) {
                      throw Error(response.error)
                  }
                  document.getElementById("title").textContent = "Done!"
                  document.getElementById("verify").style.display = "none";
                  document.getElementById("verify-success").style.display = "block";
              })
              .catch(error => {
                  document.getElementById("title").textContent = "Uh oh"
                  document.getElementById("verify").style.display = "none";
                  document.getElementById("verify-error").style.display = "block";
                  document.getElementById("verify-error-text").textContent = error.message;
              })
      }

      var verifyCallback = function(response) {
          const urlParams = new URLSearchParams(window.location.search);
          const temporary_auth_token = urlParams.get('auth_token');
          const email = urlParams.get('email')
          const verification_token = urlParams.get('verification_token');
          // In the future we will have an `origin` query, to smartly redirect (or tell users they are verified)
          // eg. if origin == "android" and device == "android" open the app, else say "your android app is verified"
          verifyUser(temporary_auth_token, email, verification_token, response);

          document.getElementById("captcha-block").style.display = "none";
          document.getElementById("verify").style.display = "block";
      }

      var expiredCallback = function(error) {
          console.log("expired", error)
      }
    </script>
  </head>

  <body>
    <main class="ancillary">
      <section class="hero hero--half-height">
        <div class="inner-wrap inner-wrap--center-hero">
          <h1 id="title">Almost Done!</h1>
        </div>
      </section>

      <section>
        <div class="inner-wrap inner-wrap--center">
          <div id="captcha-block">
            <p>Click the captcha to continue...</p>
            <br/>
            <div class="g-recaptcha" data-sitekey="6LePsJgUAAAAAFTuWOKRLnyoNKhm0HA4C3elrFMG" data-callback="verifyCallback" data-expired-callback="expiredCallback"></div>
          </div>

          <div style="display: none; margin-top: 10px;" id="verify">
            <p>Verifying...</p>
          </div>

          <div style="display: none; margin-top: 10px;" id="verify-error">
            <code id="verify-error-text"></code>
            <p style="margin-top: 10px">There was an error. Please make sure you are clicking the latest verification email and try again. Contact help@lbry.com if this keeps happening.</p>
          </div>

          <div style="display: none; margin-top: 10px;" id="verify-success">
            <p class="spacer1">Success! Your email is now verified.</p>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
