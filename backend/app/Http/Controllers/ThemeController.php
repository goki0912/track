<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{
    public function index(): JsonResponse
    {
        $themes = Theme::all();

        return response()->json(['themes' => $themes]);
    }
}
