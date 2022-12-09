<?php

namespace ApiBundle\Serializer\Normalizer;

use AppBundle\Utils\TransKey\TransKeyGeneratorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Validator\Constraints\Form as FormConstraint;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints;

/**
 * Class FormNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class FormNormalizer implements NormalizerInterface
{

    /**
     * Already founded form element keys.
     *
     * @var string[]
     */
    private $transKeysCache = [];

    /**
     * Map between comparison constraint fqcn and generator config.
     *
     * Config has next keys:
     *  * key - it value must start from upper case character, and should by an
     *    antonym for constraint.
     *  * valueName - name of value in response with which we got conflict, for
     *    example: bound value for comparison. May be omitted.
     *
     * @var string[]
     */
    private static $comparisonConfig = [
        Constraints\GreaterThanOrEqual::class => [
            'key' => 'Lower',
            'valueName' => 'than',
        ],
        Constraints\GreaterThan::class => [
            'key' => 'LowerOrEqual',
            'valueName' => 'than',
        ],
        Constraints\LessThan::class => [
            'key' => 'GreaterOrEqual',
            'valueName' => 'than',
        ],
        Constraints\LessThanOrEqual::class => [
            'key' => 'Greater',
            'valueName' => 'than',
        ],
    ];

    /**
     * Checks whether the given class is supportedClass for normalization by this
     * normalizer.
     *
     * @param mixed  $data   Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FormInterface;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object|FormInterface $object  Object to normalize.
     * @param string               $format  Format the normalization result will
     *                                      be encoded as.
     * @param array                $context Context options for the normalizer.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (! $object->isSubmitted()) {
            return [ 'Form not submitted.' ];
        }

        return array_map(function (FormError $error) {
            return $this->explainError($error);
        }, iterator_to_array($object->getErrors(true, true)));
    }

    /**
     * Generate form key in camelCase.
     *
     * @param FormInterface $form A FormInterface instance.
     *
     * @return string
     */
    private function getTransKey(FormInterface $form)
    {
        $hash = spl_object_hash($form);

        if (! isset($this->transKeysCache[$hash])) {
            /** @var TransKeyGeneratorInterface $generator */
            $generator = $form->getConfig()->getOption('key');
            $this->transKeysCache[$hash] = $generator->generate($form);
        }

        return $this->transKeysCache[$hash];
    }

    /**
     * Explain occurred form error.
     *
     * @param FormError $error A FormError instance.
     *
     * @return array
     */
    private function explainError(FormError $error)
    {
        $form = $error->getOrigin();
        $cause = $error->getCause();

        //
        // Prepare current translation key and translation parameters.
        //
        $transKey = $this->getTransKey($form);
        $parameters = [];

        //
        // Check cause.
        //
        if ($cause instanceof ConstraintViolation) {
            //
            // We got constraint violation so we should check that we known about
            // constrain which spawned this violation and make proper explanation.
            //
            $isOrder = is_numeric($form->getName());
            if ($isOrder || ($form->getParent() && is_numeric(\app\op\invokeIf($form->getParent(), 'getName')))) {
                //
                // Entry type of CollectionType had order number instead of name
                // and we should store this number in parameters and send to
                // client.
                //
                $order = (int) ($isOrder ? $form->getName() : \app\op\invokeIf($form->getParent(), 'getName'));
                $parameters['order'] = $order;
            }

            $parameters = array_merge($parameters, $this->getParametersForViolation(
                $cause,
                $form,
                $transKey
            ));
        }

        return [
            'message' => $error->getMessage(),
            'transKey' => $transKey,
            // This hardcoded by requesting from Frontend Developers.
            'type' => 'error',
            'parameters' => $parameters,
        ];
    }

    /**
     * @param ConstraintViolation $violation Founded violation.
     * @param FormInterface       $form      Form on which violation found.
     * @param string              $transKey  Translation key for this form.
     *
     * @return array
     */
    private function getParametersForViolation(
        ConstraintViolation $violation,
        FormInterface $form,
        &$transKey
    ) {
        $parameters = $this->getParametersForViolationByCode($violation, $form, $transKey);
        if ($parameters === null) {
            $parameters = $this->getParametersForViolationByClass($violation, $transKey);
        }

        return $parameters !== null ? $parameters : [];
    }

    /**
     * @param ConstraintViolation $violation Founded violation.
     * @param FormInterface       $form      Form on which violation found.
     * @param string              $transKey  Translation key for this form.
     *
     * @return array|null
     */
    private function getParametersForViolationByCode(
        ConstraintViolation $violation,
        FormInterface $form,
        &$transKey
    ) {
        $code = $violation->getCode();
        $current = $violation->getInvalidValue();

        $parameters = null;
        switch (true) {
            //
            // Firstly we should check possible form constraint errors.
            //
            case $code === FormConstraint::NO_SUCH_FIELD_ERROR:
                $transKey .= 'UnknownField';
                $parameters = [
                    'name' => key($current),
                ];
                break;

            case $code === FormConstraint::NOT_SYNCHRONIZED_ERROR:
                $transKey .= 'Invalid';
                $parameters = [
                    'current' => $current,
                ];

                if ($this->isChoiceType($form)) {
                    // We should return available choice values and also should
                    // return only invalid values.
                    $choices = $form->getConfig()->getOption('choices');

                    $parameters['available'] = $choices;
                    if (is_array($choices) && $form->getConfig()->getOption('multiple')) {
                        $parameters['invalid'] = array_diff($current, $choices);
                    }
                }

                break;

            //
            // Finally we should check UniqueEntity constraint.
            // For some reasons violation with 'Form::NO_SUCH_FIELD_ERROR'
            // code has UniqueEntity constraint so we should check check
            // 'Form::NO_SUCH_FIELD_ERROR' first to avoid strange error
            // messages.
            //
            case $code === UniqueEntity::NOT_UNIQUE_ERROR:
                $transKey .= 'NotUnique';
                $parameters = [
                    'current' => $current,
                ];
                break;
        }

        return $parameters;
    }

    /**
     * @param ConstraintViolation $violation Founded violation.
     * @param string              $transKey  Translation key for this form.
     *
     * @return array|null
     */
    private function getParametersForViolationByClass(
        ConstraintViolation $violation,
        &$transKey
    ) {
        $constraint = $violation->getConstraint();
        $class = get_class($constraint);
        $current = $violation->getInvalidValue();

        $parameters = null;

        switch (true) {
            //
            // Length constraint.
            //
            case $constraint instanceof Constraints\Length:
                $transKey .= 'TooShort';
                $parameters = [
                    'min' => $constraint->min,
                ];
                break;

            //
            // Next we check comparison errors.
            // For comparison error we also should check that we hav config
            // for it.
            //
            case ($constraint instanceof AbstractComparison)
                && isset(self::$comparisonConfig[$class]):
                $config = self::$comparisonConfig[$class];
                $transKey .= $config['key'];
                $parameters = [
                    'current' => $current,
                    $config['valueName'] => $constraint->value,
                ];
                break;

            //
            // Required field is blank.
            //
            case ($constraint instanceof Constraints\NotBlank):
                $transKey .= 'Empty';
                $parameters = [
                    'current' => $current,
                ];
                break;
        }

        return $parameters;
    }

    /**
     * Check that specified form is choice type or extend it.
     *
     * @param FormInterface $form A FormInterface instance.
     *
     * @return boolean
     */
    private function isChoiceType(FormInterface $form)
    {
        $type = $form->getConfig()->getType();
        $innerType = $type->getInnerType();

        return ($type instanceof ChoiceType)
            || ($innerType instanceof  ChoiceType)
            || ($innerType->getParent() === ChoiceType::class);
    }
}
