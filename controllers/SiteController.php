<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Account;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\ExportForm;
use app\models\TransactionForm;
use app\models\RegisterForm;
use app\forms\TransferForms\TransferForm;
use app\models\User;
use app\models\UpdateForm;
use Exception;
use yii\data\ActiveDataProvider;
use kartik\base\Widget;
use kartik\dialog\DialogAsset;
use yii\web\ForbiddenHttpException;
use yii\db\ActiveRecord;
use app\models\SearchForm;
use kartik\export\ExportMenu;
use kartik\mpdf\Pdf;
use app\models\Transaction;
use mPDF;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //if post will error
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        //throw new Exception(Yii::$app->params['randomKey']);
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->session->setFlash('signupSuccess');
                return $this->redirect(['view']);
            }
            else {
                throw new Exception(current($model->getFirstErrors()));
            }
        }   
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->register()) {
                // Yii::$app->session->setFlash('Pending Register, We will come back to you asap.');
                return $this->redirect(['index']);
            }
            else {
                throw new Exception(current($model->getFirstErrors()));
            }
        }   
        return $this->render('registration', [
            'model' => $model,
        ]);        
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    public function actionAjax()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->request->format = Response::FORMAT_JSON;

            $res = array(
                'body' => print_r($_POST, true),
                'success' => true,
            );

            return $res;
        }

        $model = '';
        return $this->render('ajax', [
            'model' => $model,
        ]);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionView()
    {
        Yii::$app->session->setFlash('successs', 'You have signup success');
        // if (Yii::$app->user->can('view_profile'))
        // {
            $searchModel = new SearchForm();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render('view', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        // } else {
            // throw new ForbiddenHttpException;
        // }
    }

    public function actionTransfer()
    {
        $model = new TransactionForm();
        $model->getAccount(Yii::$app->user->identity->id);
        $model->to_account = "123431";
        // $model->getAccountNumber();
        // $model->getAccountBalance();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->transaction()) {
                return $this->redirect(['transaction']);
            }
            else {
                // throw new Exception(current($model->getFirstErrors()));
            }
        }
        return $this->render('transaction', [
            'model' => $model,
        ]);
    }

    public function actionTest()
    {
        $model = new TransactionForm();

       if (Yii::$app->request->post('submit1') === 'submit_1') {
            throw new Exception("Go get 1", 1);
        }
        if (Yii::$app->request->post('submit2') === 'submit_2') {
            throw new Exception("Go get 2", 1);
        }
        return $this->render('testing\buttontesting', [
        'model' => $model,
        ]);
    }

    public function actionGenPdf()
    {
        $id = Yii::$app->user->identity->id;
        $transaction = Transaction::find()
            ->where(['user_id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $transaction,
        ]);

        $pdf_content = $this->render('index-pdf', [
            'dataProvider' => $dataProvider,
        ]);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdf_content);
        $mpdf->Output();
        exit;
    }

    public function actionSendMoney()
    {
        $model = new TransferForm();
        // $receiver = $model->findReceiver($model->to_account);

        $model->getAccount(Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validateBalance($model->available_balance, $model->amount)) {
                if( $model->findReceiver($model->to_account)) {
                    
                    if (Yii::$app->request->post('submit') === 'getPin') {
                        $model->pendingSender();
                        // throw new Exception("Go get Pin", 1);
                    }
                    if (Yii::$app->request->post('submit') === 'transfer') {
                        if($model->validateTransfer())
                        {
                            throw new Exception("Cant lah",1);
                        }
                        else
                        {
                            $model->status = 'In';
                            $model->remark = 'Success';
                            $model->created_at = date('Y-m-d H:i:s');
                            $pdf_content = $this->render('Transferfile\afterTransfer', ['model' => $model]);
                            $mpdf = new \Mpdf\Mpdf();
                            $mpdf->WriteHTML($pdf_content);
                            $mpdf->Output();
                            exit;
                        }                       
                    }
                    return $this->render('Transferfile\duringTransfer', [
                    'model' => $model]);
                }
            }
        }
        return $this->render('transferfile\beginTransfer', [
            'model' => $model,
        ]);    
    }

    public function actionSendEmail()
    {
        $this->layout = '@app/mail/layouts/html';
        $model = new TransferForm();
        $recipient = [
            [
                'email' => 'seeyhong@hotmail.com.com',
                'name' => 'YenHong See',
                'type' => 'to'
            ]
        ];
        $subject = "Verify Code";
        $message = "testing 123";
        $content = $this->render('@app/mail/layouts/html', ['content' => '12321']);
        $mandrill = Yii::$app->mandrill->instance;
        $message = Yii::$app->mandrill->message;
        $message['to'] = $recipient;
        $message['html'] = $content;
        $message['subject'] = $subject;

        $status = $mandrill->messages->send($message, false, "Test", null);
        echo var_export($status,true) . "\n";
    }

    public function actionDelete($id)
    {
        $db = Yii::$app->db->beginTransaction();
        try{
            $user = User::findOne($id);
            $user->is_deleted = 1;
            $user->status = 'Deactivated';
            $user->remark = 'Deleted At '. date('Y-m-d H:i:s');
            if (!$user->save()) {
                //throw error
            } else {
                $account = Account::findOne(['user_id' => $id]);
                $account->is_deleted = 1;
                if (!$account->save()) {
                    Yii::$app->session->setFlash('Error', 'Account Delete No Successful');
                    //throw error
                } else {
                    $db->commit();
                    return $this->redirect(['view']);
                }
            }
        } catch (Exception $e) {
            $db->rollback();
            throw new Exception($e, 1);
        }
    }

    public function actionChangepassword()
    {
        $user = Yii::$app->user->identity;

        return $this->render('resetpassword', [
            'model' => $user,
        ]);
    }


    // public function actionUpdate($id)
    // {
    //     $model = new UpdateForm();

    //     if ($model->load(Yii::$app->request->post())) {

    //         if ($model->update($id)) {
    //                 return $this->redirect(['view' , 'id' => Yii::$app->user->identity->id]);
    //             }
    //         else {
    //                 throw new Exception("Update Problem", 1);
    //             }
    //     }        
    //     return $this->render('update', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionUpdate($id)
    {
        $personal = new UpdateForm();
        $personal->findUser($id);
        $personal->findAccount($id);
        if ($personal->load(Yii::$app->request->post())) {
            if (Yii::$app->request->post('random') === 'random_1') {
                $personal->randomGenerate();
            }
            if (Yii::$app->request->post('update') === 'update_1') {
                $personal->update($id);
                return $this->redirect(['view']);
            }            
        }
        return $this->render('update', [
            'model' => $personal,
        ]);
    }

        //    if (Yii::$app->request->post('submit1') === 'submit_1') {
        //     throw new Exception("Go get 1", 1);
        // }
        // if (Yii::$app->request->post('submit2') === 'submit_2') {
        //     throw new Exception("Go get 2", 1);
        // }
        // return $this->render('testing\buttontesting', [
        // 'model' => $model,
        // ]);

    // public function actionUpdate($id)
    // {
    //     $personal = new UpdateForm();
    //     $account = new Account(); 
    //     $personal->randomGenerate();
    //     // throw new Exception(var_export($personal, 1));
    //     if ($personal->load(Yii::$app->request->post())) {

    //          throw new Exception("Go get 1", 1);
    //     }
    //     if ($personal->load(Yii::$app->request->post() && $account->load(Yii::$app->request->post()))) {
    //         if (Yii::$app->request->post('submit1') === 'submit1') {
    //             throw new Exception("Go get 1", 1);
    //         }

    //         if (Yii::$app->request->post('update') === 'update') {
    //             throw new Exception("Go get 2", 1);
    //         }
    //     }
    //     return $this->render('update', [
    //         'model' => $this->findModel($id),
    //         'model1' => $this->findAccount($id),
    //     ]);
    // }

    public function findAccount($id)
    {
        $account = Account::find()
                    ->where(['user_id' => $id])
                    ->one();
        // $this->account_number = $account->account_number;
        // $this->current_balance = $account->current_balance;
        return $account;       
    }

    public function findModel($id)
    {
        if (($model = User::findOne($id)) !== null)
            {
                return $model;
            }
            throw NotFoundHttpException('Nothing');
    }
}
