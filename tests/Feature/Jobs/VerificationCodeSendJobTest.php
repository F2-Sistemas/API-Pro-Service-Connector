<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\VerificationCodeSendJob;
use App\Models\User;

class VerificationCodeSendJobTest extends TestCase
{
    /**
     * A basic feature test example.
     * @test
     */
    public function testVerificationCodeSendJobDispatched()
    {
        Queue::fake();

        // // Assert that a job was pushed...
        // Queue::assertPushed(
        //     VerificationCodeSendJob::class,
        //     1
        // );

        // $user = User::factory()->createOne();
        // $method = 'sms';

        // // Assert a job was pushed with specific properties...
        // Queue::assertPushed(
        //     fn (VerificationCodeSendJob $job) => ($job->user?->id === $user->id) && ($job->{$method} === $method)
        // );
    }
}
