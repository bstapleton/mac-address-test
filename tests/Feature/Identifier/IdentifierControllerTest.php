<?php

namespace Tests\Feature\Identifier;

use App\Http\Controllers\IdentifierController;
use App\Models\Identifier;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\TestCase;

#[CoversClass(IdentifierController::class)]
#[UsesClass(Identifier::class)]
#[UsesClass(Organisation::class)]
class IdentifierControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index()
    {
        $this->get('/identifiers')
            ->assertStatus(200);
    }

    #[Test]
    public function get_not_found(): void
    {
        $input = '03-00-00-00-00-00';
        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(404);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => 'None found',
                'vendors' => [],
                'is_potentially_randomised' => false
            ]
        ]);
    }

    #[Test]
    public function get_not_found_potentially_obfuscated(): void
    {
        $input = '02-00-00-00-00-00';
        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(404);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => 'None found',
                'vendors' => [],
                'is_potentially_randomised' => true
            ]
        ]);
    }

    #[Test]
    public function get_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $input = '03-00-00-00-00-00';

        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(200);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => $identifier->assignment,
                'vendors' => [
                    $vendor->name
                ],
                'is_potentially_randomised' => false
            ]
        ]);
    }

    #[Test]
    public function get_potential_obfuscation_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '020000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $input = '02-00-00-00-00-00';

        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(200);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => $identifier->assignment,
                'vendors' => [
                    $vendor->name
                ],
                'is_potentially_randomised' => true
            ]
        ]);
    }

    #[Test]
    public function test_get_colon_separated_characters_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $input = '03:00:00:00:00:00';

        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(200);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => $identifier->assignment,
                'vendors' => [
                    $vendor->name
                ],
                'is_potentially_randomised' => false
            ]
        ]);
    }

    #[Test]
    public function test_get_dot_separated_characters_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $input = '03.00.00.00.00.00';

        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(200);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => $identifier->assignment,
                'vendors' => [
                    $vendor->name
                ],
                'is_potentially_randomised' => false
            ]
        ]);
    }

    #[Test]
    public function test_get_no_separated_characters_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $input = '030000000000';

        $request = $this->getJson('/api/identifier?mac_address=' . $input);

        $request->assertStatus(200);
        $request->assertJsonFragment([
            'data' => [
                'mac_address' => $input,
                'assignment' => $identifier->assignment,
                'vendors' => [
                    $vendor->name
                ],
                'is_potentially_randomised' => false
            ]
        ]);
    }

    #[Test]
    public function find_success(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $secondIdentifier = Identifier::create([
            'assignment' => '020000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);
        $secondIdentifier->organisations()->attach($vendor);
        $data = ['03-00-00-00-00-00','02-00-00-00-00-00'];

        $request = $this->postJson('/api/identifier', [
            'mac_addresses' => $data
        ]);

        $request->assertStatus(200);
        $response = json_decode($request->getContent(), true);
        $this->assertCount(2, $response['data']);
    }

    #[Test]
    public function find_not_found(): void
    {
        $data = ['03-00-00-00-00-00','02-00-00-00-00-00'];

        $request = $this->postJson('/api/identifier', [
            'mac_addresses' => $data
        ]);

        $request->assertStatus(404);
        $response = json_decode($request->getContent(), true);
        $this->assertCount(2, $response['data']);
    }

    #[Test]
    public function find_partial_not_found(): void
    {
        $identifier = Identifier::create([
            'assignment' => '030000',
        ]);

        $vendor = Organisation::factory()->create();
        $identifier->organisations()->attach($vendor);

        $data = ['03-00-00-00-00-00','02-00-00-00-00-00'];

        $request = $this->postJson('/api/identifier', [
            'mac_addresses' => $data
        ]);

        $request->assertStatus(200);
        $response = json_decode($request->getContent(), true);
        $this->assertCount(2, $response['data']);

        // Check that the one we did find is as expected
        $foundMatch = collect($response['data'])->where('mac_address', '03-00-00-00-00-00')->first();
        $this->assertTrue($foundMatch['assignment'] === '030000');
        $this->assertFalse($foundMatch['is_potentially_randomised']);
    }
}
