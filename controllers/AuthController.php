<?php
/*
namespace app\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\Users';
}
 */

namespace app\controllers;
 
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
/**
* UserController implements the CRUD actions for User model.
*/
class AuthController extends Controller
{
    
    public function behaviors()
    {
    return [
        'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'index'=>['get'],
            'view'=>['get'],
        ],
 
        ]
    ];
    }
 
 
    public function beforeAction($event)
    {
    $action = $event->id;
    if (isset($this->actions[$action])) {
        $verbs = $this->actions[$action];
    } elseif (isset($this->actions['*'])) {
        $verbs = $this->actions['*'];
    } else {
        return $event->isValid;
    }
    $verb = Yii::$app->getRequest()->getMethod();
 
      $allowed = array_map('strtoupper', $verbs);
 
      if (!in_array($verb, $allowed)) {
 
        $this->setHeader(400);
        echo json_encode(array('status'=>0,'error_code'=>400,'message'=>'Method not allowed'),JSON_PRETTY_PRINT);
        exit;
 
    }  
 
      return true;  
    }

    /**
    * Lists all User models.
    * @return mixed
    */

    public function actionIndex() {
        
        if(!empty($_REQUEST['login']) && !empty($_REQUEST['pass'])){
            $user = User::find()
                    ->where(['login' => $_REQUEST['login']])
                    ->one();
            if(Yii::$app->getSecurity()->validatePassword($_REQUEST['pass'], $user->pass)){
                
                //Авторизация
                $model = new User();
//                $auth = $model->login(); //Не понимаю почему не создается объект с интерфейсом IdentityInterface :(
                $auth = true;   //Тут должен быть кусок авторизации :) но его нет...
                //---------------------------
                if($auth) {
                    $this->setHeader(202);
                    echo json_encode(array('status'=>1,'error_code'=>202,'message'=>'Accepted'),JSON_PRETTY_PRINT);
                    exit;
                } else {
                    $this->setHeader(401);
                    echo json_encode(array('status'=>0,'error_code'=>401,'message'=>'Unauthorized'),JSON_PRETTY_PRINT);
                    exit;
                }
            } else {
                $this->setHeader(401);
                echo json_encode(array('status'=>0,'error_code'=>401,'message'=>'Unauthorized'),JSON_PRETTY_PRINT);
                exit;
            }    
        } else {
            $this->setHeader(400);
            echo json_encode(array('status'=>0,'error_code'=>400,'message'=>'Bad request'),JSON_PRETTY_PRINT);
            exit;
        }
    }
    
    /**
    * Displays a single User model.
    * @param integer $id
    * @return mixed
    */
    public function actionView($id)
    {
 
      $model=$this->findModel($id);
 
      $this->setHeader(200);
      echo json_encode(array('status'=>1,'data'=>array_filter($model->attributes)),JSON_PRETTY_PRINT);
 
    }
 
    /**
    * Creates a new User model.
    * @return json
    */

    protected function findModel($id)
    {
    if (($model = User::findOne($id)) !== null) {
        return $model;
    } else {
 
      $this->setHeader(400);
      echo json_encode(array('status'=>0,'error_code'=>400,'message'=>'Bad request'),JSON_PRETTY_PRINT);
      exit;
    }
    }
 
    private function setHeader($status)
      {
 
      $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
      $content_type="application/json; charset=utf-8";
 
      header($status_header);
      header('Content-type: ' . $content_type);
      header('X-Powered-By: ' . "Nintriva <nintriva.com>");
      }
    private function _getStatusCodeMessage($status)
    {
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        200 => 'OK',
        202 => 'Accepted',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
    }
}