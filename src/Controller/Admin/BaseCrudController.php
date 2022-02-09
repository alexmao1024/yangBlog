<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function setWorkflowAction(AdminContext $adminContext,string $workflowName)
    {
        $post = $adminContext->getEntity()->getInstance();
        if ($post instanceof Post){
            $workflow = $this->container->get('workflow.blog_publishing');
            if ($workflow->can($post,$workflowName)){
                $workflow->apply($post,$workflowName);
                $this->container->get('doctrine')->getManager()->flush();
            }
        }
    }

    public function setRedirectForPostCrud(): RedirectResponse
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator
            ->setController(PostCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        );
    }
}