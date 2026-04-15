<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Live Chat Setup';
        $extension = Extension::first();
        return view('admin.extension.index', compact('pageTitle', 'extension'));
    }

    public function update(Request $request, $id)
    {
        $extension = Extension::findOrFail($id);

        $request->validate([
            'app_key'=>'required',
            'status'=>'required|in:1,2'
        ]);

        $shortcode = json_decode(json_encode($extension->shortcode), true);

        foreach ($shortcode as $key => $code) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = $shortcode;
        $extension->status = $request->status;
        $extension->save();

        $notify[] = ['success', $extension->name . ' has been updated'];
        return redirect()->route('admin.extensions.index')->withNotify($notify);
    }

    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $extension = Extension::findOrFail($request->id);
        $extension->status = 1;
        $extension->save();
        $notify[] = ['success', $extension->name . ' has been activated'];
        return redirect()->route('admin.extensions.index')->withNotify($notify);
    }

    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $extension = Extension::findOrFail($request->id);
        $extension->status = 0;
        $extension->save();
        $notify[] = ['success', $extension->name . ' has been disabled'];
        return redirect()->route('admin.extensions.index')->withNotify($notify);
    }
}
