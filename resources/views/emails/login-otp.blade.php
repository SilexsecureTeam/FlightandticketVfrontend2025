<!DOCTYPE html>
<html>

<head>
    <title>Login OTP Verification - Mymore Travels</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f7f7fb;">
    <!-- Top Banner -->
    <div style="background: #808080; padding: 20px; text-align: center;">
        <img src="https://flightandticketing.com/images/logo.png" alt="Mymore Travels"
            style="max-width: 200px; height: auto;">
    </div>

    <!-- Header Section -->
    <div
        style="background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%); padding: 35px 25px; text-align: center; border-bottom: 1px solid #e0e6ff;">
        <h1 style="color: #97050C; margin: 0; font-weight: 300; letter-spacing: 1px; font-size: 28px;">OTP Verification</h1>
    </div>

    <!-- Main Content Container -->
    <div
        style="background-color: white; padding: 0; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 20px;">
        <!-- Message Greeting -->
        <div style="padding: 30px 30px 20px 30px; border-bottom: 1px solid #f0f0f7;">
            <h2 style="color: #333; font-weight: 400; margin-top: 0; font-size: 20px;">Hello,</h2>
            <p style="font-size: 16px; color: #555; margin-bottom: 0;">You are receiving this email because a login attempt was made for your account that requires additional verification.</p>
        </div>

        <!-- OTP Summary Box -->
        <div style="background-color: #f8faff; padding: 25px 30px; border-bottom: 1px solid #f0f0f7; text-align: center;">
            <p style="font-size: 13px; text-transform: uppercase; color: #888; margin: 0 0 10px 0;">Your Verification Code</p>
            <div style="font-size: 42px; color: #97050C; font-weight: bold; letter-spacing: 10px; margin: 0; padding: 10px; border: 2px dashed #97050C; display: inline-block; border-radius: 8px; background: white;">
                {{ $otp }}
            </div>
            <p style="font-size: 14px; color: #777; margin-top: 15px;">
                This code will expire in <strong>10 minutes</strong>.
            </p>
        </div>

        <!-- Information Section -->
        <div style="padding: 30px 30px 30px 30px;">
            <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 18px;">
                <img src="https://img.icons8.com/material-rounded/24/0047AB/info.png" alt="ℹ️"
                    style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;">
                Safety Warning
            </h2>

            <div style="background-color: #f8faff; border-radius: 8px; padding: 20px; border: 1px solid #e7eeff;">
                <p style="font-size: 14px; color: #555; margin: 0; padding-left: 15px; border-left: 3px solid #0047AB;">
                    If you did not attempt to login to your Mymore Travels account, please ignore this email or contact support immediately if you suspect unauthorized access.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div
        style="background-color: #97050C; padding: 40px 20px; text-align: center; color: white; border-radius: 0 0 8px 8px; margin: 0 20px 20px 20px;">
        <h3 style="margin: 0 0 20px 0; font-weight: 300; letter-spacing: 1px; color: #ffffff;">Mymore Travels Security</h3>
        <p style="margin: 0 0 25px 0; font-size: 14px; color: #f0f0f0;">Protecting your account is our priority</p>

        <div style="margin: 0 0 25px 0;">
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/facebook-f.png" alt="fb"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/twitter.png" alt="tw"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/instagram-new.png" alt="ig"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
        </div>
        <p style="color: #f0f0f0; font-size: 12px; margin: 20px 0 0 0;">© 2025 Mymore Travels. All rights reserved.</p>
        <p style="color: #f0f0f0; font-size: 12px; margin: 5px 0 0 0;">Suite A061 H and A Plaza, Wuye, Abuja</p>
    </div>
</body>

</html>
