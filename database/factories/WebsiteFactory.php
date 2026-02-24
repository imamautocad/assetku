<?php

namespace Database\Factories;

use App\Models\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteFactory extends Factory
{
    protected $model = Website::class;

    public function definition(): array
    {
        $payDate = $this->faker->date();
        $expiredDate = date('Y-m-d', strtotime('+1 year', strtotime($payDate)));

        return [
            'category_id' => null,
            'company_id' => null,
            'desc' => $this->faker->sentence(),
            'user_website' => $this->faker->userName(),
            'subscribe_year' => 1,
            'website' => $this->faker->domainName(),
            'pay_date' => $payDate,
            'expired_date' => $expiredDate,
            'day_left' => rand(0, 365),
            'price' => $this->faker->randomFloat(2, 100000, 5000000),
        ];
    }
}
