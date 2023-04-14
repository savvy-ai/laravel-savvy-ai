<?php

test('parse-snippet-name', function () {
    $regex               = '/<(\S+)(\s+[^>]+)?\s*\/?>/';
    $snippetName         = 'MySnippet';
    $snippetWithSpace    = sprintf('<%s />', $snippetName);
    $snippetWithoutSpace = sprintf('<%s/>', $snippetName);

    preg_match_all($regex, $snippetWithSpace, $withSpaceMatches, PREG_SET_ORDER);
    preg_match_all($regex, $snippetWithoutSpace, $withoutSpaceMatches, PREG_SET_ORDER);

    $withSpaceResult    = $withSpaceMatches[0][1];
    $withoutSpaceResult = rtrim($withoutSpaceMatches[0][1], '/');

    expect($withSpaceResult)->toBe($snippetName);
    expect($withoutSpaceResult)->toBe($snippetName);
});