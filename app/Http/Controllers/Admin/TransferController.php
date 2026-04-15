<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{

    public function user()
    {
        $pageTitle = 'User Fund Transfer';
        $emptyMessage = 'No Transfer Log.';
        $log = Transfer::whereMethod_id(1)->paginate(10);
        return view('admin.transfer.usertransfer', compact('pageTitle', 'emptyMessage', 'log'));
    }

  public function other()
    {
        $pageTitle = 'Other Bank Transfer';
        $emptyMessage = 'No Transfer Log.';
        $log = Transfer::whereMethod_id(2)->paginate(10);
        return view('admin.transfer.othertransfer', compact('pageTitle', 'emptyMessage', 'log'));
    }


    public function view($id)
    {
    $log = Transfer::whereTrx($id)->first();
    $pageTitle = 'Other Bank Transfer';
    $emptyMessage = 'No Transfer Log.';
         if (!$log)
           {
            $notify[] = ['error', 'Invalid Transfer.'];
            return back()->withNotify($notify);
            }
     return view('admin.transfer.view', compact('pageTitle', 'emptyMessage', 'log'));

    }

 public function approve(Request $request)
    {
     $this->validate($request, [
            'details' => 'required',
        ]);
    $log = Transfer::whereId($request->id)->whereStatus(0)->first();
    if (!$log)
           {
            $notify[] = ['error', 'Invalid Transfer Status.'];
            return back()->withNotify($notify);
            }
    $log->reason = $request->details;
    $log->status = 1;
    $log->save();
     $notify[] = ['success', 'Transfer Has Been Approved'];
     return back()->withNotify($notify);

    }


 public function reject(Request $request)
    {
     $this->validate($request, [
            'details' => 'required',
        ]);
     $log = Transfer::whereId($request->id)->whereStatus(0)->first();
     if (!$log)
           {
            $notify[] = ['error', 'Invalid Transfer Status.'];
            return back()->withNotify($notify);
            }
     $user = User::whereId($log->user_id)->first();
     $log->reason = $request->details;
     $log->status = 2;
     $log->save();

     if($user)
     {
     $total = $log->amount + $log->charge;
     $user->balance += $total;
     $user->save();
     }

     $notify[] = ['success', 'Transfer Has Been Reject'];
     return back()->withNotify($notify);

    }



}
