<?php

namespace App\Form;

use App\Entity\Crypto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AddType extends AbstractType
{
    // This form cointains fiels of Crypto entity and allow user to save his transactions

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'required' => true,
                'label' => false,
                'placeholder' => 'Sélectionner une crypto',
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => [
                    'Bitcoin' => 'BTC',
                    'Ethereum' => 'ETH',
                    'Tether' => 'USDT',
                    'BNB' => 'BNB',
                    'USD Coin' => 'USDC',
                    'XRP' => 'XRP',
                    'Solana' => 'SOL',
                    'Cardano' => 'ADA',
                    'Terra' => 'LUNA',
                    'Avalanche' => 'AVAX',
                ]
            ])
            ->add('value', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'Prix d\'achat'],
            ])
            ->add('qte', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'Quantité (unitaire)'],
            ])
            ->add('editer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Crypto::class,
        ]);
    }
}
