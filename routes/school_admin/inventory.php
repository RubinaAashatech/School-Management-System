<?php

use App\Http\Controllers\SchoolAdmin\InventoriesController;

Route::resource('incomes', InventoriesController::class);
Route::post('incomes/get', [InventoriesController::class, 'getAllInventories'])->name('inventories.get');