<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings\StoreSettings;

class SettingsController extends Controller
{
    public function index(StoreSettings $settings)
    {
        $currencies = ['&#36;','&#8361;','&#8364;', '&#8377;', '&#8358;', '&#165;'];
        $audits = \OwenIt\Auditing\Models\Audit::with('user')
        ->orderBy('created_at', 'desc')
        ->get();
        return view('settings.index', compact('settings', 'currencies', 'audits'));
    }

    public function updateStoreSettings(Request $request, StoreSettings $settings)
    {
        $this->validate($request, [
            'store_name' => 'required',
            'store_address' => 'required',
            'store_logo' => 'nullable|file|image',
            'sell_margin' => 'required|numeric|min:0',

        ]);

        $logo = $settings->store_logo;
        if ($request->hasFile('store_logo')) {
            $logo = time() . '.' . $request->store_logo->extension();
            $request->file('store_logo')->storeAs('public/store', $logo);

        }

        $settings->store_name = $request->store_name;
        $settings->store_logo = $logo;
        $settings->store_address = $request->store_address;
        $settings->sell_margin = $request->sell_margin;
        $settings->save();
        return redirect()->route('app.settings.index')->with('Store Settings Has Been Updated');
    }

    public function updateStoreCurrency(Request $request, StoreSettings $settings)
    {
        $settings->currency = $request->store_currency;
        $settings->save();
        return redirect()->route('app.settings.index')->with('Store Settings Has Been Updated');
    }
}
