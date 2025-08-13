<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDnsFieldsToServersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('dns_name')->nullable()->after('name');
            $table->boolean('dns_configured')->default(false)->after('dns_name');
            $table->timestamp('dns_last_updated')->nullable()->after('dns_configured');
            
            // Add index for DNS lookups
            $table->index('dns_name');
            $table->index('dns_configured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropIndex(['dns_name']);
            $table->dropIndex(['dns_configured']);
            
            $table->dropColumn(['dns_name', 'dns_configured', 'dns_last_updated']);
        });
    }
}
