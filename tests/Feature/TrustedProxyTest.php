<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    public function test_forwarded_proto_header_is_trusted(): void
    {
        Route::get('/_test-scheme', fn (): string => request()->getScheme());

        $this->withHeader('X-Forwarded-Proto', 'https')
            ->get('/_test-scheme')
            ->assertOk()
            ->assertSee('https');
    }
}
