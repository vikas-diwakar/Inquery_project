<?php

// Add to routes/web.php

Route::middleware(['auth'])->group(function () {
    // Follow-up routes
    Route::get('/follow-ups', [\App\Http\Controllers\FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/inquiries/{inquiry}/follow-ups', [\App\Http\Controllers\FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::post('/inquiries/{inquiry}/follow-ups/complete', [\App\Http\Controllers\FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::post('/follow-ups/bulk-schedule', [\App\Http\Controllers\FollowUpController::class, 'bulkSchedule'])->name('follow-ups.bulk-schedule');
    Route::get('/api/follow-ups/stats', [\App\Http\Controllers\FollowUpController::class, 'getStats'])->name('follow-ups.stats');
});
