<?php

namespace Tests\Unit;

use App\Models\Identifier;
use App\Models\Organisation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(Identifier::class)]
class IdentifierTest extends TestCase
{
    use RefreshDatabase;

    protected Organisation $organisation;

    public function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::factory()->create();
    }

    #[Test]
    public function identifier_data_is_present(): void
    {
        $identifier = Identifier::create([
            'assignment' => '123456',
        ]);

        $identifier->organisations()->attach($this->organisation);

        // Confirm we created the thing
        $this->assertCount(1, Identifier::all());
        $this->assertEquals('123456', Identifier::first()->assignment);
    }

    #[Test]
    public function identifier_assignment_is_unique(): void
    {
        $assignment = '123456';

        $identifier = Identifier::create([
            'assignment' => $assignment,
        ]);

        $identifier->organisations()->attach($this->organisation);

        $this->expectException(QueryException::class);

        Identifier::create([
            'assignment' => $assignment,
        ]);

        // Confirm we only created one
        $this->assertCount(1, Identifier::all());
        $this->assertEquals(Identifier::first()->assignment, $assignment);
    }

    #[Test]
    public function identifier_assignment_missing_throws_an_exception(): void
    {
        $this->expectException(QueryException::class);

        Identifier::create([
            'organisation_id' => $this->organisation->id,
        ]);

        // Confirm it wasn't created
        $this->assertCount(0, Identifier::all());
    }

    #[Test]
    public function identifier_can_be_shared_between_organisations(): void
    {
        $identifier = Identifier::create([
            'assignment' => '123456',
        ]);

        $identifier->organisations()->attach($this->organisation);

        $newOrganisation = Organisation::factory()->create();

        $identifier->organisations()->attach($newOrganisation);

        $this->assertCount(2, $identifier->organisations);
    }
}
