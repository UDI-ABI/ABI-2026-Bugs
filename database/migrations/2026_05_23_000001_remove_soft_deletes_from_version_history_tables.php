<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El historial de versiones (versions y content_version) debe ser
 * inmutable: nunca se edita ni se elimina, ni siquiera de forma logica.
 *
 * La columna deleted_at quedo agregada a estas dos tablas por error dentro
 * de una migracion pensada para tablas de catalogo
 * (2025_10_04_153117_add_soft_deletes_to_catalog_tables.php). Esta
 * migracion la retira solo de versions y content_version; las tablas de
 * catalogo reales (contents, content_frameworks, frameworks,
 * investigation_lines, programs, research_groups) conservan su soft delete.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('versions', 'deleted_at')) {
            Schema::table('versions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('content_version', 'deleted_at')) {
            Schema::table('content_version', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('versions', 'deleted_at')) {
            Schema::table('versions', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('content_version', 'deleted_at')) {
            Schema::table('content_version', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }
};
