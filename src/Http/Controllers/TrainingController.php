<?php

namespace SavvyAI\Http\Controllers;

use SavvyAI\Models\Trainable;
use Illuminate\Http\Request;
use SavvyAI\Savvy;

class TrainingController extends Controller
{
    public function intake(Request $request, Trainable $trainable): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $text = $request->input('text');

        Savvy::train(
            $trainable,
            $text,
            $request->user()->uuid,
            [
                'trainable_id' => $trainable->id,
            ],
        );

        return redirect()->back();
    }
}
