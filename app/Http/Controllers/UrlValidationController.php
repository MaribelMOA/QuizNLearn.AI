<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlValidationController extends Controller
{
    public function validate(Request $request)
    {
        $url = $request->input('url');

        // Forbidden domains
        $forbidden = [
            "youtube.com", "youtu.be", "netflix.com", "spotify.com",
            "vimeo.com", "dailymotion.com", "soundcloud.com"
        ];
        // Validar que sea una URL válida
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'The URL format is invalid.'], 422);
        }

        $host = parse_url($url, PHP_URL_HOST);

        foreach ($forbidden as $domain) {
            if (Str::contains($host, $domain)) {
                return response()->json(['error' => 'This site is not allowed for quiz creation.'], 422);
            }
        }

        // Aquí puedes agregar más validaciones si quieres

        return response()->json(['message' => 'URL is valid.']);

    }
}
