<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so we use raw SQL to recreate
        // For MySQL/PostgreSQL this would use Schema::table with change()
        // We add 'pending' and 'rejected' to the borrowings status
        DB::statement("CREATE TABLE IF NOT EXISTS borrowings_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            member_id INTEGER NOT NULL,
            book_id INTEGER NOT NULL,
            borrow_date DATE NOT NULL,
            due_date DATE NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        )");
        DB::statement("INSERT INTO borrowings_new SELECT * FROM borrowings");
        DB::statement("DROP TABLE borrowings");
        DB::statement("ALTER TABLE borrowings_new RENAME TO borrowings");
    }

    public function down(): void
    {
        // Revert not needed for dev
    }
};
