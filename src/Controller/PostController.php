<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(Request $request,PostRepository $postRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $this->getParameter('page_limit');
        $offset = ($page -1) * $limit;
        $paginator = $postRepository->getPostPaginator($offset,$limit);
        $max_page = ceil($paginator->count() / $limit);
        return $this->render('post/index.html.twig', [
            'max_page' => $max_page,
            'paginator' => $paginator,
            'page' => $page
        ]);
    }


    #[Route('/post/{id}', name: 'post_show', methods: ['GET','POST'])]
    public function show(Request $request, Post $post,EntityManagerInterface $entityManager,PaginatorInterface $paginator): Response
    {
        $commentForm = $this->createForm(CommentType::class);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid())
        {
            /**@var Comment $data**/
            $data = $commentForm->getData();
            $data->setPost($post);
            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('success','您的评论已经成功提交！');
        }


        $query = $entityManager->getRepository(Comment::class)->getPaginationQuery($post);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );


        return $this->render('post/show.html.twig', [
            'post' => $post,
            'pagination' => $pagination,
            'comment_form'=>$commentForm->createView()
        ]);
    }

}
