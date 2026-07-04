<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\RecurringInvoiceTemplate;
use App\Models\RecurringInvoiceLineItem;
use App\Models\RecurringInvoiceLog;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\Space;
use App\Models\Contract;
use App\Models\Tax;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class RecurringInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->can('manage invoice')) {
            $templates = RecurringInvoiceTemplate::where('owned_by', Auth::user()->ownedId())->get();
            return view('recurring_invoice.index', compact('templates'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->can('create invoice')) {
            $customers = Customer::where('owned_by', Auth::user()->ownedId())->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            
            $spaces = Space::where('owned_by', Auth::user()->ownedId())->get()->pluck('name', 'id');
            $spaces->prepend('Select Space', '');
            
            $taxes = Tax::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            
            $cycles = [
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'half_yearly' => 'Half Yearly',
                'annually' => 'Annually',
            ];

            return view('recurring_invoice.create', compact('customers', 'spaces', 'taxes', 'cycles'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('create invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'customer_id' => 'required',
                    'cycle' => 'required',
                    'start_date' => 'required|date',
                    'invoice_day' => 'required|numeric|min:1|max:31',
                    'items' => 'required|array',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            DB::beginTransaction();
            try {
                $template = new RecurringInvoiceTemplate();
                $template->name = $request->name;
                $template->customer_id = $request->customer_id;
                $template->contract_id = $request->contract_id;
                $template->space_id = $request->space_id;
                $template->cycle = $request->cycle;
                $template->start_date = $request->start_date;
                $template->end_date = $request->end_date;
                $template->next_invoice_date = $request->start_date; // Initial next date
                $template->invoice_day = $request->invoice_day;
                $template->auto_send = $request->has('auto_send') ? 1 : 0;
                $template->status = 'active';
                $template->notes = $request->notes;
                $template->owned_by = Auth::user()->ownedId();
                $template->created_by = Auth::user()->creatorId();

                // Calculate Initial Next Invoice Date
                $today = \Carbon\Carbon::today();
                $invoiceDay = (int)$request->invoice_day;
                $startDate = \Carbon\Carbon::parse($request->start_date);
                
                // If today is past the invoice day of the current month, start from next month
                if ($today->day >= $invoiceDay) {
                    $nextDate = $today->copy()->addMonth()->day($invoiceDay);
                } else {
                    $nextDate = $today->copy()->day($invoiceDay);
                }

                // Ensure next date is not before start date
                if ($nextDate->isBefore($startDate)) {
                    $nextDate = $startDate->copy();
                }

                $template->next_invoice_date = $nextDate->toDateString();
                $template->save();

                foreach ($request->items as $item) {
                    $taxArr = is_array(@$item['tax']) ? @$item['tax'] : explode(',', @$item['tax']);
                    $taxRate = 0;
                    foreach ($taxArr as $taxId) {
                        if (!empty($taxId)) {
                            $tax = Tax::find($taxId);
                            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
                        }
                    }
                    $taxAmount = ($taxRate / 100) * (($item['quantity'] * $item['price']) - ($item['discount'] ?? 0));

                    RecurringInvoiceLineItem::create([
                        'template_id' => $template->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'tax' => implode(',', array_filter($taxArr)),
                        'tax_amount' => $taxAmount,
                        'discount' => $item['discount'] ?? 0,
                        'total' => (($item['quantity'] * $item['price']) - ($item['discount'] ?? 0) + $taxAmount),
                        'sort_order' => $item['sort_order'] ?? 0,
                    ]);
                }

                DB::commit();
                return redirect()->route('recurring-invoices.index')->with('success', __('Recurring Invoice Template successfully created.'));
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($ids)
    {
        if (Auth::user()->can('manage invoice')) {
            $id = Crypt::decrypt($ids);
            $template = RecurringInvoiceTemplate::find($id);
            return view('recurring_invoice.show', compact('template'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($ids)
    {
        if (Auth::user()->can('edit invoice')) {
            $id = Crypt::decrypt($ids);
            $template = RecurringInvoiceTemplate::find($id);
            
            $customers = Customer::where('owned_by', Auth::user()->ownedId())->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            
            $spaces = Space::where('owned_by', Auth::user()->ownedId())->get()->pluck('name', 'id');
            $spaces->prepend('Select Space', '');
            
            $taxes = Tax::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            
            $cycles = [
                'monthly' => 'Monthly',
                'quarterly' => 'Quarterly',
                'half_yearly' => 'Half Yearly',
                'annually' => 'Annually',
            ];

            return view('recurring_invoice.edit', compact('template', 'customers', 'spaces', 'taxes', 'cycles'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $ids)
    {
        if (Auth::user()->can('edit invoice')) {
            $id = Crypt::decrypt($ids);
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'customer_id' => 'required',
                    'cycle' => 'required',
                    'start_date' => 'required|date',
                    'invoice_day' => 'required|numeric|min:1|max:31',
                    'items' => 'required|array',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            DB::beginTransaction();
            try {
                $template = RecurringInvoiceTemplate::find($id);
                $template->name = $request->name;
                $template->customer_id = $request->customer_id;
                $template->contract_id = $request->contract_id;
                $template->space_id = $request->space_id;
                $template->cycle = $request->cycle;
                $template->start_date = $request->start_date;
                $template->end_date = $request->end_date;
                $template->invoice_day = $request->invoice_day;
                $template->auto_send = $request->has('auto_send') ? 1 : 0;
                $template->notes = $request->notes;

                // Re-calculate Next Invoice Date if invoice_day or start_date changed
                $today = \Carbon\Carbon::today();
                $invoiceDay = (int)$request->invoice_day;
                $startDate = \Carbon\Carbon::parse($request->start_date);
                
                if ($today->day >= $invoiceDay) {
                    $nextDate = $today->copy()->addMonth()->day($invoiceDay);
                } else {
                    $nextDate = $today->copy()->day($invoiceDay);
                }

                if ($nextDate->isBefore($startDate)) {
                    $nextDate = $startDate->copy();
                }
                $template->next_invoice_date = $nextDate->toDateString();

                $template->save();

                // Simple approach: delete and recreate items
                RecurringInvoiceLineItem::where('template_id', $template->id)->delete();
                foreach ($request->items as $item) {
                    $taxArr = is_array($item['tax']) ? $item['tax'] : explode(',', $item['tax']);
                    $taxRate = 0;
                    foreach ($taxArr as $taxId) {
                        if (!empty($taxId)) {
                            $tax = Tax::find($taxId);
                            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
                        }
                    }
                    $taxAmount = ($taxRate / 100) * (($item['quantity'] * $item['price']) - ($item['discount'] ?? 0));

                    RecurringInvoiceLineItem::create([
                        'template_id' => $template->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'tax' => implode(',', array_filter($taxArr)),
                        'tax_amount' => $taxAmount,
                        'discount' => $item['discount'] ?? 0,
                        'total' => (($item['quantity'] * $item['price']) - ($item['discount'] ?? 0) + $taxAmount),
                        'sort_order' => $item['sort_order'] ?? 0,
                    ]);
                }

                DB::commit();
                return redirect()->route('recurring-invoices.index')->with('success', __('Recurring Invoice Template successfully updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids)
    {
        if (Auth::user()->can('delete invoice')) {
            $id = Crypt::decrypt($ids);
            $template = RecurringInvoiceTemplate::find($id);
            $template->delete();
            return redirect()->route('recurring-invoices.index')->with('success', __('Recurring Invoice Template successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pause($ids)
    {
        if (Auth::user()->can('edit invoice')) {
            $id = Crypt::decrypt($ids);
            $template = RecurringInvoiceTemplate::find($id);
            $template->status = 'paused';
            $template->save();
            return redirect()->back()->with('success', __('Template paused successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function resume($ids)
    {
        if (Auth::user()->can('edit invoice')) {
            $id = Crypt::decrypt($ids);
            $template = RecurringInvoiceTemplate::find($id);
            $template->status = 'active';
            $template->save();
            return redirect()->back()->with('success', __('Template resumed successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getContracts(Request $request)
    {
        $customers = Customer::find($request->id);
        if (!$customers) return response()->json(['data' => []]);

        $item = Contract::where('company_id', $customers->company_id)->where('close_date', Null)->get();
        return response()->json(['data' => $item]);
    }

    public function getContractDetails(Request $request)
    {
        $contract_data = Contract::where('id', $request->id)->first();
        if (!$contract_data) return response()->json(['html' => '']);

        $assign_room = \App\Models\Roomassign::with('space')->where('contract_id', $request->id)->get();
        
        $space_id = @$assign_room[0]->space->id;
        $owned_id = Auth::user()->ownedId();
        $creator_id = Auth::user()->creatorId();

        $product = Auth::user()->type == 'company' 
            ? ProductService::where('created_by', $creator_id)->where('space_id', $space_id)->first()
            : ProductService::where('owned_by', $owned_id)->where('space_id', $space_id)->first();

        $taxes_data = [];
        $totalItemTaxRate = 0;
        if ($product && $product->tax_id != 0) {
            $taxData = $product->tax($product->tax_id);
            foreach ($taxData as $taxItem) {
                $taxes_data[] = ['id' => $taxItem->id, 'name' => $taxItem->name, 'rate' => $taxItem->rate];
                $totalItemTaxRate += floatval($taxItem->rate);
            }
        }

        $report = view('recurring_invoice.item_details', compact('contract_data', 'assign_room', 'product', 'taxes_data', 'totalItemTaxRate'))->render();

        // Services
        $serv = Auth::user()->type == 'company'
            ? ProductService::where('created_by', $creator_id)->where('id', @$contract_data->service_id)->first()
            : ProductService::where('owned_by', $owned_id)->where('id', @$contract_data->service_id)->first();

        $report2 = '';
        if ($serv) {
            $taxes_data2 = [];
            $totalItemTaxRate2 = 0;
            if ($serv->tax_id != 0) {
                $taxData2 = $serv->tax($serv->tax_id);
                foreach ($taxData2 as $taxItem2) {
                    $taxes_data2[] = ['id' => $taxItem2->id, 'name' => $taxItem2->name, 'rate' => $taxItem2->rate];
                    $totalItemTaxRate2 += floatval($taxItem2->rate);
                }
            }
            $report2 = view('recurring_invoice.service_details', [
                'description' => $serv->name,
                'price' => (float)$contract_data->service_price,
                'quantity' => $assign_room->count('chair_id'),
                'taxes_data' => $taxes_data2,
                'totalItemTaxRate' => $totalItemTaxRate2
            ])->render();
        }

        // Security Deposit
        $invoice = Invoice::where('contract_id', $request->id)->first();
        $report3 = '';
        if (empty($invoice)) {
            $serv2 = Auth::user()->type == 'company'
                ? ProductService::where('created_by', $creator_id)->where('id', @$contract_data->security_deposit_id)->first()
                : ProductService::where('owned_by', $owned_id)->where('id', @$contract_data->security_deposit_id)->first();

            if ($serv2) {
                $taxes_data3 = [];
                $totalItemTaxRate3 = 0;
                if ($serv2->tax_id != 0) {
                    $taxData3 = $serv2->tax($serv2->tax_id);
                    foreach ($taxData3 as $taxItem3) {
                        $taxes_data3[] = ['id' => $taxItem3->id, 'name' => $taxItem3->name, 'rate' => $taxItem3->rate];
                        $totalItemTaxRate3 += floatval($taxItem3->rate);
                    }
                }
                $report3 = view('recurring_invoice.service_details', [
                    'description' => $serv2->name,
                    'price' => (float)$contract_data->security_deposit_price,
                    'quantity' => 1,
                    'taxes_data' => $taxes_data3,
                    'totalItemTaxRate' => $totalItemTaxRate3
                ])->render();
            }
        }

        return response()->json(['html' => $report, 'html2' => $report2, 'html3' => $report3, 'space_id' => $space_id]);
    }
}