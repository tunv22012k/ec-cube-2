<?php

namespace Plugin\ProductOptions\Controller\Admin\Product;

use Eccube\Controller\Admin\Product\ProductController as BaseProductController;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\ProductImageRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\TagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\CsvExportService;
use Eccube\Util\CacheUtil;
use Plugin\ProductOptions\Repository\OptionsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

// class ProductController
class ProductController extends BaseProductController
{
    /**
     * @var CsvExportService
     */
    protected $csvExportService;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var ProductImageRepository
     */
    protected $productImageRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var ProductStatusRepository
     */
    protected $productStatusRepository;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @var OptionsRepository
     */
    private $optionsRepository;

    /**
     * ProductController constructor.
     *
     * @param CsvExportService $csvExportService
     * @param ProductClassRepository $productClassRepository
     * @param ProductImageRepository $productImageRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param PageMaxRepository $pageMaxRepository
     * @param ProductStatusRepository $productStatusRepository
     * @param TagRepository $tagRepository
     * @param OptionsRepository $optionsRepository
     */
    public function __construct(
        CsvExportService $csvExportService,
        ProductClassRepository $productClassRepository,
        ProductImageRepository $productImageRepository,
        TaxRuleRepository $taxRuleRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        BaseInfoRepository $baseInfoRepository,
        PageMaxRepository $pageMaxRepository,
        ProductStatusRepository $productStatusRepository,
        TagRepository $tagRepository,
        OptionsRepository $optionsRepository,
    ) {
        $this->csvExportService = $csvExportService;
        $this->productClassRepository = $productClassRepository;
        $this->productImageRepository = $productImageRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->pageMaxRepository = $pageMaxRepository;
        $this->productStatusRepository = $productStatusRepository;
        $this->tagRepository = $tagRepository;
        $this->optionsRepository = $optionsRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/product/product/new", name="admin_product_product_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/product/product/{id}/edit", requirements={"id" = "\d+"}, name="admin_product_product_edit", methods={"GET", "POST"})
     * @Template("@admin/Product/product.twig")
     */
    public function edit(Request $request, RouterInterface $router, CacheUtil $cacheUtil, $id = null)
    {
        // call data parent
        $response = parent::edit($request, $router, $cacheUtil, $id);

        // request POST
        if ('POST' === $request->getMethod()) {
        } else { // request GET
            // get all data options
            $listOptions = $this->optionsRepository->getListDatafindUseFlgActive();

            // set list options in response
            $response['listOptions'] = $listOptions;

            // get data id option when edit
            $options = [];
            $ProductOptions = $response['Product']->getProductOptions();
            foreach ($ProductOptions as $ProductOption) {
                /* @var $ProductOptions \Plugin\ProductOptions\Entity\ProductOptions */
                $options[] = $ProductOption->getOptions();
            }

            $ChoicedOptionsIds = array_map(function ($Options) {
                return $Options->getId();
            }, $options);

            // set data response choice option
            $response['choicedOptionsIds'] = $ChoicedOptionsIds;
        }

        return $response;
        // return [
        //     'Product' => $Product,
        //     'Tags' => $Tags,
        //     'TagsList' => $TagsList,
        //     'form' => $form->createView(),
        //     'searchForm' => $searchForm->createView(),
        //     'has_class' => $has_class,
        //     'id' => $id,
        //     'TopCategories' => $TopCategories,
        //     'ChoicedCategoryIds' => $ChoicedCategoryIds,
        // ];
    }
}
