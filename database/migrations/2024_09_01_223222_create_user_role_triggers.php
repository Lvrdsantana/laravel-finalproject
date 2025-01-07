<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUserRoleTriggers extends Migration
{
    const ROLE_STUDENT = 'students';
    const ROLE_TEACHER = 'teachers';
    const ROLE_PARENT = 'parents';
    const ROLE_COORDINATOR = 'coordinators';

    public function up()
    {
        $roles = [
            self::ROLE_STUDENT,
            self::ROLE_TEACHER,
            self::ROLE_PARENT,
            self::ROLE_COORDINATOR,
        ];

        // Suppression des triggers existants avant de les recréer
        foreach ($roles as $role) {
            DB::unprepared("DROP TRIGGER IF EXISTS insert_{$role}");
        }
        DB::unprepared("DROP TRIGGER IF EXISTS update_roles");
        DB::unprepared("DROP TRIGGER IF EXISTS delete_roles");

        // Création des triggers pour l'insertion
        foreach ($roles as $role) {
            DB::unprepared("
                CREATE TRIGGER insert_{$role} AFTER INSERT ON users
                FOR EACH ROW
                BEGIN
                    INSERT INTO {$role} (user_id) VALUES (NEW.id);
                END;
            ");
        }

        // Création du trigger pour la mise à jour
        DB::unprepared("
            CREATE TRIGGER update_roles AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                -- Suppression de l'ancien rôle
                DELETE FROM students WHERE OLD.role = 'students' AND user_id = OLD.id;
                DELETE FROM teachers WHERE OLD.role = 'teachers' AND user_id = OLD.id;
                DELETE FROM parents WHERE OLD.role = 'parents' AND user_id = OLD.id;
                DELETE FROM coordinators WHERE OLD.role = 'coordinators' AND user_id = OLD.id;

                -- Insertion dans la nouvelle table
                INSERT INTO students (user_id) SELECT NEW.id WHERE NEW.role = 'students';
                INSERT INTO teachers (user_id) SELECT NEW.id WHERE NEW.role = 'teachers';
                INSERT INTO parents (user_id) SELECT NEW.id WHERE NEW.role = 'parents';
                INSERT INTO coordinators (user_id) SELECT NEW.id WHERE NEW.role = 'coordinators';
            END;
        ");

        // Création du trigger pour la suppression
        DB::unprepared("
            CREATE TRIGGER delete_roles AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                DELETE FROM students WHERE OLD.role = 'students' AND user_id = OLD.id;
                DELETE FROM teachers WHERE OLD.role = 'teachers' AND user_id = OLD.id;
                DELETE FROM parents WHERE OLD.role = 'parents' AND user_id = OLD.id;
                DELETE FROM coordinators WHERE OLD.role = 'coordinators' AND user_id = OLD.id;
            END;
        ");
    }

    public function down()
    {
        $roles = [
            self::ROLE_STUDENT,
            self::ROLE_TEACHER,
            self::ROLE_PARENT,
            self::ROLE_COORDINATOR,
        ];

        foreach ($roles as $role) {
            DB::unprepared("DROP TRIGGER IF EXISTS insert_{$role}");
        }

        DB::unprepared('DROP TRIGGER IF EXISTS update_roles');
        DB::unprepared('DROP TRIGGER IF EXISTS delete_roles');
    }
}
