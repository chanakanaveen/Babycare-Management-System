<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Scheduled</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 30px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .icon { font-size: 48px; margin-bottom: 12px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.7; margin: 0 0 16px; font-size: 15px; }
        .info-card { background: #eff6ff; border-left: 4px solid #2563eb; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .info-card table { width: 100%; border-collapse: collapse; }
        .info-card td { padding: 8px 0; color: #374151; font-size: 14px; }
        .info-card td:first-child { font-weight: 600; width: 140px; color: #1e40af; }
        .cta { text-align: center; margin: 28px 0; }
        .cta a { display: inline-block; background: #2563eb; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; }
        .footer { background: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; color: #9ca3af; font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">💉</div>
            <h1>Vaccination Scheduled</h1>
            <p>BabyCare Management System</p>
        </div>
        <div class="body">
            <p>Dear Parent,</p>
            <p>A vaccination appointment has been scheduled for your baby. Please review the details below:</p>

            <div class="info-card">
                <table>
                    <tr>
                        <td>Baby Name</td>
                        <td>{{ $babyName }}</td>
                    </tr>
                    <tr>
                        <td>Vaccine</td>
                        <td>{{ $vaccineName }}</td>
                    </tr>
                    <tr>
                        <td>Scheduled Date</td>
                        <td><strong>{{ $scheduledDate }}</strong></td>
                    </tr>
                    <tr>
                        <td>Midwife</td>
                        <td>{{ $midwifeName }}</td>
                    </tr>
                    <tr>
                        <td>Location</td>
                        <td>{{ $clinicLocation }}</td>
                    </tr>
                </table>
            </div>

            <p>Please ensure your baby is available on the scheduled date. If you have any questions, please contact your midwife.</p>

            <div class="cta">
                <a href="{{ url('/parent') }}">View in Dashboard</a>
            </div>
        </div>
        <div class="footer">
            <p>This is an automated notification from the BabyCare Management System.<br>
            Please do not reply to this email directly.</p>
        </div>
    </div>
</body>
</html>
