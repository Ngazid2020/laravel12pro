<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Évite les erreurs "SVG not found" pour les icônes Heroicon dans les tests Livewire
        config(['blade-icons.fallback' => '']);
    }
}
