<?php

namespace App\Http\Controllers;

use App\Mail\SendLeadEmail;
use App\Models\ClientDeal;
use App\Models\Deal;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\Label;
use App\Models\Lead;
use App\Models\LeadActivityLog;
use App\Models\LeadCall;
use App\Models\LeadDiscussion;
use App\Models\LeadEmail;
use App\Models\LeadFile;
use App\Models\LeadStage;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\Source;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\UserLead;
use App\Models\Utility;
use App\Models\WebhookSetting;
use Illuminate\Http\Request;
use App\Exports\LeadExport;
use App\Imports\LeadImport;
use App\Models\CustomField;
use App\Models\Notification;
use App\Models\WorkFlow;
use App\Models\WorkFlowAction;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Auth;
use Illuminate\Support\Facades\DB;
class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $filter = false;
        $requestData = $request->all();
        $combinedData = [];
        if (\Auth::user()->can('manage lead')) {
            if (\Auth::user()->default_pipeline) {
                $pipeline = Pipeline::where(function($query) {
                    $query->where('created_by', '=', \Auth::user()->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->where('id', '=', \Auth::user()->default_pipeline)->first();
                if (!$pipeline) {
                    if (\Auth::user()->type == 'company') {
                        $pipeline = Pipeline::where(function($query) {
                            $query->where('created_by', '=', \Auth::user()->creatorId())
                                ->orWhere('is_global', '=', 1);
                        })->first();
                    } else {
                        $pipeline = Pipeline::where('owned_by', '=', \Auth::user()->ownedId())->first();
                    }
                }
            } else {
                $pipeline = Pipeline::where(function($query) {
                    $query->where('created_by', '=', \Auth::user()->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->first();
            }


            $pipelines = Pipeline::where(function($query) {
                $query->where('created_by', '=', \Auth::user()->creatorId())
                    ->orWhere('is_global', '=', 1);
            })->get()->pluck('name', 'id');

            if (!$pipeline) {
                return redirect()->back()->with('error', __('Please create a pipeline first.'));
            }

            $labels = Label::where('created_by', '=', \Auth::user()->creatorId())->where('pipeline_id', $pipeline->id)->get();
            $query = Lead::query();
            if (!empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('subject', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            }
            if (!empty($request->date)) {
                if (count(explode('to', $request->date)) > 1) {
                    $date_range = explode(' to ', $request->date);
                    $query->whereBetween('date', $date_range);
                } elseif (!empty($request->date)) {
                    $date_range = [$request->date, $request->date];
                    $query->whereBetween('date', $date_range);
                }
            }
            if (!empty($request->labels)) {
                $label = is_array($request->labels) ? $request->labels : explode(',', $request->labels);
                $query->where(function ($query) use ($label) {
                    foreach ($label as $label_1) {
                        $query->orWhere('labels', 'LIKE', '%' . $label_1 . '%');
                    }
                });
            }
            if (!empty($requestData) && !empty($requestData['fields'])) {
                $filter = true;
                $fields = $requestData['fields'];
                $operators = $requestData['operators'];
                $values = $requestData['values'];
                $fields = array_values($fields);
                $operators = array_values($operators);
                $values = array_values($values);
                $fields = array_merge([null], $fields);
                $operators = array_merge([null], $operators);
                $values = array_merge([null], $values);
                $maxLength = max(count($fields), count($operators), count($values));
                for ($i = 0; $i < $maxLength; $i++) {
                    $combinedData[] = array_filter([
                        'field' => isset($fields[$i]) ? $fields[$i][0] : null,
                        'operator' => isset($operators[$i]) ? $operators[$i][0] : null,
                        'value' => isset($values[$i]) ? $values[$i] : null,
                    ]);
                }
                $combinedData = array_filter($combinedData);
                $customFields = CustomField::where('module', 'lead')->get();
                $standardFields = ['name', 'email', 'phone', 'subject', 'inbox_url', 'team_members', 'sr_no', 'update_value', 'follow_up_1', 'update_2_0', 'follow_up_2', 'follow_up_3'];
                $query = Lead::query()->where('pipeline_id', $pipeline->id);
                foreach ($combinedData as $index => $data) {
                    if (empty($data['field'])) {
                        continue;
                    }
                    // Standard lead column filter
                    if (in_array($data['field'], $standardFields)) {
                        $value = is_array($data['value']) ? ($data['value'][0] ?? null) : $data['value'];
                        if ($value === null || $value === '') {
                            continue;
                        }
                        switch (strtolower($data['operator'])) {
                            case 'like':
                                $query->where($data['field'], 'like', '%' . $value . '%');
                                break;
                            case '%like%':
                                $query->where($data['field'], 'like', $value);
                                break;
                            case '=':
                            default:
                                $query->where($data['field'], $value);
                                break;
                        }
                        continue;
                    }
                    // Custom field filter
                    $customField = $customFields->firstWhere('name', $data['field']);
                    if ($customField) {
                        $query->whereHas('customFieldValues', function ($q) use ($customField, $data) {
                            $q->where('field_id', '=', $customField->id);
                            if (is_array($data['value'])) {
                                switch (strtolower($data['operator'])) {
                                    case '=':
                                        $q->whereIn('value', $data['value']);
                                        break;
                                    case 'like':
                                        $q->where(function ($subQuery) use ($data) {
                                            foreach ($data['value'] as $value) {
                                                $subQuery->orWhere('value', 'like', '%' . $value . '%');
                                            }
                                        });
                                        break;
                                    case '%like%':
                                        $q->where(function ($subQuery) use ($data) {
                                            foreach ($data['value'] as $value) {
                                                $subQuery->orWhere('value', 'like', $value);
                                            }
                                        });
                                        break;
                                }
                            }
                        });
                    }
                }
                $leads = $query->get();
            }
            return view('leads.index', compact('pipelines', 'pipeline', 'labels', 'query', 'filter'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function lead_list()
    {
        $usr = \Auth::user();

        if ($usr->can('manage lead')) {
            if ($usr->default_pipeline) {
                $pipeline = Pipeline::where(function($query) use ($usr) {
                    $query->where('created_by', '=', $usr->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->where('id', '=', $usr->default_pipeline)->first();
                if (!$pipeline) {
                    $pipeline = Pipeline::where(function($query) use ($usr) {
                        $query->where('created_by', '=', $usr->creatorId())
                            ->orWhere('is_global', '=', 1);
                    })->first();
                }
            } else {
                $pipeline = Pipeline::where(function($query) use ($usr) {
                    $query->where('created_by', '=', $usr->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->first();
            }
            if (\Auth::user()->type == 'company') {
                $pipelines = Pipeline::where(function($query) {
                    $query->where('created_by', '=', \Auth::user()->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->get()->pluck('name', 'id');
            } else {
                $pipelines = Pipeline::where('owned_by', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
            }

            if (!$pipeline) {
                return redirect()->back()->with('error', __('Please create a pipeline first.'));
            }

            $leads = Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', $usr->id)->where('leads.pipeline_id', '=', $pipeline->id)->orderBy('leads.order')->get();

            return view('leads.list', compact('pipelines', 'pipeline', 'leads'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (\Auth::user()->can('create lead')) {
            $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('type', '!=', 'branch')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
            $users->prepend(__('Select User'), '');
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'lead')->get();

            return view('leads.create', compact('users', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Log::info('=== LEAD STORE STARTED ===');
        \Log::info('Request data: ' . json_encode($request->all()));
        \DB::beginTransaction();
        try {
        $usr = \Auth::user();
        \Log::info('User: ' . $usr->id . ', can create lead: ' . ($usr->can('create lead') ? 'yes' : 'no'));
        if ($usr->can('create lead')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'subject' => 'required',
                    'name' => 'required',
                    'email' => 'required|email',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                \Log::error('Validation failed: ' . $messages->first());
                return redirect()->back()->with('error', $messages->first());
            }

            // Default Field Value
            \Log::info('Default pipeline: ' . ($usr->default_pipeline ?? 'null'));
            if ($usr->default_pipeline) {
                $pipeline = Pipeline::where(function($query) use ($usr) {
                    $query->where('created_by', '=', $usr->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->where('id', '=', $usr->default_pipeline)->first();
                if (!$pipeline) {
                    $pipeline = Pipeline::where(function($query) use ($usr) {
                        $query->where('created_by', '=', $usr->creatorId())
                            ->orWhere('is_global', '=', 1);
                    })->first();
                }
            } else {
                $pipeline = Pipeline::where(function($query) use ($usr) {
                    $query->where('created_by', '=', $usr->creatorId())
                        ->orWhere('is_global', '=', 1);
                })->first();
            }
            \Log::info('Pipeline found: ' . ($pipeline ? $pipeline->id . ' - ' . $pipeline->name : 'NULL'));

            if (!$pipeline) {
                \Log::error('No pipeline found for user');
                return redirect()->back()->with('error', __('Please create a pipeline first.'));
            }

            $stage = LeadStage::where('pipeline_id', '=', $pipeline->id)->first();
            \Log::info('Stage found: ' . ($stage ? $stage->id . ' - ' . $stage->name : 'NULL'));
            // End Default Field Value

            if (empty($stage)) {
                return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
            } else {
                $lead = new Lead();
                $lead->name = $request->name;
                $lead->email = $request->email;
                $lead->phone = $request->phone;
                $lead->subject = $request->subject;
                $lead->user_id = $request->user_id;
                $lead->pipeline_id = $pipeline->id;
                $lead->stage_id = $stage->id;
                $lead->created_by = $usr->creatorId();
                $lead->date = date('Y-m-d');
                $lead->company_name = $request->company_name;
                $lead->sector = $request->sector;
                $lead->number_of_employees = $request->number_of_employees;
                $lead->revenue = $request->revenue;
                $lead->contact_person = $request->contact_person;
                $lead->region = $request->region;
                $lead->address = $request->address;
                $lead->save();
                \Log::info('Lead saved with ID: ' . $lead->id);
                $a=CustomField::saveData($lead, $request->customField);

                if ($request->user_id != \Auth::user()->id) {
                    $usrLeads = [
                        $usr->id,
                        $request->user_id,
                    ];
                } else {
                    $usrLeads = [
                        $request->user_id,
                    ];
                }

                $newStage = LeadStage::find($lead->stage_id);
                // WorkFlow get which is active
                $us_mail = 'false';
                $us_notify = 'false';
                $us_approve = 'false';
                $usr_Lead = [];
                try {
                    $workflow = WorkFlow::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'crm')->where('status', 1)->first();
                    \Log::info('Workflow found: ' . ($workflow ? 'yes' : 'no'));
                } catch (\Exception $e) {
                    \Log::error('Workflow query failed: ' . $e->getMessage());
                    $workflow = null;
                }
                if ($workflow) {
                    $workflowaction = WorkFlowAction::where('workflow_id', $workflow->id)->where('status', 1)->where('level_id', 1)->get();
                    foreach ($workflowaction as $action) {
                        $useraction = json_decode($action->assigned_users);
                        if (strtolower('create-lead') == $action->node_id) {
                            // Pick that stage user assign or change on lead
                            if (@$useraction != '') {
                                $useraction = json_decode($useraction);
                                foreach ($useraction as $anyaction) {
                                    // make new user array
                                    if ($anyaction->type == 'user') {
                                        $usr_Lead[] = $anyaction->id;
                                    }
                                }
                            }
                            //  if user assign on this stage then check for mail and notification conditions
                            $raw_json = trim($action->applied_conditions, '"');
                            $cleaned_json = stripslashes($raw_json);
                            $applied_conditions = json_decode($cleaned_json, true);

                            if (isset($applied_conditions['conditions']) && is_array($applied_conditions['conditions'])) {
                                $arr1 = [
                                    'products' => 'App\Models\ProductService',
                                    'sources' => 'App\Models\Source',
                                    'labels' => 'App\Models\Label',
                                ];
                                $arr = [
                                    'pipeline' => 'pipeline_name',
                                ];
                                $relate = [
                                    'pipeline_name' => 'pipeline',
                                ];
                                
                                foreach ($applied_conditions['conditions'] as $conditionGroup) {
                                    if (in_array($conditionGroup['action'], ['send_email', 'send_notification','send_approval'])) {
                                        $query = Lead::where('id',$lead->id);
                                        foreach ($conditionGroup['conditions'] as $condition) {
                                            $field = $condition['field'];
                                            $operator = $condition['operator'];
                                            $value = $condition['value'];
                                            if(isset($condition['origin']) && $condition['origin'] == 'db'){
                                                if (array_key_exists($field, $arr1)) {
                                                    $a =$arr1[$field]::where('name',$value)->pluck('id')->toArray();
                                                    if(isset($a) && count($a) > 0){
                                                        $query->where($field,$operator,$a);
                                                    }
                                                }else if (isset($arr[$field], $relate[$arr[$field]])) {
                                                    $relatedField = $arr[$field];
                                                    $relation = $relate[$relatedField];
                                                    $relatedField = strpos($arr[$field], '_') !== false ? explode('_', $arr[$field], 2)[1] : $arr[$field];
                                                    // Apply condition to the related model
                                                    $query->whereHas($relation, function ($relatedQuery) use ($relatedField, $operator, $value) {
                                                        $relatedQuery->where($relatedField, $operator, $value);
                                                    });
                                                }else{
                                                    $query->where($field, $operator, $value);
                                                }
                                            }else{
                                                $customData = CustomField::getData($lead, 'lead');
                                                if($customData->isNotEmpty()){
                                                    $query->whereHas('customFieldValues', function ($query2) use($value) {
                                                        $query2->where('value',  $value);
                                                    })->whereHas('customFieldValues.customField', function ($query1) use ($operator, $field) {
                                                        $query1->where('module', 'lead')
                                                            ->where('name', $operator , $field);
                                                    });
                                                }
                                            }
                                            $result = $query->first();
                                            if (!empty($result)) {
                                                if ($conditionGroup['action'] === 'send_email') {
                                                    $us_mail = 'true';
                                                } elseif ($conditionGroup['action'] === 'send_notification') {
                                                    $us_notify = 'true';
                                                }
                                                elseif ($conditionGroup['action'] === 'send_approval') {
                                                    $us_approve = 'true';
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                            if($us_mail == 'true'){
                                // email send
                            }
                            if($us_notify == 'true' || $us_approve == 'true'){
                                // notification generate
                                if(count($usr_Lead) > 0){
                                    $usr_Lead[] = Auth::user()->creatorId();
                                    foreach($usr_Lead as $usrLead)
                                    {
                                        $data = [
                                            "updated_by" => Auth::user()->id,
                                            "data_id" => $lead->id,
                                            "name" => $lead->name,
                                        ];
                                        if($us_notify == 'true'){
                                            Utility::makeNotification($usrLead,'assign_lead',$data,$lead->id,'create Lead');
                                        }
                                        if($us_approve == 'true'){
                                            Utility::makeNotification($usrLead,'assign_lead',$data,$lead->id,'For Approval Lead');
                                        }
                                    }
                                }
                            } else {
                                foreach ($usrLeads as $usrLead) {
                                    $data = [
                                        "updated_by" => Auth::user()->id,
                                        "data_id" => $lead->id,
                                        "name" => $lead->name,
                                    ];
                                    if ($us_notify == 'true') {
                                        Utility::makeNotification($usrLead, 'assign_lead', $data, $lead->id, 'Assign Lead');
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($usr_Lead) > 0) {
                    $usr_Lead[] = Auth::user()->creatorId();
                    foreach ($usr_Lead as $usr_Leads) {
                        UserLead::create(
                            [
                                'user_id' => $usr_Leads,
                                'lead_id' => $lead->id,
                            ]
                        );
                    }
                } else {
                    foreach ($usrLeads as $usrLead) {
                        UserLead::create(
                            [
                                'user_id' => $usrLead,
                                'lead_id' => $lead->id,
                            ]
                        );
                    }
                }

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];
                $lArr = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $pipeline->name,
                    'lead_stage' => $stage->name,
                ];

                $usrEmail = User::find($request->user_id);

                $lArr = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $pipeline->name,
                    'lead_stage' => $stage->name,
                ];

                // Send Email
                $setings = Utility::settings();
                if ($setings['lead_assigned'] == 1) {
                    $usrEmail = User::find($request->user_id);
                    $leadAssignArr = [
                        'lead_name' => $lead->name,
                        'lead_email' => $lead->email,
                        'lead_subject' => $lead->subject,
                        'lead_pipeline' => $pipeline->name,
                        'lead_stage' => $stage->name,
                    ];
                    $resp = Utility::sendEmailTemplate('lead_assigned', [$usrEmail->id => $usrEmail->email], $leadAssignArr);
                }

                //For Notification
                $setting = Utility::settings(\Auth::user()->creatorId());
                $leadArr = [
                    'user_name' => \Auth::user()->name,
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                ];
                //Slack Notification
                if (isset($setting['lead_notification']) && $setting['lead_notification'] == 1) {
                    Utility::send_slack_msg('new_lead', $leadArr);
                }

                //Telegram Notification
                if (isset($setting['telegram_lead_notification']) && $setting['telegram_lead_notification'] == 1) {
                    Utility::send_telegram_msg('new_lead', $leadArr);
                }

                //webhook
                $module = 'New Lead';
                $webhook = Utility::webhookSetting($module);
                if ($webhook) {
                    $parameter = json_encode($lead);
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                    if($status == true)
                    {
                        Utility::makeActivityLog(\Auth::user()->id,'Lead',$lead->id,'Create Lead',$lead->name);
                        \DB::commit();
                        \Log::info('=== LEAD STORE COMPLETED - SUCCESS (webhook) ===');
                        return redirect()->back()->with('success', __('Lead successfully created!'));
                    }
                    else
                    {
                        \DB::commit();
                        \Log::info('=== LEAD STORE COMPLETED - WEBHOOK FAILED ===');
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }
                Utility::makeActivityLog(\Auth::user()->id,'Lead',$lead->id,'Create Lead',$lead->name);
                \DB::commit();
                \Log::info('=== LEAD STORE COMPLETED - SUCCESS (no webhook) ===');
                return redirect()->back()->with('success', __('Lead successfully created!'));

            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        }catch (\Exception $e) {
            \Log::error('=== LEAD STORE EXCEPTION ===');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            \DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        if ($lead->is_active) {
            $calenderTasks = [];
            $deal = Deal::where('id', '=', $lead->is_converted)->first();
            $pipelineModel = Pipeline::find($lead->pipeline_id);
            if ($pipelineModel && $pipelineModel->is_global) {
                $stageCnt = LeadStage::where('pipeline_id', '=', $lead->pipeline_id)->orderBy('order')->get();
            } else {
                $stageCnt = LeadStage::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', '=', $lead->created_by)->orderBy('order')->get();
            }
            $i = 0;
            foreach ($stageCnt as $stage) {
                $i++;
                if ($stage->id == $lead->stage_id) {
                    break;
                }
            }
            $precentage = count($stageCnt) > 0 ? number_format(($i * 100) / count($stageCnt)) : 0;

            return view('leads.show', compact('lead', 'calenderTasks', 'deal', 'precentage'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        if (\Auth::user()->can('edit lead')) {
            if ($lead->created_by == \Auth::user()->creatorId()) {
                if (\Auth::user()->type == 'company') {
                    $pipelines = Pipeline::where(function($query) {
                        $query->where('created_by', '=', \Auth::user()->creatorId())
                            ->orWhere('is_global', '=', 1);
                    })->get()->pluck('name', 'id');
                    $sources = Source::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                    $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                } else {
                    $pipelines = Pipeline::where(function($query) {
                        $query->where('owned_by', '=', \Auth::user()->ownedId())
                            ->orWhere('is_global', '=', 1);
                    })->get()->pluck('name', 'id');
                    $sources = Source::where('owned_by', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                    $products = ProductService::where('owned_by', '=', \Auth::user()->ownedId())->get()->pluck('name', 'id');
                }
                $pipelines->prepend(__('Select Pipeline'), '');
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('type', '!=', 'branch')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $lead->sources = explode(',', $lead->sources);
                $lead->products = explode(',', $lead->products);
                $lead->customField = CustomField::getData($lead, 'lead');
                $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'lead')->get();

                return view('leads.edit', compact('lead', 'pipelines', 'sources', 'products', 'users', 'customFields'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        if (\Auth::user()->can('edit lead')) {
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'email' => 'required|email',
                        'subject' => 'nullable',
                        'pipeline_id' => 'nullable',
                        'user_id' => 'nullable',
                        'stage_id' => 'nullable',
                        'sources' => 'nullable',
                        'products' => 'nullable',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $lead->name = $request->name;
                $lead->email = $request->email;
                $lead->phone = $request->phone;
                $lead->subject = $request->subject;
                $lead->user_id = $request->user_id;
                $lead->pipeline_id = $request->pipeline_id;
                $lead->stage_id = $request->stage_id;
                $lead->sources = implode(",", array_filter((array) $request->input('sources', [])));
                $lead->products = implode(",", array_filter((array) $request->input('products', [])));
                $lead->notes = $request->notes;
                $lead->inbox_url = $request->inbox_url;
                $lead->team_members = $request->team_members;
                $lead->sr_no = $request->sr_no;
                $lead->update_value = $request->update_value;
                $lead->follow_up_1 = $request->follow_up_1;
                $lead->update_2_0 = $request->update_2_0;
                $lead->follow_up_2 = $request->follow_up_2;
                $lead->follow_up_3 = $request->follow_up_3;
                $lead->company_name = $request->company_name;
                $lead->sector = $request->sector;
                $lead->number_of_employees = $request->number_of_employees;
                $lead->revenue = $request->revenue;
                $lead->contact_person = $request->contact_person;
                $lead->region = $request->region;
                $lead->address = $request->address;
                $lead->save();
                CustomField::saveData($lead, $request->customField);
                Utility::makeActivityLog(\Auth::user()->id, 'Lead', $lead->id, 'Update Lead', $lead->name);
                return redirect()->back()->with('success', __('Lead successfully updated!'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        if (\Auth::user()->can('delete lead')) {
            if ($lead->created_by == \Auth::user()->creatorId()) {
                LeadDiscussion::where('lead_id', '=', $lead->id)->delete();
                LeadFile::where('lead_id', '=', $lead->id)->delete();
                UserLead::where('lead_id', '=', $lead->id)->delete();
                LeadActivityLog::where('lead_id', '=', $lead->id)->delete();
                $lead->delete();

                return redirect()->back()->with('success', __('Lead successfully deleted!'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function json(Request $request)
    {
        $lead_stages = new LeadStage();
        if ($request->pipeline_id && !empty($request->pipeline_id)) {


            $lead_stages = $lead_stages->where('pipeline_id', '=', $request->pipeline_id);
            $lead_stages = $lead_stages->get()->pluck('name', 'id');
        } else {
            $lead_stages = [];
        }

        return response()->json($lead_stages);
    }

    public function fileUpload($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {

                //storage limit
                $image_size = $request->file('file')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->lead_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();

                $file = LeadFile::create(
                    [
                        'lead_id' => $request->lead_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                if ($result == 1) {
                    $request->file->storeAs('lead_files', $file_path);
                    $return = [];
                    $return['is_success'] = true;
                    $return['download'] = route(
                        'leads.file.download',
                        [
                            $lead->id,
                            $file->id,
                        ]
                    );
                    $return['delete'] = route(
                        'leads.file.delete',
                        [
                            $lead->id,
                            $file->id,
                        ]
                    );
                } else {
                    $return = [];
                    $return['is_success'] = true;
                    $return['status'] = 1;
                    $return['success_msg'] = ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '');
                }

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Upload File',
                        'remark' => json_encode(['file_name' => $file_name]),
                    ]
                );

                return response()->json($return);
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function fileDownload($id, $file_id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $file = LeadFile::find($file_id);
                if ($file) {
                    $file_path = storage_path('lead_files/' . $file->file_path);
                    $filename = $file->file_name;

                    return \Response::download(
                        $file_path,
                        $filename,
                        [
                            'Content-Length: ' . filesize($file_path),
                        ]
                    );
                } else {
                    return redirect()->back()->with('error', __('File is not exist.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function fileDelete($id, $file_id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $file = LeadFile::find($file_id);
                if ($file) {

                    //storage limit
                    $file_path = 'lead_files/' . $file->file_path;
                    $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);

                    $path = storage_path('lead_files/' . $file->file_path);
                    if (file_exists($path)) {
                        \File::delete($path);
                    }
                    $file->delete();

                    return response()->json(['is_success' => true], 200);
                } else {
                    return response()->json(
                        [
                            'is_success' => false,
                            'error' => __('File is not exist.'),
                        ],
                        200
                    );
                }
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function noteStore($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $lead->notes = $request->notes;
                $lead->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Note successfully saved!'),
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function labels($id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $pipelineModel = Pipeline::find($lead->pipeline_id);
                $isGlobalPipeline = $pipelineModel && $pipelineModel->is_global;
                if ($isGlobalPipeline) {
                    $labels = Label::where('pipeline_id', '=', $lead->pipeline_id)->get();
                } elseif (\Auth::user()->type == 'company') {
                    $labels = Label::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', \Auth::user()->creatorId())->get();
                } else {
                    $labels = Label::where('pipeline_id', '=', $lead->pipeline_id)->where('owned_by', \Auth::user()->ownedId())->get();
                }
                $selected = $lead->labels();
                if ($selected) {
                    $selected = $selected->pluck('name', 'id')->toArray();
                } else {
                    $selected = [];
                }

                return view('leads.labels', compact('lead', 'labels', 'selected'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function labelStore($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $leads = Lead::find($id);
            if ($leads->created_by == \Auth::user()->creatorId()) {
                if ($request->labels) {
                    $leads->labels = implode(',', $request->labels);
                } else {
                    $leads->labels = $request->labels;
                }
                $leads->save();

                return redirect()->back()->with('success', __('Labels successfully updated!'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userEdit($id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);

            if ($lead->created_by == \Auth::user()->creatorId()) {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('type', '!=', 'branch')->whereNOTIn(
                    'id',
                    function ($q) use ($lead) {
                        $q->select('user_id')->from('user_leads')->where('lead_id', '=', $lead->id);
                    }
                )->get();


                $users = $users->pluck('name', 'id');

                return view('leads.users', compact('lead', 'users'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function userUpdate($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $usr = \Auth::user();
            $lead = Lead::find($id);

            if ($lead->created_by == $usr->creatorId()) {
                if (!empty($request->users)) {
                    $users = array_filter($request->users);
                    $leadArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                    ];

                    foreach ($users as $user) {
                        UserLead::create(
                            [
                                'lead_id' => $lead->id,
                                'user_id' => $user,
                            ]
                        );
                    }
                }

                if (!empty($users) && !empty($request->users)) {
                    return redirect()->back()->with('success', __('Users successfully updated!'));
                } else {
                    return redirect()->back()->with('error', __('Please Select Valid User!'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userDestroy($id, $user_id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                UserLead::where('lead_id', '=', $lead->id)->where('user_id', '=', $user_id)->delete();

                return redirect()->back()->with('success', __('User successfully deleted!'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productEdit($id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                if (\Auth::user()->type == 'company') {
                    $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->whereNOTIn('id', explode(',', $lead->products))->get()->pluck('name', 'id');
                } else {
                    $products = ProductService::where('owned_by', '=', \Auth::user()->ownedId())->whereNOTIn('id', explode(',', $lead->products))->get()->pluck('name', 'id');
                }


                return view('leads.products', compact('lead', 'products'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productUpdate($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $usr = \Auth::user();
            $lead = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if ($lead->created_by == \Auth::user()->creatorId()) {
                if (!empty($request->products)) {
                    $products = array_filter($request->products);
                    $old_products = explode(',', $lead->products);
                    $lead->products = implode(',', array_merge($old_products, $products));
                    $lead->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();

                    LeadActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'lead_id' => $lead->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                    ];

                }

                if (!empty($products) && !empty($request->products)) {
                    return redirect()->back()->with('success', __('Products successfully updated!'))->with('status', 'products');
                } else {
                    return redirect()->back()->with('error', __('Please Select Valid Product!'))->with('status', 'general');
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function productDestroy($id, $product_id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $products = explode(',', $lead->products);
                foreach ($products as $key => $product) {
                    if ($product_id == $product) {
                        unset($products[$key]);
                    }
                }
                $lead->products = implode(',', $products);
                $lead->save();

                return redirect()->back()->with('success', __('Products successfully deleted!'))->with('status', 'products');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function sourceEdit($id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $sources = Source::where('created_by', '=', \Auth::user()->creatorId())->get();

                $selected = $lead->sources();
                if ($selected) {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('leads.sources', compact('lead', 'sources', 'selected'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function sourceUpdate($id, Request $request)
    {
        if (\Auth::user()->can('edit lead')) {
            $usr = \Auth::user();
            $lead = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if ($lead->created_by == \Auth::user()->creatorId()) {
                if (!empty($request->sources) && count($request->sources) > 0) {
                    $lead->sources = implode(',', $request->sources);
                } else {
                    $lead->sources = "";
                }

                $lead->save();

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Sources successfully updated!'))->with('status', 'sources');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function sourceDestroy($id, $source_id)
    {
        if (\Auth::user()->can('edit lead')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $sources = explode(',', $lead->sources);
                foreach ($sources as $key => $source) {
                    if ($source_id == $source) {
                        unset($sources[$key]);
                    }
                }
                $lead->sources = implode(',', $sources);
                $lead->save();

                return redirect()->back()->with('success', __('Sources successfully deleted!'))->with('status', 'sources');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function discussionCreate($id)
    {
        $lead = Lead::find($id);
        if ($lead->created_by == \Auth::user()->creatorId()) {
            return view('leads.discussions', compact('lead'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function discussionStore($id, Request $request)
    {
        $usr = \Auth::user();
        $lead = Lead::find($id);
        $lead_users = $lead->users->pluck('id')->toArray();

        if ($lead->created_by == $usr->creatorId()) {
            $discussion = new LeadDiscussion();
            $discussion->comment = $request->comment;
            $discussion->lead_id = $lead->id;
            $discussion->created_by = $usr->id;
            $discussion->save();

            $leadArr = [
                'lead_id' => $lead->id,
                'name' => $lead->name,
                'updated_by' => $usr->id,
            ];

            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

    public function order(Request $request)
    {
        try {
            if (\Auth::user()->can('move lead')) {
                $usr = \Auth::user();
                $post = $request->all();
                $lead = $this->lead($post['lead_id']);
                $lead_users = $lead->users->pluck('email', 'id')->toArray();
                if ($lead->stage_id != $post['stage_id']) {
                    $usrLeads = array();
                    $newStage = LeadStage::find($post['stage_id']);
                    // WorkFlow get which is active
                    $us_mail = 'false';
                    $us_notify = 'false';
                    $us_approve = 'false';
                    $workflow = WorkFlow::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'crm')->where('status', 1)->first();
                    if ($workflow) {
                        $workflowaction = WorkFlowAction::where('workflow_id', $workflow->id)->where('status', 1)->where('level_id', 1)->get();
                        foreach ($workflowaction as $action) {
                            $useraction = json_decode($action->assigned_users);
                            if (strtolower($newStage->name . '-1') == $action->node_id) {
                                // Pick that stage user assign or change on lead
                                if (@$useraction != '') {
                                    $useraction = json_decode($useraction);
                                    foreach ($useraction as $anyaction) {
                                        // make new user array
                                        if ($anyaction->type == 'user') {
                                            $usrLeads[] = $anyaction->id;
                                        }
                                    }
                                }

                                //  if user assign on this stage then check for mail and notification conditions

                                $raw_json = trim($action->applied_conditions, '"');
                                $cleaned_json = stripslashes($raw_json);
                                $applied_conditions = json_decode($cleaned_json, true);

                                if (isset($applied_conditions['conditions']) && is_array($applied_conditions['conditions'])) {
                                    $arr1 = [
                                        'products' => 'App\Models\ProductService',
                                        'sources' => 'App\Models\Source',
                                        'labels' => 'App\Models\Label',
                                    ];
                                    $arr = [
                                        'pipeline' => 'pipeline_name',
                                    ];
                                    $relate = [
                                        'pipeline_name' => 'pipeline',
                                    ];
                                    foreach ($applied_conditions['conditions'] as $conditionGroup) {
                                        if (in_array($conditionGroup['action'], ['send_email', 'send_notification', 'send_approval'])) {
                                            $query = Lead::where('id', $post['lead_id']);
                                            foreach ($conditionGroup['conditions'] as $condition) {
                                                $field = $condition['field'];
                                                $operator = $condition['operator'];
                                                $value = $condition['value'];
                                                if(isset($condition['origin']) && $condition['origin'] == 'db'){
                                                    if (array_key_exists($field, $arr1)) {
                                                        $a =$arr1[$field]::where('name',$value)->pluck('id')->toArray();
                                                        if(isset($a) && count($a) > 0){
                                                            $query->where($field,$operator,$a);
                                                        }
                                                    }else if (isset($arr[$field], $relate[$arr[$field]])) {
                                                        $relatedField = $arr[$field];
                                                        $relation = $relate[$relatedField];
                                                        $relatedField = strpos($arr[$field], '_') !== false ? explode('_', $arr[$field], 2)[1] : $arr[$field];
                                                        // Apply condition to the related model
                                                        $query->whereHas($relation, function ($relatedQuery) use ($relatedField, $operator, $value) {
                                                            $relatedQuery->where($relatedField, $operator, $value);
                                                        });
                                                    }else{
                                                        $query->where($field, $operator, $value);
                                                    }
                                                }else{
                                                    $customData = CustomField::getData($lead, 'lead');
                                                    if($customData->isNotEmpty()){
                                                        $query->whereHas('customFieldValues', function ($query2) use($value) {
                                                            $query2->where('value',  $value);
                                                        })->whereHas('customFieldValues.customField', function ($query1) use ($operator, $field) {
                                                            $query1->where('module', 'lead')
                                                                ->where('name', $operator , $field);
                                                        });
                                                    }
                                                }
                                            }
                                            $result = $query->first();
                                            if (!empty($result)) {
                                                if ($conditionGroup['action'] === 'send_email') {
                                                    $us_mail = 'true';
                                                } elseif ($conditionGroup['action'] === 'send_notification') {
                                                    $us_notify = 'true';
                                                } elseif ($conditionGroup['action'] === 'send_approval') {
                                                    $us_approve = 'true';
                                                }
                                            }
                                        }

                                    }
                                }
                                if ($us_mail == 'true') {
                                    // email send
                                }
                                if ($us_notify == 'true' || $us_approve == 'true') {
                                    if (count($usrLeads) > 0) {
                                        foreach ($usrLeads as $usrLead) {
                                            $data = [
                                                "updated_by" => Auth::user()->id,
                                                "data_id" => $request->lead_id,
                                                "name" => $lead->name,
                                            ];

                                            if($us_notify == 'true'){
                                                Utility::makeNotification($usrLead,'assign_lead',$data,$request->lead_id,'Assign Lead');
                                            }
                                            if($us_approve == 'true'){
                                                Utility::makeNotification($usrLead,'approve_lead_stage',$data,$request->lead_id,'For Approval Lead Stage');
                                            }
                                        }
                                    } else {
                                        $userLeads = UserLead::where('lead_id', $request->lead_id)->pluck('user_id');
                                        $userIds = $userLeads->toArray();
                                        foreach ($userIds as $usr) {
                                            $data = [
                                                "updated_by" => Auth::user()->id,
                                                "data_id" => $request->lead_id,
                                                "name" => $lead->name,
                                            ];

                                            if ($us_notify == 'true') {
                                                Utility::makeNotification($usr, 'lead_stage_update', $data, $request->lead_id, 'Lead Stage Update');
                                            }
                                        }
                                    }
                                }
                                $output = json_decode($action->outputs);

                                $triger_mail = 'false';
                                $triger_notify = 'false';
                                $send_approval = 'false';
                                // For pick its Child nodes or connected nodes
                                if (isset($output->output_1->connections)) {
                                    foreach ($output->output_1->connections as $out) {
                                        //  if any of its child have trigger
                                        $rows = WorkFlowAction::where('workflow_id', $workflow->id)->where('status', 1)->where('node_actual_id', 'node-' . $out->node)->where('type', 'trigger')->get();
                                        if ($rows) {
                                            foreach ($rows as $row) {
                                                $rowdata = json_decode($row->assigned_users);
                                                if (@$rowdata != '') {
                                                    $rowdata = json_decode($rowdata);
                                                    foreach ($rowdata as $us_action) {
                                                        if ($us_action->type == 'user') {
                                                            $rowusrLeads[] = $us_action->id;
                                                        }
                                                    }
                                                }

                                                $triger_mail = 'true';
                                                $triger_notify = 'true';
                                                $send_approval = 'true';
                                                //  if user assign on this stage then check for mail and notification conditions

                                                // $raw_json = trim($row->applied_conditions, '"');
                                                // $cleaned_json = stripslashes($raw_json);
                                                // $applied_conditions = json_decode($cleaned_json, true);

                                                // if (isset($applied_conditions['conditions']) && is_array($applied_conditions['conditions'])) {
                                                //     $arr = [
                                                //         'products' => 'App\Models\ProductService',
                                                //         'sources' => 'App\Models\Source',
                                                //     ];
                                                //     foreach ($applied_conditions['conditions'] as $conditionGroup) {
                                                //         if (in_array($conditionGroup['action'], ['send_email', 'send_notification','send_approval'])) {
                                                //             $query = Lead::where('id',$post['lead_id']);
                                                //             foreach ($conditionGroup['conditions'] as $condition) {
                                                //                 $field = $condition['field'];
                                                //                 $operator = $condition['operator'];
                                                //                 $value = $condition['value'];
                                                //                 if (array_key_exists($field, $arr)) {
                                                //                     $a =$arr[$field]::where('name',$value)->pluck('id')->toArray();
                                                //                     if(isset($a) && count($a) > 0){
                                                //                         $query->where($field,$operator,$a);
                                                //                     }
                                                //                 }else{
                                                //                     $query->where($field, $operator, $value);
                                                //                 }
                                                //             }
                                                //             $result = $query->first();
                                                //             if (!empty($result)) {
                                                //                 if ($conditionGroup['action'] === 'send_email') {
                                                //                     $us_mail = 'true';
                                                //                 } elseif ($conditionGroup['action'] === 'send_notification') {
                                                //                     $us_notify = 'true';
                                                //                 }
                                                //                 elseif ($conditionGroup['action'] === 'send_approval') {
                                                //                     $us_approve = 'true';
                                                //                 }
                                                //             }
                                                //         }

                                                //     }
                                                // }

                                                if ($triger_mail == 'true') {
                                                    // email send
                                                }

                                                if ($triger_notify == 'true' || $send_approval == 'true') {
                                                    if ($row->node_id == 'create-deal') {
                                                        $type = 'create_deal';
                                                    } else if ($row->node_id == 'create-contract') {
                                                        $type = 'create_contract';
                                                    } else if ($row->node_id == 'create-invoice') {
                                                        $type = 'create_invoice';
                                                    } else {
                                                        $type = 'other';
                                                    }

                                                    // notification generate
                                                    if (count($rowusrLeads) > 0) {
                                                        $rowusrLeads[] = Auth::user()->creatorId();

                                                        foreach ($rowusrLeads as $usrLead) {
                                                            $data = [
                                                                "updated_by" => Auth::user()->id,
                                                                "data_id" => $request->lead_id,
                                                                "name" => $lead->name,
                                                            ];
                                                            if ($triger_notify == 'true') {
                                                                Utility::makeNotification($usrLead, $type, $data, $request->lead_id, $type);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                    if (count($usrLeads) > 0) {
                        $olduser = UserLead::where('lead_id', $request->lead_id)->delete();
                        $usrLeads[] = Auth::user()->created_by;
                        foreach ($usrLeads as $usrLead) {
                            UserLead::create(
                                [
                                    'user_id' => $usrLead,
                                    'lead_id' => $request->lead_id,
                                ]
                            );
                        }
                    }

                    LeadActivityLog::create(
                        [
                            'user_id' => \Auth::user()->id,
                            'lead_id' => $lead->id,
                            'log_type' => 'Move',
                            'remark' => json_encode(
                                [
                                    'title' => $lead->name,
                                    'old_status' => $lead->stage->name,
                                    'new_status' => $newStage->name,
                                ]
                            ),
                        ]
                    );

                    $leadArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                        'old_status' => $lead->stage->name,
                        'new_status' => $newStage->name,
                    ];

                    $lArr = [
                        'lead_name' => $lead->name,
                        'lead_email' => $lead->email,
                        'lead_pipeline' => $lead->pipeline->name,
                        'lead_stage' => $lead->stage->name,
                        'lead_old_stage' => $lead->stage->name,
                        'lead_new_stage' => $newStage->name,
                    ];

                    // Send Email
                    Utility::sendEmailTemplate('Move Lead', $lead_users, $lArr);
                }

                foreach ($post['order'] as $key => $item) {
                    $lead = $this->lead($item);
                    $lead->order = $key;
                    $lead->stage_id = $post['stage_id'];
                    $lead->save();
                }
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->back()->with('error', __('Something Wrong'));
        }
    }

    private static $leadData = NULL;

    public function lead($item)
    {
        if (self::$leadData == null) {
            $lead = Lead::find($item);

            self::$leadData = $lead;
        }
        return self::$leadData;
    }

    public function showConvertToDeal($id)
    {

        $lead = Lead::findOrFail($id);
        $exist_client = User::where('type', '=', 'client')->where('email', '=', $lead->email)->where('created_by', '=', \Auth::user()->creatorId())->first();
        $clients = User::where('type', '=', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();

        return view('leads.convert', compact('lead', 'exist_client', 'clients'));
    }

    public function convertToDeal($id, Request $request)
    {

        $lead = Lead::findOrFail($id);
        $usr = \Auth::user();

        if ($request->client_check == 'exist') {
            $validator = \Validator::make(
                $request->all(),
                [
                    'clients' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $client = User::where('type', '=', 'client')->where('email', '=', $request->clients)->where('created_by', '=', $usr->creatorId())->first();

            if (empty($client)) {
                return redirect()->back()->with('error', 'Client is not available now.');
            }
        } else {
            $validator = \Validator::make(
                $request->all(),
                [
                    'client_name' => 'required',
                    'client_email' => 'required|email|unique:users,email',
                    'client_password' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $role = Role::findByName('client');
            $client = User::create(
                [
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'password' => \Hash::make($request->client_password),
                    'type' => 'client',
                    'lang' => 'en',
                    'created_by' => $usr->creatorId(),
                ]
            );
            $client->assignRole($role);

            $cArr = [
                'email' => $request->client_email,
                'password' => $request->client_password,
            ];

            // Send Email to client if they are new created.
            Utility::sendEmailTemplate('New User', [$client->id => $client->email], $cArr);
        }

        // Create Deal
        $stage = Stage::where('pipeline_id', '=', $lead->pipeline_id)->first();
        if (empty($stage)) {
            return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
        }

        $deal = new Deal();
        $deal->name = $request->name;
        $deal->price = empty($request->price) ? 0 : $request->price;
        $deal->pipeline_id = $lead->pipeline_id;
        $deal->stage_id = $stage->id;
        if (!empty($request->is_transfer)) {
            $deal->sources = in_array('sources', $request->is_transfer) ? $lead->sources : '';
            $deal->products = in_array('products', $request->is_transfer) ? $lead->products : '';
            $deal->notes = in_array('notes', $request->is_transfer) ? $lead->notes : '';
        } else {
            $deal->sources = '';
            $deal->products = '';
            $deal->notes = '';
        }

        $deal->labels = $lead->labels;
        $deal->status = 'Active';
        $deal->created_by = $lead->created_by;
        $deal->save();
        // end create deal

        // Make entry in ClientDeal Table
        ClientDeal::create(
            [
                'deal_id' => $deal->id,
                'client_id' => $client->id,
            ]
        );
        // end

        $dealArr = [
            'deal_id' => $deal->id,
            'name' => $deal->name,
            'updated_by' => $usr->id,
        ];
        // Send Notification

        // Send Mail
        $pipeline = Pipeline::find($lead->pipeline_id);
        $dArr = [
            'deal_name' => $deal->name,
            'deal_pipeline' => $pipeline->name,
            'deal_stage' => $stage->name,
            'deal_status' => $deal->status,
            'deal_price' => $usr->priceFormat($deal->price),
        ];
        Utility::sendEmailTemplate('Assign Deal', [$client->id => $client->email], $dArr);

        // Make Entry in UserDeal Table
        $leadUsers = UserLead::where('lead_id', '=', $lead->id)->get();
        foreach ($leadUsers as $leadUser) {
            UserDeal::create(
                [
                    'user_id' => $leadUser->user_id,
                    'deal_id' => $deal->id,
                ]
            );
        }
        // end

        //Transfer Lead Discussion to Deal
        if (!empty($request->is_transfer)) {
            if (in_array('discussion', $request->is_transfer)) {
                $discussions = LeadDiscussion::where('lead_id', '=', $lead->id)->where('created_by', '=', $usr->creatorId())->get();
                if (!empty($discussions)) {
                    foreach ($discussions as $discussion) {
                        DealDiscussion::create(
                            [
                                'deal_id' => $deal->id,
                                'comment' => $discussion->comment,
                                'created_by' => $discussion->created_by,
                            ]
                        );
                    }
                }
            }
            // end Transfer Discussion

            // Transfer Lead Files to Deal
            if (in_array('files', $request->is_transfer)) {
                $files = LeadFile::where('lead_id', '=', $lead->id)->get();
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $location = base_path() . '/storage/lead_files/' . $file->file_path;
                        $new_location = base_path() . '/storage/deal_files/' . $file->file_path;
                        $copied = copy($location, $new_location);

                        if ($copied) {
                            DealFile::create(
                                [
                                    'deal_id' => $deal->id,
                                    'file_name' => $file->file_name,
                                    'file_path' => $file->file_path,
                                ]
                            );
                        }
                    }
                }
            }
            // end Transfer Files

            // Transfer Lead Calls to Deal
            if (in_array('calls', $request->is_transfer)) {
                $calls = LeadCall::where('lead_id', '=', $lead->id)->get();
                if (!empty($calls)) {
                    foreach ($calls as $call) {
                        DealCall::create(
                            [
                                'deal_id' => $deal->id,
                                'subject' => $call->subject,
                                'call_type' => $call->call_type,
                                'duration' => $call->duration,
                                'user_id' => $call->user_id,
                                'description' => $call->description,
                                'call_result' => $call->call_result,
                            ]
                        );
                    }
                }
            }
            //end

            // Transfer Lead Emails to Deal
            if (in_array('emails', $request->is_transfer)) {
                $emails = LeadEmail::where('lead_id', '=', $lead->id)->get();
                if (!empty($emails)) {
                    foreach ($emails as $email) {
                        DealEmail::create(
                            [
                                'deal_id' => $deal->id,
                                'to' => $email->to,
                                'subject' => $email->subject,
                                'description' => $email->description,
                            ]
                        );
                    }
                }
            }
        }
        // Update is_converted field as deal_id
        $lead->is_converted = $deal->id;
        $lead->save();

        //For Notification
        $setting = Utility::settings(\Auth::user()->creatorId());
        $leadUsers = Lead::where('id', '=', $lead->id)->first();
        $leadUserArr = [
            'lead_user_name' => $leadUsers->name,
            'lead_name' => $lead->name,
            'lead_email' => $lead->email,
        ];
        //Slack Notification
        if (isset($setting['leadtodeal_notification']) && $setting['leadtodeal_notification'] == 1) {
            Utility::send_slack_msg('lead_to_deal_conversion', $leadUserArr);
        }
        //Telegram Notification
        if (isset($setting['telegram_leadtodeal_notification']) && $setting['telegram_leadtodeal_notification'] == 1) {
            Utility::send_telegram_msg('lead_to_deal_conversion', $leadUserArr);
        }

        //webhook
        $module = 'Lead to Deal Conversion';
        $webhook = Utility::webhookSetting($module);
        if ($webhook) {
            $parameter = json_encode($lead);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            if ($status == true) {
                return redirect()->back()->with('success', __('Lead successfully converted!'));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.'));
            }
        }


        return redirect()->back()->with('success', __('Lead successfully converted'));
    }

    // Lead Calls
    public function callCreate($id)
    {
        if (\Auth::user()->can('create lead call')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('lead', 'users'));
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function callStore($id, Request $request)
    {
        if (\Auth::user()->can('create lead call')) {
            $usr = \Auth::user();
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'subject' => 'required',
                        'call_type' => 'required',
                        'user_id' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leadCall = LeadCall::create(
                    [
                        'lead_id' => $lead->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead call',
                        'remark' => json_encode(['title' => 'Create new Lead Call']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Call successfully created!'))->with('status', 'calls');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    public function callEdit($id, $call_id)
    {
        if (\Auth::user()->can('edit lead call')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $call = LeadCall::find($call_id);
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('call', 'lead', 'users'));
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function callUpdate($id, $call_id, Request $request)
    {
        if (\Auth::user()->can('edit lead call')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'subject' => 'required',
                        'call_type' => 'required',
                        'user_id' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $call = LeadCall::find($call_id);

                $call->update(
                    [
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                return redirect()->back()->with('success', __('Call successfully updated!'))->with('status', 'calls');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function callDestroy($id, $call_id)
    {
        if (\Auth::user()->can('delete lead call')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                $task = LeadCall::find($call_id);
                $task->delete();

                return redirect()->back()->with('success', __('Call successfully deleted!'))->with('status', 'calls');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    // Lead email
    public function emailCreate($id)
    {
        if (\Auth::user()->can('create lead email')) {
            $lead = Lead::find($id);
            if ($lead->created_by == \Auth::user()->creatorId()) {
                return view('leads.emails', compact('lead'));
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ],
                    401
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ],
                401
            );
        }
    }

    public function emailStore($id, Request $request)
    {

        if (\Auth::user()->can('create lead email')) {
            $lead = Lead::find($id);

            if ($lead->created_by == \Auth::user()->creatorId()) {
                $settings = Utility::settings();
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'to' => 'required|email',
                        'subject' => 'required',
                        'description' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $leadEmail = LeadEmail::create(
                    [
                        'lead_id' => $lead->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );

                $leadEmail =
                    [
                        'lead_name' => $lead->name,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ];


                try {
                    Mail::to($request->to)->send(new SendLeadEmail($leadEmail, $settings));
                } catch (\Exception $e) {

                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
                //

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead email',
                        'remark' => json_encode(['title' => 'Create new Deal Email']),
                    ]
                );

                return redirect()->back()->with('success', __('Email successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'emails');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
        }
    }

    public function export($id = null)
    {
        if ($id == null) {
            $pipeline = 'all';
        } else {
            $pipeline = $id;
        }
        $name = 'Lead_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeadExport($pipeline), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function importFile($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with('error', 'Select pipline first');
        }

        $stages = LeadStage::where('pipeline_id', $id)->orderBy('order')->get();
        $users = User::where('created_by', '=', \Auth::user()->creatorId())->whereNotIn('type', ['client', 'company', 'branch'])->where('id', '!=', \Auth::user()->id)->get();

        return view('leads.import', compact('id', 'stages', 'users'));
    }

    public function import(Request $request)
    {

        $rules = [
            'file' => 'required|mimes:csv,txt',
            'pipeline' => 'required',
            'stage_id' => 'required',
            'all_users' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $leads = (new LeadImport())->toArray(request()->file('file'))[0];

        $totalLead = count($leads) - 1;
        $errorArray = [];
        for ($i = 1; $i <= count($leads) - 1; $i++) {
            $lead = $leads[$i];

            $leadByEmail = Lead::where('email', $lead[1])->where('pipeline_id', $request->pipeline)->first();
            if (!empty($leadByEmail)) {
                $leadData = $leadByEmail;
            } else {
                $leadData = new Lead();
            }

            $leadData->name = $lead[0];
            $leadData->email = $lead[1];
            $leadData->phone = $lead[2];
            $leadData->subject = $lead[3];
            $leadData->user_id = Auth::user()->id;
            $leadData->date = date("Y-m-d");
            $leadData->pipeline_id = !empty($request->pipeline) ? $request->pipeline : 1;
            $leadData->stage_id = !empty($request->stage_id) ? $request->stage_id : 1;
            $leadData->created_by = \Auth::user()->creatorId();

            if (empty($leadData)) {
                $errorArray[] = $leadData;
            } else {
                $leadData->save();
                foreach ($request->all_users as $user) {
                    $userlead = UserLead::firstOrCreate(
                        ['user_id' => $user, 'lead_id' => $leadData->id]
                    );
                }
            }
        }

        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg'] = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg'] = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalLead . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function importExcelFile()
    {
        return view('leads.import_excel');
    }

    public function importExcel(Request $request)
    {
        \Log::info('=== Excel Import Started ===');
        \Log::info('User: ' . Auth::user()->id . ' (' . Auth::user()->email . ')');
        \Log::info('CreatorId: ' . Auth::user()->creatorId());

        $rules = [
            'file' => 'required|mimes:xlsx,xls',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Validation failed: ' . $validator->getMessageBag()->first());
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        try {
            set_time_limit(300); // Increase execution time to 5 minutes
            ini_set('memory_limit', '512M');

            \Log::info('File name: ' . $request->file('file')->getClientOriginalName());
            \Log::info('File size: ' . $request->file('file')->getSize() . ' bytes');

            $countBefore = Lead::where('pipeline_id', 59)->count();
            \Log::info('Leads count before import: ' . $countBefore);

            Excel::import(new \App\Imports\LeadsImport(), $request->file('file'));

            $countAfter = Lead::where('pipeline_id', 59)->count();
            $imported = $countAfter - $countBefore;
            \Log::info('Leads count after import: ' . $countAfter);
            \Log::info('Total imported: ' . $imported);
            \Log::info('=== Excel Import Completed ===');

            return redirect()->route('leads.import_excel_file')
                ->with('success', __('Successfully imported :count leads!', ['count' => $imported]));
        } catch (\Exception $e) {
            \Log::error('=== Excel Import Failed ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', __('Error importing leads: ') . $e->getMessage());
        }
    }

    public function stagemove($id)
    {
        $lead = Lead::find($id);
        $lead_stages = new LeadStage();
        if ($lead->pipeline_id && !empty($lead->pipeline_id)) {


            $lead_stages = $lead_stages->where('pipeline_id', '=', $lead->pipeline_id);
            $lead_stages = $lead_stages->get()->pluck('name', 'id');
        } else {
            $lead_stages = [];
        }
        return view('leads.movestage', compact('lead', 'lead_stages'));
    }
    public function updatestagemove(Request $request, $id)
    {
        $lead = Lead::find($id);
        $lead->stage_id = $request->stage_id;
        $lead->save();
        $newStage = LeadStage::find($request['stage_id']);
        LeadActivityLog::create(
            [
                'user_id' => \Auth::user()->id,
                'lead_id' => $lead->id,
                'log_type' => 'Move',
                'remark' => json_encode(
                    [
                        'title' => $lead->name,
                        'old_status' => $lead->stage->name,
                        'new_stage' => $newStage->name
                    ]
                ),
            ]
        );
        return redirect()->back()->with('success', __('Lead Moved successfully!'));
    }

    public function filterlead()
    {
        $rowIndex = 0;
        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'lead')->get();
        return view('leads.filter_lead', compact('customFields', 'rowIndex'));
    }
}
