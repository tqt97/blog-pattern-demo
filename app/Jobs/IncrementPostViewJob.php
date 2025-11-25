<?php

namespace App\Jobs;

use App\Actions\Post\IncrementPostViewCountAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class IncrementPostViewJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $postId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(IncrementPostViewCountAction $action): void
    {
        $action($this->postId);
    }
}
