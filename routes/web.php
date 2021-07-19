<?php
use Illuminate\Http\Request;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * Display authentification
 */
$router->get('/auth', ['as' => 'auth', function (Request $request) {
    session_destroy();
    return view('auth', $request->input());
}]);

/**
 * Entry point
 * Redirect to main if user authentificated, else redirect to auth page
 */
$router->get('/', function () {
    return redirect()->route('main');
});

/**
 * Process authentification
 */
$router->post('auth', 'AuthController@auth');

/**
 * Display main page
 */
$router->get('/main', [
        'as' => 'main',
        'uses' => 'MainController@index'
    ]);

/**
 * Members
 */

// Display members list
$router->get('/members', [
        'as' => 'members',
        'uses' => 'MemberController@getAll'
    ]);

//Display member info
$router->get('/member/{id}', [
        'as' => 'member',
        'uses' => 'MemberController@get'
    ]);

// Update member data
$router->post('/members/{id}', 'MemberController@update');

// Search members
$router->get('/members/search', 'MemberController@search');

// Display member posts
$router->get('/member/{id}/posts', [
        'as' => 'memberposts',
        'uses' => 'MemberController@getAllMemberPosts'
    ]);

// Display member comments
$router->get('/member/{id}/comments', [
        'as' => 'membercomments',
        'uses' => 'MemberController@getAllMemberComments'
    ]);

/**
 * Posts
 */

// Display posts list
$router->get('/posts',[
        'as' => 'posts',
        'uses' => 'PostController@getAll'
    ]);

//Display post info
$router->get('/post/{id}', [
        'as' => 'post',
        'uses' => 'PostController@get'
    ]);

// Update post data
$router->post('/posts/{id}', 'PostController@update');

// Search post
$router->get('/posts/search', 'PostController@search');

// Display post comments
$router->get('/post/{id}/comments', [
        'as' => 'postcomments',
        'uses' => 'PostController@getAllPostComments'
    ]);

/**
 * Comments
 */

// Display comments list
$router->get('/comments', [
        'as' => 'comments',
        'uses' => 'CommentController@getAll'
    ]);

//Display comment info
$router->get('/comment/{id}', [
        'as' => 'comment',
        'uses' => 'CommentController@get'
    ]);

// Update comment data
$router->post('/comments/{id}', 'CommentController@update');

// Search comment
$router->get('/comments/search', 'CommentController@search');

/**
 * Tags
 */

// Display tags list
$router->get('/tags',[
        'as' => 'tags',
        'uses' => 'TagController@getAll'
    ]);

//Display tags info
$router->get('/tag/{id}', [
        'as' => 'tag',
        'uses' => 'TagController@get'
    ]);

// Update tag data
$router->post('/tags/{id}', 'TagController@update');

// Search tag
$router->get('/tags/search', 'TagController@search');

// Add tag
$router->post('/tags', 'TagController@create');
