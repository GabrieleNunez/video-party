<?php namespace App\Controllers;

use Library\View;
use Library\Request;

//A base Controller class. Light and sweet
class Controller
{
    protected $errors = array(); // anything that can be reported as an error
    protected $json = array('success' => true, 'response' => array());
    protected $templates = array(
        'header ' => '/templates/header.php',
        'content' => false,
        'footer' => '/templates/footer.php'
    );

    protected $linked_templates = array();

    protected $variables = array(); // template variables

    private $response_errors = array(
        '400' => 'HTTP/1.0 400 Bad Request',
        '401' => 'HTTP/1.0 401 Unauthorized',
        '403' => 'HTTP/1.0 403 Forbidden',
        '404' => 'HTTP/1.0 404 Not Found',
        '500' => 'HTTP/1.0 500 Internal Server Error'
    );

    public function redirect($url, $delay = 0)
    {
        if ($delay > 0) {
            header('refresh:' . $delay . '; url=' . $url);
        } else {
            header('Location: ' . $url);
            exit();
        }
    }

    protected function bad_request($force_json = false)
    {
        // Use this when you can't make sense of a request - like asking to see a user profile but not sending a user id
        header($this->response_errors['400'], true, 400);
        $this->render_error('400', $force_json);
    }

    protected function unauthorized($force_json = false)
    {
        // Use this when the user is a guest or their login token is expired
        header($this->response_errors['401'], true, 401);
        $this->render_error('401', $force_json);
    }

    protected function forbidden($force_json = false)
    {
        // Use this when the user is authenticated properly but they're not allowed to view this particular resource or data object
        header($this->response_errors['403'], true, 403);
        $this->render_error('403', $force_json);
    }

    protected function not_found($force_json = false)
    {
        // Use this when we can't find the requested resource or when we want the user to think it doesn't exist
        header($this->response_errors['404'], true, 404);
        $this->render_error('404', $force_json);
    }

    protected function internal_error($force_json = false)
    {
        // Use this when the application itself throws an error that we can't recover from and we just need to bail
        header($this->response_errors['500'], true, 500);
        $this->render_error('500', $force_json);
    }

    public function content_view($path)
    {
        $this->templates['content'] = $path;
    }

    public function header_view($path)
    {
        $this->templates['header'] = $path;
    }

    public function footer_view($path)
    {
        $this->templates['footer'] = $path;
    }

    public function clear_variables()
    {
        $this->variables = array();
    }

    // store a variable
    public function variable($name, $value = null)
    {
        if (is_array($name)) {
            // where we simply passed an array of variables with keys ?
            $this->variables = array_merge($this->variables, $name);
        } elseif ($value !== null) {
            $this->variables[$name] = $value;
        } else {
            return $this->variables[$name];
        }
    }

    // store an error about what occurred during the controller logic process
    public function error($name, $value = null)
    {
        if (is_array($name)) {
            $this->errors += $name;
        } else {
            $this->errors[$name] = $value;
        }
    }

    public function has_errors()
    {
        return $this->errors ? true : false;
    }

    // render our view as a template and pass in our defined variables
    public function render_template()
    {
        $this->variables['errors'] = $this->errors;
        $this->variables['copyright'] = 'Copyright &copy; ' . date('Y');
        foreach ($this->templates as $type => $template) {
            if ($template !== false) {
                $links = array_merge($this->linked_templates);
            }
            echo View::make($template)
                ->with($this->variables)
                ->link($links);
        }
    }

    // render our view as raw json using our variables and errors as data
    public function render_json()
    {
        header('Content-type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        $this->json['response'] = $this->variables; // place our template varables
        $this->json['errors'] = $this->errors; // place the errors
        $this->json['success'] = !$this->errors ? true : false;
        echo json_encode($this->json, JSON_FORCE_OBJECT);
        exit();
    }

    // render some kind of header error
    public function render_error($error, $force_json = false)
    {
        $status = str_replace('HTTP/1.0', '', $this->response_errors[$error]);
        $this->variables = array('status' => $error, 'message' => $status);
        $this->errors = array();
        if (Request::isAjax() || $force_json) {
            $this->render_json();
        } else {
            $this->variables = array('status' => $error, 'message' => $status);
            echo View::make('errors/error.php')->with($this->variables);
            exit();
        }
    }
}
?>
