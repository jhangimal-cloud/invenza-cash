@php
    $money = fn($value) => '$' . number_format((float) ($value ?? 0), 2);
    $customerName = $tracking->receivable->customer_name ?: 'cliente';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recordatorio de pago pendiente</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:720px; margin:0 auto; padding:24px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; overflow:hidden;">
            <div style="background:#3d1c85; color:#ffffff; padding:24px;">
                <div style="font-size:13px; text-transform:uppercase; letter-spacing:.08em; color:#d7cdff;">
                    Recordatorio enviado desde Invenza Cash
                </div>
                <h1 style="margin:8px 0 0 0; font-size:24px; line-height:1.25;">
                    Recordatorio de pago pendiente
                </h1>
                <p style="margin:8px 0 0 0; color:#eae5ff; font-size:14px;">
                    {{ $companyName }}
                </p>
            </div>

            <div style="padding:24px;">
                <p style="font-size:15px; line-height:1.6; margin:0 0 18px 0;">
                    Estimado(a) <strong>{{ $customerName }}</strong>,
                </p>

                <p style="font-size:15px; line-height:1.6; margin:0 0 20px 0;">
                    Le recordamos que tiene un saldo pendiente con nosotros. A continuación el detalle:
                </p>

                <table style="width:100%; border-collapse:collapse; margin-bottom:22px;">
                    <tr>
                        <td style="width:50%; padding:10px; border:1px solid #e2e8f0; background:#f8fafc;">
                            <div style="font-size:12px; color:#64748b; font-weight:bold;">Documento</div>
                            <div style="font-size:14px; font-weight:bold;">{{ $tracking->title }}</div>
                        </td>
                        <td style="width:50%; padding:10px; border:1px solid #e2e8f0; background:#f8fafc;">
                            <div style="font-size:12px; color:#64748b; font-weight:bold;">Saldo pendiente</div>
                            <div style="font-size:14px; font-weight:bold;">{{ $money($tracking->receivable->balance) }}</div>
                        </td>
                    </tr>
                    @if($tracking->original_due_date)
                        <tr>
                            <td style="padding:10px; border:1px solid #e2e8f0;" colspan="2">
                                <div style="font-size:12px; color:#64748b; font-weight:bold;">Vencimiento</div>
                                <div style="font-size:14px; font-weight:bold;">{{ $tracking->original_due_date->format('d/m/Y') }}</div>
                            </td>
                        </tr>
                    @endif
                </table>

                @if($customMessage)
                    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:14px; margin-bottom:20px;">
                        <p style="font-size:14px; line-height:1.6; color:#92400e; margin:0; white-space:pre-line;">{{ $customMessage }}</p>
                    </div>
                @endif

                <p style="font-size:14px; line-height:1.6; color:#334155; margin:0;">
                    Para cualquier consulta sobre este saldo, puede comunicarse directamente con nosotros.
                </p>
            </div>

            <div style="background:#f1f5f9; padding:16px 24px; text-align:center;">
                <p style="font-size:12px; color:#64748b; margin:0;">
                    Este correo fue generado automáticamente por Invenza Cash.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
