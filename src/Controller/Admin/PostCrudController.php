<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Security\Voter\PostVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Workflow\WorkflowInterface;

class PostCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            ImageField::new('postImage')
                ->setBasePath($this->getParameter('base_path'))
                ->setUploadDir($this->getParameter('upload_dir'))
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]'),

            TextareaField::new('summary'),
            TextEditorField::new('body'),
            AssociationField::new('author')->onlyOnIndex(),
            ArrayField::new('status')
                ->setTemplatePath('fields/post_status.html.twig')
                ->onlyOnIndex(),
//            ChoiceField::new('status')
//                ->setChoices(fn () => ['draft' => 'draft','published' => 'published']),
            TimeField::new('createAt')->setFormat('Y-MM-dd HH:mm:ss')->onlyOnIndex(),
            TimeField::new('updateAt')->setFormat('Y-MM-dd HH:mm:ss')->onlyOnIndex()

        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC'])
            ->setSearchFields(['title','summary','body']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(ChoiceFilter::new('status')->setChoices(['draft'=>'draft','published'=>'published']));
    }

    public function configureActions(Actions $actions): Actions
    {
        $workflow = $this->container->get('workflow.blog_publishing');

        //review_request
        $reviewRequestAction = Action::new('review_request','review_request')
            ->displayIf(fn($entity)=>$workflow->can($entity,'review_request'))
            ->linkToCrudAction('reviewRequestAction');

        //editor_review
        $editorReviewAction = Action::new('editor_review','editor_review')
            ->displayIf(fn($entity)=>$workflow->can($entity,'editor_review'))
            ->linkToCrudAction('editorReviewAction');

        //checker_check
        $checkerCheckAction = Action::new('checker_check','checker_check')
            ->displayIf(fn($entity)=>$workflow->can($entity,'checker_check'))
            ->linkToCrudAction('checkerCheckAction');

        //published
        $publishedAction = Action::new('published','published')
            ->displayIf(fn($entity)=>$workflow->can($entity,'published'))
            ->linkToCrudAction('publishedAction');

        return $actions
            ->update(Crud::PAGE_INDEX,Action::EDIT,
                function (Action $action) {
                    return $action->displayIf(fn($entity) => $this->isGranted(PostVoter::POST_OWNER_EDITOR, $entity));
                })
            ->update(Crud::PAGE_INDEX,Action::DELETE,
                function (Action $action) {
                    return $action->displayIf(fn($entity) => $this->isGranted(PostVoter::POST_OWNER_DELETE, $entity));
                })
            ->add(Crud::PAGE_INDEX,$reviewRequestAction)
            ->add(Crud::PAGE_INDEX,$editorReviewAction)
            ->add(Crud::PAGE_INDEX,$checkerCheckAction)
            ->add(Crud::PAGE_INDEX,$publishedAction);
    }

    //review_request Controller
    public function reviewRequestAction(AdminContext $adminContext)
    {
        $workflowName = 'review_request';
        $this->setWorkflowAction($adminContext,$workflowName);

        return $this->setRedirectForPostCrud();
    }

    //editor_review Controller
    public function editorReviewAction(AdminContext $adminContext)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $workflowName = 'editor_review';
        $this->setWorkflowAction($adminContext,$workflowName);

        return $this->setRedirectForPostCrud();
    }

    //checker_check Controller
    public function checkerCheckAction(AdminContext $adminContext)
    {
        $this->denyAccessUnlessGranted('ROLE_CHECKER');
        $workflowName = 'checker_check';
        $this->setWorkflowAction($adminContext,$workflowName);

        return $this->setRedirectForPostCrud();
    }

    //published Controller
    public function publishedAction(AdminContext $adminContext)
    {
        $workflowName = 'published';
        $this->setWorkflowAction($adminContext,$workflowName);

        return $this->setRedirectForPostCrud();
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(),[
            'workflow.blog_publishing'=>'?'. WorkflowInterface::class
        ]);
    }
}
