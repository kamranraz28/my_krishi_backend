<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Office Payment Confirmation - My Krishi</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f6f6;">

    <table width="100%" bgcolor="#f6f6f6" cellpadding="0" cellspacing="0" style="padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

                    <!-- Header with logo -->
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="My Krishi Logo" width="200">
                        </td>
                    </tr>

                    <!-- Body content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px; color: #333333; margin-bottom: 20px;">
                                Dear {{ $booking->investor->name }},
                            </p>

                            <p style="font-size: 16px; color: #333333;">
                                Congratulations! Your booking has been confirmed. We have received your payment. Below is the summary of your payment with office payment:
                            </p>

                            <h3 style="font-size: 18px; color: #2e7d32; margin-top: 30px;">Payment Summary</h3>

                            <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="border: 1px solid #ddd;">Project Name</th>
                                        <th style="border: 1px solid #ddd;">BDT/Unit</th>
                                        <th style="border: 1px solid #ddd;">Total Units</th>
                                        <th style="border: 1px solid #ddd;">Amount</th>
                                        <th style="border: 1px solid #ddd;">Booking Time</th>
                                        <th style="border: 1px solid #ddd;">Payment Time</th>
                                        <th style="border: 1px solid #ddd;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $unitPrice = $booking->project->details->unit_price ?? 0;
                                        $amount = $booking->total_unit * $unitPrice;
                                    @endphp
                                    <tr>
                                        <td style="border: 1px solid #ddd;">{{ $booking->project->details->title ?? 'N/A' }}</td>
                                        <td style="border: 1px solid #ddd;">{{ number_format($unitPrice, 2) }} BDT</td>
                                        <td style="border: 1px solid #ddd;">{{ $booking->total_unit }}</td>
                                        <td style="border: 1px solid #ddd;">{{ number_format($amount, 2) }} BDT</td>
                                        <td style="border: 1px solid #ddd;">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, h:i A') }}</td>
                                        <td style="border: 1px solid #ddd;">{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y, h:i A') }}</td>
                                        <td style="border: 1px solid #ddd;">Confirmed</td>

                                    </tr>
                                </tbody>
                            </table>


                            <p style="font-size: 16px; color: #333333; margin-top: 30px;">
                                Please, visit our application to get the updates of the project regularly. If you have any questions, feel free to contact our support team.
                            </p>

                            <p style="font-size: 16px; color: #333333;">Best regards,<br><strong>The My Krishi Team</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #eeeeee; padding: 15px; font-size: 13px; color: #666666;">
                            &copy; {{ date('Y') }} My Krishi. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
