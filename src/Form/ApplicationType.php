<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    protected function getConfiguration(string $label, string $placeholder, array $options=[]):array
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ],
        ], $options
        );
        
    }

}



?>