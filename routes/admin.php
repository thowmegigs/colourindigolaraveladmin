
Route::resource('tickets',App\Http\Controllers\TicketController::class);
Route::post('tickets/view', [App\Http\Controllers\TicketController::class,'view'])->name('tickets.view');
