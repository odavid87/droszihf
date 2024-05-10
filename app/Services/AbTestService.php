<?php

namespace App\Services;

use App\Exceptions\AbtestStartingException;
use App\Models\AbTest;
use App\Models\Session;

class AbTestService
{
    public function start(AbTest $abTest)
    {
        if (!$abTest->status == AbTest::STATUS_STOPPED) {
            throw new AbtestStartingException("It was stopped.");
        }

        if ($abTest->variants->count() < 2) {
            throw new AbtestStartingException("Not enough variants.");
        }

        $abTest->status = AbTest::STATUS_STARTED;
        $abTest->save();
    }

    public function stop(AbTest $abTest)
    {
        $abTest->status = AbTest::STATUS_STOPPED;
        $abTest->save();
    }

    public function selectVariantByProbability(AbTest $abTest)
    {
        // Calculate total targeting ratio sum
        $totalRatio = $abTest->variants->sum('targeting_ratio');
        $variants = $abTest->variants;

        // Calculate the probability of each variant
        foreach ($variants as $variant) {
            $variant->probablility = $variant->targeting_ratio / $totalRatio;
        }

        // Generate a random number between 0 and 1
        $randomNumber = mt_rand() / mt_getrandmax();

        // Select a variant based on the random number and probabilities
        $cumulativeProbability = 0;
        foreach ($variants as $variant) {
            $cumulativeProbability += $variant->probablility;
            if ($randomNumber <= $cumulativeProbability) {
                return $variant;
            }
        }
    }

    public function defineAbtestVariantsForSession(Session $session)
    {
        $runningTests = AbTest::running()->get();

        // Loop through each running A/B test
        foreach ($runningTests as $abTest) {
            // Select a variant for the session based on probability
            $selectedVariant = $this->selectVariantByProbability($abTest);

            // Store the selected variant for the session
            $session->abTestVariants()->attach($selectedVariant);
        }
    }
}