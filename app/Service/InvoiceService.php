<?php

namespace App\Service;

use App\Models\Order\Invoice;
use App\Models\Order\OrderData;
use DateTime;
use shared\ConfigDefaultInterface;

class InvoiceService
{
    public static function getInvoiceDisplayColor(?string $number): string
    {
        if (!$number) {
            return 'order-field-status-';
        }

        $invoice = Invoice::where('invoice_number', $number)->first();
        if (!$invoice) {
            return 'order-field-status-';
        }

        if ($invoice->status === 'paid') {
            return 'order-field-status-green';
        }

        // Check date and set display color class
        $payUntilDate = new DateTime($invoice->pay_until_date);
        $currentDate = new DateTime();
        $warningDate = (clone $currentDate)->modify('+3 days'); // Date 3 days from now

        // Check if the current date is past the pay until date or if it's today
        if ($currentDate > $payUntilDate) {
            $colorClass = 'order-field-status-red invoice-after-deadline';
        } elseif ($warningDate >= $payUntilDate) {
            // Check if the current date is within 3 days before the deadline
            $colorClass = 'order-field-status-yellow';
        } else {
            $colorClass = 'order-field-status-';
        }

        return $colorClass;
    }

    public static function getInvoiceName(string $number): string
    {
        $fieldId = OrderData::where('value', $number)->first()->field_id;

        return TableService::getFieldById($fieldId)->name;
    }

    public static function getNotPaidInvoicesForOrder(int $orderId): array
    {
        $invoices = [];

        $invoiceEntities = Invoice::where('order_id', $orderId)
            ->where('status', ConfigDefaultInterface::INVOICE_STATUS_AWAITING)->get();

        foreach ($invoiceEntities as $entity) {
            $invoices[] = [
                'invoice_name' => TableService::getFieldById($entity->field_id)->name,
                'invoice_number' => $entity->invoice_number,
            ];
        }

        return $invoices;
    }
}
