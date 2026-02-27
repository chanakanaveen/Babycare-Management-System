<p>Dear {{ $admin->name }} </p>
<br>
<p>Your password was change sucsessfully.
   Here is your new login details:
   <br>
   <b>Login ID:</b> {{ $admin->email }} or {{ $admin->username }}
   <br>
   <b>Password:</b> {{ $new_password }}
</p>
<br>
<p>Thank you</p>
