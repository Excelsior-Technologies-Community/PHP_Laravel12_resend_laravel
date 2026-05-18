<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f6; padding: 30px; margin: 0;">
    <div style="max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
        
        <div style="background-color: #1a1a1a; padding: 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600; letter-spacing: 1px;">ORDER CONFIRMED</h1>
            <p style="margin: 5px 0 0 0; color: #a3a3a3; font-size: 14px;">Thank you for shopping with us!</p>
        </div>

        <div style="padding: 30px;">
            <p style="font-size: 16px; color: #333333; margin-top: 0;">Hello <strong>{{ $order->customer_name }}</strong>,</p>
            <p style="font-size: 14px; color: #666666; line-height: 1.5;">We have successfully received your order. Below are your transaction and delivery details:</p>
            
            <div style="background-color: #f9f9f9; border-left: 4px solid #1a1a1a; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; font-size: 14px; color: #555555;"><strong>Order ID:</strong> {{ $order->order_no }}</p>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #555555;"><strong>Product:</strong> {{ $order->product_name }}</p>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #555555;"><strong>Amount Paid:</strong> <span style="color: #2e7d32; font-weight: bold;">₹{{ number_format($order->price, 2) }}</span></p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="https://www.delhivery.com/track/package/12345" style="background-color: #1a1a1a; color: #ffffff; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 14px; display: inline-block;">Track Your Package 🚚</a>
            </div>

            <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 20px 0;">
            <p style="font-size: 12px; color: #999999; text-align: center; margin: 0;">This email was sent instantly via Resend API.</p>
        </div>
    </div>
</body>
</html>