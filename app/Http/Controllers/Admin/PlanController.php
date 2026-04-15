<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\Plan;
use App\Models\PlanTimer;

class PlanController extends Controller
{


    public function index(){
        $pageTitle = 'Investment Plans';
        $plan = Plan::latest()->paginate(getPaginate());
        $timer = PlanTimer::latest()->get();
        $emptyMessage = 'Data Not Found';
        return view('admin.plan.index', compact('pageTitle', 'plan','timer','emptyMessage'));
    }


    public function create(Request $request){

        $request->validate([
            'name'=> 'required|string|max:191',
            'usd'=> 'required|string|max:191',
            'min_amount'=> 'required|numeric|gt:0',
            'max_amount'=> 'required|numeric|gt:min_amount',
            'start'=> 'required|string|max:191',
            'total_return'=> 'required|integer|gt:0',
            'interest_type'=> 'required|in:0,1',
            'interest'=> 'required|numeric|gt:0',
            'status'=> 'required|in:0,1',
            'timer'=> 'required|int',
        ]);

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->timer = $request->timer;
        $plan->min_amount = $request->min_amount;
        $plan->start_at = $request->start;
        $plan->usd = $request->usd;
        $plan->max_amount = $request->max_amount;
        $plan->total_return = $request->total_return;
        $plan->interest_type = $request->interest_type;  //	1=>Percent, 0=>Fixed
        $plan->interest_amount = $request->interest;
        $plan->status = $request->status;
        $plan->save();

        $notify[] = ['success', 'Plan created successfully'];
        return redirect()->back()->withNotify($notify);

    }

    public function edit(Request $request){

        $request->validate([
            'id'=> 'required|exists:plans,id',
            'name'=> 'required|string|max:191',
            'usd'=> 'required|string|max:191',
            'start'=> 'required|string|max:191',
            'min_amount'=> 'required|numeric|gt:0',
            'max_amount'=> 'required|numeric|gt:min_amount',
            'total_return'=> 'required|integer|gt:0',
            'interest_type'=> 'required|in:0,1',
            'interest'=> 'required|numeric|gt:0',
            'status'=> 'required|in:0,1',
        ]);

        $findPlan = Plan::find($request->id);
        $findPlan->name = $request->name;
        $findPlan->min_amount = $request->min_amount;
        $findPlan->max_amount = $request->max_amount;
        $findPlan->usd = $request->usd;
        $findPlan->total_return = $request->total_return;
        $findPlan->start_at = $request->start;
        $findPlan->interest_type = $request->interest_type;  //	1=>Percent, 0=>Fixed
        $findPlan->interest_amount = $request->interest;
        $findPlan->status = $request->status;
        $findPlan->save();

        $notify[] = ['success', 'Plan updated successfully'];
        return redirect()->back()->withNotify($notify);
    }

     public function timer(){
        $pageTitle = 'Plan Timer';
        $timer = PlanTimer::latest()->paginate(getPaginate());
        $emptyMessage = 'Data Not Found';
        return view('admin.plan.timer', compact('pageTitle','timer','emptyMessage'));
    }

     public function timercreate(Request $request){

        $request->validate([
            'name'=> 'required|string|max:191',
            'slug'=> 'required|string|max:50',
            'timer'=> 'required|int',
        ]);

        $timer = new PlanTimer();
        $timer->name = $request->name;
        $timer->time = $request->timer;
        $timer->slug = $request->slug;
        $timer->save();

        $notify[] = ['success', 'Plan Timer created successfully'];
        return redirect()->back()->withNotify($notify);

    }

     public function timeredit(Request $request){

        $request->validate([
            'id'=> 'required|exists:plans,id',
            'name'=> 'required|string|max:191',
            'slug'=> 'required|string',
            'timer'=> 'required|numeric',
        ]);

        $timer = PlanTimer::find($request->id);
        $timer->name = $request->name;
        $timer->slug = $request->slug;
        $timer->time = $request->timer;
        $timer->save();

        $notify[] = ['success', 'Plan Timer updated successfully'];
        return redirect()->back()->withNotify($notify);
    }

    public function start( $id){

        $plan = Plan::find($id);

        $pool = Investment::wherePlanId($id)->get();
        foreach($pool as $data)
        {
        $data->status = 0;
        $data->save();
        }

        $notify[] = ['success', 'All Investment Pool under this plan has Started Successfully'];
        return redirect()->back()->withNotify($notify);
    }








}
