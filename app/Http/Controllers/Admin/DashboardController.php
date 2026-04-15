<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Internetbundle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $bundles = InternetBundle::all();
        $pageTitle = 'Admin Bundles';
        return view('admin.dashboard.index', compact('bundles','pageTitle'));
    }

    public function edit(InternetBundle $bundle)
    {
        $pageTitle = 'Edit Bundles';
        return view('admin.dashboard.edit', compact('bundle','pageTitle'));
    }

    public function update(Request $request, InternetBundle $bundle)
    {
        $bundle->update($request->all());
        return redirect()->route('admin.dashboard')->with('success', 'Data updated successfully!');
    }

    public function bundles()
    {
        $pageTitle = 'Edit Bundles';
        $bundles = InternetBundle::all();
        return view('admin.dashboard.bundles', compact('bundles','pageTitle'));
    }
}