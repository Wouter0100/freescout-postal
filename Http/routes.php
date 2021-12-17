<?php

Route::group(['prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\Postal\Http\Controllers'], function()
{
    Route::post('/postal/endpoint/{mailbox_id}', ['uses' => 'PostalController@endpoint'])->name('postal.endpoint.http');
});
