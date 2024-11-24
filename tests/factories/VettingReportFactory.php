<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\VettingStatus;
use App\Models\VettingReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VettingReport> */
final class VettingReportFactory extends Factory
{
    protected $model = VettingReport::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'message' => fake()->countryCode(),
            'status' => VettingStatus::NEW,
            'vettable_type' => $this->vettableType(...),
            'vettable_id' => RegistrationFactory::new(),
            'vetting_step_id' => VettingStepFactory::new(),
        ];
    }

    /** @param array<string, string> $values */
    private function vettableType(array $values): string
    {
        $type = $values['vettable_id'];

        $modelName = $type->modelName();

        return (new $modelName())->getMorphClass();
    }
}
