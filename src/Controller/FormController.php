<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 21/05/2019
 * Time: 13:32
 */
namespace App\Controller;

use App\Entity\Client;
use App\Service\SaveCsv;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FormController extends AbstractController
{

    /**
     * @Route("/", name="home")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showForm(Request $request, EntityManagerInterface $em)
    {
        $result= $request->query->get('result');
        $message = $request->query->get('message');

        if ($this->isGranted('ROLE_ADMIN')) {

            return $this->render('form.html.twig', array('result' => $result, 'message' => $message));

        }
        return $this->redirectToRoute('login');
        //return $this->redirectToRoute('login');

    }

    /**
     * @Route("/form/validation", name="validation")
     */
    public function insertClient(Request $request, EntityManagerInterface $em, SaveCsv $saveCsv)
    {

        $civility = $request->request->get('civility');
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $address = $request->request->get('address');
        $postalCode = $request->request->get('postCode');
        $city = $request->request->get('city');
        $country = $request->request->get('country');
        $dateOfBirth = new \DateTime($request->request->get('dateOfBirth'));
        $fidelityCardNumber = $request->request->get('fidelityCardNumber');

        if ($em->getRepository(Client::class)->findOneBy(["fidelityCardNumber" => $fidelityCardNumber]) == null)
        {

            $client = new Client();
            $client->setCivility($civility);
            $client->setFirstName($firstName);
            $client->setLastName($lastName);
            $client->setAddress($address);
            $client->setPostalCode($postalCode);
            $client->setCity($city);
            $client->setCountry($country);
            $client->setDateOfBirth($dateOfBirth);
            $client->setFidelityCardNumber($fidelityCardNumber);

            $em->persist($client);
            $em->flush();

            $fileName = $fidelityCardNumber.".csv";
            $dataTab = array(
                "civility" => $civility,
                "firstName" => $firstName,
                "lastName" => $lastName,
                "address" => $address,
                "postalCode" => $postalCode,
                "city," => $city,
                "country" => $country,
                "dateOfBirth" => $dateOfBirth,
                "fidelityCardNumber" => $fidelityCardNumber
            );

            $saveCsv->saveFile($fileName, $dataTab);

            $result = "success";
            $message = "Inscription réussi";
        }
        else
        {
            $result = "failed";
            $message = "Fidelity card number failed, Card number already taken";
        }
;
        return $this->redirectToRoute('home', array('result' => $result, 'message' => $message));

    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', array('last_username' => $lastUsername, 'error' => $error));
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     * @throws Exception
     */
    public function logout()
    {
        throw new Exception('Error logout');
    }

}
