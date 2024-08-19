<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Order\Invoice;
use App\Service\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;

class InvoiceController extends MainController
{
    public function index(Request $request)
    {
        $itemsPerPage = 25;
        $search = '';
        $sortColumn = $request->input('sortColumn');
        $sortOrder = $request->input('sortOrder', 'asc');
        $currentSortOrder = $sortOrder;
        $invoiceStatusMap = ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT;

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_INVOICE_TABLE)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $query = Invoice::query(); // Start with a query builder instance

        // Check if a search term is provided and adjust the query
        if ($search = $request->input('search')) {
            // Assuming 'invoice_number' is the column you want to search by
            $query->where('invoice_number', 'like', '%' . $search . '%');
        }

        // Apply sort if column is provided
        if ($sortColumn) {
            $query->orderBy($sortColumn, $sortOrder);
        }

        // Paginate the results, 25 items per page
        $invoices = $query->paginate($itemsPerPage)->appends([
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder
        ]);

        // Ensure pagination links are appended with search term if there is one
        // This keeps the search term persistent when navigating pagination links
        if ($search) {
            $invoices->appends(['search' => $search]);
        }

        $invoiceStatusColorClass = $this->getColorClassForInvoices($invoices->items());

        return view(
            'main.user.invoice.index',
            compact('invoices', 'search', 'invoiceStatusMap', 'invoiceStatusColorClass', 'currentSortOrder'),
        );
    }

    private function getColorClassForInvoices(array $invoices): array
    {
        $results = [];
        foreach ($invoices as $invoice) {
            $results[$invoice->invoice_number] = InvoiceService::getInvoiceDisplayColor($invoice->invoice_number);
        }

        return $results;
    }
}
