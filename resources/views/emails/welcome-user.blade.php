<!DOCTYPE html>
<html>

<head>
    <title>Welcome to Mymore Flight and Ticketing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f7f7fb;">
    <div style="background: #808080; padding: 20px; text-align: center;">
        <img src="https://flightandticketing.com/images/logo.png" alt="Mymore Flight and Ticketing"
            style="max-width: 200px; height: auto;">
    </div>

    <div
        style="background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%); padding: 35px 25px; text-align: center; border-bottom: 1px solid #e0e6ff;">
        <h1 style="color: #97050C; margin: 0; font-weight: 300; letter-spacing: 1px; font-size: 28px;">Welcome Onboard</h1>
    </div>

    <div
        style="background-color: white; padding: 0; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 20px;">
        <div style="padding: 30px 30px 20px 30px; border-bottom: 1px solid #f0f0f7;">
            <h2 style="color: #333; font-weight: 400; margin-top: 0; font-size: 20px;">
                Hello{{ !empty($name) ? ', ' . $name : '' }},
            </h2>
            <p style="font-size: 16px; color: #555; margin-bottom: 15px;">
                Welcome to Mymore Flight and Ticketing platform. An account has been automatically created for you on <a href="https://flightandticketing.com" style="color: #97050C; text-decoration: none;">flightandticketing.com</a> based on your recent booking.
            </p>
            <p style="font-size: 16px; color: #555; margin-bottom: 0;">
                To access your account, please use the "Forgot Password" feature on our website or mobile app to set your preferred password.
            </p>
        </div>

        <div style="padding: 30px 30px 30px 30px;">
            <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 18px;">Next Steps</h2>

            <div style="background-color: #f8faff; border-radius: 8px; padding: 20px; border: 1px solid #e7eeff;">
                <p style="font-size: 14px; color: #555; margin: 0; padding-left: 15px; border-left: 3px solid #0047AB;">
                    Visit the login page and click on <strong>"Forgot Password"</strong>. Enter your email address to receive a password reset link.
                </p>
            </div>
        </div>
    </div>

    <div
        style="background-color: #97050C; padding: 40px 20px; text-align: center; color: white; border-radius: 0 0 8px 8px; margin: 0 20px 20px 20px;">
        <h3 style="margin: 0 0 20px 0; font-weight: 300; letter-spacing: 1px; color: #ffffff;">Mymore Flight and Ticketing</h3>
        <p style="margin: 0; font-size: 14px; color: #f0f0f0;">Thank you for choosing us.</p>
    </div>
</body>

</html>
