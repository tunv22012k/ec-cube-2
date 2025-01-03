<?php

namespace Plugin\ProductOptions\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\PriceType;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpsertOptionsType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ContainerBagInterface
     */
    protected $container;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * UpsertOptionsType __construct
     *
     * @param EccubeConfig $eccubeConfig
     * @param ContainerBagInterface $container
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        ContainerBagInterface $container,
        ValidatorInterface $validator
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->container    = $container;
        $this->validator    = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currency = $this->container->get('currency');
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            // ->add('price', TextType::class, [
            //     'constraints' => [
            //         new Assert\NotBlank(),
            //         new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
            //     ],
            // ])
            ->add('fee', PriceType::class, [
                'label' => 'fee options',
                'required' => true,
                'currency' => $currency,
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                    ]),
                ],
            ])
            ->add('return_link', HiddenType::class, [
                'mapped' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (!empty($data['fee']) || $data['fee'] <= 0) {
                    // 値引率
                    /** @var ConstraintViolationList $errors */
                    $errors = $this->validator->validate($data['fee'], [
                        new Assert\NotBlank(),
                    ]);
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['fee']->addError(new FormError($error->getMessage()));
                        }
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_upsert_options';
    }
}
