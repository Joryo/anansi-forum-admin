<?php

namespace App\Http\Controllers;

use App\Models\Member as MemberModel;

use Illuminate\Http\Request;

/**
 * Member controller
 */
class MemberController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->middleware('auth');
        $this->model = new MemberModel();
    }

    /**
     * Display all members
     * @param Request $request
     */
    public function getAll(Request $request)
    {
        $page = $request->input('offset') ? $request->input('offset') : 0;

        return view('members', ['content' => $this->model->getAll($page)]);
    }

    /**
     * Display a single member
     * @param Request $request
     * @param int $id - Member id
     */
    public function get(Request $request, $id)
    {
        return view('members', [
            'content' => $this->model->get($id),
            'content_relationships' => [
                'posts' => $this->model->getLastRelationships($id, 'posts'),
                'comments' => $this->model->getLastRelationships($id, 'comments')
            ]
        ]);
    }

    /**
     * Update a single member and display member
     * @param Request $request
     * @param int $id - Member id
     */
    public function update(Request $request, $id)
    {
        switch ($request->input('action')) {
            case 'update':
                $this->model->update($id, $request->input());
                return redirect()->route('member', ['id' => $id]);
                break;
            case 'delete':
                $this->model->delete($id);
                break;
            default:
                break;
        }
    
        return redirect()->route('members');
    }

    /**
     * Search members and display results
     * @param Request $request
     */
    public function search(Request $request)
    {
        return view('members',
            ['content' => $this->model->search($request->input())]
        );
    }

    /**
     * Display all member's posts
     * @param Request $request
     * @param int $id - Member id
     */
    public function getAllMemberPosts(Request $request, $id)
    {
        return view('posts',
            [
                'content_source' => ['author' => $this->model->get($id)],
                'content' => $this->model->getRelationships($id, 'posts', $request->input('offset'))
            ]
        );
    }

    /**
     * Display all member's comments
     * @param Request $request
     * @param int $id - Member id
     */
    public function getAllMemberComments(Request $request, $id)
    {
        return view('comments',
            [
                'content_source' => ['author' => $this->model->get($id)],
                'content' => $this->model->getRelationships($id, 'comments', $request->input('offset'))
            ]
        );
    }
}
