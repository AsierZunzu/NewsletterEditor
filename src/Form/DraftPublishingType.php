<?php

namespace App\Form;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

use function Symfony\Component\Translation\t;

class DraftPublishingType extends AbstractType
{
    public const string FIELD_NEWSLETTER = 'newsletter';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::FIELD_NEWSLETTER, EntityType::class, [
                'class' => Newsletter::class,
                'choice_label' => 'id',//TODO use a better field
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('n')
                        ->andWhere('n.sent = false');
                },
            ])
            ->add('save', SubmitType::class, ['label' => t('Publish draft')])
        ;
    }
}
