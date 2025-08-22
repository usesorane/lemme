<?php

it('has precedence notice when both subdomain and route_prefix set', function () {
    config()->set('lemme.subdomain', 'docs');
    config()->set('lemme.route_prefix', 'docs');
    \Sorane\Lemme\Facades\Lemme::getPages();
    expect(config('lemme.route_prefix'))->toBe('docs');
});
