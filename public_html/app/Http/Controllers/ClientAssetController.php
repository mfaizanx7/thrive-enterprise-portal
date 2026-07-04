<?php

namespace App\Http\Controllers;

use App\Models\ClientAsset;
use App\Models\ClientAssetDetail;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class ClientAssetController extends Controller
{
    public function index(Request $request)
    {
        if(\Auth::user()->can('manage assets'))
        {
            if(\Auth::user()->type == 'company'){
                $branches = User::where('type', '=', 'branch')->get()->pluck('name', 'id');
                $branches->prepend(\Auth::user()->name, \Auth::user()->id);               
                $branches->prepend('Select Branch', '');
                $query = ClientAsset::with('company')->where('created_by', '=', \Auth::user()->creatorId());
            }else{
                $branches = User::where('id', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                $branches->prepend('Select Branch', '');
                $query = ClientAsset::with('company')->where('owned_by', '=', \Auth::user()->ownedId());
            }
            if (!empty($request->branches)) {
                $query->where('owned_by', '=', $request->branches);
            }
            $assets = $query->get();

            return view('client_assets.index', compact('assets','branches'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create assets'))
        {
            // $employee      = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'user_id');
            if(\Auth::user()->type == 'company'){
                $company      = Company::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }else{
                $company      = Company::where('owned_by', \Auth::user()->ownedId())->get()->pluck('name', 'id');
            }

            return view('client_assets.create',compact('company'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create assets'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'company_id' => 'required',
                    'issue_date' => 'required',
                    'quantity' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $assets                 = new ClientAsset();
            // $assets->employee_id         = !empty($request->employee_id) ? implode(',', $request->employee_id) : '';
            $assets->company_id         = $request->company_id;
            // $assets->name           = $request->name;
            $assets->purchase_date  = $request->issue_date;
            $assets->supported_date = $request->end_date;
            // $assets->amount         = $request->amount;
            $assets->description    = $request->description;
            $assets->owned_by     = \Auth::user()->ownedId();
            $assets->created_by     = \Auth::user()->creatorId();
            $assets->save();

            if($request->name){
                for ($i=0; $i < count($request->name) ; $i++) {
                    ClientAssetDetail::create(
                        [
                            'asset_id' => $assets->id,
                            'name' => $request->name[$i],
                            'quantity' => $request->quantity[$i],
                        ]
                    );
                }
            }
            
            return redirect()->route('account-assets-client.index')->with('success', __('Assets successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(ClientAsset $asset)
    {
        //
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit assets'))
        {
            if(\Auth::user()->type == 'company'){
                $asset = ClientAsset::with('assetdetail')->find($id);
                $company      = Company::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }else{
                $asset = ClientAsset::with('assetdetail')->find($id);
                $company      = Company::where('owned_by', \Auth::user()->ownedId())->get()->pluck('name', 'id');
            }
            // $asset = Asset::find($id);
            // $employee      = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $asset->employee_id      = explode(',', $asset->employee_id);
            return view('client_assets.edit', compact('asset','company'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit assets'))
        {
            $asset = ClientAsset::find($id);
            if($asset->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                        // 'name' => 'required',
                                        'company_id' => 'required',
                                        'issue_date' => 'required',
                                        // 'quantity' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                // $asset->name           = $request->name;
                // $asset->employee_id         = !empty($request->employee_id) ? implode(',', $request->employee_id) : '';
                // $asset->purchase_date  = $request->purchase_date;
                // $asset->supported_date = $request->supported_date;
                // $asset->amount         = $request->amount;
                // $asset->description    = $request->description;
                // $asset->save();

                $asset->company_id         = $request->company_id;
                $asset->purchase_date  = $request->issue_date;
                $asset->supported_date = $request->end_date;
                $asset->description    = $request->description;
                $asset->save();
               
                
            // Determine which row you want to update and which ones you want to delete.
            $existingRows = ClientAssetDetail::where('asset_id', $asset->id)->get();
            if ($existingRows->isEmpty()) {}{
                $deleteIds = $existingRows->pluck('id')->diff($request->input('ids'));
            }
            // Update the specific row(s).
            if($request->input('ids')){
                for ($i=0; $i<count($request->input('ids')); $i++) {
                    ClientAssetDetail::where('id', $request->input('ids')[$i])->update(['name' => $request->input('names')[$i],'quantity' => $request->quantitys[$i]]);
                }
            }

            // Delete the other row(s).
            if(@$deleteIds){
                ClientAssetDetail::whereIn('id', $deleteIds)->delete();
            }
            if($request->input('name')){
                for ($j=0; $j<count($request->input('name')); $j++) {
                    $section = ClientAssetDetail::create([
                        'asset_id' => $asset->id,
                        'name' => $request->name[$j],
                        'quantity' => $request->quantity[$j],
                    ]);
                }
            }
                return redirect()->route('account-assets-client.index')->with('success', __('Assets successfully updated.'));
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


    public function destroy($id)
    {
        if(\Auth::user()->can('delete assets'))
        {
            $asset = ClientAsset::find($id);
            if($asset->created_by == \Auth::user()->creatorId())
            {
                $asset->delete();

                return redirect()->route('account-assets-client.index')->with('success', __('Assets successfully deleted.'));
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
