<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New message from your website</title>
</head>
<body style="margin:0; padding:0; background-color:#191919; font-family:'Space Mono', 'Courier New', monospace; color:#CCCCCC;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#191919;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; background-color:#161617; border:1px solid #343434;">
                    <tr>
                        <td style="padding:20px 28px; border-bottom:1px solid #343434;">
                            <p style="margin:0; font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#6A9955;">./<span style="color: #6f6f6f;">new message from your website</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:12px 0 4px; font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#569CD6;">name</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 12px; font-size:15px; color:#CCCCCC;">{{ $contactData['name'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0 4px; font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#569CD6;">email</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 12px; font-size:15px;"><a href="mailto:{{ $contactData['email'] }}" style="color:#DCDCAA; text-decoration:none;">{{ $contactData['email'] }}</a></td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0 4px; font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#569CD6;">message</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 8px; font-size:15px; line-height:1.7; color:#CCCCCC; white-space:pre-wrap; border:1px solid #343434; background-color:#191919; padding:16px;">{{ $contactData['message'] }}</td>
                                </tr>
                                @if (!empty($attachmentData))
                                <tr>
                                    <td style="padding:16px 0 4px; font-size:12px; letter-spacing:1px; text-transform:uppercase; color:#569CD6;">attachment</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 0 8px; font-size:14px; color:#DCDCAA;">&gt; {{ $attachmentData['name'] }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px; border-top:1px solid #343434;">
                            <p style="margin:0; font-size:12px; color:#6f6f6f;">&gt; rispondi direttamente a questa email</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>