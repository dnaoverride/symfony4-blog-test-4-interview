<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    /**
     * @Route("/article/comments", name="comments")
     */
    public function index(int $article_id, LoggerInterface $logger)
    {
        return $this->json([
            'message' => 'Welcome to your manage controller!',
            'path' => 'src/Controller/CommentsController.php',
        ]);
    }
}
