<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @todo Refactor: isolate Front modules in specific files (like, CommentsController, VideoController, etc...)
 */
class FrontController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/search-results/{page}', name: 'search_results', methods: ['GET'], defaults: ['page' => "1"])]
    public function searchResults(Request $request, $page, VideoRepository $videoRepository): Response
    {
        $videos = null;

        if ($query = $request->get('query')) {
            $videos = $videoRepository->findByTitle($query, $page, $request->get('sortby'));

            if (!$videos->getItems()) {
                $videos = null;
            }
        }

        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', name: 'video_list', defaults: ['page' => '1'])]
    public function videoList(
        $id,
        $page,
        Request $request,
        VideoRepository $videoRepository,
        CategoryTreeFrontPage $categories
    ): Response {
        $categories->getCategoryListAndParent($id);
        $categoryIds = $categories->getChildIds($id);
        array_push($categoryIds, (int) $id);

        $videos = $videoRepository->findByChildIds(
            $categoryIds,
            (int) $page,
            $request->get('sortby')
        );

        return $this->render('front/videolist.html.twig', [
            'subcategories' => $categories,
            'videos' => $videos,
        ]);
    }

    #[Route('/video-details/{video}', name: 'video_details')]
    public function videoDetails(VideoRepository $videoRepository, $video): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $videoRepository->videoDetails($video),
        ]);
    }

    #[Route('/new-comment/{video}', name: 'new_comment', methods: ['POST'])]
    public function newComment(
        Request $request,
        Video $video,
        CommentRepository $commentRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $content = $request->request->get('comment');
        if (!empty(trim($content))) {
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setOwner($this->getUser());
            $comment->setVideo($video);
            $commentRepository->add($comment, true);
        }

        return $this->redirectToRoute('video_details', [
            'video' => $video->getId(),
        ]);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @todo Needs a service to store User (clear this code...)
     */
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userFieldsData = $request->request->all('user');
            $user->setName($userFieldsData['name']);
            $user->setLastName($userFieldsData['last_name']);
            $user->setEmail($userFieldsData['email']);
            $password = $passwordHasher->hashPassword($user, $userFieldsData['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['USER_ROLE']);

            $userRepository->add($user, true);
            $this->loginUserAutomatically($user);
            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('front/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    private function loginUserAutomatically(User $user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );
        $this->container->get('security.token_storage')->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony's managed route
        throw new \Exception('This should never be reacher!');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            ['parent' => null],
            ['name' => 'ASC']
        );

        return $this->render('front/partials/_main_categories.html.twig', compact('categories'));
    }
}
