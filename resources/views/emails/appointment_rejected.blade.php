<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Declined</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 30px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 24px; }
        .icon { font-size: 48px; margin-bottom: 12px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.7; font-size: 15px; margin: 0 0 16px; }
        .info-card { background: #fef2f2; border-left: 4px solid #dc2626; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .info-card td { padding: 8px 0; color: #374151; font-size: 14px; }
        .info-card td:first-child { font-weight: 600; width: 140px; color: #991b1b; }
        .cta { text-align: center; margin: 28px 0; }
        .cta a { display: inline-block; background: #2563eb; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>Appointment Declined</h1>
        </div>
        <div class="body">
            <p>Dear Parent,</p>
            <p>Unfortunately, your appointment request has been declined by your midwife.</p>
            <div class="info-card">
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td>Midwife</td><td>{{ $midwifeName }}</td></tr>
                    <tr><td>Date</td><td>{{ $appointmentDate }}</td></tr>
                    <tr><td>Time</td><td>{{ $appointmentTime }}</td></tr>
                    <tr><td>Reason</td><td>{{ $rejectionReason }}</td></tr>
                </table>
            </div>
            <p>You can book a new appointment at a different time.</p>
            <div class="cta">
                <a href="{{ url('/parent/appointments/book') }}">Book New Appointment</a>
            </div>
        </div>
        <div class="footer">
            <p>BabyCare Management System — Automated Notification</p>
        </div>
    </div>
</body>
</html>
