<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DeliveryPdfController extends Controller
{
    public function show($id)
    {
        $delivery = Delivery::with(['team', 'church', 'resources', 'resources.deliveryResources', 'resources.deliveryResources.resource'])->findOrFail($id);
        return view('pdf.delivery', compact('delivery'));
    }

    public function generate($id)
    {
        $delivery = Delivery::with(['team', 'church', 'resources', 'resources.deliveryResources', 'resources.deliveryResources.resource'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.delivery', compact('delivery'));
        return $pdf->download('delivery_'.$delivery->id.'.pdf');
    }

    public function view($id)
    {
        $delivery = Delivery::with(['team', 'church', 'resources', 'resources.deliveryResources', 'resources.deliveryResources.resource'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.delivery', compact('delivery'));
        return $pdf->stream('delivery_'.$delivery->id.'.pdf');
    }
}
