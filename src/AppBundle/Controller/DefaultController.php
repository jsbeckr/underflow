<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use DateTime;
use Embed\Embed;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="start")
     */
    public function startAction()
    {
        return $this->redirectToRoute('main', ['today' => date('Y-m-d')]);
    }

    /**
     * @Route("show/{today}", name="main")
     */
    public function indexAction(Request $request, $today = "")
    {
        $em = $this->getDoctrine()->getManager();
        $postRepository = $em->getRepository('AppBundle:Post');
        $categoryRepository = $em->getRepository("AppBundle:Category");

        if (empty($today)) {
            $today = date('Y-m-d');
        }

        $result = $postRepository->createQueryBuilder('post')
            ->where('post.created > :startDate ')
            ->andWhere('post.created < :endDate')
            ->setParameter('startDate', new DateTime(date('Y-m-d', strtotime($today))))
            ->setParameter('endDate', new DateTime(date('Y-m-d', strtotime($today . '+1 day'))))
            ->orderBy('post.created', 'DESC')
            ->getQuery()
            ->getResult();

        $parameters = [
            'posts' => $result,
            'previous' => date('Y-m-d', strtotime($today . ' -1 day')),
            'today' => $today];

        if (!($today == date('Y-m-d'))) {
            $parameters['next'] = date('Y-m-d', strtotime($today . ' +1 day'));
        }

        return $this->render('default/index.html.twig', $parameters);
    }

    /**
     * @Route("post", name="createPost")
     * @Security("has_role('ROLE_USER')")
     * @Method("post")
     */
    public function createPostAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $name = $request->get("name");
        $description = $request->get("description");
        $url = $request->get("url");
//        $categoryId = $request->get("category");
        $category = $em->getRepository("AppBundle:Category")->find(0);

        $info = Embed::create($url);
        $image_filename = "images/" . uniqid("", true) . ".png";
        try {
            file_put_contents($image_filename, file_get_contents($info->image));
        } catch (\Exception $ex) {
            $image_filename = "images/placeholder.png";
        }

        $post = new Post();
        $post->setImageUrl($image_filename);
        $post->setCreator($this->getUser());
        $post->setName($name);
        $post->setDescription($description);
        $post->setUrl($this->addScheme($url));
        $post->setCategory($category);
        $post->setCreated(new DateTime());

        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute("main");
    }

    /**
     * @Route("post/{id}", name="showPost")
     * @Security("has_role('ROLE_USER')")
     * @Method("get")
     */
    public function showPostAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository("AppBundle:Post")->find($id);

        return $this->render(':default:show_post.html.twig', [
            "post" => $post
        ]);
    }

    /**
     * @Route("post/comment/{post_id}", name="postComment")
     * @Method("post")
     */
    public function postCommentAction(Request $request, $post_id)
    {
        $em = $this->getDoctrine()->getManager();

        $text = $request->get("text");
        $comment = new Comment();
        $comment->setCreator($this->getUser());
        $comment->setCreated(new DateTime());
        $comment->setText($text);
        $post = $em->getRepository("AppBundle:Post")->find($post_id);
        $post->addComment($comment);
        $em->persist($comment);
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute("showPost", ["id" => $post_id]);
    }

    /**
     * @Route("post/{id}/favorite/", name="favoritePost")
     * @Security("has_role('ROLE_USER')")
     * @Method("get")
     */
    public function favoritePostAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository("AppBundle:Post")->find($id);
        $post->addFavoritedUser($this->getUser());
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute("main", ["today" => $post->getCreated()->format("Y-m-d")]);
    }

    /**
     * @Route("post/{id}/unfavorite/", name="unfavoritePost")
     * @Method("get")
     */
    public function unfavoritePost($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository("AppBundle:Post")->find($id);
        $post->removeFavoritedUser($this->getUser());
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute("main", ["today" => $post->getCreated()->format("Y-m-d")]);
    }

    function addScheme($url, $scheme = 'http://')
    {
        return parse_url($url, PHP_URL_SCHEME) === null ?
            $scheme . $url : $url;
    }
}
