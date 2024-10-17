<?php

namespace Tests\Feature;

use App\Events\EmailProgressUpdated;
use App\Events\EmailSaveUpdated;
use App\Models\Campaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CampaignControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function it_can_list_campaigns()
    {
        $this->actingAs($user = User::factory()->create());

        $campaigns = Campaign::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get('/api/campaign');

        $response->assertSuccessful()
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    /** @test */
    public function it_can_store_a_campaign()
    {
        $this->actingAs($user = User::factory()->create());

        $csvFile = UploadedFile::fake()->create('test.csv', 100);

        $response = $this->post('/api/campaign', [
            'campaign_name' => 'Test Campaign',
            'csv_file' => $csvFile,
        ]);

        $response->assertSuccessful()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Test Campaign',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_validates_campaign_store_request()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->post('/api/campaign', [
            // Missing fields
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    /** @test */
    public function it_can_edit_a_campaign()
    {
        $this->actingAs($user = User::factory()->create());

        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $response = $this->get("/api/campaign/{$campaign->id}/edit");

        $response->assertSuccessful()
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_update_a_campaign()
    {
        $this->actingAs($user = User::factory()->create());

        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $response = $this->put("/api/campaign/{$campaign->id}", [
            'name' => 'Updated Campaign',
            'contant' => 'Updated content',
            'scheduled_at' => "2024-10-08 06:30:00",
        ]);

        $response->assertSuccessful()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Updated Campaign',
        ]);
    }

    /** @test */
    public function it_can_broadcast_save_count()
    {
        Event::fake();

        $this->actingAs($user = User::factory()->create());

        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $response = $this->post("/api/campaign/emails-count/{$campaign->id}");

        Event::assertDispatched(EmailSaveUpdated::class);
    }

    /** @test */
    public function it_can_broadcast_send_count()
    {
        Event::fake();

        $this->actingAs($user = User::factory()->create());

        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $response = $this->post("/api/campaign/send-count/{$campaign->id}");

        Event::assertDispatched(EmailProgressUpdated::class);
    }
}
