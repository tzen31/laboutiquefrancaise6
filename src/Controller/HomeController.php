<?php

namespace App\Controller;

//require 'vendor/autoload.php';

//use \Mailjet\Resources;
use App\Classe\Mail;
use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Bridge\Brevo;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    #[Route('/', name: 'app_home')]
    //public function index(RequestStack $session): Response
    public function index(): Response
    {
        //MAILJET
        // $mail = new Mail();

        // $vars = [
        //     'firstname' => "Thierry",
        // ];

        // $content = 'Bonjour Thierry<br>J\'espère que vous allez bien.';
        // $mail->send('tzen@hotmail.fr','Thierry ZENNARO', 'Mon premier mail Mailjet sur Symfony 6', 'welcome.html.twig', $vars);

        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        $headers = $this->entityManager->getRepository(Header::class)->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers' => $headers
        ]);
    }

    #[Route('/brevo', name: 'app_home_brevo')]
    public function brevo(MailerInterface $mailer): Response
    {
        // Configure API key authorization: api-key
        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');

        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );

        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'subject' => 'from the PHP SDK!',
            'sender' => ['name' => 'Brevo', 'email' => 'tzen@hotmail.fr'],
            'replyTo' => ['name' => 'Brevo', 'email' => 'tzen@hotmail.fr'],
            'to' => [[ 'name' => 'Max Mustermann', 'email' => 'tzen@hotmail.fr']],
            'templateId' => 2,
            //'htmlContent' => '<html><body><h1>This is a transactional email {{params.bodyMessage}}</h1></body></html>',
            'params' => ['bodyMessage' => 'made just for you!'],
        ]); // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            print_r($result);
        } catch (\Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
        }

        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        $headers = $this->entityManager->getRepository(Header::class)->findAll();
        
        return $this->render('home/brevo.html.twig', [
            'products' => $products,
            'headers' => $headers
        ]);
    }
}
