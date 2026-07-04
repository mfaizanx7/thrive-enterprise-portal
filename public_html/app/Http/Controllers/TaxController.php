<?php

namespace App\Http\Controllers;

use App\Models\BillProduct;
use App\Models\ChartOfAccount;
use App\Models\InvoiceProduct;
use App\Models\ProposalProduct;
use App\Models\Tax;
use Auth;
use Illuminate\Http\Request;

class TaxController extends Controller
{


    public function index()
    {
        if(\Auth::user()->can('manage constant tax'))
        {
            $user = \Auth::user();
            $ownerId = $user->type === 'company' ? $user->creatorId() : $user->ownedId();
            $column = ($user->type == 'company') ? 'created_by' : 'owned_by';
            $taxes = Tax::with('chartAccount')->where($column, '=',$ownerId)->get();
            // $taxes = Tax::with('chartAccount')->where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('taxes.index')->with('taxes', $taxes);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create constant tax'))
        {
            $ChartofAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name, " (", chart_of_account_sub_types.name, ")") AS code_name, chart_of_accounts.id'))
                ->join('chart_of_account_sub_types', 'chart_of_accounts.sub_type', '=', 'chart_of_account_sub_types.id')
                ->join('chart_of_account_types', 'chart_of_accounts.type', '=', 'chart_of_account_types.id')
                ->whereIn('chart_of_account_types.name', ['Liabilities', 'Assets'])
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->orderBy('chart_of_account_sub_types.id')
                ->get()
                ->pluck('code_name', 'id');
            return view('taxes.create', compact('ChartofAccounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create constant tax'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name' => 'required|max:20',
                                    'rate' => 'required|numeric',
                                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $tax             = new Tax();
            $tax->name       = $request->name;
            $tax->rate       = $request->rate;
            $tax->account_id = $request->account_id;
            $tax->owned_by = \Auth::user()->ownedId();
            $tax->created_by = \Auth::user()->creatorId();
            $tax->save();

            return redirect()->route('taxes.index')->with('success', __('Tax rate successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Tax $tax)
    {
        return redirect()->route('taxes.index');
    }


    public function edit(Tax $tax)
    {
        if(\Auth::user()->can('edit constant tax'))
        {
            if($tax->created_by == \Auth::user()->creatorId())
            {
                $ChartofAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name, " (", chart_of_account_sub_types.name, ")") AS code_name, chart_of_accounts.id'))
                ->join('chart_of_account_sub_types', 'chart_of_accounts.sub_type', '=', 'chart_of_account_sub_types.id')
                ->join('chart_of_account_types', 'chart_of_accounts.type', '=', 'chart_of_account_types.id')
                ->whereIn('chart_of_account_types.name', ['Liabilities', 'Assets'])
                ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
                ->orderBy('chart_of_account_sub_types.id')
                ->get()
                ->pluck('code_name', 'id');
                return view('taxes.edit', compact('tax','ChartofAccounts'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, Tax $tax)
    {
        if(\Auth::user()->can('edit constant tax'))
        {
            if($tax->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'rate' => 'required|numeric',
                                    //    'account_id' => 'required|numeric',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $tax->name = $request->name;
                $tax->rate = $request->rate;
                // $tax->account_id = $request->account_id;
                $tax->save();

                return redirect()->route('taxes.index')->with('success', __('Tax rate successfully updated.'));
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

    public function destroy(Tax $tax)
    {
        if(\Auth::user()->can('delete constant tax'))
        {
            if($tax->created_by == \Auth::user()->creatorId())
            {
                $proposalData = ProposalProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
                $billData     = BillProduct::whereRaw("find_in_set('$tax->id',tax)")->first();
                $invoiceData  = InvoiceProduct::whereRaw("find_in_set('$tax->id',tax)")->first();

                if(!empty($proposalData) || !empty($billData) || !empty($invoiceData))
                {
                    return redirect()->back()->with('error', __('this tax is already assign to proposal or bill or invoice so please move or remove this tax related data.'));
                }

                $tax->delete();

                return redirect()->route('taxes.index')->with('success', __('Tax rate successfully deleted.'));
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
}
