<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\Feature\Support\CreatesTestData;

class NowFormatTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;


    public function now_is_rendered_in_ui_format()
    {
        $user = $this->makeUser();
        Carbon::setTestNow('2025-10-01 09:30:00');

        $res = $this->actingAs($user)->get('/attendance');
        $res->assertOk();

        $expected = now()->format('Y/m/d H:i');
        $res->assertSee($expected);
    }
}
