<p> Dear {{ $admin->name }} </p>
<p>
    We are received a request to reset for BabyBloom Care account accociate with {{ $admin->email }}. If you made this request, please click the link below to reset your password.
    <br>
    <a href="{{ $actionLink }}" target="_blank" > Reset Password </a>
    <br>
    <b> NB: </b> This link will validate with 15 minutes.
    <br>
    If you did not request for a password reset, please ignore this email.
</p>
