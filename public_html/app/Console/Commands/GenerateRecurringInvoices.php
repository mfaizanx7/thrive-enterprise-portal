<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\RecurringInvoiceTemplate;
use App\Models\RecurringInvoiceLog;
use App\Models\Customer;
use App\Models\Utility;
use App\Models\CreditNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices from recurring templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $templates = RecurringInvoiceTemplate::where('status', 'active')
            ->where('next_invoice_date', '<=', $today->toDateString())
            ->where(function($query) use ($today) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $today->toDateString());
            })
            ->get();

        if ($templates->isEmpty()) {
            $this->info('No templates due for generation.');
            return 0;
        }

        foreach ($templates as $template) {
            $this->info("Processing template: {$template->name}");
            
            DB::beginTransaction();
            try {
                // 1. Create Invoice
                $invoice = new Invoice();
                $invoice->invoice_id = $this->invoiceNumber($template->created_by);
                $invoice->customer_id = $template->customer_id;
                $invoice->status = 0; // Draft
                $invoice->issue_date = $today->toDateString();
                $invoice->due_date = $today->addDays(7)->toDateString(); // Default 7 days
                $invoice->category_id = 1; // Default or from template if added
                $invoice->contract_id = $template->contract_id;
                $invoice->recurring_template_id = $template->id;
                $invoice->owned_by = $template->owned_by;
                $invoice->created_by = $template->created_by;
                $invoice->save();

                $reciveable = 0;
                $items = [];

                // 2. Create products
                foreach ($template->items as $item) {
                    $invoiceProduct = new InvoiceProduct();
                    $invoiceProduct->invoice_id = $invoice->id;
                    $invoiceProduct->product_id = 0; // Recurring item might not be a standard product
                    $invoiceProduct->quantity = $item->quantity;
                    $invoiceProduct->tax = $item->tax;
                    $invoiceProduct->discount = $item->discount;
                    $invoiceProduct->price = $item->unit_price;
                    $invoiceProduct->description = $item->description;
                    $invoiceProduct->save();

                    // Calculate tax
                    if (isset($item->tax_amount) && $item->tax_amount !== null) {
                        $itemTaxPrice = $item->tax_amount;
                    } else {
                        $taxRate = 0;
                        if (!empty($item->tax)) {
                            $taxRate = Utility::totalTaxRate($item->tax);
                        }
                        $itemTaxPrice = ($taxRate / 100) * ($item->unit_price * $item->quantity - $item->discount);
                    }
                    
                    $reciveable += (($item->quantity * $item->unit_price) - $item->discount) + $itemTaxPrice;
                    
                    $items[] = [
                        'item' => 0,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'tax' => $item->tax,
                        'discount' => $item->discount,
                        'description' => $item->description,
                        'itemTaxPrice' => $itemTaxPrice,
                        'prod_id' => $invoiceProduct->id
                    ];
                }

                // 3. Credit Note / Balance / Journal Entry
                $credit = new CreditNote();
                $credit->invoice = $invoice->id;
                $credit->customer = $invoice->customer_id;
                $credit->date = $invoice->issue_date;
                $credit->amount = $reciveable;
                $credit->description = "Recurring invoice #{$invoice->invoice_id} generated";
                $credit->save();

                Utility::updateUserBalance('customer', $invoice->customer_id, $reciveable, 'debit');

                $data = [
                    'id' => $invoice->id,
                    'no' => $invoice->invoice_id,
                    'date' => $invoice->issue_date,
                    'reference' => "Recurring #{$template->id}",
                    'category' => 'Invoice',
                    'owned_by' => $invoice->owned_by,
                    'created_by' => $invoice->created_by,
                    'items' => $items
                ];
                
                // $voucher_id = Utility::jrentry($data);
                // $invoice->voucher_id = $voucher_id;
                $invoice->save();

                // 4. Update Template next_invoice_date
                $nextDate = Carbon::parse($template->next_invoice_date);
                switch ($template->cycle) {
                    case 'monthly': $nextDate->addMonth(); break;
                    case 'quarterly': $nextDate->addMonths(3); break;
                    case 'half_yearly': $nextDate->addMonths(6); break;
                    case 'annually': $nextDate->addYear(); break;
                }
                
                $template->next_invoice_date = $nextDate->toDateString();
                if ($template->end_date && $nextDate->isAfter($template->end_date)) {
                    $template->status = 'completed';
                }
                $template->save();

                // 5. Log
                RecurringInvoiceLog::create([
                    'template_id' => $template->id,
                    'invoice_id' => $invoice->id,
                    'generated_at' => now(),
                    'status' => 'generated'
                ]);

                DB::commit();
                $this->info("Successfully generated invoice #{$invoice->invoice_id}");

            } catch (\Exception $e) {
                DB::rollback();
                $this->error("Failed to generate for template {$template->id}: " . $e->getMessage());
                
                RecurringInvoiceLog::create([
                    'template_id' => $template->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
        }

        return 0;
    }

    private function invoiceNumber($userId)
    {
        $latest = Invoice::where('created_by', $userId)->latest()->first();
        if (!$latest) {
            return 1;
        }
        return $latest->invoice_id + 1;
    }
}