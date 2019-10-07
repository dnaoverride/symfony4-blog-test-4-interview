<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $articleRepository;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $userRepository;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $commentRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $entityManager->getRepository('App:Article');
        $this->userRepository = $entityManager->getRepository('App:User');
        $this->commentRepository = $entityManager->getRepository('App:Comment');
    }

    /**
     * @Route("/comment/{articleid}/", name="article_comment", methods={"POST"})
     */
    public function addComment(Request $request, CommentRepository $commentRepository, int $articleid, EntityManagerInterface $em)
    {
        $currentUser = $this->getUser();
        $article = $this->articleRepository->findOneBy(['id' => $articleid]);
        $comment = new Comment();
        $comment->setTitle($request->get('title'));
        $comment->setCommenttext($request->get('comment'));
        $comment->setUser($currentUser);
        $comment->setCreatedAt(new \DateTime());
        $comment->setArticle($article);
        $this->addFlash('success','Comment added');

        $em->persist($comment);
        $em->flush();

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'id' => $articleid,
            'canedit' => true,

        ]);
    }

    /**
     * @Route("/comment/edit/{commentid}/", name="edit_comment", methods={"GET"})
     */
    public function editComment(Request $request, CommentRepository $commentRepository, ArticleRepository $articleRepository, int $commentid, $canEdit = true, EntityManagerInterface $em)
    {
        $currentUser = $this->getUser();
        $comment = $this->commentRepository->findOneBy(['id' => $commentid]);
        $article = $this->articleRepository->findOneBy(['id' => $comment->getArticle()]);
        if ($currentUser == $comment->getUser())
        {
            $processed = null;
            if ($request->query->has('processed'))
            {
                $comment->setTitle($request->get('title'));
                $comment->setCommentText($request->get('commenttext'));
                $comment->setUser($currentUser);
                $comment->setArticle($article);
                $comment->setModifiedAt(new \DateTime());

                $em->persist($comment);
                $em->flush();
                $this->addFlash('success','Comment updated!');
                $processed = null;
                return $this->redirectToRoute('blog_index');
            }
        }
        else
        {
            $this->addFlash('danger','You can not edit comment of another user!!!');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'canedit' => $canEdit,
            'editcommentid' => $commentid,
        ]);
    }

    /**
     * @Route("/comment/delete/{commentid}/", name="delete_comment", methods={"GET"})
     */
    public function deleteComment(Request $request, CommentRepository $commentRepository, int $commentid, EntityManagerInterface $em)
    {
        $currentUser = $this->getUser();

        $comment = $this->commentRepository->findOneBy(['id' => $commentid]);
        if ($currentUser === $comment->getUser())
        {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success','Comment deleted.');
        }
        else
        {
            $badUser = $currentUser->getUsername();
            $this->addFlash('danger',"Not allowed to delete other users comments!!!");
        }

        return $this->redirectToRoute('blog_index');
    }


}
