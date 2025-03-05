<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo {
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            font-size: 22px;
        }

        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .button-container {
            margin: 30px 0;
        }

        .reset-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 14px 25px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .reset-button:hover {
            background-color: #0056b3;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 30px;
        }

    </style>
</head>
<body>

    <div class="container">
        <!-- LOGO -->
        <div class="logo">
            <img src="{{ $logo_url }}" alt="Logo" style="width: 150px;">
        </div>

        <!-- JUDUL -->
        <h2>Hello, {{ $notifiable->name }}!</h2>

        <!-- PESAN -->
        <p>Kami menerima permintaan untuk mereset password akun Anda.</p>

        <!-- TOMBOL RESET PASSWORD -->
        <div class="button-container">
            <a href="{{ route('password.reset', ['token' => $token]) }}" class="reset-button">
                Reset Password
            </a>
        </div>

        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>

        <p><strong>Terima kasih telah menggunakan layanan kami!</strong></p>

        <p class="footer">Salam, <br> <strong>Tim Support PT. Karya Prima Usahatama</strong></p>
    </div>

</body>
</html>