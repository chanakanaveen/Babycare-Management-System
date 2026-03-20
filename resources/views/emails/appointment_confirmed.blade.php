<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmed</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #16a34a, #15803d); padding: 30px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 24px; }
        .icon { font-size: 48px; margin-bottom: 12px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.7; font-size: 15px; margin: 0 0 16px; }
        .info-card { background: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .info-card td { padding: 8px 0; color: #374151; font-size: 14px; }
        .info-card td:first-child { font-weight: 600; width: 140px; color: #166534; }
        .cta { text-align: center; margin: 28px 0; }
        .cta a { display: inline-block; background: #16a34a; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✅</div>
            <h1>Appointment Confirmed</h1>
        </div>
        <div class="body">
            <p>Dear Parent,</p>
            <p>Your appointment has been confirmed by your midwife!</p>
            <div class="info-card">
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td>Midwife</td><td>{{ $midwifeName }}</td></tr>
                    <tr><td>Date</td><td><strong>{{ $appointmentDate }}</strong></td></tr>
                    <tr><td>Time</td><td><strong>{{ $appointmentTime }}</strong></td></tr>
                </table>
            </div>
            <p>Please be on time for your appointment. You can use the chat feature to communicate with your midwife if needed.</p>
            <div class="cta">
                <a href="{{ url('/parent/appointments') }}">View Appointment</a>
            </div>
        </div>
        <div class="footer">
            <p>BabyCare Management System — Automated Notification</p>
        </div>
    </div>
</body>
</html>
