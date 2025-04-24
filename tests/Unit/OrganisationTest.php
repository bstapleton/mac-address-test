<?php

namespace Tests\Unit;

use App\Models\Identifier;
use App\Models\Organisation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\TestCase;

#[CoversClass(Organisation::class)]
#[UsesClass(Identifier::class)]
class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organisation_data_is_present(): void
    {
        Organisation::create([
            'name' => 'Acme',
            'address' => '123 Main St',
        ]);

        // Confirm we created the thing
        $this->assertCount(1, Organisation::all());
        $this->assertEquals('Acme', Organisation::first()->name);
        $this->assertEquals('123 Main St', Organisation::first()->address);
    }

    #[Test]
    public function organisation_name_is_unique(): void
    {
        $name = 'Acme';

        Organisation::create([
            'name' => $name,
            'address' => '123 Main St'
        ]);

        $this->expectException(QueryException::class);

        Organisation::create([
            'name' => $name,
            'address' => '999 Main St'
        ]);

        // Confirm we only created one, and the data matches the first
        $this->assertCount(1, Organisation::all());
        $this->assertEquals(Organisation::first()->name, $name);
        $this->assertEquals('123 Main St', Organisation::first()->address);
    }

    #[Test]
    public function organisation_name_missing_throws_an_exception(): void
    {
        $this->expectException(QueryException::class);

        Organisation::create([
            'address' => '123 Main St'
        ]);

        // Confirm it wasn't created
        $this->assertFalse(Organisation::where('address', '123 Main St')->exists());
    }

    #[Test]
    public function organisation_address_missing_throws_an_exception(): void
    {
        $this->expectException(QueryException::class);

        Organisation::create([
            'name' => 'Acme'
        ]);

        // Confirm it wasn't created
        $this->assertFalse(Organisation::where('name', 'Acme')->exists());
    }

    #[Test]
    public function organisation_can_have_multiple_identifiers(): void
    {
        $organisation = Organisation::create([
            'name' => 'Acme',
            'address' => '123 Main St',
        ]);

        $first = Identifier::create([
            'assignment' => '123456',
        ]);
        $organisation->identifiers()->attach($first);

        $second = Identifier::create([
            'assignment' => '789000',
        ]);
        $organisation->identifiers()->attach($second);

        $this->assertCount(2, $organisation->identifiers);
    }
}
