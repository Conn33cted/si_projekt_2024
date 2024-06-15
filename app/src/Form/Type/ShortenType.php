<?php
/**
 * Shorten type.
 */

namespace App\Form\Type;

use App\Entity\Shorten;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShortenType.
 */
class ShortenType extends AbstractType
{
    private TagsDataTransformer $tagsDataTransformer;

    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Transformer for tags data
     */
    public function __construct(TagsDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('shorten_in', TextType::class, [
            'label' => 'label.shorten_in',
            'required' => true,
            'attr' => ['max_length' => 255],
        ]);
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );
        $builder->add('guest', EmailType::class, [
            'label' => 'label.enter_email',
            'required' => true,
            'mapped' => false,
            'attr' => ['max_length' => 255],
        ]);

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Shorten::class]);
    }

    /**
     * Get block prefix.
     *
     * @return string The block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'shorten';
    }
}
