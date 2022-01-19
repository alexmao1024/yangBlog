<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[
        Route('/post/{post_id}/comment/{comment_id}/reply', name: 'reply_comment', options: ['expose' => true]),
        ParamConverter('post',options: ['id'=>'post_id']),
        ParamConverter('parentComment',options: ['id'=>'comment_id'])
    ]
    public function replyComment(Request $request, Post $post,Comment $parentComment,EntityManagerInterface $entityManager): Response
    {
        $maxLevel = $this->getParameter('max_comment_level');

        if ($parentComment->getLevel() == $maxLevel)
        {
            return new Response('<p class="max-level-info">当前层级已经不允许用户添加新层级了,</p>');
        }
        $replyComment = $this->createForm(CommentType::class,null,[
            'action'=>$request->getUri()
        ]);

        $replyComment->handleRequest($request);

        if ($replyComment->isSubmitted() && $replyComment->isValid())
        {
            /**@var Comment $data**/
            $data = $replyComment->getData();
            $data->setParent($parentComment);
            $data->setLevel($parentComment->getLevel()+1);

            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('success','您的评论已经成功提交！');

            return $this->redirectToRoute('post_show',['id'=>$post->getId()]);
        }

        return $this->render('comment/_reply_comment_form.html.twig', [
            'reply_comment_form' => $replyComment->createView()
        ]);
    }
}
