<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveRequestAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'hr_manager', 'guard_name' => 'web']);
        Role::create(['name' => 'dept_head', 'guard_name' => 'web']);
        Role::create(['name' => 'mis_support', 'guard_name' => 'web']);
        Role::create(['name' => 'staff', 'guard_name' => 'web']);
    }

    public function test_staff_can_only_see_own_leave_requests(): void
    {
        // Create staff users
        $staff1 = User::factory()->create();
        $staff1->assignRole('staff');

        $staff2 = User::factory()->create();
        $staff2->assignRole('staff');

        // Create leave requests
        $ownLeave = LeaveRequest::factory()->create(['user_id' => $staff1->id]);
        $otherLeave = LeaveRequest::factory()->create(['user_id' => $staff2->id]);

        // Act as staff1
        $this->actingAs($staff1);

        // Query leave requests using the resource's eloquent query
        $visibleLeaves = \App\Filament\Resources\LeaveRequestResource::getEloquentQuery()->get();

        // Assert staff1 can only see their own leave request
        $this->assertCount(1, $visibleLeaves);
        $this->assertTrue($visibleLeaves->contains($ownLeave));
        $this->assertFalse($visibleLeaves->contains($otherLeave));
    }

    public function test_mis_support_can_only_see_own_leave_requests(): void
    {
        // Create MIS support users
        $mis1 = User::factory()->create();
        $mis1->assignRole('mis_support');

        $mis2 = User::factory()->create();
        $mis2->assignRole('mis_support');

        // Create leave requests
        $ownLeave = LeaveRequest::factory()->create(['user_id' => $mis1->id]);
        $otherLeave = LeaveRequest::factory()->create(['user_id' => $mis2->id]);

        // Act as mis1
        $this->actingAs($mis1);

        // Query leave requests
        $visibleLeaves = \App\Filament\Resources\LeaveRequestResource::getEloquentQuery()->get();

        // Assert mis1 can only see their own leave request
        $this->assertCount(1, $visibleLeaves);
        $this->assertTrue($visibleLeaves->contains($ownLeave));
        $this->assertFalse($visibleLeaves->contains($otherLeave));
    }

    public function test_hr_manager_can_see_all_leave_requests(): void
    {
        // Create HR manager
        $hrManager = User::factory()->create();
        $hrManager->assignRole('hr_manager');

        // Create staff users and their leave requests
        $staff1 = User::factory()->create();
        $staff2 = User::factory()->create();
        $leave1 = LeaveRequest::factory()->create(['user_id' => $staff1->id]);
        $leave2 = LeaveRequest::factory()->create(['user_id' => $staff2->id]);

        // Act as HR manager
        $this->actingAs($hrManager);

        // Query leave requests
        $visibleLeaves = \App\Filament\Resources\LeaveRequestResource::getEloquentQuery()->get();

        // Assert HR manager can see all leave requests
        $this->assertCount(2, $visibleLeaves);
        $this->assertTrue($visibleLeaves->contains($leave1));
        $this->assertTrue($visibleLeaves->contains($leave2));
    }

    public function test_dept_head_can_see_all_leave_requests(): void
    {
        // Create dept head
        $deptHead = User::factory()->create();
        $deptHead->assignRole('dept_head');

        // Create staff users and their leave requests
        $staff1 = User::factory()->create();
        $staff2 = User::factory()->create();
        $leave1 = LeaveRequest::factory()->create(['user_id' => $staff1->id]);
        $leave2 = LeaveRequest::factory()->create(['user_id' => $staff2->id]);

        // Act as dept head
        $this->actingAs($deptHead);

        // Query leave requests
        $visibleLeaves = \App\Filament\Resources\LeaveRequestResource::getEloquentQuery()->get();

        // Assert dept head can see all leave requests
        $this->assertCount(2, $visibleLeaves);
        $this->assertTrue($visibleLeaves->contains($leave1));
        $this->assertTrue($visibleLeaves->contains($leave2));
    }

    public function test_super_admin_can_see_all_leave_requests(): void
    {
        // Create super admin
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Create staff users and their leave requests
        $staff1 = User::factory()->create();
        $staff2 = User::factory()->create();
        $leave1 = LeaveRequest::factory()->create(['user_id' => $staff1->id]);
        $leave2 = LeaveRequest::factory()->create(['user_id' => $staff2->id]);

        // Act as super admin
        $this->actingAs($superAdmin);

        // Query leave requests
        $visibleLeaves = \App\Filament\Resources\LeaveRequestResource::getEloquentQuery()->get();

        // Assert super admin can see all leave requests
        $this->assertCount(2, $visibleLeaves);
        $this->assertTrue($visibleLeaves->contains($leave1));
        $this->assertTrue($visibleLeaves->contains($leave2));
    }
}
