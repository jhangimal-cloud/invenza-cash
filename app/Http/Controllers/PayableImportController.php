<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File;

class PayableImportController extends Controller
{
    public function create()
    {
        return view('payables.import');
    }

    /**
     * Importa un CSV de cuentas por pagar (egresos). Columnas esperadas:
     * proveedor, correo, documento, monto, vencimiento. Idempotente por
     * company_id + documento, igual que el importador de receivables.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', File::types(['csv', 'txt'])->max(5 * 1024)],
        ]);

        $companyId = $request->user()->company_id;

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'No se pudo leer el archivo.');
        }

        $delimiter = $this->detectDelimiter($handle);

        $header = fgetcsv($handle, 0, $delimiter);

        if (!$header) {
            fclose($handle);

            return back()->with('error', 'El archivo está vacío o no tiene encabezado.');
        }

        $columns = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $header);

        $required = ['proveedor', 'documento', 'monto'];
        $missing = array_diff($required, $columns);

        if (!empty($missing)) {
            fclose($handle);

            return back()->with('error', 'Faltan columnas obligatorias en el CSV: ' . implode(', ', $missing) . '. Columnas esperadas: proveedor, correo, documento, monto, vencimiento.');
        }

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($handle, $delimiter, $columns, $companyId, &$imported, &$skipped) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                $data = array_combine($columns, array_pad($row, count($columns), null));

                $vendorName = trim((string) ($data['proveedor'] ?? ''));
                $documentNumber = trim((string) ($data['documento'] ?? ''));
                $amount = $this->toDecimal($data['monto'] ?? null);

                if ($vendorName === '' || $documentNumber === '' || $amount === null) {
                    $skipped++;
                    continue;
                }

                Payable::updateOrCreate(
                    ['company_id' => $companyId, 'document_number' => $documentNumber],
                    [
                        'vendor_name' => $vendorName,
                        'vendor_email' => trim((string) ($data['correo'] ?? '')) ?: null,
                        'total' => $amount,
                        'balance' => $amount,
                        'due_date' => $this->parseDate($data['vencimiento'] ?? null),
                        'status' => 'PENDIENTE',
                    ]
                );

                $imported++;
            }
        });

        fclose($handle);

        return redirect()
            ->route('collections.cashflow')
            ->with('success', "Importación completa: {$imported} cuenta(s) por pagar creada(s)/actualizada(s)" . ($skipped > 0 ? ", {$skipped} fila(s) omitida(s) por datos incompletos." : '.'));
    }

    private function detectDelimiter($handle): string
    {
        $firstLine = fgets($handle);
        rewind($handle);

        if ($firstLine === false) {
            return ',';
        }

        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');

        return $semicolonCount > $commaCount ? ';' : ',';
    }

    private function toDecimal(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace(',', '', $value);
        } elseif (str_contains($value, ',') && !str_contains($value, '.')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $value);

            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
