<?php

namespace CodeBuds\SyliusFCMPlugin\Controller;

use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMNotification;
use CodeBuds\SyliusFCMPlugin\Form\Type\ShopUserFCMNotificationType;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ShopUserFCMNotificationController extends ResourceController
{
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private FirebaseMessaging $messaging;

    public function createAction(Request $request): Response
    {
        $shopUserNotification = new ShopUserFCMNotification();

        $requestConfiguration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $notificationForm = $this->formFactory->create(ShopUserFCMNotificationType::class, $shopUserNotification, [
            'action' => $this->generateUrl('codebuds_sylius_fcm_plugin_admin_shop_user_fcm_notification_create')
        ]);

        $notificationForm->handleRequest($request);


        if ($notificationForm->isSubmitted() && !$notificationForm->isValid()) {
            $response = new Response(null, 422);
            return $this->render('@CodeBudsSyliusFCMPlugin/Grid/Form/_shopUserNotificationForm.html.twig',
                [
                    'form' => $notificationForm->createView()
                ], $response
            );
        }

        if ($notificationForm->isSubmitted() && $notificationForm->isValid()) {
            $this->messaging->sendShopUserNotification(
                $shopUserNotification
            );
            return new JsonResponse(['url' => $this->generateUrl('codebuds_sylius_fcm_plugin_admin_shop_user_fcm_notification_index')]);
        }

        if ($requestConfiguration->isHtmlRequest()) {
            return $this->render($requestConfiguration->getTemplate(ResourceActions::CREATE . '.html'), [
                'configuration' => $requestConfiguration,
                'metadata' => $this->metadata,
                'notificationForm' => $notificationForm->createView(),
                'credentials' => null,
            ]);
        }
        return $this->createRestView($requestConfiguration, []);
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function setTwigEnvironment(Environment $twig): void
    {
        $this->twig = $twig;
    }

    public function setFCMService(FirebaseMessaging $messaging): void
    {
        $this->messaging = $messaging;
    }
}
