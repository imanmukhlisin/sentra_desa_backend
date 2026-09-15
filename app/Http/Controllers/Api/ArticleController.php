<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['village.district.regency.province'])
            ->published()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at');

        if ($request->filled('village_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('village_id', $request->village_id)->orWhereNull('village_id');
            });
        }

        if ($request->filled('province_id')) {
            $query->whereHas('village.district.regency', fn ($q) =>
                $q->where('province_id', $request->province_id)
            );
        }

        if ($request->filled('regency_id')) {
            $query->whereHas('village.district', fn ($q) =>
                $q->where('regency_id', $request->regency_id)
            );
        }

        if ($request->filled('district_id')) {
            $query->whereHas('village', fn ($q) =>
                $q->where('district_id', $request->district_id)
            );
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate($request->input('per_page', 12));

        return response()->json([
            'status' => 'success',
            'data'   => $articles,
        ]);
    }

    public function show($slugOrId)
    {
        $query = Article::with(['village.district.regency.province'])->published();

        $article = is_numeric($slugOrId)
            ? $query->where('id', $slugOrId)->first()
            : $query->where('slug', $slugOrId)->first();

        if (!$article) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Artikel tidak ditemukan',
            ], 404);
        }

        $article->increment('view_count');

        return response()->json([
            'status' => 'success',
            'data'   => $article,
        ]);
    }
}
