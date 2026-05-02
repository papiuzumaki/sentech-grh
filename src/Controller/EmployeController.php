<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Employe;
use App\Form\ContratType;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use App\Service\EmployeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employes')]
class EmployeController extends AbstractController
{
    public function __construct(
        private EmployeService $employeService,
        private EmployeRepository $employeRepo,
    ) {}

    #[Route('/', name: 'employe_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $qb = $this->employeRepo->findTousAvecRelations();

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('employe/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/nouveau', name: 'employe_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultat = $this->employeService->creerEmploye($employe);

            if ($resultat->isSucces()) {
                $this->addFlash('success', 'Employé créé avec succès.');
                return $this->redirectToRoute('employe_index');
            }

            $this->addFlash('danger', $resultat->getPremierreErreur());
        }

        return $this->render('employe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'employe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employe $employe): Response
    {
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultat = $this->employeService->modifierEmploye($employe);

            if ($resultat->isSucces()) {
                $this->addFlash('success', 'Employé mis à jour.');
                return $this->redirectToRoute('employe_index');
            }

            $this->addFlash('danger', $resultat->getPremierreErreur());
        }

        return $this->render('employe/edit.html.twig', [
            'form'    => $form,
            'employe' => $employe,
        ]);
    }

    #[Route('/{id}/details', name: 'employe_details', methods: ['GET'])]
    public function details(Employe $employe): Response
    {
        return $this->render('employe/details.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'employe_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, int $id): Response
    {
        if ($request->isMethod('POST')) {
            $resultat = $this->employeService->supprimerEmploye($id);

            if ($resultat->isSucces()) {
                $this->addFlash('success', 'Employé supprimé.');
                return $this->redirectToRoute('employe_index');
            }

            $this->addFlash('danger', $resultat->getPremierreErreur());
            return $this->redirectToRoute('employe_index');
        }

        $employe = $this->employeRepo->find($id);
        if (!$employe) {
            throw $this->createNotFoundException('Employé introuvable.');
        }

        return $this->render('employe/delete.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/{id}/contrat', name: 'employe_ajouter_contrat', methods: ['GET', 'POST'])]
    public function ajouterContrat(Request $request, Employe $employe): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultat = $this->employeService->ajouterContrat($employe, $contrat);

            if ($resultat->isSucces()) {
                $this->addFlash('success', 'Contrat ajouté avec succès.');
                return $this->redirectToRoute('employe_details', ['id' => $employe->getId()]);
            }

            $this->addFlash('danger', $resultat->getPremierreErreur());
        }

        return $this->render('employe/contrat.html.twig', [
            'form'    => $form,
            'employe' => $employe,
        ]);
    }
}
