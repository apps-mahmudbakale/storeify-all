<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddBankDetailsToStoreSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('store.bank_details', null);
    }
}
