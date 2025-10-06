<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\CorrectionRequest;

class CorrectionRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_flow(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $att = Attendance::where('user_id',$user->id)->first();

        $payload = [
            'clock_in' => '09:30',
            'clock_out' => '18:15',
            'breaks' => [['in'=>'12:10','out'=>'12:50']],
            'note' => 'テスト修正',
        ];
        $this->post("/attendance/{$att->id}/request-correction", $payload)->assertRedirect();

        $req = CorrectionRequest::where('attendance_id',$att->id)->first();
        $this->assertNotNull($req);
        $this->assertEquals('pending',$req->status);

        $admin = Admin::first();
        $this->actingAs($admin,'admin');
        $this->post("/admin/requests/{$req->id}/approve")->assertRedirect();

        $req->refresh();
        $this->assertEquals('approved',$req->status);

        $att->refresh();
        $this->assertEquals('09:30', optional($att->clock_in)->format('H:i'));
        $this->assertEquals('18:15', optional($att->clock_out)->format('H:i'));
    }
}
