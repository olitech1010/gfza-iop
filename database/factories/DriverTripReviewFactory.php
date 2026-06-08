<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverTripReview;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverTripReview>
 */
class DriverTripReviewFactory extends Factory
{
    protected $model = DriverTripReview::class;

    public function definition(): array
    {
        $isAdmin = $this->faker->boolean();

        return [
            'driver_id' => Driver::factory(),
            'vehicle_id' => Vehicle::factory(),
            'vehicle_requisition_id' => null,
            'audit_trip_id' => null,
            'reviewed_by' => User::factory(),
            'review_type' => $isAdmin ? 'admin' : 'passenger',
            'review_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'transmission_used' => $this->faker->randomElement(['manual', 'automatic']),
            // Admin ratings
            'vehicle_condition' => $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'cleanliness' => $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'fuel_efficiency' => $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'timeliness' => $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'rule_compliance' => $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            // Passenger ratings
            'punctuality' => ! $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'driving_quality' => ! $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'professionalism' => ! $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'safety_feeling' => ! $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            'overall_satisfaction' => ! $isAdmin ? $this->faker->numberBetween(1, 5) : null,
            // Qualitative
            'damage_severity' => $isAdmin ? $this->faker->randomElement(['none', 'minor', 'moderate', 'severe']) : null,
            'recommendation' => $isAdmin ? $this->faker->randomElement(['recommended', 'needs_training', 'restricted', 'not_recommended']) : null,
            'comments' => $this->faker->optional(0.5)->sentence(),
        ];
    }

    /**
     * Configure as an admin review.
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'review_type' => 'admin',
            'vehicle_condition' => $this->faker->numberBetween(1, 5),
            'cleanliness' => $this->faker->numberBetween(1, 5),
            'fuel_efficiency' => $this->faker->numberBetween(1, 5),
            'timeliness' => $this->faker->numberBetween(1, 5),
            'rule_compliance' => $this->faker->numberBetween(1, 5),
            'punctuality' => null,
            'driving_quality' => null,
            'professionalism' => null,
            'safety_feeling' => null,
            'overall_satisfaction' => null,
            'damage_severity' => $this->faker->randomElement(['none', 'minor', 'moderate', 'severe']),
            'recommendation' => $this->faker->randomElement(['recommended', 'needs_training', 'restricted', 'not_recommended']),
        ]);
    }

    /**
     * Configure as a passenger review.
     */
    public function passenger(): static
    {
        return $this->state(fn () => [
            'review_type' => 'passenger',
            'vehicle_condition' => null,
            'cleanliness' => null,
            'fuel_efficiency' => null,
            'timeliness' => null,
            'rule_compliance' => null,
            'punctuality' => $this->faker->numberBetween(1, 5),
            'driving_quality' => $this->faker->numberBetween(1, 5),
            'professionalism' => $this->faker->numberBetween(1, 5),
            'safety_feeling' => $this->faker->numberBetween(1, 5),
            'overall_satisfaction' => $this->faker->numberBetween(1, 5),
            'damage_severity' => null,
            'recommendation' => null,
        ]);
    }

    /**
     * Configure with excellent ratings.
     */
    public function excellent(): static
    {
        return $this->state(fn () => [
            'vehicle_condition' => 5,
            'cleanliness' => 5,
            'fuel_efficiency' => 5,
            'timeliness' => 5,
            'rule_compliance' => 5,
            'punctuality' => 5,
            'driving_quality' => 5,
            'professionalism' => 5,
            'safety_feeling' => 5,
            'overall_satisfaction' => 5,
        ]);
    }

    /**
     * Configure with poor ratings.
     */
    public function poor(): static
    {
        return $this->state(fn () => [
            'vehicle_condition' => $this->faker->numberBetween(1, 2),
            'cleanliness' => $this->faker->numberBetween(1, 2),
            'fuel_efficiency' => $this->faker->numberBetween(1, 2),
            'timeliness' => $this->faker->numberBetween(1, 2),
            'rule_compliance' => $this->faker->numberBetween(1, 2),
            'punctuality' => $this->faker->numberBetween(1, 2),
            'driving_quality' => $this->faker->numberBetween(1, 2),
            'professionalism' => $this->faker->numberBetween(1, 2),
            'safety_feeling' => $this->faker->numberBetween(1, 2),
            'overall_satisfaction' => $this->faker->numberBetween(1, 2),
        ]);
    }

    /**
     * Configure for manual transmission.
     */
    public function manual(): static
    {
        return $this->state(fn () => ['transmission_used' => 'manual']);
    }

    /**
     * Configure for automatic transmission.
     */
    public function automatic(): static
    {
        return $this->state(fn () => ['transmission_used' => 'automatic']);
    }
}
