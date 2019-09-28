<?php
/**
 * Created by PhpStorm.
 * UserOld: dnaoverride
 * Date: 9/23/2019
 * Time: 3:53 PM
 */

namespace App\Controller;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleAdminController extends AbstractController
{
    /**
     * @Route("/article/new",name="article_new")
     */
    public function new(EntityManagerInterface $entity)
    {
        $article = new Article();
        $article->setTitle('Creating My First Symfony Blog')
            ->setArticleslug('creating-my-first-symfony-blog-'.rand(100, 999))
            ->setContent(<<<EOF
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam in purus et justo iaculis rutrum vulputate vel urna. Nunc ac mattis leo, et convallis libero. Nullam congue faucibus massa non porta. Suspendisse sit amet congue lacus. Ut auctor at libero a faucibus. Duis pulvinar, mauris a porttitor bibendum, quam nisi aliquam felis, a imperdiet nulla urna malesuada metus. Pellentesque elit sem, aliquet eu gravida vitae, lacinia ac mi. Sed id turpis ac dui eleifend ornare. Donec tempor gravida augue, in facilisis elit ultricies quis. Etiam sollicitudin urna sem, sit amet consequat risus posuere ac. Aliquam erat volutpat. Sed vulputate ut odio non pretium. Vestibulum auctor felis in consectetur mattis.

Ut a cursus ex. Ut eget faucibus lorem, in vehicula augue. Nam et feugiat velit, at imperdiet lacus. Maecenas ut felis justo. Duis eu nisi fringilla, iaculis risus quis, mattis erat. Etiam auctor est volutpat justo porttitor molestie. Fusce at auctor tellus. Vivamus elementum hendrerit nulla, quis eleifend dolor pharetra ac. Nam eleifend ligula et finibus aliquam. Quisque nec maximus erat, in interdum magna. Curabitur aliquet sem eu nisi eleifend, sed hendrerit arcu volutpat. Duis id rhoncus lorem. Integer tellus nisi, semper et felis quis, tempor malesuada magna. Proin elementum odio eu est auctor laoreet eu ut leo. Nunc sollicitudin rutrum arcu at tincidunt. Integer tincidunt facilisis erat vitae luctus.

In vel interdum felis. Proin eu dui dapibus, efficitur felis id, malesuada est. Integer vel magna id quam tempor pellentesque at nec felis. Nam auctor imperdiet dui vitae vulputate. Fusce iaculis sem at nibh aliquet efficitur. Proin mattis dignissim cursus. Sed faucibus urna est, at dictum tortor faucibus non. Fusce convallis pharetra eros et fringilla. Fusce eget pretium dolor. Phasellus non velit sit amet sem semper blandit in in mauris. Etiam eu mauris aliquet tortor mollis condimentum. Maecenas porta lorem est, a vulputate dolor aliquam in. Donec rutrum gravida purus non dapibus. Aenean vulputate ligula ac varius euismod. Aliquam erat volutpat. Donec eu est dignissim, tincidunt magna id, mollis ante.

Maecenas malesuada rutrum elit, nec molestie ligula molestie pulvinar. Quisque nec varius velit, a mollis sapien. Aenean mattis nulla neque, ut ornare quam molestie eu. Ut ut viverra odio. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam dignissim non arcu eget sagittis. In nunc ipsum, scelerisque semper cursus sit amet, aliquet sed dui. Etiam efficitur posuere quam ac tincidunt. Fusce tempus dui non tellus mollis venenatis. Cras mattis erat at justo egestas congue.

Ut bibendum blandit massa, blandit cursus neque mollis vel. Ut efficitur at dui sed viverra. Nunc sed molestie enim. Mauris gravida convallis augue quis dictum. Sed a erat tristique, iaculis elit ac, tempus lorem. Nulla imperdiet mauris quis tellus commodo pharetra. Mauris aliquet venenatis mollis. Curabitur quis metus eleifend, consectetur ligula quis, malesuada nulla. Duis sed erat sagittis, euismod est eget, ornare ligula.
EOF
            );
        // publish most articles
        if (rand(1, 10) > 2) {
            $article->setPublishedAt(new \DateTime(sprintf('-%d days', rand(1, 20))));
        }
        $entity->persist($article);
        $entity->flush();
        return new Response(sprintf(
            'New ArticleFixture id: #%d slug: %s',
            $article->getId(),
            $article->getSlug()
        ));
    }
}