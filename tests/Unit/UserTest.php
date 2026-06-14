<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_getFullNameAttribute_returns_first_and_last_name(): void
    {
        $user = new User(['first_name' => 'Fatouma', 'last_name' => 'Soilihi', 'name' => 'fsoilihi']);

        $this->assertSame('Fatouma Soilihi', $user->full_name);
    }

    public function test_getFullNameAttribute_falls_back_to_name_when_first_and_last_are_empty(): void
    {
        $user = new User(['first_name' => '', 'last_name' => '', 'name' => 'fsoilihi']);

        $this->assertSame('fsoilihi', $user->full_name);
    }

    public function test_getFullNameAttribute_trims_when_only_first_name_set(): void
    {
        $user = new User(['first_name' => 'Hamid', 'last_name' => '', 'name' => 'hamid']);

        $this->assertSame('Hamid', $user->full_name);
    }
}