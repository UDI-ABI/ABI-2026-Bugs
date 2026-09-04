<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchStaff\ResearchStaffVersion;
use App\Models\ResearchStaff\ResearchStaffProject;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Support\Facades\Hash;

/**
 * Test for VersionController.
 *
 * The version history is read-only: /api/versions only exposes index/show
 * (see routes/api.php). There is no store/update/destroy/restore anymore,
 * so a version can never be created, edited or deleted through this API.
 */
class VersionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $project;

    protected function setUp(): void
    {
        parent::setUp();
        // Note: This is a simplified setup. In production you'd need to create all related models
        $this->project = ResearchStaffProject::create([
            'title' => 'Proyecto Test',
            'delivery_date' => now(),
            'project_status_id' => 1,
            'thematic_area_id' => 1
        ]);
    }

    /** @test */
    public function test_can_list_versions_as_json()
    {
        $user = $this->createAuthUser();
        ResearchStaffVersion::create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('versions.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function test_can_filter_versions_by_project()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->getJson(route('versions.index', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_can_show_version()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('versions.show', $version));

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'project_id']);
    }

    /** @test */
    public function test_cannot_create_version_through_the_api()
    {
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->postJson('/api/versions', [
            'project_id' => $this->project->id,
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function test_cannot_update_version_through_the_api()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($user)->putJson("/api/versions/{$version->id}", [
            'project_id' => $this->project->id,
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function test_cannot_delete_version_through_the_api()
    {
        $user = $this->createAuthUser();
        $version = ResearchStaffVersion::create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/versions/{$version->id}");

        $response->assertStatus(405);
        $this->assertDatabaseHas('versions', ['id' => $version->id]);
    }

    private function createAuthUser(): ResearchStaffUser
    {
        return ResearchStaffUser::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
            'state' => 1,
        ]);
    }
}
