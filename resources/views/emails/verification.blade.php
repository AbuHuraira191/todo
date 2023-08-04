<!DOCTYPE html>
<html>
<head>
    <title>Verify your email</title>
</head>
<body>
<h2>Hello, {{ $user->name }}!</h2>
<p>Please use the following verification code to verify your email:</p>
<p><strong>{{ $user->verification_code }}</strong></p>
</body>
</html>
