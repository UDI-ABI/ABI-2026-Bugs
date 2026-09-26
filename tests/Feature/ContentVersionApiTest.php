<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentVersion;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ResearchGroup;
use App\Models\InvestigationLine;
use App\Models\ThematicArea;
use App\Models\User;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The version/content-version history is read-only and requires an
 * authenticated research_staff session: these routes live in
 * routes/web.php (under auth + role:research_staff) at
 * /api/content-versions, only exposing index/show. Content values are
 * written internally by ProjectController/ProjectEvaluationController
 * when a version is created, never through this API.
 */
class ContentVersionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_store_content_version_through_the_api(): void
    {
        [$content, $version] = $this->createContentAndVersion();
        $user = $this->createAuthUser();

        $response = $this->actingAs($user)->postJson('/api/content-versions', [
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor diligenciado',
        ]);

        $response->assertStatus(405);

        $this->assertDatabaseMissing('content_version', [
            'content_id' => $content->id,
            'version_id' => $version->id,
        ]);
    }

    public function test_can_list_and_show_content_versions(): void
    {
        [$content, $version] = $this->createContentAndVersion();
        $user = $this->createAuthUser();

        $contentVersion = ContentVersion::create([
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor diligenciado',
        ]);

        $this->actingAs($user)->getJson('/api/content-versions')
            ->assertStatus(200)
            ->assertJsonFragment(['value' => 'Valor diligenciado']);

        $this->actingAs($user)->getJson("/api/content-versions/{$contentVersion->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['value' => 'Valor diligenciado']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/content-versions');

        $response->assertStatus(401);
    }

    private function createAuthUser(): User
    {
        return User::create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'research_staff',
        ]);
    }

    private function createContentAndVersion(): array
    {
        $content = Content::create([
            'name' => 'Elemento evaluable',
            'description' => 'Contenido base para la versión.',
            'roles' => ['research_staff'],
        ]);

        $researchGroup = ResearchGroup::create([
            'name' => 'Grupo',
            'initials' => 'GR',
            'description' => 'Grupo de investigación',
        ]);

        $investigationLine = InvestigationLine::create([
            'name' => 'Línea',
            'description' => 'Línea base',
            'research_group_id' => $researchGroup->id,
        ]);

        $thematicArea = ThematicArea::create([
            'name' => 'Área',
            'description' => 'Área base',
            'investigation_line_id' => $investigationLine->id,
        ]);

        $projectStatus = new ProjectStatus();
        $projectStatus->name = 'Activo';
        $projectStatus->description = 'Estado activo';
        $projectStatus->save();

        $project = Project::create([
            'title' => 'Proyecto base',
            'evaluation_criteria' => 'Criterios',
            'thematic_area_id' => $thematicArea->id,
            'project_status_id' => $projectStatus->id,
        ]);

        $version = Version::create([
            'project_id' => $project->id,
        ]);

        return [$content, $version];
    }
}
