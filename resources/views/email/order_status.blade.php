<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2a37;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border-radius:10px; border:1px solid #e5e7eb; overflow:hidden;">
                    <tr>
                        <td style="background:#0b5ed7; color:#ffffff; padding:16px 20px;">
                            <h1 style="margin:0; font-size:20px; line-height:1.3;">MedStock Order Notification</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px;">
                            <p style="margin:0 0 12px; font-size:15px; line-height:1.6;">
                                Your transaction was completed successfully. Here is a summary of your order items:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:12px;">
                                <tr>
                                    <td style="padding:3px 0; font-size:13px; color:#4b5563; width:140px;">Order Number:</td>
                                    <td style="padding:3px 0; font-size:13px;">{{ $receipt['order_number'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0; font-size:13px; color:#4b5563;">Customer:</td>
                                    <td style="padding:3px 0; font-size:13px;">{{ $receipt['customer_name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0; font-size:13px; color:#4b5563;">Status:</td>
                                    <td style="padding:3px 0; font-size:13px;">{{ $receipt['status'] ?? 'N/A' }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-top:12px;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding:10px 8px; border-bottom:1px solid #d1d5db; font-size:13px; color:#4b5563;">Item</th>
                                        <th align="center" style="padding:10px 8px; border-bottom:1px solid #d1d5db; font-size:13px; color:#4b5563;">Qty</th>
                                        <th align="right" style="padding:10px 8px; border-bottom:1px solid #d1d5db; font-size:13px; color:#4b5563;">Unit Price</th>
                                        <th align="right" style="padding:10px 8px; border-bottom:1px solid #d1d5db; font-size:13px; color:#4b5563;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order as $line)
                                        @php
                                            $quantity = (int) ($line->quantity ?? 0);
                                            $price = (float) ($line->sell_price ?? 0);
                                            $subtotal = $quantity * $price;
                                        @endphp
                                        <tr>
                                            <td style="padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:14px;">{{ $line->description ?? 'Item' }}</td>
                                            <td align="center" style="padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:14px;">{{ $quantity }}</td>
                                            <td align="right" style="padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:14px;">PHP {{ number_format($price, 2) }}</td>
                                            <td align="right" style="padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:14px;">PHP {{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="padding:12px 8px; font-size:14px; color:#6b7280;">No order items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <p style="margin:16px 0 0; font-size:16px; font-weight:700; text-align:right;">
                                Total: PHP {{ $orderTotal }}
                            </p>

                           
                            </p>

                            <p style="margin:10px 0 0; font-size:14px; color:#4b5563; line-height:1.6;">
                                Thank you for choosing MedStock.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>