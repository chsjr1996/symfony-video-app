<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/su', name: 'admin_dashboard_main')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
