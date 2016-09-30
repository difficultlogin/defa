<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Callback form</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<form action="callback.php" id="callback-form" method="POST">
    <p>
        <label for="name">ФИО:</label>
        <input id="name" type="text" name="name" required>
    </p>
    <p>
        <label for="phone">Телефон:</label>
        <input id="phone" type="tel" name="phone" required>
    </p>
    <p>
        <label for="email">E-mail:</label>
        <input id="email" type="email" name="email" required>
    </p>
    <p>
        <label for="comment">Комментарий:</label>
        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
    </p>
    <hr>
    <p>
        <input type="submit">
    </p>
</form>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>