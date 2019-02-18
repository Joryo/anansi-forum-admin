<?php

namespace App\Http\Controllers;

use App\Models\Tag as TagModel;

use Illuminate\Http\Request;

/**
 * Tag controller
 */
class TagController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware('auth');
        $this->model = new TagModel();
    }

    /**
     * Display all tags
     * @param Request $request
     */
    public function getAll(Request $request)
    {
        $page = $request->input('offset') ? $request->input('offset') : 0;

        return view('tags', ['content' => $this->model->getAll($page)]);
    }

    /**
     * Display a single tag
     * @param Request $request
     * @param int $id - Member id
     */
    public function get(Request $request, $id)
    {
        return view('tags', [
            'content' => $this->model->get($id),
        ]);
    }

    /**
     * Create a single tag and display tag list
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->model->create($request->input());

        return redirect()->route('tags');
    }

    /**
     * Update a single tag and display tag
     * @param Request $request
     * @param int $id - Tag id
     */
    public function update(Request $request, $id)
    {
        switch ($request->input('action')) {
            case 'update':
                $this->model->update($id, $request->input());
                return redirect()->route('tag', ['id' => $id]);
                break;
            case 'delete':
                $this->model->delete($id);
                break;
            default:
                break;
        }

        return redirect()->route('tags');
    }

    /**
     * Search tags and display results
     * @param Request $request
     */
    public function search(Request $request)
    {
        return view('tags',
            ['content' => $this->model->search($request->input())]
        );
    }
}
