<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillAccount;
use App\Models\BillPayment;
use App\Models\BillProduct;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\Project;
use App\Models\StockReport;
use App\Models\Utility;
use App\Models\ActivityLog;
use App\Models\ChartOfAccountSubType;
use App\Models\ChartOfAccountType;
use App\Models\DebitNote;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    function billNumber()
    {
        $latest = Bill::where('owned_by', '=', \Auth::user()->ownedId())->where('type' ,'=' ,'Bill')->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->bill_id + 1;
    }

    function expenseNumber()
    {
        $latest = Bill::where('owned_by', '=', \Auth::user()->ownedId())->where('type' ,'=' ,'Expense')->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->bill_id + 1;
    }

    public function employee(Request $request)
    {
        $employee = Employee::where('id', '=', $request->id)->first();

        return view('expense.employee_detail', compact('employee'));
    }

    public function vender(Request $request)
    {
        $vender = Vender::where('id', '=', $request->id)->first();

        return view('expense.vender_detail', compact('vender'));
    }
    public function customer(Request $request)
    {
        $customer = Customer::where('id', '=', $request->id)->first();
        return view('expense.customer_detail', compact('customer'));
    }

    public function product(Request $request)
    {
        $data['product']     = $product = ProductService::find($request->product_id);
        $data['unit']        = !empty($product->unit()) ? $product->unit()->name : '';
        $data['taxRate']     = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes']       = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice           = $product->purchase_price;
        $quantity            = 1;
        $taxPrice            = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function index(Request $request)
    {
        if(\Auth::user()->can('manage bill')){
            if(\Auth::user()->type == ('company')){
                $branches = User::where('type', '=', 'branch')->get()->pluck('name', 'id');
                $branches->prepend(\Auth::user()->name, \Auth::user()->id);
                $branches->prepend('Select Branch', '');
                $vender = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $vender->prepend('Select Vendor', '');

                $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('type', ['product & service', 'income',])
                    ->get()->pluck('name', 'id');
                $category->prepend('Select Category', '');

                $status = Bill::$statues;
                $query = Bill::where('type', '=', 'Expense')
                ->where('created_by', '=', \Auth::user()->creatorId());
            }else{
                $branches = User::where('id', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $branches->prepend('Select Branch', '');
                $vender = Vender::where('owned_by', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $vender->prepend('Select Vendor', '');

                $category     = ProductServiceCategory::where('owned_by', \Auth::user()->ownedId())
                    ->whereNotIn('type', ['product & service', 'income',])
                    ->get()->pluck('name', 'id');
                $category->prepend('Select Category', '');

                $status = Bill::$statues;

                $query = Bill::where('type', '=', 'Expense')
                ->where('owned_by', '=', \Auth::user()->ownedId());
            }
            if(!empty($request->vender))
            {
                $query->where('vender_id', '=', $request->vender);
            }
            if(count(explode('to', $request->bill_date)) > 1)
            {
                $date_range = explode(' to ', $request->bill_date);
                $query->whereBetween('bill_date', $date_range);
            }
            elseif(!empty($request->bill_date))
            {
                $date_range = [$request->date , $request->bill_date];
                $query->whereBetween('bill_date', $date_range);
            }
            if (!empty($request->branches)) {
                $query->where('owned_by', '=', $request->branches);
            }

            if(!empty($request->category))
            {
                $query->where('category_id', '=', $request->category);
            }

            $expenses = $query->orderBy('id','Desc')->get();

            return view('expense.index', compact('expenses', 'vender', 'status','category','branches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($Id)
    {
        if(\Auth::user()->can('create bill'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'bill')->get();
            if(\Auth::user()->type == ('company')){
                $expense_number = \Auth::user()->expenseNumberFormat($this->expenseNumber());
                $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('type', ['product & service', 'income',])
                    ->get()->pluck('name', 'id');
                $category->prepend('Select Category', '');

                $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $employees->prepend('Select Employee', '');

                $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $customers->prepend('Select Customer', '');

                $venders     = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $venders->prepend('Select Vender', '');

                $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $product_services->prepend('Select Item', '');

                $chartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                    ->where('created_by', \Auth::user()->creatorId())->get()
                    ->pluck('code_name', 'id');
                $chartAccounts->prepend('Select Account', '');

                $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))
                            ->where('created_by', \Auth::user()->creatorId())
                            ->get()->pluck('name', 'id');
            }else{
                $expense_number = \Auth::user()->expenseNumberFormat($this->expenseNumber());
                $category     = ProductServiceCategory::where('owned_by', \Auth::user()->ownedId())
                ->whereNotIn('type', ['product & service', 'income',])
                ->get()->pluck('name', 'id');
                $category->prepend('Select Category', '');

                $employees        = Employee::where('owned_by', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $employees->prepend('Select Employee', '');

                $customers = Customer::where('owned_by', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $customers->prepend('Select Customer', '');

                $venders     = Vender::where('owned_by', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $venders->prepend('Select Vender', '');

                $product_services = ProductService::where('owned_by', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $product_services->prepend('Select Item', '');

                $chartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                    ->where('owned_by', \Auth::user()->ownedId())->get()
                    ->pluck('code_name', 'id');
                $chartAccounts->prepend('Select Account', '');

                $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))
                            ->where('owned_by', \Auth::user()->ownedId())
                            ->get()->pluck('name', 'id');
            }


            return view('expense.create', compact('employees','customers','venders', 'expense_number', 'product_services', 'category', 'customFields', 'Id','chartAccounts','accounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
// dd($request->all());
        if(\Auth::user()->can('create bill'))
        {

            DB::beginTransaction();
            try {
                $validator = \Validator::make(
                    $request->all(), [
                        //     'vender_id' => 'required',
                        'payment_date' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages3 = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages3->first());
                }

                if (!empty($request->items) && empty($request->items[0]['item']) && empty($request->items[0]['chart_account_id']) && empty($request->items[0]['amount']))
                {
                    $itemValidator = \Validator::make(
                        $request->all(), [
                            'item' => 'required'
                        ]
                    );
                    if ($itemValidator->fails()) {
                        $messages1 = $itemValidator->getMessageBag();
                        return redirect()->back()->with('error', $messages1->first());
                    }
                }

                if (!empty($request->items) && empty($request->items[0]['chart_account_id'])  && !empty($request->items[0]['amount']) )
                {
                    $accountValidator = \Validator::make(
                        $request->all(), [
                            'chart_account_id' => 'required'
                        ]
                    );
                    if ($accountValidator->fails()) {
                        $messages2 = $accountValidator->getMessageBag();
                        return redirect()->back()->with('error', $messages2->first());
                    }

                }

                $expense                 = new Bill();
                $expense->bill_id        = $this->expenseNumber();
                if($request->type == 'employee')
                {
                    $expense->vender_id      = $request->employee_id;
                }
                elseif ($request->type == 'customer')
                {
                    $expense->vender_id      = $request->customer_id;
                }
                else
                {
                    $expense->vender_id      = $request->vender_id;
                }
                    $expense->bill_date      = $request->payment_date;
                    if($request->exp_type == 'pay_now'){
                        $expense->status         = 4;
                    }else{
                        $expense->status         = 0;
                    }
                    $expense->type           = 'Expense';
                    $expense->user_type      = $request->type;
                    $expense->exp_type       = $request->exp_type;
                    $expense->due_date       = $request->payment_date;
                    $expense->category_id    = !empty($request->category_id) ? $request->category_id :0;
                    $expense->order_number   = 0;
                    $expense->owned_by     = \Auth::user()->ownedId();
                    $expense->created_by     = \Auth::user()->creatorId();
                    $expense->save();
                    $products = $request->items;
                    $newitems = $request->items;

                    $total_amount=0;

                for($i = 0; $i < count($products); $i++)
                {
                    if(!empty($products[$i]['item']))
                    {
                        $expenseProduct              = new BillProduct();
                        $expenseProduct->bill_id     = $expense->id;
                        $expenseProduct->product_id  = $products[$i]['item'];
                        $expenseProduct->quantity    = $products[$i]['quantity'];
                        $expenseProduct->tax         = $products[$i]['tax'];
                        $expenseProduct->discount    = $products[$i]['discount'];
                        $expenseProduct->price       = $products[$i]['price'];
                        $expenseProduct->description = $products[$i]['description'];
                        $expenseProduct->save();
                    }

                    $expenseTotal=0;
                    if(!empty($products[$i]['chart_account_id'])){
                        $expenseAccount                    = new BillAccount();
                        $expenseAccount->chart_account_id  = $products[$i]['chart_account_id'];
                        $expenseAccount->price             = $products[$i]['amount'];
                        $expenseAccount->description       = $products[$i]['description'];
                        $expenseAccount->type              = 'Expense';
                        $expenseAccount->ref_id            = $expense->id;
                        $expenseAccount->save();
                        $expenseTotal= $expenseAccount->price;
                        $newitems[$i]['prod_id'] = $expenseAccount->id;
                        $total_amount += $products[$i]['amount'];
                    }

                    //inventory management (Quantity)
                    // if(!empty($expenseProduct))
                    // {
                    //     Utility::total_quantity('plus',$expenseProduct->quantity,$expenseProduct->product_id);
                    // }

                    //Product Stock Redashboardrt
                    // if(!empty($products[$i]['item']))
                    // {
                    //     $type='bill';
                    //     $type_id = $expense->id;
                    //     $description=$products[$i]['quantity'].'  '.__('quantity purchase in bill').' '. \Auth::user()->expenseNumberFormat($expense->bill_id);
                    //     Utility::addProductStock( $products[$i]['item'],$products[$i]['quantity'],$type,$description,$type_id);
                    //     $total_amount += ($expenseProduct->quantity * $expenseProduct->price)+$expenseTotal ;

                    // }

                }

                // if(!empty($request->chart_account_id))
                // {

                //     $expenseaccount= ProductServiceCategory::find($request->category_id);
                //     $chart_account = ChartOfAccount::find($expenseaccount->chart_account_id);
                //     $expenseAccount                    = new BillAccount();
                //     $expenseAccount->chart_account_id  = $chart_account['id'];
                //     $expenseAccount->price             = $total_amount;
                //     $expenseAccount->description       = $request->description;
                //     $expenseAccount->type              = 'Expense';
                //     $expenseAccount->ref_id            = $expense->id;
                //     $expenseAccount->save();
                // }
                        $bankAccount = BankAccount::find($request->account_id);
                        $data['id'] = $expense->id;
                        $data['no']  = $expense->bill_id;
                        $data['date'] = $expense->bill_date;
                        $data['reference'] = $expense->id;
                        $data['description'] = $expense->bill_id;
                        $data['category'] = 'Expanse';
                        $data['owned_by'] = $expense->owned_by;
                        $data['created_by'] = $expense->created_by;
                        $data['account_id'] = $bankAccount->chart_account_id;
                        $data['items'] = $newitems;

                if($request->exp_type == 'pay_now'){
                    $bankAccount = BankAccount::find($request->account_id);

                    $expensePayment                 = new BillPayment();
                    $expensePayment->bill_id        =  $expense->id;
                    $expensePayment->date           = $request->payment_date;
                    $expensePayment->amount         = $request->totalAmount;
                    $expensePayment->account_id     = $request->account_id;
                    $expensePayment->bank_chart_account_id     = $bankAccount->chart_account_id;
                    $expensePayment->payment_method = 0;
                    $expensePayment->reference      = 'NULL';
                    $expensePayment->description    ='NULL';
                    $expensePayment->add_receipt    = 'NULL';
                    $expensePayment->save();

                    Utility::bankAccountBalance($request->account_id, $expensePayment->amount, 'debit');

                    $data['prod_id'] = $expensePayment->id;
                    $data['amount'] = $expensePayment->amount;

                    if(strtolower($bankAccount->bank_name) == 'cash' || strtolower($bankAccount->holder_name) == 'cash'){
                        $dataret  = Utility::cpv_entry($data);
                    }else{
                        $dataret  = Utility::bpv_entry($data);
                    }
                    $expensePayment->voucher_id = $dataret;
                    $expensePayment->save();
                    // $expense->voucher_id = $dataret;
                    // $expense->save();

                }
                if($request->exp_type == 'pay_later'){
                    $data['amount'] = @$total_amount;
                    $dataret  = Utility::jr_exp_entry($data);
                    $expense->voucher_id = $dataret;
                    $expense->save();

                    $debit              = new DebitNote();
                    $debit->bill        = $expense->id;
                    $debit->vendor      = $expense->vender_id;
                    $debit->date        = $expense->due_date;
                    $debit->amount      = @$total_amount;
                    $debit->description = 'auto create';
                    $debit->save();
                }

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());

                if($request->type == 'employee')
                {
                    $user = Employee::find($request->employee_id);
                    $contact =  $user->phone;
                }
                else if($request->type  == 'customer')
                {
                    $user = Customer::find($request->customer_id);
                    $contact = $user->contact;

                }
                else{
                    $user = Vender::find($request->vender_id);
                    $contact = $user->contact;
                }


                $expenseNotificationArr = [
                    'expense_number' => \Auth::user()->expenseNumberFormat($expense->bill_id),
                    'user_name' => \Auth::user()->name,
                    'bill_date' => $expense->bill_date,
                    'bill_due_date' => $expense->due_date,
                    'vendor_name' => $user->name,
                ];


                //Slack Notification
                if(isset($setting['bill_notification']) && $setting['bill_notification'] ==1)
                {
                    Utility::send_slack_msg('new_bill', $expenseNotificationArr);
                }
                //Telegram Notification
                if(isset($setting['telegram_bill_notification']) && $setting['telegram_bill_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_bill', $expenseNotificationArr);
                }
                //Twilio Notification
                if(isset($setting['twilio_bill_notification']) && $setting['twilio_bill_notification'] ==1)
                {
                    Utility::send_twilio_msg($contact,'new_bill', $expenseNotificationArr);
                }


                //webhook
                $module ='New Bill';
                $webhook =  Utility::webhookSetting($module);
                if($webhook)
                {
                    $parameter = json_encode($expense);
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                    if($status == true)
                    {
                        return redirect()->route('expense.index', $expense->id)->with('success', __('Expense successfully created.'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }

                DB::commit();
                return redirect()->route('expense.index', $expense->id)->with('success', __('Expense successfully created.'));

            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
                return redirect()->back()->with('error', 'something went wrong');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($ids)
    {

        if(\Auth::user()->can('show bill'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Expense Not Found.'));
            }

            $id   = Crypt::decrypt($ids);

            $expense = Bill::find($id);



            if(!empty($expense) && $expense->created_by == \Auth::user()->creatorId())
            {
                $expensePayment = BillPayment::where('bill_id', $expense->id)->first();

                if($expense->user_type == 'employee')
                {
                    $user      = $expense->employee;
                }
                elseif ($expense->user_type == 'customer')
                {
                    $user      = $expense->customer;
                }
                else
                {
                    $user      = $expense->vender;
                }



                $item      = $expense->items;
                $accounts  = $expense->accounts;
                $items     = [];
                if(!empty($item) && count($item) > 0)
                {
                    foreach ($item as $k=>$val)
                    {
                        if(!empty($accounts[$k]))
                        {
                            $val['chart_account_id']=$accounts[$k]['chart_account_id'];
                            $val['account_id']=$accounts[$k]['id'];
                            $val['amount']=$accounts[$k]['price'];
                        }
                        $items[]=$val;
                    }
                }
                else{

                    foreach ($accounts as $k=>$val){
                        $val1['chart_account_id']=$accounts[$k]['chart_account_id'];
                        $val1['account_id']=$accounts[$k]['id'];
                        $val1['amount']=$accounts[$k]['price'];
                        $items[]=$val1;

                    }
                }


                return view('expense.view', compact('expense', 'user', 'items', 'expensePayment'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function items(Request $request)
    {
        $items = BillProduct::where('bill_id', $request->bill_id)->where('product_id', $request->product_id)->first();
        return json_encode($items);
    }

    public function edit($ids)
    {

        if(\Auth::user()->can('edit bill'))
        {
            try {
                $id       = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Expense Not Found.'));
            }

            $id       = Crypt::decrypt($ids);
            $expense     = Bill::find($id);

            if(!empty($expense))
            {
                $category     = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('type', ['product & service', 'income',])
                    ->get()->pluck('name', 'id');
                $category->prepend('Select Category', '');
                $expense_number      = \Auth::user()->expenseNumberFormat($expense->bill_id);

                $venders          = Vender::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                $employees        = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $employees->prepend('Select Employee', '');

                $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $customers->prepend('Select Customer', '');


                $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');


                $chartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                    ->where('created_by', \Auth::user()->creatorId())->get()
                    ->pluck('code_name', 'id');
                $chartAccounts->prepend('Select Account', '');

                $bank_Account   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))
                    ->where('created_by', \Auth::user()->creatorId())
                    ->get()->pluck('name', 'id');


                //for item and account show in repeater
                $item      = $expense->items;
                $accounts  = $expense->accounts;
                $items     = [];
                if(!empty($item) && count($item) > 0)
                {
                    foreach ($item as $k=>$val)
                    {
                        if(!empty($accounts[$k]))
                        {
                            $val['chart_account_id']=$accounts[$k]['chart_account_id'];
                            $val['account_id']=$accounts[$k]['id'];
                            $val['amount']=$accounts[$k]['price'];
                        }
                        $items[]=$val;
                    }
                }
                else{
                    foreach ($accounts as $k=>$val){
                        $val1['chart_account_id']=$accounts[$k]['chart_account_id'];
                        $val1['account_id']=$accounts[$k]['id'];
                        $val1['amount']=$accounts[$k]['price'];
                        $items[]=$val1;

                    }
                }

                return view('expense.edit', compact('employees','customers','venders', 'product_services', 'expense', 'expense_number', 'category',
                    'bank_Account','chartAccounts','items'));
            }
            else{
                return redirect()->back()->with('error', __('Expense Not Found.'));
            }


        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit bill'))
        {
            $expense = Bill::find($id);

            if ($expense->created_by == \Auth::user()->creatorId()) {

                $validator = \Validator::make(
                    $request->all(),
                    [
//                        'vender_id' => 'required',
                        'bill_date' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('expense.index')->with('error', $messages->first());
                }
                DB::beginTransaction();
                try {
                    $expense->vender_id      = $request->vender_id;

                    if($request->type == 'employee')
                    {
                        $expense->vender_id      = $request->employee_id;
                    }
                    elseif ($request->type == 'customer')
                    {
                        $expense->vender_id      = $request->customer_id;
                    }
                    else
                    {
                        $expense->vender_id      = $request->vender_id;
                    }

                    $expense->bill_date      = $request->bill_date;
                    $expense->due_date       = $request->bill_date;
                    $expense->order_number   = 0;
                    $expense->category_id    = $request->category_id;
                    $expense->save();

                    $products = $request->items;
                    $newitems = $request->items;

                    $total_amount=0;
                    $total_pay = 0;
                    if ($expense->status == 0 || $expense->status == 1 || \Auth::user()->type == 'company') {
                        for ($i = 0; $i < count($products); $i++)
                        {
                            $expenseProduct = BillProduct::find($products[$i]['id']);
                            if ($expenseProduct == null)
                            {
                                $expenseProduct             = new BillProduct();
                                $expenseProduct->bill_id    = $expense->id;

                                if(isset($products[$i]['items']) ) {
                                    Utility::total_quantity('plus', $products[$i]['quantity'], $products[$i]['items']);
                                }

                                // $updatePrice= ($products[$i]['price']*$products[$i]['quantity'])+($products[$i]['itemTaxPrice'])-($products[$i]['discount']);
                                // Utility::updateUserBalance('vendor', $request->vender_id, $updatePrice, 'debit');

                            }
                            else{

                                // Utility::total_quantity('minus',$expenseProduct->quantity,$expenseProduct->product_id);
                            }

                            if (isset($products[$i]['items'])) {
                                $expenseProduct->product_id = $products[$i]['items'];
                                $expenseProduct->quantity    = $products[$i]['quantity'];
                                $expenseProduct->tax         = $products[$i]['tax'];
                                $expenseProduct->discount    = $products[$i]['discount'];
                                $expenseProduct->price       = $products[$i]['price'];
                                $expenseProduct->description = $products[$i]['description'];
                                $expenseProduct->save();
                            }

                        if($expense->exp_type == 'pay_now'){
                            $voucher = JournalEntry::where('category', 'Expanse')->where('reference_id', $expense->id)->whereIn('voucher_type', ['BPV','CPV'])->first();
                        }else if($expense->exp_type == 'pay_later'){
                            $voucher = JournalEntry::where('category', 'Expanse')->where('reference_id', $expense->id)->where('voucher_type', 'JV')->first();
                        }

                            $expenseTotal=0;
                            if(!empty($products[$i]['chart_account_id'])){
                                $expenseAccount = BillAccount::find($products[$i]['account_id']);

                                if ($expenseAccount == null) {
                                    $expenseAccount                    = new BillAccount();
                                    $expenseAccount->chart_account_id = $products[$i]['chart_account_id'];
                                    $expenseAccount->price             = $products[$i]['amount'];
                                    $expenseAccount->description       = $products[$i]['description'];
                                    $expenseAccount->type              = 'Expanse';
                                    $expenseAccount->ref_id            = $expense->id;
                                    $expenseAccount->save();
                                    $expenseTotal= $expenseAccount->price;

                                    $journalItem = new JournalItem();
                                    $journalItem->journal = $voucher->id;
                                    $journalItem->account = $products[$i]['chart_account_id'];
                                    $journalItem->description = $products[$i]['description'];
                                    $journalItem->product_ids = $expenseAccount->id;
                                    $journalItem->credit = 0;
                                    $journalItem->debit =   $products[$i]['amount'];
                                    $journalItem->save();

                                    $total_pay += $expenseAccount->price;
                                }
                                else{
                                    $expenseAccount->chart_account_id = $products[$i]['chart_account_id'];
                                    $expenseAccount->price             = $products[$i]['amount'];
                                    $expenseAccount->description       = $products[$i]['description'];
                                    $expenseAccount->type              = 'Expanse';
                                    $expenseAccount->ref_id            = $expense->id;
                                    $expenseAccount->save();
                                    $expenseTotal= $expenseAccount->price;

                                    $jouitem = JournalItem::where('journal', $voucher->id)->where('product_ids', $expenseAccount->id)->first();
                                    $jouitem->account  = $expenseAccount->chart_account_id;
                                    $jouitem->debit = $expenseAccount->price;
                                    $jouitem->save();
                                    $total_pay += $expenseAccount->price;

                                    // $jouitem2 = JournalItem::where('journal', $voucher->id)->where('product_ids', $expenseProduct->id)->where('account',$expenseProduct->account_id)->first();
                                    // $jouitem2->credit = ($products[$i]['price'] * $products[$i]['quantity']) - ($products[$i]['discount']);
                                    // $jouitem->save();

                                }
                            }
                            if($expense->exp_type == 'pay_now'){
                                $product = BillPayment::where('bill_id',$expense->id)->first();

                                if($product->amount > $total_pay){
                                    $taxdiff = $product->amount - $total_pay;
                                    $newval = $total_pay;
                                    // dd($taxdiff);
                                    Utility::bankAccountBalance($product->account_id, $taxdiff, 'credit');
                                }elseif($product->amount < $total_pay){
                                    $taxdiff = $total_pay - $product->amount;
                                    $newval = $total_pay;
                                    // dd($taxdiff,'asdas',$total_pay);
                                    Utility::bankAccountBalance($product->account_id, $taxdiff, 'debit');
                                }else{
                                    $newval = $product->amount;
                                }
                                $product->amount = $newval;
                                $product->save();

                                $bankAccount = BankAccount::find($product->account_id);
                                $item_last2 = JournalItem::where('journal',$voucher->id)->where('account',$bankAccount->chart_account_id)->first();
                                $item_last2->credit = $newval;
                                $item_last2->save();
                            }else if($expense->exp_type == 'pay_later'){
                                $types = ChartOfAccountType::where('created_by', '=', $expense->created_by)->where('name', 'Liabilities')->first();
                                if ($types) {
                                    $sub_type = ChartOfAccountSubType::where('type', $types->id)->where('name', 'Current Liabilities')->first();
                                    $account = ChartOfAccount::where('type', $types->id)->where('sub_type', $sub_type->id)->where('name', 'Account Payable')->first();
                                }
                                $item_last = JournalItem::where('journal', $voucher->id)->where('account', $account->id)->first();
                                // $item_last->debit = $item_last->debit - ($total_pay + $newtax);
                                $item_last->credit = $total_pay;
                                $item_last->save();

                                $debit    = DebitNote::where('bill',$expense->id)->first();
                                if($debit){
                                    $debit->amount      =  $expense->newgetDue();
                                    $debit->save();
                                }else{
                                    $debit              = new DebitNote();
                                    $debit->bill        = $expense->id;
                                    $debit->vendor      = $expense->vender_id;
                                    $debit->date        = $expense->due_date;
                                    $debit->amount      = $expense->newgetDue();
                                    $debit->description = 'auto create';
                                    $debit->save();
                                }
                            }

                            // if ($products[$i]['id'] > 0) {
                            //     Utility::total_quantity('plus',$products[$i]['quantity'],$expenseProduct->product_id);
                            // }

                            //Product Stock Report
                            // $type='bill';
                            // $type_id = $expense->id;
                            // StockReport::where('type','=','bill')->where('type_id','=',$expense->id)->delete();
                            // $description=$products[$i]['quantity'].'  '.__(' quantity purchase in bill').' '. \Auth::user()->expenseNumberFormat($expense->bill_id);

                            // if(isset($products[$i]['items']) ){
                            //     Utility::addProductStock( $products[$i]['items'],$products[$i]['quantity'],$type,$description,$type_id);
                            // }

                            $total_amount += ($expenseProduct->quantity * $expenseProduct->price)+$expenseTotal ;

                        }

                        // $expensePayment                 = new BillPayment();
                        // $expensePayment->bill_id        =  $expense->id;
                        // $expensePayment->date           = $request->bill_date;
                        // $expensePayment->amount         = $request->totalAmount;
                        // $expensePayment->account_id     = $request->account_id;
                        // $expensePayment->payment_method = 0;
                        // $expensePayment->reference      = 'NULL';
                        // $expensePayment->description    ='NULL';
                        // $expensePayment->add_receipt    = 'NULL';
                        // $expensePayment->save();

                        // if(!empty($request->chart_account_id))
                        // {
                        //     $expenseaccount= ProductServiceCategory::find($request->category_id);
                        //     $chart_account = ChartOfAccount::find($expenseaccount->chart_account_id);
                        //     $expenseAccount                    = new BillAccount();
                        //     $expenseAccount->chart_account_id  = $chart_account['id'];
                        //     $expenseAccount->price             = $total_amount;
                        //     $expenseAccount->description       = $request->description;
                        //     $expenseAccount->type              = 'Bill Category';
                        //     $expenseAccount->ref_id            = $expense->id;
                        //     $expenseAccount->save();
                        // }

                    } else {
                        return redirect()->back()->with('error', __("You Can't Update Invoice Products"));
                    }

                    DB::commit();
                    return redirect()->route('expense.index')->with('success', __('Expense successfully updated.'));
                } catch (\Exception $e) {
                    DB::rollback();
                    dd($e);
                    return redirect()->back()->with('error', 'something went wrong');
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function expense($expense_id)
    {

        $settings = Utility::settings();
        try {
            $expenseId       = Crypt::decrypt($expense_id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Bill Not Found.'));
        }
        $expenseId   = Crypt::decrypt($expense_id);

        $expense  = Bill::where('id', $expenseId)->first();
        $data  = DB::table('settings');
        $data  = $data->where('created_by', '=', $expense->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $vendor = $expense->vender;

        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        $items         = [];

        foreach($expense->items as $product)
        {

            $item              = new \stdClass();
            $item->name        = !empty($product->product()) ? $product->product()->name : '';
            $item->quantity    = $product->quantity;
            $item->tax         = $product->tax;
            $item->discount    = $product->discount;
            $item->price       = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = Utility::tax($product->tax);
            $itemTaxes = [];
            if(!empty($item->tax))
            {
                foreach($taxes as $tax)
                {
                    $taxPrice      = Utility::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate . '%';
                    $itemTax['price'] = Utility::priceFormat($settings, $taxPrice);
                    $itemTax['tax_price'] =$taxPrice;
                    $itemTaxes[]      = $itemTax;


                    if(array_key_exists($tax->name, $taxesData))
                    {
                        $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                    }
                    else
                    {
                        $taxesData[$tax->name] = $taxPrice;
                    }

                }

                $item->itemTax = $itemTaxes;
            }
            else
            {
                $item->itemTax = [];
            }
            $items[] = $item;
        }

        $expense->itemData      = $items;
        $expense->totalTaxPrice = $totalTaxPrice;
        $expense->totalQuantity = $totalQuantity;
        $expense->totalRate     = $totalRate;
        $expense->totalDiscount = $totalDiscount;
        $expense->taxesData     = $taxesData;
        $expense->customField   = CustomField::getData($expense, 'bill');

        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($expense->created_by);
        $expense_logo = $settings_data['bill_logo'];
        if(isset($expense_logo) && !empty($expense_logo))
        {
            $img = Utility::get_file('bill_logo/') . $expense_logo;
        }
        else{
            $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }

        if($expense)
        {
            $color      = '#' . $settings['bill_color'];
            $font_color = Utility::getFontColor($color);

            return view('bill.templates.' . $settings['bill_template'], compact('expense', 'color', 'settings', 'vendor', 'img', 'font_color'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function productDestroy(Request $request)
    {

        if(\Auth::user()->can('delete bill product'))
        {
            DB::beginTransaction();
            try {
                $expenseProduct=BillAccount::find($request->id);
                $bill=Bill::find($expenseProduct->ref_id);
                $billProduct=BillPayment::where('bill_id',$bill->id)->first();
                // dd($expenseProduct,$billProduct,$request->all());
                if ($bill->status == 0 || $bill->status == 1 || \Auth::user()->type == 'company') {
                    if($bill->exp_type == 'pay_now'){
                        $voucher =  JournalEntry::where('category', 'Expanse')->where('reference_id', $bill->id)->whereIn('voucher_type', ['BPV','CPV'])->first();
                        $item = JournalItem::where('journal',$voucher->id)->where('account',$expenseProduct->chart_account_id)->where('product_ids',$expenseProduct->id)->first();
                        $bankAccount = BankAccount::find($billProduct->account_id);
                        $value = $item->debit;
                        $item_last2 = JournalItem::where('journal',$voucher->id)->where('account',$billProduct->bank_chart_account_id)->first();
                        $item_last2->credit = $item_last2->credit - $item->debit;
                        $item_last2->save();

                        $billProduct->amount = $billProduct->amount - $item->debit;
                        $billProduct->save();
                        $item->delete();
                        Utility::bankAccountBalance($billProduct->account_id, $value, 'credit');
                    }else if($bill->exp_type == 'pay_later'){
                        $voucher =  JournalEntry::where('category', 'Expanse')->where('reference_id', $bill->id)->where('voucher_type', 'JV')->first();
                        $item = JournalItem::where('journal',$voucher->id)->where('account',$expenseProduct->chart_account_id)->where('product_ids',$expenseProduct->id)->first();
                        $value = $item->debit;

                        $types = ChartOfAccountType::where('created_by', '=', $bill->created_by)->where('name', 'Liabilities')->first();
                        if ($types) {
                            $sub_type = ChartOfAccountSubType::where('type', $types->id)->where('name', 'Current Liabilities')->first();
                            $account = ChartOfAccount::where('type', $types->id)->where('sub_type', $sub_type->id)->where('name', 'Account Payable')->first();
                        }
                        $item_last2 = JournalItem::where('journal',$voucher->id)->where('account',@$account->id)->first();
                        @$item_last2->credit = @$item_last2->credit - $value ;
                        @$item_last2->save();
                        $item->delete();

                        $debit    = DebitNote::where('bill',$bill->id)->first();
                        if($debit){
                            $debit->amount      = $debit->amount - $value;
                            $debit->save();
                        }

                    }
                    $expenseProduct->delete();
                    // $expenseProduct=BillProduct::find($request->id);
                    // $expense=Bill::find($expenseProduct->id);

                    Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');

                    BillProduct::where('id', '=', $request->id)->delete();

                } else {
                    return redirect()->back()->with('error', __("You Can't perform any action"));
                }
                DB::commit();
                return redirect()->back()->with('success', __('Bill product successfully deleted.'));
            } catch (\Exception $e) {
                DB::rollback();
                // dd($e);
                return redirect()->back()->with('error', 'something went wrong');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {

        if(\Auth::user()->can('delete bill'))
        {
            $expense= Bill::find($id);
            if($expense->created_by == \Auth::user()->creatorId())
            {
                DB::beginTransaction();
                try {
                    if ($expense->status == 0 || $expense->status == 1 || \Auth::user()->type == 'company') {
                        $expensepayments = $expense->payments;

                        foreach($expensepayments as $key => $value)
                        {
                            Utility::bankAccountBalance($value->account_id, $value->amount, 'credit');

                            $expensepayment = BillPayment::find($value->id)->first();
                            $expensepayment->delete();
                        }
                        $expense->delete();

                        if($expense->exp_type == 'pay_now'){
                            $voucher = JournalEntry::where('category', 'Expanse')->where('reference_id', $expense->id)->get();

                                if (!$voucher->isEmpty()) {
                                    foreach ($voucher as $v) {
                                        $vitem =JournalItem::where('journal', $v->id)->delete();
                                        $v->delete();
                                    }
                                }
                        }else if($expense->exp_type == 'pay_later'){
                            $voucher = JournalEntry::where('category', 'Expanse')->where('reference_id', $expense->id)->get();
                            if (!$voucher->isEmpty()) {
                                foreach ($voucher as $v) {
                                    $vitem =JournalItem::where('journal', $v->id)->delete();
                                    $v->delete();
                                }
                            }
                            DebitNote::where('bill', '=', $expense->id)->delete();
                        }

                        if($expense->vender_id != 0 && $expense->status!=0)
                        {
                            Utility::updateUserBalance('vendor', $expense->vender_id, $expense->getDue(), 'credit');
                        }
                        BillProduct::where('bill_id', '=', $expense->id)->delete();

                        BillAccount::where('ref_id', '=', $expense->id)->delete();
                        DB::commit();

                        return redirect()->route('expense.index')->with('success', __('Expense successfully deleted.'));
                    } else {
                        return redirect()->back()->with('error', __('Permission denied.'));
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    dd($e);
                    return redirect()->back()->with('error', 'something went wrong');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }
    public function branch_category(Request $request)
    {
        $category     = ProductServiceCategory::where('owned_by', $request->id)
        ->whereNotIn('type', ['product & service', 'income',])->get();
        if($category == null){
            $result = [
                'status' => 'error',
                'company' => 'null',
            ];
            return response()->json($result);
        }else{
            $result = [
                'status' => 'success',
                'category' => $category,
            ];
            return response()->json($result);
        }
    }

    public function payment($expanse_id)
    {
        if(\Auth::user()->can('create payment bill'))
        {
            $expense    = Bill::where('id', $expanse_id)->first();
            $venders = Vender::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('expense.payment', compact('venders', 'categories', 'accounts', 'expense'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));

        }
    }

    public function createPayment(Request $request, $bill_id)
    {
        // dd($request->all());
        if(\Auth::user()->can('create payment bill'))
        {
            DB::beginTransaction();
            try {
                $validator = \Validator::make(
                    $request->all(), [
                                    'date' => 'required',
                                    'amount' => 'required',
                                    'account_id' => 'required',
                                ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $bankAccount = BankAccount::find($request->account_id);

                $billPayment                 = new BillPayment();
                $billPayment->bill_id        = $bill_id;
                $billPayment->date           = $request->date;
                $billPayment->amount         = $request->amount;
                $billPayment->account_id     = $request->account_id;
                $billPayment->payment_method = 0;
                $billPayment->reference      = $request->reference;
                $billPayment->description    = $request->description;
                $billPayment->bank_chart_account_id     = $bankAccount->chart_account_id;
                if(!empty($request->add_receipt))
                {
                    //storage limit
                    $image_size = $request->file('add_receipt')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                    if($result==1)
                    {
                        if($billPayment->add_receipt)
                        {
                            $path = storage_path('uploads/payment' . $billPayment->add_receipt);
                            if(file_exists($path))
                            {
                                \File::delete($path);
                            }
                        }
                        $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                        $billPayment->add_receipt = $fileName;
                        $dir        = 'uploads/payment';
                        $path = Utility::upload_file($request,'add_receipt',$fileName,$dir,[]);
                        if($path['flag']==0){
                            return redirect()->back()->with('error', __($path['msg']));
                        }

                    }

                }
                $billPayment->save();

                $bill  = Bill::where('id', $bill_id)->first();
                $due   = $bill->newgetDue();
                $total = $bill->getTotal();

                if($bill->status == 0)
                {
                    $bill->send_date = date('Y-m-d');
                    $bill->save();
                }

                if($due <= 0)
                {
                    $bill->status = 4;
                    $bill->save();
                }
                else
                {
                    $bill->status = 3;
                    $bill->save();
                }
                $billPayment->user_id    = $bill->vender_id;
                $billPayment->user_type  = 'Vender';
                $billPayment->type       = 'Partial';
                $billPayment->owned_by = \Auth::user()->id;
                $billPayment->created_by = \Auth::user()->id;
                $billPayment->payment_id = $billPayment->id;
                $billPayment->category   = 'Expanse';
                $billPayment->account    = $request->account_id;
                Transaction::addTransaction($billPayment);

                $vender = Vender::where('id', $bill->vender_id)->first();

                // $payment         = new BillPayment();
                // $payment->name   = $vender['name'];
                // $payment->method = '-';
                // $payment->date   = \Auth::user()->dateFormat($request->date);
                // $payment->amount = \Auth::user()->priceFormat($request->amount);
                // $payment->bill   = 'bill ' . \Auth::user()->billNumberFormat($billPayment->bill_id);

    //            Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');
                // Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');


                Utility::bankAccountBalance($request->account_id, $request->amount, 'debit');

                $bankAccount = BankAccount::find($request->account_id);
                    $data['id'] = $bill_id;
                    $data['no'] = $bill->bill_id;
                    $data['date'] = $billPayment->date;
                    $data['reference'] = $billPayment->reference;
                    $data['description'] = $billPayment->description;
                    $data['amount'] = $billPayment->amount;
                    $data['category'] = 'Bill';
                    $data['type'] = 'Expanse';
                    $data['prod_id'] = $billPayment->id;
                    $data['owned_by'] = $billPayment->owned_by;
                    $data['created_by'] = \Auth::user()->creatorId();
                    $data['account_id'] = $bankAccount->chart_account_id;
                    $bankAccount = BankAccount::find($request->account_id);

                    if(strtolower($bankAccount->bank_name) == 'cash' || strtolower($bankAccount->holder_name) == 'cash'){
                        $dataret  = Utility::cpv_entry($data);
                    }else{
                        $dataret  = Utility::bpv_entry($data);
                    }
                    BillPayment::where('id', $billPayment->id)->update([
                        'voucher_id' => $dataret,
                    ]);

                // Send Email
                $setings = Utility::settings();
                // if($setings['new_bill_payment'] == 1)
                // {

                //     $vender = Vender::where('id', $bill->vender_id)->first();
                //     $billPaymentArr = [
                //         'vender_name'   => $vender->name,
                //         'vender_email'  => $vender->email,
                //         'payment_name'  =>$payment->name,
                //         'payment_amount'=>$payment->amount,
                //         'payment_bill'  =>$payment->bill,
                //         'payment_date'  =>$payment->date,
                //         'payment_method'=>$payment->method,
                //         'company_name'=>$payment->method,

                //     ];


                //     // $resp = Utility::sendEmailTemplate('new_bill_payment', [$vender->id => $vender->email], $billPaymentArr);

                //     return redirect()->back()->with('success', __('Payment successfully added.') .((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : '').(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '') );

                // }
                DB::commit();
                return redirect()->back()->with('success', __('Payment successfully added.'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
            } catch (\Exception $e) {
                DB::rollback();
                dd($e);
                return redirect()->back()->with('error', 'something went wrong');
            }

        }

    }

    public function paymentDestroy(Request $request, $bill_id, $payment_id)
    {

        if(\Auth::user()->can('delete payment bill'))
        {
            $bill = Bill::where('id', $bill_id)->first();
            if ($bill->status == 0 || $bill->status == 1 || \Auth::user()->type == 'company') {
                $payment = BillPayment::find($payment_id);
                $voucher = JournalEntry::where('category', 'Invoice')->where('reference_id', $bill->id)->where('prod_id',$payment->id)->orwhere('id',$payment->voucher_id)->first();
                $vitem =JournalItem::where('journal', $voucher->id)->delete();
                $voucher->delete();
                BillPayment::where('id', '=', $payment_id)->delete();

                $bill = Bill::where('id', $bill_id)->first();

                $due   = $bill->getDue();
                $total = $bill->getTotal();

                if($due > 0 && $total != $due)
                {
                    $bill->status = 3;
                }
                else
                {
                    $bill->status = 2;
                }

                //     Utility::userBalance('vendor', $bill->vender_id, $payment->amount, 'credit');
                Utility::updateUserBalance('vendor', $bill->vender_id, $payment->amount, 'debit');

                Utility::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

                if(!empty($payment->add_receipt))
                {
                    //storage limit
                    $file_path = '/uploads/payment/'.$payment->add_receipt;
                    $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                }

                $bill->save();
                $type = 'Partial';
                $user = 'Vender';
                Transaction::destroyTransaction($payment_id, $type, $user);
            } else {
                return redirect()->back()->with('error', __("You Can't perform any action"));
            }
            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
