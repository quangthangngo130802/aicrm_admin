<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZaloOa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    //
    public function fetchAllArticles()
    {
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        // $accessToken = 'CGaPMx5iDM8t2H9SicGrOZ0E5rkiC40mLHqV38yIKM04Apr5tI0TEmOfN5_v0IrZSmXWF_Ct7W003KHYW1GR1NahMrZuDmPaU5vh2F9xEKXvT1WN_avrKWK-E6FOAsGY7mK_UDaJ6nO12cHBnpq7Tq8gOHso821FQXS2CPvoTsz8GZm-aozCMsCY2M6c6aXOLWuLEO8_Qnnv0KbRh0yQP5KcQJFUCJ1_8ID1MjqG4JTMVKjLjrOf549xNot3OG9_EabGCRfiC0rIScvCgqmy4LvIIagXMWi7QdHSDufaEae1LKKXy686KoTsNZJnUJ1C3sTc0uvDDNC5Pcj8q0yH0JGKRJhS3q140HqZEk4dMrmP7Yiwd39U06i0U7-F7pO4KWnLNem0UH5j3nbuhn1f6NC3Ir9IibqK2BTwEs0';
        $offset = 0;
        $limit = 10;
        $allArticles = [];

        do {
            $response = Http::withHeaders([
                'access_token' => $accessToken,
            ])->get('https://openapi.zalo.me/v2.0/article/getslice', [
                'offset' => $offset,
                'limit' => $limit,
                'type' => 'normal',
            ]);

            if (!$response->successful()) {
                Log::error('API lỗi: ' . $response->body());
                break;
            }

            $data = $response->json();
            // dd($data);
            //  dd($data['data']['medias']);

            // ✅ LẤY CHÍNH XÁC danh sách bài viết từ data[0]
            $articles = $data['data']['medias'] ?? [];

            // Nếu không phải mảng => dừng
            if (!is_array($articles)) {
                break;
            }

            // Nếu rỗng => kết thúc
            if (count($articles) === 0) {
                break;
            }

            $allArticles = array_merge($allArticles, $articles);

            $offset += count($articles);
        } while (count($articles) === $limit);

        return response()->json([
            'total' => count($allArticles),
            'articles' => $allArticles,
        ]);
    }

    public function index() {
        $result = $this->fetchAllArticles();
        // dd($result);
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $data = $result->getData(true);
        $articles = $data['articles'];
        return view('admin.article.index', compact('articles', 'accessToken'));
        // dd($data['articles']);
    }

    public function broadcast(){
        $result = $this->fetchAllArticles();
        // dd($result);
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $data = $result->getData(true);
        $articles = $data['articles'];
        return view('admin.article.broadcast', compact('articles', 'accessToken'));
    }
}
