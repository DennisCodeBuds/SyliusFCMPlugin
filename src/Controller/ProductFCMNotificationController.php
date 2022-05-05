<?php

namespace CodeBuds\SyliusFCMPlugin\Controller;

use App\Entity\Product\Product;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use CodeBuds\SyliusFCMPlugin\Form\Type\ProductFCMNotificationType;
use CodeBuds\SyliusFCMPlugin\Service\FirebaseMessaging;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ProductFCMNotificationController extends ResourceController
{
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private FirebaseMessaging $messaging;

    public function createAction(Request $request): Response
    {
        $productNotification = new ProductFCMNotification();

        if ($topicId = $request->query->get('topic_id')) {
            $topic = $this->manager->getRepository(ProductFCMTopic::class)->findOneBy(['id' => $topicId]);
            $productNotification->setTopic($topic);
        } elseif ($productId = $request->query->get('subject_id')) {
            $product = $this->manager->getRepository(Product::class)->findOneBy(['id' => $productId]);
            $topic = $product->getTopic();
            $productNotification->setTopic($topic);
        }

        if (isset($topic)) {
            $action = $this->generateUrl('codebuds_sylius_fcm_plugin_admin_product_notify', ['topic_id' => $topic->getId()]);
        } else {
            $action = $this->generateUrl('codebuds_sylius_fcm_plugin_admin_product_notify');
        }

        $requestConfiguration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $notificationForm = $this->formFactory->create(ProductFCMNotificationType::class, $productNotification, [
            'action' => $action
        ]);

        $notificationForm->handleRequest($request);


        if ($notificationForm->isSubmitted() && !$notificationForm->isValid()) {
            $response = new Response(null, 422);
            return $this->render('@CodeBudsSyliusFCMPlugin/Grid/Form/_productNotificationForm.html.twig',
                [
                    'form' => $notificationForm->createView()
                ], $response
            );
        }

        if ($notificationForm->isSubmitted() && $notificationForm->isValid()) {
            $this->messaging->sendProductNotification(
                $productNotification
            );
            return new JsonResponse(['url' => $this->generateUrl('codebuds_sylius_fcm_plugin_admin_product_fcm_notification_index')]);
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
