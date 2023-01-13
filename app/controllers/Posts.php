<?php
class Posts extends Controller
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = $this->model('Post');
    }

    public function index()
    {
        // $posts = $this->postModel->findAllPosts();
        // $data = [
        //     'posts' => $posts
        // ];

        $data['posts'] = $this->postModel->findAllPosts();
        $this->view('posts/index', $data);
        // var_dump($data);
    }

    public function create()
    {
        if (!isLoggedIn()) {
            header('Location: ' . URL_ROOT . 'posts');
        }

        $data = [
            'title' => '',
            'excerpt' => '',
            'content' => '',
            'category' => '',
            'date' => '',
            'author' => '',
            'titleError' => '',
            'excerptError' => '',
            'contentError' => '',
            'categoryError' => '',
            'dateError' => '',
            'authorError' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'excerpt' => trim($_POST['excerpt']),
                'content' => trim($_POST['content']),
                'category' => trim($_POST['category']),
                'date' => date('l j F Y'),
                'author' => $_SESSION['alias'],
            ];

            if(empty($data['title'])) $data['titleError'] = 'The title cannot be empty';
            if(empty($data['excerpt'])) $data['excerptError'] = 'The excerpt cannot be empty';
            if(empty($data['content'])) $data['contentError'] = 'The content cannot be empty';
            if(empty($data['category'])) $data['categoryError'] = 'The category cannot be empty';
            // Equivalence
            // ATTENTION seulement si il n'y qu'une seul instruction dans le if
            // if (empty($data['category'])) {
            //     $data['categoryError'] = 'The category cannot be empty'; // Instruction
            // }

            if (empty($data['titleError']) && empty($data['excerptError']) && empty($data['contentError']) && empty($data['categoryError'])) {
                if($this->postModel->addPost($data)) header('Location: ' . URL_ROOT . 'posts');
                else die('Something went wrong, please try again!!!');
                // Equivalent
                // if ($this->postModel->addPost($data)) {
                //     header("Location: " . URLROOT . "/posts");
                // } else {
                //     die("Something went wrong, please try again!");
                // }
            } else {
                $this->view('posts/create');
            }
        }

        $this->view('posts/create');
    }

    public function read($id)
    {
        $post = $this->postModel->findPostByID($id);
        $data = [
            'post' => $post
        ];

        $this->view('posts/read', $data);
    }

    public function update($id)
    {
        $post = $this->postModel->findPostById($id);

        if(!isLoggedIn()) {
            header("Location: " . URL_ROOT . "posts");
        } elseif($post->author != $_SESSION['alias']) {
            header("Location: " . URL_ROOT . "posts");
        }

        $data = [
            'post' => $post,
            'title' => '',
            'excerpt' => '',
            'content' => '',
            'category' => '',
            'titleError' => '',
            'excerptError' => '',
            'contentError' => '',
            'categoryError' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'post' => $post,
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'excerpt' => trim($_POST['excerpt']),
                'content' => trim($_POST['content']),
                'category' => trim($_POST['category']),
                'titleError' => '',
                'excerptError' => '',
                'contentError' => '',
                'categoryError' => '',
            ];

            if(empty($data['title'])) $data['titleError'] = 'The title of a post cannot be empty';
            if(empty($data['excerpt'])) $data['excerptError'] = 'The excerpt of a post cannot be empty';
            if(empty($data['content'])) $data['contentError'] = 'The content of a post cannot be empty';
            if(empty($data['category'])) $data['categoryError'] = 'The category of a post cannot be empty';

            // Inutile dans le monde réel
            // Uniquement dans un but pédagogique pour vous faire la façon dont on compare 2 entrées
            // if($data['title'] == $this->postModel->findPostById($id)->title) $data['titleError'] == 'At least change the title!';
            // if($data['excerpt'] == $this->postModel->findPostById($id)->excerpt) $data['excerptError'] == 'At least change the excerpt!';
            // if($data['content'] == $this->postModel->findPostById($id)->content) $data['contentError'] == 'At least change the content!';
            // if($data['category'] == $this->postModel->findPostById($id)->category) $data['categoryError'] == 'At least change the category!';

            if (empty($data['titleError']) && empty($data['excerptError']) && empty($data['contentError']) && empty($data['categoryError'])) {
                if ($this->postModel->updatePost($data)) {
                    header("Location: " . URL_ROOT . "posts");
                } else {
                    die("Something went wrong, please try again!");
                }
            } else {
                $this->view('posts/update', $data);
            }
        }

        $this->view('posts/update', $data);
    }

    public function delete($id)
    {
        $post = $this->postModel->findPostById($id);

        if(!isLoggedIn()) {
            header("Location: " . URL_ROOT . "posts");
        } elseif($post->id != $_SESSION['user_id']){
            header("Location: " . URL_ROOT . "posts");
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if($this->postModel->deletePost($id)) {
                header("Location: " . URL_ROOT . "posts");
            } else {
            die('Something went wrong!');
            }
        }
    }
}