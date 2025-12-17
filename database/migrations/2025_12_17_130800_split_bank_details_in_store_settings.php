<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class SplitBankDetailsInStoreSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('store.bank_details');
        $this->migrator->add('store.bank_name', null);
        $this->migrator->add('store.account_name', null);
        $this->migrator->add('store.account_number', null);
    }

    public function down(): void
    {
        $this->migrator->delete('store.bank_name');
        $this->migrator->delete('store.account_name');
        $this->migrator->delete('store.account_number');
        $this->migrator->add('store.bank_details', null);
    }
}
