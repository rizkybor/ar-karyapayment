{{-- <!DOCTYPE html>
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
</html> --}}

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color: #f4f4f4;">

    <table width="100%" bgcolor="#f4f4f4" cellpadding="0" cellspacing="0" style="padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" bgcolor="#ffffff" cellpadding="0" cellspacing="0" style="border-radius: 10px; padding: 30px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom: 20px;">
                            <img src="{{ $logo_url }}" alt="Prime Billing Service" width="150" style="display:block;">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td align="center" style="color:#333333; font-size:22px; font-weight:bold; padding-bottom: 10px;">
                            Hello, {{ $notifiable->name }}!
                        </td>
                    </tr>

                    <!-- Message -->
                    <tr>
                        <td align="center" style="color:#555555; font-size:16px; line-height:1.5; padding-bottom: 20px;">
                            Kami menerima permintaan untuk mereset password akun Anda.
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td align="center" style="padding: 30px 0;">
                            <a href="{{ route('password.reset', ['token' => $token]) }}"
                               style="background-color:#007bff; color:white; padding:14px 25px; text-decoration:none; font-size:16px; font-weight:bold; border-radius:5px; display:inline-block;">
                                Reset Password
                            </a>
                        </td>
                    </tr>

                    <!-- Note -->
                    <tr>
                        <td align="center" style="color:#555555; font-size:14px; line-height:1.5;">
                            Jika Anda tidak meminta reset password, abaikan email ini.
                        </td>
                    </tr>

                    <!-- Thank You -->
                    <tr>
                        <td align="center" style="color:#333333; font-size:14px; font-weight:600; padding-top: 15px;">
                            Terima kasih telah menggunakan layanan kami!
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="color:#888888; font-size:13px; padding-top:30px;">
                            Salam, <br><strong>Tim Support PT. Karya Prima Usahatama</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>