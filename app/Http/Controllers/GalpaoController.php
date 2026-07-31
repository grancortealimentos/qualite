<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalpaoRequest;
use App\Services\GalpaoService;
use Illuminate\Http\RedirectResponse;

class GalpaoController extends Controller
{
    public function __construct(
        private readonly GalpaoService $galpaoService
    ) {}

    public function store(GalpaoRequest $request): RedirectResponse
    {
        try
        {

        }

    }
}