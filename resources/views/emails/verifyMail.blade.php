<!DOCTYPE html>
<html>
  <head>
    <title>Welcome Email</title>
  </head>
  <body>
    <h2>Welcome to the site {{$user['first_name']}} {{$user['last_name']}}</h2>
    <br/>
    Your registered email is {{$user['email']}} , Please click on the below link to verify your email account
    <br/>
    <a href="{{url('users/verify', $user->verifyUser->token)}}">Verify Email</a>
  </body>
</html>