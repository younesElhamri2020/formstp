<?php


namespace App\Forms;


use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class productType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description',TextType::class)
            ->add('prix',NumberType::class)
            ->add('quantite',IntegerType::class)
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'choice_label' => 'name',
            ])
            ->add('image',FileType::class,[
                'mapped' => false,
                'required' => false,

                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'le fomrat invalid',
                    ])
                ],
            ])
            ->add('TTC', CheckboxType::class, [
                'mapped'=>false,
                'label'    => 'inclue  le TVA?',
                'required' =>$options['required_ttc']
            ])
            ->add('save', SubmitType::class, ['label' => 'Save']);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'required_ttc'=>false,

        ]);
    }
}