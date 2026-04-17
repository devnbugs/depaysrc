<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    public function index(Request $request, Analytics $analytics)
    {
        $pageTitle = 'Analytics';

        $propertyId = (string) config('analytics.property_id');
        if (trim($propertyId) === '') {
            return view('admin.analytics.index', [
                'pageTitle' => $pageTitle,
                'configured' => false,
                'propertyId' => null,
                'periodDays' => 30,
                'visitorsAndPageViews' => collect(),
                'totalsByDate' => collect(),
                'topPages' => collect(),
                'topReferrers' => collect(),
                'topBrowsers' => collect(),
                'topCountries' => collect(),
                'topOperatingSystems' => collect(),
            ]);
        }

        $periodDays = max(1, (int) $request->integer('days', 30));
        $period = Period::days($periodDays);

        return view('admin.analytics.index', [
            'pageTitle' => $pageTitle,
            'configured' => true,
            'propertyId' => $propertyId,
            'periodDays' => $periodDays,
            'visitorsAndPageViews' => $analytics->fetchVisitorsAndPageViews($period, 10),
            'totalsByDate' => $analytics->fetchTotalVisitorsAndPageViews($period, 60),
            'topPages' => $analytics->fetchMostVisitedPages($period, 15),
            'topReferrers' => $analytics->fetchTopReferrers($period, 15),
            'topBrowsers' => $analytics->fetchTopBrowsers($period, 10),
            'topCountries' => $analytics->fetchTopCountries($period, 10),
            'topOperatingSystems' => $analytics->fetchTopOperatingSystems($period, 10),
        ]);
    }
}

