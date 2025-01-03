<?php

namespace Plugin\ProductOptions\Controller\Admin;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Event\EventArgs;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Util\CacheUtil;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Plugin\ProductOptions\Entity\Options;
use Plugin\ProductOptions\Event\CustomizePluginEvents;
use Plugin\ProductOptions\Form\Type\Admin\SearchOptionsFormType;
use Plugin\ProductOptions\Form\Type\Admin\UpsertOptionsType;
use Plugin\ProductOptions\Repository\OptionsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class OptionsController extends AbstractController
{
    /**
     * @var OptionsRepository
     */
    private $optionsRepository;

    /**
     * @var PageMaxRepository
     */
    private $pageMaxRepository;

    /**
     * ConfigController constructor.
     *
     * @param OptionsRepository $optionsRepository
     * @param PageMaxRepository $pageMaxRepository
     */
    public function __construct(
        OptionsRepository $optionsRepository,
        PageMaxRepository $pageMaxRepository
    ) {
        $this->optionsRepository    = $optionsRepository;
        $this->pageMaxRepository    = $pageMaxRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/options/list", name="plugin_admin_options_list", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/options/page/{page_no}", requirements={"page_no" = "\d+"}, name="plugin_admin_options_page", methods={"GET", "POST"})
     * @Template("@ProductOptions/admin/options/index.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator, $page_no = null)
    {
        // create form to form type
        $builder = $this->formFactory->createBuilder(SearchOptionsFormType::class);

        // set event begin
        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_INDEX_INITIALIZE);

        // get form to form type
        $searchForm = $builder->getForm();

        /**
         * SET DATA PANIGATE
         * ページの表示件数は, 以下の順に優先される.
         * - リクエストパラメータ
         * - セッション
         * - デフォルト値
         * また, セッションに保存する際は mtb_page_maxと照合し, 一致した場合のみ保存する.
         **/
        $page_count = $this->session->get('eccube.admin.options.search.page_count', $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('eccube.admin.options.search.page_count', $page_count);
                    break;
                }
            }
        }

        // check data when search
        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                // set data default
                $page_no = 1;

                // get data form
                $searchData = $searchForm->getData();

                // set session
                $this->session->set('eccube.admin.options.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.options.search.page_no', $page_no);
            } else {
                // when err search data
                return [
                    'searchForm'    => $searchForm->createView(),
                    'pagination'    => [],
                    'pageMaxis'     => $pageMaxis,
                    'page_no'       => $page_no,
                    'page_count'    => $page_count,
                    'has_errors'    => true,
                ];
            }
        } else { // data begin
            // get data on resum page
            if (null !== $page_no || $request->get('resume')) {
                if ($page_no) {
                    // ページ送りで遷移した場合.
                    $this->session->set('eccube.admin.options.search.page_no', (int) $page_no);
                } else {
                    // 他画面から遷移した場合.
                    $page_no = $this->session->get('eccube.admin.options.search.page_no', 1);
                }
                $viewData   = $this->session->get('eccube.admin.options.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else { // get data on first page
                // set data default
                $page_no = 1;

                // submit default value
                $viewData   = FormUtil::getViewData($searchForm);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

                // set session
                $this->session->set('eccube.admin.options.search', $viewData);
                $this->session->set('eccube.admin.options.search.page_no', $page_no);
            }
        }

        // get data call sql to repository
        $qb = $this->optionsRepository->getQueryBuilderBySearchDataForAdmin($searchData);

        // set event search
        $event = new EventArgs(
            [
                'qb'            => $qb,
                'searchData'    => $searchData,
            ],
            $request
        );

        $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_SEARCH);

        // get data sort table
        $sortKey = $searchData['sortkey'];

        // get data sort
        if (
            empty($this->optionsRepository::COLUMNS[$sortKey])
            || $sortKey == 'id'
            || $sortKey == 'fee'
            || $sortKey == 'code'
            || $sortKey == 'create_date'
        ) {
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count
            );
        } else { // get show data no sort
            // call data and paginator
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count,
                ['wrap-queries' => true]
            );
        }

        // return data
        return [
            'searchForm'    => $searchForm->createView(),
            'pagination'    => $pagination,
            'pageMaxis'     => $pageMaxis,
            'page_no'       => $page_no,
            'page_count'    => $page_count,
            'has_errors'    => false,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/options/{id}/visibility", requirements={"id" = "\d+"}, name="admin_options_visibility", methods={"PUT"})
     */
    public function visibility(Request $request, Options $options)
    {
        $this->isTokenValid();

        // check data use_flg
        if ($options->getUseFlg()) {
            $message = "Đã ẩn thành công với tên " . $options->getName();
            $options->setUseFlg(false);
        } else {
            $message = "Đã hiển thị thành công với tên " . $options->getName();
            $options->setUseFlg(true);
        }

        // call update data
        $this->entityManager->persist($options);
        $this->entityManager->flush();

        // add event
        $event = new EventArgs(
            [
                'options' => $options,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_VISIBILITY_COMPLETE);

        // add notification
        $this->addSuccess($message, 'admin');

        // redicrect route
        return $this->redirectToRoute('plugin_admin_options_list');
    }

    /**
     * @Route("/%eccube_admin_route%/options/{id}/delete", requirements={"id" = "\d+"}, name="admin_options_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Options $options)
    {
        $this->isTokenValid();

        // dd($options);

        try {
            // call delete data
            // $this->entityManager->remove($options);
            // $this->entityManager->flush();
            $qb = $this->entityManager->createQueryBuilder();
            $qb->delete(Options::class, 'po')->where('po.id = :id')->setParameter('id', $options->getId())->getQuery()->execute();

            // call event
            $event = new EventArgs(
                [
                    'options' => $options,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_DELETE_COMPLETE);
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addError("Không thể xóa " . $options->getName() . " vì nó có dữ liệu liên quan đến option đang sử dụng", 'admin');

            return $this->redirectToRoute('plugin_admin_options_list');
        }

        // add notification
        $this->addSuccess("Đã xóa thành công với tên " . $options->getName(), 'admin');

        // return route list
        return $this->redirectToRoute('plugin_admin_options_list');
    }

    /**
     * @Route("/%eccube_admin_route%/options/new", name="admin_options_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/options/{id}/edit", requirements={"id" = "\d+"}, name="admin_options_edit", methods={"GET", "POST"})
     * @Template("@ProductOptions/admin/options/upsert.twig")
     */
    public function upsert(Request $request, RouterInterface $router, CacheUtil $cacheUtil, $id = null)
    {
        // check form insert or update
        if (!empty($id)) {
            $options = $this->optionsRepository->getListDatafindOneBy($id);
        } else {
            $options = new Options();
        }

        // create form type
        $builder = $this->formFactory->createBuilder(UpsertOptionsType::class, $options);

        // set event
        $event = new EventArgs(
            [
                'builder'   => $builder,
                'options'   => $options,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_UPDATE_INITIALIZE);

        $form = $builder->getForm();

        // when request submit update or create
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // get data request
                $options = $form->getData();

                // upsert data
                $this->entityManager->persist($options);
                $this->entityManager->flush();

                // set event
                $event = new EventArgs(
                    [
                        'form'  => $form,
                        'options'   => $options,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, CustomizePluginEvents::ADMIN_OPTIONS_UPDATE_COMPLETE);

                // add notification
                if (!empty($options)) {
                    $this->addSuccess("Đã cập nhật thành công với tên " . $options->getName(), 'admin');
                } else {
                    $this->addSuccess("Đã thêm mới thành công với tên " . $options->getName(), 'admin');
                }

                // return route list
                return $this->redirectToRoute('plugin_admin_options_list');
            }
        }

        // return data
        return [
            'options'   => $options,
            'form'      => $form->createView(),
        ];
    }
}
