<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentVersion;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ResearchGroup;
use App\Models\InvestigationLine;
use App\Models\ThematicArea;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The version/content-version history is read-only: /api/content-versions
 * only exposes index/show (see routes/api.php). Content values are written
 * internally by ProjectController/ProjectEvaluationController when a
 * version is created, never through this public API.
 */
class ContentVersionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_store_content_version_through_the_api(): void
    {
        [$content, $version] = $this->createContentAndVersion();

        $response = $this->postJson('/api/content-versions', [
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

        $contentVersion = ContentVersion::create([
            'content_id' => $content->id,
            'version_id' => $version->id,
            'value' => 'Valor diligenciado',
        ]);

        $this->getJson('/api/content-versions')
            ->assertStatus(200)
            ->assertJsonFragment(['value' => 'Valor diligenciado']);

        $this->getJson("/api/content-versions/{$contentVersion->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['value' => 'Valor diligenciado']);
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
