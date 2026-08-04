<?php

it('redirects home page to dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});
