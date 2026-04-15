<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavingPay;
use App\Models\Savings;
use App\Models\User;

class SavingsController extends Controller
{


     public function target()
    {
        $pageTitle = 'Target Savings';
        $saved = Savings::where('type', 2)->paginate(10);
        $emptyMessage = "Data Not Found";
        return view('admin.savings.index', compact('pageTitle','saved','emptyMessage'));
    }

     public function recurrent()
    {
        $pageTitle = 'Recurrent Savings';
        $saved = Savings::where('type', 1)->paginate(10);
        $emptyMessage = "Data Not Found";
        return view('admin.savings.index', compact('pageTitle','saved','emptyMessage'));
    }

     public function view($id)
    {

        $saved = Savings::whereReference($id)->first();
         if (!$saved) {
            $notify[] = ['error', 'Invalid Savings.'];
            return back()->withNotify($notify);
        }


        $pageTitle = 'Savings Log';


        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $jan = '01';
        $feb = '02';
        $mar = '03';
        $apr = '04';
        $may = '05';
        $jun = '06';
        $jul = '07';
        $aug = '08';
        $sep = '09';
        $oct = '10';
        $nov = '11';
        $dec = '12';

        $data['jan'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jan)->sum('amount');
        $data['feb'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $feb)->sum('amount');
        $data['mar'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $mar)->sum('amount');
        $data['apr'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $apr)->sum('amount');
        $data['may'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $may)->sum('amount');
        $data['jun'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jun)->sum('amount');
        $data['jul'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $jul)->sum('amount');
        $data['aug'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $aug)->sum('amount');
        $data['sep'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $sep)->sum('amount');
        $data['oct'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $oct)->sum('amount');
        $data['nov'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $nov)->sum('amount');
        $data['dec'] = SavingPay::whereStatus(1)->whereYear('created_at', $year)->whereLoanId($id)->whereMonth('created_at', $dec)->sum('amount');


        $pay = SavingPay::whereLoanId($id)->get();
        $sum = SavingPay::whereLoanId($id)->sum('amount');
        $data['count'] = SavingPay::whereLoanId($id)->count();
        return view('admin.savings.view',$data, compact('pageTitle','saved','pay','sum'));
    }



}
