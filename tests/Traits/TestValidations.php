<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestValidations
{
    protected abstract function routeStore(): string;
    protected abstract function routeUpdate(): string;

    protected function assertInvalidationInStoreAction(
        array $data,
        string $fieldWithError,
        string $rule,
        array $ruleParams = []
    )
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationsFields($response, $fieldWithError, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $fieldWithError,
        string $rule,
        array $ruleParams = []
    )
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationsFields($response, $fieldWithError, $rule, $ruleParams);
    }

    protected function assertInvalidationsFields(
        TestResponse $response,
        string $fieldWithError,
        string $rule,
        array $ruleParams = []
    )
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([$fieldWithError]);

        $fieldName = str_replace('_', ' ', $fieldWithError);
        $response->assertJsonFragment([
            \Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
        ]);
    }
}
