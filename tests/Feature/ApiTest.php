<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'teacher'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'device_name' => 'test'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ]);
    }

    public function test_teacher_can_view_timetable()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        Sanctum::actingAs($user, ['teacher']);

        $response = $this->getJson('/api/timetables');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'timetables' => [
                    '*' => [
                        'id',
                        'day',
                        'week',
                        'start_time',
                        'end_time',
                        'course',
                        'class',
                        'teacher'
                    ]
                ]
            ]);
    }

    public function test_teacher_can_view_attendance()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        Sanctum::actingAs($user, ['teacher']);

        $response = $this->getJson('/api/timetables/1/attendance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'timetable' => [
                    'id',
                    'date',
                    'start_time',
                    'end_time',
                    'course',
                    'class'
                ],
                'students' => [
                    '*' => [
                        'id',
                        'name',
                        'status',
                        'justification',
                        'justified'
                    ]
                ]
            ]);
    }

    public function test_teacher_can_view_stats()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        Sanctum::actingAs($user, ['teacher']);

        $response = $this->getJson('/api/stats/teacher');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_courses',
                'total_students_tracked',
                'absence_rate',
                'classes'
            ]);
    }

    public function test_unauthorized_access()
    {
        $response = $this->getJson('/api/timetables');
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'student']);
        Sanctum::actingAs($user, ['student']);

        $response = $this->getJson('/api/stats/teacher');
        $response->assertStatus(403);
    }
} 