<?php

namespace DD4You\Dpanel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GlobalSettingController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->can('global-settings'), 403, 'You don\'t have permission to access this.');

        return view('dpanel::global_setting');
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('global-settings'), 403, 'You don\'t have permission to access this.');

        foreach ($request->key as $key) {

            $setting = settings($key, false, false);

            if (in_array($setting['type'], ['text', 'longtext', 'array'])) {
                settings()->set(
                    $key,
                    [
                        'type' => $setting['type'],
                        'label' => $setting['label'],
                        'hint' => $setting['hint'],
                        'value' => $request->{"value-" . $key},
                    ]
                );
            } else {
                if ($request->{"value-" . $key}) {
                    if ($setting['value']) {
                        Storage::disk('public')->delete($setting['value']);
                    }
                    settings()->set(
                        $key,
                        [
                            'type' => $setting['type'],
                            'label' => $setting['label'],
                            'hint' => $setting['hint'],
                            'value' => $request->file("value-" . $key)->store('media', 'public'),
                        ]
                    );
                }
            }
        }


        return back()->withSuccess('Global Settings Updated Successfully.');
    }
}
