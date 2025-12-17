<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;


class StoreSettings extends Settings {

    public string $store_name;
    public string $store_logo;
    public string $store_address;
    public string $currency;
    public string $sell_margin;
    public ?string $bank_name;
    public ?string $account_name;
    public ?string $account_number;

    public static function group(): string
    {
        return 'store';
    }

}


