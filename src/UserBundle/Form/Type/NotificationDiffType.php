<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use UserBundle\Enum\FontFamilyEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;
use UserBundle\Enum\ThemeOptionsTableOfContentsEnum;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;

/**
 * Class ScheduleType
 * @package UserBundle\Form\Type
 */
class NotificationDiffType extends AbstractType
{

    /**
     * @var array
     */
    private static $typesMap = [
        'summary' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Summary text at top of notification.',
            ],
        ],
        'conclusion' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Notification conclusion text.',
            ],
        ],

        'header:imageUrl' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Path to notification logo image.',
            ],
        ],
        'header:logoLink' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Logo href.',
            ],
        ],
        'header:title' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Notification title. Enhanced only',
            ],
        ],

        'fonts:header:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Header font size.',
            ],
        ],
        'fonts:header:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Header font family.',
            ],
        ],
        'fonts:header:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should header text bold or not.',
            ],
        ],
        'fonts:header:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should header text italic or not.',
            ],
        ],
        'fonts:header:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should header text underlined or not.',
            ],
        ],
        'fonts:tableOfContents:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Table of contents font size.',
            ],
        ],
        'fonts:tableOfContents:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Table of contents font family.',
            ],
        ],
        'fonts:tableOfContents:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should table of contents text bold or not.',
            ],
        ],
        'fonts:tableOfContents:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should table of contents text italic or not.',
            ],
        ],
        'fonts:tableOfContents:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should table of contents text underlined or not.',
            ],
        ],
        'fonts:feedTitle:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Feed title font size.',
            ],
        ],
        'fonts:feedTitle:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Feed title font family.',
            ],
        ],
        'fonts:feedTitle:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should feed title text bold or not.',
            ],
        ],
        'fonts:feedTitle:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should feed text text italic or not.',
            ],
        ],
        'fonts:feedTitle:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should feed title text underlined or not.',
            ],
        ],
        'fonts:articleHeadline:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Article headline font size.',
            ],
        ],
        'fonts:articleHeadline:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Article headline font family.',
            ],
        ],
        'fonts:articleHeadline:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article headline text bold or not.',
            ],
        ],
        'fonts:articleHeadline:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article headline text italic or not.',
            ],
        ],
        'fonts:articleHeadline:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article headline text underlined or not.',
            ],
        ],
        'fonts:source:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Source font size.',
            ],
        ],
        'fonts:source:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Source font family.',
            ],
        ],
        'fonts:source:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should source text bold or not.',
            ],
        ],
        'fonts:source:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should source text italic or not.',
            ],
        ],
        'fonts:source:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should source text underlined or not.',
            ],
        ],
        'fonts:author:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Author font size.',
            ],
        ],
        'fonts:author:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Author font family.',
            ],
        ],
        'fonts:author:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should author text bold or not.',
            ],
        ],
        'fonts:author:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should author text italic or not.',
            ],
        ],
        'fonts:author:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should author text underlined or not.',
            ],
        ],
        'fonts:date:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Date font size.',
            ],
        ],
        'fonts:date:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Date font family.',
            ],
        ],
        'fonts:date:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should date text bold or not.',
            ],
        ],
        'fonts:date:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should date text italic or not.',
            ],
        ],
        'fonts:date:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should date text underlined or not.',
            ],
        ],
        'fonts:articleContent:size' => [
            'class' => NumberType::class,
            'options' => [
                'description' => 'Article content font size.',
            ],
        ],
        'fonts:articleContent:family' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => FontFamilyEnum::class,
                'description' => 'Article content font family.',
            ],
        ],
        'fonts:articleContent:style:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article content text bold or not.',
            ],
        ],
        'fonts:articleContent:style:italic' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article content text italic or not.',
            ],
        ],
        'fonts:articleContent:style:underline' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should article content text underlined or not.',
            ],
        ],

        'content:highlightKeywords:highlight' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should highlights search keywords or not',
            ],
        ],
        'content:highlightKeywords:bold' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Should highlighted search keywords bold or not.',
            ],
        ],
        'content:highlightKeywords:color' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Highlight color.',
            ],
        ],
        'content:showInfo:userComments' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => ThemeOptionsUserCommentsEnum::class,
                'description' => 'How user comments should shown.',
            ],
        ],
        'content:showInfo:tableOfContents' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => ThemeOptionsTableOfContentsEnum::class,
                'description' => 'How table of contents should shown.',
            ],
        ],
        'content:showInfo:sourceCountry' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show source country or not.',
            ],
        ],
        'content:showInfo:articleSentiment' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show article sentiment or not.',
            ],
        ],
        'content:showInfo:articleCount' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show article count or not.',
            ],
        ],
        'content:showInfo:images' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show images or not.',
            ],
        ],
        'content:showInfo:sharingOptions' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show section divider or not.',
            ],
        ],
        'content:showInfo:sectionDivider' => [
            'class' => CheckboxType::class,
            'options' => [
                'description' => 'Show section divider or not.',
            ],
        ],
        'content:language' => [
            'class' => TextType::class,
            'options' => [
                'description' => 'Notification language.',
            ],
        ],
        'content:extract' => [
            'class' => EnumType::class,
            'options' => [
                'enum_class' => ThemeOptionExtractEnum::class,
                'description' => 'How article content should be extracted',
            ],
        ],

        'colors:background:header' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Header background color.',
            ],
        ],
        'colors:background:emailBody' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Email body background color.',
            ],
        ],
        'colors:background:accent' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Accent background color.',
            ],
        ],

        'colors:text:header' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Header text color.',
            ],
        ],
        'colors:text:articleHeadline' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Article headline text color.',
            ],
        ],
        'colors:text:articleContent' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Article content text color.',
            ],
        ],
        'colors:text:author' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Author text color.',
            ],
        ],
        'colors:text:publishDate' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Publish date text color.',
            ],
        ],
        'colors:text:source' => [
            'class' => ColorType::class,
            'options' => [
                'description' => 'Source text color.',
            ],
        ],
    ];

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach (self::$typesMap as $name => $config) {
            $class = $config['class'];
            $typeOptions = array_merge($config['options'], [ 'required' => false ]);

            $builder->add($name, $class, $typeOptions);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            //
            // Remove not form field which don't submit.
            //
            $data = $event->getData();
            $form = $event->getForm();

            $availableDiffs = array_keys(self::$typesMap);
            $submittedDiffs = array_keys(($data === null) ? [] : $data);

            $notProvidedDiffs = array_diff($availableDiffs, $submittedDiffs);

            foreach ($notProvidedDiffs as $name) {
                $form->remove($name);
            }
        });
    }
}
