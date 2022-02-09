<?php

namespace App\Factory;

use App\Entity\Post;

class PostFactory
{
    public function create(string $title,string $body,string $summary = null,string $status = 'draft'): Post
    {
        $post = new Post();
        $post->setTitle($title);
        if ($summary)
        {
            $post->setSummary($summary);
        }
        else{
            $post->setSummary($this->sliceBodyToSummary($body));
        }
        $post->setStatus([$status => 1]);
        $post->setBody($body);
        return $post;
    }

    private function sliceBodyToSummary(string $body,int $length = 140)
    {
        return mb_substr(strip_tags($body),0,$length);
    }
}