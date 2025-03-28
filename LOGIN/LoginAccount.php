<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 25px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        .google-btn img {
            width: 20px;
            margin-right: 10px;
        }
        .google-btn:hover {
            background: #f4f4f4;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h3 class="text-primary">Login</h3>

    <?php if (isset($email)): ?>
        <p>Welcome, <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($email) ?>)</p>
        <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="rounded-circle" width="100">
        <br>
        <a href="?logout=true" class="btn btn-danger mt-3 w-100">Logout</a>
    <?php else: ?>
        <form action="Sign-inFunction.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="mt-3">
            <p class="mb-0">Don't have an account?</p>
            <a href="RegisterAccount.php" class="btn btn-outline-primary w-100">Sign Up</a>
        </div>
        </form>

        <a href="<?= htmlspecialchars($client->createAuthUrl()) ?>" class="google-btn mt-3">
            <img src="google-icon.png" alt="Google Icon"> Sign in with Google
        </a>

        <div class="mt-3">
            <p class="mb-0">Don't have an account?</p>
            <a href="RegisterAccount.php" class="btn btn-outline-primary w-100">Sign Up</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
