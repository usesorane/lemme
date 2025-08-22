<?php

arch('livewire components should not access filesystem directly')
    ->expect('Sorane\\Lemme\\Livewire')
    ->not->toUse(['Illuminate\\Support\\Facades\\File', 'Illuminate\\Filesystem\\Filesystem']);
