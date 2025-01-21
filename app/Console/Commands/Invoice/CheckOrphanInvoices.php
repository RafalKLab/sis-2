<?php

namespace App\Console\Commands\Invoice;

use App\Models\Order\Invoice;
use Illuminate\Console\Command;

class CheckOrphanInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-orphan-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks invoices with deleted or mismatched items';

    protected array $orphanInvoices = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $invoices = $this->getSuspectedInvoices();

        foreach ($invoices as $invoice) {
            if ($this->isOrphanInvoice($invoice)) {
                $this->addInvoiceToOrphanGroup($invoice);
            }
        }

        $this->outputOrphanInvoices();

        if ($this->orphanInvoices) {
            $delete = $this->confirmDeletion();
            if ($delete) {
                $this->deleteOrphanInvoices();
            } else {
                $this->info('No invoices were deleted.');
            }
        } else {
            $this->info('No orphan invoices found.');
        }
    }

    /**
     * Retrieve invoices that are potential suspects.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getSuspectedInvoices()
    {
        return Invoice::whereNotNull('customer')
            ->where('is_trans', 0)
            ->get();
    }

    /**
     * Check if an invoice is orphaned.
     *
     * @param Invoice $invoice
     * @return bool
     */
    private function isOrphanInvoice(Invoice $invoice): bool
    {
        if ($invoice->order->items->isEmpty()) {
            // No items in the order
            return true;
        }

        $orderBuyers = $this->getOrderBuyers($invoice);

        if (!$orderBuyers->contains($invoice->customer)) {
            $this->info(sprintf(
                'Invoice: %s is suspected because Order: %s does not have buyer: %s',
                $invoice->invoice_number,
                $invoice->order->getKeyField(),
                $invoice->customer
            ));

            return true;
        }

        return false;
    }

    /**
     * Get all buyers associated with an order's items.
     *
     * @param Invoice $invoice
     * @return \Illuminate\Support\Collection
     */
    private function getOrderBuyers(Invoice $invoice)
    {
        return $invoice->order->items
            ->flatMap(fn($item) => $item->buyers->pluck('name'));
    }

    /**
     * Add an invoice to the orphan group if not already present.
     *
     * @param Invoice $invoice
     */
    private function addInvoiceToOrphanGroup(Invoice $invoice): void
    {
        $number = $invoice->invoice_number;

        if (!in_array($number, $this->orphanInvoices)) {
            $this->orphanInvoices[] = $number;
        }
    }

    /**
     * Output the list of orphan invoices.
     */
    private function outputOrphanInvoices(): void
    {
        $this->info('Suspected invoice list:');
        foreach ($this->orphanInvoices as $invoiceNumber) {
            $this->info($invoiceNumber);
        }
    }

    /**
     * Confirm if the user wants to delete the orphan invoices.
     *
     * @return bool
     */
    private function confirmDeletion(): bool
    {
        return $this->confirm(
            'Do you want to delete the orphan invoices? (default: NO)',
            false
        );
    }

    /**
     * Delete the orphan invoices.
     */
    private function deleteOrphanInvoices(): void
    {
        Invoice::whereIn('invoice_number', $this->orphanInvoices)->delete();
        $this->info('The orphan invoices have been deleted.');
    }
}
