<?php declare(strict_types=1);

namespace RadiologiesWishlist\Storefront\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use RadiologiesWishlist\Storefront\Page\RadiologiesWishlist\RadiologiesWishlistPageLoader;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class RadilogiesWishlistController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private $wishlistRepository;
    private $radiologiesWishlistPageLoader;
    private $systemConfigService;
    protected $shopVersion;

    public function __construct(EntityRepository $wishlistRepository,RadiologiesWishlistPageLoader $radiologiesWishlistPageLoader, 
    SystemConfigService $systemConfigService,string $shopVersion)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->radiologiesWishlistPageLoader = $radiologiesWishlistPageLoader;
        $this->systemConfigService = $systemConfigService;
        $this->shopVersion        = $shopVersion;
    }

    #[Route(
        path: '/radiologies/wishlist/create',name: 'frontend.radiologies.wishlist.create',methods: ['POST','GET'],defaults: ['XmlHttpRequest' => true, '_loginRequired' => true]
    )]
    public function createWishlist(Request $request, SalesChannelContext $context): Response
    {
        if (!$context->getCustomer()) {
            return $this->redirectToRoute('frontend.account.login.page');
        }
        
        $response = [];
        
        try {
            $name = $request->get('wishlistName');
            $productId = $request->get('wishlistProductId');
    
            if (empty($productId)) {
                throw RoutingException::missingRequestParameter('wishlistProductId');
            }
    
            $customerId = $context->getCustomer()->getId();
            $data = [
                'id' => Uuid::randomHex(),
                'name' => $name,
                'customerId' => $customerId,
                'productId' => $productId,
                'customFields' =>
                [
                    'productNumber' => $request->get('wishlistProductNumber'),
                ]
            ];
            
            $this->wishlistRepository->upsert([$data], $context->getContext());
    
            $response[] = [
                'type' => 'success',
                $this->addFlash(self::SUCCESS, $this->trans('radiologies-wishlist.empty.success')),
            ];
            
            
        } catch (ConstraintViolationException $exception) {
            $errors = [];
            foreach ($exception->getViolations() as $error) {
                $errors[] = $error->getMessage();
            }
            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => $errors,
                ]),
            ];
        }
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter("customerId", $customerId));
        $radiologiesWishlistsDetails = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();
        $radiologiesWishlist = [];
        foreach ($radiologiesWishlistsDetails as $key => $value) {
            $radiologiesWishlist[] = $value;
        }
        $page = $this->radiologiesWishlistPageLoader->load($request, $context);
        // $page->setRadiologiesWishlistData($radiologiesWishlist);
        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,nofollow');
        }
        $response = $this->redirectToRoute('frontend.account.radiologies.wishlist.list', ['page'=>$page]);
        return $response;
    }

    #[Route(
        path: '/radiologies/wishlist/add',name: 'frontend.radiologies.wishlist.add',methods: ['POST','GET'],defaults:["XmlHttpRequest"=>true]
    )]
    public function addToWishlist(Request $request,SalesChannelContext $context): Response
    {
        if (!$context->getCustomer()) {
            return $this->redirectToRoute('frontend.account.login.page');
        }
    
        $response = [];
    
        try {
            $wishlistId = $request->get('wishlistId');
            if ($wishlistId && $context->getCustomer()->getId()) {
                $data = [
                    'id' => $wishlistId,
                    'productId' => $request->get('radiologiesWishlistProductId'),
                    'customFields' =>
                    [
                        'productNumber' => $request->get('radiologiesWishlistProductNumber'),
                    ]
                ];
                $this->wishlistRepository->update([$data], $context->getContext());
            }
                
            $response[] = [
                'type' => 'success',
                $this->addFlash(self::SUCCESS, $this->trans('radiologies-wishlist.empty.updated')),
            ];
        } catch (ConstraintViolationException $exception) {
            $errors = [];
            foreach ($exception->getViolations() as $error) {
                $errors[] = $error->getMessage();
            }
            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => $errors,
                ]),
            ];
        }
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter("customerId", $context->getCustomer()->getId()));
        $radiologiesWishlistsDetails = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();
        $radiologiesWishlist = [];
        foreach ($radiologiesWishlistsDetails as $key => $value) {
            $radiologiesWishlist[] = $value;
        }
        $page = $this->radiologiesWishlistPageLoader->load($request, $context);
        // $page->setRadiologiesWishlistData($radiologiesWishlist);
        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,nofollow');
        }
        $response = $this->redirectToRoute('frontend.account.radiologies.wishlist.list', ['page'=>$page]);
        return $response;
    }
    #[Route(
        path: '/account/radiologies-wishlist/list',name: 'frontend.account.radiologies.wishlist.list',methods: ['GET']
    )]
    public function accountWishlist(Request $request, SalesChannelContext $context):Response
    {
        if (!$context->getCustomer()) {
            return $this->redirectToRoute('frontend.account.login.page');
        }
        $response = [];
        
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $context->getCustomer()->getId()));
        $wishlistItems = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();
        $productId = [];
        foreach ($wishlistItems as $key => $value) {
            $productId[$key] = $value->getProductId();
            // print"<pre>";print_r($value);die;
        }
        $csrf_enable = 0;
        if ($this->shopVersion < 6.5) {
            $csrf_enable = 1;
        }
        // Load the wishlist with enhanced product data
        $page = $this->radiologiesWishlistPageLoader->load($request, $context);
        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,nofollow');
        }
        $response = $this->renderStorefront('@Storefront/storefront/page/account/radiologies-wishlist/index.html.twig', 
        ['page'=>$page,'productId'=>$productId,'csrf_enable'=>$csrf_enable]);
        return $response;
    }
    #[Route(
        path: '/account/radiologies/wishlist', 
        name: 'frontend.account.radiologies.wishlist', 
        methods: ['GET']
    )]
    public function fetchSortedWishlist(Request $request, SalesChannelContext $context): Response
    {
        // Redirect to login if user is not authenticated
        if (!$context->getCustomer()) {
            return $this->redirectToRoute('frontend.account.login.page');
        }
    
        // Get sorting and search query parameters
        $sortType = $request->get('sortType', 'createdAt');
        $sortDirection = $request->get('sortDirection', FieldSorting::DESCENDING);  // Default sorting direction is DESC
        $searchQuery = $request->get('searchQuery', '');
    
        // Set up criteria for wishlist
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('customerId', $context->getCustomer()->getId()));
    
        // Apply sorting based on sortType and sortDirection
        switch ($sortType) {
            case 'productId':
                $criteria->addSorting(new FieldSorting('productId', $sortDirection));
                break;
            case 'name':
                $criteria->addSorting(new FieldSorting('product.name', $sortDirection));
                break;
            case 'price':
                $criteria->addSorting(new FieldSorting('product.price', $sortDirection));
                break;
            case 'manual':
                if (!empty($searchQuery)) {
                    $criteria->addFilter(new ContainsFilter('product.name', $searchQuery));
                }
                break;
            default:
                $criteria->addSorting(new FieldSorting('createdAt', $sortDirection)); // Default sort by creation date
        }
    
        // Fetch wishlist items based on the criteria
        $wishlistItems = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();
    
        // Load wishlist page with enhanced product data
        $page = $this->radiologiesWishlistPageLoader->load($request, $context);
        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,nofollow');
        }
    
        // Render the wishlist page with the sorting and search data
        $response = $this->renderStorefront('@Storefront/storefront/page/account/radiologies-wishlist/index.html.twig', [
            'page' => $page,
            'sortType' => $sortType,
            'sortDirection' => $sortDirection,
            'searchQuery' => $searchQuery,
        ]);
    
        return $response;
    }
    
    private function getConfig($key, SalesChannelContext $context)
    {
        return $this->systemconfigService->get(
            'RadiologiesWishlist.config.' . $key,
            $salesChannelContext->getSalesChannel()->getId()
        );
    }
    
}
