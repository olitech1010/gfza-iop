<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_employee_directory()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('employees.index'));

        $response->assertStatus(200);
        $response->assertViewIs('employees.index');
    }

    public function test_unauthenticated_user_cannot_view_employee_directory()
    {
        $response = $this->get(route('employees.index'));

        $response->assertRedirect('/login');
    }

    public function test_can_search_employees()
    {
        $user = User::factory()->create();
        $employee1 = User::factory()->create(['name' => 'Alice Smith', 'is_active' => true]);
        $employee2 = User::factory()->create(['name' => 'Bob Jones', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('employees.index', ['search' => 'Alice']));

        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
        $response->assertDontSee('Bob Jones');
    }

    public function test_can_filter_by_department()
    {
        $user = User::factory()->create();
        $dept1 = Department::create(['name' => 'IT', 'description' => 'IT Dept']);
        $dept2 = Department::create(['name' => 'HR', 'description' => 'HR Dept']);

        $employee1 = User::factory()->create(['name' => 'Tech Guy', 'department_id' => $dept1->id, 'is_active' => true]);
        $employee2 = User::factory()->create(['name' => 'HR Lady', 'department_id' => $dept2->id, 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('employees.index', ['department_id' => $dept1->id]));

        $response->assertStatus(200);
        $response->assertSee('Tech Guy');
        $response->assertDontSee('HR Lady');
    }

    public function test_can_view_employee_profile()
    {
        $user = User::factory()->create();
        $employee = User::factory()->create(['name' => 'John Doe', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('employees.show', $employee));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertViewIs('employees.show');
    }
}
