<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .row div {
            flex: 1;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .checkbox-container input {
            width: auto;
            margin-right: 10px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        .btn:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Sign Up</h3>
    <form action="Sign-upFunction.php" method="POST">
    <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

        <div class="row">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Full name" required>
            </div>
        </div>

        <label for="nickname">Nickname</label>
        <input type="text" id="nickname" name="nickname" placeholder="Enter your nickname">

        <label for="rank">What is your current rank?</label>
        <select id="rank" name="rank" required>
            <option value="" disabled selected>Select your rank</option>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
            <option value="Expert">Expert</option>
        </select>

        <div class="checkbox-container">
            <input type="checkbox" id="confirmed" name="confirmed" required>
            <label for="confirmed">✅ I confirm that my information is correct</label>
        </div>

        <button type="submit" class="btn">Sign Up</button>
        <a href="LoginAccount.php" class="signup-link">have an account? Login</a>

    </form>

    <div class="message" id="message"></div>
</div>

<script>
    document.getElementById("signupForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent normal form submission

        const formData = new FormData(this);

        fetch("LoginAccount.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById("message").textContent = data;
        })
        .catch(error => console.error("Error:", error));
    });
</script>

</body>
</html>
