<?php

test('parse-snippet-attribute', function () {
    $regex              = '/([^\s=]+)="([^"]+)"/';
    $snippet            = '<MySnippet some-attribute="some-value">';
    $attributeWithValue = 'some-attribute="some-value"';
    $attributeOnly      = 'some-attribute';
    $valueOnly          = 'some-value';

    preg_match_all($regex, $snippet, $results, PREG_SET_ORDER);

    expect($results[0][0])->toBe($attributeWithValue);
    expect($results[0][1])->toBe($attributeOnly);
    expect($results[0][2])->toBe($valueOnly);
});