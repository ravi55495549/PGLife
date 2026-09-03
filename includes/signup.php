<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGLife - Signup</title>
</head>
<body>

    <h2>Create PGLife Account</h2>

    <!-- Form Snippet yahan Body ke andar aayega -->
    <form action="signup_submit.php" method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required><br><br>
        <input type="text" name="phone" placeholder="Phone Number" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="text" name="college_name" placeholder="College Name" required><br><br>
        
        <label><input type="radio" name="gender" value="male" required> Male</label>
        <label><input type="radio" name="gender" value="female" required> Female</label><br><br>

        <button type="submit" name="signup">Create Account</button>
    </form>

</body>
</html>