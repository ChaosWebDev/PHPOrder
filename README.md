Please note that the README is in progress. If you have any questions, feel free
to throw an issue log and I'll get to it as quick as I can.

<h2>Instillation</h2>
<h3>With Composer</h3>
`composer install chaoswd/phporder`

<h2>Instantiation</h2>
<em>/public_html/index.php</em>
````
  require(__DIR__ . "/../vendor/autoload.php"); use
  ChaosWD\Controller\SystemController;
  SystemController::setup();
  $request = new ChaosWD\Controller\RequestController();
  $request->request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
````

<h2>Controllers</h2>
<h3>EnvironmentController</h3>
<p>
  This controller grabs any .env files it can find and turns them into $_ENV
  variables.<br />
</p>

<h3>FormController.php</h3>
<p>
  This controller, when properly used in the `$routes`, will validate, and
  sanitize the variables that are passed through $_POST.<br />
  For XSS security, you can use $formController->generateToken() to get a token
  for your form. You'd add this in a hidden input field. It will then validate
  the token on the server side.
</p>
<h4>Utilization</h4>
````
  use ChaosWD\Controller\FormController;
  $form = new FormController();
  $form->validateToken($_POST['token']);
  $username = $form->process($_POST['username'], "string");
  $password = $form->process($_POST['password'], "string");
````

<h3>JSONController</h3>
<p>
  This controller will work any any API Response that comes back in JSON format.
</p>
<h4>Utilization</h4>
````
  use ChaosWD\Controller\JSONController;
  
  $jc = new JSONController($url); // The base URL for the API (without Endpoints) 
  
  $fullResponse = $jc->getFull($url); // URL is optional. If left blank, it will take the one from the instantiation.
  
  $endpoint = "endpoint";
  $endpointResponse = $jc->getEndpoint($endpoint); 
  
  // $path = "primary->sub_object->sub_sub_object";
  depending on how the response if formatted (before decoding) 
  
  $specificResponse = $jc->getPartial($path, $fullResponse); // $fullResponse can be left blank.
  
  $options = array( "url" => null,
                    "path" => $path,
                    "endpoint" => "endpoint_name", 
                    "params" => method=active", 
                    "limit" => 5 ); // EACH VALUE CAN BE LEFT NULL OR OMITTED 
  $complexResponse = $jc->getComplex($options); // Limit will only trigger if the results before limit are an array. Otherwise, it'll ignore this field.
````

<h3>LogController</h3>
<p>
    Log Controller is fairly straight forward. When you instantiate it, you put what you want the log name to be (without the .log).<br>
    It will create the log and make a note of the creation time (based on your set default_timezone).
</p>
<h4>Utilization</h4>
````
    $userLogger = new ChaosWD\Controller\LogController("userLog");
    $userLog->add($object);
````
<p>
    $object needs to include at least 2 parts:<br>
    * $object->reason<br>
    * $object->message<br>
    <br>
    $object may have an optional `$object->data` piece that can be a string, object, or array.
</p>

<h3>RequestController</h3>
<p>
    This is the backbone of the system. It takes the URI (e.g.: /index), compares it to the $routes file, and completes the action based on the appropriate $route. If no route is found, it will log in it the `errorLog.log` 
</p>

<h3>SystemController</h3>
<p>
    Right now, this one does some basic setup for the site (timezone, triggers EnvironmentController), and has a file searching method to find any files matching your requested target (e.g.: `.env`).
</p>

<h3>TemplateController</h3>
<p>
    This one works with the RequestController. Any 'page' for display, will go through here.
</p>

<h3>UserController</h3>
<p>
    This one has the functionality to check (on each page) if a user is logged in or not. It DOES NOT work with JWT or other Token systems yet.
</p>
<p>
    For normal user logins, the `RequestController` will automatically trigger it, so no need to instantiate it on your own. It will check if the username exists in the database, if the password matches, and, depending on your .env settings, can also check if the user has failed log in too many times, or if there's a restricted `status` on their profile.
</p>