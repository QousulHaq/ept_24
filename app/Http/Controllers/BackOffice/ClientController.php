<?php

namespace App\Http\Controllers\BackOffice;

use App\Entities\Passport\Client;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Middleware;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Prefix;

#[Prefix('client'), Name('client', dotSuffix: true), Middleware('can:client.manage')]
class ClientController extends Controller
{
    #[Get('', name: 'index')]
    public function index(): View
    {
        return view('pages.client.index');
    }

    #[Get('create', name: 'create')]
    public function create(): View
    {
        return view('pages.client.create');
    }

    #[Get('{client}/edit', name: 'edit')]
    public function edit(Client $client): View
    {
        
        return view('pages.client.edit', ['client' => $client]);
    }
}
