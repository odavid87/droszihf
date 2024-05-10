<?php

namespace Tests\Unit\Services;

use App\Exceptions\AbtestStartingException;
use App\Models\AbTest;
use App\Models\AbTestVariant;
use App\Models\Session;
use App\Services\AbTestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbTestServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AbTestService $abTestService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->abTestService = new AbTestService();
    }

    /** @test */
    public function it_can_start_an_abtest()
    {
        $abTest = AbTest::factory()->create(['status' => AbTest::STATUS_STOPPED]);
        $abTest->variants()->create(['name' => 'Variant A', 'targeting_ratio' => 50]);
        $abTest->variants()->create(['name' => 'Variant B', 'targeting_ratio' => 100]);

        $this->abTestService->start($abTest);

        $this->assertEquals(AbTest::STATUS_STARTED, $abTest->fresh()->status);
    }

    /** @test */
    public function it_cannot_start_an_already_started_abtest()
    {
        $abTest = AbTest::factory()->create(['status' => AbTest::STATUS_STARTED]);

        $this->expectException(AbtestStartingException::class);

        $this->abTestService->start($abTest);
    }

    /** @test */
    public function it_cannot_start_an_abtest_with_less_than_two_variants()
    {
        $abTest = AbTest::factory()->create(['status' => AbTest::STATUS_STOPPED]);
        // Assuming there's only one variant associated with the test
        $abTest->variants()->create(['name' => 'Variant A', 'targeting_ratio' => 50]);

        $this->expectException(AbtestStartingException::class);

        $this->abTestService->start($abTest);
    }

    /** @test */
    public function it_can_stop_an_abtest()
    {
        $abTest = AbTest::factory()->create(['status' => AbTest::STATUS_STARTED]);

        $this->abTestService->stop($abTest);

        $this->assertEquals(AbTest::STATUS_STOPPED, $abTest->fresh()->status);
    }

    /** @test */
    public function it_can_select_a_variant_for_an_abtest()
    {
        $abTest = AbTest::factory()->create();
        $variantA = AbTestVariant::factory()->create(['ab_test_id' => $abTest->id, 'targeting_ratio' => 50]);
        $variantB = AbTestVariant::factory()->create(['ab_test_id' => $abTest->id, 'targeting_ratio' => 50]);

        $selectedVariant = $this->abTestService->selectVariantByProbability($abTest);

        $this->assertTrue(in_array($selectedVariant->id, [$variantA->id, $variantB->id]));
    }

    /** @test */
    public function it_returns_a_variant_when_variants_have_different_targeting_ratios()
    {
        $abTest = AbTest::factory()->create();
        $variantA = AbTestVariant::factory()->create(['ab_test_id' => $abTest->id, 'targeting_ratio' => 100]);
        $variantB = AbTestVariant::factory()->create(['ab_test_id' => $abTest->id, 'targeting_ratio' => 0]);

        $selectedVariant = $this->abTestService->selectVariantByProbability($abTest);

        $this->assertEquals($variantA->id, $selectedVariant->id);
    }

    /** @test */
    public function it_can_define_abtest_variants_for_session()
    {
        // Create a running A/B test with variants
        $abTest1 = AbTest::factory()->state(['status' => AbTest::STATUS_STARTED])->create();
        $variant1 = $abTest1->variants()->create(['name' => 'Variant A', 'targeting_ratio' => 100]);
        $variant2 = $abTest1->variants()->create(['name' => 'Variant B', 'targeting_ratio' => 00]);

        // Create another running A/B test with variants
        $abTest2 = AbTest::factory()->state(['status' => AbTest::STATUS_STARTED])->create();
        $variant3 = $abTest2->variants()->create(['name' => 'Variant C', 'targeting_ratio' => 0]);
        $variant4 = $abTest2->variants()->create(['name' => 'Variant D', 'targeting_ratio' => 100]);

        $session = Session::factory()->create(); // this is being observed

        $session->refresh();

        $this->assertEquals([$variant1->id, $variant4->id], $session->abTestVariants()->pluck('id')->toArray());
    }
}
