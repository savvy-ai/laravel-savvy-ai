<?php

use SavvyAI\Tests\TestClasses\DummyForTraining;

it('summarizes training data', function () {
    $input = <<<EOT
About this space

Stirling's

Stirling's, "family style home" is a separate living area in lower & upper area of home. Private entrance and parking. No indoor/outdoor pets, no smoking indoors, or $100 fine per day. Located on 8 acres, 365 ft lakeshore, fishing, water and jet skiing, kayaking, snowmobiling, paddle boat.

Indoor pool + hot tub. Restaurants located within 3 miles, major cities within 15-45 minutes. Ice fishing + snowmobile trails right outside your door. Golf courses are near. Fire pits, bring own wood.

The space

Private retreat from city life, which provides a creative and relaxing atmosphere for any musicians, bands, parties, group meetings, family reunions, or individuals. No confetti, silly string, sprays, water or regular ballon popping, etc. is allowed indoors, otherwise large fines for professional cleaning will be incurred. When outdoors, clean up after yourselves. No fireworks allowed due to dry conditions and heavily wooded areas. All cigarettes should be picked up and disposed of properly or $100 fine applies.
EOT;

    $output = [
        "About this space Stirling's Stirling's, \"family style home\" is a separate living area in lower & upper area of home. Private entrance and parking.",
        "No indoor/outdoor pets, no smoking indoors, or $100 fine per day. Located on 8 acres, 365 ft lakeshore, fishing, water and jet skiing, kayaking, snowmobiling, paddle boat.",
        "Indoor pool + hot tub. Restaurants located within 3 miles, major cities within 15-45 minutes. Ice fishing + snowmobile trails right outside your door.",
        "Golf courses are near. Fire pits, bring own wood. The space Private retreat from city life, which provides a creative and relaxing atmosphere for any musicians, bands, parties, group meetings, family reunions, or individuals.",
        "No confetti, silly string, sprays, water or regular ballon popping, etc. is allowed indoors, otherwise large fines for professional cleaning will be incurred.",
        "When outdoors, clean up after yourselves. No fireworks allowed due to dry conditions and heavily wooded areas. All cigarettes should be picked up and disposed of properly or $100 fine applies."
    ];

    $sentencesArray = (new DummyForTraining())->summarizeForTraining($input, 128, 512);

    expect($sentencesArray)->toBe($output);
});
