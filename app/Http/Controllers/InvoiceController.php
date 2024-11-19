<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();

        return response()->json($invoices);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'xml' => 'required|file|mimes:xml',
        ]);

        $filePath = $request->file('xml')->getPathname();
        $xml = simplexml_load_file($filePath);

        if (!$xml) {
            return response()->json([
                'success' => false,
                'descripcionError' => 'Problema al leer el archivo. El archivo XML es inválido.',
            ], 400);
        }

        $namespaces = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
        $xml->registerXPathNamespace('tfd', $namespaces['tfd']);

        $comprobante = $xml->xpath('//cfdi:Comprobante')[0] ?? null;

        if (!$comprobante) {
            return response()->json([
                'success' => false,
                'descripcionError' => 'No se encontró el nodo Comprobante en el archivo XML.',
            ], 400);
        }

        $uuidNode = $xml->xpath('//tfd:TimbreFiscalDigital')[0] ?? null;
        $uuid = $uuidNode ? (string)$uuidNode['UUID'] : null;

        if (!$uuid) {
            return response()->json([
                'success' => false,
                'descripcionError' => 'No se encontró el UUID en el archivo XML.',
            ], 400);
        }

        $folio = (string)$comprobante['Folio'] ?? null;
        $fecha = (string)$comprobante['Fecha'] ?? null;
        $total = (float)$comprobante['Total'] ?? null;
        $tipoCambio = $this->getExchangeRate();
        if ($tipoCambio === null) {
            return response()->json([
                'success' => false,
                'descripcionError' => 'No se pudo obtener el tipo de cambio.',
            ], 500);
        }
        $moneda = (string)$comprobante['Moneda'] ?? null;

        $emisor = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0] ?? null;
        $receptor = $xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0] ?? null;

        $nombreEmisor = $emisor ? (string)$emisor['Nombre'] : null;
        $nombreReceptor = $receptor ? (string)$receptor['Nombre'] : null;

        $existingInvoice = Invoice::where('uuid', $uuid)->first();
        if ($existingInvoice) {
            return response()->json([
                'success' => false,
                'descripcionError' => 'La factura con este UUID ya está registrada.',
            ], 409);
        }

        $invoice = Invoice::create([
            'uuid' => $uuid,
            'folio' => $folio,
            'fecha' => $fecha,
            'total' => $total,
            'tipo_cambio' => $tipoCambio,
            'emisor' => $nombreEmisor,
            'receptor' => $nombreReceptor,
            'moneda' => $moneda
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Factura cargada exitosamente.',
            'invoice' => $invoice,
        ], 201);
    }



    private function getExchangeRate()
    {
        $token = config('services.banxico.token');
        $url = config('services.banxico.url', 'https://www.banxico.org.mx/SieAPIRest/service/v1/series/SF43718/datos/oportuno');

        $response = Http::withHeaders([
            'Bmx-Token' => $token,
            'Accept' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();
            return $data['bmx']['series'][0]['datos'][0]['dato'] ?? null;
        }

        return null;
    }
}
