
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Information</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
    </style>
</head>
<body>

<div class="container">
    <h3>General Information</h3>
    <form id="updateForm">
        <input type="hidden" name="usersid" id="usersid" value="<?= $usersid; ?>">
        <input type="hidden" name="action" value="update">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" readonly>

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname">

        <label for="nickname">Nickname:</label>
        <input type="text" id="nickname" name="nickname">

        <label for="rank">Rank:</label>
        <input type="text" id="rank" name="rank">

        <button type="submit" class="btn">Update</button>
    </form>
</div>

<script>
$(document).ready(function(){
    let usersid = $("#usersid").val();

    // Fetch user data
    $.ajax({
        url: "FetchEditprofile.php",
        type: "POST",
        data: { action: "fetch" },
        dataType: "json",
        success: function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                $("#email").val(response.email);
                $("#password_hash").val(response.password_hash);
                $("#fullname").val(response.fullname);
                $("#nickname").val(response.nickname);
                $("#rank").val(response.rank);
            }
        }
    });

    // Update user data
    $("#updateForm").submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "FetchEditprofile.php",
            type: "POST",
            data: $("#updateForm").serialize(),
            dataType: "json",
            success: function(response) {
                alert(response.success || response.error);
            }
        });
    });
});
</script>

</body>
</html>