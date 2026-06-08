<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Driver;
use App\Models\DriverTripReview;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTripReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin review auto-computes overall_rating from admin rating fields.
     */
    public function test_admin_review_auto_computes_overall_rating(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        $review = DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'vehicle_condition' => 5,
            'cleanliness' => 4,
            'fuel_efficiency' => 3,
            'timeliness' => 4,
            'rule_compliance' => 4,
            'damage_severity' => 'none',
            'recommendation' => 'recommended',
        ]);

        // Average of 5, 4, 3, 4, 4 = 4.0
        $this->assertEquals(4.0, $review->overall_rating);
    }

    /**
     * Test that passenger review auto-computes overall_rating from passenger rating fields.
     */
    public function test_passenger_review_auto_computes_overall_rating(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        $review = DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'passenger',
            'review_date' => now(),
            'transmission_used' => 'automatic',
            'punctuality' => 5,
            'driving_quality' => 5,
            'professionalism' => 4,
            'safety_feeling' => 5,
            'overall_satisfaction' => 4,
        ]);

        // Average of 5, 5, 4, 5, 4 = 4.6
        $this->assertEquals(4.6, $review->overall_rating);
    }

    /**
     * Test that driver average_rating accessor computes correctly across reviews.
     */
    public function test_driver_average_rating_accessor(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        // Create admin review with avg 4.0
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'vehicle_condition' => 4,
            'cleanliness' => 4,
            'fuel_efficiency' => 4,
            'timeliness' => 4,
            'rule_compliance' => 4,
            'damage_severity' => 'none',
            'recommendation' => 'recommended',
        ]);

        // Create passenger review with avg 3.0
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'passenger',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'punctuality' => 3,
            'driving_quality' => 3,
            'professionalism' => 3,
            'safety_feeling' => 3,
            'overall_satisfaction' => 3,
        ]);

        $driver->refresh();
        // Average of 4.0 and 3.0 = 3.5
        $this->assertEquals(3.5, $driver->average_rating);
    }

    /**
     * Test transmission competency split.
     */
    public function test_driver_transmission_competency_ratings(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        // Manual review with 5s
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'vehicle_condition' => 5,
            'cleanliness' => 5,
            'fuel_efficiency' => 5,
            'timeliness' => 5,
            'rule_compliance' => 5,
            'damage_severity' => 'none',
            'recommendation' => 'recommended',
        ]);

        // Automatic review with 2s
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'automatic',
            'vehicle_condition' => 2,
            'cleanliness' => 2,
            'fuel_efficiency' => 2,
            'timeliness' => 2,
            'rule_compliance' => 2,
            'damage_severity' => 'minor',
            'recommendation' => 'needs_training',
        ]);

        $driver->refresh();
        $this->assertEquals(5.0, $driver->manual_rating);
        $this->assertEquals(2.0, $driver->automatic_rating);
    }

    /**
     * Test performance status based on average rating.
     */
    public function test_driver_performance_status(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        // No reviews = no_reviews
        $this->assertEquals('no_reviews', $driver->performance_status);

        // Create excellent review
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'vehicle_condition' => 5,
            'cleanliness' => 5,
            'fuel_efficiency' => 5,
            'timeliness' => 5,
            'rule_compliance' => 5,
            'damage_severity' => 'none',
            'recommendation' => 'recommended',
        ]);

        $driver->refresh();
        $this->assertEquals('excellent', $driver->performance_status);
    }

    /**
     * Test VehicleRequisition review tracking helpers.
     */
    public function test_requisition_review_helpers(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();
        $requester = User::factory()->create();
        $department = Department::create(['name' => 'Test Department']);

        $requisition = VehicleRequisition::create([
            'requester_id' => $requester->id,
            'department_id' => $department->id,
            'destination' => 'Test destination',
            'purpose' => 'Test purpose',
            'requested_date' => now(),
            'status' => 'completed',
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
        ]);

        // Initially needs review
        $this->assertTrue($requisition->needsReview());
        $this->assertFalse($requisition->hasAdminReview());
        $this->assertFalse($requisition->hasPassengerReview());

        // Submit admin review
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_requisition_id' => $requisition->id,
            'reviewed_by' => $reviewer->id,
            'review_type' => 'admin',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'vehicle_condition' => 4,
            'cleanliness' => 4,
            'fuel_efficiency' => 4,
            'timeliness' => 4,
            'rule_compliance' => 4,
            'damage_severity' => 'none',
            'recommendation' => 'recommended',
        ]);

        $this->assertTrue($requisition->hasAdminReview());
        $this->assertFalse($requisition->hasPassengerReview());
        $this->assertTrue($requisition->needsReview()); // Still needs passenger

        // Submit passenger review
        DriverTripReview::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_requisition_id' => $requisition->id,
            'reviewed_by' => $requester->id,
            'review_type' => 'passenger',
            'review_date' => now(),
            'transmission_used' => 'manual',
            'punctuality' => 5,
            'driving_quality' => 4,
            'professionalism' => 5,
            'safety_feeling' => 5,
            'overall_satisfaction' => 4,
        ]);

        $this->assertTrue($requisition->hasPassengerReview());
        $this->assertFalse($requisition->needsReview()); // Both done
    }

    /**
     * Test that the admin rating labels are defined for all admin rating fields.
     */
    public function test_admin_rating_labels_exist_for_all_fields(): void
    {
        foreach (DriverTripReview::ADMIN_RATINGS as $field) {
            $this->assertArrayHasKey($field, DriverTripReview::ADMIN_RATING_LABELS, "Missing label for admin rating field: {$field}");
            $this->assertNotEmpty(DriverTripReview::ADMIN_RATING_LABELS[$field]);
        }
    }

    /**
     * Test that the passenger rating labels are defined for all passenger rating fields.
     */
    public function test_passenger_rating_labels_exist_for_all_fields(): void
    {
        foreach (DriverTripReview::PASSENGER_RATINGS as $field) {
            $this->assertArrayHasKey($field, DriverTripReview::PASSENGER_RATING_LABELS, "Missing label for passenger rating field: {$field}");
            $this->assertNotEmpty(DriverTripReview::PASSENGER_RATING_LABELS[$field]);
        }
    }

    /**
     * Test scopes filter correctly.
     */
    public function test_review_scopes(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $reviewer = User::factory()->create();

        $base = [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'reviewed_by' => $reviewer->id,
            'review_date' => now(),
        ];

        // Admin manual
        DriverTripReview::create(array_merge($base, [
            'review_type' => 'admin',
            'transmission_used' => 'manual',
            'vehicle_condition' => 4, 'cleanliness' => 4, 'fuel_efficiency' => 4, 'timeliness' => 4, 'rule_compliance' => 4,
            'damage_severity' => 'none', 'recommendation' => 'recommended',
        ]));

        // Passenger automatic
        DriverTripReview::create(array_merge($base, [
            'review_type' => 'passenger',
            'transmission_used' => 'automatic',
            'punctuality' => 5, 'driving_quality' => 5, 'professionalism' => 5, 'safety_feeling' => 5, 'overall_satisfaction' => 5,
        ]));

        $this->assertEquals(1, DriverTripReview::admin()->count());
        $this->assertEquals(1, DriverTripReview::passenger()->count());
        $this->assertEquals(1, DriverTripReview::manual()->count());
        $this->assertEquals(1, DriverTripReview::automatic()->count());
        $this->assertEquals(2, DriverTripReview::forDriver($driver->id)->count());
    }
}
