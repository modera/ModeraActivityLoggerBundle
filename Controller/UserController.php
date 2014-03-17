<?php

namespace Modera\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Model\UserInterface;
use Modera\SecurityBundle\Form\Model\Registration;
use Modera\SecurityBundle\Form\Type\RegistrationType;
use Symfony\Component\Form\FormError;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UserController extends Controller
{
    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createRegistrationType()
    {
        $form = $this->createForm(new RegistrationType(), new Registration(), array(
            'action' => $this->generateUrl('_security_account_create'),
        ));
        $form->add('save', 'submit');

        return $form;
    }

    /**
     * @Route("/signup", name="_security_sign_up")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        return array(
            'form' => $this->createRegistrationType()->createView()
        );
    }

    /**
     * @Route("/account/create", name="_security_account_create")
     */
    public function createAction(Request $request)
    {
        $form = $this->createRegistrationType();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData(); //new Registration;
            $user = $registration->getUser(); //new User;

            $validator = $this->get('validator');
            $errors = $validator->validate($user);
            if (count($errors) == 0) {
                $plainPassword = $registration->getPlainPassword();

                $factory  = $this->get('security.encoder_factory');
                $encoder  = $factory->getEncoder($user);
                $password = $encoder->encodePassword($plainPassword, $user->getSalt());
                $user->setPassword($password);
                $user->eraseCredentials();

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();

                return $this->redirect($this->generateUrl('_security_login'));
            } else {
                $userType = $form->get('user');
                foreach ($errors as $error) {
                    /* @var \Symfony\Component\Validator\ConstraintViolation $error */
                    $field = $userType->get($error->getPropertyPath());
                    $field->addError(new FormError($error->getMessageTemplate()));
                }
            }
        }

        return $this->render(
            'ModeraSecurityBundle:User:register.html.twig',
            array('form' => $form->createView())
        );
    }
}