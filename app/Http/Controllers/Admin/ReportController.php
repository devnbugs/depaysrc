<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\Transaction;
use App\Models\Investment;
use App\Models\Power;
use App\Models\Deposit;
use App\Models\Network;
use App\Models\Bill;
use App\Models\User;
use App\Models\Internetbundle;
use App\Models\Cabletvbundle;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{

        public function airtime()
    {
        $pageTitle = 'Airtime Trx History';
        $bills = Bill::whereType(1)->get();
        return view('admin.reports.airtime', compact('pageTitle', 'bills'));
    }

        public function internet()
    {
        $pageTitle = 'Data Trx History';
        $bills = Bill::whereType(2)->get();
        return view('admin.reports.internet', compact('pageTitle', 'bills'));
    }


        public function cabletv()
    {
        $pageTitle = 'Cable TV Subscription';
        $bills = Bill::whereType(3)->get();
        return view('admin.reports.cabletv', compact('pageTitle', 'bills'));
    }


        public function utility()
    {
        $pageTitle = 'Utility Bills Payment';
        $bills = Bill::whereType(4)->get();
        return view('admin.reports.utility', compact('pageTitle', 'bills'));
    }



    public function waecreg()
    {
        $pageTitle = 'WAEC Regostration Token';
        $bills = Bill::whereType(5)->get();
        return view('admin.reports.waecreg', compact('pageTitle', 'bills'));
    }

    public function waecres()
    {
        $pageTitle = 'WAEC Result Checker';
        $bills = Bill::whereType(6)->get();
        return view('admin.reports.waecres', compact('pageTitle', 'bills'));
    }



    public function transaction()
    {
        $pageTitle = 'Transaction Logs';
        $transactions = Transaction::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions.';
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'emptyMessage'));
    }

    public function transactionSearch(Request $request)
    {
        $request->validate(['search' => 'required']);
        $search = $request->search;
        $pageTitle = 'Transactions Search - ' . $search;
        $emptyMessage = 'No transactions.';

        $transactions = Transaction::with('user')->whereHas('user', function ($user) use ($search) {
            $user->where('username', 'like',"%$search%");
        })->orWhere('trx', $search)->orderBy('id','desc')->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'emptyMessage','search'));
    }

    public function loginHistory(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'User Login History Search - ' . $search;
            $emptyMessage = 'No search result found.';
            $login_logs = UserLogin::whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            })->orderBy('id','desc')->with('user')->paginate(getPaginate());


        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


            return view('admin.reports.logins', compact('pageTitle', 'chart','emptyMessage', 'search', 'login_logs'));
        }

        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);

        $pageTitle = 'User Login History';
        $emptyMessage = 'No users login found.';
        $login_logs = UserLogin::orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle','chart', 'emptyMessage', 'login_logs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login By - ' . $ip;
        $login_logs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        $emptyMessage = 'No users login found.';
        return view('admin.reports.logins', compact('pageTitle', 'emptyMessage', 'login_logs','ip'));

    }

    public function emailHistory(){
        $pageTitle = 'Email history';
        $logs = EmailLog::with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.reports.email_history', compact('pageTitle', 'emptyMessage','logs'));
    }

    public function investLog(){
        $pageTitle = 'Investment Log';
        $investments = Investment::latest()->paginate(getPaginate());
        $emptyMessage = 'Data Not Found';
        return view('admin.investment', compact('pageTitle', 'investments', 'emptyMessage'));
    }


}
