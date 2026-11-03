<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsBlogController extends Controller
{
    /**
     * Public: Retrieve CMS Page by slug.
     */
    public function publicCms(string $slug): JsonResponse
    {
        $page = CmsPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return response()->json([
            'success' => true,
            'page'    => $page,
        ]);
    }

    /**
     * Public: List published blog posts.
     */
    public function publicBlogIndex(Request $request): JsonResponse
    {
        $posts = BlogPost::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'posts'   => $posts,
        ]);
    }

    /**
     * Public: Show single blog post by slug.
     */
    public function publicBlogShow(string $slug): JsonResponse
    {
        $post = BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return response()->json([
            'success' => true,
            'post'    => $post,
        ]);
    }

    /**
     * Admin: List CMS pages.
     */
    public function adminCmsIndex(): JsonResponse
    {
        $pages = CmsPage::latest()->get();
        return response()->json([
            'success' => true,
            'pages'   => $pages,
        ]);
    }

    /**
     * Admin: Store CMS page.
     */
    public function adminCmsStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:cms_pages,slug',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);

        $page = CmsPage::create([
            'title'        => $validated['title'],
            'slug'         => $validated['slug'] ?? Str::slug($validated['title']),
            'content'      => $validated['content'],
            'is_published' => $validated['is_published'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CMS page created.',
            'page'    => $page,
        ], 201);
    }

    /**
     * Admin: Update CMS page.
     */
    public function adminCmsUpdate(Request $request, int $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:cms_pages,slug,' . $id,
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
        ]);

        $page->update([
            'title'        => $validated['title'],
            'slug'         => $validated['slug'] ?? Str::slug($validated['title']),
            'content'      => $validated['content'],
            'is_published' => $validated['is_published'] ?? $page->is_published,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CMS page updated.',
            'page'    => $page,
        ]);
    }

    /**
     * Admin: Delete CMS page.
     */
    public function adminCmsDestroy(int $id): JsonResponse
    {
        $page = CmsPage::findOrFail($id);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'CMS page deleted.',
        ]);
    }

    /**
     * Admin: List blog posts.
     */
    public function adminBlogIndex(): JsonResponse
    {
        $posts = BlogPost::latest()->get();
        return response()->json([
            'success' => true,
            'posts'   => $posts,
        ]);
    }

    /**
     * Admin: Store blog post.
     */
    public function adminBlogStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:blog_posts,slug',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = BlogPost::create([
            'title'        => $validated['title'],
            'slug'         => $validated['slug'] ?? Str::slug($validated['title']),
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'is_published' => $validated['is_published'] ?? true,
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog post created.',
            'post'    => $post,
        ], 201);
    }

    /**
     * Admin: Update blog post.
     */
    public function adminBlogUpdate(Request $request, int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update([
            'title'        => $validated['title'],
            'slug'         => $validated['slug'] ?? Str::slug($validated['title']),
            'summary'      => $validated['summary'] ?? $post->summary,
            'content'      => $validated['content'],
            'is_published' => $validated['is_published'] ?? $post->is_published,
            'published_at' => $validated['published_at'] ?? $post->published_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog post updated.',
            'post'    => $post,
        ]);
    }

    /**
     * Admin: Delete blog post.
     */
    public function adminBlogDestroy(int $id): JsonResponse
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted.',
        ]);
    }
}
