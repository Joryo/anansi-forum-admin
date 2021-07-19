<?php

namespace App\Http\Controllers;

use App\Models\Post as PostModel;
use App\Models\Tag as TagModel;

use Illuminate\Http\Request;

/**
 * Post controller
 */
class PostController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware('auth');
        $this->model = new PostModel();
        $this->tagModel = new TagModel();
    }

    /**
     * Display all posts
     * @param Request $request
     */
    public function getAll(Request $request)
    {
        $content = $this->model->getAll();
        $tags = $this->tagModel->getAll();
        $post_tags = $this->extractPostTags($content, $tags);
        $page = $request->input('offset', 0);

        return view(
            'posts', [
            'content' => $this->model->getAll($page),
            'tags' => $tags,
            'post_tags' => $post_tags
        ]);
    }

    /**
     * Display a single post
     * @param Request $request
     * @param int $id - Post id
     */
    public function get(Request $request, $id)
    {
        $content = $this->model->get($id);
        $tags = $this->tagModel->getAll();
        $post_tags = $this->extractPostTags($content, $tags);

        return view(
            'posts', [
            'content' => $content,
            'content_relationships' => [
                'author' => $this->model->getRelationships($id, 'author'),
                'comments' => $this->model->getLastRelationships($id, 'comments')
            ],
            'tags' => $tags,
            'post_tags' => $post_tags
        ]);
    }

    /**
     * Extract checked tags from post content
     * @param object $content - Post content
     * @param object $tags - All available tags
     *
     * @return array All tags with check of post's tags
     */
    private function extractPostTags($content, $tags)
    {
        $post_tags = [];
        foreach ($content->data as $post) {
            $post_tags[$post->id] = [];
            $checked_tags = array_map(
                function ($e) {
                    return $e->id;
                },
                $post->relationships->tags->data
            );

            foreach ($tags->data as $tag) {
                $post_tags[$post->id][$tag->id] = in_array($tag->id, $checked_tags);
            }
        }

        return $post_tags;
    }

    /**
     * Update a single post and display post
     * @param Request $request
     * @param int $id - Post id
     */
    public function update(Request $request, $id)
    {
        switch ($request->input('action')) {
            case 'update':
                $this->model->update($id, $request->input());
                return redirect()->route('post', ['id' => $id]);
                break;
            case 'delete':
                $this->model->delete($id);
                break;
            default:
                break;
        }

        return redirect()->route('posts');
    }

    /**
     * Search posts and display results
     * @param Request $request
     */
    public function search(Request $request)
    {
        return view(
            'posts',
            ['content' => $this->model->search($request->input())]
        );
    }

    /**
     * Display all post's comments
     * @param Request $request
     * @param int $id - Post id
     */
    public function getAllPostComments(Request $request, $id)
    {
        return view('comments',
            [
                'content_source' => ['post' => $this->model->get($id)],
                'content' => $this->model->getRelationships($id, 'comments', $request->input('offset'))
            ]
        );
    }
}
