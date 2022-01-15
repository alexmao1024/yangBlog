<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[
        Route('/post/{post_id}/comment/{comment_id}/replay', name: 'comment'),
        ParamConverter('post',options: ['id'=>'post_id']),
        ParamConverter('parentComment',options: ['id'=>'comment_id'])
    ]
    public function replayComment(Request $request, Post $post,Comment $parentComment): Response
    {
        $replayComment = $this->createForm(CommentType::class,null,[
            'action'=>$request->getUri()
        ]);
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
}
