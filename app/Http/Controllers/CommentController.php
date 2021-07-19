<?php

namespace App\Http\Controllers;

use App\Models\Comment as CommentModel;

use Illuminate\Http\Request;

/**
 * Comment controller
 */
class CommentController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware('auth');
        $this->model = new CommentModel();
    }

    /**
     * Display all comments
     * @param Request $request
     */
    public function getAll(Request $request)
    {
        $page = $request->input('offset', 0);

        return view('comments', ['content' => $this->model->getAll($page)]);
    }

    /**
     * Display a single comment
     * @param Request $request
     * @param int $id - Comment id
     */
    public function get(Request $request, $id)
    {
        return view('comments', [
            'content' => $this->model->get($id),
            'content_relationships' => [
                'post' => $this->model->getRelationships($id, 'post'),
                'author' => $this->model->getRelationships($id, 'author')
            ]
        ]);
    }

    /**
     * Update a single comment and display comment
     * @param Request $request
     * @param int $id - Comment id
     */
    public function update(Request $request, $id)
    {
        switch ($request->input('action')) {
            case 'update':
                $this->model->update($id, $request->input());
                return redirect()->route('comment', ['id' => $id]);
                break;
            case 'delete':
                $this->model->delete($id);
                break;
            default:
                break;
        }

        return redirect()->route('comments');
    }

    /**
     * Search comments and display results
     * @param Request $request
     */
    public function search(Request $request)
    {
        return view(
            'comments',
            ['content' => $this->model->search($request->input())]
        );
    }
}
