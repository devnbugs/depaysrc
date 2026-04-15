<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $template = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Update</title>
</head>
<body style="margin:0;padding:0;background-color:#eef3f8;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#eef3f8;margin:0;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:680px;margin:0 auto;">
                    <tr>
                        <td style="padding-bottom:18px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:separate;border-spacing:0;background:linear-gradient(135deg,#0f172a 0%,#111827 58%,#0f3b5f 100%);border-radius:28px;overflow:hidden;">
                                <tr>
                                    <td style="padding:28px 30px 24px 30px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="font-size:11px;line-height:16px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:#7dd3fc;padding-bottom:18px;">
                                                    Account notification
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:30px;line-height:38px;font-weight:700;color:#ffffff;padding-bottom:12px;">
                                                    A secure update from your account
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:15px;line-height:24px;color:#cbd5e1;">
                                                    Your activity alert, verification code, or billing confirmation has been prepared in a cleaner format so the important details are easier to review.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:20px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="padding-right:8px;padding-bottom:8px;">
                                                                <span style="display:inline-block;border:1px solid rgba(125,211,252,0.28);background-color:rgba(125,211,252,0.12);border-radius:999px;padding:8px 14px;font-size:11px;line-height:14px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#e0f2fe;">Protected delivery</span>
                                                            </td>
                                                            <td style="padding-right:8px;padding-bottom:8px;">
                                                                <span style="display:inline-block;border:1px solid rgba(16,185,129,0.28);background-color:rgba(16,185,129,0.12);border-radius:999px;padding:8px 14px;font-size:11px;line-height:14px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#d1fae5;">Verified activity</span>
                                                            </td>
                                                            <td style="padding-bottom:8px;">
                                                                <span style="display:inline-block;border:1px solid rgba(255,255,255,0.16);background-color:rgba(255,255,255,0.08);border-radius:999px;padding:8px 14px;font-size:11px;line-height:14px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#f8fafc;">Readable on mobile</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#ffffff;border:1px solid #dbe4ef;border-radius:28px;overflow:hidden;box-shadow:0 18px 50px rgba(15,23,42,0.08);">
                                <tr>
                                    <td style="padding:30px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom:18px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8fbff;border:1px solid #e2e8f0;border-radius:20px;">
                                                        <tr>
                                                            <td style="padding:18px 20px;">
                                                                <div style="font-size:11px;line-height:16px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#64748b;padding-bottom:10px;">
                                                                    Recipient details
                                                                </div>
                                                                <div style="font-size:18px;line-height:26px;font-weight:700;color:#0f172a;padding-bottom:6px;">
                                                                    {{fullname}}
                                                                </div>
                                                                <div style="font-size:14px;line-height:22px;color:#475569;">
                                                                    {{username}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:14px;font-size:12px;line-height:18px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#64748b;">
                                                    Message details
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:15px;line-height:25px;color:#334155;">
                                                    {{message}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:26px;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top:1px solid #e2e8f0;">
                                                        <tr>
                                                            <td style="padding-top:18px;font-size:13px;line-height:22px;color:#64748b;">
                                                                If you did not expect this email, review your account activity and contact support immediately.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 8px 0 8px;text-align:center;">
                            <div style="font-size:12px;line-height:20px;color:#64748b;">
                                This message was sent automatically from your account activity workflow.
                            </div>
                            <div style="font-size:12px;line-height:20px;color:#94a3b8;padding-top:6px;">
                                Please do not share verification codes, one-time links, or billing confirmations with anyone.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        DB::table('general_settings')->update([
            'email_template' => $template,
        ]);
    }

    public function down(): void
    {
        // Intentionally left without destructive rollback because previous
        // template contents may differ between installations.
    }
};
