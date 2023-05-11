<?php

namespace SavvyAI\Http\Controllers;

use SavvyAI\Models\Trainable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainableController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Trainables/Index', [
            'trainables' => Trainable::query()->withCount('statements')->get(),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Trainables/Create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'required|string|max:255|unique:domains,handle'
        ]);

        $trainable = Trainable::query()->create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'handle' => $request->handle,
        ]);

        return redirect()->route('trainables.show', $trainable);
    }

    public function show(Trainable $trainable): \Inertia\Response
    {
        return Inertia::render('Trainable/Show', compact('trainable'));
    }
}
