<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Project Closed - My Krishi</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f6f6;">

    <table width="100%" bgcolor="#f6f6f6" cellpadding="0" cellspacing="0" style="padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">

                    <!-- Header with logo -->
                    <tr>
                        <td align="center">
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="My Krishi Logo" width="300">
                            <!-- <h1 style="color: #ffffff; margin: 0; font-size: 24px;">My Krishi</h1> -->
                        </td>
                    </tr>

                    <!-- Body content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="font-size: 16px; color: #333333; margin-bottom: 20px;">Dear {{ $booking->investor->name ?? 'Investor' }},</p>

                            <p style="font-size: 16px; color: #333333;">
                                We would like to inform you that the project <strong>{{ $booking->project->details->title }}({{ $booking->project->unique_id }})</strong> has been <strong>closed</strong>.
                            </p>

                            <h3 style="font-size: 18px; color: #2e7d32; margin-top: 30px;">Summary</h3>

                            <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                                <tr style="background-color: #f2f2f2;">
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Quantity Booked</td>
                                    <td style="border: 1px solid #ddd;">{{ $booking->total_unit }} Units</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Unit Price</td>
                                    <td style="border: 1px solid #ddd;">{{ number_format($booking->project->details->unit_price, 2) }} BDT</td>
                                </tr>
                                <tr style="background-color: #f2f2f2;">
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Your Investment</td>
                                    <td style="border: 1px solid #ddd;">{{ number_format($booking->total_unit * $booking->project->details->unit_price, 2) }} BDT</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Total Revenue</td>
                                    <td style="border: 1px solid #ddd;">{{ number_format($booking->project->details->closing_amount, 2) }} BDT</td>
                                </tr>
                                <tr style="background-color: #f2f2f2;">
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Total Cost</td>
                                    <td style="border: 1px solid #ddd;">{{ number_format($booking->project->cost->sum('cost'), 2) }} BDT</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Total Profit</td>
                                    <td style="border: 1px solid #ddd;">{{ number_format($booking->project->details->closing_amount - $booking->project->cost->sum('cost'), 2) }} BDT</td>
                                </tr>
                                <tr style="background-color: #f2f2f2;">
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Service Charge</td>
                                    <td style="border: 1px solid #ddd;">{{$booking->project->details->service_charge }}%</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Net Profit</td>
                                    <td style="border: 1px solid #ddd;">
                                        {{ number_format((($booking->project->details->closing_amount - $booking->project->cost->sum('cost')) * $booking->project->details->service_charge) / 100, 2) }} BDT
                                    </td>
                                </tr>
                                <tr style="background-color: #f2f2f2;">
                                    <td style="border: 1px solid #ddd; font-weight: bold;">Profit/Unit</td>
                                    <td style="border: 1px solid #ddd;">
                                        {{ number_format((($booking->project->details->closing_amount - $booking->project->cost->sum('cost')) * $booking->project->details->service_charge) / 100 / $booking->sum('total_unit'), 2) }} BDT
                                    </td>
                                </tr>


                            </table>

                            <p style="font-size: 16px; color: #333333; margin-top: 30px;">
                                See more financial details by logging into our <strong>My Krishi</strong> application.
                            </p>

                            <p style="font-size: 16px; color: #333333; margin-top: 30px;">Thank you for being with us.</p>

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
