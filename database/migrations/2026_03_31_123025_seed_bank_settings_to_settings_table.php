<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $fields = ['bank_name', 'account_name', 'account_number'];

        foreach ($fields as $field) {
            $exists = DB::table('settings')
                ->where('group', 'store')
                ->where('name', $field)
                ->exists();

            if (!$exists) {
                DB::table('settings')->insert([
                    'group'      => 'store',
                    'name'       => $field,
                    'locked'     => false,
                    'payload'    => json_encode(null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        DB::table('settings')
            ->where('group', 'store')
            ->whereIn('name', ['bank_name', 'account_name', 'account_number'])
            ->delete();
    }
};
