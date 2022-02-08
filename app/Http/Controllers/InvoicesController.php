<?php

namespace App\Http\Controllers;

use App\Model\Invoice;
use Illuminate\Http\Request;
use JavaScript;

class InvoicesController extends Controller
{
    /**
     * Renders the transaction invoice.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        JavaScript::put(['phpVar' => true, 'skipDefaultScrollInits' => true]);
        $invoiceId = $request->route('id');
        $invoice = Invoice::query()->where(['id' => $invoiceId])->first();

        return view('pages.invoice', ['invoice' => $invoice]);
    }
}
