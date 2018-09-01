<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogEditType;
use App\Repository\BlogRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/intranet/admin_blog/index", name="blog_admin_index")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(RegistryInterface $doctrine, Request $request)
    {
        /**
         * @var $blogsRepo BlogRepository
         */
        $blogsRepo = $doctrine->getRepository(Blog::class);

        $em = $this->getDoctrine()->getManager();

        // Afficher les blogs

        $blogs     = $blogsRepo->getAllPosts();

        return $this->render('intranet/blog_index.html.twig',[
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/intranet/admin_blog/delete/{blogId}", name="blog_admin_delete")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @param string $blogId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(RegistryInterface $doctrine, Request $request, $blogId = '')
    {
        /**
         * @var $blogsRepo BlogRepository
         */
        $blogsRepo = $doctrine->getRepository(Blog::class);

        $em = $this->getDoctrine()->getManager();

        if (!$blogsRepo->deleteById($blogId)) {
            throw $this->createNotFoundException(
                "Blog non trouvé : ID = " . $blogId
            );
        }

        // Afficher les blogs

        $blogs     = $blogsRepo->getAllPosts();

        return $this->render('intranet/blog_index.html.twig',[
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/intranet/admin_blog/up/{blogId}", name="blog_admin_up")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @param string $blogId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function up(RegistryInterface $doctrine, Request $request, $blogId = '')
    {
        /**
         * @var $blogsRepo BlogRepository
         */
        $blogsRepo = $doctrine->getRepository(Blog::class);

        $em = $this->getDoctrine()->getManager();

        $blogSrc = $blogsRepo->find($blogId);
        $positionSrc = $blogSrc->getPosition();

        $positionDest = $blogsRepo->selectPosJustBelow($positionSrc);

        $blogDest = $blogsRepo->selectByPosition($positionDest);

        $blogSrc->setPosition($positionDest);
        $blogDest->setPosition($positionSrc);
        $em->persist($blogDest);
        $em->persist($blogSrc);
        $em->flush();

        // Afficher les blogs

        $blogs     = $blogsRepo->getAllPosts();

        return $this->render('intranet/blog_index.html.twig',[
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/intranet/admin_blog/down/{blogId}", name="blog_admin_down")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @param string $blogId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function down(RegistryInterface $doctrine, Request $request, $blogId = '')
    {
        /**
         * @var $blogsRepo BlogRepository
         */
        $blogsRepo = $doctrine->getRepository(Blog::class);

        $em = $this->getDoctrine()->getManager();

        $blogSrc = $blogsRepo->find($blogId);
        $positionSrc = $blogSrc->getPosition();

        $positionDest = $blogsRepo->selectPosJustAbove($positionSrc);

        $blogDest = $blogsRepo->selectByPosition($positionDest);

        $blogSrc->setPosition($positionDest);
        $blogDest->setPosition($positionSrc);
        $em->persist($blogDest);
        $em->persist($blogSrc);
        $em->flush();

        // Afficher les blogs

        $blogs     = $blogsRepo->getAllPosts();

        return $this->render('intranet/blog_index.html.twig',[
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/intranet/admin_blog/edit/new", name="blog_admin_create")
     * @param RegistryInterface $doctrine
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(RegistryInterface $doctrine, Request $request)
    {
        $blog = new Blog();

        $form = $this->createForm(BlogEditType::class, $blog);

        $form->handleRequest($request);

        $imageName = '';
        $orient = '';

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile $image
             */
            $image = $form['image']->getData();

            $imageName = md5(uniqid()) . '.' . $image->guessExtension();

            $info = getimagesize($image->getPathname());

            if ($info[0] > $info[1]) {
                $orient = 'paysage';
            } else {
                $orient = 'portrait';
            }
            $image->move('temp',$imageName);

        }

        return $this->render('intranet/blog_edit.html.twig',[
            'formBlogEdit' => $form->createView(),
            'image' => $imageName,
            'orient' => $orient
        ]);
    }

    /**
     * @Route("/intranet/admin_blog/edit/{blogId}", name="blog_admin_edit")
     * @param RegistryInterface $doctrine
     */
    public function edit(RegistryInterface $doctrine, $blogId)
    {

    }
}
