<?php

namespace App\Http\Controllers;

use App\Models\ProjectUser;
use Carbon\Carbon;
use App\Models\Bug;
use App\Models\Task;
use App\Models\User;
use App\Models\Chair;
use App\Models\Space;
use App\Models\Booking;
use App\Models\Project;
use App\Models\Utility;
use App\Models\TaskFile;
use App\Models\BugStatus;
use App\Models\TaskStage;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\ProjectTask;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use App\Models\TaskChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{

    public function index()
    {
        // $projects = Booking::join('users', 'bookings.user_id', '=', 'users.id')
        // ->join('spaces', 'bookings.space_id', '=', 'spaces.id')
        // ->select('bookings.*', 'users.name as user_name', 'spaces.name as space_name')
        // ->get();
        if(\Auth::user()->type == ('company')){
            $bookings = Booking::with('company','user','space')->where('created_by', '=', \Auth::user()->creatorId())->get();
        }else{
            $bookings = Booking::with('company','user','space')->where('owned_by', '=', \Auth::user()->ownedId())->get();
        }
        return view('booking.index', compact('bookings'));
    }

    

    public function create($id = null)
    {
        if(\Auth::user()->type == ('company')){
            $comp= Company::where('created_by', '=', \Auth::user()->creatorId())->pluck('name', 'id');
        }else{
            $comp = Company::where('owned_by', '=', \Auth::user()->ownedId())->pluck('name', 'id');
        }
        if(\Auth::user()->type == ('clientuser')){
            $comp= Company::where('id',\Auth::user()->company_id)->pluck('name', 'id');
        }

        return view('booking.create',['space_id'=>$id,'comp'=>$comp]);     
    }
  
    public function bookingcreate($id = null)
    {
        if(\Auth::user()->type == ('company')){
            $comp= Company::where('created_by', '=', \Auth::user()->creatorId())->pluck('name', 'id');
        }else{
            $comp = Company::where('owned_by', '=', \Auth::user()->ownedId())->pluck('name', 'id');
        }
        if(\Auth::user()->type == ('clientuser')){
            $comp= Company::where('id',\Auth::user()->company_id)->pluck('name', 'id');
        }

        return view('booking.create',['space_id'=>$id,'comp'=>$comp]);
        
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'start_time' => 'required',
                'end_time' => 'required',
                'company' => 'required',
                'space_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $starttime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->start_time)));
        $endtime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->end_time)));

        if ($endtime < $starttime ) {
            return back()->with('error','End time should be greater than or equal to start time.');
        }
        if( $endtime->diffInMinutes($starttime) < '15'){
            return back()->with('error','Booking time must be greater then 15 mint.');
        }
        $overlappingBooking = Booking::where('space_id', $request->space_id)
        ->where(function ($query) use ($starttime, $endtime) {
            $query->where(function ($q) use ($starttime, $endtime) {
                $q->where('start_date', '>=', $starttime)
                    ->where('start_date', '<', $endtime);
            })
            ->orWhere(function ($q) use ($starttime, $endtime) {
                $q->where('end_date', '>', $starttime)  
                    ->where('end_date', '<=', $endtime);
            })
            ->orWhere(function ($q) use ($starttime, $endtime) {
                $q->where('start_date', '<', $starttime)
                    ->where('end_date', '>', $endtime);
            });
        })->first();

        if ($overlappingBooking) {
            return redirect()->back()->with('error', __('Already Booked.'));
        }
 
            $usr = Auth::user();
            $starttime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->start_time)));
            $endtime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->end_time)));
            $post = $request->all();
            $post['company_id'] = $request->company;
            $post['space_id'] = $request->space_id;
            $post['user_id'] = \Auth::user()->id;
            $post['created_by'] = \Auth::user()->creatorId();
            $post['owned_by'] = \Auth::user()->ownedId();
            $post['start_date'] = $starttime;
            $post['end_date'] = $endtime;
            $post['total_min'] = $endtime->diffInMinutes($starttime);
 
            $task = Booking::create($post);
            return redirect()->back()->with('success', __('Booking added successfully.'));
    }

    public function edit($id)
    {
        if(\Auth::user()->type == ('company')){
            $space = Space::where('meeting','yes')->where('created_by', '=', \Auth::user()->creatorId())->pluck('name', 'id');
        }else{
            $space = Space::where('meeting','yes')->where('owned_by', '=', \Auth::user()->ownedId())->pluck('name', 'id');
        }
        $booking = Booking::where('id', $id)->first();
        return view('booking.edit',['space'=>$space,'booking'=> $booking]);        
    }

    // Calendar View for Booking showing in the calendar
    public function update(Request $request, Booking $booking)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'start_time' => 'required',
                'end_time' => 'required',
                'space_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $starttime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->start_time)));
        $endtime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->end_time)));

        if ($endtime < $starttime ) {
            return back()->with('error','End time should be greater than or equal to start time.');
        }
        if( $endtime->diffInMinutes($starttime) < '15'){
            return back()->with('error','Booking time must be greater then 15 mint.');
        }
        $overlappingBooking = Booking::where('space_id', $request->space_id)->where('id','!=',$booking->id)
        ->where(function ($query) use ($starttime, $endtime) {
            $query->where(function ($q) use ($starttime, $endtime) {
                $q->where('start_date', '>=', $starttime)
                    ->where('start_date', '<', $endtime);
            })
            ->orWhere(function ($q) use ($starttime, $endtime) {
                $q->where('end_date', '>', $starttime)  
                    ->where('end_date', '<=', $endtime);
            })
            ->orWhere(function ($q) use ($starttime, $endtime) {
                $q->where('start_date', '<', $starttime)
                    ->where('end_date', '>', $endtime);
            });
        })->first();

        if ($overlappingBooking) {
            return redirect()->back()->with('error', __('Already Booked.'));
        }
            $starttime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->start_time)));
            $endtime = Carbon::parse(date("Y-m-d H:i:s", strtotime($request->end_time)));
            $booking->space_id = $request->space_id;
            $booking->start_date = $starttime;
            $booking->end_date = $endtime;
            $booking->total_min = $endtime->diffInMinutes($starttime);
            $booking->save();
            return redirect()->route('booking.index')->with('success', __('Booking Updated successfully.'));
    }
    // Calendar View for Booking showing in the calendar
    public function calendarView($task_by)
    {
        if (\Auth::user()->type == 'company') {
            $spaces = Space::where('created_by', '=', \Auth::user()->creatorId())->where('meeting','yes')->get();
        } else{
            $spaces = Space::where('owned_by', '=', \Auth::user()->ownedId())->where('meeting','yes')->get();
        }
        return view('booking.calendar', compact('spaces'));
    }

     //for Google Calendar second auto hit
     public function get_booking_data(Request $request)
     {
         $data = Booking::with('company','user')->where('space_id', $request->space_id)->get();
         $arrayJson = [];
         foreach ($data as $val) {
             $arrayJson[] = [
                 "id" => $val->id,
                 "title" => ($val->company ? $val->company->name : '') .'('. ($val->user ? $val->user->name : '').')',
                 "start" => date_format(Carbon::parse($val->start_date), "Y-m-d H:i:s"),
                 "end" => date_format(Carbon::parse($val->end_date), "Y-m-d H:i:s"),
                 "className" => 'event-primary',
                 "textColor" => '#51459d',
                 "allDay" => false,
                 "editable" => true, // Allow events to be dragged or resized
                 "eventResizableFromStart" => true, // Allow resizing from the start of the event
                 'url' => route('booking.calendar.show', $val->id),
                 'resize_url' => route('booking.calendar.drag', $val->id),
             ];
 
         }
 
         return $arrayJson;
     }
     
      // Calendar Show
      public function calendarShow($id)
      {
          $booking = Booking::find($id);
  
          return view('booking.show', compact('booking'));
      }

    // Calendar Drag
    public function calendarDrag(Request $request, $id)
    {
        // $task = ProjectTask::find($id);
        // $task->start_date = $request->start;
        // $task->end_date = $request->end;
        // $task->save();
        $task = Chair::find($id);
        $task->price = '100';
        // $task->end_date = $request->end;
        $task->save();
    }


}
