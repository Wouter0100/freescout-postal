<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInProtocolOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $inProtocols = \App\Mailbox::getInProtocols();

        \App\Option::set('postal.incoming.http.id', array_key_last($inProtocols) + 1);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Option::where('name', 'postal.incoming.http.id')->delete();
    }
}
