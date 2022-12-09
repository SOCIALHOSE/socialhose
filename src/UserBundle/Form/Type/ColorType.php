<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ColorType
 *
 * @package UserBundle\Form\Type
 */
class ColorType extends AbstractType
{

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('constraints', new Callback([ $this, 'validate' ]));
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * Validate sources.
     *
     * @param mixed                     $color   Color.
     * @param ExecutionContextInterface $context A ExecutionContextInterface
     *                                           instance.
     *
     * @return void
     */
    public function validate($color, ExecutionContextInterface $context)
    {
        if ($color === null) {
            // Do not validate null values.
            return;
        }

        $matches = [];

        if (is_string($color) && preg_match('/rgba\(([0-9%.,\s]+)\)/', $color, $matches)) {
            $arguments = array_map('trim', explode(',', $matches[1]));

            if (count($arguments) === 4) {
                $alpha = array_pop($arguments);

                //
                // Validate color components.
                //
                if (is_numeric($alpha) && $this->containsColorDigits($arguments)) {
                    //
                    // Validate alpha component.
                    //

                    $alpha = (float) $alpha;

                    if (($alpha >= 0.0) && ($alpha <= 1.0)) {
                        return;
                    }
                }
            }
        }

        // It's not valid 'rgba' color.
        $context
            ->buildViolation('Color should be valid css color definition string')
            ->addViolation();
    }

    /**
     * @param array $array Checked array.
     *
     * @return boolean
     */
    private function containsColorDigits(array $array)
    {
//        return \Functional\every($array, function ($item) {
        return \nspl\a\all($array, function ($item) {
            if (! is_numeric($item)) {
                return false;
            }

            $item = (int) $item;

            return ($item >= 0) && ($item <= 255);
        });
    }
}
