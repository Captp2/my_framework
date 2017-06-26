<?php
namespace my_framework;
class Controller{
    protected $vars = array();
    protected $layout = 'pagedefault';

    function __construct(){
        if (isset($_POST)){
            $this->data = $_POST;   
        }
        if(isset($_SESSION['auth'])){
            $this->setLayout('loggedin');
        }
        if(!empty($this->data) && !isset($this->data['email'])){
            $modelsUser = $this->loadModel('Users');
            $user = $modelsUser->login($this->data);
            if($user){
                $_SESSION['auth'] = $user;
            }else{
                $data['login'] = '<p class="warning">The username and the password doesn\'t match.</p>';
            }
        }
        if(!empty($this->data) && isset($this->data['email'])){
            $modelsUser = $this->loadModel('Users');
            $data['subscription'] = $modelsUser->checkSubscription($this->data);
        }
        if(isset($_SESSION['auth'])){
            $this->setLayout('loggedin');
            if($_SESSION['auth']->rights == 1){
                $this->setLayout('admin');
            }
        }
    }

    function setLayout($layout){
        $this->layout = $layout;
    }

    function render($filename, $data, $controller_name){
        $controller_name = explode('\\', $controller_name)[1];
        Core::registerTwig();
        $loader = new \Twig_Loader_Filesystem(ROOT . 'public/views/' . $controller_name . '/');
        $twig = new \Twig_Environment($loader);
        $template = $twig->load('index.php');
        // extract($data);
        // ob_start();
        // $dirname = strtolower(explode('\\', get_class($this))[1]);
        // require(ROOT.'public/views/'. $dirname .'/'. strtolower($filename) .'.php');
        // $content_for_layout = ob_get_clean();
        // $content_for_layout = $this->template($content_for_layout, $data);
        if($this->layout == false){
            echo $template->render($data);
        } else {
            require(ROOT.'public/views/layout/' . $this->layout . '.php');
        }
    }

    function template($content, $data){
        foreach ($data as $key => $value) {
            $regex = '/\{\{ (' . $key . ') \}\}/';
            $content = preg_replace($regex, $value, $content);
        }
        return $content;
    }

    function getParam($name){
        $params = explode('/', $_GET['p']);
        $pointer = array_search($name, $params);
        if(isset($params[$pointer + 1])){
            return $params[$pointer + 1];
        }
        return false;
    }
}