<?php
class Users extends Controller {
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        $data = [
            'lastname' => '',
            'firstname' => '',
            'email' => '',
            'password' => '',
            'confirmPassword' => '',
            'alias' => '',
            'bio' => '',
            'lastnameError' => '',
            'firstnameError' => '',
            'emailError' => '',
            'passwordError' => '',
            'confirmPasswordError' => '',
            'aliasError' => ''
        ];

      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

              $data = [
                'lastname' => trim($_POST['lastname']),
                'firstname' => trim($_POST['firstname']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirmPassword' => trim($_POST['confirmPassword']),
                'alias' => trim($_POST['alias']),
                'bio' => trim($_POST['bio']),
                'lastnameError' => '',
                'firstnameError' => '',
                'emailError' => '',
                'passwordError' => '',
                'confirmPasswordError' => '',
                'aliasError' => ''
            ];

            $nameValidation = "/^[a-zA-Z0-9]*$/";
            $passwordValidation = "/^(.{0,7}|[^a-z]*|[^\d]*)$/i";

            //Validate alias on letters/numbers
            if (empty($data['lastname'])) {
                $data['lastnameError'] = 'Please enter lastname.';
            } elseif (!preg_match($nameValidation, $data['lastname'])) {
                $data['lastnameError'] = 'Name can only contain letters and numbers.';
            }

            if (empty($data['firstname'])) {
                $data['firstnameError'] = 'Please enter firstname.';
            } elseif (!preg_match($nameValidation, $data['firstname'])) {
                $data['firstnameError'] = 'Name can only contain letters and numbers.';
            }

            //Validate email
            if (empty($data['email'])) {
                $data['emailError'] = 'Please enter email address.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['emailError'] = 'Please enter the correct format.';
            } else {
                //Check if email exists.
                if ($this->userModel->findUserByEmail($data['email'])) {
                $data['emailError'] = 'Email is already taken.';
                }
            }

           // Validate password on length, numeric values,
            if(empty($data['password'])){
              $data['passwordError'] = 'Please enter password.';
            } elseif(strlen($data['password']) < 6){
              $data['passwordError'] = 'Password must be at least 8 characters';
            } elseif (preg_match($passwordValidation, $data['password'])) {
              $data['passwordError'] = 'Password must be have at least one numeric value.';
            }

            //Validate confirm password
             if (empty($data['confirmPassword'])) {
                $data['confirmPasswordError'] = 'Please enter password.';
            } else {
                if ($data['password'] != $data['confirmPassword']) {
                $data['confirmPasswordError'] = 'Passwords do not match, please try again.';
                }
            }

            // Make sure that errors are empty
            if (empty($data['lastnameError']) && empty($data['fistnameError']) && empty($data['emailError']) && empty($data['passwordError']) && empty($data['confirmPasswordError'])) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                //Register user from model function
                if ($this->userModel->register($data)) {
                    //Redirect to the login page
                    header('location: ' . URL_ROOT . 'users/login');
                } else {
                    die('Something went wrong.');
                }
            }
        }
        $this->view('users/register', $data);
    }

    public function login() {
        $data = [
            'title' => 'Login page',
            'alias' => '',
            'password' => '',
            'aliasError' => '',
            'passwordError' => ''
        ];

        //Check for post
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Sanitize post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'alias' => trim($_POST['alias']),
                'password' => trim($_POST['password']),
                'aliasError' => '',
                'passwordError' => '',
            ];
            //Validate alias
            if (empty($data['alias'])) {
                $data['aliasError'] = 'Please enter a alias.';
            }

            //Validate password
            if (empty($data['password'])) {
                $data['passwordError'] = 'Please enter a password.';
            }

            //Check if all errors are empty
            if (empty($data['aliasError']) && empty($data['passwordError'])) {
                $loggedInUser = $this->userModel->login($data['alias'], $data['password']);

                if ($loggedInUser) {
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['passwordError'] = 'Password or alias is incorrect. Please try again.';

                    $this->view('users/login', $data);
                }
            }

        } else {
            $data = [
                'alias' => '',
                'password' => '',
                'aliasError' => '',
                'passwordError' => ''
            ];
        }
        $this->view('users/login', $data);
        print_r($_SESSION);
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['alias'] = $user->alias;
        $_SESSION['email'] = $user->email;
        header('location:' . URL_ROOT . 'pages/index');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['alias']);
        unset($_SESSION['email']);
        header('location:' . URL_ROOT . 'users/login');
    }
}